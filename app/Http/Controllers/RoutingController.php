<?php

namespace App\Http\Controllers;

use App\Exceptions\V2ray\ValidationException;
use App\Http\Resources\Routing as ResourceModel;
use App\Http\Resources\RoutingCollection as ResourceCollection;
use App\Models\DataFilter;
use App\V2rayV\DefaultRouting;
use App\V2rayV\Routing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RoutingController extends Controller
{
    private $model;
    private $filter;
    private $defaultRouting;

    /**
     * Routing constructor.
     * @param Routing $routing
     * @param DataFilter $filter
     * @param DefaultRouting $defaultRouting
     */
    public function __construct(Routing $routing, DataFilter $filter, DefaultRouting $defaultRouting)
    {
        $this->model = $routing;
        $this->filter = $filter;
        $this->defaultRouting = $defaultRouting;
    }

    /**
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
     * @param  \Illuminate\Http\Request  $request
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
     * @param string $id
     * @return ResourceModel|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        if ($id === '0') {
            return Response::result(true, 0, "", $this->defaultRouting->get());
        }
        try {
            return new ResourceModel($this->model->get((int)$id));
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        return $this->save($request->post(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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
     * @return \Illuminate\Http\Response
     */
    public function switch(Request $request): \Illuminate\Http\Response
    {
        $request->validate([
            'enable' => 'required|boolean',
        ]);
        return Response::result(true, 0, '', $this->model->switch(
            $request->post(),
            (bool)$request->input('enable')
        ));
    }

    /**
     * @param array $config
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    private function save(array $config, $id = 0): \Illuminate\Http\JsonResponse
    {
        if (!isset($config['port'])) {
            $config['port'] = '';
        }
        try {
            if ($id === '0') {
                $this->defaultRouting->put($config);
                return Response::result(true);
            }
            if (empty($id)) {
                $result_id = $this->model->add($config);
            } else {
                $result_id = $this->model->update($config, $id);
            }
            return Response::result(true, $code ?? 0, $msg ?? '', ['id' => $result_id]);
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
