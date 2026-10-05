<?php declare(strict_types=1);

function handleMessageRoutes(string $r, string $m): bool
{
    global $db;

    if($r==='/api/messages'&&$m==='GET'){$u=me();$id=(int)($_GET['user_id']??0);if($id<=0||$id===$u['id'])out(['error'=>'A conversation participant is required'],422);$q=$db->prepare('SELECT id FROM users WHERE id=?');$q->execute([$id]);if(!$q->fetchColumn())out(['error'=>'User not found'],404);$q=$db->prepare('SELECT * FROM messages WHERE (from_user_id=? AND to_user_id=?) OR(from_user_id=? AND to_user_id=?) ORDER BY id');$q->execute([$u['id'],$id,$id,$u['id']]);out(['messages'=>$q->fetchAll(PDO::FETCH_ASSOC)]);}
    if($r==='/api/messages'&&$m==='POST'){$u=me();rateLimit('message',(string)$u['id'],120,3600);$x=b();$to=(int)($x['to_user_id']??0);$text=trim($x['text']??'');if(!$to||$to===$u['id']||$text===''||mb_strlen($text)>4000)out(['error'=>'A valid message recipient and text are required'],422);$recipient=$db->prepare('SELECT id FROM users WHERE id=?');$recipient->execute([$to]);if(!$recipient->fetchColumn())out(['error'=>'User not found'],404);$q=$db->prepare('INSERT INTO messages(from_user_id,to_user_id,text,created_at)VALUES(?,?,?,?)');$q->execute([$u['id'],$to,$text,date('c')]);$n=$db->prepare('INSERT INTO notifications(user_id,type,actor_id,created_at)VALUES(?,?,?,?)');$n->execute([$to,'sent_you_a_message',$u['id'],date('c')]);out(['ok'=>true]);}
    
    return false;
}
