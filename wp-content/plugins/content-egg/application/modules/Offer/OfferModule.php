<?php

namespace ContentEgg\application\modules\Offer;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\components\LinkHandler;
use ContentEgg\application\helpers\TextHelper;

/**
 * OfferModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class OfferModule extends AffiliateParserModule {

    public function info()
    {
        return array(
            'name' => 'Offer',
            'description' => __('Manually create offer from any site with price update.', 'content-egg'),
        );
    }

    public function getParserType()
    {
        return self::PARSER_TYPE_PRODUCT;
    }

    public function releaseVersion()
    {
        return '3.0.0';
    }

    public function defaultTemplateName()
    {
        return 'data_grid';
    }

    public function isFree()
    {
        return true;
    }

    public function isItemsUpdateAvailable()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        return array();
    }

    public function doRequestItems(array $items)
    {
        $parser = new OfferParser();
        foreach ($items as $key => $item)
        {
            if (empty($item['extra']['priceXpath']))
                continue;

            if ($item['orig_url'])
                $url = $item['orig_url'];
            elseif ($item['url'])
                $url = $item['url'];
            else
                continue;

            try
            {
                $parser->setUrl($url);
                $price = $parser->xpathScalar($item['extra']['priceXpath']);
                if (!$price)
                    continue;
            } catch (\Exception $e)
            {
                continue;
            }

            // assign new price        
            $items[$key]['price'] = (float) TextHelper::parsePriceAmount($price);
        }
        return $items;
    }

    public function presavePrepare($data, $post_id)
    {
        $data = parent::presavePrepare($data, $post_id);
        $return = array();
        foreach ($data as $key => $item)
        {
            $item['title'] = trim(\sanitize_text_field($item['title']));
            $item['description'] = trim(\wp_kses_post($item['description']));
            $item['orig_url'] = trim(strip_tags($item['orig_url']));
            $item['img'] = trim(strip_tags($item['img']));
            $item['extra']['deeplink'] = trim(strip_tags($item['extra']['deeplink']));
            $item['price'] = (float) TextHelper::parsePriceAmount($item['price']);
            $item['priceOld'] = (float) TextHelper::parsePriceAmount($item['priceOld']);
            $item['rating'] = TextHelper::ratingPrepare($item['rating']);

            if (!$item['title'])
                continue;
            if (!filter_var($item['orig_url'], FILTER_VALIDATE_URL))
                continue;
            if ($item['img'] && !filter_var($item['img'], FILTER_VALIDATE_URL))
                continue;

            if ($item['extra']['deeplink'])
                $item['url'] = LinkHandler::createAffUrl($item['orig_url'], $item['extra']['deeplink'], $item);
            else
                $item['url'] = $item['orig_url'];
            $return[$key] = $item;
        }
        return $return;
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderMetaboxModule()
    {
        $this->render('metabox_module', array('module_id' => $this->getId(), 'module' => $this ));
    }

}
