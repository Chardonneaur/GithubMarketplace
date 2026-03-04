# Changelog

## 1.0.0 — 2026-03-04

Initial release.

- Install Matomo plugins from any GitHub release ZIP URL via the admin panel
- Version and installation date tracking per plugin in a dedicated database table
- Install-or-update logic: re-installing an existing plugin updates its version record
- Remove-from-tracking action (does not uninstall the plugin files)
- Super-administrator access enforced on all actions
- CSRF protection via Matomo nonce on all mutating actions
