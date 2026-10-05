import { describe,expect,it,vi,afterEach } from "vitest";
import { api } from "./services/api";

afterEach(()=>vi.restoreAllMocks());

describe("Social API client",()=>{
  it("encodes people search queries",async()=>{
    const fetchMock=vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:true,json:async()=>({users:[]})} as Response);
    await api.users("Jane Doe");
    expect(fetchMock).toHaveBeenCalledWith(expect.stringContaining("q=Jane%20Doe"),expect.any(Object));
  });
  it("stores the CSRF token returned by authentication",async()=>{
    const fetchMock=vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:true,json:async()=>({user:{id:1,name:"Jane",bio:"",avatar:""},csrf:"test-csrf"})} as Response);
    await api.login("jane@example.com","password");
    await api.logout();
    const logoutCall=fetchMock.mock.calls[1];
    expect(logoutCall?.[1]).toEqual(expect.objectContaining({headers:expect.objectContaining({"X-CSRF-Token":"test-csrf"})}));
  });
  it("clears the CSRF token after logout",async()=>{
    const fetchMock=vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:true,json:async()=>({user:{id:1,name:"Jane",bio:"",avatar:""},csrf:"test-csrf"})} as Response);
    await api.login("jane@example.com","password");
    await api.logout();
    await api.users("Jane");
    expect(fetchMock.mock.calls[2]?.[1]).toEqual(expect.objectContaining({headers:expect.not.objectContaining({"X-CSRF-Token":"test-csrf"})}));
  });
  it("requests follow status from the dedicated endpoint",async()=>{
    const fetchMock=vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:true,json:async()=>({following:true})} as Response);
    await expect(api.followStatus(42)).resolves.toEqual({following:true});
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/api/users/42/follow"),
      expect.any(Object),
    );
  });
  it("keeps the stories API response flat",async()=>{
    const fetchMock=vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:true,json:async()=>({stories:[]})} as Response);
    await expect(api.stories()).resolves.toEqual({stories:[]});
    expect(fetchMock).toHaveBeenCalledWith(expect.stringContaining("/api/stories"),expect.any(Object));
  });
  it("surfaces API errors",async()=>{
    vi.spyOn(globalThis,"fetch").mockResolvedValue({ok:false,json:async()=>({error:"Forbidden"})} as Response);
    await expect(api.users("Jane")).rejects.toThrow("Forbidden");
  });
});
