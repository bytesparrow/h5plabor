#!/bin/bash
#environment needs some infos about pathes - keep line! ("." means "source")
. $( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )/get-drush-include-path.sh

drush sset system.maintenance_mode 1
drush locale-check && drush locale-update && drush cr
drush cim
drush cache-rebuild
drush updatedb --yes

drush sset system.maintenance_mode 0
