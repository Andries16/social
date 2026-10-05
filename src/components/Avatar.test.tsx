import { renderToStaticMarkup } from "react-dom/server";
import { describe, expect, it } from "vitest";
import { Avatar } from "./Avatar";

describe("Avatar", () => {
  it("renders the user's initial when no avatar exists", () => {
    const html = renderToStaticMarkup(<Avatar user={{ name: "Maria" }} />);
    expect(html).toContain(">M</");
  });

  it("renders an image when an avatar URL exists", () => {
    const html = renderToStaticMarkup(
      <Avatar user={{ name: "Maria", avatar: "/maria.png" }} />,
    );
    expect(html).toContain('src="/maria.png"');
  });
});
