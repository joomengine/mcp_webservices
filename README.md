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
[changelog.xml](changelog.xml) under exactly one `[[[NEXT_VERSION]]]` section in
each file. After a release, add a new pending section above the existing history
when recording the next change.

Configure these repository Actions secrets for `octoleo/git-user@v2`:

| Secret | Value |
| --- | --- |
| `GPG_KEY` | Private signing key |
| `GPG_USER` | Signing key identity |
| `SSH_KEY` | Private SSH key with push access to this repository |
| `SSH_PUB` | Matching public SSH key |
| `GIT_USER` | Git author name |
| `GIT_EMAIL` | Git author email |

The signing identity must be able to push release commits to `main` and create
tags under the repository's branch and tag rules.

After merging reviewed changes, open **Actions → Release webservices plugin with
OctoShoom → Run workflow**, select `main`, and enter the desired stable `X.Y.Z`
version (a leading `v` is accepted). The first release may use the manifest's
current version; subsequent versions must increase. The workflow updates the
manifest and both changelogs, commits them, creates `vX.Y.Z`, adds the tagged ZIP
to [the plugin update feed](joomengine_mcp_update_server.xml), then runs OctoShoom
to add its SHA-512. No manual tag push is needed. A tag push alone does not start
the workflow. If publication or hashing fails, rerun the same version; published
tags and existing update hashes are preserved.

For the first combined package, complete this release and the console plugin's
release before running the component's package release workflow. OctoJPack then
selects the latest tag from each independent plugin repository. This repository
also serves its own Joomla plugin updates; it does not build the combined package.

## Verification

The CI workflow checks PHP syntax, installable manifest contents, and release
metadata for first releases, later releases and retries. Run the metadata checks
locally with `php tests/release.php`. Installed
Joomla routing, fresh-install enablement and preservation of disabled state are
verified by the integration workflow in `joomengine/mcp_component`, which installs
this repository as a separate plugin.

GNU General Public License version 3 or later; see [LICENSE](LICENSE).
