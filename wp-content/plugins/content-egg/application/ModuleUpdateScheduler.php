<?php

namespace ContentEgg\application;

use ContentEgg\application\components\Scheduler;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\components\ModuleManager;

/**
 * ModuleUpdateScheduler class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class ModuleUpdateScheduler extends Scheduler {

    const CRON_TAG = 'cegg_module_updater_cron';
    const BYKEYWORD_UPDATE_LIMIT_FOR_MODULE = 50;
    const ITEMS_UPDATE_LIMIT_FOR_MODULE = 50;

    public static function getCronTag()
    {
        return self::CRON_TAG;
    }

    public static function run()
    {
        @set_time_limit(2000);

        // 1. By keyword update
        self::byKeywordUpdate();
        // 2. Price update
        self::priceUpdate();
    }

    private static function byKeywordUpdate()
    {
        global $wpdb;

        $module_ids = ModuleManager::getInstance()->getByKeywordUpdateModuleIds();
        if (!$module_ids)
            return;

        $time = time();

        foreach ($module_ids as $module_id)
        {
            $module = ModuleManager::getInstance()->factory($module_id);
            $ttl = $module->config('ttl');

            $meta_key_keyword = self::addKeywordPrefix($module_id);
            $meta_key_last_bykeyword_update = self::addByKeywordUpdatePrefix($module_id);

            $sql = "SELECT last_bykeyword_update.post_id
            FROM    {$wpdb->postmeta} last_bykeyword_update
            INNER JOIN  {$wpdb->postmeta} keyword 
            ON last_bykeyword_update.post_id = keyword.post_id
                AND keyword.meta_key = %s
            WHERE   
                {$time} - last_bykeyword_update.meta_value  > {$ttl}
                AND last_bykeyword_update.meta_key = %s
            ORDER BY    last_bykeyword_update.meta_value ASC
            LIMIT " . self::BYKEYWORD_UPDATE_LIMIT_FOR_MODULE;

            $query = $wpdb->prepare($sql, $meta_key_keyword, $meta_key_last_bykeyword_update);
            $results = $wpdb->get_results($query);
            if (!$results)
                continue;

            //\ContentEgg\prn($results);
            // update!
            foreach ($results as $r)
            {
                ContentManager::updateByKeyword($r->post_id, $module_id);
            }
        }
    }

    private static function priceUpdate()
    {
        global $wpdb;

        $module_ids = ModuleManager::getInstance()->getItemsUpdateModuleIds();
        if (!$module_ids)
            return;

        $time = time();
        foreach ($module_ids as $module_id)
        {
            $module = ModuleManager::getInstance()->factory($module_id);
            $ttl_items = $module->config('ttl_items');
            $meta_key_last_update = self::addLastItemsUpdatePrefix($module_id);

            $sql = "SELECT last_update.post_id
            FROM    {$wpdb->postmeta} last_update
            WHERE   
                {$time} - last_update.meta_value  > {$ttl_items}
                AND last_update.meta_key = %s
            ORDER BY    last_update.meta_value ASC
            LIMIT " . self::ITEMS_UPDATE_LIMIT_FOR_MODULE;

            $query = $wpdb->prepare($sql, $meta_key_last_update);
            $results = $wpdb->get_results($query);
            if (!$results)
                continue;

            // update!
            foreach ($results as $r)
            {
                ContentManager::updateItems($r->post_id, $module_id);
            }
        }
    }

    public static function addByKeywordUpdatePrefix($module_id)
    {
        return ContentManager::META_PREFIX_LAST_BYKEYWORD_UPDATE . $module_id;
    }

    public static function addKeywordPrefix($module_id)
    {
        return ContentManager::META_PREFIX_KEYWORD . $module_id;
    }

    public static function addLastItemsUpdatePrefix($module_id)
    {
        return ContentManager::META_PREFIX_LAST_ITEMS_UPDATE . $module_id;
    }

}
