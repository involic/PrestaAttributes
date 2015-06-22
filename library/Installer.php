<?php

/**
 * File: Installer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * PrestaAttributes additional attributes to PrestaShop product.
 * Add MPN and ISBN attributes
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Installer
{
    const VERSION_COMPARE_EQUAL = 0;
    const VERSION_COMPARE_LOWER = -1;
    const VERSION_COMPARE_GREATER = 1;

    protected $_resourceFilePath = null;

    public function __construct()
    {
        $this->_resourceFilePath = _PS_MODULE_DIR_ . "prestaattributes/var/sql";
    }

    public function applyAction($action, $fromVersion, $toVersion)
    {
        $filesListAvailable = $this->_readAvailableResourceFiles($action);
        if (empty($filesListAvailable)) {
            return false;
        }

        $filesListForApply = $this->_getApplicableResourceFiles($action, $fromVersion, $toVersion, $filesListAvailable);
        if (empty($filesListForApply)) {
            return false;
        }

        $versionThatsApply = false;
        foreach ($filesListForApply as $resourceFile) {
            $sql = file_get_contents($resourceFile['fileName']);
            try {
                include($resourceFile['fileName']);
            } catch (Exception $ex) {
                $message = L::t("Error in file").' '.$resourceFile['fileName']. ": ". $ex->getMessage();
                throw new Exception($message);
            }
            // Success install file. Apply version
            $versionThatsApply = $resourceFile['toVersion'];
        }

        return $versionThatsApply;
    }

    public function executeSql($commandString)
    {
        // Change Prefix
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $commandString);
        // Split sqls to single queary
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            $query = trim($query);
            if ($query && sizeof($query) && !Db::getInstance()->Execute($query)) {
                $message = 'Error execute SQL:<br/><i>' . $query . "</i>";
                $message.= '<br/>Description: "' . mysql_error() . '"';

                throw new Exception($message);
            }
        }
    }

    public function registerHook($hookName)
    {
        if (!CoreHelper::isPS15()) {
            if ($hookName == "updateQuantity" || $hookName == "actionAdminOrdersTrackingNumberUpdate") {
                // This hooks valid only for PS 1.5
                return;
            }
        }
        try {
            if (!Module::getInstanceByName("prestaattributes")->registerHook($hookName)) {
                 DebugHelper::addDebug("Can't register hook {$hookName}");
            }
        } catch (Exception $ex) {
             DebugHelper::addDebug("Can't register hook {$hookName}"." ".$ex->getMessage());
        }
    }

    public function unregisterHook($hookName)
    {
        if (!Module::getInstanceByName("prestaattributes")->unregisterHook($hookName)) {
             DebugHelper::addDebug("Can't unregister hook {$hookName}");
        }
    }

    protected function _readAvailableResourceFiles($actionType)
    {
        $sqlFilesDir = $this->_resourceFilePath;

        if (!is_dir($sqlFilesDir) || !is_readable($sqlFilesDir)) {
            return false;
        }

        // Read resource files
        $arrAvailableFiles = array();
        $fileToDir = glob($sqlFilesDir . "/" . $actionType . "-*.php");
        foreach ($fileToDir as $sqlFile) {
            $matches = array();
            if (preg_match('#.*' . $actionType . '-(.*)\.php$#i', $sqlFile, $matches)) {
                $arrAvailableFiles[$matches[1]] = $sqlFile;
            }
        }

        if (empty($arrAvailableFiles)) {
            return false;
        }

        return $arrAvailableFiles;
    }

    /**
     * Get sql files that need apply for modifications
     *
     * @param     $actionType
     * @return    array
     */
    protected function _getApplicableResourceFiles($actionType, $fromVersion, $toVersion, $arrFiles)
    {
        $arrRes = array();

        switch ($actionType) {
            case 'install':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $toVersion) !== self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = array('toVersion' => $version, 'fileName' => $file);
                    }
                }
                break;

            case 'upgrade':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    $version_info = explode('-', $version);

                    // In array must be 2 elements: 0 => version from, 1 => version to
                    if (count($version_info) != 2) {
                        break;
                    }
                    $infoFrom = $version_info[0];
                    $infoTo = $version_info[1];
                    if (version_compare($infoFrom, $fromVersion) !== self::VERSION_COMPARE_LOWER
                            && version_compare($infoTo, $toVersion) !== self::VERSION_COMPARE_GREATER) {
                        $arrRes[] = array('fromVersion' => $infoFrom, 'toVersion' => $infoTo, 'fileName' => $file);
                    }
                }
                break;

            case 'uninstall':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $fromVersion) !== self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = array('toVersion' => $version, 'fileName' => $file);
                    }
                }
                break;
        }

        return $arrRes;
    }

}
