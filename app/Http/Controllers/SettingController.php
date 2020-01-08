<?php

namespace App\Http\Controllers;

use App\Exceptions\V2ray\ValidationException;
use App\Models\ErrorCode;
use App\V2rayV\Setting;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        return Response::result(true, 0, '', $this->model->get());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): ?JsonResponse
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
     * @return JsonResponse
     */
    public function getMainServer(): JsonResponse
    {
        return Response::result(true, 0, '', ['id' => $this->model->main_server]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setMainServer(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        $result = $this->model->setMainServer((int)$request->input('id'));
        return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
    }
}
