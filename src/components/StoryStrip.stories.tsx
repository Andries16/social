import type { Meta, StoryObj } from "@storybook/react";
import { StoryStrip } from "./StoryStrip";

const meta = { title: "Components/StoryStrip", component: StoryStrip } satisfies Meta<typeof StoryStrip>;
export default meta;
type Story = StoryObj<typeof meta>;

export const Default: Story = {
  args: {
    stories: [
      { id: 1, text: "Welcome to the network", created_at: new Date().toISOString(), user: { id: 1, name: "Ada", bio: "", avatar: "" } },
      { id: 2, text: "Working on something new", created_at: new Date().toISOString(), user: { id: 2, name: "Grace", bio: "", avatar: "" } },
    ],
    onSelect: () => {},
  },
};
