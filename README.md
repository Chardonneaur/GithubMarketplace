# GithubMarketplace

**Install Matomo plugins directly from GitHub ZIP releases — no Marketplace account required.**

> This plugin is a community contribution and is not officially supported by Matomo.
> It is provided under the GPL v3 license without any warranty of fitness for a particular purpose.

## Description

GithubMarketplace adds a simple installation interface to your Matomo administration panel. Paste the URL of any GitHub release ZIP file, click Install, and Matomo will download and install the plugin automatically — using the same installer that the official Marketplace uses internally.

This is particularly useful for:
- Self-hosted Matomo instances that cannot reach the Marketplace
- Installing community plugins hosted on GitHub that are not yet listed on the Marketplace
- Installing private or in-house plugins from a GitHub repository

All installed plugins are tracked in a dedicated table so you can see which plugins were installed this way, at what version, and from which URL.

### Features

- **One-click install** from any GitHub release ZIP URL
- **Version tracking** — see the installed version and installation date for each plugin
- **Install or update** — installing an already-tracked plugin updates its version record
- **Accessible at** Administration → Platform → GitHub Plugins
- **Super-administrator only** — not accessible to regular users or view-only users

### Security notice

This plugin downloads a ZIP file from a URL you provide and installs it as a Matomo plugin. **Only install plugins from sources you trust.** A malicious ZIP could contain arbitrary PHP code that executes on your server. This plugin does not perform code review or sandboxing — that responsibility lies with you as the administrator.

Only HTTPS URLs should be used. The plugin accepts any URL that resolves to a valid ZIP file, so exercise the same caution you would when installing any third-party software.

## Requirements

- Matomo >= 5.0
- PHP >= 8.1
- Super-administrator access

## Installation

### From the Matomo Marketplace

1. Go to **Administration → Marketplace**.
2. Search for **GithubMarketplace**.
3. Click **Install** and then **Activate**.

### Manual Installation

1. Download the latest release archive from the [GitHub repository](https://github.com/Chardonneaur/GithubMarketplace/releases).
2. Extract it into your `matomo/plugins/` directory so that the path `matomo/plugins/GithubMarketplace/plugin.json` exists.
3. Go to **Administration → Plugins** and activate **GithubMarketplace**.

## Usage

1. Go to **Administration → Platform → GitHub Plugins**.
2. In the **Install Plugin from ZIP URL** form, paste the ZIP URL of the plugin you want to install.
   - Example: `https://github.com/owner/repo/archive/refs/tags/v1.0.0.zip`
   - You can find release ZIP URLs on any GitHub repository's **Releases** page.
3. Click **Install**.
4. Matomo will download, extract, and install the plugin. Activate it afterwards from **Administration → Plugins**.

To update a plugin, paste the new version's ZIP URL and click Install again. The version record will be updated automatically.

## FAQ

**Does this replace the official Matomo Marketplace?**
No. It complements it. You can still install plugins from the official Marketplace normally. This plugin adds a separate installation path for plugins hosted on GitHub.

**What does "Remove from tracking" do?**
It removes the plugin's entry from GithubMarketplace's tracking table. It does **not** uninstall the plugin from Matomo. To fully uninstall a plugin, use **Administration → Plugins**.

**Can non-admin users access this?**
No. The entire plugin interface requires super-administrator access. Regular users and view-only users cannot see or use it.

**Does it work with private GitHub repositories?**
No. The download is performed without authentication, so the ZIP URL must be publicly accessible.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for the full version history.

## License

GPL v3+. See [LICENSE](LICENSE) for details.
