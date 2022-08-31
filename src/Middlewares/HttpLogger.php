<?php

namespace Spatie\HttpLogger\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Spatie\HttpLogger\LogProfile;
use Spatie\HttpLogger\LogWriter;
use Illuminate\Support\Facades\Log;

class HttpLogger
{
    protected $logProfile;

    protected $logWriter;

    protected $hash;

    public function __construct(LogProfile $logProfile, LogWriter $logWriter)
    {
        $this->logProfile = $logProfile;
        $this->logWriter = $logWriter;
        $this->hash = md5(env('APP_NAME', '') .'-'. microtime(true) .'-'. rand(100,999));
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (env('LOG_HTTP', false) && $this->logProfile->shouldLogRequest($request)) {

            $this->logWriter->logRequest($request, $this->hash);

            Log::channel(config('http-logger.log_channel'))->log(config('http-logger.log_level', 'info'), "Response [{$this->hash}]: " . json_encode($response));
        }

        return $response;
    }
}
