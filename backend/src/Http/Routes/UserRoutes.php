<?php declare(strict_types=1);

function handleUserRoutes(string $r, string $m): bool
{
    global $db;
    require_once __DIR__ . '/../../Domain/Users/UserRepository.php';
    require_once __DIR__ . '/../../Domain/Users/UserService.php';
    $userService = new \Social\Domain\Users\UserService(new \Social\Domain\Users\UserRepository($db));

    if(preg_match('#^/api/users/(\d+)$#',$r,$x)&&$m==='GET'){ $u=me();$id=(int)$x[1];$q=$db->prepare('SELECT id,name,email,bio,avatar FROM users WHERE id=?');$q->execute([$id]);$target=$q->fetch(PDO::FETCH_ASSOC);if(!$target)out(['error'=>'User not found'],404);$settings=$db->prepare('SELECT private_account FROM user_settings WHERE user_id=?');$settings->execute([$id]);$private=(bool)$settings->fetchColumn();$following=$id===$u['id'];if(!$following){$follow=$db->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');$follow->execute([$u['id'],$id]);$following=(bool)$follow->fetchColumn();}$canView=!$private||$following;if($id!==$u['id'])$target['email']=null;if(!$canView)$target['bio']='';out(['user'=>$target,'private_account'=>$private,'can_view_posts'=>$canView]);}if($r==='/api/users'&&$m==='GET'){ $u=me();$term=trim($_GET['q']??'');$limit=min(50,max(1,(int)($_GET['limit']??20)));$q=$db->prepare('SELECT id,name,bio,avatar FROM users WHERE name LIKE ? ORDER BY name LIMIT '.$limit);$q->execute(['%'.$term.'%']);$users=$q->fetchAll(PDO::FETCH_ASSOC);foreach($users as&$candidate){$settings=$db->prepare('SELECT private_account FROM user_settings WHERE user_id=?');$settings->execute([$candidate['id']]);$candidate['private_account']=(bool)$settings->fetchColumn();if($candidate['private_account']&&$candidate['id']!==$u['id']){$follow=$db->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');$follow->execute([$u['id'],$candidate['id']]);if(!$follow->fetchColumn())$candidate['bio']='';}}out(['users'=>$users]);}
    
    return false;
}if(preg_match('#^/api/users/(\d+)$#',$r,$x)&&$m==='GET'){ $u=me(); $id=(int)$x[1]; try { out($userService->profile((int)$u['id'],$id)); } catch (\RuntimeException $e) { out(['error'=>$e->getMessage()],404); } }
if($r==='/api/users'&&$m==='GET'){ $u=me();$term=trim($_GET['q']??'');$limit=min(50,max(1,(int)($_GET['limit']??20)));$q=$db->prepare('SELECT id,name,bio,avatar FROM users WHERE name LIKE ? ORDER BY name LIMIT '.$limit);$q->execute(['%'.$term.'%']);$users=$q->fetchAll(PDO::FETCH_ASSOC);foreach($users as&$candidate){$settings=$db->prepare('SELECT private_account FROM user_settings WHERE user_id=?');$settings->execute([$candidate['id']]);$candidate['private_account']=(bool)$settings->fetchColumn();if($candidate['private_account']&&$candidate['id']!==$u['id']){$follow=$db->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');$follow->execute([$u['id'],$candidate['id']]);if(!$follow->fetchColumn())$candidate['bio']='';}}out(['users'=>$users]);}
    
    return false;
}
