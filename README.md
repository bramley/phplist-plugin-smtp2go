# SMTP2GO Plugin #

## Description ##

This plugin sends emails through SMTP2GO using its API.

## Installation ##

### Dependencies ###

This plugin is for phplist 3.3.0 or later and requires php version 5.4 or later and the curl extension.

It also requires the Common Plugin version 3.7.0 or later to be installed.
You should install or upgrade to the latest version. See https://github.com/bramley/phplist-plugin-common

### Set the plugin directory ###
The default plugin directory is `plugins` within the admin directory.

You can use a directory outside of the web root by changing the definition of `PLUGIN_ROOTDIR` in config.php.
The benefit of this is that plugins will not be affected when you upgrade phplist.

### Install through phplist ###
The recommended way to install is through the Plugins page (menu Config > Manage Plugins) using the package
URL `https://github.com/bramley/phplist-plugin-smtp2go/archive/master.zip`.
The installation should create

* the file Smtp2GoPlugin.php
* the directory Smtp2GoPlugin

## Usage ##

For guidance on using the plugin see the plugin's page within the phplist documentation site <https://resources.phplist.com/plugin/smtp2go>

## Support ##

Please raise any questions or problems in the user forum <https://discuss.phplist.org/>.

## Version history ##

    version         Description
    1.1.1+20190213  Ensure that multi-curl calls complete
    1.1.0+20170929  Allow concurrent requests
    1.0.0+20170717  Initial version
