<?php

namespace App\Core\Classes;

use App\Core\Interfaces\IService;

class Manager{
    public static function call(IService $service, $id){
        return $service->run($id);
    }
}