<?php

namespace ContentEgg\application\helpers;

use ContentEgg\application\components\ContentManager;
use ContentEgg\application\models\PriceHistoryModel;
use ContentEgg\application\helpers\ArrayHelper;
use ContentEgg\application\admin\GeneralConfig;
use ContentEgg\application\components\ModuleManager;

/**
 * TemplateHelper class file
 * 
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 * 
 */
class TemplateHelper {

    const MERHANT_LOGO_DIR = 'ce-logos';

    public static function formatPriceCurrency($price, $currencyCode, $before_symbol = '', $after_symbol = '')
    {
        $decimal_sep = __('number_format_decimal_point', 'content-egg-tpl');
        $thousand_sep = __('number_format_thousands_sep', 'content-egg-tpl');
        if ($decimal_sep == 'number_format_decimal_point')
            $decimal_sep = null;
        if ($thousand_sep == 'number_format_thousands_sep')
            $thousand_sep = null;

        return CurrencyHelper::getInstance()->currencyFormat($price, $currencyCode, $thousand_sep, $decimal_sep, $before_symbol, $after_symbol);
    }

    public static function currencyTyping($c)
    {
        return CurrencyHelper::getInstance()->getSymbol($c);
    }

    /*
     * @deprecated
     */

    public static function number_format_i18n($number, $decimals = 0, $currency = null)
    {
        $decimal_sep = __('number_format_decimal_point', 'content-egg-tpl');
        $thousand_sep = __('number_format_thousands_sep', 'content-egg-tpl');
        if ($decimal_sep == 'number_format_decimal_point')
            $decimal_sep = null;
        if ($thousand_sep == 'number_format_thousands_sep')
            $thousand_sep = null;
        return CurrencyHelper::getInstance()->numberFormat($number, $currency, $thousand_sep, $decimal_sep, $decimals);
    }

    /*
     * @deprecated
     */

    public static function price_format_i18n($number, $currency = null)
    {
        return self::number_format_i18n($number, $decimal = null, $currency);
    }

    public static function truncate($string, $length = 80, $etc = '...', $charset = 'UTF-8', $break_words = false, $middle = false)
    {
        if ($length == 0)
            return '';

        if (mb_strlen($string, 'UTF-8') > $length)
        {
            $length -= min($length, mb_strlen($etc, 'UTF-8'));
            if (!$break_words && !$middle)
            {
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, $charset));
            }
            if (!$middle)
            {
                return mb_substr($string, 0, $length, $charset) . $etc;
            } else
            {
                return mb_substr($string, 0, $length / 2, $charset) . $etc . mb_substr($string, -$length / 2, $charset);
            }
        } else
        {
            return $string;
        }
    }

    /**
     * Возвращает количтсво дней, секунд и минут с текущего момента
     * до окончания события
     * @param string $end_time_gmt GNU формат даты
     * @param bool $return_array вернуть в виде массива или форматированной строки?
     * @return mixed false - если $timeleft < 0, массив или строку
     */
    static public function getTimeLeft($end_time_gmt, $return_array = false)
    {

        $current_time = strtotime(gmdate("M d Y H:i:s"));
        $timeleft = strtotime($end_time_gmt) - $current_time;
        if ($timeleft < 0)
            return '';

        $days_left = floor($timeleft / 86400);
        $hours_left = floor(($timeleft - $days_left * 86400) / 3600);
        $min_left = floor(($timeleft - $days_left * 86400 - $hours_left * 3600) / 60);
        // Если нужно вернуть в виде массива
        if ($return_array)
        {
            return array(
                'days' => $days_left,
                'hours' => $hours_left,
                'min' => $min_left,
            );
        }

        if ($days_left)
            return $days_left . __('d', 'content-egg-tpl') . ' ';
        elseif ($hours_left)
            return $hours_left . __('h', 'content-egg-tpl') . ' ';
        elseif ($min_left)
            return $min_left . __('m', 'content-egg-tpl');
        else
            return '<1' . __('m', 'content-egg-tpl');
    }

    public static function filterData($data, $field_name, $field_values, $extra = false, $inverse = false)
    {
        $results = array();
        foreach ($data as $key => $d)
        {
            if ($extra)
            {
                if (!isset($d['extra']) || !isset($d['extra'][$field_name]))
                    continue;
                $value = $d['extra'][$field_name];
            } else
            {
                if (!isset($d[$field_name]))
                    continue;
                $value = $d[$field_name];
            }
            if (!is_array($field_values))
                $field_values = array($field_values);

            if (!$inverse && in_array($value, $field_values))
                $results[$key] = $d;
            elseif ($inverse && !in_array($value, $field_values))
                $results[$key] = $d;
        }
        return $results;
    }

    public static function formatDatetime($datetime, $type = 'mysql', $separator = ' ')
    {
        if ('mysql' == $type)
        {
            return mysql2date(get_option('date_format'), $datetime) . $separator . mysql2date(get_option('time_format'), $datetime);
        } else
        {
            return date_i18n(get_option('date_format'), $datetime) . $separator . date_i18n(get_option('time_format'), $datetime);
        }
    }

    public static function formatDate($timestamp, $gmt = false)
    {
        return date_i18n(get_option('date_format'), $timestamp, $gmt);
    }

    public static function splitAttributeName($attribute)
    {
        return trim(preg_replace('/([A-Z])([a-z])/', ' $1$2', $attribute));
    }

    public static function getAmazonLink(array $itemLinks, $description)
    {
        foreach ($itemLinks as $link)
        {
            if ($link['Description'] == $description)
                return $link['URL'];
        }
        return false;
    }

    public static function getLastUpdate($module_id, $post_id = null)
    {
        if (!$post_id)
        {
            global $post;
            $post_id = $post->ID;
        }
        $res = \get_post_meta($post_id, ContentManager::META_PREFIX_LAST_ITEMS_UPDATE . $module_id, true);
        if (!$res)
            $res = time();
        return $res;
    }

    public static function dateFormatFromGmt($timestamp, $time = true)
    {
        $format = \get_option('date_format');
        if ($time)
            $format .= ' ' . \get_option('time_format');

        // last update date stored in gmt, convert into local time        
        $timestamp = strtotime(\get_date_from_gmt(date('Y-m-d H:i:s', $timestamp)));
        return \date_i18n($format, $timestamp);
    }

    public static function getLastUpdateFormattedAmazon(array $data, $time = true)
    {
        if (!isset($data['Amazon']))
            return false;

        $item = current($data['Amazon']);
        if (empty($item['last_update']))
            return false;

        $last_update = $item['last_update'];

        return self::dateFormatFromGmt($last_update, $time);
    }

    public static function getLastUpdateFormatted($module_id, $post_id = null, $time = true)
    {
        if (!$post_id || $post_id === true) // $post_id === true - fix func params...
        {
            global $post;
            $post_id = $post->ID;
        }

        $last_update = self::getLastUpdate($module_id, $post_id);
        return self::dateFormatFromGmt($last_update, $time);

        /*

          $format = \get_option('date_format');
          if ($time)
          $format .= ' ' . \get_option('time_format');

          $last_update = self::getLastUpdate($module_id, $post_id);
          // last update date stored in gmt, convert into local time
          $last_update = strtotime(\get_date_from_gmt(date('Y-m-d H:i:s', $last_update)));

          return \date_i18n($format, $last_update);
         * 
         */
    }

    public static function filterDataByType($data, $type)
    {
        $results = array();
        foreach ($data as $module_id => $items)
        {
            $module = \ContentEgg\application\components\ModuleManager::getInstance()->factory($module_id);
            if ($module->getParserType() == $type)
                $results[$module_id] = $items;
        }
        return $results;
    }

    public static function filterDataByModule($data, $module_ids)
    {
        if (!is_array($module_ids))
            $module_ids = array($module_ids);
        $results = array();

        foreach ($data as $module_id => $items)
        {
            if (in_array($module_id, $module_ids))
                $results[$module_id] = $items;
        }
        return $results;
    }

    public static function priceHistoryPrices($unique_id, $plugin_id, $limit = 5)
    {
        $prices = PriceHistoryModel::model()->getLastPrices($unique_id, $plugin_id, $limit);
        $results = array();
        foreach ($prices as $price)
        {
            $results[] = array(
                'date' => strtotime($price['create_date']),
                'price' => $price['price'],
            );
        }
        return $results;
    }

    public static function priceHistoryMax($unique_id, $module_id)
    {
        if (!$price = PriceHistoryModel::model()->getMaxPrice($unique_id, $module_id))
            return null;
        return array('price' => $price['price'], 'date' => strtotime($price['create_date']));
    }

    public static function priceHistoryMin($unique_id, $module_id)
    {
        if (!$price = PriceHistoryModel::model()->getMinPrice($unique_id, $module_id))
            return null;
        return array('price' => $price['price'], 'date' => strtotime($price['create_date']));
    }

    public static function priceHistorySinceDate($unique_id, $module_id)
    {
        if (!$date = PriceHistoryModel::model()->getFirstDateValue($unique_id, $module_id))
            return null;
        return strtotime($date);
    }

    public static function priceChangesProducts($limit = 5)
    {
        $params = array(
            //'select' => 'DISTINCT unique_id',
            'order' => 'create_date DESC',
            'where' => 'post_id IS NOT NULL',
            'group' => 'unique_id',
            'limit' => $limit,
        );
        $prices = PriceHistoryModel::model()->findAll($params);
        $products = array();
        // find products
        foreach ($prices as $price)
        {
            if ($prod = ContentManager::getProductbyUniqueId($price['unique_id'], $price['module_id'], $price['post_id']))
                $products[] = $prod;
        }
        return $products;
    }

    public static function priceHistoryMorrisChart($unique_id, $module_id, $days = 180, array $options = array(), $htmlOptions = array())
    {
        $where = PriceHistoryModel::model()->prepareWhere(
                (array('unique_id = %s AND module_id = %s', array($unique_id, $module_id))), false);
        $params = array(
            'select' => 'date(create_date) as date, price as price',
            'where' => $where . ' AND TIMESTAMPDIFF( DAY, create_date, "' . \current_time('mysql') . '") <= ' . $days,
            //'group' => 'date',
            'order' => 'date ASC'
        );
        $results = PriceHistoryModel::model()->findAll($params);
        $results = array_reverse($results);
        $prices = array();
        /**
         * php fix for selecting non-aggregate columns
         * @see: https://stackoverflow.com/questions/1066453/mysql-group-by-and-order-by
         */
        foreach ($results as $key => $r)
        {
            if ($key > 0 && $results[$key - 1]['date'] == $r['date'])
                continue;
            $price = array(
                'date' => $r['date'],
                'price' => $r['price'],
            );
            $prices[] = $price;
        }

        //add last known price to the chart     
        /*
          $price = array(
          'date' => $r['date'],
          'price' => $r['price'],
          );
          $prices[] = $price;
         * 
         */
        $data = array(
            'chartType' => 'Area',
            'data' => $prices,
            'xkey' => 'date',
            'ykeys' => array('price'),
            'labels' => array(__('Price', 'content-egg-tpl')),
        );
        $options = array_merge($data, $options);

        $id = $module_id . '-' . $unique_id . '-chart' . rand(0, 10000);
        self::viewMorrisChart($id, $options, $htmlOptions);
    }

    public static function viewMorrisChart($id, array $options, $htmlOptions = array('style' => 'height: 250px;'))
    {
        // morris.js
        \wp_enqueue_style('morrisjs');
        \wp_enqueue_script('morrisjs');

        if (!empty($options['chartType']) && in_array($options['chartType'], array('Line', 'Area', 'Donut', 'Bar')))
        {
            $chartType = $options['chartType'];
            unset($options['chartType']);
        } else
            $chartType = 'Line';
        $options['element'] = $id;

        $html_attr = '';
        foreach ($htmlOptions as $name => $value)
        {
            $html_attr .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
        }

        echo '<div style="direction: ltr;" id="' . esc_attr($id) . '"' . $html_attr . '></div>
        <script>
        jQuery(document).ready(function($) {
            new Morris.' . $chartType . '(' . json_encode($options) . ');
                });
        </script>';
    }

    public static function isPriceAlertAllowed($unique_id = null, $module_id = null)
    {
        return \ContentEgg\application\PriceAlert::isPriceAlertAllowed($unique_id, $module_id);
    }

    public static function getCurrencyPos($currency)
    {
        return CurrencyHelper::getInstance()->getCurrencyPos($currency);
    }

    public static function getCurrencySymbol($currency)
    {
        return CurrencyHelper::getInstance()->getSymbol($currency);
    }

    public static function getCurrencyName($currency)
    {
        return CurrencyHelper::getInstance()->getName($currency);
    }

    private static function getMerchantImageUrl(array $item, $prefix = '', $remote_url = null, $blank_on_error = false)
    {
        $default_ext = 'png'; // ???

        if (!empty($item['domain']))
            $logo_file_name = $item['domain'];
        elseif (!empty($item['logo']))
            $logo_file_name = md5($item['logo']);
        else
            return $blank_on_error ? self::getBlankImg() : false;

        $logo_file_name = str_replace('.', '-', $logo_file_name);
        $logo_file_name .= '.' . $default_ext;
        $logo_file_name = $prefix . $logo_file_name;

        // check in distrib
        if (file_exists(\ContentEgg\PLUGIN_PATH . 'res/logos/' . $logo_file_name))
            return \ContentEgg\PLUGIN_RES . '/logos/' . $logo_file_name;

        $uploads = \wp_upload_dir();
        if (!$logo_dir = self::getMerchantLogoDir())
            return $blank_on_error ? self::getBlankImg() : false;
        $logo_file = \trailingslashit($logo_dir) . $logo_file_name;
        $logo_url = $uploads['baseurl'] . '/' . self::MERHANT_LOGO_DIR . '/' . $logo_file_name;

        // logo exists
        if (file_exists($logo_file))
            return $logo_url;

        // download
        if (!$remote_url)
            return $blank_on_error ? self::getBlankImg() : false;
        if ($logo_file_name = ImageHelper::downloadImg($remote_url, $logo_dir, $logo_file_name, '', true))
            return $uploads['baseurl'] . '/' . self::MERHANT_LOGO_DIR . '/' . $logo_file_name;
        else
        {
            // save blank to prevent new requests           
            copy(\ContentEgg\PLUGIN_PATH . 'res/img/blank.gif', $logo_file);
            return $blank_on_error ? self::getBlankImg() : false;
        }
    }

    public static function getMerhantLogoUrl(array $item, $blank_on_error = false)
    {
        $prefix = '';
        if (!empty($item['logo']))
            $remote_url = $item['logo'];
        elseif (!empty($item['domain']))
            $remote_url = 'https://logo.clearbit.com/' . urlencode($item['domain']) . '?size=128';
        else
            $remote_url = '';
        return self::getMerchantImageUrl($item, $prefix, $remote_url, $blank_on_error);
    }

    public static function getMerhantIconUrl(array $item, $blank_on_error = false)
    {
        $prefix = 'icon_';

        if (!empty($item['module_id']) && $item['module_id'] == 'Amazon')
        {
            $show_small_logos = ModuleManager::getInstance()->parserFactory($item['module_id'])->config('show_small_logos');
            if (!$show_small_logos)
                return $blank_on_error ? self::getBlankImg() : false;
        }
        /*
          if (empty($item['domain']) || preg_match('/^amazon\./', $item['domain']))
          return $blank_on_error ? self::getBlankImg() : false;
         * 
         */
        $remote_url = 'https://www.google.com/s2/favicons?domain=' . urlencode($item['domain']);
        return self::getMerchantImageUrl($item, $prefix, $remote_url, $blank_on_error);
    }

    public static function getMerhantName(array $item, $print = false)
    {
        if (!empty($item['domain']))
            $name = ucwords($item['domain']);
        elseif (!empty($item['merchant']))
            $name = $item['merchant'];
        else
            $name = '';

        if ($print)
            echo \esc_html($name);
        else
            return $name;
    }

    public static function getMerchantLogoDir()
    {
        $uploads = \wp_upload_dir();
        $logo_dir = \trailingslashit($uploads['basedir']) . self::MERHANT_LOGO_DIR;
        if (is_dir($logo_dir))
            return $logo_dir;

        // create
        if (\wp_mkdir_p($logo_dir))
            return $logo_dir;
        else
            return false;
    }

    public static function getBlankImg()
    {
        return \ContentEgg\PLUGIN_RES . '/img/blank.gif';
    }

    public static function mergeData(array $data)
    {
        foreach ($data as $module_id => $items)
        {
            foreach ($items as $item_ar)
            {
                $item_ar['module_id'] = $module_id;
                $all_items[] = $item_ar;
            }
        }
        return $all_items;
    }

    public static function getMaxPriceItem(array $data)
    {
        return $data[ArrayHelper::getMaxKeyAssoc($data, 'price', true)];
    }

    public static function getMinPriceItem(array $data)
    {
        return $data[ArrayHelper::getMinKeyAssoc($data, 'price', true)];
    }

    public static function getCommonCurrencyCode($data)
    {
        $first = reset($data);
        $currency = $first['currencyCode'];
        foreach ($data as $d)
        {
            if (!empty($d['currencyCode']) && $d['currencyCode'] != $currency)
                return false;
        }
        return $currency;
    }

    public static function getShopsList($data)
    {
        $list = array();
        foreach ($data as $d)
        {
            if (!isset($list[$d['domain']]))
            {
                if (!empty($d['merchant']))
                    $list[$d['domain']] = $d['merchant'];
                else
                    $list[$d['domain']] = self::getNameFromDomain($d['domain']);
            }
        }
        return $list;
    }

    public static function getNameFromDomain($domain)
    {
        $parts = explode('.', $domain);
        return ucfirst($parts[0]);
    }

    public static function sortByPrice(array $data, $order = 'asc')
    {
        if (!$order)
            $order = 'asc';
        if (!in_array($order, array('asc', 'desc')))
            $order = 'asc';
        // convert all prices to one currency
        $currency_codes = array();
        foreach ($data as $d)
        {
            if (empty($d['currencyCode']))
                continue;

            if (!isset($currency_codes[$d['currencyCode']]))
                $currency_codes[$d['currencyCode']] = 1;
            else
                $currency_codes[$d['currencyCode']] ++;
        }
        arsort($currency_codes);
        $base_currency = key($currency_codes);
        foreach ($data as $key => $d)
        {
            if (!empty($d['currencyCode']) && $d['currencyCode'] != $base_currency)
            {
                $rate = CurrencyHelper::getCurrencyRate($d['currencyCode'], $base_currency);
                if (!$rate)
                    $rate = 1;
                $data[$key]['converted_price'] = $d['price'] * $rate;
            } else
                $data[$key]['converted_price'] = $d['price'];
        }

        if ($order == 'asc')
        {
            usort($data, function($a, $b) {
                if (!$a['converted_price'])
                    return 1;
                if (!$b['converted_price'])
                    return -1;
                return ($a['converted_price'] < $b['converted_price']) ? -1 : 1;
            });
        } else
        {
            usort($data, function($a, $b) {
                if (!$a['converted_price'])
                    return 1;
                if (!$b['converted_price'])
                    return -1;
                return ($a['converted_price'] < $b['converted_price']) ? 1 : -1;
            });
        }
        return $data;
    }

    public static function sortAllByPrice(array $data, $order = 'asc')
    {
        $all_items = array();
        foreach ($data as $module_id => $items)
        {
            foreach ($items as $item_ar)
            {
                $item_ar['module_id'] = $module_id;
                $all_items[] = $item_ar;
            }
        }
        return TemplateHelper::sortByPrice($all_items, $order);
    }

    public static function buyNowBtnText($print = true)
    {
        return self::btnText('btn_text_buy_now', __('Buy Now', 'content-egg-tpl'), $print);
    }

    public static function couponBtnText($print = true)
    {
        return self::btnText('btn_text_coupon', __('Shop Sale', 'content-egg-tpl'), $print);
    }

    public static function getCurrentUserEmail()
    {
        if (!$current_user = wp_get_current_user())
            return '';
        return $current_user->user_email;
    }

    public static function getDaysAgo($ptime)
    {
        $etime = current_time('timestamp') - $ptime;
        if ($etime < 1)
            return '';
        $d = $etime / (24 * 60 * 60);
        if ($d < 1)
            return __('today', 'content-egg-tpl');
        $d = ceil($d);

        if ($d > 1)
            return sprintf(__('%d days ago', 'content-egg-tpl'), array($d));
        else
            return sprintf(__('%d day ago', 'content-egg-tpl'), array($d));
    }

    public static function getAmazonDisclaimer()
    {
        return __('Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on Amazon at the time of purchase will apply to the purchase of this product.', 'content-egg-tpl');
    }

    public static function printAmazonDisclaimer()
    {
        echo '<i class="fa fa-question-circle-o" title="' . \esc_attr(self::getAmazonDisclaimer()) . '"></i>';
    }

    private static function btnText($option_name, $default, $print = true)
    {
        $text = GeneralConfig::getInstance()->option($option_name);
        if (!$text)
            $text = $default;
        $text = \esc_attr($text);

        if (!$print)
            return $text;

        echo $text;
    }

}
