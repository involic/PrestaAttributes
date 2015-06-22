<?php

/**
 * File: Autoloader
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
class Autoloader
{

    public static $instance;

    private $_src = array('controllers/', 'models/', 'helpers/', 'library/');
    private $_ext = array('.php');
    protected $_docRoot = null;

    protected static $_classListCache = false;

    /**
     * Initialize the autoloader class
     */
    public static function init($docRoot)
    {
        if (self::$instance == null) {
            self::$instance = new self($docRoot);
        }

        return self::$instance;
    }

    /**
     * Put the custom functions in the autoload register when the class
     * is initialized
     */
    private function __construct($docRoot)
    {
        define('_PRESTAATTR_AUTOLOADER_LOADED_', true);

        $this->_docRoot = $docRoot;
        self::$_classListCache = $this->getClassesFromDir();

        if (version_compare(phpversion(), "5.3.0") >= 0) {
            spl_autoload_register(array($this, 'dirty'), true, true);
        } else {
            spl_autoload_register(array($this, 'dirty'));
        }
        if (substr(_PS_VERSION_, 0, 3) == '1.4') {
            // is PS 1.4?
            spl_autoload_register('__autoload');
        }
    }

    /**
     * The dirty method to autoload the class after including the php file
     * containing the class
     */
    private function dirty($class)
    {
        if (isset(self::$_classListCache[$class])) {
            include(self::$_classListCache[$class]);
            return true;
        }
        return false;
    }

    protected function getClassesFromDir()
    {
        $files = array();
        foreach ($this->_src as $resource) {
            $files = array_merge($files, $this->getFilesFromDir($resource));
        }

        return $files;
    }

    /**
     * Retrieve recursively all classes in a directory and its subdirectories
     *
     * @param string $path Relativ path from root to the directory
     * @return array
     */
    protected function getFilesFromDir($path, $addPath = '')
    {
        $classes = array();

        foreach (scandir($this->_docRoot . $path . $addPath) as $file) {
            if ($file[0] != '.') {
                if (is_dir($this->_docRoot . $path . $addPath . $file)) {
                    $classes = array_merge($classes, $this->getFilesFromDir($path, $addPath . $file . '/'));
                } else {
                    if (substr($file, -4) == '.php') {
                        $key           = str_replace('/', '_', $addPath . substr($file, 0, -4));
                        $classes[$key] = $this->_docRoot . $path . $addPath . $file;
                    }
                }
            }
        }

        return $classes;
    }
}
