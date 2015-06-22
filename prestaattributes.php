<?php
/**
 * File: PrestaAttributes
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
class PrestaAttributes extends Module
{

    public function __construct()
    {
        $this->name = 'prestaattributes';
        $this->tab = 'Product';
        $this->version = '1.0.0';
        $this->tabName = "AdminPrestaAttributes";

        if (substr(_PS_VERSION_, 0, 3) == '1.6') {
            $this->bootstrap = true;
        }

        parent::__construct();

        $this->displayName = $this->l('Presta Attributes');
        $this->description = $this->l('Add to PrestaShop Product attributes - MPN, ISBN.');
        $this->author = 'Involic';
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
    }

    /**
     * This is specific hook related to PS 1.5 and PS 1.6
     *
     * @param $params
     * @return mixed
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $this->_initPrestaAttrAutoloader();

        $productId = Tools::getValue('id_product', 0);

        if (!$productId) {
            return;
        }

        return RenderHelper::view('product/attribute-tab.phtml', array(
            'attributesData' => AttributesDataModel::loadByProductId($productId)
        ), false);
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $this->_initPrestaAttrAutoloader();

        if (Configuration::get('INVATTR_VERSION_DATA')) {
            echo "Module already installed";
            // Module already installed don't allow install one more time
            return false;
        }

        // Registering menu
        // old value 1,
        $resultOfMainTabId = $this->_installModuleTab($this->tabName, 'Attributes', CoreHelper::isPS15()? 9 : 1);
        if ($resultOfMainTabId === false) {
            return false;
        }

        // Perform sql updates
        // install or upgrade?
        $action = "install";
        $fromVersion = null;
        if (Configuration::get('INVATTR_VERSION_DATA')) {
            $fromVersion = Configuration::get('INVATTR_VERSION_DATA');
            $action = "upgrade";
        }

        $installer = new Installer();
        try {
            $applyDataVersion = $installer->applyAction($action, $fromVersion, $this->version);
        } catch (Exception $ex) {
            return false;
        }

        if (!$applyDataVersion) {
            return false;
        }

        if (!Configuration::updateValue('INVATTR_VERSION_DATA', $applyDataVersion)) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $this->_initPrestaAttrAutoloader();

        $this->_uninstallModuleTab($this->tabName);

        // Run uninstall sql
        if (!($fromVersion = Configuration::get('INVATTR_VERSION_DATA'))) {
            return false;
        }

        include _PS_MODULE_DIR_ . $this->name . '/library/Installer.php';

        $installer = new Installer();
        try {
            $applyUninstallDataVersion = $installer->applyAction("uninstall", $fromVersion, null);
        } catch (Exception $ex) {
            return false;
        }

        if (!$applyUninstallDataVersion) {
            return false;
        }

        // Remove configuration data
        if (!Configuration::deleteByName('INVATTR_VERSION_DATA') || !Configuration::deleteByName("INVATTR_VERSION_DATA")) {
            return false;
        }

        return true;
    }

    protected function _initPrestaAttrAutoloader()
    {
        if (defined('_PRESTAATTR_AUTOLOADER_LOADED_') && _PRESTAATTR_AUTOLOADER_LOADED_) {
            return;
        }
        $path = _PS_MODULE_DIR_ . $this->name . '/';
        include($path . 'library/Autoloader.php');
        Autoloader::init($path);
    }


    protected function _installModuleTab($tabClass, $tabName, $idTabParent)
    {
        $tab = new Tab();
        foreach (Language::getLanguages() as $language) {
            $tab->name[$language['id_lang']] = $tabName;
        }

        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;
        if (!$tab->save())
            return false;
        return $tab->id;
    }

    protected function _uninstallModuleTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }
}