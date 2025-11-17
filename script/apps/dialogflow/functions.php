<?php

/*
 * ==========================================================
 * AI APP
 * ==========================================================
 *
 * Artificial Intelligence app. © 2017-2025 board.support. All rights reserved.
 *
 */

define('SB_DIALOGFLOW', '1.5.6');

/*
 * -----------------------------------------------------------
 * SYNC
 * -----------------------------------------------------------
 *
 */

if (isset($_GET['code']) && file_exists('../../include/functions.php')) {
    require('../../include/functions.php');
    sb_cloud_load();
    $info = sb_google_key();
    $query = '{ code: "' . $_GET['code'] . '", grant_type: "authorization_code", client_id: "' . $info[0] . '", client_secret: "' . $info[1] . '", redirect_uri: "' . SB_URL . '/apps/dialogflow/functions.php" }';
    $response = sb_curl('https://accounts.google.com/o/oauth2/token', $query, ['Content-Type: application/json', 'Content-Length: ' . strlen($query)]);
    sb_delete_external_setting('google-token');
    die($response && isset($response['refresh_token']) ? '<script>document.location = "' . (sb_is_cloud() ? str_replace('/script', '', SB_URL) : SB_URL . '/admin.php') . '?setting=dialogflow&refresh_token=' . $response['refresh_token'] . '";</script>' : 'Error while trying to get Dialogflow token. Dialogflow code: ' . $_GET['code'] . '. Response: ' . json_encode($response));
}

/*
 * -----------------------------------------------------------
 * OBJECTS
 * -----------------------------------------------------------
 *
 * Dialogflow objects
 *
 */

class SBDialogflowEntity {
    public $data;

    function __construct($id, $values, $prompts = []) {
        $this->data = ['displayName' => $id, 'entities' => $values, 'kind' => 'KIND_MAP', 'enableFuzzyExtraction' => true];
    }

    public function __toString() {
        return $this->json();
    }

    function json() {
        return json_encode($this->data);
    }

    function data() {
        return $this->data;
    }
}

class SBDialogflowIntent {
    public $data;

    function __construct($name, $training_phrases, $bot_responses, $entities = [], $entities_values = [], $payload = false, $input_contexts = [], $output_contexts = [], $prompts = [], $id = false) {
        $training_phrases_api = [];
        $parameters = [];
        $parameters_checks = [];
        $messages = [];
        $json = json_decode(file_get_contents(SB_PATH . '/apps/dialogflow/data.json'), true);
        $entities = array_merge($entities, $json['entities']);
        $entities_values = array_merge($entities_values, $json['entities-values']);
        $project_id = false;
        if (is_string($bot_responses)) {
            $bot_responses = [$bot_responses];
        }
        if (is_string($training_phrases)) {
            $training_phrases = [$training_phrases];
        }
        for ($i = 0; $i < count($training_phrases); $i++) {
            $parts_temp = explode('@', $training_phrases[$i]);
            $parts = [];
            $parts_after = false;
            for ($j = 0; $j < count($parts_temp); $j++) {
                $part = ['text' => ($j == 0 ? '' : '@') . $parts_temp[$j]];
                for ($y = 0; $y < count($entities); $y++) {
                    $entity = is_string($entities[$y]) ? $entities[$y] : $entities[$y]['displayName'];
                    $entity_type = '@' . $entity;
                    $entity_name = str_replace('.', '-', $entity);
                    $entity_value = empty($entities_values[$entity]) ? $entity_type : $entities_values[$entity][array_rand($entities_values[$entity])];
                    if (str_contains($part['text'], $entity_type)) {
                        $mandatory = true;
                        if (str_contains($part['text'], $entity_type . '*')) {
                            $mandatory = false;
                            $part['text'] = str_replace($entity_type . '*', $entity_type, $part['text']);
                        }
                        $parts_after = explode($entity_type, $part['text']);
                        $part = ['text' => $entity_value, 'entityType' => $entity_type, 'alias' => $entity_name, 'userDefined' => true];
                        if (count($parts_after) > 1) {
                            $parts_after = ['text' => $parts_after[1]];
                        } else {
                            $parts_after = false;
                        }
                        if (!in_array($entity, $parameters_checks)) {
                            array_push($parameters, ['displayName' => $entity_name, 'value' => '$' . $entity, 'mandatory' => $mandatory, 'entityTypeDisplayName' => '@' . $entity, 'prompts' => sb_isset($prompts, $entity_name, [])]);
                            array_push($parameters_checks, $entity);
                        }
                        break;
                    }
                }
                array_push($parts, $part);
                if ($parts_after)
                    array_push($parts, $parts_after);
            }
            array_push($training_phrases_api, ['type' => 'EXAMPLE', 'parts' => $parts]);
        }
        for ($i = 0; $i < count($bot_responses); $i++) {
            array_push($messages, ['text' => ['text' => $bot_responses[$i]]]);
        }
        if (!empty($payload)) {
            $std = new stdClass;
            $std->payload = $payload;
            array_push($messages, $std);
        }
        if (!empty($input_contexts) && is_array($input_contexts)) {
            $project_id = sb_get_multi_setting('google', 'google-project-id');
            for ($i = 0; $i < count($input_contexts); $i++) {
                $input_contexts[$i] = 'projects/' . $project_id . '/agent/sessions/-/contexts/' . $input_contexts[$i];
            }
        }
        if (!empty($output_contexts) && is_array($output_contexts)) {
            $project_id = $project_id ? $project_id : sb_get_multi_setting('google', 'google-project-id');
            for ($i = 0; $i < count($output_contexts); $i++) {
                $is_array = is_array($output_contexts[$i]);
                $output_contexts[$i] = ['name' => 'projects/' . $project_id . '/agent/sessions/-/contexts/' . ($is_array ? $output_contexts[$i][0] : $output_contexts[$i]), 'lifespanCount' => ($is_array ? $output_contexts[$i][1] : 3)];
            }
        }
        $t = ['displayName' => $name, 'trainingPhrases' => $training_phrases_api, 'parameters' => $parameters, 'messages' => $messages, 'inputContextNames' => $input_contexts, 'outputContexts' => $output_contexts];
        if ($id) {
            $t['name'] = $id;
        }
        $this->data = $t;
    }

    public function __toString() {
        return $this->json();
    }

    function json() {
        return json_encode($this->data);
    }

    function data() {
        return $this->data;
    }
}

/*
 * -----------------------------------------------------------
 * DIALOGFLOW MESSAGE
 * -----------------------------------------------------------
 *
 * Send the user message to the bot and return the reply
 *
 */

$sb_recursion_dialogflow = [true, true, true, true, true];
function sb_dialogflow_message($conversation_id = false, $message = '', $language = false, $attachments = [], $event = '', $parameters = false, $project_id = false, $session_id = false, $audio = false) {
    global $sb_recursion_dialogflow;
    if (sb_is_cloud()) {
        sb_cloud_membership_validation(true);
    }
    $smart_reply = $event == 'smart-reply';
    $user_id = $conversation_id && !$smart_reply && sb_is_agent() ? sb_db_get('SELECT user_id FROM sb_conversations WHERE id = ' . sb_db_escape($conversation_id, true))['user_id'] : sb_get_active_user_ID();
    if (!sb_cloud_membership_has_credits('google')) {
        return sb_error('no-credits', 'sb_dialogflow_message');
    }
    $cx = sb_get_multi_setting('google', 'dialogflow-edition', sb_get_setting('dialogflow-edition')) == 'cx'; // Deprecated: sb_get_setting('dialogflow-edition', 'es')
    $query = ['queryInput' => [], 'queryParams' => $cx ? ['parameters' => ['user_id' => $user_id, 'conversation_id' => $conversation_id]] : ['payload' => ['support_board' => ['conversation_id' => $conversation_id, 'user_id' => $user_id]]]];
    $bot_id = sb_get_bot_ID();
    $human_takeover = sb_dialogflow_get_human_takeover_settings();
    $human_takeover = $human_takeover['active'] ? $human_takeover : false;
    $response_success = [];
    $multilingual = sb_get_setting('dialogflow-multilingual') || sb_get_multi_setting('google', 'google-multilingual'); // Deprecated: sb_get_setting('dialogflow-multilingual')
    $multilingual_translation = sb_get_setting('dialogflow-multilingual-translation') || sb_get_multi_setting('google', 'google-multilingual-translation'); // Deprecated: sb_get_setting('dialogflow-multilingual-translation')
    $user_language = $multilingual_translation ? sb_get_user_extra($user_id, 'language') : false;
    $unknow_language_message = false;
    $dialogflow_agent = false;
    $is_human_takeover = $conversation_id && !$smart_reply && !sb_dialogflow_is_human_takeover($conversation_id);
    $message_id = false;
    $translations = false;
    $payload = [];
    if ($human_takeover && $conversation_id && sb_dialogflow_is_human_takeover($conversation_id) && sb_isset($human_takeover, 'disable_chatbot')) {
        return false;
    }
    if ($event == 'translations') {
        unset($GLOBALS['SB_LANGUAGE']);
        $translations = sb_get_current_translations();
    }
    if ($parameters) {
        $query['queryParams'][$cx ? 'parameters' : 'payload'] = array_merge($query['queryParams'][$cx ? 'parameters' : 'payload'], $parameters);
    }
    if (empty($bot_id)) {
        return new SBValidationError('bot-id-not-found');
    }
    if (!$language || empty($language[0])) {
        $language = $multilingual ? ($user_language ? $user_language : sb_get_user_language($user_id)) : false;
        $language = $language ? [$language] : ['en'];
    } else {
        $language[0] = sb_dialogflow_language_code($language[0]);
        if (count($language) > 1 && $language[1] == 'language-detection') {
            $response_success['language_detection'] = $language[0];
        }
    }
    $query['queryInput']['languageCode'] = $language[0];
    $token = sb_google_get_token();

    // Attachments
    $attachments = sb_json_array($attachments);
    for ($i = 0; $i < count($attachments); $i++) {
        $message .= ' ' . $attachments[$i][1];
    }

    if (!empty($audio)) {

        // Audio
        if (pathinfo($audio, PATHINFO_EXTENSION) == 'ogg' && sb_get_multi_setting('open-ai', 'open-ai-speech-recognition')) {
            $message .= sb_open_ai_audio_to_text($audio);
            $audio = false;
        } else {
            $audio = strpos($audio, 'http') === 0 ? sb_get($audio) : file_get_contents($audio);
            if ($cx) {
                $query['queryInput']['audio'] = ['config' => ['SampleRateHertz' => 16000, 'audioEncoding' => 'AUDIO_ENCODING_OGG_OPUS', 'languageCode' => $language[0]], 'audio' => base64_encode($audio)];
            } else {
                $query['queryInput']['audioConfig'] = ['audioEncoding' => 'AUDIO_ENCODING_UNSPECIFIED', 'languageCode' => $language[0]];
                $query['inputAudio'] = base64_encode($audio);
            }
        }
    }
    if (empty($audio) && !empty($message)) {

        // Message
        $query['queryInput']['text'] = ['text' => $message, 'languageCode' => $language[0]];
    } else if (!empty($event)) {

        // Events
        $query['queryInput']['event'] = $cx ? ['event' => $event] : ['name' => $event, 'languageCode' => $language[0]];
    }

    // Department linking
    if (!$project_id && $conversation_id && !$smart_reply) {
        $departments = sb_get_setting('dialogflow-departments');
        if ($departments && is_array($departments)) {
            $department = sb_db_get('SELECT department FROM sb_conversations WHERE id = ' . sb_db_escape($conversation_id, true))['department'];
            for ($i = 0; $i < count($departments); $i++) {
                if ($departments[$i]['dialogflow-departments-id'] == $department) {
                    $project_id = $departments[$i]['dialogflow-departments-agent'];
                    break;
                }
            }
        }
    }

    // Dialogflow response
    $session_id = $session_id ? $session_id : ($user_id ? $user_id : 'sb');
    $response = sb_dialogflow_curl('/agent/sessions/' . $session_id . ':detectIntent', $query, false, 'POST', $token, $project_id);
    sb_cloud_membership_use_credits(($cx ? 'cx' : 'es') . (empty($audio) ? '' : '-audio'), 'google', strlen($audio));
    sb_webhooks('SBDialogflowMessage', ['response' => $response, 'message' => $message, 'conversation_id' => $conversation_id]);
    if (is_string($response)) {
        if (strpos($response, 'Error 404')) {
            return ['response' => ['error' => 'Error 404. Dialogflow Project ID or Agent Name not found.']];
        }
        $response = [];
    }
    if (sb_is_error($response)) {
        return $response;
    }
    if (isset($response['error'])) {
        sb_error('dialogflow-access-token', 'sb_dialogflow_message', $response);
    }
    if ($event == 'Welcome') {
        sb_dialogflow_set_active_context('welcome', [], 2, $token, $user_id, $language[0]);
    }
    if (isset($response['error'])) {
        return ['response' => $response];
    }
    $response_query = sb_isset($response, 'queryResult', []);
    $messages = sb_isset($response_query, 'fulfillmentMessages', sb_isset($response_query, 'responseMessages', []));
    $unknow_answer = sb_dialogflow_is_unknow($response);
    $results = [];
    $message_length = strlen($message);
    if (!$messages && isset($response_query['knowledgeAnswers'])) {
        $messages = sb_isset($response_query['knowledgeAnswers'], 'answers', []);
        for ($i = 0; $i < count($messages); $i++) {
            $messages[$i] = ['text' => ['text' => [$messages[$i]['answer']]]];
        }
    }
    if (isset($messages[0]) && isset($messages[0]['text']) && $messages[0]['text']['text'][0] == 'skip-intent') {
        $unknow_answer = true;
        $messages = [];
    }
    if (isset($response_query['webhookPayload'])) {
        array_push($messages, ['payload' => $response_query['webhookPayload']]);
    }

    // Parameters
    $parameters = isset($response_query['parameters']) && count($response_query['parameters']) ? $response_query['parameters'] : [];
    if (isset($response_query['outputContexts']) && count($response_query['outputContexts']) && isset($response_query['outputContexts'][0]['parameters'])) {
        for ($i = 0; $i < count($response_query['outputContexts']); $i++) {
            if (isset($response_query['outputContexts'][$i]['parameters'])) {
                $parameters = array_merge($response_query['outputContexts'][$i]['parameters'], $parameters);
            }
        }
    }

    // Google search, spelling correction
    if ($unknow_answer && !sb_is_agent()) {
        if ($message_length > 2) {
            if ($sb_recursion_dialogflow[1] && sb_get_multi_setting('open-ai', 'open-ai-spelling-correction-dialogflow') && empty(sb_get_shortcode($message))) {
                $spelling_correction = sb_open_ai_spelling_correction($message);
                $sb_recursion_dialogflow[1] = false;
                if ($spelling_correction != $message) {
                    return sb_dialogflow_message($conversation_id, $spelling_correction, $language, $attachments, $event, $parameters);
                }
            }
            $google_search_settings = sb_get_setting('dialogflow-google-search');
            if ($google_search_settings) {
                $spelling_correction = $google_search_settings['dialogflow-google-search-spelling-active'];
                $continue = $google_search_settings['dialogflow-google-search-active'] && $message_length > 4 && !sb_get_multi_setting('open-ai', 'open-ai-active');
                if ($continue) {
                    $entities = sb_isset($google_search_settings, 'dialogflow-google-search-entities');
                    if (!empty($entities) && is_array($entities)) {
                        $continue = false;
                        $entities_response = sb_isset(sb_google_analyze_entities($message, $language[0], $token), 'entities', []);
                        for ($i = 0; $i < count($entities_response); $i++) {
                            if (in_array($entities_response[$i]['type'], $entities)) {
                                $continue = true;
                                break;
                            }
                        }
                    }
                }
                if ($continue || $spelling_correction) {
                    $google_search_response = sb_get('https://www.googleapis.com/customsearch/v1?key=' . $google_search_settings['dialogflow-google-search-key'] . '&cx=' . $google_search_settings['dialogflow-google-search-id'] . '&q=' . urlencode($message), true);
                    if ($sb_recursion_dialogflow[2] && $spelling_correction && isset($google_search_response['spelling'])) {
                        $sb_recursion_dialogflow[2] = false;
                        return sb_dialogflow_message($conversation_id, $google_search_response['spelling']['correctedQuery'], $language, $attachments, $event, $parameters);
                    }
                    if ($continue) {
                        $google_search_response = sb_isset($google_search_response, 'items');
                        if ($google_search_response && count($google_search_response)) {
                            $google_search_response = $google_search_response[0];
                            $google_search_message = $google_search_response['snippet'];
                            $pos = strrpos($google_search_message, '. ');
                            if (!$pos && substr($google_search_message, -3) !== '...' && substr($google_search_message, -1) === '.') {
                                $pos = strlen($google_search_message);
                            }
                            if ($pos) {
                                $google_search_message = substr($google_search_message, 0, $pos);
                                $unknow_answer = false;
                                $messages = [['text' => ['text' => [$google_search_message]]]];
                                sb_dialogflow_set_active_context('google-search', ['link' => $google_search_response['link']], 2, $token, $user_id, $language[0]);
                            } else {
                                $google_search_message = false;
                            }
                        }
                    }
                }
            }
        }
    }
    if (!sb_is_agent() || $smart_reply) {
        $detected_language = false;
        $repeated_intent = false;

        // Language detection
        if ($sb_recursion_dialogflow[3] && (sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active') || sb_get_multi_setting('google', 'google-language-detection')) && (($unknow_answer || !$user_language) && count(sb_db_get('SELECT id FROM sb_messages WHERE user_id = ' . $user_id . ' LIMIT 3', false)) < 3)) { // Deprecated: sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active')
            $sb_recursion_dialogflow[3] = false;
            $detected_language = sb_google_language_detection($message);
            if (!empty($detected_language) && ($detected_language != $language[0] || ($user_language && $detected_language != $user_language))) {
                $dialogflow_agent = sb_dialogflow_get_agent();
                sb_language_detection_db($user_id, $detected_language);
                $user_language = $detected_language;
                $payload['event'] = 'update-user';
                if ($detected_language != $language[0] && ($detected_language == sb_isset($dialogflow_agent, 'defaultLanguageCode') || in_array($detected_language, sb_isset($dialogflow_agent, 'supportedLanguageCodes', [])))) {
                    return sb_dialogflow_message($conversation_id, $message, [$detected_language, 'language-detection'], $attachments, $event);
                } else if (!$multilingual_translation) {
                    $unknow_language_message = true;
                } else {
                    $event = 'translations';
                }
            }
        }

        // Repeated Intent
        if ($conversation_id && !$smart_reply && !$unknow_answer && sb_get_multi_setting('open-ai', 'open-ai-active') && empty($response_query['parameters']) && isset($messages[0]) && isset($messages[0]['text'])) {
            $last_message = sb_get_last_message($conversation_id, false, $bot_id);
            $repeated_intent = $last_message && sb_google_get_message_translation($last_message)['message'] == $messages[0]['text']['text'][0] && empty($response_query['parameters']);
        }

        if ($unknow_answer || $repeated_intent) {

            // Multilingual and translations
            if ($sb_recursion_dialogflow[4] && $multilingual_translation && !$repeated_intent) {
                $sb_recursion_dialogflow[4] = false;
                if (empty($GLOBALS['dialogflow_languages'])) {
                    $dialogflow_agent = $dialogflow_agent ? $dialogflow_agent : sb_dialogflow_get_agent();
                    $lang = sb_isset($dialogflow_agent, 'defaultLanguageCode', $language[0]);
                } else {
                    $lang = $GLOBALS['dialogflow_languages'][0];
                }
                $message_translated = sb_google_translate([$message], $lang);
                if (!empty($message_translated)) {
                    return sb_dialogflow_message($conversation_id, $message_translated[0], [$language[0], 'language-translation'], $attachments, $event);
                }
            }

            // OpenAI
            if ($message_length > 4 && sb_get_multi_setting('open-ai', 'open-ai-active')) {
                if ($conversation_id && !$smart_reply) {
                    $is_human_takeover = sb_dialogflow_is_human_takeover($conversation_id);
                }
                if (!$is_human_takeover || !$conversation_id) {
                    $extra = [];
                    if ($multilingual && !$multilingual_translation) {
                        $extra['language'] = $user_language ? $user_language : sb_get_user_language($user_id);
                    }
                    if ($smart_reply) {
                        $extra['smart_reply'] = true;
                    }
                    $response_open_ai = sb_open_ai_message($message, false, false, $conversation_id, $extra, false, $attachments);
                    if (!sb_is_error($response_open_ai) && $response_open_ai[0] && $response_open_ai[1] != 'sb-human-takeover') {
                        $unknow_answer = false;
                        $messages = [['text' => ['text' => [$response_open_ai[1]]]]];
                        $response = ['dialogflow' => $response, 'openai' => $response_open_ai];
                    }
                }
            }
            if ($unknow_answer && $unknow_language_message) {
                $language_detection_message = sb_get_multi_setting('google', 'google-language-detection-message', sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-message')); // Deprecated: sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-message')
                if (!empty($language_detection_message) && $conversation_id && $detected_language) {
                    $language_name = sb_google_get_language_name($detected_language);
                    $language_detection_message = str_replace('{language_name}', $language_name, sb_t($language_detection_message, $detected_language));
                    $message_id = sb_send_message($bot_id, $conversation_id, $language_detection_message)['id'];
                    return ['token' => $token, 'messages' => [['message' => $language_detection_message, 'attachments' => [], 'payload' => ['language_detection' => true], 'id' => $message_id]], 'response' => $response, 'language_detection_message' => $language_detection_message, 'message_id' => $message_id, 'user_language' => $user_language];
                }
            }
        }
    }

    $count = count($messages);
    $is_assistant = true;
    if (is_string($response)) {
        return ['response' => $response];
    }
    $response['outputAudio'] = '';
    for ($i = 0; $i < $count; $i++) {
        if (isset($messages[$i]['text']) && $messages[$i]['text']['text'][0]) {
            $is_assistant = false;
            break;
        }
    }
    for ($i = 0; $i < $count; $i++) {
        $bot_message = '';

        // Payload
        $payload = $i ? sb_isset($messages[$i], 'payload', []) : array_merge($payload, sb_isset($messages[$i], 'payload', []));
        if ($payload && $conversation_id && !$smart_reply) {
            if (isset($payload['redirect'])) {
                $payload['redirect'] = sb_dialogflow_merge_fields($payload['redirect'], $parameters, $language[0]);
            }
            if (isset($payload['archive-chat'])) {
                sb_update_conversation_status($conversation_id, 3);
                if (sb_get_multi_setting('close-message', 'close-active')) {
                    sb_close_message($conversation_id, $bot_id);
                }
                if (sb_get_multi_setting('close-message', 'close-transcript') && sb_isset(sb_get_active_user(), 'email')) {
                    $transcript = sb_transcript($conversation_id);
                    sb_email_create(sb_get_active_user_ID(), sb_get_user_name(), sb_isset(sb_get_active_user(), 'profile_image'), sb_get_multi_setting('transcript', 'transcript-message', ''), [[$transcript, $transcript]], true, $conversation_id);
                    $payload['force-message'] = true;
                }
            }
            if (isset($payload['update-user-details']) || isset($payload['update-user-language'])) {
                $payload_user_details = sb_isset($payload, 'update-user-details', []);
                $user = sb_get_user($user_id);
                if (!sb_is_agent($user)) {
                    if (isset($payload['update-user-language'])) {
                        $language_code = $payload['update-user-language'];
                        $language_codes = sb_get_json_resource('languages/language-codes.json');
                        $language_code = sb_get_language_code_by_name($language_code, $language_codes);
                        if (strlen($language_code) > 2) {
                            $language_code = sb_google_translate([$language_code], 'en');
                            if (!empty($language_code)) {
                                foreach ($language_codes as $key => $value) {
                                    if ($language_code[0] == $value) {
                                        $language_code = $key;
                                        break;
                                    }
                                }
                            }
                        }
                        if (is_string($language_code) && strlen($language_code) == 2 && isset($language_codes[$language_code])) {
                            $payload_user_details['extra'] = ['language' => $language_code, 'browser_language' => ''];
                            $user_language = $language_code;
                            if ($multilingual) {
                                $dialogflow_agent = sb_dialogflow_get_agent();
                                if ($language_code == sb_isset($dialogflow_agent, 'defaultLanguageCode') || in_array($language_code, sb_isset($dialogflow_agent, 'supportedLanguageCodes', []))) {
                                    $response_success['language_detection'] = $language_code;
                                }
                            }
                        } else {
                            return false;
                        }
                    }
                    $payload['event'] = 'update-user';
                    $user['user_type'] = '';
                    sb_update_user($user_id, array_merge($user, $payload_user_details), sb_isset($payload_user_details, 'extra', []));
                }
            }
        }

        // Google Assistant
        if ($is_assistant) {
            if (isset($messages[$i]['platform']) && $messages[$i]['platform'] == 'ACTIONS_ON_GOOGLE') {
                if (isset($messages[$i]['simpleResponses']) && isset($messages[$i]['simpleResponses']['simpleResponses'])) {
                    $item = $messages[$i]['simpleResponses']['simpleResponses'];
                    if (isset($item[0]['textToSpeech'])) {
                        $bot_message = $item[0]['textToSpeech'];
                    } else if ($item[0]['displayText']) {
                        $bot_message = $item[0]['displayText'];
                    }
                }
            }
        } else if (isset($messages[$i]['text'])) {

            // Message
            $bot_message = $messages[$i]['text']['text'][0];
        }

        // Attachments
        $attachments = [];
        if ($payload) {
            if (isset($payload['attachments'])) {
                $attachments = $payload['attachments'];
                if (!$attachments && !is_array($attachments)) {
                    $attachments = [];
                }
            }
        }

        // WooCommerce
        if (defined('SB_WOOCOMMERCE')) {
            $woocommerce = sb_woocommerce_dialogflow_process_message($bot_message, $payload);
            $bot_message = $woocommerce[0];
            $payload = $woocommerce[1];
        }

        // Send message and human takeover
        if ($bot_message || $payload) {
            if ($conversation_id && !$smart_reply) {
                $is_human_takeover = sb_dialogflow_is_human_takeover($conversation_id);
                if ($human_takeover && $unknow_answer && strlen($message) > 3 && strpos($message, ' ') && !$is_human_takeover) {
                    $human_takeover_response = sb_chatbot_human_takeover($conversation_id, $human_takeover);
                    if ($human_takeover_response[1]) {
                        $response_success['human_takeover'] = true;
                    }
                    $results = array_merge($results, $human_takeover_response[0]);
                } else {
                    $last_agent = sb_isset(sb_get_last_agent_in_conversation($conversation_id), 'id');
                    if ($is_human_takeover && (isset($payload['human-takeover']) || strpos($bot_message, 'sb-human-takeover'))) {
                        $bot_message = sb_isset($human_takeover, 'message_fallback');
                        $payload = false;
                    }
                    if (($bot_message || $payload) && (!$is_human_takeover || !empty($payload['force-message']) || ((!$last_agent || !sb_is_user_online($last_agent)) && !$unknow_answer))) {
                        if (!$bot_message && isset($payload['force-message']) && $i > 0 && isset($messages[$i - 1]['text'])) {
                            $bot_message = $messages[$i - 1]['text']['text'][0];
                        }
                        $bot_message = sb_dialogflow_merge_fields($bot_message, $parameters, $language[0]);
                        if ($multilingual_translation && $bot_message) {
                            $continue = isset($language[1]) && $language[1] == 'language-translation';
                            $user_language = $user_language ? $user_language : sb_get_user_language($user_id);
                            if (!$continue) {
                                $dialogflow_agent = $dialogflow_agent ? $dialogflow_agent : sb_dialogflow_get_agent();
                                $continue = $user_language != sb_isset($dialogflow_agent, 'defaultLanguageCode') && !in_array($user_language, sb_isset($dialogflow_agent, 'supportedLanguageCodes', []));
                            }
                            if ($continue) {
                                $message = sb_google_translate([$bot_message], $user_language);
                                if (!empty($message)) {
                                    $bot_message = $message[0];
                                }
                            }
                        }
                        $bot_message = sb_open_ai_text_formatting($bot_message);
                        $message_id = sb_send_message($bot_id, $conversation_id, $bot_message, $attachments, -1, $payload)['id'];
                        array_push($results, ['message' => sb_open_ai_text_formatting($bot_message), 'attachments' => $attachments, 'payload' => $payload, 'id' => $message_id]);
                    }
                }
            } else {
                array_push($results, ['message' => sb_dialogflow_merge_fields($bot_message, $parameters, $language[0]), 'attachments' => $attachments, 'payload' => $payload]);
            }
        }
    }
    if (count($results)) {
        $response_success['token'] = $token;
        $response_success['messages'] = $results;
        $response_success['response'] = $response;
        $response_success['user_language'] = $user_language;
        $response_success['message_language'] = $language[0];
        $response_success['translations'] = $translations;
        return $response_success;
    }
    if (isset($response['error']) && sb_isset($response['error'], 'code') != 400) {
        $admin_emails = sb_db_get('SELECT email FROM sb_users WHERE user_type = "admin"', false);
        $admin_emails_string = '';
        for ($i = 0; $i < count($admin_emails); $i++) {
            $admin_emails_string .= $admin_emails[$i]['email'] . ',';
        }
        $text = 'Dialogflow Error | ' . SB_URL . '/admin.php';
        sb_email_send(substr($admin_emails_string, 0, -1), $text, $text . '<br><br>' . json_encode($response));
    }
    return ['response' => $response];
}

/*
 * -----------------------------------------------------------
 * INTENTS
 * -----------------------------------------------------------
 *
 * 1. Create an Intent
 * 2. Update an existing Intent
 * 3. Create multiple Intents
 * 4. Delete multiple Intents
 * 5. Return all Intents
 *
 */

function sb_dialogflow_create_intent($training_phrases, $bot_responses, $language = '', $conversation_id = false, $services = false) {
    global $sb_entity_types;
    $training_phrases_api = [];
    $cx = sb_get_multi_setting('google', 'dialogflow-edition', sb_get_setting('dialogflow-edition')) == 'cx'; // Deprecated: sb_get_setting('dialogflow-edition')
    $sb_entity_types = $cx ? ($sb_entity_types ? $sb_entity_types : sb_isset(sb_dialogflow_curl('/entityTypes', '', false, 'GET'), 'entityTypes', [])) : false;
    $parameters = [];

    // Training phrases and parameters
    if (is_string($bot_responses)) {
        $bot_responses = [['text' => ['text' => $bot_responses]]];
    }
    for ($i = 0; $i < count($training_phrases); $i++) {
        if (is_string($training_phrases[$i])) {
            $parts = ['text' => $training_phrases[$i]];
        } else {
            $parts = $training_phrases[$i]['parts'];
            for ($j = 0; $j < count($parts); $j++) {
                if (empty($parts[$j]['text'])) {
                    array_splice($parts, $j, 1);
                } else if ($cx && isset($parts[$j]['entityType'])) {
                    for ($y = 0; $y < count($sb_entity_types); $y++) {
                        if ($sb_entity_types[$y]['displayName'] == $parts[$j]['alias']) {
                            $id = 'parameter_id_' . $y;
                            $parts[$j]['parameterId'] = $id;
                            $new = true;
                            for ($k = 0; $k < count($parameters); $k++) {
                                if ($parameters[$k]['id'] == $id) {
                                    $new = false;
                                    break;
                                }
                            }
                            if ($new) {
                                array_push($parameters, ['id' => $id, 'entityType' => $sb_entity_types[$y]['name']]);
                            }
                            break;
                        }
                    }
                }
            }
        }
        array_push($training_phrases_api, ['type' => 'TYPE_UNSPECIFIED', 'parts' => $parts, 'repeatCount' => 1]);
    }

    // Intent name
    $name = sb_isset($training_phrases_api[0]['parts'], 'text');
    if (!$name) {
        $parts = $training_phrases_api[0]['parts'];
        for ($i = 0; $i < count($parts); $i++) {
            $name .= $parts[$i]['text'];
        }
    }

    // Create the Intent
    $query = ['displayName' => ucfirst(str_replace('-', ' ', sb_string_slug(strlen($name) > 100 ? substr($name, 0, 99) : $name))), 'priority' => 500000, 'webhookState' => 'WEBHOOK_STATE_UNSPECIFIED', 'trainingPhrases' => $training_phrases_api, 'messages' => $bot_responses];
    if ($parameters) {
        $query['parameters'] = $parameters;
    }
    $response = sb_dialogflow_curl('/agent/intents', $query, $language);
    if ($cx) {
        $flow_name = '00000000-0000-0000-0000-000000000000';
        if ($conversation_id) {
            $messages = sb_db_get('SELECT payload FROM sb_messages WHERE conversation_id = ' . sb_db_escape($conversation_id, true) . ' AND payload <> "" ORDER BY id DESC');
            for ($i = 0; $i < count($messages); $i++) {
                $payload = json_decode($messages['payload'], true);
                if (isset($payload['queryResult']) && isset($payload['queryResult']['currentPage'])) {
                    $flow_name = $payload['queryResult']['currentPage'];
                    $flow_name = substr($flow_name, strpos($flow_name, '/flows/') + 7);
                    if (strpos($flow_name, '/'))
                        $flow_name = substr($flow_name, 0, strpos($flow_name, '/'));
                    break;
                }
            }
        }
        $flow = sb_dialogflow_curl('/flows/' . $flow_name, '', $language, 'GET');
        array_push($flow['transitionRoutes'], ['intent' => $response['name'], 'triggerFulfillment' => ['messages' => $bot_responses]]);
        $response = sb_dialogflow_curl('/flows/' . $flow_name . '?updateMask=transitionRoutes', $flow, $language, 'PATCH');
    }
    $response['response_open_ai'] = $services != 'dialogflow' && sb_chatbot_active(false, true) ? sb_open_ai_qea_training([[$training_phrases[0], $bot_responses[0]['text']['text']]], $language) : true;
    if (isset($response['displayName']) && $response['response_open_ai']) {
        return true;
    }
    return $response;
}

function sb_dialogflow_update_intent($intent, $training_phrases, $language = '', $services = false) {
    $intent_name = is_string($intent) ? $intent : $intent['name'];
    $pos = strpos($intent_name, '/intents/');
    $intent_name = $pos ? substr($intent_name, $pos + 9) : $intent_name;
    if (is_string($intent)) {
        $intent = sb_dialogflow_get_intents($intent_name, $language);
    }
    if (!isset($intent['trainingPhrases'])) {
        $intent['trainingPhrases'] = [];
    }
    for ($i = 0; $i < count($training_phrases); $i++) {
        array_push($intent['trainingPhrases'], ['type' => 'TYPE_UNSPECIFIED', 'parts' => ['text' => $training_phrases[$i]], 'repeatCount' => 1]);
    }
    $response = sb_dialogflow_curl('/agent/intents/' . $intent_name . '?updateMask=trainingPhrases', $intent, $language, 'PATCH');
    if ($services != 'dialogflow' && sb_chatbot_active(false, true)) {
        $response['response_open_ai'] = sb_open_ai_qea_training([[$training_phrases[0], $services]], $language);
    }
    return isset($response['name']) ? true : $response;
}

function sb_dialogflow_batch_intents($intents, $language = '') {
    if (sb_get_multi_setting('google', 'dialogflow-edition', sb_get_setting('dialogflow-edition')) == 'cx') { // Deprecated: sb_get_setting('dialogflow-edition', 'es')
        $response = [];
        for ($i = 0; $i < count($intents); $i++) {
            array_push($response, sb_dialogflow_create_intent($intents[$i]->data['trainingPhrases'], $intents[$i]->data['messages'], $language));
        }
        return $response;
    } else {
        $intents_array = [];
        for ($i = 0; $i < count($intents); $i++) {
            array_push($intents_array, $intents[$i]->data());
        }
        $query = ['intentBatchInline' => ['intents' => $intents_array], 'intentView' => 'INTENT_VIEW_UNSPECIFIED'];
        if (!empty($language))
            $query['languageCode'] = $language;
        return sb_dialogflow_curl('/agent/intents:batchUpdate', $query);
    }
}

function sb_dialogflow_batch_intents_delete($intents) {
    return sb_dialogflow_curl('/agent/intents:batchDelete', ['intents' => $intents]);
}

function sb_dialogflow_get_intents($intent_name = false, $language = '') {
    $next_page_token = true;
    $paginatad_items = [];
    $intents = [];
    while ($next_page_token) {
        $items = sb_dialogflow_curl($intent_name ? ('/agent/intents/' . $intent_name . '?intentView=INTENT_VIEW_FULL') : ('/agent/intents?pageSize=1000&intentView=INTENT_VIEW_FULL' . ($next_page_token !== true && $next_page_token !== false ? ('&pageToken=' . $next_page_token) : '')), '', $language, 'GET');
        if ($intent_name)
            return $items;
        $next_page_token = sb_isset($items, 'nextPageToken');
        if (sb_is_error($next_page_token))
            die($next_page_token);
        array_push($paginatad_items, sb_isset($items, 'intents'));
    }
    for ($i = 0; $i < count($paginatad_items); $i++) {
        $items = $paginatad_items[$i];
        if ($items) {
            for ($j = 0; $j < count($items); $j++) {
                if (!empty($items[$j]))
                    array_push($intents, $items[$j]);
            }
        }
    }
    return $intents;
}

/*
 * -----------------------------------------------------------
 * ENTITIES
 * -----------------------------------------------------------
 *
 * Create, get, update, delete a Dialogflow entities
 *
 */

function sb_dialogflow_create_entity($entity_name, $values, $language = '') {
    $response = sb_dialogflow_curl('/agent/entityTypes', is_a($values, 'SBDialogflowEntity') ? $values->data() : (new SBDialogflowEntity($entity_name, $values))->data(), $language);
    if (isset($response['displayName'])) {
        return true;
    } else if (isset($response['error']) && sb_isset($response['error'], 'status') == 'FAILED_PRECONDITION') {
        return new SBValidationError('duplicate-dialogflow-entity');
    }
    return $response;
}

function sb_dialogflow_update_entity($entity_id, $values, $entity_name = false, $language = '') {
    $response = sb_dialogflow_curl('/agent/entityTypes/' . $entity_id, is_a($values, 'SBDialogflowEntity') ? $values->data() : (new SBDialogflowEntity($entity_name, $values))->data(), $language, 'PATCH');
    if (isset($response['displayName'])) {
        return true;
    }
    return $response;
}

function sb_dialogflow_get_entity($entity_id = 'all', $language = '') {
    $entities = sb_dialogflow_curl('/agent/entityTypes', '', $language, 'GET');
    if (isset($entities['entityTypes'])) {
        $entities = $entities['entityTypes'];
        if ($entity_id == 'all') {
            return $entities;
        }
        for ($i = 0; $i < count($entities); $i++) {
            if ($entities[$i]['displayName'] == $entity_id) {
                return $entities[$i];
            }
        }
        return new SBValidationError('entity-not-found');
    } else
        return $entities;
}

/*
 * -----------------------------------------------------------
 * MISCELLANEOUS
 * -----------------------------------------------------------
 *
 * 2. Convert the Dialogflow merge fields to the final values
 * 3. Activate a context in the active conversation
 * 4. Return the details of a Dialogflow agent
 * 5. Chinese language sanatization
 * 6. Dialogflow curl
 * 7. Human takeover
 * 8. Check if human takeover is active
 * 9. Return the Dialogflow human takeover settings
 * 10. Execute payloads
 * 11. Add Intents to saved replies
 * 12. Check if unknow answer
 * 13. PDF to text
 * 14. JSON to text
 * 15. CSV to text
 * 16. Support Board database embedding
 * 17. Check if manual or automatic sync mode
 * 18. Data scraping
 * 19. Sitemap generation
 * 20. Check if a string terminates with a dot or similar character
 *
 */

function sb_dialogflow_merge_fields($message, $parameters, $language = '') {
    if (defined('SB_WOOCOMMERCE')) {
        $message = sb_woocommerce_merge_fields($message, $parameters, $language);
    }
    return $message;
}

function sb_dialogflow_set_active_context($context_name, $parameters = [], $life_span = 5, $token = false, $user_id = false, $language = false) {
    if (!sb_get_multi_setting('google', 'dialogflow-active')) {
        return false;
    }
    $language = $language === false ? (sb_get_multi_setting('google', 'google-multilingual') ? sb_get_user_language($user_id) : '') : $language;
    $session_id = $user_id === false ? sb_isset(sb_get_active_user(), 'id', 'sb') : $user_id;
    $parameters = empty($parameters) ? '' : ', "parameters": ' . (is_string($parameters) ? $parameters : json_encode($parameters));
    $query = '{ "queryInput": { "text": { "languageCode": "' . (empty($language) ? 'en' : $language) . '", "text": "sb-trigger-context" }}, "queryParams": { "contexts": [{ "name": "projects/' . sb_get_multi_setting('google', 'google-project-id') . '/agent/sessions/' . $session_id . '/contexts/' . $context_name . '", "lifespanCount": ' . $life_span . $parameters . ' }] }}';
    return sb_dialogflow_curl('/agent/sessions/' . $session_id . ':detectIntent', $query, false, 'POST', $token);
}

function sb_dialogflow_get_agent() {
    return sb_dialogflow_curl('/agent', '', '', 'GET');
}

function sb_dialogflow_language_code($language) {
    return $language == 'zh' ? 'zh-cn' : ($language == 'zt' ? 'zh-tw' : $language);
}

function sb_dialogflow_curl($url_part, $query = '', $language = false, $type = 'POST', $token = false, $project_id = false) {

    // Project ID
    if (!$project_id) {
        $project_id = sb_get_multi_setting('google', 'google-project-id');
        if (empty($project_id)) {
            return sb_error('project-id-not-found', 'sb_dialogflow_curl');
        }
    }

    // Retrive token
    $token = empty($token) || $token == -1 ? sb_google_get_token() : $token;
    if (sb_is_error($token)) {
        return sb_error('token-error', 'sb_dialogflow_curl');
    }

    // Language
    if (!empty($language)) {
        $language = (strpos($url_part, '?') ? '&' : '?') . 'languageCode=' . $language;
    }

    // Query
    if (!is_string($query)) {
        $query = json_encode($query);
    }

    // Edition and version
    $edition = sb_get_multi_setting('google', 'dialogflow-edition', sb_get_setting('dialogflow-edition', 'es')); // Deprecated: sb_get_setting('dialogflow-edition', 'es')
    $version = 'v2beta1/projects/';
    $cx = $edition == 'cx';
    if ($cx) {
        $version = 'v3beta1/';
        $url_part = str_replace('/agent/', '/', $url_part);
    }

    // Location
    $location = sb_get_multi_setting('google', 'dialogflow-location', sb_get_setting('dialogflow-location', '')); // Deprecated: sb_get_setting('dialogflow-location', '')
    $location_session = $location && !$cx ? '/locations/' . substr($location, 0, -1) : '';

    // Send
    $response = sb_google_curl($location . 'dialogflow', $query, $version . $project_id . $location_session . $url_part . $language, $type);
    return $type == 'GET' ? json_decode($response, true) : $response;
}

function sb_dialogflow_human_takeover($conversation_id, $auto_messages = false) {
    $human_takeover = sb_dialogflow_get_human_takeover_settings();
    $conversation_id = sb_db_escape($conversation_id, true);
    $bot_id = sb_get_bot_ID();
    $data = sb_db_get('SELECT A.id AS `user_id`, A.email, A.first_name, A.last_name, A.profile_image, B.agent_id, B.department, B.status_code FROM sb_users A, sb_conversations B WHERE A.id = B.user_id AND B.id = ' . $conversation_id);
    $user_id = $data['user_id'];
    $messages = sb_db_get('SELECT A.user_id, A.message, A.attachments, A.creation_time, B.first_name, B.last_name, B.profile_image, B.user_type FROM sb_messages A, sb_users B WHERE A.conversation_id = ' . $conversation_id . ' AND A.user_id = B.id AND A.message <> "' . $human_takeover['confirm'] . '" AND A.message NOT LIKE "%sb-human-takeover%" AND A.payload NOT LIKE "%human-takeover%" ORDER BY A.id ASC', false);
    $count = count($messages);
    $last_message = $messages[$count - 1]['message'];
    $response = [];
    sb_send_message($bot_id, $conversation_id, '', [], 2, ['human-takeover' => true]);
    $GLOBALS['human-takeover-' . $conversation_id] = true;

    // Human takeover message and status code
    $message = $human_takeover['message_confirmation'];
    if (!empty($message)) {
        $message_id = sb_send_message($bot_id, $conversation_id, $message, [], 2, ['human-takeover-message-confirmation' => true, 'preview' => $last_message])['id'];
        array_push($response, ['message' => $message, 'id' => $message_id]);
    }

    // Auto messages
    if ($auto_messages) {
        $auto_messages = ['offline', 'follow_up'];
        for ($i = 0; $i < count($auto_messages); $i++) {
            $auto_message = $i == 0 || empty($data['email']) ? sb_execute_bot_message($auto_messages[$i], $conversation_id, $last_message) : false;
            if ($auto_message) {
                array_push($response, $auto_message);
            }
        }
    }

    // Notifications
    sb_send_agents_notifications($last_message, str_replace('{T}', sb_get_setting('bot-name', 'Chatbot'), sb_('This message has been sent because {T} does not know the answer to the user\'s question.')), $conversation_id, false, $data, ['email' => sb_email_get_conversation_code($conversation_id, 20, true)]);

    // Slack
    if (defined('SB_SLACK') && sb_get_setting('slack-active')) {
        for ($i = 0; $i < count($messages); $i++) {
            sb_send_slack_message($user_id, sb_get_user_name($messages[$i]), $messages[$i]['profile_image'], $messages[$i]['message'], sb_isset($messages[$i], 'attachments', []), $conversation_id);
        }
    }

    return $response;
}

function sb_chatbot_human_takeover($conversation_id, $human_takeover_settings) {
    if ($human_takeover_settings['auto']) {
        $human_takeover_messages = sb_dialogflow_human_takeover($conversation_id);
        $messages = [];
        for ($j = 0; $j < count($human_takeover_messages); $j++) {
            array_push($messages, ['message' => sb_t($human_takeover_messages[$j]['message']), 'attachments' => [], 'payload' => false, 'id' => $human_takeover_messages[$j]['id']]);
        }
        return [$messages, true];
    } else {
        $human_takeover_message = '[chips id="sb-human-takeover" options="' . str_replace(',', '\,', sb_rich_value($human_takeover_settings['confirm'], false)) . ',' . str_replace(',', '\,', sb_rich_value($human_takeover_settings['cancel'], false)) . '" message="' . sb_rich_value($human_takeover_settings['message']) . '"]';
        $message_id = sb_send_message(sb_get_bot_ID(), $conversation_id, $human_takeover_message)['id'];
        return [[['message' => $human_takeover_message, 'attachments' => [], 'payload' => false, 'id' => $message_id]], false];
    }
}

function sb_dialogflow_is_human_takeover($conversation_id) {
    $name = 'human-takeover-' . $conversation_id;
    if (isset($GLOBALS[$name])) {
        return $GLOBALS[$name];
    }
    $agent_ids = sb_get_agents_ids();
    $response = sb_isset(sb_db_get('SELECT id FROM sb_messages WHERE (user_id IN (' . implode(',', $agent_ids) . ') || payload = "{\"human-takeover\":true}") AND conversation_id = ' . sb_db_escape($conversation_id, true) . ' AND creation_time > "' . sb_gmt_now(864000) . '" ORDER BY id DESC LIMIT 1'), 'id');
    if ($response) {
        $response = empty(sb_db_get('SELECT id FROM sb_messages WHERE id >= ' . $response . ' AND conversation_id = ' . sb_db_escape($conversation_id, true) . ' AND payload = "{\"event\":\"conversation-status-update-3\"}"'));
    }
    $GLOBALS[$name] = $response;
    return $response;
}

function sb_dialogflow_get_human_takeover_settings() {
    $settings = sb_get_setting('dialogflow-human-takeover');
    return ['active' => $settings['dialogflow-human-takeover-active'], 'message' => sb_t(sb_isset($settings, 'dialogflow-human-takeover-message', 'I\'m a chatbot. Do you want to get in touch with one of our agents?')), 'message_confirmation' => sb_t(sb_isset($settings, 'dialogflow-human-takeover-message-confirmation', 'Alright! We will get in touch soon!')), 'message_fallback' => sb_t(sb_isset($settings, 'dialogflow-human-takeover-message-fallback', 'An agent has already been contacted and will respond shortly.')), 'confirm' => sb_t(sb_isset($settings, 'dialogflow-human-takeover-confirm', 'Yes')), 'cancel' => sb_t(sb_isset($settings, 'dialogflow-human-takeover-cancel', 'Cancel')), 'auto' => $settings['dialogflow-human-takeover-auto'], 'disable_chatbot' => $settings['dialogflow-human-takeover-disable-chatbot']];
}

function sb_dialogflow_payload($payload, $conversation_id, $message = false, $extra = false) {
    if (isset($payload['agent'])) {
        sb_update_conversation_agent($conversation_id, $payload['agent'], $message);
    }
    if (isset($payload['department'])) {
        sb_update_conversation_department($conversation_id, $payload['department'], $message);
    }
    if (isset($payload['tags'])) {
        sb_tags_update($conversation_id, $payload['tags'], true);
    }
    if (isset($payload['human-takeover'])) {
        $messages = sb_dialogflow_human_takeover($conversation_id, $extra && isset($extra['source']));
        $source = sb_isset($extra, 'source');
        if ($source) {
            for ($i = 0; $i < count($messages); $i++) {
                $message = $messages[$i]['message'];
                $attachments = sb_isset($messages[$i], 'attachments', []);
                sb_messaging_platforms_send_message($message, $extra, $messages[$i]['id'], $attachments);
            }
        }
    }
    if (isset($payload['send-email'])) {
        $send_to_active_user = $payload['send-email']['recipient'] == 'active_user';
        sb_email_create($send_to_active_user ? sb_get_active_user_ID() : 'agents', $send_to_active_user ? sb_get_setting('bot-name') : sb_get_user_name(), $send_to_active_user ? sb_get_setting('bot-image') : sb_isset(sb_get_active_user(), 'profile_image'), $payload['send-email']['message'], sb_isset($payload['send-email'], 'attachments'), false, $conversation_id);
    }
    if (isset($payload['redirect']) && $extra) {
        $message_id = sb_send_message(sb_get_bot_ID(), $conversation_id, $payload['redirect']);
        sb_messaging_platforms_send_message($payload['redirect'], $extra, $message_id);
    }
    if (isset($payload['transcript']) && $extra) {
        $transcript_url = sb_transcript($conversation_id);
        $attachments = [[$transcript_url, $transcript_url]];
        $message_id = sb_send_message(sb_get_bot_ID(), $conversation_id, '', $attachments);
        sb_messaging_platforms_send_message($extra['source'] == 'ig' || $extra['source'] == 'fb' ? '' : $transcript_url, $attachments, $message_id);
    }
    if (isset($payload['rating'])) {
        sb_set_rating(['conversation_id' => $conversation_id, 'agent_id' => sb_isset(sb_get_last_agent_in_conversation($conversation_id), 'id', sb_get_bot_ID()), 'user_id' => sb_get_active_user_ID(), 'message' => '', 'rating' => $payload['rating']]);
    }
}

function sb_dialogflow_saved_replies() {
    $settings = sb_get_settings();
    $saved_replies = sb_get_setting('saved-replies', []);
    $intents = sb_dialogflow_get_intents();
    $count = count($saved_replies);
    for ($i = 0; $i < count($intents); $i++) {
        if (isset($intents[$i]['messages'][0]) && isset($intents[$i]['messages'][0]['text']) && isset($intents[$i]['messages'][0]['text']) && isset($intents[$i]['messages'][0]['text']['text'])) {
            $slug = sb_string_slug($intents[$i]['displayName']);
            $existing = false;
            for ($j = 0; $j < $count; $j++) {
                if ($slug == $saved_replies[$j]['reply-name']) {
                    $existing = true;
                    break;
                }
            }
            if (!$existing) {
                array_push($saved_replies, ['reply-name' => $slug, 'reply-text' => $intents[$i]['messages'][0]['text']['text'][0]]);
            }
        }
    }
    $settings['saved-replies'][0] = $saved_replies;
    return sb_save_settings($settings);
}

function sb_dialogflow_is_unknow($dialogflow_response) {
    $dialogflow_response = sb_isset($dialogflow_response, 'response', $dialogflow_response);
    $query_result = sb_isset($dialogflow_response, 'queryResult', []);
    return (sb_isset($query_result, 'action') == 'input.unknown' || (isset($query_result['match']) && $query_result['match']['matchType'] == 'NO_MATCH')) || (sb_get_multi_setting('google', 'dialogflow-confidence') && sb_isset($query_result, 'intentDetectionConfidence') < floatval(sb_get_multi_setting('google', 'dialogflow-confidence'))) || isset($dialogflow_response['error']);
}

function sb_pdf_to_text($path) {
    if (file_exists($path)) {
        require('pdf/autoload.php');
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }
    return '';
}

function sb_json_to_text($path_or_data, $indent = 0) {
    $text = '';
    $prefix = str_repeat('  ', $indent);
    if ($indent === 0 && is_string($path_or_data)) {
        $path_or_data = json_decode(file_get_contents($path_or_data), true);
    }
    foreach ($path_or_data as $key => $value) {
        $text .= is_array($value) ? $prefix . ucfirst($key) . ':' . PHP_EOL . sb_json_to_text($value, $indent + 1) : $prefix . ucfirst($key) . ': ' . $value . PHP_EOL;
    }
    return $text;
}

function sb_csv_to_text($url) {
    $data = sb_csv_read($url);
    $text = '';
    if ($data) {
        $keys = array_keys($data[0]);
        foreach ($data as $row) {
            foreach ($keys as $key) {
                $text .= ucfirst($key) . ': ' . (isset($row[$key]) ? $row[$key] : '') . PHP_EOL;
            }
            $text .= PHP_EOL;
        }
    }
    return $text;
}

function sb_get_sitemap_urls($sitemap_url, &$urls = []) {
    $xml = sb_get($sitemap_url);
    $sitemap = new SimpleXmlElement($xml);
    foreach ($sitemap->url as $url) {
        if (!strpos($url->loc, 'mailto:') && !strpos($url->loc, 'javascript:')) {
            array_push($urls, strval($url->loc));
        }
    }
    foreach ($sitemap->sitemap as $sitemap_entry) {
        $nested_sitemap_url = strval($sitemap_entry->loc);
        sb_get_sitemap_urls($nested_sitemap_url, $urls);
    }
    return $urls;
}

function sb_ai_is_manual_sync($source) {
    switch ($source) {
        case 'google':
            return !sb_is_cloud() || !defined('GOOGLE_CLIENT_ID') || sb_get_multi_setting('google', 'google-sync-mode', 'manual') == 'manual'; // Deprecated: remove default , 'manual'
        case 'open-ai':
            return !sb_is_cloud() || !defined('OPEN_AI_KEY') || (sb_get_multi_setting('open-ai', 'open-ai-sync-mode', 'manual') == 'manual' && sb_defined('OPEN_AI_KEY') != trim(sb_get_multi_setting('open-ai', 'open-ai-key'))); // Deprecated: remove default , 'manual'
    }
    return false;
}

function sb_is_string_ends($string) {
    return in_array(substr(trim($string), -1), ['.', ',', ':', '!', '?', ';', '።', '।', '。', '။']);
}

/*
 * -----------------------------------------------------------
 * SMART REPLY
 * -----------------------------------------------------------
 *
 * 1. Return the suggestions
 * 2. Update a smart reply conversation with a new message
 * 3. Generate the conversation transcript data for a dataset
 *
 */

function sb_dialogflow_smart_reply($message, $dialogflow_languages = false, $conversation_id = false) {
    $suggestions = [];
    $smart_reply_response = false;
    if (!empty($dialogflow_languages)) {
        $GLOBALS['dialogflow_languages'] = $dialogflow_languages;
    }
    $token = empty($token) ? sb_google_get_token() : $token;
    $dialogflow_active = sb_chatbot_active(true, false);
    $messages = $dialogflow_active ? sb_dialogflow_message($conversation_id, $message, false, [], 'smart-reply') : [];
    if (sb_is_error($messages)) {
        return sb_error('smart-reply-error', 'sb_dialogflow_smart_reply', $messages);
    }
    if (!empty($messages['messages']) && !sb_dialogflow_is_unknow($messages['response'])) {
        for ($i = 0; $i < count($messages['messages']); $i++) {
            $value = $messages['messages'][$i]['message'];
            if (!empty($value) && !strpos($value, 'sb-human-takeover')) {
                array_push($suggestions, $value);
            }
        }
        if (sb_get_multi_setting('google', 'google-multilingual-translation') && $messages['message_language'] != sb_get_user_language(sb_get_active_user_ID())) {
            $translation = sb_google_translate($suggestions, sb_get_user_language(sb_get_active_user_ID()));
            if (!empty($translation)) {
                for ($i = 0; $i < count($suggestions); $i++) {
                    if (!empty($translation[$i])) {
                        $suggestions[$i] = $translation[$i];
                    }
                }
            }
        }
    }
    if (!count($suggestions) && !$dialogflow_active && (sb_get_multi_setting('open-ai', 'open-ai-active') || sb_get_multi_setting('open-ai', 'open-ai-smart-reply'))) {
        $suggestions = sb_isset(sb_open_ai_smart_reply($message, $conversation_id), 'suggestions', []);
    }
    return ['suggestions' => $suggestions, 'token' => sb_isset($messages, 'token'), 'dialogflow_languages' => $dialogflow_languages, 'smart_reply' => $smart_reply_response];
}

function sb_dialogflow_knowledge_articles($articles = false, $language = false) {
    $language = $language ? sb_dialogflow_language_code($language) : false;
    if (sb_isset(sb_dialogflow_get_agent(), 'defaultLanguageCode') != 'en') {
        return 'dialogflow-language-not-supported';
    }
    if (!$articles) {
        $articles = sb_get_articles(false, false, true, false, 'all');
    }
    if ($articles) {

        // Create articles file
        $faq = [];
        for ($i = 0; $i < count($articles); $i++) {
            $content = strip_tags($articles[$i]['content']);
            if (mb_strlen($content) > 150) {
                $content = mb_substr($content, 0, 150);
                $content = mb_substr($content, 0, mb_strrpos($content, ' ') + 1) . '... [button link="#article-' . $articles[$i]['id'] . '" name="' . sb_('Read more') . '" style="link"]';
                $content = str_replace(', ...', '...', $content);
            }
            array_push($faq, [$articles[$i]['title'], $content]);
        }
        $file_path = sb_csv($faq, false, 'dialogflow-faq', false);
        $file = fopen($file_path, 'r');
        $file_bytes = fread($file, filesize($file_path));
        fclose($file);
        unlink($file_path);

        // Create new knowledge if not exist
        $knowledge_base_name = sb_get_external_setting('dialogflow-knowledge', []);
        if (!isset($knowledge_base_name[$language ? $language : 'default'])) {
            $query = ['displayName' => 'Support Board'];
            if ($language) {
                $query['languageCode'] = $language;
            }
            $name = sb_isset(sb_dialogflow_curl('/knowledgeBases', $query, false, 'POST'), 'name');
            $name = substr($name, strripos($name, '/') + 1);
            $knowledge_base_name[$language ? $language : 'default'] = $name;
            sb_save_external_setting('dialogflow-knowledge', $knowledge_base_name);
            $knowledge_base_name = $name;
        } else {
            $knowledge_base_name = $knowledge_base_name['default'];
        }

        // Save knowledge in Dialogflow
        $documents = sb_isset(sb_dialogflow_curl('/knowledgeBases/' . $knowledge_base_name . '/documents', '', false, 'GET'), 'documents', []);
        for ($i = 0; $i < count($documents); $i++) {
            $name = $documents[0]['name'];
            $response = sb_dialogflow_curl(substr($name, stripos($name, 'knowledgeBases/') - 1), '', false, 'DELETE');
        }
        $response = sb_dialogflow_curl('/knowledgeBases/' . $knowledge_base_name . '/documents', ['displayName' => 'Support Board', 'mimeType' => 'text/csv', 'knowledgeTypes' => ['FAQ'], 'rawContent' => base64_encode($file_bytes)], false, 'POST');
        if ($response && isset($response['error']) && sb_isset($response['error'], 'status') == 'NOT_FOUND') {
            sb_save_external_setting('dialogflow-knowledge', false);
            return false;
        }
    }
    return true;
}

function sb_generate_sitemap($url) {
    require_once('sitemap-generator.php');
    set_time_limit(900);
    $path = sb_upload_path() . '/sitemap.xml';
    $smg = new SitemapGenerator([
        'SITE_URL' => $url,
        'ALLOW_EXTERNAL_LINKS' => false,
        'ALLOW_ELEMENT_LINKS' => false,
        'CRAWL_ANCHORS_WITH_ID' => '',
        'KEYWORDS_TO_SKIP' => [],
        'SAVE_LOC' => $path,
        'PRIORITY' => 1,
        'CHANGE_FREQUENCY' => 'daily',
        'LAST_UPDATED' => date('Y-m-d'),
    ]);
    $smg->GenerateSitemap();
    $urls = sb_get_sitemap_urls(sb_upload_path(true) . '/sitemap.xml');
    unlink($path);
    return $urls;
}

/*
 * -----------------------------------------------------------
 * OPEN AI
 * -----------------------------------------------------------
 *
 * 1. OpenAI curl
 * 2. Send a message and returns the OpenAI reply
 * 3. Generate Dialogflow user expressions
 * 4. Generate user questions
 * 5. Generate the smart replies
 * 6. Spelling correction
 * 7. Remove auto generated AI texts
 * 8. Check if the message returned by OpenAI is valid
 * 9. Upload a file to OpenAI
 * 10. Embedding functions
 * 11. PDF or TEXT file to paragraphs
 * 12. Get the default gpt model
 * 13. Send an audio file to OpenAI and return it's transcription
 * 14. Return the OpenAI key
 * 15. OpenAI Assistant
 * 16. AI data scraper
 * 17. Troubleshoting
 * 18. HTML to paragraphs
 * 23. Server-side training
 * 20. Get training file names
 * 21. Playground message
 * 22. Create a temporary conversation to test the chatbot
 * 23. Check if an URL is a of a file compatible with the OpenAI training
 * 24. Execute set data
 * 25. Execute actions
 * 26. Get max tokens
 * 27. Send fallback message
 * 28. Tools calling query creation
 * 29. Analyze a message for structured data and return it
 * 30. OpenAI for internal tasks
 *
 */

function sb_open_ai_curl($url_part, $post_fields = [], $type = 'POST') {
    if (sb_cloud_membership_has_credits('open-ai')) {
        $open_ai_key = sb_open_ai_key();
        $response = sb_curl('https://api.openai.com/v1/' . $url_part, json_encode($post_fields, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), ['Content-Type: application/json', 'Authorization: Bearer ' . $open_ai_key], $type, 30);
        if (isset($response['error'])) {
            return sb_error('open-ai-error', 'sb_open_ai_curl', $response['error']);
        }
        if (sb_is_cloud() && sb_defined('OPEN_AI_KEY') == $open_ai_key) {
            $tokens = sb_isset(sb_isset($response, 'usage'), 'total_tokens');
            $model = sb_isset($post_fields, 'model', 'gpt-4.1-mini');
            if (!$tokens) {
                return sb_error('no-usage', 'sb_open_ai_curl');
            }
            sb_cloud_membership_use_credits($model, 'open-ai', $tokens);
        }
        return $response;
    }
    return sb_error('no-credits', 'sb_open_ai_curl');
}

function sb_open_ai_message($message, $max_tokens = false, $model = false, $conversation_id = false, $extra = false, $audio = false, $attachments = [], $context = false) {
    global $SB_OPEN_AI_PLAYGROUND;
    global $SB_OPEN_AI_RECURSION_CHECK;
    global $SB_OPEN_AI_RECURSION_CHECK_2;
    global $SB_OPEN_AI_RECURSION_CHECK_3;
    global $SB_OPEN_AI_RECURSION_CHECK_4;
    $language = strtolower(sb_isset($extra, 'language'));
    $language_mvt = false;
    $attachments_response = [];
    $prompt_base = 'Provide answers to the user message from the CONTEXT below. If the answer is not included in the CONTEXT, write exactly "I don\'t know." in English language and stop after that. Do not provide external knowledge. Do not answer unrelated questions. Never break character. Only provide the direct answer without explaining it was from the information or context provided.';
    $is_scraping = $extra == 'scraping';
    $is_embeddings = sb_isset($extra, 'embeddings');
    $is_rewrite = sb_isset($extra, 'rewrite');
    $is_smart_reply = sb_isset($extra, 'smart_reply');
    $is_system_task = $extra == 'system-task';
    $tools_calling = sb_isset($extra, 'tools');
    $message = $audio ? sb_open_ai_audio_to_text($audio, $language, sb_isset($extra, 'user_id'), false, $conversation_id) : (is_string($message) ? trim($message) : $message);
    $message_ = '';
    $attachments = sb_json_array($attachments);
    if ($is_rewrite) {
        $prompt_message_rewrite = sb_get_multi_setting('open-ai', 'open-ai-prompt-message-rewrite');
        $message = ($prompt_message_rewrite ? $prompt_message_rewrite : 'Rewrite the following text and make it more friendly and professional, do no add any additional text or comments, always return the rewritten text only.') . (empty($extra['language']) ? '' : ' The response must be in ' . sb_get_language_name_by_code($extra['language']) . ' language.') . PHP_EOL . PHP_EOL . ' Text: ' . $message . '.';
    }
    if (!$is_embeddings && !$is_system_task && !$tools_calling) {
        for ($i = 0; $i < count($attachments); $i++) {
            if (strpos($attachments[$i][0], 'voice_message') === false) {
                $message_ .= $attachments[$i][1] . ', ';
            }
        }
        if ($message_) {
            $message = trim(str_replace('..', '.', $message . ($message ? '.' : '') . ' ' . substr($message_, 0, -2)));
        }
    }
    if (empty($message) && !$tools_calling) {
        sb_delete_external_setting('open_ai_message_busy');
        return [true, false, false, false];
    }
    if (sb_is_cloud()) {
        sb_cloud_membership_validation(true);
        if (!sb_cloud_membership_has_credits('open-ai')) {
            sb_delete_external_setting('open_ai_message_busy');
            return sb_error('no-credits', 'sb_open_ai_message');
        }
    }
    $settings = sb_get_setting('open-ai');
    $response = false;
    $conversation_status_code = false;
    $dialogflow_active = sb_chatbot_active(true, false);
    $token = sb_isset($extra, 'token');
    $human_takeover = false;
    $human_takeover_settings = sb_dialogflow_get_human_takeover_settings();
    $human_takeover_active = $human_takeover_settings['active'];
    $payload = [];
    $unknow_answer = false;
    $open_ai_mode = sb_isset($settings, 'open-ai-mode', '');
    $extra_response = false;
    $client_side_payload = [];
    $messages = $conversation_id ? sb_db_get('SELECT A.id, A.message, A.payload, A.user_id, A.creation_time, B.user_type FROM sb_messages A, sb_users B, sb_conversations C WHERE A.conversation_id = ' . sb_db_escape($conversation_id, true) . ' AND A.conversation_id = C.id AND B.id = A.user_id ORDER BY A.id ASC LIMIT 1000', false) : ($is_system_task ? [] : [['message' => $message, 'user_type' => 'user']]);
    $count = count($messages);
    $flows_structured_output = false;
    $is_chips_response = false;
    $user_id = sb_isset($extra, 'user_id', sb_get_active_user_ID());
    $is_embedding_response = false;
    $is_human_takeover = false;
    $is_multilingual_via_translation = sb_get_multi_setting('google', 'google-multilingual-translation');
    $model = $model ? $model : sb_isset($settings, 'open-ai-custom-model', sb_isset($settings, 'open-ai-model', 'gpt-5-mini'));
    $url_part = 'chat/completions';
    $is_human_request = false;
    $is_chat_message = $conversation_id && !$is_embeddings && !$is_rewrite && !$is_scraping && !$is_smart_reply && !$is_system_task && !$tools_calling;
    $is_playground = sb_open_ai_is_playground();
    $query = ['model' => $model, 'temperature' => floatval(sb_isset($settings, 'open-ai-temperature', 1)), 'presence_penalty' => floatval(sb_isset($settings, 'open-ai-presence-penalty', 0)), 'frequency_penalty' => floatval(sb_isset($settings, 'open-ai-frequency-penalty', 0)), 'top_p' => 1, 'tools' => []];
    if ($token == 'false') {
        $token = false;
    }
    if ($is_chat_message && !$is_playground) {
        if (sb_get_external_setting('open_ai_message_busy', 0) > time()) {
            $infinite_loop_index = 0;
            do {
                sleep(5);
                $infinite_loop_index++;
            } while (sb_get_external_setting('open_ai_message_busy') && $infinite_loop_index < 8);
            if ($infinite_loop_index == 8) {
                sb_delete_external_setting('open_ai_message_busy');
            }
            return [true, false];
        }
        sb_save_external_setting('open_ai_message_busy', time() + 60);
    }
    if (!$dialogflow_active) {
        $is_human_takeover = $is_chat_message && !$is_playground && sb_dialogflow_is_human_takeover($conversation_id);
        if ($is_human_takeover && $count) {
            $time = sb_gmt_now(600, true);
            $message_fallback = $human_takeover_active ? $human_takeover_settings['message_fallback'] : false;
            for ($i = $count - 1; $i > -1; $i--) {
                if (sb_is_agent($messages[$i]['user_type'], true) && sb_is_user_online($messages[$i]['user_id'])) {
                    sb_delete_external_setting('open_ai_message_busy');
                    return [true, false];
                }
            }
            if ($message_fallback) {
                for ($i = $count - 1; $i > -1; $i--) {
                    if (strpos($messages[$i]['payload'], 'human-takeover-message-fallback')) {
                        if (strtotime($messages[$i]['creation_time']) > $time) {
                            $message_fallback = false;
                        }
                        break;
                    }
                }
                if ($message_fallback) {
                    sb_delete_external_setting('open_ai_message_busy');
                    sb_send_message(sb_get_bot_ID(), $conversation_id, $message_fallback, $attachments_response, false, ['human-takeover-message-fallback' => true]);
                    sb_webhooks('SBOpenAIMessage', ['response' => $message_fallback, 'message' => $message, 'conversation_id' => $conversation_id]);
                    return [true, $message_fallback, $token, true];
                }
            }
            if (sb_isset($human_takeover_settings, 'disable_chatbot')) {
                sb_delete_external_setting('open_ai_message_busy');
                return [true, false];
            }
        }

        // Human takeover messaging apps
        if ((sb_isset($extra, 'messaging-app') || $is_playground) && $human_takeover_active && !$is_smart_reply && !$is_system_task && !$tools_calling) {
            $is_button_confirm = sb_rich_value($human_takeover_settings['confirm'], false) == $message;
            $is_human_takeover_request = $count > 1 && strpos($messages[$count - 2]['message'] . $messages[$count - 2]['payload'], 'sb-human-takeover');
            $ai_detection = false;
            if (!$is_button_confirm && $is_human_takeover_request) {
                $ai_detection = sb_open_ai_analyze_message('If the message is a positive affirmation, confirmation, or approval (e.g., ok, yes please, sure), respond exactly "yes". If it does not, respond exactly "no". Always respond exactly "yes" or "no" with no additional text.', $message, $conversation_id);
                $is_button_confirm = $ai_detection == 'yes';
            }
            if ($is_button_confirm) {
                if ($is_human_takeover_request) {
                    sb_delete_external_setting('open_ai_message_busy');
                    return [true, sb_dialogflow_human_takeover($conversation_id), false, $is_button_confirm];
                }
            } else if (sb_rich_value($human_takeover_settings['cancel'], false) == $message) {
                sb_delete_external_setting('open_ai_message_busy');
                return [true, false, false, false];
            }
        }

        // Multilingual
        if (!$is_rewrite && !$is_scraping && !$is_system_task && (sb_get_multi_setting('google', 'google-translation') || $is_multilingual_via_translation)) {
            if (!$language && sb_get_multi_setting('google', 'google-language-detection') && strlen($message) > 2) {
                $language = sb_get_user_extra($user_id, 'language') ?: sb_google_language_detection($message);
                if ($language) {
                    sb_language_detection_db($user_id, $language);
                    $payload['event'] = 'update-user';
                }
            } elseif (!$language) {
                $language = sb_get_user_language($user_id);
            }
        }
        $language_mvt = $is_multilingual_via_translation ? $language : false;
    }

    // Assistant
    if ($open_ai_mode == 'assistant') {
        if ($conversation_id) {
            $response = sb_open_ai_assistant($message, $conversation_id, $is_chat_message);
            if (sb_is_error($response)) {
                $response = '';
            }
        } else {
            $open_ai_mode = '';
        }
    } else {

        // Flows structured output check and chips response
        if ($is_chat_message) {
            if (($count == 1 || ($count == 2 && $messages[0]['user_type'] == 'bot')) && $conversation_id) {
                $flow_start = sb_flows_on_conversation_start_or_load($messages, $language_mvt, $conversation_id);
                if ($flow_start) {
                    sb_delete_external_setting('open_ai_message_busy');
                    return [true, [['message' => $flow_start]], $token, false];
                }
            }
            for ($i = $count - 1; $i > -1; $i--) {
                $is_break = false;
                $payload_temp = sb_isset($messages[$i], 'payload');
                if (str_contains($payload_temp, 'flow_end_so')) {
                    if ($i == $count - 2) {
                        $ai_detection = sb_open_ai_handle_ai_detection($messages, $count, $message, $conversation_id, $language_mvt, $token, $extra_response, 1);
                        if ($ai_detection) {
                            sb_delete_external_setting('open_ai_message_busy');
                            return $ai_detection;
                        }
                    }
                    $flows_structured_output = false;
                    $is_break = true;
                }
                if (str_contains($payload_temp, 'flow_so')) {
                    $flows_structured_output = json_decode($payload_temp, true);
                    $is_break = true;
                }
                if ($is_break) {
                    break;
                }
            }
            $is_tools_end = false;
            $is_tools_before_end = false;
            for ($i = $count - 1; $i > -1; $i--) {
                $message_text = $messages[$i]['message'];
                $payload_ = json_decode(sb_isset($messages[$i], 'payload'), true);
                if (empty($message_text)) {
                    $message_text = sb_isset($payload_, 'hidden_message', '');
                }
                if (!$is_tools_before_end) {
                    $is_tools_before_end = sb_isset($payload_, 'tools');
                }
                if (!$is_tools_before_end && !$is_tools_end) {
                    $is_tools_end = sb_isset($payload_, 'tools_end');
                }
                if ($message_text) {
                    if (sb_isset($payload_, 'tools') && !$is_tools_end) {
                        if (!$is_tools_end) {
                            $payload['parent_message_id'] = $messages[$i]['id'];
                        }
                    }
                    if (str_contains($message_text, '[chips ')) {
                        $shortcode = sb_isset(sb_get_shortcode($message_text, false), 0);
                        $message_id = sb_isset($shortcode, 'id');
                        if (strpos($message_id, 'flow_') === 0) {
                            if (!$is_tools_end) {
                                $payload['parent_message_id'] = $messages[$i]['id'];
                            }
                            $message_options_hidden = sb_isset($shortcode, 'hidden-options');
                            $message_options = explode(',', str_replace('\,', '{R}', strtolower(sb_isset($shortcode, 'options', $message_options_hidden))));
                            $flow_identifier = explode('_', substr($message_id, 5));
                            $match_index = false;
                            $message_ = strtolower($message);
                            for ($j = 0; $j < count($message_options); $j++) {
                                $message_option = array_map('trim', explode('|', str_replace('{R}', '\,', $message_options[$j])));
                                if (in_array($message_, $message_option)) {
                                    $match_index = $j;
                                    break;
                                }
                            }
                            if ($match_index === false) {
                                $prompt = '';
                                $history = [];
                                $ai_detection = sb_open_ai_analyze_message('If the message mention one of the given comma-separated OPTIONS, return exactly that option. Otherwise, if the message indicates the user wants to revert to a previous step (e.g., due to incorrect input, cancellation, or change of details), return exactly and only "back". Otherwise, return exactly and only "no". Always return only either the matched OPTION, "back", or "no", with no additional text. If you are unsure return "no". OPTIONS: "' . implode(',', str_replace('|', ',', $message_options)) . '".', $message, $conversation_id);
                                if ($ai_detection) {
                                    if ($ai_detection == 'back') {
                                        $ai_detection = sb_open_ai_handle_ai_detection($messages, $count, $message, $conversation_id, $language_mvt, $token, $extra_response, 2);
                                        if ($ai_detection) {
                                            $response = $ai_detection;
                                        } else {
                                            $response = str_replace('disabled="true"', '', $messages[$i]['message']);
                                            $payload_ = json_decode($messages[$i]['payload'], true);
                                            if (!empty($payload_)) {
                                                $payload = array_merge($payload, $payload_);
                                            }
                                        }
                                        break;
                                    } else if ($ai_detection != 'no') {
                                        for ($j = 0; $j < count($message_options); $j++) {
                                            $message_option = array_map('trim', explode('|', $message_options[$j]));
                                            if (in_array($ai_detection, $message_option)) {
                                                $match_index = $j;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($match_index !== false) {
                                $payload['parent_message_id'] = $messages[$i]['id'];
                                $next_response = sb_flows_get_open_ai_message_response($flow_identifier[0], $flow_identifier[1], $flow_identifier[2], $match_index, $payload, $conversation_id);
                                if ($next_response[0] !== false) {
                                    $response = $next_response[0];
                                    if ($is_multilingual_via_translation && $language != sb_get_multi_setting('open-ai', 'open-ai-training-data-language', 'en') && !sb_is_rich_message($response) && !strpos($response, '[action ')) {
                                        $response = sb_t($response, $language);
                                    }
                                }
                                $response = trim($response);
                                $payload = array_merge($payload, $next_response[1]);
                                $attachments_block = sb_isset($next_response[1], 'attachments');
                                if ($attachments_block) {
                                    $attachments_response = array_merge($attachments_response, $attachments_block);
                                }
                                if ($response && ($count < 2 || strpos(str_replace('\\', '', $messages[$count - 2]['payload']), str_replace('\\', '', $response)) === false)) {
                                    $response = ['choices' => [['message' => ['content' => $response]]]];
                                    $is_chips_response = true;
                                } else {
                                    $response = '';
                                }
                            } else if ($i == $count - 2) {
                                $message_options = array_map(function ($item) {
                                    $parts = explode('|', $item);
                                    return trim($parts[0]);
                                }, $message_options);
                                $message_options = implode(',', $message_options);
                                $response = sb_open_ai_rewrite_message('Please choose one of the following options ' . $message_options . '.', $is_multilingual_via_translation ? $language : false);
                            }
                        }
                        break;
                    } else if ($i != ($count - 1) && sb_is_agent($messages[$i]) && !sb_isset(json_decode(sb_isset($messages[$i], 'payload'), true), 'parent_message_id')) {
                        break;
                    }
                }
            }
        }
        for ($i = 0; $i < $count; $i++) {
            $payload_temp = sb_isset($messages[$i], 'payload');
            if (str_contains($payload_temp, 'action')) {
                $payload_temp = json_decode($payload_temp, true);
                if (!empty($payload_temp['action'])) {
                    $messages[$i]['message'] .= ' ' . $payload_temp['action'];
                }
            }
        }
    }

    // Embeddings
    if (!$is_embeddings && !$flows_structured_output && !$is_rewrite && !$is_scraping && !$response && !$is_chips_response && !$is_system_task && !$tools_calling && in_array($open_ai_mode, ['sources', 'all', ''])) { // Deprecated. Remove All
        $extra_embeddings = ['conversation_id' => $conversation_id, 'user_id' => $user_id, 'attachments' => $attachments];
        if ($is_smart_reply) {
            $extra_embeddings['smart_reply'] = true;
        }
        if ($context) {
            $extra_embeddings['context'] = $context;
        }
        if (!$dialogflow_active && $is_multilingual_via_translation) {
            $embeddings_language = sb_open_ai_embeddings_language();
            if (!empty($embeddings_language) && !in_array($language, $embeddings_language)) {
                $translation = sb_google_translate([$message], $embeddings_language[0]);
                if (!empty($translation)) {
                    $message = $translation[0];
                }
                $response = sb_open_ai_embeddings_message($message, $conversation_id, false, $extra_embeddings);
                if ($response) {
                    $translation = sb_google_translate([$response['message']], $language);
                    if (!empty($translation)) {
                        $response['message'] = $translation[0];
                    }
                }
            } else {
                $response = sb_open_ai_embeddings_message($message, $conversation_id, false, $extra_embeddings);
            }
        } else {
            $response = sb_open_ai_embeddings_message($message, $conversation_id, false, $extra_embeddings);
        }
        if (sb_is_error($response)) {
            sb_delete_external_setting('open_ai_message_busy');
            return $response;
        }
        if ($response) {
            $client_side_payload = $response['payload'];
            $payload = array_merge($payload, sb_isset($response, 'payload_message', []));
            $attachments_response = $response['attachments'];
            $embedding_extra = $response['embedding_extra'];
            $embedding_extra_json = json_encode($embedding_extra, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            $response = $response['message'];
            $entities = ['language' => 'language name (e.g. spanish or italian)'];
            if (!$is_smart_reply && str_contains($response, '[chips ') && str_contains($response, 'flow_') && empty($SB_OPEN_AI_RECURSION_CHECK_4)) {
                $shortcode = sb_get_shortcode($response, 'chips');
                $options = sb_isset($shortcode, 'options') . ',' . str_replace(',', ' ', $shortcode['message']);
                if ($options) {
                    $ai_detection = sb_open_ai_analyze_message('If the message mention one of the given comma-separated OPTIONS, return exactly that option, otherwise, return exactly and only "no". Always return only either the matched option, or "no", with no additional text. OPTIONS: "' . $options . '".', $message, $conversation_id);
                    if ($ai_detection && $ai_detection != 'no' && $ai_detection != $shortcode['message']) {
                        $payload['chips'] = $response;
                        sb_db_query('INSERT INTO sb_messages(user_id, message, creation_time, attachments, status_code, payload, conversation_id) VALUES (' . sb_get_bot_ID() . ', "", "' . sb_gmt_now() . '", "", 0, "' . sb_db_json_escape(['hidden_message' => $response]) . '", ' . sb_db_escape($conversation_id, true) . ')');
                        sb_delete_external_setting('open_ai_message_busy');
                        $SB_OPEN_AI_RECURSION_CHECK_4 = true;
                        return sb_open_ai_message($ai_detection, $max_tokens, $model, $conversation_id, $extra, false, $attachments);
                    }
                }
            }
            foreach ($entities as $entity => $entity_description) {
                $entity_ = '{' . $entity . '}';
                if (strpos($response, $entity_) || strpos($embedding_extra_json, $entity_)) {
                    $properties = [];
                    $query_ = $query;
                    unset($query_['tools']);
                    $properties[$entity] = ['type' => 'string', 'description' => $entity_description];
                    $query_['messages'] = [['role' => 'user', 'content' => $message]];
                    $query_['response_format'] = ['type' => 'json_schema', 'json_schema' => ['name' => $entity, 'schema' => ['type' => 'object', 'properties' => $properties, 'required' => [$entity], 'additionalProperties' => false], 'strict' => true]];
                    $response_json = sb_open_ai_curl($url_part, $query_);
                    if ($response_json && !empty($response_json['choices'])) {
                        $response_json = sb_isset(json_decode(sb_isset($response_json['choices'][0]['message'], 'content', '[]'), true), $entity);
                        if ($response_json) {
                            if ($entity == 'language') {
                                $response_json = sb_get_language_code_by_name($response_json);
                                if (strlen($response_json) == 2 && !sb_is_agent()) {
                                    $payload['event'] = 'update-user';
                                    sb_update_user($user_id, [], ['language' => [$response_json, 'Language']]);
                                }
                            }
                            $response = str_replace($entity_, $response_json, $response);
                            $embedding_extra_json = str_replace($entity_, $response_json, $embedding_extra_json);
                            if ($is_multilingual_via_translation) {
                                $translation = sb_google_translate([$response], $response_json);
                                if (!empty($translation)) {
                                    $response = $translation[0];
                                }
                            }
                        }
                    }
                }
            }
            if ($embedding_extra && !sb_is_agent()) {
                $embedding_extra_set_data = sb_isset(json_decode($embedding_extra_json, true), 'set_data');
                if ($embedding_extra_set_data) {
                    sb_open_ai_execute_set_data($embedding_extra_set_data);
                    $embedding_extra_set_data_ = [];
                    foreach ($embedding_extra_set_data as $key => $value) {
                        array_push($embedding_extra_set_data_, [$key, $value]);
                    }
                    if (isset($embedding_extra_set_data['archive_conversation']) || isset($embedding_extra_set_data['human_takeover'])) {
                        $conversation_status_code = 'skip';
                    }
                    $execute_actions = sb_open_ai_execute_actions($embedding_extra_set_data_, $conversation_id);
                    $client_side_payload = array_merge($client_side_payload, $execute_actions['client_side_payload']);
                    $attachments_response = array_merge($attachments_response, $execute_actions['attachments']);
                }
            }
        }
        $is_embedding_response = true;
    }
    if ($is_chips_response || $flows_structured_output || !$response || $tools_calling) {
        $is_function_calling_only = !$is_chips_response && !$flows_structured_output && !$response && !$is_embeddings && !$is_rewrite && !$is_system_task && !$is_scraping && $open_ai_mode == 'sources';
        $max_tokens = intval($max_tokens ? $max_tokens : sb_isset($settings, 'open-ai-tokens'));
        $is_translations = !$is_scraping && sb_get_multi_setting('google', 'google-translation');
        $first_message = false;
        $open_ai_length = 0;
        $open_ai_max_tokens = sb_open_ai_get_max_tokens($model);
        $prompt_real_time = $is_embeddings && sb_get_setting('dialogflow-google-search', 'dialogflow-google-search-active') ? ' If the user message is about real-time information, a calendar date or time, recent events, or current information, write exactly "I don\'t know."' : '';
        $prompt_language = $is_multilingual_via_translation || ($is_smart_reply && sb_get_multi_setting('google', 'google-translation')) ? sb_get_user_language($is_smart_reply ? sb_get_active_user_ID() : $user_id) : false;
        $prompt_language = $prompt_language && $prompt_language != 'en' ? ' If the answer is included in the context always answer to the user message in the language of the "' . strtoupper($prompt_language) . '" language code.' : '';
        $prompt = $is_scraping ? $message : sb_isset($settings, 'open-ai-prompt', $is_embeddings ? $prompt_base . $prompt_language . $prompt_real_time : $prompt_language);
        if ($context && !$is_embeddings) {
            $prompt .= PHP_EOL . PHP_EOL . ' The user\'s message might be about this: ' . $context . '.';
        }
        if ($is_translations) {
            for ($i = 0; $i < $count; $i++) {
                $messages[$i] = sb_google_get_message_translation($messages[$i], $language);
            }
        }
        if (!empty($settings['open-ai-logit-bias'])) {
            $query['logit_bias'] = json_decode($settings['open-ai-logit-bias'], true);
        }
        $query_messages = [];
        if ($max_tokens) {
            $query['max_tokens'] = $max_tokens;
        }
        if ($prompt) {
            $message_context = $is_embeddings ? $context : $message;
            if (strlen($message_context) > 9999) {
                $message_context = substr($message_context, 0, 9999);
            }
            if (sb_is_rich_message($message_context) || strpos($message_context, '[action ')) {
                $prompt .= ' If your answer includes text in square brackets, provide all strings in square brackets as they are and stop generating additional text' . ($prompt_language ? ' and do not translate the text within square brackets but leave the original text as it is' : '') . '.';
            }
            if (substr_count($message_context, 'Answer: ') > 1) {
                $prompt .= ' If the CONTEXT contains multiple Questions: and Answers:, return only the single answer that is most relevant to the user message.';
            }
            $first_message = ['role' => 'developer', 'content' => $prompt . ($is_embeddings ? PHP_EOL . PHP_EOL . ' CONTEXT: ' . $message_context : '')];
            $open_ai_length += strlen($message_context);
        }
        for ($i = $count - 1; $i > -1; $i--) {
            $message_text = $messages[$i]['message'];
            if (intval(($open_ai_length + strlen($message_text)) / 4) < $open_ai_max_tokens) {
                if (sb_open_ai_is_valid($message_text) || $is_scraping) {
                    $message_is_agent = sb_is_agent($messages[$i]['user_type']);
                    if (!$is_scraping || !$message_is_agent) {
                        array_unshift($query_messages, ['role' => $message_is_agent ? 'assistant' : 'user', 'content' => $message_text]);
                        $open_ai_length += strlen($message_text);
                    }
                }
            } else {
                break;
            }
        }

        // Vision
        if (!$is_smart_reply && !$is_rewrite && !$is_scraping && !$is_system_task && !$tools_calling && in_array($model, ['gpt-5', 'gpt-5-mini', 'gpt-5-nano', 'gpt-4.1-nano', 'gpt-4.1-mini', 'gpt-4.1', 'gpt-4o-mini', 'gpt-4o', 'gpt-4', 'gpt-4-32k']) && sb_isset($settings, 'open-ai-vision')) {
            foreach ($attachments as $attachment) {
                if (preg_match('/\.(jpe?g|png|webp|gif|bmp|tiff?)$/i', $attachment[1])) {
                    array_push($query_messages, ['role' => 'user', 'content' => [['type' => 'image_url', 'image_url' => ['url' => $attachment[1]]]]]);
                }
            }
        }

        if (empty($query_messages) && !$tools_calling && !$is_system_task) {
            sb_delete_external_setting('open_ai_message_busy');
            return [false, false];
        }
        if (!$is_rewrite && !$is_system_task && !$tools_calling) {
            if ($first_message) {
                array_unshift($query_messages, $first_message);
            }

            // Structured output
            if ($flows_structured_output) {
                $flows_structured_output_string = $flows_structured_output['flow_so'];
                $block = sb_flows_get_by_string($flows_structured_output_string);
                if ($block) {
                    $descriptions = ['full_name' => 'The person name e.g. Olivia Smith', 'email' => 'The email address e.g. olivia.smith@gmail.com', 'password' => 'An string used as a password', 'address' => 'An full address e.g. 90 Fetter Ln, London EC4A 1EN or 125 W 24th St, New York, NY 10011, USA', 'country' => 'A country name or code e.g. US or United Kingdom', 'state' => 'The state name e.g. New York', 'phone' => 'The phone number e.g. +393203057977', 'language' => 'The language name or language code e.g. Spanish or ES', 'company' => 'The business or company name e.g. Nike', 'webiste' => 'The website URL e.g. www.google.com', 'city' => 'The city e.g. San Francisco', 'postal_code' => 'The postal code e.g. 10001 or SW1A 1AA', 'birthdate' => 'The birthdate e.g. 25 July 1990 or 31/05/89'];
                    $properties = [];
                    $required = [];
                    for ($i = 0; $i < count($block['details']); $i++) {
                        $details = $block['details'][$i];
                        $properties[$details[0]] = ['type' => ['string', 'null'], 'description' => sb_isset($details, 1, sb_isset($descriptions, $details[0], sb_string_slug($details[0], 'string')))];
                        array_push($required, $details[0]);
                    }
                    $query['response_format'] = ['type' => 'json_schema', 'json_schema' => ['name' => 'flow-' . sb_string_slug($flows_structured_output_string, 'slug', true), 'schema' => ['type' => 'object', 'properties' => $properties, 'required' => $required]]];
                    $count_query_messages = count($query_messages);
                    if ($count_query_messages > 1) {
                        $query_messages_ = [];
                        for ($i = $count_query_messages - 1; $i > -1; $i--) {
                            array_unshift($query_messages_, $query_messages[$i]);
                            if ($i < $count_query_messages - 1 && str_contains($query_messages[$i]['content'], 'flow-so')) {
                                break;
                            }
                        }
                        $query_messages = $query_messages_;
                    }
                }
            }

            // Tools calling
            if (!$is_smart_reply && !$is_rewrite && !$is_scraping && !$is_system_task && in_array($model, ['gpt-5', 'gpt-5-mini', 'gpt-5-nano', 'gpt-4.1-nano', 'gpt-4.1-mini', 'gpt-4.1', 'o3-mini', 'o4-mini', 'o1', 'gpt-4o-mini', 'gpt-4o', 'gpt-4', 'gpt-4-32k'])) {
                if ($human_takeover_active) {
                    $query['tools'] = sb_open_ai_tools_query('sb-human-takeover', 'I want to contact a human support agent or team member. I want human support. How can I have support?');
                }
                if (!$flows_structured_output && empty($SB_OPEN_AI_RECURSION_CHECK_2)) {
                    $continue = true;
                    for ($i = $count - 1; $i > -1; $i--) {
                        $message_payload = json_decode(sb_isset($messages[$i], 'payload'), true);
                        $tool_name = sb_isset($message_payload, 'tools');
                        if ($tool_name) {
                            $tools_block = sb_flows_get_by_string($tool_name);
                            $query['tools'] = $query['tools'] = array_merge($query['tools'], sb_open_ai_tools_query($tool_name, $tools_block['message'], $tools_block['properties']));
                            $continue = false;
                            break;
                        } else if ((sb_is_agent($messages[$i]) && empty($message_payload['parent_message_id'])) || isset($message_payload['tools_end'])) {
                            break;
                        }
                    }
                    if ($continue) {
                        $qea = sb_get_external_setting('embedding-texts', []);
                        for ($i = 0; $i < count($qea); $i++) {
                            if (!empty($qea[$i][2])) {
                                $query['tools'] = array_merge($query['tools'], sb_open_ai_tools_query(substr(sb_string_slug($qea[$i][0][0], 'slug', true), 0, 20) . '-' . $i, $qea[$i][0][0], $qea[$i][5]));
                            }
                        }
                        if (sb_is_cloud()) {
                            require_once(SB_CLOUD_PATH . '/account/functions.php');
                            if (shopify_get_shop_name()) {
                                $query['tools'] = array_merge($query['tools'], shopify_open_ai_function());
                            }
                        }
                        if (defined('SB_WOOCOMMERCE') && !sb_get_setting('wc-disable-bot-integration')) {
                            $query['tools'] = array_merge($query['tools'], sb_woocommerce_open_ai_function());
                        }
                    }
                }
            }
            $query['messages'] = $query_messages;
        } else if ($tools_calling) {
            $query['tools'] = sb_open_ai_tools_query($tools_calling['name'], '', $tools_calling['properties']);
            $query['messages'] = empty($query_messages) ? [['role' => 'assistant', 'content' => $tools_calling['message']]] : $query_messages;
        } else if ($is_system_task && $count) {
            $query['messages'] = array_merge([['role' => 'system', 'content' => $message]], $query_messages);
        } else {
            $query['messages'] = [['role' => 'system', 'content' => $message]];
        }
        if (isset($extra['query'])) {
            $query = array_merge($query, $extra['query']);
        }
        if (empty($query['tools'])) {
            unset($query['tools']);
        }

        // OpenAI response
        if (!$is_smart_reply && !$is_rewrite && !$is_scraping && !$is_system_task && !$tools_calling) {
            if (empty($response['choices'])) {
                $continue = true;
                if (!empty($query_messages) && !empty($query['tools'])) {
                    if (empty($SB_OPEN_AI_RECURSION_CHECK_3) && !empty($query['response_format']) && !$is_function_calling_only) {
                        $SB_OPEN_AI_RECURSION_CHECK_3 = true;
                        $tools = $query['tools'];
                        $query['messages'][0]['content'] = '';
                        $query['tools'] = [];
                        $response = sb_open_ai_curl($url_part, $query);
                        $query['messages'][0]['content'] = $query_messages[0]['content'];
                        $query['tools'] = $tools;
                        $continue = empty($response['choices']) || empty($response['choices'][0]['message']);
                    }
                    if ($continue && empty($SB_OPEN_AI_RECURSION_CHECK_2) && (sb_isset($query_messages[0], 'role') == 'developer' || $is_function_calling_only) && (count($query['tools']) > 1 || $query['tools'][0]['function']['name'] != 'sb-human-takeover')) {
                        $SB_OPEN_AI_RECURSION_CHECK_2 = true;
                        $human_takeover_tool = $query['tools'][0]['function']['name'] == 'sb-human-takeover' ? $query['tools'][0] : false;
                        if ($is_function_calling_only) {
                            array_unshift($query['messages'], ['role' => 'system', 'content' => 'Only respond when a function call is appropriate. Do not reply to general questions or small talk. If you cannot reply, reply exactly and only "I don\'t know".']);
                        } else {
                            $query['messages'][0]['content'] = '';
                        }
                        if ($human_takeover_tool) {
                            array_shift($query['tools']);
                        }
                        $response = sb_open_ai_curl($url_part, $query);
                        $continue = empty($response['choices']);
                        if (!$continue && empty($response['choices'][0]['message']['tool_calls'])) {
                            $open_ai_response = $response['choices'][0]['message'];
                            $continue = true;
                            $open_ai_response_embeddings = sb_open_ai_embeddings_generate($open_ai_response['content']);
                            if (!empty($open_ai_response_embeddings) && isset($open_ai_response_embeddings[0]['embedding'])) {
                                foreach ($query['tools'] as $tool) {
                                    $tool_embeddings = sb_open_ai_embeddings_generate($tool['function']['description']);
                                    if (!empty($tool_embeddings) && isset($tool_embeddings[0]['embedding']) && sb_open_ai_embeddings_compare($tool_embeddings[0]['embedding'], $open_ai_response_embeddings[0]['embedding'], $tool_embeddings[0]['text'], $open_ai_response_embeddings[0]['text']) > 0.4) {
                                        $continue = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($human_takeover_tool) {
                            $query['tools'] = [$human_takeover_tool];
                        }
                        $query['messages'][0]['content'] = $query_messages[0]['content'];
                    }
                }
                if ($continue && !$is_function_calling_only) {
                    $response = sb_open_ai_curl($url_part, $query);
                }
            }
        } else {
            $response = sb_open_ai_curl($url_part, $query);
        }
        if ($is_playground) {
            $SB_OPEN_AI_PLAYGROUND['usage'] = sb_isset($response, 'usage');
            $SB_OPEN_AI_PLAYGROUND['query'] = $query;
            $SB_OPEN_AI_PLAYGROUND['embeddings'] = sb_isset($GLOBALS, 'SB_OPEN_AI_PLAYGROUND_E');
        }
        if (sb_is_error($response)) {
            return $response;
        }
        if ($response && !empty($response['choices'])) {
            if (isset($query['n'])) {
                if ($is_chat_message) {
                    sb_delete_external_setting('open_ai_message_busy');
                }
                return $response['choices'];
            }
            $response_message = $response['choices'][0]['message'];
            $response_message_content = $response_message['content'];
            $tool_calls = sb_isset($response_message, 'tool_calls');
            if ($tool_calls && count($tool_calls) > 1 && $tool_calls[0]['function']['name'] == 'sb-human-takeover') {
                array_shift($tool_calls);
                $response['choices'][0]['message']['tool_calls'] = $tool_calls;
            }
            $function_calling = sb_open_ai_tools_calling($response, sb_isset($query, 'tools'), $conversation_id);
            if ($function_calling) {

                // Tools calling response
                if ($function_calling[0] == 'sb-human-takeover') {
                    $response = 'sb-human-takeover';
                    $is_human_request = true;
                } else if ($function_calling[2] == 'sb-ignore-call') {
                    $SB_OPEN_AI_RECURSION_CHECK_2 = true;
                    sb_delete_external_setting('open_ai_message_busy');
                    return sb_open_ai_message($message, $max_tokens, $model, $conversation_id, $extra);
                } else {
                    unset($query['tools']);
                    unset($query['tool_choice']);
                    if ($is_embeddings) {
                        array_shift($query_messages);
                    }
                    if ($function_calling[0] == 'sb-shortcode') {
                        $query_messages = array_slice($query_messages, -5);
                        $button_text = sb_('More details');
                        array_unshift($query_messages, ['role' => 'developer', 'content' => 'If the last user message is about showing products that meet the user criteria format the response using the following shortcode and replace the strings like {{id}} with the correct values: [slider image-1="{{image}}" header-1="{{title}}" description-1="{{description}}..." link-1="{{url}}" link-text-1="' . $button_text . '" extra-1="{{price}}" image-2="{{image}}" header-2="{{title}}" description-2="{{description}}..." link-2="{{url}}" link-text-2="' . $button_text . '" extra-2="{{price}}" image-3="{{image}}" header-3="{{title}}" description-3="{{description}}..." link-3="{{url}}" link-text-3="' . $button_text . '" extra-3="{{price}}"]. If your response include only one product, use this shortcode: [card image="{{image}}" header="{{title}}" description="{{description}}..." link="{{link}}" link-text="' . $button_text . '" extra="{{price}}"]. If the response contains image links show the images with the shortcode [slider-images images="URL,URL,URL"].']);
                    }
                    if (!empty($function_calling[3])) {
                        $payload = array_merge($payload, $function_calling[3]);
                    }
                    array_push($query_messages, $response_message);
                    foreach ($tool_calls as $tool_call) {
                        array_push($query_messages, ['role' => 'tool', 'content' => json_encode($function_calling[2], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), 'tool_call_id' => $tool_call['id']]);
                    }
                    $query['messages'] = $query_messages;
                    $response = sb_open_ai_curl('chat/completions', $query);
                    if (isset($response['error'])) {
                        sb_delete_external_setting('open_ai_message_busy');
                        return [false, $response];
                    }
                    if (!empty($response['choices'])) {
                        $response_message_content = sb_isset($response['choices'][0], 'message', [])['content'];
                        if ($function_calling[0] == 'sb-shortcode') {
                            if (strpos($response_message_content, '[slider ') || strpos($response_message_content, '[card ') || strpos($response_message_content, '[slider-images ')) {
                                $response_message_content_shortcode = sb_get_shortcode($response_message_content, false);
                                if (!empty($response_message_content_shortcode)) {
                                    $message_ = str_replace($response_message_content_shortcode[0]['shortcode'], '', $response_message_content);
                                    $message_ = str_replace(': .', '.', preg_replace('/\s+/', ' ', $message_));
                                    if (substr($message_, -1) === '.') {
                                        $message_ = substr($message_, 0, -1);
                                    }
                                    $message_ = trim($message_);
                                    sb_send_message(sb_get_bot_ID(), $conversation_id, $message_);
                                    sb_messaging_platforms_send_message($message_, $conversation_id);
                                    $response_message_content = $response_message_content_shortcode[0]['shortcode'];
                                }
                            }
                        } else {
                            $response_message_content = sb_open_ai_text_formatting($response_message_content);
                        }
                    }
                    $function_calling = false;
                }
            } else if ($flows_structured_output) {
                $user_data = [];
                if (empty($tool_calls)) {
                    if (substr($response_message_content, 0, 2) == '{"') {
                        $array = explode("\n", $response_message_content);
                        $array = array_filter($array, 'trim');
                        $array = array_unique($array);
                        $response_message_content = implode("\n", $array);
                        $user_data = json_decode($response_message_content, true);
                    }
                } else {
                    $user_data = json_decode(sb_isset($tool_calls[0]['function'], 'arguments', '{}'), true);
                }
                $block = sb_flows_get_by_string($flows_structured_output_string);
                $required_missing = '';
                $is_back = strpos(sb_isset(sb_isset($messages, $count - 2), 'payload'), 'is_back');
                foreach ($block['details'] as $detail) {
                    if (empty($user_data[$detail[0]]) || $user_data[$detail[0]] == 'unknown') {
                        if (!$detail[2] || $is_back) {
                            if (sb_is_agent()) {
                                $user_data[$detail[0]] = '';
                            } else if ($detail[0] == 'email') {
                                $user_data[$detail[0]] = sb_get_active_user()['email'];
                            } else if ($detail[0] == 'full_name') {
                                $user_data[$detail[0]] = (empty(sb_get_active_user()['last_name']) || substr(sb_get_active_user()['last_name'], 0, 1) == '#') ? false : sb_get_user_name();
                            } else {
                                $user_data[$detail[0]] = sb_get_user_extra($user_id, $detail[0]);
                            }
                        }
                        if ($detail[2] && empty($user_data[$detail[0]])) {
                            $required_missing .= sb_string_slug($detail[0], 'string') . ', ';
                        }
                    }
                }
                if (empty($required_missing)) {
                    $payload['flow_end_so'] = true;
                    $continue = true;
                    if ($is_playground) {
                        $SB_OPEN_AI_PLAYGROUND['payload'] = ['flow_end_so' => true];
                    }
                    if ($is_back) {
                        $back_details = json_decode($response_message_content, true);
                        $response_message_content = '';
                        foreach ($back_details as $key => $detail) {
                            if (!empty($detail)) {
                                $response_message_content .= sb_string_slug($key, 'string') . ' successfully updated to ' . $user_data[$key] . ', ';
                            }
                        }
                        $response_message_content = sb_open_ai_rewrite_message(substr($response_message_content, 0, -2), $language_mvt);
                        for ($i = $count - 2; $i > -1; $i--) {
                            if (strpos($messages[$i]['payload'], 'flow_end_so') || strpos($messages[$i]['payload'], 'action flow-so')) {
                                if (strpos($messages[$i]['payload'], 'flow_end_so') && !strpos($messages[$i]['payload'], 'action flow-so')) {
                                    $continue = false;
                                }
                                break;
                            }
                        }
                        if (!$continue) {
                            sb_send_message(sb_get_bot_ID(), $conversation_id, $response_message_content, [], -1, $payload);
                        }
                    }
                    if (!sb_is_agent()) {
                        $full_name = sb_isset($user_data, 'full_name');
                        if ($full_name) {
                            $full_name = sb_split_name($full_name);
                            $user_data['first_name'] = $full_name[0];
                            $user_data['last_name'] = $full_name[1];
                            unset($user_data['full_name']);
                        }
                        sb_update_user(sb_get_active_user_ID(), $user_data, $user_data, true, true);
                    }
                    if ($continue) {
                        $next_response = sb_flows_get_open_ai_message_response($block['index'][0], $block['index'][1], $block['index'][2], $block['index'][3], $payload, $conversation_id);
                        if ($next_response[0] !== false) {
                            $response_message_content = $next_response[0];
                            $embeddings_language = sb_open_ai_embeddings_language();
                            if ($language_mvt && !empty($embeddings_language) && !in_array($language_mvt, $embeddings_language)) {
                                $translation = sb_google_translate([$response_message_content], $language_mvt);
                                if (!empty($translation)) {
                                    $response_message_content = $translation[0];
                                }
                            }
                        }
                        $payload = array_merge($payload, $next_response[1]);
                    } else {
                        if ($is_chat_message) {
                            sb_delete_external_setting('open_ai_message_busy');
                        }
                        return [true, ''];
                    }
                } else {
                    $continue = true;
                    $response_message_content_ = json_decode($response_message_content, true);
                    if (!empty($response_message_content_)) {
                        foreach ($response_message_content_ as $value) {
                            if (!empty($value)) {
                                $continue = false;
                                break;
                            }
                        }
                    }
                    if ($continue) {
                        $ai_detection = sb_open_ai_handle_ai_detection($messages, $count, $message, $conversation_id, $language_mvt);
                        if ($ai_detection) {
                            $response_message_content = $ai_detection;
                            $continue = false;
                            $payload = array_merge($payload, ['is_back' => true]);
                        }
                    } else {
                        $continue = true;
                    }
                    if ($continue) {
                        $response_message_content = sb_open_ai_rewrite_message('What is your ' . substr($required_missing, 0, -2) . '?', $language_mvt);
                    }
                }
            }
            if (is_array($response_message_content)) {
                $response_message_content = '';
            }
            if ($response_message_content && str_contains($response_message_content, '[action ') && !$is_smart_reply && !$is_rewrite && !$is_scraping && !$is_system_task) {

                // Action response
                $action = sb_flows_execute($response_message_content, $messages, $language_mvt, $conversation_id);
                $response = $action[0];
                $client_side_payload = array_merge($client_side_payload, $action[1]);
                $attachments_response = array_merge($attachments_response, $action[2]);
                $payload = array_merge($payload, $action[4]);
            } else if (!$is_human_request && sb_isset($function_calling, 0) != 'sb-shortcode') {

                // Normal response
                $response = sb_open_ai_text_formatting($response_message_content);
            }
        } else {
            if (isset($response['error'])) {
                sb_delete_external_setting('open_ai_message_busy');
                return [false, $response];
            } else {
                $response = false;
            }
        }
    }
    $unknow_answer = !sb_open_ai_is_valid($response);

    // Human Takeover and Google Search
    if (!$is_rewrite && !$is_scraping && !$is_system_task) {
        if ($is_smart_reply) {
            return $response && !$unknow_answer ? [true, $response, $token, $extra_response, false, $client_side_payload, $attachments_response, $payload] : [false, false];
        }
        if (empty($SB_OPEN_AI_RECURSION_CHECK) && !$is_embeddings && !$is_human_request && $unknow_answer && $response != 'sb-human-takeover' && strlen($message) > 4) {
            $google_search_settings = sb_get_setting('dialogflow-google-search');
            $SB_OPEN_AI_RECURSION_CHECK = true;
            if (sb_isset($google_search_settings, 'dialogflow-google-search-active')) {
                $google_search_response = sb_isset(sb_get('https://www.googleapis.com/customsearch/v1?key=' . $google_search_settings['dialogflow-google-search-key'] . '&cx=' . $google_search_settings['dialogflow-google-search-id'] . '&q=' . urlencode($message), true), 'items');
                if (!empty($google_search_response)) {
                    $limiter = 0;
                    $google_search_page_response_text = '';
                    for ($i = 0; $i < count($google_search_response); $i++) {
                        $google_search_page_response = sb_open_ai_html_to_paragraphs(sb_isset($google_search_response[$i], 'link'));
                        if ($google_search_page_response[1] == 200) {
                            $google_search_page_response_text .= ($limiter ? PHP_EOL . '--------------------------------------------------------------------------------' . PHP_EOL : '') . implode(PHP_EOL . ' ' . PHP_EOL, array_column($google_search_page_response[0], 0));
                            $limiter++;
                            if ($limiter > 4) {
                                break;
                            }
                        }
                    }
                    $google_search_response = sb_open_ai_message($message, false, false, false, ['embeddings' => true, 'user_id' => $user_id], false, [], trim($google_search_page_response_text));
                    if ($google_search_response[0] && !empty($google_search_response[1]) && empty($google_search_response[5]['unknow_answer'])) {
                        $response = $google_search_response[1];
                        $unknow_answer = false;
                    }
                }
            }
        }
        $human_takeover = !$is_embeddings && !$dialogflow_active && $human_takeover_active && ($is_human_request || ($unknow_answer && ((strlen($message) > 3 && strpos($message, ' ')) || strlen($message) > 10)));
        if ($human_takeover && $conversation_id) {
            if ($is_chat_message) {
                sb_delete_external_setting('open_ai_message_busy');
            }
            if (!$is_human_takeover) {
                if (sb_isset($extra, 'messaging-app') == 'em') {
                    return [true, sb_dialogflow_human_takeover($conversation_id), $token, true];
                }
                $human_takeover = sb_chatbot_human_takeover($conversation_id, $human_takeover_settings);
                return [true, $human_takeover[0], $token, $human_takeover[1]];
            }
            return [true, '', $token];
        }
        if ($is_human_request && $is_human_takeover) {
            $response = $human_takeover_settings['message_fallback'];
            $payload['human-takeover-message-fallback'] = true;
            if ($is_embeddings) {
                $extra_response = 'skip-references';
            }
        } else if (!$response && empty($payload)) {
            $response = $dialogflow_active || $is_embeddings ? false : sb_t(sb_isset($settings, 'open-ai-fallback-message', 'Sorry, I didn\'t get that. Can you rephrase?'), $language_mvt);
        } else if (!$dialogflow_active && $is_human_takeover && $unknow_answer) {
            $response = false;
        }
    }
    if (!$is_embeddings && $response == 'sb-human-takeover') {
        $response = '';
    }
    if ($is_chat_message) {
        sb_delete_external_setting('open_ai_message_busy');
    }

    // Response
    if ($response || !empty($payload)) {
        if ($is_chat_message && !empty($response) && !$dialogflow_active) {
            if ($conversation_id && $user_id && count($messages)) {
                $message_ = sb_db_get('SELECT message, attachments FROM sb_messages WHERE  conversation_id = ' . sb_db_escape($conversation_id, true) . ' AND id > ' . $messages[$count - 1]['id'] . ' AND user_id = ' . sb_db_escape($user_id, true) . ' ORDER BY id DESC LIMIT 1');
                if ($message_) {
                    return sb_open_ai_message($message_['message'], $max_tokens, $model, $conversation_id, $extra, false, $message_['attachments']);
                }
            }
            sb_send_message(sb_get_bot_ID(), $conversation_id, $response, $attachments_response, $conversation_status_code, $payload);
            sb_webhooks('SBOpenAIMessage', ['response' => $response, 'message' => $message, 'conversation_id' => $conversation_id]);
        }
        if ($unknow_answer) {
            $client_side_payload['unknow_answer'] = true;
        }
        return [true, $response, $token, $extra_response, false, $client_side_payload, $attachments_response, $payload];
    }
    return [$is_embedding_response, $response];
}

function sb_open_ai_handle_ai_detection($messages, $count, $message, $conversation_id, $language_mvt, $token = false, $extra_response = false, $type = 1) {
    $options = [];
    $history = [];
    for ($k = $count - 3; $k > -1; $k--) {
        $message = $messages[$k]['message'];
        $payload = json_decode(sb_isset($messages[$k], 'payload'), true);
        if (empty($message)) {
            $message = sb_isset($payload, 'hidden_message', '');
        }
        $flow_block_id = sb_isset($payload, 'flow_so');
        if (($type != 2 && $flow_block_id) || ($type == 2 && str_contains($message, '[chips ') && str_contains($message, 'flow_'))) {
            if ($type == 2) {
                $shortcode = sb_isset(sb_get_shortcode($message), 0);
                if ($shortcode && !in_array($shortcode['id'], $history)) {
                    array_push($options, ['id' => $shortcode['id'], 'options' => $shortcode['message'] . ' ' . $shortcode['options']]);
                    array_push($history, $shortcode['id']);
                }
            } else {
                $block_details = sb_isset(sb_flows_get_by_string($flow_block_id), 'details');
                if ($block_details && !in_array($flow_block_id, $history)) {
                    $options_ = '';
                    foreach ($block_details as $detail) {
                        $options_ .= $detail[0] . ',';
                    }
                    array_push($options, ['id' => $flow_block_id, 'options' => substr($options_, 0, -1) . '". ']);
                    array_push($history, $flow_block_id);
                }
            }
        }
    }
    if ($type == 2 && empty($options)) {
        return false;
    }
    $ai_detection = sb_open_ai_analyze_message('Check if the message is a request to revert to a previous step (for example, "incorrect details I want to change", "Sorry I want to cancel", "I want to change previous details"), ' . (!empty($options) ? ' if yes, check if the message mention one of the given comma-separated OPTIONS and only if yes return the ID of that option, otherwise, ' . ($type == 2 ? 'return exactly and only "no". Always return exactly "no" or the option ID with no additional text' : 'if the message does not include any option, but message is a request to revert to a previous step, return exactly and only "back". Otherwise, if none of the previous checks applies, return exactly and only "no". Always return exactly one of the following: "back", "no", or the option ID, with no additional text') . '. OPTIONS: ' . json_encode($options, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE) : 'return exactly and only "back". Otherwise, return exactly and only "no". Always return only either "back" or "no", with no additional text.'), $message, $conversation_id);
    if ($ai_detection != 'no') {
        for ($k = $count - 2; $k > -1; $k--) {
            $message = $messages[$k]['message'];
            $payload = json_decode(sb_isset($messages[$k], 'payload'), true);
            if (empty($message)) {
                $message = sb_isset($payload, 'hidden_message', '');
            }
            $action = $type == 2 ? $message : sb_isset($payload, 'action');
            if ($k == $count - 2 && str_contains($action, '"' . $ai_detection . '"')) {
                break;
            } else {
                if ($action && ($ai_detection == 'back' || str_contains($action, $ai_detection))) {
                    switch ($type) {
                        case 1:
                            $texts = ['Sure, please share the updated details.', 'Okay, please provide the updated details.', 'Sure! Could you share the updated details?'];
                            $action = sb_flows_execute($action, $messages, $language_mvt, $conversation_id, ['is_back' => true]);
                            $action[0] = sb_($texts[rand(0, 2)]);
                            sb_send_message(sb_get_bot_ID(), $conversation_id, $action[0], [], -1, ['is_back' => true]);
                            return [true, $action[0], $token, $extra_response, false, $action[1], $action[2], ['action' => $action[3]['shortcode']]];
                        case 2:
                            $action = substr($ai_detection, 5);
                            return sb_flows_get_block_code(sb_flows_get_by_string($action), $action);
                        default:
                            return $action;
                    }
                }
            }
        }
    }
    return false;
}

function sb_open_ai_user_expressions($message) {
    $settings = sb_get_setting('open-ai');
    $response = sb_open_ai_curl('chat/completions', ['messages' => [['role' => 'user', 'content' => 'Create a numbered list of minimum 5 variants of this sentence and only return the list. Change all the words with another word. Sentence: ' . $message]], 'model' => sb_open_ai_get_gpt_model(), 'max_tokens' => 200, 'temperature' => floatval(sb_isset($settings, 'open-ai-temperature', 1)), 'presence_penalty' => floatval(sb_isset($settings, 'open-ai-presence-penalty', 0)), 'frequency_penalty' => floatval(sb_isset($settings, 'open-ai-frequency-penalty', 0))]);
    $error = sb_isset($response, 'error');
    $choices = sb_isset($response, 'choices');
    if ($choices) {
        $choices = explode("\n", trim($choices[0]['message']['content']));
        for ($i = 0; $i < count($choices); $i++) {
            $expression = trim($choices[$i]);
            if (in_array(substr($expression, 0, 2), [($i + 1) . '.', ($i + 1) . ')'])) {
                $expression = trim(substr($expression, 2));
            }
            if (substr($expression, 0, 1) === '.') {
                $expression = trim(substr($expression, 1));
            }
            $choices[$i] = $expression;
        }
        return $choices;
    } else if ($error) {
        return sb_error($error['type'], 'sb_open_ai_user_expressions', $error['message']);
    }
    return $response;
}

function sb_open_ai_user_expressions_intents() {
    $intents = sb_dialogflow_get_intents();
    $response = 0;
    $history = sb_get_external_setting('open-ai-intents-history', []);
    for ($i = 0; $i < count($intents); $i++) {
        $intent_name = substr($intents[$i]['name'], strripos($intents[$i]['name'], '/') + 1);
        if (in_array(sb_isset($intents[$i], 'action'), ['input.unknown', 'input.welcome']) || in_array($intent_name, $history)) {
            continue;
        }
        $messages = [];
        $training_phrases = $intents[$i]['trainingPhrases'];
        for ($j = 0; $j < count($training_phrases); $j++) {
            $parts = $training_phrases[$j]['parts'];
            $message = '';
            for ($y = 0; $y < count($parts); $y++) {
                $message .= $parts[$y]['text'];
            }
            array_push($messages, strtolower($message));
        }
        $count = count($messages) > 5 ? 5 : count($messages);
        $user_expressions_final = [];
        for ($j = 0; $j < $count; $j++) {
            if (strlen($messages[$j]) > 5) {
                $user_expressions = sb_open_ai_user_expressions($messages[$j]);
                for ($y = 0; $y < count($user_expressions); $y++) {
                    $expression = $user_expressions[$y];
                    if (!in_array(strtolower($expression), $messages) && strlen($expression) > 4)
                        array_push($user_expressions_final, $expression);
                }
            }
        }
        if (count($user_expressions_final)) {
            if (sb_dialogflow_update_intent($intents[$i], $user_expressions_final) === true) {
                array_push($history, $intent_name);
                sb_save_external_setting('open-ai-intents-history', $history);
            } else
                $response++;
        }
    }
    return $response === 0 ? true : $response;
}

function sb_open_ai_smart_reply($message, $conversation_id) {
    $response = sb_open_ai_message($message, false, sb_open_ai_get_gpt_model(), $conversation_id, ['smart_reply' => true]);
    $suggestions = [];
    if (sb_is_error($response)) {
        return $response;
    }
    if (isset($response[1]) && sb_isset($response[1], 'error')) {
        return sb_error('openai-error', 'sb_open_ai_smart_reply', $response, true);
    }
    for ($i = 0; $i < count($response); $i++) {
        if ($response[$i] && !is_bool($response[$i])) {
            $suggestion = is_string($response[$i]) ? $response[$i] : sb_isset(sb_isset($response[$i], 'message'), 'content');
            if (!in_array($suggestion, $suggestions) && strlen($suggestion) > 2 && !str_contains($suggestion, 'flow_') && !str_contains($suggestion, '[action')) {
                array_push($suggestions, $suggestion);
            }
        }
    }
    return ['suggestions' => $suggestions];
}

function sb_open_ai_spelling_correction($message) {
    if (strlen($message) < 2) {
        return $message;
    }
    $message_original = $message;
    $skip = [];
    $text_formatting = [];
    $regexes = [['/`[\S\s]*?`/', 0], ['/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', PREG_PATTERN_ORDER]];
    $index = 0;
    for ($i = 0; $i < count($regexes); $i++) {
        preg_match_all($regexes[$i][0], $message, $skip_sub, $regexes[$i][1]);
        $skip_sub = $skip_sub[0];
        for ($j = 0; $j < count($skip_sub); $j++) {
            $message = str_replace($skip_sub[$j], '{{' . $index . '}}', $message);
            array_push($skip, $skip_sub[$j]);
            $index++;
        }
    }
    if ($message == '{{0}}') {
        return $message_original;
    }
    $regexes = ['/\*(.*?)\*/', '/__(.*?)__/', '/~(.*?)~/', '/```(.*?)```/', '/`(.*?)`/'];
    for ($i = 0; $i < count($regexes); $i++) {
        $values = [];
        if (preg_match_all($regexes[$i], $message, $values)) {
            for ($j = 0; $j < count($values[0]); $j++) {
                $message = str_replace($values[0][$j], $values[1][$j], $message);
                array_push($text_formatting, [$values[0][$j], $values[1][$j]]);
            }
        }
    }
    $shortcode = sb_get_shortcode($message);
    if (!empty($shortcode)) {
        $message = str_replace($shortcode[0]['shortcode'], 'shortcode', $message);
    }
    if ($message && $message != 'shortcode') {
        $model = sb_open_ai_get_gpt_model();
        $response = sb_open_ai_curl('chat/completions', ['model' => $model, 'messages' => [['role' => 'user', 'content' => 'Fix spelling mistakes of the following text, return only the corrected version, or the original if none, never add comments, and do not remove text markdown: "' . $message . '"']]]);
        if (sb_is_error($response)) {
            return $response;
        }
        $error = sb_isset($response, 'error');
        if ($response && isset($response['choices']) && count($response['choices'])) {
            $response = $response['choices'][0]['message']['content'];
            $response = sb_open_ai_is_valid($response) && strlen($response) > (strlen($message) * 0.5) ? $response : $message;
            if (count($skip) != substr_count($response, '{{')) {
                return $message_original;
            }
            for ($i = 0; $i < count($skip); $i++) {
                $response = str_replace('{{' . $i . '}}', $skip[$i], $response);
            }
            for ($i = 0; $i < count($text_formatting); $i++) {
                $response = str_replace($text_formatting[$i][1], $text_formatting[$i][0], $response);
            }
            $response = sb_open_ai_text_formatting($response);
            return empty($shortcode) ? $response : str_replace('shortcode', $shortcode[0]['shortcode'], $response);
        } else if ($error) {
            sb_error($error['type'], 'sb_open_ai_spelling_correction', $error['message'], true);
        }
    }
    return $message_original;
}

function sb_open_ai_text_formatting($message) {
    if (empty($message)) {
        return $message;
    }
    $message = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function ($matches) {
        return $matches[2] . '#sb-' . str_replace(' ', '--', $matches[1]);
    }, str_replace(['**', '- *'], ['*', "\n*"], $message));
    $message = preg_replace('/mailto:([^#]+)#sb-.*?/', '', $message);
    $message = str_replace(['```html', '```php', '```css', '```js', '```javascript', '```c++', '```sql', '```plaintext', '```nginx', '```bash'], '```', $message);
    $is_inner_2 = false;
    if (str_contains($message, '[chips') && str_contains($message, 'message="')) {
        $message = mb_substr($message, mb_strpos($message, '[chips'));
        $message = mb_substr($message, 0, mb_strpos($message, ']') + 1);
    }
    if (strpos($message, '- ') || (str_contains($message, '1. ') && str_contains($message, '2. ')) || (strpos($message, '### ') || strpos($message, '#### '))) {
        $rows = preg_split("/\r\n|\n|\r/", $message);
        $message = '';
        $is_list = false;
        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $row_previous = false;
            for ($j = $i - 1; $j > -1; $j--) {
                if (!empty($rows[$j])) {
                    $row_previous = $rows[$j];
                    break;
                }
            }
            if (!empty(trim($row))) {
                for ($j = 1; $j < 30; $j++) {
                    $row = str_replace('     ' . $j . '. ', '  - ', $row);
                }
                $row = str_replace(['     - ', '    - ', '   - '], '  - ', $row);
                $is_line = strpos($row, '- ') === 0 || strpos($row, ' - ') === 0;
                $is_inner = strpos($row, '  - ') === 0;
                $is_inner_2 = !$is_inner ? $is_line && $is_list && ($is_inner_2 || (in_array(strpos($row_previous, '. '), [1, 2, 3]) && strpos($row_previous, ':'))) : false;
                if ($is_line || $is_inner || $is_inner_2 || (in_array(strpos($row, '. '), [1, 2]) && is_numeric(substr($row, 0, 1)))) {
                    $row = trim(str_replace([',', '"', ':', '[', ']'], ['\,', '\'', '\:', '', ''], substr($row, $is_inner ? 4 : 2)));
                    if ($row[0] == '.') {
                        $row = substr($row, 1);
                    }
                    if ($is_inner || $is_inner_2) {
                        $row = '- ' . $row;
                    }
                    if (!$is_list) {
                        $message .= '[list' . ($is_line ? '' : ' numeric="true"') . ' values="' . $row . PHP_EOL;
                        $is_list = true;
                        $is_inner_2 = false;
                    } else {
                        $message .= ',' . $row . PHP_EOL;
                    }
                    $row_next = isset($rows[$i + 1]) ? trim($rows[$i + 1]) : false;
                    if ((!$is_list || strpos($row, '*:')) && $row_next && $row_next[0] != '-' && !(is_numeric($row_next[0]) && $row_next[1] == '.')) {
                        $rows[$i + 1] = '  - ' . $row_next;
                    }
                } else {
                    $is_inner_2 = false;
                    if (strpos($row, '### ') === 0 || strpos($row, '#### ') === 0) {
                        $row = '*' . trim(str_replace('#', '', str_replace('*', '', $row))) . '*' . (empty($rows[$i + 1]) ? '' : PHP_EOL);
                    }
                    if ($is_list && strpos($row, '   ') !== 0) {
                        $message = $message . '"]' . (preg_match('/^\n/', $row) === 1 ? '' : PHP_EOL) . $row . PHP_EOL;
                        $is_list = false;
                    } else {
                        $message .= ($is_list ? str_replace([',', '"', ':', '[', ']'], ['\,', '\'', '\:', '', ''], $row) : $row) . PHP_EOL;
                    }
                }
                $row_previous = $row;
            } else if (!$is_list) {
                $message .= PHP_EOL . PHP_EOL;
            }
        }
        if ($is_list) {
            $message = $message . '"]';
        }
    }
    $rows = preg_split("/\r\n|\n|\r/", $message);
    $message = '';
    $is_open = false;
    $is_open_2 = false;
    for ($i = 0; $i < count($rows); $i++) {
        if (str_contains($rows[$i], '```') && $i) {
            if (!$is_open) {
                if (!empty($rows[$i - 1])) {
                    $rows[$i - 1] .= PHP_EOL;
                } else if ($i > 1 && empty($rows[$i - 2])) {
                    $rows[$i - 2] = '{S}';
                }
            } else {
                if (empty($rows[$i + 1])) {
                    $rows[$i + 1] = '{S}';
                    if (isset($rows[$i + 2]) && empty($rows[$i + 2])) {
                        $rows[$i + 2] = '{S}';
                    }
                }
            }
            $is_open = !$is_open;
        } else {
            if (!$is_open_2 && sb_is_rich_message($rows[$i])) {
                if (!empty($rows[$i - 1])) {
                    $rows[$i - 1] .= PHP_EOL;
                } else if ($i > 1 && empty($rows[$i - 2])) {
                    $rows[$i - 2] = '{S}';
                }
                $is_open_2 = true;
            }
            if ($is_open_2 && strpos($rows[$i], ']')) {
                if (empty($rows[$i + 1])) {
                    $rows[$i + 1] = '{S}';
                    if (isset($rows[$i + 2]) && empty($rows[$i + 2])) {
                        $rows[$i + 2] = '{S}';
                    }
                }
                $is_open_2 = false;
            }
        }
    }
    for ($i = 0; $i < count($rows); $i++) {
        if (strpos($rows[$i], '{S}') === false) {
            $message .= $rows[$i] . PHP_EOL;
        }
    }
    $message = str_replace('```,', '```' . PHP_EOL . ',', $message);
    while (in_array(mb_substr($message, 0, 1), ["\n", "\r", '\\n', '\\', ',', ':', '?', '!', '"', '”', '\''])) {
        $message = mb_substr($message, 1);
    }
    while (in_array(mb_substr($message, -1), ["\n", "\r", '\\n', '\\', ',', ':', '”', '\''])) {
        $message = mb_substr($message, 0, -1);
    }
    while (strpos($message, "\n ")) {
        $message = str_replace("\n ", "\n", $message);
    }
    if (!strpos(mb_substr($message, 0, -5), '"')) {
        while (mb_substr($message, -1) == '"') {
            $message = mb_substr($message, 0, -1);
        }
    }
    while (mb_substr($message, -1) == '"' && substr_count($message, '"') % 2 != 0) {
        $message = mb_substr($message, 0, -1);
    }
    if (mb_substr($message, 0, 2) == 'n ') {
        $message = mb_substr($message, 2);
    }
    if (preg_match('/(\|[^\r\n]+\|)(?:\r?\n\|[-:|]+)((?:\r?\n\|[^\r\n]+\|)+)/s', $message, $matches)) {
        $table = $matches[0];
        $lines = explode("\n", $table);
        $header = [];
        $values = [];
        foreach ($lines as $index => $line) {
            $columns = array_map('trim', explode('|', trim($line, '|')));
            if ($index == 0) {
                $header = $columns;
            } elseif ($index > 1 && !empty($columns)) {
                $values[] = implode(':', $columns);
            }
        }
        $header = implode(', ', $header);
        $values = implode(',', $values);
        $message = trim(str_replace($table, PHP_EOL . PHP_EOL . '[table header="' . $header . '" values="' . $values . '"]' . PHP_EOL . PHP_EOL, $message));
    }
    return trim(str_replace(['The fixed text is:', '(with correct punctuation)', 'Fix: ', 'Fixed: ', 'Corrected text:', 'A:', 'Answer: ', 'Question:', 'Fixed text:'], '', $message));
}

function sb_open_ai_is_valid($message) {
    return $message ? ($message == 'I don\'t know.' || $message == 'I don\'t know' || substr($message, -9) == 'don\'t know' || substr($message, -10) == 'don\'t know.' ? false : preg_match('/(Non lo so.|Bilmiyorum.|не знаю|Tôi không biết.|我不知道。|Jeg vet ikke.|Nie wiem.|Nu știu.|ne vem|nuk e di.|не знам.|Abdi henteu terang.|jag vet inte.|ฉันไม่รู้.|hindi ko alam.|Я не знаю.|ja neviem.|わからない。|არ ვიცი.|모르겠습니다.|aš nežinau.|не знам.|Би мэдэхгүй.|saya tak tahu.|ကျွန်တော်မသိပါ။|Ik weet het niet.|Je ne sais pas.|չգիտեմ։|لا أعرف.|аз не знам|Não sei.|Nevím.|Jeg ved det ikke.|Δεν ξέρω.|No sé.|ma ei tea.|من نمی دانم.|En tiedä.|אני לא יודע.|मुझें नहीं पता।|ne znam|Nem tudom.|Aku tidak tahu.|Ég veit það ekki.|Ich weiß nicht.|I can\'t help|I can\'t assist with that|provide real-time|Sorry, as an AI model|don\'t have access to real-time|don\'t have real-time|can\'t access the internet or real-time|provide real-time information|access to real-time|don\'t have access to real-time|not included in the context|sb-human-takeover|provide more context|provide a valid text|What was that|I didn\'t get that|I don\'t understand|no text provided|provide the text|I cannot provide|I don\'t have access|I don\'t have any|As a language model|I do not have the capability|I do not have access|modelo de lenguaje de IA|no tengo acceso|no tinc accés|En tant qu\'IA|je n\'ai pas d\'accès|en tant qu\'intelligence artificielle|je n\'ai pas accès|programme d\'IA|স্মার্ট AI কম্পিউটার প্রোগ্রাম|আমি একটি AI|আমি জানি না|我無法回答未來的活動|AI 語言模型|我無法提供|作為AI|我無法得知|作為一名AI|我無法預測|作为AI|我没有未来预测的功能|作為一個AI|我無法預測未來|作为一个AI|我无法预测|我不具备预测|我作为一个人工智能|Как виртуальный помощник|я не могу предоставить|как AI-ассистента|Как ИИ|Как искусственный интеллект|я не имею доступа|я не могу ответить|я не могу предсказать|como um modelo de linguagem|eu não tenho informações|sou um assistente de linguagem|Não tenho acesso|modelo de idioma de AI|não é capaz de fornecer|não tenho a capacidade|como modelo de linguagem de IA|como uma AI|não tenho um|como modelo de linguagem de inteligência artificial|como modelo de linguagem AI|não sou capaz|poiché sono un modello linguistico|non posso fornire informazioni|in quanto intelligenza artificiale|non ho la capacità|non sono in grado|non ho la possibilità|non posso dare|non posso fare previsioni|non posso predire|in quanto sono un\'Intelligenza Artificiale|Come assistente digitale|come assistente virtuale|Si një AI|nuk mund të parashikoj|Si inteligjencë artificiale|nuk kam informacion|Nuk mund të jap parashikime|nuk mund të parashikoj|لا يمكنني توفير|نموذجًا لغة|لا يمكنني التنبؤ|AI भाषा मॉडल हूँ|मैं एक AI|मुझे इसकी जानकारी नहीं है|मैं आपको बता नहीं सकती|AI सहायक|मेरे पास भविष्य के बारे में कोई जानकारी नहीं है|का पता नहीं है|не мога да|Като AI|не разполагам с|нямам достъп|ne mogu pratiti|Nisam u mogućnosti|nisam sposoban|ne mogu prikazivati|ne mogu ti dati|ne mogu pružiti|nemam pristup|nemam sposobnosti|nemam trenutne informacije|nemam sposobnost|ne mogu s preciznošću|nemůžu předpovídat|nemohu s jistotou|Jako AI|nemohu předpovídat|nemohu s jistotou znát|Jako umělá inteligence|nemám informace|nemohu predikovat|Jako NLP AI|nemohu předvídat|nedokážu předvídat|nemám schopnost|som AI|som en AI|har jeg ikke adgang|Jeg kan desværre ikke besvare|jeg ikke har adgang|kan jeg ikke give|jeg har ikke|har jeg ikke mulighed|Jeg er en AI og har ikke|har jeg ikke evnen|Jeg kan desværre ikke hjælpe med|jeg kan ikke svare|Som sprog AI|jeg ikke i stand)/i', $message) !== 1) : false;
}

function sb_open_ai_upload($path, $post_fields = []) {
    return sb_curl('https://api.openai.com/v1/files', array_merge(['file' => new CurlFile($path, 'application/json')], $post_fields), ['Content-Type: multipart/form-data', 'Authorization: Bearer ' . sb_open_ai_key()], 'UPLOAD', 30);
}

function sb_open_ai_file_training($url) {
    $response = sb_open_ai_source_file_to_paragraphs($url);
    sb_file_delete($url);
    return is_array($response) ? sb_open_ai_embeddings_generate($response, $url) : $response;
}

function sb_open_ai_url_training($url) {
    $response = sb_open_ai_html_to_paragraphs($url);
    $embedding_urls = sb_open_ai_get_training_source_names();
    if ($response[1] != 200) {
        return [false, 'http-error-' . $response[1], 'The URL ' . $url . ' returned a ' . $response[1] . ' error: ' . PHP_EOL . strip_tags(preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "", $response[0]))];
    }
    if (in_array(rtrim($url, '/'), $embedding_urls) || in_array($url . '/', $embedding_urls)) {
        return [true];
    }
    if (empty($response[0])) {
        return [false, 'The page at ' . $url . ' has not content or is not accessible.'];
    }
    return is_array($response) ? sb_open_ai_embeddings_generate($response[0], $url) : $response;
}

function sb_open_ai_qea_training($questions_answers, $language = false, $reset = false, $update_index = false) {
    $db_embeddings = $reset ? [] : sb_get_external_setting('embedding-texts', []);
    $db_embeddings_questions = $reset ? [] : array_column($db_embeddings, 0);
    $questions_answers_to_save = $reset ? [] : $db_embeddings;
    $embeddings = [];
    $set_data_null_keys = ['transcript', 'transcript_email', 'human_takeover', 'archive_conversation'];
    for ($i = 0; $i < count($db_embeddings); $i++) {
        $db_embeddings[$i] = [$db_embeddings[$i][0], $db_embeddings[$i][1], sb_isset($db_embeddings[$i], 6)];
    }

    // Update an existing question and answer
    if ($update_index !== false && $update_index !== '') {
        $db_embeddings[$update_index] = [$questions_answers[0], $questions_answers[1], sb_isset($questions_answers, 6)];
        $questions_answers_to_save[$update_index] = $questions_answers;
        $questions_answers = [];
    }

    // Update embeddings and questions
    for ($i = 0; $i < count($questions_answers); $i++) {
        $questions = [];
        if (!empty($questions_answers[$i][0])) {
            if (isset($questions_answers[$i][5]) && !is_array($questions_answers[$i][5])) {
                $questions_answers[$i][5] = [];
            }
            if (count(sb_isset($questions_answers[$i], 6, [])) == 1 && !$questions_answers[$i][6][0][1] && !in_array($questions_answers[$i][6][0][0], $set_data_null_keys)) {
                $questions_answers[$i][6] = [];
            }
            for ($j = 0; $j < count($questions_answers[$i][0]); $j++) {
                if (!in_array($questions_answers[$i][0][$j], $db_embeddings_questions)) {
                    $questions_answers[$i][1] = str_replace('..', '.', $questions_answers[$i][1], $questions_answers[$i][1]);
                    array_push($questions, $questions_answers[$i][0][$j]);
                }
            }
            array_push($questions_answers_to_save, $questions_answers[$i]);
            array_push($db_embeddings, [$questions, $questions_answers[$i][1], $language, sb_isset($questions_answers[$i], 6)]);
        }
    }
    for ($i = 0; $i < count($db_embeddings); $i++) {
        for ($j = 0; $j < count($db_embeddings[$i][0]); $j++) {
            $extra = [];
            if (!empty($db_embeddings[$i][3])) {
                foreach ($db_embeddings[$i][3] as $value) {
                    if ($value[1] || in_array($value[0], $set_data_null_keys)) {
                        $extra['set_data'][$value[0]] = $value[1];
                    }
                }
            }
            $question = trim($db_embeddings[$i][0][$j]);
            array_push($embeddings, [[$question, $db_embeddings[$i][1]], sb_isset($db_embeddings[$i], 2, ''), 'qea', $extra ? $extra : false]);
        }
    }

    // Delete embeddings of deleted or updated questions
    $files_names = sb_isset(sb_get_external_setting('embedding-sources'), 'sb-database', []);
    for ($i = 0; $i < count($files_names); $i++) {
        $file_path = sb_open_ai_embeddings_get_file($files_names[$i]);
        if (file_exists($file_path)) {
            $embeddings_file = json_decode(file_get_contents($file_path), true);
            $embeddings_file_final = [];
            $is_updated = false;
            for ($j = 0; $j < count($embeddings_file); $j++) {
                $is_deleted = true;
                for ($y = 0; $y < count($embeddings); $y++) {
                    if (trim($embeddings_file[$j]['text']) == trim(is_string($embeddings[$y][0]) ? $embeddings[$y][0] : $embeddings[$y][0][0]) && json_encode(sb_isset($embeddings_file[$j], 'extra')) == json_encode(sb_isset($embeddings[$y], 3))) {
                        $is_deleted = false;
                        break;
                    }
                }
                if (!$is_deleted) {
                    array_push($embeddings_file_final, $embeddings_file[$j]);
                } else {
                    $is_updated = true;
                }
            }
            if ($is_updated) {
                if (count($embeddings_file_final)) {
                    sb_file($file_path, json_encode($embeddings_file_final, JSON_UNESCAPED_UNICODE));
                } else {
                    sb_file_delete($file_path);
                }
            }
        }
    }
    sb_save_external_setting('embedding-texts', $questions_answers_to_save);
    $response = sb_open_ai_embeddings_generate($embeddings, 'sb-database');
    return $response[0] ? true : $response;
}

function sb_open_ai_articles_training() {
    $paragraphs = [];
    $articles = sb_get_articles(false, false, true, false, 'all');
    for ($i = 0; $i < count($articles); $i++) {
        array_push($paragraphs, [strip_tags($articles[$i]['title'] . ' ' . $articles[$i]['content']), sb_isset($articles[$i], 'language'), 'article-' . $articles[$i]['id']]);
    }
    return count($paragraphs) ? sb_open_ai_embeddings_generate($paragraphs, 'sb-articles') : true;
}

function sb_open_ai_conversations_training() {
    $last_check = explode('|', sb_get_external_setting('open-ai-embeddings-conversations', '2017-01-01 01:00:00|0'));
    $last_check_unix = strtotime($last_check[0]);
    $count_conversations = $last_check[1];
    $conversations = sb_db_get('SELECT conversation_id, creation_time FROM sb_messages WHERE creation_time > "' . $last_check[0] . '" GROUP BY conversation_id', false);
    $paragraphs = [];
    if ($conversations) {
        $bot_id = sb_get_bot_ID();
        $agent_ids = sb_get_agents_ids();
        $language = sb_open_ai_embeddings_language();
        if (empty($language)) {
            $language = sb_get_user_language($agent_ids[0]);
        } else {
            $language = $language[0];
        }
        for ($i = 0; $i < count($conversations); $i++) {
            $conversation_id = $conversations[$i]['conversation_id'];
            $messages = sb_db_get('SELECT user_id, message, payload FROM sb_messages WHERE conversation_id = ' . $conversation_id . ' AND message != "" AND creation_time > "' . $last_check[0] . '" ORDER BY id ASC', false);
            $messages_final = [];
            $count = count($messages);
            for ($j = 0; $j < $count; $j++) {
                $user_id = $messages[$j]['user_id'];
                $is_agent = in_array($user_id, $agent_ids);
                if (strlen($messages[$j]['message']) < 3 || !strpos($messages[$j]['message'], ' ') || ($user_id == $bot_id) || (!$is_agent && $j < ($count - 2) && $messages[$j + 1]['user_id'] == $bot_id && !in_array($messages[$j + 2]['user_id'], $agent_ids)) && (!isset($messages[$j + 3]) || !strpos($messages[$j + 3]['payload'], 'sb-human-takeover'))) {
                    continue;
                }
                array_push($messages_final, [$is_agent, $messages[$j]]);
            }
            for ($j = count($messages_final) - 1; $j > -1; $j--) {
                if (!$messages_final[$j][0]) {
                    $messages_final[$j] = false;
                } else {
                    break;
                }
            }
            $question = '';
            $answer = '';
            for ($j = 0; $j < count($messages_final); $j++) {
                if ($messages_final[$j]) {
                    $is_agent = $messages_final[$j][0];
                    $message = strip_tags(sb_google_get_message_translation($messages_final[$j][1], $language)['message']);
                    if ($is_agent) {
                        $answer .= (!$answer || sb_is_string_ends($answer) ? '' : '.') . ' ' . $message;
                    } else {
                        $question .= (!$question || sb_is_string_ends($question) ? '' : '.') . ' ' . $message;
                    }
                    if ($answer && $question && $is_agent && (empty($messages_final[$j + 1]) || !$messages_final[$j + 1][0])) {
                        array_push($paragraphs, [[trim($question), trim($answer)], '', 'conversation-' . $conversation_id]);
                        $answer = '';
                        $question = '';
                    }
                }
            }
            if (strtotime($conversations[$i]['creation_time']) > $last_check_unix) {
                $count_conversations++;
            }
        }
        $response = sb_open_ai_embeddings_generate($paragraphs, 'sb-conversations');
        if ($response) {
            sb_save_external_setting('open-ai-embeddings-conversations', sb_gmt_now() . '|' . $count_conversations);
        }
    }
    return $paragraphs;
}

function sb_open_ai_embeddings_delete($sources_to_delete) {
    $is_all = $sources_to_delete == 'all';
    $is_all_website = $sources_to_delete == 'all-website';
    $is_all_conversations = $sources_to_delete == 'all-conversations';
    if (empty($sources_to_delete) || (!$is_all && !$is_all_website && !$is_all_conversations && !is_array($sources_to_delete))) {
        return false;
    }
    $embedding_sources_new = [];
    $embedding_sources = sb_get_external_setting('embedding-sources', []);
    $deleted = 0;
    $count_sources_to_delete = is_array($sources_to_delete) ? count($sources_to_delete) : 0;
    foreach ($embedding_sources as $key => $value) {
        if ($is_all || ($is_all_website && !in_array($key, ['sb-conversations', 'sb-articles', 'sb-database']) && !sb_open_ai_is_file($key)) || ($is_all_conversations && $key == 'sb-conversations') || (!$is_all_website && !$is_all_conversations && in_array($key, $sources_to_delete))) {
            for ($i = 0; $i < count($value); $i++) {
                $file_name = sb_open_ai_embeddings_get_file($value[$i]);
                if (file_exists($file_name)) {
                    if (unlink($file_name)) {
                        $deleted++;
                    }
                }
            }
        } else {
            for ($i = 0; $i < $count_sources_to_delete; $i++) {
                $index = array_search($sources_to_delete[$i], $value);
                if ($index !== false) {
                    $file_name = sb_open_ai_embeddings_get_file($sources_to_delete[$i]);
                    if (file_exists($file_name)) {
                        if (unlink($file_name)) {
                            $deleted++;
                            array_splice($value, $index, 1);
                            break;
                        }
                    }
                }
            }
            $embedding_sources_new[$key] = $value;
        }
    }
    sb_delete_external_setting('embeddings-language');
    sb_save_external_setting('embedding-sources', $embedding_sources_new);
    if ($is_all || $is_all_conversations) {
        sb_save_external_setting('open-ai-embeddings-conversations', '');
    }
    if ($is_all) {
        $files = scandir(sb_open_ai_embeddings_get_path());
        for ($i = 0; $i < count($files); $i++) {
            if ($files[$i] != '.' && $files[$i] != '..') {
                unlink(sb_open_ai_embeddings_get_path() . $files[$i]);
            }
        }
        sb_save_external_setting('embedding-texts', []);
        return true;
    }
    return $is_all_website || $is_all_conversations || $deleted >= $count_sources_to_delete;
}

function sb_open_ai_embeddings_generate($paragraphs_or_string, $save_source = false) {
    if (is_string($paragraphs_or_string)) {
        if (mb_substr(trim($paragraphs_or_string), 0, 1) == '[') {
            $paragraphs_or_string_ = json_decode($paragraphs_or_string, true);
            if (!empty($paragraphs_or_string_)) {
                $paragraphs_or_string == $paragraphs_or_string_;
            } else {
                $paragraphs_or_string = [[$paragraphs_or_string, false]];
            }
        } else {
            $paragraphs_or_string = [[$paragraphs_or_string, false]];
        }
    }
    if (!sb_cloud_membership_has_credits('open-ai')) {
        return sb_error('no-credits', 'sb_open_ai_embeddings_generate');
    }
    if (isset($paragraphs_or_string[0][0]) && is_string($paragraphs_or_string[0][0])) {
        $paragraphs_or_string = sb_open_ai_embeddings_split_paragraphs($paragraphs_or_string, -1, [])[0];
    }
    $paragraphs_or_string_final = $paragraphs_or_string;
    $chars_limit = false;
    $chars_count = 0;
    $answers = [];
    $answers_final = [];
    if ($save_source) {
        $paragraphs_or_string_final = [];
        $path = sb_open_ai_embeddings_get_path();
        $embeddings = sb_open_ai_embeddings_get();
        $embedding_texts = [];
        if (sb_is_cloud()) {
            require_once(SB_CLOUD_PATH . '/account/functions.php');
            $chars_limit = cloud_embeddings_chars_limit();
        }
        for ($i = 0; $i < count($embeddings); $i++) {
            $embedding = $embeddings[$i];
            $embedding_file = json_decode(file_get_contents($path . $embedding), true);
            $texts = array_column($embedding_file, 'text');
            if ($chars_limit) {
                $chars_count += strlen(implode(array_column($embedding_file, 'answer')));
            }
            for ($j = 0; $j < count($texts); $j++) {
                $texts[$j] = [$texts[$j], $embedding, sb_isset($embedding_file[$j], 'answer')];
            }
            $embedding_texts = array_merge($embedding_texts, $texts);
        }

        // Remove duplicates and adjust paragraphs
        for ($i = 0; $i < count($paragraphs_or_string); $i++) {
            if (is_string($paragraphs_or_string[$i])) {
                $paragraphs_or_string[$i] = [$paragraphs_or_string[$i], false];
            }
            if (is_array($paragraphs_or_string[$i][0])) {
                array_push($answers, trim($paragraphs_or_string[$i][0][1]));
                $paragraphs_or_string[$i][0] = $paragraphs_or_string[$i][0][0];
            } else {
                array_push($answers, '');
            }
            if (empty(trim($paragraphs_or_string[$i][0]))) {
                continue;
            }
            if (isset($paragraphs_or_string[$i][2]) && $paragraphs_or_string[$i][2] != 'qea' && strpos($paragraphs_or_string[$i][2], 'article-') === false && strpos($paragraphs_or_string[$i][2], 'conversation-') === false && strpos($paragraphs_or_string[$i][2], 'flow-') === false && !in_array(pathinfo($paragraphs_or_string[$i][2], PATHINFO_EXTENSION), ['csv', 'pdf', 'txt']) && !strpos($paragraphs_or_string[$i][0], 'More details at ')) {
                $paragraphs_or_string[$i][0] .= (sb_is_string_ends($paragraphs_or_string[$i][0]) ? '' : '.') . ' More details at ' . $paragraphs_or_string[$i][2] . '.';
            }
            $paragraphs_or_string[$i][0] = trim($paragraphs_or_string[$i][0]);
            $text = $paragraphs_or_string[$i][0];
            $duplicate = false;
            for ($j = 0; $j < count($paragraphs_or_string); $j++) {
                if ($text == trim(is_array($paragraphs_or_string[$j][0]) ? $paragraphs_or_string[$j][0][0] : $paragraphs_or_string[$j][0]) && $j != $i) {
                    $duplicate = true;
                    break;
                }
            }
            if (!$duplicate) {
                for ($j = 0; $j < count($embedding_texts); $j++) {
                    if ($embedding_texts[$j][0] == $text) {
                        $duplicate = true;
                        if (!empty($embedding_texts[$j][2]) && !empty($answers[$i]) && $embedding_texts[$j][2] != $answers[$i]) {
                            $embedding_file = json_decode(file_get_contents($path . $embedding_texts[$j][1]), true);
                            $is_updated = false;
                            for ($k = 0; $k < count($embedding_file); $k++) {
                                if ($embedding_file[$k]['answer'] == $embedding_texts[$j][2]) {
                                    $embedding_file[$k]['answer'] = $answers[$i];
                                    $is_updated = true;
                                    break;
                                }
                            }
                            if ($is_updated) {
                                sb_file($path . $embedding_texts[$j][1], json_encode($embedding_file, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
                            }
                        }
                        break;
                    }
                }
                if (!$duplicate) {
                    array_push($paragraphs_or_string_final, $paragraphs_or_string[$i]);
                    array_push($answers_final, $answers[$i]);
                }
            }
        }
        if ($chars_limit) {
            for ($i = 0; $i < count($paragraphs_or_string_final); $i++) {
                $chars_count += strlen($paragraphs_or_string_final[$i][0]) + strlen($answers_final[$i]);
            }
            for ($i = 0; $i < count($embedding_texts); $i++) {
                $chars_count += strlen($embedding_texts[$i][0]);
            }
        }
    }
    if ($chars_limit && $chars_count > $chars_limit) {
        return [false, 'chars-limit-exceeded', $chars_limit, $chars_count];
    }
    $data_all = [];
    $paragraphs = sb_open_ai_embeddings_split_paragraphs($paragraphs_or_string_final, 0, $answers_final);
    $index = $paragraphs[1];
    $answers = $paragraphs[2];
    $paragraphs = $paragraphs[0];
    $errors = [];

    // Generate embeddings
    while ($paragraphs) {
        $paragraphs_texts = [];
        $paragraphs_languages = [];
        for ($i = 0; $i < count($paragraphs); $i++) {
            array_push($paragraphs_texts, is_string($paragraphs[$i]) ? $paragraphs[$i] : $paragraphs[$i][0]);
            array_push($paragraphs_languages, is_string($paragraphs[$i]) ? '' : (is_string($paragraphs[$i][1]) ? $paragraphs[$i][1] : ''));
        }
        $response = sb_open_ai_curl('embeddings', ['model' => 'text-embedding-3-small', 'input' => $paragraphs_texts]);
        if (sb_is_error($response)) {
            return $response;
        }
        $data = sb_isset($response, 'data');
        if ($data) {
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['text'] = trim($paragraphs_texts[$i]);
                $data[$i]['language'] = $paragraphs_languages[$i];
                if (isset($paragraphs[$i][2])) {
                    $data[$i]['source'] = $paragraphs[$i][2];
                }
                if (isset($paragraphs[$i][3])) {
                    $attachments = sb_isset($paragraphs[$i][3], 'attachments', []);
                    for ($x = 0; $x < count($attachments); $x++) {
                        $paragraphs[$i][3]['attachments'][$x] = [basename($attachments[$x]), $attachments[$x]];
                    }
                    $data[$i]['extra'] = $paragraphs[$i][3];
                }
                if (isset($answers[$i])) {
                    $data[$i]['answer'] = $answers[$i];
                }
            }
            $data_all = array_merge($data_all, $data);
        } else {
            array_push($errors, $response);
        }
        $paragraphs = sb_open_ai_embeddings_split_paragraphs($paragraphs_or_string_final, $index, $answers_final);
        if (empty($paragraphs[0])) {
            $paragraphs = false;
        } else {
            $index = $paragraphs[1];
            $answers = $paragraphs[2];
            $paragraphs = $paragraphs[0];
        }
    }

    // Save embedding files
    if ($save_source) {
        $len_total = 0;
        $embeddings_part = [];
        $count = count($data_all);
        $response = [];
        $embedding_sources = sb_get_external_setting('embedding-sources', []);
        for ($i = 0; $i < $count; $i++) {
            $len_total += strlen(json_encode($data_all[$i]));
            array_push($embeddings_part, $data_all[$i]);
            if ($len_total > 2000000 || $i == $count - 1) {
                $name = bin2hex(openssl_random_pseudo_bytes(10));
                array_push($response, sb_file(sb_open_ai_embeddings_get_file($name), json_encode($embeddings_part, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE)));
                $embeddings_part = [];
                $len_total = 0;
                if (isset($embedding_sources[$save_source])) {
                    if (!in_array($name, $embedding_sources[$save_source])) {
                        array_push($embedding_sources[$save_source], $name);
                    }
                } else {
                    $embedding_sources[$save_source] = [$name];
                }
            }
        }

        // Delete embeddings of deleted articles
        $count = count(sb_isset($embedding_sources, $save_source, []));
        if ($save_source == 'sb-articles' && $count) {
            $paragraphs_or_strings_text = array_column(sb_open_ai_embeddings_split_paragraphs($paragraphs_or_string, -1, [])[0], 0);
            for ($i = 0; $i < $count; $i++) {
                $file_name = sb_open_ai_embeddings_get_file($embedding_sources[$save_source][$i]);
                if (file_exists($file_name)) {
                    $article_embeddings = json_decode(file_get_contents($file_name), true);
                    $is_save = false;
                    for ($j = 0; $j < count($article_embeddings); $j++) {
                        if (!in_array($article_embeddings[$j]['text'], $paragraphs_or_strings_text)) {
                            array_splice($article_embeddings, $j, 1);
                            $j--;
                            $is_save = true;
                        }
                    }
                    if ($is_save) {
                        if (count($article_embeddings)) {
                            sb_file($file_name, json_encode($article_embeddings, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
                        } else {
                            unlink($file_name);
                            $embedding_sources[$save_source][$i] = false;
                        }
                    }
                }
            }
        }

        // Delete old embedding file references
        $embedding_sources_final = [];
        foreach ($embedding_sources as $key => $file_names) {
            $embedding_source_file_names = [];
            for ($i = 0; $i < count($file_names); $i++) {
                if (file_exists(sb_open_ai_embeddings_get_file($file_names[$i]))) {
                    array_push($embedding_source_file_names, $file_names[$i]);
                }
            }
            if (count($embedding_source_file_names)) {
                $embedding_sources_final[$key] = $embedding_source_file_names;
            }
        }
        sb_save_external_setting('embedding-sources', $embedding_sources_final);

        //  Miscellaneous
        if (!empty($paragraphs_languages)) {
            $language_code = strtolower(substr($paragraphs_languages[0], 0, 2));
            if ($language_code) {
                $embeddings_language = sb_open_ai_embeddings_language();
                if (!in_array($language_code, $embeddings_language)) {
                    array_push($embeddings_language, $language_code);
                    sb_save_external_setting('embeddings-language', $embeddings_language);
                }
            }
        }
        if (!file_exists($path . 'index.html')) {
            sb_file($path . 'index.html', 'Forbidden');
        }
        return empty($paragraphs_or_string_final) ? [true, []] : [$response, $errors];
    }
    return $data_all;
}

function sb_open_ai_embeddings_split_paragraphs($paragraphs, $last_index, $answers) {
    $response = [];
    $len_total = 0;
    $paragraphs_2 = [];
    $answers_2 = [];
    for ($i = 0; $i < count($paragraphs); $i++) {
        $len = strlen($paragraphs[$i][0]);
        if ($len > 8000) {
            $splits = mb_str_split($paragraphs[$i][0], 8000);
            for ($j = 0; $j < count($splits); $j++) {
                array_push($paragraphs_2, [$splits[$j], $paragraphs[$i][1]]);
                if (isset($answers[$i])) {
                    array_push($answers_2, $answers[$i]);
                }
            }
        } else {
            array_push($paragraphs_2, $paragraphs[$i]);
            if (isset($answers[$i])) {
                array_push($answers_2, $answers[$i]);
            }
        }
    }
    if ($last_index !== -1) {
        for ($i = $last_index; $i < count($paragraphs_2); $i++) {
            $len = strlen($paragraphs_2[$i][0]);
            if ($len_total + $len < 100000 || !$len_total) {
                array_push($response, $paragraphs_2[$i]);
                $len_total += $len;
                $last_index = $i;
            } else {
                break;
            }
        }
    } else {
        $response = $paragraphs_2;
    }
    return [$response, $last_index + 1, $answers_2];
}

function sb_open_ai_embeddings_compare($vector_1, $vector_2, $vector_1_text, $vector_2_text) {
    $dot_product = 0.0;
    $norm_query = 0.0;
    $norm_text = 0.0;
    for ($i = 0; $i < count($vector_1); $i++) {
        $dot_product += $vector_1[$i] * $vector_2[$i];
        $norm_query += $vector_1[$i] ** 2;
        $norm_text += $vector_2[$i] ** 2;
    }
    return min((($norm_query > 0 && $norm_text > 0) ? ($dot_product / (sqrt($norm_query) * sqrt($norm_text))) : 0.0) + (mb_stripos($vector_2_text, $vector_1_text, 0, 'UTF-8') !== false ? 0.2 : 0.0), 1.0);
}

function sb_open_ai_embeddings_message($user_prompt, $conversation_id, $language = false, $extra = false, $min_score = 0.2) {
    $user_prompt_embeddings = sb_open_ai_embeddings_generate($user_prompt);
    $conversation = $conversation_id ? sb_db_get('SELECT department, source FROM sb_conversations WHERE id = ' . sb_db_escape($conversation_id, true)) : false;
    $conversation_department = sb_isset($conversation, 'department');
    $conversation_source = sb_isset($conversation, 'source');
    if (sb_is_error($user_prompt_embeddings)) {
        return $user_prompt_embeddings;
    }
    if (!empty($user_prompt_embeddings) && isset($user_prompt_embeddings[0]['embedding'])) {
        $scores = [];
        $user_prompt_embeddings = $user_prompt_embeddings[0]['embedding'];
        $embeddings = sb_open_ai_embeddings_get(true);
        $path = sb_open_ai_embeddings_get_path();
        $embedding_languages = [];
        if ($language) {
            $language = strtolower($language);
        }
        for ($i = 0; $i < count($embeddings); $i++) {
            $embeddings_content = json_decode(file_get_contents(substr($embeddings[$i], 0, 12) == 'integration-' ? SB_PATH . '/apps/dialogflow/embeddings/' . substr($embeddings[$i], 12) : $path . $embeddings[$i]), true);
            for ($j = 0; $j < count($embeddings_content); $j++) {
                $embedding = $embeddings_content[$j];
                $embedding_extra = sb_isset($embedding, 'extra');
                $embedding_language = sb_isset($embedding, 'language');
                $embedding_department = sb_isset($embedding_extra, 'department');
                $embedding_source = sb_isset($embedding_extra, 'conversation_source');
                if (($embedding_department && $conversation_department != $embedding_department) || ($embedding_source && $embedding_source != $conversation_source && ($embedding_source != 'ch' || !empty($conversation_source)))) {
                    continue;
                }
                if ($embedding_language && is_string($embedding_language)) {
                    $embedding_language = substr($embedding_language, 0, 2);
                    if (!in_array($embedding_language, $embedding_languages)) {
                        array_push($embedding_languages, $embedding_language);
                    }
                }
                if (!$language || !$embedding_language || $embedding_language == $language || count(sb_open_ai_embeddings_language()) == 1) {
                    $score = !empty($user_prompt_embeddings) && !empty($embedding['embedding']) ? sb_open_ai_embeddings_compare($user_prompt_embeddings, $embedding['embedding'], $user_prompt, $embedding['text']) : 0;
                    if ($score > $min_score && (!$embedding_extra || empty($embedding_extra['conditions']) || sb_automations_validate($embedding_extra['conditions'], true))) {
                        array_push($scores, ['score' => $score, 'text' => $embedding['text'], 'answer' => sb_isset($embedding, 'answer'), 'source' => sb_isset($embedding, 'source'), 'extra' => sb_isset($embedding, 'extra')]);
                    }
                }
            }
        }
        $count = count($scores);
        if ($count) {
            usort($scores, function ($a, $b) {
                return $a['score'] <=> $b['score'];
            });
            if ($count > 10) {
                $scores = array_slice($scores, -10);
            }
            $count = count($scores);
            if ($count && $scores[$count - 1]['score'] > 0.9) {
                $scores = [$scores[$count - 1]];
                $count = 1;
            }
            $context = '';
            $model = sb_open_ai_get_gpt_model();
            $model_context_map = [
                'gpt-5-nano' => 100000,
                'gpt-5-mini' => 100000,
                'gpt-5' => 100000,
                'gpt-4.1-nano' => 500000,
                'gpt-4.1-mini' => 500000,
                'gpt-4.1' => 500000,
                'o1-mini' => 32000,
                'gpt-4' => 32000,
                'gpt-4o' => 32000,
                'gpt-4o-mini' => 32000,
                'gpt-4-turbo' => 32000,
                'o3-mini' => 50000,
                'o4-mini' => 50000,
                'o1' => 50000,
            ];
            $context_max_length = $model_context_map[$model] ?? 4000;
            for ($i = $count - 1; $i > -1; $i--) {
                if (mb_strlen($context) < $context_max_length) {
                    $answer = $scores[$i]['answer'];
                    $text_ = $scores[$i]['text'];
                    $text = (substr($text_, 0, 9) != 'Question:' ? 'Question: ' : '') . $text_ . ($answer ? (sb_is_string_ends($text_) ? '' : '.') . PHP_EOL . PHP_EOL . 'Answer: ' . $answer : '');
                    $context .= ($context ? PHP_EOL . PHP_EOL : '') . $text;
                }
            }
            $context = trim($context);
            if (mb_strlen($context) > $context_max_length) {
                $context = mb_substr($context, 0, $context_max_length);
            }
            if (sb_open_ai_is_playground()) {
                $GLOBALS['SB_OPEN_AI_PLAYGROUND_E'] = $scores;
            }
            $extra_ = ['embeddings' => true, 'user_id' => sb_isset($extra, 'user_id')];
            if (!empty($extra['smart_reply'])) {
                $extra_['smart_reply'] = true;
            }
            $response = sb_open_ai_message($user_prompt, false, false, sb_isset($extra, 'conversation_id'), $extra_, false, sb_isset($extra, 'attachments'), $context . ' ' . sb_isset($extra, 'context'));
            if (sb_is_error($response)) {
                return $response;
            }
            if ($response) {
                if (empty($response[0])) {
                    if (!empty($response[1])) {
                        sb_error('open-ai-error', 'sb_open_ai_message', sb_isset($response[1], 'error', $response[1]));
                    }
                } else {
                    $is_human_takeover = $response[1] == 'sb-human-takeover';
                    $response_text = sb_open_ai_text_formatting($response[1]);
                    if (sb_open_ai_is_valid($response[1]) || $is_human_takeover) {
                        if (!$is_human_takeover) {
                            $top_score = $scores[count($scores) - 1];
                            $embedding_extra = false;
                            if ($extra == 'translation') {
                                $message = sb_google_translate([$response_text], $language);
                                if (!empty($message)) {
                                    $response_text = $message[0];
                                }
                            }
                            if (sb_get_multi_setting('open-ai', 'open-ai-source-links') && (empty($response[3]) || $response[3] != 'skip-references')) {
                                $sources_string = '';
                                $index = 1;
                                $is_articles_home = sb_get_articles_page_url();
                                for ($i = 0; $i < $count; $i++) {
                                    $source = sb_isset($scores[$i], 'source');
                                    $is_article = $is_articles_home && strpos($source, 'article-') === 0;
                                    if ($source && strpos($sources_string, $source) === false && ($is_article || strpos($source, 'http') === 0) && strpos($response_text, $source) === false) {
                                        $sources_string .= ($is_article ? sb_get_article_url(substr($source, 8)) : $source) . '#sb-' . $index . ' | ';
                                        $index++;
                                    }
                                }
                                if ($sources_string && !strpos($sources_string, sb_('References') . ': ')) {
                                    $response_text .= PHP_EOL . PHP_EOL . sb_('References') . ': ' . substr($sources_string, 0, -3);
                                }
                            }
                            if ($top_score['score'] > 0.45 || strpos(sb_string_slug($top_score['text']), sb_string_slug(substr($response_text, 1, -1)))) {
                                $embedding_extra = sb_isset($top_score, 'extra');
                                $attachments = sb_isset($embedding_extra, 'attachments');
                                if ($attachments) {
                                    $response[6] = array_merge($response[6], $attachments);
                                }
                            }
                        }
                        return ['message' => str_replace('disabled="true"]', ']', $response_text), 'payload' => $response[5], 'payload_message' => $response[7], 'attachments' => $response[6], 'embedding_extra' => $is_human_takeover ? [] : $embedding_extra];
                    }
                }
            }
        } else if ($extra != 'translation' && $language && count($embedding_languages) && !in_array($language, $embedding_languages) && sb_get_multi_setting('google', 'google-multilingual-translation')) {
            $message = sb_google_translate([$user_prompt], $embedding_languages[0]);
            if (!empty($message)) {
                return sb_open_ai_embeddings_message($message[0], $conversation_id, $embedding_languages[0], 'translation');
            }
        }
    }
    return false;
}

function sb_open_ai_embeddings_language() {
    global $SB_EMBEDDINGS_LANGUAGES;
    if (!empty($SB_EMBEDDINGS_LANGUAGES)) {
        return $SB_EMBEDDINGS_LANGUAGES;
    }
    $embeddings_language = sb_get_external_setting('embeddings-language');
    if (!$embeddings_language || empty($embeddings_language[0]) || (is_string($embeddings_language) && strpos($embeddings_language, '[') === false)) { // Deprecated: remove || (is_string($embeddings_language) && strpos($embeddings_language, '[') === false)
        $embeddings = sb_open_ai_embeddings_get();
        $embeddings_language = [sb_get_multi_setting('open-ai', 'open-ai-training-data-language', 'en')];
        for ($i = 0; $i < count($embeddings); $i++) {
            $embeddings_single = json_decode(file_get_contents(sb_open_ai_embeddings_get_path() . $embeddings[$i]), true);
            for ($j = 0; $j < count($embeddings_single); $j++) {
                $language_code = sb_isset($embeddings_single[$j], 'language');
                if ($language_code && is_string($language_code)) {
                    $language_code = strtolower(substr($language_code, 0, 2));
                    if (!in_array($language_code, $embeddings_language)) {
                        array_push($embeddings_language, $language_code);
                    }
                }
            }
        }
        sb_save_external_setting('embeddings-language', $embeddings_language ? $embeddings_language : '-');
    } else if ($embeddings_language == '-') {
        $embeddings_language = '';
    }
    $SB_EMBEDDINGS_LANGUAGES = $embeddings_language;
    return $embeddings_language;
}

function sb_open_ai_embeddings_get($include_integrations = false) {
    $files = scandir(sb_open_ai_embeddings_get_path());
    $embeddings = [];
    for ($i = 0; $i < count($files); $i++) {
        $file = $files[$i];
        if (strpos($file, 'embeddings-') === 0) {
            array_push($embeddings, $files[$i]);
        }
    }
    if ($include_integrations) {
        $integrations = sb_integrations_get_active();
        foreach ($integrations as $integration) {
            array_push($embeddings, 'integration-' . $integration . '.json');
        }
    }
    return $embeddings;
}

function sb_open_ai_embeddings_get_path() {
    $name = 'SB_EMBEDDINGS_PATH';
    if (isset($GLOBALS[$name])) {
        return $GLOBALS[$name];
    }
    $path = sb_upload_path() . '/embeddings/';
    $cloud = sb_is_cloud() ? sb_cloud_account() : false;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    if ($cloud) {
        require_once(SB_CLOUD_PATH . '/account/functions.php');
        $path .= $cloud['user_id'] . '/';
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }
    $GLOBALS[$name] = $path;
    return $path;
}

function sb_open_ai_embeddings_get_information() {
    $information = ['limit' => false, 'files' => [0, 0], 'website' => [0, 0], 'qea' => [0, 0], 'flows' => [0, 0], 'articles' => [0, 0], 'conversations' => [0, 0]];
    $sources = sb_get_external_setting('embedding-sources', []);
    $path = sb_open_ai_embeddings_get_path();
    $total = 0;
    foreach ($sources as $key => $embeddings) {
        $key_info = $key == 'sb-database' ? 'qea' : ($key == 'sb-conversations' ? 'conversations' : ($key == 'sb-articles' ? 'articles' : ($key == 'sb-flows' ? 'flows' : (sb_open_ai_is_file($key) ? 'files' : 'website'))));
        for ($i = 0; $i < count($embeddings); $i++) {
            $text_len = strlen(implode('', array_column(sb_open_ai_embeddings_get_file($embeddings[$i], true), 'text')));
            $information[$key_info][0] += $text_len;
            $total += $text_len;
        }
        switch ($key_info) {
            case 'website':
            case 'files':
                $count = $information[$key_info][1] + 1;
                break;
            case 'conversations':
                $count = explode('|', sb_get_external_setting('open-ai-embeddings-conversations', '0|0')); // Deprecated
                $count = isset($count[1]) ? $count[1] : 0; // Deprecated. Replace with: $information[$key_info][1] = explode('|', sb_get_external_setting('open-ai-embeddings-conversations', '0|0'))[0]
                break;
            case 'articles':
                $count = count(sb_get_articles());
                break;
            case 'qea':
                $count = count(sb_get_external_setting('embedding-texts', []));
                break;
            case 'flows':
                $count = count(sb_flows_get());
                break;
        }
        $information[$key_info][1] = $count;
    }
    $information['total'] = $total;
    if (sb_is_cloud()) {
        require_once(SB_CLOUD_PATH . '/account/functions.php');
        $information['limit'] = cloud_embeddings_chars_limit();
    }
    return $information;
}

function sb_open_ai_embeddings_get_file($embedding_id, $return_content = false) {
    $path = sb_open_ai_embeddings_get_path() . 'embeddings-' . $embedding_id . '.json';
    return $return_content ? (file_exists($path) ? json_decode(file_get_contents($path), true) : []) : $path;
}

function sb_open_ai_embeddings_update_single($embedding_id, $index, $text) {
    $path = sb_open_ai_embeddings_get_path() . 'embeddings-' . $embedding_id . '.json';
    $embedding = sb_open_ai_embeddings_get_file($embedding_id, true);
    $chars_count = 0;
    $answer = false;
    if (is_array($text) && isset($text['answer'])) {
        $answer = $text['answer'];
        $text = $text['question'];
    }
    if ($text) {
        if (sb_is_cloud()) {
            require_once(SB_CLOUD_PATH . '/account/functions.php');
            $chars_limit = cloud_embeddings_chars_limit();
            if ($chars_limit) {
                $embeddings = sb_open_ai_embeddings_get();
                for ($i = 0; $i < count($embeddings); $i++) {
                    $embedding_file = json_decode(file_get_contents(sb_open_ai_embeddings_get_path() . $embeddings[$i]), true);
                    $chars_count += strlen(implode(array_column($embedding_file, 'text'))) + strlen(implode(array_column($embedding_file, 'answer')));
                }
                if ($chars_count > $chars_limit) {
                    return [false, 'chars-limit-exceeded', $chars_limit, $chars_count];
                }
            }
        }
        $response = sb_open_ai_curl('embeddings', ['model' => 'text-embedding-3-small', 'input' => [$text]]);
        $data = sb_isset($response, 'data');
        if (!empty($data)) {
            $embedding[$index]['text'] = $text;
            $embedding[$index]['embedding'] = $data[0]['embedding'];
            if ($answer) {
                $embedding[$index]['answer'] = $answer;
            }
            sb_file($path, json_encode($embedding, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
            return true;
        }
    } else {
        array_splice($embedding, $index, 1);
        if (empty($embedding)) {
            sb_open_ai_embeddings_delete([$embedding_id]);
        } else {
            sb_file($path, json_encode($embedding, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
        }
        return true;
    }
    return $response;
}

function sb_open_ai_embeddings_get_conversations() {
    $embeddings = sb_isset(sb_get_external_setting('embedding-sources'), 'sb-conversations', []);
    $response = [];
    for ($i = 0; $i < count($embeddings); $i++) {
        $embedding = sb_open_ai_embeddings_get_file($embeddings[$i], true);
        for ($y = 0; $y < count($embedding); $y++) {
            $id = $y . '-' . $embeddings[$i];
            array_push($response, ['question' => $embedding[$y]['text'], 'answer' => $embedding[$y]['answer'], 'id' => $id]);
        }
    }
    return $response;
}

function sb_open_ai_embeddings_save_conversations($qea) {
    $response = [];
    for ($i = 0; $i < count($qea); $i++) {
        $id = explode('-', $qea[$i]['id']);
        array_push($response, sb_open_ai_embeddings_update_single($id[1], $id[0], $qea[$i]));
    }
    return $response;
}

function sb_open_ai_source_file_to_paragraphs($url, $content = false) {
    $paragraphs = [];
    if (!$content) {
        $extension = sb_open_ai_is_file($url);
        if (!$extension) {
            sb_file_delete($url);
            return 'invalid-file-extension';
        }
        if (in_array($extension, ['pdf', 'json', 'csv'])) {
            $upload_url = sb_upload_path(true);
            $file = strpos($url, $upload_url) === 0 ? sb_upload_path() . str_replace($upload_url, '', $url) : sb_download_file($url, 'sb_open_ai_source_file.' . $extension, false, [], 0, true);
            switch ($extension) {
                case 'pdf':
                    $content = sb_pdf_to_text($file);
                    break;
                case 'json':
                    $content = sb_json_to_text($file);
                    break;
                case 'csv':
                    $content = sb_csv_to_text($file);
                    break;
            }
            sb_file_delete($file);
        } else {
            $content = trim(sb_get($url));
        }
    }
    if ($content) {
        $encoding = mb_detect_encoding($content);
        if (str_contains($encoding, 'UTF-16')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16');
        }
        $separator = ['።', '。', '။', '.', '।'];
        for ($i = 0; $i < count($separator); $i++) {
            if (strpos($content, $separator[$i])) {
                $separator = $separator[$i];
                break;
            }
        }
        $parts = is_string($separator) ? explode($separator . ' ', $content) : [$content];
        $paragraph = '';
        for ($i = 0; $i < count($parts); $i++) {
            $part = trim($parts[$i]);
            $length_1 = mb_strlen($paragraph);
            $length_2 = mb_strlen($parts[$i]);
            if (($length_1 + $length_2 < 2000) || $length_1 < 100 || $length_2 < 100) {
                $paragraph .= $part;
            } else {
                array_push($paragraphs, [$paragraph ? $paragraph . ' ' . $part : $part, '', sb_beautify_file_name(basename($url))]);
                $paragraph = '';
            }
        }
        if ($paragraph) {
            array_push($paragraphs, [$paragraph, '', sb_beautify_file_name(basename($url))]);
        }
    }
    return $paragraphs;
}

function sb_open_ai_get_gpt_model() {
    return sb_get_multi_setting('open-ai', 'open-ai-model', 'gpt-5-mini');
}

function sb_open_ai_audio_to_text($path_or_url, $audio_language = false, $user_id = false, $message_id = false, $conversation_id = false) {
    $is_delete = false;
    if (!sb_cloud_membership_has_credits('open-ai')) {
        return sb_error('no-credits', 'sb_open_ai_audio_to_text');
    }
    if (!$audio_language) {
        $audio_language = sb_get_user_language($user_id ? $user_id : sb_get_active_user_ID());
    }
    if (strpos($path_or_url, 'http') === 0) {
        $path_file = sb_upload_path(false, true) . '/' . basename($path_or_url);
        if (file_exists($path_file)) {
            $path_or_url = $path_file;
        } else {
            $is_delete = true;
            $path_or_url = sb_download_file($path_or_url, 'temp_open_ai_' . basename($path_or_url), false, [], 0, true);
        }
    }
    if (!$audio_language) {
        $audio_language = sb_get_user_language();
    }
    $response = sb_curl('https://api.openai.com/v1/audio/transcriptions', ['file' => new CURLFile($path_or_url), 'model' => 'whisper-1', 'language' => sb_isset(['br' => 'pt'], $audio_language, $audio_language)], ['Content-Type: multipart/form-data', 'Authorization: Bearer ' . sb_open_ai_key()], 'POST', 30);
    $message = sb_isset($response, 'text');
    if ($message) {
        if ($conversation_id || $message_id) {
            if (!$message_id) {
                $message_id = sb_isset(sb_db_get('SELECT id FROM sb_messages WHERE conversation_id = ' . sb_db_escape($conversation_id, true) . ' ORDER BY id DESC LIMIT 1'), 'id');
            }
            if ($message_id) {
                if (sb_get_multi_setting('open-ai', 'open-ai-speech-recognition')) {
                    sb_update_message($message_id, $message);
                } else {
                    sb_db_query('UPDATE sb_messages SET message = "' . sb_db_escape($message) . '" WHERE id = ' . $message_id);
                }
            }
        }
    } else {
        sb_error('open-ai-error', 'sb_open_ai_audio_to_text', $response);
    }
    if ($is_delete) {
        sb_file_delete($path_or_url);
    }
    return $message;
}

function sb_open_ai_key() {
    return sb_ai_is_manual_sync('open-ai') ? trim(sb_get_multi_setting('open-ai', 'open-ai-key')) : OPEN_AI_KEY;
}

function sb_open_ai_assistant($message, $conversation_id, $human_takeover_check = true) {
    $assistant_id = sb_get_multi_setting('open-ai', 'open-ai-assistant-id');
    $conversation = sb_db_get('SELECT extra_2, department FROM sb_conversations WHERE id = ' . sb_db_escape($conversation_id, true));
    $thread_id = sb_isset($conversation, 'extra_2');
    $department_id = sb_isset($conversation, 'department');
    if ($department_id) {
        $assistants = sb_get_setting('open-ai-assistants');
        if ($assistants && is_array($assistants)) {
            for ($i = 0; $i < count($assistants); $i++) {
                if ($assistants[$i]['open-ai-assistants-department-id'] == $department_id) {
                    $assistant_id = $assistants[$i]['open-ai-assistants-id'];
                    break;
                }
            }
        }
    }
    if (!$assistant_id) {
        return sb_error('open-ai-error', 'sb_open_ai_assistant', 'No assistant ID', true);
    }
    $header = ['Content-Type: application/json', 'OpenAI-Beta: assistants=v2', 'Authorization: Bearer ' . trim(sb_get_multi_setting('open-ai', 'open-ai-key'))];
    $url_part = 'https://api.openai.com/v1/';
    if ($thread_id) {
        $response = sb_curl($url_part . 'threads/' . $thread_id . '/messages', json_encode(['role' => 'user', 'content' => $message], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), $header);
    }
    $response = sb_curl($url_part . 'threads' . ($thread_id ? '/' . $thread_id : '') . '/runs', json_encode($thread_id ? ['assistant_id' => $assistant_id] : ['assistant_id' => $assistant_id, 'thread' => ['messages' => [['role' => 'user', 'content' => $message]]]], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), $header);
    $run_id = sb_isset($response, 'id');
    if ($run_id) {
        if (!$thread_id) {
            $thread_id = sb_isset($response, 'thread_id');
            sb_db_query('UPDATE sb_conversations SET extra_2 = "' . sb_db_escape($thread_id) . '" WHERE id = ' . sb_db_escape($conversation_id, true));
        }
        for ($i = 0; $i < 30; $i++) {
            sleep(1);
            $response = json_decode(sb_curl($url_part . 'threads/' . $thread_id . '/runs/' . $run_id, '', $header, 'GET'), true);
            $status = sb_isset($response, 'status');
            if ($status == 'completed') {
                $response = json_decode(sb_curl($url_part . 'threads/' . $thread_id . '/messages', '', $header, 'GET'), true);
                $message = isset($response['data']) ? $response['data'][0]['content'][0]['text']['value'] : '';
                if ($message) {
                    $message = preg_replace('/【[\s\S]+?】/', '', sb_open_ai_text_formatting($message));
                }
                return $message;
            } else if ($status == 'expired') {
                break;
            } else {
                $function_calling = sb_open_ai_tools_calling($response, false, $conversation_id);
                if ($function_calling) {
                    if ($human_takeover_check || $function_calling[0] != 'sb-human-takeover') {
                        sb_curl($url_part . 'threads/' . $thread_id . '/runs/' . $run_id . '/submit_tool_outputs', json_encode(['tool_outputs' => [['tool_call_id' => $function_calling[1], 'output' => $function_calling[2]]]], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), $header);
                    }
                    if ($function_calling[0] == 'sb-human-takeover') {
                        return $function_calling[0];
                    }
                }
            }
        }
    } else if (isset($response['error'])) {
        $error = sb_isset($response['error'], 'message');
        if (strpos($error, 'active run')) {
            $run_id = substr($error, strrpos($error, ' run_') + 1, -1);
            for ($i = 0; $i < 30; $i++) {
                sleep(2);
                if (sb_isset(json_decode(sb_curl($url_part . 'threads/' . $thread_id . '/runs/' . $run_id, '', $header, 'GET'), true), 'status') == 'completed') {
                    return sb_open_ai_assistant($message, $conversation_id, $human_takeover_check);
                }
            }
        }
    }
    return sb_error('open-ai-error', 'sb_open_ai_assistant', $response);
}

function sb_open_ai_data_scraping($conversation_id, $prompt_id) {
    if (!sb_cloud_membership_has_credits('open-ai')) {
        return sb_error('no-credits', 'sb_open_ai_audio_to_text');
    }
    $prompt = sb_open_ai_data_scraping_get_prompts()[$prompt_id];
    if ($prompt_id == 'summary') {
        $messages = sb_get_conversation(false, $conversation_id)['messages'];
        $text = 'Here is the conversation to summarize: ' . PHP_EOL;
        foreach ($messages as $message) {
            $text .= (sb_is_agent($message['user_type']) ? 'Agent' : 'User') . ': ' . $message['message'] . PHP_EOL;
        }
        $agent_language = sb_get_admin_language();
        $response = sb_open_ai_curl('chat/completions', ['model' => sb_open_ai_get_gpt_model(), 'messages' => [['role' => 'developer', 'content' => 'You are a helpful assistant that summarizes conversations between users and agents. Generate a summary of the conversation with the key user questions and agent answers. Only return the summary and nothing else. Do not add any other text. Do not ask questions.' . ($agent_language ? 'The summary language must be ' . sb_get_language_name_by_code($agent_language) . '.' : '')], ['role' => 'user', 'content' => $text]]]);
        $response = sb_isset(sb_isset(sb_isset($response, 'choices', [[]])[0], 'message'), 'content', '');
        $response = [true, $response, false, null, empty($response) ? ['unknow_answer' => true] : []];
    } else {
        $response = sb_open_ai_message($prompt[0] . ' from the user messages. Do not scrape anything else, return only the scraped information separated by breaklines, do not add text, separate different logins with one breakline, include links and IPs before login details. If the information is not included, write exactly "I don\'t know".', false, false, $conversation_id, 'scraping');
    }
    if (!$response || !$response[0]) {
        sb_error('open-ai-error', 'sb_open_ai_data_scrape', $response);
    } else if (empty($response[5]['unknow_answer'])) {
        $lines = preg_split("/\r\n|\n|\r/", $response[1]);
        $text = '';
        if (in_array('duplicate', $prompt[1])) {
            $lines = array_unique($lines);
        }
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i] . '';
            $lines[$i] = '';

            for ($j = 0; $j < count($prompt[1]); $j++) {
                $check = $prompt[1][$j];
                if (str_contains($line, $check) || ($check == 123 && is_numeric($line))) {
                    continue 2;
                }
            }
            $count = count($prompt[2]);
            if ($count) {
                $valid = false;
                for ($j = 0; $j < count($prompt[2]); $j++) {
                    $check = $prompt[2][$j];
                    if (str_contains($line, $check) || ($check == 123 && !is_numeric($line))) {
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    continue;
                }
            }
            $text .= trim($line) . PHP_EOL;
        }
        return $text;
    } else {
        $response = '';
    }
    return $response;
}

function sb_open_ai_data_scraping_get_prompts($type = false) {
    $prompts = ['login' => ['The login details are made up of a URL or IP address, a username or email, and a password. Scrape all login details', [], [], 'Login information'], 'links' => ['Scrape all links and URLs', ['@', 123], ['http', 'www'], 'Links and URLs'], 'contacts' => ['Scrape addresses, phone numbers and emails', ['http'], [], 'Contact information'], 'summary' => ['Generate a summary of the conversation', [], [], 'Summary']];
    if ($type == 'name') {
        foreach ($prompts as $key => $value) {
            $prompts[$key] = sb_($value[3]);
        }
    }
    return $prompts;
}

function sb_open_ai_tools_calling($response, $query_tools = false, $conversation_id = false) {
    $function = false;
    $function_name = false;
    $id = false;
    $payload = [];
    if (sb_isset($response, 'status') == 'requires_action') {
        $response = sb_isset($response, 'required_action');
        if ($response) {
            $response = sb_isset(sb_isset($response, 'submit_tool_outputs'), 'tool_calls');
            if (!empty($response)) {
                $function = sb_isset($response[0], 'function');
                $id = $response[0]['id'];
            }
        }
    } else if (!empty($response['choices'])) {
        $response = sb_isset(sb_isset($response['choices'][0], 'message'), 'tool_calls');
        if (!empty($response)) {
            $function = sb_isset($response[count($response) - 1], 'function');
        }
    }
    if ($function) {
        $function_name = $function['name'];
        if ($function_name == 'sb-human-takeover') {
            return [$function_name, $id, '', $payload];
        }
        if (($function_name == 'sb-shopify' || $function_name == 'sb-shopify-single') && sb_is_cloud()) {
            require_once(SB_CLOUD_PATH . '/account/functions.php');
            $arguments = json_decode($function['arguments'], true);
            if ($arguments) {
                return shopify_ai_function_calling($function_name, $id, $arguments, $query_tools);
            }
        }
        if (defined('SB_WOOCOMMERCE') && !sb_get_setting('wc-disable-bot-integration')) {
            $arguments = json_decode($function['arguments'], true);
            if (sb_woocommerce_open_ai_check_function_name($function_name)) {
                if ($arguments) {
                    return sb_woocommerce_open_ai_function_calling($function_name, $id, $arguments, $query_tools);
                }
            } else if (sb_woocommerce_open_ai_check_function_name($function_name, 2)) {
                return sb_woocommerce_open_ai_function_calling_2($function_name, $id, $arguments);
            }
        }
        $qea = sb_get_external_setting('embedding-texts', []);
        for ($i = 0; $i < count($qea); $i++) {
            $qea[$i][0] = is_array($qea[$i][0]) ? $qea[$i][0] : [$qea[$i][0]]; // Deprecated;
            for ($j = 0; $j < count($qea[$i][0]); $j++) {
                if (substr(sb_string_slug($qea[$i][0][$j], 'slug', true), 0, 20) . '-' . $i == $function_name) {
                    $function['user_id'] = sb_get_active_user_ID();
                    $function['conversation_id'] = $conversation_id;
                    $response = sb_curl($qea[$i][2], json_encode($function, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $qea[$i][4] ? explode(',', $qea[$i][4]) : [], $qea[$i][3], 30);
                    return [$function_name, $id, $response, $payload];
                }
            }
        }
        $flows_tools_block = sb_flows_get_by_string($function_name);
        if ($flows_tools_block) {
            if (empty($flows_tools_block['integration'])) {
                $function['sb'] = ['user' => sb_get_active_user(), 'conversation_id' => $conversation_id, 'user_language' => sb_get_user_language(sb_get_active_user_ID())];
                $headers = sb_isset(sb_isset(sb_isset($flows_tools_block, 'headers'), 0), 0) ? array_map(function ($items) {
                    return implode(':', $items);
                }, $flows_tools_block['headers']) : [];
                $response = sb_curl($flows_tools_block['url'], json_encode($function, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $headers, $flows_tools_block['method'], 30);
            } else {
                $response = sb_integrations_execute($flows_tools_block, $function, $conversation_id);
            }
            if (is_array($response)) {
                $response['open_ai_internal_instruction'] = 'Do not provide suggestions to the user. Only give short and concise responses.';
                if (!empty($response['success'])) {
                    $payload['tools_end'] = $function_name;
                }
            }
            return [$function_name, $id, $response, $payload];
        }
    }
    return false;
}

function sb_open_ai_troubleshoot($debug = false) {
    $message = 'Hello world!';
    $conversation_id = false;
    $response = false;
    if ($debug) {
        $_GET['debug'] = true;
    } else {
        $response = sb_curl('https://api.openai.com/v1/embeddings', '{"model":"text-embedding-3-small","input":["Hello world!"]}', ['Content-Type: application/json', 'Authorization: Bearer ' . sb_open_ai_key()], 'POST');
        $error = sb_isset($response, 'error');
        if ($error) {
            return sb_isset($error, 'message', $error);
        }
    }
    $response = sb_open_ai_rewrite_message($message);
    if ($response && !sb_is_error($response) && $response[0]) {
        $conversation = sb_open_ai_dummy_data();
        $conversation_id = $conversation['details']['id'];
        sb_send_message(sb_get_active_user_ID(), $conversation_id, $message);
        $response = sb_open_ai_message($message, false, false, $conversation_id);
        if ($response && !sb_is_error($response) && $response[0]) {
            if (sb_get_multi_setting('open-ai', 'open-ai-mode') == 'assistant') {
                $response = sb_open_ai_assistant($message, $conversation_id, false);
                if (sb_is_error($response)) {
                    $response = $response->error;
                    return isset($response['response']) && isset($response['response']['error']) ? $response['response']['error']['message'] : $response['message'];
                }
            }
            if ($response && !sb_is_error($response) && $response[0]) {
                sb_open_ai_dummy_data('delete');
                if ($debug) {
                    return true;
                } else {
                    return sb_open_ai_troubleshoot(true);
                }
            }
        }
    }
    if ($conversation_id) {
        sb_open_ai_dummy_data('delete');
    }
    if (sb_is_error($response)) {
        if ($response->code() == 'no-credits') {
            return str_replace('{R}', '<a href="' . (defined('SB_CLOUD_DOCS') ? SB_CLOUD_DOCS : '') . '#cloud-credits" target="_blank" class="sb-link-text">' . sb_('here') . '</a>', sb_('Credits are required to use some features in automatic sync mode. If you don\'t want to buy credits, switch to manual sync mode and use your own API key. For more details click {R}.'));
        }
        $response = $response->response() ? $response->response() : ($response->message() ? $response->message() : $response->code());
        return is_string($response) ? $response : sb_isset($response, 'message', $response);
    }
    return isset($response[1]) && isset($response[1]['error']) ? $response[1]['error']['message'] : $response;
}

function sb_open_ai_html_to_paragraphs($url) {
    error_reporting(E_ERROR | E_PARSE);
    libxml_use_internal_errors(true);
    $response = sb_curl($url, '', [], 'GET-SC');
    if ($response[1] == 200) {
        $paragraphs = [];
        $response = $response[0];
        $html_start = strpos($response, '<html');
        $html_end = strpos($response, '>', $html_start);
        $html_tag_content = substr($response, $html_start, $html_end - $html_start);
        $language = '';
        if (str_contains($html_tag_content, 'lang=')) {
            $lang_start = strpos($html_tag_content, 'lang=') + 6;
            $language = strtolower(substr($html_tag_content, $lang_start, strpos($html_tag_content, '"', $lang_start) - $lang_start));
        }
        $body_start = strpos($response, '<body');
        $body_content = substr($response, $body_start, strpos($response, '</body>') - $body_start);
        $body_content = str_replace('><', '> <', $body_content);
        $body_content = str_replace(['<br>', '<br />'], "\n", $body_content);
        $body_content = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $body_content);
        $body_content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $body_content);
        $duplicate_check_strings = [];
        if ($body_content) {
            preg_match_all('/<a\s[^>]*href=["\'](#.*?)["\']/i', $body_content, $matches);
            foreach ($matches[1] as $match) {
                $match = substr($match, 1);
                $pos = strpos($body_content, 'id="' . $match . '"');
                if (!$pos) {
                    $pos = strpos($body_content, 'id=\'' . $match . '\'');
                }
                if ($pos) {
                    $body_content = substr_replace($body_content, '<p>More details at ' . $url . '#' . $match . '.</p>', strpos($body_content, '>', $pos) + 1, 0);
                }
            }
            preg_match_all('/<a\b[^>]*>.*?<\/a>/is', $body_content, $matches);
            $matches = array_unique($matches[0]);
            foreach ($matches as $match) {
                $match_ = preg_replace('/<img\b[^>]*>/i', '', str_replace('</a>', '', preg_replace('/\s+/', ' ', str_replace(["\r\n", "\r", "\n"], '', $match))));
                $match_ = substr($match_, strpos($match_, '>') + 1);
                if (empty(trim($match_))) {
                    $body_content = str_replace($match, '', $body_content);
                }
            }
            $dom = new DOMDocument();
            @$dom->loadHTML('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>' . $body_content);
            $xpath = new DOMXPath($dom);
            $uls = $xpath->query('//ul');
            foreach ($uls as $ul) {
                try {
                    if (isset($ul->nodeValue)) {
                        $code = '';
                        $lis = $xpath->query('.//li', $ul);
                        $index = 0;
                        foreach ($lis as $li) {
                            $sub_uls = isset($li->nodeValue) ? $xpath->query('.//ul', $li) : [];
                            foreach ($sub_uls as $sub_ul) {
                                try {
                                    $li->removeChild($sub_ul);
                                } catch (Exception $e) {
                                }
                            }
                            $text = isset($li->textContent) ? trim($li->textContent) : '';
                            if (str_word_count($text) > 1 && strlen($text) > 10) {
                                $code .= '###B###' . ($index + 1) . '. ' . $text;
                            }
                            $index++;
                        }
                        $ul->nodeValue = '';
                        if ($code) {
                            $ul->appendChild($dom->createTextNode($code));
                        }
                    }
                } catch (Exception $e) {
                }
            }
            $ps = $xpath->query('//p');
            foreach ($ps as $p) {
                try {
                    if (isset($el->nodeValue)) {
                        $p->nodeValue .= '###B###';
                    }
                } catch (Throwable $e) {
                }
            }
            $spans_labels = $xpath->query('//span | //label');
            foreach ($spans_labels as $el) {
                try {
                    if (isset($el->nodeValue)) {
                        $el->nodeValue = ' ' . $el->nodeValue . ' ';
                    }
                } catch (Exception $e) {
                }
            }
            $as = $xpath->query('//a');
            foreach ($as as $a) {
                try {
                    $href = trim($a->getAttribute('href'));
                    if ($href && $href != '#' && strpos($href, 'javascript:') === false && strpos($href, 'data:') === false) {
                        if (strpos($href, 'http') !== 0 || strpos($href, 'www') !== 0) {
                            $base_url_parts = parse_url($url);
                            $base_protocol = isset($base_url_parts['scheme']) ? $base_url_parts['scheme'] . ':' : '';
                            $base_host = isset($base_url_parts['host']) ? '//' . $base_url_parts['host'] : '';
                            $base_path = isset($base_url_parts['path']) ? rtrim(dirname($base_url_parts['path']), '/') : '';
                            $continue = true;
                            if (substr($href, 0, 2) == "//") {
                                $href = $base_protocol . $href;
                                $continue = false;
                            }
                            if ($href[0] == '/') {
                                $href = $base_protocol . $base_host . $href;
                                $continue = false;
                            }
                            if (preg_match('/^\s*$/', $href)) {
                                $href = '';
                                $continue = false;
                            }
                            if ($continue) {
                                if (substr($href, 0, 2) == './') {
                                    $href = '.' . $href;
                                    $base_full_path = $base_protocol . $base_host . $base_path;
                                    $href = rtrim($base_full_path, '/') . '/' . ltrim($href, '/');
                                } else if (strpos($href, 'http') === false && strpos($href, 'www') === false && strpos($href, 'tel:') === false && strpos($href, 'sms:') === false && strpos($href, 'file:') === false && strpos($href, 'ftp:') === false && strpos($href, 'whatsapp:') === false && strpos($href, 'mailto:') === false) {
                                    $href = $base_protocol . $base_host . '/' . $href;
                                }
                            }
                            while (preg_match('/\/\.\.\//', $href)) {
                                $href = preg_replace('/[^\/]+\/+\.\.\//', '', $href);
                            }
                            $href = str_replace(['."', "/./", '"', "'", '<', '>'], ['', '/', '%22', '%27', '%3C', '%3E'], $href);
                        }
                        if (isset($a->nodeValue)) {
                            $href_text = trim($a->nodeValue);
                            $a->nodeValue = $href_text ? ' [' . $href_text . ': ' . $href . ']' : $href;
                        }
                    }
                } catch (Exception $e) {
                }
            }
            $h2s = $xpath->query('//h1/following-sibling::h2[1]');
            foreach ($h2s as $h2) {
                try {
                    $prev = $h2->previousSibling;
                    if ($prev && isset($prev->nodeValue) && $prev->nodeName === 'h1') {
                        $prev->nodeValue .= ' ' . $h2->textContent;
                        $h2->parentNode->removeChild($h2);
                    }
                } catch (Exception $e) {
                }
            }
            $headers = $xpath->query('//h1 | //h2');
            foreach ($headers as $header) {
                try {
                    if (isset($header->nodeValue)) {
                        $header->nodeValue = '###P###' . $header->nodeValue . '. \n';
                    }
                } catch (Exception $e) {
                }
            }
            $all_headers = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
            foreach ($all_headers as $header) {
                try {
                    if (isset($header->nodeValue)) {
                        $header->nodeValue = '###P###' . $header->nodeValue . '. ';
                    }
                } catch (Exception $e) {
                }
            }
            $text_content = $dom->textContent;
            $list = explode('###P###', $text_content);
            $list_2 = [];
            foreach ($list as $text) {
                try {
                    $text = preg_replace('/\s\s+/', ' ', $text);
                    if (str_word_count($text) > 5 && strlen($text) > 20) {
                        $list_2[] = str_replace('###B###', ' \n ', $text);
                    }
                } catch (Exception $e) {
                }
            }
            $list = [];
            for ($i = 0; $i < count($list_2); $i++) {
                $text = trim($list_2[$i]);
                while (strlen($text) < 300 && count($list_2) > $i + 1) {
                    $text .= (sb_is_string_ends($text) ? '' : ' . ') . trim($list_2[$i + 1]);
                    $i++;
                }
                $text = str_replace([' .', '. \n.'], '.', $text);
                if (substr($text, 0, 10) == '----------') {
                    $text = substr($text, 80);
                }
                while (substr($text, 0, 4) == 'http') {
                    $text = substr($text, strpos($text, ' ') + 1);
                }
                $text = str_replace('. \n.', '.', $text);
                if (strlen($text) > 3500) {
                    $texts = explode('\n', $text);
                    $text = '';
                    foreach ($texts as $j => $t) {
                        if (strlen($text) + strlen($t) < 3500 || $j == count($texts) - 1) {
                            $text .= $t;
                        } else {
                            $temp = sb_open_ai_html_to_paragraphs_2($text, $language, $url);
                            array_push($paragraphs, $temp[0]);
                            $duplicate_check_strings = array_merge($duplicate_check_strings, $temp[1]);
                            $text = '';
                        }
                    }
                } else {
                    $temp = sb_open_ai_html_to_paragraphs_2($text, $language, $url);
                    array_push($paragraphs, $temp[0]);
                    $duplicate_check_strings = array_merge($duplicate_check_strings, $temp[1]);
                }
            }
        } else {
            $decoded = json_decode($response, true);
            if (!empty($decoded) && json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return [sb_open_ai_source_file_to_paragraphs($url, sb_json_to_text($decoded)), 200];
            }
        }

        // Check for duplicated content
        $embedding_sources = sb_get_external_setting('embedding-sources', []);
        $count = count($duplicate_check_strings);
        $count_paragraphs = count($paragraphs);
        foreach ($embedding_sources as $key => $value) {
            if (!sb_open_ai_is_file($key) && sb_isset(parse_url($url), 'host') == sb_isset(parse_url($key), 'host')) {
                for ($i = 0; $i < count($value); $i++) {
                    $text = implode('', array_column(sb_open_ai_embeddings_get_file($value[$i], true), 'text'));
                    for ($y = 0; $y < $count; $y++) {
                        $duplicate_check_string = trim($duplicate_check_strings[$y]);
                        if (str_contains($text, $duplicate_check_string)) {
                            for ($j = 0; $j < $count_paragraphs; $j++) {
                                $paragraphs[$j][0] = trim(str_replace($duplicate_check_string, '', $paragraphs[$j][0]));
                            }
                        }
                    }
                }
            }
        }
        for ($i = 0; $i < count($paragraphs); $i++) {
            $paragraphs[$i][0] = ltrim($paragraphs[$i][0], '. ');
        }
        return [$paragraphs, 200];
    }
    return $response;
}

function sb_open_ai_html_to_paragraphs_2($text, $language, $url) {
    if (substr($text, 0, 10) == '----------') {
        $text = substr($text, 80);
    }
    return [[preg_replace('!\s+!', ' ', str_replace(['\\n', '\n'], ' ', $text)), $language, $url], explode("\n", str_replace(['\\n', '\n'], "\n", $text))];
}

function sb_open_ai_trainig_server_side() {
    ignore_user_abort(true);
    set_time_limit(900);
    $embedding_keys = sb_open_ai_get_training_source_names();
    sb_open_ai_embeddings_delete('all-website');
    for ($i = 0; $i < count($embedding_keys); $i++) {
        $key = $embedding_keys[$i];
        if (sb_open_ai_is_file($key) || in_array($key, ['sb-conversations', 'sb-articles', 'sb-database'])) {
            continue;
        }
        $urls = strpos($key, '.xml') ? sb_get_sitemap_urls($key) : [$key];
        for ($j = 0; $j < count($urls); $j++) {
            $paragraphs = sb_isset(sb_open_ai_html_to_paragraphs($urls[$j]), 0);
            if (count($paragraphs)) {
                $response = sb_open_ai_embeddings_generate($paragraphs, $urls[$j]);
                if ($response[1] == 'chars-limit-exceeded') {
                    die($response[1]);
                }
            }
        }
    }
    sb_open_ai_articles_training();
    return true;
}

function sb_open_ai_get_training_source_names() {
    return array_keys(sb_get_external_setting('embedding-sources', []));
}

function sb_open_ai_playground_message($message, $user_type = 'user', $is_clear = false) {
    global $SB_OPEN_AI_PLAYGROUND;
    global $SB_LOGIN;
    $SB_OPEN_AI_PLAYGROUND = [];
    $active_login = $SB_LOGIN;
    if ($is_clear) {
        sb_open_ai_dummy_data('delete');
    }
    $conversation = sb_open_ai_dummy_data();
    $conversation_id = $conversation['details']['id'];
    sb_send_message($user_type == 'user' ? sb_get_active_user_ID() : sb_get_bot_ID(), $conversation_id, $message);
    sb_open_ai_message($message, false, false, $conversation_id);
    $SB_LOGIN = $active_login;
    return ['conversation' => sb_get_conversation(false, $conversation_id), 'playground' => $SB_OPEN_AI_PLAYGROUND];
}

function sb_open_ai_is_playground() {
    global $SB_OPEN_AI_PLAYGROUND;
    return $SB_OPEN_AI_PLAYGROUND !== null;
}

function sb_open_ai_dummy_data($action = false, $extra = false) {
    global $SB_LOGIN;
    if ($action == 'delete') {
        return sb_db_query('DELETE FROM sb_users WHERE (first_name = "open-ai-temp-user" OR token = "open-ai-temp-user") AND last_name = ' . sb_get_active_user_ID()) && sb_db_query('DELETE FROM sb_conversations WHERE title = "open-ai-temp-conversation" AND extra = ' . sb_get_active_user_ID());
    }
    if ($action == 'delete-message') {
        return empty($extra) ? false : sb_db_query('DELETE FROM sb_messages WHERE id = ' . sb_db_escape($extra, true));
    }
    $conversation_id = sb_isset(sb_db_get('SELECT id FROM sb_conversations WHERE title = "open-ai-temp-conversation" LIMIT 1'), 'id');
    if (empty($conversation_id)) {
        $user_id = sb_db_query('INSERT INTO sb_users(first_name, last_name, user_type, token, creation_time) VALUES ("open-ai-temp-user", "' . sb_get_active_user_ID() . '", "lead", "open-ai-temp-user-' . sb_get_active_user_ID() . '", NOW())', true);
        $conversation_id = sb_db_query('INSERT INTO sb_conversations(user_id, title, extra, creation_time) VALUES (' . $user_id . ', "open-ai-temp-conversation", ' . sb_get_active_user_ID() . ', NOW())', true);
    }
    $SB_LOGIN = sb_db_get('SELECT * FROM sb_users WHERE id = (SELECT user_id FROM sb_conversations WHERE id = ' . $conversation_id . ' LIMIT 1)');
    return sb_get_conversation(false, $conversation_id);
}

function sb_open_ai_is_file($url) {
    $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
    return in_array($extension, ['pdf', 'txt', 'csv', 'json']) ? $extension : false;
}

function sb_open_ai_execute_set_data($user_data) {
    if (sb_is_agent()) {
        return false;
    }
    $full_name = sb_isset($user_data, 'full_name');
    if ($full_name) {
        $full_name = sb_split_name($full_name);
        $user_data['first_name'] = $full_name[0];
        $user_data['last_name'] = $full_name[1];
    }
    return sb_update_user(sb_get_active_user_ID(), $user_data, $user_data, true, true);
}

function sb_open_ai_execute_actions($data, $conversation_id) {
    $client_side_payload = [];
    $attachments_response = [];
    for ($i = 0; $i < count($data); $i++) {
        $action = $data[$i];
        switch ($action[0]) {
            case 'tags':
                sb_tags_update($conversation_id, explode(strpos($action[1], '|') ? '|' : ',', $action[1]), true);
                break;
            case 'agent':
                sb_update_conversation_agent($conversation_id, $action[1]);
                break;
            case 'department':
                sb_update_conversation_department($conversation_id, $action[1]);
                break;
            case 'send_email_agents':
            case 'send_email':
            case 'transcript_email':
            case 'archive_conversation':
                $is_archive_conversation = $action[0] == 'archive_conversation';
                $is_transcript = $action[0] == 'transcript_email' || $is_archive_conversation;
                if ($is_archive_conversation) {
                    sb_update_conversation_status($conversation_id, 3);
                    if (sb_get_multi_setting('close-message', 'close-active')) {
                        sb_close_message($conversation_id, sb_get_bot_ID());
                    }
                    $client_side_payload['event'] = 'conversation-status-update-3';
                }
                if ((!$is_archive_conversation || sb_get_multi_setting('close-message', 'close-transcript')) && (sb_isset(sb_get_active_user(), 'email') || $action[0] == 'send_email_agents')) {
                    $attachments = [];
                    if ($is_transcript) {
                        $transcript = sb_transcript($conversation_id);
                        $attachments = [[$transcript, $transcript]];
                    }
                    $bot = sb_db_get('SELECT profile_image, first_name FROM sb_users WHERE user_type = "bot" LIMIT 1');
                    sb_email_create($action[0] == 'send_email_agents' ? 'agents' : sb_get_active_user_ID(), $bot['first_name'], $bot['profile_image'], $is_transcript ? sb_get_multi_setting('transcript', 'transcript-message', '') : str_replace('|', ',', $action[1]), $attachments, true, $conversation_id);
                }
                break;
            case 'transcript':
                $transcript = sb_transcript($conversation_id);
                array_push($attachments_response, [$transcript, $transcript]);
                break;
            case 'redirect':
                $client_side_payload['redirect'] = 'https://' . str_replace('https://', '', $action[1]);
                break;
            case 'open_article':
                $client_side_payload['open_article'] = $action[1];
                break;
            case 'human_takeover':
                sb_dialogflow_human_takeover($conversation_id);
                break;
        }
    }
    return ['client_side_payload' => $client_side_payload, 'attachments' => $attachments_response];
}

function sb_open_ai_send_fallback_message($conversation_id) {
    sb_send_message(sb_get_bot_ID(), $conversation_id, sb_t(sb_get_multi_setting('open-ai', 'open-ai-fallback-message', 'Sorry, I didn\'t get that. Can you rephrase?'), sb_get_user_language(sb_get_active_user_ID())));
}
function sb_open_ai_tools_query($name, $description, $properties = false) {
    $response = [];
    $properties_query = [];
    $properties_required = [];
    if (!empty($properties)) {
        foreach ($properties as $value) {
            if (!empty($value[0])) {
                $property_slug = sb_string_slug($value[0], 'slug', true);
                $properties_query[$property_slug] = ['type' => 'string', 'description' => $value[1]];
                if ($value[2]) {
                    $properties_query[$property_slug]['enum'] = explode(',', $value[2]);
                }
                array_push($properties_required, $property_slug);
            }
        }
    }
    array_push($response, ['type' => 'function', 'function' => [
        'name' => $name,
        'description' => $description,
        'strict' => true,
        'parameters' => [
            'type' => 'object',
            'properties' => count($properties_query) ? $properties_query : (object) [],
            'required' => $properties_required,
            'additionalProperties' => false
        ]
    ]]);
    return $response;
}

function sb_open_ai_get_max_tokens($model) {
    $max_tokens_list = [
        'gpt-5-nano' => 400000,
        'gpt-5-mini' => 400000,
        'gpt-5' => 400000,
        'gpt-4.1-nano' => 1047576,
        'gpt-4.1-mini' => 1047576,
        'gpt-4.1' => 1047576,
        'gpt-4' => 8192,
        'gpt-4-turbo' => 128000,
        'gpt-4o' => 128000,
        'gpt-4o-mini' => 128000,
        'o1' => 200000,
        'o1-mini' => 128000,
        'o3-mini' => 200000,
        'o4-mini' => 200000,
        'gpt-3.5-turbo' => 16385,
        'gpt-3.5-turbo-instruct' => 4096
    ];
    $open_ai_max_tokens = sb_isset($max_tokens_list, $model);
    if (!$open_ai_max_tokens) {
        foreach ($max_tokens_list as $key => $value) {
            if (str_contains($model, $key)) {
                return $value;
            }
        }
        return 99999;
    }
    return $open_ai_max_tokens;
}

function sb_open_ai_analyze_message($prompt, $message, $conversation_id = false) {
    return sb_open_ai_system_task($conversation_id ? 'Analyze the last user message. If the last user message refers to or depends on information from a previous message, use the previous message to complete and generate the returned value. ' . $prompt : 'Analyze the provided MESSAGE. ' . $prompt . ' MESSAGE: "' . $message . '"', false, $conversation_id);
}

function sb_open_ai_rewrite_message($message, $language = false) {
    return trim(sb_isset(sb_open_ai_message($message, false, false, false, ['rewrite' => true, 'language' => $language ? $language : sb_get_setting('front-auto-translations')]), 1, $message));
}

function sb_open_ai_system_task($message, $language_code = false, $conversation_id = false) {
    return sb_isset(sb_open_ai_message($message . ($language_code ? ' The returned text language must be in the language of the ' . strtoupper($language_code) . ' language code.' : ''), false, false, $conversation_id, 'system-task'), 1);
}

/*
 * -----------------------------------------------------------
 * FLOWS
 * -----------------------------------------------------------
 *
 * 1. Save the flows
 * 2. Return the flows
 * 3. Return the block message to send to the user
 * 4. Return a block by the string identifier
 * 5. Return the next block container of the given block
 * 6. Send the start message for flows that start on new converstations
 *
 */

function sb_flows_save($flows) {
    $flows = json_decode($flows, true);
    $previous_flows = sb_flows_get();
    $response = sb_save_external_setting('open-ai-flows', $flows);
    if ($response === true) {
        $paragraphs = [];
        $updated_flows = [];
        for ($i = 0; $i < count($flows); $i++) {
            $flow_name = 'flow-' . $flows[$i]['name'];
            $is_updated = true;

            // Check if the flow has been updated
            for ($j = 0; $j < count($previous_flows); $j++) {
                if ($previous_flows[$j] == $flows[$i]) {
                    $is_updated = false;
                    break;
                }
            }

            // Train the chatbot
            if ($is_updated) {
                $steps = $flows[$i]['steps'];
                $count = count($steps) - 1;
                for ($j = 0; $j < $count; $j++) {
                    $block_cnts = $steps[$j];
                    $index = 0;
                    for ($y = 0; $y < count($block_cnts); $y++) {
                        $blocks = $block_cnts[$y];
                        for ($x = 0; $x < count($blocks); $x++) {
                            $block = $blocks[$x];
                            if ($block['type'] == 'start' && $block['start'] == 'message' && !sb_isset($block, 'disabled')) {
                                $block_cnts_next_step = $steps[$j + 1];
                                $answer = '';
                                $answer_attachments = [];
                                $blocks_next = $block_cnts_next_step[$index];
                                for ($k = 0; $k < count($blocks_next); $k++) {
                                    $answer_block = sb_flows_get_block_code($blocks_next[$k], $flows[$i]['name'] . '_' . ($j + 1) . '_' . $index . '_' . $k, false) . ' ';
                                    $answer = sb_flows_merge_actions($answer, $answer_block);
                                    if (!empty($blocks_next[$k]['attachments'])) {
                                        $answer_attachments = array_merge($answer_attachments, $blocks_next[$k]['attachments']);
                                    }
                                }
                                if ($answer || !empty($answer_attachments)) {
                                    $extra = [];
                                    if (!empty($block['conditions'])) {
                                        $extra['conditions'] = $block['conditions'];
                                    }
                                    if (!empty($answer_attachments)) {
                                        $extra['attachments'] = $answer_attachments;
                                    }
                                    if (!empty($block['department'])) {
                                        $extra['department'] = $block['department'];
                                    }
                                    if (!empty($block['conversation_source'])) {
                                        $extra['conversation_source'] = $block['conversation_source'];
                                    }
                                    $extra = empty($extra) ? false : $extra;
                                    if (!empty($block['message'])) {
                                        for ($k = 0; $k < count($block['message']); $k++) {
                                            array_push($paragraphs, [[$block['message'][$k]['message'], trim($answer)], false, $flow_name, $extra]);
                                        }
                                    }
                                }
                                $index++;
                            }
                        }
                    }
                }
                array_push($updated_flows, $flow_name);
            }
        }

        // Delete the previous embeddings
        $embedding_sources_all = sb_get_external_setting('embedding-sources');
        $embedding_sources = sb_isset($embedding_sources_all, 'sb-flows', []);
        $embedding_sources_new = [];
        $flow_names = array_column($flows, 'name');
        for ($i = 0; $i < count($embedding_sources); $i++) {
            $embeddings = sb_open_ai_embeddings_get_file($embedding_sources[$i], true);
            $embeddings_new = [];
            for ($y = 0; $y < count($embeddings); $y++) {
                if (!in_array($embeddings[$y]['source'], $updated_flows) && in_array(substr($embeddings[$y]['source'], 5), $flow_names)) {
                    array_push($embeddings_new, $embeddings[$y]);
                }
            }
            $file_path = sb_open_ai_embeddings_get_file($embedding_sources[$i]);
            if (empty($embeddings_new)) {
                sb_file_delete($file_path);
            } else {
                array_push($embedding_sources_new, $embedding_sources[$i]);
                if ($embeddings_new != $embeddings) {
                    sb_file($file_path, json_encode($embeddings_new, JSON_UNESCAPED_UNICODE));
                }
            }
        }
        if ($embedding_sources_new != $embedding_sources) {
            $embedding_sources_all['sb-flows'] = $embedding_sources_new;
            sb_save_external_setting('embedding-sources', $embedding_sources_all);
        }

        // Return
        $response = sb_open_ai_embeddings_generate($paragraphs, 'sb-flows');
        return $response[0] ? true : $response;
    }
    return $response;
}

function sb_flows_get($flow_name = false, $is_integrations = true) {
    $flows = sb_get_external_setting('open-ai-flows', []);
    $integrations = $is_integrations ? sb_integrations_get_active() : [];
    foreach ($integrations as $integration) {
        array_push($flows, json_decode(file_get_contents(SB_PATH . '/apps/dialogflow/flows/' . $integration . '.json'), true));
    }
    if ($flow_name) {
        for ($i = 0; $i < count($flows); $i++) {
            if ($flows[$i]['name'] == $flow_name) {
                return $flows[$i];
            }
        }
        return false;
    }
    return $flows;
}

function sb_flows_get_block_code($block, $flow_identifier, $is_merge_fields = true, $conversation_id = false) {
    switch ($block['type']) {
        case 'choices':
        case 'button_list': // Deprecated.
            $options_text = '';
            $is_hidden = sb_isset($block, 'conversational_mode') || ($conversation_id && in_array(sb_get_conversation_source($conversation_id), ['fb', 'ig', 'tg', 'wc', 'ln', 'vb', 'tm', 'za']));
            for ($i = 0; $i < count($block['options']); $i++) {
                if (!$is_hidden) {
                    $block['options'][$i] = explode('|', $block['options'][$i])[0];
                }
                $options_text .= $block['options'][$i] . ',';
            }
            return '[chips id="flow_' . $flow_identifier . '" ' . ($is_hidden ? 'hidden-' : '') . 'options="' . substr($options_text, 0, -1) . '" message="' . ($is_merge_fields ? sb_merge_fields($block['message']) : $block['message']) . '"]';
        case 'message':
            return $is_merge_fields ? sb_merge_fields($block['message']) : $block['message'];
        case 'video':
            preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $block['url'], $matches);
            return $matches ? ($is_merge_fields ? sb_merge_fields($block['message']) : $block['message']) . ' [video type="' . (strpos($block['url'], 'vimeo') ? 'vimeo' : 'youtube') . '" id="' . $matches[1] . '"]' : false;
        case 'get_user_details':
            return '[action flow-so="' . $flow_identifier . '"]';
        case 'action':
        case 'set_data':
            $string = '';
            $slugs = $block['type'] == 'action' ? ['actions', 'actions'] : ['data', 'set-data'];
            $items = $block[$slugs[0]];
            for ($i = 0; $i < count($items); $i++) {
                $string .= $items[$i][0] . (empty($items[$i][1]) ? '' : ':' . str_replace(',', '|', $items[$i][1])) . ',';
            }
            return '[action ' . $slugs[1] . '="' . substr($string, 0, -1) . '"]';
        case 'rest_api':
            return '[action rest-api="' . $flow_identifier . '"]';
        case 'condition':
            $flow_identifier = explode('_', $flow_identifier);
            $next_block_cnt = sb_flows_get_next_block_cnt($flow_identifier[0], $flow_identifier[1], $flow_identifier[2], sb_automations_validate($block['conditions'], true) ? 0 : 1);
            $response = '';
            if ($next_block_cnt) {
                for ($i = 0; $i < count($next_block_cnt[0]); $i++) {
                    $block_code = sb_flows_get_block_code($next_block_cnt[0][$i], $flow_identifier[0] . '_' . ($flow_identifier[1] + 1) . '_' . $next_block_cnt[1] . '_' . $i, $is_merge_fields, sb_get_conversation_source($conversation_id));
                    if (str_contains($block_code, '[action ')) {
                        $response = sb_flows_merge_actions($response, $block_code);
                    } else {
                        $response .= ' ' . $block_code;
                    }
                }
            }
            return $response;
        case 'tools':
            return '[action tools="' . $flow_identifier . '"]';
        case 'flow_connector':
            $response = '';
            $flow = sb_flows_get($block['flow_name']);
            $flow_block_cnt = $flow['steps'][1][0];
            for ($i = 0; $i < count($flow_block_cnt); $i++) {
                $response_block = sb_flows_get_block_code($flow_block_cnt[$i], $block['flow_name'] . '_1_0_' . $i, false) . ' ';
                $response = sb_flows_merge_actions($response, $response_block);
            }
            return $response;
    }
    return false;
}

function sb_flows_get_by_string($flow_identifier, $type = 'block') {
    $response = false;
    $flow_identifier = explode('_', $flow_identifier);
    if ($flow_identifier[0]) {
        $flow = sb_flows_get($flow_identifier[0]);
        $is_integration = isset($flow['integration']);
        if ($flow) {
            $flow = $flow['steps'][$flow_identifier[1]];
            switch ($type) {
                case 'block_cnts':
                    $response = $flow;
                    break;
                case 'blocks':
                    $response = $flow[$flow_identifier[2]];
                    break;
                case 'block':
                    $response = $flow[$flow_identifier[2]][$flow_identifier[3]];
                    break;
            }
            $response['index'] = $flow_identifier;
            if ($is_integration) {
                $response['integration'] = true;
            }
        }
    }
    return $response;
}

function sb_flows_get_next_block_cnt($flow_name, $current_step_index, $current_block_cnt_index, $current_connector_index = 0) {
    $flow = sb_isset(sb_flows_get($flow_name), 'steps');
    if ($flow && isset($flow[$current_step_index + 1])) {
        $current_block_cnts = $flow[$current_step_index];
        $next_block_cnt_index = $current_connector_index;
        for ($i = 0; $i < $current_block_cnt_index; $i++) {
            $blocks = $current_block_cnts[$i];
            for ($j = 0; $j < count($blocks); $j++) {
                if ($blocks[$j]['type'] == 'button_list' || $blocks[$j]['type'] == 'choices') { // Deprecated. Remove $blocks[$j]['type'] == 'button_list' ||
                    $next_block_cnt_index += count($blocks[$j]['options']);
                } else if ($blocks[$j]['type'] == 'get_user_details') {
                    $next_block_cnt_index++;
                } else if ($blocks[$j]['type'] == 'condition') {
                    $next_block_cnt_index += 2;
                }
            }
        }
        return [$flow[$current_step_index + 1][$next_block_cnt_index], $next_block_cnt_index];
    }
    return false;
}

function sb_flows_on_conversation_start_or_load($messages, $language, $conversation_id, $is_on_load = false) {
    $flows = sb_flows_get();
    $response = false;
    for ($i = 0; $i < count($flows); $i++) {
        $flow = $flows[$i];
        if ($flow) {
            $start_step = $flow['steps'][0][0][0];
            if (sb_isset($start_step, 'start') == ($is_on_load ? 'load' : 'conversation') && !$start_step['disabled'] && sb_automations_validate($start_step['conditions'], true)) {
                $next_block_cnt = sb_isset($flow['steps'][1], 0, []);
                $code = '';
                if (!empty($next_block_cnt)) {
                    $flow_id = $flow['name'] . '_1_0_';
                    for ($j = 0; $j < count($next_block_cnt); $j++) {
                        $code = sb_flows_merge_actions($code, sb_flows_get_block_code($next_block_cnt[$j], $flow_id . $j, true, $conversation_id));
                    }
                }
                if ($is_on_load) {
                    return $code;
                }
                $action = sb_flows_execute($code, $messages, $language, $conversation_id);
                $shortcode = sb_isset(sb_isset($action, 3), 'shortcode');
                sb_send_message(sb_get_bot_ID(), $conversation_id, $action[0] ? $action[0] : $code, $action[2], -1, $shortcode ? ['action' => $shortcode] : false);
                $response = $action[0] ? $action[0] : $code;
            }
        }
    }
    return $response;
}

function sb_flows_execute($message, $messages, $language, $conversation_id, $payload = false) {
    $action = sb_get_shortcode($message, 'action');
    $count = count($messages);
    $response = false;
    $attachments_response = [];
    $client_side_payload = [];
    $server_side_payload = [];
    if ($action) {
        $response = str_replace($action['shortcode'], '', $message);
        $is_tools = isset($action['tools']);
        $is_integration = false;
        $flow_name = sb_isset($action, $is_tools ? 'tools' : 'flow-so');
        $server_side_payload = ['action' => $action['shortcode']];
        if ($flow_name) {
            $user_message_payload = $count ? json_decode(sb_isset($messages[$count - 1], 'payload', '{}'), true) : [];
            $user_message_payload[$is_tools ? 'tools' : 'flow_so'] = $flow_name;
            $block = sb_flows_get_by_string($flow_name);
            $is_integration = !empty($block['integration']);
            $is_tools = sb_isset($block, 'type') == 'tools';
            if ($is_tools) {
                $block['name'] = implode('_', $block['index']);
            }
            if ($count) {
                sb_update_message_payload($messages[$count - 1]['id'], $payload ? array_merge($payload, $user_message_payload) : $user_message_payload);
            }
            if (sb_open_ai_is_playground()) {
                $GLOBALS['SB_OPEN_AI_PLAYGROUND']['payload'] = $user_message_payload;
            }
            $response = sb_merge_fields(sb_t($block['message'], $language ? (is_string($language) ? $language : $language[0]) : false));
        }
        if (isset($action['set-data'])) {
            $data = explode(',', $action['set-data']);
            $user_data = [];
            for ($i = 0; $i < count($data); $i++) {
                $data_item = explode(':', $data[$i]);
                $user_data[$data_item[0]] = $data_item[1];
            }
            sb_open_ai_execute_set_data($user_data);
        }
        if (isset($action['actions'])) {
            $data = explode(',', $action['actions']);
            $execute_actions = sb_open_ai_execute_actions(array_map(function ($item) {
                return explode(':', $item);
            }, $data), $conversation_id);
            $client_side_payload = $execute_actions['client_side_payload'];
            $attachments_response = $execute_actions['attachments'];
        }
        if (isset($action['rest-api']) || $is_tools) {
            $continue = true;
            $block = sb_flows_get_by_string($action[$is_tools ? 'tools' : 'rest-api']);
            if ($block) {
                if (!$is_integration) {
                    $headers = array_map(function ($items) {
                        return implode(':', $items);
                    }, $block['headers']);
                }
                $body = json_decode(sb_isset($block, 'body', '{}'), true);
                $body['sb'] = ['user' => sb_get_active_user(), 'conversation_id' => $conversation_id, 'user_language' => sb_get_user_language(sb_get_active_user_ID())];
                $body = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($is_tools) {
                    $continue = false;
                    $properties = sb_isset($block, 'properties');
                    if (empty($properties) || !sb_isset(sb_isset($properties, 0), 0) || sb_isset($properties[0], 3)) {
                        $continue = true;
                    }
                }
                if ($continue) {
                    if ($is_integration) {
                        $response = $response_rest_api = sb_integrations_execute($block, $messages, $conversation_id);
                    } else {
                        $response_call = sb_curl($block['url'], $body, array_merge($headers, ['Content-Type: application/json', 'Content-Length: ' . strlen($body)]), $block['method']);
                        $response_rest_api = is_array($response_call) ? $response_call : json_decode($response_call, true);
                        $save_response = sb_isset($block, 'save_response');
                        if ($save_response) {
                            $user_data = [];
                            for ($i = 0; $i < count($save_response); $i++) {
                                $keys = explode('.', $save_response[$i][1]);
                                $response_rest_api_now = $response_rest_api;
                                foreach ($keys as $key) {
                                    if (isset($response_rest_api_now[$key])) {
                                        $response_rest_api_now = $response_rest_api_now[$key];
                                    } else {
                                        $response_rest_api_now = false;
                                        break;
                                    }
                                }
                                if ($response_rest_api_now) {
                                    $user_data[$save_response[$i][0]] = $response_rest_api_now;
                                }
                                if (!empty($user_data) && !sb_open_ai_is_playground()) {
                                    sb_update_user(sb_get_active_user_ID(), $user_data, $user_data, true, true);
                                }
                            }
                        }
                        if ($is_tools) {
                            $response = sb_open_ai_rewrite_message(sb_isset($response_rest_api, 'message', is_array($response_call) ? json_encode($response_call) : $response_call));
                            if (!empty($response_rest_api['success'])) {
                                $server_side_payload['tools_end'] = $flow_name;
                            }
                        }
                    }
                    if ($is_tools && $block['index'][1] == 0 && $count) {
                        $server_side_payload['parent_message_id'] = $messages[$count - 1]['id'];
                    }
                }
            }
        }
    }
    return [$response, $client_side_payload, $attachments_response, $action, $server_side_payload];
}

function sb_flows_merge_actions($actions_string, $action_string) {
    if (strpos($actions_string, '[action ') === false || strpos($action_string, '[action ') === false) {
        return $actions_string . ' ' . $action_string;
    }
    return str_replace('[action', '[action ' . str_replace(['[action', ']'], '', $action_string), $actions_string);
}

function sb_flows_get_open_ai_message_response($flow_name, $current_step_index, $current_block_cnt_index, $current_connector_index, $payload, $conversation_id) {
    $next_block_cnt = sb_flows_get_next_block_cnt($flow_name, $current_step_index, $current_block_cnt_index, $current_connector_index);
    $response = false;
    if ($next_block_cnt) {
        $response = '';
        for ($i = 0; $i < count($next_block_cnt[0]); $i++) {
            $next_flow_name = $flow_name . '_' . ($current_step_index + 1) . '_' . $next_block_cnt[1] . '_' . $i;
            $block_code = sb_flows_get_block_code($next_block_cnt[0][$i], $next_flow_name, true, $conversation_id);
            if (str_contains($block_code, '[action ')) {
                if ($next_block_cnt[0][$i]['type'] == 'get_user_details') {
                    $payload['flow_so'] = $next_flow_name;
                }
                $response = sb_flows_merge_actions($response, $block_code);
            } else {
                $response .= ' ' . $block_code;
            }
        }
    }
    $attachments = sb_isset(sb_isset($next_block_cnt[0], 0), 'attachments');
    if ($attachments) {
        for ($i = 0; $i < count($attachments); $i++) {
            if (!empty($attachments[$i])) {
                $attachments[$i] = [basename($attachments[$i]), $attachments[$i]];
            }
        }
        $payload['attachments'] = $attachments;
    }
    return [$response, $payload];
}

function sb_flows_run_on_load($message, $conversation_id, $language = false) {
    $is_action = str_contains($message, '[action');
    $shortcode = sb_get_shortcode($message, $is_action ? 'action' : false);
    $language = $language ? (is_array($language) ? $language[0] : $language) : false;
    if (!empty($shortcode)) {
        if ($is_action) {
            $flow_shortcode = sb_isset($shortcode, 'flow-so');
            if ($flow_shortcode) {
                $block = sb_flows_get_by_string($flow_shortcode);
                return sb_send_message(sb_get_bot_ID(), $conversation_id, sb_merge_fields(sb_t($block['message'], $language)), [], 3, ['flow_so' => $flow_shortcode]);
            } else {
                sb_flows_execute($message, [], $language, $conversation_id);
            }
        } else {
            $shortcode = $shortcode[0];
        }
        return sb_send_message(sb_get_bot_ID(), $conversation_id, sb_merge_fields(sb_t($message, $language)), [], 3);
    }
    return false;
}

/*
 * -----------------------------------------------------------
 * GOOGLE
 * -----------------------------------------------------------
 *
 * 1. Make a CURL request to Google APIs
 * 2. Get the access token
 * 3. Detect the language of a string
 * 4. Retrieve the full language name in the desired language
 * 5. Text translation
 * 6. Analyze Entities
 * 7. Return the client ID and secret key
 * 8. Return the message in the desired language
 * 9. Google troubleshooting
 * 10. Google Calendar functions
 *
 */

function sb_google_curl($url_part, $query = false, $url_part_prefix = false, $method = 'POST') {
    $response = sb_curl('https://' . ($url_part_prefix ? $url_part_prefix . '.' : '') . 'googleapis.com/' . $url_part, $query, ['Content-Type: application/json', 'Authorization: Bearer ' . sb_google_get_token(), 'Content-Length: ' . strlen($query)], $method);
    if (isset($response['error'])) {
        if ($response['error']['status'] == 'UNAUTHENTICATED') {
            global $SB_GOOGLE_RECURSION;
            if (empty($SB_GOOGLE_RECURSION)) {
                $SB_GOOGLE_RECURSION = true;
                sb_google_get_token();
                return sb_google_curl($url_part, $query, $url_part_prefix);
            }
        }
        return sb_error('google-api-error', 'sb_google_curl', isset($response['error']['message']) ? $response['error']['message'] : json_encode($response));
    }
    return $response;
}

function sb_google_get_token() {
    global $SB_GOOGLE_TOKEN;
    if (!empty($SB_GOOGLE_TOKEN)) {
        return $SB_GOOGLE_TOKEN;
    }
    $token = sb_get_external_setting('google-token');
    if ($token && time() < $token[1]) {
        $SB_GOOGLE_TOKEN = $token[0];
        return $SB_GOOGLE_TOKEN;
    }
    $token = sb_get_multi_setting('google', 'google-refresh-token', sb_defined('GOOGLE_REFRESH_TOKEN'));
    if (empty($token)) {
        return sb_error('google-refresh-token-not-found', 'sb_google_get_token', 'Click the synchronize button to get the refresh token.');
    }
    $info = sb_google_key();
    $query = '{ refresh_token: "' . $token . '", grant_type: "refresh_token", client_id: "' . $info[0] . '", client_secret: "' . $info[1] . '" }';
    $response = sb_curl('https://accounts.google.com/o/oauth2/token', $query, ['Content-Type: application/json', 'Content-Length: ' . strlen($query)]);
    $token = sb_isset($response, 'access_token');
    if ($token) {
        sb_save_external_setting('google-token', [$token, time() + $response['expires_in']]);
        $SB_GOOGLE_TOKEN = $token;
        return $token;
    }
    return json_encode($response);
}

function sb_google_language_detection($string) {
    $query = json_encode(['q' => $string], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
    if (!sb_cloud_membership_has_credits('google')) {
        $response = sb_error('no-credits', 'sb_google_get_language_name');
        return sb_is_debug() ? $response : false;
    }
    $response = sb_google_curl('language/translate/v2/detect', $query, 'translation');
    sb_cloud_membership_use_credits('translation', 'google', $string);
    return isset($response['data']) && !empty($response['data']['detections']) ? sb_language_code($response['data']['detections'][0][0]['language']) : false;
}

function sb_google_get_language_name($target_language_code) {
    $query = json_encode(['target' => $target_language_code], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
    $response = sb_google_curl('language/translate/v2/languages', $query, 'translation');
    if (isset($response['data'])) {
        $languages = $response['data']['languages'];
        for ($i = 0; $i < count($languages); $i++) {
            if ($languages[$i]['language'] == $target_language_code) {
                return $languages[$i]['name'];
            }
        }
    }
    return $response;
}

function sb_google_translate($strings, $language_code, $message_ids = false, $conversation_id = false) {
    if (empty($language_code)) {
        return $strings;
    }
    $translations = [];
    $chunks = array_chunk($strings, 125);
    $language_code = strtolower(substr(sb_dialogflow_language_code($language_code), 0, 2));
    $language_code = sb_isset(['br' => 'pt'], $language_code, $language_code);
    $shortcode_replacements = [
        ['[chips ', '[buttons ', '[button ', '[select ', '[email ', '[articles ', '[rating ', '[list ', '[list-image ', '[table ', '[inputs ', '[card ', '[slider ', '[slider-images ', '[video ', '[image ', '[share ', '[registration]', '[timetable]', '[email]', '[articles]', ' options="', ' title="', ' message="', ' success="', ' placeholder="', ' name="', ' phone="', ' phone-required="', ' link="', ' label="', '  label-positive="', ' label-negative="', ' success-negative="', ' values="', ' header="', ' button="', ' image="', ' target="', ' extra="', ' link-text="', ' type="', ' height="', ' id="', ' url="', ' numeric="true', ']', ' hidden-options=', ',', ':'],
        ['[1 ', '[2 ', '[3 ', '[4 ', '[5 ', '[7 ', '[9 ', '[10 ', '[11 ', '[12 ', '[13 ', '[14 ', '[15 ', '[16 ', '[17', '[18', '[19', '[20', '[21', ' 22="', ' 23="', ' 24="', ' 25="', ' 26="', ' 27="', ' 28="', ' 29="', ' 30="', ' 31="', ' 32="', ' 33="', ' 34="', ' 35="', ' 36="', ' 37="', ' 38="', ' 39="', ' 40="', ' 41="', ' 42="', ' 43="', ' 44="', ' 45="', ' 46=', ' 47=', ' 48=', ' 49=', ' 50=', '{R}', '{T}']
    ];
    $skipped_translations = [];
    $strings_original = $strings;
    if (!sb_cloud_membership_has_credits('google')) {
        return sb_error('no-credits', 'sb_dialogflow_message');
    }
    for ($j = 0; $j < count($chunks); $j++) {
        $strings = $chunks[$j];
        for ($i = 0; $i < count($strings); $i++) {
            $string = $strings[$i];
            if (str_contains($string, '[') || str_contains($string, '="')) {
                $string = str_replace($shortcode_replacements[0], $shortcode_replacements[1], $string);
                $string = str_replace('="true"', '="1"', $string);
            }
            preg_match_all('/`[\S\s]*?`/', $string, $matches);
            $matches = $matches[0];
            array_push($skipped_translations, $matches);
            for ($y = 0; $y < count($matches); $y++) {
                if ($matches[$y] != '``') {
                    $string = str_replace($matches[$y], '"' . $y . '"', $string);
                }
            }
            $strings[$i] = str_replace('"', '«»', str_replace(['\r\n', PHP_EOL, '\r', '\n'], '~~', $string));
        }
        $query = json_encode(['q' => $strings, 'target' => $language_code, 'format' => 'text'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
        $response = sb_google_curl('language/translate/v2', $query, 'translation');
        if ($response && isset($response['data'])) {
            sb_cloud_membership_use_credits('translation', 'google', json_encode($strings, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
            $translations_partial = sb_isset($response['data'], 'translations', []);
            for ($i = 0; $i < count($translations_partial); $i++) {
                $string = $translations_partial[$i]['translatedText'];
                while (mb_substr($string, 0, 1) == '"') {
                    $string = mb_substr($string, 1);
                }
                $string = str_replace([PHP_EOL, '\r\n', '\r', '<br>', '~~', '”', '«»', '« »', '»»', '««', '_}', '“', '""'], ["\n", "\n", "\n", "\n", "\n", '"', '"', '"', '"', '"', '}', '', '"'], $string);
                for ($y = 0; $y < count($skipped_translations[$i]); $y++) {
                    $string = str_replace('"' . $y . '"', $skipped_translations[$i][$y], $string);
                }
                $string = str_replace($shortcode_replacements[1], $shortcode_replacements[0], str_replace(['44 =', '{R}'], ['44=', '{RR}'], $string));
                $shortcodes = sb_get_shortcode($string);
                foreach ($shortcodes as $shortcode) {
                    if ($shortcode && $shortcode['shortcode_name'] == 'list') {
                        $string = str_replace(sb_isset($shortcode, 'values', ''), str_replace([':', ','], ['\:', '\,'], str_replace(['\\:', '\\,'], [':', ','], sb_isset($shortcode, 'values', ''))), $string);
                    }
                }
                $string = str_replace('{RR}', ',', $string);
                $string = str_replace('="1"', '="true"', $string);
                $string = str_replace(['{R}', '{T}', '\,\,', ',,'], [',', ':', '\,', ','], $string);
                array_push($translations, $string);
            }
        } else {
            $error = sb_isset($response, 'error');
            if ($error) {
                sb_error('error', 'sb_google_translate', $error, sb_is_agent());
                return $strings_original;
            }
        }
    }
    $count = count($translations);
    if ($count && $message_ids && $conversation_id && $count == count($message_ids)) {
        $data = sb_db_get('SELECT id, payload FROM sb_messages WHERE id IN (' . sb_db_escape(implode(',', $message_ids)) . ') AND conversation_id = ' . sb_db_escape($conversation_id, true), false);
        for ($i = 0; $i < $count; $i++) {
            if (strlen($string) > 1) {
                $payload = json_decode($data[$i]['payload'], true);
                $payload['translation'] = $translations[$i];
                $payload['translation-language'] = $language_code;
                sb_update_message_payload($data[$i]['id'], $payload);
            }
        }
    }
    return $count ? $translations : $response;
}

function sb_google_translate_auto($string, $user_id) {
    if (is_numeric($user_id) && sb_get_multi_setting('google', 'google-translation')) {
        $recipient_language = sb_get_user_language($user_id);
        $active_user_language = sb_get_user_language(sb_get_active_user_ID());
        if ($recipient_language && $active_user_language && $recipient_language != $active_user_language) {
            $translation = sb_google_translate([$string], $recipient_language);
            if (count($translation)) {
                $translation = trim($translation[0]);
                if (!empty($translation)) {
                    return $translation;
                }
            }
        }
    }
    return $string;
}

function sb_google_translate_article($article_id, $language_code) {
    $article = sb_get_articles($article_id, false, true);
    if (count($article)) {
        $article = $article[0];
        $editos_js = json_decode($article['editor_js'], true);
        $blocks = sb_isset($editos_js, 'blocks', []);
        $strings = [$article['title']];
        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'header':
                case 'paragraph':
                    array_push($strings, html_entity_decode($block['data']['text']));
                    break;
                case 'list':
                    foreach ($block['data']['items'] as $item) {
                        array_push($strings, html_entity_decode($item));
                    }
                    break;
            }
        }
        $strings_translated = sb_google_translate($strings, $language_code);
        if (sb_is_error($strings_translated)) {
            return $article;
        }
        $index = 1;
        for ($i = 0; $i < count($blocks); $i++) {
            switch ($blocks[$i]['type']) {
                case 'header':
                case 'paragraph':
                    $blocks[$i]['data']['text'] = $strings_translated[$index];
                    $index++;
                    break;
                case 'list':
                    $items = [];
                    foreach ($blocks[$i]['data']['items'] as $item) {
                        array_push($items, $strings_translated[$index]);
                        $index++;
                    }
                    $blocks[$i]['data']['items'] = $items;
            }
        }
        for ($i = 0; $i < count($strings); $i++) {
            $article['content'] = str_replace(htmlentities($strings[$i]), htmlentities($strings_translated[$i]), $article['content']);
        }
        $editos_js['blocks'] = $blocks;
        $article['title'] = $strings_translated[0];
        $article['language'] = $language_code;
        $article['parent_id'] = $article_id;
        $article['editor_js'] = $editos_js;
        unset($article['id']);
        return $article;
    }
    return false;
}

function sb_google_language_detection_update_user($string, $user_id = false) {
    $user_id = $user_id ? $user_id : sb_get_active_user_ID();
    $detected_language = sb_google_language_detection($string);
    $language = sb_get_user_language($user_id);
    if ($detected_language != $language[0] && !empty($detected_language)) {
        $response = sb_language_detection_db($user_id, $detected_language);
        if ($response) {
            unset($GLOBALS['SB_LANGUAGE']);
            return sb_get_current_translations();
        }
    }
    return false;
}

function sb_language_detection_db($user_id, $detected_language) {
    if ($detected_language == 'un') {
        return false;
    }
    $response = sb_update_user_value($user_id, 'language', $detected_language);
    sb_db_query('DELETE FROM sb_users_data WHERE user_id = ' . sb_db_escape($user_id) . ' AND slug = "browser_language"');
    return $response;
}

function sb_google_language_detection_get_user_extra($message) {
    if ($message && (sb_get_multi_setting('google', 'google-language-detection') || sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active'))) { // Deprecated: sb_get_multi_setting('dialogflow-language-detection', 'dialogflow-language-detection-active')
        return [sb_google_language_detection($message), 'Language'];
    }
    return '';
}

function sb_google_analyze_entities($string, $language = false, $token = false) {
    if (!strpos(trim($string), ' ')) {
        return false;
    }
    $token = $token ? $token : sb_google_get_token();
    $query = ['document' => ['type' => 'PLAIN_TEXT', 'content' => ucwords($string)]];
    if ($language) {
        $query['document']['language'] = $language;
    }
    $query = json_encode($query, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
    $response = sb_google_curl('v1/documents:analyzeEntities', $query, 'language');
    if (isset($response['error'])) {
        trigger_error($response['error']['message']);
    }
    return $response;
}

function sb_google_key() {
    return sb_ai_is_manual_sync('google') ? [trim(sb_get_multi_setting('google', 'google-client-id')), trim(sb_get_multi_setting('google', 'google-client-secret'))] : [GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET];
}

function sb_google_get_message_translation($message, $language = false) {
    $payload = json_decode(sb_isset($message, 'payload'), true);
    $translation = sb_isset($payload, 'original-message');
    if ($translation && (!$language || $language == sb_isset($payload, 'original-message-language'))) {
        $message['message'] = $translation;
    } else {
        $translation = sb_isset($payload, 'translation');
        if ($translation && ($language == sb_isset($payload, 'translation-language'))) {
            $message['message'] = $translation;
        }
    }
    return $message;
}

function sb_google_troubleshoot($debug = false) {
    if ($debug) {
        $_GET['debug'] = true;
    }
    if (sb_chatbot_active(true, false) || sb_get_setting('ai-smart-reply')) {
        $response = sb_dialogflow_message(false, 'Hello world');
        if (sb_is_error($response)) {
            return $response;
        }
        if ($response && isset($response['response']) && isset($response['response']['error'])) {
            return $response['response']['error']['message'];
        }
    }
    if (sb_get_multi_setting('google', 'google-multilingual-translation') || sb_get_multi_setting('google', 'google-translation') || sb_get_multi_setting('google', 'google-language-detection')) {
        $query = json_encode(['q' => ['hello world'], 'target' => 'it', 'format' => 'text']);
        $token = sb_google_get_token();
        if (sb_is_error($token)) {
            return $token;
        }
        $response = sb_google_curl('language/translate/v2', $query, 'translation');
        if (sb_is_error($response)) {
            return $response;
        }
    }
    if ($debug) {
        return true;
    } else {
        return sb_google_troubleshoot(true);
    }
}

/*
 * -----------------------------------------------------------
 * INTEGRATIONS
 * -----------------------------------------------------------
 *
 * 1. Shared integration functions
 * 2. Google Calendar
 * 3. TidyCal
 *
 */

function sb_integrations_list() {
    return ['google-calendar', 'tidycal'];
}

function sb_integrations_get_active($is_chatbot = true) {
    $integrations = sb_integrations_list();
    $active_integrations = [];
    for ($i = 0; $i < count($integrations); $i++) {
        if (sb_get_multi_setting($integrations[$i], $integrations[$i] . '-active') && (!$is_chatbot || !sb_get_multi_setting($integrations[$i], $integrations[$i] . '-disable-chatbot'))) {
            array_push($active_integrations, $integrations[$i]);
        }
    }
    return $active_integrations;
}

function sb_integrations_execute($block, $messages_or_function, $conversation_id) {
    $arguments = sb_isset($messages_or_function, 'arguments');
    $properties = false;
    $index = $block['index'][2];
    if ($arguments) {
        $arguments = json_decode($arguments, true);
    } else {
        $count = count($messages_or_function) - 1;
        for ($i = $count; $i > -1; $i--) {
            $message_text = sb_isset(sb_isset($messages_or_function, $i), 'message');
            if ($message_text) {
                $properties = sb_integrations_analyze_properties($block, $message_text, $conversation_id);
                break;
            }
        }
    }
    switch ($block['index'][0]) {
        case 'sb-integration-tidycal':
        case 'sb-integration-google-calendar':
            $is_google_calendar = $block['index'][0] == 'sb-integration-google-calendar';
            $language = sb_get_multi_setting('google', 'google-multilingual-translation') ? sb_get_user_language(sb_get_active_user_ID()) : sb_get_setting('front-auto-translations');
            if ($index == 4) {
                return sb_integration_get_available_slots_message($is_google_calendar ? 'google-calendar' : 'tidycal', $is_google_calendar ? sb_google_calendar_get_available_slots() : sb_tidycal_get_available_slots(), false, $language);
            }
            $user_bookings = $is_google_calendar ? sb_google_calendar_get_user_booking() : sb_tidycal_get_user_booking();
            $now = sb_convert_date(sb_gmt_now(), 'd F Y H:i');
            if ($index == 1 || $index == 3) {
                if (empty($user_bookings)) {
                    return sb_open_ai_rewrite_message('No booked appointments found.', $language);
                }
                if ($index == 3) {
                    $response = $is_google_calendar ? sb_google_calendar_cancel_booking($user_bookings[0]['id']) : sb_tidycal_cancel_booking($user_bookings[0]['id']);
                    return sb_open_ai_rewrite_message($response === true ? 'Booking successfully cancelled' : 'booking cancellation failed', $language);
                }
            }
            if (($index == 0 && !empty($user_bookings)) || $index == 2) {
                return empty($user_bookings) ? sb_open_ai_rewrite_message('No booked appointments found.') : sb_open_ai_system_task('Create a message telling the user that ' . ($index == 0 ? 'they can only have one booking and already have one' : 'their booking is') . ' from ' . implode(', ', array_map(function ($event) use ($is_google_calendar) {
                    return $is_google_calendar ? sb_beautify_date($event['start']['dateTime']) . ' to ' . sb_beautify_date($event['end']['dateTime']) : sb_beautify_date(sb_convert_date($event['starts_at'], 'Y-m-d H:i:s')) . ' to ' . sb_beautify_date(sb_convert_date($event['ends_at'], 'Y-m-d H:i:s'));
                }, $user_bookings)) . '. Also beautify date and time taking into account that the current date and time is ' . $now, $language);
            }
            $booking_date = sb_isset($arguments, 'booking-date', sb_isset($properties, 0));
            if ($booking_date) {
                $booking_date = sb_open_ai_system_task('Get the calendar year from ' . (new DateTime())->format('d F Y') . ' to ' . (new DateTime('+1 year'))->format('d F Y') . ' and use it to return the date and time for \'' . $booking_date . '\' in the format \'Y-m-d H:i\', calculated based on the current date and time ' . $now . '. Return only the start date and time and nothing else. Do not add comments. Make sure the returned date is more recent than ' . sb_convert_date(sb_gmt_now(), 'd F Y H:i') . ', if it is not, it was calculated incorrectly and must be generated again.');
            }
            if ($booking_date) {
                $booking_date_gmt = $is_google_calendar ? false : sb_convert_date($booking_date, 'Y-m-d H:i', true);
                $booking_date_ = DateTime::createFromFormat('Y-m-d H:i', $is_google_calendar ? $booking_date : $booking_date_gmt);
                if ($booking_date_->format('Y-m-d H:i') === ($is_google_calendar ? $booking_date : $booking_date_gmt)) {
                    $slots = $is_google_calendar ? sb_google_calendar_get_available_slots($booking_date) : sb_tidycal_get_available_slots($booking_date);
                    $slot_time = $is_google_calendar ? '+' . sb_get_multi_setting('google-calendar', 'google-calendar-slot-time', 30) . ' minutes' : $booking_date_->format('Y-m-d\TH:i:s.u\Z');
                    if ($slots && (($is_google_calendar && in_array($booking_date_->format('H:i'), sb_isset($slots, $booking_date_->format('Y-m-d'), []))) || (!$is_google_calendar && in_array($slot_time, array_column($slots, 'starts_at'))))) {
                        if ($index == 1 || !empty($user_bookings)) {
                            $response = $is_google_calendar ? sb_google_calendar_update_booking($user_bookings[0]['id'], $booking_date_->format('Y-m-d\TH:i:s'), $booking_date_->modify($slot_time)->format('Y-m-d\TH:i:s')) : sb_tidycal_update_booking($user_bookings[0]['id'], $slot_time);
                        } else {
                            if ($is_google_calendar) {
                                $response = sb_google_calendar_create_booking($booking_date_->format('Y-m-d\TH:i:s'), $booking_date_->modify($slot_time)->format('Y-m-d\TH:i:s'));
                            } else {
                                $response = sb_tidycal_create_booking($slot_time);
                            }
                        }
                        $response = $response && !sb_is_error($response) && isset($response['id']) ? ['success' => 'Booking ' . ($index == 1 ? 'updated.' : 'confirmed.'), 'booking' => $response] : ['error' => 'Booking failed.'];
                    } else {
                        $response = ['error' => $index == 1 && !empty($user_bookings) && str_contains($is_google_calendar ? $user_bookings[0]['start']['dateTime'] : $user_bookings[0]['starts_at'], $booking_date_->format('Y-m-d\TH:i')) ? 'Your event is already scheduled for ' . $booking_date_->format('Y-m-d TH:i') : 'slot already booked', 'available-slots' => $slots];
                    }
                    if (empty($arguments['booking-date'])) {
                        $available_slots = sb_isset($response, 'available-slots');
                        $response_booking = sb_isset($response, 'booking');
                        $response = sb_isset($response, 'success', sb_isset($response, 'error'));
                        if ($available_slots) {
                            $response = sb_integration_get_available_slots_message($is_google_calendar ? 'google-calendar' : 'tidycal', $available_slots, $booking_date, $language, $response);
                        } else {
                            if ($response_booking) {
                                $response .= ' Your booking is scheduled for ' . ($is_google_calendar ? sb_beautify_date($response_booking['start']['dateTime']) . ' to ' . sb_beautify_date($response_booking['end']['dateTime']) : sb_beautify_date(sb_convert_date($response_booking['starts_at'], 'Y-m-d H:i:s')) . ' to ' . sb_beautify_date(sb_convert_date($response_booking['ends_at'], 'Y-m-d H:i:s'))) . '.';
                            }
                            $response = sb_open_ai_rewrite_message($response, $language);
                        }
                    }
                }
            } else {
                $response = sb_integration_get_available_slots_message($is_google_calendar ? 'google-calendar' : 'tidycal', $is_google_calendar ? sb_google_calendar_get_available_slots($booking_date) : sb_tidycal_get_available_slots($booking_date), $booking_date, $language);
            }
            if ($language && is_array($response)) {
                $response['user_and_message_language'] = $language;
            }
            return $response;
    }
    return false;
}

function sb_integrations_analyze_properties($block, $message, $conversation_id) {
    $response = [];
    $properties = sb_isset($block, 'properties');
    $prompt = '';
    if (!empty($properties)) {
        $count = count($properties);
        if ($count == 1) {
            $prompt .= 'If it contains the ' . $properties[0][0] . ' (' . $properties[0][1] . ') return exactly it in English language (translate if in another language) without any additional text. Otherwise return exactly "no". Always return only the ' . $properties[0][0] . ' in English or exactly "no" if you cannot find it.';
        } else {
            $prompt .= 'If it contains one or more of the DETAILS of the following LIST return them in exactly the format [INDEX, DETAIL], [INDEX, DETAIL] where INDEX is the number before the detail and DETAIL is the string in the message containing the detail in English language (translate if in another language). Always return only details in English in exactly the format [INDEX, DETAIL], [INDEX, DETAIL] or exactly "no" if you cannot find any detail in the message. LIST: ';
            for ($i = 0; $i < $count; $i++) {
                if (!empty($properties[$i][3])) {
                    $prompt .= PHP_EOL . '[INDEX ' . $i . '] ' . $properties[$i][0] . ' - ' . $properties[$i][1];
                }
            }
        }
        $ai_detection = sb_open_ai_analyze_message($prompt, $message, $conversation_id);
        if ($ai_detection && $ai_detection != 'no') {
            if ($count == 1) {
                $response = [$ai_detection];
            } else {

            }
        }
    }
    return $response;
}

function sb_integrations_calendar_panel($user_id) {
    if (empty($user_id)) {
        return;
    }
    $is_tidycal = sb_get_multi_setting('tidycal', 'tidycal-active');
    $bookings = $is_tidycal ? sb_tidycal_get_user_booking($user_id) : sb_google_calendar_get_user_booking($user_id);
    if (empty($bookings)) {
        return;
    }
    $code = '';
    foreach ($bookings as $booking) {
        $duration = $is_tidycal ? $booking['booking_type']['duration_minutes'] : intval((new DateTime($booking['end']['dateTime']))->getTimestamp() - (new DateTime($booking['start']['dateTime']))->getTimestamp()) / 60;
        $code .= '<a data-id="' . $booking['id'] . '" href="' . ($is_tidycal ? 'https://tidycal.com/dashboard/bookings' : $booking['htmlLink']) . '" target="_blank">' . sb_beautify_date($is_tidycal ? $booking['starts_at'] : $booking['start']['dateTime'], true) . ' <span>' . $duration . ' ' . sb_('minutes') . '</span><i class="sb-icon-close sb-integration-calendar-cancel"></i></a>';
    }
    return $code;
}

function sb_integrations_calendar_create_booking($start_date_local, $user_id) {
    $start = DateTime::createFromFormat('d/m/Y H:i', $start_date_local);
    if (sb_get_multi_setting('tidycal', 'tidycal-active')) {
        $response = sb_tidycal_create_booking(sb_convert_date($start->format('Y-m-d\TH:i:s'), 'Y-m-d\TH:i:s\Z', true), sb_get_user($user_id));
        return sb_is_error($response) ? $response : (empty($response['errors']) ? true : reset($response['errors']));
    }
    $response = sb_google_calendar_create_booking($start->format('Y-m-d\TH:i:s'), (clone $start)->modify('+' . sb_get_multi_setting('google-calendar', 'google-calendar-slot-time', 30) . ' minutes')->format('Y-m-d\TH:i:s'), $user_id);
    return sb_is_error($response) || empty($response['id']) ? $response : true;
}

function sb_integrations_calendar_cancel_booking($booking_id) {
    return sb_get_multi_setting('tidycal', 'tidycal-active') ? sb_tidycal_cancel_booking($booking_id) : sb_google_calendar_cancel_booking($booking_id);
}

function sb_integrations_calendar_get_available_slots($date_local, $max_days_from_start = false) {
    if (!str_contains($date_local, ':')) {
        $date_local .= ' 00:00:00';
    }
    if (str_contains($date_local, '/')) {
        $date_local = DateTime::createFromFormat('d/m/Y H:i:s', $date_local);
        $date_local = $date_local->format('Y-m-d H:i:s');
    }

    if (sb_get_multi_setting('tidycal', 'tidycal-active')) {
        $response = sb_tidycal_get_available_slots($date_local, $max_days_from_start);
        $response_ = [];
        foreach ($response as $item) {
            $dt = new DateTime($item['starts_at']);
            $date = $dt->format('Y-m-d');
            if (empty($response_[$date])) {
                $response_[$date] = [];
            }
            array_push($response_[$date], sb_convert_date($item['starts_at'], 'H:i'));
        }
        $response = $response_;
    } else {
        $response = sb_google_calendar_get_available_slots($date_local, false, $max_days_from_start);
    }
    return $max_days_from_start == 1 ? reset($response) : $response;
}

function sb_integration_get_available_slots_message($integration, $available_slots, $booking_date = false, $language = false, $extra_message = '') {
    $is_tidycal = $integration == 'tidycal';
    if (!$is_tidycal) {
        $first_key = array_key_first($available_slots);
        $last_key = array_key_last($available_slots);
    }
    return sb_open_ai_system_task('Create a message asking the user for their preferred booking date and time. ' . $extra_message . 'Inform them that the next available slot' . ($booking_date ? ' after ' . sb_beautify_date($booking_date) : '') . ' is ' . sb_beautify_date($is_tidycal ? sb_convert_date($available_slots[0]['starts_at'], 'Y-m-d H:i:s') : $first_key . ' ' . $available_slots[$first_key][0]) . '. ' . (count($available_slots) > 10 ? 'Also, inform the user that there are various slots available from ' . sb_beautify_date($is_tidycal ? sb_convert_date($available_slots[0]['starts_at'], 'Y-m-d H:i:s') : $first_key . ' ' . $available_slots[$first_key][0]) . ' till ' . sb_beautify_date($is_tidycal ? sb_convert_date($available_slots[count($available_slots) - 1]['starts_at'], 'Y-m-d H:i:s') : $last_key . ' ' . $available_slots[$last_key][count($available_slots[$last_key]) - 1]) . '.' : 'Also, check the LIST of available date and time slots in the following LIST, and provide the user with a clear summary of these slots. LIST: ' . json_encode(array_map(function ($row) use ($is_tidycal) {
        return [
            'starts_at' => sb_beautify_date($is_tidycal ? sb_convert_date($row['starts_at'], 'Y-m-d H:i:s') : $row['start']['dateTime']),
            'ends_at' => sb_beautify_date($is_tidycal ? sb_convert_date($row['ends_at'], 'Y-m-d H:i:s') : $row['end']['dateTime']),
        ];
    }, $available_slots))), $language);
}

function sb_google_calendar_get_bookings($date_local = false) {
    $date_range = sb_google_calendar_get_date_range($date_local);
    $response = sb_google_curl('calendar/v3/calendars/' . sb_get_multi_setting('google-calendar', 'google-calendar-id', 'primary') . '/events?singleEvents=true&orderBy=startTime&timeMin=' . urlencode($date_range[0]) . '&timeMax=' . urlencode($date_range[1]), false, 'www', 'GET');
    return sb_is_error($response) ? $response : sb_isset(json_decode($response, true), 'items');
}

function sb_google_calendar_get_user_booking($user_id = false) {
    $now = new DateTime('now', new DateTimeZone(sb_utc_offset(true)));
    $response = sb_google_curl('calendar/v3/calendars/' . sb_get_multi_setting('google-calendar', 'google-calendar-id', 'primary') . '/events?singleEvents=true&orderBy=startTime&timeMin=' . urlencode($now->format('Y-m-d\TH:i:s') . sb_get_timezone_offset()) . '&privateExtendedProperty=user_id%3D' . ($user_id ? $user_id : sb_get_active_user_ID()), false, 'www', 'GET');
    return sb_is_error($response) ? $response : sb_isset(json_decode($response, true), 'items');
}

function sb_google_calendar_get_date_range($date_local = false, $max_days_from_start = false) {
    $offset = sb_get_timezone_offset();
    $timezone = new DateTimeZone($offset);
    $now = new DateTime('now', $timezone);
    $start = new DateTime($date_local, $timezone);
    $min_start = (clone $now)->modify('+' . sb_get_multi_setting('google-calendar', 'google-calendar-min-booking-time', 120) . ' minutes');
    if ($start && $start < $min_start) {
        $start = clone $min_start;
    }
    return [($start ? $start->format('Y-m-d\TH:i:s') : (new DateTime('today', $timezone))->format('Y-m-d\T00:00:00')) . $offset, ($max_days_from_start ? (clone $start)->modify('+' . ($max_days_from_start == 1 ? '0' : $max_days_from_start) . ' days')->setTime(23, 59, 59) : (new DateTime('today +' . sb_get_multi_setting('google-calendar', 'google-calendar-max-advance-booking', 60) . ' days', $timezone)))->format('Y-m-d\T23:59:59') . $offset];
}

function sb_google_calendar_get_available_slots($date_local = false, $slot_minutes = false, $max_days_from_start = false) {
    $bookings = sb_google_calendar_get_bookings($date_local);
    $range = sb_google_calendar_get_date_range($date_local, $max_days_from_start);
    $slot_minutes = $slot_minutes ? $slot_minutes : sb_get_multi_setting('google-calendar', 'google-calendar-slot-time', 30);
    $slot_minutes_string = '+' . ($slot_minutes) . ' minutes';
    $range[0] = preg_replace('/[+-]\d{2}:\d{2}$/', '', $range[0]);
    $range[1] = preg_replace('/[+-]\d{2}:\d{2}$/', '', $range[1]);
    $start = new DateTime($range[0]);
    $minutes = (int) $start->format('i');
    $seconds = (int) $start->format('s');
    $total_minutes = $minutes + $seconds / 60;
    $slots_passed = floor($total_minutes / $slot_minutes);
    if ($total_minutes > $slots_passed * $slot_minutes) {
        $slots_passed++;
    }
    $rounded_minutes = $slots_passed * $slot_minutes;
    if ($rounded_minutes >= 60) {
        $start->modify('+1 hour');
        $rounded_minutes = 0;
    }
    $start->setTime((int) $start->format('H'), $rounded_minutes, 0);
    $end = new DateTime($range[1]);
    $slots = [];
    $booked = [];
    $utc_offset = floatval(sb_utc_offset());
    $utc_offset_seconds = $utc_offset * 3600;
    $now = new DateTime(sb_gmt_now());
    $min_start = (clone $now)->modify('+' . sb_get_multi_setting('google-calendar', 'google-calendar-min-booking-time', 120) . ' minutes');
    $buffer_minutes = sb_get_multi_setting('google-calendar', 'google-calendar-buffer-time', 0);
    foreach ($bookings as $booking) {
        array_push($booked, ['start' => strtotime($booking['start']['dateTime']) - ($buffer_minutes * 60), 'end' => strtotime($booking['end']['dateTime']) + ($buffer_minutes * 60)]);
    }
    while ($start <= $end) {
        $slot_start = $start->getTimestamp();
        $slot_end = (clone $start)->modify($slot_minutes_string)->getTimestamp();
        if ($slot_start < $min_start->getTimestamp()) {
            $start->modify($slot_minutes_string);
            continue;
        }
        if (sb_office_hours($slot_start + $utc_offset_seconds, $slot_end + $utc_offset_seconds)) {
            $continue = true;
            foreach ($booked as $booking) {
                if (($slot_start + $utc_offset_seconds) < $booking['end'] && ($slot_end + $utc_offset_seconds) > $booking['start']) {
                    $continue = false;
                    break;
                }
            }
            if ($continue) {
                $local_day = date('Y-m-d', $slot_start);
                $local_time = date('H:i', $slot_start);
                if (!isset($slots[$local_day])) {
                    $slots[$local_day] = [];
                }
                array_push($slots[$local_day], $local_time);
            }
        }
        $start->modify($slot_minutes_string);
    }
    return $slots;
}

function sb_google_calendar_create_booking($start_date_local, $end_date_local, $user_id = false) {
    $offset = sb_get_timezone_offset();
    $user_id = $user_id ? $user_id : sb_get_active_user_ID();
    $data = [
        'summary' => sb_get_multi_setting('google-calendar', 'google-calendar-event-title', ''),
        'description' => sb_get_multi_setting('google-calendar', 'google-calendar-event-description', '') . PHP_EOL . PHP_EOL . sb_get_user_name(sb_get_user($user_id)) . ' - #' . $user_id . PHP_EOL . SB_URL . '/admin.php?user=' . $user_id,
        'start' => [
            'dateTime' => $start_date_local,
            'timeZone' => $offset
        ],
        'end' => [
            'dateTime' => $end_date_local,
            'timeZone' => $offset
        ],
        'extendedProperties' => ['private' => ['user_id' => $user_id, 'conversation_id' => sb_get_last_conversation_id_or_create($user_id)]]
    ];
    return sb_google_curl('calendar/v3/calendars/' . sb_get_multi_setting('google-calendar', 'google-calendar-id', 'primary') . '/events', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), 'www');
}

function sb_google_calendar_update_booking($booking_id, $start_date = false, $end_date = false, $extended_properties = false, $title = false, $description = false) {
    $offset = sb_get_timezone_offset();
    $data = [];
    if ($title) {
        $data['summary'] = $title;
    }
    if ($description) {
        $data['description'] = $description;
    }
    if ($start_date) {
        $data['start'] = ['dateTime' => $start_date, 'timeZone' => $offset];
    }
    if ($end_date) {
        $data['end'] = ['dateTime' => $end_date, 'timeZone' => $offset];
    }
    if ($extended_properties) {
        $data['extendedProperties'] = ['private' => $extended_properties];
    }
    return sb_google_curl('calendar/v3/calendars/' . sb_get_multi_setting('google-calendar', 'google-calendar-id', 'primary') . '/events/' . $booking_id, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), 'www', 'PATCH');
}
function sb_google_calendar_cancel_booking($booking_id) {
    $response = sb_google_curl('calendar/v3/calendars/' . sb_get_multi_setting('google-calendar', 'google-calendar-id', 'primary') . '/events/' . $booking_id, false, 'www', 'DELETE');
    return sb_is_error($response) ? $response : empty($response);
}

function sb_tidycal_curl($url_part, $query = false, $method = 'POST') {
    $response = sb_curl('https://tidycal.com/api/' . $url_part, $query, ['Content-Type: application/json', 'Authorization: Bearer ' . sb_get_multi_setting('tidycal', 'tidycal-access-token')], $method);
    if (isset($response['error'])) {
        return sb_error('tidycal-api-error', 'sb_tidycal_curl', isset($response['message']) ? $response['message'] : json_encode($response));
    }
    return $method == 'GET' ? json_decode($response, true) : $response;
}

function sb_tidycal_get_date_range($date_local = false, $max_days_from_start = false) {
    $offset = sb_get_timezone_offset();
    $booking_type = sb_tinycal_get_booking_type();
    $timezone = new DateTimeZone($offset);
    $now = new DateTime('now', $timezone);
    $start = new DateTime($date_local, $timezone);
    $min_start = (clone $now)->modify('+' . $booking_type['booking_threshold_minutes'] . ' minutes');
    if ($start && $start < $min_start) {
        $start = clone $min_start;
    }
    $start_utc = clone $start;
    $start_utc->setTimezone(new DateTimeZone('UTC'));
    $end_utc = $max_days_from_start ? (clone $start)->modify('+' . $max_days_from_start . ' days') : new DateTime('today +' . $booking_type['latest_availability_days'] . ' days', $timezone);
    $end_utc->setTimezone(new DateTimeZone('UTC'));
    $end_utc->setTime(23, 59, 59);
    return [$start_utc->format('Y-m-d\TH:i:s\Z'), $end_utc->format('Y-m-d\TH:i:s\Z')];
}

function sb_tidycal_get_bookings($date_local = false) {
    $date_range = sb_tidycal_get_date_range($date_local);
    $bookings = [];
    $bookings_ = [];
    $page = false;
    do {
        $response = sb_tidycal_curl('bookings?starts_at=' . urlencode($date_range[0]) . '&ends_at=' . urlencode($date_range[1]) . ($page ? '&page=' . $page : ''), false, 'GET');
        if (sb_is_error($response)) {
            return $response;
        }
        $bookings_ = array_merge($bookings_, sb_isset($response, 'data', []));
        $page = sb_isset($response, 'next_page');
    } while ($page);
    foreach ($bookings_ as $booking) {
        if (empty($booking['cancelled_at'])) {
            array_push($bookings, $booking);
        }
    }
    return $bookings;
}

function sb_tinycal_get_booking_type() {
    $booking_type_title = trim(sb_get_multi_setting('tidycal', 'tidycal-booking-type'));
    $booking_type_cache = sb_get_external_setting('tidycal-booking-type');
    if ($booking_type_cache && (!$booking_type_title || $booking_type_cache['title'] == $booking_type_title) && $booking_type_cache['sb_last_update'] > time()) {
        return $booking_type_cache;
    }
    $response = sb_tidycal_curl('booking-types', false, 'GET');
    if (sb_is_error($response)) {
        return $response;
    }
    $booking_types = sb_isset($response, 'data', []);
    $booking_type_selected = false;
    foreach ($booking_types as $booking_type) {
        if ($booking_type['title'] == $booking_type_title) {
            $booking_type_selected = $booking_type;
            break;
        }
    }
    if (!$booking_type_selected && count($booking_types)) {
        $booking_type_selected = $booking_types[0];
    }
    if ($booking_type_selected) {
        $booking_type_selected['sb_last_update'] = time() + 86400;
        sb_save_external_setting('tidycal-booking-type', $booking_type_selected);
        return $booking_type_selected;
    } else {
        return sb_error('tidycal-booking-type-not-found', 'sb_tinycal_get_booking_type', 'The Booking type title does not exist. Please check the setting.', true);
    }
}

function sb_tidycal_get_available_slots($date_local = false, $max_days_from_start = false) {
    $date_range = sb_tidycal_get_date_range($date_local, $max_days_from_start);
    $response = sb_tidycal_curl('booking-types/' . sb_tinycal_get_booking_type()['id'] . '/timeslots?starts_at=' . $date_range[0] . '&ends_at=' . $date_range[1], false, 'GET');
    return sb_is_error($response) ? $response : sb_isset($response, 'data', []);
}

function sb_tidycal_create_booking($start_date, $user = false) {
    if (!$user) {
        $user = sb_get_active_user();
    }
    $name = sb_get_user_name($user);
    $email = sb_isset($user, 'email');
    $data = [
        'starts_at' => $start_date,
        'name' => $name . ' - ID' . $user['id'],
        'email' => $email ? $email : sb_string_slug($name) . '@example.com',
        'timezone' => sb_get_timezone_offset()
    ];
    $response = sb_tidycal_curl('booking-types/' . sb_tinycal_get_booking_type()['id'] . '/bookings', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
    return sb_is_error($response) ? $response : sb_isset($response, 'data', $response);
}

function sb_tidycal_update_booking($booking_id, $start_date) {
    sb_tidycal_cancel_booking($booking_id);
    return sb_tidycal_create_booking($start_date);
}

function sb_tidycal_cancel_booking($booking_id) {
    $response = sb_tidycal_curl('bookings/' . $booking_id . '/cancel', false, 'PATCH');
    return sb_is_error($response) ? $response : !empty($response['cancelled_at']);
}

function sb_tidycal_get_user_booking($user_id = false) {
    $bookings = sb_tidycal_get_bookings();
    $bookings_user = [];
    $user = $user_id ? sb_get_user($user_id) : sb_get_active_user();
    $user_id = 'ID' . $user['id'];
    $history = [];
    foreach ($bookings as $booking) {
        if ((strpos($booking['contact']['name'], $user_id) || $booking['contact']['name'] == sb_get_user_name($user) || $booking['contact']['email'] == $user['email']) && !in_array($booking['starts_at'], $history)) {
            array_unshift($bookings_user, $booking);
            array_push($history, $booking['starts_at']);
        }
    }
    return $bookings_user;
}