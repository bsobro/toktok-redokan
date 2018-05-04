<?php

namespace ContentEgg\application;

use ContentEgg\application\admin\GeneralConfig;
use ContentEgg\application\helpers\CurrencyHelper;

/**
 * Plugin class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class Plugin {

    const version = '4.5.0';
    const db_version = 36;
    const wp_requires = '4.6.0';
    const slug = 'content-egg';
    const api_base = 'http://www.keywordrush.com/api/v1';
    const api_base2 = 'http://67.225.139.212/~srvrush/api/v1';
    const product_id = 302;

    private static $instance = null;
    private static $is_pro = null;
    private static $is_envato = null;

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;

        return self::$instance;
    }
	
    private function __construct()
    {
        $this->loadTextdomain();
        if (self::isFree() || (self::isPro() && self::isActivated()) || self::isEnvato())
        {
            if (!\is_admin())
            {
                \add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
                \add_action('amp_post_template_css', array($this, 'registerAmpStyles'));
                EggShortcode::getInstance();
                BlockShortcode::getInstance();
                ModuleViewer::getInstance()->init();
                ModuleUpdateVisit::getInstance()->init();
                LocalRedirect::initAction();
                CurrencyHelper::getInstance(GeneralConfig::getInstance()->option('lang'));
                ProductSearch::initAction();
            }
            PriceAlert::getInstance()->init();
            AutoblogScheduler::initAction();
            ModuleUpdateScheduler::initAction();
            WooIntegrator::initAction();
            new ProductSearchWidget;
            new PriceMoversWidget;            
        }
    }

    public function registerScripts()
    {
        \wp_register_style('egg-bootstrap', \ContentEgg\PLUGIN_RES . '/bootstrap/css/egg-bootstrap.css');
        \wp_register_script('bootstrap', \ContentEgg\PLUGIN_RES . '/bootstrap/js/bootstrap.min.js', array('jquery'), null, false);
        \wp_register_style('egg-products', \ContentEgg\PLUGIN_RES . '/css/products.css');
        \wp_register_script('raphaeljs', \ContentEgg\PLUGIN_RES . '/js/morrisjs/raphael.min.js', array('jquery'));
        \wp_register_script('morrisjs', \ContentEgg\PLUGIN_RES . '/js/morrisjs/morris.min.js', array('raphaeljs'));
        \wp_register_style('morrisjs', \ContentEgg\PLUGIN_RES . '/js/morrisjs/morris.min.css');
    }

    static public function version()
    {
        return self::version;
    }

    static public function slug()
    {
        return self::slug;
    }

    public static function getApiBase()
    {
        return self::api_base;
    }

    public static function isFree()
    {
        return !self::isPro();
    }

    public static function isPro()
    {
        if (self::$is_pro === null)
        {
            if (class_exists("\\ContentEgg\\application\\Autoupdate", true))
                self::$is_pro = true;
            else
                self::$is_pro = false;
        }
        return self::$is_pro;
    }

    public static function isEnvato()
    {
        if (self::$is_envato === null)
        {
            if (class_exists("\\ContentEgg\\application\\admin\\EnvatoConfig", true) || \get_option(Plugin::slug . '_env_install'))
                self::$is_envato = true;
            else
                self::$is_envato = false;
        }
        return self::$is_envato;
    }

    public static function isActivated()
    {
        if (self::isPro() && \ContentEgg\application\admin\LicConfig::getInstance()->option('license_key'))
            return true;
        else
            return false;
    }

    public static function isInactiveEnvato()
    {
        if (self::isEnvato() && !self::isActivated())
            return true;
        else
            return false;
    }

    public static function apiRequest($params = array())
    {
        $api_urls = array(self::api_base);
        if (self::api_base2)
            $api_urls[] = self::api_base2;

        foreach ($api_urls as $api_url)
        {
            $response = \wp_remote_post($api_url, $params);
            if (\is_wp_error($response))
                continue; // try alternative api uri

            $response_code = (int) \wp_remote_retrieve_response_code($response);
            if ($response_code == 200)
                return $response;
            else
                return false;
        }
        return false;
    }

    private function loadTextdomain()
    {
        // plugin admin
        \load_plugin_textdomain('content-egg', false, dirname(\plugin_basename(\ContentEgg\PLUGIN_FILE)) . '/languages/');

        // frontend templates
        $lang = GeneralConfig::getInstance()->option('lang');
        $mo_files = array(
            \trailingslashit(WP_LANG_DIR) . 'plugins/content-egg-tpl-' . $lang . '.mo', // wp lang dir
        );
        if (defined('LOCO_LANG_DIR'))
            $mo_files[] = \trailingslashit(LOCO_LANG_DIR) . 'plugins/content-egg-tpl-' . $lang . '.mo'; // loco lang dir
        $mo_files[] = \ContentEgg\PLUGIN_PATH . 'languages/tpl/content-egg-tpl-' . strtoupper($lang) . '.mo'; // plugin lang dir
        foreach ($mo_files as $mo_file)
        {
            if (file_exists($mo_file) && is_readable($mo_file))
            {
                if (\load_textdomain('content-egg-tpl', $mo_file))
                    return;
            }
        }
        // last chance load tpl .mo
        //\load_plugin_textdomain('content-egg-tpl', false, dirname(\plugin_basename(\ContentEgg\PLUGIN_FILE)) . '/languages/tpl/');
    }

    public static function getPluginDomain()
    {
        $url = 'http://www.keywordrush.com/';
        if (!in_array(\get_locale(), array('ru_RU', 'uk')))
            $url .= 'en/';
        return $url;
    }
    
    public static function pluginSiteUrl()
    {
        return self::getPluginDomain() . 'contentegg';
    }
    
    public static function pluginDocsUrl()
    {
        return self::getPluginDomain() . 'docs/content-egg/';
    }

    public function registerAmpStyles()
    {
        echo '.egg-container .btn,.egg-container .cegg-price{white-space:nowrap;font-weight:700}.cegg-couponcode,.cegg-gridbox a{text-decoration:none}.egg-container .cegg-gridbox{box-shadow:0 8px 16px -6px #eee;border:1px solid #ddd;margin-bottom:25px;padding:20px}.egg-container .egg-listcontainer .row-products>div{margin-bottom:12px}.egg-container .btn{display:inline-block;padding:7px 14px;margin-bottom:0;font-size:14px;line-height:1.42857143;text-align:center;vertical-align:middle;touch-action:manipulation;cursor:pointer;user-select:none;background-image:none;border:1px solid transparent;border-radius:4px}.egg-container .btn-success{color:#fff;background-color:#5cb85c;border-color:#4cae4c;text-decoration:none}.egg-container .panel-default{border:1px solid #ddd;padding:20px}.cegg-price-alert-wrap,.cegg-price-tracker-item div[id$=chart]{display:none}.cegg-price-tracker-panel .btn{margin-bottom:6px}.egg-container .cegg-no-top-margin{margin-top:0}.egg-container .cegg-mb5{margin-bottom:5px}.egg-container .cegg-mb10{margin-bottom:10px}.egg-container .cegg-mb15{margin-bottom:15px}.egg-container .cegg-mb20{margin-bottom:20px}.egg-container .cegg-mb25{margin-bottom:25px}.egg-container .cegg-mb30{margin-bottom:30px}.egg-container .cegg-mb35{margin-bottom:35px}.egg-container .cegg-lineh-20{line-height:20px}.egg-container .cegg-mr10{margin-right:10px}.egg-container .cegg-mr5{margin-right:5px}.egg-container .btn.cegg-btn-big{padding:13px 60px;line-height:1;font-size:20px;font-weight:700}.cegg-couponcode{text-align:center;background:#efffda;padding:8px;display:block;border:2px dashed #5cb85c;margin-bottom:12px}.cegg-bordered-box{border:2px solid #ededed;padding:25px}.cegg-price-tracker-item .cegg-price{font-size:22px;font-weight:700}.egg-list-coupons .btn{font-size:16px;font-weight:700;display:block}.cegg-listlogo-title{line-height:18px;font-size:15px}.cegg-list-withlogos .cegg-price,.egg-listcontainer .cegg-price{font-weight:700;font-size:20px;color:#5aaf0b}.egg-container .cegg-list-withlogos .btn{font-weight:700;font-size:15px;padding:8px 16px}.cegg-price-row strike{opacity:.42;font-size:90%}.cegg-list-logo-title{font-weight:700;font-size:17px}.egg-container .cegg-btn-grid .btn{display:block;margin-bottom:10px}#cegg_market .cegg-image-container img{max-height:350px}.cegg-review-block{padding:20px;border:1px solid #eee}.cegg-line-hr{clear:both;border-top:1px solid #eee;height:1px}.amp-wp-article-content .cegg-btn-row amp-img,.amp-wp-article-content .cegg-desc-cell amp-img,.amp-wp-article-content .cegg-price-tracker-panel .cegg-mb5 amp-img,.amp-wp-article-content .producttitle amp-img{display:inline-block;margin:0 4px 0 0;vertical-align:middle}.egg-container .cegg-promotion{top:25px;left:0;position:absolute;z-index:10}.egg-container .cegg-discount{background-color:#eb5e58;border-radius:0 4px 4px 0;color:#fff;display:inline-block;font-size:16px;padding:3px 5px}.cegg-thumb{position:relative}';
    }

}
