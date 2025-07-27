<?php

namespace App\Core;

use App\Core\Database;

abstract class Model
{
    protected static function db(): Database
    {
        return Database::getInstance();
    }
}
