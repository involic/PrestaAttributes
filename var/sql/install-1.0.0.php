<?php
/**
 * File: install-1.0.0
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

$installer = $this;

$installer->registerHook('displayAdminProductsExtra');

$installer->executeSql("
    ALTER TABLE `PREFIX_product`
            ADD COLUMN `attribute_mpn` VARCHAR(15) NULL,
            ADD COLUMN `attribute_isbn` VARCHAR(13) NULL;
");