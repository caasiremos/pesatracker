<?php

namespace App\Utils;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class Logger
{
    /**
     * Log any information needed to be logger for example,
     * Request Payload, Http Response, Debugging messages etc
     */
    public static function info($message)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        Log::info($message);
    }

    /**
     * Logs custom exceptions passed to it or throwable
     */
    public static function error(Throwable $throwable = null, Exception $exception = null)
    {
        $exceptionThrowable = $throwable ?: $exception;
        Log::error("{$exceptionThrowable->getMessage()} @ {$exceptionThrowable->getFile()}: {$exceptionThrowable->getLine()}");
    }
}
