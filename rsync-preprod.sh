#!/bin/bash
rsync -avzpr --delete -e ssh . "root@ns368978.ovh.net:/var/www/vhosts/dring93.org/preprod/ws/" --exclude-from 'rsync.exclude'
ssh root@ns368978.ovh.net "chown -R dring93:psacln /var/www/vhosts/dring93.org/preprod/ws/; chmod 777 -R /var/www/vhosts/dring93.org/preprod/ws/Application/logs/;"