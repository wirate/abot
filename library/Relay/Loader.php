<?php

/**
 * @see Relay_Exception
 */
require_once 'Relay/Exception.php';

/**
 * Class for loading other classes.
 */
class Relay_loader
{
    /**
     * Name of the function used for autoloading.
     *
     * @var array|string
     */
    protected $_autoloadFunction = array(__CLASS__, 'autoload');

    /**
     * Keep track of if autoloading is enabled or not.
     *
     * @var boolean
     */
    protected $_autoload = false;

    /**
     * Singleton instance
     *
     * @var Relay_Loader
     */
    private static $_instance = null;

    /**
     * Singleton
     */
    private function __construct()
    {
    }

    /**
     * Singleton
     */
    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Returns true if autoload is enabled for this loader. false otherwise.
     *
     * @return type
     */
    public function isAutoloadEnabled()
    {
        return $this->_autoload;
    }

    public function setAutoload($enable)
    {
        $this->_autoload = (bool) $enable;

        if ($this->_autoload) {
            spl_autoload_register($this->_autoloadFunction);
        } else {
            spl_autoload_unregister($this->_autoloadFunction);
        }
    }

    /**
     * Load a file. returns true if the file was loaded. false otherwise.
     *
     * @param string $filename  The filename
     * @return boolean
     */
    public function loadFile($filename)
    {
        if (self::isReadable($filename)) {
            require_once $filename;
            return true;
        }
        return false;
    }

    /**
     * Check if a file is readable. this will only check
     * if a file is readable from the include path.
     *
     * @param type $filename  The filename
     * @return boolean
     */
    public static function isReadable($filename)
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach($paths as $path) {

            $fullpath = $path . DIRECTORY_SEPARATOR . $filename;

            if (is_readable($fullpath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Load a class
     *
     * @param string $class     Name of the class.
     * @throws Relay_Exception
     * @return void
     */
    public function loadClass($class)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }

        $filename = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if ($this->loadFile($filename) === false) {
            require_once 'Relay/Exception.php';
            throw new Relay_Exception("Filename '$filename' was not found.");
        }

        // Check if class now after include.
        if (!class_exists($class, false) && !interface_exists($class, false)) {
            require_once 'Relay/Exception.php';
            throw new Relay_Exception("class '$class' was not found");
        }
    }

    /**
     * spl_autoload() implementation.
     *
     * @param string $class
     * @return boolean
     */
    public static function autoload($class)
    {
        $instance = self::getInstance();

        try {
            @$instance->loadClass($class);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}