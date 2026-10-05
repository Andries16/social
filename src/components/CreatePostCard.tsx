import { useState } from "react";
import { Button,Card,CardContent,Stack,TextField,Typography } from "@mui/material";
import { api } from "../services/api";

export function CreatePostCard({onCreated}:{onCreated:()=>void}){
  const[body,setBody]=useState(""),[images,setImages]=useState<string[]>([]),[uploading,setUploading]=useState(false);
  async function addFiles(files:FileList|null){
    if(!files)return;
    try{
      setUploading(true);
      const urls:string[]=[];
      for(const file of Array.from(files).slice(0,10-images.length)) urls.push((await api.uploadMedia(file)).url);
      setImages(current=>[...current,...urls]);
    }finally{setUploading(false)}
  }
  async function submit(){
    if(!body.trim())return;
    await api.createPost(body.trim(),images[0]||"",images);
    setBody("");setImages([]);onCreated();
  }
  return <Card><CardContent><Stack spacing={1.5}>
    <TextField multiline minRows={3} label="What's on your mind?" value={body} onChange={e=>setBody(e.target.value)}/>
    <Button component="label" variant="outlined" disabled={uploading}>Attach images
      <input hidden type="file" multiple accept="image/jpeg,image/png,image/webp,image/gif" onChange={e=>void addFiles(e.target.files)}/>
    </Button>
    {images.length>0&&<Stack direction="row" spacing={1} sx={{overflowX:"auto"}}>
      {images.map((url,index)=><Stack key={url} spacing={.5} sx={{minWidth:96}}>
        <img src={url} alt="" style={{width:96,height:96,objectFit:"cover",borderRadius:8}}/>
        <Button size="small" onClick={()=>setImages(current=>current.filter((_,i)=>i!==index))}>Remove</Button>
      </Stack>)}
    </Stack>}
    <Typography variant="caption" color="text.secondary">{images.length}/10 images</Typography>
    <Button disabled={uploading} variant="contained" onClick={()=>void submit()} sx={{alignSelf:"flex-start"}}>Publish</Button>
  </Stack></CardContent></Card>
}