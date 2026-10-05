import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { Avatar } from "./Avatar";

describe("Avatar", () => {
  it("renders the user's initial when no avatar exists", () => {
    render(<Avatar user={{ name: "Maria" }} />);
    expect(screen.getByText("M")).toBeInTheDocument();
  });

  it("renders an image when an avatar URL exists", () => {
    render(<Avatar user={{ name: "Maria", avatar: "/maria.png" }} />);
    expect(screen.getByRole("img")).toHaveAttribute("src", "/maria.png");
  });
});
