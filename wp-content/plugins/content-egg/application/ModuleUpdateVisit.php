<?php

namespace ContentEgg\application;

use ContentEgg\application\components\ModuleManager;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\admin\GeneralConfig;

/**
 * ModuleUpdateVisit class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class ModuleUpdateVisit {

    private static $instance = null;

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
        if (GeneralConfig::getInstance()->option('filter_bots'))
        {
            if (!class_exists('\Jaybizzle\CrawlerDetect'))
                require_once \ContentEgg\PLUGIN_PATH . 'application/vendor/CrawlerDetect.php';

            $CrawlerDetect = new \Jaybizzle\CrawlerDetect\CrawlerDetect();
            // Check the user agent of the current 'visitor'
            if ($CrawlerDetect->isCrawler())
            {
                // true if crawler user agent detected
                return;
            }
        }
        // priority = 10 because ModuleViewer added with a priority of 12 
        // & do_shortcode() is registered as a default filter on 'the_content' with a priority of 11.
        \add_filter('the_content', array($this, 'update'), 10);
    }

    public function update($content)
    {
        if (!is_single() && !is_page())
            return $content;

        $this->updateByKeyword();
        $this->updateItems();
        return $content;
    }

    private function updateByKeyword()
    {
        global $post;

        foreach (ModuleManager::getInstance()->getAffiliateParsers(true) as $module)
        {
            $is_visit_update = in_array($module->config('update_mode'), array('visit', 'visit_cron'));
            $is_data_exists = ContentManager::isDataExists($post->ID, $module->getId());

            // parse data if not exists in any case
            if (!$is_visit_update && $is_data_exists)
                continue;

            $ttl = $module->config('ttl');
            if (!$ttl && $is_data_exists)
                continue;

            $keyword = \get_post_meta($post->ID, ContentManager::META_PREFIX_KEYWORD . $module->getId(), true);
            if (!$keyword)
                continue;

            $last_update = (int) \get_post_meta($post->ID, ContentManager::META_PREFIX_LAST_BYKEYWORD_UPDATE . $module->getId(), true);
            if ($last_update && time() - $last_update < $ttl)
                continue;
            ContentManager::updateByKeyword($post->ID, $module->getId());
        }
    }

    private function updateItems()
    {
        global $post;
        foreach (ModuleManager::getInstance()->getAffiliateParsers(true) as $module)
        {
            if (!in_array($module->config('update_mode'), array('visit', 'visit_cron')))
                continue;

            if (!$module->isItemsUpdateAvailable())
                continue;

            if (!$ttl_items = $module->config('ttl_items'))
                continue;

            $last_items_update = (int) \get_post_meta($post->ID, ContentManager::META_PREFIX_LAST_ITEMS_UPDATE . $module->getId(), true);
            if (!$last_items_update || time() - $last_items_update < $ttl_items)
                continue;
            ContentManager::updateItems($post->ID, $module->getId());
        }
    }

}
