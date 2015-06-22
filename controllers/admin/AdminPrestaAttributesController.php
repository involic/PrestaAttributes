<?php

/**
 * File: AdminPrestaAttributesController
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
class AdminPrestaAttributesController extends ModuleAdminController
{
    protected $_module = 'prestaattributes';

    public $multishop_context_group = true;
    public $multishop_context = null;

    protected $myCone = "";

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->meta_title = $this->l('Presta Attributes');
        parent::__construct();
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function renderView()
    {
        $this->prestaattributesProccessRequest();
        if (empty($_POST)) {
            echo 'Please open PrestaShop product, Attributes tab';
            return;
        }

        $post = $_POST;
        $result = false;
        if (isset($post['id_product']) && isset($post['prestaattributes'])) {
            $result = AttributesDataModel::createUpdateProductId($post['id_product'], $post['prestaattributes']);
        }

        RenderHelper::cleanOutput();

        if ($result) {
            echo json_encode(array('success' => true, 'message' => 'Attributes saved'));
        } else {
            echo json_encode(array('success' => false));
        }
        exit;
    }


    protected function prestaattributesProccessRequest()
    {
        if (defined('INVATTR_VERSION_DATA') && INVATTR_VERSION_DATA) {
        } else {
            $path = _PS_MODULE_DIR_ . 'prestaattributes/';
            include($path . 'library/Autoloader.php');
            Autoloader::init($path);
        }
    }

}
