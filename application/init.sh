#!/usr/bin/env bash

DEBIAN_FRONTEND=noninteractive

# Update the Ubuntu repository
apt-get update

# Install apt tools
apt-get install -y apt-utils
apt-get install -y --no-install-recommends dialog

# Update the distribution
apt-get dist-upgrade -y

# Install SSH
apt-get install -y ssh

# Open port 22 for ssh access
echo "Port 22" >> /etc/ssh/sshd_config

# Create admin user and start ssh
useradd -ms /bin/bash admin
echo 'admin:qkfw18djksai8rbjkasy' | chpasswd

### PACKAGE INSTALLATION

# Install apt tools
apt-get install -y --no-install-recommends apt-utils dialog

# Install requirede packages
apt-get install -y ssh nano curl apt-transport-https apache2 wget php git php-curl php-gd php-mysql php-xml php-zip libapache2-mod-php composer unzip php-mbstring
apt-get install -y php-redis

### WEB SERVER

# Allow override in apache2 config file so that .htaccess can include redirection of urls for Drupal
echo "<Directory /var/www/html/>" >> /etc/apache2/apache2.conf
echo " Options Indexes FollowSymLinks" >> /etc/apache2/apache2.conf
echo " AllowOverride All" >> /etc/apache2/apache2.conf
echo " Require all granted" >> /etc/apache2/apache2.conf
echo "</Directory>" >> /etc/apache2/apache2.conf

# Enable rewrite on apache and restart the server
a2enmod rewrite

## Enable SSL (https)
a2enmod ssl

# Enable https . This needs further configuration
a2ensite default-ssl.conf

# Restart the web server
service apache2 restart

