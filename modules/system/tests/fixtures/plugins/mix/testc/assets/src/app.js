const missing = require('some-missing-package');

(() => {
    for (let i = 0; i <= 100; i++) {
        if ([2, 3].every((p) => i % p)) {
            missing.log(i);
        }
    }
})();
