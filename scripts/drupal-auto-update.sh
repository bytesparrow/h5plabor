#!/bin/bash

# 
##############
#   HOWTO
##############
#
# a) Script testen aus dem script-directory: /web/scripts$ bash drupal-auto-update.sh
# b) cronjob einrichten: 
#     b1) 0 */6 * * * bash --SITENAME--/web/scripts/drupal-auto-update.sh
#     b2) mit Mailbenachrichtigung:0 */6 * * * bash --SITENAME--/web/scripts/drupal-auto-update.sh   2>&1 > /dev/null  |  bash --SITENAME--/web/scripts/mail_if_output.sh RECIPIENT@HOST.COM,RECIPIENT@HOSTER.COM
# c1) gibt es updates, werden dies automatisch eingespielt
# c2) bei Verwendung von b2) erhalten die Empfänger auch eine E-Mail.
# 


cd "$(dirname "$0")"

#environment needs some infos about pathes - keep line! ("." means "source")
. $( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )/get-drush-include-path.sh


cd ../web/
#drush refresh cache, send output to null
drush rf   >  /dev/null 2>&1  ||  ((>&2 printf "DRUSH COMMAND NOT FOUND\n\n") && exit 1);# pm-refresh OR warning if drush not found

#composer-StdErr geht in die message-variable
#UPDATE_MESSAGE=`COMPOSER_MEMORY_LIMIT=-1 composer update  2> /dev/null`
UPDATE_MESSAGE=$(COMPOSER_MEMORY_LIMIT=-1 composer update 2>&1)

#changed-variable: hat sich was geaenderT?
CHANGED_DRUP=`echo $UPDATE_MESSAGE |  grep "Updating drupal"`

 


if [ "$CHANGED_DRUP" = ""  ]   ;
then
  #printf "\n\nThats good! No code-change\n"
 exit 0;
else
 #printf "oh-oh."

 bash_user=$(whoami)
#write to stdErr. stdErr will be sent via email.
 (>&2 printf "H5PLabor $bash_user was updated!\n\n")
 (>&2 printf "Pfad: $PWD \nNotice: $UPDATE_MESSAGE\n")
 
 exit 1;
fi
