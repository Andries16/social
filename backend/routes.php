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

try{
if($r==='/api/media'&&$m==='POST'){ $u=me();rateLimit('media',(string)$u['id'],20,3600);if(empty($_FILES['file'])||$_FILES['file']['error']!==UPLOAD_ERR_OK)out(['error'=>'Upload failed'],422);$file=$_FILES['file'];if($file['size']>5242880)out(['error'=>'Maximum file size is 5 MB'],422);$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($file['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];if(!isset($allowed[$mime]))out(['error'=>'Unsupported image type'],422);$dir=__DIR__.'/uploads';if(!is_dir($dir)&&!mkdir($dir,0755,true))out(['error'=>'Upload storage unavailable'],500);$name=bin2hex(random_bytes(16)).'.'.$allowed[$mime];if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name))out(['error'=>'Unable to store upload'],500);out(['url'=>((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost').'/uploads/'.$name]);}
if(preg_match('#^/api/stories/(\d+)/view$#',$r,$x)&&$m==='POST'){ $u=me();$storyId=(int)$x[1];$q=$db->prepare("SELECT s.user_id,COALESCE((SELECT private_account FROM user_settings s2 WHERE s2.user_id=s.user_id),0) private_account FROM stories s WHERE s.id=?");$q->execute([$storyId]);$story=$q->fetch(PDO::FETCH_ASSOC);if(!$story)out(['error'=>'Story not found'],404);if((bool)$story['private_account']&&(int)$story['user_id']!==(int)$u['id']){$follow=$db->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');$follow->execute([$u['id'],(int)$story['user_id']]);if(!$follow->fetchColumn())out(['error'=>'Story not found'],404);}$q=$db->prepare('INSERT OR IGNORE INTO story_views(story_id,user_id,created_at)VALUES(?,?,?)');$q->execute([$storyId,$u['id'],date('c')]);out(['ok'=>true]);}
if(preg_match('#^/api/notifications/(\d+)/read$#',$r,$x)&&$m==='POST'){ $u=me();$q=$db->prepare('UPDATE notifications SET read_at=? WHERE id=? AND user_id=?');$q->execute([date('c'),(int)$x[1],$u['id']]);out(['ok'=>true]);}
if($r==='/api/notifications'&&$m==='GET'){$u=me();$q=$db->prepare('SELECT n.*,u.id uid,u.name,u.avatar,u.bio FROM notifications n JOIN users u ON u.id=n.actor_id WHERE n.user_id=? ORDER BY n.id DESC LIMIT 30');$q->execute([$u['id']]);$items=$q->fetchAll(PDO::FETCH_ASSOC);foreach($items as&$n)$n['actor']=['id'=>$n['uid'],'name'=>$n['name'],'avatar'=>$n['avatar'],'bio'=>$n['bio']];out(['notifications'=>$items]);}out(['error'=>'Not found'],404);
}catch(Throwable $e){out(['error'=>'Server error'],500);}