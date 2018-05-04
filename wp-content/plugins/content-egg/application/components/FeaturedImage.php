<?php

namespace ContentEgg\application\components;

use ContentEgg\application\components\ModuleManager;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\helpers\ImageHelper;

/**
 * FeaturedImage class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class FeaturedImage {

    private $app_params = array();

    public function __construct()
    {
        if (\is_admin())
            $this->adminInit();
    }

    public function adminInit()
    {
        // priority 11 - after meta save
        \add_action('save_post', array($this, 'setImage'), 11, 2);
    }

    public function setImage($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (\get_post_status($post_id) == 'auto-draft' || \wp_is_post_revision($post_id))
            return;

        if (\has_post_thumbnail($post_id))
            return;

        $modules_ids = ModuleManager::getInstance()->getParserModulesIdList();
        foreach ($modules_ids as $module_id)
        {
            if (self::setFuturedImageByModule($post_id, $module_id))
                return;
        }
    }

    public static function setFuturedImageByModule($post_id, $module_id)
    {
        $module = ModuleManager::factory($module_id);
        $featured_image = $module->config('featured_image', false);
        if (!$featured_image)
            return false;

        $data = ContentManager::getData($post_id, $module->getId());
        if (!$data)
            return false;

        if ($featured_image == 'second' && isset($data[1]))
            unset($data[0]);
        elseif ($featured_image == 'last')
            $data = array_reverse($data);
        elseif ($featured_image == 'rand')
            shuffle($data);

        require_once( \ABSPATH . 'wp-admin/includes/image.php' );

        foreach ($data as $d)
        {
            $img_file = self::getImgFile($d);
            if (!$img_file)
                continue;

            return self::attachThumbnail($img_file, $post_id, $d['title']);
        } //data foreach

        return false;
    }

    public static function getImgFile($item)
    {
        if (empty($item['img']))
            return false;

        // already saved? dublicate image file
        if (isset($item['img_file']) && $item['img_file'])
        {
            $img_file = ImageHelper::getFullImgPath($item['img_file']);
            if (!is_file($img_file))
                return false;

            $uploads = \wp_upload_dir();
            $dublicate_name = \wp_unique_filename($uploads['path'], basename($item['img_file']));
            $dublicate_file = $uploads['path'] . '/' . $dublicate_name;

            if (!copy($img_file, $dublicate_file))
                return false;

            return $dublicate_file;
        } else
        {
            // save image localy
            $file_name = ImageHelper::saveImgLocaly($item['img'], $item['title']);
            if (!$file_name)
                return false;
            $uploads = \wp_upload_dir();
            $image = ltrim(trailingslashit($uploads['subdir']), '\/') . $file_name;
            return ImageHelper::getFullImgPath($image);
        }

        return $img_file;
    }

    public static function attachThumbnail($img_file, $post_id, $title = '')
    {
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $filetype = \wp_check_filetype(basename($img_file), null);
        $attachment = array(
            'guid' => $img_file,
            'post_mime_type' => $filetype['type'],
            'post_title' => $title,
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = \wp_insert_attachment($attachment, $img_file, $post_id);
        $attach_data = \wp_generate_attachment_metadata($attach_id, $img_file);
        \wp_update_attachment_metadata($attach_id, $attach_data);
        return \set_post_thumbnail($post_id, $attach_id);
    }

}
