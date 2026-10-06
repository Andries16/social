import { useEffect, useState } from "react";
import { Alert, Avatar as MuiAvatar, Button, Card, CardContent, Container, Stack, TextField, Typography } from "@mui/material";
import type { User } from "../types";
import { api } from "../services/api";

export function ProfilePage({
  user,
  currentUser,
  onUpdate,
}: {
  user: User;
  currentUser: User;
  onUpdate: (u: User) => void;
}) {
  const isOwner = user.id === currentUser.id;
  const [bio, setBio] = useState(user.bio || "");
  const [avatar, setAvatar] = useState(user.avatar || "");
  const [following, setFollowing] = useState(false);
  const [privateAccount, setPrivateAccount] = useState(false);
  const [canView, setCanView] = useState(true);
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    setBio(user.bio || "");
    setAvatar(user.avatar || "");
    setPrivateAccount(false);
    setCanView(true);
    setError("");

    if (!isOwner) {
      void api
        .profile(user.id)
        .then((r) => {
          setPrivateAccount(r.private_account);
          setCanView(r.can_view_posts);
          setBio(r.user.bio || "");
          setAvatar(r.user.avatar || "");
        })
        .catch((e) => setError(e instanceof Error ? e.message : "Unable to load profile"));
    }
  }, [user.id, isOwner]);

  useEffect(() => {
    if (!isOwner) {
      void api
        .followStatus(user.id)
        .then((r) => setFollowing(r.following))
        .catch((e) => setError(e instanceof Error ? e.message : "Unable to load follow status"));
    }
  }, [user.id, isOwner]);

  async function save() {
    try {
      setError("");
      setSaving(true);
      const r = await api.updateProfile(bio, avatar);
      onUpdate(r.user);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to save profile");
    } finally {
      setSaving(false);
    }
  }

  async function upload(file: File) {
    try {
      setError("");
      setUploading(true);
      const r = await api.uploadMedia(file);
      setAvatar(r.url);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to upload profile photo");
    } finally {
      setUploading(false);
    }
  }

  async function toggleFollow() {
    try {
      setError("");
      if (following) {
        await api.unfollow(user.id);
        setFollowing(false);
      } else {
        await api.follow(user.id);
        setFollowing(true);
      }
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to update follow status");
    }
  }

  return (
    <Container maxWidth="sm" sx={{ py: 4 }}>
      <Card>
        <CardContent>
          <Stack spacing={2.5} sx={{ alignItems: "center" }}>
            {error && <Alert severity="error" sx={{ width: "100%" }}>{error}</Alert>}
            <MuiAvatar src={avatar || undefined} sx={{ width: 104, height: 104, fontSize: 40 }}>
              {user.name[0]}
            </MuiAvatar>
            <Typography variant="h4" sx={{ fontWeight: 800 }}>{user.name}</Typography>
            <Typography color="text.secondary">
              {isOwner ? user.email || "Social member" : "Social member"}
            </Typography>
            {!isOwner && (
              <Button variant={following ? "outlined" : "contained"} onClick={() => void toggleFollow()}>
                {following ? "Unfollow" : "Follow"}
              </Button>
            )}
            {!isOwner && privateAccount && !canView && (
              <Alert severity="info" sx={{ width: "100%" }}>
                This account is private. Follow this person to see their posts and profile details.
              </Alert>
            )}
            {isOwner && (
              <>
                <TextField fullWidth multiline minRows={3} label="Bio" value={bio} onChange={(e) => setBio(e.target.value)} />
                <Button component="label" variant="outlined" fullWidth disabled={uploading}>
                  {uploading ? "Uploading…" : "Upload profile photo"}
                  <input hidden type="file" accept="image/jpeg,image/png,image/webp,image/gif" onChange={(e) => {
                    const file = e.target.files?.[0];
                    if (file) void upload(file);
                  }} />
                </Button>
                <Button fullWidth variant="contained" disabled={saving || uploading} onClick={() => void save()}>
                  {saving ? "Saving…" : "Save profile"}
                </Button>
              </>
            )}
            {!isOwner && canView && (
              <Typography color="text.secondary" sx={{ alignSelf: "stretch", whiteSpace: "pre-wrap" }}>
                {bio || "No bio yet."}
              </Typography>
            )}
          </Stack>
        </CardContent>
      </Card>
    </Container>
  );
}
