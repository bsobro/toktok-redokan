<?php

namespace ContentEgg\application\modules\Amazon;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\libs\amazon\AmazonProduct;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\components\ExtraData;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\helpers\TemplateHelper;

/**
 * AmazonModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class AmazonModule extends AffiliateParserModule {

    private $api_client = null;

    public function info()
    {
        return array(
            'name' => 'Amazon',
            'api_agreement' => 'https://affiliate-program.amazon.com/gp/advertising/api/detail/agreement.html',
            'description' => __('Adds products from Amazon.', 'content-egg'),
        );
    }

    public function getParserType()
    {
        return self::PARSER_TYPE_PRODUCT;
    }

    public function defaultTemplateName()
    {
        return 'data_item';
    }

    public function isItemsUpdateAvailable()
    {
        return true;
    }

    public function isFree()
    {
        return true;
    }

    public function isUrlSearchAllowed()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        $options = array();
        $keyword = trim($keyword);
        $search_index = $this->config('search_index');
        // Если не задана категория для поиска, то все остальные опции фильтрации работать не будут!
        if ($search_index != 'All' && $search_index != 'Blended')
        {
            if ($this->config('title'))
                $options['Title'] = $keyword;
            else
                $options['Keywords'] = $keyword;

            $options['Sort'] = $this->config('sort');
            if ((int) $this->config('brouse_node'))
                $options['BrowseNode'] = (int) $this->config('brouse_node');

            // Specifies the minimum price of the items to return. Prices are in 
            // terms of the lowest currency denomination, for example, pennies, 
            // for example, 3241 represents $32.41.
            if (!empty($query_params['minimum_price']))
                $options['MinimumPrice'] = TextHelper::pricePenniesDenomination($query_params['minimum_price']);
            elseif ($this->config('minimum_price'))
                $options['MinimumPrice'] = TextHelper::pricePenniesDenomination($this->config('minimum_price'));

            if (!empty($query_params['maximum_price']))
                $options['MaximumPrice'] = TextHelper::pricePenniesDenomination($query_params['maximum_price']);
            elseif ($this->config('maximum_price'))
                $options['MaximumPrice'] = TextHelper::pricePenniesDenomination($this->config('maximum_price'));

            // Specifies the minimum percentage off for the items to return.
            // @link: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleUS.html
            if (!empty($query_params['min_percentage_off']))
                $options['MinPercentageOff'] = (int) $query_params['min_percentage_off'];
            elseif ($this->config('min_percentage_off'))
                $options['MinPercentageOff'] = (int) $this->config('min_percentage_off');
        } else
            $options['Keywords'] = $keyword; // Для категории "All" работает только поиск по ключевому слову

        if ($this->config('merchant_id'))
            $options['MerchantId'] = "Amazon";

        $options['ResponseGroup'] = 'ItemIds,Offers,ItemAttributes,Images,VariationOffers';

        // Customer Reviews
        if ($this->config('customer_reviews'))
        {
            $options['ResponseGroup'] .= ',Reviews';
            $options['TruncateReviewsAt'] = $this->config('truncate_reviews_at');
            //$options['ReviewSort'] = $this->config('review_sort');
            $options['IncludeReviewsSummary'] = true;
        }
        // Editorial Reviews
        if ($this->config('editorial_reviews'))
        {
            $options['ResponseGroup'] .= ',EditorialReview';
        }

        // locale
        if (!empty($query_params['locale']) && array_key_exists($query_params['locale'], AmazonConfig::getActiveLocalesList()))
            $locale = $query_params['locale'];
        else
            $locale = $this->config('locale');

        // associate tag
        if (!empty($query_params['associate_tag']) && $query_params['associate_tag'])
            $associate_tag = $query_params['associate_tag'];
        else
            $associate_tag = $this->getAssociateTagForLocale($locale);

        // api client
        $client = $this->getAmazonClient();
        $client->setLocale($locale);
        $client->setAssociateTag($associate_tag);

        // Paging Through Results
        // @link: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PagingThroughResults.html
        if ($is_autoupdate)
            $total = $this->config('entries_per_page_update');
        else
            $total = $this->config('entries_per_page');

        $pages_count = ceil($total / 10);
        $results = array();

        // Is URL passed? Search by product URL
        if (filter_var($keyword, FILTER_VALIDATE_URL) && $asin = AmazonModule::parseAsinFromUrl($keyword))
        {
            $keyword = $asin;
        }

        // EAN or ASIN search
        if (TextHelper::isEan($keyword) && $search_index != 'All')
        {
            $options['IdType'] = 'EAN';
            // All IdTypes except ASINx require a SearchIndex to be specified.
            $options['SearchIndex'] = $search_index;
            unset($options['Keywords']);
            $ItemLookup = true;
        } elseif (TextHelper::isAsin($keyword))
        {
            $options['IdType'] = 'ASIN';
            unset($options['Keywords']);
            $ItemLookup = true;
        } else
            $ItemLookup = false;
        for ($i = 0; $i < $pages_count; $i++)
        {
            /**
             * If your application is submitting requests faster than once per second per IP address, you may receive error messages from the Product Advertising API until you decrease the rate of your requests. 
             */
            if ($i > 0)
                sleep(1); // ???

            $options['ItemPage'] = $i + 1;
            if ($ItemLookup)
                $data = $client->ItemLookup($keyword, $options); // EAN or ASIN search
            else
                $data = $client->ItemSearch($search_index, $options); // keyword search

            if (!is_array($data))
                break;

            $totalPages = (int) $data['Items']['TotalPages'];
            $data = array_slice($data['Items']['Item'], 0, $total - count($results));
            $results = array_merge($results, $this->prepareResults($data, $is_autoupdate, $locale, $associate_tag));
            if ($totalPages <= $i + 1)
                break;
        }
        return $results;
    }

    public function doRequestItems(array $items)
    {
        $locales = array();
        $default_locale = $this->config('locale');

        $items = self::fixUniqueIds($items);

        // find all locales
        foreach ($items as $item)
        {
            if (!empty($item['extra']['locale']))
                $locale = $item['extra']['locale'];
            else
            {
                $locale = $default_locale;
                $item['extra']['locale'] = $locale;
            }

            if (!in_array($locale, $locales))
                $locales[] = $locale;
        }

        // request by locale
        $results = array();
        foreach ($locales as $locale)
        {
            $request = array();
            foreach ($items as $item)
            {
                if ($item['extra']['locale'] == $locale)
                    $request[] = $item;
            }

            // Your request contains too many values for ItemId. This parameter can have a maximum of 10 values.
            $pages_count = ceil(count($request) / 10);
            for ($i = 0; $i < $pages_count; $i++)
            {
                /**
                 * If your application is submitting requests faster than once per second per IP address, you may receive error messages from the Product Advertising API until you decrease the rate of your requests. 
                 */
                if ($i > 0)
                    sleep(1); // ???

                $request10 = array_slice($request, $i * 10, 10);
                $results = array_merge($results, $this->requestItems($request10, $locale));
            }
        }

        // assign new data
        foreach ($items as $key => $item)
        {
            if (isset($results[$item['unique_id']]))
                $items[$key] = $results[$item['unique_id']];
            
            //@DEBUG
            //$items[$key]['price'] = $items[$key]['price'] - rand(1, 10);
        }

        return $items;
    }

    private function requestItems(array $items, $locale)
    {
        $options = array();

        $item_ids = array();
        foreach ($items as $item)
        {
            $item_ids[] = $item['extra']['ASIN'];
        }

        $options['ResponseGroup'] = 'Offers,VariationOffers';

        // update iframe url  for customer reviews
        if ($this->config('customer_reviews'))
        {
            $options['ResponseGroup'] .= ',Reviews';
            $options['TruncateReviewsAt'] = $this->config('truncate_reviews_at');
            $options['IncludeReviewsSummary'] = true;
        }

        // associate tag
        $associate_tag = $this->getAssociateTagForLocale($locale);

        // api client
        $client = $this->getAmazonClient();
        $client->setLocale($locale);
        $client->setAssociateTag($associate_tag);

        $results = $client->ItemLookup($item_ids, $options);

        if (!isset($results['Items']))
            throw new \Exception('ItemLookup request error.');

        $results = $results['Items']['Item'];

        $i = 0;
        $return = array();
        foreach ($items as $key => $item)
        {
            if ($item['extra']['ASIN'] != $results[$i]['ASIN'])
                continue;

            // offer
            $items[$key] = self::fillOfferVars($results[$i], $item, $item['extra']);
            if (!empty($results[$i]['CustomerReviews']))
            {
                $items[$key]['extra']['customerReviews'] = (array) new ExtraAmazonCustomerReviews;
                $items[$key]['extra']['customerReviews'] = ExtraData::fillAttributes($items[$key]['extra']['customerReviews'], $results[$i]['CustomerReviews']);
            } else
                $items[$key]['extra']['customerReviews'] = array();
            
            $items[$key]['domain'] = AmazonConfig::getDomainByLocale($locale);

            if (!$this->config('save_img'))
            {
                $items[$key]['img'] = $this->rewriteSslImageUrl($items[$key]['img']);
            }

            foreach ($items[$key]['extra']['imageSet'] as $set_key => $set)
            {
                if (!$set)
                    continue;
                $items[$key]['extra']['imageSet'][$set_key]['SwatchImage'] = $this->rewriteSslImageUrl($set['SwatchImage']);
                $items[$key]['extra']['imageSet'][$set_key]['SmallImage'] = $this->rewriteSslImageUrl($set['SmallImage']);
                $items[$key]['extra']['imageSet'][$set_key]['ThumbnailImage'] = $this->rewriteSslImageUrl($set['ThumbnailImage']);
                $items[$key]['extra']['imageSet'][$set_key]['TinyImage'] = $this->rewriteSslImageUrl($set['TinyImage']);
                $items[$key]['extra']['imageSet'][$set_key]['MediumImage'] = $this->rewriteSslImageUrl($set['MediumImage']);
                $items[$key]['extra']['imageSet'][$set_key]['LargeImage'] = $this->rewriteSslImageUrl($set['LargeImage']);
            }

            $return[$item['unique_id']] = $items[$key];
            $i++;
        }

        return $return;
    }

    private function prepareResults($results, $is_autoupdate, $locale, $associate_tag)
    {
        $data = array();
        foreach ($results as $key => $r)
        {
            $content = new ContentProduct;
            $extra = new ExtraDataAmazon;
            ExtraData::fillAttributes($extra, $r);
            $extra->locale = $locale;
            $extra->associate_tag = $associate_tag;

            if (isset($r['ItemLinks']) && isset($r['ItemLinks']['ItemLink']))
            {
                foreach ($r['ItemLinks']['ItemLink'] as $link_r)
                {
                    $link = new ExtraAmazonItemLinks;
                    ExtraData::fillAttributes($link, $link_r);
                    $extra->itemLinks[] = $link;
                }
            }

            if (!empty($r['ImageSets']) && !empty($r['ImageSets']['ImageSet']))
            {
                if (!isset($r['ImageSets']['ImageSet'][0]))
                    $r['ImageSets'] = array($r['ImageSets']['ImageSet']);
                else
                    $r['ImageSets'] = $r['ImageSets']['ImageSet'];


                foreach ($r['ImageSets'] as $image_r)
                {
                    $image = new ExtraAmazonImageSet;
                    $image->attributes = $image_r['@attributes'];
                    $image->SwatchImage = $this->rewriteSslImageUrl($image_r['SwatchImage']['URL']);
                    $image->SmallImage = $this->rewriteSslImageUrl($image_r['SmallImage']['URL']);
                    $image->ThumbnailImage = $this->rewriteSslImageUrl($image_r['ThumbnailImage']['URL']);
                    $image->TinyImage = $this->rewriteSslImageUrl($image_r['TinyImage']['URL']);
                    $image->MediumImage = $this->rewriteSslImageUrl($image_r['MediumImage']['URL']);
                    $image->LargeImage = $this->rewriteSslImageUrl($image_r['LargeImage']['URL']);
                    $extra->imageSet[] = $image;
                }
            }

            if (isset($r['ItemAttributes']['Feature']) && !is_array($r['ItemAttributes']['Feature']))
                $r['ItemAttributes']['Feature'] = array($r['ItemAttributes']['Feature']);

            $extra->itemAttributes = new ExtraAmazonItemAttributes;
            ExtraData::fillAttributes($extra->itemAttributes, $r['ItemAttributes']);

            if (isset($r['ItemAttributes']['Category']))
                $content->category = $r['ItemAttributes']['Category'];
            if (isset($r['ItemAttributes']['Manufacturer']))
                $content->manufacturer = $r['ItemAttributes']['Manufacturer'];
            if (isset($r['ItemAttributes']['Author']))
                $extra->author = $r['ItemAttributes']['Author'];

            foreach ($r['ItemAttributes'] as $attr_name => $attr_value)
            {
                if (!$attr_value || in_array($attr_name, array('Feature', 'ListPrice')))
                    continue;

                if (is_array($attr_value))
                {
                    $tmp_attr_value = reset($attr_value);
                    if (isset($attr_value[0]) && !is_array($attr_value[0]))
                        $attr_value = join('; ', $attr_value);
                    elseif (is_array($tmp_attr_value) && isset($tmp_attr_value[0]) && !is_array($tmp_attr_value[0]))
                        $attr_value = join('; ', $tmp_attr_value);
                    else
                    {
                        if (is_array(reset($attr_value)))
                            $attr_value = reset($attr_value);
                        if (isset($attr_value[0]))
                            continue;
                        $tmp_attr_value = array();
                        foreach ($attr_value as $kk => $vv)
                        {
                            $tmp_attr_value[] = TemplateHelper::splitAttributeName($kk) . ': ' . $vv;
                        }
                        $attr_value = join('; ', $tmp_attr_value);
                    }
                }
                $feature = array(
                    'name' => TemplateHelper::splitAttributeName($attr_name),
                    'value' => $attr_value,
                );
                $content->features[] = $feature;
            }

            // Offers
            self::fillOfferVars($r, $content, $extra);

            if (isset($r['CustomerReviews']))
            {
                $extra->customerReviews = new ExtraAmazonCustomerReviews;
                ExtraData::fillAttributes($extra->customerReviews, $r['CustomerReviews']);
            } else
                $extra->customerReviews = array();

            // Editorial Reviews
            if (isset($r['EditorialReviews']['EditorialReview']))
            {
                if (!isset($r['EditorialReviews']['EditorialReview'][0]))
                    $r['EditorialReviews']['EditorialReview'] = array($r['EditorialReviews']['EditorialReview']);

                foreach ($r['EditorialReviews']['EditorialReview'] as $editorialReview_r)
                {
                    $editorialReview = new ExtraAmazonEditorialReviews;
                    ExtraData::fillAttributes($editorialReview, $editorialReview_r);

                    // safe html
                    $editorialReview->Content = TextHelper::safeHtml($editorialReview->Content, $this->config('editorial_reviews_type'));

                    //размер
                    if ($this->config('editorial_reviews_size'))
                    {
                        $editorialReview->Content = TextHelper::truncateHtml($editorialReview->Content, $this->config('editorial_reviews_size'));
                    }
                    $extra->editorialReviews[] = $editorialReview;
                }
            }

            $content->url = self::formatProductUrl(rawurldecode($r['DetailPageURL'])); // urldecode???

            if (isset($r['ItemAttributes']['Title']))
                $content->title = $r['ItemAttributes']['Title'];

            //MediumImage может и не быть, а только ImageSets, тогда берем картинки с ImageSets
            if (isset($r['MediumImage']))
            {
                $extra->smallImage = $r['SmallImage']['URL'];
                $extra->mediumImage = $r['MediumImage']['URL'];
                $extra->largeImage = $r['LargeImage']['URL'];
            } elseif (!empty($r['ImageSets']))
            {
                $extra->smallImage = $r['ImageSets'][0]['SmallImage']['URL'];
                $extra->mediumImage = $r['ImageSets'][0]['MediumImage']['URL'];
                $extra->largeImage = $r['ImageSets'][0]['LargeImage']['URL'];
            }

            if (isset($r['LargeImage']))
                $content->img = $r['LargeImage']['URL'];
            elseif ($extra->largeImage)
                $content->img = $extra->largeImage;

            if (!$this->config('save_img'))
            {
                $content->img = $this->rewriteSslImageUrl($content->img);
            }

            $extra->addToCartUrl = $this->getAmazonAddToCartUrl($locale) .
                    '?ASIN.1=' . $extra->ASIN . '&Quantity.1=1' .
                    '&AssociateTag=' . $this->getAssociateTagForLocale($locale);

            if ($this->config('link_type') == 'add_to_cart')
            {
                $content->orig_url = $content->url;
                $content->url = $extra->addToCartUrl;
            }

            $content->extra = $extra;
            $content->unique_id = $locale . '-' . $extra->ASIN;

            $content->domain = AmazonConfig::getDomainByLocale($locale);
            $content->merchant = ucfirst($content->domain);

            $data[] = $content;
        }
        return $data;
    }

    private function getAmazonClient()
    {
        if ($this->api_client === null)
        {
            $access_key_id = $this->config('access_key_id');
            $secret_access_key = $this->config('secret_access_key');
            $associate_tag = $this->config('associate_tag');
            $this->api_client = new AmazonProduct($access_key_id, $secret_access_key, $associate_tag);
            //$this->api_client->setLocale($this->config('locale'));
        }
        return $this->api_client;
    }

    static private function fillOfferVars($r, $content, $extra)
    {
        // dirty tricks with object2array conversation for doRequestItems
        $return_array = false;
        if (!is_object($content))
        {
            $return_array = true;
            unset($content['extra']);
            $content = json_decode(json_encode($content), FALSE);
            $extra = json_decode(json_encode($extra), FALSE);
        }

        // Parent ASIN do not have offers. Return first(???) Offer From Item Variations
        // @link: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemsThatDoNotHaveOffers.html
        /*
          if (!isset($r['Offers']['Offer']) && isset($r['Variations']) && isset($r['Variations']['Item']))
          {
          if (!isset($r['Variations']['Item'][0]) && isset($r['Variations']['Item']['ASIN']))
          $r['Variations']['Item'] = array($r['Variations']['Item']);
          $r['Offers'] = $r['Variations']['Item'][0]['Offers'];
          // $r['OfferSummary'] = $r['VariationSummary'];
          }
         * 
         */

        // VariationSummary only for parent products
        if (!$content->price && isset($r['VariationSummary']))
        {
            if (isset($r['VariationSummary']['LowestSalePrice']))
                $r['Price'] = $r['VariationSummary']['LowestSalePrice'];
            elseif (isset($r['VariationSummary']['LowestPrice']))
                $r['Price'] = $r['VariationSummary']['LowestPrice'];
        }


        // OfferSummary
        if (isset($r['OfferSummary']))
        {
            if (!empty($r['OfferSummary']['LowestNewPrice']) && isset($r['OfferSummary']['LowestNewPrice']['Amount']))
                $extra->lowestNewPrice = self::pricePenniesDenomination($r['OfferSummary']['LowestNewPrice']['Amount'], $r['OfferSummary']['LowestNewPrice']);
            if (!empty($r['OfferSummary']['LowestUsedPrice']) && isset($r['OfferSummary']['LowestUsedPrice']['Amount']))
                $extra->lowestUsedPrice = self::pricePenniesDenomination($r['OfferSummary']['LowestUsedPrice']['Amount'], $r['OfferSummary']['LowestUsedPrice']);
            if (!empty($r['OfferSummary']['LowestCollectiblePrice']) && isset($r['OfferSummary']['LowestCollectiblePrice']['Amount']))
                $extra->lowestCollectiblePrice = self::pricePenniesDenomination($r['OfferSummary']['LowestCollectiblePrice']['Amount'], $r['OfferSummary']['LowestCollectiblePrice']);
            if (!empty($r['OfferSummary']['LowestRefurbishedPrice']) && isset($r['OfferSummary']['LowestRefurbishedPrice']['Amount']))
                $extra->lowestRefurbishedPrice = self::pricePenniesDenomination($r['OfferSummary']['LowestRefurbishedPrice']['Amount'], $r['OfferSummary']['LowestRefurbishedPrice']);
            $extra->totalNew = (int) $r['OfferSummary']['TotalNew'];
            $extra->totalUsed = (int) $r['OfferSummary']['TotalUsed'];
            $extra->totalCollectible = (int) $r['OfferSummary']['TotalCollectible'];
            $extra->totalRefurbished = (int) $r['OfferSummary']['TotalRefurbished'];
        }

        // Offers
        if (isset($r['Offers']) && isset($r['Offers']['Offer']) && isset($r['Offers']['Offer']['OfferListing']))
        {
            // SalePrice for amazon de?
            if (isset($r['Offers']['Offer']['OfferListing']['SalePrice']))
                $r['Price'] = $r['Offers']['Offer']['OfferListing']['SalePrice'];
            else
                $r['Price'] = $r['Offers']['Offer']['OfferListing']['Price'];

            if (isset($r['Offers']['Offer']['OfferListing']['AmountSaved']))
            {
                $extra->AmountSaved = $r['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'];
                if (isset($r['Offers']['Offer']['OfferListing']['PercentageSaved']))
                    $content->percentageSaved = $r['Offers']['Offer']['OfferListing']['PercentageSaved'];
            }

            // @link: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AvailabilityValues.html
            if (isset($r['Offers']['Offer']['OfferListing']['Availability']))
            {
                $extra->availability = $r['Offers']['Offer']['OfferListing']['Availability'];
            }

            if (isset($r['Offers']['Offer']['OfferListing']['IsEligibleForSuperSaverShipping']))
                $extra->IsEligibleForSuperSaverShipping = $r['Offers']['Offer']['OfferListing']['IsEligibleForSuperSaverShipping'];
        }elseif (isset($r['OfferSummary']['LowestNewPrice']))
            $r['Price'] = $r['OfferSummary']['LowestNewPrice'];


        if ((!isset($r['Price']) || !$r['Price']) && isset($r['ItemAttributes']['ListPrice']))
            $r['Price'] = $r['ItemAttributes']['ListPrice'];

        if (isset($r['ItemAttributes']['ListPrice']) &&
                $r['ItemAttributes']['ListPrice']['Amount'] &&
                ( (isset($r['Price']['Amount']) && $r['ItemAttributes']['ListPrice']['Amount'] > $r['Price']['Amount']) || $r['Price']['FormattedPrice'] == 'Too low to display'))
        {
            $content->priceOld = self::pricePenniesDenomination($r['ItemAttributes']['ListPrice']['Amount'], $r['ItemAttributes']['ListPrice']);
            $content->currencyCode = $r['ItemAttributes']['ListPrice']['CurrencyCode'];
            $content->currency = TextHelper::currencyTyping($content->currencyCode);
        }

        if (isset($r['Price']['FormattedPrice']) && $r['Price']['FormattedPrice'] == 'Too low to display')
            $extra->toLowToDisplay = true;

        if (!empty($r['Price']['Amount']))
        {
            $content->price = self::pricePenniesDenomination($r['Price']['Amount'], $r['Price']);
            $content->currencyCode = $r['Price']['CurrencyCode'];
            $content->currency = TextHelper::currencyTyping($content->currencyCode);
        } else
            $content->price = 0;

        $content->ean = $extra->itemAttributes->EAN;

        if ($return_array)
        {
            $content = json_decode(json_encode($content), true);
            $extra = json_decode(json_encode($extra), true);
            $content['extra'] = $extra;
            return $content;
        }
    }

    private function getLocaleSite($locale)
    {
        switch ($locale)
        {
            case 'uk':
                return 'http://www.amazon.co.uk';
            case 'de':
                return 'http://www.amazon.de';
            case 'fr':
                return 'http://www.amazon.fr';
            case 'jp':
                return 'http://www.amazon.co.jp';
            case 'cn':
                return 'http://www.amazon.cn';
            case 'it':
                return 'http://www.amazon.it';
            case 'es':
                return 'http://www.amazon.es';
            case 'ca':
                return 'http://www.amazon.ca';
            case 'br':
                return 'http://www.amazon.com.br';
            case 'in':
                return 'http://www.amazon.in';
            case 'mx':
                return 'http://www.amazon.com.mx';
            default: //'us'
                return 'http://www.amazon.com';
        }
    }

    private function getAssociateTagForLocale($locale)
    {
        if ($locale == $this->config('locale'))
            return $this->config('associate_tag');
        else
            return $this->config('associate_tag_' . $locale);
    }

    private function rewriteSslImageUrl($img)
    {
        if ($this->config('https_img'))
            return str_replace('http://ecx.images-amazon.com', 'https://images-na.ssl-images-amazon.com', $img);
        else
            return $img;
    }

    /**
     * Add to shopping cart url
     * @link: http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/AddToCartForm.html
     * @link: https://affiliate-program.amazon.com/gp/associates/help/t1/a10?ie=UTF8&pf_rd_i=assoc_help_t6_a1&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_177735_1
     * @link: https://affiliate-program.amazon.com/gp/associates/help/operating
     * @link: https://affiliate-program.amazon.com/gp/associates/help/t2/a11
     */
    private function getAmazonAddToCartUrl($locale)
    {
        return $this->getLocaleSite($locale) . '/gp/aws/cart/add.html';
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    public function renderSearchPanel()
    {
        $this->render('search_panel', array('module_id' => $this->getId()));
    }

    public function renderUpdatePanel()
    {
        $this->render('update_panel', array('module_id' => $this->getId()));
    }

    public static function fixUniqueIds(array $items)
    {
        // fix for old items
        $fixed = array();
        foreach ($items as $key => $item)
        {
            if (empty($item['extra']['locale']))
            {
                $fixed[$key] = $item;
                continue;
            }

            if (!strstr($item['unique_id'], '-'))
            {
                $new_unique_id = $item['extra']['locale'] . '-' . $item['unique_id'];
                $item['unique_id'] = $new_unique_id;
                $fixed[$new_unique_id] = $item;
            } else
                $fixed[$key] = $item;
        }
        return $fixed;
    }

    private static function pricePenniesDenomination($amount, $currency)
    {
        if (is_array($currency) && isset($currency['CurrencyCode']))
            $currency = $currency['CurrencyCode'];

        if ($currency == 'JPY')
            return $amount;
        else
            return TextHelper::pricePenniesDenomination($amount, false);
    }

    private static function parseAsinFromUrl($url)
    {
        $regex = '~(?:www\.)?ama?zo?n\.(?:com|ca|co\.uk|co\.jp|de|fr|in|es|com\.mx)/(?:exec/obidos/ASIN/|o/|gp/product/|(?:(?:[^"\'/]*)/)?dp/|)(B[0-9]{2}[0-9A-Z]{7}|[0-9]{9}(X|0-9]))(?:(?:/|\?|\#)(?:[^"\'\s]*))?~isx';

        if (preg_match($regex, $url, $matches))
            return $matches[1];
        else
            return '';
    }

    private static function formatProductUrl($url)
    {
        if (!strstr($url, '%'))
            return $url;

        if (!$parts = parse_url($url))
            return $urls;

        // fix % in url: https://www.amazon.in/Optimum-Nutrition-100%-Whey-Standard/dp/B002DYJ00C
        $parts['path'] = str_replace('%', '', $parts['path']);
        $result = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . '?' . $parts['query'];
        return $result;
    }

    public function viewDataPrepare($data)
    {
        foreach ($data as $key => $d)
        {
            $data[$key]['merchant'] = ucfirst($d['domain']);
        }

        return parent::viewDataPrepare($data);
    }

}
