
**Requirements**

PHP >= 5.3

Apache

Composer https://getcomposer.org/download/

**Installation**

Add virtual host to Apache, for example:

~~~
<VirtualHost *:80>
	# The ServerName directive sets the request scheme, hostname and port that
	# the server uses to identify itself. This is used when creating
	# redirection URLs. In the context of virtual hosts, the ServerName
	# specifies what hostname must appear in the request's Host: header to
	# match this virtual host. For the default virtual host (this file) this
	# value is not decisive as it is used as a last resort host regardless.
	# However, you must set it for any further virtual host explicitly.
	ServerName localhost
	ServerAdmin webmaster@localhost
	DocumentRoot /path/to/application

        <Directory /path/to/application>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all

		Require all granted
        </Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
~~~

Clone repo
~~~
git clone https://github.com/ordman/shortener.git 

~~~
Install dependencies from project dir
~~~
composer update
~~~

Import mysql dump from `dump.sql`

Copy `config.example.inc` to `config.inc`  and edit connection params

**Tests execute**
~~~
php vendor/phpunit/phpunit/phpunit.php
~~~