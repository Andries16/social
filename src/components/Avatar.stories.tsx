import type { Meta, StoryObj } from "@storybook/react";
import { Avatar } from "./Avatar";

const meta = {
  title: "Components/Avatar",
  component: Avatar,
  args: {
    user: {
      id: 1,
      name: "Ada Lovelace",
      avatar: "",
      bio: "",
    },
  },
} satisfies Meta<typeof Avatar>;

export default meta;
type Story = StoryObj<typeof meta>;

export const Default: Story = {};

export const CustomSize: Story = {
  args: {
    size: 64,
  },
};
