<?php declare(strict_types=1);

function handleAuthRoutes(string $r, string $m): bool
{
    global $db;

    if($r==='/api/register'&&$m==='POST'){rateLimit('register',clientKey(),5,900);$x=b();$name=trim((string)($x['name']??''));$email=strtolower(trim((string)($x['email']??'')));if($name===''||mb_strlen($name)>120)out(['error'=>'Name must contain between 1 and 120 characters'],422);if(!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>254)out(['error'=>'A valid email address is required'],422);if(strlen($x['password']??'')<6)out(['error'=>'Password must contain at least 6 characters'],422);$q=$db->prepare('INSERT INTO users(name,email,password,created_at)VALUES(?,?,?,?)');$q->execute([$name,$email,password_hash($x['password'],PASSWORD_DEFAULT),date('c')]);session_regenerate_id(true);$_SESSION['user_id']=$db->lastInsertId();out(['user'=>me(),'csrf'=>$_SESSION['csrf']]);}
    if($r==='/api/login'&&$m==='POST'){rateLimit('login',clientKey(),10,900);$x=b();$q=$db->prepare('SELECT * FROM users WHERE email=?');$q->execute([strtolower(trim($x['email']??''))]);$u=$q->fetch(PDO::FETCH_ASSOC);if(!$u||!password_verify($x['password']??'',$u['password']))out(['error'=>'Invalid credentials'],401);session_regenerate_id(true);$_SESSION['user_id']=$u['id'];out(['user'=>me(),'csrf'=>$_SESSION['csrf']]);}
    if($r==='/api/logout'&&$m==='POST'){$_SESSION=[];session_destroy();out(['ok'=>true]);}
    if($r==='/api/me'&&$m==='GET')out(['user'=>me(),'csrf'=>$_SESSION['csrf']]);
    if($r==='/api/settings'&&$m==='GET'){ $u=me();$q=$db->prepare('SELECT private_account,email_notifications FROM user_settings WHERE user_id=?');$q->execute([$u['id']]);$s=$q->fetch(PDO::FETCH_ASSOC);if(!$s){$s=['private_account'=>0,'email_notifications'=>1];}$s['private_account']=(bool)$s['private_account'];$s['email_notifications']=(bool)$s['email_notifications'];out(['settings'=>$s]);}
    if($r==='/api/settings'&&$m==='PATCH'){ $u=me();$x=b();$private=!empty($x['private_account'])?1:0;$email=!empty($x['email_notifications'])?1:0;$q=$db->prepare('INSERT INTO user_settings(user_id,private_account,email_notifications)VALUES(?,?,?) ON CONFLICT(user_id) DO UPDATE SET private_account=excluded.private_account,email_notifications=excluded.email_notifications');$q->execute([$u['id'],$private,$email]);out(['ok'=>true]);}
    if($r==='/api/me'&&$m==='PATCH'){$u=me();$x=b();$bio=array_key_exists('bio',$x)?trim((string)$x['bio']):$u['bio'];$avatar=array_key_exists('avatar',$x)?trim((string)$x['avatar']):$u['avatar'];if(mb_strlen($bio)>500)out(['error'=>'Bio must be 500 characters or fewer'],422);if(mb_strlen($avatar)>2048)out(['error'=>'Avatar URL is too long'],422);$q=$db->prepare('UPDATE users SET bio=?,avatar=? WHERE id=?');$q->execute([$bio,$avatar,$u['id']]);out(['user'=>me()]);}
    
    return false;
}
