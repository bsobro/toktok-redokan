<?php

namespace ContentEgg\application\helpers;

use ContentEgg\application\admin\GeneralConfig;


/**
 * AdminHelper class file
 * 
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 * 
 */
class AdminHelper {

    public static function getCategoryList()
    {
        $opt = array('name' => 'item[category]', 'id' => 'category', 'hide_empty' => false);

        // categs + product categs
        $taxonomy = array('category');

        // @todo: widget is initialized before woo? taxonomy does not exist
        if (in_array('product', GeneralConfig::getInstance()->option('post_types')) && \taxonomy_exists('product_cat'))
            $taxonomy[] = 'product_cat';

        $cat_args = array('taxonomy' => $taxonomy, 'orderby' => 'name', 'order' => 'asc', 'hide_empty' => false);
        $categories = \get_terms($cat_args);

        $results = array();
        foreach ($categories as $key => $category)
        {
            $results[$category->term_id] = $category->name;
            if ($category->taxonomy == 'product_cat')
                $results[$category->term_id] .= ' [product]';
        }

        return $results;
    }

}
