<?php

namespace App\Http\Controllers;

use App\Models\ErrorCode;
use App\V2rayV\File\Dns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DnsController extends Controller
{
    private $model;

    /**
     * DnsController constructor.
     * @param Dns $dns
     */
    public function __construct(Dns $dns)
    {
        $this->model = $dns;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(): \Illuminate\Http\JsonResponse
    {
        return Response::result(true, 0, '', $this->model->readFile());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function put(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isset($request->post()['data'])) {
            $result = $this->model->writeFile($request->post()['data']);
            return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
        }
        return Response::result(false, ErrorCode::DATA_SAVE_FAIL);
    }
}
