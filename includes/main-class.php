<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EZ_Order_Clone' ) ) {

    class EZ_Order_Clone {

        public static function init() {
            add_filter( 'woocommerce_admin_order_actions', array( __CLASS__, 'add_clone_action_button' ), 10, 2 );
            add_action( 'admin_post_ez_clone_order', array( __CLASS__, 'handle_clone_order' ) );
            add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
            add_action( 'admin_head', array( __CLASS__, 'add_custom_order_actions_button_css' ) );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

            require_once plugin_dir_path(__FILE__) . 'cloning.php';
            require_once plugin_dir_path(__FILE__) . 'notices.php';
            require_once plugin_dir_path(__FILE__) . 'cloning_custom.php';
        }

        // Add the clone order action button in the admin order page
        public static function add_clone_action_button( $actions, $order ) {
            $clone_url = wp_nonce_url( admin_url( 'admin-post.php?action=ez_clone_order&order_id=' . $order->get_id() ), 'ez-clone-order' );
            $actions['ez-order-clone'] = array(
                'url'       => $clone_url,
                'name'      => __( 'Clone', 'ez-order-clone' ),
                'action'    => 'clone',
            );
            return $actions;
        }

        // Add custom CSS for the clone action button
        public static function add_custom_order_actions_button_css() {
            echo '<style>
                .wc-action-button-clone::after {
                    font-family: "woocommerce"!important;
                    content: "\e027";
                }
            </style>';
        }

        // Handle the clone order request
        public static function handle_clone_order() {
            EZ_Order_Clone_Cloning::handle_clone_order();
        }

        // Display admin notices
        public static function admin_notices() {
            EZ_Order_Clone_Notices::admin_notices();
        }

        // Enqueue scripts and styles
        public static function enqueue_scripts() {
            wp_enqueue_script( 'ez-clone-highlight', plugin_dir_url(__FILE__) . '../js/clone-highlight.js', array('jquery'), '1.0.0', true );
            wp_enqueue_style( 'ez-clone-highlight', plugin_dir_url(__FILE__) . '../css/styling.css' );
        }

    }
}