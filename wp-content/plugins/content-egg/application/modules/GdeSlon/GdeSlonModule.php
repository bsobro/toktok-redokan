<?php

namespace ContentEgg\application\modules\GdeSlon;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\libs\gdeslon\GdeSlonApi;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;

/**
 * GdeSlonModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class GdeSlonModule extends AffiliateParserModule {

    private $api_client;
    private $merchants;

    public function info()
    {
        return array(
            'name' => 'GdeSlon',
            'description' => sprintf(__('Adds products from %s.', 'content-egg'), '<a target="_blank" href="http://www.keywordrush.com/go/gdeslon">GdeSlon.ru</a>'),
        );
    }

    public function getParserType()
    {
        return self::PARSER_TYPE_PRODUCT;
    }

    public function defaultTemplateName()
    {
        return 'grid';
    }

    public function isItemsUpdateAvailable()
    {
        return true;
    }

    public function isFree()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        $options = array();

        if ($is_autoupdate)
            $options['l'] = $this->config('entries_per_page_update');
        else
            $options['l'] = $this->config('entries_per_page');

        if ($m = $this->config('merchant_id'))
            $options['m'] = self::prepareArrParam($m);
        if ($tid = $this->config('search_category'))
            $options['tid'] = self::prepareArrParam($tid);

        if ($this->config('order') && $this->config('order') != 'default')
            $options['order'] = $this->config('order');

        $client = $this->getApiClient();
        $results = $client->search($keyword, $options);

        if (!isset($results['offers']) || !isset($results['offers']['offer']))
            return array();
        $results = $results['offers']['offer'];
        if (!isset($results[0]) && isset($results['name']))
            $results = array($results);

        return $this->prepareResults($results);
    }

    private function prepareResults($results)
    {
        $data = array();
        foreach ($results as $key => $r)
        {
            $content = new ContentProduct;

            if (!$r['name'] && $r['description'])
                $r['name'] = TextHelper::truncate(strip_tags($r['description']));

            if (!$r['name'])
                continue;

            $content->unique_id = $r['@attributes']['gs_product_key'] . $r['@attributes']['id'];
            $content->title = trim($r['name']);

            if ($r['description'])
            {
                $content->description = strip_tags(trim($r['description']));
                $content->description = preg_replace("/\n\W*\n/msi", "\n", $content->description);
                $content->description = nl2br($content->description);
            }
            if ($max_size = $this->config('description_size'))
                $content->description = TextHelper::truncate($content->description, $max_size);

            $content->url = $r['url'];
            if ($this->config('subid'))
                $content->url .= '?sub_id=' . urlencode($this->config('subid'));

            $content->price = (float) $r['price'];
            if (!empty($r['oldprice']))
                $content->priceOld = (float) $r['oldprice'];
            $content->currencyCode = $r['currencyId'];
            $content->currency = TextHelper::currencyTyping($content->currencyCode);

            if ($r['original_picture'])
            {
                $content->img = $r['original_picture'];
            } elseif ($r['picture'] && $r['picture'] != 'http://www.gdeslon.ru/images/default_picture/small.png')
            {
                $imgs = explode(',', $r['picture']);
                $content->img = $imgs[0];
            }
            $content->availability = (bool) $r['@attributes']['available'];
            $content->manufacturer = $r['vendor'];

            $content->extra = new ExtraDataGdeSlon;
            $content->extra->productId = $r['@attributes']['id'];
            $content->extra->gsCategoryId = (isset($r['@attributes']['gs_category_id'])) ? (int) $r['@attributes']['gs_category_id'] : '';
            $content->extra->merchantId = (isset($r['@attributes']['merchant_id'])) ? (int) $r['@attributes']['merchant_id'] : '';
            $content->extra->gsProductKey = (isset($r['@attributes']['gs_product_key'])) ? $r['@attributes']['gs_product_key'] : '';
            $content->extra->article = (isset($r['@attributes']['article'])) ? $r['@attributes']['article'] : '';
            if (!empty($r['destination-url-do-not-send-traffic']))
                $content->extra->original_url = $r['destination-url-do-not-send-traffic'];
            if (!empty($r['original_picture']))
                $content->extra->original_url = $r['original_picture'];
            // get merchant info
            $this->fillMerchantInfo($content);

            $data[] = $content;
        }
        return $data;
    }

    public function doRequestItems(array $items)
    {
        $product_ids = array();
        foreach ($items as $key => $item)
        {
            if (empty($item['extra']['article']))
                throw new \Exception('doRequestItems request error.');

            $product_ids[] = $item['extra']['article'];
        }

        $client = $this->getApiClient();
        $results = $client->product($product_ids);

        if (!isset($results['shop']['offers']) || !isset($results['shop']['offers']['offer']))
            throw new \Exception('doRequestItems request error.');
        $results = $results['shop']['offers']['offer'];
        if (!isset($results[0]) && isset($results['name']))
            $results = array($results);

        // article ID not unique?!..
        $ordered_results = array();
        foreach ($results as $r)
        {
            $ordered_results[$r['@attributes']['id']] = $r;
        }

        // assign new price
        foreach ($items as $key => $item)
        {
            if (!isset($ordered_results[$item['extra']['productId']]))
                continue;
            $r = $ordered_results[$item['extra']['productId']];
            $items[$key]['price'] = (float) $r['price'];
            if (!empty($r['oldprice']))
                $items[$key]['priceOld'] = (float) $r['oldprice'];
            $items[$key]['availability'] = (bool) $r['@attributes']['available'];
        }
        return $items;
    }

    private static function prepareArrParam($value)
    {
        $m = explode(',', $value);

        if (count($m) > 1)
        {
            $results = array();
            foreach ($m as $mv)
            {
                $results[] = trim((int) $mv);
            }
            return $results;
        } else
            return $m[0];
    }

    private function fillMerchantInfo($content)
    {
        $merchant_id = $content->extra->merchantId;

        if ($this->merchants === null)
        {
            try
            {
                $results = $this->getApiClient()->getMerhants();
            } catch (\Exception $e)
            {
                $results = array();
            }
            if (!is_array($results))
                $results = array();

            foreach ($results as $r)
            {
                $this->merchants[$r['_id']] = $r['name'];
            }
        }
        if (isset($this->merchants[$merchant_id]))
        {
            $merhant_name = $this->merchants[$merchant_id];
            $merhant_name = preg_replace('/_new$/', '', $merhant_name);

            if (TextHelper::isValidDomainName($merhant_name))
                $content->domain = $merhant_name;
            elseif (!empty($content->extra->original_url))
                $content->domain = TextHelper::getHostName($content->extra->original_url);
            elseif (!empty($content->extra->original_picture))
            {
                $content->domain = TextHelper::getHostName($content->extra->original_picture);
                if ($content->domain == 'alicdn.com')
                    $content->domain = 'aliexpress.com';
            }
        }
    }

    private function getApiClient()
    {
        if ($this->api_client === null)
        {
            $this->api_client = new GdeSlonApi($this->config('api_key'));
        }
        return $this->api_client;
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

}
