import { useEffect, useState } from "react";
import { Alert, Autocomplete, Avatar, Box, TextField } from "@mui/material";
import type { User } from "../types";
import { api } from "../services/api";

export function SearchBar({ onSelect }: { onSelect: (user: User) => void }) {
  const [users, setUsers] = useState<User[]>([]);
  const [query, setQuery] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    const timer = setTimeout(() => {
      if (!query.trim()) {
        setUsers([]);
        setError("");
        return;
      }
      void api.users(query).then((r) => {
        setUsers(r.users);
        setError("");
      }).catch((e) => {
        setUsers([]);
        setError(e instanceof Error ? e.message : "Unable to search people");
      });
    }, 250);
    return () => clearTimeout(timer);
  }, [query]);

  const options = users.slice(0, 8);

  return (
    <Box sx={{ position: "relative", width: { xs: 160, sm: 280 } }}>
      <Autocomplete
        options={options}
        inputValue={query}
        onInputChange={(_, value) => setQuery(value)}
        getOptionLabel={(u) => u.name}
        sx={{ width: "100%" }}
        renderInput={(params) => <TextField {...params} size="small" label="Search people" />}
        onChange={(_, user) => {
          if (user) {
            onSelect(user);
            setQuery("");
          }
        }}
        renderOption={(props, user) => (
          <Box component="li" {...props}>
            <Avatar src={user.avatar || undefined} sx={{ mr: 1, width: 30, height: 30 }} />
            {user.name}
          </Box>
        )}
      />
      {error && <Alert severity="error" sx={{ mt: 0.5, position: "absolute", zIndex: 2, width: "100%" }}>{error}</Alert>}
    </Box>
  );
}
