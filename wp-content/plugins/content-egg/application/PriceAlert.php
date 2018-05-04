<?php

namespace ContentEgg\application;

use ContentEgg\application\models\PriceAlertModel;
use ContentEgg\application\helpers\InputHelper;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\models\PriceHistoryModel;
use ContentEgg\application\components\ContentManager;
use ContentEgg\application\helpers\TemplateHelper;
use ContentEgg\application\admin\GeneralConfig;

/**
 * PriceAlert class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class PriceAlert {

    private static $instance = null;
    private $tickbox_message;
    private $tickbox_subject;

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;

        return self::$instance;
    }

    private function __construct()
    {
        
    }

    public function init()
    {
        if (!self::isPriceAlertAllowed())
            return;

        if (\is_admin())
        {
            // anonymous visitors
            \add_action('wp_ajax_nopriv_start_tracking', array($this, 'ajaxTrackProduct'));
            // logged in users
            \add_action('wp_ajax_start_tracking', array($this, 'ajaxTrackProduct'));
        }
        \add_action('init', array($this, 'registerJs'));
        \add_action('template_redirect', array($this, 'subscriptionManager'));
    }

    public function registerJs()
    {
        \wp_enqueue_script('cegg-price-alert', \ContentEgg\PLUGIN_RES . '/js/price_alert.js', array('jquery'));
        \wp_localize_script('cegg-price-alert', 'ceggPriceAlert', array(
            'ajaxurl' => \admin_url('admin-ajax.php'),
            'nonce' => \wp_create_nonce('cegg-price-alert')
        ));
    }

    public function ajaxTrackProduct()
    {
        if (!isset($_POST['nonce']) || !\wp_verify_nonce($_POST['nonce'], 'cegg-price-alert'))
            die('Invalid nonce');

        $module_id = TextHelper::clear(InputHelper::post('module_id', null));
        $unique_id = TextHelper::clearId(InputHelper::post('unique_id', null));
        $price = (float) TextHelper::parsePriceAmount(InputHelper::post('price', null));
        $post_id = (int) InputHelper::post('post_id', null);
        $email = strtolower(TextHelper::clearId(InputHelper::post('email', null)));
        if (!$module_id || !$unique_id || !$post_id)
            die('Invalid params');

        $current = PriceHistoryModel::model()->getLastPrices($unique_id, $module_id, $limit = 1);
        if (!$current)
            die('Product not found.');
        $current = $current[0];

        // find product, check post_id
        $product = ContentManager::getProductbyUniqueId($current['unique_id'], $current['module_id'], $post_id);
        if (!$product)
            die('Product not found.');

        if (!$price || !$email)
            $this->jsonError(__('All fields are required.', 'content-egg-tpl'));

        if (!\is_email($email))
            $this->jsonError(__('Your email address is invalid.', 'content-egg-tpl'));

        if ($price >= $current['price'])
            $this->jsonError(__('The price has already been reached.', 'content-egg-tpl'));

        // dublicate?
        $where = array(
            'unique_id = %s AND module_id = %s AND email = %s AND status != %d',
            array($unique_id, $module_id, $email, PriceAlertModel::STATUS_DELETED)
        );
        if (PriceAlertModel::model()->find(array('where' => $where)))
            $this->jsonError(__('You already tracking this product.', 'content-egg-tpl'));

        $alert = array(
            'unique_id' => $current['unique_id'],
            'module_id' => $current['module_id'],
            'post_id' => $post_id,
            'email' => $email,
            'price' => $price,
            'start_price' => $current['price'],
            'status' => PriceAlertModel::STATUS_INACTIVE,
            'activkey' => TextHelper::randomPassword(16),
        );

        // save
        if (PriceAlertModel::model()->save($alert))
        {
            // email
            $this->sendActivationEmail($email, $product, $alert);
            $this->jsonResult(__('We are now tracking this product for you. Please verify your email address to be notified of price drops.', 'content-egg-tpl'), 'success');
        } else
            $this->jsonError(__('Internal Error. Please notify the administrator.', 'content-egg-tpl'));
        exit;
    }

    private function sendActivationEmail($email, $product, $alert)
    {
        $subject = sprintf(__('Welcome to %s', 'content-egg-tpl'), \esc_html(\get_bloginfo('name')));
        $product_title = \esc_html(TextHelper::truncate($product['title']));

        $uri = \add_query_arg(array(
            'ceggaction' => 'validate',
            'email' => urlencode($email),
            'key' => urlencode($alert['activkey']),
                ), \get_permalink($alert['post_id']));
        $uri .= '#' . urlencode($alert['unique_id']);

        $body = '<p>' . __('Hello,', 'content-egg-tpl') . '<br></p>';
        $body .= '<p>' . sprintf(__('You have successfully set a price drop alert for %s.', 'content-egg-tpl'), $product_title) . '<p>';
        $body .= '<p>' . __('We will not send you any price alerts until you verified your email address.', 'content-egg-tpl');
        $body .= ' ' . sprintf(__('Please open this link to validate your email address:<br> <a href="%s">%s</a>', 'content-egg-tpl'), \esc_url($uri), \esc_url($uri)) . '</p>';
        $body .= $this->getEmailSignature();

        self::mail($email, $subject, $body);
    }

    private function getEmailSignature()
    {
        return "<br><pre class=\"moz-signature\" cols=\"72\">--\r\n" . sprintf(__('Thank You,\r\n Team %s', 'content-egg-tpl'), \get_bloginfo('name')) . "</pre>";
    }

    private function jsonResult($message, $status = 'success')
    {
        header("Content-Type: application/json");
        echo json_encode(array(
            'status' => $status,
            'message' => $message
        ));
        exit;
    }

    private function jsonError($message)
    {
        $this->jsonResult($message, 'error');
        exit;
    }

    public function subscriptionManager()
    {
        if (!$action = InputHelper::get('ceggaction', null))
            return;

        switch ($action)
        {
            case 'validate':
                $this->actionValidateEmail();
                return;
            case 'unsubscribe':
                $this->actionUnsubscribeAll();
                return;
            case 'delete':
                $this->actionDeleteSubscription();
                return;
            default:
                return;
        }
    }

    private function actionValidateEmail()
    {
        $email = strtolower(TextHelper::clearId(InputHelper::get('email', null)));
        $key = TextHelper::clear(InputHelper::get('key', null));

        $where = array(
            'email = %s AND activkey = %s AND status = %d',
            array($email, $key, PriceAlertModel::STATUS_INACTIVE)
        );
        $alert = PriceAlertModel::model()->find(array('where' => $where));
        if (!$alert)
            return;
        $alert['status'] = PriceAlertModel::STATUS_ACTIVE;
        // save
        PriceAlertModel::model()->save($alert);
        // tickbox
        $this->openTickbox(__('Your email has been verified. We will let you know by email when the Price Drops.', 'content-egg-tpl'), __('Success!', 'content-egg-tpl'));
    }

    private function actionUnsubscribeAll()
    {
        $email = strtolower(TextHelper::clearId(InputHelper::get('email', null)));
        $key = TextHelper::clear(InputHelper::get('key', null));

        $where = array(
            'email = %s AND activkey = %s',
            array($email, $key)
        );
        $alert = PriceAlertModel::model()->find(array('where' => $where));
        if (!$alert)
            return;

        PriceAlertModel::model()->unsubscribeAll($alert['email']);
        $this->openTickbox(__('You are now unsubscribed from our Price Alerts via email.', 'content-egg-tpl'), __('Unsubscribed!', 'content-egg-tpl'));
    }

    private function actionDeleteSubscription()
    {
        $email = strtolower(TextHelper::clearId(InputHelper::get('email', null)));
        $key = TextHelper::clear(InputHelper::get('key', null));

        $where = array(
            'email = %s AND activkey = %s',
            array($email, $key)
        );
        $alert = PriceAlertModel::model()->find(array('where' => $where));
        if (!$alert)
            return;
        // save
        PriceAlertModel::model()->delete($alert['id']);
        // tickbox
        $this->openTickbox(__('Your subscription has been deleted from our database.', 'content-egg-tpl'), __('Success!', 'content-egg-tpl'));
    }

    public function openTickbox($message, $subject = "")
    {
        $this->tickbox_message = strip_tags($message);
        $this->tickbox_subject = strip_tags($subject);
        \add_thickbox();
        \add_action('wp_footer', array($this, 'tickboxInlineScript'));
    }

    public function tickboxInlineScript()
    {
        echo '<script>
            jQuery(window).load(function()
            {
                jQuery("body").append("<div id=\"cegg-price-alert-tickbox\"><p>' . \esc_js($this->tickbox_message) . '<div style=\"text-align:center; padding-top: 30px;padding-right: 20px;\"><input value=\"' . esc_js(__('  Ok  ', 'content-egg-tpl')) . '\" type=\"button\" onclick=\"javascript:tb_remove()\"></div></p></div>");
                tb_show("' . esc_js($this->tickbox_subject) . '", "#TB_inline?height=200&amp;width=300&amp;inlineId=cegg-price-alert-tickbox", false);
            });</script>';
    }

    public static function mail($to, $subject, $message, $headers = '', $attachments = array())
    {
        \add_filter('wp_mail_content_type', array(__CLASS__, 'setMailContentType'));
        if (GeneralConfig::getInstance()->option('from_email'))
            \add_filter('wp_mail_from', array(__CLASS__, 'setMailFrom'));
        if (GeneralConfig::getInstance()->option('from_name'))
            \add_filter('wp_mail_from_name', array(__CLASS__, 'setMailFromName'));

        \wp_mail($to, $subject, $message, $headers, $attachments);

        \remove_filter('wp_mail_content_type', 'setMailContentType');
        \remove_filter('wp_mail_from', 'setMailFrom');
        \remove_filter('wp_mail_from_name', 'setMailFromName');
    }

    public static function setMailContentType()
    {
        return 'text/html';
    }

    public static function setMailFrom()
    {
        return GeneralConfig::getInstance()->option('from_email');
    }

    public static function setMailFromName()
    {
        return GeneralConfig::getInstance()->option('from_name');
    }

    public function sendAlerts(array $data, $module_id, $post_id)
    {
        $total = 0;
        foreach ($data as $key => $d)
        {
            if (empty($d['unique_id']) || empty($d['price']))
                continue;

            // Price drops?
            $previous_price = PriceHistoryModel::model()->getPreviousPriceValue($d['unique_id'], $module_id);
            if (!$previous_price || (float) $previous_price <= (float) $d['price']) //!!!!!
                continue;

            // Subscribers exist?
            $params = array(
                'where' => array('unique_id=%s AND module_id=%s AND status=%d AND price >= %f', array($d['unique_id'], $module_id, PriceAlertModel::STATUS_ACTIVE, $d['price'])),
            );
            $subscribers = PriceAlertModel::model()->findAll($params);
            if (!$subscribers)
                continue;

            $total += count($total);
            $this->sendAlertEmails($subscribers, $d, $post_id);
        }

        // clean up & optimize
        if ($total && rand(1, 5) == 5)
        {
            PriceAlertModel::model()->cleanOld(PriceAlertModel::CLEAN_DELETED_DAYS);
        }
    }

    private function sendAlertEmails($alerts, $product, $post_id)
    {
        foreach ($alerts as $alert)
        {
            $product_title = \esc_html(TextHelper::truncate($product['title']));
            $subject = sprintf(__('Price alert: "%s"', 'content-egg-tpl'), $product_title);
            $post_url = \get_permalink($post_id) . '#' . urlencode($product['unique_id']);

            $unsubscribe_url = \add_query_arg(array(
                'ceggaction' => 'unsubscribe',
                'email' => urlencode($alert['email']),
                'key' => urlencode($alert['activkey']),
                    ), \get_site_url());

            $desired_price = TemplateHelper::formatPriceCurrency($alert['price'], $product['currencyCode']);
            $current_price = TemplateHelper::formatPriceCurrency($product['price'], $product['currencyCode']);
            $start_price = TemplateHelper::formatPriceCurrency($alert['start_price'], $product['currencyCode']);
            $saved_amount = round($alert['start_price'] - $product['price'], 2);
            $saved_amount = TemplateHelper::formatPriceCurrency($saved_amount, $product['currencyCode']);
            $saved_percentage = round(100 - (100 * $product['price']) / $alert['start_price'], 2);

            $body = '<p>' . __('Good news!', 'content-egg-tpl') . '<br></p>';
            $body .= '<p>' . __('The price target you set for the item has been reached.', 'content-egg-tpl');
            $body .= '<p>' . sprintf(__('<a href="%s">Save %s (%s%%) on %s</a>', 'content-egg-tpl'), $post_url, $saved_amount, $saved_percentage, $product_title);
            $body .= '<ul>';
            $body .= '<li>' . sprintf(__('Desired Price: %s', 'content-egg-tpl'), $desired_price) . '</li>';
            $body .= '<li>' . sprintf(__('Current Price: <strong>%s</strong>', 'content-egg-tpl'), $current_price)
                    . ' (' . __('as of', 'content-egg-tpl') . ' ' . TemplateHelper::getLastUpdateFormatted($alert['module_id'], $post_id) . ')</li>';
            $body .= '<li>' . sprintf(__('Price dropped from %s to %s', 'content-egg-tpl'), $start_price, $current_price) . '</li>';
            $body .= '</ul><br>';
            $body .= sprintf(__('<a href="%s">More info...</a>', 'content-egg-tpl'), $post_url);
            $body .= '</p><br>';

            $body .= '<p>' . sprintf(__('This present alert has now expired. You may <a href="%s">create a new alert</a> for this item.', 'content-egg-tpl'), $post_url);
            $body .= '<br>' . sprintf(__('If you don\'t want to receive any price alerts from us in the future, <a href="%s">please click here</a>.', 'content-egg-tpl'), $unsubscribe_url) . '</p>';
            $body .= $this->getEmailSignature();

            // send alert email
            self::mail($alert['email'], $subject, $body);

            // delete alert
            $alert['status'] = PriceAlertModel::STATUS_DELETED;
            $alert['complet_date'] = \current_time('mysql');
            PriceAlertModel::model()->save($alert);
        }
    }

    public static function isPriceAlertAllowed($unique_id = null, $module_id = null)
    {
        if (!GeneralConfig::getInstance()->option('price_history_days'))
            return false;

        if (!GeneralConfig::getInstance()->option('price_alert_enabled'))
            return false;

        if ($unique_id && $module_id)
        {
            if (!PriceHistoryModel::model()->getLastPrices($unique_id, $module_id, 1))
                return false;
        }
        return true;
    }

}
