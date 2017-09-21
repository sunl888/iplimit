<?php
/**
 * Created by PhpStorm.
 * User: 孙龙
 * Date: 2017/9/20
 * Time: 19:03
 */

namespace Wqer\IpLimit\Models;

use Illuminate\Database\Eloquent\Model;

class IpLimit extends Model
{
    protected $table = 'ip_limit';

    protected $guarded = [];

    protected $casts = [
        'is_expire' => 'boolean',
    ];
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}