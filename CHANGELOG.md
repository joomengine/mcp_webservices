# Changelog

## 1.0.0

### Addition
- Add a manual version release workflow and native plugin update feed, using Git User once and OctoShoom for SHA-512 hashes.

### Change
- Move the webservices plugin from the component into its own directly installable repository, included independently by OctoJPack.

### Fix
- Register no MCP API routes when the component is absent or disabled.
- Enable fresh plugin installations while preserving an administrator's disabled state on updates.

### Note
- Release both independent plugins before the component's first package release; OctoJPack includes their latest tags.
