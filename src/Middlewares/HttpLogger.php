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

    public function __construct(LogProfile $logProfile, LogWriter $logWriter)
    {
        $this->logProfile = $logProfile;
        $this->logWriter = $logWriter;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (env('LOG_HTTP', false) && $this->logProfile->shouldLogRequest($request)) {
            $hash = md5(strtotime('now'));
            $this->logWriter->logRequest($request, $hash);

            Log::channel(config('http-logger.log_channel'))->log(config('http-logger.log_level', 'info'), "Response [{$hash}]: " . json_encode($response));
        }

        return $response;
    }
}
