# RESTfull Controller/Routes

## Create a new API Entry

### Craete a basic Entity

~~~bash
app/console doctrine:generate:entity --entity=LevCRMBundle:Post
~~~

Follow the steps:

~~~bash

  Welcome to the Doctrine2 entity generator  

This command helps you generate Doctrine2 entities.

First, you need to give the entity name you want to generate.
You must use the shortcut notation like AcmeBlogBundle:Post.

The Entity shortcut name [LevCRMBundle:Office]:

Determine the format to use for the mapping information.

Configuration format (yml, xml, php, or annotation) [annotation]:

Instead of starting with a blank entity, you can add some fields now.
Note that the primary key will be added automatically (named id).

Available types: array, simple_array, json_array, object,
boolean, integer, smallint, bigint, string, text, datetime, datetimetz,
date, time, decimal, float, blob, guid.

New field name (press <return> to stop adding fields): name
Field type [string]:
Field length [255]: 30

New field name (press <return> to stop adding fields):

Do you want to generate an empty repository class [no]? yes

  Summary before generation  

You are going to generate a "LevCRMBundle:Office" Doctrine2 entity
using the "annotation" format.

Do you confirm generation [yes]?

  Entity generation  

Generating the entity code: OK

  You can now start using the generated code!  
~~~

### Create a Controler

Copy this as a sample, create as `src/Lev/CRMBundle/Controller/OfficeController.php`.

~~~php
<?php

namespace Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use Lev\APIBundle\Controller\ORM\AbstractApiController;

/**
 * @RouteResource("Staffs")
 */
class StaffController extends AbstractApiController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\Lev\CRMBundle\Entity\Staff';
    }

    /**
     * @inheritdoc
     */
    public function configure(ApiConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'roles', 'exposed' => true, 'saved' => true),
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'firstName', 'exposed' => true, 'saved' => false, 'filter' => 'string'),
            array('name' => 'lastName', 'exposed' => true, 'saved' => false, 'filter' => 'string'),
            array('name' => 'email', 'exposed' => true, 'saved' => false, 'filter' => 'string'),
            // ... any other field here
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('staff')
            ->setQuerySort(array(
                'firstName' => 'ASC'
            ));
    }

}
~~~

**Field definition**

~~~php
array(
    'name'        => 'camelCaseFieldName',          // Name of the field
    'exposed'     => true/false,                    // If it will be exposed by the API
    'saved'       => true/false,                    // If it will be saved by the API
    'filter'      => 'setFilter',                   // Array of fields to be filter with definitions (see below)
    'search'      => 'setSearch',                   // Array of searchable fields (string fields on a Google style search)
    'date'        => true/false,                    // Fields to be handle as dates on saving
    'file'        => true/false,                    // File fields (saved on filesystem)
    'filemulti'   => true/false,                    // Array of file fields (saved on filesystem)
    'object'      => '\Namespace\Entity\MyEntity',  // Related fields, saved by ID on Doctrine way (setters need object)
    'collection'  => '\Namespace\Entity\MyEntity',  // Related many-to-many fields, saved by ID on Doctrine way (setters need object)
    'currency'    => true/false,                    // Field is an integer currency
);
~~~

**Filter types**

*   **date**: exact date   
*   **daterange**: date range (values must be an array('min' => ... , 'max' => ...)
*   **daterange_min**: date greater or equal
*   **daterange_max**: date lesser or equal
*   **time**: exact time (not tested)
*   **objectid**: for related entities (ManyToOne, will search with foreingEntity.id)
*   **boolean**: exact true or false
*   **integer**: exact integer
*   **integerrange**: integer range (values must be an array('min' => ... , 'max' => ...)
*   **float**: exact float
*   **floatrange**: float range (values must be an array('min' => ... , 'max' => ...)
*   **string**:  exact string
*   **stringsearch**:  multiple string (Google style / SQL LIKE '%%')
*   **calendarrange**: special date calendar range, config must be:

~~~php
    array(
        'name' => 'anydatename'
        'filter' => array(
            'type' => 'calendarrange',
            'config' => array(
                'startField' => 'startDateTime', // <= name of start (from) field
                'endField'   => 'endDateTime',   // <= name of end (from) field
            )
        )
    )
~~~


**Config (Lev\APIBundle\Config\ApiConfig)**

~~~php
$config
    ->setQueryMaxPerPage(20)       // Limit to paginator, 20 if not set
    ->setFieldsFromArray($fields)  // Array of fields definition
    ->setDefaultRoles('staff')     // To manage authorization for actions => ROLE_STAFF_*
    ->setQuerySort(array(          // Define default sort
        'firstName' => 'ASC'
    ));
~~~

- All Lev\APIBundle\Config\ApiConfig methods   

~~~php
addField(Field $field)
addFieldFromArray(array $fieldConfig)
setFieldsFromArray(array $fields)
getFields()
setFields(array $fields)
setRoles(array $roles)
getRoles()
setExposed(array $fields)
getExposed()
setSaved(array $fields)
getSaved()
addRole($basename, $role)
setDefaultRoles($module)
setQuerySort(array $sort)
getQuerySort()
setQueryMaxPerPage($maxPerPage)
getQueryMaxPerPage()
getToFilter()
~~~

### Publish roles

Add to `app/config/api_roles.yml`.

~~~yml
parameters:
    lev_api.crud_roles:
        # Another entities here
        office: [ view, create, update, delete ]
~~~

### Publish the route

Add to `src/Lev/CRMBundle/Resources/config/routing_api.yml`.

~~~yml
lev_crm_office:
    resource: 'Lev\CRMBundle\Controller\API\OfficeControlle
    type:     rest
~~~

You can check the routes with:

~~~bash
app/console route:debug | grep office

 unique_office                     GET      ANY    ANY  /api/v1/unique/{field}/{value}/{id}   
 get_offices                       GET      ANY    ANY  /api/v1/offices                       
 get_office                        GET      ANY    ANY  /api/v1/offices/{id}                  
 put_office                        PUT      ANY    ANY  /api/v1/offices/{id}                  
 post_office                       POST     ANY    ANY  /api/v1/offices                       
 delete_office                     DELETE   ANY    ANY  /api/v1/offices/{id}
~~~

### Update Database

~~~bash
composer dbrebuild

Generating entities for bundle "LevCRMBundle"
  > generating Lev\CRMBundle\Entity\Office
  > generating Lev\CRMBundle\Entity\StaffMember
  > generating Lev\CRMBundle\Entity\Group
~~~

### Clear the cache to production

~~~bash
composer clearcache
~~~

## API Responses

### GET /api/v1/unique/{field}/{value}/{id}
@ TODO

### GET /api/v1/offices

~~~javascript
{
    "pagination": {
        "paginate": false,
        "total": 0,
        "limit": 20,
        "pages": 1,
        "currentPage": 1,
        "next": false,
        "prev": false
    },
    "results": []
}
~~~

or HTTP 502 error:

~~~javascript
{
    "error": "validate",
    "error_description": "Some error on request",
}

// OR

{
    "error": "api_cget",
    "error_description": "Malformed filter - invalid JSON"
}

// OR
{
    "error": "api_cget",
    "error_description": "Filter ERROR: costAmount filter is floatrange but 'min'\/'max' is missing on value (expected { \"costAmount\": {\"min\": ... ,
}     
~~~


### GET /api/v1/offices/{id}

Returns the object requested:

~~~javascript
{
    "id": 1,
    "name": "Some place"
}

~~~

or HTTP 404

~~~javascript
{
    "error": "validate",
    "error_description": "Record not found"
}
~~~

### PUT /api/v1/offices/{id}

Returns HTTP 200 and the object updated:

~~~javascript
{
    "id": 2,
    "name": "Another place"
}

~~~

or HTTP 502 validation errors:

~~~javascript
{
    "error": "validate",
    "error_description": "Record invalid",
    "validation_errors": {
        "name": [
            "Required"
        ]
    }
}
~~~

### POST /api/v1/offices

Returns HTTP 201 and the object created :

~~~javascript
{
    "id": 3,
    "name": "Random place"
}

~~~

or HTTP 502 validation errors:

~~~javascript
{
    "error": "validate",
    "error_description": "Record invalid",
    "validation_errors": {
        "name": [
            "Required"
        ]
    }
}
~~~

### DELETE /api/v1/offices/{id}

Returns HTTP 200 and the object created:

~~~javascript
{
    "success": true,
    "message": "Record deleted"
}
~~~

or error:

~~~javascript
{
    "success": false,
    "message": "Record not found",
    "stacktrace": "... only on DEV..."
}         
~~~


or HTTP 500

~~~javascript
{
    "error": "validate",
    "error_description": "Record not found"
}         
~~~

## Security API Responses

### Loggedin

Gets security info about the logged user, including full roles listing:

~~~bash
http://api.crm.dev/app_dev.php/api/v1/security/loggedin?access_token=??...
~~~

~~~javascript
{
    "username": "petesaia",
    "fullname": 0,
    "email": "petesaia@gmail.com",
    "office": {
        "id": 1,
        "name": "Washington DC"
    },
    "roles": [
         "SUPER_ADMIN",
         "ADMIN",
         "USER",
         "CALC_BUDGET",
         "STAFF",
         "STAFFROLE",
         "OFFICE",
         "ADVISORYZIPCODE",
         "PRODUCT",
         "CUSTOMER",
         "STAFF_VIEW",
         "STAFF_CREATE",
         "STAFF_UPDATE",
         "STAFF_DELETE",
         "STAFFVIEW",
         "STAFFCREATE",
         "STAFFUPDATE",
         "STAFFDELETE",
         "OFFICE_VIEW",
         "OFFICE_CREATE",
         "OFFICE_UPDATE",
         "OFFICE_DELETE",
         "ADVISORYZIPCODE_VIEW",
         "ADVISORYZIPCODE_CREATE",
         "ADVISORYZIPCODE_UPDATE",
         "ADVISORYZIPCODE_DELETE",
         "PRODUCT_VIEW",
         "PRODUCT_CREATE",
         "PRODUCT_UPDATE",
         "PRODUCT_DELETE",
         "CUSTOMER_VIEW",
         "CUSTOMER_CREATE",
         "CUSTOMER_UPDATE",
         "CUSTOMER_DELETE"
     ]
}
~~~

### Logout

Set `expiresAt` to now, invalidating AccessToken and RefreshToken.

~~~bash
http://api.crm.dev/app_dev.php/api/v1/security/logout?access_token=??...
~~~

### Profile

GET / PUT: Allows to retrieve and update user data, but NOT roles and office
(so user cannot change his acccess).
~~~bash
http://api.crm.dev/app_dev.php/api/v1/security/profile?access_token=??...
~~~
