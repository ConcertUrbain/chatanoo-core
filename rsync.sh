#!/bin/bash
rsync -avz -e ssh . "root@ns368978.ovh.net:/var/www/vhosts/dring93.org/subdomains/ws/httpdocs/" --exclude-from 'rsync.exclude'