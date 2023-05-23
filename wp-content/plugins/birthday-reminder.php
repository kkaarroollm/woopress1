<?php
/*
Plugin Name: Birthday Reminders
Description: sends birthday reminders
*/


register_activation_hook(__FILE__, 'schedule_birthday_reminders');

// schedule reminders
function schedule_birthday_reminders() {
    if (!wp_next_scheduled('send_birthday_reminders_event')) {
        wp_schedule_event(time(), 'daily', 'send_birthday_reminders_event');
    }
}

// send birthday reminders
function send_birthday_reminders() {
    $users = get_users(array('role' => 'customer'));

    foreach ($users as $user) {
        $user_id = $user->ID;
        $customer_email = $user->user_email;

        $email_status_14days = get_user_meta($user_id, 'birthday_email_sent_14days', true);
        $email_status_2days = get_user_meta($user_id, 'birthday_email_sent_2days', true);
        $email_status_birthday = get_user_meta($user_id, 'birthday_email_sent_birthday', true);


        $current_date = date('Y-m-d');
        $reminder_14_days = date('Y-m-d', strtotime('+14 days', strtotime($birthday)));
        $reminder_2_days = date('Y-m-d', strtotime('+2 days', strtotime($birthday)));

        if ($current_date == $reminder_14_days && $email_status_14days !== 'sent-14') {
            wp_mail($customer_email, 'Birthday Reminder - 14 days', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel sem at tellus dapibus fermentum. Aenean sodales, sem a efficitur posuere, metus quam eleifend sem, ut pulvinar dui nunc id sapien.');
            update_user_meta($user_id, 'birthday_email_sent_14days', 'sent-14');
        }

        if ($current_date == $reminder_2_days && $email_status_2days !== 'sent-2') {
            wp_mail($customer_email, 'Birthday Reminder - 2 days', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel sem at tellus dapibus fermentum. Aenean sodales, sem a efficitur posuere, metus quam eleifend sem, ut pulvinar dui nunc id sapien.');
            update_user_meta($user_id, 'birthday_email_sent_2days', 'sent-2');
        }

        if ($current_date == $birthday && $email_status_birthday !== 'sent-birthday') {
            wp_mail($customer_email, 'Happy Birthday!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel sem at tellus dapibus fermentum. Aenean sodales, sem a efficitur posuere, metus quam eleifend sem, ut pulvinar dui nunc id sapien.');
            update_user_meta($user_id, 'birthday_email_sent_birthday', 'sent-birthday');
        }
    }
}


add_action('send_birthday_reminders_event', 'send_birthday_reminders');

add_action('init', 'schedule_birthday_reminders');
