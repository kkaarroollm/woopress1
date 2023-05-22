<?php
/*
Plugin Name: Custom Order Generator
Description: generate 5 orders hourly with 5 products
*/


register_activation_hook(__FILE__, 'start_custom_order_generation');


function start_custom_order_generation() {
    // schedule the custom order generation to run every hour
    if (!wp_next_scheduled('custom_order_generation_event')) {
        wp_schedule_event(time(), 'hourly', 'custom_order_generation_event');
    }

    add_action('custom_order_generation_event', 'create_custom_orders');
}


function create_custom_orders() {
    $product_ids = array(13, 14, 15, 16, 17);
    $user_count = 5;

    for ($i = 0; $i < $user_count; $i++) {
        $random_email = generate_random_email();
        $random_user_data = generate_random_user_data();
        $random_birthday = generate_random_birthday();

        // check if user exists
        $username_exists = username_exists($random_user_data['username']);

        if (!$username_exists) {
            // create new customer
            $user_id = wc_create_new_customer($random_email, $random_user_data['username']);
        } else {
            // take id of exisitng user
            $user = get_user_by('login', $random_user_data['username']);
            $user_id = $user ? $user->ID : false;
        }

        if ($user_id) {
            // update user data
            update_user_meta($user_id, 'first_name', $random_user_data['first_name']);
            update_user_meta($user_id, 'last_name', $random_user_data['last_name']);
            update_user_meta($user_id, 'birthday', $random_birthday);

            // new order for customer
            $order = wc_create_order();

            $order->set_customer_id($user_id);
            $order->set_billing_email($random_email);
            $order->set_billing_first_name($random_user_data['first_name']);
            $order->set_billing_last_name($random_user_data['last_name']);
            $order->set_billing_phone($random_user_data['phone']);

            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);

                if ($product) {
                    $item = new WC_Order_Item_Product();
                    $item->set_props(array(
                        'product' => $product,
                        'quantity' => 1,
                        'total' => $product->get_price()
                    ));
                    $item->set_backorder_meta();
                    $order->add_item($item);
                }
            }

            $order->calculate_totals();

            $order->set_status('completed');
            $order->save();
        }
    }
}


add_action('init', 'start_custom_order_generation');


function generate_random_email() {
    $email = substr(md5(rand()), 0, 7) . '@example.com';
    return $email;
}


function generate_random_user_data() {
    $first_names = array('michal', 'karol', 'maciek', 'ala', 'ela');
    $last_names = array('kot', 'pies', 'ptak', 'malpa', 'zebra');
    $phones = array('123456789', '987654321', '192837465', '918273645', '101010101');

    $first_name = $first_names[array_rand($first_names)];
    $last_name = $last_names[array_rand($last_names)];
    $phone = $phones[array_rand($phones)];
    $username = substr($email, 0, strpos($email, '@'));

    return array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone' => $phone,
        'username' => $username
    );
}

function generate_random_birthday() {
    $start_date = strtotime('-50 years');
    $end_date = time();
    $random_timestamp = mt_rand($start_date, $end_date);
    $random_date = date('Y-m-d', $random_timestamp);

    return $random_date;
}
