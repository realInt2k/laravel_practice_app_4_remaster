<?php

namespace App\Services;

use Exception;


class BaseService
{
    public function throwException($message, Exception $e)
    {
        throw new Exception($message . ": " . $e->getMessage());
    }
}
