import { useState } from "react";
import { Alert, Button, Stack, TextField } from "@mui/material";
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
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState("");

  async function submit() {
    const value = text.trim();
    if (!value || submitting) return;

    try {
      setError("");
      setSubmitting(true);
      const result = await api.createStory(value);
      setText("");
      onCreated({
      id: result.id,
      text: value,
      created_at: new Date().toISOString(),
        user,
      });
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to create story");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <Stack spacing={1} direction={{ xs: "column", sm: "row" }}>
      {error && <Alert severity="error">{error}</Alert>}
      <TextField
        fullWidth
        size="small"
        label="Create a story"
        value={text}
        onChange={(event) => setText(event.target.value)}
      />
      <Button disabled={submitting} variant="outlined" onClick={() => void submit()}>
        Story
      </Button>
    </Stack>
  );
}
