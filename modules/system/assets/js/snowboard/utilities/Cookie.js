import BaseCookie from 'js-cookie';
import Singleton from '../abstracts/Singleton';

/**
 * Cookie utility.
 *
 * This utility is a thin wrapper around the "js-cookie" library.
 *
 * @see https://github.com/js-cookie/js-cookie
 */
export default class Cookie extends Singleton {
    get(name) {
        return BaseCookie.get(name);
    }

    set(name, value, options) {
        return BaseCookie.set(name, value, options);
    }

    remove(name, options) {
        return BaseCookie.remove(name, options);
    }
}
