<?php

namespace App\Http\Traits;

trait CheckServer
{
    public function ServerChecking()
    {
        if (env('DB_HOST') == '127.0.0.1') {
            return "local";
        } else {
            return "server";
        }
    }

    public function ToastrMessage($message)
    {

    }
}
