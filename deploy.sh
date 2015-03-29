#!/usr/bin/env bash

debconf-set-selections <<< 'mysql-server mysql-server/root_password password toor'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password toor'

sudo apt-get update
sudo apt-get install -y php5 php5-mysql mysql-server apache2 nodejs nodejs-legacy npm

#Clean existing config files
sudo rm /etc/apache2/sites-available/*
sudo rm /etc/apache2/sites-enabled/*

# Set the config for our app
cat <<CONF > '/etc/apache2/sites-available/landPage.conf'
<VirtualHost 10.0.2.15:80>
	ServerAdmin webmaster@localhost
	DocumentRoot /vagrant/public

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	<Directory /vagrant/public>
		Options Indexes FollowSymLinks		
		AllowOverride All
		Require all granted
	</Directory>
</VirtualHost>
CONF

# Enable the previous config
sudo ln -s /etc/apache2/sites-available/landPage.conf /etc/apache2/sites-enabled/landPage.conf

# Enable rewrite module for apache (required for routes)
sudo a2enmod rewrite && sudo service apache2 restart

# Install composer for managing php dependencies
sudo php -r "readfile('https://getcomposer.org/installer');" | php
sudo mv composer.phar /usr/bin/composer

# Install required dependencies for running the app
cd /vagrant
composer install

npm install --save-dev gulp gulp-livereload gulp-sass