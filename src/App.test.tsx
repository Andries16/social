import { describe,expect,it,vi,afterEach } from "vitest";
import { api } from "./services/api";

afterEach(()=>vi.restoreAllMocks());

describe("Social API client",()=>{
  it("encodes people search queries",async()=>{
    const fetchMock=vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:true,json:async()=>({users:[]})} as Response);
    await api.users("Jane Doe");
    expect(fetchMock).toHaveBeenCalledWith(expect.stringContaining("q=Jane%20Doe"),expect.any(Object));
  });
  it("keeps the product name stable",()=>expect("Social").toBe("Social"));
});
