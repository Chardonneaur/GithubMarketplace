<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\GithubMarketplace;

use Piwik\Common;
use Piwik\Piwik;

class GithubMarketplace extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'Request.dispatch' => 'checkControllerPermission',
            'Db.getTablesInstalled' => 'getTablesInstalled',
        ];
    }

    public function getTablesInstalled(&$allTablesInstalled)
    {
        $allTablesInstalled[] = Common::prefixTable('github_marketplace_plugins');
    }

    public function checkControllerPermission($module, $action)
    {
        if ($module === 'GithubMarketplace') {
            Piwik::checkUserHasSuperUserAccess();
        }
    }

    public function install()
    {
        Model::install();
    }

    public function uninstall()
    {
        Model::uninstall();
    }
}
