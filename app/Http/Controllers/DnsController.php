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

    public function get()
    {
        return Response::result(true, 0, "", $this->model->readFile());
    }

    public function put(Request $request)
    {
        if (isset($request->post()["data"])) {
            $result = $this->model->writeFile($request->post()["data"]);
            return Response::result($result, $result ? 0 : ErrorCode::DATA_SAVE_FAIL);
        } else {
            return Response::result(false, ErrorCode::DATA_SAVE_FAIL);
        }
    }
}
