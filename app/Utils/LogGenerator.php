<?php
namespace App\Utils;

use App\Models\SystemLog;
use Carbon\Carbon;

trait LogGenerator
{
    public function storeLog($user, $functionName, $errorMessage)
    {
        SystemLog::insert([
            'user' => $user,
            'function' => $functionName,
            'log' => $errorMessage,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}