<?php

namespace ContentEgg\application\components;

/**
 * VirtualPage abstract class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
abstract class VirtualPage {

    private static $created = false;


    public function __construct()
    {
        \add_action('parse_request', array($this, 'sniffRequests'));
    }
    
    public static function initAction()
    {
        $class = get_called_class();
        new $class;
    }    

    /**
     * Page slug
     */
    abstract function getSlug();

    /**
     * Page body
     */
    abstract function getBody();

    /**
     * Page title
     */
    abstract function getTitle();

    /**
     * Custom page template
     */
    public function getTemplate()
    {
        return '';
    }

    public function sniffRequests($wp)
    {
        if (isset($wp->query_vars['pagename']))
        {
            $page = $wp->query_vars['pagename'];            
        }
        elseif (\get_option('permalink_structure'))
        {
            $home_path = parse_url( \home_url('/'), PHP_URL_PATH );            
            $page = preg_replace( "#^" . preg_quote($home_path) . "#", '', $_SERVER['REQUEST_URI']);            
            $page = parse_url($page, PHP_URL_PATH);
            $page = trim($page, '/');
        }
        else
            return;

        if ($page && $page == $this->getSlug())
        {
            $this->handleRequest($wp->query_vars);
        }
    }

    protected function handleRequest($query_vars = array())
    {
        \add_action('template_redirect', array($this, 'renderTemplate'));
        \add_filter('the_posts', array($this, 'createDummyPage'));
    }

    /**
     * Modified version of Virtual_Themed_Pages_BC class
     * @link: https://gist.github.com/brianoz/9105004
     */
    public function createDummyPage($posts)
    {
        if (self::$created)
            return $posts;
        
        /*
        if ($posts)
            return $posts;
         * 
         */
        
        // have to create a dummy post as otherwise many templates
        // don't call the_content filter
        global $wp, $wp_query;
        
        //create a fake post intance
        $p = new \stdClass;
        // fill $p with everything a page in the database would have
        $p->ID = -1;
        $p->post_author = 1;
        $p->post_date = current_time('mysql');
        $p->post_date_gmt = current_time('mysql', 1);
        $p->post_content = $this->getBody();
        $p->post_title = $this->getTitle();
        $p->post_excerpt = '';
        $p->post_status = 'publish';
        $p->ping_status = 'closed';
        $p->post_password = '';
        $p->post_name = $this->getSlug(); // slug
        $p->to_ping = '';
        $p->pinged = '';
        $p->modified = $p->post_date;
        $p->modified_gmt = $p->post_date_gmt;
        $p->post_content_filtered = '';
        $p->post_parent = 0;
        $p->guid = \get_home_url('/' . $p->post_name); // use url instead?
        $p->menu_order = 0;
        $p->post_type = 'page';
        $p->post_mime_type = '';
        $p->comment_status = 'closed';
        $p->comment_count = 0;
        $p->filter = 'raw'; // How to sanitize post fields. Accepts 'raw', 'edit', 'db', or 'display'.
        $p->ancestors = array(); // 3.6

        //$p = new \WP_Post($p); // ??
        //$GLOBALS['post'] = $p;

        // reset wp_query properties to simulate a found page
        $wp_query->is_page = TRUE;
        $wp_query->is_singular = TRUE;
        $wp_query->is_home = FALSE;
        $wp_query->is_archive = FALSE;
        $wp_query->is_category = FALSE;
        unset($wp_query->query['error']);
        $wp->query = array();
        $wp_query->query_vars['error'] = '';
        $wp_query->is_404 = FALSE;
        $wp_query->found_posts = 1;
        $wp_query->comment_count = 0;
        // -1 for current_comment displays comment if not logged in!
        $wp_query->current_comment = null;
        $wp_query->is_singular = 1;
        $wp_query->post = $p;
        $wp_query->posts = array($p);
        $wp_query->queried_object = $p;
        $wp_query->queried_object_id = $p->ID;
        $wp_query->current_post = $p->ID;
        $wp_query->post_count = 1;
        $wp_query->is_attachment = false;
        
        self::$created = true;
        
        return array($p);
    }

    public function renderTemplate()
    {
        $templates = array('page.php', 'index.php');
        $template = $this->getTemplate();
        if ($template)
            $templates = array_merge(array($template), $templates);
        \locate_template($templates, true);
        exit;
    }

}
