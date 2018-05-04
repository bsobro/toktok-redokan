<?php

namespace ContentEgg\application;

use ContentEgg\application\components\VirtualPage;
use ContentEgg\application\admin\GeneralConfig;
use ContentEgg\application\components\ModuleManager;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\ModuleViewer;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\helpers\InputHelper;

/**
 * ProductSearch class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class ProductSearch extends VirtualPage {

    const PAGE_SLUG = 'product-search';
    const shortcode = 'content-egg-search-form';

    private $keyword;

    public static function initAction()
    {
        \add_shortcode(self::shortcode, array(__CLASS__, 'viewSearchFrom'));

        if (!GeneralConfig::getInstance()->option('search_modules'))
            return;

        new self;
        //parent::initAction();
    }

    protected function handleRequest($query_vars = array())
    {
        if (empty($query_vars['s']))
            return;

        $this->keyword = trim(TextHelper::clear_utf8(\sanitize_text_field($query_vars['s'])));
        if (!$this->keyword)
            return;

        parent::handleRequest($query_vars);
    }

    public static function viewSearchFrom($atts, $content = "")
    {
        echo ProductSearchWidget::getSearchForm();
    }

    public function getSlug()
    {
        return \apply_filters('cegg_product_search_slug', self::PAGE_SLUG);
    }
    
    public static function getPageSlug()
    {
        return \apply_filters('cegg_product_search_slug', self::PAGE_SLUG);
    }
    

    public function getBody()
    {
        // search & add data to ModuleViewer
        $total = $this->addSearchData();
        if ($total)
            return GeneralConfig::getInstance()->option('search_page_tpl');
        else
            return __('Sorry. No products found.', 'content-egg-tpl');
    }

    public function getTemplate()
    {
        return 'ce-product-search.php';
    }

    /**
     * Search and set view data
     */
    private function addSearchData()
    {
        $post_id = -1;
        $module_ids = GeneralConfig::getInstance()->option('search_modules');
        $total = 0;
        foreach ($module_ids as $module_id)
        {
            $parser = ModuleManager::getInstance()->parserFactory($module_id);
            if (!$parser->isActive())
                continue;

            try
            {
                $data = $parser->doRequest($this->keyword, array(), true);
            } catch (\Exception $e)
            {
                // error
                continue;
            }

            // nodata!
            if (!$data)
                continue;

            $data = ContentManager::dataPresavePrepare($data, $module_id, $post_id);
            $data = ContentManager::dataPreviewPrepare($data, $module_id, $post_id);
            $total += count($data);
            ModuleViewer::getInstance()->setData($module_id, $post_id, $data);
        }
        return $total;
    }

    public function getTitle()
    {
        return sprintf(__('Search Results for "%s"', 'content-egg-tpl'), $this->keyword);
    }

}
