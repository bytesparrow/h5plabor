#!/bin/bash
#environment needs some infos about pathes - keep line! ("." means "source")
. $( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )/get-drush-include-path.sh

echo "UpGRADING System. Don't forget to PUSH the changes!"


drush sset system.maintenance_mode 1
drush locale-check && drush locale-update && drush cr
drush updatedb --yes
drush cex --yes
drush cache-rebuild


#h5p-fullscreen-fix when embedded as "div"

echo ">>>>>>>>>>>>> please check, if h5p-interactive video fullscreen-toggle works."
drush cache-rebuild

drush sset system.maintenance_mode 0
