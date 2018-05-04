<?php

namespace ContentEgg\application\admin;

use ContentEgg\application\models\PriceAlertModel;
use ContentEgg\application\helpers\FileHelper;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\components\ModuleManager;

/**
 * ToolsController class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class ToolsController {

    const slug = 'content-egg-tools';

    public function __construct()
    {
        \add_action('admin_menu', array($this, 'actionHandler'));
    }

    public function actionHandler()
    {
        if (empty($GLOBALS['pagenow']) || $GLOBALS['pagenow'] != 'admin.php')
            return;

        if (empty($_GET['page']) || $_GET['page'] != 'content-egg-tools')
            return;

        if (!empty($_GET['action']) && $_GET['action'] == 'subscribers-export')
        {
            $this->actionSubscribersExport();
        }

        if (!empty($_GET['action']) && $_GET['action'] == 'offer-urls-export')
        {
            $this->actionOfferUrlsExport();
        }
    }

    public function actionSubscribersExport()
    {
        if (!\current_user_can('administrator'))
            die('You do not have permission to view this page.');

        $where = array();
        if (!empty($_GET['active_only']) && (bool) $_GET['active_only'])
            $where = array('where' => 'status = ' . PriceAlertModel::STATUS_ACTIVE);
        $subscribers = $total_price_alerts = PriceAlertModel::model()->findAll($where);

        $csv_arr = array();
        $ignore_fields = array('activkey', 'email', 'status');
        foreach ($subscribers as $subscriber)
        {
            $csv_line = array();
            $csv_line['email'] = $subscriber['email'];
            $csv_line['status'] = PriceAlertModel::getStatus($subscriber['status']);

            foreach ($subscriber as $key => $s)
            {
                if (in_array($key, $ignore_fields))
                    continue;
                $csv_line[$key] = $s;
            }
            
            $unsubscribe_all_url = \add_query_arg(array(
                'ceggaction' => 'unsubscribe',
                'email' => urlencode($subscriber['email']),
                'key' => urlencode($subscriber['activkey']),
                    ), \get_site_url());
            $delete_url = \add_query_arg(array(
                'ceggaction' => 'delete',
                'email' => urlencode($subscriber['email']),
                'key' => urlencode($subscriber['activkey']),
                    ), \get_site_url());
            
            $csv_line['unsubscribe_url'] = $unsubscribe_all_url;
            $csv_line['delete_url'] = $delete_url;
            
            $csv_arr[] = $csv_line;
        }
        $filename = 'subscribers-' . date('d-m-Y') . '.csv';
        FileHelper::sendDownloadHeaders($filename);
        echo FileHelper::array2Csv($csv_arr);
        exit;
    }

    public function actionOfferUrlsExport()
    {
        if (!\current_user_can('administrator'))
            die('You do not have permission to view this page.');

        if (isset($_GET['module']))
            $module_id = TextHelper::clear($_GET['module']);
        else
            die('Module param can not be empty.');

        if (!ModuleManager::getInstance()->moduleExists($module_id))
            die('The module does not exist.');

        global $wpdb;

        $sql = $wpdb->prepare('SELECT * FROM ' . $wpdb->postmeta . ' WHERE meta_key LIKE "%s"', ContentManager::META_PREFIX_DATA . $module_id);
        $results = $wpdb->get_results($sql, \ARRAY_A);

        $csv_arr = array();
        foreach ($results as $result)
        {
            if (!$data = unserialize($result['meta_value']))
                continue;

            $csv_line = array();
            $csv_line['post_id'] = $result['post_id'];
            foreach ($data as $d)
            {
                $csv_line['title'] = $d['title'];
                $csv_line['price'] = $d['price'];
                $csv_line['priceOld'] = $d['priceOld'];
                $csv_line['currencyCode'] = $d['currencyCode'];
                $csv_line['url'] = $d['url'];
                $csv_line['orig_url'] = $d['orig_url'];
                $csv_line['img'] = $d['img'];
                $csv_arr[] = $csv_line;
            }
        }
        $filename = $module_id . '-data-' . date('d-m-Y') . '.csv';
        FileHelper::sendDownloadHeaders($filename);
        echo FileHelper::array2Csv($csv_arr);
        exit;
    }

}
