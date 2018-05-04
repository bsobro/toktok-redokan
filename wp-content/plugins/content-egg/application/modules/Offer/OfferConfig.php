<?php

namespace ContentEgg\application\modules\Offer;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * OfferConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class OfferConfig extends AffiliateParserModuleConfig {

    public function options()
    {
        $options = array(
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images on server', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
        );

        $parent = parent::options();
        unset($parent['ttl']);
        $parent['ttl_items']['default'] = 2592000;
        return array_merge($parent, $options);
    }

}
