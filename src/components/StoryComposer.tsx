import { useState } from "react";
import { Button, Stack, TextField } from "@mui/material";
import type { Story, User } from "../types";
import { api } from "../services/api";

export function StoryComposer({
  user,
  onCreated,
}: {
  user: User;
  onCreated: (story: Story) => void;
}) {
  const [text, setText] = useState("");

  async function submit() {
    const value = text.trim();
    if (!value) return;

    const result = await api.createStory(value);
    setText("");
    onCreated({
      id: result.id,
      text: value,
      created_at: new Date().toISOString(),
      user,
    });
  }

  return (
    <Stack direction={{ xs: "column", sm: "row" }} spacing={1}>
      <TextField
        fullWidth
        size="small"
        label="Create a story"
        value={text}
        onChange={(event) => setText(event.target.value)}
      />
      <Button variant="outlined" onClick={() => void submit()}>
        Story
      </Button>
    </Stack>
  );
}
