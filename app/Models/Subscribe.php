<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Subscribe
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $mux
 * @property string $password
 * @property int $auto_update
 * @property int $proxy_update
 * @property int $last_success
 * @property string|null|Carbon $update_at
 * @property-read mixed $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereLastSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereMux($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereProxyUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereUpdateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscribe whereUrl($value)
 * @mixin \Eloquent
 */
class Subscribe extends Model
{
    protected $table = "subscribe";
    protected $attributes = [
        "last_success" => false,
    ];
    public $timestamps = false;

    protected $casts = [
        "auto_update" => "bool",
        "proxy_update" => "bool",
        "mux" => "array"
    ];

    protected $dates = [
        "update_at"
    ];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function getLastSuccessAttribute($value)
    {
        if ($value !== null) {
            $value = boolval($value);
        }
        return $value;
    }
}
