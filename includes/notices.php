<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EZ_Order_Clone_Notices' ) ) {

    class EZ_Order_Clone_Notices {

        // Display admin notices
        public static function admin_notices() {
            if ( isset( $_GET['ez_order_cloned'] ) ) {
                if ( 'true' === $_GET['ez_order_cloned'] ) {
                    echo '<div class="notice notice-success is-dismissible">';
                    echo '<p>' . __( 'Order has been cloned', 'ez-order-clone' ) . '</p>';
                    echo '</div>';
                } elseif ( 'false' === $_GET['ez_order_cloned'] && isset( $_GET['error_message'] ) ) {
                    echo '<div class="notice notice-error is-dismissible">';
                    echo '<p>' . __( 'Order cloning failed: ', 'ez-order-clone' ) . esc_html( $_GET['error_message'] ) . '</p>';
                    echo '</div>';
                }
            }
        }
    }
}