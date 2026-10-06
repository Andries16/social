import { useEffect, useRef, useState } from "react";
import { Alert, Button, Container, Stack } from "@mui/material";
import type { Post, Story, User } from "../types";
import { api } from "../services/api";
import { PostCard } from "../components/PostCard";
import { StoryStrip } from "../components/StoryStrip";
import { StoryComposer } from "../components/StoryComposer";
import { StoryViewer } from "../components/StoryViewer";
import { CreatePostCard } from "../components/CreatePostCard";

export function FeedPage({ user }: { user: User }) {
  const [posts, setPosts] = useState<Post[]>([]);
  const [stories, setStories] = useState<Story[]>([]);
  const [selectedStory, setSelectedStory] = useState<Story | null>(null);
  const [error, setError] = useState("");
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [loadingMore, setLoadingMore] = useState(false);
  const sentinelRef = useRef<HTMLDivElement | null>(null);

  async function load() {
    try {
      setError("");
      const [postResult, storyResult] = await Promise.all([
        api.posts(1),
        api.stories(),
      ]);
      setPosts(postResult.posts);
      setPage(1);
      setHasMore(postResult.hasMore);
      setStories(storyResult.stories);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to load feed");
    }
  }

  async function loadMore() {
    if (loadingMore || !hasMore) return;

    setLoadingMore(true);
    try {
      const result = await api.posts(page + 1);
      setPosts((current) => [...current, ...result.posts]);
      setPage(result.page);
      setHasMore(result.hasMore);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Unable to load more posts");
    } finally {
      setLoadingMore(false);
    }
  }

  useEffect(() => {
    void load();
  }, []);

  useEffect(() => {
    const sentinel = sentinelRef.current;
    if (!sentinel || !hasMore) return;

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries.some((entry) => entry.isIntersecting)) void loadMore();
      },
      { rootMargin: "600px 0px" },
    );

    observer.observe(sentinel);
    return () => observer.disconnect();
  }, [hasMore, page, loadingMore]);

  return (
    <Container maxWidth="md" sx={{ py: 3 }}>
      <Stack spacing={2}>
        <StoryStrip
          stories={stories}
          onSelect={(story) => {
            setSelectedStory(story);
            void api.storyView(story.id);
          }}
        />
        <StoryComposer user={user} onCreated={(story) => setStories((current) => [story, ...current.filter((item) => item.id !== story.id)])} />
        <CreatePostCard onCreated={() => void load()} />
        {error && <Alert severity="error">{error}</Alert>}
        {posts.map((post) => (
          <PostCard
            key={post.id}
            post={post}
            currentUser={user}
            onChanged={() => void load()}
          />
        ))}
        <div ref={sentinelRef} aria-hidden="true" style={{ minHeight: 1 }} />
        {hasMore && (
          <Button
            variant="outlined"
            onClick={() => void loadMore()}
            disabled={loadingMore}
          >
            {loadingMore ? "Loading..." : "Load more"}
          </Button>
        )}
      </Stack>
      <StoryViewer
        story={selectedStory}
        onClose={() => setSelectedStory(null)}
      />
    </Container>
  );
}
