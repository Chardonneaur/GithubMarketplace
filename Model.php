<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\GithubMarketplace;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\DbHelper;

class Model
{
    private const TABLE_NAME = 'github_marketplace_plugins';

    public static function install()
    {
        $table = "`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `zip_url` VARCHAR(500) NOT NULL,
                  `plugin_name` VARCHAR(100) NOT NULL,
                  `version` VARCHAR(50) NOT NULL,
                  `installed_at` DATETIME NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `unique_plugin` (`plugin_name`)";

        DbHelper::createTable(self::TABLE_NAME, $table);
    }

    public static function uninstall()
    {
        Db::dropTables([Common::prefixTable(self::TABLE_NAME)]);
    }

    private function getTableName()
    {
        return Common::prefixTable(self::TABLE_NAME);
    }

    public function getAllPlugins()
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " ORDER BY plugin_name ASC";
        return Db::fetchAll($sql);
    }

    public function getPlugin($id)
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE id = ? LIMIT 1";
        return Db::fetchRow($sql, [$id]);
    }

    public function addPlugin($zipUrl, $pluginName, $version)
    {
        $db = Db::get();
        $now = Date::now()->getDatetime();

        $db->insert($this->getTableName(), [
            'zip_url' => $zipUrl,
            'plugin_name' => $pluginName,
            'version' => $version,
            'installed_at' => $now,
        ]);

        return $db->lastInsertId();
    }

    public function updatePlugin($id, $zipUrl, $version)
    {
        $db = Db::get();
        $db->update($this->getTableName(), [
            'zip_url' => $zipUrl,
            'version' => $version,
            'installed_at' => Date::now()->getDatetime(),
        ], "id = " . (int)$id);
    }

    public function deletePlugin($id)
    {
        Db::get()->query("DELETE FROM " . $this->getTableName() . " WHERE id = ?", [$id]);
    }

    public function getPluginByName($pluginName)
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE plugin_name = ? LIMIT 1";
        return Db::fetchRow($sql, [$pluginName]);
    }
}
