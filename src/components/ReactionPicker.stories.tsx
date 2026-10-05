import type { Meta, StoryObj } from "@storybook/react";
import { ReactionPicker } from "./ReactionPicker";

const meta = { title: "Components/ReactionPicker", component: ReactionPicker } satisfies Meta<typeof ReactionPicker>;
export default meta;
type Story = StoryObj<typeof meta>;

export const Default: Story = {
  args: { postId: 1, onChanged: () => {} },
};
