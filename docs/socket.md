# Socket install/running

## Go running

Install deps:

~~~bash
cd PATH_TO/crm-core-backend
composer install
~~~

Configuration:

~~~bash
#app/config/parameters.yml
#...
    cors_hosts: [ '^api\.', '^odr-crm-api\.']
    socket_server: 'odr-crm-api.web.lev-interactive.com'
    socket_port: 8080
~~~

Install dev assets:

~~~bash
app/console assets:install --symlink
~~~

Init script install, add to startup

~~~bash
cp init/crm-callcenter-socket /etc/init.d/
chmod 755 /etc/init.d/crm-callcenter-socket
update-rc.d crm-callcenter-socket defaults
~~~

Edit with `vim /etc/init.d/crm-callcenter-socket` and change directory path:

~~~bash
DAEMON_PATH="/var/www/LevInteractive/OneDayRoofing/crm-core-backend"
~~~

To start / stop / restart:

~~~bash
service crm-callcenter-socket start
service crm-callcenter-socket stop
service crm-callcenter-socket restart
~~~

## Extras

To run a dev server:

~~~bash
composer socket-server-dev
~~~

To remove (if needed):

~~~bash
update-rc.d -f crm-callcenter-socket remove
~~~

## Javascript Using

Uses Authbahn + Gos WS wrapper.

Check `web/socket.php` for an example.

Test with actual username/access_token on browser (check console): http://api.crm.dev/socket.php?username=dev@lev-interactive.com&access_token=NTZiZTFmOWRkZjhiOWI2Y2ZmNzNiODNhZGQzMzA3YjMyMDc2YWQ4Yjc2ZWRmY2QyOTEyYjJmZmZmZDVmOWNmZA
