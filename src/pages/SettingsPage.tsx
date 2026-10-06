import { useEffect, useState } from "react";
import { Alert, Button, Card, CardContent, Container, FormControlLabel, Stack, Switch, TextField, Typography } from "@mui/material";
import type { User } from "../types";
import { api } from "../services/api";

export function SettingsPage({ user, onUpdate }: { user: User; onUpdate: (u: User) => void }) {
  const [bio, setBio] = useState(user.bio || "");
  const [avatar, setAvatar] = useState(user.avatar || "");
  const [privateAccount, setPrivateAccount] = useState(false);
  const [emailNotifications, setEmailNotifications] = useState(true);
  const [saving, setSaving] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    void api.settings().then((r) => {
      setPrivateAccount(r.settings.private_account);
      setEmailNotifications(r.settings.email_notifications);
    }).catch((e) => {
      setError(e instanceof Error ? e.message : "Unable to load settings");
    }).finally(() => setLoading(false));
  }, []);

  async function save() {
    try {
      setError("");
      setSaving(true);
      const r = await api.updateProfile(bio, avatar);
      await api.updateSettings({ private_account: privateAccount, email_notifications: emailNotifications });
      onUpdate(r.user);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to save settings");
    } finally {
      setSaving(false);
    }
  }

  return (
    <Container maxWidth="sm" sx={{ py: 4 }}>
      <Card>
        <CardContent>
          <Stack spacing={2.5}>
            {error && <Alert severity="error">{error}</Alert>}
            <Typography variant="h4" sx={{ fontWeight: 800 }}>Account settings</Typography>
            {loading ? (
              <Typography color="text.secondary">Loading settings…</Typography>
            ) : (
              <>
                <TextField label="Bio" multiline minRows={3} value={bio} onChange={(e) => setBio(e.target.value)} />
                <TextField label="Profile photo URL" value={avatar} onChange={(e) => setAvatar(e.target.value)} />
                <FormControlLabel control={<Switch checked={privateAccount} onChange={(e) => setPrivateAccount(e.target.checked)} />} label="Private account" />
                <FormControlLabel control={<Switch checked={emailNotifications} onChange={(e) => setEmailNotifications(e.target.checked)} />} label="Email notifications" />
                <Button variant="contained" disabled={saving} onClick={() => void save()}>{saving ? "Saving…" : "Save changes"}</Button>
              </>
            )}
          </Stack>
        </CardContent>
      </Card>
    </Container>
  );
}
