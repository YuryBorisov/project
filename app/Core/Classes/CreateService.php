<?php
namespace App\Core\Classes;

class CreateService{
    public static function create($typeService){
        switch ($typeService){
            case "VK":
                return new VK();
                break;
        }
    }
}