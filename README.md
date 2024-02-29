<p align="center">
    <img src="https://github.com/wintercms/winter/raw/develop/.github/assets/Github%20Banner.png?raw=true" alt="Winter CMS Logo" width="100%" />
</p>

[Winter](https://wintercms.com) is a free, open-source content management system based on the [Laravel](https://laravel.com) PHP framework. Developers and agencies all around the world rely upon Winter for its quick prototyping and development, safe and secure codebase and dedication to simplicity.

No matter how large or small your project is, Winter provides a rich development environment, regardless of your level of experience.

[![Version](https://img.shields.io/github/v/release/wintercms/winter?sort=semver&style=flat-square)](https://github.com/wintercms/winter/releases)
[![Tests](https://img.shields.io/github/actions/workflow/status/wintercms/winter/tests.yml?branch=develop&label=tests&style=flat-square)](https://github.com/wintercms/winter/actions)
[![License](https://img.shields.io/github/license/wintercms/winter?label=open%20source&style=flat-square)](https://packagist.org/packages/wintercms/winter)
[![Discord](https://img.shields.io/discord/816852513684193281?label=discord&style=flat-square)](https://discord.gg/D5MFSPH6Ux)
[![RINGER](https://www.ringerhq.com/images/get-support-on-ringer.svg)](https://www.ringerhq.com/i/wintercms/winter)

[![Open in Gitpod](https://gitpod.io/button/open-in-gitpod.svg)](https://gitpod.io/#https://github.com/wintercms/winter)

## Installing Winter

Winter can be installed in several ways for both new users and experienced developers - see our [Installation page](https://wintercms.com/install) for more information.

### Quick start with Composer

For advanced users, run the following command in your terminal to install Winter via Composer:

```shell
composer create-project wintercms/winter example.com "dev-develop"
```

Run the following command with the folder created by the previous command to generate an environment file which will contain your configuration settings:

```shell
php artisan winter:env
```

After configuring your installation, you can run the following command to run the database migrations and automatically create an administrator account with the username `admin`. The password of this account will be automatically generated and displayed in your terminal.

```shell
php artisan winter:up
```

## Learning Winter

The best place to learn Winter is by [reading the documentation](https://wintercms.com/docs) or [following some tutorials](https://wintercms.com/blog/category/tutorials). You can also join the maintenance team and our active community on [Discord](https://discord.gg/D5MFSPH6Ux) who are always willing to help out with questions.

## Development team

Winter was forked from October CMS in March 2021 due to a difference in open source management philosophies between the core maintainer team and the two founders of October.

The development of Winter is lead by [Luke Towers](https://luketowers.ca/), along with many wonderful people that dedicate their time to help support and grow the community. The [Frostbyte Foundation](mailto:hello@frostbytefoundation.org) provides an organisational backing for the project and the continued development of Winter, its plugins and themes and its ecosystem.

<table>
  <tr>
    <td align="center"><a href="https://github.com/luketowers"><img src="https://avatars.githubusercontent.com/u/7253840?v=3" width="100px;" alt="Luke Towers"/><br /><sub><b>Luke Towers</b></sub></a></td>
    <td align="center"><a href="https://github.com/bennothommo"><img src="https://avatars.githubusercontent.com/u/15900351?v=3" width="100px;" alt="Ben Thomson"/><br /><sub><b>Ben Thomson</b></sub></a></td>
    <td align="center"><a href="https://github.com/mjauvin"><img src="https://avatars.githubusercontent.com/u/2013630?v=3" width="100px;" alt="Marc Jauvin"/><br /><sub><b>Marc Jauvin</b></sub></a></td>
    <td align="center"><a href="https://github.com/jaxwilko"><img src="https://avatars.githubusercontent.com/u/31214002?v=4" width="100px;" alt="Jack Wilkinson"/><br /><sub><b>Jack Wilkinson</b></sub></a></td>
  </tr>
</table>

## Foundation library

Winter is built on top of the wildly-popular [Laravel](https://laravel.com) framework for PHP, with the in-house [Storm](https://github.com/wintercms/storm) library as a buffer between the Laravel framework and the Winter project, to minimize breaking changes and improve stability.

## Getting in touch

You can get in touch with the maintainer team using the following mediums:

* [Follow us on Twitter](https://twitter.com/usewintercms) for announcements and updates.
* [Join us on Discord](https://discord.gg/D5MFSPH6Ux) to chat with us.

## Contributing

Before contributing issues or pull requests, be sure to review the [Contributing Guidelines](https://github.com/wintercms/.github/blob/master/CONTRIBUTING.md) first.

### Coding standards

Please follow the following guides and code standards:

* [PSR 4 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

### Code of conduct

In order to ensure that the Winter community is welcoming to all, please review and abide by the [Code of Conduct](https://github.com/wintercms/.github/blob/master/CODE_OF_CONDUCT.md).

## Sponsors

Winter CMS development is financially supported by the generosity of the following sponsors:

### Organizations

[![Froala logo](https://froala.com/wp-content/uploads/2019/10/froala.svg)](https://froala.com/wysiwyg-editor/)

Froala provides a perpetual, Enterprise license to Winter CMS which allows us and our users to use the Froala WYSIWYG Editor in Winter CMS powered projects.

Big thanks to our sponsors on OpenCollective:

- [FrogeHost](https://froge.host/?utm_source=wintercms)

### Individuals

Big thanks to our sponsors on OpenCollective:

- Orville

If you would like to have your name, company and link added to this list and support open-source development, feel free to make a donation to our [Open Collective](https://opencollective.com/wintercms).

## License

The Winter platform is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Security vulnerabilities

Please review [our security policy](https://github.com/wintercms/winter/security/policy) on how to report security vulnerabilities.
