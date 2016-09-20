# Laravel 5 JSON API Server Package


[![Build Status](https://travis-ci.org/nilportugues/laravel5-jsonapi.svg?branch=master)](https://travis-ci.org/nilportugues/laravel5-jsonapi)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/laravel5-jsonapi-transformer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/laravel5-jsonapi-transformer/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/22db88f5-d061-4b32-bad1-4b806ac07318/mini.png)](https://insight.sensiolabs.com/projects/22db88f5-d061-4b32-bad1-4b806ac07318) 
[![Latest Stable Version](https://poser.pugx.org/nilportugues/laravel5-json-api/v/stable)](https://packagist.org/packages/nilportugues/laravel5-json-api) 
[![Total Downloads](https://poser.pugx.org/nilportugues/laravel5-json-api/downloads)](https://packagist.org/packages/nilportugues/laravel5-json-api) 
[![License](https://poser.pugx.org/nilportugues/laravel5-json-api/license)](https://packagist.org/packages/nilportugues/laravel5-json-api) 
[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://paypal.me/nilportugues)

*Compatible with Laravel 5.0, 5.1 & 5.2*

- Package provides a full implementation of the **[JSON API](https://github.com/json-api/json-api)** specification, and is **featured** on the official site!
- A **JSON API Transformer** that will allow you to convert any mapped object into a valid JSON API resource.
- Controller boilerplate to write a fully compiliant **JSON API Server** using your **exisiting Eloquent Models**.
- Works for Laravel 5 and Lumen frameworks.

---

- [Installation](#installation)
- [Configuration (Laravel 5 & Lumen)](#configuration-laravel-5--lumen)
  - [Configuration for Laravel 5](#configuration-for-laravel-5)
        - [Step 1: Add the Service Provider](#step-1-add-the-service-provider)
        - [Step 2: Defining routes](#step-2-defining-routes)
        - [Step 3: Definition](#step-3-definition)
        - [Step 4: Usage](#step-4-usage)
  - [Configuration for Lumen](#configuration-for-lumen)
        - [Step 1: Add the Service Provider](#step-1-add-the-service-provider-1)
        - [Step 2: Defining routes](#step-2-defining-routes-1)
        - [Step 3: Definition](#step-3-definition-1)
        - [Step 4: Usage](#step-4-usage-1)
- [JsonApiController](#jsonapicontroller)
- [Examples: Consuming the API](#examples-consuming-the-api)
  - [GET](#get)
  - [POST](#post)
  - [PUT](#put)
  - [PATCH](#patch)
  - [DELETE](#delete)
- [GET Query Params: include, fields, sort and page](#get-query-params-include-fields-sort-and-page)
- [POST/PUT/PATCH with Relationships](#postputpatch-with-relationships)
- [Custom Response Headers](#custom-response-headers)
- [Common Errors and Solutions](#common-errors-and-solutions)

## Installation

Use [Composer](https://getcomposer.org) to install the package:

```
$ composer require nilportugues/laravel5-json-api
```

Now run the following artisan command: 

```
$ php artisan vendor:publish
```

## Configuration (Laravel 5 & Lumen)


For the sake of having a real life example, this configuration will guide you on how to set up **7 end-points** for two resources, `Employees` and `Orders`.

Both `Employees` and `Orders` resources will be **Eloquent** models, being related one with the other. 

Furthermore, `Employees`will be using an Eloquent feature, `appended fields` to demonstrate how it is possible to make the most of Eloquent and this package all together.

### Configuration for Laravel 5

#### Step 1: Add the Service Provider

Open up `config/app.php` and add the following line under `providers` array:

```php
'providers' => [
    //...
    NilPortugues\Laravel5\JsonApi\Laravel5JsonApiServiceProvider::class,
],
```



#### Step 2: Defining routes

We will be planning the resources ahead its implementation. All routes require to have a name.  

This is how our `app/Http/routes.php` will look:


```php
<?php
Route::group(['namespace' => 'Api'], function() {
    Route::resource('employees', 'EmployeesController');    
    Route::get(
        'employees/{employee_id}/orders', [
        'as' => 'employees.orders',
        'uses' => 'EmployeesController@getOrdersByEmployee'
    ]);
});
//...
```

#### Step 3: Definition


First, let's define the Models for `Employees` and `Orders` using Eloquent.


**Employees (Eloquent Model)**
```php
<?php namespace App\Model\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Employees extends Model
{
    public $timestamps = false;
    protected $table = 'employees';    
    protected $primaryKey = 'id';
    protected $appends = ['full_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestOrders()
    {
        return $this->hasMany(Orders::class, 'employee_id')->limit(10);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}

```

**Employees SQL**

```sql
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `email_address` varchar(50) DEFAULT NULL,
  `job_title` varchar(50) DEFAULT NULL,
  `business_phone` varchar(25) DEFAULT NULL,
  `home_phone` varchar(25) DEFAULT NULL,
  `mobile_phone` varchar(25) DEFAULT NULL,
  `fax_number` varchar(25) DEFAULT NULL,
  `address` longtext,
  `city` varchar(50) DEFAULT NULL,
  `state_province` varchar(50) DEFAULT NULL,
  `zip_postal_code` varchar(15) DEFAULT NULL,
  `country_region` varchar(50) DEFAULT NULL,
  `web_page` longtext,
  `notes` longtext,
  `attachments` longblob,
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `company` (`company`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `zip_postal_code` (`zip_postal_code`),
  KEY `state_province` (`state_province`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
INSERT INTO `employees` (`id`, `company`, `last_name`, `first_name`, `email_address`, `job_title`, `business_phone`, `home_phone`, `mobile_phone`, `fax_number`, `address`, `city`, `state_province`, `zip_postal_code`, `country_region`, `web_page`, `notes`, `attachments`)
VALUES
    (10, 'Acme Industries', 'Smith', 'Mike', 'mike.smith@mail.com', 'Horticultarlist', '0118 9843212', NULL, NULL, NULL, '343 Friary Road', 'Manchester', 'Lancs.', 'M3 3DL', 'United Kingdom', NULL, NULL, NULL);

```

**Orders (Eloquent Model)**

```php
<?php namespace App\Model\Database;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{   
    public $timestamps = false;
    protected $table = 'orders';
    protected $primaryKey = 'id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
}
```

**Orders SQL**

```sql
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `shipped_date` datetime DEFAULT NULL,
  `shipper_id` int(11) DEFAULT NULL,
  `ship_name` varchar(50) DEFAULT NULL,
  `ship_address` longtext,
  `ship_city` varchar(50) DEFAULT NULL,
  `ship_state_province` varchar(50) DEFAULT NULL,
  `ship_zip_postal_code` varchar(50) DEFAULT NULL,
  `ship_country_region` varchar(50) DEFAULT NULL,
  `shipping_fee` decimal(19,4) DEFAULT '0.0000',
  `taxes` decimal(19,4) DEFAULT '0.0000',
  `payment_type` varchar(50) DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `notes` longtext,
  `tax_rate` double DEFAULT '0',
  `tax_status_id` tinyint(4) DEFAULT NULL,
  `status_id` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`),
  KEY `id` (`id`),
  KEY `shipper_id` (`shipper_id`),
  KEY `tax_status` (`tax_status_id`),
  KEY `ship_zip_postal_code` (`ship_zip_postal_code`),
  KEY `fk_orders_orders_status1` (`status_id`),  
  CONSTRAINT `fk_orders_employees1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;
INSERT INTO `orders` (`id`, `employee_id`, `customer_id`, `order_date`, `shipped_date`, `shipper_id`, `ship_name`, `ship_address`, `ship_city`, `ship_state_province`, `ship_zip_postal_code`, `ship_country_region`, `shipping_fee`, `taxes`, `payment_type`, `paid_date`, `notes`, `tax_rate`, `tax_status_id`, `status_id`)
VALUES
    (82, 10, NULL, '2015-03-12 00:00:00', '2015-03-12 00:00:00', NULL, NULL, '43, Borrowed Drive', 'New Oreleans', 'Louisiana', '4322', 'USA', 1.4000, 0.0000, NULL, NULL, NULL, 0, NULL, 0);

```

Follow up, we'll be creating Transformers. One Transformer is required for each class and it must implement the `\NilPortugues\Api\Mappings\JsonApiMapping` interface.

We will be placing these files at `app/Model/Api`:

**EmployeesTransformer**

```php
<?php namespace App\Model\Api;

use App\Model\Database\Employees;
use NilPortugues\Api\Mappings\JsonApiMapping;

class EmployeesTransformer implements JsonApiMapping
{
    /**
     * Returns a string with the full class name, including namespace.
     *
     * @return string
     */
    public function getClass()
    {
        return Employees::class;
    }

    /**
     * Returns a string representing the resource name 
     * as it will be shown after the mapping.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'employee';
    }

    /**
     * Returns an array of properties that will be renamed.
     * Key is current property from the class. 
     * Value is the property's alias name.
     *
     * @return array
     */
    public function getAliasedProperties()
    {
        return [
            'last_name' => 'surname',
            
        ];
    }

    /**
     * List of properties in the class that will be  ignored by the mapping.
     *
     * @return array
     */
    public function getHideProperties()
    {
        return [
            'attachments'
        ];
    }

    /**
     * Returns an array of properties that are used as an ID value.
     *
     * @return array
     */
    public function getIdProperties()
    {
        return ['id'];
    }

    /**
     * Returns a list of URLs. This urls must have placeholders 
     * to be replaced with the getIdProperties() values.
     *
     * @return array
     */
    public function getUrls()
    {
        return [
            'self' => ['name' => 'employees.show', 'as_id' => 'id'],
            'employees' => ['name' => 'employees.index'],
            'employee_orders' => ['name' => 'employees.orders', 'as_id' => 'id']
        ];
    }

    /**
     * Returns an array containing the relationship mappings as an array.
     * Key for each relationship defined must match a property of the mapped class.
     *
     * @return array
     */
    public function getRelationships()
    {
        return [];
    }
} 
```

Same goes for `Orders`,  these files will also be placed at `app/Model/Api`:

**OrdersTransformer**

```php
<?php namespace App\Model\Api;

use App\Model\Database\Orders;
use NilPortugues\Api\Mappings\JsonApiMapping;

class OrdersTransformer implements JsonApiMapping
{
    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return Orders::class;
    }
    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'order';
    }
    /**
     * {@inheritDoc}
     */
    public function getAliasedProperties()
    {
        return [];
    }
    /**
     * {@inheritDoc}
     */
    public function getHideProperties()
    {
        return [];
    }
    /**
     * {@inheritDoc}
     */
    public function getIdProperties()
    {
        return ['id'];
    }
    /**
     * {@inheritDoc}
     */
    public function getUrls()
    {
        return [
            'self'     => ['name' => 'orders.show', 'as_id' => 'id'],
            'employee' => ['name' => 'employees.show', 'as_id' => 'employee_id'],
        ];
    }
    /**
     * {@inheritDoc}
     */
    public function getRelationships()
    {
        return [];
    }
} 
```


#### Step 4: Usage

Create file `config/jsonapi.php`. This file should return an array returning all the class mappings.


```php
<?php
use App\Model\Api\EmployeesTransformer;
use App\Model\Api\OrdersTransformer;

return [
    EmployeesTransformer::class,
    OrdersTransformer::class,
];
```
<br>

### Configuration for Lumen

#### Step 1: Add the Service Provider

Open up `bootstrap/app.php`and add the following lines before the `return $app;` statement:

```php
$app->register(\NilPortugues\Laravel5\JsonApi\Laravel5JsonApiServiceProvider::class);
$app->configure('jsonapi');
```

Also, enable Facades by uncommenting:

```php
$app->withFacades();
```

#### Step 2: Defining routes

We will be planning the resources ahead its implementation. All routes require to have a name.

This is how our `app/Http/routes.php` will look:


```php
<?php
$app->group(
    ['namespace' => 'Api'], function($app) {
        $app->get(
            'employees', [
            'as' => 'employees.index',
            'uses' =>'EmployeesController@index'
        ]);
        $app->post(
            'employees', [
            'as' => 'employees.store',
            'uses' =>'EmployeesController@store'
        ]);
        $app->get(
            'employees/{employee_id}', [
            'as' => 'employees.show', 
            'uses' =>'EmployeesController@show'
        ]);
        $app->put(
            'employees/{employee_id}', [
            'as' => 'employees.update', 
            'uses' =>'EmployeesController@update'
        ]);
        $app->patch(
            'employees/{employee_id}', [
            'as' => 'employees.patch',
            'uses' =>'EmployeesController@update'
        ]);
        $app->delete(
            'employees/{employee_id}', [
            'as' => 'employees.destroy',
            'uses' =>'EmployeesController@destroy'
        ]);
        
        $app->get(
            'employees/{employee_id}/orders', [
            'as' => 'employees.orders', 
            'uses' => 'EmployeesController@getOrdersByEmployee'
        ]);
    }
);
//...
``` 

#### Step 3: Definition

Same as Laravel 5.

#### Step 4: Usage

Same as Laravel 5.

## JsonApiController

Whether it's Laravel 5 or Lumen, usage is exactly the same. 

Let's create a new controller that extends the `JsonApiController` provided by this package, as follows:

**Lumen users must extends from `LumenJsonApiController` not `JsonApiController`**.

```php
<?php namespace App\Http\Controllers;

use App\Model\Database\Employees;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;

class EmployeesController extends JsonApiController
{
    /**
     * Return the Eloquent model that will be used 
     * to model the JSON API resources. 
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDataModel()
    {
        return new Employees();
    }
}
```


In case you need to overwrite any default behaviour, the **JsonApiController** methods are:

```php
//Constructor and defined actions
public function __construct(JsonApiSerializer $serializer);
public function listAction();
public function getAction(Request $request);
public function postAction(Request $request);
public function patchAction(Request $request);
public function putAction(Request $request);
public function deleteAction(Request $request);

//Methods returning callables that access the persistence layer
protected function totalAmountResourceCallable();
protected function listResourceCallable();
protected function findResourceCallable(Request $request);
protected function createResourceCallable();
protected function updateResourceCallable();

//Allows modification of the response object
protected function addHeaders(Response $response);
```


But wait! We're missing out one action, `EmployeesController@getOrdersByEmployee`. 

As the name suggests, it should list orders, so the behaviour should be the same as the one of `ListAction`.

If you look inside the `listAction`you'll find a code similar to the one below, but we just ajusted the behaviour and used it in our controller to support an additional action:

```php
<?php namespace App\Http\Controllers;

use App\Model\Database\Employees;
use App\Model\Database\Orders;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;

class EmployeesController extends JsonApiController
{
    /**
     * Return the Eloquent model that will be used 
     * to model the JSON API resources. 
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDataModel()
    {
        return new Employees();
    }    
    
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getOrdersByEmployee(Request $request)
    {       
        $apiRequest = RequestFactory::create();
        $page = $apiRequest->getPage();

        if (!$page->size()) {
            $page->setSize(10); //Default elements per page
        }

        $resource = new ListResource(
            $this->serializer,
            $page,
            $apiRequest->getFields(),
            $apiRequest->getSort(),
            $apiRequest->getIncludedRelationships(),
            $apiRequest->getFilters()
        );
        
        $totalAmount = function() use ($request) {
            $id = (new Orders())->getKeyName();
            return Orders::query()
                ->where('employee_id', '=', $request->employee_id)
                ->get([$id])
                ->count();
        };

        $results = function()  use ($request) {
            return EloquentHelper::paginate(
                $this->serializer,
                Orders::query()
                    ->where('employee_id', '=', $request->employee_id)
            )->get();
        };

        $uri = route('employees.orders', ['employee_id' => $request->employee_id]);
        
        return $resource->get($totalAmount, $results, $uri, Orders::class);
    }
}
```


And you're ready to go. Yes, it is **THAT** simple!


## Examples: Consuming the API

### GET

This is the output for `EmployeesController@getAction` being consumed from command-line method issuing: `curl -X GET "http://localhost:9000/employees/1"`.

**Output:**

```
HTTP/1.1 200 OK
Cache-Control: private, max-age=0, must-revalidate
Content-type: application/vnd.api+json
```

```json
{
    "data": {
        "type": "employee",
        "id": "1",
        "attributes": {
            "company": "Northwind Traders",
            "surname": "Freehafer",
            "first_name": "Nancy",
            "email_address": "nancy@northwindtraders.com",
            "job_title": "Sales Representative",
            "business_phone": "(123)555-0100",
            "home_phone": "(123)555-0102",
            "mobile_phone": null,
            "fax_number": "(123)555-0103",
            "address": "123 1st Avenue",
            "city": "Seattle",
            "state_province": "WA",
            "zip_postal_code": "99999",
            "country_region": "USA",
            "web_page": "http://northwindtraders.com",
            "notes": null,
            "full_name": "Nancy Freehafer"
        },
        "links": {
            "self": {
                "href": "http://localhost:9000/employees/1"
            },
            "employee_orders": {
                "href": "http://localhost:9000/employees/1/orders"
            }
        },
        "relationships": {
            "latest_orders": [
                {
                    "data": {
                        "type": "order",
                        "id": "71"
                    }
                }
            ]
        }
    },
    "included": [        
        {
            "type": "order",
            "id": "71",
            "attributes": {
                "employee_id": "1",
                "customer_id": "1",
                "order_date": "2006-05-24 00:00:00",
                "shipped_date": null,
                "shipper_id": "3",
                "ship_name": "Anna Bedecs",
                "ship_address": "123 1st Street",
                "ship_city": "Seattle",
                "ship_state_province": "WA",
                "ship_zip_postal_code": "99999",
                "ship_country_region": "USA",
                "shipping_fee": "0.0000",
                "taxes": "0.0000",
                "payment_type": null,
                "paid_date": null,
                "notes": null,
                "tax_rate": "0",
                "tax_status_id": null,
                "status_id": "0"
            },
            "links": {
                "self": {
                    "href": "http://localhost:9000/orders/71"
                },
                "employee": {
                    "href": "http://localhost:9000/employees/1"
                }
            }
        }
    ],
    "links": {
        "employees": {
            "href": "http://localhost:9000/employees"
        },
        "employee_orders": {
            "href": "http://localhost:9000/employees/1/orders"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}
```


### POST

POST requires all member attributes to be accepted, even those hidden by the mapper. 

For instance, `attachments` member was hidden, but it is required, so it needs to be passed in with a valid value. On the other hand, `full_name` member value must not be passed in as an attribute or resource creation will fail.

Passing and `id` is optional and will be used instead of a server-side generated value if provided.

Sending the following data to the server using `POST`to the following URI  `http://localhost:9000/employees`: 

```json
{
    "data": {
        "type": "employee",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "nilportugues@example.com",
            "job_title": "Web Developer",
            "business_phone": "(123)555-0100",
            "home_phone": "(123)555-0102",
            "mobile_phone": null,
            "fax_number": "(123)555-0103",
            "address": "Plaça Catalunya 1",
            "city": "Barcelona",
            "state_province": "Barcelona",
            "zip_postal_code": "08028",
            "country_region": "Spain",
            "web_page": "http://nilportugues.com",
            "notes": null,
            "attachments": null
        }
    }        
}        
```

Will produce: 

```
HTTP/1.1 201 Created
Cache-Control: private, max-age=0, must-revalidate
Content-type: application/vnd.api+json
Location: http://localhost:9000/employees/10
```

Notice how 201 HTTP Status Code is returned and Location header too. Also `attachments` is not there anymore, and `full_name` was displayed.

```json
{
    "data": {
        "type": "employee",
        "id": "10",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "nilportugues@example.com",
            "job_title": "Web Developer",
            "business_phone": "(123)555-0100",
            "home_phone": "(123)555-0102",
            "mobile_phone": null,
            "fax_number": "(123)555-0103",
            "address": "Plaça Catalunya 1",
            "city": "Barcelona",
            "state_province": "Barcelona",
            "zip_postal_code": "08028",
            "country_region": "Spain",
            "web_page": "http://nilportugues.com",
            "notes": null,
            "full_name": "Nil Portugués"
        },
        "links": {
            "self": {
                "href": "http://localhost:9000/employees/10"
            },
            "employee_orders": {
                "href": "http://localhost:9000/employees/10/orders"
            }
        }
    },
    "links": {
        "employees": {
            "href": "http://localhost:9000/employees"
        },
        "employee_orders": {
            "href": "http://localhost:9000/employees/10/orders"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}
```

### PUT

PUT requires all member attributes to be accepted, just like POST.

For the sake of this example, we'll just send in a new `job_title` value, and keep everything else exactly the same.

It's important to notice this time we are required to pass in the `id`, even if it has been passed in by the URI, and of course the `id` values must match. Otherwise it will fail.


Sending the following data to the server using `PUT`to the following URI  `http://localhost:9000/employees/10`: 

```json
{
  "data": {
    "type": "employee",
    "id": 10,
    "attributes": {
      "company": "NilPortugues.com",
      "surname": "Portugués",
      "first_name": "Nil",
      "email_address": "nilportugues@example.com",
      "job_title": "Full Stack Web Developer",
      "business_phone": "(123)555-0100",
      "home_phone": "(123)555-0102",
      "mobile_phone": null,
      "fax_number": "(123)555-0103",
      "address": "Plaça Catalunya 1",
      "city": "Barcelona",
      "state_province": "Barcelona",
      "zip_postal_code": "08028",
      "country_region": "Spain",
      "web_page": "http://nilportugues.com",
      "notes": null,
      "attachments": null
    }
  }
}
```


Will produce: 

```
HTTP/1.1 200 OK
Cache-Control: private, max-age=0, must-revalidate
Content-type: application/vnd.api+json
```

```json
{
    "data": {
        "type": "employee",
        "id": "10",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "contact@nilportugues.com",
            "job_title": "Full Stack Web Developer",
            "business_phone": "(123)555-0100",
            "home_phone": "(123)555-0102",
            "mobile_phone": null,
            "fax_number": "(123)555-0103",
            "address": "Plaça Catalunya 1",
            "city": "Barcelona",
            "state_province": "Barcelona",
            "zip_postal_code": "08028",
            "country_region": "Spain",
            "web_page": "http://nilportugues.com",
            "notes": null,
            "full_name": "Nil Portugués"
        },
        "links": {
            "self": {
                "href": "http://localhost:9000/employees/10"
            },
            "employee_orders": {
                "href": "http://localhost:9000/employees/10/orders"
            }
        }
    },
    "included": [],
    "links": {
        "employees": {
            "href": "http://localhost:9000/employees"
        },
        "employee_orders": {
            "href": "http://localhost:9000/employees/10/orders"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}
```


### PATCH

PATCH allows partial updates, unlike PUT. 

We are required to pass in the `id` member, even if it has been passed in by the URI, and of course the `id` values must match. Otherwise it will fail.

For instance, sending the following data to the server using the following URI  `http://localhost:9000/employees/10`: 

```json
{
  "data": {
    "type": "employee",
    "id": 10,
    "attributes": {
      "email_address": "contact@nilportugues.com"
    }
  }
}
```

Will produce: 

```
HTTP/1.1 200 OK
Cache-Control: private, max-age=0, must-revalidate
Content-type: application/vnd.api+json
```

```json
{
    "data": {
        "type": "employee",
        "id": "10",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "contact@nilportugues.com",
            "job_title": "Full Stack Web Developer",
            "business_phone": "(123)555-0100",
            "home_phone": "(123)555-0102",
            "mobile_phone": null,
            "fax_number": "(123)555-0103",
            "address": "Plaça Catalunya 1",
            "city": "Barcelona",
            "state_province": "Barcelona",
            "zip_postal_code": "08028",
            "country_region": "Spain",
            "web_page": "http://nilportugues.com",
            "notes": null,
            "full_name": "Nil Portugués"
        },
        "links": {
            "self": {
                "href": "http://localhost:9000/employees/10"
            },
            "employee_orders": {
                "href": "http://localhost:9000/employees/10/orders"
            }
        }
    },
    "included": [],
    "links": {
        "employees": {
            "href": "http://localhost:9000/employees"
        },
        "employee_orders": {
            "href": "http://localhost:9000/employees/10/orders"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}
```



### DELETE

DELETE is the easiest method to use, as it does not require body. Just issue a DELETE to `http://localhost:9000/employees/10/` and `Employee` with `id 10` will be gone.

It will produce the following output: 

```
HTTP/1.1 204 No Content
Cache-Control: private, max-age=0, must-revalidate
Content-type: application/vnd.api+json
```

And notice how response will be empty:

```
```

<br>

## GET Query Params: include, fields, sort, page and filter

According to the standard, for GET method, it is possible to:
- Show only those fields requested using `fields`query parameter.
    - &fields[resource]=field1,field2
    
For instance, passing `/employees/10?fields[employee]=company,first_name` will produce the following output: 

```json
{
    "data": {
        "type": "employee",
        "id": "10",
        "attributes": {
            "company": "NilPortugues.com",
            "first_name": "Nil"
        },
        "links": {
            "self": {
                "href": "http://localhost:9000/employees/10"
            },
            "employee_orders": {
                "href": "http://localhost:9000/employees/10/orders"
            }
        }
    },
    "links": {
        "employees": {
            "href": "http://localhost:9000/employees"
        },
        "employee_orders": {
            "href": "http://localhost:9000/employees/10/orders"
        }
    },
    "jsonapi": {
        "version": "1.0"
    }
}
```
    
- Show only those `include` resources by passing in the relationship between them separated by dot, or just pass in list of resources separated by comma.
    - &include=resource1
    - &include=resource1.resource2,resource2.resource3
    
    
For instance, `/employees?include=order` will only load order type data inside `include` member, but `/employees?include=order.employee` will only load those orders related to the `employee` type.

- Sort results using `sort` and passing in the member names of the main resource defined in `data[type]` member. If it starts with a `-` order is `DESCENDING`, otherwise it's `ASCENDING`.

  - &sort=field1,-field2
  - &sort=-field1,field2
  
For instance: `/employees?sort=surname,-first_name`  

- Pagination is also defined to allow doing page pagination, cursor pagination or offset pagination.
  - &page[number]
  - &page[limit]
  - &page[cursor]
  - &page[offset]
  - &page[size]
  
For instance: `/employees?page[number]=1&page[size]=10`  

While the standard specifies that a 'filter' query parameter can be provided in order to restrict the list of items that will be returned it doesn't specify how the contents of that parameter should 
be structured.  In this implementation we've provided support for the following subset of RQL operators:

- Scalar operators: eq, ne, lt, le, gt, ge and like
- Array operators: in and out
- Logical operators: and, or and not

These can be used together to form complex queries such as to retrieve all employees whose surname starts with the letter 'b' and whose job title is not 'developer' or 'tester'.

For instance: `/employees?filter=and(like(surname,b*),not(or(eq(job_title,developer),eq(job_title,tester))))`

For more information on how to use and encode these operators please see the [Resource Query Language](https://github.com/persvr/rql) at the GitHub site. 


## POST/PUT/PATCH with Relationships

The JSON API allows resource creation and modification and passing in `relationships` that will create or alter existing resources too. 

Let's say we want to create a new `Employee` and pass in its first `Order`too. 

This could be done issuing 2 `POST` to the end-points (one for Employee, one for Order) or pass in the first `Order` as a `relationship` with our `Employee`, for instance:

```json
{
  "data": {
    "type": "employee",
    "attributes": {
        "company": "Northwind Traders",
        "surname": "Giussani",
        "first_name": "Laura",
        "email_address": "laura@northwindtraders.com",
        "job_title": "Sales Coordinator",
        "business_phone": "(123)555-0100",
        "home_phone": "(123)555-0102",
        "mobile_phone": null,
        "fax_number": "(123)555-0103",
        "address": "123 8th Avenue",
        "city": "Redmond",
        "state_province": "WA",
        "zip_postal_code": "99999",
        "country_region": "USA",
        "web_page": "http://northwindtraders.com",
        "notes": "Reads and writes French.",
        "full_name": "Laura Giussani"
    },    
    "relationships": {
      "order": {
        "data": [
          {
            "type": "order",
            "attributes": {
              "customer_id": "28",
              "order_date": "2006-05-11 00:00:00",
              "shipped_date": "2006-05-11 00:00:00",
              "shipper_id": "3",
              "ship_name": "Amritansh Raghav",
              "ship_address": "789 28th Street",
              "ship_city": "Memphis",
              "ship_state_province": "TN",
              "ship_zip_postal_code": "99999",
              "ship_country_region": "USA",
              "shipping_fee": "10.0000",
              "taxes": "0.0000",
              "payment_type": "Check",
              "paid_date": "2006-05-11 00:00:00",
              "notes": null,
              "tax_rate": "0",
              "tax_status_id": null,
              "status_id": "0"
            }
          }
        ]
      }
    }    
  }
}       
```

Due to the existance of this use case, we'll have to ajust our Controller implementation overwriting some methods provided by the **JsonApiController**: `createResourceCallable`, `updateResourceCallable` and `patchResourceCallable`.

Here's how it would be done for `createResourceCallable`.


```php
<?php namespace App\Http\Controllers;

use App\Model\Database\Employees;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use NilPortugues\Api\JsonApi\Server\Errors\Error;
use NilPortugues\Api\JsonApi\Server\Errors\ErrorBag;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;

class EmployeesController extends JsonApiController
{
    /**
     * Now you can actually create Employee and Orders at once.
     * Use transactions - DB::beginTransaction() for data integrity!
     *
     * @return callable
     */
    protected function createResourceCallable()
    {
        $createOrderResource = function (Model $model, array $data) {
            if (!empty($data['relationships']['order']['data'])) {
                $orderData = $data['relationships']['order']['data'];

                if (!empty($orderData['type'])) {
                    $orderData = [$orderData];
                }

                foreach ($orderData as $order) {
                    $attributes = array_merge($order['attributes'], ['employee_id' => $model->getKey()]);
                    Orders::create($attributes);
                }
            }
        };

        return function (array $data, array $values, ErrorBag $errorBag) use ($createOrderResource) {

            $attributes = [];
            foreach ($values as $name => $value) {
                $attributes[$name] = $value;
            }

            if (!empty($data['id'])) {
                $attributes[$this->getDataModel()->getKeyName()] = $values['id'];
            }

            DB::beginTransaction();
            try {
                $model = $this->getDataModel()->create($attributes);
                $createOrderResource($model, $data);
                DB::commit();
                return $model;
                
            } catch(\Exception $e) {
                DB::rollback();
                $errorBag[] = new Error('creation_error', 'Resource could not be created');
                throw $e;
            }
        };
    }

}
```

It is important, in order to use Transactions, do define in `Eloquent` models the `$fillable` values. 

Here's how `Employees` and `Orders` look like with `$fillable` defined.


**Employees (Eloquent Model) with $fillable**
```php
<?php namespace App\Model\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Employees extends Model
{
    public $timestamps = false;
    protected $table = 'employees';    
    protected $primaryKey = 'id';
    protected $appends = ['full_name'];
    
    /**
     * @var array
     */
    protected $fillable = [
        'company',
        'last_name',
        'first_name',
        'email_address',
        'job_title',
        'business_phone',
        'home_phone',
        'mobile_phone',
        'fax_number',
        'address',
        'city',
        'state_province',
        'zip_postal_code',
        'country_region',
        'web_page',
        'notes',
        'attachments',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestOrders()
    {
        return $this->hasMany(Orders::class, 'employee_id')->limit(10);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}

```


**Orders (Eloquent Model) with $fillable**

```php
<?php namespace App\Model\Database;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{   
    public $timestamps = false;
    protected $table = 'orders';
    protected $primaryKey = 'id';
    
    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'customer_id',
        'order_date',
        'shipped_date',
        'shipper_id',
        'ship_name',
        'ship_address',
        'ship_city',
        'ship_state_province',
        'ship_zip_postal_code',
        'ship_country_region',
        'shipping_fee',
        'taxes',
        'payment_type',
        'paid_date',
        'notes',
        'tax_rate',
        'tax_status_id',
        'status_id',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
}
```



## Custom Response Headers

Adding custom response headers can be done for multiple reasons: *versioning, setting expire headers, caching, setting private or public the served content...*

In order to do this, it's as simple as overwriting the JsonApiController `addHeaders` method. For instance, let's use the EmployeeController as an example: 


```php
<?php namespace App\Http\Controllers;

use App\Model\Database\Employees;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;
use Symfony\Component\HttpFoundation\Response;

class EmployeesController extends JsonApiController
{
    //All your supported methods...
    
    /**
     * @param Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response) {
        $response->headers->set('X-API-Version', '1.0');
        $response->setPublic();
        $response->setMaxAge(60);
        $response->setSharedMaxAge(60);

        return $response;
    }
}    
```

Now all supported actions will include the added custom headers.

# Common Errors and Solutions

### "Undefined index: @type"

This usually happens because you did not write the namespace of your `Mapping` in `config/jsonapi.php`. 
Double check, if missing, add it and refresh the resource. It should be gone!

----

<br>

## Contribute

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker](https://github.com/nilportugues/laravel5-jsonapi-transformer/issues/new).
* You can grab the source code at the package's [Git repository](https://github.com/nilportugues/laravel5-jsonapi-transformer).


<br>
## Support

Get in touch with me using one of the following means:

 - Emailing me at <contact@nilportugues.com>
 - Opening an [Issue](https://github.com/nilportugues/laravel5-jsonapi-transformer/issues/new)

<br>
## Authors

* [Nil Portugués Calderó](http://nilportugues.com)
* [The Community Contributors](https://github.com/nilportugues/laravel5-jsonapi-transformer/graphs/contributors)


## License
The code base is licensed under the [MIT license](LICENSE).
