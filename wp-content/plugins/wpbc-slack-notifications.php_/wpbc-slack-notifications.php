<?php
/*
Plugin Name: Booking Calendar Slack Notifications
Description: Sends Slack notifications for booking events (new, modified, approved, pending, denied, trashed, deleted) in the Booking Calendar plugin.
Version: 1.1
Author: Creative Solution Zone
*/

// Helper: Fetch full booking details from DB by booking ID
function wpbc_get_booking_details_by_id($booking_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'booking';
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE booking_id = %d", $booking_id), ARRAY_A);
    if (!$row) return array();
    // Parse form_data
    $fields = array();
    if (!empty($row['form'])) {
        $pairs = explode('~', $row['form']);
        foreach ($pairs as $pair) {
            $parts = explode('^', $pair);
            if (count($parts) >= 3) {
                $fields[$parts[1]] = $parts[2];
            }
        }
    }
    // Get resource title if possible
    $resource_title = '';
    if (!empty($row['booking_type'])) {
        $resource_title = wpbc_get_resource_title_by_id($row['booking_type']);
    }
    // Get dates
    $dates = '';
    $dates_table = $wpdb->prefix . 'bookingdates';
    $date_rows = $wpdb->get_results($wpdb->prepare("SELECT booking_date FROM $dates_table WHERE booking_id = %d ORDER BY booking_date ASC", $booking_id));
    if ($date_rows) {
        $date_list = array();
        foreach ($date_rows as $d) {
            $date_list[] = $d->booking_date;
        }
        $dates = implode(' | ', $date_list);
    }
    // Get cost if available
    $cost = isset($fields['cost']) ? $fields['cost'] : '';
    return array(
        'booking_id' => $booking_id,
        'resource_title' => $resource_title,
        'dates' => $dates,
        'cost' => $cost,
        'fields' => $fields,
    );
}

// Helper: Get resource title by ID (simple version)
function wpbc_get_resource_title_by_id($resource_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'bookingtypes';
    $title = $wpdb->get_var($wpdb->prepare("SELECT title FROM $table WHERE booking_type_id = %d", $resource_id));
    return $title ? $title : $resource_id;
}

// Helper: Get emoji for booking status
function wpbc_status_emoji($status) {
    $map = [
        'new booking' => '🆕',
        'booking pending' => '⏳',
        'pending' => '⏳',
        'booking approved' => '✅',
        'approved' => '✅',
        'booking modified' => '✏️',
        'modified' => '✏️',
        'booking trashed' => '🗑️',
        'trashed' => '🗑️',
        'booking restored' => '♻️',
        'restored' => '♻️',
        'booking deleted' => '❌',
        'deleted' => '❌',
        'booking denied' => '🚫',
        'denied' => '🚫',
    ];
    $key = strtolower(trim($status));
    return isset($map[$key]) ? $map[$key] : '';
}

// Helper: Format booking details for Slack (all fields, clean labels, with emoji)
function wpbc_format_slack_message($status, $params) {
    $emoji = wpbc_status_emoji($status);
    $msg = "*Booking Status:* $emoji $status\n";
    $msg .= "*Booking ID:* " . (isset($params['booking_id']) ? $params['booking_id'] : '') . "\n";
    $msg .= "*Resource:* " . (isset($params['resource_title']) ? $params['resource_title'] : '') . "\n";
    $msg .= "*Dates:* " . (isset($params['dates']) ? $params['dates'] : '') . "\n";
    if (isset($params['cost']) && $params['cost']) $msg .= "*Cost:* {$params['cost']}\n";
    if (isset($params['fields']) && is_array($params['fields'])) {
        foreach ($params['fields'] as $key => $value) {
            // Remove trailing numbers from field names
            $label = preg_replace('/\d+$/', '', $key);
            $label = ucwords(str_replace('_', ' ', $label));
            $msg .= "*{$label}:* {$value}\n";
        }
    }
    return $msg;
}

// 1. Slack notification sender
if (!function_exists('wpbc_send_slack_notification')) {
    function wpbc_send_slack_notification($message) {
        $webhook_url = 'https://hooks.slack.com/services/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'; // <-- Replace with your Slack webhook URL
        if (strpos($webhook_url, 'hooks.slack.com') === false) return; // Prevent accidental posts if not set
        $payload = json_encode(['text' => $message]);
        $args = [
            'body'    => $payload,
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 15,
        ];
        wp_remote_post($webhook_url, $args);
    }
}

// 2. New Booking
add_action('wpbc_track_new_booking', function($params) {
    $details = wpbc_get_booking_details_by_id($params['booking_id']);
    $msg = wpbc_format_slack_message('New Booking', $details);
    wpbc_send_slack_notification($msg);
});

// 3. Modified Booking
add_action('wpbc_track_edit_booking', function($params) {
    $details = wpbc_get_booking_details_by_id($params['booking_id']);
    $msg = wpbc_format_slack_message('Booking Modified', $details);
    wpbc_send_slack_notification($msg);
});

// 4. Approved Booking
add_action('wpbc_booking_approved', function($booking_id, $is_approved) {
    $status = $is_approved ? 'Approved' : 'Pending';
    $details = wpbc_get_booking_details_by_id($booking_id);
    $msg = wpbc_format_slack_message("Booking $status", $details);
    wpbc_send_slack_notification($msg);
}, 10, 2);

// 5. Trashed Booking
add_action('wpbc_booking_action__trash', function($booking_id, $is_trash_or_restore) {
    $status = ($is_trash_or_restore == '1') ? 'Trashed' : 'Restored';
    $details = wpbc_get_booking_details_by_id($booking_id);
    $msg = wpbc_format_slack_message("Booking $status", $details);
    wpbc_send_slack_notification($msg);
}, 10, 2);

// 6. Deleted Booking
add_action('wpbc_booking_action__delete', function($booking_id) {
    $details = wpbc_get_booking_details_by_id($booking_id);
    $msg = wpbc_format_slack_message('Booking Deleted', $details);
    wpbc_send_slack_notification($msg);
});

// 7. Approved/Pending/Denied (from admin actions)
add_action('wpbc_booking_action__approved', function($booking_id, $is_approve_or_pending) {
    $status = ($is_approve_or_pending == '1') ? 'Approved' : 'Pending/Denied';
    $details = wpbc_get_booking_details_by_id($booking_id);
    $msg = wpbc_format_slack_message("Booking $status", $details);
    wpbc_send_slack_notification($msg);
}, 10, 2);

// 8. Denied Booking (email denied)
if (!function_exists('wpbc_send_email_deny')) {
    function wpbc_send_email_deny($booking_id, $is_send_emails, $action_reason) {
        $details = wpbc_get_booking_details_by_id($booking_id);
        $msg = wpbc_format_slack_message('Booking Denied', $details);
        wpbc_send_slack_notification($msg);
    }
} 