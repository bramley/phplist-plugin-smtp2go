# SMTP2GO Plugin #

## Description ##

This plugin sends emails through SMTP2GO using its API.

## Installation ##

### Dependencies ###

This plugin is for phplist 3.3.0 or later and requires php version 5.4 or later.

It also requires the php curl extension to be installed.

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
    1.0.0+20170717  Initial version
