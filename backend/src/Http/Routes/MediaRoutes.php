<?php declare(strict_types=1);

function handleMediaRoutes(string $r, string $m): bool
{
    global $db;

    if($r==='/api/media'&&$m==='POST'){ $u=me();rateLimit('media',(string)$u['id'],20,3600);if(empty($_FILES['file'])||$_FILES['file']['error']!==UPLOAD_ERR_OK)out(['error'=>'Upload failed'],422);$file=$_FILES['file'];if($file['size']>5242880)out(['error'=>'Maximum file size is 5 MB'],422);$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($file['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];if(!isset($allowed[$mime]))out(['error'=>'Unsupported image type'],422);$dir=dirname(__DIR__, 3).'/uploads';if(!is_dir($dir)&&!mkdir($dir,0755,true))out(['error'=>'Upload storage unavailable'],500);$name=bin2hex(random_bytes(16)).'.'.$allowed[$mime];if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name))out(['error'=>'Unable to store upload'],500);out(['url'=>((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost').'/uploads/'.$name]);}
    
    return false;
}
