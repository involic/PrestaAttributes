<?php

/**
 * File: RenderHelper
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
class RenderHelper
{

    private static $_scriptsList = array();
    private static $_scriptsGlobalList = array();
    private static $_cssList = array();
    private static $_isClean = false;
    private static $_template_dir = "prestaattributes/views";

    public static function addGrobalScript($scriptPath)
    {
        $pos = strrpos($scriptPath, "/");
        $key = null;
        if ($pos === false) {
            $key = $scriptPath;
        } else {
            $key = substr($scriptPath, $pos);
        }
        self::$_scriptsGlobalList[$key] = $scriptPath;
    }

    public static function addScript($scriptPath)
    {
        $pos = strrpos($scriptPath, "/");
        $key = null;
        if ($pos === false) {
            $key = $scriptPath;
        } else {
            $key = substr($scriptPath, $pos);
        }
        self::$_scriptsList[$key] = $scriptPath;
    }

    public static function addCss($cssPath)
    {
        $pos = strrpos($cssPath, "/");
        $key = null;
        if ($pos === false) {
            $key = $cssPath;
        } else {
            $key = substr($cssPath, $pos);
        }
        self::$_cssList[$key] = $cssPath;
    }

    public static function generateHeader()
    {
        foreach (self::$_cssList as $singleCss) {
            echo '<link rel="stylesheet" type="text/css" media="screen" href="../modules/prestabay/css/' . $singleCss . '" />';
        }

        foreach (self::$_scriptsGlobalList as $singleScript) {
            echo '<script type="text/javascript" src="../js/' . $singleScript . '"></script>';
        }

        foreach (self::$_scriptsList as $singleScript) {
            echo '<script type="text/javascript" src="../modules/prestabay/js/' . $singleScript . '"></script>';
        }
    }

    public static function cleanOutput()
    {
        $controllerOutput = ob_get_contents();
        @ob_end_clean();
        self::$_isClean = true;

        self::$_scriptsList = array();
        self::$_cssList = array();
    }

    public static function isSetClean()
    {
        return self::$_isClean;
    }

    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
    
    public static function view($view, $vars = array(), $showOutput = true)
    {
        $output = self::_loadView(array('_view' => $view, '_vars' => $vars));
        if ($showOutput) {
            echo $output;
        } else {
            return $output;
        }
    }

    protected static function _loadView($_data)
    {
        $defaultExt = ".phtml";

        $templateDir = _PS_MODULE_DIR_ . self::$_template_dir;
        // Set the default data variables
        foreach (array('_view', '_vars', '_path') as $_val) {
            $$_val = (!isset($_data[$_val])) ? false : $_data[$_val];
        }

        // Set the path to the requested file
        $_ext = pathinfo($_view, PATHINFO_EXTENSION);
        $_file = ($_ext == '') ? $_view . $defaultExt : $_view;
        $_path = $templateDir . "/" . $_file;


        if (!file_exists($_path)) {
            echo L::t('Unable to load the requested file') . ': ' . $_file . "<br>";
        }
        extract($_vars);

        // Start buffer the output 
        ob_start();

        include($_path);

        // Return the file data
        // Buffered output to variable 
        $buffer = ob_get_contents();
        @ob_end_clean();
        return $buffer;
    }

}

