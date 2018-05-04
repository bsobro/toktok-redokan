<?php

namespace ContentEgg\application\components;

use ContentEgg\application\helpers\TextHelper;

/**
 * LinkHandler class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class LinkHandler {

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * Deeplink & more...
     */
    public static function createAffUrl($url, $deeplink, $item = array(), $subid = '')
    {
        // custom filter
        $filtered = \apply_filters('cegg_create_affiliate_link', $url, $deeplink);
        if ($filtered != $url)
            return $url;
                
        // profitshare fix. return if url already created
        if (!empty($item['url']) && strstr($item['url'], '/l.profitshare.ro/'))
            return $item['url'];

        if (!$deeplink)
        {
            $result = $url;
        } elseif (substr(trim($deeplink), 0, 7) == '[regex]')
        {
            // regex preg_replace
            $result = self::getRegexReplace($url, $deeplink);
        } elseif (substr(trim($deeplink), 0, 13) == '[profitshare]')
        {
            // ProfitShare link creator
            $result = self::getProfitshareLink($url, $deeplink, $item);
        } elseif (strstr($deeplink, '{{') && strstr($deeplink, '}}'))
        {
            // template deeplink
            $result = self::getUrlTemplate($url, $deeplink, $item);
        } elseif (!preg_match('/^https?:\/\//i', $deeplink))
        {
            // url with tail
            $result = self::getUrlWithTail($url, $deeplink);
        } else
        {
            // deeplink
            // @todo: subid
            //if ($subid)
            //$deeplink = Cpa::deeplinkSetSubid($deeplink, $subid);
            $result = $deeplink . urlencode($url);
        }
        if ($subid)
        {
            $result = self::getUrlWithTail($result, $subid);
        }

        return $result;
    }

    public static function getUrlWithTail($url, $tail)
    {
        $tail = preg_replace('/^[?&]/', '', $tail);

        $query = parse_url($url, PHP_URL_QUERY);
        if ($query)
            $url .= '&';
        else
            $url .= '?';

        parse_str($tail, $tail_array);
        $url .= http_build_query($tail_array);
        return $url;
    }

    public static function getUrlTemplate($url, $template, $item = array())
    {
        $template = str_replace('{{url}}', $url, $template);
        $template = str_replace('{{url_encoded}}', urlencode($url), $template);
        if ($item)
        {
            if (!empty($item['post_id']))
                $template = str_replace('{{post_id}}', urlencode($item['post_id']), $template);
            if (!empty($item['unique_id']))
                $template = str_replace('{{item_unique_id}}', urlencode($item['unique_id']), $template);
        }
        return $template;
    }

    public static function getRegexReplace($url, $regex)
    {
        $regex = trim($regex);

        $parts = explode('][', $regex);
        if (count($parts) != 3)
            return $url;

        $pattern = $parts[1];
        $replacement = rtrim($parts[2], ']');

        // null character allows a premature regex end and "/../e" injection
        if (strpos($pattern, 0) !== false || !trim($pattern))
            return $url;

        if ($result = @preg_replace($pattern, $replacement, $url))
            return $result;
        else
            return $url;
    }

    public static function getProfitshareLink($url, $regex, $item = array())
    {
        $regex = trim($regex);
        $parts = explode('][', $regex);
        if (count($parts) != 3)
            return $url;

        $api_user = $parts[1];
        $api_key = rtrim($parts[2], ']');

        $api_url = 'http://api.profitshare.ro/affiliate-links/?';
        $query_string = '';

        $spider = curl_init();
        curl_setopt($spider, CURLOPT_HEADER, false);
        curl_setopt($spider, CURLOPT_URL, $api_url . $query_string);
        curl_setopt($spider, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($spider, CURLOPT_TIMEOUT, 30);
        curl_setopt($spider, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($spider, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($spider, CURLOPT_USERAGENT, 'Content Egg WP Plugin (http://www.keywordrush.com/en/contentegg)');

        $data = array();
		$name = 'CE:' . TextHelper::getHostName($url);
		if (!empty($item['title']))
			$name .= ' ' . $item['title'];
        $data[] = array(
            'name' => $name,
            'url' => $url
        );

        curl_setopt($spider, CURLOPT_POST, true);
        curl_setopt($spider, CURLOPT_POSTFIELDS, http_build_query($data));

        $profitshare_login = array('api_user' => $api_user, 'api_key' => $api_key,);
        $date = gmdate('D, d M Y H:i:s T', time());
        $signature_string = 'POSTaffiliate-links/?' . $query_string . '/' . $profitshare_login['api_user'] . $date;
        $auth = hash_hmac('sha1', $signature_string, $profitshare_login['api_key']);

        $extra_headers = array("Date: {$date}", "X-PS-Client: {$profitshare_login['api_user']}", "X-PS-Accept: json", "X-PS-Auth: {$auth}");

        curl_setopt($spider, CURLOPT_HTTPHEADER, $extra_headers);

        $output = curl_exec($spider);
        if (!$output)
            return $url;

        $result = json_decode($output, true);

        if (!$result)
            return $url;
        if (isset($result['result'][0]['ps_url']))
            return $result['result'][0]['ps_url'];
        else
            $url;
    }

}
