<?php declare(strict_types=1);

function handleStoryRoutes(string $r, string $m): bool
{
    global $db;

    if($r==='/api/stories'&&$m==='GET'){$u=me();$q=$db->prepare("SELECT s.*,u.id uid,u.name,u.avatar FROM stories s JOIN users u ON u.id=s.user_id WHERE datetime(s.created_at)>=datetime('now','-1 day') AND (COALESCE((SELECT private_account FROM user_settings s2 WHERE s2.user_id=s.user_id),0)=0 OR s.user_id=? OR EXISTS(SELECT 1 FROM follows f WHERE f.follower_id=? AND f.following_id=s.user_id)) ORDER BY s.id DESC");$q->execute([$u['id'],$u['id']]);$a=$q->fetchAll(PDO::FETCH_ASSOC);foreach($a as&$s)$s['user']=['id'=>$s['uid'],'name'=>$s['name'],'avatar'=>$s['avatar']];out(['stories'=>$a]);}
    if($r==='/api/stories'&&$m==='POST'){$u=me();rateLimit('story',(string)$u['id'],20,86400);$x=b();$text=trim((string)($x['text']??''));if($text===''||mb_strlen($text)>1000)out(['error'=>'Story text is required'],422);$q=$db->prepare('INSERT INTO stories(user_id,text,created_at)VALUES(?,?,?)');$q->execute([$u['id'],$text,date('c')]);out(['ok'=>true]);}
    
    return false;
}
