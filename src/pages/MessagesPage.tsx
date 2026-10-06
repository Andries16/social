import { useEffect, useState } from "react";
import { Alert, Button, Card, CardContent, Container, FormControl, InputLabel, ListItemText, MenuItem, Select, Stack, TextField, Typography } from "@mui/material";
import type { Message, User } from "../types";
import { api } from "../services/api";

export function MessagesPage({ currentUser }: { currentUser: User }) {
  const [users, setUsers] = useState<User[]>([]);
  const [to, setTo] = useState<number | "">("");
  const [messages, setMessages] = useState<Message[]>([]);
  const [text, setText] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    void api.users().then((r) => setUsers(r.users.filter((u) => u.id !== currentUser.id))).catch((e) => setError(e instanceof Error ? e.message : "Unable to load people"));
  }, [currentUser.id]);

  useEffect(() => {
    if (to) void api.messages(to).then((r) => setMessages(r.messages)).catch((e) => setError(e instanceof Error ? e.message : "Unable to load messages"));
  }, [to]);

  async function send() {
    if (!to || !text.trim()) return;
    try {
      setError("");
      await api.sendMessage(Number(to), text.trim());
      setText("");
      setMessages((await api.messages(Number(to))).messages);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to send message");
    }
  }

  return <Container maxWidth="md" sx={{ py: 3 }}>
    <Card><CardContent><Stack spacing={2}>
      <Typography variant="h5" sx={{ fontWeight: 800 }}>Messages</Typography>
      {error && <Alert severity="error">{error}</Alert>}
      <FormControl fullWidth>
        <InputLabel>Person</InputLabel>
        <Select label="Person" value={to} onChange={(e) => setTo(Number(e.target.value))}>
          {users.map((u) => <MenuItem key={u.id} value={u.id}>{u.name}<ListItemText secondary={u.bio} sx={{ ml: 1 }} /></MenuItem>)}
        </Select>
      </FormControl>
      <Stack sx={{ minHeight: 300, maxHeight: "55vh", overflowY: "auto", p: 1, bgcolor: "grey.50", borderRadius: 2 }}>
        {messages.map((m) => <Stack key={m.id} sx={{ alignItems: m.from_user_id === currentUser.id ? "flex-end" : "flex-start" }}>
          <Typography sx={{ p: 1.2, px: 2, borderRadius: 3, bgcolor: m.from_user_id === currentUser.id ? "primary.main" : "white", color: m.from_user_id === currentUser.id ? "white" : "text.primary", maxWidth: "75%" }}>{m.text}</Typography>
        </Stack>)}
      </Stack>
      <Stack direction="row" spacing={1}>
        <TextField fullWidth placeholder="Message" value={text} onChange={(e) => setText(e.target.value)} onKeyDown={(e) => { if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); void send(); } }} />
        <Button variant="contained" onClick={() => void send()}>Send</Button>
      </Stack>
    </Stack></CardContent></Card>
  </Container>;
}
