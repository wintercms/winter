# Snowboard Framework Testing Suite

The files in this directory form the testing suite for the new Winter JavaScript framework - Snowboard. You must install
all Node dependencies in order to run the testing suite:

```bash
cd tests/js
npm install
```

You can then run the tests by using the following command:

```bash
npm run test
```

Please note that the tests are run against the "built" versions of Snowboard and its plugins, in order to test
the exact same functionality that would be delivered to the end user. We do this by leveraging the `JSDOM` library to
simulate an entire HTML document.

Therefore, you must compile a new build if you make any changes and wish to run the tests:

```bash
php artisan mix:compile --package snowboard
```

You can also watch the framework for any changes, which will automatically run a build after a change is made. This will
make development and testing in parallel much quicker:

```bash
php artisan mix:watch snowboard
```
