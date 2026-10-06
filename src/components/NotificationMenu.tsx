import { useEffect, useState } from "react";
import {
  Badge,
  IconButton,
  Menu,
  MenuItem,
  Typography,
} from "@mui/material";
import NotificationsRoundedIcon from "@mui/icons-material/NotificationsRounded";
import type { Notification } from "../types";
import { api } from "../services/api";

export function NotificationMenu() {
  const [items, setItems] = useState<Notification[]>([]);
  const [anchor, setAnchor] = useState<null | HTMLElement>(null);
  const [error, setError] = useState("");

  useEffect(() => {
    void api
      .notifications()
      .then((result) => setItems(result.notifications))
      .catch((e) => setError(e instanceof Error ? e.message : "Unable to load notifications"));
  }, []);

  const markRead = async (notification: Notification) => {
    if (!notification.read_at) {
      try {
        await api.markNotificationRead(notification.id);
      setItems((current) =>
        current.map((item) =>
          item.id === notification.id
            ? { ...item, read_at: new Date().toISOString() }
            : item,
        ),
      );
      } catch (e) {
        setError(e instanceof Error ? e.message : "Unable to mark notification as read");
      }
    }
  };

  return (
    <>
      <IconButton
        aria-label="Notifications"
        onClick={(event) => setAnchor(event.currentTarget)}
      >
        <Badge
          badgeContent={items.filter((item) => !item.read_at).length}
          color="error"
        >
          <NotificationsRoundedIcon />
        </Badge>
      </IconButton>
      <Menu
        anchorEl={anchor}
        open={Boolean(anchor)}
        onClose={() => setAnchor(null)}
      >
        {error ? <MenuItem>{error}</MenuItem> : items.length ? (
          items.map((notification) => (
            <MenuItem
              key={notification.id}
              onClick={() => void markRead(notification)}
            >
              <Typography variant="body2">
                {notification.actor.name}{" "}
                {notification.type.replaceAll("_", " ")}
              </Typography>
            </MenuItem>
          ))
        ) : (
          <MenuItem>No notifications</MenuItem>
        )}
      </Menu>
    </>
  );
}
