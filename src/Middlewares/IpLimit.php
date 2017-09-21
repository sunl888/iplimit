<?php

namespace Wqer\IpLimit\Middlewares;

use Closure;
use Wqer\IpLimit\IpLimitService;

class IpLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            'Request-Limit' => $this->ipLimit($request)->getLimit(),
            'Request-Limit-Expire-Data' => $this->ipLimit($request)->getExpireDate(),
            'Request-Already' => $this->ipLimit($request)->getLimit() - $this->ipLimit($request)->getCount()
        ];
        if ($this->ipLimit($request)->hasTooManyAttempts($request)) {
            /*return response([
                'data' => '访问次数限制!',
                'code' => 429,
                'status_code' => 429,
            ],429, $headers);*/
			return response('访问次数限制!',429, $headers);
        }
        return $next($request);
    }

    public function ipLimit($request)
    {
        return new IpLimitService($request);
    }
}
