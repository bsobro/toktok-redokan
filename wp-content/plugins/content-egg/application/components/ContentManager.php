<?php

namespace ContentEgg\application\components;

use ContentEgg\application\helpers\ImageHelper;
use ContentEgg\application\helpers\ArrayHelper;
use ContentEgg\application\admin\GeneralConfig;
use ContentEgg\application\models\PriceHistoryModel;
use ContentEgg\application\PriceAlert;
use ContentEgg\application\helpers\CurrencyHelper;

/**
 * ContentManager class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class ContentManager {

    const META_PREFIX_DATA = '_cegg_data_';
    const META_PREFIX_LAST_ITEMS_UPDATE = '_cegg_last_update_';
    const META_PREFIX_KEYWORD = '_cegg_keyword';
    const META_PREFIX_UPDATE_PARAMS = '_cegg_update_params';
    const META_PREFIX_LAST_BYKEYWORD_UPDATE = '_cegg_last_bykeyword_update';

    private static $_view_data = array();

    public static function saveData(array $data, $module_id, $post_id, $is_last_iteration = true)
    {
        if (!$data)
        {
            self::deleteData($module_id, $post_id, $is_last_iteration);
            return;
        }

        $data = self::dataPresavePrepare($data, $module_id, $post_id);
        $old_data = ContentManager::getData($post_id, $module_id);

        $outdated = array();
        $data_changed = true;
        if ($old_data)
        {
            $outdated = array_diff_key($old_data, $data);
            $new = array_diff_key($data, $old_data);

            if (!$outdated && !$new)
                $data_changed = false;
            /*
             * we need force data update because title or description can be edited manually or items price update
             */
        }

        // save data
        \update_post_meta($post_id, self::META_PREFIX_DATA . $module_id, $data);
        self::clearData($outdated);

        // touch last update time only if data changed?
        if ($data_changed)
        {
            self::touchUpdateTime($post_id, $module_id);
        }

        // save price history
        if (GeneralConfig::getInstance()->option('price_history_days'))
        {
            PriceHistoryModel::model()->saveData($data, $module_id, $post_id);
            // ...and send price alerts
            if (GeneralConfig::getInstance()->option('price_alert_enabled'))
                PriceAlert::getInstance()->sendAlerts($data, $module_id, $post_id);
        }

        self::resetViewDataCache($module_id, $post_id);

        \do_action('content_egg_save_data', $data, $module_id, $post_id, $is_last_iteration);
    }

    public static function dataPresavePrepare(array $data, $module_id, $post_id)
    {
        foreach ($data as $i => $d)
        {
            if (is_object($d))
                $data[$i] = ArrayHelper::object2Array($d);
        }
        $data = self::setIds($data);
        // Sanitize content for allowed HTML tags and more.
        array_walk_recursive($data, array('self', 'sanitizeData'));
        $module = ModuleManager::getInstance()->factory($module_id);
        $data = $module->presavePrepare($data, $post_id);
        return $data;
    }

    public static function deleteData($module_id, $post_id, $is_last_iteration = true)
    {
        $data = ContentManager::getData($post_id, $module_id);
        if (!$data)
        {
            // last chance to fire last_iteration hook
            \do_action('content_egg_save_data', array(), $module_id, $post_id, $is_last_iteration);
            return;
        }

        \delete_post_meta($post_id, self::META_PREFIX_DATA . $module_id);
        \delete_post_meta($post_id, self::META_PREFIX_LAST_BYKEYWORD_UPDATE . $module_id);
        \delete_post_meta($post_id, self::META_PREFIX_LAST_ITEMS_UPDATE . $module_id);

        self::clearData($data);
        self::resetViewDataCache($module_id, $post_id);

        \do_action('content_egg_save_data', array(), $module_id, $post_id, $is_last_iteration);
    }

    private static function clearData($data)
    {
        // delete old img files if needed
        foreach ($data as $d)
        {
            if (empty($d['img_file']))
                continue;
            $img_file = ImageHelper::getFullImgPath($d['img_file']);
            
            if (is_file($img_file))
                @unlink($img_file);
        }
    }

    private static function setIds($data)
    {
        $results = array();
        foreach ($data as $d)
        {
            $results[$d['unique_id']] = $d;
        }
        return $results;
    }

    public static function touchUpdateTime($post_id, $module_id)
    {
        $time = time();
        \update_post_meta($post_id, self::META_PREFIX_LAST_BYKEYWORD_UPDATE . $module_id, $time);
        self::touchUpdateItemsTime($post_id, $module_id, $time);
    }

    public static function touchUpdateItemsTime($post_id, $module_id, $time = null)
    {
        if (!$time)
            $time = time();
        \update_post_meta($post_id, self::META_PREFIX_LAST_ITEMS_UPDATE . $module_id, $time);
    }

    private static function sanitizeData(&$data, $key)
    {
        if (in_array((string) $key, array('img', 'url', 'IFrameURL', 'orig_url')))
        {
            //$data = \esc_url_raw($data);
            //@todo... This filter allows all letters, digits and $-_.+!*'(),{}|\\^~[]`"><#%;/?:@&=
            $data = filter_var($data, FILTER_SANITIZE_URL);
        } elseif ($key === 'description')
        {
            $data = \wp_kses_post($data);
        } elseif ($key === 'linkHtml')
        {
            $data; //cj link
        } elseif ($key === 'title')
        {
            $data = \sanitize_text_field($data);
        } elseif ($key === 'last_update' && !$data)
        {
            $data = time();
        } else
            $data = \strip_tags($data);
    }

    public static function isDataExists($post_id, $module_id)
    {
        return (bool) \get_post_meta($post_id, self::META_PREFIX_LAST_BYKEYWORD_UPDATE . $module_id, true);
    }

    public static function getData($post_id, $module_id)
    {
        $data = self::fixData(\get_post_meta($post_id, ContentManager::META_PREFIX_DATA . $module_id, true), $module_id);
        if (!$data)
            $data = array();
        return $data;
    }

    public static function fixData($data, $module_id)
    {
        if (!$data || !is_array($data))
            return $data;

        if ($module_id == 'Amazon')
        {
            $data = \ContentEgg\application\modules\Amazon\AmazonModule::fixUniqueIds($data);
        }
        return $data;
    }

    public static function getViewData($module_id, $post_id, $params = array())
    {
        $data_id = $post_id . '-' . $module_id;
        if (!isset(self::$_view_data[$data_id]))
        {
            $data = self::getData($post_id, $module_id);
            $data = self::dataPreviewPrepare($data, $module_id, $post_id, $params);
            self::$_view_data[$data_id] = $data;
        }

        $data = self::$_view_data[$data_id];

        // locale fix...
        if (!empty($params['locale']))
        {
            foreach ($data as $key => $d)
            {
                if (isset($d['extra']['locale']) && strtolower($d['extra']['locale']) != strtolower($params['locale']))
                    unset($data[$key]);
            }
        }

        // convert all prices to one currency
        if (!empty($params['currency']))
        {
            foreach ($data as $key => $d)
            {
                $rate = CurrencyHelper::getCurrencyRate($d['currencyCode'], $params['currency']);
                if (!$rate)
                    continue;

                if (!empty($d['price']))
                {
                    $data[$key]['price'] = $d['price'] * $rate;
                    $data[$key]['currencyCode'] = $params['currency'];
                }
                if (!empty($d['priceOld']))
                {
                    $data[$key]['priceOld'] = $d['priceOld'] * $rate;
                }
            }
        }
        return $data;
    }

    public static function resetViewDataCache($module_id = null, $post_id = null)
    {
        if ($module_id && $post_id)
        {
            $data_id = $post_id . '-' . $module_id;
            if (isset(self::$_view_data[$data_id]))
                unset(self::$_view_data[$data_id]);
        } else
            self::$_view_data = array();
    }

    public static function dataPreviewPrepare(array $data, $module_id, $post_id, $params = array())
    {
        $is_ssl = \is_ssl();
        $http_home_url = str_replace('https://', 'http://', \home_url('/'));
        //$base_currency = GeneralConfig::getInstance()->option('base_currency');

        foreach ($data as $key => $d)
        {
            if (empty($data[$key]['extra']) || !is_array($data[$key]['extra']))
                $data[$key]['extra'] = array();

            // domain fix && logo
            if (empty($d['extra']['domain']) && isset($d['domain']))
                $data[$key]['extra']['domain'] = $d['domain'];
            elseif (empty($d['domain']) && isset($d['extra']['domain']))
                $data[$key]['domain'] = $d['extra']['domain'];
            if (empty($d['extra']['logo']) && isset($d['logo']))
                $data[$key]['extra']['logo'] = $d['logo'];
            elseif (empty($d['logo']) && isset($d['extra']['logo']))
                $data[$key]['logo'] = $d['extra']['logo'];

            // https fix for local images
            if ($is_ssl && strstr($d['img'], $http_home_url))
                $data[$key]['img'] = str_replace('http://', '//', $d['img']);

            $data[$key]['post_id'] = $post_id;
            $data[$key]['module_id'] = $module_id;
        }
        // local redirect & other
        $module = ModuleManager::getInstance()->factory($module_id);
        if ($module->isParser())
            $data = $module->viewDataPrepare($data);

        return $data;
    }

    public static function getProductbyUniqueId($unique_id, $module_id, $post_id, $params = array())
    {
        $data = self::getViewData($module_id, $post_id, $params);
        if ($data && isset($data[$unique_id]))
            return $data[$unique_id];
        else
            return null;
    }

    public static function updateByKeyword($post_id, $module_id)
    {
        $keyword = \get_post_meta($post_id, ContentManager::META_PREFIX_KEYWORD . $module_id, true);
        if (!$keyword)
            return;

        $updateParams = \get_post_meta($post_id, ContentManager::META_PREFIX_UPDATE_PARAMS . $module_id, true);

        $module = ModuleManager::getInstance()->factory($module_id);

        // update time in any case...
        ContentManager::touchUpdateTime($post_id, $module_id);
        try
        {
            $data = $module->doRequest($keyword, $updateParams, true);
            // nodata!
            if (!$data)
            {
                return;
            }
        } catch (\Exception $e)
        {
            // error
            return;
        }

        $data = array_map(array('self', 'object2Array'), $data);
        ContentManager::saveData($data, $module_id, $post_id);
    }

    public static function updateItems($post_id, $module_id)
    {
        $module = ModuleManager::getInstance()->factory($module_id);
        if (!$module->isItemsUpdateAvailable())
            return;

        $items = ContentManager::getData($post_id, $module_id);

        if (!$items)
            return;

        try
        {
            $updated_data = $module->doRequestItems($items);
        } catch (\Exception $e)
        {
            // error
            ContentManager::touchUpdateItemsTime($post_id, $module_id);
            return;
        }

        $time = time();
        foreach ($updated_data as $key => $data)
        {
            $updated_data[$key]['last_update'] = $time;
        }

        // save & update time
        ContentManager::saveData($updated_data, $module_id, $post_id);
        ContentManager::touchUpdateItemsTime($post_id, $module_id);
    }

    /**
     *  Full depth recursive conversion to array
     * @param type $object
     * @return array
     */
    public static function object2Array($object)
    {
        return json_decode(json_encode($object), true);
    }

    public static function getNormalizedReviews($data)
    {
        $struct = array(
            'summary' => '',
            'comment' => '',
            'rating' => '',
            'name' => '',
            'date' => '',
            'pros' => '',
            'cons' => '',
            'review' => '',
            'parent_id' => '',
        );

        $reviews = array();
        foreach ($data as $item)
        {
            if (is_object($item))
                $item = ContentManager::object2Array($item);

            // AE modules & walmart
            if (!empty($item['extra']['comments']))
            {
                foreach ($item['extra']['comments'] as $r)
                {
                    $review = $struct;
                    $review['comment'] = $r['comment'];
                    if (!empty($r['name']))
                        $review['name'] = $r['name'];
                    if (!empty($r['date']))
                        $review['date'] = $r['date'];
                    if (!empty($r['review']))
                        $review['review'] = $r['review'];
                    if (!empty($r['rating']))
                        $review['rating'] = $r['rating'];
                    if (!empty($r['pros']))
                        $review['pros'] = $r['pros'];
                    if (!empty($r['cons']))
                        $review['cons'] = $r['cons'];
                    if (isset($r['parent_id']))
                        $review['parent_id'] = (int) $r['parent_id'];

                    $reviews[] = $review;
                }
            }
            // Ozon
            elseif (!empty($item['extra']['Reviews']))
            {
                foreach ($item['extra']['Reviews'] as $r)
                {
                    $review = $struct;
                    $review['summary'] = $r->Title;
                    $review['date'] = $r->Date;
                    $review['rating'] = $r->Rate;
                    $review['comment'] = $r->Comment;
                    $review['name'] = $r->FIO;
                    $reviews[] = $review;
                }
            }
        }

        foreach ($reviews as $i => $review)
        {
            if (!$review['comment'])
            {
                if ($review['review'])
                    $review['comment'] = $review['review'];
                if ($review['pros'])
                    $review['comment'] .= "\r\n" . __('Pros:', 'content-egg-tpl') . $review['pros'];
                if ($review['cons'])
                    $review['comment'] .= "\r\n" . __('Cons:', 'content-egg-tpl') . $review['cons'];
                $review['comment'] = trim($review['comment']);
                $reviews[$i] = $review;
            }
        }
        return $reviews;
    }

    public static function removeReviews($data)
    {
        foreach ($data as $i => $item)
        {
            if (!empty($item['extra']['comments']))
                $data[$i]['extra']['comments'] = array();
            elseif (!empty($item['extra']['Reviews']))
                $data[$i]['extra']['Reviews'] = array();
        }
        return $data;
    }

    public static function saveReviewsAsComments($post_id, array $normalized_comments)
    {
        $comment_data = array(
            'comment_post_ID' => $post_id,
            'comment_author_email' => '',
            'comment_author_url' => '',
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => 0,
            'comment_approved' => 1,
        );

        $is_rehub_theme = (basename(\get_template_directory()) == 'rehub') ? true : false;
        $rehub_post_type = \get_post_meta($post_id, 'rehub_framework_post_type', true);

        if ($rehub_post_type && $rehub_post_type == 'review')
            $is_review_post_type = true;
        else
            $is_review_post_type = false;

        if (\get_post_type($post_id) == 'product')
            $is_woo_product = true;
        else
            $is_woo_product = false;

        $comments_keys_map = array();

        foreach ($normalized_comments as $i => $comment)
        {
            $comment_pros = '';
            $comment_cons = '';
            $comment_rating = 0;

            // rehub comment meta
            if ($is_rehub_theme && $is_review_post_type && !empty($comment['review']))
                $comment_content = $comment['review'];
            else
                $comment_content = $comment['comment'];

            $comment_data['comment_content'] = \wp_kses($comment_content, 'default');
            if (!empty($comment['name']))
                $comment_data['comment_author'] = $comment['name'];

            if (!empty($comment['date']))
                $comment_data['comment_date'] = date('Y-m-d H:i:s', $comment['date']);

            if (isset($comment['parent_id']) && is_numeric($comment['parent_id']) && isset($comments_keys_map[$comment['parent_id']]))
                $comment_data['comment_parent'] = $comments_keys_map[$comment['parent_id']];
            else
                $comment_data['comment_parent'] = 0;

            $comment_id = \wp_insert_comment($comment_data);

            //$comment_id = \wp_new_comment($comment_data);
            $comments_keys_map[$i] = $comment_id;

            if ($is_rehub_theme && $is_review_post_type)
            {
                if (!empty($comment['pros']))
                    \add_comment_meta($comment_id, 'pros_review', $comment['pros']);
                if (!empty($comment['cons']))
                    \add_comment_meta($comment_id, 'cons_review', $comment['cons']);
                if (!empty($comment['rating']))
                {
                    $rating_value = $comment['rating'] * 2;
                    \add_comment_meta($comment_id, 'user_average', $rating_value);
                    \add_comment_meta($comment_id, 'user_criteria', array(array('name' => __('Rating', 'content-egg-tpl'), 'value' => $rating_value)));
                }
                \add_comment_meta($comment_id, 'counted', 0);
                // calculate rating
                if (function_exists('add_comment_rates'))
                    \add_comment_rates($comment_id);
            }

            if ($is_woo_product && !empty($comment['rating']) && $comment['rating'] > 0 && $comment['rating'] <= 5)
            {
                \add_comment_meta($comment_id, 'rating', $comment['rating'], true);
            }
        }

        if ($is_woo_product && class_exists('\WC_Comments'))
        {
            // Ensure product average rating and review count is kept up to date.
            \WC_Comments::clear_transients($post_id);
        }
    }

    public static function getMainProduct($modules_data, $main_product_selector = 'min_price')
    {
        $all_items = array();
        foreach ($modules_data as $module_id => $items)
        {
            foreach ($items as $item)
            {
                $item = ArrayHelper::object2Array($item);
                $item['module_id'] = $module_id;
                /*
                  if (empty($item['price']))
                  continue;
                 * 
                 */
                $all_items[] = $item;
            }
        }

        if (!$all_items)
            return null;

        if ($main_product_selector == 'random')
            return $all_items[array_rand($all_items)];

        if ($main_product_selector == 'max_price')
            return $all_items[ArrayHelper::getMaxKeyAssoc($all_items, 'price', true)];
        else
            return $all_items[ArrayHelper::getMinKeyAssoc($all_items, 'price', true)];
    }

}
