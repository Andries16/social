export interface User { id:number; name:string; email?:string|null; bio:string; avatar:string; }
export interface Comment { id:number; text:string; created_at:string; user:User; }
export interface Post { id:number; body:string; image:string; created_at:string; likes:number; user:User; comments:Comment[]; }
export interface Story { id:number; text:string; created_at:string; user:User; }
export interface Message { id:number; from_user_id:number; to_user_id:number; text:string; created_at:string; }
export interface Notification { id:number; type:string; actor:User; post_id?:number; created_at:string; read_at?:string|null; }
