<?php declare(strict_types=1);

function handleSocialGraphRoutes(string $r, string $m): bool
{
    global $db;

    if(preg_match('#^/api/users/(\d+)/follow$#',$r,$x)&&in_array($m,['POST','DELETE'],true)){ $u=me();$target=(int)$x[1];if($target===$u['id'])out(['error'=>'Cannot follow yourself'],422);$q=$db->prepare('SELECT id FROM users WHERE id=?');$q->execute([$target]);if(!$q->fetchColumn())out(['error'=>'User not found'],404);if($m==='POST'){$q=$db->prepare('INSERT OR IGNORE INTO follows(follower_id,following_id,created_at)VALUES(?,?,?)');$q->execute([$u['id'],$target,date('c')]);if($q->rowCount()===0)out(['ok'=>true]);$n=$db->prepare('INSERT INTO notifications(user_id,type,actor_id,created_at)VALUES(?,?,?,?)');$n->execute([$target,'started_following_you',$u['id'],date('c')]);}else{$q=$db->prepare('DELETE FROM follows WHERE follower_id=? AND following_id=?');$q->execute([$u['id'],$target]);}out(['ok'=>true]);}
    if(preg_match('#^/api/users/(\d+)/(followers|following)$#',$r,$x)&&$m==='GET'){me();$id=(int)$x[1];$join=$x[2]==='followers'?'follower_id':'following_id';$column=$x[2]==='followers'?'following_id':'follower_id';$q=$db->prepare('SELECT u.id,u.name,u.avatar FROM follows f JOIN users u ON u.id=f.'.$join.' WHERE f.'.$column.'=? ORDER BY u.name');$q->execute([$id]);out(['users'=>$q->fetchAll(PDO::FETCH_ASSOC)]);}
    
    return false;
}
