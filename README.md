# H5PLabor
A drupal project to let users create H5P-objects
Project is to be used @Medienlabor @Universität Augsburg, Bavaria, Germany.

Provided in the hope it is usefull without any warranty.

# INSTALLATION

some steps are required for a full installation

### Prerequisits


Have a webspace with PHP >= 7.4 and a database
and ssh-access.

Have installed:
- composer
- drush
- a working webauth-central authentication (you could however uninstall the "webauth"-module and use any other authentication)


Follow the steps EXACTLY in the given order.

Create or use any empty directory. Then:

- git clone https://github.com/null/h5plabor.git .

- cd web
- composer install

Point your vhost to the "web"-directory

Visit your new website

Install "standard" (not: minimal)

Goto: /user and add the role "administrator" to your useraccount

Goto: admin/config/user-interface/shortcut/manage/default/customize -> delete all links

- drush cset system.site uuid b61929b4-12e6-4382-86a8-624959b78517
- drush cset language.entity.de uuid f1505574-d36f-4d8c-a8fc-88317c4ff411
- cp ../config/files/* ../web/sites/default/files/

(temporarily) change the permission of folder sites/default so that you can write in it

Copy the file default.site_environment.php:
- cp sites/default/default.site_environment.php  sites/default/site_environment.php 
And adjust its content


Edit: sites/default/settings.php

```
$settings['config_sync_directory'] = '../config/sync';
require(dirname(__FILE__).'/site_environment.php');
switch($site_environment){
	case 'dev':
	 $config['config_split.config_split.config_ui']['status'] = TRUE;
	 $config['config_split.config_split.development']['status'] = TRUE;
	break;
	case 'staging':
	case 'production':
	break;

}
```
Create the webauth-secret keys (see FILE: webauth-module/README.txt) and store the keys in settings.php


Create the trusted_host_patterns-setting as proposed in admin/reports/status


revert the permissionchange of folder sites/default

- drush cim 
-- drush locale-check && drush locale-update && drush cr

Goto: admin/appearance/settings/barrio_h5plabor
Click "save"


Goto: /node/add/page
Create: "Über" Seite ("About" page)


Import blocks: 
- drush import-all 
(prompt answer is always "1")


Goto: admin/content/h5p
Install desired libraries from h5p.org

Change InteractiveVideo to "div" instead of "iframe":
- drush php-eval "_h5plabor_set_interactiveVideoToDivEmbed();"

Do another reimport
- drush cim


Goto: admin/appearance/settings/barrio_h5plabor
Click "save"

Create Cronjob (for example every hour):

cd ~/web/web && php  ~/.composer/vendor/bin/drush.php cron

**Your Site should be done.**

Optional (for dev)

Create Cronjob to automatically keep Drupal up to date (and have yourself informed if an update was made):

bash web/scripts/drupal-auto-update.sh 2>&1 > /dev/null | bash web/scripts/mail_if_output.sh your@personalema.il
(for example every 5 hours: */5 * * * *)
