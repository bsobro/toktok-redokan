<?php

namespace ContentEgg\application;

use ContentEgg\application\components\ModuleManager;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\components\ModuleTemplateManager;
use ContentEgg\application\components\Shortcoded;
use ContentEgg\application\helpers\ArrayHelper;
use ContentEgg\application\components\BlockTemplateManager;

/**
 * ModuleViewer class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class ModuleViewer {

    private static $instance = null;
    private $module_data_pointer = array();
    private $block_data_pointer = array();
    private $data = array();

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;

        return self::$instance;
    }

    private function __construct()
    {
        
    }

    public function init()
    {
        // priority = 12 because do_shortcode() is registered as a default filter on 'the_content' with a priority of 11. 
        \add_filter('the_content', array($this, 'viewData'), 12);
    }

    public function setData($module_id, $post_id, array $data)
    {
        if (!isset($this->data[$post_id]))
            $this->data[$post_id] = array();
        $this->data[$post_id][$module_id] = $data;
    }

    public function getData($module_id, $post_id, $params = array())
    {
        if (isset($this->data[$post_id]) && isset($this->data[$post_id][$module_id]))
            return $this->data[$post_id][$module_id];
        else
            return ContentManager::getViewData($module_id, $post_id, $params);
    }

    public function viewData($content)
    {
        global $post;
        if ($post)
            $post_id = $post->ID;
        else
            $post_id = -1;

        /*
          if (!is_single() && !is_page)
          return $content;
         * 
         */
        $top_modules_priorities = array();
        $bottom_modules_priorities = array();
        foreach (ModuleManager::getInstance()->getModules(true) as $module_id => $module)
        {
            $embed_at = $module->config('embed_at');
            if ($embed_at != 'post_bottom' && $embed_at != 'post_top')
                continue;
            if (Shortcoded::getInstance($post_id)->isShortcoded($module->getId()))
                continue;

            $priority = (int) $module->config('priority');
            if ($embed_at == 'post_top')
                $top_modules_priorities[$module_id] = $priority;
            elseif ($embed_at == 'post_bottom')
                $bottom_modules_priorities[$module_id] = $priority;
        }

        // sort by priority, keep module_id order
        $top_modules_priorities = ArrayHelper::asortStable($top_modules_priorities);
        $bottom_modules_priorities = ArrayHelper::asortStable($bottom_modules_priorities);

        // reverse for corret gluing order
        $top_modules_priorities = array_reverse($top_modules_priorities, true);
        foreach ($top_modules_priorities as $module_id => $p)
        {
            $content = $this->viewModuleData($module_id, $post_id, array()) . $content;
        }
        foreach ($bottom_modules_priorities as $module_id => $p)
        {
            $content = $content . $this->viewModuleData($module_id, $post_id, array());
        }

        return $content;
    }

    public function viewModuleData($module_id, $post_id = null, $params = array())
    {
        if (!$post_id)
        {
            global $post;
            $post_id = $post->ID;
        }

        $data = $this->getData($module_id, $post_id, $params);
        if (!$data)
            return '';

        $module = ModuleManager::factory($module_id);
        $keyword = \get_post_meta($post_id, ContentManager::META_PREFIX_KEYWORD . $module->getId(), true);

        if (!isset($this->module_data_pointer[$post_id]))
            $this->module_data_pointer[$post_id] = array();

        // next param
        if (!empty($params['next']))
        {
            if (!isset($this->module_data_pointer[$post_id][$module_id]))
                $this->module_data_pointer[$post_id][$module_id] = 0;

            $data = array_splice($data, $this->module_data_pointer[$post_id][$module_id], $params['next']);
            if (count($data) < $params['next'])
                $params['next'] = count($data);

            $this->module_data_pointer[$post_id][$module_id] += $params['next'];
        } elseif (!empty($params['limit']))
        {
            if (!isset($params['offset']))
                $params['offset'] = 0;

            $data = array_splice($data, $params['offset'], $params['limit']);
            $this->module_data_pointer[$post_id][$module_id] = $params['offset'] + $params['limit'];
        }
        if (!$data)
            return;

        // template
        $tpl_manager = ModuleTemplateManager::getInstance($module_id);
        if (!empty($params['template']) && $tpl_manager->isTemplateExists($params['template']))
            $template = $params['template'];
        else
            $template = $module->config('template');

        if (!empty($params['title']))
            $title = $params['title'];
        else
            $title = $module->config('tpl_title');

        if (!empty($params['cols']))
            $cols = $params['cols'];
        else
            $cols = 0;

        return $tpl_manager->render($template, array('items' => $data, 'title' => $title, 'keyword' => $keyword, 'post_id' => $post_id, 'module_id' => $module_id, 'cols' => $cols));
    }

    public function viewBlockData(array $module_ids, $post_id = null, $params = array())
    {
        if (!$post_id)
        {
            global $post;
            $post_id = $post->ID;
        }

        // Get modules data
        $data = array();
        foreach ($module_ids as $module_id)
        {
            $module_data = $this->getData($module_id, $post_id, $params);
            if ($module_data)
                $data[$module_id] = $module_data;

            // shortcoded!
            if (!isset($params['shortcoded']) || (bool) $params['shortcoded'])
                Shortcoded::getInstance($post_id)->setShortcodedModule($module_id);
        }
        if (!$data)
            return;

        // template
        $tpl_manager = BlockTemplateManager::getInstance();
        if (empty($params['template']) || !$tpl_manager->isTemplateExists($params['template']))
            return;
        $template = $params['template'];

        // next, limit, offset
        if (!isset($this->block_data_pointer[$post_id]))
            $this->block_data_pointer[$post_id] = array();
        if (!empty($params['next']))
        {
            if (!isset($this->block_data_pointer[$post_id][$template]))
                $this->block_data_pointer[$post_id][$template] = 0;

            $data = $this->spliceBlockData($data, $this->block_data_pointer[$post_id][$template], $params['next']);
            $count = $this->countBlockData($data);
            if ($count < $params['next'])
                $params['next'] = $count;
            $this->block_data_pointer[$post_id][$template] += $params['next'];
        } elseif (!empty($params['limit']))
        {
            if (!isset($params['offset']))
                $params['offset'] = 0;

            $data = $this->spliceBlockData($data, $params['offset'], $params['limit']);
            $this->block_data_pointer[$post_id][$module_id] = $params['offset'] + $params['limit'];
        }
        if (!$data)
            return;

        // title
        if (!empty($params['title']))
            $title = $params['title'];
        else
            $title = '';

        if (!empty($params['cols']))
            $cols = $params['cols'];
        else
            $cols = 0;

        return $tpl_manager->render($params['template'], array('data' => $data, 'post_id' => $post_id, 'title' => $title, 'cols' => $cols, 'sort' => $params['sort'], 'order' => $params['order']));
    }

    private function spliceBlockData($data, $offset, $length)
    {
        $results = array();
        $count = 0;
        $results_count = 0;
        foreach ($data as $module_id => $module_data)
        {
            $results[$module_id] = array();
            foreach ($module_data as $key => $data)
            {
                if ($count < $offset)
                {
                    $count++;
                    continue;
                }

                $results[$module_id][$key] = $data;
                $count++;
                $results_count++;
                if ($results_count >= $length)
                    return $results;
            }
        }
        return $results;
    }

    private function countBlockData($data)
    {
        $count = 0;
        foreach ($data as $module_id => $module_data)
        {
            $count += count($module_data);
        }
        return $count;
    }

}
