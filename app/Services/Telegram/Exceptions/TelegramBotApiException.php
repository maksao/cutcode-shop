<?php

namespace App\Services\Telegram\Exceptions;

use Exception;
use Illuminate\Http\Request;

class TelegramBotApiException extends Exception
{
    public function render(Request $request)
    {
        return response()->json([]);
    }
}