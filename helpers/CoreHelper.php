<?php

/**
 * File: CoreHelper
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
class CoreHelper
{
    protected static $_is16 = null;
    protected static $_is15 = null;
    protected static $_is14 = null;
    protected static $_is13 = null;

    public static function isPS16()
    {
        if (is_null(self::$_is16)) {
            self::$_is16 = substr(_PS_VERSION_, 0, 3) == '1.6';
        }
        return self::$_is16;
    }

    public static function isPS15()
    {
        return self::isOnlyPS15() || self::isPS16();
    }

    public static function isOnlyPS15()
    {
        if (is_null(self::$_is15)) {
            self::$_is15 = substr(_PS_VERSION_, 0, 3) == '1.5';
        }

        return self::$_is15;
    }

    public static function isPS14()
    {
        if (is_null(self::$_is14)) {
            self::$_is14 = substr(_PS_VERSION_, 0, 3) == '1.4';
        }
        return self::$_is14;
    }

    public static function isPS13()
    {
        if (is_null(self::$_is13)) {
            self::$_is13 = substr(_PS_VERSION_, 0, 3) == '1.3';
        }
        return self::$_is13;
    }

    public static function createMultiLangField($field)
    {
        $languages = Language::getLanguages(false);
        $res = array();
        foreach ($languages AS $lang)
            $res[$lang['id_lang']] = $field;
        return $res;
    }
}
