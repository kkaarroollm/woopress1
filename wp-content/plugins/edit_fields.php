<?php
/**
 * Plugin Name: custom fields
 * Description: new fields in usar data, in checkout +  valid, birthday tag in admin panel
 */


function add_birthday_field($user) {
    ?>
    <h3><?php _e('Birthday', 'text-domain'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="birthday"><?php _e('Date of Birth', 'text-domain'); ?></label></th>
            <td>
                <input type="date" name="birthday" id="birthday" value="<?php echo esc_attr(get_user_meta($user->ID, 'birthday', true)); ?>" class="regular-text" />
                <p class="description"><?php _e('Please enter your date of birth.', 'text-domain'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_birthday_field');
add_action('edit_user_profile', 'add_birthday_field');


function save_birthday_field($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'birthday', sanitize_text_field($_POST['birthday']));
    }
}
add_action('personal_options_update', 'save_birthday_field');
add_action('edit_user_profile_update', 'save_birthday_field');



add_action('woocommerce_after_order_notes', 'add_birthday_field_to_checkout');
function add_birthday_field_to_checkout($checkout) {
    echo '<div id="birthday_field">';
    woocommerce_form_field('birthday', array(
        'type' => 'date',
        'class' => array('form-row-wide'),
        'label' => __('Birthday', 'custom-woocommerce-fields'),
        'required' => true,
    ), $checkout->get_value('birthday'));
    echo '</div>';
}

add_action('woocommerce_checkout_process', 'validate_birthday_field');
function validate_birthday_field() {
    if (empty($_POST['birthday'])) {
        wc_add_notice(__('Please enter your birthday.', 'custom-woocommerce-fields'), 'error');
    }
}


add_action('woocommerce_checkout_update_order_meta', 'save_birthday_field_to_order_meta');
function save_birthday_field_to_order_meta($order_id) {
    if (!empty($_POST['birthday'])) {
        $birthday = sanitize_text_field($_POST['birthday']);
        update_post_meta($order_id, 'customer_birthday', $birthday);
    }
}


add_action('admin_menu', 'add_birthdays_menu_item');

function add_birthdays_menu_item() {
    add_menu_page(
        __('Birthdays', 'custom-woocommerce-fields'),
        __('Birthdays', 'custom-woocommerce-fields'),
        'manage_options',
        'birthdays',
        'display_birthdays_page',
        'dashicons-calendar-alt'
    );
}



function display_birthdays_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Birthdays', 'custom-woocommerce-fields'); ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Username', 'custom-woocommerce-fields'); ?></th>
                    <th><?php _e('Name', 'custom-woocommerce-fields'); ?></th>
                    <th><?php _e('Birthday', 'custom-woocommerce-fields'); ?></th>
                    <th><?php _e('Email 14 Days', 'custom-woocommerce-fields'); ?></th>
                    <th><?php _e('Email 2 Days', 'custom-woocommerce-fields'); ?></th>
                    <th><?php _e('Email Sent', 'custom-woocommerce-fields'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $customers = get_users(array('role' => 'customer'));
                foreach ($customers as $customer) {
                    $user_id = $customer->ID;
                    $birthday = get_user_meta($user_id, 'random_birthday', true);
                    $email_sent_14days = get_user_meta($user_id, 'birthday_email_sent_14days', true);
                    $email_sent_2days = get_user_meta($user_id, 'birthday_email_sent_2days', true);
                    $email_sent_birthday = get_user_meta($user_id, 'birthday_email_sent_birthday', true);

                    $username = $customer->user_login;
                    $name = $customer->first_name . ' ' . $customer->last_name;
                    $birthday = $customer->birthday;
                    ?>
                    <tr>
                        <td><?php echo esc_html($username); ?></td>
                        <td><?php echo esc_html($name); ?></td>
                        <td><?php echo esc_html($birthday); ?></td>
                        <td><?php echo $email_sent_14days ? __('Sent', 'custom-woocommerce-fields') : __('Not Sent', 'custom-woocommerce-fields'); ?></td>
                        <td><?php echo $email_sent_2days ? __('Sent', 'custom-woocommerce-fields') : __('Not Sent', 'custom-woocommerce-fields'); ?></td>
                        <td><?php echo $email_sent_birthday ? __('Sent', 'custom-woocommerce-fields') : __('Not Sent', 'custom-woocommerce-fields'); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
