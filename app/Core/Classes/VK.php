<?php

namespace App\Core\Classes;

use App\Core\Interfaces\IService;

class VK implements IService{

    const COUNT_WALL = 50;

    const MAIN_DIR = "UsersZipFiles/";

    public function run($id)
    {
        $attr = is_numeric($id) ? "owner_id={$id}" : "domain={$id}";
        $wall = json_decode(file_get_contents("https://api.vk.com/method/wall.get?v=5.50&{$attr}&count=".self::COUNT_WALL));
        $user = json_decode(file_get_contents("https://api.vk.com/method/users.get?v=5.50&user_ids={$id}&fields=id,photo_max_orig,first_name,last_name"));
        if(isset($user->error->error_code) && isset($wall->error->error_code))
           return json_encode(['status' => 'error', 'msg' => 'Некорректный идентификатор']);
        $path = $_SERVER['DOCUMENT_ROOT']."/".self::MAIN_DIR;
        $v = "{$user->response[0]->first_name}_{$user->response[0]->last_name}_id{$user->response[0]->id}";
        if(file_exists("{$path}{$v}.zip")) unlink("{$path}{$v}.zip");
        $zip = new \ZipArchive();
        $zip->open("{$path}{$v}.zip", \ZipArchive::CREATE);
        $zip->addFromString("{$v}/avatar.jpg", file_get_contents($user->response[0]->photo_max_orig));
        $text = 0;
        $photos = 0;
        $audio = 0;
        for($i = 0, $lItems = count($wall->response->items); $i < $lItems; $i++){
            if(isset($wall->response->items[$i]->copy_history)) 
                $post = $wall->response->items[$i]->copy_history[0];
            else 
                $post = $wall->response->items[$i];
            if(strlen($post->text) > 0) $zip->addFromString("/{$v}/Text/" . (++$text) . ".txt", $post->text);
            for($j = 0, $l = isset($post->attachments) ? count($post->attachments) : 0; $j < $l; $j++){
                switch ($post->attachments[$j]->type){
                    case 'photo':
                        $zip->addFromString("{$v}/Photos/" . (++$photos) . ".jpg", file_get_contents($post->attachments[$j]->photo->photo_130));
                        break;
                    case 'audio':
                        $zip->addFromString("{$v}/Audio/" . (++$audio) . ". {$post->attachments[$j]->audio->artist} - {$post->attachments[$j]->audio->title}.mp3\"", file_get_contents($post->attachments[$j]->audio->url));
                        break;
                }
            }
        }
        $zip->close();
        return json_encode(['link' => $v]);
    }

}