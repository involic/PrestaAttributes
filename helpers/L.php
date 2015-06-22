<?php

/**
 * File: L
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
class L
{
    protected static $_messageFile = null;
    protected static $_cachedTranslate = array();

    public static function t($textToTranslate)
    {
        $key = $textToTranslate; //strtolower($textToTranslate);
        if (isset(self::$_cachedTranslate[$key])) {
            return self::$_cachedTranslate[$key];
        }

        if (is_null(self::$_messageFile)) {
            global $cookie;
            $idLang = (!isset($cookie) OR !is_object($cookie)) ? (int) (Configuration::get('PS_LANG_DEFAULT')) : (int) ($cookie->id_lang);
            self::$_messageFile = _PS_MODULE_DIR_ . 'prestaattributes' . '/locale/' . Language::getIsoById($idLang) . '.php';
        }

        if (!file_exists(self::$_messageFile)) {
            return self::$_cachedTranslate[$key] = $textToTranslate;
        }

        $messages = include(self::$_messageFile);
        if (!is_array($messages)) {
            $messages = array();
        }
        return self::$_cachedTranslate[$key] = isset($messages[$key]) ? $messages[$key] : $textToTranslate;
    }

}