import { request } from './winter-request';

export default (installKey) => {
    // eslint-disable-next-line no-undef
    $.popup({
        size: 'large updates-app installer-popup',
        content: `
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Installing...</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
            </div>
        `,
    });

    const popup = document.querySelector('.installer-popup .modal-body');
    const prepareMessage = (str) => `<div class="p-2 mb-2 rounded bg-gray-800 text-gray-100">${
        str.split('\n').filter((line) => (line.indexOf('FINISHED:') === 0 ? false : !!line)).map((entry) => {
            let line = entry;
            ['INFO', 'ERROR'].forEach((status) => {
                if (line.indexOf(status) === 0) {
                    line = `<span class="${status === 'INFO' ? 'text-green-500' : 'text-red-500'}">${status}</span> ${line.substring(status.length + 1)}`;
                }
            });

            let search = line.match(/^[\d].*: /);
            if (search) {
                line = `<span class="text-blue-400">${search[0].replace(':', '').trim()}</span>:${line.substring(search[0].length + 1)}`;
            }

            search = line.match(/\.*?[\d][\w]*DONE$/);
            if (search) {
                line = `${line.substring(0, line.length - search[0].length)} <span class="text-yellow-600">${search[0].match(/\.*?[\d][\w]*DONE$/)[0].replace(/\.*/, '').replace('DONE', '')}</span>`;
            }

            return `<pre>${line}</pre>`;
        }).join('\n')
    }</div>`;

    const checkStatus = () => {
        request('onInstallProductStatus', {
            data: {
                install_key: installKey,
            },
            success: (statusResponse) => {
                popup.innerHTML = prepareMessage(statusResponse.data);

                if (!statusResponse.done) {
                    return setTimeout(checkStatus, 500);
                }

                const store = JSON.parse(localStorage.winterInstalling);
                store.splice(store.indexOf(installKey), 1);
                localStorage.winterInstalling = JSON.stringify(store);

                return null;
            },
        });
    };
    checkStatus();
};
