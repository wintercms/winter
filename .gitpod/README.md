# Winter CMS on Gitpod

Winter CMS now supports the [Gitpod.io](https://gitpod.io) service to provide near-instant development and testing environments for Winter CMS.

This service allows you to check out the Winter CMS codebase at any commit, any branch or any pull request and be given a full Visual Studio Code environment that is completely configured and bootstrapped to run Winter CMS immediately.

Each instance contains the following:

- Winter CMS with the [DebugBar plugin](https://github.com/wintercms/wn-debugbar-plugin).
- VSCode.
- MySQL 5.7.
- PHP 7.4 with all required extensions.
- PHP Xdebug extension.
- Composer 2.
- [MailHog service](https://github.com/mailhog/MailHog/) for capturing emails.

To use this service, you will need an account on Gitpod - one can easily be created by using your GitHub login. You will receive 50 hours per month free for use on Gitpod, but can opt to increase your hours (or even get unlimited hours) by [purchasing a higher plan](https://www.gitpod.io/pricing) on Gitpod.

## Creating a Gitpod instance

There are several ways to create a Gitpod instance of Winter CMS:

- Use one of the **Open in Gitpod** button, which will be available in the README of Winter CMS, as well as any pull request submitted to Winter CMS.
- Install the [Gitpod extension](https://www.gitpod.io/docs/browser-extension#browser-extension) for Chrome or Firefox, which provides a **Gitpod** button in GitHub.
- Manually create an instance by copying a GitHub address within the Winter CMS repo, and prefixing the address with `https://gitpod.io/#/`

The Gitpod instance may take a minute or two to boot up if it has not been pre-built.

> **Note:** For brevity, Gitpod instances have the initial admin account set to **admin / admin** as the username and password to login. If you intend to share the URL, we recommend you change this password.

## Accessing the services

The Gitpod instance is set-up to boot all necessary services and then provides two web-facing services - the Winter CMS install itself, which is run on port 8000, and MailHog, which is run on port 8025.

You can click the **Ports** section in the status bar of VSCode, which will take you to the available ports, and click on one of these ports to view the actions for the port. The "globe" icon will open up a special URL which will access the service on that port. We automatically load up Winter CMS on boot in a new tab.

### MySQL

By default, MySQL only runs locally within the Gitpod instance, and cannot be connected to from the outside. However, you can use the [Gitpod Local Companion](https://www.gitpod.io/blog/local-app) service to tunnel into the running Gitpod instance and access its services on your own computer.

Install the app for your OS, then run `gitpod-local-companion-[darwin|linux|windows]` to set up the tunneling service. For MySQL, this will make the database available on port 3306 on your computer.

You can then connect to it using any MySQL management program of your choice.

## Config files

By default, the Winter CMS Gitpod instance will use `php artisan winter:env` to create an `.env` file that will contain your config. Because this command rewrites the main config files in the `config` directory, which will appear as changes in Git, we mark these files as "unwatched" in Git so that they are not committed to GitHub.

If you are editing a pull request that does contain config changes that you wish to include in the PR, you can use the `.gitpod/gitpod-show-config` helper script inside your Gitpod instance to make these files appear in the Git changes.

## Debugging

Debugging Winter CMS in Gitpod is super simple - all necessary setup has already been done! You can access the Debugging tab in VSCode and press start on the debugging tool to use breakpoints within your code. Note that Gitpod does impose a time limit on responses from the web server, so you may find that using the debugging will result in timeouts when viewing your Winter CMS instance, however, debugging should still continue without issue.
