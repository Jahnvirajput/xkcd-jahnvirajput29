#!/bin/bash
# This script should set up a CRON job to run cron.php every 24 hours.
# You need to implement the CRON setup logic here.


CRON_JOB="0 10 * * * php $(pwd)/cron.php"
(crontab -l; echo "$CRON_JOB") | sort -u | crontab -
echo "CRON job scheduled to run every 24 hours."
