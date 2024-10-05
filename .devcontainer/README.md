# Welcome to the Winter development environment

<p align="center">
    <img src="https://github.com/wintercms/winter/raw/develop/.github/assets/Github%20Banner.png?raw=true" alt="Winter CMS Logo" width="100%" style="max-width: 600px" />
</p>

This development environment container sets up a fully-functional installation of Winter CMS, running on Apache 2 with PHP 8.3, and makes it simple to start working with Winter CMS in VSCode, PHPStorm and online code-editing suites such as GitHub Codespaces.

If you opted to use the `bootstrap-winter` feature, which is enabled by default, Winter CMS will be automatically configured and an administrator account will be generated with the credentials **admin / admin** for you to quickly sign in. It is recommended once you have done so that you change this password immediately.

The following plugins and themes will be installed automatically with this feature:

- Workshop theme (https://github.com/wintercms/wn-workshop-theme)
- Pages plugin (https://github.com/wintercms/wn-pages-plugin)
- Blog plugin (https://github.com/wintercms/wn-blog-plugin)
- Test plugin (https://github.com/wintercms/wn-test-plugin)

## Using this environment

When this environment is built, the Apache 2 service is automatically started, with the root folder of the Winter project being used. A preview of the website will be opened immediately - if you do not see this, you can open the **Ports** tab in VSCode to view the URL generated for viewing the project.

XDebug is enabled by default, and allows you to quickly use step debugging. It will be available in the **Debug** tab of VSCode or similar screen in other IDEs.

By default, when using the `bootstrap-winter` feature, changes to certain folders and locations will be ignored by Git to keep the change list clean. This includes the `plugins` and `themes` folders, the `config/app.php` file and the `composer.json` file in the root folder. If you wish to use this environment for your own projects, it is recommended that you do not use this feature. Please see the **Using in your own projects** section below for using this environment outside of Winter development.

## Environment platform

The following software is installed in this environment.

- Apache 2.4
- PHP 8.3 with the following extensions:
  - `intl`
  - `gd`
  - `xdebug`
- Composer
- NodeJS 22 (including `npm`)
- Git

## Using in your own projects

You may use this development environment for your own projects, making it a great starting point to hit the ground running with Winter. It is recommended that you *disable* the `bootstrap-winter` feature when using this environment for your own projects.

You may disable this feature by modifying the `.devcontainer/devcontainer.json` file before running the container and commenting out the feature:

```json5
        "ghcr.io/devcontainers/features/git:1": {},
        "./local-features/apache-config": "latest",
        // Comment the following feature if you wish to bootstrap and configure Winter manually (ie. you wish to use this for your own project)
        //"./local-features/bootstrap-winter": "latest"
    },
    "overrideFeatureInstallOrder": [
```

If this feature is disabled, you must bootstrap your project manually. This includes:

- Downloading the Composer dependencies.
- Generating the configuration for the project, either as an `.env` file or in the `config` folder.
- Finally, Running the database migrations.

You may view the `.devcontainer/local-features/bootstrap-winter/bootstrap.sh` file to see how we bootstrap Winter, and run these commands manually. You will only need to do this once per project container.

If you wish to mount your own volumes, use your own databases or any other complex usages, please review the [Docker documentation](https://docs.docker.com/) to set this up on the container.

## Troubleshooting

### Preview website missing styles / assets on Codespaces

By default, ports that are forwarded in Codespaces are private by default. While we have tried to fix this automatically in the Winter bootstrap process by making the port public through the GitHub CLI, it unfortunately is not consistently applied.

If you find that your preview website is missing assets or styling, open the **Ports** tab by opening the Action Palette in Codespaces (`F1`) and using the **View: Toggle Ports** action. Right click on the **Preview Winter installation** port, right click on it and choose **Port Visiblity -> Public**. This should resolve the issue.
