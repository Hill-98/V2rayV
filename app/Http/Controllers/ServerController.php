<?php

namespace App\Http\Controllers;

use App\Exceptions\V2ray\ValidationException;
use App\Http\Resources\Server as ResourceModel;
use App\Http\Resources\ServerCollection as ResourceCollection;
use App\Models\DataFilter;
use App\V2rayV\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ServerController extends Controller
{
    private $model;
    private $filter;

    /**
     * ServerController constructor.
     * @param Server $server
     * @param DataFilter $filter
     */
    public function __construct(Server $server, DataFilter $filter)
    {
        $this->model = $server;
        $this->filter = $filter;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->save($request->post());
    }

    /**
     * @param Request $request
     * @param int $id
     * @return ResourceModel|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            return new ResourceModel($this->model->get((int)$id));
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        return $this->save($request->post(), $id);
    }

    /**
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function switch(Request $request): \Illuminate\Http\JsonResponse
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        try {
            /** @var \App\Models\Server $server */
            $server = $this->model->get((int)$request->input('id'));
            exec("cmd /c chcp 65001 && ping -w 1000 \"$server->address\"", $output);
            return Response::result(true, 0, '', array_slice($output, 2));
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportConfig(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'servers' => 'required',
            'type' => 'required'
        ]);
        $servers = explode(',', $request->input('servers'));
        foreach ($servers as &$server) {
            $server = (int)trim($server);
        }
        unset($server);
        $type = $request->input('type');
        switch ($type) {
            case 'client':
                return Response::result(true, 0, '', $this->model->exportConfig($servers, true));
            case 'server':
                return Response::result(true, 0, '', $this->model->exportConfig($servers, false));
            default:
                return Response::result(false);
        }
    }

    /**
     * @param array $config
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    private function save(array $config, int $id = 0): \Illuminate\Http\JsonResponse
    {
        try {
            if (isset($config['subscribe_id'])) {
                unset($config['subscribe_id']);
            }
            if ($id === 0) {
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
                ['key' => $e->getKey(),
                'status' => $e->getStatus()]
            );
        } catch (\Exception $e) {
            return Response::result(false, $e->getCode(), $e->getMessage());
        }
    }
}
