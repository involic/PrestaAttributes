<?php

/**
 * File: UrlHelper
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
class UrlHelper
{

    public static function getPost($key = false, $defaultValue = false)
    {
        if (isset($_SERVER["CONTENT_TYPE"])) {
            $v = strpos($_SERVER["CONTENT_TYPE"], "application/json");
            if ($v !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
            }
        }
        if ($key == false) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $defaultValue;
    }

    public static function getGet($key, $defaultValue = false)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $defaultValue;
    }

    public static function getUrl($request, $params = array())
    {
        // Generate token
        global $currentIndex, $cookie;

        // Generate full path
        $curSchema = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $curSchema .= "s";
        }
        $port = "";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $port = ":" . $_SERVER["SERVER_PORT"];
        }
        // HTTP_HOST <- for path with www
        $baseUrl = $curSchema . '://' . $_SERVER['HTTP_HOST'] . $port;

        // Append additional parameters
        $requestStr = $request;
        if (!is_null($params) && $params != array()) {
            foreach ($params as $key => $value) {
                $requestStr .= "/" . $key . "/" . $value;
            }
        }

        if (CoreHelper::isPS16()) {
            $className = 'AdminPrestaAttributes';
            $tokenKey  = Tools::getAdminToken($className . (int) (Tab::getCurrentTabId()) . (int) ($cookie->id_employee));

            return $baseUrl . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $currentIndex . "&request=" . $requestStr . "&token=" . $tokenKey;
        } else {
            $className = 'AdminBay';
            $tokenKey  = Tools::getAdminToken($className . (int) (Tab::getCurrentTabId()) . (int) ($cookie->id_employee));
            is_null($currentIndex) && $currentIndex = AdminTab::$currentIndex;

            return $baseUrl . $currentIndex . '&request=' . $requestStr . '&token=' . $tokenKey;
        }
    }

    public static function getPrestaUrl($tab, $requestArray = array())
    {
        // Generate token
        global $cookie;
        $tokenKey = Tools::getAdminToken($tab . (int) (Tab::getIdFromClassName($tab)) . (int) ($cookie->id_employee));

        $url = '?tab=' . $tab . '&token=' . $tokenKey;
        if ($requestArray != array()) {
            foreach ($requestArray as $paramKey => $paramValue) {
                $url .= '&' . $paramKey . (!is_null($paramValue) ? '=' . $paramValue : '');
            }
        }
        return $url;
    }

    public static function redirect($request, $params = array())
    {
        header('Location: ' . self::getUrl($request, $params));
        exit;
    }

    public static function redirectExternal($url)
    {
        header('Location: ' . $url);
        exit;
    }

}