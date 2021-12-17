#!/bin/bash
#environment needs some infos about pathes - keep line! ("." means "source")
. $( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )/get-drush-include-path.sh

drush sset system.maintenance_mode 1
drush locale-check && drush locale-update && drush cr
drush cim --yes
drush cache-rebuild
drush updatedb --yes

#h5p-fullscreen-fix when embedded as "div"
sed -i "s/var first, eventName = (H5P.fullScreenBrowserPrefix === 'ms' ? 'MSFullscreenChange' : H5P.fullScreenBrowserPrefix + 'fullscreenchange');/var first, eventName = (H5P.fullScreenBrowserPrefix === 'ms' ? 'MSFullscreenChange' : (H5P.fullScreenBrowserPrefix === 'moz'?'fullscreenchange' : H5P.fullScreenBrowserPrefix + 'fullscreenchange'));/" /modules/contrib/h5p/vendor/h5p/h5p-core/js/h5p.js

echo ">>>>>>>>>>>>> please check, if h5p-interactive video fullscreen-toggle works."
drush cache-rebuild

drush sset system.maintenance_mode 0
