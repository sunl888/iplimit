<?php
/**
 * Created by PhpStorm.
 * User: 孙龙
 * Date: 2017/9/20
 * Time: 18:53
 */
namespace Wqer\IpLimit;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Wqer\IpLimit\Models\IpLimit;

class IpLimitService
{
    protected $limit;
    protected $seconds;
    protected $ip_model;

    public function __construct(Request $request)
    {
        $this->limit = config('iplimit.request_limit');
        $this->seconds = config('iplimit.expire_second');
        if (!$this->hasIp($request)) {
            $this->addIp($request);
        }
    }

    public function hasTooManyAttempts(Request $request)
    {
        $flag = $this->ip_model->count >= $this->limit;
        if(!$flag){
            $this->incrementAttempts($request);
        }
        return $flag;
    }

    protected function incrementAttempts()
    {
        return $this->ip_model->increment('count');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function ip(Request $request)
    {
        $this->ip_model = IpLimit::recent()->where(['ip' => $request->ip(), 'is_expire' => 0])->first();
        return $this->ip_model;
    }

    private function addIp(Request $request)
    {
        $this->ip_model = IpLimit::create(['ip' => $request->ip(), 'expire_date' => Carbon::now()->addSeconds($this->seconds)]);
        return $this->ip_model;
    }

    protected function hasIp(Request $request)
    {
        $this->setExpire($request);
        return (boolean)($this->ip($request));
    }

    protected function setExpire(Request $request)
    {
        $this->ip_model = $this->ip($request);
        if ($this->ip_model && Carbon::now()->gt(Carbon::parse($this->ip_model->expire_date))) {
            $data['is_expire'] = true;
            $this->ip_model->update($data);
        }
    }

    public function getLimit(){
        return $this->limit;
    }

    public function getExpireDate(){
        return $this->ip_model->expire_date;
    }

    public function getCount(){
        return $this->ip_model->count;
    }
}