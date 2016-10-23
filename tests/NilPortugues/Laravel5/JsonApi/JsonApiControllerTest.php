<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/9/15
 * Time: 3:05 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Laravel5\JsonApi;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class JsonApiControllerTest extends LaravelTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testListActionCanSort()
    {
        $this->call('GET', 'http://localhost/employees?sort=-id');
        $response = $this->response;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
        $this->assertContains('&sort=-id', $response->getContent());
    }

    public function testListActionCanFilterMembers()
    {
        $this->call('GET', 'http://localhost/employees?fields[employee]=company,first_name');
        $response = $this->response;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
        $this->assertContains('&fields[employee]=company,first_name', $response->getContent());
    }

    public function testListAction()
    {
        $response = $this->call('GET', 'http://localhost/employees');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testGetAction()
    {
        $this->createNewEmployee();
        $response = $this->call('GET', 'http://localhost/employees/1');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testPatchAction()
    {
        $this->createNewEmployee();

        $content = <<<JSON
{
    "data": {
        "type": "employee",
        "attributes": {
            "job_title": "Senior Web Developer"
        }
    }
}
JSON;
        $response = $this->call('PATCH', 'http://localhost/employees/1', json_decode($content, true), [], [], [], '');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testPutAction()
    {
        $this->createNewEmployee();

        $content = <<<JSON
{
    "data": {
        "type": "employee",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "nilportugues@localhost",
            "job_title": "Senior Web Developer",
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
JSON;
        $response = $this->call('PUT', 'http://localhost/employees/1', json_decode($content, true), [], [], [], '');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testDeleteAction()
    {
        $this->createNewEmployee();
        $response = $this->call('DELETE', 'http://localhost/employees/1');

        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @return \Illuminate\Http\Response
     */
    private function createNewEmployee()
    {
        $content = <<<JSON
{
    "data": {
        "type": "employee",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "nilportugues@localhost",
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
JSON;
        $response = $this->call('POST', 'http://localhost/employees', json_decode($content, true), [], [], []);

        return $response;
    }

    public function testGetActionWhenEmployeeDoesNotExist()
    {
        //error need to be tested as production or exception will be thrown in debug mode.
        $this->app['config']->set('app.debug', false);

        $response = $this->call('GET', 'http://localhost/employees/1000');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testPostAction()
    {
        $response = $this->createNewEmployee();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
        $this->assertEquals('http://localhost/employees/1', $response->headers->get('Location'));
    }

    public function testPostActionCreateNonexistentTypeAndReturnErrors()
    {
        //error need to be tested as production or exception will be thrown in debug mode.
        $this->app['config']->set('app.debug', false);

        $content = <<<JSON
{
    "data": {
        "type": "not_employee",
        "attributes": {}
    }
}
JSON;
        $response = $this->call('POST', 'http://localhost/employees', json_decode($content, true), [], [], []);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testPostActionReturnsErrorBecauseAttributesAreMissing()
    {
        //error need to be tested as production or exception will be thrown in debug mode.
        $this->app['config']->set('app.debug', false);

        $content = <<<JSON
{
    "data": {
        "type": "employee",
        "attributes": {
            "company": "NilPortugues.com",
            "surname": "Portugués",
            "first_name": "Nil",
            "email_address": "nilportugues@localhost",
            "job_title": "Web Developer",
            "business_phone": "(123)555-0100",
            "home_phone": "(123)555-0102",
            "mobile_phone": null,
            "country_region": "Spain",
            "web_page": "http://nilportugues.com",
            "notes": null,
            "attachments": null
        }
    }
}
JSON;
        $response = $this->call('POST', 'http://localhost/employees', json_decode($content, true), [], [], []);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testPatchActionWhenEmployeeDoesNotExistReturns404()
    {
        //error need to be tested as production or exception will be thrown in debug mode.
        $this->app['config']->set('app.debug', false);

        $content = <<<JSON
{
  "data": {
    "type": "employee",
    "id": 1000,
    "attributes": {
      "email_address": "nilopc@github.com"
    }
  }
}
JSON;
        $response = $this->call(
            'PATCH',
            'http://localhost/employees/1000',
            json_decode($content, true),
            [],
            [],
            []
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }

    public function testPutActionWhenEmployeeDoesNotExistReturns404()
    {
        //error need to be tested as production or exception will be thrown in debug mode.
        $this->app['config']->set('app.debug', false);

        $content = <<<JSON
{
  "data": {
    "type": "employee",
    "id": 1000,
    "attributes": {
          "company": "NilPortugues.com",
          "surname": "Portugués",
          "first_name": "Nil",
          "email_address": "nilportugues@localhost",
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
JSON;
        $response = $this->call(
            'PUT',
            'http://localhost/employees/1000',
            json_decode($content, true),
            [],
            [],
            []
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('Content-type'));
    }
}
