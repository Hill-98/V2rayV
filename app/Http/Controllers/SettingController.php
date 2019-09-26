<?php

namespace App\Http\Controllers;

use App\Exceptions\V2ray\ValidationException;
use App\Jobs\V2rayControl;
use App\Models\ErrorCode;
use App\V2rayV\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SettingController extends Controller
{
    private $model;

    /**
     * SettingController constructor.
     * @param Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->model = $setting;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(): \Illuminate\Http\JsonResponse
    {
        return Response::result(true, 0, '', $this->model->get());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->model->save($request->post());
            return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
        } catch (ValidationException $e) {
            return Response::result(
                false,
                $e->getCode(),
                $e->getMessage(),
                ['key' => $e->getKey(), 'status' => $e->getStatus()]
            );
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMainServer(): \Illuminate\Http\JsonResponse
    {
        return Response::result(true, 0, '', ['id' => $this->model->main_server]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMainServer(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        $result = $this->model->setMainServer((int)$request->input('id'));
        return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
    }
}
