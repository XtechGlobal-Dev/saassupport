<?php
use UI\Draw\Color;

/*
 * ==========================================================
 * FUNCTIONS_SETTINGS.PHP
 * ==========================================================
 *
 * Settings functions file. © 2017-2025 board.support. All rights reserved.
 *
 * -----------------------------------------------------------
 * SETTINGS
 * -----------------------------------------------------------
 * 1. Return the JS settings of the front-end
 * 2. Return the JS settings of admin area
 * 3. Return the JS settings shared by both the admin and the front-end
 * 4. Return the JS settings of block message
 * 5. Populate the admin area with the settings of the file /resources/json/settings.json
 * 6. Pupulate the admin area of the apps
 * 7. Return the HTML code of a setting element
 * 8. Save the all settings and external settings
 * 9. Save an external setting
 * 10. Return the settings array
 * 11. Return all settings and external settings
 * 12. Return the setting with the given name
 * 13. Return a single setting of a multi values setting
 * 14. Return the external setting with the given name
 * 15. Return a multilingual external setting
 * 16. Return the HTML code of the color palette
 * 17. Export all settings and external settings
 * 18. Import all settings and external settings
 * 19. Return the departments array
 * 20. Echo the departments list
 * 21. Check if the current time is within the office hours
 * 22. Generate the CSS with values setted in the settings area
 * 23. Check the system for requirements and issues
 * 24. Countries list
 * 25. Langauges list
 * 26. Phone codes list
 * 27. Get config file settings
 * 28. Update the service worker file
 *
 */

function sb_get_front_settings() {
    global $SB_LANGUAGE;
    sb_updates_validation();
    $active_user = sb_get_active_user();
    $is_office_hours = sb_office_hours();
    if (sb_get_setting('front-auto-translations') && sb_get_active_user()) {
        $SB_LANGUAGE = [sb_get_user_language(sb_get_active_user_id()), 'front'];
    }
    $return = [
        'language' => sb_get_setting('front-auto-translations'),
        'translations' => sb_get_current_translations(),
        'registration_required' => sb_get_setting('registration-required'),
        'registration_otp' => sb_get_setting('registration-otp'),
        'registration_timetable' => sb_get_setting('registration-timetable'),
        'registration_offline' => sb_get_setting('registration-offline'),
        'registration_link' => sb_get_setting('registration-link', ''),
        'registration_details' => sb_get_setting('registration-user-details-success'),
        'visitors_registration' => sb_get_setting('visitors-registration') || sb_get_setting('online-users-notification'),
        'privacy' => sb_get_multi_setting('privacy', 'privacy-active'),
        'popup' => empty($_POST['popup']) ? false : sb_get_block_setting('popup'),
        'follow' => sb_get_multi_setting('follow-message', 'follow-active') && ($is_office_hours || !sb_get_multi_setting('follow-message', 'follow-disable-office-hours')) ? sb_get_multi_setting('follow-message', 'follow-delay', true) : false,
        'popup_mobile_hidden' => sb_get_multi_setting('popup-message', 'popup-mobile-hidden'),
        'welcome' => sb_get_multi_setting('welcome-message', 'welcome-active'),
        'chat_manual_init' => sb_get_setting('chat-manual-init'),
        'chat_login_init' => sb_get_setting('chat-login-init'),
        'sound' => sb_get_multi_setting('sound-settings', 'sound-settings-active') ? ['volume' => sb_get_multi_setting('sound-settings', 'sound-settings-volume', 0.6), 'repeat' => sb_get_multi_setting('sound-settings', 'sound-settings-repeat')] : false,
        'header_name' => sb_get_setting('header-name', ''),
        'desktop_notifications' => sb_get_setting('desktop-notifications') && !sb_get_multi_setting('push-notifications', 'push-notifications-active'),
        'flash_notifications' => sb_get_setting('flash-notifications'),
        'push_notifications_users' => sb_get_multi_setting('push-notifications', 'push-notifications-users-active'),
        'notifications_icon' => sb_is_cloud() ? SB_CLOUD_BRAND_ICON_PNG : sb_get_setting('notifications-icon', SB_URL . '/media/icon.png'),
        'notify_email_cron' => sb_get_setting('notify-email-cron'),
        'bot_id' => sb_get_bot_id(),
        'bot_name' => sb_get_setting('bot-name', 'Bot'),
        'bot_image' => sb_get_setting('bot-image', SB_URL . '/media/user.png'),
        'bot_delay' => sb_get_setting('dialogflow-bot-delay', 3000),
        'dialogflow_active' => sb_chatbot_active(true, false),
        'open_ai_active' => sb_chatbot_active(false, true),
        'slack_active' => defined('SB_SLACK') && sb_get_setting('slack-active'),
        'rich_messages' => sb_get_rich_messages_ids(),
        'display_users_thumb' => sb_get_setting('display-users-thumb'),
        'hide_agents_thumb' => sb_get_setting('hide-agents-thumb'),
        'auto_open' => sb_get_setting('auto-open'),
        'office_hours' => $is_office_hours,
        'disable_office_hours' => sb_get_setting('chat-timetable-disable'),
        'disable_offline' => sb_get_setting('chat-offline-disable'),
        'timetable' => sb_get_multi_setting('chat-timetable', 'chat-timetable-active'),
        'articles' => sb_get_setting('articles-active'),
        'articles_url_rewrite' => sb_is_articles_url_rewrite() ? sb_get_articles_page_url() : false,
        'init_dashboard' => sb_get_setting('init-dashboard') && !sb_get_setting('disable-dashboard'),
        'disable_dashboard' => sb_get_setting('disable-dashboard'),
        'queue' => sb_get_multi_setting('queue', 'queue-active'),
        'hide_conversations_routing' => !sb_get_multi_setting('queue', 'queue-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-routing'),
        'webhooks' => sb_get_multi_setting('webhooks', 'webhooks-active') ? sb_get_multi_setting('webhooks', 'webhooks-allowed', true) : false,
        'agents_online' => sb_agents_online(),
        'cron' => date('H') != sb_get_external_setting('cron'),
        'cron_email_piping' => sb_email_piping_is_active() && (!sb_is_cloud() && !sb_get_multi_setting('email-piping', 'email-piping-disable-cron')) && date('i') != sb_get_external_setting('cron-email-piping'),
        'cron_email_piping_active' => sb_email_piping_is_active(),
        'wp' => defined('SB_WP'),
        'perfex' => defined('SB_PERFEX'),
        'whmcs' => defined('SB_WHMCS'),
        'aecommerce' => defined('SB_AECOMMERCE'),
        'martfury' => defined('SB_MARTFURY') && sb_get_setting('martfury-private') ? sb_get_setting('martfury-linking') : false,
        'messenger' => defined('SB_MESSENGER'),
        'pusher' => sb_pusher_active(),
        'cookie_domain' => sb_get_setting('cookie-domain'),
        'visitor_default_name' => sb_get_setting('visitor-default-name'),
        'sms_active_agents' => sb_get_multi_setting('sms', 'sms-active-agents'),
        'language_detection' => false,
        'cloud' => sb_is_cloud() ? ['cloud_user_id' => json_decode(sb_encryption($_POST['cloud'], false), true)['user_id']] : false,
        'automations' => sb_automations_run_all(),
        'close_chat' => sb_get_setting('close-chat'),
        'sender_name' => sb_get_setting('sender-name'),
        'tickets' => defined('SB_TICKETS') && !empty($_POST['tickets']) && $_POST['tickets'] != 'false',
        'max_file_size' => sb_get_server_max_file_size(),
        'tickets_hide' => sb_get_setting('tickets-hide'),
        'rating' => sb_get_multi_setting('rating-message', 'rating-active')
    ];
    if ($return['registration_required'] == 'registration-login' && !sb_isset(sb_get_setting('registration-fields'), 'reg-email')) {
        $return['registration_required'] = 'registration';
    }
    if ($return['rating']) {
        $return['rating_message'] = sb_get_multi_setting('rating-message', 'rating-message-area');
    }
    if ($return['articles']) {
        $return['articles_title'] = sb_get_setting('articles-title', '');
        $return['articles_categories'] = sb_get_setting('articles-categories');
    }
    if ($return['welcome']) {
        $return['welcome_trigger'] = sb_get_multi_setting('welcome-message', 'welcome-trigger', 'load');
        $return['welcome_delay'] = sb_get_multi_setting('welcome-message', 'welcome-delay', 2000);
        $return['welcome_disable_office_hours'] = sb_get_multi_setting('welcome-message', 'welcome-disable-office-hours');
    }
    if ($return['queue']) {
        $return['queue_message'] = sb_get_multi_setting('queue', 'queue-message', '');
        $return['queue_response_time'] = sb_get_multi_setting('queue', 'queue-response-time', 5);
        $return['queue_sound'] = sb_get_multi_setting('queue', 'queue-sound');
    }
    if ($return['timetable']) {
        $return['timetable_type'] = sb_get_multi_setting('chat-timetable', 'chat-timetable-type');
        $return['timetable_hide'] = sb_get_multi_setting('chat-timetable', 'chat-timetable-hide');
        $return['timetable_disable_agents'] = sb_get_multi_setting('chat-timetable', 'chat-timetable-agents');
    }
    if ($return['wp']) {
        $return['wp_users_system'] = sb_get_setting('wp-users-system', 'sb');
        $return['wp_registration'] = sb_get_setting('wp-registration');
    }
    if ($return['push_notifications_users']) {
        $return['push_notifications_provider'] = sb_is_cloud() ? 'onesignal' : sb_get_multi_setting('push-notifications', 'push-notifications-provider', 'pusher');
        $return['push_notifications_id'] = sb_is_cloud() ? ONESIGNAL_APP_ID : sb_get_multi_setting('push-notifications', $return['push_notifications_provider'] == 'onesignal' ? 'push-notifications-onesignal-app-id' : 'push-notifications-id');
        $return['push_notifications_url'] = sb_get_multi_setting('push-notifications', 'push-notifications-sw-url');
    }
    if ($return['pusher']) {
        $return['pusher_key'] = sb_pusher_get_details()[0];
        $return['pusher_cluster'] = sb_pusher_get_details()[3];
    }
    if (!empty($return['timetable_hide']) || !empty($return['timetable_type'])) {
        $return['timetable_message'] = [sb_t(sb_get_multi_setting('chat-timetable', 'chat-timetable-title')), sb_t(sb_get_multi_setting('chat-timetable', 'chat-timetable-msg'))];
    }
    if ($return['tickets']) {
        $return['tickets_registration_required'] = sb_get_setting('tickets-registration-required');
        $return['tickets_registration_redirect'] = sb_get_setting('tickets-registration-redirect', '');
        $return['tickets_default_form'] = sb_get_setting('tickets-registration-disable-password') ? 'registration' : sb_get_setting('tickets-default-form', 'login');
        $return['tickets_conversations_title_user'] = sb_get_setting('tickets-conversations-title-user');
        $return['tickets_welcome_message'] = sb_get_multi_setting('tickets-welcome-message', 'tickets-welcome-message-active') ? sb_merge_fields(sb_t(sb_get_multi_setting('tickets-welcome-message', 'tickets-welcome-message-msg'))) : false;
        $return['tickets_conversation_name'] = sb_get_setting('tickets-conversation-name', '');
        $return['tickets_enter_button'] = sb_get_setting('tickets-enter-button');
        $return['tickets_manual_init'] = sb_get_setting('tickets-manual-init');
        $return['tickets_default_department'] = sb_get_setting('tickets-default-department');
        $return['tickets_names'] = sb_get_setting('tickets-names');
        $return['tickets_recaptcha'] = sb_get_multi_setting('tickets-recaptcha', 'tickets-recaptcha-active') ? sb_get_multi_setting('tickets-recaptcha', 'tickets-recaptcha-key') : false;
        $return['tickets_disable_first'] = sb_get_multi_setting('tickets-disable-features', 'tickets-first-ticket');
        $return['tickets_close'] = sb_get_setting('close-ticket');
    }
    if (defined('SB_WOOCOMMERCE')) {
        $return['woocommerce'] = true;
        $return['woocommerce_returning_'] = !in_array(sb_isset($active_user, 'user_type'), ['user', 'agent', 'admin']) && sb_get_multi_setting('wc-returning-visitor', 'wc-returning-visitor-active');
    }
    if ($return['dialogflow_active'] || $return['open_ai_active']) {
        $return['dialogflow_human_takeover'] = sb_get_multi_setting('dialogflow-human-takeover', 'dialogflow-human-takeover-active');
        $return['dialogflow_human_takeover_disable_chatbot'] = sb_get_multi_setting('dialogflow-human-takeover', 'dialogflow-human-takeover-disable-chatbot');
        $return['dialogflow_welcome'] = sb_get_setting('dialogflow-welcome') || sb_get_multi_setting('google', 'dialogflow-welcome'); // Deprecated: sb_get_setting('dialogflow-welcome')
        $return['dialogflow_send_user_details'] = sb_get_setting('dialogflow-send-user-details') || sb_get_multi_setting('google', 'dialogflow-send-user-details'); // Deprecated: sb_get_setting('dialogflow-send-user-details')
        $return['dialogflow_departments'] = sb_get_setting('dialogflow-departments');
        $return['dialogflow_disable_tickets'] = sb_get_setting('dialogflow-disable-tickets');
        $return['dialogflow_office_hours'] = sb_get_setting('dialogflow-timetable');
        $return['flow_on_load'] = sb_flows_on_conversation_start_or_load(false, sb_get_user_language(), false, true);
        $return['open_ai_context_awareness'] = sb_get_multi_setting('open-ai', 'open-ai-context-awareness');
        if ($return['queue'] && $return['dialogflow_human_takeover']) {
            $return['queue'] = false;
            $return['queue_human_takeover'] = true;
        }
        if (sb_get_multi_setting('chatbot-usage-limit', 'chatbot-usage-limit-quota')) {
            $return['chatbot_limit'] = ['quota' => intval(sb_get_multi_setting('chatbot-usage-limit', 'chatbot-usage-limit-quota')), 'interval' => intval(sb_get_multi_setting('chatbot-usage-limit', 'chatbot-usage-limit-interval')), 'message' => sb_get_multi_setting('chatbot-usage-limit', 'chatbot-usage-limit-message')];
        }
    } else if (defined('SB_DIALOGFLOW')) {
        $return['language_detection'] = sb_get_multi_setting('google', 'google-language-detection') || sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active'); // Deprecated: sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active')
        $return['speech_recognition'] = sb_get_multi_setting('open-ai', 'open-ai-speech-recognition');
    }
    if ($active_user) {
        $user_id = $active_user['id'];
        $current_url = false;
        if (!sb_is_agent($active_user)) {
            try {
                $current_url = isset($_POST['current_url']) ? $_POST['current_url'] : $_SERVER['HTTP_REFERER'];
                if ($current_url) {
                    sb_current_url($user_id, $current_url);
                }
            } catch (Exception $e) {
            }
            if ($return['pusher']) {
                sb_pusher_trigger('private-user-' . $user_id, 'init', ['current_url' => $current_url]);
            }
        }
        sb_update_users_last_activity($user_id);
    }
    return $return;
}

function sb_js_admin() {
    $is_cloud = sb_is_cloud();
    $active_user = sb_get_active_user();
    $active_user_type = sb_isset($active_user, 'user_type');
    $is_agent = sb_is_agent($active_user_type, true, false, true);
    $language = sb_get_admin_language();
    $routing_type = sb_get_multi_setting('queue', 'queue-active') ? 'queue' : (sb_get_multi_setting('routing', 'routing-active') ? 'routing' : (sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') ? 'hide-conversations' : false));
    $settings = [
        'bot_id' => sb_get_bot_id(),
        'close_message' => sb_get_multi_setting('close-message', 'close-active'),
        'close_message_transcript' => sb_get_multi_setting('close-message', 'close-transcript'),
        'routing' => (!$active_user || $is_agent) && $routing_type ? $routing_type : false,
        'desktop_notifications' => sb_get_setting('desktop-notifications'),
        'push_notifications' => sb_get_multi_setting('push-notifications', 'push-notifications-active'),
        'push_notifications_users' => sb_get_multi_setting('push-notifications', 'push-notifications-users-active'),
        'flash_notifications' => sb_get_setting('flash-notifications'),
        'notifications_icon' => $is_cloud ? SB_CLOUD_BRAND_ICON_PNG : sb_get_setting('notifications-icon', SB_URL . '/media/icon.png'),
        'auto_updates' => sb_get_setting('auto-updates'),
        'sound' => sb_get_multi_setting('sound-settings', 'sound-settings-active-admin') ? ['volume' => sb_get_multi_setting('sound-settings', 'sound-settings-volume-admin', 0.6), 'repeat' => sb_get_multi_setting('sound-settings', 'sound-settings-repeat-admin')] : false,
        'pusher' => sb_pusher_active(),
        'notify_user_email' => sb_get_setting('notify-user-email') || sb_email_piping_is_active(),
        'assign_conversation_to_agent' => $is_agent && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-view'),
        'allow_agent_delete_message' => $active_user_type == 'admin' || sb_get_multi_setting('agents', 'agents-delete-message'),
        'supervisor' => sb_supervisor(),
        'sms_active_users' => sb_get_multi_setting('sms', 'sms-active-users'),
        'sms' => sb_get_multi_setting('sms', 'sms-user'),
        'now_db' => sb_gmt_now(),
        'login_time' => time(),
        'single_agent' => intval(sb_db_get('SELECT COUNT(*) as count FROM sb_users WHERE user_type = "agent" OR user_type = "admin"')['count']) == 1,
        'slack_active' => sb_get_setting('slack-active'),
        'zendesk_active' => sb_get_setting('zendesk-active'),
        'active_agent_language' => sb_get_user_language(sb_get_active_user_ID()),
        'transcript_message' => sb_get_multi_setting('transcript', 'transcript-message', ''),
        'cookie_domain' => sb_get_setting('cookie-domain'),
        'cloud' => $is_cloud,
        'online_users_notification' => sb_get_setting('online-users-notification') ? sb_('New user online') : false,
        'webhooks' => sb_get_multi_setting('webhooks', 'webhooks-active') ? sb_get_multi_setting('webhooks', 'webhooks-allowed', true) : false,
        'show_profile_images' => sb_get_setting('show-profile-images-admin'),
        'sender_name' => sb_get_setting('sender-name'),
        'notify_email_cron' => sb_get_setting('notify-email-cron'),
        'order_by_date' => sb_get_setting('order-by-date'),
        'max_file_size' => sb_get_server_max_file_size(),
        'reports_disabled' => sb_get_multi_setting('performance', 'performance-reports'),
        'rich_messages' => sb_get_rich_messages_ids(),
        'color' => sb_get_setting('color-admin-1'),
        'away_mode' => sb_get_setting('away-mode'),
        'chatbot_features' => sb_get_multi_setting('open-ai', 'open-ai-active') || sb_get_multi_setting('google', 'dialogflow-active') || sb_get_setting('ai-smart-reply'),
        'tags' => sb_get_setting('tags', []),
        'tags_show' => sb_get_multi_setting('tags-settings', 'tags-show'),
        'departments' => sb_get_setting('departments'),
        'departments_show' => sb_get_multi_setting('departments-settings', 'departments-show-list'),
        'notes_hide_information' => sb_get_multi_setting('notes-settings', 'notes-hide-name'),
        'visitor_default_name' => sb_get_setting('visitor-default-name'),
        'hide_conversation_details' => sb_get_setting('hide-conversation-details'),
        'visitors_registration' => sb_get_setting('visitors-registration') || sb_get_setting('online-users-notification')
    ];
    $code = '<script>';
    if (defined('SB_DIALOGFLOW')) {
        $settings['dialogflow'] = sb_get_multi_setting('google', 'dialogflow-active');
        $settings['open_ai_user_expressions'] = sb_get_multi_setting('open-ai', 'open-ai-user-expressions');
        $settings['open_ai_prompt_rewrite'] = sb_get_multi_setting('open-ai', 'open-ai-prompt-message-rewrite');
        $settings['smart_reply'] = sb_get_multi_setting('dialogflow-smart-reply', 'dialogflow-smart-reply-active') || sb_get_setting('ai-smart-reply'); // Deprecated: sb_get_multi_setting('dialogflow-smart-reply', 'dialogflow-smart-reply-active')
        $settings['open_ai_model'] = function_exists('sb_open_ai_get_gpt_model') ? sb_open_ai_get_gpt_model() : 'gpt-3.5-turbo'; // Deprecated: function_exists('sb_open_ai_get_gpt_model') ? sb_open_ai_get_gpt_model() : 'gpt-3.5-turbo';
        $settings['translation'] = sb_get_setting('google-translation') || sb_get_multi_setting('google', 'google-translation'); // Deprecated: sb_get_setting('google-translation')
        $settings['multilingual_translation'] = sb_get_multi_setting('google', 'google-multilingual-translation');
        $settings['speech_recognition'] = sb_get_multi_setting('open-ai', 'open-ai-speech-recognition');
        $settings['note_data_scrape'] = sb_get_multi_setting('open-ai', 'open-ai-note-scraping') ? sb_open_ai_data_scraping_get_prompts('name') : false;
        $settings['open_ai_chatbot_status'] = sb_get_multi_setting('open-ai', 'open-ai-active') ? (in_array(sb_get_multi_setting('open-ai', 'open-ai-mode'), ['assistant', 'general']) ? 'mode' : ((!sb_is_cloud() || sb_get_multi_setting('open-ai', 'open-ai-sync-mode') == 'manual') && empty(trim(sb_get_multi_setting('open-ai', 'open-ai-key'))) ? 'key' : true)) : 'inactive';
    }
    if (defined('SB_WOOCOMMERCE')) {
        $settings['currency'] = sb_get_setting('wc-currency-symbol');
        $settings['languages'] = json_encode(sb_isset(sb_wp_language_settings(), 'languages', []));
    }
    if (defined('SB_PERFEX')) {
        $settings['perfex_url'] = sb_get_setting('perfex-url');
    }
    if (defined('SB_WHMCS')) {
        $settings['whmcs_url'] = sb_get_setting('whmcs-url');
    }
    if (defined('SB_AECOMMERCE')) {
        $settings['aecommerce_panel_title'] = sb_get_setting('aecommerce-panel-title', 'Active eCommerce');
    }
    if ($settings['pusher']) {
        $settings['pusher_key'] = sb_pusher_get_details()[0];
        $settings['pusher_cluster'] = sb_pusher_get_details()[3];
    }
    if ($settings['push_notifications'] || $settings['push_notifications_users'] || sb_is_cloud()) {
        $settings['push_notifications_provider'] = sb_is_cloud() ? 'onesignal' : sb_get_multi_setting('push-notifications', 'push-notifications-provider');
        $settings['push_notifications_id'] = sb_is_cloud() ? ONESIGNAL_APP_ID : sb_get_multi_setting('push-notifications', $settings['push_notifications_provider'] == 'onesignal' ? 'push-notifications-onesignal-app-id' : 'push-notifications-id');
        $settings['push_notifications_url'] = sb_get_multi_setting('push-notifications', 'push-notifications-sw-url');
    }
    if ($settings['supervisor']) {
        $settings['allow_supervisor_delete_message'] = $settings['supervisor']['supervisor-delete-message'];
        $settings['supervisor'] = true;
    }
    if ($active_user) {
        if (empty($active_user['url']) || $active_user['url'] == SB_URL) {
            $code .= 'var SB_ACTIVE_AGENT = { id: "' . $active_user['id'] . '", email: "' . $active_user['email'] . '", full_name: "' . sb_get_user_name($active_user) . '", user_type: "' . $active_user_type . '", profile_image: "' . $active_user['profile_image'] . '", department: "' . sb_isset($active_user, 'department', '') . '"};';
        } else {
            $code .= 'SBF.reset();';
        }
    } else {
        $code .= 'var SB_ACTIVE_AGENT = { id: "", full_name: "", user_type: "", profile_image: "", email: "" };';
    }
    if ($active_user && $is_agent && ($routing_type == 'queue' || $routing_type == 'routing')) {
        sb_routing_assign_conversations_active_agent($routing_type == 'queue');
    }
    if (defined('SB_WP')) {
        $code .= 'var SB_WP = true;';
    }
    if ($is_cloud) {
        $cookie_cloud = json_decode(sb_encryption($_POST['cloud'], false), true);
        $settings['cloud'] = $cookie_cloud && isset($cookie_cloud['email']) ? ['email' => $cookie_cloud['email'], 'cloud_user_id' => $cookie_cloud['user_id'], 'token' => $cookie_cloud['token'], 'chat_id' => account_chat_id($cookie_cloud['user_id'])] : [];
        $settings['credits'] = membership_get_active()['credits'];
        $settings['google_client_id'] = sb_defined('GOOGLE_CLIENT_ID');
        $settings['shopify_shop'] = shopify_get_shop_name();
        if ($settings['credits'] <= 0 && (((sb_get_multi_setting('open-ai', 'open-ai-active') || sb_get_multi_setting('open-ai', 'open-ai-spelling-correction') || sb_get_multi_setting('open-ai', 'open-ai-rewrite')) && sb_get_multi_setting('open-ai', 'open-ai-sync-mode', 'manual') != 'manual') || ((sb_get_multi_setting('google', 'dialogflow-active') || sb_get_multi_setting('google', 'google-multilingual-translation') || sb_get_multi_setting('google', 'google-translation') || sb_get_multi_setting('google', 'google-language-detection')) && sb_get_multi_setting('google', 'google-sync-mode', 'manual') != 'manual'))) { // Deprecated: remove , 'manual') default value.
            $settings['credits_required'] = true;
        }
    }
    $file_path = SB_PATH . '/resources/languages/admin/js/' . $language . '.json';
    $translations = $language && $language != 'en' && file_exists($file_path) ? file_get_contents($file_path) : '[]';
    $code .= 'var SB_LANGUAGE_CODES = ' . file_get_contents(SB_PATH . '/resources/languages/language-codes.json') . ';';
    $code .= 'var SB_ADMIN_SETTINGS = ' . json_encode($settings) . ';';
    $code .= 'var SB_TRANSLATIONS = ' . ($translations ? $translations : '[]') . ';';
    $code .= 'var SB_VERSIONS = ' . json_encode(array_merge(['sb' => SB_VERSION], sb_get_installed_apps_version())) . ';';
    $code .= '</script>';
    echo $code;
}

function sb_js_global() {
    global $SB_LANGUAGE;
    if (!isset($SB_LANGUAGE)) {
        sb_init_translations();
    }
    $ajax_url = str_replace('//include', '/include', SB_URL . '/include/ajax.php');
    $code = '<script data-cfasync="false">';
    $code .= 'var SB_AJAX_URL = "' . $ajax_url . '";';
    $code .= 'var SB_URL = "' . SB_URL . '";';
    $code .= 'var SB_LANG = ' . ($SB_LANGUAGE ? json_encode($SB_LANGUAGE) : 'false') . ';';
    $code .= '</script>';
    echo $code;
}

function sb_get_block_setting($value) {
    switch ($value) {
        case 'privacy':
            $settings = sb_get_setting('privacy');
            return $settings && $settings['privacy-active'] ? ['title' => sb_rich_value($settings['privacy-title']), 'message' => sb_rich_value($settings['privacy-msg']), 'decline' => sb_rich_value($settings['privacy-msg-decline']), 'link' => $settings['privacy-link'], 'link-name' => sb_rich_value(sb_isset($settings, 'privacy-link-text', ''), false), 'btn-approve' => sb_rich_value(sb_isset($settings, 'privacy-btn-approve', 'Yes'), false), 'btn-decline' => sb_rich_value(sb_isset($settings, 'privacy-btn-decline', 'Cancel'), false)] : false;
        case 'popup':
            $settings = sb_get_setting('popup-message');
            return $settings && $settings['popup-active'] ? ['title' => sb_rich_value($settings['popup-title']), 'message' => sb_rich_value(nl2br($settings['popup-msg'])), 'image' => $settings['popup-image']] : false;
        case 'welcome':
            $settings = sb_get_setting('welcome-message');
            return $settings && $settings['welcome-active'] ? ['message' => sb_rich_value($settings['welcome-msg'], true, true, true), 'open' => $settings['welcome-open'], 'sound' => $settings['welcome-sound']] : false;
        case 'follow':
            $settings = sb_get_setting('follow-message');
            return $settings && $settings['follow-active'] ? ['title' => sb_rich_value($settings['follow-title']), 'message' => sb_rich_value($settings['follow-msg'], false, true), 'sound' => $settings['follow-sound'], 'name' => $settings['follow-name'] ? 'true' : 'false', 'last-name' => sb_isset($settings, 'follow-last-name') ? 'true' : 'false', 'phone' => sb_isset($settings, 'follow-phone') ? 'true' : 'false', 'phone-required' => sb_isset($settings, 'follow-phone-required') ? 'true' : 'false', 'success' => sb_rich_value(str_replace('{user_name}', '{user_name_}', $settings['follow-success'])), 'placeholder' => sb_rich_value(sb_isset($settings, 'follow-placeholder', 'Email')), 'delay' => sb_isset($settings, 'follow-delay'), 'disable-office-hours' => sb_isset($settings, 'follow-disable-office-hours')] : false;
    }
    return false;
}

// function sb_populate_settings($category, $settings, $echo = true) {
//     if (!isset($settings) && file_exists(SB_PATH . '/resources/json/settings.json')) {
//         $settings = sb_get_json_resource('json/settings.json');
//     }
//     $settings = $settings[$category];
//     $code = '';
//     for ($i = 0; $i < count($settings); $i++) {
//         $code .= sb_get_setting_code($settings[$i]);
//     }
//     if ($echo) {
//         echo $code;
//         return true;
//     } else {
//         return $code;
//     }
// }

function sb_populate_settings($category, $settings, $echo = true, $subcategory = null) {
    if (!isset($settings) && file_exists(SB_PATH . '/resources/json/settings.json')) {
        $settings = sb_get_json_resource('json/settings.json');
    }

    // Validate main category
    if (!isset($settings[$category])) {
        return false;
    }

    $category_data = $settings[$category];

    // Determine the format and extract settings list accordingly
    if (isset($subcategory) && is_array($category_data) && isset($category_data[$subcategory])) {
        // Nested subcategory format: "chat": { "subcat": [ ... ] }
        $settings_list = $category_data[$subcategory];
    } elseif (isset($category_data[0]) && is_array($category_data[0])) {
        // Flat array format: "chat": [ ... ]
        $settings_list = $category_data;
    } else {
        // Fallback: maybe invalid structure or missing subcategory
        return false;
    }

    // Generate HTML
    $code = '';
    foreach ($settings_list as $setting) {
       // if($category == 'chat')
            $code .= sb_get_chat_setting_code($setting);
        // else
        //     $code .= sb_get_setting_code($setting);
    }

    if ($echo) {
        echo $code;
        return true;
    } else {
        return $code;
    }
}

function sb_is_assoc(array $arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function sb_populate_app_settings($app_name) {
    $file = SB_PATH . '/apps/' . $app_name . '/settings.json';
    $settings = [$app_name => []];
    if (file_exists($file)) {
        $settings[$app_name] = json_decode(file_get_contents($file), true);
        if (sb_is_cloud()) {
            $settings = sb_cloud_merge_settings($settings);
        }
    }
    return sb_populate_settings($app_name, $settings, false);
}

function sb_get_setting_code($setting) {
    if (isset($setting)) {
        $id = $setting['id'];
        $type = $setting['type'];
        $disable_translations = sb_get_setting('admin-disable-settings-translations');
        $keywords = sb_isset($setting, 'keywords');
        $content = '<div id="' . $id . '" data-type="' . $type . '"' . ($keywords ? ' data-keywords="' . $keywords . '"' : '') . (isset($setting['setting']) ? ' data-setting="' . $setting['setting'] . '"' : '') . ' class="sb-setting sb-type-' . $type . '"><div class="sb-setting-content"><h2>' . sb_s($setting['title'], $disable_translations) . '</h2><p>' . sb_s($setting['content'], $disable_translations) . sb_get_setting_code_help($setting) . '</p></div><div class="input">';
        switch ($type) {
            case 'color':
                $content .= '<input type="text"><i class="sb-close sb-icon-close"></i>';
                break;
            case 'text':
                $content .= '<input type="text">';
                break;
            case 'password':
                $content .= '<input type="password">';
                break;
            case 'textarea':
                $content .= '<textarea></textarea>';
                break;
            case 'select':
                $values = $setting['value'];
                $content .= '<select>';
                for ($i = 0; $i < count($values); $i++) {
                    $content .= '<option value="' . $values[$i][0] . '">' . sb_s($values[$i][1], $disable_translations) . '</option>';
                }
                $content .= '</select>';
                break;
            case 'checkbox':
                $content .= '<input type="checkbox">';
                break;
            case 'radio':
                $values = $setting['value'];
                for ($i = 0; $i < count($values); $i++) {
                    $content .= '<div><input type="radio" name="' . $id . '" value="' . strtolower(str_replace(' ', '-', $values[$i])) . '"><label>' . $setting["value"][$i] . '</label></div>';
                }
                break;
            case 'number':
                $content .= '<input type="number">' . (isset($setting['unit']) ? '<label>' . $setting['unit'] . '</label>' : '');
                break;
            case 'upload':
                $content .= (empty($setting['text-field']) ? '' : '<input type="url">') . '<a class="sb-btn">' . sb_(sb_isset($setting, 'button-text', 'Choose file')) . '</a>';
                break;
            case 'upload-image':
                $content .= '<div class="image"' . (isset($setting['background-size']) ? ' style="background-size: ' . $setting['background-size'] . '"' : '') . '><i class="sb-icon-close"></i></div>';
                break;
            case 'input-button':
                $content .= '<input type="text"><a class="sb-btn">' . sb_s($setting['button-text'], $disable_translations) . '</a>';
                break;
            case 'button':
                $content .= '<a class="sb-btn" href="' . $setting['button-url'] . '" target="_blank">' . sb_s($setting['button-text'], $disable_translations) . '</a>';
                break;
            case 'multi-input':
                $values = $setting['value'];
                for ($i = 0; $i < count($values); $i++) {
                    $sub_type = $values[$i]['type'];
                    $content .= '<div id="' . $values[$i]['id'] . '" data-type="' . $sub_type . '" class="multi-input-' . $sub_type . '"><label>' . sb_s($values[$i]['title'], $disable_translations) . sb_get_setting_code_help($values[$i]) . '</label>';
                    switch ($sub_type) {
                        case 'text':
                            $content .= '<input type="text">';
                            break;
                        case 'password':
                            $content .= '<input type="password">';
                            break;
                        case 'number':
                            $content .= '<input type="number">';
                            break;
                        case 'textarea':
                            $content .= '<textarea></textarea>';
                            break;
                        case 'upload':
                            $content .= '<input type="url"><button type="button">' . sb_('Choose file') . '</button>';
                            break;
                        case 'upload-image':
                            $content .= '<div class="image"><i class="sb-icon-close"></i></div>';
                            break;
                        case 'checkbox':
                            $content .= '<input type="checkbox">';
                            break;
                        case 'select':
                            $content .= '<select>';
                            $items = $values[$i]['value'];
                            for ($j = 0; $j < count($items); $j++) {
                                $content .= '<option value="' . $items[$j][0] . '">' . sb_s($items[$j][1], $disable_translations) . '</option>';
                            }
                            $content .= '</select>';
                            break;
                        case 'button':
                            $content .= '<a class="sb-btn" href="' . $values[$i]['button-url'] . '" target="_blank">' . sb_s($values[$i]['button-text'], $disable_translations) . '</a>';
                            break;
                        case 'select-checkbox':
                            $items = $values[$i]['value'];
                            $content .= '<input type="text" class="sb-select-checkbox-input" readonly><div class="sb-select-checkbox">';
                            for ($i = 0; $i < count($items); $i++) {
                                $content .= '<div class="multi-input-checkbox"><input id="' . $items[$i][0] . '" type="checkbox"><label>' . sb_s($items[$i][1], $disable_translations) . '</label></div>';
                            }
                            $content .= '</div>';
                            break;
                    }
                    $content .= '</div>';
                }
                break;
            case 'range':
                $range = (key_exists('range', $setting) ? $setting['range'] : array(0, 100));
                $unit = (key_exists('unit', $setting) ? '<label>' . $setting['unit'] . '</label>' : '');
                $content .= '<label class="range-value">' . $range[0] . '</label><input type="range" min="' . $range[0] . '" max="' . $range[1] . '" value="' . $range[0] . '" />' . $unit;
                break;
            case 'repeater':
                $content .= '<div class="sb-repeater"><div class="repeater-item">';
                for ($i = 0; $i < count($setting['items']); $i++) {
                    $item = $setting['items'][$i];
                    $content .= '<div>' . (isset($item['name']) ? '<label>' . sb_s($item['name'], $disable_translations) . '</label>' : '');
                    switch ($item['type']) {
                        case 'url':
                        case 'text':
                        case 'number':
                        case 'password':
                            $content .= '<input data-id="' . $item['id'] . '" type="' . $item['type'] . '">';
                            break;
                        case 'textarea':
                            $content .= '<textarea data-id="' . $item['id'] . '"></textarea>';
                            break;
                        case 'checkbox':
                            $content .= '<input data-id="' . $item['id'] . '" type="checkbox">';
                            break;
                        case 'auto-id':
                            $content .= '<input data-type="auto-id" data-id="' . $item['id'] . '" value="1" type="text" readonly="true">';
                            break;
                        case 'hidden':
                            $content .= '<input data-id="' . $item['id'] . '" type="hidden">';
                            break;
                        case 'color-palette':
                            $content .= sb_color_palette($item['id']);
                            break;
                        case 'upload-image':
                            $content .= '<div data-type="upload-image"><div data-id="' . $item['id'] . '" class="image"><i class="sb-icon-close"></i></div></div>';
                            break;
                        case 'upload-file':
                            $content .= '<div data-type="upload-file" class="sb-flex"><input type="url" data-id="' . $item['id'] . '" disabled><a class="sb-btn">' . sb_('Choose file') . '</a></div>';
                            break;
                        case 'button':
                            $content .= '<a data-id="' . $item['id'] . '" href="' . $item['button-url'] . '" class="sb-btn" target="_blank">' . sb_s($item['button-text'], $disable_translations) . '</a>';
                            break;
                        case 'select':
                            $values = $item['value'];
                            $content .= '<select data-id="' . $item['id'] . '">';
                            for ($i = 0; $i < count($values); $i++) {
                                $content .= '<option value="' . $values[$i][0] . '">' . sb_s($values[$i][1], $disable_translations) . '</option>';
                            }
                            $content .= '</select>';
                            break;
                    }
                    $content .= '</div>';
                }
                $content .= '<i class="sb-icon-close"></i></div></div><a class="sb-btn sb-repeater-add">' . sb_('Add new item') . '</a>';
                break;
            case 'timetable':
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $hours = [['', ''], ['00:00', '12:00 am'], ['00:30', '12:30 am'], ['01:00', '1:00 am'], ['01:30', '1:30 am'], ['02:00', '2:00 am'], ['02:30', '2:30 am'], ['03:00', '3:00 am'], ['03:30', '3:30 am'], ['04:00', '4:00 am'], ['04:30', '4:30 am'], ['05:00', '5:00 am'], ['05:30', '5:30 am'], ['06:00', '6:00 am'], ['06:30', '6:30 am'], ['07:00', '7:00 am'], ['07:30', '7:30 am'], ['08:00', '8:00 am'], ['08:30', '8:30 am'], ['09:00', '9:00 am'], ['09:30', '9:30 am'], ['10:00', '10:00 am'], ['10:30', '10:30 am'], ['11:00', '11:00 am'], ['11:30', '11:30 am'], ['12:00', '12:00 pm'], ['12:30', '12:30 pm'], ['13:00', '1:00 pm'], ['13:30', '1:30 pm'], ['14:00', '2:00 pm'], ['14:30', '2:30 pm'], ['15:00', '3:00 pm'], ['15:30', '3:30 pm'], ['16:00', '4:00 pm'], ['16:30', '4:30 pm'], ['17:00', '5:00 pm'], ['17:30', '5:30 pm'], ['18:00', '6:00 pm'], ['18:30', '6:30 pm'], ['19:00', '7:00 pm'], ['19:30', '7:30 pm'], ['20:00', '8:00 pm'], ['20:30', '8:30 pm'], ['21:00', '9:00 pm'], ['21:30', '9:30 pm'], ['22:00', '10:00 pm'], ['22:30', '10:30 pm'], ['23:00', '11:00 pm'], ['23:30', '11:30 pm'], ['closed', sb_('Closed')]];
                $select = '<div class="sb-custom-select">';
                for ($i = 0; $i < count($hours); $i++) {
                    $select .= '<span data-value="' . $hours[$i][0] . '">' . $hours[$i][1] . '</span>';
                }
                $content .= '<div class="sb-timetable">';
                for ($i = 0; $i < 7; $i++) {
                    $content .= '<div data-day="' . strtolower($days[$i]) . '"><label>' . sb_($days[$i]) . '</label><div><div></div><span>' . sb_('To') . '</span><div></div><span>' . sb_('And') . '</span><div></div><span>' . sb_('To') . '</span><div></div></div></div>';
                }
                $content .= $select . '</div></div>';
                break;
            case 'select-images':
                $content .= '<div class="sb-icon-close"></div>';
                for ($i = 0; $i < count($setting['images']); $i++) {
                    $content .= '<div data-value="' . $setting['images'][$i] . '" style="background-image: url(\'' . SB_URL . '/media/' . $setting['images'][$i] . '\')"></div>';
                }
                break;
            case 'select-checkbox':
                $values = $setting['value'];
                $content .= '<select disabled><option>AA</option></select><div class="sb-select-checkbox">';
                for ($i = 0; $i < count($values); $i++) {
                    $content .= '<div id="' . $values[$i]['id'] . '" data-type="checkbox" class="multi-input-checkbox"><input type="checkbox"><label>' . sb_s($values[$i]['title'], $disable_translations) . '</label></div>';
                }
                $content .= '</div>';
                break;
        }
        if (isset($setting['setting']) && ($type == 'multi-input' || !empty($setting['multilingual']))) {
            $content .= '<div class="sb-language-switcher-cnt"><label>' . sb_('Languages') . '</label></div>';
        }
        
         $content .= '</div></div>';

        if ($id == 'tickets-custom-fields') {
            return ticket_custom_field_settings();
        } else if ($id == 'tickets-statuses') {
            return ticket_statuses_settings();
        } else {
            return $content;
        }
    }
    return '';
}

function sb_get_chat_setting_code($setting) {
    if (isset($setting)) {
        $id = $setting['id'];
        $type = $setting['type'];
        $disable_translations = sb_get_setting('admin-disable-settings-translations');
        $keywords = sb_isset($setting, 'keywords');
        $content = '<div id="' . $id . '" data-type="' . $type . '"' . ($keywords ? ' data-keywords="' . $keywords . '"' : '') . (isset($setting['setting']) ? ' data-setting="' . $setting['setting'] . '"' : '') . ' class="sb-setting sb-type-' . $type . '"><div class="sb-setting-content"><h2>' . sb_s($setting['title'], $disable_translations) . '</h2><p>' . sb_s($setting['content'], $disable_translations) . sb_get_setting_code_help($setting) . '</p></div><div class="input">';
        switch ($type) {
            case 'color':
                $content .= '<input type="text"><i class="sb-close sb-icon-close"></i>';
                break;
            case 'text':
                $content .= '<input type="text">';
                break;
            case 'password':
                $content .= '<input type="password">';
                break;
            case 'textarea':
                $content .= '<textarea></textarea>';
                break;
            case 'select':
                $values = $setting['value'];
                $content .= '<select>';
                for ($i = 0; $i < count($values); $i++) {
                    $content .= '<option value="' . $values[$i][0] . '">' . sb_s($values[$i][1], $disable_translations) . '</option>';
                }
                $content .= '</select>';
                break;
            case 'checkbox':
                //$content .= '<input type="checkbox">';
                $content .= '<label class="custom-switch">
						<input type="checkbox">
						<span class="slider"></span>
					</label>';
                break;
            case 'radio':
                $values = $setting['value'];
                for ($i = 0; $i < count($values); $i++) {
                    $content .= '<div><input type="radio" name="' . $id . '" value="' . strtolower(str_replace(' ', '-', $values[$i])) . '"><label>' . $setting["value"][$i] . '</label></div>';
                }
                break;
            case 'number':
                $content .= '<input type="number">' . (isset($setting['unit']) ? '<label>' . $setting['unit'] . '</label>' : '');
                break;
            case 'upload':
                $content .= (empty($setting['text-field']) ? '' : '<input type="url">') . '<a class="sb-btn">' . sb_(sb_isset($setting, 'button-text', 'Choose file')) . '</a>';
                break;
            case 'upload-image':
                $content .= '<div class="image"' . (isset($setting['background-size']) ? ' style="background-size: ' . $setting['background-size'] . '"' : '') . '><i class="sb-icon-close"></i></div>';
                break;
            case 'input-button':
                $content .= '<input type="text"><a class="sb-btn">' . sb_s($setting['button-text'], $disable_translations) . '</a>';
                break;
            case 'button':
                $content .= '<a class="sb-btn" href="' . $setting['button-url'] . '" target="_blank">' . sb_s($setting['button-text'], $disable_translations) . '</a>';
                break;
            case 'multi-input':
                $values = $setting['value'];
                for ($i = 0; $i < count($values); $i++) {
                    $sub_type = $values[$i]['type'];
                    $content .= '<div id="' . $values[$i]['id'] . '" data-type="' . $sub_type . '" class="multi-input-' . $sub_type . '"><label>' . sb_s($values[$i]['title'], $disable_translations) . sb_get_setting_code_help($values[$i]) . '</label>';
                    switch ($sub_type) {
                        case 'text':
                            $content .= '<input type="text">';
                            break;
                        case 'password':
                            $content .= '<input type="password">';
                            break;
                        case 'number':
                            $content .= '<input type="number">';
                            break;
                        case 'textarea':
                            $content .= '<textarea></textarea>';
                            break;
                        case 'upload':
                            $content .= '<input type="url"><button type="button">' . sb_('Choose file') . '</button>';
                            break;
                        case 'upload-image':
                            $content .= '<div class="image"><i class="sb-icon-close"></i></div>';
                            break;
                        case 'checkbox':
                            //$content .= '<input type="checkbox">';
                            $content .= '<label class="custom-switch">
						<input type="checkbox">
						<span class="slider"></span>
					</label>';
                            break;
                        case 'select':
                            $content .= '<select>';
                            $items = $values[$i]['value'];
                            for ($j = 0; $j < count($items); $j++) {
                                $content .= '<option value="' . $items[$j][0] . '">' . sb_s($items[$j][1], $disable_translations) . '</option>';
                            }
                            $content .= '</select>';
                            break;
                        case 'button':
                            $content .= '<a class="sb-btn" href="' . $values[$i]['button-url'] . '" target="_blank">' . sb_s($values[$i]['button-text'], $disable_translations) . '</a>';
                            break;
                        case 'select-checkbox':
                            $items = $values[$i]['value'];
                            $content .= '<input type="text" class="sb-select-checkbox-input" readonly><div class="sb-select-checkbox">';
                            for ($i = 0; $i < count($items); $i++) {
                                $content .= '<div class="multi-input-checkbox"><input id="' . $items[$i][0] . '" type="checkbox"><label>' . sb_s($items[$i][1], $disable_translations) . '</label></div>';
                            }
                            $content .= '</div>';
                            break;
                    }
                    $content .= '</div>';
                }
                break;
            case 'range':
                $range = (key_exists('range', $setting) ? $setting['range'] : array(0, 100));
                $unit = (key_exists('unit', $setting) ? '<label>' . $setting['unit'] . '</label>' : '');
                $content .= '<label class="range-value">' . $range[0] . '</label><input type="range" min="' . $range[0] . '" max="' . $range[1] . '" value="' . $range[0] . '" />' . $unit;
                break;
            case 'repeater':
                $content .= '<div class="sb-repeater"><div class="repeater-item">';
                for ($i = 0; $i < count($setting['items']); $i++) {
                    $item = $setting['items'][$i];
                    $content .= '<div class="item-index">' . (isset($item['name']) ? '<label>' . sb_s($item['name'], $disable_translations) . '</label>' : '');
                    switch ($item['type']) {
                        case 'url':
                        case 'text':
                        case 'number':
                        case 'password':
                            $content .= '<input data-id="' . $item['id'] . '" type="' . $item['type'] . '">';
                            break;
                        case 'textarea':
                            $content .= '<textarea data-id="' . $item['id'] . '"></textarea>';
                            break;
                        case 'checkbox':
                            $content .= '<input data-id="' . $item['id'] . '" type="checkbox">';
                            break;
                        case 'auto-id':
                            $content .= '<input data-type="auto-id" data-id="' . $item['id'] . '" value="1" type="text" readonly="true">';
                            break;
                        case 'hidden':
                            $content .= '<input data-id="' . $item['id'] . '" type="hidden">';
                            break;
                        case 'color-palette':
                            $content .= sb_color_palette($item['id']);
                            break;
                        case 'upload-image':
                            $content .= '<div data-type="upload-image"><div data-id="' . $item['id'] . '" class="image"><i class="sb-icon-close"></i></div></div>';
                            break;
                        case 'upload-file':
                            $content .= '<div data-type="upload-file" class="sb-flex"><input type="url" data-id="' . $item['id'] . '" disabled><a class="sb-btn">' . sb_('Choose file') . '</a></div>';
                            break;
                        case 'button':
                            $content .= '<a data-id="' . $item['id'] . '" href="' . $item['button-url'] . '" class="sb-btn" target="_blank">' . sb_s($item['button-text'], $disable_translations) . '</a>';
                            break;
                        case 'select':
                            $values = $item['value'];
                            $content .= '<select data-id="' . $item['id'] . '">';
                            for ($i = 0; $i < count($values); $i++) {
                                $content .= '<option value="' . $values[$i][0] . '">' . sb_s($values[$i][1], $disable_translations) . '</option>';
                            }
                            $content .= '</select>';
                            break;
                    }
                    $content .= '</div>';
                }
                $content .= '<i class="sb-icon-close"></i></div></div><a class="sb-btn sb-repeater-add">' . sb_('Add new item') . '</a>';
                break;
            case 'timetable':
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $hours = [['', ''], ['00:00', '12:00 am'], ['00:30', '12:30 am'], ['01:00', '1:00 am'], ['01:30', '1:30 am'], ['02:00', '2:00 am'], ['02:30', '2:30 am'], ['03:00', '3:00 am'], ['03:30', '3:30 am'], ['04:00', '4:00 am'], ['04:30', '4:30 am'], ['05:00', '5:00 am'], ['05:30', '5:30 am'], ['06:00', '6:00 am'], ['06:30', '6:30 am'], ['07:00', '7:00 am'], ['07:30', '7:30 am'], ['08:00', '8:00 am'], ['08:30', '8:30 am'], ['09:00', '9:00 am'], ['09:30', '9:30 am'], ['10:00', '10:00 am'], ['10:30', '10:30 am'], ['11:00', '11:00 am'], ['11:30', '11:30 am'], ['12:00', '12:00 pm'], ['12:30', '12:30 pm'], ['13:00', '1:00 pm'], ['13:30', '1:30 pm'], ['14:00', '2:00 pm'], ['14:30', '2:30 pm'], ['15:00', '3:00 pm'], ['15:30', '3:30 pm'], ['16:00', '4:00 pm'], ['16:30', '4:30 pm'], ['17:00', '5:00 pm'], ['17:30', '5:30 pm'], ['18:00', '6:00 pm'], ['18:30', '6:30 pm'], ['19:00', '7:00 pm'], ['19:30', '7:30 pm'], ['20:00', '8:00 pm'], ['20:30', '8:30 pm'], ['21:00', '9:00 pm'], ['21:30', '9:30 pm'], ['22:00', '10:00 pm'], ['22:30', '10:30 pm'], ['23:00', '11:00 pm'], ['23:30', '11:30 pm'], ['closed', sb_('Closed')]];
                $select = '<div class="sb-custom-select">';
                for ($i = 0; $i < count($hours); $i++) {
                    $select .= '<span data-value="' . $hours[$i][0] . '">' . $hours[$i][1] . '</span>';
                }
                $content .= '<div class="sb-timetable">';
                for ($i = 0; $i < 7; $i++) {
                    $content .= '<div data-day="' . strtolower($days[$i]) . '"><label>' . sb_($days[$i]) . '</label><div><div></div><span>' . sb_('To') . '</span><div></div><span>' . sb_('And') . '</span><div></div><span>' . sb_('To') . '</span><div></div></div></div>';
                }
                $content .= $select . '</div></div>';
                break;
            case 'select-images':
                $content .= '<div class="sb-icon-close"></div>';
                for ($i = 0; $i < count($setting['images']); $i++) {
                    $content .= '<div data-value="' . $setting['images'][$i] . '" style="background-image: url(\'' . SB_URL . '/media/' . $setting['images'][$i] . '\')"></div>';
                }
                break;
            case 'select-checkbox':
                $values = $setting['value'];
                $content .= '<select disabled><option>AA</option></select><div class="sb-select-checkbox">';
                for ($i = 0; $i < count($values); $i++) {
                    $content .= '<div id="' . $values[$i]['id'] . '" data-type="checkbox" class="multi-input-checkbox"><input type="checkbox"><label>' . sb_s($values[$i]['title'], $disable_translations) . '</label></div>';
                }
                $content .= '</div>';
                break;
        }
        if (isset($setting['setting']) && ($type == 'multi-input' || !empty($setting['multilingual']))) {
            $content .= '<div class="sb-language-switcher-cnt"><label>' . sb_('Languages') . '</label></div>';
        }
        
         $content .= '</div></div>';

        if ($id == 'tickets-custom-fields') {
            return ticket_custom_field_settings();
        } else if ($id == 'tickets-statuses') {
            return ticket_statuses_settings();
        } else {
            return $content;
        }
    }
    return '';
}

function sb_get_setting_code_help($setting) {
    return isset($setting['help']) && (!sb_is_cloud() || defined('SB_CLOUD_DOCS')) ? '<a href="' . (defined('SB_CLOUD_DOCS') ? (SB_CLOUD_DOCS . substr($setting['help'], strpos($setting['help'], '#'))) : $setting['help']) . '" target="_blank" class="sb-icon-help"></a>' : '';
}

function sb_save_settings($settings, $external_settings = [], $external_settings_translations = []) {
    if (isset($settings)) {
        global $SB_SETTINGS;
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }
        $settings_encoded = sb_db_json_escape($settings);
        if (isset($settings_encoded) && is_string($settings_encoded)) {

            // Save main settings
            $query = 'INSERT INTO sb_settings(name, value) VALUES (\'settings\', \'' . $settings_encoded . '\') ON DUPLICATE KEY UPDATE value = \'' . $settings_encoded . '\'';
            $result = sb_db_query($query);
            if (sb_is_error($result)) {
                return $result;
            }

            // Save external settings
            foreach ($external_settings as $key => $value) {
                sb_save_external_setting($key, $value);
            }

            // Save external settings translations
            $db = '';
            foreach ($external_settings_translations as $key => $value) {
                $name = 'external-settings-translations-' . $key;
                sb_save_external_setting($name, $value);
                $db .= '"' . $name . '",';
            }
            if ($db) {
                sb_db_query('DELETE FROM sb_settings WHERE name LIKE "external-settings-translations-%" AND name NOT IN (' . substr($db, 0, -1) . ')');
            }

            // Update bot
            sb_update_bot($settings['bot-name'][0], $settings['bot-image'][0]);

            // Cloud
            if (sb_is_cloud()) {
                require_once(SB_CLOUD_PATH . '/account/functions.php');
                sb_cloud_save_settings($settings);
            }

            $SB_SETTINGS = $settings;
            return true;
        } else {
            return sb_error('json-encode-error', 'sb_save_settings');
        }
    } else {
        return sb_error('settings-not-found', 'sb_save_settings');
    }
}

function sb_save_external_setting($name, $value) {
    $settings_encoded = sb_db_json_escape($value);
    return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : sb_db_query('INSERT INTO sb_settings(name, value) VALUES (\'' . sb_db_escape($name) . '\', \'' . $settings_encoded . '\') ON DUPLICATE KEY UPDATE value = \'' . $settings_encoded . '\'');
}

function sb_get_settings() {
    global $SB_SETTINGS;
    if (!isset($SB_SETTINGS)) {
        $SB_SETTINGS = sb_get_external_setting('settings', []);
        if (isset($GLOBALS['SB_LOCAL_SETTINGS'])) {
            $SB_SETTINGS = array_merge($SB_SETTINGS, $GLOBALS['SB_LOCAL_SETTINGS']);
        }
    }
    return $SB_SETTINGS;
}

function sb_get_all_settings() {
    $translations = [];
    $settings = [];
    $rows = sb_db_get('SELECT value FROM sb_settings WHERE name="emails" || name="rich-messages" || name="wc-emails"', false);
    for ($i = 0; $i < count($rows); $i++) {
        $settings = array_merge($settings, json_decode($rows[$i]['value'], true));
    }
    $rows = sb_db_get('SELECT name, value FROM sb_settings WHERE name LIKE "external-settings-translations-%"', false);
    for ($i = 0; $i < count($rows); $i++) {
        $translations[substr($rows[$i]['name'], -2)] = json_decode($rows[$i]['value'], true);
    }
    return array_merge(sb_get_settings(), $settings, ['external-settings-translations' => $translations]);
}

function sb_get_setting($id, $default = false) {
    $settings = sb_get_settings();
    if (!sb_is_error($settings)) {
        if (isset($settings[$id]) && !empty($settings[$id][0])) {
            $setting = $settings[$id][0];
            if (is_array($setting) && !isset($setting[0])) {
                $settings_result = [];
                foreach ($setting as $key => $value) {
                    $settings_result[$key] = $value[0];
                }
                return $settings_result;
            } else {
                return $setting;
            }
        } else {
            return $default;
        }
    } else {
        return $settings;
    }
}

function sb_get_multi_setting($id, $sub_id, $default = false) {
    $setting = sb_get_setting($id);
    if ($setting && !sb_is_error($setting) && !empty($setting[$sub_id])) {
        return $setting[$sub_id];
    }
    return $default;
}

function get_ticket_custom_fields()
{
    $query = "SELECT * FROM custom_fields ORDER BY `order_no`";
    return sb_db_get($query, false);
}

function sb_get_external_setting($name, $default = false) {
    $result = sb_db_get('SELECT value FROM sb_settings WHERE name = "' . sb_db_escape($name) . '"', false);
    $settings = [];
    if (empty($result)) {
        return $default;
    }
    if (sb_is_error($settings)) {
        return $settings;
    }
    if (!is_array($result)) {
        return $result;
    }
    if (count($result) == 1) {
        $result = $result[0]['value'];
        return empty($result) && is_string($result) ? $default : json_decode($result, true);
    }
    for ($i = 0; $i < count($result); $i++) {
        $settings = array_merge($settings, json_decode($result[$i]['value'], true));
    }
    return $settings;
}

function sb_get_multilingual_setting($name, $sub_name, $language = false) {
    $language = $language ? $language : sb_get_user_language();
    $value = $language && $language != 'en' ? sb_isset(sb_get_external_setting('external-settings-translations-' . $language), $sub_name) : false;
    if ($value)
        return $value;
    $value = sb_isset(sb_get_external_setting($name), $sub_name);
    if ($value && is_array($value)) {
        $value = $value[0];
        if (!empty($value) && !is_string($value) && array() !== $value) {
            foreach ($value as $key => $setting) {
                $value[$key] = $setting[0];
            }
        }
    }
    return $value;
}

function sb_color_palette($id = '') {
    return '<div data-type="color-palette" data-value="" data-id="' . $id . '" class="sb-color-palette"><span></span><ul><li data-value=""></li><li data-value="red"></li><li data-value="yellow"></li><li data-value="green"></li><li data-value="pink"></li><li data-value="gray"></li><li data-value="blue"></li></ul></div>';
}

function sb_export_settings() {
    $setting_keys = ['automations', 'emails', 'rich-messages', 'settings', 'app-keys', 'articles', 'articles-categories', 'dialogflow-knowledge', 'open-ai-intents-history', 'slack-channels'];
    $settings = [];
    for ($i = 0; $i < count($setting_keys); $i++) {
        $value = sb_isset(sb_db_get('SELECT value FROM sb_settings WHERE name = "' . $setting_keys[$i] . '"'), 'value');
        if ($value) {
            $value = json_decode($value, true);
            if ($value)
                $settings[$setting_keys[$i]] = $value;
        }
    }
    $settings = json_encode($settings, JSON_INVALID_UTF8_IGNORE);
    if ($settings) {
        $name = 'settings' . '_' . rand(100000, 999999999) . '.json';
        $response = sb_file(SB_PATH . '/uploads/' . $name, $settings);
        return $response ? (SB_URL . '/uploads/' . $name) : $response;
    }
    return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : false;
}

function sb_import_settings($file_url) {
    $settings = json_decode(sb_download($file_url), true);
    if ($settings) {
        foreach ($settings as $key => $setting) {
            sb_save_external_setting($key, $setting);
        }
        sb_file_delete(SB_PATH . substr($file_url, strpos($file_url, '/uploads/')));
        return true;
    }
    return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : false;
}

function sb_get_departments() {
    $items = sb_get_setting('departments');
    $count = is_array($items) ? count($items) : 0;
    $departments = [];
    for ($i = 0; $i < $count; $i++) {
        $departments[$items[$i]['department-id']] = ['name' => sb_($items[$i]['department-name']), 'color' => $items[$i]['department-color'], 'image' => sb_isset($items[$i], 'department-image', '')];
    }
    return $departments;
}

function sb_departments($type) {
    $items = sb_get_setting('departments');
    $count = is_array($items) ? count($items) : 0;
    if ($count) {
        switch ($type) {
            case 'select':
                $code = '<div id="department" data-type="select" class="sb-input sb-input-select"><span>' . sb_('Department') . '</span><select><option value=""></option>';
                for ($i = 0; $i < $count; $i++) {
                    $code .= '<option value="' . $items[$i]['department-id'] . '">' . ucfirst(sb_($items[$i]['department-name'])) . '</option>';
                }
                echo $code . '</select></div>';
                break;
            case 'custom-select':
                $code = '<div class="sb-inline sb-inline-departments"><h3>' . sb_('Department') . '</h3><div id="conversation-department" class="sb-select sb-select-colors' . (!sb_is_agent(false, true, true) && !sb_get_multi_setting('agents', 'agents-update-department') ? ' sb-disabled' : '') . '"><p>' . sb_('None') . '</p><ul><li data-id="" data-value="">' . sb_('None') . '</li>';
                for ($i = 0; $i < $count; $i++) {
                    $id = $items[$i]['department-id'];
                    $code .= '<li data-id="' . $id . '" data-value="' . sb_isset($items[$i], 'department-color', $id) . '">' . ucfirst(sb_($items[$i]['department-name'])) . '</li>';
                }
                echo $code . '</ul></div></div>';
                break;
            case 'dashboard':
                $settings = sb_get_setting('departments-settings');
                if ($settings) {
                    $is_image = sb_isset($settings, 'departments-images') && sb_isset($items[0], 'department-image');
                    $code = '<div class="sb-dashboard-departments"><div class="sb-title">' . sb_(sb_isset($settings, 'departments-title', 'Departments')) . '</div><div class="sb-departments-list"' . (sb_isset($settings, 'departments-force-one') ? ' data-force-one="true"' : '') . '>';
                    for ($i = 0; $i < $count; $i++) {
                        $code .= '<div data-id="' . $items[$i]['department-id'] . '">' . ($is_image ? '<img src="' . $items[$i]['department-image'] . '">' : '<div data-color="' . sb_isset($items[$i], 'department-color') . '"></div>') . '<span>' . sb_($items[$i]['department-name']) . '</span></div>';
                    }
                    echo $code . '</div></div>';
                    break;
                }
        }
    }
}

function sb_office_hours() {
    $settings = sb_get_settings();
    $timetable = sb_isset($settings, 'timetable', [[]])[0];
    $now = time();
    $utc_offset = intval(sb_get_setting('timetable-utc', 0));
    $offset = $now - ($utc_offset * 3600);
    $today = strtolower(gmdate('l', $offset));
    $today_array = explode('-', gmdate('m-d-y', $offset));
    $today_array = [intval($today_array[0]), intval($today_array[1]), intval($today_array[2])];
    if (isset($timetable[$today]) && !empty($timetable[$today][0][0])) {
        $status = false;
        for ($i = 0; $i < 3; $i += 2) {
            if (!empty($timetable[$today][$i][0]) && $timetable[$today][$i][0] != 'closed') {
                $start = explode(':', $timetable[$today][$i][0]);
                $end = explode(':', $timetable[$today][$i + 1][0]);
                $office_hours_start = gmmktime(intval($start[0]) + $utc_offset, intval($start[1]), 0, $today_array[0], $today_array[1], $today_array[2]);
                $office_hours_end = gmmktime(intval($end[0]) + $utc_offset, intval($end[1]), 0, $today_array[0], $today_array[1], $today_array[2]);
                if ($now >= $office_hours_start && $now <= $office_hours_end) {
                    $status = true;
                }
            }
        }
        return $status;
    }
    return true;
}

function sb_css($color_1 = false, $color_2 = false, $color_3 = false, $return = false) {
    $css = '';
    $color_1 = $color_1 ? $color_1 : sb_get_setting('color-1');
    $color_2 = $color_2 ? $color_2 : sb_get_setting('color-2');
    $color_3 = $color_3 ? $color_3 : sb_get_setting('color-3');
    $chat_button_offset_top = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-top');
    $chat_button_offset_bottom = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-bottom');
    $chat_button_offset_right = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-right');
    $chat_button_offset_left = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-left');
    $chat_button_offset_left_mobile = sb_get_multi_setting('chat-button-offset', 'chat-button-offset-mobile');
    $chat_button_offset_left_mobile = $chat_button_offset_left_mobile == 'desktop' ? ['@media (min-width: 768px) {', '}'] : ($chat_button_offset_left_mobile == 'mobile' ? ['@media (max-width: 768px) {', '}'] : ['', '']);
    if ($color_1) {
        $css .= '.sb-chat-btn, .sb-chat>div>.sb-header,.sb-chat .sb-dashboard>div>.sb-btn:hover,.sb-chat .sb-scroll-area .sb-header,.sb-input.sb-input-btn>div,div ul.sb-menu li:hover,
                 .sb-select ul li:hover,.sb-popup.sb-emoji .sb-emoji-bar>div.sb-active, .sb-popup.sb-emoji .sb-emoji-bar>div:hover,.sb-btn,a.sb-btn,.sb-rich-message[disabled] .sb-buttons .sb-btn,
                 .sb-ul>span:before,.sb-article-category-links>span+span:before { background-color: ' . $color_1 . '; }';
        $css .= '.sb-chat .sb-dashboard>div>.sb-btn,.sb-search-btn>input,.sb-input>input:focus, .sb-input>select:focus, .sb-input>textarea:focus,
                 .sb-input.sb-input-image .image:hover { border-color: ' . $color_1 . '; }';
        $css .= '.sb-chat .sb-dashboard>div>.sb-btn,.sb-editor .sb-bar-icons>div:hover:before,.sb-articles>div:hover>div,.sb-main .sb-btn-text:hover,.sb-editor .sb-submit,.sb-table input[type="checkbox"]:checked:before,
                 .sb-select p:hover,div ul.sb-menu li.sb-active, .sb-select ul li.sb-active,.sb-search-btn>i:hover,.sb-search-btn.sb-active i,.sb-rich-message .sb-input>span.sb-active:not(.sb-filled),
                 .sb-input.sb-input-image .image:hover:before,.sb-rich-message .sb-card .sb-card-btn,.sb-slider-arrow:hover,.sb-loading:not(.sb-btn):before,.sb-articles>div.sb-title,.sb-article-categories>div:hover, .sb-article-categories>div.sb-active,
                 .sb-article-categories>div span:hover,.sb-article-categories>div span.sb-active,.sb-btn-text:hover,.sb-player > div:hover,.sb-loader:before { color: ' . $color_1 . '; }';
        $css .= '.sb-search-btn>input:focus,.sb-input>input:focus, .sb-input>select:focus, .sb-input>textarea:focus,.sb-input.sb-input-image .image:hover { box-shadow: 0 0 5px rgba(104, 104, 104, 0.2); }';
        $css .= '.sb-list>div.sb-rich-cnt { border-top-color: ' . $color_1 . '; }';
        $css .= '.sb-list>div.sb-right .sb-message, .sb-list>div.sb-right .sb-message a { color: #566069; } .sb-list>div.sb-right,.sb-right .sb-player>div { background-color: #f0f0f0; }';
    }
    if ($color_2) {
        $css .= '.sb-chat-btn:hover,.sb-input.sb-input-btn>div:hover,.sb-btn:hover,a.sb-btn:hover,.sb-rich-message .sb-card .sb-card-btn:hover { background-color: ' . $color_2 . '; }';
        $css .= '.sb-list>.sb-right .sb-message, .sb-list>.sb-right .sb-message a,.sb-editor .sb-submit:hover { color: ' . $color_2 . '; }';
    }
    if ($color_3) {
        $css .= '.sb-list>.sb-right,.sb-user-conversations>li:hover { background-color: ' . $color_3 . '; }';
    }
    if ($chat_button_offset_top) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { top: ' . $chat_button_offset_top . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($chat_button_offset_bottom) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { bottom: ' . $chat_button_offset_bottom . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($chat_button_offset_right) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { right: ' . $chat_button_offset_right . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($chat_button_offset_left) {
        $css .= $chat_button_offset_left_mobile[0] . '.sb-chat-btn { left: ' . $chat_button_offset_left . 'px; }' . $chat_button_offset_left_mobile[1];
    }
    if ($return)
        return $css;
    if ($css) {
        echo '<style>' . $css . '</style>';
    }
    return false;
}

function sb_system_requirements() {
    $checks = [];

    // PHP version
    $checks['php-version'] = version_compare(PHP_VERSION, '7.2.0') >= 0;

    // ZipArchive
    $checks['zip-archive'] = class_exists('ZipArchive');

    // File permissions
    $permissions = [['plugin', SB_PATH], ['uploads', sb_upload_path()], ['apps', SB_PATH . '/apps'], ['languages', SB_PATH . '/resources/languages']];
    for ($i = 0; $i < count($permissions); $i++) {
        $path = $permissions[$i][1] . '/sb-permissions-check.txt';
        sb_file($path, 'permissions-check');
        $checks[$permissions[$i][0] . '-folder'] = file_exists($path) && strpos(file_get_contents($path), 'permissions-check');
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // AJAX file
    $checks['ajax'] = function_exists('curl_init') && sb_download(SB_URL . '/include/ajax.php') == 'true';

    // cURL
    $checks['curl'] = function_exists('curl_version') && is_array(sb_get_versions());

    // MySQL UTF8MB4 support
    $checks['UTF8mb4'] = !sb_is_error(sb_db_query('SET NAMES UTF8mb4'));

    return $checks;
}

function sb_select_html($type) {
    $code = '<select><option value=""></option>';
    $is_countries = $type == 'countries';
    $items = sb_get_json_resource($is_countries ? 'json/countries.json' : 'languages/language-codes.json');
    foreach ($items as $key => $value) {
        $code .= '<option value="' . ($is_countries ? $value : $key) . '">' . sb_($is_countries ? $key : $value) . '</option>';
    }
    return $code . '</select>';
}

function sb_select_phone() {
    $single = sb_get_setting('phone-code');
    if ($single) {
        return $single;
    } else {
        $phones = sb_get_json_resource('json/phone.json');
        $country_code_ip = strtoupper(sb_isset(sb_ip_info('countryCode'), 'countryCode'));
        $phone_prefix_ip = sb_isset($phones, $country_code_ip);
        $code = '<div class="sb-select"><p data-value="' . ($phone_prefix_ip ? '+' . $phone_prefix_ip : '') . '">' . ($phone_prefix_ip ? '<img src="' . SB_URL . '/media/flags/' . strtolower($country_code_ip) . '.png" alt="' . $country_code_ip . '" loading="lazy" />+' . $phone_prefix_ip : '<svg width="23" height="13" viewBox="0 0 900 600" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_946_18107)">
<path d="M0 0H900V600H0V0Z" fill="white"/>
<path d="M0 0H900V200H0V0Z" fill="#FF6820"/>
<path d="M0 400H900V600H0V400Z" fill="#046A38"/>
<path d="M450 392.5C501.086 392.5 542.5 351.086 542.5 300C542.5 248.914 501.086 207.5 450 207.5C398.914 207.5 357.5 248.914 357.5 300C357.5 351.086 398.914 392.5 450 392.5Z" fill="#07038D"/>
<path d="M450 380C494.183 380 530 344.183 530 300C530 255.817 494.183 220 450 220C405.817 220 370 255.817 370 300C370 344.183 405.817 380 450 380Z" fill="white"/>
<path d="M450 316C458.837 316 466 308.837 466 300C466 291.163 458.837 284 450 284C441.163 284 434 291.163 434 300C434 308.837 441.163 316 450 316Z" fill="#07038D"/>
<path d="M450.002 220L453.002 268.141L450.002 291.976L447.002 268.141L450.002 220Z" fill="#07038D"/>
<path d="M459.988 224.155C461.904 224.407 463.663 223.058 463.915 221.142C464.167 219.225 462.818 217.467 460.902 217.215C458.985 216.962 457.227 218.311 456.975 220.228C456.722 222.144 458.072 223.902 459.988 224.155Z" fill="#07038D"/>
<path d="M450.002 380.001L447.002 331.86L450.002 308.024L453.002 331.86L450.002 380.001Z" fill="#07038D"/>
<path d="M440.016 375.846C438.099 375.594 436.341 376.943 436.089 378.859C435.837 380.776 437.186 382.534 439.102 382.786C441.019 383.039 442.777 381.69 443.029 379.773C443.281 377.857 441.932 376.099 440.016 375.846Z" fill="#07038D"/>
<path d="M470.708 222.727L461.146 270.004L452.079 292.251L455.351 268.451L470.708 222.727Z" fill="#07038D"/>
<path d="M479.277 229.323C481.063 230.063 483.111 229.215 483.85 227.429C484.59 225.643 483.742 223.596 481.956 222.856C480.17 222.116 478.123 222.964 477.383 224.75C476.644 226.536 477.492 228.583 479.277 229.323Z" fill="#07038D"/>
<path d="M429.296 377.274L438.858 329.997L447.924 307.75L444.653 331.55L429.296 377.274Z" fill="#07038D"/>
<path d="M420.726 370.678C418.941 369.938 416.893 370.786 416.153 372.572C415.414 374.358 416.262 376.405 418.048 377.145C419.834 377.885 421.881 377.037 422.621 375.251C423.36 373.465 422.512 371.418 420.726 370.678Z" fill="#07038D"/>
<path d="M490 230.718L468.528 273.909L454.012 293.051L463.332 270.909L490 230.718Z" fill="#07038D"/>
<path d="M496.57 239.308C498.104 240.485 500.301 240.196 501.477 238.662C502.654 237.129 502.365 234.931 500.831 233.755C499.298 232.578 497.101 232.867 495.924 234.401C494.747 235.934 495.037 238.131 496.57 239.308Z" fill="#07038D"/>
<path d="M410.002 369.281L431.474 326.09L445.99 306.948L436.67 329.09L410.002 369.281Z" fill="#07038D"/>
<path d="M403.432 360.691C401.898 359.514 399.701 359.803 398.524 361.337C397.348 362.87 397.637 365.068 399.171 366.244C400.704 367.421 402.901 367.132 404.078 365.598C405.255 364.065 404.965 361.868 403.432 360.691Z" fill="#07038D"/>
<path d="M506.569 243.432L474.649 279.594L455.674 294.327L470.407 275.351L506.569 243.432Z" fill="#07038D"/>
<path d="M510.69 253.431C511.867 254.964 514.064 255.253 515.598 254.077C517.131 252.9 517.421 250.703 516.244 249.169C515.067 247.636 512.87 247.347 511.337 248.523C509.803 249.7 509.514 251.897 510.69 253.431Z" fill="#07038D"/>
<path d="M393.431 356.569L425.351 320.407L444.326 305.674L429.593 324.65L393.431 356.569Z" fill="#07038D"/>
<path d="M389.31 346.57C388.133 345.037 385.936 344.748 384.402 345.924C382.869 347.101 382.579 349.298 383.756 350.832C384.933 352.365 387.13 352.654 388.663 351.478C390.197 350.301 390.486 348.104 389.31 346.57Z" fill="#07038D"/>
<path d="M519.281 260L479.09 286.669L456.948 295.989L476.09 281.473L519.281 260Z" fill="#07038D"/>
<path d="M520.677 270.725C521.417 272.511 523.464 273.359 525.25 272.619C527.036 271.879 527.884 269.832 527.144 268.046C526.404 266.26 524.357 265.412 522.571 266.152C520.785 266.892 519.937 268.939 520.677 270.725Z" fill="#07038D"/>
<path d="M380.719 340L420.91 313.331L443.052 304.011L423.91 318.527L380.719 340Z" fill="#07038D"/>
<path d="M379.323 329.275C378.583 327.489 376.536 326.641 374.75 327.381C372.964 328.121 372.116 330.168 372.856 331.954C373.596 333.74 375.643 334.588 377.429 333.848C379.215 333.108 380.063 331.061 379.323 329.275Z" fill="#07038D"/>
<path d="M527.273 279.295L481.548 294.653L457.749 297.924L479.996 288.857L527.273 279.295Z" fill="#07038D"/>
<path d="M525.845 290.016C526.097 291.932 527.855 293.281 529.772 293.029C531.688 292.776 533.037 291.018 532.785 289.102C532.533 287.185 530.775 285.836 528.858 286.089C526.942 286.341 525.593 288.099 525.845 290.016Z" fill="#07038D"/>
<path d="M372.725 320.706L418.45 305.348L442.249 302.077L420.002 311.144L372.725 320.706Z" fill="#07038D"/>
<path d="M374.153 309.985C373.901 308.069 372.143 306.72 370.226 306.972C368.31 307.224 366.961 308.983 367.213 310.899C367.465 312.816 369.224 314.165 371.14 313.912C373.056 313.66 374.406 311.902 374.153 309.985Z" fill="#07038D"/>
<path d="M530 300L481.859 303L458.024 300L481.859 297L530 300Z" fill="#07038D"/>
<path d="M525.845 309.985C525.593 311.902 526.942 313.66 528.858 313.912C530.775 314.164 532.533 312.815 532.785 310.899C533.038 308.982 531.689 307.224 529.772 306.972C527.856 306.72 526.098 308.069 525.845 309.985Z" fill="#07038D"/>
<path d="M370 300L418.141 297L441.976 300L418.141 303L370 300Z" fill="#07038D"/>
<path d="M374.155 290.015C374.407 288.098 373.058 286.34 371.142 286.088C369.225 285.836 367.467 287.185 367.215 289.101C366.962 291.018 368.311 292.776 370.228 293.028C372.144 293.28 373.902 291.931 374.155 290.015Z" fill="#07038D"/>
<path d="M527.276 320.704L479.999 311.142L457.752 302.076L481.552 305.347L527.276 320.704Z" fill="#07038D"/>
<path d="M520.678 329.275C519.938 331.06 520.786 333.108 522.572 333.847C524.358 334.587 526.405 333.739 527.145 331.953C527.885 330.167 527.037 328.12 525.251 327.38C523.465 326.641 521.418 327.489 520.678 329.275Z" fill="#07038D"/>
<path d="M372.726 279.294L420.003 288.856L442.25 297.923L418.45 294.651L372.726 279.294Z" fill="#07038D"/>
<path d="M379.324 270.724C380.064 268.938 379.216 266.89 377.43 266.151C375.644 265.411 373.597 266.259 372.857 268.045C372.117 269.831 372.965 271.878 374.751 272.618C376.537 273.357 378.584 272.509 379.324 270.724Z" fill="#07038D"/>
<path d="M519.283 340L476.092 318.528L456.95 304.012L479.092 313.332L519.283 340Z" fill="#07038D"/>
<path d="M510.693 346.57C509.516 348.104 509.805 350.301 511.339 351.477C512.872 352.654 515.07 352.365 516.246 350.831C517.423 349.298 517.134 347.101 515.6 345.924C514.067 344.747 511.87 345.037 510.693 346.57Z" fill="#07038D"/>
<path d="M380.717 260L423.908 281.472L443.05 295.988L420.908 286.668L380.717 260Z" fill="#07038D"/>
<path d="M389.307 253.43C390.484 251.896 390.195 249.699 388.661 248.523C387.128 247.346 384.93 247.635 383.754 249.169C382.577 250.702 382.866 252.899 384.4 254.076C385.933 255.253 388.13 254.963 389.307 253.43Z" fill="#07038D"/>
<path d="M506.568 356.569L470.406 324.649L455.673 305.674L474.649 320.407L506.568 356.569Z" fill="#07038D"/>
<path d="M496.571 360.691C495.038 361.868 494.749 364.065 495.925 365.599C497.102 367.132 499.299 367.422 500.833 366.245C502.366 365.068 502.655 362.871 501.479 361.338C500.302 359.804 498.105 359.515 496.571 360.691Z" fill="#07038D"/>
<path d="M393.432 243.43L429.594 275.35L444.327 294.326L425.351 279.593L393.432 243.43Z" fill="#07038D"/>
<path d="M403.429 239.308C404.962 238.131 405.251 235.934 404.075 234.4C402.898 232.867 400.701 232.577 399.167 233.754C397.634 234.931 397.345 237.128 398.521 238.662C399.698 240.195 401.895 240.484 403.429 239.308Z" fill="#07038D"/>
<path d="M490 369.282L463.331 329.091L454.011 306.949L468.527 326.091L490 369.282Z" fill="#07038D"/>
<path d="M479.275 370.677C477.489 371.417 476.641 373.464 477.381 375.25C478.121 377.036 480.168 377.884 481.954 377.144C483.74 376.404 484.588 374.357 483.848 372.571C483.108 370.785 481.061 369.937 479.275 370.677Z" fill="#07038D"/>
<path d="M410 230.718L436.669 270.909L445.989 293.051L431.473 273.909L410 230.718Z" fill="#07038D"/>
<path d="M420.725 229.323C422.511 228.583 423.359 226.536 422.619 224.75C421.879 222.964 419.832 222.116 418.046 222.856C416.26 223.596 415.412 225.643 416.152 227.429C416.892 229.215 418.939 230.063 420.725 229.323Z" fill="#07038D"/>
<path d="M470.706 377.275L455.348 331.55L452.077 307.751L461.144 329.998L470.706 377.275Z" fill="#07038D"/>
<path d="M459.985 375.846C458.069 376.098 456.72 377.856 456.972 379.773C457.224 381.689 458.983 383.038 460.899 382.786C462.816 382.534 464.165 380.776 463.912 378.859C463.66 376.943 461.902 375.594 459.985 375.846Z" fill="#07038D"/>
<path d="M429.294 222.725L444.652 268.45L447.923 292.249L438.856 270.002L429.294 222.725Z" fill="#07038D"/>
<path d="M440.015 224.154C441.931 223.902 443.28 222.144 443.028 220.227C442.776 218.311 441.017 216.962 439.101 217.214C437.184 217.466 435.835 219.224 436.088 221.141C436.34 223.057 438.098 224.406 440.015 224.154Z" fill="#07038D"/>
</g>
<defs>
<clipPath id="clip0_946_18107">
<rect width="900" height="600" fill="white"/>
</clipPath>
</defs>
</svg> &nbsp;+91') . '</p><div class="sb-select-search"><input type="text" placeholder="' . sb_('Search ...') . '" /></div><ul class="sb-scroll-area">';
        foreach ($phones as $country_code => $phone_prefix) {
            $country_code = strtolower($country_code);
            $code .= ' <li data-value="+' . $phone_prefix . '" data-country="' . $country_code . '"' . ($phone_prefix_ip == $phone_prefix ? ' class="sb-active"' : '') . '><img src="' . SB_URL . '/media/flags/' . $country_code . '.png" alt="' . $country_code . '" loading="lazy" />+' . $phone_prefix . '</li>';
        }
        return $code . '</ul></div>';
    }
}

function sb_get_config_details($path) {
    $details = [];
    $slugs = ['SB_URL', 'SB_DB_NAME', 'SB_DB_USER', 'SB_DB_PASSWORD', 'SB_DB_HOST', 'SB_DB_PORT'];
    $lines = preg_split("/\r\n|\n|\r/", file_get_contents($path));
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        for ($j = 0; $j < count($slugs); $j++) {
            if (strpos($line, $slugs[$j])) {
                $details[$slugs[$j]] = str_replace(['define(\'' . $slugs[$j] . '\', \'', '\');'], '', $line);
            }
        }
    }
    return $details;
}

function sb_update_sw($url) {
    $path = SB_PATH . '/sw.js';
    if (!file_exists($path)) {
        copy(SB_PATH . '/resources/sw.js', $path);
    }
    $lines = preg_split("/\r\n|\n|\r/", file_get_contents($path));
    $code = '';
    $url = str_replace(['importScripts(', ');', '\'', '"'], '', $url);
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'importScripts') !== false) {
            $lines[$i] = 'importScripts(\'' . $url . '\');';
        }
        $code .= $lines[$i] . "\n";
    }
    return sb_file(SB_PATH . '/sw.js', $code);
}

/*
 * -----------------------------------------------------------
 * ARTICLES
 * -----------------------------------------------------------
 *
 * 1. Save all articles
 * 2. Save all articles categories
 * 3. Returns all articles
 * 4. Returns all articles categories
 * 5. Returns a single article category
 * 6. Article search
 * 7. Article ratings
 * 8. Inits articles for the admin area
 * 9. Generates an excerpt of the article contents
 * 10. Returns the article.php
 * 11. Returns the article URL
 * 12. Returns the articles page URL
 * 13. Checks if the article URL rewriting is enabled
 *
 */

// Deprecated
function sb_temp_deprecated_articles_migration() {
    $articles = sb_get_external_setting('articles');
    $articles_translations = sb_db_get('SELECT name, value FROM sb_settings WHERE name LIKE "articles-translations-%"', false);
    $now = date('Y-m-d H:i:s');
    $ids = [];
    if ($articles) {
        for ($i = 0; $i < count($articles); $i++) {
            $categories = sb_isset($articles[$i], 'categories');
            $ids[$articles[$i]['id']] = sb_db_query('INSERT INTO sb_articles (title, content, editor_js, nav, link, category, parent_category, language, slug, update_time) VALUES ( "' . sb_db_escape(sb_sanatize_string($articles[$i]['title'])) . '", "' . str_replace(['\"', '"'], ['"', '\"'], sb_sanatize_string($articles[$i]['content'])) . '", "' . sb_db_escape(sb_sanatize_string(json_encode($articles[$i]['editor_js'], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE))) . '", "", "' . sb_db_escape(sb_sanatize_string($articles[$i]['link'])) . '", "' . (empty($categories[0]) ? '' : sb_db_escape(sb_sanatize_string($categories[0]))) . '", "' . sb_db_escape(sb_sanatize_string($articles[$i]['parent_category'])) . '", "", "' . sb_db_escape(sb_sanatize_string(sb_string_slug($articles[$i]['title']))) . '", "' . $now . '")', true);
        }
        for ($j = 0; $j < count($articles_translations); $j++) {
            $articles = json_decode($articles_translations[$j]['value'], true);
            for ($i = 0; $i < count($articles); $i++) {
                $categories = sb_isset($articles[$i], 'categories');
                sb_db_query('INSERT INTO sb_articles (title, content, editor_js, nav, link, category, parent_category, language, parent_id, slug, update_time) VALUES ("' . sb_db_escape(sb_sanatize_string($articles[$i]['title'])) . '", "' . str_replace(['\"', '"'], ['"', '\"'], sb_sanatize_string($articles[$i]['content'])) . '", "' . sb_db_escape(sb_sanatize_string(json_encode($articles[$i]['editor_js'], JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE))) . '", "", "' . sb_db_escape(sb_sanatize_string($articles[$i]['link'])) . '", "' . (empty($categories[0]) ? '' : sb_db_escape(sb_sanatize_string($categories[0]))) . '", "' . sb_db_escape(sb_sanatize_string(sb_isset($articles[$i], 'parent_category', ''))) . '", "' . sb_db_escape(sb_sanatize_string(str_replace('articles-translations-', '', $articles_translations[$j]['name']))) . '", "' . $ids[$articles[$i]['id']] . '", "' . sb_db_escape(sb_sanatize_string(sb_string_slug($articles[$i]['title']))) . '", "' . $now . '")');
            }
        }
        $parent_categories = array_column(sb_db_get('SELECT parent_category FROM sb_articles WHERE parent_category <> "" GROUP BY parent_category', false), 'parent_category');
        $categories = sb_get_articles_categories();
        for ($i = 0; $i < count($categories); $i++) {
            for ($j = 0; $j < count($parent_categories); $j++) {
                if (strtolower($categories[$i]['id']) == strtolower($parent_categories[$j])) {
                    $categories[$i]['parent'] = true;
                    break;
                }
            }
            $category_new_id = sb_string_slug($categories[$i]['title']);
            sb_db_query('UPDATE sb_articles SET category = "' . $category_new_id . '" WHERE category = "' . $categories[$i]['id'] . '"');
            sb_db_query('UPDATE sb_articles SET parent_category = "' . $category_new_id . '" WHERE parent_category = "' . $categories[$i]['id'] . '"');
            $categories[$i]['id'] = $category_new_id;
        }
        sb_save_articles_categories($categories);
        sb_db_query('DELETE FROM sb_settings WHERE name = "articles"');
        sb_db_query('DELETE FROM sb_settings WHERE name LIKE "articles-translations-%"');
    }
}
// Deprecated

function sb_save_article($article) {
    if (is_string($article)) {
        $article = json_decode($article, true);
    }
    $article_id = sb_db_escape(sb_isset($article, 'id'), true);
    if (sb_isset($article, 'delete')) {
        return sb_db_query('DELETE FROM sb_articles WHERE id = ' . $article_id);
    }
    $article_title = sb_db_escape(sb_sanatize_string($article['title']));
    $article_content = str_replace(['\"', '"'], ['"', '\"'], sb_sanatize_string(sb_isset($article, 'content')));
    $article_editor_js = sb_isset($article, 'editor_js', '');
    $article_link = trim(sb_db_escape(sb_sanatize_string(sb_isset($article, 'link'))));
    $article_category = sb_db_escape(sb_sanatize_string(sb_isset($article, 'category', '')));
    $article_parent_category = sb_db_escape(sb_sanatize_string(sb_isset($article, 'parent_category', '')));
    $article_language = sb_db_escape(sb_sanatize_string(sb_isset($article, 'language', '')));
    $article_parent_id = sb_db_escape(sb_isset($article, 'parent_id', 'NULL'));
    $article_slug = sb_db_escape(sb_sanatize_string(sb_string_slug($article_title)));
    preg_match_all('/<code>(.*?)<\/code>/s', $article_content, $matches);
    foreach ($matches[1] as $code) {
        $article_content = str_replace($code, htmlspecialchars($code), $article_content);
    }
    if ($article_editor_js) {
        $article_editor_js = sb_db_json_escape($article_editor_js);
    }
    if (sb_db_get('SELECT slug FROM sb_articles WHERE slug = "' . $article_slug . '"' . ($article_id ? ' AND id <> ' . $article_id : ''))) {
        $random = rand(1000, 9999);
        if ($article_id) {
            $saved_slug = sb_db_get('SELECT slug FROM sb_articles WHERE id = ' . $article_id)['slug'];
            $saved_random = str_replace($article_slug . '-', '', $saved_slug);
            if (is_numeric($saved_random)) {
                $random = $saved_random;
            }
            $article_slug .= '-' . $random;
        }
    }
    if (empty($article_title)) {
        $article_title = '#' . $article_id;
    }
    if (!$article_id) {
        $response = sb_db_query('INSERT INTO sb_articles (title, content, editor_js, nav, link, category, parent_category, language, parent_id, slug, update_time) VALUES ("' . $article_title . '", "' . $article_content . '", "' . $article_editor_js . '", "", "' . $article_link . '", "' . $article_category . '", "' . $article_parent_category . '", "' . $article_language . '", ' . $article_parent_id . ', "' . $article_slug . '", "' . date('Y-m-d H:i:s') . '")', true);
    } else {
        sb_db_query('UPDATE sb_articles SET category = "' . $article_category . '", parent_category = "' . $article_parent_category . '" WHERE parent_id = ' . $article_id);
        $response = sb_db_query('UPDATE sb_articles SET title = "' . $article_title . '", content = "' . $article_content . '", editor_js = "' . $article_editor_js . '", link = "' . $article_link . '", category = "' . $article_category . '", parent_category = "' . $article_parent_category . '", language = "' . $article_language . '", parent_id = ' . $article_parent_id . ', slug = "' . $article_slug . '", update_time = "' . date('Y-m-d H:i:s') . '" WHERE id = ' . $article_id);
    }
    return $response;
}

function sb_save_articles_categories($categories) {
    if (is_string($categories)) {
        $categories = json_decode($categories, true);
    }
    $previous_categories = sb_get_articles_categories();
    $response = sb_save_external_setting('articles-categories', $categories);
    if ($response) {
        $query_categories = ['', ''];
        for ($i = 0; $i < count($categories); $i++) {
            $id = $categories[$i]['id'];
            $is_sub_category = empty($categories[$i]['parent']);
            $query_categories[$is_sub_category] .= '"' . $id . '",';
            if (isset($previous_categories[$i]) && $previous_categories[$i]['id'] != $id) {
                $response = sb_db_query('UPDATE sb_articles SET ' . ($is_sub_category ? 'category' : 'parent_category') . ' = "' . $id . '" WHERE ' . ($is_sub_category ? 'category' : 'parent_category') . ' = "' . $previous_categories[$i]['id'] . '"');
            }
        }
        sb_db_query('UPDATE sb_articles SET parent_category = ""' . ($query_categories[0] ? ' WHERE parent_category NOT IN (' . substr($query_categories[0], 0, -1) . ')' : ''));
        sb_db_query('UPDATE sb_articles SET category = ""' . ($query_categories[1] ? ' WHERE category NOT IN (' . substr($query_categories[1], 0, -1) . ')' : ''));
    }
    return $response;
}

function sb_get_articles($article_id = false, $count = false, $full = false, $categories = false, $language = false, $skip_language = false) {
    $query_part = '';
    if (is_array($language)) {
        $language = $language[0];
    }
    if ($language == 'en' || ($skip_language && !sb_get_setting('front-auto-translations'))) {
        $language = false;
    }
    if ($article_id) {
        $article_id = is_array($article_id) ? $article_id : explode(',', str_replace(' ', '', $article_id));
        $count = count($article_id);
        $query_part = ($count == 1 && !is_numeric($article_id[0]) ? 'slug' : 'id') . ' IN ("' . implode('","', $article_id) . '")';
        for ($i = 0; $i < $count; $i++) {
            sb_reports_update('articles-views', false, false, $article_id[$i]);
        }
        if ($count == 1) {
            $language = 'all';
        }
    }
    if (is_string($categories)) {
        $categories = [$categories];
    }
    if ($categories) {
        $query_part .= ($query_part ? ' AND ' : '') . '(category IN ("' . implode('","', $categories) . '") OR parent_category IN ("' . implode('","', $categories) . '"))';
    }
    $is_multilingual_by_translation = defined('SB_DIALOGFLOW') && $language && $language != 'all' && (sb_get_multi_setting('google', 'google-multilingual-translation'));
    $query_part .= ($language == 'all' || $is_multilingual_by_translation ? '' : ($query_part ? ' AND ' : '') . 'language = "' . ($language ? sb_db_escape($language) : '') . '"');
    $articles = sb_db_get('SELECT * FROM sb_articles' . ($query_part ? ' WHERE ' . $query_part : '') . '  ORDER BY id' . ($count ? ' LIMIT ' . sb_db_escape($count, true) : ''), false);
    if ($is_multilingual_by_translation) {
        $articles_2 = [];
        for ($i = 0; $i < count($articles); $i++) {
            if (empty($articles[$i]['parent_id'])) {
                $article_id_2 = $articles[$i]['id'];
                $is_translated = false;
                for ($j = 0; $j < count($articles); $j++) {
                    if ($article_id_2 == $articles[$j]['parent_id'] && $articles[$j]['language'] == $language) {
                        array_push($articles_2, $articles[$j]);
                        $is_translated = true;
                        break;
                    }
                }
                if (!$is_translated) {
                    $article = sb_google_translate_article($article_id_2, $language);
                    $article['id'] = sb_save_article($article);
                    array_push($articles_2, $article);
                }
            }
        }
        $articles = $articles_2;
    }
    if (empty($articles) && $language && $language != 'all') {
        return sb_get_articles($article_id, $count, $full, $categories);
    }
    if (!$full) {
        $articles = sb_articles_excerpt($articles);
    }
    return $articles;
}

function sb_get_articles_categories($category_type = false) {
    $categories = sb_isset($GLOBALS, 'SB_ARTICLES_CATEGORIES');
    if (!$categories) {
        $categories = sb_get_external_setting('articles-categories', []);
    }
    if ($category_type) {
        $is_parent = $category_type == 'parent';
        $response = [];
        for ($i = 0; $i < count($categories); $i++) {
            $is_parent_item = sb_isset($categories[$i], 'parent');
            if (($is_parent_item && $is_parent) || (!$is_parent_item && !$is_parent)) {
                array_push($response, $categories[$i]);
            }
        }
        return $response;
    }
    return $categories;
}

function sb_get_article_category($category_id) {
    $categories = sb_get_articles_categories();
    for ($i = 0; $i < count($categories); $i++) {
        if ($categories[$i]['id'] == $category_id) {
            return $categories[$i];
        }
    }
    return false;
}

function sb_search_articles($search, $language = false) {
    $search = sb_db_escape($search);
    if (empty($search)) {
        return [];
    }
    if (is_array($language)) {
        $language = $language[0];
    }
    if ($language == 'en') {
        $language = false;
    }
    $articles = sb_db_get('SELECT * FROM sb_articles WHERE (title LIKE "%' . $search . '%" OR content LIKE "%' . $search . '%" OR link LIKE "%' . $search . '%") ' . ($language == 'all' ? '' : 'AND language = "' . ($language ? sb_db_escape($language) : '') . '"') . ' ORDER BY id', false);
    if (empty($articles) && $language && $language != 'en') {
        return sb_search_articles($search);
    }
    $articles = sb_articles_excerpt($articles);
    sb_reports_update('articles-searches', $search);
    return $articles;
}

function sb_article_ratings($article_id, $rating = false) {
    $article_id = sb_db_escape($article_id);
    $rating = $rating ? sb_db_escape($rating) : false;
    $now = gmdate('Y-m-d');
    $ratings = sb_isset(sb_db_get('SELECT value FROM sb_reports WHERE name = "article-ratings" AND extra = "' . sb_db_escape($article_id) . '" AND creation_time = "' . $now . '"'), 'value', []);
    if ($rating) {
        if (empty($ratings)) {
            return sb_db_query('INSERT INTO sb_reports (name, value, creation_time, external_id, extra) VALUES ("article-ratings", "[' . $rating . ']", "' . $now . '", NULL, "' . $article_id . '")');
        } else {
            $ratings = json_decode($ratings);
            array_push($ratings, intval($rating));
            return sb_db_query('UPDATE sb_reports SET value = "' . json_encode($ratings) . '" WHERE name = "article-ratings" AND extra = "' . $article_id . '"');
        }
    }
    return $ratings;
}

function sb_init_articles_admin() {
    $articles = sb_get_external_setting('articles'); // Deprecated
    if ($articles) { // Deprecated
        sb_temp_deprecated_articles_migration(); // Deprecated
    } // Deprecated
    $articles_all = sb_db_get('SELECT id, title, language, parent_id FROM sb_articles ORDER BY id', false);
    $articles = [];
    $articles_translations = [];
    $cloud_chat_id = '';
    for ($i = 0; $i < count($articles_all); $i++) {
        if ($articles_all[$i]['language']) {
            $id = $articles_all[$i]['parent_id'];
            $languages = sb_isset($articles_translations, $id, []);
            array_push($languages, [$articles_all[$i]['language'], $articles_all[$i]['id']]);
            $articles_translations[$id] = $languages;
        } else {
            array_push($articles, $articles_all[$i]);
        }
    }
    if (defined('ARTICLES_URL')) {
        require_once(SB_CLOUD_PATH . '/account/functions.php');
        $cloud_chat_id = account_chat_id(get_active_account_id());
    }
    return [$articles, sb_get_articles_categories(), $articles_translations, sb_get_articles_page_url(), sb_is_articles_url_rewrite(false), $cloud_chat_id];
}

function sb_articles_excerpt($articles) {
    for ($i = 0; $i < count($articles); $i++) {
        $content = strip_tags(sb_isset($articles[$i], 'content', ''));
        $articles[$i]['editor_js'] = '';
        $articles[$i]['content'] = strlen($content) > 100 ? mb_substr($content, 0, 100) . '...' : $content;
    }
    return $articles;
}

function sb_get_articles_page() {
    require_once(SB_PATH . '/include/articles.php');
}

function sb_get_article_url($article) {
    if (is_numeric($article)) {
        $article = sb_db_get('SELECT slug, id FROM sb_articles WHERE id = ' . sb_db_escape($article, true));
    }
    if (empty($article)) {
        return '';
    }
    $articles_page_url = sb_get_articles_page_url();
    $articles_page_url_slash = $articles_page_url . (substr($articles_page_url, -1) == '/' ? '' : '/');
    $url_rewrite = $articles_page_url && sb_is_articles_url_rewrite();
    return $url_rewrite ? $articles_page_url_slash . sb_isset($article, 'slug', $article['id']) : $articles_page_url . '?article_id=' . $article['id'];
}

function sb_get_articles_page_url() {
    return trim(sb_get_setting('articles-page-url', sb_defined('ARTICLES_URL')));
}

function sb_is_articles_url_rewrite($check_referrer = true) {
    return sb_get_setting('articles-url-rewrite') || (defined('ARTICLES_URL') && (!$check_referrer || empty($_SERVER['HTTP_REFERER']) || strpos(ARTICLES_URL, $_SERVER['HTTP_REFERER'])) && (!sb_get_setting('articles-page-url') || strpos(ARTICLES_URL, parse_url(sb_get_setting('articles-page-url'), PHP_URL_HOST))));
}

?>