#!/bin/bash
rsync -avzpr --delete -e ssh . "root@ns368978.ovh.net:/var/www/vhosts/chatanoo.org/core/ws/preprod/" --exclude-from 'rsync.exclude'