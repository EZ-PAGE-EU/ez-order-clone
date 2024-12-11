<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EZ_Order_Clone_Cloning_Custom' ) ) {

    class EZ_Order_Clone_Cloning_Custom {

        public static function clone_custom_fields( $order, $new_order ) {
            
            // Copy custom field billing_nip
            $billing_nip = $order->get_meta('_billing_nip');
            if ( ! empty( $billing_nip ) ) {
                $new_order->update_meta_data('_billing_nip', $billing_nip);
            }
        }
    }
}