<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Server
 *
 * @property int $id
 * @property string $name
 * @property int $subscribe_id
 * @property string $address
 * @property int $port
 * @property string $protocol
 * @property string|array $protocol_setting
 * @property string $network
 * @property string|array $network_setting
 * @property string $security
 * @property string|array $security_setting
 * @property string|array $mux
 * @property int $local_port
 * @property int $enable
 * @property-read \App\Models\Subscribe $subscribe
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereLocalPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereMux($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereNetworkSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereProtocolSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereSecurity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereSecuritySetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Server whereSubscribeId($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
    public $timestamps = false;

    protected $attributes = [
        "subscribe_id" => 0,
        "local_port" => 0
    ];

    protected $guarded = [];

    protected $casts = [
        "subscribe_id" => "int",
        "port" => "int",
        "local_port" => "int",
        "protocol_setting" => "array",
        "network_setting" => "array",
        "security_setting" => "array",
        "mux" => "array",
        "enable" => "bool"
    ];

    public function subscribe()
    {
        return $this->hasOne(Subscribe::class, "id", "subscribe_id");
    }
}
