<?php

namespace Spatie\HttpLogger;

use Illuminate\Http\Request;

interface LogProfile
{
    public function shouldLogRequest(Request $request): bool;

    public function logRequest(Request $request): void;
}
