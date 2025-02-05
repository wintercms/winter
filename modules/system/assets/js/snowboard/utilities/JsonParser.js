import Singleton from '../abstracts/Singleton';

/**
 * JSON Parser utility.
 *
 * This utility parses JSON-like data that does not strictly meet the JSON specifications in order to simplify development.
 * It is a safe replacement for JSON.parse(JSON.stringify(eval("({" + value + "})"))) that does not require the use of eval()
 *
 * @author Ayumi Hamasaki
 * @author Ben Thomson <git@alfreido.com>
 * @see https://github.com/octobercms/october/pull/4527
 */
export default class JsonParser extends Singleton {
    construct() {
        // Add to global function for backwards compatibility
        window.wnJSON = (json) => this.parse(json);
        window.ocJSON = window.wnJSON;
    }

    parse(str) {
        const jsonString = this.parseString(str);
        return JSON.parse(jsonString);
    }

    parseString(value) {
        let str = value.trim();

        if (!str.length) {
            throw new Error('Broken JSON object.');
        }

        let result = '';
        let type = null;
        let key = null;
        let body = '';

        /*
        * the mistake ','
        */
        while (str && str[0] === ',') {
            str = str.substr(1);
        }

        /*
        * string
        */
        if (str[0] === '"' || str[0] === '\'') {
            if (str[str.length - 1] !== str[0]) {
                throw new Error('Invalid string JSON object.');
            }

            body = '"';
            for (let i = 1; i < str.length; i += 1) {
                if (str[i] === '\\') {
                    if (str[i + 1] === '\'') {
                        body += str[i + 1];
                    } else {
                        body += str[i];
                        body += str[i + 1];
                    }
                    i += 1;
                } else if (str[i] === str[0]) {
                    body += '"';
                    return body;
                } else if (str[i] === '"') {
                    body += '\\"';
                } else {
                    body += str[i];
                }
            }

            throw new Error('Invalid string JSON object.');
        }

        /*
        * boolean
        */
        if (str === 'true' || str === 'false') {
            return str;
        }

        /*
        * null
        */
        if (str === 'null') {
            return 'null';
        }

        /*
        * number
        */
        const num = Number(str);
        if (!Number.isNaN(num)) {
            return num.toString();
        }

        /*
        * object
        */
        if (str[0] === '{') {
            type = 'needKey';
            key = null;
            result = '{';

            for (let i = 1; i < str.length; i += 1) {
                if (this.isBlankChar(str[i])) {
                    /* eslint-disable-next-line */
                    continue;
                }
                if (type === 'needKey' && (str[i] === '"' || str[i] === '\'')) {
                    key = this.parseKey(str, i + 1, str[i]);
                    result += `"${key}"`;
                    i += key.length;
                    i += 1;
                    type = 'afterKey';
                } else if (type === 'needKey' && this.canBeKeyHead(str[i])) {
                    key = this.parseKey(str, i);
                    result += '"';
                    result += key;
                    result += '"';
                    i += key.length - 1;
                    type = 'afterKey';
                } else if (type === 'afterKey' && str[i] === ':') {
                    result += ':';
                    type = ':';
                } else if (type === ':') {
                    body = this.getBody(str, i);

                    i = i + body.originLength - 1;
                    result += this.parseString(body.body);

                    type = 'afterBody';
                } else if (type === 'afterBody' || type === 'needKey') {
                    let last = i;
                    while (str[last] === ',' || this.isBlankChar(str[last])) {
                        last += 1;
                    }
                    if (str[last] === '}' && last === str.length - 1) {
                        while (result[result.length - 1] === ',') {
                            result = result.substr(0, result.length - 1);
                        }
                        result += '}';
                        return result;
                    }
                    if (last !== i && result !== '{') {
                        result += ',';
                        type = 'needKey';
                        i = last - 1;
                    }
                }
            }

            throw new Error(`Broken JSON object near ${result}`);
        }

        /*
        * array
        */
        if (str[0] === '[') {
            result = '[';
            type = 'needBody';
            for (let i = 1; i < str.length; i += 1) {
                if (str[i] === ' ' || str[i] === '\n' || str[i] === '\t') {
                    /* eslint-disable-next-line */
                    continue;
                } else if (type === 'needBody') {
                    if (str[i] === ',') {
                        result += 'null,';
                        /* eslint-disable-next-line */
                        continue;
                    }
                    if (str[i] === ']' && i === str.length - 1) {
                        if (result[result.length - 1] === ',') {
                            result = result.substr(0, result.length - 1);
                        }
                        result += ']';
                        return result;
                    }

                    body = this.getBody(str, i);

                    i = i + body.originLength - 1;
                    result += this.parseString(body.body);

                    type = 'afterBody';
                } else if (type === 'afterBody') {
                    if (str[i] === ',') {
                        result += ',';
                        type = 'needBody';

                        // deal with mistake ","
                        while (str[i + 1] === ',' || this.isBlankChar(str[i + 1])) {
                            if (str[i + 1] === ',') {
                                result += 'null,';
                            }
                            i += 1;
                        }
                    } else if (str[i] === ']' && i === str.length - 1) {
                        result += ']';
                        return result;
                    }
                }
            }

            throw new Error(`Broken JSON array near ${result}`);
        }

        return '';
    }

    getBody(str, pos) {
        let body = '';

        // parse string body
        if (str[pos] === '"' || str[pos] === '\'') {
            body = str[pos];

            for (let i = pos + 1; i < str.length; i += 1) {
                if (str[i] === '\\') {
                    body += str[i];
                    if (i + 1 < str.length) {
                        body += str[i + 1];
                    }
                    i += 1;
                } else if (str[i] === str[pos]) {
                    body += str[pos];
                    return {
                        originLength: body.length,
                        body,
                    };
                } else {
                    body += str[i];
                }
            }

            throw new Error(`Broken JSON string body near ${body}`);
        }

        // parse true / false
        if (str[pos] === 't') {
            if (str.indexOf('true', pos) === pos) {
                return {
                    originLength: 'true'.length,
                    body: 'true',
                };
            }

            throw new Error(`Broken JSON boolean body near ${str.substr(0, pos + 10)}`);
        }
        if (str[pos] === 'f') {
            if (str.indexOf('f', pos) === pos) {
                return {
                    originLength: 'false'.length,
                    body: 'false',
                };
            }

            throw new Error(`Broken JSON boolean body near ${str.substr(0, pos + 10)}`);
        }

        // parse null
        if (str[pos] === 'n') {
            if (str.indexOf('null', pos) === pos) {
                return {
                    originLength: 'null'.length,
                    body: 'null',
                };
            }

            throw new Error(`Broken JSON boolean body near ${str.substr(0, pos + 10)}`);
        }

        // parse number
        if (str[pos] === '-' || str[pos] === '+' || str[pos] === '.' || (str[pos] >= '0' && str[pos] <= '9')) {
            body = '';

            for (let i = pos; i < str.length; i += 1) {
                if (str[i] === '-' || str[i] === '+' || str[i] === '.' || (str[i] >= '0' && str[i] <= '9')) {
                    body += str[i];
                } else {
                    return {
                        originLength: body.length,
                        body,
                    };
                }
            }

            throw new Error(`Broken JSON number body near ${body}`);
        }

        // parse object
        if (str[pos] === '{' || str[pos] === '[') {
            const stack = [
                str[pos],
            ];
            body = str[pos];

            for (let i = pos + 1; i < str.length; i += 1) {
                body += str[i];
                if (str[i] === '\\') {
                    if (i + 1 < str.length) {
                        body += str[i + 1];
                    }
                    i += 1;
                } else if (str[i] === '"') {
                    if (stack[stack.length - 1] === '"') {
                        stack.pop();
                    } else if (stack[stack.length - 1] !== '\'') {
                        stack.push(str[i]);
                    }
                } else if (str[i] === '\'') {
                    if (stack[stack.length - 1] === '\'') {
                        stack.pop();
                    } else if (stack[stack.length - 1] !== '"') {
                        stack.push(str[i]);
                    }
                } else if (stack[stack.length - 1] !== '"' && stack[stack.length - 1] !== '\'') {
                    if (str[i] === '{') {
                        stack.push('{');
                    } else if (str[i] === '}') {
                        if (stack[stack.length - 1] === '{') {
                            stack.pop();
                        } else {
                            throw new Error(`Broken JSON ${(str[pos] === '{' ? 'object' : 'array')} body near ${body}`);
                        }
                    } else if (str[i] === '[') {
                        stack.push('[');
                    } else if (str[i] === ']') {
                        if (stack[stack.length - 1] === '[') {
                            stack.pop();
                        } else {
                            throw new Error(`Broken JSON ${(str[pos] === '{' ? 'object' : 'array')} body near ${body}`);
                        }
                    }
                }
                if (!stack.length) {
                    return {
                        originLength: i - pos,
                        body,
                    };
                }
            }

            throw new Error(`Broken JSON ${(str[pos] === '{' ? 'object' : 'array')} body near ${body}`);
        }

        throw new Error(`Broken JSON body near ${str.substr((pos - 5 >= 0) ? pos - 5 : 0, 50)}`);
    }

    parseKey(str, pos, quote) {
        let key = '';

        for (let i = pos; i < str.length; i += 1) {
            if (quote && quote === str[i]) {
                return key;
            }
            if (!quote && (str[i] === ' ' || str[i] === ':')) {
                return key;
            }

            key += str[i];

            if (str[i] === '\\' && i + 1 < str.length) {
                key += str[i + 1];
                i += 1;
            }
        }

        throw new Error(`Broken JSON syntax near ${key}`);
    }

    canBeKeyHead(ch) {
        if (ch[0] === '\\') {
            return false;
        }
        if ((ch[0] >= 'a' && ch[0] <= 'z') || (ch[0] >= 'A' && ch[0] <= 'Z') || ch[0] === '_') {
            return true;
        }
        if (ch[0] >= '0' && ch[0] <= '9') {
            return true;
        }
        if (ch[0] === '$') {
            return true;
        }
        if (ch.charCodeAt(0) > 255) {
            return true;
        }

        return false;
    }

    isBlankChar(ch) {
        return ch === ' ' || ch === '\n' || ch === '\t';
    }
}
