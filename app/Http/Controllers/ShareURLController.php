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

    public function export(Request $request)
    {
        $request->validate([
            "servers" => "required",
            "encrypt" => "boolean"
        ]);
        $servers = explode(",", $request->input("servers"));
        foreach ($servers as &$server) {
            $server = intval(trim($server));
        }
        $data = $this->shareURL->export($servers, boolval($request->input("encrypt")));
        return Response::result(true, 0, "", $data);
    }

    public function import(Request $request)
    {
        $data = $request->post();
        return Response::result(true, 0, "", $this->shareURL->import($data, $request->input("password", "")));
    }
}
