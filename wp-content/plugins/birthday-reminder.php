<?php
/*
Plugin Name: Birthday Reminders
Description: sends birthday reminders
*/


register_activation_hook(__FILE__, 'schedule_birthday_reminders');

// schedule reminders
function schedule_birthday_reminders() {
    if (!wp_next_scheduled('send_birthday_reminders_event')) {
        // Schedule the event to run daily
        wp_schedule_event(time(), 'daily', 'send_birthday_reminders_event');
    }
}

// send birthday reminders
function send_birthday_reminders() {
    $customers = get_posts(array(
        'post_type' => 'wc_customer',
        'posts_per_page' => -1,
    ));

    foreach ($customers as $customer) {
        $customer_id = $customer->ID;
        $customer_email = get_post_meta($customer_id, '_billing_email', true);
        $customer_birthday = get_post_meta($customer_id, 'birthday', true);
        $email_status = get_post_meta($customer_id, 'email_status', true);

        if (empty($customer_birthday)) {
            continue; // Skip customers without a birthday set
        }

        $current_date = date('Y-m-d');
        $reminder_14_days = date('Y-m-d', strtotime('+14 days', strtotime($customer_birthday)));
        $reminder_2_days = date('Y-m-d', strtotime('+2 days', strtotime($customer_birthday)));

        if ($current_date == $reminder_14_days && $email_status !== 'sent-14') {
            send_email($customer_email, 'Birthday Reminder - 14 days', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel sem at tellus dapibus fermentum. Aenean sodales, sem a efficitur posuere, metus quam eleifend sem, ut pulvinar dui nunc id sapien.');
            update_post_meta($customer_id, 'email_status', 'sent-14');
        }

        if ($current_date == $reminder_2_days && $email_status !== 'sent-2') {
            send_email($customer_email, 'Birthday Reminder - 2 days', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel sem at tellus dapibus fermentum. Aenean sodales, sem a efficitur posuere, metus quam eleifend sem, ut pulvinar dui nunc id sapien.');
            update_post_meta($customer_id, 'email_status', 'sent-2');
        }

        if ($current_date == $customer_birthday && $email_status !== 'sent-birthday') {
            send_email($customer_email, 'Happy Birthday!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel sem at tellus dapibus fermentum. Aenean sodales, sem a efficitur posuere, metus quam eleifend sem, ut pulvinar dui nunc id sapien.');
            update_post_meta($customer_id, 'email_status', 'sent-birthday');
        }
    }
}

// email to customer
function send_email($to, $subject, $message) {
    wp_mail($to, $subject, $message);
}


add_action('send_birthday_reminders_event', 'send_birthday_reminders');

add_action('init', 'schedule_birthday_reminders');
