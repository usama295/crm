# Oauth2 and Access Control

## Creating a user

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

## Creating Clients (Apps and Sites)

Available Grant Types:

- authorization_code
- token
- password
- client_credentials
- refresh_token
- extensions

You may pass multiple `--redirect-uri` (or none) and 
multiple `--grant-type`

Common use (user/pass with refres token)

~~~bash
app/console oauth:client:create \
   --redirect-uri=https://www.getpostman.com/oauth2/callback \
   --grant-type=password --grant-type=refresh_token
   
## Output:

                  Oauth2 Client created!                    
                                                            
Redirect URIs : https://www.getpostman.com/oauth2/callback
Grant Types   : password, refresh_token
Client ID     : 1_56tg554h3uo0gswkos00gcgwkw4o000w4w4oko0444cwswoww8
Client Secret : 3e06tttspy4ggg8okwscgkgg8o88k0ws0gwc840k8cgc00gssw
  
~~~

## User/Password auth

**Obs**: [ouauth2-php](https://github.com/FriendsOfSymfony/oauth2-php) currently requires both
Client ID **AND** Secret for this grant type. Some implementations allows with only Client ID,
as it would be considered a security hole in some environments to keep the secret 
(as Javascripts apps or mobile apps).

A big discussion here: [Password Grant requires secret, incorrect?](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/issues/115)

~~~bash
http://api.crm.dev/oauth/v2/token?grant_type=password&username=rafaelgou&password=123456&client_id=1_56tg554h3uo0gswkos00gcgwkw4o000w4w4oko0444cwswoww8&client_secret=3e06tttspy4ggg8okwscgkgg8o88k0ws0gwc840k8cgc00gssw
~~~

This would return: 

~~~json
{"access_token":"MWUxMTY4Mjk2NTI5NjVkMzBmYmNmMTA4NjNiZDc3ZDJlZjZmZGQ1ZWMyNWQ4NTMzYTZhMmIzZjE4YzQ0NGZkYw","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"MzkwMThmYmFhNGEwYzVkMzk0YjQwODdmYTk2M2MzNmEwYWUwYWQ1OGZkMjY4MWRhNzlkODdiMzY5N2I5NDc0NQ"}
~~~

## Refresh Token

Passing the `refresh_token` will refresh `access_token` expiration.

~~~bash
http://api.crm.dev/oauth/v2/token?grant_type=refresh_token&username=REFRESH_TOKEN&client_id=1_56tg554h3uo0gswkos00gcgwkw4o000w4w4oko0444cwswoww8&client_secret=3e06tttspy4ggg8okwscgkgg8o88k0ws0gwc840k8cgc00gssw
~~~

## API Call

~~~bash
http://api.crm.dev/app_dev.php/api/v1/offices?access_token=MDJlODExY2NhOWY4MTFmMzBlYWJiNDg1ZDQxMWY3N2ZlODFiMWJiMTFiZjI0NDQ4NDNkNDc1YjA0NzZmMDllNw
~~~

## Authorization info

Once a user is authenticated, the user/authorization info can be get from the URI:

~~~bash
http://api.crm.dev/app_dev.php/api/v1/security/loggedin?access_token=MDJlODExY2NhOWY4MTFmMzBlYWJiNDg1ZDQxMWY3N2ZlODFiMWJiMTFiZjI0NDQ4NDNkNDc1YjA0NzZmMDllNw
~~~

with the following info:

~~~bash
{
    "loggedIn": true,
    "username":"petesaia",
    "fullname":"Pete Saia",
    "email":"petesaia@gmail.com",
    "office":{
        "id":1,
        "name":"Washington DC"
        },
    "roles":[
        "SUPER_ADMIN",
        "ADMIN",
        "USER",
        "CALC_BUDGET",
        "STAFF",
        "STAFFROLE",
        "OFFICE",
        "ADVISORYZIPCODE",
        "PRODUCT",
        // ...
        ]
}
~~~

Notice:

- roles will be retrievied without "ROLE_*" prefix
- hierarchical roles defined in `app/config/security` => `security.role_hierarchy`
  will be retrieved expanded, so if user is `SUPER_ADMIN` all roles will appear
  on `roles` array.

Any extra info needed can be added on 
`src/Lev/CRMBundle/Controller/API/SecurityController.php` => `loggedin` method.