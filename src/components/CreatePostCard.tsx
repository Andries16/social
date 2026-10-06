import { useState } from "react";
import { Alert, Button, Card, CardContent, Stack, TextField, Typography } from "@mui/material";
import { api } from "../services/api";

export function CreatePostCard({onCreated}:{onCreated:()=>void}){
  const[body,setBody]=useState(""),[images,setImages]=useState<string[]>([]),[uploading,setUploading]=useState(false),[submitting,setSubmitting]=useState(false),[error,setError]=useState("");
  async function addFiles(files:FileList|null){
    if(!files)return;
    try{
      setError("");
      setUploading(true);
      const urls:string[]=[];
      for(const file of Array.from(files).slice(0,10-images.length)) urls.push((await api.uploadMedia(file)).url);
      setImages(current=>[...current,...urls]);
    }catch(e){setError(e instanceof Error?e.message:"Upload failed")}finally{setUploading(false)}
  }
  async function submit(){
    if(!body.trim()||submitting)return;
    try{
      setError("");setSubmitting(true);
      await api.createPost(body.trim(),images[0]||"",images);
      setBody("");setImages([]);onCreated();
    }catch(e){setError(e instanceof Error?e.message:"Unable to publish post")}finally{setSubmitting(false)}
  }
  return <Card><CardContent><Stack spacing={1.5}>
    {error&&<Alert severity="error">{error}</Alert>}
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
    <Button disabled={uploading||submitting} variant="contained" onClick={()=>void submit()} sx={{alignSelf:"flex-start"}}>Publish</Button>
  </Stack></CardContent></Card>
}