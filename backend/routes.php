<?php declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$r = is_string($path) ? $path : '/';
$m = $method;

if ($path === '/health.php' && $method === 'GET') {
    out(['status' => 'ok']);
}

if (in_array($method, ['POST', 'PATCH', 'DELETE'], true) && $path !== '/api/login' && $path !== '/api/register') {
    csrf();
}

require_once __DIR__ . '/src/Http/Routes/AuthRoutes.php';

if (handleAuthRoutes($r, $m)) {
    exit;
}
require_once __DIR__ . '/src/Http/Routes/UserRoutes.php';

if (handleUserRoutes($r, $m)) {
    exit;
}
require_once __DIR__ . '/src/Http/Routes/PostRoutes.php';

if (handlePostRoutes($r, $m)) {
    exit;
}
require_once __DIR__ . '/src/Http/Routes/StoryRoutes.php';
if (handleStoryRoutes($r, $m)) { exit; }
require_once __DIR__ . '/src/Http/Routes/MessageRoutes.php';
if (handleMessageRoutes($r, $m)) { exit; }
require_once __DIR__ . '/src/Http/Routes/SocialGraphRoutes.php';
if (handleSocialGraphRoutes($r, $m)) { exit; }
require_once __DIR__ . '/src/Http/Routes/NotificationRoutes.php';
if (handleNotificationRoutes($r, $m)) { exit; }

try{
if($r==='/api/media'&&$m==='POST'){ $u=me();rateLimit('media',(string)$u['id'],20,3600);if(empty($_FILES['file'])||$_FILES['file']['error']!==UPLOAD_ERR_OK)out(['error'=>'Upload failed'],422);$file=$_FILES['file'];if($file['size']>5242880)out(['error'=>'Maximum file size is 5 MB'],422);$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($file['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];if(!isset($allowed[$mime]))out(['error'=>'Unsupported image type'],422);$dir=__DIR__.'/uploads';if(!is_dir($dir)&&!mkdir($dir,0755,true))out(['error'=>'Upload storage unavailable'],500);$name=bin2hex(random_bytes(16)).'.'.$allowed[$mime];if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name))out(['error'=>'Unable to store upload'],500);out(['url'=>((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost').'/uploads/'.$name]);}
out(['error'=>'Not found'],404);
}catch(Throwable $e){out(['error'=>'Server error'],500);}