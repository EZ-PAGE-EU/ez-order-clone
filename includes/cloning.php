<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EZ_Order_Clone_Cloning' ) ) {

    class EZ_Order_Clone_Cloning {

        // Handle the clone order request
        public static function handle_clone_order() {
            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                wp_redirect( admin_url( 'edit.php?post_type=shop_order&ez_order_cloned=false&error_message=' . urlencode( __( 'You do not have permission to clone this order', 'ez-order-clone' ) ) ) );
                exit;
            }

            if ( ! isset( $_GET['order_id'] ) || empty( $_GET['order_id'] ) ) {
                wp_redirect( admin_url( 'edit.php?post_type=shop_order&ez_order_cloned=false&error_message=' . urlencode( __( 'Invalid order ID', 'ez-order-clone' ) ) ) );
                exit;
            }

            check_admin_referer( 'ez-clone-order' );

            $order_id = absint( $_GET['order_id'] );
            $order = wc_get_order( $order_id );

            if ( ! $order ) {
                wp_redirect( admin_url( 'edit.php?post_type=shop_order&ez_order_cloned=false&error_message=' . urlencode( __( 'Order does not exist', 'ez-order-clone' ) ) ) );
                exit;
            }

            $new_order = self::create_clone_order( $order );

            if ( is_wp_error( $new_order ) ) {
                wp_redirect( admin_url( 'edit.php?post_type=shop_order&ez_order_cloned=false&error_message=' . urlencode( $new_order->get_error_message() ) ) );
                exit;
            }

            wp_redirect( admin_url( 'edit.php?post_type=shop_order&ez_order_cloned=true&cloned_order_id=' . $new_order->get_id() ) );
            exit; // Redirect to the orders page with the cloned order ID
        }

        // Create a cloned order
        private static function create_clone_order( $order ) {
            $new_order = wc_create_order( array(
                'status' => 'pending',
                'customer_id' => $order->get_customer_id(),
            ) );

            if ( is_wp_error( $new_order ) ) {
                return $new_order;
            }

            // Copy products - single and variable
            foreach ( $order->get_items() as $item_id => $item ) {
                $product_id = $item->get_product_id();
                $quantity = $item->get_quantity();
                $item_data = $item->get_data();
                
                $product = wc_get_product( $product_id );

                if ( $product->is_type( 'variable' ) ) {
                    $variation_id = $item->get_variation_id();
                    $variation = wc_get_product( $variation_id );
                    $new_order->add_product( $variation, $quantity, array(
                        'subtotal' => $item_data['subtotal'],
                        'total'    => $item_data['total'],
                    ) );
                } else {
                    $new_order->add_product( $product, $quantity, array(
                        'subtotal' => $item_data['subtotal'],
                        'total'    => $item_data['total'],
                    ) );
                }
            }

            // Copy billing and shipping addresses
            $new_order->set_address( $order->get_address( 'billing' ), 'billing' );
            $new_order->set_address( $order->get_address( 'shipping' ), 'shipping' );

            // Copy fees
            foreach ( $order->get_fees() as $fee_id => $fee ) {
                $new_fee = new WC_Order_Item_Fee();
                $new_fee->set_name( $fee->get_name() );
                $new_fee->set_amount( $fee->get_amount() );
                $new_fee->set_total( $fee->get_total() );
                $new_fee->set_taxes( $fee->get_taxes() );
                $new_fee->set_tax_class( $fee->get_tax_class() );

                $new_order->add_item( $new_fee );
            }

            // Copy shipping methods
            foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
                $new_shipping = new WC_Order_Item_Shipping();
                $new_shipping->set_method_title( $shipping_item->get_method_title() );
                $new_shipping->set_method_id( $shipping_item->get_method_id() );
                $new_shipping->set_total( $shipping_item->get_total() );
                $new_shipping->set_taxes( $shipping_item->get_taxes() );

                $new_order->add_item( $new_shipping );
            }

            // Set shipping total
            $new_order->set_shipping_total( $order->get_shipping_total() );

            // Copy taxes
            foreach ( $order->get_tax_totals() as $tax_code => $tax ) {
                $new_order->add_tax( $tax );
            }

            // Copy coupons
            foreach ( $order->get_coupons() as $coupon_item_id => $coupon_item ) {
                $new_order->apply_coupon( $coupon_item->get_code() );
            }

            // Copy payment method
            $new_order->set_payment_method( $order->get_payment_method() );

            // Copy payment method title
            $payment_method_title = $order->get_payment_method_title();
            if ( ! empty( $payment_method_title ) ) {
                $new_order->set_payment_method_title( $payment_method_title );
            }
            
            // Clone custom fields
            EZ_Order_Clone_Cloning_Custom::clone_custom_fields( $order, $new_order );           

            $new_order->calculate_totals();
            $new_order->save();

            return $new_order;
        }
    }
}