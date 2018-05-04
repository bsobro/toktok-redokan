<?php

namespace ContentEgg\application\components;

use ContentEgg\application\helpers\ImageHelper;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\LocalRedirect;

/**
 * ParserModule abstract class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
abstract class ParserModule extends Module {

    const PARSER_TYPE_CONTENT = 'CONTENT';
    const PARSER_TYPE_PRODUCT = 'PRODUCT';
    const PARSER_TYPE_COUPON = 'COUPON';
    const PARSER_TYPE_IMAGE = 'IMAGE';
    const PARSER_TYPE_VIDEO = 'VIDEO';
    const PARSER_TYPE_OTHER = 'OTHER';

    abstract public function doRequest($keyword, $query_params = array(), $is_autoupdate = false);

    abstract public function getParserType();

    public function isActive()
    {
        if ($this->is_active === null)
        {
            if ($this->getConfigInstance()->option('is_active'))
                $this->is_active = true;
            else
                $this->is_active = false;
        }
        return $this->is_active;
    }

    final public function isParser()
    {
        return true;
    }

    public function isUrlSearchAllowed()
    {
        return false;
    }

    public function presavePrepare($data, $post_id)
    {
        global $post;
        $data = parent::presavePrepare($data, $post_id);

        // do not save images for revisions & search results
        if (($post && wp_is_post_revision($post_id)) || $post_id < 0)
            return $data;

        $old_data = ContentManager::getData($post_id, $this->getId());

        foreach ($data as $key => $item)
        {
            // fill domain
            if (empty($item['domain']))
            {
                if (!empty($item['orig_url']))
                    $url = $item['orig_url'];
                elseif (!empty($item['img']))
                    $url = $item['img'];
                else
                    $url = $item['url'];

                if ($url)
                {
                    $domain = TextHelper::getHostName($url);
                    if (!in_array($domain, array('buscape.com.br')))
                        $data[$key]['domain'] = TextHelper::getHostName($url);
                }
            }

            // save img
            if ($this->config('save_img') && !wp_is_post_revision($post_id))
            {
                // check old_data also. need for fix behavior with "preview changes" button and by keyword update
                if (isset($old_data[$key]) && !empty($old_data[$key]['img_file']) && file_exists(ImageHelper::getFullImgPath($old_data[$key]['img_file'])))
                {
                    // image exists
                    $item['img'] = $old_data[$key]['img'];
                    $item['img_file'] = $old_data[$key]['img_file'];
                } elseif ($item['img'] && empty($item['img_file']))
                {
                    $local_img_name = ImageHelper::saveImgLocaly($item['img'], $item['title']);
                    if ($local_img_name)
                    {
                        $uploads = \wp_upload_dir();
                        $item['img'] = $uploads['url'] . '/' . $local_img_name;
                        $item['img_file'] = ltrim(trailingslashit($uploads['subdir']), '\/') . $local_img_name;
                    }
                }
                $data[$key] = $item;                
            }
        }
        return $data;
    }

    public static function getFullImgPath($img_path)
    {
        $uploads = \wp_upload_dir();
        return trailingslashit($uploads['basedir']) . $img_path;
    }

    public function defaultTemplateName()
    {
        return 'data_simple';
    }

    public function viewDataPrepare($data)
    {
        // local redirect
        if ($this->config('set_local_redirect'))
        {
            foreach ($data as $key => $d)
            {
                $data[$key]['aff_url'] = $d['url']; // url without redirect
                $data[$key]['url'] = LocalRedirect::createRedirectUrl($d);
            }
        }

        return $data;
    }

}
