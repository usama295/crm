# CRM Core Backend Install

## Server Requirements

**If you already have the requirements, goto [Installing the APP](#installing-the-app)**.

[Requirements for Running Symfony2](http://symfony.com/doc/current/reference/requirements.html)

### Base LAMP Install

Base: Ubuntu 14.04

~~~bash
apt-get update
apt-get install apache2 libapache2-mod-php5 php5 php-pear \
    php5-cli php5-intl php5-json php5-mysql php5-xsl \
    mysql-server-5.5 git vim unzip curl
~~~

### Composer

[Composer - Installation - Linux / Unix / OSX](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

~~~bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/user/local/bin
~~~

### Zend OpCache

[OPcache](http://php.net/manual/pt_BR/book.opcache.php)

It's default PHP 5.5 in Ubuntu 14.04.

Config File: `/etc/php5/mods-available/opcache.ini`.

- Production sugested file:

(Source: [Best Zend OpCache Settings/Tuning/Config](https://www.scalingphpbook.com/best-zend-opcache-settings-tuning-config/))

~~~bash
opcache.validate_timestamps=off    # For PROD / comment for DEV!!!
opcache.max_accelerated_files=8000 # Cache max files
opcache.memory_consumption=128     # default 64Mb
opcache.interned_strings_buffer=16 # default 4Mb
opcache.fast_shutdown=1
~~~

Web interfaces:

- [Rasmus opcache-status website](https://github.com/rlerdorf/opcache-status)
- [Amnuts opcache-gui website](https://github.com/amnuts)
- [OpCache GUI website](https://github.com/PeeHaa/OpCacheGUI)
- [CK-ON website](https://gist.github.com/ck-on/4959032)

### Secure Apache2

Do not expose server data for better security.

~~~bash
vim /etc/apache2/conf-available/security.conf
~~~

With:

~~~bash
ServerTokens Prod
ServerSignature Off
TraceEnable Off
~~~

### VirtualHost

- Create the file:

~~~bash
vim /etc/apache2/sites-available/crm-core-backend.conf
~~~

With (`crm-core-backend.dev` is a sample):

~~~apache
<VirtualHost *:80>
  ServerName crm-core-backend.dev
  DocumentRoot "/var/www/crm-core-backend/web"
  DirectoryIndex app.php index.html
  CustomLog /var/log/apache2/access_crm-core-backend.log combined
  ErrorLog /var/log/apache2/error_crm-core-backend.log
  <Directory "/var/www/crm-core-backend/web">
    AllowOverride All
    Allow from All
  </Directory>
</VirtualHost>
~~~

- Activating VirtualHost:

~~~bash
a2ensite crm-core-backend
~~~

### Set Timezone on PHP

Change both files (for WEB and CLI)

~~~bash
vim /etc/php5/apache2/php.ini
vim /etc/php5/cli/php.ini
~~~

Change (your timezone here):

~~~bash
date.timezone = America/Sao_Paulo
~~~

### Activating deflate and expires

~~~bash
a2enmod rewrite deflate headers expires
~~~

Create the file:

~~~bash
vim /etc/apache2/conf-available/deflate-expires.conf
~~~

With:

~~~apache
<Directory "/var/www/">
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE application/json
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/truetype
  AddOutputFilterByType DEFLATE font/woff
  BrowserMatch ^Mozilla/4 gzip-only-text/html
  BrowserMatch ^Mozilla/4\.0[678] no-gzip
  BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
</Directory>
FileETag none
ExpiresActive On
<FilesMatch "\.(gif|jpg|jpeg|png|ico|css|js|swf|GIF|JPG|JPEG|PNG|txt|TXT)$">
  ExpiresDefault "access plus 1 month"
</FilesMatch>
~~~

- Activating:

~~~bash
a2enconf deflate-expires
~~~

### Restart Apache

~~~bash
service apache2 restart
~~~

## Installing the APP

~~~bash
cd /var/www
git clone git@dev.lev-interactive.com:one-day-roofing/crm-core-backend.git
cd crm-core-backend
composer install
~~~

Composer will ask some questions on post install (setting `app/config/parameters.yml`):

~~~bash
Some parameters are missing. Please provide them.
database_driver (pdo_mysql):
database_host (127.0.0.1):
database_port (null):
database_name (symfony): odr-crm
database_user (root): odr-user
database_password (null): odr-password
mailer_transport (smtp):
mailer_host (127.0.0.1):
mailer_user (null):
mailer_password (null):
locale (en):
secret (ThisTokenIsNotSoSecretChangeIt):
~~~

After install, be sure the database is running and user is created with creating tables permission, then run:

~~~bash
composer dbupdate
composer cacheclear
~~~

### Running a DEV server

To run a dev server on http://localhost:8000, run:

~~~bash
app/console server:run
~~~

or, if you want to customize binding host and port:

~~~bash
app/console server:run 127.0.0.1:8080
~~~

### Creating a user

~~~bash
app/console lev:user:create rafaelgou "Rafael Goulart" rafaelgou@gmail.com 123456
~~~

As superadmin:

~~~bash
app/console lev:user:create rafaelgou "Rafael Goulart" rafaelgou@gmail.com 123456 --super-admin
~~~

Add roles to a user:

~~~bash
app/console fos:user:promote rafaelgou ROLE_STAFF
~~~

## Crontab Tasks


Add to `/etc/crontab`:

~~~bash
*/5 * * * * root /var/www/crm-core-backend/app/console lev:cron
~~~
