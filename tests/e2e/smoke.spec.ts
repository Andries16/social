import { expect, test } from "@playwright/test";

test("authentication screen is visible", async ({ page }) => {
  await page.goto("/");
  await expect(page.getByText("Social", { exact: true })).toBeVisible();
  await expect(page.getByRole("button", { name: "Log in" })).toBeVisible();
});

test("user can register and reach the feed", async ({ page }) => {
  await page.goto("/");
  await page.getByRole("button", { name: "Create an account" }).click();
  await page.getByLabel("Name").fill("E2E User");
  await page.getByLabel("Email").fill("e2e-" + Date.now() + "@example.test");
  await page.getByLabel("Password").fill("password");
  await page.getByRole("button", { name: "Create account", exact: true }).click();
  await expect(page.getByLabel("What's on your mind?")).toBeVisible({ timeout: 10000 });
});

test("authenticated user can publish a story, post and comment", async ({ page }) => {
  await page.goto("/");
  await page.getByRole("button", { name: "Create an account" }).click();
  await page.getByLabel("Name").fill("Social E2E User");
  await page.getByLabel("Email").fill("social-e2e-" + Date.now() + "@example.test");
  await page.getByLabel("Password").fill("password");
  await page.getByRole("button", { name: "Create account", exact: true }).click();

  await expect(page.getByLabel("Create a story")).toBeVisible({ timeout: 10000 });
  await page.getByLabel("Create a story").fill("E2E story");
  const storyResponse = page.waitForResponse(
    (response) =>
      response.url().includes("/api/stories") &&
      response.request().method() === "POST",
  );
  await page.getByRole("button", { name: "Story", exact: true }).click();
  await expect((await storyResponse).status()).toBe(200);
  await expect(page.getByText("Social E2E User", { exact: true })).toBeVisible({
    timeout: 10000,
  });

  await page.getByLabel("What's on your mind?").fill("E2E post");
  await page.getByRole("button", { name: "Publish", exact: true }).click();

  const post = page.locator(".MuiCard-root").filter({ hasText: "E2E post" }).first();
  await expect(post).toContainText("E2E post", { timeout: 10000 });
  await post.getByRole("button", { name: "React" }).click();
  await page.getByRole("menuitem", { name: "Love" }).click();
  await expect(post).toContainText("love 1", { timeout: 10000 });
  const comment = post.getByPlaceholder("Write a comment...");
  await comment.fill("E2E comment");
  await post.getByRole("button", { name: "Comment" }).click();
  await expect(post.getByText("E2E comment", { exact: true })).toBeVisible({ timeout: 10000 });
});


test("authenticated user can update account settings", async ({ page }) => {
  await page.goto("/");
  await page.getByRole("button", { name: "Create an account" }).click();
  await page.getByLabel("Name").fill("Settings E2E User");
  await page.getByLabel("Email").fill("settings-e2e-" + Date.now() + "@example.test");
  await page.getByLabel("Password").fill("password");
  await page.getByRole("button", { name: "Create account", exact: true }).click();

  await expect(page.getByLabel("What's on your mind?")).toBeVisible({ timeout: 10000 });
  await page.getByRole("button", { name: "Settings" }).click();
  await expect(page.getByText("Account settings", { exact: true })).toBeVisible({ timeout: 10000 });

  const privateSwitch = page.getByLabel("Private account");
  await privateSwitch.check();
  await page.getByRole("button", { name: "Save changes", exact: true }).click();
  await expect(page.getByRole("button", { name: "Save changes", exact: true })).toBeEnabled({ timeout: 10000 });

  await page.reload();
  await page.getByRole("button", { name: "Settings" }).click();
  await expect(page.getByLabel("Private account")).toBeChecked({ timeout: 10000 });
});
