# JoomEngine MCP Web Services

Native Joomla webservices plugin for the authenticated JoomEngine MCP endpoint
at `/api/index.php/v1/joomengine-mcp`. Requires Joomla 6.1–6.x, PHP 8.3 or later
and the enabled [MCP component](https://github.com/joomengine/mcp_component).

Install the combined extension from
[joomengine/mcp_package](https://github.com/joomengine/mcp_package).
OctoJPack reads the component repository's `.octojpack`, selects the latest
tag of each extension and builds the Joomla package in that separate repository.
This repository contains only the webservices plugin.

The source ZIP from this repository is also directly installable in Joomla.
No Composer run or build step is required. A fresh installation enables the
plugin. Updates preserve its enabled or disabled state. Without the enabled
component, the plugin registers no routes. Component removal does not remove
this independent extension; use Joomla's extension manager to uninstall it.

Protocol requests use Joomla API authentication. The public `OPTIONS` route
serves CORS preflight only; it exposes no catalogue data or execution.

## Changes and releases

Record pending changes in [CHANGELOG.md](CHANGELOG.md) and
[changelog.xml](changelog.xml) under `[[[NEXT_VERSION]]]`. To release a reviewed
version, set the manifest version and creation date, replace that marker in both
changelogs with the same version, commit the metadata, then create and push its
`vX.Y.Z` Git tag. Do not move published tags. The next component package release
will select this tag through OctoJPack's normal latest-tag selection.

The Joomla package owns update delivery; this plugin does not maintain an
independent update feed or package builder.

## Verification

The CI workflow checks PHP syntax and installable manifest contents. Installed
Joomla routing, fresh-install enablement and preservation of disabled state are
verified by the integration workflow in `joomengine/mcp_component`, which installs
this repository as a separate plugin.

GNU General Public License version 3 or later; see [LICENSE](LICENSE).
