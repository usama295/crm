# CRM Core Backend BASE Install

## Server Requirements

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
vim /etc/apache2/sites-available/api.crm.conf
~~~

With **(Replace `[PATH]` accordingly)**:

~~~apache
<VirtualHost *:80>
  ServerName api.crm.dev
  DocumentRoot "[PATH]/web"
  DirectoryIndex app.php index.html
  CustomLog /var/log/apache2/access_api.crm.log combined
  ErrorLog /var/log/apache2/error_api.crm.log
  <Directory "[PATH]/web">
    AllowOverride All
    Allow from All
  </Directory>
</VirtualHost>
~~~

- Add to `/etc/hosts`:

~~~bash
127.0.0.1 api.crm.dev
~~~

- Activating VirtualHost:

~~~bash
a2ensite api.crm
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

## Creating Symfony Project

### Symfony base install

- [Installing and Configuring Symfony](http://symfony.com/doc/current/book/installation.html)

~~~bash
cd /var/www/[WHATEVER]
sudo curl -LsS http://symfony.com/installer -o /usr/local/bin/symfony
sudo chmod a+x /usr/local/bin/symfony
~~~

Create the new project:

~~~bash
symfony new backend
~~~

Output:

~~~bash
Downloading Symfony...

    4.91 MB/4.91 MB ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  100%

 Preparing project...

 ✔  Symfony 2.6.6 was successfully installed. Now you can:

    * Change your current directory to /var/www/Clientes/PeteSaia/OneDayRoofing/backend

    * Configure your application in app/config/parameters.yml file.

    * Run your application:
        1. Execute the php app/console server:run command.
        2. Browse to the http://localhost:8000 URL.

    * Read the documentation at http://symfony.com/doc
~~~

- Permissioning

~~~bash
chown -R www-data:www-data /var/www/[WHATEVER]
~~~

## Symfony Post-Install

### Removing AcmeBundle / AppBundle

Just follows [How to Remove the AcmeDemoBundle](http://symfony.com/doc/current/cookbook/bundles/remove.html)

**Tip**:

Just after removing we can set `app/config/security.yml` to
minumum set just for start, as described below. Further we'll
set up FOSUserBundle, so it doesn't matter for now.

~~~yml
security:
    firewalls:
        anonymous:
            anonymous: ~

    providers:
        in_memory:
            memory:
~~~

### Database settings

Edit `app/config/parameters.yml` and change DATABASE, USER and PASSWORD:

~~~yml
# This file is auto-generated during the composer install
parameters:
    database_driver: pdo_mysql
    database_host: 127.0.0.1
    database_port: null
    database_name: DATABASE
    database_user: USER
    database_password: PASSWORD
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: null
    mailer_password: null
    locale: en
    secret: 322e9484044549f24d674a6309b2492efab192d6
~~~

### Adding extra packages to Composer

Edit `composer.json` and add:

~~~javascript
// ...
    "require": {
        // ...
        "stof/doctrine-extensions-bundle": "~1.1@dev",
        "friendsofsymfony/user-bundle": "~2.0@dev",
        "pagerfanta/pagerfanta": "1.0.*@dev",
        "friendsofsymfony/rest-bundle": "dev-master",
        "jms/serializer-bundle": "~0.13"
    },
// ...
~~~

~~~bash
composer update
~~~

### Configuring StofDoctrineExtensionsBundle

Adds some cool Doctrine extensions:

- Tree - this extension automates the tree handling process and adds some tree specific functions on repository. (closure, nestedset or materialized path)
- Translatable - gives you a very handy solution for translating records into diferent languages. Easy to setup, easier to use.
- Sluggable - urlizes your specified fields into single unique slug
- Timestampable - updates date fields on create, update and even property change.
- Blameable - updates string or assocation fields on create, update and even property change with a user name resp. reference.
- Loggable - helps tracking changes and history of objects, also supports version managment.
- Sortable - makes any document or entity sortable
- Translator - explicit way to handle translations
- Softdeleteable - allows to implicitly remove records
- Uploadable - provides file upload handling in entity fields
- Reference Integrity - provides reference integrity for MongoDB, supports 'nullify' and 'restrict'

Those will be activated as needed. Check
[StofDoctrineExtensionsBundle Docs](https://github.com/stof/StofDoctrineExtensionsBundle/blob/master/Resources/doc/index.rst)
for more info.

For now, just add to `app/AppKernel.php`:

~~~php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        // ...
    );
}
~~~

### Configuring FOSUserBundle

TODO



### Configuring FOSRestBundle / JMSSerializerBundle

- [Setting up the bundle](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/1-setting_up_the_bundle.rst)
- [JMSSerializerBundle Installation](http://jmsyst.com/bundles/JMSSerializerBundle)

~~~php
// in app/AppKernel::registerBundles()
$bundles = array(
    // ...
    new FOS\RestBundle\FOSRestBundle(),
    new JMS\SerializerBundle\JMSSerializerBundle(),
    // ...
);
~~~

~~~yml
# app/config/config.yml
fos_rest:
    routing_loader:
        default_format: json
        include_format: false
        
jms_serializer:
    handlers:
        datetime:
            default_format:    "c"   # ISO8601
            #default_timezone: "UTC" #defaults to whatever timezone set in php.ini or via date_default_timezone_set
~~~


### Mastering `.gitignore`

~~~bash
/app/bootstrap.php.cache
/app/cache/*
!app/cache/.gitkeep
/app/config/parameters.yml
/app/logs/*
!app/logs/.gitkeep
/app/phpunit.xml
/bin/
/composer.phar
/vendor/
/web/bundles/
.idea/
bower_components/
node_modules/
/composer.phar
web/assets/js/*.js.map
.DS_Store
~~~

## Creating CRM Base Bundles

For now, 3 bundles:
- Lev/APIBaseBundle (abstract classes for API)
- Lev/CRMBundle (main bundle)
- Lev/DevBundle (for tests)

~~~bash
app/console generate:bundle --namespace=Lev/APIBaseBundle \
    --bundle-name=LevAPIBaseBundle --dir=src --format=yml \
    --structure --no-interaction
app/console generate:bundle --namespace=Lev/CRMBundle \
    --bundle-name=LevCRMBundle --dir=src --format=yml \
    --structure --no-interaction
app/console generate:bundle --namespace=Lev/DevBundle \
    --bundle-name=LevDevBundle --dir=src --format=yml \
    --structure --no-interaction
~~~
