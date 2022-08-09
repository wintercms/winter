/* eslint-disable no-plusplus, no-console */
(() => {
    for (let i = 0; i <= 100; i++) {
        if ([2, 3].every((p) => i % p)) {
            console.log(i);
        }
    }
})();
