[![Build Status](https://travis-ci.org/avvertix/wp-unittest-tools.svg?branch=master)](https://travis-ci.org/avvertix/wp-unittest-tools)

# Wordpress UnitTest tools

This project serves as command line to enable the download of the Wordpress Unittests helper functions and setup the environment for executing your plugin Unit Tests


This suite is heavily inspired by the [WordPress REST API plugin](https://github.com/WP-API/WP-API) [install-wp-test.sh](https://github.com/WP-API/WP-API/blob/develop/bin/install-wp-tests.sh) shell script. 
That script was a great source to learn how to unittest Wordpress plugins.


## What?

This toolset, available also in a phar version, downloads the Wordpress release, the Unit Tests includes and assist you during the execution configuration of the tests 


## Usage

*step 1*

In order to configure the required dependencies neeeded to execute a Wordpress based unit test execute:

```
[php] ./bin/wptesttools.phar configure [--db=] [--user=] [--pass=] [--host=] [wp-version]
```

**Available parameters**


| parameter    | purpose |
| ------------ | --------------------------- |
| `wp-version` | The Wordpress version to use for the tests (e.g. 4.4.2). Default value if not specified is `latest` therefore the latest release will be used |


**Available options**

| option | default value | purpose                |
| ------ | ------------- | ---------------------- |
| `db`   | `wptest`      | The test database name |
| `user` | `wptest`      | The test database username |
| `pass` | `wptest`      | The test database password |
| `host` | `localhost`   | The test database host |


*step 2*

Now you can create a stub of the `phpunit.xml` file and the bootstrap to be loaded when running the unit tests.

To create the stub files launch

```
[php] ./bin/wptesttools.phar stub [--plugin=plugin.php] [test-folder]
```

| parameter    | purpose |
| ------------ | --------------------------- |
| `test-folder` | The folder where you will place the unit-tests. Default `tests` |

| option   | description                |
| -------- | ---------------------- |
| `plugin` | You can specify the file that will be readed by Wordpress to load the plugin |


The boostrap will enable the usage of an environment variable called `PLUGIN_FILE` you can use to specify the main file that will load the plugin in Wordpress.


## Full Commands List


## `downloadwp` Command

Download a Wordpress version and extracts into `./tmp/wordpress/`

```
[php] ./bin/wptesttools.phar downloadwp [wp-version]
```

| parameter    | purpose |
| ------------ | --------------------------- |
| `wp-version` | The Wordpress version to use for the tests (e.g. 4.4.2). Default value if not specified is `latest` therefore the latest release will be used |


## `installwp` Command

Download the Wordpress unit test inclusions and the example wp-config to be used wjen running the tests. The folder used is `./tmp/`

```
[php] ./bin/wptesttools.phar installwp [wp-version]
```

| parameter    | purpose |
| ------------ | --------------------------- |
| `wp-version` | The Wordpress version to use for the tests (e.g. 4.4.2). Default value if not specified is `latest` therefore the latest release will be used |


## `stub` Command

Creates the `phpunit.xml` and a `boostrap.php` file that define the uni tests configuration and the loader of the dependencies for running the tests.

```
[php] ./bin/wptesttools.phar stub [--plugin=plugin.php] [test-folder]
```

| parameter    | purpose |
| ------------ | --------------------------- |
| `test-folder` | The folder where you will place the unit-tests. Default `tests` |

| option   | description                |
| -------- | ---------------------- |
| `plugin` | You can specify the file that will be readed by Wordpress to load the plugin |
| `phpunit` | You can specify the PHPUnit configuration file name (default `phpunit.xml`) |


the phpunit.xml file will be created in the current directory from which the wptesttools binary is executed, while the boostrap.php will be created in the `test-folder`.

**calling multiple times the `stub` command will overwrite existing files**
