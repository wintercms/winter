<p align="center">
    <img src="https://github.com/wintercms/winter/raw/develop/modules/backend/assets/images/wordmark.png?raw=true" alt="Winter CMS Logo" width="100%" />
</p>

[Winter](https://wintercms.com) is a Content Management System (CMS) and web platform whose sole purpose is to make your development workflow simple again. It was born out of frustration with existing systems. We feel building websites has become a convoluted and confusing process that leaves developers unsatisfied. We want to turn you around to the simpler side and get back to basics.

Winter's mission is to show the world that web development is not rocket science.

![Stable Build](https://github.com/wintercms/winter/workflows/Tests/badge.svg?branch=develop)
[![License](https://poser.pugx.org/wintercms/winter/license.svg)](https://packagist.org/packages/wintercms/winter)
[![Discord](https://img.shields.io/discord/816852513684193281?label=discord&style=flat-square)](https://discord.gg/D5MFSPH6Ux)

## Installing Winter

Instructions on how to install Winter can be found at the [installation guide](https://wintercms.com/docs/setup/installation).

### Quick Start Installation

For advanced users, run this in your terminal to install Winter from command line:

```shell
composer create-project wintercms/winter example.com "dev-develop"
```

If you plan on using a database, run this command inside the application directory.

```shell
php artisan winter:install
```

## Learning Winter

The best place to learn Winter is by [reading the documentation](https://wintercms.com/docs), [watching some screencasts](https://octobercms.com/support/topic/screencast) or [following some tutorials](https://octobercms.com/support/articles/tutorials).

You may also watch these introductory videos for [beginners](https://vimeo.com/79963873) and [advanced users](https://vimeo.com/172202661).

## Development Team

Winter was forked from October CMS in March 2021 due to a difference in open source management philosophies between the core maintainer team and the two founders of October.

The development of Winter is lead by [Luke Towers](https://luketowers.ca/), along with many wonderful people that dedicate their time to help support and grow the community.

<table>
  <tr>
    <td align="center"><a href="https://github.com/luketowers"><img src="https://avatars.githubusercontent.com/u/7253840?v=3" width="100px;" alt="Luke Towers"/><br /><sub><b>Luke Towers</b></sub></a></td>
    <td align="center"><a href="https://github.com/bennothommo"><img src="https://avatars.githubusercontent.com/u/15900351?v=3" width="100px;" alt="Ben Thomson"/><br /><sub><b>Ben Thomson</b></sub></a></td>
    <td align="center"><a href="https://github.com/mjauvin"><img src="https://avatars.githubusercontent.com/u/2013630?v=3" width="100px;" alt="Marc Jauvin"/><br /><sub><b>Marc Jauvin</b></sub></a></td>
    <td align="center"><a href="https://github.com/jaxwilko"><img src="https://avatars.githubusercontent.com/u/31214002?v=4" width="100px;" alt="Jack Wilkinson"/><br /><sub><b>Jack Wilkinson</b></sub></a></td>
  </tr>
</table>

## Foundation library

The CMS is built on top of the wildly-popular [Laravel](https://laravel.com) PHP framework, with the in-house [Storm](https://github.com/wintercms/storm) Library as a buffer between the Laravel Framework and the CMS project to minimize breaking changes and improve stability.

## Contact

You can communicate with us using the following mediums:

* [Follow us on Twitter](https://twitter.com/usewintercms) for announcements and updates.
* [Join us on Discord](https://discord.gg/D5MFSPH6Ux) to chat with us.

## Contributing

Before sending or reviewing Pull Requests, be sure to review the [Contributing Guidelines](https://github.com/wintercms/.github/blob/master/CONTRIBUTING.md) first.

### Coding standards

Please follow the following guides and code standards:

* [PSR 4 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

### Code of Conduct

In order to ensure that the Winter CMS community is welcoming to all, please review and abide by the [Code of Conduct](https://github.com/wintercms/.github/blob/master/CODE_OF_CONDUCT.md).

## License

The Winter CMS platform is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Security Vulnerabilities

Please review [our security policy](https://github.com/wintercms/winter/security/policy) on how to report security vulnerabilities.
