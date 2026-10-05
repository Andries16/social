import { Avatar as MuiAvatar } from "@mui/material"; import type { User } from "../types";
export function Avatar({user,size=44}:{user?:Partial<User>;size?:number}){return <MuiAvatar src={user?.avatar||undefined} sx={{width:size,height:size,fontWeight:700,bgcolor:"primary.light",color:"primary.main"}}>{(user?.name||"?")[0].toUpperCase()}</MuiAvatar>}
