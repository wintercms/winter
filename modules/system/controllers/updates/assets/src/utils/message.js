const makeLine = (entry) => {
    let line = entry;
    let search;
    ['INFO', 'ERROR'].forEach((status) => {
        if (line.indexOf(status) === 0) {
            line = `
                <span class="${status === 'INFO' ? 'text-green-500' : 'text-red-500'}">${status}</span>
                <span>${line.substring(status.length + 1)}</span>
            `;
        }
    });

    if (line.indexOf('<span>') > -1) {
        return `
            <tr class="border-spacing-y-3">
                <td colspan="3">
                    <div class="py-4">${line}</div>
                </td>
            </tr>
        `;
    }

    search = line.match(/^[\d].*: /);

    let heading = null;
    if (search) {
        heading = `<span class="text-blue-400 pl-4">${search[0].replace(':', '').trim()}</span>`;
        line = line.substring(search[0].length + 1);
    }

    line = line.trim();

    search = line.match(/\.*?[\d][\w]*DONE$/);

    let ending = null;
    if (search) {
        line = `<div class="pl-3">${line.substring(0, line.length - search[0].length)}</div>`;
        ending = `
            <span class="text-orange-500 ml-auto">
                ${search[0].match(/\.*?[\d][\w]*DONE$/)[0].replace(/\.*/, '').replace('DONE', '')}
            </span>
        `;
    }

    return `
        <tr>
            <td class="py-2">${heading}</td>
            <td class="py-2">${line}</td>
            <td class="text-right py-2">${ending}</td>
        </tr>
    `;
};


export const prepareMessage = (str) => {
    return `
        <div class="p-6 mb-10 -mt-1 rounded-3xl shadow-sm bg-blue-100 text-gray-900">
            <table class="font-mono !border-separate !border-spacing-2 -mt-3">
                ${str.split('\n').filter((line) => (line.indexOf('FINISHED:') === 0 ? false : !!line)).map(makeLine).join("\n")}
            </table>
        </div>
    `;
}
