import type { Meta, StoryObj } from "@storybook/react";
import { CreatePostCard } from "./CreatePostCard";

const meta = { title: "Components/CreatePostCard", component: CreatePostCard } satisfies Meta<typeof CreatePostCard>;
export default meta;
type Story = StoryObj<typeof meta>;

export const Default: Story = {
  args: { onCreated: () => {} },
};
