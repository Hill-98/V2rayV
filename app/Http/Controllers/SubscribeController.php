<?php

namespace App\Http\Controllers;

use App\Exceptions\V2ray\ValidationException;
use App\Http\Resources\Subscribe as ResourceModel;
use App\Http\Resources\SubscribeCollection as ResourceCollection;
use App\Jobs\SubscribeUpdate;
use App\Models\DataFilter;
use App\V2rayV\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SubscribeController extends Controller
{
    private $model;
    private $filter;

    /**
     * SubscribeController constructor.
     * @param Subscribe $subscribe
     * @param DataFilter $filter
     */
    public function __construct(Subscribe $subscribe, DataFilter $filter)
    {
        $this->model = $subscribe;
        $this->filter = $filter;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return new ResourceCollection($this->model->list(true, $this->filter->filter, $this->filter->filer_value));
    }

    /**
     * @return ResourceCollection
     */
    public function all(): ResourceCollection
    {
        return new ResourceCollection($this->model->list(false, $this->filter->filter, $this->filter->filer_value));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->save($request->post());
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return ResourceModel|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            return new ResourceModel($this->model->get($id));
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        return $this->save($request->post(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            return Response::result(true, 0, '', ['id' => $this->model->delete($id)]);
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribeUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        SubscribeUpdate::dispatch((int)$request->input('id'));
        return Response::result(true);
    }

    /**
     * @param array $config
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    private function save(array $config, int $id = 0): \Illuminate\Http\JsonResponse
    {
        try {
            if (empty($id)) {
                $result_id = $this->model->add($config);
            } else {
                $result_id = $this->model->update($config, $id);
            }
            return Response::result(true, 0, '', ['id' => $result_id]);
        } catch (ValidationException $e) {
            return Response::result(
                false,
                $e->getCode(),
                $e->getMessage(),
                ['key' => $e->getKey(), 'status' => $e->getStatus()]
            );
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }
}
