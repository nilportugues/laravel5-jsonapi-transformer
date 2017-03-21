<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/9/15
 * Time: 2:57 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\App\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use NilPortugues\Api\JsonApi\Http\Factory\RequestFactory;
use NilPortugues\Api\JsonApi\Server\Actions\ListResource;
use NilPortugues\Api\JsonApi\Server\Errors\Error;
use NilPortugues\Api\JsonApi\Server\Errors\ErrorBag;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;
use NilPortugues\Laravel5\JsonApi\Eloquent\EloquentHelper;
use NilPortugues\Tests\App\Models\Employees;
use NilPortugues\Tests\App\Models\Orders;

/**
 * Class EmployeeController.
 */
class EmployeesController extends JsonApiController
{
    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDataModel()
    {
        return new Employees();
    }

    /**
     * @return callable
     */
    private function createOrderResourceCallable()
    {
        return function (Model $model, array $data) {
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
    }

    /**
     * @return callable
     */
    protected function createResourceCallable()
    {
        $createOrderResource = $this->createOrderResourceCallable();

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
            } catch (\Exception $e) {
                DB::rollback();
                $errorBag[] = new Error('creation_error', 'Resource could not be created');
                throw new \Exception();
            }

        };
    }

    /**
     * @return callable
     */
    protected function updateResourceCallable()
    {
        $createOrderResource = $this->createOrderResourceCallable();

        return function (Model $model, array $data, array $values, ErrorBag $errorBag) use ($createOrderResource) {

            foreach ($values as $attribute => $value) {
                $model->$attribute = $value;
            }

            DB::beginTransaction();
            try {
                $model->update();
                $createOrderResource($model, $data);
                DB::commit();

                return $model;
            } catch (\Exception $e) {
                DB::rollback();
                $errorBag[] = new Error('update_error', 'Resource could not be updated');
                throw new \Exception();
            }

        };
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getOrdersByEmployee($id)
    {
        $apiRequest = RequestFactory::create();

        $page = $apiRequest->getPage();
        if (!$page->size()) {
            $page->setSize($this->pageSize);
        }

        $fields = $apiRequest->getFields();
        $sorting = $apiRequest->getSort();
        $included = $apiRequest->getIncludedRelationships();
        $filters = $apiRequest->getRawFilter();
        
        $resource = new ListResource($this->serializer, $page, $fields, $sorting, $included, $filters);

        $totalAmount = function () use ($id) {
            $idKey = (new Orders())->getKeyName();

            return Orders::query()->where('employee_id', '=', $id)->get([$idKey])->count();
        };

        $results = function () use ($id) {
            return EloquentHelper::paginate(
                $this->serializer,
                Orders::query()->where('employee_id', '=', $id)
            )->get();
        };

        $uri = route('employees.orders', ['employee_id' => $id]);

        return $resource->get($totalAmount, $results, $uri, Orders::class);
    }
}
