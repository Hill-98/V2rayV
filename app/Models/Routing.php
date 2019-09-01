<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\Routing
 *
 * @property int $id
 * @property string $name
 * @property string|array $proxy
 * @property string|array $direct
 * @property string|array $block
 * @property string $port
 * @property string $network
 * @property string|array $protocol
 * @property string|array $servers
 * @property int $enable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereBlock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereDirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereProxy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routing whereServers($value)
 * @mixin \Eloquent
 */
class Routing extends Model
{
    protected $table = "routing";

    public $timestamps = false;

    protected $casts = [
        "proxy" => "array",
        "direct" => "array",
        "block" => "array",
        "protocol" => "array",
        "servers" => "array",
        "enable" => "bool"
    ];
}
