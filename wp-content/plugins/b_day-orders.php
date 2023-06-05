<?php
/*
Plugin Name: Custom Order Generator and Birthday Reminder
Description: Generate orders and birthday reminders
*/

// Activation hook
register_activation_hook(__FILE__, 'start_custom_order_generation');

// Function to start the custom order generation
function start_custom_order_generation() {
    create_custom_orders();

    $birthday_reminder_days = get_option('custom_order_generation_birthday_reminder_days', 7);
    schedule_birthday_reminders($birthday_reminder_days);

    if (!wp_next_scheduled('custom_order_generation_event')) {
        $cron_interval = get_option('custom_order_generation_cron_interval', 'daily');
        wp_schedule_event(time(), $cron_interval, 'custom_order_generation_event');
    }
    add_action('custom_order_generation_event', 'create_custom_orders');
}

// Function to create custom orders
function create_custom_orders() {
    $product_ids = array(13, 14, 15, 16, 17);
    $user_count = get_option('custom_order_generation_user_count', 5);

    for ($i = 0; $i < $user_count; $i++) {
        $random_email = generate_random_email();
        $random_user_data = generate_random_user_data();
        $random_birthday = generate_random_birthday();

        // Check if user exists
        $username_exists = username_exists($random_user_data['username']);

        if (!$username_exists) {
            // Create new customer
            $user_id = wc_create_new_customer($random_email, $random_user_data['username']);
        } else {
            // Take ID of existing user
            $user = get_user_by('login', $random_user_data['username']);
            $user_id = $user ? $user->ID : false;
        }

        if ($user_id) {
            // Update user data
            update_user_meta($user_id, 'first_name', $random_user_data['first_name']);
            update_user_meta($user_id, 'last_name', $random_user_data['last_name']);
            update_user_meta($user_id, 'birthday', $random_birthday);

            // Create a new order for the customer
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
                    $item->save();
                    $order->add_item($item);
                }
            }

            $order->calculate_totals();

            $order->set_status('completed');
            $order->save();
        }
    }
}

// Function to generate a random email
function generate_random_email() {
    $email = substr(md5(rand()), 0, 7) . '@example.com';
    return $email;
}

// Function to generate random user data
function generate_random_user_data() {
    $first_names = array('michal', 'karol', 'maciek', 'ala', 'ela');
    $last_names = array('kot', 'pies', 'ptak', 'malpa', 'zebra');
    $phones = array('123456789', '987654321', '192837465', '918273645', '101010101');

    $first_name = $first_names[array_rand($first_names)];
    $last_name = $last_names[array_rand($last_names)];
    $phone = $phones[array_rand($phones)];
    $username = generate_random_email();

    return array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone' => $phone,
        'username' => $username
    );
}

// Function to generate a random birthday
function generate_random_birthday() {
    $start_date = strtotime('-50 years');
    $end_date = time();
    $random_timestamp = mt_rand($start_date, $end_date);
    $random_date = date('Y-m-d', $random_timestamp);

    return $random_date;
}

// Function to schedule birthday reminders
function schedule_birthday_reminders($days_before) {
    $users_with_birthdays = get_users(array(
        'meta_query' => array(
            array(
                'key' => 'birthday',
                'value' => date('m-d', strtotime('+' . $days_before . ' days')),
                'compare' => '=',
            ),
        ),
    ));

    foreach ($users_with_birthdays as $user) {
        $user_id = $user->ID;
        $user_email = $user->user_email;
        $user_first_name = $user->first_name;
        $user_birthday = get_user_meta($user_id, 'birthday', true);

        $reminder_subject = get_option('custom_order_generation_birthday_reminder_subject', 'Birthday Reminder');
        $reminder_content = get_option('custom_order_generation_birthday_reminder_content', 'Happy birthday, {{name}}!');

        $reminder_content = str_replace('{{name}}', $user_first_name, $reminder_content);

        wp_schedule_single_event(strtotime('today midnight'), 'custom_order_generation_birthday_reminder', array(
            'user_id' => $user_id,
            'user_email' => $user_email,
            'reminder_subject' => $reminder_subject,
            'reminder_content' => $reminder_content
        ));
    }
}

// Function to send birthday reminder email
function send_birthday_reminder_email($user_id, $user_email, $reminder_subject, $reminder_content) {
    wp_mail($user_email, $reminder_subject, $reminder_content);
}

// Admin settings page
add_action('admin_menu', 'custom_order_generation_settings_page');

function custom_order_generation_settings_page() {
    add_menu_page(
        'Orders and Birthdays',
        'Orders and Birthdays',
        'manage_options',
        'custom_order_generation_settings',
        'custom_order_generation_settings_page_content',
        'dashicons-tickets',
        20
    );
}

function custom_order_generation_settings_page_content() {
    // Check if user has permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['custom_order_generation_save_settings'])) {
        update_option('custom_order_generation_user_count', absint($_POST['custom_order_generation_user_count']));
        update_option('custom_order_generation_cron_interval', sanitize_text_field($_POST['custom_order_generation_cron_interval']));
        update_option('custom_order_generation_birthday_reminder_days', absint($_POST['custom_order_generation_birthday_reminder_days']));
        update_option('custom_order_generation_birthday_reminder_subject', sanitize_text_field($_POST['custom_order_generation_birthday_reminder_subject']));
        update_option('custom_order_generation_birthday_reminder_content', sanitize_textarea_field($_POST['custom_order_generation_birthday_reminder_content']));
    }

    // Render settings form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Order Generation</th>
                    <td>
                        <label for="custom_order_generation_user_count">Number of Users to Generate Orders for:</label>
                        <input type="number" id="custom_order_generation_user_count" name="custom_order_generation_user_count" min="1" value="<?php echo esc_attr(get_option('custom_order_generation_user_count', 5)); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Cron Interval</th>
                    <td>
                        <label for="custom_order_generation_cron_interval">Cron Interval:</label>
                        <select id="custom_order_generation_cron_interval" name="custom_order_generation_cron_interval">
                            <option value="hourly" <?php selected(get_option('custom_order_generation_cron_interval', 'daily'), 'hourly'); ?>>Hourly</option>
                            <option value="2hours" <?php selected(get_option('custom_order_generation_cron_interval', 'daily'), '2hours'); ?>>Every 2 hours</option>
                            <option value="6hours" <?php selected(get_option('custom_order_generation_cron_interval', 'daily'), '6hours'); ?>>Every 6 hours</option>
                            <option value="daily" <?php selected(get_option('custom_order_generation_cron_interval', 'daily'), 'daily'); ?>>Daily</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Birthday Reminder</th>
                    <td>
                        <label for="custom_order_generation_birthday_reminder_days">Days Before Birthday to Send Reminder:</label>
                        <input type="number" id="custom_order_generation_birthday_reminder_days" name="custom_order_generation_birthday_reminder_days" min="1" value="<?php echo esc_attr(get_option('custom_order_generation_birthday_reminder_days', 7)); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Reminder Email</th>
                    <td>
                        <label for="custom_order_generation_birthday_reminder_subject">Subject:</label>
                        <input type="text" id="custom_order_generation_birthday_reminder_subject" name="custom_order_generation_birthday_reminder_subject" value="<?php echo esc_attr(get_option('custom_order_generation_birthday_reminder_subject', 'Birthday Reminder')); ?>" />
                        <br />
                        <label for="custom_order_generation_birthday_reminder_content">Content:</label>
                        <textarea id="custom_order_generation_birthday_reminder_content" name="custom_order_generation_birthday_reminder_content" rows="5"><?php echo esc_textarea(get_option('custom_order_generation_birthday_reminder_content', 'Hello {first_name}, Happy Birthday')); ?></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="custom_order_generation_save_settings" class="button-primary" value="Save Settings" /></p>
        </form>

        <h2>User Details</h2>
        <?php
        $users_with_birthdays = get_users()
        ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Birthday</th>
                    <th>Left Days</th>
                    <th>Reminder Sent</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_date = date('Y-m-d');
                $current_year = date('Y');
                foreach ($users_with_birthdays as $user) {
                    $user_id = $user->ID;
                    $user_email = $user->user_email;
                    $user_first_name = $user->first_name;
                    $user_birthday = get_user_meta($user_id, 'birthday', true);
                    $reminder_sent = wp_next_scheduled('custom_order_generation_birthday_reminder', array('user_id' => $user_id));

                    $user_birthday_year = date('Y', strtotime($user_birthday));

                    $next_birthday_year = $current_year;
                    $next_birthday = date('Y-m-d', strtotime($next_birthday_year . '-' . date('m-d', strtotime($user_birthday))));

                    // Check if the user's birthday has already passed this year
                    if (date('md', strtotime($next_birthday)) < date('md', strtotime($current_date))) {
                        // Add one year to the next birthday year
                        $next_birthday_year++;
                        $next_birthday = date('Y-m-d', strtotime($next_birthday_year . '-' . date('m-d', strtotime($user_birthday))));
                    }

                    $diff = strtotime($next_birthday) - strtotime($current_date);
                    $days_left = floor($diff / (60 * 60 * 24));


                    ?>
                    <tr>
                        <td><?php echo esc_html($user_first_name); ?></td>
                        <td><?php echo esc_html($user_email); ?></td>
                        <td><?php echo esc_html($user_birthday); ?></td>
                        <td><?php echo esc_html($days_left); ?></td>
                        <td><?php echo $reminder_sent ? 'Sent' : 'Not Sent Yet'; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}


// Birthday reminder event
add_action('custom_order_generation_birthday_reminder', 'send_birthday_reminder_email', 10, 4);
