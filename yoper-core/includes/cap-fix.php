<?php
if (!defined('ABSPATH')) {
    exit;
}

// Guard to avoid WP 6.1+ warnings when delete_post/read_post are checked without a post ID.
add_filter('map_meta_cap', function ($caps, $cap, $user_id, $args) {
    if (in_array($cap, array('delete_post', 'read_post'), true)) {
        if (empty($args) || empty($args[0])) {
            return array($cap);
        }
    }

    return $caps;
}, 10, 4);

// Suppress _doing_it_wrong noise for meta caps without post ID (defensive when other code calls current_user_can incorrectly).
add_filter('doing_it_wrong_trigger_error', function ($trigger, $function, $message) {
    if ('map_meta_cap' === $function) {
        if (strpos($message, 'delete_post') !== false || strpos($message, 'read_post') !== false) {
            return false;
        }
    }
    return $trigger;
}, 1, 3);
