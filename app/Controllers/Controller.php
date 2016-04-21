<?php
namespace App\Controllers;

use App\Core\Classes\CreateService;
use App\Core\Classes\Manager;

class Controller{
    public function get($typeService, $id){
      return Manager::call(CreateService::create($typeService), $id);
    }

    public function downloadZIP($fileName){
        $fileName = $_SERVER['DOCUMENT_ROOT'].'/UsersZipFiles/'.$fileName.'.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileName));
        readfile($fileName);
    }
    
}