<?php

/*
 * ==========================================================
 * TICKETS APP
 * ==========================================================
 *
 * Tickets app. � 2017-2025 board.support. All rights reserved.
 *
 * 1. The tickets main block that render the whole tickets panel code.
 * 2. Generate the CSS for the ticketswith values setted in the settings area
 * 3. Send ticket confirmation email
 *
 */

define('SB_TICKETS', '1.2.5');

function sb_component_tickets()
{
    sb_js_global();
    sb_css();
    sb_tickets_css();
    sb_cross_site_init();
    $css = '';
    $disable_fields = sb_get_setting('tickets-disable-features', []);
    $disable_arrows = sb_isset($disable_fields, 'tickets-arrows');
    $custom_fields = sb_get_setting('tickets-custom-fields');
    $button_name = sb_get_multi_setting('tickets-names', 'tickets-names-button');
    if ($disable_arrows) {
        $css .= ' sb-no-arrows';
    }
    if (sb_is_rtl()) {
        $css .= ' sb-rtl';
    }
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="sb-main sb-tickets sb-loading sb-load<?php echo $css ?>"
        data-height="<?php echo sb_get_setting('tickets-height') ?>"
        data-offset="<?php echo sb_get_setting('tickets-height-offset') ?>">
        <header class="user_header d-none">
            <div class="header_left">
                <h2 class="tab  sb-active" data-id="tickets-list-area">Tickets</h2>
                <h2 class="tab" data-id="sb-tickets-area">Conversations</h2>
            </div>
            <div class="header_right">
                <div class="user_profile">
                    <img class="avatar" src="<?php echo sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>"
                        alt="User" data-name="">
                    <span class="user-initials avatar_initials" style="display:none;">
                        <span class="initials avatar_name"></span>
                    </span>
                    <div class="user_info">
                        <p class="sb_name"></p>
                        <span class="user_type">User</span>
                    </div>
                </div>
                <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4.83333 24.5C4.19167 24.5 3.64256 24.2717 3.186 23.8152C2.72944 23.3586 2.50078 22.8091 2.5 22.1667V5.83333C2.5 5.19167 2.72867 4.64256 3.186 4.186C3.64333 3.72944 4.19244 3.50078 4.83333 3.5H11.8333C12.1639 3.5 12.4412 3.612 12.6652 3.836C12.8892 4.06 13.0008 4.33689 13 4.66667C12.9992 4.99644 12.8872 5.27372 12.664 5.4985C12.4408 5.72328 12.1639 5.83489 11.8333 5.83333H4.83333V22.1667H11.8333C12.1639 22.1667 12.4412 22.2787 12.6652 22.5027C12.8892 22.7267 13.0008 23.0036 13 23.3333C12.9992 23.6631 12.8872 23.9404 12.664 24.1652C12.4408 24.3899 12.1639 24.5016 11.8333 24.5H4.83333ZM19.0375 15.1667H10.6667C10.3361 15.1667 10.0592 15.0547 9.836 14.8307C9.61278 14.6067 9.50078 14.3298 9.5 14C9.49922 13.6702 9.61122 13.3933 9.836 13.1693C10.0608 12.9453 10.3377 12.8333 10.6667 12.8333H19.0375L16.85 10.6458C16.6361 10.4319 16.5292 10.1694 16.5292 9.85833C16.5292 9.54722 16.6361 9.275 16.85 9.04167C17.0639 8.80833 17.3361 8.68661 17.6667 8.6765C17.9972 8.66639 18.2792 8.77839 18.5125 9.0125L22.6833 13.1833C22.9167 13.4167 23.0333 13.6889 23.0333 14C23.0333 14.3111 22.9167 14.5833 22.6833 14.8167L18.5125 18.9875C18.2792 19.2208 18.0023 19.3328 17.6818 19.3235C17.3614 19.3142 17.0841 19.1924 16.85 18.9583C16.6361 18.725 16.5342 18.4481 16.5443 18.1277C16.5544 17.8072 16.6661 17.5397 16.8792 17.325L19.0375 15.1667Z"
                            fill="#67636D" />
                    </svg>
                </div>
            </div>
        </header>
        <div class="tickets-list-area">
            <div class="sb-panel-left">
                <div class="tickets-list">
                    <div class="sb-top p-4">
                        <div>
                            <?php if (!sb_isset($disable_fields, 'tickets-button'))
                                echo '<div class="sb-btn sb-icon sb-new-ticket"><i class="sb-icon-plus"></i>' . sb_($button_name ? $button_name : 'Create New Ticket') . '</div>';
                            else
                                echo '<div class="sb-title">' . sb_($button_name ? $button_name : 'Tickets') . '</div>';
                            ?>
                        </div>
                        <div class="sb-search-btn">
                            <i class="sb-icon sb-icon-search"></i>
                            <input type="text" autocomplete="false"
                                placeholder="<?php sb_e('Search for keywords or users...') ?>" />
                        </div>
                    </div>
                    <ul class="sb-user-tickets sb-scroll-area"
                        data-profile-image="<?php echo sb_isset($disable_fields, 'tickets-profile-image') ? 'false' : 'true' ?>">
                        <p class="p-4">
                            <?php sb_e('No results found.') ?>
                        </p>
                    </ul>
                </div>
            </div>
            <div class="sb-panel-main">
                <div class="sb-top" style="display:none">
                    <div class="sb-title sb-active"></div>
                    <a class="sb-close sb-btn-icon sb-btn-red">
                        <i class="sb-icon-close"></i>
                    </a>
                    <div class="sb-label-date-top"></div>
                </div>
                <p class="no-reords text-center d-none">No results found.</p>
                <div class="tickets-area" style="height: 100%; visibility:hidden;">
                    <div class="ticket-description-header" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <h4 class="ticket-subject mb-0"></h4>
                        <div class="text-muted small tickets-header">
                            <span class="user-name"></span> <span class="ms-1">raised this on
                            <span class="ticket-creation-time"></span></span>
                        </div>
                    </div>
                    <div class="tickets-text d-none">
                    </div>
                    <div class="mb-3 pb-0 ticket-description-container" style="padding: 15px;">
                        <p class="ticket-description"></p>
                    </div>
                    <div class="tickets-chat-area ">
                        <strong>Comments</strong>
                        <!-- Comments/Chat Section -->
                        <div id="ticket-comments" class="row mt-4 mx-0">
                            <div class="col-md-12 p-0 bg-white">
                                <div class=""
                                    style="max-height: 350px; overflow-y: auto; background: #fff; border: 1px solid #d5d5d5; border-bottom: 0;"
                                    id="comments-section">
                                    <!-- Comments will be loaded here by JS -->
                                </div>

                                <div class="d-flex align-items-center gap-2 mt-2"
                                    style="border: 1px solid #d5d5d5;background: #fff;border-radius: 14px;padding: 0 15px;min-height: 100px;">
                                    <input type="hidden" id="currentUserId"
                                        value="<?php echo sb_get_active_user()['id'] ?? 0; ?>">
                                    <textarea class="form-control me-2" id="newComment" placeholder="Type your comment..."
                                        style="border: 0; resize: none; box-shadow: none; padding: 0;"></textarea>
                                    <textarea class="form-control me-2 d-none" data-comment-id=""
                                        id="oldComment"></textarea>

                                    <button id="addComment" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sb-panel sb-scroll-area ticket-login"></div>
            </div>
            <div class="sb-panel-right">
                <div class="right-side-wrapper d-none">
                    <div class="mb-2 text-muted small user-name"></div>
                    <div class="mb-2">
                        <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path
                                d="M22.6929 9.12895C22.626 7.58687 22.4385 6.58298 21.9132 5.78884C21.611 5.33196 21.2357 4.93459 20.8041 4.61468C19.6376 3.75 17.9919 3.75 14.7007 3.75H10.686C7.39472 3.75 5.74908 3.75 4.58256 4.61468C4.15099 4.93459 3.77561 5.33196 3.47341 5.78884C2.9482 6.58289 2.7607 7.58665 2.69377 9.12843C2.68232 9.39208 2.90942 9.59375 3.15825 9.59375C4.54403 9.59375 5.66743 10.783 5.66743 12.25C5.66743 13.717 4.54403 14.9062 3.15825 14.9062C2.90942 14.9062 2.68232 15.1079 2.69377 15.3716C2.7607 16.9134 2.9482 17.9171 3.47341 18.7112C3.77561 19.168 4.15099 19.5654 4.58256 19.8853C5.74908 20.75 7.39472 20.75 10.686 20.75H14.7007C17.9919 20.75 19.6376 20.75 20.8041 19.8853C21.2357 19.5654 21.611 19.168 21.9132 18.7112C22.4385 17.917 22.626 16.9131 22.6929 15.3711V9.12895Z"
                                stroke="#5F6465" stroke-width="1.5" stroke-linejoin="round"></path>
                            <path d="M13.6934 12.25H17.6934" stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M9.69336 16.25H17.6934" stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>Ticket ID: <span class="ticket-id"></span>
                    </div>

                    <div class="mb-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-1"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M18.9809 9.25283C18.2198 7.32031 16.6794 5.77999 14.7469 5.01897C14.5229 5.27358 14.3413 5.56647 14.2133 5.88656C16.0226 6.54172 17.4581 7.97718 18.1133 9.7864C18.4334 9.6584 18.7263 9.47685 18.9809 9.25283ZM12.2276 5.50391C12.1521 5.50131 12.0762 5.5 12 5.5C8.41015 5.5 5.5 8.41015 5.5 12C5.5 15.5899 8.41015 18.5 12 18.5C15.5899 18.5 18.5 15.5899 18.5 12C18.5 11.9237 18.4987 11.8478 18.4961 11.7721C18.8387 11.6648 19.1655 11.5216 19.472 11.347C19.4905 11.5622 19.5 11.78 19.5 12C19.5 16.1421 16.1421 19.5 12 19.5C7.85786 19.5 4.5 16.1421 4.5 12C4.5 7.85786 7.85786 4.5 12 4.5C12.2199 4.5 12.4376 4.50946 12.6527 4.52801C12.4781 4.83451 12.3349 5.16128 12.2276 5.50391Z"
                                fill="#222222" />
                            <circle cx="17" cy="7" r="3" fill="#222222" />
                        </svg>
                        Status:<span class="badge bg-secondary ticket-status"></span>
                    </div>

                    <p>
                        <svg width="20" height="20" viewBox="-8 0 32 32" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">

                            <title>attachment</title>
                            <desc>Created with Sketch Beta.</desc>
                            <defs>

                            </defs>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
                                sketch:type="MSPage">
                                <g id="Icon-Set-Filled" sketch:type="MSLayerGroup"
                                    transform="translate(-214.000000, -153.000000)" fill="#000000">
                                    <path
                                        d="M228,157 L228,177 C228,180.313 225.313,183 222,183 C218.687,183 216,180.313 216,177 L216,159 C216,156.791 217.791,155 220,155 C222.209,155 224,156.791 224,159 L224,177 C224,178.104 223.104,179 222,179 C220.896,179 220,178.104 220,177 L220,161 L218,161 L218,177 C218,179.209 219.791,181 222,181 C224.209,181 226,179.209 226,177 L226,159 C226,155.687 223.313,153 220,153 C216.687,153 214,155.687 214,159 L214,178 C214.493,181.945 217.921,185 222,185 C226.079,185 229.507,181.945 230,178 L230,157 L228,157"
                                        id="attachment" sketch:type="MSShapeGroup">

                                    </path>
                                </g>
                            </g>
                        </svg>
                        Ticket Attachments:
                    </p>
                    <div class="row ticket-attachments"></div>

                    
                </div>
                <div class="sb-scroll-area">

                <?php
                        $code = '';
                        // if (!sb_isset($disable_fields, 'tickets-agent')) {
                        //     echo '<div class="sb-profile sb-profile-agent sb-profile-empty"><img src="" /><div><span class="sb-name"></span><span class="sb-status"></span></div></div>' . (sb_isset($disable_fields, 'tickets-agent-details') ? '' : '<div class="sb-agent-label"></div>');
                        // }
                        // $code .= '<div class="sb-ticket-details"></div>';
                        // if (!sb_isset($disable_fields, 'tickets-department')) {
                        //     $code .= '<div class="sb-department" data-label="' . sb_(sb_isset(sb_get_setting('departments-settings'), 'departments-single-label', 'Department')) . '"></div>';
                        // }
                        //$code .= '<div class="sb-conversation-attachments"></div>';
                        if (sb_get_setting('tickets-articles')) {
                            $code .= sb_get_rich_message('articles');
                        }
                        echo $code;

                        ?>

                </div>
            </div>
            <?php
            if (!sb_isset($disable_fields, 'tickets-left-panel') && !$disable_arrows) {
                echo '<i class="sb-btn-collapse sb-left sb-icon-arrow-left"></i>';
            }
            if (!sb_isset($disable_fields, 'tickets-right-panel') && !$disable_arrows) {
                echo '<i class="sb-btn-collapse sb-right sb-icon-arrow-right"></i>';
            }
            ?>
        </div>
        <div class="sb-tickets-area" style="visibility: hidden; opacity: 0;display:none">
            <?php if (!sb_isset($disable_fields, 'tickets-left-panel')) { ?>
                <div class="sb-panel-left">
                    <div class="conversation-list">
                        <!--div class="sb-top">
                            <div>
                                <?php /*if (!sb_isset($disable_fields, 'tickets-button'))
                     echo '<div class="sb-btn sb-icon sb-new-ticket"><i class="sb-icon-plus"></i>' . sb_($button_name ? $button_name : 'Create New Ticket') . '</div>';
                 else
                     echo '<div class="sb-title">' . sb_($button_name ? $button_name : 'Tickets') . '</div>'; */ ?>
                            </div>
                            <div class="sb-search-btn">
                                <i class="sb-icon sb-icon-search"></i>
                                <input type="text" autocomplete="false" placeholder="<?php sb_e('Search for keywords or users...') ?>" />
                            </div>
                        </div-->
                        <ul class="sb-user-conversations sb-scroll-area"
                            data-profile-image="<?php echo sb_isset($disable_fields, 'tickets-profile-image') ? 'false' : 'true' ?>">
                            <p style="padding: 15px;">
                                <?php sb_e('No results found.') ?>
                            </p>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="sb-panel-main">
                <div class="sb-top<?php echo sb_isset($disable_fields, 'tickets-top-bar') ? ' sb-top-hide' : '' ?>">
                    <?php
                    if (sb_isset($disable_fields, 'tickets-right-panel') && !sb_isset($disable_fields, 'tickets-agent')) {
                        echo '<div class="sb-profile sb-profile-agent sb-profile-empty"><img src="" /><div><span class="sb-name"></span><span class="sb-status"></span></div></div>';
                    }
                    ?>
                    <div class="sb-title"></div>
                    <a class="sb-close sb-btn-icon sb-btn-red">
                        <i class="sb-icon-close"></i>
                    </a>
                    <div class="sb-label-date-top"></div>
                </div>
                <div class="sb-conversation">
                    <div class="sb-list"></div>
                    <?php sb_component_editor(); ?>
                    <div class="sb-no-conversation-message">
                        <div>
                            <label>
                                <?php sb_e('Select a ticket or create a new one') ?>
                            </label>
                            <p>
                                <?php sb_e('Select a ticket from the left area or create a new one.') ?>
                            </p>
                        </div>
                    </div>
                    <audio id="sb-audio" preload="auto">
                        <source src="<?php echo SB_URL ?>/media/sound.mp3" type="audio/mpeg">
                    </audio>
                </div>
                <div class="sb-panel sb-scroll-area"></div>
            </div>
            <?php if (!sb_isset($disable_fields, 'tickets-right-panel')) { ?>
                <div class="sb-panel-right">
                    <div class="sb-top">
                        <?php if (sb_get_setting('tickets-registration-required')) { ?>
                            <div class="sb-profile-menu">
                                <div
                                    class="sb-profile<?php echo !sb_get_setting('registration-profile-img') || sb_get_setting('tickets-registration-required') ? ' sb-no-profile-image' : '' ?>">
                                    <img src="" />
                                    <span class="sb-name"></span>
                                </div>
                                <div>
                                    <ul class="sb-menu">
                                        <?php
                                        if (!sb_isset($disable_fields, 'tickets-edit-profile')) {
                                            echo '<li data-value="edit-profile">' . sb_('Edit profile') . '</li>';
                                        }
                                        if (!sb_get_setting('tickets-registration-disable-password')) {
                                            echo '<li data-value="logout">' . sb_('Logout') . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo '<div class="sb-title">' . sb_('Details') . '</div>';
                        }
                        ?>
                    </div>
                    <div class="sb-scroll-area">
                        <?php
                        $code = '';
                        if (!sb_isset($disable_fields, 'tickets-agent')) {
                            echo '<div class="sb-profile sb-profile-agent sb-profile-empty"><img src="" /><div><span class="sb-name"></span><span class="sb-status"></span></div></div>' . (sb_isset($disable_fields, 'tickets-agent-details') ? '' : '<div class="sb-agent-label"></div>');
                        }
                        $code .= '<div class="sb-ticket-details"></div>';
                        if (!sb_isset($disable_fields, 'tickets-department')) {
                            $code .= '<div class="sb-department" data-label="' . sb_(sb_isset(sb_get_setting('departments-settings'), 'departments-single-label', 'Department')) . '"></div>';
                        }
                        $code .= '<div class="sb-conversation-attachments"></div>';
                        if (sb_get_setting('tickets-articles')) {
                            $code .= sb_get_rich_message('articles');
                        }
                        echo $code;

                        ?>
                    </div>
                    <div class="sb-no-conversation-message"></div>
                </div>
                <?php
            }
            if (!sb_isset($disable_fields, 'tickets-left-panel') && !$disable_arrows) {
                echo '<i class="sb-btn-collapse sb-left sb-icon-arrow-left"></i>';
            }
            if (!sb_isset($disable_fields, 'tickets-right-panel') && !$disable_arrows) {
                echo '<i class="sb-btn-collapse sb-right sb-icon-arrow-right"></i>';
            }
            ?>
        </div>

        <div class="sb-lightbox sb-lightbox-media">
            <div></div>
            <i class="sb-icon-close"></i>
        </div>
        <div class="sb-lightbox-overlay"></div>
        <div class="sb-ticket-fields">
            <?php
            $code = '';
            if (sb_get_multi_setting('tickets-fields', 'tickets-field-departments')) {
                $departments = sb_get_departments();

                ?>
                <div id="department_id" data-type="select" class="sb-input">
                    <span>Department</span>
                    <select>
                        <option value=""><?php echo sb_(
                            "Select Department"
                        ); ?></option>
                        <?php
                        foreach ($departments as $key => $value) {
                            echo '<option value="' . $key . '">' . sb_($value["name"]) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
            }
            if (sb_get_multi_setting('tickets-fields', 'tickets-field-priority')) {
                function sb_get_priorities()
                {
                    $priorities = sb_db_get(
                        "SELECT * FROM priorities",
                        false
                    );
                    return $priorities;
                }

                $priorities = sb_get_priorities();
                ?>
                <div id="priority_id" data-type="select" class="sb-input">
                    <span class="required-label">Priority</span>
                    <select required>
                        <option value="">Select Priority</option>
                        <?php
                        foreach ($priorities as $key => $value) {
                            echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
            }
            if (sb_get_multi_setting('wc-tickets-products', 'wc-tickets-products-active')) {
                $products = sb_woocommerce_get_products([], false, sb_get_user_language());
                $code .= '<div id="products" class="sb-input sb-input-select"><span>' . sb_(sb_get_multi_setting('wc-tickets-products', 'wc-tickets-products-label', 'Related product')) . '</span><div class="sb-select"><p data-value="" data-required="true">' . sb_('Select a product') . '</p><ul class="sb-scroll-area">';
                $exclude = explode(',', sb_get_multi_setting('wc-tickets-products', 'wc-tickets-products-exclude'));
                for ($i = 0; $i < count($products); $i++) {
                    if (!in_array($products[$i]['id'], $exclude)) {
                        $name = $products[$i]['name'];
                        $code .= '<li data-value="' . $name . '">' . $name . '</li>';
                    }
                }
                $code .= '</ul></div></div>';
            }
            if ($custom_fields && is_array($custom_fields)) {
                for ($i = 0; $i < count($custom_fields); $i++) {
                    $value = $custom_fields[$i];
                    if ($value['tickets-extra-field-name']) {
                        $code .= '<div id="' . sb_string_slug($value['tickets-extra-field-name']) . '" class="sb-input sb-input-text"><span>' . sb_($value['tickets-extra-field-name']) . '</span><input type="text"' . (sb_isset($value, 'tickets-extra-field-required') ? ' required' : '') . '></div>';
                    }
                }
            }
            echo $code;
            ?>
        </div>
        <div class="ticket-custom-fields" style="display:none">
            <?php 
            
            $fields = get_ticket_custom_fields();

            if (!empty($fields) && is_array($fields)) {
                foreach ($fields as $field) {
                    echo '<div id="' . htmlspecialchars($field['title']) . '" data-type="' . htmlspecialchars($field['type']) . '" class="sb-input">';
                    
                    // Label
                    echo '<span class="' . ($field['required'] == '1' ? 'required-label' : '') . '">' . htmlspecialchars($field['title']) . '</span>';

                    switch ($field['type']) {
                        case 'text':
                            echo '<input type="text" class="form-control" id="custom_' . $field['id'] . '" data-id="custom_' . $field['id'] . '" 
                                    name="custom_fields[' . $field['id'] . ']" 
                                    value="' . htmlspecialchars($field['default_value']) . '" 
                                    ' . ($field['required'] == '1' ? 'required' : '') . ' 
                                    placeholder="' . htmlspecialchars($field['title']) . '">';
                            break;

                        case 'textarea':
                            echo '<textarea class="form-control" id="custom_' . $field['id'] . '" data-id="custom_' . $field['id'] . '" 
                                    name="custom_fields[' . $field['id'] . ']" 
                                    rows="3" ' . ($field['required'] == '1' ? 'required' : '') . ' 
                                    placeholder="' . htmlspecialchars($field['title']) . '">' . htmlspecialchars($field['default_value']) . '</textarea>';
                            break;

                        case 'select':
                            $options = array_filter(array_map('trim', explode('|', $field['options'])));
                            echo '<select class="form-control" id="custom_' . $field['id'] . '" data-id="custom_' . $field['id'] . '" 
                                    name="custom_fields[' . $field['id'] . ']" 
                                    ' . ($field['required'] == '1' ? 'required' : '') . '>';
                            echo '<option value="">Select ' . htmlspecialchars($field['title']) . '</option>';
                            foreach ($options as $option) {
                                $selected = ($option === $field['default_value']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
                            }
                            echo '</select>';
                            break;

                        case 'checkbox':
                            $checked = ($field['default_value'] == '1') ? 'checked' : '';
                            echo '<div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="custom_' . $field['id'] . '" data-id="custom_' . $field['id'] . '" 
                                        name="custom_fields[' . $field['id'] . ']" 
                                        value="1" 
                                        ' . $checked . ' 
                                        ' . ($field['required'] == '1' ? 'required' : '') . '>
                                    <label class="form-check-label" for="custom_' . $field['id'] . '">' . htmlspecialchars($field['title']) . '</label>
                                </div>';
                            break;
                    }

                    echo '</div>'; // close sb-input
                }
            }
            ?>
        </div>
    </div>
<?php }

function sb_tickets_css()
{
    $css = '';
    $color_1 = sb_get_setting('color-1');
    if ($color_1 != '') {
        $css .= '.sb-tickets .sb-panel-right .sb-input.sb-input-btn>div:hover, .sb-tickets .sb-panel-right .sb-input.sb-input-btn input:focus+div,.sb-tickets .sb-top .sb-btn:hover, .sb-tickets .sb-create-ticket:hover, .sb-tickets .sb-panel-right .sb-btn:hover { background-color: ' . $color_1 . '; border-color: ' . $color_1 . '; }';
        $css .= '.sb-tickets .sb-ticket-details>div .sb-icon,.sb-btn-collapse:hover,.sb-profile-menu:hover .sb-name,.sb-tickets .sb-conversation-attachments a i { color: ' . $color_1 . '; }';
        $css .= '.sb-user-conversations>li.sb-active{ border-left-color: ' . $color_1 . '; }';
        $css .= '.sb-search-btn>input:focus,[data-panel="new-ticket"] .sb-editor.sb-focus { border-color: ' . $color_1 . '; }';
        $css .= '.sb-btn-icon:hover { border-color: ' . $color_1 . '; color: ' . $color_1 . '; }';
    }

    /* Timeline chat style for comments */
    $css .= '
	.comment-row {
		display: flex;
		align-items: flex-end;
	}

	.comment-row.customer+.comment-row.customer {
		margin-top: 15px;
	}

	.comment-row.agent+.comment-row.agent {
		margin-top: 15px;
	}

	.comment-row.customer+.comment-row.agent {
		margin-top: 5px;
	}

	.comment-row.agent+.comment-row.customer {
		margin-top: 5px;
	}

	.comment-row.customer {
		flex-direction: row-reverse;
	}

	.comment-avatar {
		width: 36px;
		height: 36px;
		border-radius: 50%;
		background: #e0e0e0;
		object-fit: cover;
		margin: 0 8px;
	}

	.comment-bubble {
		max-width: 70%;
		padding: 5px 10px;
		border-radius: 18px;
		position: relative;
		font-size: 15px;
		word-break: break-word;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
	}

	.comment-row.agent .comment-bubble {
		background: #f8f8f8;
		color: #222;
		border-bottom-right-radius: 6px;
		border-bottom-left-radius: 18px;
		margin-left: 10px;
		border: 1px solid #e0e0e0;
	}

	.comment-row.customer .comment-bubble {
		background: #f8f8f8;
		color: #222;
		border-bottom-left-radius: 6px;
		border-bottom-right-radius: 18px;
		margin-right: 10px;
		border: 1px solid #e0e0e0;
	}

	.comment-meta {
		position: relative;
		font-size: 9px;
		color: #222;
		display: flex;
		justify-content: flex-end;
		align-items: center;
		gap: 6px;
		width: 100%;
	}

	.edited-label {
		font-size: 10px;
		color: #b0b0b0;
		margin-left: 4px;
		vertical-align: middle;
		opacity: 0.7;
	}


	.edit-comment-btn,
	.delete-comment-btn {
		font-size: 12px;
		color: #ffc107;
		background: none;
		border: none;
		cursor: pointer;
		padding: 0;
	}

	.comment-text {
		padding-right: 30px;
		line-height: 18px;
		font-size: 13px;
	}

	.comment-row
	{
		line-height: 36px;
		font-size: 15px;
		margin: 0 8px;
	}
    .user-name {
        text-transform: capitalize;
    }
    .user-initials {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #ccc;
        color: #fff;
        font-weight: bold;
        font-size: 16px;
        text-align: center;
        line-height: 50px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }
    .user_header .user-initials {
        width: 45px;
        height: 45px;
        line-height: 45px;
        position: absolute;
        left: 0;
    }
    .user_profile{position: relative;padding-left: 58px;}
    ';

    if ($css != '') {
        echo '<style>' . $css . '</style>';
    }
}

function sb_tickets_email($user, $message = false, $attachments = false, $conversation_id = false)
{
    if (empty($message) && empty($attachments)) {
        return false;
    }
    $user_email = sb_isset($user, 'email');
    $email = sb_get_multilingual_setting('emails', 'tickets-email', sb_get_user_language($user['id']));
    if ($user_email && !empty($email['tickets-email-subject'])) {
        $body = str_replace(['{user_name}', '{message}', '{attachments}', '{conversation_id}'], [sb_get_user_name($user), $message, sb_email_attachments_code($attachments), $conversation_id], $email['tickets-email-content']);
        if ($conversation_id && $user['token']) {
            $body = str_replace('{conversation_url_parameter}', '?conversation=' . $conversation_id . '&token=' . $user['token'], $body);
        }
        return sb_email_send($user_email, str_replace('{conversation_id}', $conversation_id, sb_merge_fields($email['tickets-email-subject'])), $body, sb_email_piping_suffix($conversation_id));
    }
    return false;
}

function sb_tickets_recaptcha($token)
{
    return sb_isset(sb_curl('https://www.google.com/recaptcha/api/siteverify', ['response' => $token, 'secret' => sb_get_multi_setting('tickets-recaptcha', 'tickets-recaptcha-secret')]), 'success');
}

?>