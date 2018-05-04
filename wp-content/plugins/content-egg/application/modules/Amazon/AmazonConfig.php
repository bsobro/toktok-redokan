<?php

namespace ContentEgg\application\modules\Amazon;

use ContentEgg\application\components\AffiliateParserModuleConfig;
use ContentEgg\application\admin\GeneralConfig;

/**
 * AmazonConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class AmazonConfig extends AffiliateParserModuleConfig {

    public function options()
    {
        $options = array(
            'access_key_id' => array(
                'title' => 'Access Key ID <span class="cegg_required">*</span>',
                'description' => __('Special key to access the Amazon API.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => __('The "Access Key ID" can not be empty', 'content-egg'),
                    ),
                ),
                'section' => 'default',
            ),
            'secret_access_key' => array(
                'title' => 'Secret Access Key <span class="cegg_required">*</span>',
                'description' => __('Another special key to access the Amazon API.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => __('The "Secret Access Key" can not be empty.', 'content-egg'),
                    ),
                ),
                'section' => 'default',
            ),
            'associate_tag' => array(
                'title' => __('Default Tracking ID', 'content-egg') . ' <span class="cegg_required">*</span>',
                'description' => __('Connection with your account in the affiliate program. In order to receive a commission from sales, specify this option correctly.', 'content-egg') . ' ' .
                __('Tracking ID must point to locale settings by default', 'content-egg') . ' ' .
                __('You can set Tracking ID for other locales if you want to add products more than one locale.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => __('The "Tracking ID" can not be empty.', 'content-egg'),
                    ),
                ),
                'section' => 'default',
                'metaboxInit' => true,
            ),
            'locale' => array(
                'title' => __('Default locale', 'content-egg'),
                'description' => __('The branch/locale of Amazon. Each branch requires a separate registration in certain affiliate program.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => self::getLocalesList(),
                'default' => self::getDefaultLocale(),
                'section' => 'default',
            ),
            'entries_per_page' => array(
                'title' => __('Results', 'content-egg'),
                'description' => __('Number of results for one search query.', 'content-egg') . ' ' .
                __('It needs a bit more time to get more than 10 results in one request', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 10,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 50, // The value you specified for ItemPage is invalid. Valid values must be between 1 and 5.
                        'message' => __('The field "Results" can not be more than 50.', 'content-egg'),
                    ),
                ),
                'section' => 'default',
            ),
            'entries_per_page_update' => array(
                'title' => __('Results for updates ', 'content-egg'),
                'description' => __('Number of results for automatic updates and autoblogging.', 'content-egg') . ' ' .
                __('It needs a bit more time to get more than 10 results in one request', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 3,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 50,
                        'message' => __('The field "Results" can not be more than 50.', 'content-egg'),
                    ),
                ),
                'section' => 'default',
            ),
            'link_type' => array(
                'title' => __('Link type', 'content-egg'),
                'description' => __('Type of partner links. Know more about amazon <a target="_blank" href="https://affiliate-program.amazon.com/gp/associates/help/t2/a11">90 day cookie</a>.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'product' => 'Product page',
                    'add_to_cart' => 'Add to cart',
                ),
                'default' => 'product',
                'section' => 'default',
            ),
            'search_index' => array(
                'title' => __('Categories for search', 'content-egg'),
                'description' => __('The list of categories for US Amazon. For local branches some of categories may be not available. If you do not set category for searching, no other filtering options in addition to searching for the keyword (for example, the minimal price or sorting) will not working. ', 'content-egg')
                    . ' ' . __('Search by EAN require a Category to be specified.', 'content-egg'),                
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array('All' => '[ All ]', 'Blended' => '[ Blended ]', 'Music' => '[ Music ]', 'Video' => '[ Video ]', 'Apparel' => 'Apparel', 'Automotive' => 'Automotive', 'Baby' => 'Baby', 'Beauty' => 'Beauty', 'Books' => 'Books', 'Classical' => 'Classical', 'DigitalMusic' => 'DigitalMusic', 'DVD' => 'DVD', 'Electronics' => 'Electronics', 'GourmetFood' => 'GourmetFood', 'Grocery' => 'Grocery', 'HealthPersonalCare' => 'HealthPersonalCare', 'HomeGarden' => 'HomeGarden', 'Industrial' => 'Industrial', 'Jewelry' => 'Jewelry', 'KindleStore' => 'KindleStore', 'Kitchen' => 'Kitchen', 'Magazines' => 'Magazines', 'Merchants' => 'Merchants', 'Miscellaneous' => 'Miscellaneous', 'MP3Downloads' => 'MP3Downloads', 'MusicalInstruments' => 'MusicalInstruments', 'MusicTracks' => 'MusicTracks', 'OfficeProducts' => 'OfficeProducts', 'OutdoorLiving' => 'OutdoorLiving', 'PCHardware' => 'PCHardware', 'PetSupplies' => 'PetSupplies', 'Photo' => 'Photo', 'Shoes' => 'Shoes', 'Software' => 'Software', 'SportingGoods' => 'SportingGoods', 'Tools' => 'Tools', 'Toys' => 'Toys', 'UnboxVideo' => 'UnboxVideo', 'VHS' => 'VHS', 'VideoGames' => 'VideoGames', 'Watches' => 'Watches', 'Wireless' => 'Wireless', 'WirelessAccessories' => 'WirelessAccessories'),
                'default' => 'All',
                'section' => 'default',
            ),
            'sort' => array(
                'title' => __('Sorting order', 'content-egg'),
                'description' => __('Sorting variants depend on locale and chosed category. List of all available values you can find <a href="http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/index.html?APPNDX_SortValuesArticle.html">here</a>.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'section' => 'default',
            ),
            'brouse_node' => array(
                'title' => __('Brouse node', 'content-egg'),
                'description' => __('Integer ID "node" on Amazon. The search will be made only in this "node".', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'section' => 'default',
            ),
            'title' => array(
                'title' => __('Search in title', 'content-egg'),
                'description' => __('The search will produce only by product name.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'merchant_id' => array(
                'title' => __('Only Amazon', 'content-egg'),
                'description' => __('Select products that are selling by Amazon. Other sellers are excluded from the search.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'minimum_price' => array(
                'title' => __('Minimal price', 'content-egg'),
                'description' => __('Example, 8.99', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'metaboxInit' => true,
            ),
            'maximum_price' => array(
                'title' => __('Maximal price', 'content-egg'),
                'description' => __('Example, 98.50', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'metaboxInit' => true,
            ),
            'min_percentage_off' => array(
                'title' => __('Minimal discount', 'content-egg'),
                'description' => __('Choose products with discount. You must set category of product. Note, that this option works not for all categories.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('Any', 'content-egg'),
                    '5%' => '5%',
                    '10%' => '10%',
                    '15%' => '15%',
                    '20%' => '20%',
                    '25%' => '25%',
                    '30%' => '30%',
                    '35%' => '35%',
                    '40%' => '40%',
                    '45%' => '45%',
                    '50%' => '50%',
                    '60%' => '60%',
                    '70%' => '70%',
                    '80%' => '80%',
                    '90%' => '90%',
                    '95%' => '95%',
                ),
                'default' => '',
                'section' => 'default',
                'metaboxInit' => true,
            ),
            'customer_reviews' => array(
                'title' => __('Customer reviews', 'content-egg'),
                'description' => __('Get user reviews. Reviews will be in iframe. Iframe url is valid only 24 hours, please, use autoupdating function with less than 24 hour to keep actual url.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'truncate_reviews_at' => array(
                'title' => __('Cut reviews', 'content-egg'),
                'description' => __('Number of characters for one review. 0 - the maximal length of the text.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 500,
                'validator' => array(
                    'trim',
                    'absint',
                ),
                'section' => 'default',
            ),
            'editorial_reviews' => array(
                'title' => __('Parse description', 'content-egg'),
                'description' => __('Parse description of products from seller', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'editorial_reviews_type' => array(
                'title' => __('Type of description', 'content-egg'),
                'description' => '',
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'allow_all' => __('Like on Amazon', 'content-egg'),
                    'safe_html' => __('Safe HTML', 'content-egg'),
                    'allowed_tags' => __('Only allowed HTML tags', 'content-egg'),
                    'text' => __('Text only', 'content-egg'),
                ),
                'default' => 'All',
                'section' => 'default',
            ),
            'editorial_reviews_size' => array(
                'title' => __('Size of description', 'content-egg'),
                'description' => __('The maximum size of the item description. 0 - do not cut.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 1000,
                'validator' => array(
                    'trim',
                    'absint',
                ),
                'section' => 'default',
            ),
            'https_img' => array(
                'title' => __('Use images with https', 'content-egg'),
                'description' => __('Rewrite url of images with https. Use it if you have SSL on your domain', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images on server', 'content-egg') . ' ' . __('Enabling this option violates rules of API.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'show_small_logos' => array(
                'title' => __('Small logos', 'content-egg'),
                'description' => __('Show small logos', 'content-egg') . '<p class="description">' . sprintf(__('Read more: <a target="_blank" href="%s">Amazon brand usage guidelines</a>.', 'content-egg'), 'https://advertising.amazon.com/ad-specs/en/policy/brand-usage') . '</p>',
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            
        );

        foreach (self::getLocalesList() as $locale_id => $locale_name)
        {
            $options['associate_tag_' . $locale_id] = array(
                'title' => sprintf(__('Tracking ID for %s locale', 'content-egg'), $locale_name),
                'description' => __('Type here your tracking ID for this locale if you need multiple locale parsing', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'metaboxInit' => true,
            );
        }

        $parent = parent::options();
        $parent['ttl_items']['default'] = 86400;
        return array_merge($parent, $options);
    }

    public static function getLocalesList()
    {
        return array('us' => 'US', 'uk' => 'UK', 'de' => 'DE', 'jp' => 'JP', 'cn' => 'CN', 'fr' => 'FR', 'it' => 'IT', 'es' => 'ES', 'ca' => 'CA', 'br' => 'BR', 'in' => 'IN', 'mx' => 'MX');
    }

    public static function getDefaultLocale()
    {
        return 'us';
        
        // @todo: Fix error: Maximum function nesting level of '100' reached, aborting!
        /*
        $lang = GeneralConfig::getInstance()->option('lang');
        if (array_key_exists($lang, self::getLocalesList()))
            return $lang;
        else
            return 'us';
         * 
         */
    }

    public static function getActiveLocalesList()
    {
        $locales = self::getLocalesList();
        $active = array();

        $default = self::getInstance()->option('locale');
        $active[$default] = $locales[$default];

        foreach ($locales as $locale => $name)
        {
            if ($locale == $default)
                continue;
            if (self::getInstance()->option('associate_tag_' . $locale))
                $active[$locale] = $name;
        }
        return $active;
    }

    public static function getDomainByLocale($locale)
    {
        $domains = array(
            'us' => 'amazon.com',
            'uk' => 'amazon.co.uk',
            'de' => 'amazon.de',
            'jp' => 'amazon.jp',
            'cn' => 'amazon.cn',
            'fr' => 'amazon.fr',
            'it' => 'amazon.it',
            'es' => 'amazon.es',
            'ca' => 'amazon.ca',
            'br' => 'amazon.com.br',
            'in' => 'amazon.in',
            'mx' => 'amazon.com.mx',
        );
        if (isset($domains[$locale]))
            return $domains[$locale];
        else
            return 'amazon.com';
    }

}
