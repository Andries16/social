<?php declare(strict_types=1);

function handleNotificationRoutes(string $r, string $m): bool
{
    global $db;

    if(preg_match('#^/api/notifications/(\d+)/read$#',$r,$x)&&$m==='POST'){ $u=me();$q=$db->prepare('UPDATE notifications SET read_at=? WHERE id=? AND user_id=?');$q->execute([date('c'),(int)$x[1],$u['id']]);out(['ok'=>true]);}
    if($r==='/api/notifications'&&$m==='GET'){$u=me();$q=$db->prepare('SELECT n.*,u.id uid,u.name,u.avatar,u.bio FROM notifications n JOIN users u ON u.id=n.actor_id WHERE n.user_id=? ORDER BY n.id DESC LIMIT 30');$q->execute([$u['id']]);$items=$q->fetchAll(PDO::FETCH_ASSOC);foreach($items as&$n)$n['actor']=['id'=>$n['uid'],'name'=>$n['name'],'avatar'=>$n['avatar'],'bio'=>$n['bio']];out(['notifications'=>$items]);}
    return false;
}
