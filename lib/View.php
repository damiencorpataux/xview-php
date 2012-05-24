<?php

/*
 * (c) 2012 Damien Corpataux
 *
 * LICENSE
 * This library is licensed under the GNU GPL v3.0 license,
 * accessible at http://www.gnu.org/licenses/gpl-3.0.html
 *
**/

/**
 * Base view class.
 * - Manages markup processing.
 * - Manages caching.
**/
class xView {

    /**
     * The templates directory.
     * Example: '/path/to/files/without/trailing/slash'
     * @var string
     */
    public static $path;

    /**
     * The template files extension.
     * Example: 'tpl'
     * @var string
     */
    public static $extension = 'tpl';

    /**
     * The cache directory.
     * Example: '/path/without/trailing/slash'
     * @var string
     */
    public static $cache = '/tmp';

    /**
     * @param string Name of the template to render.
     * @param array Array of data to feed the template.
     * @return string Processed template result.
     */
    public static function render($template, $data=array()) {
        $file = self::cache($template);
        return self::apply($file, $data);
    }

    /**
     * Manages processed template caching.
     * Creates a processed tmp file (if necessary)
     * and returns its filename (including absolute path).
     * @param string Name of the view to cache.
     * @return string Filename of the cached processed view.
     */
    protected static function cache($template) {
        $markup = self::load($template);
        $file = implode('', array(
            self::$cache, '/',
            str_replace('/', '-', $template), '-',
            md5($markup)
        ));
        // Manages cache
        if (!file_exists($file)) {
            file_put_contents($file, self::process($markup));
        }
        return $file;
    }

    /**
     * Applies $data to the PHP template $file and returns result.
     * @param string PHP template stream.
     * @param array Data array to apply.
     * @return string Applied template result.
     */
    protected static function apply($file, $data) {
        // Disables notices reporting in php template code
        $error_reporting = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);
        // Renders the template
        $d = $data;
        ob_start();
        require($file);
        $s = ob_get_contents();
        ob_end_clean();
        // Reverts error reporting level
        error_reporting($error_reporting);
        // Returns applied template
        return $s;
    }

    /**
     * Processes markup into PHP template.
     * Replaces:
     * - {{ ... }} with <?php ... ?>
     * - {{{ ... }}} with <?php echo ... ?>
     * @param string A markup template.
     * @return string A PHP template.
     */
    protected static function process($stream) {
        $stream = preg_replace('/{{{(.*)}}}/', '<?php echo $1 ?>', $stream);
        $stream = preg_replace('/{{(.*)}}/', '<?php $1 ?>', $stream);
        return $stream;
    }

    /**
     * Loads a view template and returns its contents.
     * @param string Name of the template to load.
     */
    protected static function load($template) {
        $path = self::$path;
        $extension = self::$extension;
        $file = "{$path}/{$template}.{$extension}";
        if (!file_exists($file)) {
            throw new Exception("Template file not found ($file)");
        }
        return file_get_contents($file);;
    }
}