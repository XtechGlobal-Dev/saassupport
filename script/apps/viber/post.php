<?php

/*
 * ==========================================================
 * VIBER POST.PHP
 * ==========================================================
 *
 * Viber response listener. This file receive the messages sent to the Viber bot. This file requires the Viber App.
 * © 2017-2025 board.support. All rights reserved.
 *
 */

$raw = file_get_contents('php://input');
flush();
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
$response = json_decode($raw, true);
if ($response) {
    require('../../include/functions.php');
    if (isset($response['event']) && $response['event'] == 'message') {
        $GLOBALS['SB_FORCE_ADMIN'] = true;
        if (sb_is_cloud()) {
            sb_cloud_load_by_url();
            sb_cloud_membership_validation(true);
        }
        $sender = $response['sender'];
        $viber_id = $sender['id'];
        $message = $response['message'];
        $message_text = sb_isset($message, 'text', '');
        $attachments = [];
        $token = sb_get_multi_setting('viber', 'viber-token');
        $user_id = false;

        // User and conversation
        $user = sb_get_user_by('viber-id', $viber_id);
        if (!$user) {
            $extra = ['viber-id' => [$viber_id, 'Viber ID']];
            if (!empty($sender['country'])) {
                $country_codes = sb_get_json_resource('json/country_codes.json');
                if (isset($country_codes[$sender['country']])) {
                    $extra['country'] = [$country_codes[$sender['country']], 'Country'];
                }
            }
            if (!empty($sender['language'])) {
                $extra['language'] = [sb_language_code($sender['language']), 'Language'];
            } else if ($message_text && defined('SB_DIALOGFLOW'))
                $extra['language'] = sb_google_language_detection_get_user_extra($message_text);
            $user_id = sb_add_user(['first_name' => $sender['name'], 'last_name' => '', 'profile_image' => empty($sender['avatar']) ? '' : sb_download_file($sender['avatar']), 'user_type' => 'lead'], $extra);
            $user = sb_get_user($user_id);
        } else {
            $user_id = $user['id'];
            $conversation_id = sb_isset(sb_db_get('SELECT id FROM sb_conversations WHERE source = "vb" AND user_id = ' . $user_id . ' ORDER BY id DESC LIMIT 1'), 'id');
        }
        $GLOBALS['SB_LOGIN'] = $user;
        $is_routing = sb_routing_is_active();
        if (!$conversation_id) {
            $department = sb_get_setting('viber-department');
            $conversation_id = sb_isset(sb_new_conversation($user_id, 2, '', $department, $is_routing ? sb_routing_find_best_agent($department) : -1, 'vb', $chat_id), 'details', [])['id'];
        } else if ($is_routing && sb_isset(sb_db_get('SELECT status_code FROM sb_conversations WHERE id = ' . $conversation_id), 'status_code') == 3) {
            sb_update_conversation_agent($conversation_id, sb_routing_find_best_agent($department));
        }

        // Attachments
        switch ($message['type']) {
            case 'file':
            case 'video':
            case 'picture':
                array_push($attachments, [$message['file_name'], sb_download_file($message['media'], $message['file_name'])]);
                break;
            case 'sticker':
                array_push($attachments, ['', $message['media']]);
                break;
            case 'location':
                $message_text .= ($message_text ? PHP_EOL : '') . 'https://www.google.com/maps/search/?api=1&query=' . $message['location']['lat'] . ',' . $message['location']['lon'];
                break;
        }

        // Send message
        $response = sb_send_message($user_id, $conversation_id, $message_text, $attachments, false, json_encode(['message_token' => $response['message_token']]));

        // Dialogflow, Notifications, Bot messages
        $response_external = sb_messaging_platforms_functions($conversation_id, $message_text, $attachments, $user, ['source' => 'vb', 'viber_id' => $viber_id]);

        // Queue
        sb_queue_check_and_run($conversation_id, $department, 'vi');

        // Online status
        sb_update_users_last_activity($user_id);

        $GLOBALS['SB_FORCE_ADMIN'] = false;
    }
}
die();

?>