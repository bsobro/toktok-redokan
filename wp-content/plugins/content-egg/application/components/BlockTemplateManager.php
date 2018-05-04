<?php

namespace ContentEgg\application\components;

/**
 * BlockTemplateManager class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class BlockTemplateManager extends TemplateManager {

    const TEMPLATE_DIR = 'templates';
    const CUSTOM_TEMPLATE_DIR = 'content-egg-templates';
    const TEMPLATE_PREFIX = 'block_';

    private $module_id;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        
    }

    public function getTempatePrefix()
    {
        return self::TEMPLATE_PREFIX;
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

    public function getModuleId()
    {
        return $this->module_id;
    }

    public function getTemplatesList($short_mode = false)
    {
        $templates = parent::getTemplatesList($short_mode);
        $templates = \apply_filters('content_egg_block_templates', $templates);
        return $templates;
    }

    public function render($view_name, array $_data = array())
    {
        if (!self::isCustomTemplate($view_name))
            $this->enqueueProductsStyle();
        return parent::render($view_name, $_data);
    }

    public function getPartialViewPath($view_name, $block = false)
    {
        $file = parent::getPartialViewPath($view_name, $block);
        if ($file)
            return $file;

        // allow render general block templates as partial
        $file = $this->getViewPath($view_name);
        if ($file)
            return $file;
        else
            return false;
    }

}
