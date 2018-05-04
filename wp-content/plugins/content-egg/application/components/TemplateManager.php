<?php

namespace ContentEgg\application\components;

use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\admin\GeneralConfig;

/**
 * TemplateManager class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
abstract class TemplateManager {

    private $templates = null;
    private $last_render_data;

    abstract public function getTempatePrefix();

    abstract public function getTempateDir();

    abstract public function getCustomTempateDirs();

    public function getTemplatesList($short_mode = false)
    {
        $prefix = $this->getTempatePrefix();
        if ($this->templates === null)
        {
            $templates = array();
            foreach ($this->getCustomTempateDirs() as $custom_name => $dir)
            {
                $templates = array_merge($templates, $this->scanTemplates($dir, $prefix, $custom_name));
            }
            $templates = array_merge($templates, $this->scanTemplates($this->getTempateDir(), $prefix, false));
            $this->templates = $templates;
        }

        if ($short_mode)
        {
            $list = array();
            foreach ($this->templates as $id => $name)
            {
                $custom = '';
                if (self::isCustomTemplate($id))
                {
                    $parts = explode('/', $id);
                    $custom = 'custom/';
                    $id = $parts[1];
                }

                // del prefix
                $list[$custom . substr($id, strlen($prefix))] = $name;
            }
            return $list;
        }

        return $this->templates;
    }

    private function scanTemplates($path, $prefix, $custom_name = false)
    {
        if ($custom_name && !is_dir($path))
            return array();

        $tpl_files = glob($path . '/' . $prefix . '*.php');
        if (!$tpl_files)
            return array();

        $templates = array();
        foreach ($tpl_files as $file)
        {
            $template_id = basename($file, '.php');
            if ($custom_name)
                $template_id = 'custom/' . $template_id;

            $data = \get_file_data($file, array('name' => 'Name'));
            if ($data && !empty($data['name']))
                $templates[$template_id] = strip_tags($data['name']);
            else
                $templates[$template_id] = $template_id;
            if ($custom_name)
                $templates[$template_id] .= ' [' . esc_attr(__($custom_name, 'content-egg')) . ']';
        }
        return $templates;
    }

    public function render($view_name, array $_data = array())
    {
        $file = $this->getViewPath($view_name);
        if (!$file)
            return '';

        $this->last_render_data = $_data;
        extract($_data, EXTR_PREFIX_SAME, 'data');

        ob_start();
        ob_implicit_flush(false);
        include $file;
        $res = ob_get_clean();
        return $res;
    }

    public function renderPartial($view_name, array $_data = array())
    {
        $file = $this->getPartialViewPath($view_name, false);
        if (!$file)
            return '';
        $this->renderPath($file, $_data);
    }

    public function renderBlock($view_name, array $data = array())
    {
        $file = $this->getPartialViewPath($view_name, true);
        if (!$file)
            return '';
        $this->renderPath($file, $data);
    }

    protected function renderPath($view_path, $_data = array())
    {
        if (!is_file($view_path) || !is_readable($view_path))
            throw new \Exception('View file "' . $view_path . '" does not exist.');

        $_data = array_merge($this->last_render_data, $_data);
        extract($_data, EXTR_PREFIX_SAME, 'data');
        include $view_path;
    }

    private function getPartialViewPath($view_name, $block = false)
    {
        $view_name = str_replace('.', '', $view_name);
        $file = \ContentEgg\PLUGIN_PATH . 'application/templates/';
        if ($block)
            $file .= 'blocks/';
        else
            $file .= $this->getTempatePrefix();
        $file .= TextHelper::clear($view_name) . '.php';
        if (is_file($file) && is_readable($file))
            return $file;
        else
            return false;
    }

    public function getViewPath($view_name)
    {
        $view_name = str_replace('.', '', $view_name);
        if (self::isCustomTemplate($view_name))
        {
            $view_name = substr($view_name, 7);
            foreach ($this->getCustomTempateDirs() as $custom_prefix => $custom_dir)
            {
                $tpl_path = $custom_dir;
                $file = $tpl_path . DIRECTORY_SEPARATOR . TextHelper::clear($view_name) . '.php';
                if (is_file($file) && is_readable($file))
                    return $file;
            }
            return false;
        } else
        {
            $tpl_path = $this->getTempateDir();
            $file = $tpl_path . DIRECTORY_SEPARATOR . TextHelper::clear($view_name) . '.php';
            if (is_file($file) && is_readable($file))
                return $file;
            else
                return false;
        }
    }

    public function getFullTemplateId($short_id)
    {
        $prefix = $this->getTempatePrefix();
        $custom = '';
        if (self::isCustomTemplate($short_id))
        {
            $parts = explode('/', $short_id);
            $custom = 'custom/';
            $id = $parts[1];
        } else
            $id = $short_id;

        // check _data prefix
        if (substr($id, 0, strlen($prefix)) != $prefix)
        {
            $id = $prefix . $id;
        }
        return $custom . $id;
    }

    public static function isCustomTemplate($template_id)
    {
        if (substr($template_id, 0, 7) == 'custom/')
            return true;
        else
            return false;
    }

    public function isTemplateExists($tpl)
    {
        return array_key_exists($tpl, $this->getTemplatesList());
    }

    public function prepareShortcodeTempate($template)
    {
        if (self::isCustomTemplate($template))
        {
            $is_custom = true;
            // del 'custom/' prefix
            $template = substr($template, 7);
        } else
            $is_custom = false;

        $template = TextHelper::clear($template);
        if ($is_custom)
            $template = 'custom/' . $template;
        if ($template)
            $template = $this->getFullTemplateId($template);

        return $template;
    }

    public function enqueueProductsStyle()
    {
        \wp_enqueue_style('egg-bootstrap');
        \wp_enqueue_style('egg-products');

        $button_color = GeneralConfig::getInstance()->option('button_color');
        $custom_css = ".egg-container .btn-success{background-color:" . \wp_strip_all_tags($button_color) . " !important;border-color:" . \wp_strip_all_tags($button_color) . " !important}";
        \wp_add_inline_style('egg-products', $custom_css);
    }

}
