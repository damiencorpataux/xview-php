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
 *
 * Responsibilities
 * - deals with rendering and internationalization (i18n)
 * @package xFreemwork
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
     * The tmp (working) directory.
     * Example: '/path/without/trailing/slash'
     * @var string
     */
    public static $tmp = '/tmp';

    /**
     * @param string Name of the view to render.
     * @param array Array of data to feed the view.
     * @return string Processed template result.
     */
    public function render($template, $data) {
        $stream = self::load($template);
        $tmp = self::$tmp;
        $file = uniqid("$tmp/xview_generated_", true);
        file_put_contents($file, self::process($stream));
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
     * Processes markup.
     * Replaces:
     * - {{ ... }} with <?php ... ?>
     * - {{{ ... }}} with <?php echo ... ?>
     * @param string A markup template.
     * @return string A PHP template.
     */
    protected static function process($stream) {
        $stream = preg_replace('/{{{(.*)}}}/', '<?php echo $1 ?>', $stream);
        $stream = preg_replace('/{{(.*)}}/', '<?php $1 ?>', $stream);
//echo "<pre>$stream";
        return $stream;
    }

    protected static function load($template) {
        $path = self::$path;
        $extension = self::$extension;
        $file = "{$path}/{$template}.{$extension}";
        if (!file_exists($file)) {
            throw new Exception("Template file not found ($file)");
        }
        return file_get_contents($file);;
    }

    /**
     * Loads and returns the view specified object.
     * For example, the following code will
     * load the views/entry/item.php file.
     * and return an instance of the EntryItemView class:
     * <code>
     * xView::load('entry/item');
     * </code>
     * @param string The view to load.
     * @param array An array of data to be merged to the view instance
     * @param array An array of metadata on which the view metadata will be merged
     * @return xView
     */
    static function ______load($name, $data = null) {
        $file = xContext::$basepath."/views/{$name}.php";
        xContext::$log->log("Loading view: $file", 'xView');
        if (file_exists($file)) {
            require_once($file);
            $class_name = str_replace(array('/', '.', '-'), '', $name."View");
            xContext::$log->log(array("Instanciating view: $class_name"), 'xView');
            $instance = new $class_name($data);
        } else {
            $instance = new xView($data);
        }
        // Computes view basepath
        $parts = explode('/', $name);
        array_pop($parts);
        $instance->path = xContext::$basepath."/views/".implode('/', $parts);
        $instance->default_tpl = array_pop(explode('/', $name)).'.tpl';
        // Merges view meta with the given array
        if (is_array($meta_return)) {
            xContext::$log->log(array("xView::load(): merging view meta into given array"), 'xView');
            $meta_return = xUtil::array_merge($meta_return, $instance->meta);
        }
        return $instance;
    }

    /**
     * Renders the given template with the given data.
     * @param string $template The filename of the template to use
              (e.g. tplfile.tpl).
     * @param mixed $data The data to be used within the template context.
              (defaults to instance data property).
     * @return string
     */
    function ______render($template) {
        // Disables notices reporting in php template code
        $error_reporting = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);
        // Create template-wide variables and functions
        $d = xUtil::array_merge($this->data, $data);
        $m = xUtil::array_merge($this->meta, $meta);
        if (!function_exists('u')) {
            function u($path = null, $full = false) {
                return xUtil::url($path, $full);
            }
        }
        // Loads the template and processes template tags
        $file = "{$this->path}/{$template}";
        if (!file_exists($file)) throw new Exception("Template file not found ($file)");
        // Creates a pure-PHP template
//        $template = $this->process_tags(file_get_contents($file));
        // Renders the template
        // TODO: Clean spaces before '<?php' and after '>'
        ob_start();
        require($file);
        $s = ob_get_contents();
        ob_end_clean();
        // Reverts error reporting level
        error_reporting($error_reporting);
        // Embeds the template if applicable
        if ($this->container) {
            $container_view = xView::load($this->container, array(
                'contents' => $s,
                'misc' => @$d['container']
            ), $this->meta);
            $s = $container_view->render();
        }
        return $s;
    }

/*
    function process_tags($template) {
        // Processes {{ ... }}
        $template = preg_replace_callback('/\{(.*?)\}/', array($this, "process_tag_var"), $template);
        return $template;
    }
    function process_tag_var($matches) {
        $var = $matches[1];
        // Create PHP variable
        $phpvar = '$d';
        foreach (explode('.', $var) as $fragment) $phpvar .= "['{$fragment}']";
        // Replaces tag with PHP snippet
        return "<?php print @{$phpvar} ?>";
    }
*/
}