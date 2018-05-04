<?php

namespace ContentEgg\application\admin;

use ContentEgg\application\Plugin;
use ContentEgg\application\models\AutoblogModel;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\AutoblogScheduler;

/**
 * AutoblogController class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class AutoblogController {

    const slug = 'content-egg-autoblog';

    private $amazon_categs = array(
        'appliances' => 'Appliances',
        'mobile-apps' => 'Appstore for Android',
        'arts-crafts' => 'Arts, Crafts & Sewing',
        'automotive' => 'Automotive',
        'baby-products' => 'Baby',
        'beauty' => 'Beauty',
        'books' => 'Books',
        'photo' => 'Camera & Photo',
        'wireless' => 'Cell Phones & Accessories',
        'apparel' => 'Clothing',
        'pc' => 'Computers & Accessories',
        'electronics' => 'Electronics',
        'gift-cards' => 'Gift Cards Store',
        'grocery' => 'Grocery & Gourmet Food',
        'hpc' => 'Health & Personal Care',
        'home-garden' => 'Home & Kitchen',
        'hi' => 'Home Improvement',
        'industrial' => 'Industrial & Scientific',
        'jewelry' => 'Jewelry',
        'digital-text' => 'Kindle Store',
        'kitchen' => 'Kitchen & Dining',
        'dmusic' => 'MP3 Downloads',
        'magazines' => 'Magazines',
        'movies-tv' => 'Movies & TV',
        'music' => 'Music',
        'musical-instruments' => 'Musical Instruments',
        'office-products' => 'Office Products',
        'lawn-garden' => 'Patio, Lawn & Garden',
        'pet-supplies' => 'Pet Supplies',
        'shoes' => 'Shoes',
        'software' => 'Software',
        'sporting-goods' => 'Sports & Outdoors',
        'toys-and-games' => 'Toys & Games',
        'videogames' => 'Video Games',
        'watches' => 'Watches',
    );

    public function __construct()
    {
        \add_action('admin_menu', array($this, 'add_admin_menu'));

        if ($GLOBALS['pagenow'] == 'admin.php' && !empty($_GET['page']) && $_GET['page'] == 'content-egg-autoblog-edit')
        {
            \wp_enqueue_script('contentegg-keywords', \ContentEgg\PLUGIN_RES . '/js/keywords.js', array('jquery'), Plugin::version());
            // tabs
            \wp_enqueue_script('jquery-ui-tabs');
            \wp_enqueue_script('jquery-ui-button');
            \wp_enqueue_style('contentegg-admin-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css', false, Plugin::version, false);
        }
    }

    public function add_admin_menu()
    {
        \add_submenu_page(Plugin::slug, __('Autoblogging', 'content-egg') . ' &lsaquo; Content Egg', __('Autoblogging', 'content-egg'), 'manage_options', self::slug, array($this, 'actionIndex'));
        \add_submenu_page(Plugin::slug, __('Add autoblogging', 'content-egg') . ' &lsaquo; Content Egg', __('Add autoblogging', 'content-egg'), 'manage_options', 'content-egg-autoblog-edit', array($this, 'actionUpdate'));
        \add_submenu_page('options.php', __('Add autoblogging - bulk mode', 'content-egg') . ' &lsaquo; Content Egg', __('Add autoblogging - bulk mode', 'content-egg'), 'manage_options', 'content-egg-autoblog-edit--batch', array($this, 'actionUpdate'));
    }

    public function actionIndex()
    {
        if (!empty($_GET['action']) && $_GET['action'] == 'run')
        {
            @set_time_limit(180);
            AutoblogModel::model()->run((int) $_GET['id']);
        }
        \wp_enqueue_script('content-egg-blockUI', \ContentEgg\PLUGIN_RES . '/js/jquery.blockUI.js', array('jquery'));
        PluginAdmin::getInstance()->render('autoblog_index', array('table' => new AutoblogTable(AutoblogModel::model())));
    }

    public function actionUpdate()
    {
        if ($GLOBALS['pagenow'] == 'admin.php' && !empty($_GET['page']) && $_GET['page'] == 'content-egg-autoblog-edit--batch')
            $batch = true;
        else
            $batch = false;

        $_POST = array_map('stripslashes_deep', $_POST);

        $default = array(
            'id' => 0,
            'name' => '',
            'status' => 1,
            'run_frequency' => 86400,
            'keywords_per_run' => 1,
            'post_status' => 1,
            'user_id' => \get_current_user_id(),
            'template_body' => '',
            'template_title' => '%KEYWORD%',
            'keywords' => array(),
            'category' => \get_option('default_category'),
            'include_modules' => array(),
            'exclude_modules' => array(),
            'required_modules' => array(),
            'autoupdate_modules' => array(),
            'min_modules_count' => 1,
            'post_type' => 'post',
            'custom_field_names' => array_fill(0, 8, ''),
            'custom_field_values' => array_fill(0, 8, ''),
            'main_product' => 'min_price',
            'tags' => '',
            'config' => array('dynamic_categories' => 0, 'min_comments_count' => 0),
        );

        $message = '';
        $notice = '';

        if (!empty($_POST['nonce']) && \wp_verify_nonce($_POST['nonce'], basename(__FILE__)) && !empty($_POST['item']))
        {
            $item = array();
            $item['id'] = (int) $_POST['item']['id'];
            $item['name'] = trim(strip_tags($_POST['item']['name']));
            $item['status'] = absint($_POST['item']['status']);
            $item['keywords_per_run'] = absint($_POST['item']['keywords_per_run']);
            $item['run_frequency'] = absint($_POST['item']['run_frequency']);
            $item['post_status'] = absint($_POST['item']['post_status']);
            $item['user_id'] = absint($_POST['item']['user_id']);
            $item['template_body'] = trim(\wp_kses_post($_POST['item']['template_body']));
            $item['template_title'] = trim(\wp_strip_all_tags($_POST['item']['template_title']));
            $item['post_type'] = (isset($_POST['item']['post_type'])) ? $_POST['item']['post_type'] : null;
            $item['category'] = (isset($_POST['item']['category'])) ? (int) $_POST['item']['category'] : null;
            $item['include_modules'] = (isset($_POST['item']['include_modules'])) ? $_POST['item']['include_modules'] : array();
            $item['exclude_modules'] = (isset($_POST['item']['exclude_modules'])) ? $_POST['item']['exclude_modules'] : array();
            $item['required_modules'] = (isset($_POST['item']['required_modules'])) ? $_POST['item']['required_modules'] : array();
            $item['autoupdate_modules'] = (isset($_POST['item']['autoupdate_modules'])) ? $_POST['item']['autoupdate_modules'] : array();
            $item['min_modules_count'] = absint($_POST['item']['min_modules_count']);
            $item['keywords'] = (isset($_POST['item']['keywords'])) ? explode("\r\n", $_POST['item']['keywords']) : null;
            $item['custom_field_names'] = (isset($_POST['item']['custom_field_names'])) ? $_POST['item']['custom_field_names'] : array();
            $item['custom_field_values'] = (isset($_POST['item']['custom_field_values'])) ? $_POST['item']['custom_field_values'] : array();
            $item['main_product'] = (isset($_POST['item']['main_product'])) ? $_POST['item']['main_product'] : 'min_price';
            $item['tags'] = (isset($_POST['item']['tags'])) ? TextHelper::commaList($_POST['item']['tags']) : '';
            $item['config'] = $_POST['item']['config'];

            $redirect_url = \get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-autoblog');
            if ($batch)
            {
                $created_count = $this->createBatchAutoblog($item);
                if ($created_count === false)
                    $redirect_url = AdminNotice::add2Url($redirect_url, 'autoblog_csv_file_error', 'error');
                elseif (!$created_count)
                    $redirect_url = AdminNotice::add2Url($redirect_url, 'autoblog_create_error', 'error');
                else
                    $redirect_url = AdminNotice::add2Url($redirect_url, 'autoblog_batch_created', 'success', $created_count);
            } else
            {
                // single create mode
                $item['id'] = $this->createAutoblog($item);

                if ($item['id'])
                    $redirect_url = AdminNotice::add2Url($redirect_url, 'autoblog_saved', 'success', $item['id']);
                else
                    $redirect_url = AdminNotice::add2Url($redirect_url, 'autoblog_create_error', 'error');
            }

            // redirect to table list
            \wp_redirect($redirect_url);
            exit;
        } else
        {
            // view page
            if (isset($_GET['dublicate_id']))
            {
                $dublicate = AutoblogModel::model()->findByPk((int) $_GET['dublicate_id']);
                if ($dublicate)
                {
                    foreach ($default as $key => $val)
                    {
                        if (!isset($dublicate))
                            continue;
                        $item[$key] = $dublicate[$key];
                        if (is_array($val))
                            $item[$key] = unserialize($item[$key]);
                    }
                    $item['id'] = null;
                } else
                    $item = $default;
            } else
                $item = $default;
            if (isset($_GET['id']))
            {
                $item = AutoblogModel::model()->findByPk((int) $_GET['id']);
                if (!$item)
                {
                    $item = $default;
                    $notice = __('Autoblogging is not found', 'content-egg');
                } else
                {
                    $item['keywords'] = unserialize($item['keywords']);
                    $item['include_modules'] = unserialize($item['include_modules']);
                    $item['exclude_modules'] = unserialize($item['exclude_modules']);
                    $item['required_modules'] = unserialize($item['required_modules']);
                    $item['autoupdate_modules'] = unserialize($item['autoupdate_modules']);
                    $item['custom_field_names'] = unserialize($item['custom_field_names']);
                    $item['custom_field_values'] = unserialize($item['custom_field_values']);
                    $item['config'] = unserialize($item['config']);
                }
            }
        }
        $item['keywords'] = join("\n", $item['keywords']);

        \add_meta_box('autoblog_metabox', 'Autoblog data', array($this, 'metaboxAutoblogCreateHandler'), 'autoblog_create', 'normal', 'default');

        $item['amazon_categs'] = $this->amazon_categs;

        PluginAdmin::getInstance()->render('autoblog_edit', array(
            'item' => $item,
            'notice' => $notice,
            'message' => $message,
            'nonce' => \wp_create_nonce(basename(__FILE__)),
            'batch' => $batch
        ));
    }

    private function createAutoblog($item)
    {
        $item['keywords'] = TextHelper::prepareKeywords($item['keywords']);

        // save
        $item['id'] = AutoblogModel::model()->save($item);

        // add sheduler
        if ($item['status'])
        {
            AutoblogScheduler::addScheduleEvent('hourly', time() + 900);
        }

        return $item['id'];
    }

    private function createBatchAutoblog($item)
    {
        @set_time_limit(180);

        if (empty($_FILES['item']['name']) || empty($_FILES['item']['name']['keywords_file']))
            return false;

        $file_name = $_FILES['item']['name']['keywords_file'];
        $file_path = $_FILES['item']['tmp_name']['keywords_file'];

        // Get the file type of the upload        
        $supported_types = array('text/csv', 'text/plain');
        $arr_file_type = \wp_check_filetype(basename($file_name));
        $uploaded_type = $arr_file_type['type'];

        // Check if the type is supported. If not, throw an error.
        if (!in_array($uploaded_type, $supported_types))
            return false;

        $handle = fopen($file_path, "r");
        if (!$handle)
            return false;

        $separator = ';';

        $i = 0;
        $keywords = array();
        $category_keywords = array();
        while (($data = fgetcsv($handle, 1000, $separator)) !== false)
        {
            $num = count($data);

            // first line
            if ($i == 0)
            {
                // remove UTF-8 BOM
                if (substr($data[0], 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf))
                    $data[0] = substr($data[0], 3);

                // only keywords list
                if ($num == 1 && $item['category'] == -1)
                {
                    $item['category'] = \get_option('default_category');
                }
            }

            $data[0] = trim($data[0]);
            if (!$data[0])
                continue;

            if ($num == 1)
                $keywords[] = trim($data[0]);
            elseif ($num >= 2)
                $category_keywords[trim($data[0])][] = trim($data[1]);
            $i++;
        }
        fclose($handle);

        // create
        if ($keywords)
        {
            $item['keywords'] = $keywords;
            $id = $this->createAutoblog($item);
            if ($id)
                return 1; //1 count
            else
                return false;
        }

        // create by categ
        $created_count = 0;
        if ($category_keywords)
        {
            foreach ($category_keywords as $c_name => $keywords)
            {
                $c_name = \sanitize_text_field($c_name);
                $new_item = $item;

                // need create category
                if ($item['category'] == -1)
                {
                    // If the category already exists, it is not duplicated.The ID of the original existing category is returned without error. 
                    $c_id = \wp_create_category($c_name);
                    if (!$c_id)
                        continue;

                    $new_item['category'] = $c_id;
                }
                if ($new_item['name'])
                    $new_item['name'] .= ' - ';
                $new_item['name'] .= $c_name;

                $new_item['keywords'] = $keywords;
                $a_id = $this->createAutoblog($new_item);
                if ($a_id)
                    $created_count++;
            }
        }
        return $created_count;
    }

    /**
     * This function renders our custom meta box
     */
    public function metaboxAutoblogCreateHandler($item)
    {
        if (!isset($item['batch']))
            $batch = false;
        else
        {
            $batch = (bool) $item['batch'];
            unset($item['batch']);
        }
        PluginAdmin::getInstance()->render('_metabox_autoblog', array('item' => $item, 'batch' => $batch));
    }

}
