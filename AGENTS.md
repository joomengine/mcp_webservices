# Agent contract

- This repository owns only `plg_webservices_joomengine_mcp`, namespace
  `VDM\Plugin\Webservices\JoomEngineMcp`. Keep it a thin native Joomla route
  adapter. MCP runtime, catalogue and authorization code belong to
  `joomengine/mcp_component`; console commands belong to `joomengine/mcp_plugin`.
- Source ZIPs must install directly into Joomla. Keep every manifest file and
  dependency tracked. Do not add a Composer installation or build requirement.
- OctoJPack alone assembles these extensions into `joomengine/mcp_package`, using
  the component's concrete `.octojpack`. Do not add assembly scripts, package
  manifests, duplicate hash logic or shared-engine modifications here.
- Preserve authenticated protocol routes and the CORS-only public OPTIONS route.
  Register no routes when the component is absent or disabled. Enable only a
  fresh plugin installation; updates must respect an administrator's disabled
  state. The component installer must not manage this plugin's lifecycle.
- Use Joomla 6 native contracts, tabs, LF, Allman braces, typed injected
  dependencies and meaningful docblocks. Keep external imports before VDM imports.
- Update both `CHANGELOG.md` and `changelog.xml` for meaningful changes, using
  the exact pending marker `[[[NEXT_VERSION]]]`. Use Joomla categories `security`,
  `fix`, `language`, `addition`, `change`, `remove` and `note`, with XML `item`
  children and matching Markdown headings. XML identity is
  `plg_webservices_joomengine_mcp` / `plugin`.
- Before a reviewed release, set the manifest version and creation date and
  freeze both pending changelogs to that version. A `vX.Y.Z` tag makes that source
  available to OctoJPack. Never move a published tag or run a release without the
  maintainer's instruction. Package update delivery belongs to the combined
  package; keep the plugin changelog URL pointed at this repository's raw XML.
- Work on a branch and open a pull request. Check syntax and manifest contents.
  Coordinate installed Joomla tests through the component's integration workflow;
  do not copy its integration harness into this repository.
