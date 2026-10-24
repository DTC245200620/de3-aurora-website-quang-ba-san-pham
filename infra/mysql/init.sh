#!/bin/sh
set -eu
export MYSQL_PWD="$(cat /run/secrets/db_root_password)"
exporter_password="$(cat /run/secrets/exporter_password)"
mysql -uroot <<SQL
CREATE USER IF NOT EXISTS 'exporter'@'%' IDENTIFIED BY '${exporter_password}';
GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'exporter'@'%';
SQL
