<?php

namespace App\Http\Controllers;

use App\Jobs\V2rayControl;
use App\V2rayV\ShareURL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ShareURLController extends Controller
{
    private $shareURL;

    /**
     * ShareController constructor.
     * @param ShareURL $shareURL
     */
    public function __construct(ShareURL $shareURL)
    {
        $this->shareURL = $shareURL;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'servers' => 'required',
            'encrypt' => 'boolean'
        ]);
        $servers = explode(',', $request->input('servers'));
        foreach ($servers as &$server) {
            $server = (int)trim($server);
        }
        unset($server);
        $data = $this->shareURL->export($servers, (bool)$request->input('encrypt'));
        return Response::result(true, 0, '', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->post();
        return Response::result(true, 0, '', $this->shareURL->import($data, $request->input('password', '')));
    }
}
