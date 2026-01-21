# GitHub Marketplace for Matomo

A Matomo plugin that allows you to install and manage plugins directly from GitHub repositories, bypassing the official Matomo marketplace.

## Features

- Install Matomo plugins directly from GitHub ZIP URLs
- Track installed GitHub plugins with version information
- Easy management interface in the Matomo admin panel

## Requirements

- Matomo >= 5.0.0

## Installation

1. Download this plugin as a ZIP file
2. Extract to your Matomo `plugins/` directory
3. Activate the plugin in Matomo's Administration > Plugins

Or use the Matomo console:

```bash
./console plugin:activate GithubMarketplace
```

## Usage

1. Navigate to **Administration > Platform > GitHub Plugins**
2. Paste a GitHub ZIP URL (e.g., `https://github.com/owner/repo/archive/refs/tags/v1.0.0.zip`)
3. Click **Install**

The plugin will download, extract, and install the Matomo plugin from the ZIP file.

## License

GPL v3 or later
