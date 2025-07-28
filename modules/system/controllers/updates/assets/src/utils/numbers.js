export const numberFormat = (n, decimals, dec, sep) => {
    // eslint-disable-next-line no-param-reassign
    n = parseFloat((`${n}`).replace(/[^0-9+\-Ee.]/g, ''));

    if (Number.isNaN(n)) {
        return 0;
    }

    // eslint-disable-next-line no-param-reassign
    sep = sep || ',';
    // eslint-disable-next-line no-param-reassign
    dec = dec || '.';
    // eslint-disable-next-line no-param-reassign
    decimals = decimals || 0;

    let s = '';

    s = (
        decimals
            // eslint-disable-next-line no-shadow
            ? ((n, decimals) => {
                const k = 10 ** decimals;
                return `${Math.round(n * k) / k}`;
            })(n, decimals)
            : `${Math.round(n)}`
    ).split('.');

    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < decimals) {
        s[1] = s[1] || '';
        s[1] += new Array(decimals - s[1].length + 1).join('0');
    }
    return s.join(dec);
};

export const counterNumber = (n) => {
    if (n > 1000) {
        return `${Math.round((n / 1000) * 10) / 10}k`;
    }

    return n;
};
