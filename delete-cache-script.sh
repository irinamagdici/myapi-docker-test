#!/bin/bash
# Script to delete symfony application cache
SYMFONY_PATH=$1
CACHE_PATH="app/cache"
echo "Script file path: "$SYMFONY_PATH"; "
echo "User: "$USER$(whoami)
echo $SYMFONY_PATH$CACHE_PATH
echo `rm -rf /var/www/html/app/cache/*`
#echo `rm -rf $SYMFONY_PATH$CACHE_PATH/*`
#echo "Complete path: "$SYMFONY_PATH$CACHE_PATH"; "
