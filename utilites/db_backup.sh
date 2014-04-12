#!/bin/sh
filename = rez_$(date +%Y%m%d_%H%M%S).fbk; 
/usr/bin/gbak -b -v /var/www/realestate/database/renta.fdb \
/var/www/realestate/database/backups/rez_$(date +%Y%m%d_%H%M%S).fbk \
-user 'SYSDBA' -password 'atybrc';
