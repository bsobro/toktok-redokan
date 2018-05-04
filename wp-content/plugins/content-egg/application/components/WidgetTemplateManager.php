<?php

namespace ContentEgg\application\components;

/**
 * WidgetTemplateManager class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class WidgetTemplateManager extends TemplateManager {

    const TEMPLATE_DIR = 'templates';
    const CUSTOM_TEMPLATE_DIR = 'content-egg-templates';
    const TEMPLATE_PREFIX = 'wdgt_';

    protected $widget_slug;
    protected $widget_short_slug;
    private static $_instances = array();

    public static function getInstance($widget_slug)
    {
        if (!isset(self::$_instances[$widget_slug]))
        {
            $class = get_called_class();
            self::$_instances[$widget_slug] = new $class($widget_slug);
        }
        return self::$_instances[$widget_slug];
    }

    private function __construct($widget_slug)
    {
        $this->widget_slug = $widget_slug;
        $this->widget_short_slug = preg_replace('/^cegg_/', '', $this->widget_slug);
    }

    public function getTempatePrefix()
    {
        return self::TEMPLATE_PREFIX . $this->widget_short_slug . '_';
    }

    public function getTempateDir()
    {
        return \ContentEgg\PLUGIN_PATH . self::TEMPLATE_DIR;
    }

    public function getCustomTempateDirs()
    {
        return array(
            'child-theme' => \get_stylesheet_directory() . '/' . self::CUSTOM_TEMPLATE_DIR, //child theme		
            'theme' => \get_template_directory() . '/' . self::CUSTOM_TEMPLATE_DIR, // theme
            'custom' => \ABSPATH . 'wp-content/' . self::CUSTOM_TEMPLATE_DIR,
        );
    }
    
    public function render($view_name, array $_data = array())
    {
        if (!self::isCustomTemplate($view_name))
            $this->enqueueProductsStyle();
        return parent::render($view_name, $_data);
    }    

}
