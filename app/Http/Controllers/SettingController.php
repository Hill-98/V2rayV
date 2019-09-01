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

    public function get()
    {
        return Response::result(true, 0, "", $this->model->get());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        try {
            $result = $this->model->save($request->post());
            return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
        } catch (ValidationException $e) {
            return Response::result(
                false,
                $e->getCode(),
                $e->getMessage(),
                ["key" => $e->getKey(), "status" => $e->getStatus()]
            );
        }
    }

    public function getMainServer()
    {
        return Response::result(true, 0, "", ["id" => $this->model->getMainServer()]);
    }

    public function setMainServer(Request $request)
    {
        $request->validate([
            "id" => "required|numeric",
        ]);
        $result = $this->model->setMainServer(intval($request->input("id")));
        return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
    }
}
