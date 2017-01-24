#!/usr/bin/env bash
echo "Install project 'Essor Tickets' (Produced by ©ARTWEBIT)";
echo "---------------------------------------------------------------------";
BIN_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BASE_DIR=${BIN_DIR}/..
LOCAL_CONF_PATH="${BIN_DIR}/../config/local"
echo "Checking composer"
echo "---------------------------------------------------------------------";

composer global require "fxp/composer-asset-plugin:*"
composer install

echo "Checking required folders";
echo "---------------------------------------------------------------------";
if [ ! -d ${BASE_DIR}/application/migrations ] ; then
  mkdir ${BASE_DIR}/application/migrations
  echo 'Folder application/migrations was created.'
fi

if [ ! -d ${BASE_DIR}/web/assets ] ; then
  mkdir ${BASE_DIR}/web/assets
  chmod 0777 ${BASE_DIR}/web/assets
  echo 'Folder web/assets was created.'
fi

if [ ! -d ${BASE_DIR}/runtime ] ; then
  mkdir ${BASE_DIR}/runtime
  chmod 0777 ${BASE_DIR}/runtime
  echo 'Folder runtime was created.'
fi

echo "Checking requirements";
echo "---------------------------------------------------------------------";
${BIN_DIR}/requirements

echo "Checking local configs";
echo "---------------------------------------------------------------------";
if [ ! -f $LOCAL_CONF_PATH/db.php ] ; then

  RESULT=0
  while [[ $RESULT = 0 ]]; do
    # Get MySQL hostname
    MYSQL_HOST=
    while [[ $MYSQL_HOST = "" ]]; do
      echo -n "Please enter MySQL Hostname: "; read MYSQL_HOST
    done

    # Get MySQL dbname
    MYSQL_DBNAME=
    while [[ $MYSQL_DBNAME = "" ]]; do
      echo -n "Please enter MySQL Database name: "; read MYSQL_DBNAME
    done

    # Get MySQL user
    MYSQL_USER=
    while [[ $MYSQL_USER = "" ]]; do
      echo -n "Please enter MySQL User: "; read MYSQL_USER
    done

    # Get MySQL user
    MYSQL_PASSWORD=
    while [[ $MYSQL_PASSWORD = "" ]]; do
      echo -n "Please enter MySQL Password: "; read MYSQL_PASSWORD
    done
    echo "";
    echo "---------------------------------------------------------------------";
    echo "Summary:"
    echo "---------------------------------------------------------------------";
    echo "MySQL Hostname: $MYSQL_HOST";
    echo "MySQL Database name: $MYSQL_DBNAME";
    echo "MySQL User: $MYSQL_USER";
    echo "MySQL Password: $MYSQL_PASSWORD";
    echo "---------------------------------------------------------------------";
    echo "";
    READ_RESULT=
    while [[ $READ_RESULT = "" ]]; do
      echo -n "Please check your entries! Continue?(yes/no) "; read READ_RESULT
      echo "---------------------------------------------------------------------";
      echo "";
      if [[ $READ_RESULT = "yes" ]] ; then
        RESULT=1;
      else
        if [[ $READ_RESULT = "no" ]] ; then
          RESULT=0;
        else
          READ_RESULT="";
        fi
      fi

    done
  done
  # Create db config
  echo '<?php' >> $LOCAL_CONF_PATH/db.php
  echo '/**' >> $LOCAL_CONF_PATH/db.php
  echo ' * Local config for db connection.' >> $LOCAL_CONF_PATH/db.php
  echo ' */' >> $LOCAL_CONF_PATH/db.php
  echo 'return [' >> $LOCAL_CONF_PATH/db.php
  echo "    'class'    => 'yii\db\Connection'," >> $LOCAL_CONF_PATH/db.php
  echo "    'dsn'      => 'mysql:host=$MYSQL_HOST;dbname=$MYSQL_DBNAME'," >> $LOCAL_CONF_PATH/db.php
  echo "    'username' => '$MYSQL_USER'," >> $LOCAL_CONF_PATH/db.php
  echo "    'password' => '$MYSQL_PASSWORD'," >> $LOCAL_CONF_PATH/db.php
  echo "    'charset'  => 'utf8'," >> $LOCAL_CONF_PATH/db.php
  echo "];" >> $LOCAL_CONF_PATH/db.php

  echo "Local config was configured & safe config/local/db.php";
fi

if [ ! -f $LOCAL_CONF_PATH/web.php ] ; then
  cp $LOCAL_CONF_PATH/web.php.dist $LOCAL_CONF_PATH/web.php
  echo "Local config was created. Please configure config/local/web.php"
fi

if [ ! -f $LOCAL_CONF_PATH/console.php ] ; then
  cp $LOCAL_CONF_PATH/console.php.dist $LOCAL_CONF_PATH/console.php
  echo "Local config was created. Please configure config/local/console.php"
fi

if [ ! -f $LOCAL_CONF_PATH/params.php ] ; then
  cp $LOCAL_CONF_PATH/params.php.dist $LOCAL_CONF_PATH/params.php
  echo "Local config was created. Please configure config/local/params.php"
fi

echo "Checking migrations"
echo "---------------------------------------------------------------------";
${BIN_DIR}/yii migrate/up
