#!/bin/bash
###################################
this script will
    - pull the current version from git
    - composer install the current version
    - execute update_master script
###################################
#environment needs some infos about pathes - keep line! ("." means "source")
. $( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )/get-drush-include-path.sh


drush sset system.maintenance_mode 1
git pull
cd ../web
composer install

bash ../scripts/update_master.sh