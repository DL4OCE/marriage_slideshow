#!/bin/bash

# install httpd, php interpreter
apt update
apt install apache2 php-cli libapache2-mod-php lightdm feh

# copy all the files needed:
# - sync service
# - slide show service
cp usr/lib/systemd/system/hochzeit_sync.service /usr/lib/systemd/system/
cp -pR var/www/html/* /var/www/html/


# enable sync service to be available at boot-time
systemctl enable hochzeit_sync.service

echo Edit etc/lightdm/lightdm.conf: comment out autologin-user=pi
echo Edit home/pi/.config/lxsession/LXDE/autostart:
echo @unclutter
echo @xset s off
echo @xset -dpms
echo @xset s noblank
echo @feh --auto-rotate --hide-pointer --borderless --quiet --slideshow-delay 5 --image-bg black --fullscreen --auto-zoom --randomize --reload 5 --recursive /var/www/html/pics/

echo Use the file SD_WLAN/CONFIG on your WiFi SD card!
