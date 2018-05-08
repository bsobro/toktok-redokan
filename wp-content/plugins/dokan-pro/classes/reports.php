<?php

/**
 * Dokan Pro Report Class
 *
 * @since 2.4
 *
 * @package dokan
 *
 */
class Dokan_Pro_Reports {

    /**
     * Load autometically when class inistantiate
     *
     * @since 2.4
     *
     * @uses actions|filter hooks
     */
    public function __construct() {
        add_action( 'dokan_report_content_inside_before', array( $this, 'show_seller_enable_message' ) );
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_reports_menu' ) );
        add_action( 'dokan_load_custom_template', array( $this, 'load_reports_template' ) );
        add_action( 'dokan_report_content_area_header', array( $this, 'report_header_render' ) );
        add_action( 'dokan_report_content', array( $this, 'render_review_content' ) );
        add_action( 'template_redirect', array( $this, 'handle_statement' ) );
    }

    /**
     * Export statement
     *
     * @return vois
     */
    function handle_statement() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        if ( isset( $_GET['dokan_statement_export_all'] ) ) {
            $start_date = date( 'Y-m-01', current_time('timestamp') );
            $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

            if ( isset( $_GET['start_date'] ) ) {
                $start_date = $_GET['start_date'];
            }

            if ( isset( $_GET['end_date'] ) ) {
                $end_date = $_GET['end_date'];
            }

            $filename = "Statement-".date( 'Y-m-d',time() );
            header( "Content-Type: application/csv; charset=" . get_option( 'blog_charset' ) );
            header( "Content-Disposition: attachment; filename=$filename.csv" );
            $currency = get_woocommerce_currency_symbol();
            $headers  = array(
                'date'     => __( 'Date', 'dokan' ),
                'order_id' => __( 'ID', 'dokan' ),
                'type'     => __( 'Type', 'dokan' ),
                'sales'    => __( 'Sales', 'dokan' ),
                'amount'   => __( 'Earned', 'dokan' ),
                'shipping' => __( 'Shipping', 'dokan' ),
                'tax'      => __( 'Tax', 'dokan' ),
                'balance'  => __( 'Balance', 'dokan' ),
            );

            foreach ( (array)$headers as $label ) {
                echo $label .', ';
            }

            echo "\r\n";

            //calculate opening balance
            $prev_orders     = dokan_get_seller_orders_by_date( '2010-01-01', $start_date, dokan_get_current_user_id(), dokan_withdraw_get_active_order_status() );
            $prev_refunds    = dokan_get_seller_refund_by_date( '2010-01-01', $start_date );
            $prev_wthdraws   = dokan_get_seller_withdraw_by_date( '2010-01-01', $start_date );

            $old_data = array_merge( $prev_orders, $prev_refunds, $prev_wthdraws );
            $old_statements = [];

            foreach (  $old_data as $key => $odata ) {
                $date = isset( $odata->post_date ) ? strtotime( $odata->post_date ) : strtotime( $odata->date );
                $old_statements[$date] = $odata;
            }
            ksort( $old_statements );

            $net_amount = 0;

            foreach ( $old_statements as $key => $statement ) {
                if ( isset( $statement->post_date ) ) {
                    $type          = __( 'Order', 'dokan' );
                    $url           = add_query_arg( array( 'order_id' => $statement->order_id ), dokan_get_navigation_url( 'orders' ) );
                    $id            = $statement->order_id;
                    $gross_amount  = get_post_meta( $statement->order_id, '_order_total', true );
                    $sales         = wc_price( $gross_amount );
                    $seller_amount = dokan_get_seller_amount_from_order( $statement->order_id );
                    $amount        = wc_price( $seller_amount );
                    $net_amount    = $net_amount + $seller_amount;

                    $net_amount_print = wc_price( $net_amount );
                } else if ( isset( $statement->refund_amount ) ) {
                    $type             = __( 'Refund', 'dokan' );
                    $url              = add_query_arg( array( 'order_id' => $statement->order_id ), dokan_get_navigation_url( 'orders' ) );
                    $id               = $statement->order_id;
                    $sales            = wc_price( 0 );
                    $amount           = '<span style="color: #f05025;">' . wc_price( $statement->refund_amount ) . '</span>';
                    $net_amount       = $net_amount - $statement->refund_amount;
                    $net_amount_print = wc_price( $net_amount );
                } else {
                    $type             = __( 'Withdraw', 'dokan' );
                    $url              = add_query_arg( array( 'type' => 'approved' ), dokan_get_navigation_url( 'withdraw' ) );
                    $id               = $statement->id;
                    $sales            = wc_price( 0 );
                    $amount           = '<span style="color: #f05025;">' . wc_price( $statement->amount ) . '</span>';
                    $net_amount       = $net_amount - $statement->amount;
                    $net_amount_print = wc_price( $net_amount );
                }
            }

            echo $start_date . ', ';
            echo '#' .'--' . ', ';
            echo 'Opening Balance' . ', ';
            echo '--' . ', ';
            echo '--' . ', ';
            echo '--' . ', ';
            echo '--' . ', ';
            echo $net_amount . ', ';
            echo "\r\n";

            $order     = dokan_get_seller_orders_by_date( $start_date, $end_date );
            $refund    = dokan_get_seller_refund_by_date( $start_date, $end_date );
            $withdraw = dokan_get_seller_withdraw_by_date( $start_date, $end_date );

            $table_data = array_merge( $order, $refund, $withdraw );
            $statements = [];

            foreach (  $table_data as $key => $data ) {
                $date = isset( $data->post_date ) ? strtotime( $data->post_date ) : strtotime( $data->date );
                $statements[$date] = $data;
            }

            ksort( $statements );

            foreach ( $statements as $key => $statement ) {

                if ( isset( $statement->post_date ) ) {

                    $type       = __( 'Order', 'dokan' );
                    $url        = add_query_arg( array( 'order_id' => $statement->order_id ), dokan_get_navigation_url('orders') );
                    $id         = $statement->order_id;
                    $sales      =  $statement->order_total;

                    $order_amount    = dokan_get_seller_amount_from_order( $statement->order_id, true );
                    $amount          = $order_amount['net_amount'];
                    $seller_shipping = $order_amount['shipping'];
                    $seller_tax      = $order_amount['tax'];

                    $net_amount       = $net_amount + $amount + $seller_shipping + $seller_tax;
                    $net_amount_print = $net_amount;

                } else if ( isset( $statement->refund_amount ) ) {

                    $type             = __( 'Refund', 'dokan' );
                    $url              = add_query_arg( array( 'order_id' => $statement->order_id ), dokan_get_navigation_url( 'orders' ) );
                    $id               = $statement->order_id;
                    $sales            = 0;
                    $amount           = $statement->refund_amount;
                    $net_amount       = $net_amount - $statement->refund_amount;
                    $net_amount_print = $net_amount;
                    $seller_shipping  = ' -- ';
                    $seller_tax       = ' -- ';

                } else {

                    $type       = __( 'Withdraw', 'dokan' );
                    $url        = add_query_arg( array( 'type' => 'approved' ), dokan_get_navigation_url('withdraw') );
                    $id         = $statement->id;
                    $sales      =  0;
                    $amount     = $statement->amount;
                    $net_amount = $net_amount - $statement->amount;
                    $net_amount_print =  $net_amount;
                    $seller_shipping  = ' -- ';
                    $seller_tax       = ' -- ';
                }


                echo date( 'Y-m-d', $key ) . ', ';
                echo '#' .$id . ', ';
                echo $type . ', ';
                echo $sales . ', ';
                echo $amount . ', ';
                echo $seller_shipping . ', ';
                echo $seller_tax . ', ';
                echo $net_amount_print . ', ';

                echo "\r\n";
            }

            exit();
        }
    }

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Reports();
        }

        return $instance;
    }

    /**
     * Show Seller Enable Error Message
     *
     * @since 2.4
     *
     * @return void
     */
    public function show_seller_enable_message() {
        $user_id = get_current_user_id();

        if ( ! dokan_is_seller_enabled( $user_id ) ) {
            echo dokan_seller_not_enabled_notice();
        }
    }

    /**
     * Add Report Menu
     *
     * @since 2.4
     *
     * @param array $urls
     *
     * @return array
     */
    public function add_reports_menu( $urls ) {
        $urls['reports'] = array(
            'title' => __( 'Reports', 'dokan' ),
            'icon'  => '<i class="fa fa-line-chart"></i>',
            'url'   => dokan_get_navigation_url( 'reports' ),
            'pos'   => 60,
            'permission' => 'dokan_view_report_menu'
        );

        return $urls;
    }

    /**
     * Load Report Main Template
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return void
     */
    public function load_reports_template( $query_vars ) {
        if ( isset( $query_vars['reports'] ) ) {
            if ( ! current_user_can( 'dokan_view_review_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view review page', 'dokan' ) ) );
                return;
            } else {
                dokan_get_template_part( 'report/reports', '', array( 'pro' => true ) );
                return;
            }
        }
    }

    /**
     * Render Report Header Template
     *
     * @since 2.4
     *
     * @return void
     */
    public function report_header_render() {
        dokan_get_template_part( 'report/header', '', array( 'pro' => true ) );
    }

    /**
     * Render Review Content
     *
     * @return void
     */
    public function render_review_content() {
        global $woocommerce;

        $charts  = dokan_get_reports_charts();
        $link    = dokan_get_navigation_url( 'reports' );
        $current = isset( $_GET['chart'] ) ? $_GET['chart'] : 'overview';

        dokan_get_template_part( 'report/content', '', array(
            'pro' => true,
            'charts' => $charts,
            'link' => $link,
            'current' => $current,
        ) );
    }

}
