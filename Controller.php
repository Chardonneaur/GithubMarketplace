<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\GithubMarketplace;

use Exception;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Filesystem;
use Piwik\Http;
use Piwik\Nonce;
use Piwik\Notification;
use Piwik\Piwik;
use Piwik\Plugins\CorePluginsAdmin\PluginInstaller;
use Piwik\View;

class Controller extends \Piwik\Plugin\ControllerAdmin
{
    private const NONCE_NAME = 'GithubMarketplace.nonce';

    public function index()
    {
        Piwik::checkUserHasSuperUserAccess();

        $view = new View('@GithubMarketplace/index');
        $this->setGeneralVariablesView($view);

        $model = new Model();
        $view->plugins = $model->getAllPlugins();
        $view->nonce = Nonce::getNonce(self::NONCE_NAME);

        return $view->render();
    }

    public function install()
    {
        Piwik::checkUserHasSuperUserAccess();

        $nonce = Common::getRequestVar('nonce', '', 'string');
        Nonce::checkNonce(self::NONCE_NAME, $nonce);

        $zipUrl = trim(Common::getRequestVar('zipUrl', '', 'string'));

        if (empty($zipUrl)) {
            $this->addNotification('Please enter a zip URL.', Notification::CONTEXT_ERROR);
            $this->redirectToIndex('GithubMarketplace', 'index');
            return;
        }

        $urlError = $this->validateZipUrl($zipUrl);
        if ($urlError !== null) {
            $this->addNotification($urlError, Notification::CONTEXT_ERROR);
            $this->redirectToIndex('GithubMarketplace', 'index');
            return;
        }

        try {
            // Download the zip
            $tmpPath = StaticContainer::get('path.tmp') . '/latest/plugins/';
            if (!is_dir($tmpPath)) {
                mkdir($tmpPath, 0755, true);
            }
            $tmpFile = $tmpPath . 'github_' . md5($zipUrl) . '_' . time() . '.zip';

            $success = Http::fetchRemoteFile($zipUrl, $tmpFile, 0, 60);

            if (!$success || !file_exists($tmpFile)) {
                throw new Exception('Failed to download the zip file.');
            }

            // Install using Matomo's installer
            $installer = new PluginInstaller();
            $metadata = $installer->installOrUpdatePluginFromFile($tmpFile);

            // Clean up
            Filesystem::deleteFileIfExists($tmpFile);

            // Track in database
            $model = new Model();
            $existing = $model->getPluginByName($metadata->name);

            if ($existing) {
                $model->updatePlugin($existing['id'], $zipUrl, $metadata->version);
            } else {
                $model->addPlugin($zipUrl, $metadata->name, $metadata->version);
            }

            $this->addNotification(
                "Plugin '{$metadata->name}' (v{$metadata->version}) installed successfully.",
                Notification::CONTEXT_SUCCESS
            );

        } catch (Exception $e) {
            if (isset($tmpFile) && file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
            $this->addNotification($e->getMessage(), Notification::CONTEXT_ERROR);
        }

        $this->redirectToIndex('GithubMarketplace', 'index');
    }

    public function remove()
    {
        Piwik::checkUserHasSuperUserAccess();

        $nonce = Common::getRequestVar('nonce', '', 'string');
        Nonce::checkNonce(self::NONCE_NAME, $nonce);

        $pluginId = Common::getRequestVar('pluginId', 0, 'int');

        $model = new Model();
        $plugin = $model->getPlugin($pluginId);

        if ($plugin) {
            $model->deletePlugin($pluginId);
            $this->addNotification("Plugin '{$plugin['plugin_name']}' removed from tracking.", Notification::CONTEXT_SUCCESS);
        }

        $this->redirectToIndex('GithubMarketplace', 'index');
    }

    /**
     * Validates that the ZIP URL is a safe, allowed GitHub HTTPS URL.
     * Returns an error string on failure, null on success.
     *
     * Allowed hosts: github.com, codeload.github.com
     * Scheme:        https only
     * Path:          must end with .zip
     */
    private function validateZipUrl(string $url): ?string
    {
        if (!str_starts_with($url, 'https://')) {
            return 'Only HTTPS URLs are accepted.';
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowedHosts = ['github.com', 'codeload.github.com'];

        if (!in_array($host, $allowedHosts, true)) {
            return 'Only GitHub URLs (github.com) are accepted.';
        }

        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        if (!str_ends_with($path, '.zip')) {
            return 'The URL must point to a .zip file.';
        }

        return null;
    }

    private function addNotification($message, $context)
    {
        $notification = new Notification($message);
        $notification->context = $context;
        Notification\Manager::notify('GithubMarketplace', $notification);
    }
}
