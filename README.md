# twofactor_webeid

This installable [Nextcloud App](https://docs.nextcloud.com/server/latest/admin_manual/apps_management.html#apps-management) provides 2-factor authentication for [Web-eID](https://web-eid.eu/) solution.

> Currently it is supposed to run with custom [JavaCard applet](https://github.com/Muzosh/web-eid-javacard-applet), but with easy implementation of authenticate function it also works with other Web-eID compatible cards.

## Usage

1. Clone this repository into `<nextcloud-path>/apps` directory
2. Install [authtoken validation library](https://github.com/Muzosh/web-eid-authtoken-validation-php) by `composer install` (check `composer.json` for require details)
3. Check function `lib/Service/WebEidService.php:authenticate()` and implement other authentication mechanism if needed
4. Use [OCC](https://docs.nextcloud.com/server/latest/admin_manual/configuration_server/occ_command.html?highlight=occ#using-the-occ-command) command to enable this app for specific user:
   * `occ twofactorauth:enable <userID> twofactor_webeid`
5. After specified `<userID>` logins with username+password, he is asked to insert card into reader and click on Authenticate button
6. Web-eID authenticaton process is executed

<!-- # (INGORE REST OF README - building the app is used for Nextcloud App Store publishing)
## Building the app

The app can be built by using the provided Makefile by running:

    make

This requires the following things to be present:

* make
* which
* tar: for building the archive
* curl: used if phpunit and composer are not installed to fetch them from the web
* npm: for building and testing everything JS, only required if a package.json is placed inside the **js/** folder

The make command will install or update Composer dependencies if a composer.json is present and also **npm run build** if a package.json is present in the **js/** folder. The npm **build** script should use local paths for build systems and package managers, so people that simply want to build the app won't need to install npm libraries globally, e.g.:

**package.json**:

```json
"scripts": {
    "test": "node node_modules/gulp-cli/bin/gulp.js karma",
    "prebuild": "npm install && node_modules/bower/bin/bower install && node_modules/bower/bin/bower update",
    "build": "node node_modules/gulp-cli/bin/gulp.js"
}
```

## Publish to App Store

First get an account for the [App Store](http://apps.nextcloud.com/) then run:

    make && make appstore

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

## Running tests

You can use the provided Makefile to run all tests by using:

    make test

This will run the PHP unit and integration tests and if a package.json is present in the **js/** folder will execute **npm run test**

Of course you can also install [PHPUnit](http://phpunit.de/getting-started.html) and use the configurations directly:

    phpunit -c phpunit.xml

or:

    phpunit -c phpunit.integration.xml

for integration tests -->
