#!/bin/bash
cd /var/www/html
#echo `date` >> api-git-deployment-log.txt
#echo "Deploy Attempt."  >> api-git-deployment-log.txt

if [ -f web/777/.autodeploy ]; then
  echo `date` >> api-git-deployment-log.txt
  echo "Found changes. Pulling now." >> api-git-deployment-log.txt
  # git stash
  # git pull
  git fetch --all
  git reset --hard origin/master
  rm -f web/777/.autodeploy
  #grunt build
  echo "Deploy done." >> api-git-deployment-log.txt
  # compass compile src/AppBundle/Resources/public/ --sourcemap
fi
sleep 20
exit
