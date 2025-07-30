<?php

/*
 * ==========================================================
 * COMPONENTS.PHP
 * ==========================================================
 *
 * Library of static html components for the admin area. This file must not be executed directly. � 2017-2025 board.support. All rights reserved.
 *
 */

function sb_profile_box()
{
    ?>
    <div class="sb-profile-box sb-lightbox">
        <div class="sb-top-bar">
            <div class="sb-profile">
                <img src="<?php echo SB_URL; ?>/media/user.svg" data-name="" />
                <div class="user-initials" data-value="edit-profile" style="display: none">
                    <span class="initials"></span>
                </div>
                <span class="sb-name"></span>
            </div>
            <div>
                <a data-value="custom_email" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                    "Send email"
                ); ?>">
                    <i class="sb-icon-envelope"></i>
                </a>
                <?php
                if (sb_get_multi_setting("sms", "sms-user")) {
                    echo '<a data-value="sms" class="sb-btn-icon" data-sb-tooltip="' .
                        sb_("Send text message") .
                        '"><i class="sb-icon-sms"></i></a>';
                }
                if (
                    defined("SB_WHATSAPP") &&
                    (!function_exists("sb_whatsapp_active") ||
                        sb_whatsapp_active())
                ) {
                    echo '<a data-value="whatsapp" class="sb-btn-icon" data-sb-tooltip="' .
                        sb_("Send a WhatsApp message template") .
                        '"><i class="sb-icon-social-wa"></i></a>'; // Deprecated: remove function_exists('sb_whatsapp_active')
                }
                if (
                    (sb_is_agent(false, true, true) && !sb_supervisor()) ||
                    sb_get_multi_setting("agents", "agents-edit-user") ||
                    (sb_supervisor() &&
                        sb_get_multi_setting(
                            "supervisor",
                            "supervisor-edit-user"
                        ))
                ) {
                    echo ' <a class="sb-edit sb-btn sb-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-edit-area"><i class="sb-icon-user"></i>' .
                        sb_("Edit user") .
                        "</a>";
                }
                ?>
                <a class="sb-start-conversation sb-btn sb-icon">
                    <i class="sb-icon-message"></i>
                    <?php sb_e("Start a conversation"); ?>
                </a>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area"
                    data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div>
                <div class="sb-title">
                    <?php sb_e("Details"); ?>
                </div>
                <div class="sb-profile-list"></div>
                <div class="sb-agent-area"></div>
            </div>
            <div>
                <div class="sb-title">
                    <?php sb_e("User conversations"); ?>
                </div>
                <ul class="sb-user-conversations"></ul>
            </div>
        </div>
    </div>
    <?php
} ?>
<?php function sb_profile_edit_box()
{
    ?>
    <div class="sb-profile-edit-box sb-lightbox">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div class="sb-profile">
                <img src="<?php echo SB_URL; ?>/media/user.svg" />
                <span class="sb-name"></span>
            </div>
            <div>
                <a class="sb-save sb-btn sb-icon">
                    <i class="sb-icon-check"></i>
                    <?php sb_e("Save changes"); ?>
                </a>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area"
                    data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-details">
                <div class="sb-title">
                    <?php sb_e("Edit details"); ?>
                </div>
                <div class="sb-edit-box">
                    <div id="profile_image" data-type="image" class="sb-input sb-input-image sb-profile-image">
                        <span>
                            <?php sb_e("Profile image"); ?>
                        </span>
                        <div class="image">
                            <div class="sb-icon-close"></div>
                        </div>
                    </div>
                    <div id="user_type" data-type="select" class="sb-input sb-input-select">
                        <span>
                            <?php sb_e("Type"); ?>
                        </span>
                        <select>
                            <option value="agent">
                                <?php sb_e("Agent"); ?>
                            </option>
                            <option value="admin">
                                <?php sb_e("Admin"); ?>
                            </option>
                        </select>
                    </div>
                    <?php sb_departments("select"); ?>
                    <div id="first_name" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e("First name"); ?>
                        </span>
                        <input type="text" required />
                    </div>
                    <div id="last_name" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e("Last name"); ?>
                        </span>
                        <input type="text" />
                    </div>
                    <div id="password" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e("Password"); ?>
                        </span>
                        <input type="password" />
                    </div>
                    <div id="email" data-type="email" class="sb-input">
                        <span class="required-label">
                            <?php sb_e("Email"); ?>
                        </span>
                        <input type="email" />
                    </div>
                </div>
                <a class="sb-delete sb-btn-text sb-btn-red">
                    <i class="sb-icon-delete"></i>
                    <?php sb_e("Delete user"); ?>
                </a>
            </div>
            <div class="sb-additional-details">
                <div class="sb-title">
                    <?php sb_e("Edit additional details"); ?>
                </div>
                <div class="sb-edit-box">
                    <?php
                    $code = "";
                    $fields = sb_users_get_fields();
                    foreach ($fields as $field) {
                        $id = $field["id"];
                        $type =
                            $id == "country" || $id == "language"
                            ? "select"
                            : ($id == "birthdate"
                                ? "date"
                                : "text");
                        $code .=
                            '<div id="' .
                            $id .
                            '" data-type="' .
                            $type .
                            '" class="sb-input"><span>' .
                            sb_($field["name"]) .
                            "</span>";
                        if ($type == "date" || $type == "text") {
                            $code .= '<input type="' . $type . '" />';
                        } elseif ($id == "country") {
                            $code .= sb_select_html("countries");
                        } elseif ($id == "language") {
                            $code .= sb_select_html("languages");
                        }
                        $code .= "</div>";
                    }
                    echo $code; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
} ?>
<?php
function sb_ticket_box()
{
    ?>
    <div class="sb-lightbox">
        <div class="sb-top-bar">

            <div>

                <?php if (
                    (sb_is_agent(false, true, true) && !sb_supervisor()) ||
                    sb_get_multi_setting("agents", "agents-edit-user") ||
                    (sb_supervisor() &&
                        sb_get_multi_setting(
                            "supervisor",
                            "supervisor-edit-user"
                        ))
                ) {
                    echo ' <a class="sb-edit sb-btn sb-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-edit-area"><i class="sb-icon-user"></i>' .
                        sb_("Edit user") .
                        "</a>";
                } ?>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area"
                    data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div>
                <div class="sb-title">
                    <?php sb_e("Details"); ?>
                </div>
                <div class="sb-ticket-list"></div>
            </div>
        </div>
    </div>
    <?php
}
function sb_ticket_edit_box()
{
    ?>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <div class="sb-ticket-edit-box sb-lightbox">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div class="sb-ticket">
                <span class="sb-name"></span>
                <?php /*
       <div id="without_contact" data-type="checkbox" class="sb-input" style="font-size: 13px;display:none">
           <label class="ml-4">Guest Ticket</label>
           <div class="form-check form-switch mb-0 ml-2">
               <input class="form-check-input" name="without_contact" type="checkbox" role="switch"
                   id="flexSwitchCheckDefault" style="width: 27px;">
           </div>
       </div>*/
                ?>
            </div>
            <div>
                <div id="conversation_id_div" data-type="checkbox" class="sb-input mr-4 d-none" style="font-size: 13px;">
                    <label class="ml-4">Coversation ID <span>#45</span></label>
                </div>
                <a class="sb-save sb-btn sb-icon">
                    <i class="sb-icon-check"></i>
                    <?php sb_e("Save changes"); ?>
                </a>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area"
                    data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="first-section">
                <div class="sb-details">
                    <div class="sb-edit-box">
                        <div id="subject" data-type="text" class="sb-input">
                            <span class="required-label"><?php sb_e(
                                "Subject"
                            ); ?></span>
                            <input type="text" name="subject" required />
                        </div>

                        <!--div id="without_contact" data-type="checkbox" class="sb-input">
                            <span><?php // sb_e('Guest Ticket')
                                ?></span>
                            <input type="checkbox" name="without_contact" value="1" />
                        </div-->

                        <div id="contact_id" data-type="select" class="sb-input">
                            <span class="required-label pb-2"><?php sb_e(
                                "Customer"
                            ); ?></span>
                            <select id="select-customer" style="width:100%;"></select>
                        </div>

                        <div class="sb-input two-divs d-flex">
                            <div id="cust_name" data-type="text" class="sb-input">
                                <span class="required-label"><?php sb_e(
                                    "Name"
                                ); ?></span>
                                <input type="text" name="name" value="" disabled="">
                            </div>
                            <div id="cust_email" data-type="text" class="sb-input">
                                <span class="required-label"><?php sb_e(
                                    "Email"
                                ); ?></span>
                                <input type="email" name="email" value="">
                            </div>
                        </div>

                        <!--div id="cust_name" data-type="text" class="sb-input" >
                            <span class="required-label"><?php sb_e(
                                "Name"
                            ); ?></span>
                            <input type="text" name="name" required value="" disabled />
                        </div>

                        <div id="cust_email" data-type="text" class="sb-input" >
                            <span class="required-label"><?php sb_e(
                                "Email"
                            ); ?></span>
                            <input type="email" name="email" required value="" disabled />
                        </div-->

                        <div id="assigned_to" data-type="select" class="sb-input">
                            <span class="mb-2"><?php sb_e(
                                "Assigned To"
                            ); ?></span>
                            <div class="">
                                <select id="select-agent" style="width:100%;"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sb-additional-details">
                    <div class="sb-edit-box">
                        <!--div id="service_id" data-type="select" class="sb-input">
                            <span><?php sb_e("Service"); ?></span>
                            <select>
                                <option value=""><?php sb_e(
                                    "Select Service"
                                ); ?></option>
                                <option value="1" selected="selected">Hardware Issue Fixing</option>
                                <option value="2">Network issue fix</option>
                                <option value="3">Software Development</option>
                            </select>
                        </div-->

                        <?php
                        $tags = sb_get_multi_setting("disable", "disable-tags")
                            ? []
                            : sb_get_setting("tags", []);
                        $tagsHtml = "";
                        $count = count($tags);
                        if ($count > 0) { ?>
                            <div id="tags-div" data-type="select" class="sb-input">
                                <span><?php sb_e("Tags"); ?></span>
                                <select id="ticket-tags" name="tags[]" multiple>
                                    <?php
                                    for ($i = 0; $i < $count; $i++) {
                                        $tagsHtml .=
                                            '<option value="' .
                                            $tags[$i]["tag-name"] .
                                            '"  class="tag-option" data-color="' .
                                            $tags[$i]["tag-color"] .
                                            '" data-custom-properties={"color":"' .
                                            $tags[$i]["tag-color"] .
                                            '"}>' .
                                            $tags[$i]["tag-name"] .
                                            "</option>";
                                    }
                                    echo $tagsHtml;
                                    ?>
                                </select>
                                <!--select id="ticket-tags" name="tags[]" multiple>
                                    <option value="1" class="tag-option" data-color="#1976d2" data-custom-properties='{"color":"#1976d2"}'>Feature Request</option>
                                </select-->
                            </div>
                        <?php }
                        ?>

                        <?php
                        $department_settings = sb_get_setting(
                            "departments-settings"
                        );
                        $departments = sb_get_departments();
                        if (
                            isset(
                            $department_settings["departments-show-list"]
                        ) &&
                            $department_settings["departments-show-list"] ==
                            1 &&
                            !empty($departments)
                        ) { ?>
                            <div id="department_id" data-type="select" class="sb-input">
                                <span>Department</span>
                                <select>
                                    <option value=""><?php echo sb_(
                                        "Select Department"
                                    ); ?></option>
                                    <?php
                                    $code = "";
                                    foreach ($departments as $key => $value) {
                                        $code .=
                                            '<option value="' .
                                            $key .
                                            '">' .
                                            sb_($value["name"]) .
                                            "</option>";
                                    }
                                    echo $code;
                                    ?>
                                </select>
                            </div>
                        <?php }
                        ?>

                        <!--div id="cc" data-type="select" class="sb-input">
                            <span><?php sb_e("CC"); ?></span>
                            <select>
                                <option value="1">System Admin</option>
                            </select>
                        </div-->


                        <?php // { //     $priorities = sb_db_get('SELECT * FROM priorities', false); // { //     $status = sb_db_get('SELECT * FROM ticket_status', false);
                        

                            // function sb_get_priorities()
                            //     return $priorities;
                            // }
                            // function sb_get_statues()
                            //     return $status;
                            // }
                            $statues = sb_get_statues();
                            $priorities = sb_get_priorities();
                            ?>
                        <div class="sb-input two-divs d-flex">
                            <div id="status_id" data-type="select" class="sb-input">
                                <span class="required-label"><?php sb_e(
                                    "Status"
                                ); ?></span>
                                <select required>
                                    <option value="">Select Status</option>
                                    <?php foreach ($statues as $key => $value) {
                                        echo '<option value="' .
                                            $value["id"] .
                                            '">' .
                                            $value["name"] .
                                            "</option>";
                                    } ?>
                                </select>
                            </div>
                            <div id="priority_id" data-type="select" class="sb-input">
                                <span class="required-label"><?php sb_e(
                                    "Priority"
                                ); ?></span>
                                <select required>
                                    <option value=""><?php sb_e(
                                        "Select Priority"
                                    ); ?></option>
                                    <?php foreach (
                                        $priorities
                                        as $key => $value
                                    ) {
                                        echo '<option value="' .
                                            $value["id"] .
                                            '">' .
                                            $value["name"] .
                                            "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!--div data-type="file" class="sb-input">
                            <span><?php sb_e("Attachments"); ?></span>
                            <input type="file" name="attachments[]" multiple />
                            <div id="file-preview"></div>
                        </div-->
                    </div>
                </div>
            </div>
            <div id="description" class="description" data-type="textarea" style="margin: 10px 0 0 0;display: block;">

                <div style="display: inline-block;padding:0;width:100%;">
                    <div id="ticketdescription" style="height: 180px;"></div>
                </div>
                <input id="ticket_id" type="hidden" name="ticket_id" />
                <input id="conversation_id" type="hidden" name="conversation_id" />
                <!-- Hidden input to store uploaded file data -->
                <input type="hidden" id="uploaded_files1" name="uploaded_files" value="">
                <input type="hidden" id="new_user" name="new_user" value="0">
            </div>
            <div id="ticketCustomFieldsContainer" class="custom-field" style="margin: 10px 0 0 0;"></div>
            <!-- File Attachments Section -->
            <div id="ticketFileAttachments" style="margin: 10px 0 0 0;">
                <div>
                    <span class="d-block mb-2">Attachments</span>
                    <div class="custom-file">
                        <input type="file" class="form-control d-block" style="width:96%;" id="ticket-attachments1"
                            multiple>
                        <small class="form-text text-muted mt-2" style="display:block">You can select multiple files.
                            Maximum file size: 5MB. Allowed file types are .jpeg, .png, .pdf</small>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <!-- Upload Progress -->
                <div class="progress mt-2 d-none" id="upload-progress-container1">
                    <div class="progress-bar" id="upload-progress1" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                        aria-valuemax="100"></div>
                </div>

                <!-- Existing File Preview Container -->
                <div class="mt-2 d-none" id="existing-file-preview-container1">
                    <span>Current Attachments</span>
                    <div class="row" id="current-attachments1"></div>
                </div>

                <!-- File Preview Container -->
                <div class="mt-2">
                    <span class="mb-2 d-block">New Attachments</span>
                    <div class="mt-2" id="file-preview-container1">
                        <div class="row" id="file-preview-list1"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <style>
        #ticketCustomFieldsContainer,
        #ticketFileAttachments-detail,
        .first-section {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .custom-field span {
            padding-bottom: 6px;
        }

        #ticketCustomFieldsContainer>.sb-input,
        #ticketFileAttachments-detail>div,
        .first-section>div {
            flex: 0 0 calc(50% - 10px);
            /* 2 columns with spacing */
            box-sizing: border-box;
        }

        #ticketCustomFieldsContainer .sb-input {
            margin-top: 0
        }

        .required-label::after {
            content: " *";
            color: red;
        }

        #tickets-custom-fields .custom-fields-table {
            color: #566069;
            font-size: 14px;
            margin: 10px 0 0 0;
            text-align: center;
        }

        #tickets-custom-fields .custom-fields-table th,
        #tickets-custom-fields .custom-fields-table td {
            padding: 6px 12px;
            /* Increase horizontal padding */
            text-align: center;
        }

        #tickets-custom-fields .sb-new-ticket-custom-field {
            height: 30px;
            line-height: 30px;
            padding: 0 8px 0 25px;
            margin-left: 13px;
        }

        .sb-table-tickets tr {
            line-height: 25px;
        }

        span.left-sec {
            width: 15%;
        }

        div.right-sec {
            width: 100%;
            padding: 0;
        }

        table.table-striped tr {
            vertical-align: middle;
        }

        #file-preview-list .col-md-2,
        #file-preview-list1 .col-md-2,
        #current-attachments .col-md-2,
        #current-attachments1 .col-md-2 {
            padding: 0
        }

        #file-preview-list .card,
        #file-preview-list1 .card,
        #current-attachments .card,
        #current-attachments1 .card {
            margin: 6px;
            height: 100%;
        }

        #file-preview-list .card-body,
        #file-preview-list1 .card-body,
        #current-attachments .card-body,
        #current-attachments1 .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* align-items: center; */
            height: 100%;
        }

        .two-divs>div {
            width: 48% !important;
            display: inline-block;
            padding: 0 0 0 0;
            margin: 0 !important;
        }

        .custom-file #ticket-attachments {
            font-size: 12px;
        }

        .first-section .sb-input,
        #ticketCustomFieldsContainer .sb-input {
            display: block;
        }

        select[multiple] {
            width: 100%;
            height: auto;
            max-height: 78px;
            padding: 4px 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
            font-size: 16px;
            color: #333;
            outline: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: border 0.3s, box-shadow 0.3s;
            appearance: none;
            /* Remove default arrow in some browsers */
        }

        select[multiple]:focus {
            border-color: #5b9bd5;
            box-shadow: 0 0 0 3px rgba(91, 155, 213, 0.3);
        }

        select[multiple] option {
            padding: 5px 10px;
            margin: 1px 0;
            border-radius: 4px;
            transition: background 0.2s;
        }

        select[multiple] option:hover {
            background-color: #e6f0ff;
        }

        select[multiple] option:checked {
            background-color: #cce0ff;
            color: #003366;
            font-weight: 600;
        }

        span.select2-selection.select2-selection--single {
            height: 42px;
        }


        .user-initials,
        .assignee-img {
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

        .comment-row .user-initials,
        .sidepanel .user-initials,
        .sidepanel .assignee-img {
            width: 36px;
            height: 36px;
            line-height: 36px;
            font-size: 15px;
            margin: 0 8px;
        }

        .sidepanel .user-initials,
        .sidepanel .assignee-img {
            margin: 0
        }

        .initials {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .sb-scroll-area .user-initials,
        .sb-top-bar .user-initials {
            width: 45px;
            height: 45px;
            line-height: 45px;
            position: absolute;
            left: 0;
        }

        .recent-messages .user-initials {
            width: 45px;
            height: 45px;
            line-height: 45px;
            min-width: 45px;
            min-height: 45px;
        }

        .sb-user-details .user-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 13px;
            line-height: 40px;
            position: absolute;
            margin: 0 10px 0 0;
            left: 0;
        }

        .sb-profile-box .sb-top-bar .sb-profile .initials {
            margin-left: 0px;
            font-size: 16px;
            line-height: 45px;
        }

        /* .sb-td-tags span {
                                                                            margin: 3px 5px 0 0;
                                                                            padding: .45em .75em;
                                                                            font-size: 13px
                                                                        } */

        .sb_table_new tbody td.sb-td-tags {
            white-space: unset;
            text-overflow: unset;
        }

        .tsubject input {
            border: none;
            padding: 7px 0 6px 7px;
            font-size: 16px;
            width: 91%;
            cursor: pointer;
            font-weight: 600;
            border-radius: .25rem
        }

        .tsubject input:hover,
        .tsubject input.active {
            background-color: rgb(233 233 234);
        }

        .tsubject input.active {
            border: 1px solid rgb(212, 212, 212);
            ;
            background-color: rgb(248, 248, 249);
        }

        .status-dropdown .dropdown-menu li .dropdown-item {
            width: unset
        }


        /*********  Statuses list CSS ***********/
        /* .status-dropdown {
                                                                            position: relative;
                                                                            overflow: visible !important;
            
                                                                        }

                                                                        .status-btn {
                                                                            border: none;
                                                                            border-radius: 8px;
                                                                            padding: 3px 12px;
                                                                            font-weight: 500;
                                                                            cursor: pointer;
                                                                            display: flex;
                                                                            align-items: center;
                                                                            gap: 4px;
                                                                            font-size: 14px;
                                                                        }

                                                                        .status-list li,
                                                                        .priority-list li {
                                                                            font-size: 14px;
                                                                        }

                                                                        .status-dot {
                                                                            width: 10px !important;
                                                                            height: 10px !important;
                                                                        }

                                                                        .arrow {
                                                                            font-size: 12px;
                                                                        }

                                                                        .status-list,
                                                                        .priority-list {
                                                                            display: none;
                                                                            position: absolute;
                                                                            top: 75%;
                                                                            left: 0;
                                                                            min-width: 170px;
                                                                            background: #fff;
                                                                            border: 1px solid #ddd;
                                                                            border-radius: 8px;
                                                                            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.10);
                                                                            z-index: 9999;
                                                                            padding: 6px 0;
                                                                            margin: 0;
                                                                            list-style: none;
                                                                        }

                                                                        .status-list li,
                                                                        .priority-list li {
                                                                            padding: 8px 16px;
                                                                            cursor: pointer;
                                                                            display: flex;
                                                                            align-items: center;
                                                                            gap: 4px;
                                                                            font-size: 15px;
                                                                            transition: background 0.15s;
                                                                        }

                                                                        .status-list li:hover,
                                                                        .priority-list li:hover {
                                                                            background: #f5f5f5;
                                                                        }

                                                                        .status-dot {
                                                                            width: 12px;
                                                                            height: 12px;
                                                                            border-radius: 50%;
                                                                            display: inline-block;
                                                                        } */
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tagsElement = document.getElementById('ticket-tags');
            if (tagsElement) {
                const choices = new Choices(tagsElement, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Select tags...',
                    allowHTML: true,
                    itemSelectText: '',
                    callbackOnCreateTemplates: function (template) {
                        return {
                            item: (classNames, data) => {
                                const color = data.customProperties && data.customProperties.color ? data.customProperties.color : '';
                                return template(`
                                    <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable} ${data.placeholder ? classNames.placeholder : ''}"
                                         data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${data.disabled ? 'aria-disabled="true"' : ''} data-color1="${color}"  style="border: 1px solid ${color};">
                                        <span class="tag-dot" style="background-color:${color}"></span>
                                        ${data.label}
                                        <button type="button" class="choices__button" aria-label="Remove item: ${data.value}" data-button><i class="fa-solid fa-xmark choice-remove"></i></button>
                                    </div>
                                `);
                            }
                        };
                    }
                });


                refreshTagDots();
                tagsElement.addEventListener('change', refreshTagDots);
                // Listen for any DOM changes in the choices list (item removed/added)
                const choicesList = document.querySelector('.choices__list--dropdown');
                if (choicesList) {
                    const observer = new MutationObserver(() => {
                        refreshTagDots();
                    });
                    observer.observe(choicesList, {
                        childList: true,
                        subtree: true
                    });
                }
                document.querySelector('.choices').addEventListener('click', function () {
                    setTimeout(refreshTagDots, 10);
                });
            }



            // Code to display applied tags in a palette
            // const paletteContainer = document.getElementById('selected-tags-palette');
            // document.getElementById('tags-filter').addEventListener('change', function () {
            // const selected = tagsFilterChoices.getValue();
            //     const selectedValues = $(this).val(); // Gets array of selected values
            //     paletteContainer.innerHTML = ''; // clear old circles
            //     selected.forEach(item => {
            //         const color = item.customProperties?.color || '#ccc';
            //         const li = document.createElement('div');
            //         li.className = 'is-selected';
            //         li.setAttribute('data-item', '');
            //         li.setAttribute('data-id', item.id);
            //         li.setAttribute('data-value', item.value);
            //         li.setAttribute('aria-selected', 'true');
            //         li.setAttribute('data-color1', color);
            //         li.style.border = `1px solid ${color}`;

            //         const span = document.createElement('span');
            //         span.className = 'tag-dot';
            //         span.style.backgroundColor = color;

            //         const text = document.createTextNode(item.label);

            //         const button = document.createElement('button');
            //         button.type = 'button';
            //         button.className = 'choices__button';
            //         button.setAttribute('aria-label', `Remove item: ${item.value}`);
            //         button.dataset.button = '';

            //         const icon = document.createElement('i');
            //         icon.className = 'fa-solid fa-xmark choice-remove';
            //         icon.setAttribute('aria-hidden', 'true');

            //         button.appendChild(icon);
            //         li.appendChild(span);
            //         li.appendChild(text);
            //         li.appendChild(button);
            //         paletteContainer.appendChild(li);
            //     });
            // });
        });


        const capitalizedType = (str) => str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        $('#select-agent').select2({
            placeholder: 'Type and search...',
            ajax: {
                url: '<?php echo SB_URL; ?>/include/ajax.php', // Your endpoint
                method: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        function: 'ajax_calls',
                        'calls[0][function]': 'search-get-users',
                        'login-cookie': SBF.loginCookie(),
                        'q': params.term, // ✅ Pass search term
                        'type': 'agent'
                    };
                },
                processResults: function (response) {
                    //response = JSON.parse(response);
                    if (response[0][0] == 'success') {
                        const users = response[0][1];
                        console.log("Processed users:", response[0][1]);
                        return {
                            results: users.map(user => ({
                                id: user.id,
                                text: user.first_name + ' ' + user.last_name + ' (' + capitalizedType(user.user_type) + ')',
                            }))
                        };
                    }
                },
                cache: true
            },
            minimumInputLength: 1
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- File Upload Handling -->
    <script>
        function propagateTagColors() {
            // Map value to color from original select
            const select = document.getElementById('ticket-tags');
            if (!select) return;
            const valueToColor = {};
            Array.from(select.options).forEach(opt => {
                if (opt.value) valueToColor[opt.value] = opt.getAttribute('data-color');
            });
            // Dropdown items
            document.querySelectorAll('.choices__list--dropdown .choices__item').forEach(function (item) {
                const value = item.getAttribute('data-value');
                if (valueToColor[value]) {
                    item.setAttribute('data-color', valueToColor[value]);
                }
            });
            // Selected items
            document.querySelectorAll('.choices__list--multiple .choices__item').forEach(function (item) {
                const value = item.getAttribute('data-value');
                if (valueToColor[value]) {
                    item.setAttribute('data-color', valueToColor[value]);
                }
            });
        }

        function updateTagDots() {
            // Dropdown items
            document.querySelectorAll('.choices__list--dropdown .choices__item[data-color]').forEach(function (item) {
                if (!item.querySelector('.tag-dot')) {
                    let color = item.getAttribute('data-color');
                    let dot = document.createElement('span');
                    dot.className = 'tag-dot';
                    dot.style.backgroundColor = color;
                    item.prepend(dot);
                }
            });
            // Selected items
            document.querySelectorAll('.choices__list--multiple .choices__item[data-color]').forEach(function (item) {
                if (!item.querySelector('.tag-dot')) {
                    let color = item.getAttribute('data-color');
                    let dot = document.createElement('span');
                    dot.className = 'tag-dot';
                    dot.style.backgroundColor = color;
                    item.prepend(dot);
                }
            });
        }

        function refreshTagDots() {
            propagateTagColors();
            updateTagDots();
        }


        jQuery(document).ready(function ($) {
            // This listens for change events on any current or future select inside #parent-container
            // Trigger change

            $('#without_contact input').on('change', function () {

                const isChecked = $(this).is(':checked');
                //$('#cust_name input, #cust_email input').prop('disabled', !isChecked);
                $('#cust_name input, #cust_email input').prop('required', isChecked);

                if (isChecked) {
                    $('#contact_id').hide();
                    $('#contact_id #select-customer').removeAttr('required');
                    $('#cust_name span, #cust_email span').addClass('required-label');

                } else {
                    $('#contact_id').show();
                    $('#contact_id select').attr('required', true);
                    $('#cust_name span, #cust_email span').removeClass('required-label');
                    // Optionally, you can set focus back to the name field
                    $('#cust_name input').focus();
                }
            });

            $('#without_contact input').trigger('change');

            // Array to store uploaded files
            let uploadedFiles = [];

            const maxFileSizeMB = 3; // Maximum size in MB per file
            const allowedFileTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            // File upload handling
            document.getElementById('ticket-attachments').addEventListener('change', function (event) {
                const files = event.target.files;
                if (files.length === 0) return;

                let isValid = true;
                let errorMessage = '';

                if (files.length === 0) return;

                Array.from(files).forEach(file => {
                    const fileSizeMB = file.size / (1024 * 1024);

                    if (fileSizeMB > maxFileSizeMB) {
                        isValid = false;
                        errorMessage += `File "${file.name}" exceeds ${maxFileSizeMB} MB.\n`;
                    }

                    if (!allowedFileTypes.includes(file.type)) {
                        isValid = false;
                        errorMessage += `File "${file.name}" is not an allowed type.\n`;
                    }
                });

                if (!isValid) {
                    //alert(errorMessage);
                    $(this).val(''); // Clear the input
                    $('.files-error').html(errorMessage);
                    return;
                }
                else {
                    $('.files-error').html('');
                }

                // Create FormData object
                const formData = new FormData();

                let reopendTicketAttachmentsPopup = $('#reopendTicketAttachmentsPopup').val();

                if (reopendTicketAttachmentsPopup == '1') {
                    uploadedFiles = [];
                    $('#reopendTicketAttachmentsPopup').val(0);
                }

                for (let i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }

                const ticket_id = $('.sb-area-ticket-detail.sb-active').data('id');

                formData.append('function', 'ajax_calls');
                formData.append('calls[0][function]', 'upload-ticket-attachments');
                formData.append('login-cookie', SBF.loginCookie());
                formData.append('ticket_id', 0); // Replace with actual ticket ID


                console.log('Files to upload:', files);

                // Show progress container
                const progressContainer = document.getElementById('upload-progress-container');
                const progressBar = document.getElementById('upload-progress');
                progressContainer.classList.remove('d-none');
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                progressBar.textContent = '0%';

                //Create and configure XMLHttpRequest
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo SB_URL; ?>/include/ajax.php', true);

                // Track upload progress
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressBar.setAttribute('aria-valuenow', percentComplete);
                        progressBar.textContent = percentComplete + '%';
                    }
                });

                // Handle response
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        let res2 = typeof response[0][1] === 'string' ? JSON.parse(response[0][1]) : response[0][1];

                        if (res2.success) {
                            // Add uploaded files to the array
                            uploadedFiles = uploadedFiles.concat(res2.files);

                            // Update hidden input with file data
                            console.log('Uploaded files:', uploadedFiles);
                            document.getElementById('uploaded_files').value = JSON.stringify(uploadedFiles);

                            // Display file previews
                            displayFilePreviews(res2.files);

                            // Reset file input
                            document.getElementById('ticket-attachments').value = '';
                        } else {
                            alert('Error: ' + res2.error);
                        }
                    } else {
                        alert('Error uploading files. Please try again.');
                    }

                    // Hide progress container after a delay
                    setTimeout(() => {
                        progressContainer.classList.add('d-none');
                    }, 1000);
                };

                // Handle errors
                xhr.onerror = function () {
                    alert('Error uploading files. Please try again.');
                    progressContainer.classList.add('d-none');
                };

                //Send the request
                xhr.send(formData);


                //     const formData = new FormData();
                //     const files = document.getElementById('attachments').files;

                //     Append selected files to FormData
                //     for (let i = 0; i < files.length; i++) {
                //        formData.append('attachments[]', files[i]);
                //    }

                //     Show progress bar container (if it's hidden)
                //     progressContainer.classList.remove('d-none');


                //     // Perform Ajax request
                //     $.ajax({
                //         url: 'http://localhost/saassupport/script/include/ajax.php',
                //         type: 'POST',
                //         data: formData,
                //         contentType: false,  // Important for FormData
                //         processData: false,  // Important for FormData
                //         xhr: function () {
                //             let xhr = new window.XMLHttpRequest();
                //             xhr.upload.addEventListener('progress', function (e) {
                //                 if (e.lengthComputable) {
                //                     const percentComplete = Math.round((e.loaded / e.total) * 100);
                //                     progressBar.style.width = percentComplete + '%';
                //                     progressBar.setAttribute('aria-valuenow', percentComplete);
                //                     progressBar.textContent = percentComplete + '%';
                //                 }
                //             }, false);
                //             return xhr;
                //         },
                //         success: function (response) {
                //             try {
                //                 const data = typeof response === 'string' ? JSON.parse(response) : response;
                //                 if (data.success) {
                //                     // Update uploaded files array
                //                     uploadedFiles = uploadedFiles.concat(data.files);
                //                     document.getElementById('uploaded_files').value = JSON.stringify(uploadedFiles);
                //                     displayFilePreviews(data.files);
                //                     document.getElementById('attachments').value = '';
                //                 } else {
                //                     alert('Error: ' + data.error);
                //                 }
                //             } catch (e) {
                //                 alert('Error parsing response.');
                //             }
                //         },
                //         error: function () {
                //             alert('Error uploading files. Please try again.');
                //         },
                //         complete: function () {
                //             setTimeout(() => {
                //                 progressContainer.classList.add('d-none');
                //             }, 1000);
                //         }
                //     });


            });

            function displayFilePreviews(files) {
                const previewList = document.getElementById('file-preview-list');

                files.forEach(file => {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 mb-2';

                    const card = document.createElement('div');
                    card.className = 'card';

                    const cardBody = document.createElement('div');
                    cardBody.className = 'card-body p-2 attachment-card';

                    // Determine file type icon
                    let fileIcon = 'bi-file-earmark';
                    const fileType = file.file_type.split('/')[0];
                    if (fileType === 'image') {
                        fileIcon = 'bi-file-earmark-image';
                    } else if (fileType === 'application') {
                        fileIcon = 'bi-file-earmark-pdf';
                    } else if (fileType === 'text') {
                        fileIcon = 'bi-file-earmark-text';
                    }

                    // Create preview content
                    let previewContent = `
                    <div class="d-flex align-items-center">
                        <i class="bi ${fileIcon} me-2" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1 text-truncate">
                            <div class="text-truncate">${file.original_filename}</div>
                            <small class="text-muted">${formatFileSize(file.file_size)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-file" data-index="${uploadedFiles.indexOf(file)}">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;

                    // For images, add a thumbnail preview
                    if (fileType === 'image') {
                        previewContent = `
                        <i class="fa-solid fa-x remove-file" style="color: #dc3545;" data-index="${uploadedFiles.indexOf(file)}"></i>
                        <div class="text-center mb-2">
                            <img src="script/${file.file_path}" class="img-thumbnail p-0" style="max-height: 100px; margin-top: 10px;" alt="${file.original_filename}">
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 text-truncate">
                                <div class="text-truncate">${file.original_filename}</div>
                                <small class="text-muted">${formatFileSize(file.file_size)}</small>
                            </div>
                        </div>
                    `;
                    }

                    cardBody.innerHTML = previewContent;
                    card.appendChild(cardBody);
                    col.appendChild(card);
                    previewList.appendChild(col);

                    // Add event listener to remove button
                    const removeBtn = cardBody.querySelector('.remove-file');
                    removeBtn.addEventListener('click', function () {
                        const index = parseInt(this.getAttribute('data-index'));
                        removeFile(index);
                    });
                });
            }

            // Function to remove a file
            function removeFile(index) {
                if (index >= 0 && index < uploadedFiles.length) {
                    uploadedFiles.splice(index, 1);
                    document.getElementById('uploaded_files').value = JSON.stringify(uploadedFiles);

                    // Refresh all previews
                    const previewList = document.getElementById('file-preview-list');
                    previewList.innerHTML = '';
                    displayFilePreviews(uploadedFiles);
                }
            }

            // const maxFileSizeMB = 5; // Maximum size in MB per file
            // const allowedFileTypes = ['image/jpeg', 'image/png', 'application/pdf']; 
            document.getElementById('ticket-attachments1').addEventListener('change', function (event) {
                const files = event.target.files;
                let isValid = true;
                let errorMessage = '';

                if (files.length === 0) return;

                Array.from(files).forEach(file => {
                    const fileSizeMB = file.size / (1024 * 1024);

                    if (fileSizeMB > maxFileSizeMB) {
                        isValid = false;
                        errorMessage += `File "${file.name}" exceeds ${maxFileSizeMB} MB.\n`;
                    }

                    if (!allowedFileTypes.includes(file.type)) {
                        isValid = false;
                        errorMessage += `File "${file.name}" is not an allowed type.\n`;
                    }
                });

                if (!isValid) {
                    //alert(errorMessage);
                    $(this).val(''); // Clear the input
                    $('.files-error').html(errorMessage);
                    return;
                }
                else {
                    $('.files-error').html('');
                }

                // Create FormData object
                const formData = new FormData();
                uploadedFiles = [];
                for (let i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }


                formData.append('function', 'ajax_calls');
                formData.append('calls[0][function]', 'upload-ticket-attachments');
                formData.append('login-cookie', SBF.loginCookie());
                formData.append('ticket_id', ticket_id); // Replace with actual ticket ID


                console.log('Files to upload:', files);

                // Show progress container
                const progressContainer = document.getElementById('upload-progress-container1');
                const progressBar = document.getElementById('upload-progress1');
                progressContainer.classList.remove('d-none');
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                progressBar.textContent = '0%';

                //Create and configure XMLHttpRequest
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo SB_URL; ?>/include/ajax.php', true);

                // Track upload progress
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressBar.setAttribute('aria-valuenow', percentComplete);
                        progressBar.textContent = percentComplete + '%';
                    }
                });

                // Handle response
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        let res2 = typeof response[0][1] === 'string' ? JSON.parse(response[0][1]) : response[0][1];

                        if (res2.success) {
                            // Add uploaded files to the array
                            uploadedFiles = uploadedFiles.concat(res2.files);

                            // Update hidden input with file data
                            console.log('Uploaded files:', uploadedFiles);
                            document.getElementById('uploaded_files1').value = JSON.stringify(uploadedFiles);

                            // Display file previews
                            displayFilePreviews1(res2.files);

                            // Reset file input
                            document.getElementById('ticket-attachments1').value = '';
                        } else {
                            alert('Error: ' + res2.error);
                        }
                    } else {
                        alert('Error uploading files. Please try again.');
                    }

                    // Hide progress container after a delay
                    setTimeout(() => {
                        progressContainer.classList.add('d-none');
                    }, 1000);
                };

                // Handle errors
                xhr.onerror = function () {
                    alert('Error uploading files. Please try again.');
                    progressContainer.classList.add('d-none');
                };

                //Send the request
                xhr.send(formData);

            });



            // Function to display file previews
            function displayFilePreviews1(files) {
                const previewList = document.getElementById('file-preview-list1');

                files.forEach(file => {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 mb-2';

                    const card = document.createElement('div');
                    card.className = 'card';

                    const cardBody = document.createElement('div');
                    cardBody.className = 'card-body p-2 attachment-card';

                    // Determine file type icon
                    let fileIcon = 'bi-file-earmark';
                    const fileType = file.file_type.split('/')[0];
                    if (fileType === 'image') {
                        fileIcon = 'bi-file-earmark-image';
                    } else if (fileType === 'application') {
                        fileIcon = 'bi-file-earmark-pdf';
                    } else if (fileType === 'text') {
                        fileIcon = 'bi-file-earmark-text';
                    }

                    // Create preview content
                    let previewContent = `
                    <div class="d-flex align-items-center">
                        <i class="bi ${fileIcon} me-2" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1 text-truncate">
                            <div class="text-truncate">${file.original_filename}</div>
                            <small class="text-muted">${formatFileSize(file.file_size)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-file" data-index="${uploadedFiles.indexOf(file)}">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;

                    // For images, add a thumbnail preview
                    if (fileType === 'image') {
                        previewContent = `
                        <i class="fa-solid fa-x remove-file" style="color: #dc3545;" data-index="${uploadedFiles.indexOf(file)}"></i>
                        <div class="text-center mb-2">
                            <img src="script/${file.file_path}" class="img-thumbnail p-0" style="max-height: 100px; margin-top: 10px;" alt="${file.original_filename}">
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 text-truncate">
                                <div class="text-truncate">${file.original_filename}</div>
                                <small class="text-muted">${formatFileSize(file.file_size)}</small>
                            </div>
                        </div>
                    `;
                    }

                    cardBody.innerHTML = previewContent;
                    card.appendChild(cardBody);
                    col.appendChild(card);
                    previewList.appendChild(col);

                    // Add event listener to remove button
                    const removeBtn = cardBody.querySelector('.remove-file');
                    removeBtn.addEventListener('click', function () {
                        const index = parseInt(this.getAttribute('data-index'));
                        removeFile1(index);
                    });
                });
            }

            // Function to remove a file
            function removeFile1(index) {
                if (index >= 0 && index < uploadedFiles.length) {
                    uploadedFiles.splice(index, 1);
                    document.getElementById('uploaded_files1').value = JSON.stringify(uploadedFiles);

                    // Refresh all previews
                    const previewList = document.getElementById('file-preview-list1');
                    previewList.innerHTML = '';
                    displayFilePreviews1(uploadedFiles);
                }
            }

            // Function to format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Delete existing attachment
            //document.querySelectorAll('.delete-attachment').forEach(button => {
            $(document).on('click', '.delete-attachment', function () {
                const attachmentId = this.getAttribute('data-id');
                const ticketId = this.getAttribute('data-ticket-id');
                const self = this; // 🔒 Save reference to `this`
                if (confirm('Are you sure you want to delete this attachment?')) {
                    // Create FormData object
                    const formData = new FormData();
                    formData.append('attachment_id', attachmentId);
                    formData.append('ticket_id', ticketId);
                    formData.append('function', 'ajax_calls');
                    formData.append('calls[0][function]', 'remove-ticket-attachment');
                    formData.append('login-cookie', SBF.loginCookie());

                    // Create and configure XMLHttpRequest
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo SB_URL; ?>/include/ajax.php', true);

                    // Handle response
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Delete response:', response);
                            if (response[0][1].success) {
                                // Remove the attachment from the DOM
                                const card = self.closest('.col-md-2');
                                card.remove();

                                $('#view-ticket-attachments .attachments-count').html($('.attachments .col-md-2').length);

                                // Hide Current Attachments section if no attachments left
                                if ($('#current-attachments').children().length === 0) {
                                    $('#existing-file-preview-container1').addClass('d-none');
                                }
                            } else {
                                alert('Error: ' + response.error);
                            }
                        } else {
                            alert('Error deleting attachment. Please try again.');
                        }
                    };

                    // Send the request
                    xhr.send(formData);
                }
            });
            // });

        });
    </script>
    <?php
}
?>
<?php function sb_login_box()
{
    ?>
    <form class="sb sb-rich-login sb-admin-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <img src="<?php echo sb_get_setting(
                "login-icon",
                SB_URL . "/media/logo.svg"
            ); ?>" />
            <div class="sb-title">
                <?php sb_e("Sign In"); ?>
            </div>
            <div class="sb-text">
                <?php echo sb_sanatize_string(
                    sb_get_setting(
                        "login-message",
                        defined("SB_WP")
                        ? sb_(
                            "Please insert email and password of your WordPress account"
                        )
                        : sb_("Enter your login details below")
                    )
                ); ?>
            </div>
        </div>
        <div class="sb-main">
            <div id="email" class="sb-input">
                <span>
                    <?php sb_e("Email"); ?>
                </span>
                <input type="text" />
            </div>
            <div id="password" class="sb-input">
                <span>
                    <?php sb_e("Password"); ?>
                </span>
                <input type="password" />
            </div>
            <div class="sb-bottom">
                <div class="sb-btn sb-submit-login">
                    <?php sb_e("Login"); ?>
                </div>
            </div>
        </div>
    </form>
    <img id="sb-error-check" style="display:none" src="<?php echo SB_URL .
        "/media/logo.svg"; ?>" />
    <script>
        (function ($) {
            $(document).ready(function () {
                $('.sb-admin-start').removeAttr('style');
                $('.sb-submit-login').on('click', function () {
                    SBF.loginForm(this, false, function () {
                        location.reload();
                    });
                });
                $('#sb-error-check').one('error', function () {
                    $('.sb-info').html('It looks like the chat URL has changed. Edit the config.php file(it\'s in the Support Board folder) and update the SB_URL constant with the new URL.').addClass('sb-active');
                });
                SBF.serviceWorker.init();
            });
            $(window).keydown(function (e) {
                if (e.which == 13) {
                    $('.sb-submit-login').click();
                }
            });
            if (SBF.getURL('login_email')) {
                setTimeout(() => {
                    $('#email input').val(SBF.getURL('login_email'));
                    $('#password input').val(SBF.getURL('login_password'));
                    $('.sb-submit-login').click();
                }, 300);
            }
        }(jQuery));
    </script>
    <?php
} ?>
<?php function sb_dialog()
{
    ?>
    <div class="sb-dialog-box sb-lightbox">
        <div class="sb-title"></div>
        <p></p>
        <div>
            <a class="sb-confirm sb-btn">
                <?php sb_e("Confirm"); ?>
            </a>
            <a class="sb-cancel sb-btn sb-btn-red">
                <?php sb_e("Cancel"); ?>
            </a>
            <a class="sb-close sb-btn">
                <?php sb_e("Close"); ?>
            </a>
        </div>
    </div>
    <?php
} ?>
<?php function sb_updates_box()
{
    ?>
    <div class="sb-lightbox sb-updates-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div>
                <?php sb_e("Update center"); ?>
            </div>
            <div>
                <a class="sb-close sb-btn-icon sb-btn-red">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-bottom">
                <a class="sb-update sb-btn sb-icon">
                    <i class="sb-icon-reload"></i>
                    <?php sb_e("Update now"); ?>
                </a>
                <a href="https://board.support/changes" target="_blank" class="sb-btn-text">
                    <i class="sb-icon-clock"></i>
                    <?php sb_e("Change Log"); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
} ?>
<?php function sb_app_box()
{
    ?>
    <div class="sb-lightbox sb-app-box" data-app="">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div></div>
            <div>
                <a class="sb-close sb-btn-icon sb-btn-red">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main">
            <p></p>
            <div class="sb-title">
                <?php sb_e("License key"); ?>
            </div>
            <div class="sb-setting sb-type-text">
                <input type="text" required />
            </div>
            <div class="sb-bottom">
                <a class="sb-btn sb-icon sb-btn-app-setting">
                    <i class="sb-icon-settings"></i>
                    <?php sb_e("Settings"); ?>
                </a>
                <a class="sb-activate sb-btn sb-icon">
                    <i class="sb-icon-check"></i>
                    <?php sb_e("Activate"); ?>
                </a>
                <a class="sb-btn-red sb-btn sb-icon sb-btn-app-disable">
                    <i class="sb-icon-close"></i>
                    <?php sb_e("Disable"); ?>
                </a>
                <a class="sb-btn sb-icon sb-btn-app-puchase" target="_blank" href="#">
                    <i class="sb-icon-plane"></i>
                    <?php sb_e("Purchase license"); ?>
                </a>
                <a class="sb-btn-text sb-btn-app-details" target="_blank" href="#">
                    <i class="sb-icon-help"></i>
                    <?php sb_e("Read more"); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
} ?>
<?php function sb_direct_message_box()
{
    ?>
    <div class="sb-lightbox sb-direct-message-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div></div>
            <div>
                <a class="sb-close sb-btn-icon sb-btn-red">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-title">
                <?php sb_e("User IDs"); ?>
            </div>
            <div class="sb-setting sb-type-text sb-first">
                <input class="sb-direct-message-users" type="text" placeholder="<?php sb_e(
                    "User IDs separated by commas"
                ); ?>" required />
            </div>
            <div class="sb-title sb-direct-message-subject">
                <?php sb_e("Subject"); ?>
            </div>
            <div class="sb-setting sb-type-text sb-direct-message-subject">
                <input type="text" placeholder="<?php sb_e(
                    "Email subject"
                ); ?>" />
            </div>
            <div class="sb-title sb-direct-message-title-subject">
                <?php sb_e("Message"); ?>
            </div>
            <div class="sb-setting sb-type-textarea">
                <textarea placeholder="<?php sb_e(
                    "Write here your message..."
                ); ?>" required></textarea>
            </div>
            <div class="sb-bottom">
                <a class="sb-send-direct-message sb-btn sb-icon">
                    <i class="sb-icon-plane"></i>
                    <?php sb_e("Send message now"); ?>
                </a>
                <div></div>
                <?php sb_docs_link("#direct-messages", "sb-btn-text"); ?>
            </div>
        </div>
    </div>
    <?php
} ?>
<?php function sb_routing_select($exclude_id = false)
{
    $agents = sb_db_get(
        'SELECT id, first_name, last_name FROM sb_users WHERE (user_type = "agent" OR user_type = "admin")' .
        ($exclude_id ? " AND id <> " . sb_db_escape($exclude_id) : ""),
        false
    );
    $code =
        '<div class="sb-inline sb-inline-agents"><h3>' .
        sb_("Agent") .
        '</h3><div id="conversation-agent" class="sb-select"><p>' .
        sb_("None") .
        '</p><ul><li data-id="" data-value="">' .
        sb_("None") .
        "</li>";
    for ($i = 0; $i < count($agents); $i++) {
        $code .=
            '<li data-id="' .
            $agents[$i]["id"] .
            '">' .
            $agents[$i]["first_name"] .
            " " .
            $agents[$i]["last_name"] .
            "</li>";
    }
    echo $code . "</ul></div></div>";
} ?>
<?php function sb_installation_box($error = false)
{
    global $SB_LANGUAGE;
    $SB_LANGUAGE = isset($_GET["lang"])
        ? $_GET["lang"]
        : strtolower(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2));
    ?>
    <div class="sb-main sb-admin sb-admin-start">
        <form class="sb-intall sb-admin-box">
            <?php if ($error === false || $error == "installation") {
                echo '<div class="sb-info"></div>';
            } else {
                die(
                    '<div class="sb-info sb-active">' .
                    sb_(
                        'We\'re having trouble connecting to your database. Please edit the file config.php and check your database connection details. Error: '
                    ) .
                    $error .
                    ".</div>"
                );
            } ?>
            <div class="sb-top-bar">
                <img src="<?php echo !SB_URL || SB_URL == "[url]"
                    ? ""
                    : SB_URL . "/"; ?>media/logo.svg" />
                <div class="sb-title">
                    <?php sb_e("Installation"); ?>
                </div>
                <div class="sb-text">
                    <?php sb_e(
                        "Please complete the installation process by entering your database connection details below. If you are not sure about this, contact your hosting provider for support."
                    ); ?>
                </div>
            </div>
            <div class="sb-main">
                <div id="db-name" class="sb-input">
                    <span>
                        <?php sb_e("Database Name"); ?>
                    </span>
                    <input type="text" required />
                </div>
                <div id="db-user" class="sb-input">
                    <span>
                        <?php sb_e("Username"); ?>
                    </span>
                    <input type="text" required />
                </div>
                <div id="db-password" class="sb-input">
                    <span>
                        <?php sb_e("Password"); ?>
                    </span>
                    <input type="text" />
                </div>
                <div id="db-host" class="sb-input">
                    <span>
                        <?php sb_e("Host"); ?>
                    </span>
                    <input type="text" required />
                </div>
                <div id="db-port" class="sb-input">
                    <span>
                        <?php sb_e("Port"); ?>
                    </span>
                    <input type="text" placeholder="Default" />
                </div>
                <?php if ($error === false || $error == "installation") { ?>
                    <div class="sb-text">
                        <?php sb_e(
                            "Enter the user details of the main account you will use to login into the administration area. You can update these details later."
                        ); ?>
                    </div>
                    <div id="first-name" class="sb-input">
                        <span>
                            <?php sb_e("First name"); ?>
                        </span>
                        <input type="text" required />
                    </div>
                    <div id="last-name" class="sb-input">
                        <span>
                            <?php sb_e("Last name"); ?>
                        </span>
                        <input type="text" required />
                    </div>
                    <div id="email" class="sb-input">
                        <span>
                            <?php sb_e("Email"); ?>
                        </span>
                        <input type="email" required />
                    </div>
                    <div id="password" class="sb-input">
                        <span>
                            <?php sb_e("Password"); ?>
                        </span>
                        <input type="password" required />
                    </div>
                    <div id="password-check" class="sb-input">
                        <span>
                            <?php sb_e("Repeat password"); ?>
                        </span>
                        <input type="password" required />
                    </div>
                <?php } ?>
                <div class="sb-bottom">
                    <div class="sb-btn sb-submit-installation">
                        <?php sb_e("Complete installation"); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
} ?>
<?php
/*
 * ----------------------------------------------------------
 * ADMIN AREA
 * ----------------------------------------------------------
 *
 * Display the administration area
 *
 */
?>
<?php
function sb_component_admin()
{
    $is_cloud = sb_is_cloud();
    $cloud_active_apps = $is_cloud
        ? sb_get_external_setting("active_apps", [])
        : [];
    $sb_settings = sb_get_json_resource("json/settings.json");
    $active_user = sb_get_active_user(false, true);
    $collapse = sb_get_setting("collapse") ? " sb-collapse" : "";
    $apps = [
        ["SB_WP", "wordpress", "WordPress"],
        [
            "SB_DIALOGFLOW",
            "dialogflow",
            "Artificial Intelligence",
            "Connect smart chatbots and automate conversations by using one of the most advanced forms of artificial intelligence in the world.",
        ],
        [
            "SB_TICKETS",
            "tickets",
            "Tickets",
            "Provide help desk support to your customers by including a ticket area, with all chat features included, on any web page in seconds.",
        ],
        [
            "SB_MESSENGER",
            "messenger",
            "Messenger",
            "Read, manage and reply to all messages sent to your Facebook pages and Instagram accounts directly from {R}.",
        ],
        [
            "SB_WHATSAPP",
            "whatsapp",
            "WhatsApp",
            "Lets your users reach you via WhatsApp. Read and reply to all messages sent to your WhatsApp Business account directly from {R}.",
        ],
        [
            "SB_TWITTER",
            "twitter",
            "Twitter",
            "Lets your users reach you via Twitter. Read and reply to messages sent to your Twitter account directly from {R}.",
        ],
        [
            "SB_TELEGRAM",
            "telegram",
            "Telegram",
            "Connect your Telegram bot to {R} to read and reply to all messages sent to your Telegram bot directly in {R}.",
        ],
        [
            "SB_VIBER",
            "viber",
            "Viber",
            "Connect your Viber bot to {R} to read and reply to all messages sent to your Viber bot directly in {R}.",
        ],
        [
            "SB_LINE",
            "line",
            "Line",
            "Connect your LINE bot to {R} to read and reply to all messages sent to your LINE bot directly in {R}.",
        ],
        [
            "SB_WECHAT",
            "wechat",
            "WeChat",
            "Lets your users reach you via WeChat. Read and reply to all messages sent to your WeChat official account directly from {R}.",
        ],
        [
            "SB_ZALO",
            "zalo",
            "Zalo",
            "Connect your Zalo Official Account to {R} to read and reply to all messages sent to your Zalo Official Account directly in {R}.",
        ],
        [
            "SB_WOOCOMMERCE",
            "woocommerce",
            "WooCommerce",
            "Increase sales, provide better support, and faster solutions, by integrating WooCommerce with {R}.",
        ],
        [
            "SB_SLACK",
            "slack",
            "Slack",
            "Communicate with your users right from Slack. Send and receive messages and attachments, use emojis, and much more.",
        ],
        [
            "SB_ZENDESK",
            "zendesk",
            "Zendesk",
            "Automatically sync Zendesk customers with {R}, view Zendesk tickets, or create new ones without leaving {R}.",
        ],
        [
            "SB_UMP",
            "ump",
            "Ultimate Membership Pro",
            "Enable ticket and chat support for subscribers only, view member profile details and subscription details in the admin area.",
        ],
        [
            "SB_PERFEX",
            "perfex",
            "Perfex",
            "Synchronize your Perfex customers in real-time and let them contact you via chat! View profile details, proactively engage them, and more.",
        ],
        [
            "SB_WHMCS",
            "whmcs",
            "Whmcs",
            "Synchronize your customers in real-time, chat with them and boost their engagement, or provide a better and faster support.",
        ],
        [
            "SB_OPENCART",
            "opencart",
            "OpenCart",
            "Integrate OpenCart with {R} for real-time syncing of customers, order history access, and customer cart visibility.",
        ],
        [
            "SB_AECOMMERCE",
            "aecommerce",
            "Active eCommerce",
            "Increase sales and connect you and sellers with customers in real-time by integrating Active eCommerce with {R}.",
        ],
        [
            "SB_ARMEMBER",
            "armember",
            "ARMember",
            "Synchronize customers, enable ticket and chat support for subscribers only, view subscription plans in the admin area.",
        ],
        [
            "SB_MARTFURY",
            "martfury",
            "Martfury",
            "Increase sales and connect you and sellers with customers in real-time by integrating Martfury with {R}.",
        ],
    ];
    $logged =
        $active_user &&
        sb_is_agent($active_user) &&
        (!defined("SB_WP") ||
            !sb_get_setting("wp-force-logout") ||
            sb_wp_verify_admin_login());
    $supervisor = sb_supervisor();
    $is_admin =
        $active_user && sb_is_agent($active_user, true, true) && !$supervisor;
    $sms = sb_get_multi_setting("sms", "sms-user");
    $css_class =
        ($logged ? "sb-admin" : "sb-admin-start") .
        (($is_cloud && defined("SB_CLOUD_DEFAULT_RTL")) || sb_is_rtl()
            ? " sb-rtl"
            : "") .
        ($is_cloud ? " sb-cloud" : "") .
        ($supervisor ? " sb-supervisor" : "");
    // $active_areas = [
    //     'users' => $is_admin || (!$supervisor && sb_get_multi_setting('agents', 'agents-users-area')) || ($supervisor && $supervisor['supervisor-users-area']), 
    //     'settings' => $is_admin || ($supervisor && $supervisor['supervisor-settings-area']),
    //     'reports' => ($is_admin && !sb_get_multi_setting('performance', 'performance-reports')) || ($supervisor && $supervisor['supervisor-reports-area']), 
    //     'articles' => ($is_admin && !sb_get_multi_setting('performance', 'performance-articles')) || ($supervisor && sb_isset($supervisor, 'supervisor-articles-area')) || (!$supervisor && !$is_admin && sb_get_multi_setting('agents', 'agents-articles-area')),
    //     'chatbot' => defined('SB_DIALOGFLOW') && ($is_admin || ($supervisor && $supervisor['supervisor-settings-area'])) && (!$is_cloud || in_array('dialogflow', $cloud_active_apps))];


    $active_areas = [
        'users' => $is_admin || (!$supervisor && sb_get_multi_setting('agents', 'agents-users-area')) || ($supervisor && $supervisor['supervisor-users-area']),
        'settings' => $is_admin || ($supervisor && $supervisor['supervisor-settings-area']),
        'reports' => ($is_admin && !sb_get_multi_setting('performance', 'performance-reports')) || ($supervisor && $supervisor['supervisor-reports-area']),
        'articles' => ($is_admin && !sb_get_multi_setting('performance', 'performance-articles')) || ($supervisor && sb_isset($supervisor, 'supervisor-articles-area')) || (!$supervisor && !$is_admin && sb_get_multi_setting('agents', 'agents-articles-area')),
        'chatbot' => defined('SB_DIALOGFLOW') && ($is_admin || ($supervisor && $supervisor['supervisor-settings-area'])) && (!$is_cloud || in_array('dialogflow', $cloud_active_apps)),
        'tickets' => $is_admin,
        'dashboard' => $is_admin,
    ];
    $disable_translations = sb_get_setting(
        "admin-disable-settings-translations"
    );
    $admin_colors = [
        sb_get_setting("color-admin-1"),
        sb_get_setting("color-admin-2"),
    ];
    if ($supervisor && !$supervisor["supervisor-send-message"]) {
        echo '<style>.sb-board .sb-conversation .sb-editor,#sb-start-conversation,.sb-top-bar [data-value="sms"],.sb-top-bar [data-value="email"],.sb-menu-users [data-value="message"],.sb-menu-users [data-value="sms"],.sb-menu-users [data-value="email"] { display: none !important; }</style>';
    }
    if ($is_cloud) {
        require_once SB_CLOUD_PATH . "/account/functions.php";
        $sb_settings = sb_cloud_merge_settings($sb_settings);
        cloud_custom_code();
    } elseif (!sb_box_ve()) {
        return;
    }
    if ($admin_colors[0]) {
        $css =
            '.sb-menu-wide ul li.sb-active, .sb-tab > .sb-nav > ul li.sb-active,.sb-table input[type="checkbox"]:checked, .sb-table input[type="checkbox"]:hover { border-color: ' .
            $admin_colors[0] .
            "; }";
        $css .=
            ".sb-board > .sb-admin-list .sb-scroll-area li.sb-active,.sb-user-conversations > li.sb-active { border-left-color: " .
            $admin_colors[0] .
            "; }";
        $css .=
            '.sb-setting input:focus, .sb-setting select:focus, .sb-setting textarea:focus, .sb-setting input:focus, .sb-setting select:focus, .sb-setting textarea:focus,.sb-setting.sb-type-upload-image .image:hover, .sb-setting [data-type="upload-image"] .image:hover, .sb-setting.sb-type-upload-image .image:hover, .sb-setting [data-type="upload-image"] .image:hover,.sb-input > input:focus, .sb-input > input.sb-focus, .sb-input > select:focus, .sb-input > select.sb-focus, .sb-input > textarea:focus, .sb-input > textarea.sb-focus,.sb-search-btn > input,.sb-search-btn > input:focus { border-color: ' .
            $admin_colors[0] .
            "; box-shadow: 0 0 5px rgb(108 108 108 / 20%);}";
        $css .=
            '.sb-menu-wide ul li.sb-active, .sb-menu-wide ul li:hover, .sb-tab > .sb-nav > ul li.sb-active, .sb-tab > .sb-nav > ul li:hover,.sb-admin > .sb-header > .sb-admin-nav > div > a:hover, .sb-admin > .sb-header > .sb-admin-nav > div > a.sb-active,.sb-setting input[type="checkbox"]:checked:before, .sb-setting input[type="checkbox"]:checked:before,.sb-language-switcher > i:hover,.sb-admin > .sb-header > .sb-admin-nav-right .sb-account .sb-menu li:hover, .sb-admin > .sb-header > .sb-admin-nav-right .sb-account .sb-menu li.sb-active:hover,.sb-admin > .sb-header > .sb-admin-nav-right > div > a:hover,.sb-search-btn i:hover, .sb-search-btn.sb-active i, .sb-filter-btn i:hover, .sb-filter-btn.sb-active i,.sb-loading:before,.sb-board .sb-conversation > .sb-top a:hover i,.sb-panel-details > i:hover,.sb-board .sb-conversation > .sb-top > a:hover,.sb-btn-text:hover,.sb-table input[type="checkbox"]:checked:before,.sb-profile-list [data-id="wp-id"]:hover, .sb-profile-list [data-id="wp-id"]:hover label, .sb-profile-list [data-id="conversation-source"]:hover, .sb-profile-list [data-id="conversation-source"]:hover label, .sb-profile-list [data-id="location"]:hover, .sb-profile-list [data-id="location"]:hover label, .sb-profile-list [data-id="timezone"]:hover, .sb-profile-list [data-id="timezone"]:hover label, .sb-profile-list [data-id="current_url"]:hover, .sb-profile-list [data-id="current_url"]:hover label, .sb-profile-list [data-id="envato-purchase-code"]:hover, .sb-profile-list [data-id="envato-purchase-code"]:hover label,.sb-board > .sb-admin-list .sb-scroll-area li[data-conversation-status="2"] .sb-time,.sb-select p:hover,div ul.sb-menu li.sb-active:not(:hover), .sb-select ul li.sb-active:not(:hover),.sb-board .sb-conversation .sb-list > div .sb-menu-btn:hover { color: ' .
            $admin_colors[0] .
            "; }";
        $css .=
            ".sb-btn:not(.sb-btn-white), a.sb-btn:not(.sb-btn-white),.sb-area-settings .sb-tab .sb-btn:hover, .daterangepicker td.active, .daterangepicker td.active:hover, .daterangepicker .ranges li.active,div ul.sb-menu li:hover, .sb-select ul li:hover,div.sb-select.sb-select-colors > p:hover,.sb-board > .sb-admin-list .sb-scroll-area li > .sb-notification-counter { background-color: " .
            $admin_colors[0] .
            "; }";
        $css .=
            ".sb-board > .sb-admin-list.sb-departments-show li.sb-active:before { background-color: " .
            $admin_colors[0] .
            " !important;}";
        $css .=
            ".sb-btn-icon:hover,.sb-tags-cnt > span:hover { border-color: " .
            $admin_colors[0] .
            "; color: " .
            $admin_colors[0] .
            "; }";
        $css .=
            ".sb-btn-icon:hover,.daterangepicker td.in-range { background-color: rgb(151 151 151 / 8%); }";
        $css .=
            '.sb-board .sb-user-details,.sb-admin > .sb-header,.sb-select.sb-select-colors > p:not([data-value]),.sb-table tr:hover td,.sb-board .sb-user-details .sb-user-conversations li:hover, .sb-board .sb-user-details .sb-user-conversations li.sb-active, .sb-select.sb-select-colors > p[data-value=""], .sb-select.sb-select-colors > p[data-value="-1"] {background-color: #f5f5f5  }';
        $css .=
            ".sb-board > .sb-admin-list .sb-scroll-area li:hover, .sb-board > .sb-admin-list .sb-scroll-area li.sb-active {background-color: #f5f5f5 !important; }";
        $css .=
            ".sb-profile-list > ul > li .sb-icon, .sb-profile-list > ul > li > img { color: #424242 }";
        $css .=
            ".sb-area-settings .sb-tab .sb-btn:hover, .sb-btn-white:hover, .sb-lightbox .sb-btn-white:hover { background-color: " .
            $admin_colors[0] .
            "; border-color: " .
            $admin_colors[0] .
            ";}";
        if ($admin_colors[1]) {
            $css .=
                ".sb-btn:hover, .sb-btn:active, a.sb-btn:hover, a.sb-btn:active { background-color: " .
                $admin_colors[1] .
                "}";
        }
        echo "<style>" . $css . "</style>";
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <div class="sb-main <?php echo $css_class; ?>" style="opacity: 0">
        <?php if ($logged) { ?>
            <div class="sb-header header_new">
                <aside class="sidebar sb-admin-nav collapsed" id="sidebar">

                    <div class="logo">
                        <img width="35"
                            src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>"
                            alt="Logo" class="logo-icon">
                        <div class="logo-text">
                            <h1>Nexleon Helpdesk</h1>
                            <p>Agent Admin</p>
                        </div>
                    </div>

                    <nav>
                        <ul>
                            <li><a id="sb-dashboard">
                                    <i>
                                        <div class="icon-wrapper">
                                            <span class="icon-tooltip" data-tooltip="Dashboard">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10.5 6.75C10.5 4.67893 8.82107 3 6.75 3C4.67893 3 3 4.67893 3 6.75C3 8.82107 4.67893 10.5 6.75 10.5C8.82107 10.5 10.5 8.82107 10.5 6.75Z"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M21 17.25C21 15.1789 19.3211 13.5 17.25 13.5C15.1789 13.5 13.5 15.1789 13.5 17.25C13.5 19.3211 15.1789 21 17.25 21C19.3211 21 21 19.3211 21 17.25Z"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M10.5 17.25C10.5 15.1789 8.82107 13.5 6.75 13.5C4.67893 13.5 3 15.1789 3 17.25C3 19.3211 4.67893 21 6.75 21C8.82107 21 10.5 19.3211 10.5 17.25Z"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M17.25 3V10.5M21 6.75H13.5" stroke="#5F6465" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg></span>
                                        </div>
                                    </i>
                                    <span class="label">Dashboard</span></a></li>

                            <li><a id="sb-conversations"><i>
                                        <div class="icon-wrapper">
                                            <span class="icon-tooltip" data-tooltip="Inbox">
                                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.69336 2.75C6.28605 2.91536 5.31763 3.26488 4.5846 4.00363C3.19336 5.40575 3.19336 7.66242 3.19336 12.1758C3.19336 16.6891 3.19336 18.9458 4.5846 20.3479C5.97585 21.75 8.21502 21.75 12.6934 21.75C17.1717 21.75 19.4109 21.75 20.8022 20.3479C22.1934 18.9458 22.1934 16.6891 22.1934 12.1758C22.1934 7.66242 22.1934 5.40575 20.8022 4.00363C20.0691 3.26488 19.1007 2.91536 17.6934 2.75"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M10.1934 8.25C10.6849 8.7557 11.9932 10.75 12.6934 10.75M12.6934 10.75C13.3936 10.75 14.7019 8.7557 15.1934 8.25M12.6934 10.75V2.75"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M22.1934 13.75H17.2677C16.4256 13.75 15.764 14.4536 15.3929 15.1972C14.9897 16.0051 14.1823 16.75 12.6934 16.75C11.2045 16.75 10.3971 16.0051 9.9939 15.1972C9.62278 14.4536 8.96113 13.75 8.11902 13.75H3.19336"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linejoin="round" />
                                                </svg></span>
                                        </div>
                                    </i><span class="label"> Inbox</span></a></li>
                            <?php //if ($active_areas['tickets']) { ?>
                            <li><a id="sb-tickets"><i>
                                        <div class="icon-wrapper">
                                            <span class="icon-tooltip" data-tooltip="Tickets">
                                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M22.6929 9.12895C22.626 7.58687 22.4385 6.58298 21.9132 5.78884C21.611 5.33196 21.2357 4.93459 20.8041 4.61468C19.6376 3.75 17.9919 3.75 14.7007 3.75H10.686C7.39472 3.75 5.74908 3.75 4.58256 4.61468C4.15099 4.93459 3.77561 5.33196 3.47341 5.78884C2.9482 6.58289 2.7607 7.58665 2.69377 9.12843C2.68232 9.39208 2.90942 9.59375 3.15825 9.59375C4.54403 9.59375 5.66743 10.783 5.66743 12.25C5.66743 13.717 4.54403 14.9062 3.15825 14.9062C2.90942 14.9062 2.68232 15.1079 2.69377 15.3716C2.7607 16.9134 2.9482 17.9171 3.47341 18.7112C3.77561 19.168 4.15099 19.5654 4.58256 19.8853C5.74908 20.75 7.39472 20.75 10.686 20.75H14.7007C17.9919 20.75 19.6376 20.75 20.8041 19.8853C21.2357 19.5654 21.611 19.168 21.9132 18.7112C22.4385 17.917 22.626 16.9131 22.6929 15.3711V9.12895Z"
                                                        stroke="#5F6465" stroke-width="1.5" stroke-linejoin="round" />
                                                    <path d="M13.6934 12.25H17.6934" stroke="#5F6465" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M9.69336 16.25H17.6934" stroke="#5F6465" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </i><span class="label">Tickets</span></a></li>
                            <?php //} ?>
                            <?php if ($active_areas['users']) { ?>
                                <li><a id="sb-users"><i>
                                            <div class="icon-wrapper">
                                                <span class="icon-tooltip" data-tooltip="Customers">
                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14.6934 8.75C14.6934 5.98858 12.4548 3.75 9.69336 3.75C6.93194 3.75 4.69336 5.98858 4.69336 8.75C4.69336 11.5114 6.93194 13.75 9.69336 13.75C12.4548 13.75 14.6934 11.5114 14.6934 8.75Z"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M16.6934 20.75C16.6934 16.884 13.5594 13.75 9.69336 13.75C5.82737 13.75 2.69336 16.884 2.69336 20.75"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M19.6934 9.25V15.25M22.6934 12.25H16.6934" stroke="#5F6465"
                                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </i><span class="label">Customers</span></a></li>
                            <?php } ?>
                            <!-- <li><a id="sb-chatbot"><i class="fa-solid fa-robot"></i><span> Chatbot</span></a></li> -->
                            <?php if ($active_areas['articles']) { ?>
                                <li><a id="sb-articles"><i>
                                            <div class="icon-wrapper">
                                                <span class="icon-tooltip" data-tooltip="Articles">
                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M20.4433 11.25V10.25C20.4433 6.47876 20.4433 4.59315 19.2717 3.42157C18.1001 2.25 16.2145 2.25 12.4433 2.25H11.4434C7.67219 2.25 5.78658 2.25 4.61501 3.42156C3.44344 4.59312 3.44342 6.47872 3.44339 10.2499L3.44336 14.25C3.44332 18.0212 3.44331 19.9068 4.61484 21.0784C5.78641 22.2499 7.67209 22.25 11.4433 22.25"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M7.94336 7.25H15.9433M7.94336 12.25H15.9433" stroke="#5F6465"
                                                            stroke-width="1.5" stroke-linecap="round" />
                                                        <path
                                                            d="M13.9434 21.0768V22.25H15.1168C15.5262 22.25 15.7309 22.25 15.9149 22.1738C16.099 22.0975 16.2437 21.9528 16.5332 21.6634L21.3568 16.8394C21.6298 16.5664 21.7663 16.4299 21.8393 16.2827C21.9782 16.0025 21.9782 15.6736 21.8393 15.3934C21.7663 15.2461 21.6298 15.1096 21.3568 14.8366C21.0837 14.5636 20.9472 14.4271 20.7999 14.3541C20.5197 14.2153 20.1907 14.2153 19.9105 14.3541C19.7633 14.4271 19.6267 14.5636 19.3537 14.8366L14.5301 19.6606C14.2406 19.95 14.0959 20.0947 14.0197 20.2787C13.9434 20.4628 13.9434 20.6674 13.9434 21.0768Z"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </i><span class="label">Articles</span></a></li>
                            <?php } ?>
                            <?php if ($active_areas['reports']) { ?>
                                <li><a id="sb-reports"><i>
                                            <div class="icon-wrapper">
                                                <span class="icon-tooltip" data-tooltip="Reports">
                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7.69336 18.25V16.25M12.6934 18.25V15.25M17.6934 18.25V13.25M3.19336 12.25C3.19336 7.77166 3.19336 5.53249 4.5846 4.14124C5.97585 2.75 8.21502 2.75 12.6934 2.75C17.1717 2.75 19.4109 2.75 20.8022 4.14124C22.1934 5.53249 22.1934 7.77166 22.1934 12.25C22.1934 16.7283 22.1934 18.9675 20.8022 20.3588C19.4109 21.75 17.1717 21.75 12.6934 21.75C8.21502 21.75 5.97585 21.75 4.5846 20.3588C3.19336 18.9675 3.19336 16.7283 3.19336 12.25Z"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.68555 11.7363C8.84065 11.8081 13.7275 11.4828 16.5071 7.07132M14.6857 6.53835L16.5612 6.23649C16.7898 6.20738 17.1254 6.38785 17.2079 6.60298L17.7038 8.24142"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </i><span class="label"> Reports</span></a></li>
                            <?php } ?>
                            <?php if ($active_areas['settings']) { ?>
                                <li><a id="sb-settings"><i>
                                            <div class="icon-wrapper">
                                                <span class="icon-tooltip" data-tooltip="Settings">
                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 12.3379C5 14.327 5.79018 16.2346 7.1967 17.6412C8.60322 19.0477 10.5109 19.8379 12.5 19.8379C14.4891 19.8379 16.3968 19.0477 17.8033 17.6412C19.2098 16.2346 20 14.327 20 12.3379M5 12.3379C5 10.3487 5.79018 8.44108 7.1967 7.03455C8.60322 5.62803 10.5109 4.83785 12.5 4.83785C14.4891 4.83785 16.3968 5.62803 17.8033 7.03455C19.2098 8.44108 20 10.3487 20 12.3379M5 12.3379H3.5M20 12.3379H21.5M20 12.3379H12.5L8 4.54285M4.043 15.4149L5.453 14.9019M19.548 9.77185L20.958 9.25885M5.606 18.1229L6.756 17.1589M18.246 7.51685L19.395 6.55285M8.001 20.1329L8.751 18.8329L12.502 12.3379M16.251 5.84285L17.001 4.54285M10.938 21.2009L11.198 19.7239M13.803 4.95185L14.063 3.47485M14.063 21.2009L13.803 19.7239M11.198 4.95185L10.938 3.47485M17 20.1319L16.25 18.8329M19.394 18.1229L18.245 17.1589M6.756 7.51585L5.606 6.55185M20.958 15.4159L19.548 14.9029M5.454 9.77285L4.044 9.25885"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>

                                                </span>
                                            </div>
                                        </i><span class="label">Settings</span></a></li>
                            <?php } ?>
                        </ul>
                    </nav>

                    <div class="powered-by">
                        <p class="all-reserved">&copy; All Rights Reserved</p>
                        <p class="nexln-help">Made By Nexleon Helpdesk</p>
                        <div class="powered-logo">
                            <!--img src="./account/media/dashboard-logo.svg" alt="logo" style="width: 190px; height: auto;"-->
                            <p></p>
                        </div>
                    </div>
                </aside>

                <!-- <div class="sb-admin-nav">
                <a id="sb-conversations" class="sb-active">
                    <span>
                        <?php sb_e("Conversations"); ?>
                    </span>
                </a>
                        <?php
                        if ($active_areas["users"]) {
                            echo '<a id="sb-users"><span>' .
                                sb_("Users") .
                                "</span></a>";
                        }
                        //if ($active_areas['tickets']) {
                        echo '<a id="sb-tickets"><span>' . sb_('Tickets') . '</span></a>';
                        //}
                        if ($active_areas['chatbot']) {
                            echo '<a id="sb-chatbot"><span>' . sb_('Chatbot') . '</span></a>';
                        }
                        if ($active_areas["articles"]) {
                            echo '<a id="sb-articles"><span>' .
                                sb_("Articles") .
                                "</span></a>";
                        }
                        if ($active_areas["reports"]) {
                            echo '<a id="sb-reports"><span>' .
                                sb_("Reports") .
                                "</span></a>";
                        }
                        if ($active_areas["settings"]) {
                            echo '<a id="sb-settings"><span>' .
                                sb_("Settings") .
                                "</span></a>";
                        }
                        ?>
                    </div> -->
                <!-- <div class="sb-admin-nav-right sb-menu-mobile">
                    <i class="sb-icon-menu"></i>
                    <div class="sb-desktop">
                        <div class="sb-account">
                            <img src="<?php echo SB_URL; ?>/media/user.svg" />
                            <div>
                                <a class="sb-profile">
                                    <img src="<?php echo SB_URL; ?>/media/user.svg" />
                                    <span class="sb-name"></span>
                                </a>
                                <ul class="sb-menu">
                                    <li data-value="status" class="sb-online">
                                        <?php sb_e("Online"); ?>
                                    </li>
                                    <?php if ($is_admin) {
                                        echo '<li data-value="edit-profile">' .
                                            sb_("Edit profile") .
                                            "</li>" .
                                            ($is_cloud
                                                ? sb_cloud_account_menu()
                                                : "");
                                    } ?>
                                    <li data-value="logout">
                                        <?php sb_e("Logout"); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if ($is_admin) {
                            sb_docs_link();
                            echo '<a href="#" class="sb-version">' .
                                SB_VERSION .
                                "</a>";
                        } ?>
                    </div>
                    <div class="sb-mobile">
                        <?php if (
                            $is_admin ||
                            (!$supervisor &&
                                sb_get_multi_setting(
                                    "agents",
                                    "agents-edit-user"
                                )) ||
                            ($supervisor && $supervisor["supervisor-edit-user"])
                        ) {
                            echo '<a href="#" class="edit-profile">' .
                                sb_("Edit profile") .
                                "</a>" .
                                ($is_cloud
                                    ? sb_cloud_account_menu("a")
                                    : '<a href="#" class="sb-docs">' .
                                    sb_("Docs") .
                                    "</a>") .
                                '<a href="#" class="sb-version">' .
                                sb_("Updates") .
                                "</a>";
                        } ?>
                        <a href="#" class="sb-online" data-value="status">
                            <?php sb_e("Online"); ?>
                        </a>
                        <a href="#" class="logout">
                            <?php sb_e("Logout"); ?>
                        </a>
                    </div>
                </div> -->
            </div>

            <main class="main-content" id="mainContent">
                <!-- new code update -->
                <?php
                // You can set these dynamically from user session or DB
                $user_name = ucwords($active_user['first_name'] . ' ' . $active_user['last_name']); // Dynamic
                $user_image = $active_user['profile_image']; // Dynamic
                $user_role = ucfirst($active_user['user_type']); // Static
        
                // Static
                $imgSrc = $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting("admin-icon", SB_URL . "/media/icon.svg");
                $ticketUrl = dirname(SB_URL) . "?area=tickets";
                $inboxUrl = dirname(SB_URL) . "?area=conversations";
                $header =
                    '<header>
                            <div class="header-left">
                                <a class="sb-btn sb-icon ticket-back-btn sb_btn_new m-0 d-none" href="' .
                    $ticketUrl .
                    '" >
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                    Back to Tickets
                                </a>
                                <h2 class="title mb-0">Setting</h2>
                            </div>
                            <div class="header-right">
                                <!--div class="notification">
                                    <img src="./script/media/notification.svg" alt="notification">
                                    <span class="badge">0</span>
                                </div-->
                                <!-- User Profile Dropdown -->
                                <div class="sb-admin-nav-right user_menu user-profile user_avatar">
                                    <a class="sb-profile">
                                        <img class="avatar_img" src="' . $user_image . '" alt="' . $user_name . '" />
                                        <span class="user-initials avatar_initials" style="display:none;">
                                                <span class="initials avatar_name"></span>
                                            </span>
                                        <div class="user-details">
                                            <span class="sb-name">' .
                    $user_name .
                    '</span>
                                            <span class="sb-role">' .
                    $user_role .
                    '</span>
                                        </div>
                                    </a>
                                    <ul class="sb-menu">
                                            <li class="menu_head">
                                                <img class="avatar_img" src="' . $user_image . '" alt="' . $user_name . '" />
                                                <span class="user-initials avatar_initials" style="display:none;">
                                                <span class="initials avatar_name"></span>
                                            </span>
                                                <div class="user-details">
                                                    <span class="sb-name">' .
                    $user_name .
                    '</span>
                                                    <span class="sb-role">' .
                    $user_role .
                    '</span>
                                                </div>
                                            </li>
                                        <li data-value="status" class="sb-online">Online</li>';
                if ($is_admin) {
                    $header .=
                        '<li data-value="edit-profile">' .
                        sb_("Edit profile") .
                        "</li>" .
                        ($is_cloud ? sb_cloud_account_menu() : "");
                }
                $header .= '</ul>
                                </div>
                                <!-- Logout Button -->
                                <div data-value="logout" class="logout">
                                    <img src="./script/media/logout-icon.svg" alt="logout">
                                </div>
                            </div>
                        </header>';
                ?>
                <!-- new code update -->
                <!-- old code -->
                <!--?php
                $imgSrc = $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting("admin-icon", SB_URL . "/media/icon.svg");
                $ticketUrl = dirname(SB_URL) . '?area=tickets';
                $header = '<header>
                                 <div class="header-left">
                                    <a class="sb-btn sb-icon ticket-back-btn sb_btn_new m-0 d-none" href="' . $ticketUrl . '" >
                                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                        Back to Tickets
                                    </a>
                                    <h2 class="title">Setting</h2>
                                </div>
                                <div class="header-right">
                                    div class="notification">
                                        <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                        <span class="badge">0</span>
                                    </div>
                                    <div class="notification">
                                        <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                        <span class="badge">0</span>
                                    </div>
                                    <div class="sb-admin-nav-right user_menu user-profile user_avatar">
                                        <a class="sb-profile">
                                            <img class="avatar_img" src="" data-name="" />
                                            <span class="user-initials avatar_initials" style="display:none;">
                                                <span class="initials avatar_name"></span>
                                            </span>
                                        </a>
                                        <ul class="sb-menu">
                                            <li class="menu_head">
                                                <img class="avatar_img" src=""  data-name="" />
                                                <span class="user-initials avatar_initials" style="display:none;">
                                                    <span class="initials avatar_name"></span>
                                                </span>
                                                <span class="sb-name"></span>
                                            </li>
                                            <li data-value="status" class="sb-online">Online</li>';
                if ($is_admin) {
                    $header .= '<li data-value="edit-profile">' . sb_('Edit profile') . '</li>'
                        . ($is_cloud ? sb_cloud_account_menu() : '');
                }
                $header .= '<li data-value="logout">Logout</li>
                                        </ul>
                                    </div>
                                </div>
                            </header>';
                ?-->

                <!-- old code -->

                <div class="sb-area-dashboard screen-size">
                    <main>
                        <?php echo $header; ?>
                        <div class="container new_container py-3">
                            <div class="row">
                                <div class="col-md-8 p-0">
                                    <div class="px-3 pe-md-2 clmn-gap">
                                        <section class="dashboard-metrics">
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EFF4FF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #487fff;">
                                                            <svg width="21" height="23" viewBox="0 0 21 23" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M13 12.29C14.3261 12.29 15.5979 12.8168 16.5355 13.7545C17.4732 14.6922 18 15.964 18 17.29V18.29C18 18.8205 17.7893 19.3292 17.4142 19.7043C17.0391 20.0793 16.5304 20.29 16 20.29H2C1.46957 20.29 0.960859 20.0793 0.585786 19.7043C0.210714 19.3292 0 18.8205 0 18.29V17.29C0 15.964 0.526784 14.6922 1.46447 13.7545C2.40215 12.8168 3.67392 12.29 5 12.29H13ZM18.414 7.37104C18.5935 7.18979 18.8356 7.08401 19.0905 7.07536C19.3455 7.0667 19.5942 7.15584 19.7856 7.3245C19.977 7.49317 20.0967 7.72862 20.1202 7.98266C20.1437 8.2367 20.0692 8.49012 19.912 8.69104L19.828 8.78604L17 11.614C16.8278 11.7862 16.5987 11.8896 16.3557 11.9049C16.1127 11.9202 15.8724 11.8463 15.68 11.697L15.586 11.614L14.172 10.2C13.9907 10.0205 13.885 9.77847 13.8763 9.52349C13.8677 9.26851 13.9568 9.01987 14.1255 8.82846C14.2941 8.63704 14.5296 8.51734 14.7836 8.49384C15.0377 8.47033 15.2911 8.54482 15.492 8.70204L15.586 8.78604L16.293 9.49304L18.414 7.37104ZM9 0.290039C10.3261 0.290039 11.5979 0.816823 12.5355 1.75451C13.4732 2.69219 14 3.96396 14 5.29004C14 6.61612 13.4732 7.88789 12.5355 8.82557C11.5979 9.76326 10.3261 10.29 9 10.29C7.67392 10.29 6.40215 9.76326 5.46447 8.82557C4.52678 7.88789 4 6.61612 4 5.29004C4 3.96396 4.52678 2.69219 5.46447 1.75451C6.40215 0.816823 7.67392 0.290039 9 0.290039Z"
                                                                    fill="white" />
                                                            </svg>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>New Users</h3>
                                                            <p>0</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="new_users_chart">
                                                            <canvas class="mt-0" id="new_users_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">Increase by <span>0</span> in last 7 days</div>
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EAFFF9 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #45b369;">
                                                            <!--i class="fa-solid fa-user-plus" style="color: #ffffff;"></i-->
                                                            <img src="./script/media/total-user.svg" alt="Total User">
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Total Users</h3>
                                                            <p class="total-users"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="active_users_chart">
                                                            <canvas class="mt-0" id="active_users_chart"></canvas>
                                                        </div>
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <!--div class="metric-increase">Increase by <span class="total-users-increase"></span>% in last 7 days</div-->
                                                <!-- code update -->
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-users-increase"></span><span>%</span>
                                                    </span>&nbsp;in last 7 days
                                                </div>
                                                <!-- code update -->
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF5E9 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #f4941e;">
                                                            <!--i class="fa-solid fa-ticket" style="color: #ffffff;"></i-->
                                                            <img src="./script/media/total-conversations.svg"
                                                                alt="Total Conversations">
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Total Conversation</h3>
                                                            <p class="total-tickets-created">3,200</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="ticket_created_chart">
                                                            <canvas class="mt-0" id="ticket_created_chart"></canvas>
                                                        </div>
                                                        <!-- <script>
                                                            
                                                        </script> -->
                                                    </div>
                                                </div>
                                                <!--div class="metric-increase">Increase by <span class="total-tickets-increase"></span>% in last 7 days</div-->
                                                <!-- code update -->
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-tickets-increase"></span><span>%</span>
                                                    </span>&nbsp;in last 7 days
                                                </div>
                                                <!-- code update -->
                                            </div>
                                        </section>

                                        <section class="dashboard-metrics">
                                            <!--  -->
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #F3EEFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #8252E9;">
                                                            <!--i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i-->
                                                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_1679_2518)">
                                                                    <path
                                                                        d="M21.0938 5.49707H3.90625C2.61717 5.49707 1.5625 6.55174 1.5625 7.84082V18.7783C1.5625 20.0674 2.61717 21.1221 3.90625 21.1221H18.2656C18.2031 20.9268 18.1641 20.7236 18.1484 20.5127C16.9219 20.4346 15.9453 19.4267 15.9453 18.1846C15.9453 16.9424 16.9219 15.9346 18.1484 15.8565C18.2188 14.6299 19.2266 13.6611 20.4766 13.6611C21.7187 13.6611 22.7266 14.6299 22.8047 15.8565C23.0234 15.872 23.2344 15.9111 23.4375 15.9893V7.84082C23.4375 6.55174 22.3828 5.49707 21.0938 5.49707ZM7.42188 18.3877H5.07812C4.64845 18.3877 4.29688 18.0361 4.29688 17.6064C4.29688 17.1768 4.64845 16.8252 5.07812 16.8252H7.42188C7.85155 16.8252 8.20312 17.1768 8.20312 17.6064C8.20312 18.0361 7.85155 18.3877 7.42188 18.3877ZM4.29688 14.8721C4.29688 14.4424 4.64845 14.0908 5.07812 14.0908H6.25C6.67968 14.0908 7.03125 14.4424 7.03125 14.8721C7.03125 15.3017 6.67968 15.6533 6.25 15.6533H5.07812C4.64845 15.6533 4.29688 15.3017 4.29688 14.8721ZM19.9219 11.3564H5.07812C4.64845 11.3564 4.29688 11.0049 4.29688 10.5752C4.29688 10.1455 4.64845 9.79395 5.07812 9.79395H19.9219C20.3516 9.79395 20.7031 10.1455 20.7031 10.5752C20.7031 11.0049 20.3516 11.3564 19.9219 11.3564Z"
                                                                        fill="white" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M22.7207 17.2589C22.7779 17.3196 22.8226 17.391 22.8522 17.4689C22.8818 17.5469 22.8958 17.6299 22.8933 17.7132C22.8908 17.7966 22.8719 17.8786 22.8377 17.9546C22.8035 18.0307 22.7547 18.0992 22.694 18.1564L19.7934 20.8898C19.6722 21.004 19.5109 21.066 19.3445 21.0624C19.178 21.0588 19.0195 20.99 18.9034 20.8707L17.6799 19.6138C17.5625 19.4932 17.4978 19.3308 17.5001 19.1625C17.5023 18.9942 17.5714 18.8336 17.692 18.7162C17.8127 18.5987 17.975 18.534 18.1434 18.5363C18.3117 18.5386 18.4722 18.6076 18.5897 18.7283L19.3774 19.5375L21.8237 17.2324C21.9462 17.1169 22.1095 17.0549 22.2778 17.0598C22.4461 17.0648 22.6055 17.1364 22.7209 17.2589"
                                                                        fill="white" />
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_1679_2518">
                                                                        <rect width="25" height="25" fill="white"
                                                                            transform="translate(0 0.80957)" />
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Tickets Created</h3>
                                                            <p class="total-conversations"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="total_conversations_chart">
                                                            <canvas class="mt-0" id="conversations_chart"></canvas>
                                                        </div>
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <!--div class="metric-increase">Increase by <span class="total-conversations-increase"></span>% in last 7 days</div-->
                                                <!-- code update  -->
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-conversations-increase"></span><span>%</span>
                                                    </span>&nbsp;in last 7 days
                                                </div>
                                                <!-- code update -->
                                            </div>
                                            <!--  -->
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF2FE 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #DE3ACE;">
                                                            <!--i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i-->
                                                            <svg width="25" height="20" viewBox="0 0 25 20" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M19.5938 0.6875H2.40625C1.11717 0.6875 0.0625 1.74217 0.0625 3.03125V13.9688C0.0625 15.2578 1.11717 16.3125 2.40625 16.3125H16.7656C16.7031 16.1172 16.6641 15.9141 16.6484 15.7031C15.4219 15.625 14.4453 14.6172 14.4453 13.375C14.4453 12.1328 15.4219 11.125 16.6484 11.0469C16.7188 9.82031 17.7266 8.85157 18.9766 8.85157C20.2187 8.85157 21.2266 9.82031 21.3047 11.0469C21.5234 11.0625 21.7344 11.1015 21.9375 11.1797V3.03125C21.9375 1.74217 20.8828 0.6875 19.5938 0.6875ZM5.92188 13.5781H3.57812C3.14845 13.5781 2.79688 13.2266 2.79688 12.7969C2.79688 12.3672 3.14845 12.0156 3.57812 12.0156H5.92188C6.35155 12.0156 6.70312 12.3672 6.70312 12.7969C6.70312 13.2266 6.35155 13.5781 5.92188 13.5781ZM2.79688 10.0625C2.79688 9.63282 3.14845 9.28125 3.57812 9.28125H4.75C5.17968 9.28125 5.53125 9.63282 5.53125 10.0625C5.53125 10.4922 5.17968 10.8438 4.75 10.8438H3.57812C3.14845 10.8438 2.79688 10.4922 2.79688 10.0625ZM18.4219 6.54688H3.57812C3.14845 6.54688 2.79688 6.1953 2.79688 5.76562C2.79688 5.33595 3.14845 4.98438 3.57812 4.98438H18.4219C18.8516 4.98438 19.2031 5.33595 19.2031 5.76562C19.2031 6.1953 18.8516 6.54688 18.4219 6.54688Z"
                                                                    fill="white" />
                                                                <path
                                                                    d="M18.75 8C15.5744 8 13 10.5744 13 13.75C13 16.9256 15.5744 19.5 18.75 19.5C21.9256 19.5 24.5 16.9256 24.5 13.75C24.5 10.5744 21.9256 8 18.75 8Z"
                                                                    fill="#ECEFF1" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M18.75 10C19.1642 10 19.5 10.3358 19.5 10.75V13.7865L21.0854 14.5792C21.4559 14.7644 21.6061 15.2149 21.4208 15.5854C21.2356 15.9559 20.7851 16.1061 20.4146 15.9208L18.4146 14.9208C18.1605 14.7938 18 14.5341 18 14.25V10.75C18 10.3358 18.3358 10 18.75 10Z"
                                                                    fill="#8B98A6" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M18.75 8V19.5C15.5744 19.5 13 16.9256 13 13.75C13 10.5744 15.5744 8 18.75 8Z"
                                                                    fill="#CFD8DC" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M18.75 10C18.3358 10 18 10.3358 18 10.75V14.25C18 14.5341 18.1605 14.7938 18.4146 14.9208L18.75 15.0885V10Z"
                                                                    fill="#7D8995" />
                                                            </svg>

                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Tickets Pending</h3>
                                                            <p class="ticket-resolved"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="ticket_resolved_chart">
                                                            <canvas class="mt-0" id="ticket_resolved_chart"></canvas>
                                                        </div>
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <!--div class="metric-increase">Increase by <span class="total-resolved-tickets-increase"></span>% in last 7 days</div-->
                                                <!-- code update -->
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-resolved-tickets-increase"></span><span>%</span>
                                                    </span>&nbsp;in last 7 days
                                                </div>
                                                <!-- code update -->
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EEFBFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #00B8F2;">

                                                            <!-- <i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i> -->
                                                            <svg width="24" height="18" viewBox="0 0 24 18" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M19.5312 0.290039H2.34375C1.05467 0.290039 0 1.34471 0 2.63379V13.5713C0 14.8603 1.05467 15.915 2.34375 15.915H16.7031C16.6406 15.7197 16.6016 15.5166 16.5859 15.3056C15.3594 15.2275 14.3828 14.2197 14.3828 12.9775C14.3828 11.7353 15.3594 10.7275 16.5859 10.6494C16.6563 9.42285 17.6641 8.45411 18.9141 8.45411C20.1562 8.45411 21.1641 9.42285 21.2422 10.6494C21.4609 10.665 21.6719 10.7041 21.875 10.7822V2.63379C21.875 1.34471 20.8203 0.290039 19.5312 0.290039ZM5.85938 13.1807H3.51562C3.08595 13.1807 2.73438 12.8291 2.73438 12.3994C2.73438 11.9697 3.08595 11.6182 3.51562 11.6182H5.85938C6.28905 11.6182 6.64062 11.9697 6.64062 12.3994C6.64062 12.8291 6.28905 13.1807 5.85938 13.1807ZM2.73438 9.66504C2.73438 9.23536 3.08595 8.88379 3.51562 8.88379H4.6875C5.11718 8.88379 5.46875 9.23536 5.46875 9.66504C5.46875 10.0947 5.11718 10.4463 4.6875 10.4463H3.51562C3.08595 10.4463 2.73438 10.0947 2.73438 9.66504ZM18.3594 6.14941H3.51562C3.08595 6.14941 2.73438 5.79784 2.73438 5.36816C2.73438 4.93849 3.08595 4.58691 3.51562 4.58691H18.3594C18.7891 4.58691 19.1406 4.93849 19.1406 5.36816C19.1406 5.79784 18.7891 6.14941 18.3594 6.14941Z"
                                                                    fill="white" />
                                                                <path
                                                                    d="M19 18C21.7614 18 24 15.7614 24 13C24 10.2386 21.7614 8 19 8C16.2386 8 14 10.2386 14 13C14 15.7614 16.2386 18 19 18Z"
                                                                    fill="#868686" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M18.2904 13.6607L20.6449 11.3055C20.9138 11.0365 21.3511 11.0365 21.62 11.3055C21.889 11.5751 21.889 12.0117 21.62 12.2813L18.778 15.1234C18.509 15.3924 18.0718 15.3924 17.8028 15.1234L16.3814 13.702C16.1124 13.4331 16.1124 12.9958 16.3814 12.7269C16.6504 12.4579 17.0876 12.4579 17.3566 12.7269L18.2904 13.6607Z"
                                                                    fill="white" />
                                                            </svg>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Tickets Resolved</h3>
                                                            <p class="total-conversations"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="total_conversations_chart">
                                                            <canvas class="mt-0" id="conversations_chart"></canvas>
                                                        </div>
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-conversations-increase"></span><span>%</span>
                                                    </span>&nbsp;in last 7 days
                                                </div>

                                            </div>
                                        </section>
                                        <!--section class="dashboard-metrics">
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #F3EEFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #8252E9;"-->
                                        <!--i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i-->
                                        <!--img src="./script/media/total-conversations.svg" alt="Total Conversations">
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Total Conversations</h3>
                                                            <p class="total-conversations"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="total_conversations_chart">
                                                            <canvas class="mt-0" id="conversations_chart"></canvas>
                                                        </div>
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div-->
                                        <!--div class="metric-increase">Increase by <span class="total-conversations-increase"></span>% in last 7 days</div-->
                                        <!-- code update  -->
                                        <!--div class="metric-increase">
                                                        Increase by 
                                                        <span class="increase-pill">
                                                            <span class="total-conversations-increase"></span><span>%</span>
                                                        </span>&nbsp;in last 7 days </div-->
                                        <!-- code update -->
                                        <!--/div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF2FE 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #DE3ACE;"-->
                                        <!--i class="fa-solid fa-hourglass-start" style="color: #ffffff;"></i-->
                                        <!--img src="./script/media/avg-response-time.svg" alt="Avg Response Time">
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3> Avg. Response Time</h3-->
                                        <!-- <p id="avg_response_time">0h 0m 0s</p> -->
                                        <!--div id="avg_response_time_container"></div>
                                                        </div>
                                                    </div-->
                                        <!-- <div class="w-100">
                                                    <div class="avg_response_chart">
                                                        <canvas class="mt-0" id="avg_response_chart"></canvas>
                                                    </div>
                                                    <script>
                                                        const avg_responseCtx = document.getElementById('avg_response_chart').getContext('2d');
                                                        const gradient5 = avg_responseCtx.createLinearGradient(0, 0, 0, 200);
                                                        gradient5.addColorStop(0, 'rgba(220, 61, 235, 0.2)');
                                                        gradient5.addColorStop(1, 'rgba(220, 61, 235, 0)');
                                                        new Chart(avg_responseCtx, {
                                                            type: 'line',
                                                            data: {
                                                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                                                                datasets: [{
                                                                    data: [0, 5, 12, 3, 5, 7],
                                                                    borderColor: '#DE3ACE',
                                                                    backgroundColor: gradient5,
                                                                    fill: true,
                                                                    tension: 0.4,
                                                                    pointRadius: 0,
                                                                    pointHoverRadius: 0,
                                                                    borderWidth: 2
                                                                }]
                                                            },
                                                            options: {
                                                                responsive: true,
                                                                plugins: {
                                                                    legend: {
                                                                        display: false
                                                                    },
                                                                    tooltip: {
                                                                        enabled: false
                                                                    }
                                                                },
                                                                scales: {
                                                                    x: {
                                                                        grid: {
                                                                            display: false
                                                                        },
                                                                        ticks: {
                                                                            display: false
                                                                        },
                                                                        border: {
                                                                            display: false
                                                                        }
                                                                    },
                                                                    y: {
                                                                        grid: {
                                                                            display: false
                                                                        },
                                                                        ticks: {
                                                                            display: false
                                                                        },
                                                                        border: {
                                                                            display: false
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    </script>
                                                </div> -->
                                        <!--/div>

                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EEFBFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #00B8F2;"-->
                                        <!--i class="fa-solid fa-ticket" style="color: #ffffff;"></i-->
                                        <!--img src="./script/media/agent-satisfaction.svg" alt="Agent Satisfaction">
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Agent Satisfaction</h3>
                                                            <p></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="agent_chart">
                                                            <canvas class="mt-0" id="agent_chart"></canvas>
                                                        </div-->
                                        <!--script>
                                                            const agentCtx = document.getElementById('agent_chart').getContext('2d');
                                                            const gradient6 = agentCtx.createLinearGradient(0, 0, 0, 200);
                                                            gradient6.addColorStop(0, 'rgba(61, 186, 235, 0.2)');
                                                            gradient6.addColorStop(1, 'rgba(61, 186, 235, 0)');
                                                            new Chart(agentCtx, {
                                                                type: 'line',
                                                                data: {
                                                                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                                                                    datasets: [{
                                                                        data: [0, 5, 12, 3, 5, 7],
                                                                        borderColor: '#00B8F2',
                                                                        backgroundColor: gradient6,
                                                                        fill: true,
                                                                        tension: 0.4,
                                                                        pointRadius: 0,
                                                                        pointHoverRadius: 0,
                                                                        borderWidth: 2
                                                                    }]
                                                                },
                                                                options: {
                                                                    responsive: true,
                                                                    plugins: {
                                                                        legend: {
                                                                            display: false
                                                                        },
                                                                        tooltip: {
                                                                            enabled: false
                                                                        }
                                                                    },
                                                                    scales: {
                                                                        x: {
                                                                            grid: {
                                                                                display: false
                                                                            },
                                                                            ticks: {
                                                                                display: false
                                                                            },
                                                                            border: {
                                                                                display: false
                                                                            }
                                                                        },
                                                                        y: {
                                                                            grid: {
                                                                                display: false
                                                                            },
                                                                            ticks: {
                                                                                display: false
                                                                            },
                                                                            border: {
                                                                                display: false
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            });
                                                        </script-->
                                        <!--/div>
                                                </div>

                                            </div>
                                        </section-->
                                        <section class="main-charts">
                                            <div class="card p-3 main-charts-card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="head mb-1">Ticket Support Board</h6>
                                                        <p class="sub_head">Monthly overview of support ticket activity</p>
                                                    </div>
                                                    <!-- <select class="form-select form-select-sm w-auto">
                                                        <option>Yearly</option>
                                                        <option>Monthly</option>
                                                    </select> -->
                                                </div>
                                                <div class="d-flex justify-content-center gap-3 mb-3 flex-wrap flex-md-nowrap">
                                                    <div class="button_ext">
                                                        <!--i class="fa-solid fa-ticket" style="color: #000;"></i-->
                                                        <img src="./script/media/created.svg" alt="Created">
                                                        <div>
                                                            <div><strong>Created</strong></div>
                                                            <div class="tickets-created"></div>
                                                        </div>
                                                    </div>
                                                    <div class="button_ext">
                                                        <!--i class="fa-solid fa-ticket" style="color: #000;"></i-->
                                                        <img src="./script/media/resolved.svg" alt="Resolved">
                                                        <div>
                                                            <div><strong>Resolved</strong></div>
                                                            <div class="tickets-resolved"></div>
                                                        </div>
                                                    </div>
                                                    <div class="button_ext">
                                                        <!--i class="fa-solid fa-ticket" style="color: #000;"></i-->
                                                        <img src="./script/media/pending.svg" alt="Pending">
                                                        <div>
                                                            <div><strong>Pending</strong></div>
                                                            <div class="tickets-pending"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="chart-placeholder" style="height: 350px;">Bar Chart Placeholder</div> -->
                                                <div class="monthlyBarChart">
                                                    <canvas id="monthlyBarChart"></canvas>
                                                </div>
                                                <script>

                                                </script>
                                            </div>
                                        </section>
                                        <section class="main-charts high-inrs">
                                            <div class="customer-overview">
                                                <div class="customer-overview-card card p-3">
                                                    <div class="main-charts tables clmn-gap">
                                                        <div class="bg-white d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="head mb-1">All Tickets</h6>
                                                            </div>
                                                            <p class="label_blue mb-0"><a class="mr-2"
                                                                    href="<?php echo $ticketUrl; ?>">View
                                                                    All</a>
                                                            </p>
                                                        </div>
                                                        <div class="seprator"></div>
                                                        <?php
                                                        function sb_get_priorities()
                                                        {
                                                            $priorities = sb_db_get(
                                                                "SELECT * FROM priorities",
                                                                false
                                                            );
                                                            return $priorities;
                                                        }
                                                        function sb_get_statues()
                                                        {
                                                            $status = sb_db_get(
                                                                "SELECT * FROM ticket_status",
                                                                false
                                                            );
                                                            return $status;
                                                        }
                                                        $statues = sb_get_statues();
                                                        $priorities = sb_get_priorities();
                                                        ?>
                                                        <div id="ticket_statues">
                                                            <ul class="status-list dropdown-menu">
                                                                <?php foreach (
                                                                    $statues
                                                                    as $status
                                                                ) {
                                                                    echo '<li data-status="' . $status["name"] . '" class="" data-color="' . $status["color"] . '" value="' . $status["id"] . '">
                                                                        <a class="dropdown-item" href="#"> ' . $status["name"] . '</a></li>';
                                                                } ?>
                                                            </ul>
                                                        </div>

                                                        <div id="ticket_priorities">
                                                            <ul class="priority-list dropdown-menu">
                                                                <?php foreach (
                                                                    $priorities
                                                                    as $priority
                                                                ) {
                                                                    echo '<li data-status="' . $priority["name"] . '" class="" data-color="' . $priority["color"] . '" value="' . $priority["id"] . '">
                                                                <a class="dropdown-item" href="#">' . $priority["name"] . '</a></li>';
                                                                } ?>
                                                            </ul>
                                                        </div>
                                                        <div class="new_table sb-area-tickets-dash">
                                                            <div class="sb-scroll-area">
                                                                <table
                                                                    class="sb-table sb-table-tickets sb_table_new sb-table-tickets-dash">
                                                                    <thead>
                                                                        <tr>
                                                                            <th data-field="title">
                                                                                ID
                                                                            </th>
                                                                            <th data-field="title">
                                                                                Subject
                                                                            </th>
                                                                            <th data-field="assigned-to">
                                                                                Assigned To
                                                                            </th>
                                                                            <th data-field="creation-date">
                                                                                Creation Date
                                                                            </th>
                                                                            <th data-field="status">
                                                                                <?php sb_e(
                                                                                    "Status"
                                                                                ); ?>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr data-ticket-id="">
                                                                            <td class="sb-td-id">Bug fix: Login issue</td>
                                                                            <td class="sb-td-subject">Bug fix: Login issue</td>
                                                                            <td class="sb-td-tags">Kathryn Murphy</td>
                                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                                            <td class="sb-td-status"><span class="span-border"
                                                                                    style="color:#FF0000;border:1px solid #FF0000;">Open</span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                    </div>
                                </div>
                                <div class="col-md-4 p-0">
                                    <div class="px-3 ps-md-2 mt-3 mt-md-0 clmn-gap">
                                        <section class="main-charts mb-3">
                                            <div class="card p-3 tickets_activity_card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="head mb-1">Ticket Activity</h6>
                                                        <p class="sub_head">Week support summary</p>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-end">
                                                        <h6 class="head mb-1 total-tickets"><span>0</span> Tickets</h6>
                                                        <p class="green_badge new-tickets">+<span>0</span> new</p>
                                                    </div>
                                                </div>
                                                <div class="ticket_activity_chart">
                                                    <canvas id="ticket_activity_chart"></canvas>
                                                </div>
                                                <script>

                                                </script>
                                            </div>
                                        </section>
                                        <section class="main-charts mb-3 d-none">
                                            <div class="card p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold">Campaigns</h6>
                                                    <select class="form-select form-select-sm w-auto">
                                                        <option>Yearly</option>
                                                        <option>Monthly</option>
                                                    </select>
                                                </div>
                                                <div class="progress-item chat">
                                                    <div class="left">
                                                        <i class="fa-brands fa-rocketchat"></i>
                                                        <div class="label">Live Chat</div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="progress-bar">
                                                            <div class="progress-fill" style="width: 80%;"></div>
                                                        </div>
                                                        <div class="percentage">80%</div>
                                                    </div>
                                                </div>
                                                <div class="progress-item email">
                                                    <div class="left">
                                                        <i class="fa-solid fa-envelope"></i>
                                                        <div class="label">Email Support</div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="progress-bar">
                                                            <div class="progress-fill" style="width: 60%;"></div>
                                                        </div>
                                                        <div class="percentage">60%</div>
                                                    </div>
                                                </div>
                                                <div class="progress-item fb">
                                                    <div class="left">
                                                        <i class="fa-brands fa-square-facebook"></i>
                                                        <div class="label">Facebook</div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="progress-bar">
                                                            <div class="progress-fill" style="width: 40%;"></div>
                                                        </div>
                                                        <div class="percentage">40%</div>
                                                    </div>
                                                </div>
                                                <div class="progress-item wa">
                                                    <div class="left">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                        <div class="label">WhatsApp</div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="progress-bar">
                                                            <div class="progress-fill" style="width: 70%;"></div>
                                                        </div>
                                                        <div class="percentage">70%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <section class="main-charts recent-message">
                                            <div class="card p-3 messages-list">
                                                <div class="bg-white d-flex align-items-center justify-content-between">
                                                    <h6 class="head">Recent Messages</h6>
                                                    <p class="label_blue mb-0">
                                                        <a class="mr-2" href="<?php echo $inboxUrl;?>">View
                                                        All</a>
                                                    </p>
                                                </div>
                                                <div class="seprator"></div>
                                                <div class="recent card p-3">
                                                    <ul class="recent-messages list-unstyled" style="min-height:254px;">
                                                        No massge found
                                                    </ul>
                                                </div>
                                                <div class="div"></div>
                                            </div>
                                        </section>

                                        <div class="customer-overview-card card p-4 customer-overview-chart">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold">Customer Overview</h6>
                                                <select id="customer-overview" class="form-select form-select-sm w-auto">
                                                    <option value="month" selected>Last Month</option>
                                                    <option value="year">Last Year</option>
                                                    <!-- <option>Monthly</option> -->
                                                </select>
                                            </div>
                                            <!-- Donut Chart Block -->
                                            <div class="overview_chart my-auto">
                                                <ul class="legend" style="list-style: none; padding: 0;">
                                                    <span class="filter-range"></span>
                                                    <li><span class="total"></span><label></label></li>
                                                    <li><span class="new"></span><label></label></li>
                                                    <!-- <li><span class="active"></span><label>Active: 4</label></li> -->
                                                </ul>
                                                <div id="chart-container"
                                                    class="d-flex align-items-center justify-content-center flex-column">
                                                    <canvas id="donutChart"
                                                        style="max-height: 150px; max-width: 300px;"></canvas>
                                                    <div class="chart-center">
                                                        <p class="mb-1"><strong>Customer Report</strong></p>
                                                        <pre></pre>
                                                    </div>
                                                </div>
                                                <!-- <script>
                                                        const ctx = document.getElementById('donutChart').getContext('2d');
                                                        new Chart(ctx, {
                                                            type: 'doughnut',
                                                            data: {
                                                                labels: ['Total', 'New', 'Active'],
                                                                datasets: [{
                                                                    data: [500, 500, 1500],
                                                                    backgroundColor: ['#4CAF50', '#FFA726', '#4285F4'],
                                                                    borderWidth: 0
                                                                }]
                                                            },
                                                            options: {
                                                                cutout: '70%',
                                                                rotation: -90,
                                                                circumference: 180,
                                                                plugins: {
                                                                    legend: {
                                                                        display: false
                                                                    },
                                                                    tooltip: {
                                                                        enabled: true
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    </script> -->
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--div class="row mt-3">
                                <div class="col-md-8 p-0">
                                    <div class="px-3 main-charts clmn-gap">
                                        <div class="bg-white">
                                            <h6 class="head mb-1">Recent Messages</h6>
                                        </div>
                                        <div class="seprator"></div>
                                        <div class="recent card p-3">
                                            <ul class="recent-messages list-unstyled" style="min-height:254px;">
                                                No massge found
                                            </ul>
                                        </div>
                                        <div class="div"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 p-0">
                                    <div class="px-3 main-charts tables clmn-gap">
                                        <div class="bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="head mb-1">All Tickets</h6>
                                            </div>
                                            <p class="label_blue"><a class="mr-2" href="<?php echo $ticketUrl; ?>">View All</a>
                                            </p>
                                        </div-->
                            <!-- tickets_table = tickets_area.find('.sb-table-tickets');
                                        tickets_table_menu = tickets_area.find('.sb-menu-tickets'); -->
                            <!--div class="seprator"></div-->
                            <!--?php
                                        function sb_get_priorities()
                                        {
                                            $priorities = sb_db_get(
                                                "SELECT * FROM priorities",
                                                false
                                            );
                                            return $priorities;
                                        }
                                        function sb_get_statues()
                                        {
                                            $status = sb_db_get(
                                                "SELECT * FROM ticket_status",
                                                false
                                            );
                                            return $status;
                                        }
                                        $statues = sb_get_statues();
                                        $priorities = sb_get_priorities();
                                        ?>
                                        <div id="ticket_statues">
                                            <ul class="status-list"-->
                            <!--?php foreach (
                                                    $statues
                                                    as $status
                                                ) {
                                                    echo '<li data-status="' .
                                                        $status["name"] .
                                                        '" class="" data-color="' .
                                                        $status["color"] .
                                                        '" value="' .
                                                        $status["id"] .
                                                        '">
                                                    <span class="status-dot"></span> ' .
                                                        $status["name"] .
                                                        '
                                                </li>';
                                                } ?>
                                            </ul>
                                        </div>

                                        <div id="ticket_priorities">
                                            <ul class="priority-list"-->
                            <!--?php foreach (
                                                    $priorities
                                                    as $priority
                                                ) {
                                                    echo '<li data-status="' .
                                                        $priority["name"] .
                                                        '" class="" data-color="' .
                                                        $priority["color"] .
                                                        '" value="' .
                                                        $priority["id"] .
                                                        '">
                                                    <span class="status-dot"></span> ' .
                                                        $priority["name"] .
                                                        '
                                                </li>';
                                                } ?>
                                            </ul>
                                        </div>
                                        <div class="new_table sb-area-tickets-dash">
                                            <div class="sb-scroll-area">
                                                <table class="sb-table sb-table-tickets sb_table_new sb-table-tickets-dash">
                                                    <thead>
                                                        <tr>
                                                            <th data-field="title">
                                                                Ticket Title
                                                            </th>
                                                            <th data-field="assigned-to">
                                                                Assigned To
                                                            </th>
                                                            <th data-field="creation-date">
                                                                Creation Date
                                                            </th>
                                                            <th data-field="status"-->
                            <!--?php sb_e(
                                                                    "Status"
                                                                ); ?>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">Bug fix: Login issue</td>
                                                            <td class="sb-td-tags">Kathryn Murphy</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border"
                                                                    style="color:#FF0000;border:1px solid #FF0000;">Open</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div-->
                            <!-- <div class="col-md-4 p-0">
                                    <div class="px-3 main-charts">
                                        <div class="p-3 card">
                                            <div class="mb-5 d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="head mb-1">Chat Volume</h6>
                                                    <p class="sub_head">Weekly Report</p>
                                                </div>
                                            </div-->
                            <!-- <div class="chart-placeholder" style="height: 250px;">Line Chart Placeholder</div> -->
                            <!--div class="chatVolChart">
                                                <canvas id="chatVolChart"></canvas>
                                            </div>
                                            <script>
                                                const chatVolCtx = document.getElementById('chatVolChart').getContext('2d');
                                                new Chart(chatVolCtx, {
                                                    type: 'bar',
                                                    data: {
                                                        labels: ['Mon', 'Tues', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                                        datasets: [{
                                                                label: 'Reply Done: 100',
                                                                data: [30, 60, 40, 50, 30, 45, 40],
                                                                backgroundColor: '#4CAF50',
                                                                borderRadius: 4,
                                                                barThickness: 5
                                                            },
                                                            {
                                                                label: 'Pending: 500',
                                                                data: [70, 140, 80, 120, 70, 135, 110],
                                                                backgroundColor: '#4285F4',
                                                                borderRadius: 4,
                                                                barThickness: 5
                                                            },
                                                            {
                                                                label: 'Overdue: 1500',
                                                                data: [50, 120, 60, 90, 80, 95, 85],
                                                                backgroundColor: '#FFA726',
                                                                borderRadius: 4,
                                                                barThickness: 5
                                                            }
                                                        ]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                                labels: {
                                                                    usePointStyle: true,
                                                                    pointStyle: 'rectRounded',
                                                                    padding: 20
                                                                }
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(context) {
                                                                        return context.dataset.label.split(':')[0] + ': ' + context.raw;
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        scales: {
                                                            x: {
                                                                stacked: false,
                                                                grid: {
                                                                    display: false
                                                                },
                                                                ticks: {
                                                                    color: '#555',
                                                                    font: {
                                                                        size: 12
                                                                    }
                                                                }
                                                            },
                                                            y: {
                                                                beginAtZero: true,
                                                                grid: {
                                                                    drawBorder: false,
                                                                    color: '#eee'
                                                                },
                                                                ticks: {
                                                                    color: '#aaa'
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div> -->
                            <!-- <div class="col-md-3 p-0">
                                    <div class="px-3 main-charts">
                                        <div class="p-3 card">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="head mb-1">Top Agents</h6>
                                                    <p class="sub_head">Weekly Report</p>
                                                </div>
                                            </div>
                                            <div class="agents">
                                                <li class="li d-flex align-items-center mb-2 pr-2">
                                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Smith" class="small-avatar" />
                                                    <div class="head2 flex-grow-1">John Smith</div>
                                                    <div class="sub_head2">34 Chats</div>
                                                </li>
                                                <li class="li d-flex align-items-center mb-2 pr-2">
                                                    <img src="https://randomuser.me/api/portraits/men/33.jpg" alt="John Smith" class="small-avatar" />
                                                    <div class="head2 flex-grow-1">John Smith</div>
                                                    <div class="sub_head2">25 Chats</div>
                                                </li>
                                                <li class="li d-flex align-items-center mb-2 pr-2">
                                                    <img src="https://randomuser.me/api/portraits/men/33.jpg" alt="John Smith" class="small-avatar" />
                                                    <div class="head2 flex-grow-1">John Smith</div>
                                                    <div class="sub_head2">15 Chats</div>
                                                </li>
                                                <li class="li d-flex align-items-center mb-2 pr-2">
                                                    <img src="https://randomuser.me/api/portraits/men/33.jpg" alt="John Smith" class="small-avatar" />
                                                    <div class="head2 flex-grow-1">John Smith</div>
                                                    <div class="sub_head2">15 Chats</div>
                                                </li>
                                                <li class="li d-flex align-items-center mb-2 pr-2">
                                                    <img src="https://randomuser.me/api/portraits/men/33.jpg" alt="John Smith" class="small-avatar" />
                                                    <div class="head2 flex-grow-1">John Smith</div>
                                                    <div class="sub_head2">15 Chats</div>
                                                </li>
                                                <li class="li d-flex align-items-center mb-2 pr-2">
                                                    <img src="https://randomuser.me/api/portraits/men/33.jpg" alt="John Smith" class="small-avatar" />
                                                    <div class="head2 flex-grow-1">John Smith</div>
                                                    <div class="sub_head2">11 Chats</div>
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            <!--div class="col-md-12 p-0 my-3">
                                    <div class="px-3 main-charts tables">
                                        <div class="bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="head mb-1">All Tickets</h6>
                                            </div>
                                            <p class="label_blue"><a class="mr-2" href="<?php echo $ticketUrl; ?>">View All</a></p>
                                        </div-->
                            <!-- tickets_table = tickets_area.find('.sb-table-tickets');
                                        tickets_table_menu = tickets_area.find('.sb-menu-tickets'); -->
                            <!--div class="seprator"></div-->
                            <!--?php
                                        function sb_get_priorities()
                                        {
                                            $priorities = sb_db_get(
                                                "SELECT * FROM priorities",
                                                false
                                            );
                                            return $priorities;
                                        }
                                        function sb_get_statues()
                                        {
                                            $status = sb_db_get(
                                                "SELECT * FROM ticket_status",
                                                false
                                            );
                                            return $status;
                                        }
                                        $statues = sb_get_statues();
                                        $priorities = sb_get_priorities();
                                        ?>
                                        <div id="ticket_statues">
                                            <ul class="status-list"-->
                            <!--?php foreach (
                                                    $statues
                                                    as $status
                                                ) {
                                                    echo '<li data-status="' .
                                                        $status["name"] .
                                                        '" class="" data-color="' .
                                                        $status["color"] .
                                                        '" value="' .
                                                        $status["id"] .
                                                        '">
                                                    <span class="status-dot"></span> ' .
                                                        $status["name"] .
                                                        '
                                                </li>';
                                                } ?>
                                            </ul>
                                        </div>
                                        <div id="ticket_priorities">
                                            <ul class="priority-list"-->
                            <!--?php foreach (
                                                    $priorities
                                                    as $priority
                                                ) {
                                                    echo '<li data-status="' .
                                                        $priority["name"] .
                                                        '" class="" data-color="' .
                                                        $priority["color"] .
                                                        '" value="' .
                                                        $priority["id"] .
                                                        '">
                                                    <span class="status-dot"></span> ' .
                                                        $priority["name"] .
                                                        '
                                                </li>';
                                                } ?>
                                            </ul>
                                        </div>
                                        <div class="new_table sb-area-tickets-dash">
                                            <div class="sb-scroll-area">
                                                <table class="sb-table sb-table-tickets sb_table_new sb-table-tickets-dash">
                                                    <thead>
                                                        <tr>
                                                            <th data-field="title">
                                                                Ticket Title
                                                            </th>
                                                            <th data-field="assigned-to">
                                                                Assigned To
                                                            </th>
                                                            <th data-field="creation-date">
                                                                Creation Date
                                                            </th>
                                                            <th data-field="status"-->
                            <!--?php sb_e(
                                                                    "Status"
                                                                ); ?>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">Bug fix: Login issue</td>
                                                            <td class="sb-td-tags">Kathryn Murphy</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border" style="color:#FF0000;border:1px solid #FF0000;">Open</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div-->
                            <!-- <div class="col-md-6 p-0">
                                    <div class="pl-3 pr-3 pt-0 main-charts tables">
                                        <div class="bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="head mb-1">Ticket Support</h6>
                                            </div>
                                            <p class="label_blue">View All</p>
                                        </div>
                                        <div class="seprator"></div>
                                        <div class="new_table">
                                            <div class="sb-scroll-area">
                                                <table class="sb-table sb_table_new sb-table-tickets">
                                                    <thead>
                                                        <tr>
                                                            <th data-field="id">
                                                                Ticket ID
                                                            </th>
                                                            <th data-field="subject">
                                                                Date
                                                            </th>
                                                            <th data-field="status">
                                                                <?php sb_e(
                                                                    "Status"
                                                                ); ?> 
                                                            </th>
                                                            <th data-field="priority">
                                                                <?php sb_e(
                                                                    "Priority"
                                                                ); ?>   
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">TCKT-59861244</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border" style="color:#FF0000;border:1px solid #FF0000;">Open</span></td>
                                                            <td class="sb-td-priority"><span class="span-border" style="color:null;border:1px solid null;">null</span></td>
                                                        </tr>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">TCKT-59861244</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border" style="color:#FF0000;border:1px solid #FF0000;">Open</span></td>
                                                            <td class="sb-td-priority"><span class="span-border" style="color:null;border:1px solid null;">null</span></td>
                                                        </tr>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">TCKT-59861244</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border" style="color:#FF0000;border:1px solid #FF0000;">Open</span></td>
                                                            <td class="sb-td-priority"><span class="span-border" style="color:null;border:1px solid null;">null</span></td>
                                                        </tr>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">TCKT-59861244</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border" style="color:#FF0000;border:1px solid #FF0000;">Open</span></td>
                                                            <td class="sb-td-priority"><span class="span-border" style="color:null;border:1px solid null;">null</span></td>
                                                        </tr>
                                                        <tr data-ticket-id="">
                                                            <td class="sb-td-subject">TCKT-59861244</td>
                                                            <td><span>05/15/25</span> <span>10:01 AM</span></td>
                                                            <td class="sb-td-status"><span class="span-border" style="color:#FF0000;border:1px solid #FF0000;">Open</span></td>
                                                            <td class="sb-td-priority"><span class="span-border" style="color:null;border:1px solid null;">null</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div-->
                    </main>
                </div>
                <div class="sb-area-conversations">
                    <?php echo $header; ?>
                    <div class="sb-board">
                        <div class="sb-admin-list<?php echo sb_get_multi_setting(
                            "departments-settings",
                            "departments-show-list"
                        )
                            ? " sb-departments-show"
                            : ""; ?>">
                            <div class="sb-top">
                                <div class="sb-select">
                                    <p data-value="0">
                                        <?php sb_e("Inbox"); ?><span></span>
                                    </p>
                                    <ul>
                                        <li data-value="0" class="sb-active">
                                            <?php sb_e("Inbox"); ?>
                                            <span></span>
                                        </li>
                                        <li data-value="3">
                                            <?php sb_e("Archive"); ?>
                                        </li>
                                        <li data-value="4">
                                            <?php sb_e("Trash"); ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="sb-flex">
                                    <?php sb_conversations_filter(
                                        $cloud_active_apps
                                    ); ?>
                                </div>
                                <div class="sb-search-btn search-input">
                                    <i class="sb-icon sb-icon-search"></i>
                                    <input type="text" autocomplete="false" placeholder="<?php sb_e(
                                        "Search..."
                                    ); ?>" />
                                </div>
                            </div>
                            <div class="sb-scroll-area">
                                <ul></ul>
                            </div>
                        </div>
                        <div class="sb-conversation">
                            <div class="sb-top">
                                <i class="sb-btn-back sb-icon-arrow-left"></i>
                                <a></a>
                                <div class="sb-labels"></div>
                                <div class="sb-menu-mobile">
                                    <i class="sb-icon-menu"></i>
                                    <ul>
                                        <!-- <li id="convert-to-ticket-list" class="sb-convert-to-ticket-list">
                                            <a id="convert-to-ticket" data-value="convert-to-ticket" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Convert to a ticket"
                                            ); ?>">
                                                <i id="sb-icon-refresh" class="sb-icon-refresh"></i>
                                            </a>
                                        </li> -->
                                        <li id="convert-to-ticket-list" class="sb-convert-to-ticket-list">
                                            <a id="convert-to-ticket" data-value="convert-to-ticket" class="sb-btn sb-icon"
                                                data-sb-tooltip="Convert to a ticket">
                                                <span>Convert to ticket</span>
                                            </a>
                                        </li>
                                        <li id="view-profile-list" class="">
                                            <a id="view-profile-button" data-value="view-profile" class="sb-btn sb-icon"
                                                data-sb-tooltip="View Profile">
                                                <span>View Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="archive" class="sb-btn-icon" style="display: flex; align-items: center; justify-content: center;" data-sb-tooltip="<?php sb_e(
                                                "Archive conversation"
                                            ); ?>">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.30078 11.0665L5.43359 12.9337L9.1891 16.6892C9.44699 16.9471 9.78469 17.0759 10.1227 17.0759C10.4607 17.0759 10.7984 16.9471 11.0563 16.6892L18.5673 9.17822L16.7001 7.31104L10.1227 13.8884L7.30078 11.0665Z"
                                                        fill="#22C55E" />
                                                    <path
                                                        d="M23.0567 7.3288C22.4523 5.89966 21.5871 4.61663 20.4854 3.51462C19.3837 2.41291 18.1003 1.54768 16.6712 0.943276C15.1913 0.317457 13.6196 0 12 0C10.3804 0 8.8087 0.317457 7.3288 0.943276C5.89966 1.54768 4.61663 2.41291 3.51462 3.51462C2.41291 4.61633 1.54768 5.89966 0.943276 7.3288C0.317457 8.8087 0 10.3804 0 12C0 13.6196 0.317457 15.1913 0.943276 16.6712C1.54768 18.1003 2.41291 19.3834 3.51462 20.4854C4.61633 21.5871 5.89966 22.4523 7.3288 23.0567C8.8087 23.6825 10.3804 24 12 24C13.4154 24 14.8014 23.7556 16.1196 23.2738C17.3933 22.8082 18.5718 22.1334 19.6231 21.2679L18.6907 20.1354C16.8129 21.6816 14.437 22.533 12 22.533C6.19218 22.533 1.46699 17.8078 1.46699 12C1.46699 6.19218 6.19218 1.46699 12 1.46699C17.8078 1.46699 22.533 6.19218 22.533 12C22.533 14.4282 21.6871 16.7974 20.1512 18.6716L21.2858 19.6014C22.146 18.5519 22.8164 17.3759 23.2788 16.1061C23.7574 14.7923 24 13.4107 24 12C24 10.3804 23.6825 8.8087 23.0567 7.3288Z"
                                                        fill="#22C55E" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="read" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Mark as read"
                                            ); ?>">
                                                <svg width="29" height="24" viewBox="0 0 29 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M19.3418 1.19824C22.0089 -0.273185 25.4006 0.652378 26.8623 3.30176C28.3337 5.96876 27.4079 9.36042 24.7588 10.8223L24.5 10.9648V22.8604C24.5 23.0467 24.4397 23.1628 24.3711 23.2314C24.3025 23.3 24.1863 23.3604 24 23.3604H1C0.813676 23.3604 0.697524 23.3 0.628906 23.2314C0.56031 23.1628 0.500025 23.0467 0.5 22.8604V6.86035C0.5 6.63877 0.555306 6.5393 0.583984 6.50488C0.604837 6.47994 0.633876 6.45996 0.700195 6.45996H0.703125L16.0029 6.36035L16.4766 6.35742L16.499 5.88379C16.5919 3.93497 17.6115 2.10923 19.333 1.20312L19.3418 1.19824ZM1.5 22.3604H23.5V11.2666L22.8418 11.4863C22.8402 11.4868 22.8302 11.4893 22.8096 11.4941C22.7877 11.4993 22.758 11.506 22.7217 11.5127C22.6491 11.526 22.5533 11.5397 22.4385 11.5518C22.2083 11.5759 21.9097 11.5905 21.5762 11.5732C20.9023 11.5384 20.1259 11.3765 19.4775 10.9443L19.1943 10.7559L18.915 10.9492L12.7188 15.2451C12.6683 15.2774 12.5733 15.3105 12.4502 15.3105C12.3244 15.3105 12.2263 15.2772 12.1768 15.2441L2.28613 8.34961L1.5 7.80273V22.3604ZM3.91602 8.27148L12.3154 14.0713L12.6035 14.2705L12.8887 14.0684L17.9883 10.4688L18.4375 10.1523L18.0801 9.73438C17.5197 9.08055 17.1485 8.43205 16.8682 7.68457L16.7461 7.36035H2.5957L3.91602 8.27148ZM22 1.56055C19.5239 1.56055 17.5 3.5844 17.5 6.06055C17.5002 8.53655 19.524 10.5605 22 10.5605C24.476 10.5605 26.4998 8.53655 26.5 6.06055C26.5 3.5844 24.4761 1.56055 22 1.56055Z"
                                                        fill="black" stroke="#5E5E5E" />
                                                    <path
                                                        d="M22 3.36035C22.1864 3.36035 22.3025 3.42065 22.3711 3.48926C22.4397 3.55787 22.5 3.67398 22.5 3.86035V7.86035C22.5 8.04673 22.4397 8.16283 22.3711 8.23145C22.3025 8.30006 22.1864 8.36035 22 8.36035C21.8136 8.36035 21.6975 8.30006 21.6289 8.23145C21.5603 8.16283 21.5 8.04673 21.5 7.86035V3.86035C21.5 3.67398 21.5603 3.55787 21.6289 3.48926C21.6975 3.42065 21.8136 3.36035 22 3.36035Z"
                                                        fill="black" stroke="#5E5E5E" />
                                                </svg>

                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="transcript" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Transcript"
                                            ); ?>" data-action="<?php echo sb_get_multi_setting(
                                                 "transcript",
                                                 "transcript-action"
                                             ); ?>">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1 17.5V20.25C1 20.9793 1.28973 21.6788 1.80546 22.1945C2.32118 22.7103 3.02065 23 3.75 23H20.25C20.9793 23 21.6788 22.7103 22.1945 22.1945C22.7103 21.6788 23 20.9793 23 20.25V17.5M17.5 12L12 17.5M12 17.5L6.5 12M12 17.5V1"
                                                        stroke="#5E5E5E" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>

                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="inbox" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Send to inbox"
                                            ); ?>">
                                                <i class="sb-icon-back"></i>
                                            </a>
                                        </li>
                                        <?php if (
                                            $is_admin ||
                                            (!$supervisor &&
                                                sb_get_multi_setting(
                                                    "agents",
                                                    "agents-delete-conversation"
                                                )) ||
                                            ($supervisor &&
                                                $supervisor[
                                                    "supervisor-delete-conversation"
                                                ])
                                        ) {
                                            echo '<li><a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' .
                                                sb_("Delete conversation") .
                                                '"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.74 9.00003L14.394 18M9.606 18L9.26 9.00003M19.228 5.79003C19.57 5.84203 19.91 5.89703 20.25 5.95603M19.228 5.79003L18.16 19.673C18.1164 20.2383 17.8611 20.7662 17.445 21.1513C17.029 21.5364 16.4829 21.7502 15.916 21.75H8.084C7.5171 21.7502 6.97102 21.5364 6.55498 21.1513C6.13894 20.7662 5.88359 20.2383 5.84 19.673L4.772 5.79003M19.228 5.79003C18.0739 5.61555 16.9138 5.48313 15.75 5.39303M4.772 5.79003C4.43 5.84103 4.09 5.89603 3.75 5.95503M4.772 5.79003C5.92613 5.61555 7.08623 5.48313 8.25 5.39303M15.75 5.39303V4.47703C15.75 3.29703 14.84 2.31303 13.66 2.27603C12.5536 2.24067 11.4464 2.24067 10.34 2.27603C9.16 2.31303 8.25 3.29803 8.25 4.47703V5.39303M15.75 5.39303C13.2537 5.20011 10.7463 5.20011 8.25 5.39303" stroke="#5E5E5E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</a></li><li><a data-value="empty-trash" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' .
                                                sb_("Empty trash") .
                                                '"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.74 9.00003L14.394 18M9.606 18L9.26 9.00003M19.228 5.79003C19.57 5.84203 19.91 5.89703 20.25 5.95603M19.228 5.79003L18.16 19.673C18.1164 20.2383 17.8611 20.7662 17.445 21.1513C17.029 21.5364 16.4829 21.7502 15.916 21.75H8.084C7.5171 21.7502 6.97102 21.5364 6.55498 21.1513C6.13894 20.7662 5.88359 20.2383 5.84 19.673L4.772 5.79003M19.228 5.79003C18.0739 5.61555 16.9138 5.48313 15.75 5.39303M4.772 5.79003C4.43 5.84103 4.09 5.89603 3.75 5.95503M4.772 5.79003C5.92613 5.61555 7.08623 5.48313 8.25 5.39303M15.75 5.39303V4.47703C15.75 3.29703 14.84 2.31303 13.66 2.27603C12.5536 2.24067 11.4464 2.24067 10.34 2.27603C9.16 2.31303 8.25 3.29803 8.25 4.47703V5.39303M15.75 5.39303C13.2537 5.20011 10.7463 5.20011 8.25 5.39303" stroke="#5E5E5E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</a></li>';
                                        } ?>
                                        <li>
                                            <a data-value="panel" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Details"
                                            ); ?>">
                                                <i class="sb-icon-info"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="sb-label-date-top"></div>
                            </div>
                            <div class="sb-list"></div>
                            <?php sb_component_editor(true); ?>
                            <div class="sb-no-conversation-message">
                                <div>
                                    <label>
                                        <?php sb_e("Select a conversation"); ?>
                                    </label>
                                    <p>
                                        <?php sb_e(
                                            "Select a conversation from the left menu."
                                        ); ?>
                                    </p>
                                </div>
                            </div>
                            <audio id="sb-audio" preload="auto">
                                <source src="<?php echo sb_get_multi_setting(
                                    "sound-settings",
                                    "sound-settings-file-admin",
                                    SB_URL . "/media/sound.mp3"
                                ); ?>" type="audio/mpeg">
                            </audio>
                        </div>
                        <div class="sb-user-details">
                            <div class="sb-top">
                                <div class="sb-profile">
                                    <img src="<?php echo SB_URL; ?>/media/user.svg" />
                                    <div class="user-initials" style="display: none;">
                                        <span class="initials"></span>
                                    </div>
                                    <span class="sb-name"></span>
                                </div>
                            </div>
                            <div class="sb-scroll-area">
                                <a class="sb-user-details-close sb-close sb-btn-icon sb-btn-red">
                                    <i class="sb-icon-close"></i>
                                </a>
                                <div class="sb-profile-list sb-profile-list-conversation<?php echo $collapse; ?>"></div>
                                <?php
                                sb_apps_panel();
                                sb_departments("custom-select");
                                if (
                                    sb_get_multi_setting(
                                        "routing",
                                        "routing-active"
                                    ) ||
                                    (sb_is_agent(false, true, true) &&
                                        sb_get_multi_setting(
                                            "queue",
                                            "queue-active"
                                        )) ||
                                    (sb_get_multi_setting(
                                        "agent-hide-conversations",
                                        "agent-hide-conversations-active"
                                    ) &&
                                        sb_get_multi_setting(
                                            "agent-hide-conversations",
                                            "agent-hide-conversations-menu"
                                        ))
                                ) {
                                    sb_routing_select();
                                }
                                if (
                                    !sb_get_multi_setting(
                                        "disable",
                                        "disable-notes"
                                    )
                                ) {
                                    echo '<div class="sb-panel-details sb-panel-notes' .
                                        $collapse .
                                        '"><i class="sb-icon-plus"></i><h3>' .
                                        sb_("Notes") .
                                        "</h3><div></div></div>";
                                }
                                if (
                                    !sb_get_multi_setting(
                                        "disable",
                                        "disable-tags"
                                    )
                                ) {
                                    echo '<div class="sb-panel-details sb-panel-tags"><i class="sb-icon-plus"></i><h3>' .
                                        sb_("Tags") .
                                        "</h3><div></div></div>";
                                }
                                if (
                                    !sb_get_multi_setting(
                                        "disable",
                                        "disable-attachments"
                                    )
                                ) {
                                    echo '<div class="sb-panel-details sb-panel-attachments sb-collapse"></div>';
                                }
                                ?>
                                <h3 class="sb-hide">
                                    <?php sb_e("User conversations"); ?>
                                </h3>
                                <ul class="sb-user-conversations"></ul>
                            </div>
                            <div class="sb-no-conversation-message"></div>
                        </div>
                    </div>
                    <i class="sb-btn-collapse sb-left sb-icon-arrow-left"></i>
                    <i class="sb-btn-collapse sb-right sb-icon-arrow-right"></i>
                </div>
                <?php if ($active_areas["users"]) { ?>
                    <div class="sb-area-users">
                        <?php echo $header; ?>
                        <div class="sb-top-bar">
                            <div>
                                <a class="sb-btn sb-icon sb-new-user sb_btn_new">
                                    <i class="fa-solid fa-user-plus mr-1"></i>
                                    <?php sb_e("New Customer"); ?>
                                </a>
                            </div>
                            <div>
                                <!-- <h2>
                                    <?php sb_e("Users list"); ?>
                                </h2> -->
                                <div class="sb-menu-wide sb-menu-users sb-menu-wide_new">
                                    <div>
                                        <?php sb_e("All"); ?>
                                        <span data-count="0"></span>
                                    </div>
                                    <ul>
                                        <li data-type="all" class="sb-active">
                                            <?php sb_e("All"); ?>
                                            <span data-count="0">(0)</span>
                                        </li>
                                        <li data-type="user">
                                            <?php sb_e("Customers"); ?>
                                            <span data-count="0">(0)</span>
                                        </li>
                                        <li data-type="lead">
                                            <?php sb_e("Leads"); ?>
                                            <span data-count="0">(0)</span>
                                        </li>
                                        <li data-type="visitor">
                                            <?php sb_e("Visitors"); ?>
                                            <span data-count="0">(0)</span>
                                        </li>
                                        <li data-type="online">
                                            <?php sb_e("Online"); ?>
                                        </li>
                                        <?php if (
                                            $is_admin ||
                                            (!$supervisor &&
                                                sb_get_multi_setting(
                                                    "agents",
                                                    "agents-tab"
                                                )) ||
                                            ($supervisor &&
                                                $supervisor[
                                                    "supervisor-agents-tab"
                                                ])
                                        ) {
                                            echo '<li data-type="agent">' .
                                                sb_("Agents & Admins") .
                                                "</li>";
                                        } ?>
                                    </ul>
                                </div>
                                <div class="sb-menu-mobile">
                                    <i class="sb-icon-menu"></i>
                                    <ul>
                                        <?php if ($is_admin) {
                                            echo '<li><a data-value="csv" class="sb-btn-icon" data-sb-tooltip="' .
                                                sb_("Download CSV") .
                                                '"><i class="sb-icon-download"></i></a></li>';
                                        } ?>
                                        <li>
                                            <a data-value="message" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Send a message"
                                            ); ?>">
                                                <i class="sb-icon-chat"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="custom_email" class="sb-btn-icon" data-sb-tooltip="<?php sb_e(
                                                "Send an email"
                                            ); ?>">
                                                <i class="sb-icon-envelope"></i>
                                            </a>
                                        </li>
                                        <?php
                                        if ($sms) {
                                            echo '<li><a data-value="sms" class="sb-btn-icon" data-sb-tooltip="' .
                                                sb_("Send a text message") .
                                                '"><i class="sb-icon-sms"></i></a><li>';
                                        }
                                        if (
                                            defined("SB_WHATSAPP") &&
                                            (!function_exists(
                                                "sb_whatsapp_active"
                                            ) ||
                                                sb_whatsapp_active())
                                        ) {
                                            echo '<li><a data-value="whatsapp" class="sb-btn-icon" data-sb-tooltip="' .
                                                sb_(
                                                    "Send a WhatsApp message template"
                                                ) .
                                                '"><i class="sb-icon-social-wa"></i></a><li>'; // Deprecated: remove function_exists('sb_whatsapp_active')
                                        }
                                        if ($is_admin) {
                                            echo '<li><a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' .
                                                sb_("Delete users") .
                                                '" style="display: none;"><i class="sb-icon-delete"></i></a></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <?php sb_conversations_filter(
                                    $cloud_active_apps
                                ); ?>
                            </div>
                            <!-- <div>
                                <div class="sb-search-btn">
                                    <i class="sb-icon sb-icon-search"></i>
                                    <input type="text" autocomplete="false" placeholder="<?php sb_e(
                                        "Search users ..."
                                    ); ?>" />
                                </div>
                                <a class="sb-btn sb-icon sb-new-user">
                                    <i class="sb-icon-user"></i>
                                    <?php sb_e("Add new user"); ?>
                                </a>
                            </div> -->
                        </div>
                        <div class="sb-scroll-area">
                            <table class="sb-table sb_table_new sb-table-users">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" />
                                        </th>
                                        <th data-field="first_name">
                                            <?php sb_e("Full name"); ?>
                                        </th>
                                        <?php sb_users_table_extra_fields(); ?>
                                        <th data-field="email">
                                            <?php sb_e("Email"); ?>
                                        </th>
                                        <th data-field="user_type">
                                            <?php sb_e("Type"); ?>
                                        </th>
                                        <th data-field="last_activity">
                                            <?php sb_e("Last activity"); ?>
                                        </th>
                                        <th data-field="creation_time" class="sb-active">
                                            <?php sb_e("Registration date"); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($active_areas["chatbot"]) {
                    require_once SB_PATH . "/apps/dialogflow/components.php";
                    sb_dialogflow_chatbot_area();
                }
                ?>
                <?php //if ($active_areas['tickets']) { ?>
                <style>
                    .sb-table .span-border {
                        text-align: center;
                        padding: 3px 7px;
                        border-radius: 6px;
                    }
                </style>
                <div class="sb-area-tickets">
                    <?php echo $header; ?>
                    <div class="sb-top-bar new-ticket-button">
                        <div>
                            <!-- <div class="sb-search-btn">
                                    <i class="sb-icon sb-icon-search"></i>
                                    <input type="text" autocomplete="false" placeholder="<?php sb_e(
                                        "Search tickets ..."
                                    ); ?>" />
                                </div> -->
                            <a class="sb-btn sb-icon sb-new-ticket sb_btn_new">
                                <i class="fa-solid fa-plus mr-1"></i>
                                <?php sb_e("New Ticket"); ?>
                            </a>
                        </div>
                        <div>
                            <!-- <div  class="color-palette tag-palette">
                                    <span class="sb-active"></span>
                                    <ul id="selected-tags-palette">
                                    </ul>
                                </div> -->
                            <div class="mr-5 tags-filter" style="">
                                <?php
                                $tags = sb_get_multi_setting(
                                    "disable",
                                    "disable-tags"
                                )
                                    ? []
                                    : sb_get_setting("tags", []);
                                $tagsHtml = "";
                                $count = count($tags);
                                if ($count > 0) { ?>
                                    <select id="tags-filter" name="tags[]" multiple>
                                        <?php
                                        for ($i = 0; $i < $count; $i++) {
                                            $tagsHtml .=
                                                '<option value="' .
                                                $tags[$i]["tag-name"] .
                                                '"  class="tag-option" data-color="' .
                                                $tags[$i]["tag-color"] .
                                                '" data-custom-properties={"color":"' .
                                                $tags[$i]["tag-color"] .
                                                '"}>' .
                                                $tags[$i]["tag-name"] .
                                                "</option>";
                                        }
                                        echo $tagsHtml;
                                        ?>
                                    </select>
                                <?php }
                                ?>

                            </div>
                            <div class="sb-menu-wide sb-menu-tickets sb-menu-wide_new">
                                <div>
                                    <?php sb_e("All"); ?>
                                    <span data-count="0"></span>
                                </div>
                                <ul>
                                    <li data-type="all" class="sb-active">
                                        <span data-count="0">0</span>
                                        <?php sb_e("All"); ?>
                                    </li>
                                    <li data-type="open">
                                        <span data-count="0">0</span>
                                        <?php sb_e("Open"); ?>
                                    </li>
                                    <li data-type="in-progress">
                                        <span data-count="0">0</span>
                                        <?php sb_e("In Progress"); ?>
                                    </li>
                                    <li data-type="answered">
                                        <span data-count="0">0</span>
                                        <?php sb_e("Answered"); ?>
                                    </li>
                                    <li data-type="hold">
                                        <span data-count="0">0</span>
                                        <?php sb_e("On Hold"); ?>
                                    </li>
                                    <li data-type="closed">
                                        <span data-count="0">0</span>
                                        <?php sb_e("Closed"); ?>
                                    </li>
                                </ul>
                            </div>
                            <!--div class="sb-menu-mobile">
                                    <i class="sb-icon-menu"></i>
                                    <ul>
                                        <?php if ($is_admin) {
                                        // echo '<li><a data-value="csv" class="sb-btn-icon" data-sb-tooltip="' . sb_('Download CSV') . '"><i class="sb-icon-download"></i></a></li>';
                                    } ?>
                                    </ul>
                                </div-->
                        </div>
                    </div>
                    <!--  -->
                    <div class="container-fluid py-4 px-0">
                        <div class="pe-4 px-3">
                            <!-- Table -->
                            <div class="table-responsive" style="overflow: visible;">
                                <div class="sb-scroll-area scroll-table">
                                    <table id="ticketTable"
                                        class=" sb-table-tickets table table-bordered table-hover align-middle text-nowrap bg-white w-100 ">
                                        <thead class="table-light">
                                            <tr>
                                                <th data-field="id" width="5%">
                                                    <?php sb_e("ID"); ?>
                                                </th>
                                                <th data-field="subject">
                                                    <?php sb_e("Subject"); ?>
                                                </th>
                                                <?php
                                                $tags = sb_get_multi_setting("disable", "disable-tags") ? [] : sb_get_setting("tags", []);
                                                $count = count($tags);
                                                if ($count > 0) {
                                                    ?>
                                                    <th data-field="tags" width="20%">
                                                        <?php sb_e("Tags"); ?>
                                                    </th>
                                                    <?php
                                                }
                                                $department_settings = sb_get_setting(
                                                    "departments-settings"
                                                );
                                                if (
                                                    isset(
                                                    $department_settings[
                                                        "departments-show-list"
                                                    ]
                                                ) &&
                                                    $department_settings[
                                                        "departments-show-list"
                                                    ] == "1"
                                                ) { ?>
                                                    <th data-field="department">
                                                        <?php sb_e("Department"); ?>
                                                    </th>
                                                <?php }
                                                ?>
                                                <th data-field="contact">
                                                    <?php sb_e("Contact"); ?>
                                                </th>
                                                <th data-field="status">
                                                    <?php sb_e("Status"); ?>
                                                </th>
                                                <th data-field="priority">
                                                    <?php sb_e("Priority"); ?>
                                                </th>
                                                <th data-field="last_reply">
                                                    <?php sb_e("Assigned To"); ?>
                                                </th>
                                                <th data-field="creation_time">
                                                    <?php sb_e("Created At"); ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Sample Row -->



                                            <!-- Repeat for more records -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

                    <!-- Scripts -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

                    <style>
                        /* Make sure the dropdown is not cut off by table or container */
                        .table-responsive {
                            overflow: visible !important;
                        }

                        table.dataTable tbody td {
                            overflow: visible !important;
                            position: relative !important;
                            z-index: auto !important;
                        }

                        .dropdown-menu {
                            z-index: 9999 !important;
                            /* Boost visibility over DataTables */
                        }

                        .badge-tag {
                            background-color: #fce4ec;
                            color: #d81b60;
                            font-size: 12px;
                        }

                        .dropdown-menu .dropdown-item {
                            font-size: 14px;
                            padding: 6px 15px;
                        }

                        .status-inprogress {
                            background-color: #fff3cd;
                            color: #856404;
                        }

                        .status-open {
                            background-color: #f8d7da;
                            color: #721c24;
                        }

                        .status-hold {
                            background-color: #fde2e2;
                            color: #f44336;
                        }

                        .status-answered {
                            background-color: #d4edda;
                            color: #155724;
                        }

                        .status-closed {
                            background-color: #e2e3e5;
                            color: #6c757d;
                        }

                        .status-wp {
                            background-color: #cce5ff;
                            color: #004085;
                        }

                        .priority-low {
                            background-color: #E2F0CB;
                            color: #2E7D32;
                        }

                        .priority-high {
                            background-color: #FFF3CD;
                            color: #856404;
                        }

                        .priority-critical {
                            background-color: #F8D7DA;
                            color: #721C24;
                        }

                        .dropdown-toggle {
                            border: none;
                            padding: 5px 10px;
                        }

                        /* Fix dropdown visibility inside DataTables */
                        table.dataTable tbody td {
                            overflow: visible !important;
                            position: relative !important;
                            z-index: 1;
                        }

                        .dropdown-menu {
                            z-index: 1050 !important;
                        }

                        .sb-scroll-area.scroll-table {
                            height: 700px !important;
                        }
                    </style>
                    <!--  -->



                </div>
                <div class="sb-area-ticket-detail">
                    <?php echo $header; ?>
                    <div class="tc_bg" style="max-height: calc(100vh - 93px);overflow-y: auto;">
                        <div class="tc_back">
                            <div class="container">
                                <div class="row tablet-sizee">
                                    <div class="col-md-12 p-0">
                                        <div class="row">
                                            <div class="col-md-8 p-0">
                                                <h2 class="title mb-0"># <span class="tno">TR-51</span> / <span
                                                        class="tsubject"><input type="text" id="ticket-subject"
                                                            value="" /></span></h2>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="d-flex align-items-center justify-content-between pl-5">
                                                    <select class="form-select ticket-status-dropdown" id="ticket-status"
                                                        style="width: 120px;">
                                                        <?php foreach (
                                                            $statues
                                                            as $key =>
                                                            $value
                                                        ) {
                                                            echo '<option value="' .
                                                                $value[
                                                                    "id"
                                                                ] .
                                                                '">' .
                                                                $value[
                                                                    "name"
                                                                ] .
                                                                "</option>";
                                                        } ?>
                                                    </select>

                                                    <a class="sb-btn sb-icon sb-save-ticket sb_btn_new">
                                                        <i class="fa-solid fa-check mr-1"></i>
                                                        Save changes
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-12 p-0 mt-3 d-flex align-items-center"
                                                id="view-ticket-attachments">
                                                <i class="fas fa-paperclip fs-4 mr-2"></i>
                                                <span class="label">Attachments (<span
                                                        class="attachments-count">0</span>)</span>
                                            </div>
                                            <div class="col-md-9 p-0 table-align">
                                                <h2 class="sub_title my-4">Description</h2>
                                                <div id="description-d" class="description" data-type="textarea"
                                                    style="margin: 10px 0 0 0;display: block;">
                                                    <div style="display: inline-block;padding:0;width:100%;">
                                                        <div id="ticketDescriptionTicketDetail" style="height: 180px;"></div>
                                                    </div>
                                                    <input id="ticket_id_d" type="hidden" name="ticket_id" />
                                                    <input id="conversation_id_d" type="hidden" name="conversation_id" />
                                                    <!-- Hidden input to store uploaded file data -->
                                                    <input type="hidden" id="uploaded_files" name="uploaded_files" value="">
                                                </div>

                                                <!-- Nav tabs -->
                                                <ul class="nav nav-tabs" id="myTab">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-target="#tab1"
                                                            href="javascript:void(0)">Comments</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-target="#tab2"
                                                            href="javascript:void(0)">Internal Note</a>
                                                    </li>
                                                </ul>

                                                <!-- Tab content -->
                                                <div class="tab-content pt-3">
                                                    <!-- Tab 1 -->
                                                    <div class="tab-pane active" id="tab1">
                                                        <div id="blankDiv">
                                                            <!-- Comments/Chat Section -->
                                                            <div id="ticket-comments" class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <div class=""
                                                                        style="max-height: 350px; overflow-y: auto; background: #fff;"
                                                                        id="comments-section">
                                                                        <!-- Comments will be loaded here by JS -->
                                                                    </div>

                                                                    <div class="d-flex align-items-center gap-2 mt-4">
                                                                        <input type="hidden" id="currentUserId" value="<?php echo sb_get_active_user()[
                                                                            "id"
                                                                        ] ??
                                                                            0; ?>">
                                                                        <textarea class="form-control me-2" id="newComment"
                                                                            placeholder="Type your comment..."></textarea>
                                                                        <textarea class="form-control me-2 d-none"
                                                                            data-comment-id="" id="oldComment"></textarea>

                                                                        <button id="addComment"
                                                                            class="btn btn-primary">Send</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Tab 2 -->
                                                    <div class="tab-pane" id="tab2">
                                                        <div class="mb-3">
                                                            <div id="internal-note" class="description" data-type="textarea"
                                                                style="margin: 10px 0 0 0;display: block;">
                                                                <div style="display: inline-block;padding:0;width:100%;">
                                                                    <div id="internal-note-t" style="height: 180px;"></div>
                                                                </div>
                                                                <!-- Hidden input to store uploaded file data -->
                                                                <input type="hidden" id="uploaded_files" name="uploaded_files"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-primary float-end" type="button"
                                                            id="save-note">Save</button>
                                                    </div>
                                                </div>

                                                <script>
                                                    $(document).ready(function () {
                                                        $('#myTab .nav-link').click(function () {
                                                            // Remove active class from all tabs
                                                            $('#myTab .nav-link').removeClass('active');
                                                            $('.tab-pane').removeClass('active');

                                                            // Add active class to clicked tab
                                                            $(this).addClass('active');

                                                            // Show corresponding tab pane
                                                            const target = $(this).data('target');
                                                            $(target).addClass('active');
                                                        });
                                                    });
                                                </script>


                                                <style>
                                                    /* Timeline chat style for comments */
                                                    .comment-row {
                                                        display: flex;
                                                        align-items: flex-end;
                                                    }

                                                    .comment-row.customer+.comment-row.customer {
                                                        margin-top: 2px;
                                                    }

                                                    .comment-row.agent+.comment-row.agent {
                                                        margin-top: 2px;
                                                    }

                                                    .comment-row.customer+.comment-row.agent {
                                                        margin-top: 5px;
                                                    }

                                                    .comment-row.agent+.comment-row.customer {
                                                        margin-top: 5px;
                                                    }

                                                    .comment-row.agent {
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
                                                </style>
                                                <script>
                                                    // --- Comments/Chat Section Logic ---
                                                    // document.addEventListener('DOMContentLoaded', function () {
                                                    //     const ticketId = SBF.getURL('ticket')   || document.getElementById('ticket_id').value;
                                                    //     const currentUserId = document.getElementById('currentUserId').value;
                                                    //     const currentUserRole = document.getElementById('currentUserRole').value;
                                                    //     const commentsSection = document.getElementById('comments-section');
                                                    //     const addCommentForm = document.getElementById('addCommentForm');
                                                    //     const newCommentInput = document.getElementById('newComment');

                                                    //     // Placeholder avatar
                                                    //     function getAvatar(user) {
                                                    //         // If you have user.avatar, use it; else fallback
                                                    //         return user && user.avatar ? user.avatar : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user && user.user_name ? user.user_name : user.user_role) + '&size=64&background=2563eb&color=fff';
                                                    //     }

                                                    //     function formatTimeTo12Hour(dateString) {
                                                    //         const date = new Date(dateString.replace(' ', 'T'));
                                                    //         let hours = date.getHours();
                                                    //         let minutes = date.getMinutes();
                                                    //         const ampm = hours >= 12 ? 'pm' : 'am';
                                                    //         hours = hours % 12;
                                                    //         hours = hours ? hours : 12; // 0 => 12
                                                    //         minutes = minutes < 10 ? '0' + minutes : minutes;
                                                    //         return hours + ':' + minutes + ' ' + ampm;
                                                    //     }

                                                    //     function formatDateLabel(dateString) {
                                                    //         const date = new Date(dateString.replace(' ', 'T'));
                                                    //         const today = new Date();
                                                    //         const yesterday = new Date();
                                                    //         yesterday.setDate(today.getDate() - 1);
                                                    //         const isToday = date.toDateString() === today.toDateString();
                                                    //         const isYesterday = date.toDateString() === yesterday.toDateString();
                                                    //         if (isToday) return 'Today';
                                                    //         if (isYesterday) return 'Yesterday';
                                                    //         return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
                                                    //     }

                                                    //     function renderComment(comment) {
                                                    //         const isAgent = comment.user_role === 'agent';
                                                    //         const isOwn = comment.user_id == currentUserId;
                                                    //         const rowClass = isAgent ? 'agent' : 'customer';
                                                    //         // Avatar
                                                    //         const avatarUrl = getAvatar(comment);
                                                    //         // Bubble
                                                    //         let html = `<div class="comment-row ${rowClass}">
                                            //             <img src="${avatarUrl}" class="comment-avatar" alt="avatar">
                                            //             <div class="comment-bubble">
                                            //                 <div class="comment-text" data-id="${comment.id}">${escapeHtml(comment.comment)}</div>
                                            //                 <div class="comment-meta">
                                            //                     <span>${formatTimeTo12Hour(comment.created_at)}</span>`;
                                                    //         if (comment.is_edited == 1 || comment.is_edited === "1") {
                                                    //             html += `<span class="edited-label" title="Edited">&nbsp;✎</span>`;
                                                    //         }
                                                    //         // Show Edit button only if own comment and within 10 minutes
                                                    //         if (isOwn && canEditComment(comment.created_at)) {
                                                    //             html += `<button class="edit-comment-btn" data-id="${comment.id}">Edit</button>`;
                                                    //         }
                                                    //         if (isOwn) {
                                                    //             html += `<button class="delete-comment-btn" data-id="${comment.id}">Delete</button>`;
                                                    //         }
                                                    //         html += `</div>
                                            //             </div>
                                            //         </div>`;
                                                    //         return html;
                                                    //     }

                                                    //     function canEditComment(createdAt) {
                                                    //         // Use server time injected by PHP for accuracy
                                                    //         const serverNow = window.SERVER_NOW ? new Date(window.SERVER_NOW) : new Date();
                                                    //         const commentTime = new Date(createdAt.replace(' ', 'T'));
                                                    //         const diffMs = serverNow - commentTime;
                                                    //         const diffMin = diffMs / (1000 * 60);
                                                    //         return diffMin >= 0 && diffMin <= 10;
                                                    //     }

                                                    //     function escapeHtml(text) {
                                                    //         var map = {
                                                    //             '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                                                    //         };
                                                    //         return text.replace(/[&<>"']/g, function(m) { return map[m]; });
                                                    //     }



                                                    //     function scrollToBottom() {
                                                    //         commentsSection.scrollTop = commentsSection.scrollHeight;
                                                    //     }

                                                    //     addCommentForm.addEventListener('submit', function(e) {
                                                    //         e.preventDefault();
                                                    //         const comment = newCommentInput.value.trim();
                                                    //         if (!comment) return;
                                                    //         fetch('api/add-comment.php', {
                                                    //             method: 'POST',
                                                    //             headers: { 'Content-Type': 'application/json' },
                                                    //             body: JSON.stringify({
                                                    //                 ticket_id: ticketId,
                                                    //                 user_id: currentUserId,
                                                    //                 user_role: currentUserRole,
                                                    //                 comment: comment
                                                    //             })
                                                    //         })
                                                    //         .then(res => res.json())
                                                    //         .then(data => {
                                                    //             if (data.success) {
                                                    //                 newCommentInput.value = '';
                                                    //                 loadComments();
                                                    //             } else {
                                                    //                 alert(data.error || 'Failed to add comment');
                                                    //             }
                                                    //         });
                                                    //     });

                                                    //     function attachEditListeners() {
                                                    //         commentsSection.querySelectorAll('.edit-comment-btn').forEach(btn => {
                                                    //             btn.onclick = function() {
                                                    //                 const commentId = this.getAttribute('data-id');
                                                    //                 const commentDiv = commentsSection.querySelector(`.comment-text[data-id='${commentId}']`);
                                                    //                 if (!commentDiv) return;
                                                    //                 const oldText = commentDiv.innerText;
                                                    //                 // Replace with textarea
                                                    //                 // commentDiv.innerHTML = `<textarea class='form-control form-control-sm edit-comment-textarea' rows='2'>${oldText}</textarea>`;
                                                    //                 // this.style.display = 'none';
                                                    //                 // const textarea = commentDiv.querySelector('textarea');
                                                    //                 const textarea = document.getElementById('oldComment');
                                                    //                 textarea.value = oldText;
                                                    //                 textarea.classList.remove('d-none');
                                                    //                 textarea.classList.add('edit-comment-textarea');
                                                    //                 this.style.display = 'none';
                                                    //                 textarea.focus();
                                                    //                 document.getElementById('newComment').classList.add('d-none');
                                                    //                 textarea.onblur = function() {
                                                    //                     saveEdit(commentId, textarea.value, commentDiv, btn);
                                                    //                 };
                                                    //                 textarea.onkeydown = function(e) {
                                                    //                     if (e.key === 'Enter' && !e.shiftKey) {
                                                    //                         e.preventDefault();
                                                    //                         textarea.blur();
                                                    //                     }
                                                    //                 };
                                                    //             };
                                                    //         });

                                                    //         commentsSection.querySelectorAll('.delete-comment-btn').forEach(btn => {
                                                    //             btn.onclick = function() {
                                                    //                 const commentId = this.getAttribute('data-id');
                                                    //                 deleteComment(commentId);
                                                    //             };
                                                    //         });
                                                    //     }

                                                    //     function deleteComment(commentId) {
                                                    //         fetch('api/delete-ticket-comment.php', {
                                                    //             method: 'POST',
                                                    //             headers: { 'Content-Type': 'application/json' },
                                                    //             body: JSON.stringify({ comment_id: commentId })
                                                    //         })
                                                    //         .then(res => res.json())
                                                    //         .then(data => {
                                                    //             if (data.success) {
                                                    //                 loadComments();
                                                    //             } else {
                                                    //                 alert(data.error || 'Failed to delete comment');
                                                    //             }
                                                    //         });
                                                    //     }

                                                    //     function saveEdit(commentId, newText, commentDiv, editBtn) {
                                                    //         newText = newText.trim();
                                                    //         if (!newText) {
                                                    //             commentDiv.innerHTML = '<span class="text-danger small">Comment cannot be empty</span>';
                                                    //             setTimeout(() => { loadComments(); }, 1200);
                                                    //             return;
                                                    //         }
                                                    //         fetch('api/edit-comment.php', {
                                                    //             method: 'POST',
                                                    //             headers: { 'Content-Type': 'application/json' },
                                                    //             body: JSON.stringify({
                                                    //                 comment_id: commentId,
                                                    //                 user_id: currentUserId,
                                                    //                 comment: newText
                                                    //             })
                                                    //         })
                                                    //         .then(res => res.json())
                                                    //         .then(data => {
                                                    //             if (data.success) {
                                                    //                 const textarea = document.getElementById('oldComment');
                                                    //                 textarea.classList.remove('edit-comment-textarea');
                                                    //                 textarea.classList.add('d-none');
                                                    //                 editBtn.style.display = 'block';
                                                    //                 document.getElementById('newComment').classList.remove('d-none');
                                                    //                 loadComments();
                                                    //             } else {
                                                    //                 commentDiv.innerHTML = `<span class='text-danger small'>${data.error || 'Failed to edit comment'}</span>`;
                                                    //                 setTimeout(() => { loadComments(); }, 1200);
                                                    //             }
                                                    //         });
                                                    //     }

                                                    // Initial load
                                                    //loadComments();
                                                    // Optional: Poll for new comments every 30s
                                                    //setInterval(loadComments, 30000);
                                                    //});
                                                </script>
                                                <script>
                                                    window.SERVER_NOW = "<?php echo date(
                                                        "Y-m-d\TH:i:sP"
                                                    ); ?>";
                                                </script>
                                            </div>
                                            <div class="col-md-3 p-0 table-rightside">
                                                <div class="pl-5">
                                                    <div class="sidepanel">
                                                        <h4 class="sub_title mb-3 col-4 d-inline-block">Details</h4>
                                                        <span class="conversation-id d-none">Conversation ID :
                                                            <span></span></span>
                                                        <div class="ticket-fields">
                                                            <div class="mb-3 without-contact">
                                                                <div class="field-label">Guest Ticket</div>
                                                                <div class="d-flex align-items-center gap-2"></div>
                                                                <div class="form-check form-switch mb-0 ml-2">
                                                                    <input class="form-check-input" name="without_contact"
                                                                        type="checkbox" role="switch"
                                                                        id="flexSwitchCheckDefault" style="width: 27px;">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="field-label required-label">Assignee</div>
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 ticket-assignee">
                                                                        <img class="assignee-img" src="" alt="Assignee">
                                                                        <span class="user-initials avatar_initials"
                                                                            style="display:none;">
                                                                            <span class="initials avatar_name"></span>
                                                                        </span>
                                                                        <div id="assigned_to" data-type="select"
                                                                            class="sb-input">
                                                                            <select id="select-ticket-agent"
                                                                                style="width:100%;">

                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <!-- <p class="assign-link m-0 p-0">Assign to me</p> -->
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="field-label required-label">Reporter</div>
                                                                <div class="d-flex align-items-center gap-2 ticket-reporter">
                                                                    <img class="reporter-img" src="" alt="Reporter"
                                                                        style="width: 40px;">
                                                                    <span class="user-initials avatar_initials"
                                                                        style="display:none;">
                                                                        <span class="initials avatar_name"></span>
                                                                    </span>
                                                                    <span class="name"></span>
                                                                    <div id="reporter" data-type="select"
                                                                        class="sb-input d-none">
                                                                        <select id="select-ticket-reporter" style="width:100%;">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php if (
                                                                isset(
                                                                $department_settings[
                                                                    "departments-show-list"
                                                                ]
                                                            ) &&
                                                                $department_settings[
                                                                    "departments-show-list"
                                                                ] == "1"
                                                            ) { ?>
                                                                <div class="mb-3 sb-input d-block">
                                                                    <div class="field-label">Department</div>
                                                                    <select id="ticket-department" required>
                                                                        <option value=""><?php echo sb_(
                                                                            "Select Department"
                                                                        ); ?></option>
                                                                        <?php
                                                                        $departments = sb_get_departments();
                                                                        foreach (
                                                                            $departments
                                                                            as $key =>
                                                                            $value
                                                                        ) {
                                                                            echo '<option value="' .
                                                                                $key .
                                                                                '">' .
                                                                                sb_(
                                                                                    $value[
                                                                                        "name"
                                                                                    ]
                                                                                ) .
                                                                                "</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            <?php } ?>
                                                            <?php
                                                            $tags = sb_get_multi_setting(
                                                                "disable",
                                                                "disable-tags"
                                                            )
                                                                ? []
                                                                : sb_get_setting(
                                                                    "tags",
                                                                    []
                                                                );
                                                            $tagsHtml = "";
                                                            $count = count(
                                                                $tags
                                                            );
                                                            if (
                                                                $count > 0
                                                            ) { ?>
                                                                <div class="mb-3">
                                                                    <div class="field-label">Tags</div>
                                                                    <div class="mb-2">
                                                                        <!-- <input type="text" style="max-width: 220px;height:35px;padding:0 5px" class="form-control form-control-sm" placeholder="Add a tag..."> -->
                                                                        <div class="mr-5 tags-filter" style="">
                                                                            <select id="ticket-detail-tags-filter" name="tags[]"
                                                                                multiple>
                                                                                <?php
                                                                                for (
                                                                                    $i = 0;
                                                                                    $i <
                                                                                    $count;
                                                                                    $i++
                                                                                ) {
                                                                                    $tagsHtml .=
                                                                                        '<option value="' .
                                                                                        $tags[
                                                                                            $i
                                                                                        ][
                                                                                            "tag-name"
                                                                                        ] .
                                                                                        '"  class="tag-option" data-color="' .
                                                                                        $tags[
                                                                                            $i
                                                                                        ][
                                                                                            "tag-color"
                                                                                        ] .
                                                                                        '" data-custom-properties={"color":"' .
                                                                                        $tags[
                                                                                            $i
                                                                                        ][
                                                                                            "tag-color"
                                                                                        ] .
                                                                                        '"}>' .
                                                                                        $tags[
                                                                                            $i
                                                                                        ][
                                                                                            "tag-name"
                                                                                        ] .
                                                                                        "</option>";
                                                                                }
                                                                                echo $tagsHtml;
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="sb-td-tags tag-badges">
                                                                        <!-- <span class="tag-badge">
                                                                                <i class="fas fa-check text-muted"></i>
                                                                                Business
                                                                            </span>
                                                                            <span class="tag-badge">
                                                                                <i class="fas fa-check text-muted"></i>
                                                                                Urgent
                                                                            </span>
                                                                            <span class="tag-badge">
                                                                                <i class="fas fa-check text-muted"></i>
                                                                                Priority
                                                                            </span> -->
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                            ?>

                                                            <div class="mb-3 sb-input d-block">
                                                                <div class="field-label required-label">Priority</div>
                                                                <!-- <div class="ticket-priority">
                                                                        High
                                                                    </div>
                                                                    <div id="priority_id" data-type="select" class="sb-input">
                                                                        <span class="required-label"><?php sb_e(
                                                                            "Priority"
                                                                        ); ?></span> -->
                                                                <select id="ticket-priority" required>
                                                                    <?php foreach (
                                                                        $priorities
                                                                        as $key =>
                                                                        $value
                                                                    ) {
                                                                        echo '<option value="' .
                                                                            $value[
                                                                                "id"
                                                                            ] .
                                                                            '">' .
                                                                            $value[
                                                                                "name"
                                                                            ] .
                                                                            "</option>";
                                                                    } ?>
                                                                </select>
                                                                <!-- </div> -->
                                                            </div>
                                                        </div>
                                                        <div class="divider"></div>
                                                        <h5 class="field-label">More Fields <i class="fas fa-chevron-down"></i>
                                                        </h5>
                                                        <div id="custom-fields" class="sb-input d-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ticket-attachments sb-lightbox">
                    <div class="sb-info"></div>
                    <div class="sb-top-bar">
                        <div>
                            <h2 style="margin-bottom: 0;">
                                Ticket Attachments
                            </h2>
                        </div>
                        <div>
                            <a class="sb-edit sb-btn sb-icon" data-button="toggle" id="save-ticket-attachments"
                                data-hide="sb-profile-area" data-show="sb-edit-area">
                                <i class="sb-icon-sms"></i> Save Changes
                            </a>
                            <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area"
                                data-show="sb-table-area">
                                <i class="sb-icon-close"></i>
                            </a>
                        </div>
                    </div>

                    <div class="sb-main sb-scroll-area">
                        <div class="sb-details">
                            <div class="mt-5">
                                <span class="d-block mb-2">Attachments</span>
                                <div class="custom-file">
                                    <input type="file" class="form-control d-block" style="width:96%;" id="ticket-attachments"
                                        multiple>
                                    <input type="hidden" id="reopendTicketAttachmentsPopup" value="0">
                                    <span class="text-danger files-error mt-2 d-block"></span>
                                    <small class="form-text text-muted mt-2" style="display:block">You can select multiple
                                        files. Maximum file size: 5MB. Allowed file types are .jpeg, .png, .pdf</small>
                                </div>
                            </div>
                            <div class="form-group mb-3">

                                <div class="progress mt-2 d-none" id="upload-progress-container">
                                    <div class="progress-bar" id="upload-progress" role="progressbar" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <div class="mt-2 attachments" id="existing-file-preview-container">
                                    <span>Current Attachments</span>
                                    <div class="row" id="current-attachments"></div>
                                </div>

                                <div class="mt-2 attachments">
                                    <span class="mb-2 d-block">New Attachments</span>
                                    <div class="mt-2" id="file-preview-container">
                                        <div class="row" id="file-preview-list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Attachments Section -->
                <!-- <div id="ticketFileAttachments-detail" class="ticket-attachments-box sb-lightbox">
                            
                        </div> -->


                <script>
                    function propagateTagColors() {
                        // Map value to color from original select
                        const select = document.getElementById('ticket-tags');
                        if (!select) return;
                        const valueToColor = {};
                        Array.from(select.options).forEach(opt => {
                            if (opt.value) valueToColor[opt.value] = opt.getAttribute('data-color');
                        });
                        // Dropdown items
                        document.querySelectorAll('.choices__list--dropdown .choices__item').forEach(function (item) {
                            const value = item.getAttribute('data-value');
                            if (valueToColor[value]) {
                                item.setAttribute('data-color', valueToColor[value]);
                            }
                        });
                        // Selected items
                        document.querySelectorAll('.choices__list--multiple .choices__item').forEach(function (item) {
                            const value = item.getAttribute('data-value');
                            if (valueToColor[value]) {
                                item.setAttribute('data-color', valueToColor[value]);
                            }
                        });
                    }

                    function updateTagDots() {
                        // Dropdown items
                        document.querySelectorAll('.choices__list--dropdown .choices__item[data-color]').forEach(function (item) {
                            if (!item.querySelector('.tag-dot')) {
                                let color = item.getAttribute('data-color');
                                let dot = document.createElement('span');
                                dot.className = 'tag-dot';
                                dot.style.backgroundColor = color;
                                item.prepend(dot);
                            }
                        });
                        // Selected items
                        document.querySelectorAll('.choices__list--multiple .choices__item[data-color]').forEach(function (item) {
                            if (!item.querySelector('.tag-dot')) {
                                let color = item.getAttribute('data-color');
                                let dot = document.createElement('span');
                                dot.className = 'tag-dot';
                                dot.style.backgroundColor = color;
                                item.prepend(dot);
                            }
                        });
                    }

                    function refreshTagDots() {
                        propagateTagColors();
                        updateTagDots();
                    }

                    const tagsFilter = document.getElementById('tags-filter');
                    if (tagsFilter) {
                        window.tagsFilterChoices = new Choices(tagsFilter, {
                            removeItemButton: true,
                            placeholder: true,
                            placeholderValue: 'Select tags...',
                            allowHTML: true,
                            itemSelectText: '',
                            callbackOnCreateTemplates: function (template) {
                                return {
                                    item: (classNames, data) => {
                                        const color = data.customProperties && data.customProperties.color ? data.customProperties.color : '';
                                        return template(`
                                            <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable} ${data.placeholder ? classNames.placeholder : ''}"
                                                data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${data.disabled ? 'aria-disabled="true"' : ''} data-color1="${color}"  style="border: 1px solid ${color};">
                                                <span class="tag-dot" style="background-color:${color}"></span>
                                                ${data.label}
                                                <button type="button" class="choices__button" aria-label="Remove item: ${data.value}" data-button><i class="fa-solid fa-xmark choice-remove"></i></button>
                                            </div>
                                        `);
                                    }
                                };
                            }
                        });

                        refreshTagDots();
                        tagsFilter.addEventListener('change', refreshTagDots);
                        // Listen for any DOM changes in the choices list (item removed/added)
                        const choicesList = document.querySelector('.choices__list--dropdown');
                        if (choicesList) {
                            const observer = new MutationObserver(() => {
                                refreshTagDots();
                            });
                            observer.observe(choicesList, {
                                childList: true,
                                subtree: true
                            });
                        }
                        document.querySelector('.choices').addEventListener('click', function () {
                            setTimeout(refreshTagDots, 10);
                        });


                        // // Listen for removeItem from Choices instance
                        // if(tagsFilterChoices)
                        // {
                        //     tagsFilterChoices.passedElement.element.addEventListener('removeItem', function (event) {
                        //         getTicketsFilteredByTag();
                        //     });
                        // }

                    }

                    const ticketDetailTagsFilter = document.getElementById('ticket-detail-tags-filter');
                    if (ticketDetailTagsFilter) {
                        window.ticketTagsFilterChoices = new Choices(ticketDetailTagsFilter, {
                            removeItemButton: true,
                            placeholder: true,
                            placeholderValue: 'Select tags...',
                            allowHTML: true,
                            itemSelectText: '',
                            callbackOnCreateTemplates: function (template) {
                                return {
                                    item: (classNames, data) => {
                                        const color = data.customProperties && data.customProperties.color ? data.customProperties.color : '';
                                        return template(`
                                            <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable} ${data.placeholder ? classNames.placeholder : ''}"
                                                data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${data.disabled ? 'aria-disabled="true"' : ''} data-color1="${color}"  style="border: 1px solid ${color};">
                                                <span class="tag-dot" style="background-color:${color}"></span>
                                                ${data.label}
                                                <button type="button" class="choices__button" aria-label="Remove item: ${data.value}" data-button><i class="fa-solid fa-xmark choice-remove"></i></button>
                                            </div>
                                        `);
                                    }
                                };
                            }
                        });

                        refreshTagDots();
                        ticketDetailTagsFilter.addEventListener('change', refreshTagDots);
                        // Listen for any DOM changes in the choices list (item removed/added)
                        const choicesList = document.querySelector('.choices__list--dropdown');
                        if (choicesList) {
                            const observer = new MutationObserver(() => {
                                refreshTagDots();
                            });
                            observer.observe(choicesList, {
                                childList: true,
                                subtree: true
                            });
                        }
                        document.querySelector('.choices').addEventListener('click', function () {
                            setTimeout(refreshTagDots, 10);
                        });

                        // if(ticketTagsFilterChoices)
                        // {
                        //     ticketTagsFilterChoices.passedElement.element.addEventListener('removeItem', function (event) {
                        //         getTicketsFilteredByTag();
                        //     });
                        // }
                    }


                    $('#select-ticket-agent').select2({
                        placeholder: 'Type and search...',
                        ajax: {
                            url: '<?php echo SB_URL; ?>/include/ajax.php', // Your endpoint
                            method: 'POST',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    function: 'ajax_calls',
                                    'calls[0][function]': 'search-get-users',
                                    'login-cookie': SBF.loginCookie(),
                                    'q': params.term, // ✅ Pass search term
                                    'type': 'agent'
                                };
                            },
                            processResults: function (response) {
                                //response = JSON.parse(response);
                                if (response[0][0] == 'success') {
                                    const users = response[0][1];
                                    console.log("Processed users:", response[0][1]);
                                    return {
                                        results: users.map(user => ({
                                            id: user.id,
                                            text: user.first_name + ' ' + user.last_name,
                                        }))
                                    };
                                }
                            },
                            cache: true
                        },
                        minimumInputLength: 1
                    });

                    $('#select-ticket-reporter').select2({
                        placeholder: 'Type and search...',
                        ajax: {
                            url: '<?php echo SB_URL; ?>/include/ajax.php', // Your endpoint
                            method: 'POST',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    function: 'ajax_calls',
                                    'calls[0][function]': 'search-get-users',
                                    'login-cookie': SBF.loginCookie(),
                                    'q': params.term, // ✅ Pass search term
                                    'type': 'user'
                                };
                            },
                            processResults: function (response) {
                                //response = JSON.parse(response);
                                if (response[0][0] == 'success') {
                                    const users = response[0][1];
                                    console.log("Processed users:", response[0][1]);
                                    // document.querySelector('#name select').value = response.priority_id;
                                    return {
                                        results: users.map(user => ({
                                            id: user.id,
                                            text: user.first_name + ' ' + user.last_name,
                                            email: user.email,
                                            name: user.first_name + ' ' + user.last_name
                                        }))
                                    };
                                }
                            },
                            cache: true
                        },
                        minimumInputLength: 1
                    });
                </script>
                <?php //} ?>
                <?php if ($active_areas['articles']) { ?>
                    <div class="sb-area-articles sb-loading">
                        <?php echo $header; ?>
                        <div class="sb-top-bar">
                            <div>
                                <div class="sb-menu-wide sb-menu-articles">
                                    <ul>
                                        <li data-type="articles" class="sb-active">
                                            <?php sb_e("Articles"); ?>
                                        </li>
                                        <li data-type="categories">
                                            <?php sb_e("Categories"); ?>
                                        </li>
                                        <!-- <li data-type="settings">
                                            <?php sb_e("Settings"); ?>
                                        </li>
                                        <?php
                                        if ($active_areas["reports"]) {
                                            echo '<li data-type="reports">' .
                                                sb_("Reports") .
                                                "</li>";
                                        }
                                        sb_docs_link("#articles");
                                        ?> -->
                                    </ul>
                                </div>
                            </div>
                            <div>

                                <a class="sb-btn sb-save-articles sb-icon">
                                    <i class="sb-icon-check"></i>
                                    <?php sb_e("Save changes"); ?>
                                </a>
                                <a class="sb-btn-icon sb-view-article" href="" target="_blank">
                                    <i class="sb-icon-next"></i>
                                </a>
                            </div>
                        </div>
                        <div class="sb-tab sb-inner-tab">
                            <div class="sb-nav sb-nav-only sb-scroll-area">
                                <ul class="ul-articles"></ul>
                                <div class="sb-add-article sb-btn sb-icon sb-btn-white clr-change">
                                    <i class="sb-icon-plus"></i>
                                    <?php sb_e("Add new article"); ?>
                                </div>
                                <ul class="ul-categories"></ul>
                                <div class="sb-add-category sb-btn sb-icon sb-btn-white  clr-change">
                                    <i class="sb-icon-plus"></i>
                                    <?php sb_e("Add new category"); ?>
                                </div>
                            </div>
                            <div class="sb-content sb-content-articles sb-scroll-area sb-loading">
                                <div class="content_article">
                                    <div class="articleHEad">
                                        <div class="">
                                            <p class="head mb-4">Articles Settings</p>
                                            <p class="des mb-0">Manage preferences and options for your articles.</p>
                                        </div>
                                    </div>
                                    <div class="articles_bg">
                                        <h2 class="sb-language-switcher-cnt">
                                            <?php sb_e("Title"); ?>
                                        </h2>
                                        <div class="sb-setting sb-type-text sb-article-title">
                                            <div>
                                                <input type="text" />
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e("Content"); ?>
                                        </h2>
                                        <div class="sb-setting sb-type-textarea sb-article-content">
                                            <div>
                                                <?php echo sb_get_setting(
                                                    "disable-editor-js"
                                                )
                                                    ? "<textarea></textarea>"
                                                    : '<div id="editorjs"></div>'; ?>
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e("External link"); ?>
                                        </h2>
                                        <div class="sb-setting sb-type-text sb-article-link">
                                            <div>
                                                <input type="text" />
                                            </div>
                                        </div>
                                        <div class="sb-article-categories sb-grid">
                                            <div>
                                                <h2>
                                                    <?php sb_e(
                                                        "Parent category"
                                                    ); ?>
                                                </h2>
                                                <div class="sb-setting sb-type-select">
                                                    <div>
                                                        <select id="article-parent-categories"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h2>
                                                    <?php sb_e("Categories"); ?>
                                                </h2>
                                                <div class="sb-setting sb-type-select">
                                                    <div>
                                                        <select id="article-categories"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h2 id="sb-article-id"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="sb-content sb-content-categories sb-scroll-area sb-loading">
                                <div class="content_article">
                                    <div class="articleHEad">
                                        <div class="">
                                            <p class="head mb-4">Categories Settings</p>
                                            <p class="des mb-0">Manage article display, categories, and publishing options.</p>
                                        </div>
                                    </div>
                                    <div class="articles_bg">
                                        <h2 class="fw-semibold fs-6 m-0 mb-2">
                                            Categories Settings
                                        </h2>
                                        <h2 class="fw-normal fs-7 mt-0 mx-o mb-5">
                                            Manage and organize content types.
                                        </h2>
                                        <h2 class="sb-language-switcher-cnt">
                                            <?php sb_e("Name"); ?>
                                        </h2>
                                        <div class="sb-setting sb-type-text">
                                            <div>
                                                <input id="category-title" type="text" />
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e("Description"); ?>
                                        </h2>
                                        <div class="sb-setting sb-type-textarea">
                                            <div>
                                                <textarea id="category-description"></textarea>
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e("Image"); ?>
                                        </h2>
                                        <div data-type="image" class="sb-input sb-setting sb-input-image">
                                            <div id="category-image" class="image">
                                                <div class="sb-icon-close"></div>
                                            </div>
                                        </div>
                                        <h2 class="category-parent">
                                            <?php sb_e("Parent category"); ?>
                                        </h2>
                                        <div data-type="checkbox" class="sb-setting sb-type-checkbox category-parent">
                                            <div class="input">
                                                <input id="category-parent" type="checkbox" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($active_areas["reports"]) { ?>
                    <div class="sb-area-reports sb-area-reports_new sb-loading">
                        <?php echo $header; ?>
                        <div class="sb-top-bar p-3">
                            <div>
                                <!-- <h2>
                                    <?php sb_e("Reports"); ?>
                                </h2> -->
                            </div>
                            <div>
                                <div class="sb-setting sb-type-text">
                                    <input id="sb-date-picker" placeholder="00/00/0000 - 00/00/0000" type="text" />
                                </div>
                                <div class="sb-report-export sb-btn-icon">
                                    <i class="sb-icon-download"></i>
                                </div>
                            </div>
                        </div>
                        <div class="sb-tab">
                            <div class="sb-nav sb-nav-only sb-scroll-area">
                                <div>
                                    <?php sb_e("Reports"); ?>
                                </div>
                                <ul>
                                    <li class="sb-tab-nav-title">
                                        <img src="<?php echo SB_URL; ?>/media/conversation_icon.svg" alt="icon" class="mr-1" />

                                        <?php sb_e("Conversations"); ?>
                                    </li>
                                    <li class="li" id="conversations" class="sb-active">
                                        <?php sb_e("Conversations"); ?>
                                    </li>
                                    <li class="li" id="missed-conversations">
                                        <?php sb_e("Missed conversations"); ?>
                                    </li>
                                    <li class="li" id="conversations-time">
                                        <?php sb_e("Conversations time"); ?>
                                    </li>
                                    <li class="sb-tab-nav-title">
                                        <img src="<?php echo SB_URL; ?>/media/msg_icon.svg" alt="icon" class="mr-1" />
                                        <?php sb_e("Direct messages"); ?>
                                    </li>
                                    <li class="li" id="direct-messages">
                                        <?php sb_e("Chat messages"); ?>
                                    </li>
                                    <li class="li" id="direct-emails">
                                        <?php sb_e("Emails"); ?>
                                    </li>
                                    <li class="li" id="direct-sms">
                                        <?php sb_e("Text messages"); ?>
                                    </li>
                                    <li class="sb-tab-nav-title">
                                        <img src="<?php echo SB_URL; ?>/media/agents_icon.svg" alt="icon" class="mr-1" />
                                        <?php sb_e("Users and agents"); ?>
                                    </li>
                                    <li class="li" id="visitors">
                                        <?php sb_e("Visitors"); ?>
                                    </li>
                                    <li class="li" id="leads">
                                        <?php sb_e("Leads"); ?>
                                    </li>
                                    <li class="li" id="users">
                                        <?php sb_e("Users"); ?>
                                    </li>
                                    <li class="li" id="registrations">
                                        <?php sb_e("Registrations"); ?>
                                    </li>
                                    <li class="li" id="agents-response-time">
                                        <?php sb_e("Agent response time"); ?>
                                    </li>
                                    <li class="li" id="agents-conversations">
                                        <?php sb_e("Agent conversations"); ?>
                                    </li>
                                    <li class="li" id="agents-conversations-time">
                                        <?php sb_e(
                                            "Agent conversations time"
                                        ); ?>
                                    </li>
                                    <li class="li" id="agents-ratings">
                                        <?php sb_e("Agent ratings"); ?>
                                    </li>
                                    <li class="li" id="countries">
                                        <?php sb_e("Countries"); ?>
                                    </li>
                                    <li class="li" id="languages">
                                        <?php sb_e("Languages"); ?>
                                    </li>
                                    <li class="li" id="browsers">
                                        <?php sb_e("Browsers"); ?>
                                    </li>
                                    <li class="li" id="os">
                                        <?php sb_e("Operating systems"); ?>
                                    </li>
                                    <li class="sb-tab-nav-title">
                                        <img src="<?php echo SB_URL; ?>/media/automation_icon.svg" alt="icon" class="mr-1" />
                                        <?php sb_e("Automation"); ?>
                                    </li>
                                    <li class="li" id="follow-up">
                                        <?php sb_e("Follow up"); ?>
                                    </li>
                                    <li class="li" id="message-automations">
                                        <?php sb_e("Message automations"); ?>
                                    </li>
                                    <li class="li" id="email-automations">
                                        <?php sb_e("Email automations"); ?>
                                    </li>
                                    <?php if ($sms) {
                                        echo '<li class="li" id="sms-automations">' .
                                            sb_("Text message automations") .
                                            "</li>";
                                    } ?>
                                    <li class="sb-tab-nav-title">
                                        <img src="<?php echo SB_URL; ?>/media/article_icon.svg" alt="icon" class="mr-1" />
                                        <?php sb_e("Articles"); ?>
                                    </li>
                                    <li class="li" id="articles-searches">
                                        <?php sb_e("Searches"); ?>
                                    </li>
                                    <li class="li" id="articles-views">
                                        <?php sb_e("Article views"); ?>
                                    </li>
                                    <li class="li" id="articles-views-single">
                                        <?php sb_e(
                                            "Article views by article"
                                        ); ?>
                                    </li>
                                    <li class="li" id="articles-ratings">
                                        <?php sb_e("Article ratings"); ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="sb-content sb-scroll-area">
                                <div class="sb-reports-chart">
                                    <div class="chart-cnt mt-3">
                                        <canvas id="canvas"></canvas>
                                    </div>
                                </div>
                                <div class="sb-reports-sidebar mt-3">
                                    <div class="sb-title sb-reports-title"></div>
                                    <p class="sb-reports-text"></p>
                                    <div class="sb-collapse">
                                        <div>
                                            <table class="sb-table"></table>
                                        </div>
                                    </div>
                                </div>
                                <p class="sb-no-results">
                                    <?php echo sb_("No data found."); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($active_areas["settings"]) { ?>
                    <div class="sb-area-settings settings_new">
                        <?php echo $header; ?>
                        <div class="sb-tab">
                            <div class="sb-nav sb-scroll-area">
                                <div>
                                    <?php sb_e("Settings"); ?>
                                </div>
                                <ul class="setting_sidebar">
                                    <li id="tab-chat" class="sb-active">
                                        <?php echo $disable_translations
                                            ? "Chat"
                                            : sb_("Chat"); ?>
                                    </li>
                                    <li id="tab-admin">
                                        <?php echo $disable_translations
                                            ? "Admin"
                                            : sb_("Admin"); ?>
                                    </li>
                                    <li id="tab-notifications">
                                        <?php echo $disable_translations
                                            ? "Notifications"
                                            : sb_("Notifications"); ?>
                                    </li>
                                    <li id="tab-users">
                                        <?php echo $disable_translations
                                            ? "Users"
                                            : sb_("Users"); ?>
                                    </li>
                                    <!-- <li id="tab-design">
                                        <?php echo $disable_translations
                                            ? "Design"
                                            : sb_("Design"); ?>
                                    </li> -->
                                    <li id="tab-messages">
                                        <?php echo $disable_translations
                                            ? "Messages & Forms"
                                            : sb_("Messages & Forms"); ?>
                                    </li>
                                    <li id="tab-various">
                                        <?php echo $disable_translations
                                            ? "Miscellaneous"
                                            : sb_("Miscellaneous"); ?>
                                    </li>
                                    <?php for (
                                        $i = 0;
                                        $i < count($apps);
                                        $i++
                                    ) {
                                        if (
                                            defined($apps[$i][0]) &&
                                            (!$is_cloud ||
                                                in_array(
                                                    $apps[$i][1],
                                                    $cloud_active_apps
                                                ))
                                        ) {
                                            echo '<li id="tab-' .
                                                $apps[$i][1] .
                                                '">' .
                                                sb_($apps[$i][2]) .
                                                "</li>";
                                        }
                                    } ?>
                                    <li id="tab-apps">
                                        <?php echo $disable_translations
                                            ? "Apps"
                                            : sb_("Apps"); ?>
                                    </li>
                                    <li id="tab-articles">
                                        <?php echo $disable_translations
                                            ? "Articles"
                                            : sb_("Articles"); ?>
                                    </li>
                                    <!--<li id="tab-automations">
                                        <?php
                                        //echo $disable_translations ? 'Automations' : sb_('Automations')
                                        ?>
                                    </li>
                                    <li id="tab-translations">
                                        <?php
                                        //echo $disable_translations ? 'Translations' : sb_('Translations')
                                        ?>
                                    </li> -->
                                </ul>
                            </div>
                            <div class="sb-content sb-scroll-area pt-4">
                                <div class="sb-active">


                                    <!--div class="sb-top-bar save_settings">
                                        <div class="">
                                            <p class="head mb-4">Chat Settings</p>
                                            <p class="des mb-0">Configure your chat settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div-->
                                    <!--?php sb_populate_settings(
                                        "chat",
                                        $sb_settings
                                    ); ?-->
                                    <!-- chat settings -->
                                    <div class="main-content">
                                        <div class="sb-top-bar settings-header">
                                            <div>
                                                <p class="head">Chat</p>
                                            </div>
                                            <div>
                                                <a class="sb-btn sb-save-changes sb-icon sb_btn_new" style="float: right;">
                                                    <i class="sb-icon-check"></i>Save changes</a>
                                            </div>
                                        </div>

                                        <div class="settings-card">
                                            <div class="my-tabs">
                                                <div class="my-tab active" data-target="availability-content">Availability</div>
                                                <div class="my-tab" data-target="appearance-content">Appearance & Features</div>
                                                <div class="my-tab" data-target="management-content">Management</div>
                                            </div>

                                            <div id="availability-content" class="settings-tab">
                                                <?php sb_populate_settings("chat", $sb_settings, true, 'chat-availability'); ?>
                                            </div>

                                            <div id="appearance-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("chat", $sb_settings, true, 'chat-appearance-and-features'); ?>
                                            </div>

                                            <div id="management-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("chat", $sb_settings, true, 'chat-management'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <link href="https://cdn.jsdelivr.net/npm/flag-icons@6.11.0/css/flag-icons.min.css"
                                        rel="stylesheet">

                                    <style>
                                        input.agents-textfield {
                                            width: 100%;
                                            max-width: max-content;
                                        }

                                        select.country-drop {
                                            width: 100%;
                                            max-width: max-content;
                                        }

                                        .sb-setting label.custom-switch {
                                            min-width: unset;
                                        }

                                        .custom-switch {
                                            position: relative;
                                            display: inline-block;
                                            width: 36px;
                                            height: 18px;
                                        }

                                        .custom-switch input {
                                            opacity: 0;
                                            width: 0;
                                            height: 0;
                                        }

                                        .custom-switch .slider {
                                            position: absolute;
                                            cursor: pointer;
                                            background-color: #ccc;
                                            border-radius: 34px;
                                            top: 0;
                                            left: 0;
                                            right: 0;
                                            bottom: 0;
                                            transition: .4s;
                                        }

                                        .custom-switch .slider:before {
                                            position: absolute;
                                            content: "";
                                            height: 14px;
                                            width: 14px;
                                            left: 2px;
                                            bottom: 2px;
                                            background-color: white;
                                            transition: .4s;
                                            border-radius: 50%;
                                        }

                                        .custom-switch input:checked+.slider {
                                            background-color: #0d6efd;
                                            /* Bootstrap primary */
                                        }

                                        .custom-switch input:checked+.slider:before {
                                            transform: translateX(18px);
                                        }

                                        .my-tabs {
                                            display: flex;
                                            margin-bottom: 40px;
                                            gap: 10px;
                                        }

                                        .my-tab {
                                            padding: 10px 15px;
                                            cursor: pointer;
                                            border: 1px solid transparent;
                                            border-top-left-radius: 5px;
                                            border-top-right-radius: 5px;
                                            border-bottom-right-radius: 5px;
                                            border-bottom-left-radius: 5px;
                                            background-color: #f8f9fa;
                                            margin-right: 5px;
                                            text-align: center;
                                            font-size: 13px;
                                            font-weight: 800;
                                        }

                                        .my-tab.active {
                                            background-color: #1B1F23;
                                            font-weight: 400;
                                            color: #fff;
                                        }

                                        .main-content .sb-setting {
                                            border-bottom: 1px solid rgb(230, 230, 230);
                                            align-items: center;
                                            padding: 30px 0px 30px 0px;
                                        }
                                    </style>
                                    <!-- chat settings -->

                                    <!-- <div class="sb-top-bar save_settings">
                                        <div class="">
                                            <p class="head mb-4">Chat Settings</p>
                                            <p class="des mb-0">Configure your chat settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div> -->
                                    <?php //sb_populate_settings("chat",$sb_settings,true,'chat-appearance-and-features'); ?>
                                </div>
                                <div>
                                    <!-- <div class="sb-top-bar save_settings">
                                        <div class="">
                                            <p class="head mb-4">Admin Settings</p>
                                            <p class="des mb-0">Configure your admin settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div> -->
                                    <?php //sb_populate_settings("admin",$sb_settings ); ?>
                                    <div class="main-content">
                                        <div class="sb-top-bar settings-header">
                                            <div>
                                                <p class="head">Admin</p>
                                            </div>
                                            <div>
                                                <a class="sb-btn sb-save-changes sb-icon sb_btn_new" style="float: right;">
                                                    <i class="sb-icon-check"></i>Save changes</a>
                                            </div>
                                        </div>
                                        <div class="settings-card">
                                            <div class="my-tabs">
                                                <div class="my-tab active" data-target="panel-setting-content">Panel Setting</div>
                                                <div class="my-tab" data-target="language">Language</div>
                                                <div class="my-tab" data-target="admin-chat-management-content">Chat Management
                                                </div>
                                                <div class="my-tab" data-target="permission-setting-content">Permission Setting
                                                </div>
                                                <div class="my-tab" data-target="settings-customization-content">Settings
                                                    Customization</div>
                                                <div class="my-tab" data-target="customer-content">Customer </div>
                                                <div class="my-tab" data-target="auto-saved-message-inbox-content">Auto Saved
                                                    Message Inbox</div>
                                                <div class="my-tab" data-target="customization-and-themes-content">Customization &
                                                    Themes</div>
                                                <div class="my-tab" data-target="switch-accounts-content">Switch Accounts</div>
                                                <div class="my-tab" data-target="department-content">Department</div>
                                            </div>

                                            <div id="panel-setting-content" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'panel-setting'); ?>
                                            </div>

                                            <div id="language" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'language'); ?>
                                            </div>

                                            <div id="admin-chat-management-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'admin-chat-management'); ?>
                                            </div>

                                            <div id="permission-setting-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'permission-setting'); ?>
                                            </div>

                                            <div id="settings-customization-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'settings-customization'); ?>
                                            </div>

                                            <div id="customer-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'customer'); ?>
                                            </div>

                                            <div id="auto-saved-message-inbox-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'auto-saved-message-inbox'); ?>
                                            </div>

                                            <div id="customization-and-themes-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'customization-and-themes'); ?>
                                            </div>

                                            <div id="switch-accounts-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'switch-accounts'); ?>
                                            </div>

                                            <div id="department-content" style="display: none;" class="settings-tab">
                                                <?php sb_populate_settings("admin", $sb_settings, true, 'department'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Notification Settings</p>
                                            <p class="des mb-0">Configure your notification settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="users-tab">
                                        <?php sb_populate_settings(
                                            "notifications",
                                            $sb_settings
                                        ); ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Users Settings</p>
                                            <p class="des mb-0">Configure your users settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="users-tab">
                                        <?php sb_populate_settings(
                                            "users",
                                            $sb_settings
                                        ); ?>
                                    </div>
                                </div>
                                <!-- <div>
                                    <div class="sb-top-bar save_settings">
                                        <div class="">
                                            <p class="head mb-4">Design Settings</p>
                                            <p class="des mb-0">Configure your design settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php sb_populate_settings(
                                        "design",
                                        $sb_settings
                                    ); ?>
                                </div> -->
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Messages Settings</p>
                                            <p class="des mb-0">Configure your message settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="users-tab">
                                        <?php sb_populate_settings(
                                            "messages",
                                            $sb_settings
                                        ); ?>
                                    </div>

                                </div>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Miscellaneous Settings</p>
                                            <p class="des mb-0">Configure additional options and preferences.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="users-tab">
                                        <?php sb_populate_settings(
                                            "miscellaneous",
                                            $sb_settings
                                        ); ?>
                                    </div>
                                </div>
                                <?php sb_apps_area(
                                    $apps,
                                    $cloud_active_apps
                                ); ?>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Articles Settings</p>
                                            <p class="des mb-0">Configure articles settings.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="users-tab">
                                        <?php sb_populate_settings(
                                            "articles",
                                            $sb_settings
                                        ); ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="sb-automations-area">
                                        <div class="sb-select">
                                            <p data-value="messages">
                                                <?php sb_e("Messages"); ?>
                                            </p>
                                            <ul>
                                                <li data-value="messages" class="sb-active">
                                                    <?php sb_e("Messages"); ?>
                                                </li>
                                                <li data-value="emails">
                                                    <?php sb_e("Emails"); ?>
                                                </li>
                                                <?php if ($sms) {
                                                    echo '<li data-value="sms">' .
                                                        sb_("Text messages") .
                                                        "</li>";
                                                } ?>
                                                <li data-value="popups">
                                                    <?php sb_e("Pop-ups"); ?>
                                                </li>
                                                <li data-value="design">
                                                    <?php sb_e("Design"); ?>
                                                </li>
                                                <li data-value="more">
                                                    <?php sb_e("More"); ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="sb-inner-tab sb-tab">
                                            <div class="sb-nav sb-nav-only">
                                                <ul></ul>
                                                <div class="sb-add-automation sb-btn sb-icon">
                                                    <i class="sb-icon-plus"></i>
                                                    <?php sb_e(
                                                        "Add new automation"
                                                    ); ?>
                                                </div>
                                            </div>
                                            <div class="sb-content sb-hide">
                                                <div class="sb-automation-values">
                                                    <h2 class="sb-language-switcher-cnt">
                                                        <?php sb_e("Name"); ?>
                                                    </h2>
                                                    <div class="sb-setting sb-type-text">
                                                        <div>
                                                            <input data-id="name" type="text" />
                                                        </div>
                                                    </div>
                                                    <h2>
                                                        <?php sb_e(
                                                            "Message"
                                                        ); ?>
                                                    </h2>
                                                    <div class="sb-setting sb-type-textarea">
                                                        <div>
                                                            <textarea data-id="message"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="sb-automation-extra"></div>
                                                </div>
                                                <div class="sb-automation-conditions">
                                                    <hr />
                                                    <h2>
                                                        <?php sb_e(
                                                            "Conditions"
                                                        ); ?>
                                                    </h2>
                                                    <div class="sb-conditions"></div>
                                                    <div class="sb-add-condition sb-btn sb-icon">
                                                        <i class="sb-icon-plus"></i>
                                                        <?php sb_e(
                                                            "Add condition"
                                                        ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="sb-translations sb-tab">
                                        <div class="sb-nav sb-nav-only">
                                            <div class="sb-active"></div>
                                            <ul></ul>
                                        </div>
                                        <div class="sb-content">
                                            <div class="sb-hide">
                                                <div class="sb-menu-wide">
                                                    <div>
                                                        <?php sb_e(
                                                            "Front End"
                                                        ); ?>
                                                    </div>
                                                    <ul>
                                                        <li data-value="front" class="sb-active">
                                                            <?php sb_e(
                                                                "Front End"
                                                            ); ?>
                                                        </li>
                                                        <li data-value="admin">
                                                            <?php sb_e(
                                                                "Admin"
                                                            ); ?>
                                                        </li>
                                                        <li data-value="admin/js">
                                                            <?php sb_e(
                                                                "Client side admin"
                                                            ); ?>
                                                        </li>
                                                        <li data-value="admin/settings">
                                                            <?php sb_e(
                                                                "Settings"
                                                            ); ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <a class="sb-btn sb-icon sb-add-translation">
                                                    <i class="sb-icon-plus"></i>
                                                    <?php sb_e(
                                                        "New translation"
                                                    ); ?>
                                                </a>
                                            </div>
                                            <div class="sb-translations-list"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $('.my-tab').click(function () {
                            $(this).siblings().removeClass('active');
                            $(this).addClass('active');
                            $(this).parent().parent().find('.settings-tab').hide();
                            $('#' + $(this).data('target')).show();
                        });
                    </script>

                <?php } ?>
            </main>
            <?php
            sb_profile_box();
            sb_profile_edit_box();
            sb_ticket_box();
            sb_ticket_edit_box();
            sb_dialog();
            sb_direct_message_box();
            sb_app_box();
            if (defined("SB_DIALOGFLOW")) {
                require_once SB_PATH . "/apps/dialogflow/components.php";
                sb_dialogflow_intent_box();
            }
            if (defined("SB_WHATSAPP")) {
                sb_whatsapp_send_template_box();
            }
            if ($is_admin && !$is_cloud) {
                sb_updates_box();
            }
            ?>
            <div id="sb-generic-panel"></div>
            <form class="sb-upload-form-admin sb-upload-form" action="<?php echo sb_sanatize_string(
                $_SERVER["PHP_SELF"]
            ); ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="files[]" class="sb-upload-files" multiple />
            </form>
            <div class="sb-info-card"></div>
        <?php } else {
            if ($is_cloud) {
                sb_cloud_reset_login();
            } else {
                sb_login_box();
            }
        } ?>
        <div class="sb-lightbox sb-lightbox-media">
            <div></div>
            <i class="sb-icon-close"></i>
        </div>
        <div class="sb-lightbox-overlay"></div>
        <div class="sb-loading-global sb-loading sb-lightbox"></div>
        <input type="email" name="email" style="display:none" autocomplete="email" />
        <input type="password" name="hidden" style="display:none" autocomplete="new-password" />
    </div>
    <?php
    if (!empty(sb_get_setting("custom-js")) && !$is_cloud) {
        echo '<script id="sb-custom-js" src="' .
            sb_get_setting("custom-js") .
            '"></script>';
    }
    if (!empty(sb_get_setting("custom-css")) && !$is_cloud) {
        echo '<link id="sb-custom-css" rel="stylesheet" type="text/css" href="' .
            sb_get_setting("custom-css") .
            '" media="all">';
    }
    if ($is_cloud) {
        sb_cloud_css_js();
    }
}
/*
 * ----------------------------------------------------------
 * HTML FUNCTIONS
 * ----------------------------------------------------------
 *
 * 1. Echo the apps settings and apps area
 * 2. Echo the apps conversation panel container
 * 3. Code check
 * 4. Return the users table extra fields
 * 5. Return the Dialogflow languages list
 * 6. Return the conversations filter
 *
 */
function sb_apps_area($apps, $cloud_active_apps)
{
    $apps_wp = ["SB_WP", "SB_WOOCOMMERCE", "SB_UMP", "SB_ARMEMBER"];
    $apps_php = [];
    $apps_cloud_excluded = [
        "whmcs",
        "martfury",
        "aecommerce",
        "perfex",
        "opencart",
    ];
    $wp = defined("SB_WP");
    $code = "";
    $is_cloud = sb_is_cloud();
    for ($i = 0; $i < count($apps); $i++) {
        if (
            defined($apps[$i][0]) &&
            (!$is_cloud || in_array($apps[$i][1], $cloud_active_apps))
        ) {
            // $code .= '<div>' . sb_populate_app_settings($apps[$i][1]) . '</div>';
            $code .=
                '<div>
            <div class="sb-top-bar save_settings settings-header">
                <div class="">
                    <p class="head mb-4">Tickets Settings</p>
                    <p class="des mb-0">Configure tickets settings.</p>
                </div>
                <div>
                    <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                        <i class="sb-icon-check"></i>
                        Save changes
                    </a>
                </div>
            </div>
            <div class="users-tab">
            ' .
                sb_populate_app_settings($apps[$i][1]) .
                "</div></div>";
        }
    }
    $code .= '<div><div class="sb-apps">';
    for ($i = 1; $i < count($apps); $i++) {
        if (
            (($wp && !in_array($apps[$i][0], $apps_php)) ||
                (!$wp && !in_array($apps[$i][0], $apps_wp))) &&
            (!$is_cloud || !in_array($apps[$i][1], $apps_cloud_excluded))
        ) {
            $code .=
                '<div data-app="' .
                $apps[$i][1] .
                '">' .
                (defined($apps[$i][0]) &&
                    (!$is_cloud || in_array($apps[$i][1], $cloud_active_apps))
                    ? '<i class="sb-icon-check"></i>'
                    : "") .
                ' <img src="' .
                SB_URL .
                "/media/apps/" .
                $apps[$i][1] .
                '.svg" /><h2>' .
                $apps[$i][2] .
                "</h2><p>" .
                str_replace(
                    "{R}",
                    $is_cloud ? SB_CLOUD_BRAND_NAME : "Support Board",
                    sb_s($apps[$i][3])
                ) .
                "</p></div>";
        }
    }
    echo $code . "</div></div>";
}
function sb_apps_panel()
{
    $code = "";
    $collapse = sb_get_setting("collapse") ? " sb-collapse" : "";
    $panels = [
        ["SB_UMP", "ump"],
        ["SB_WOOCOMMERCE", "woocommerce"],
        ["SB_PERFEX", "perfex"],
        ["SB_WHMCS", "whmcs"],
        ["SB_AECOMMERCE", "aecommerce"],
        ["SB_ARMEMBER", "armember"],
        ["SB_ZENDESK", "zendesk"],
        ["SB_MARTFURY", "martfury"],
        ["SB_OPENCART", "opencart"],
    ];
    for ($i = 0; $i < count($panels); $i++) {
        if (defined($panels[$i][0])) {
            $code .=
                '<div class="sb-panel-details sb-panel-' .
                $panels[$i][1] .
                $collapse .
                '"></div>';
        }
    }
    if (sb_is_cloud()) {
        $code .=
            '<div class="sb-panel-details sb-panel-shopify' .
            $collapse .
            '"></div>';
    }
    echo $code;
}
function sb_box_ve()
{
    if (
        (!isset($_COOKIE["SA_" . "VGC" . "KMENS"]) &&
            !isset($_COOKIE["_ga_" . "VGC" . "KMENS"])) ||
        !password_verify(
            "VGC" . "KMENS",
            isset($_COOKIE["_ga_" . "VGC" . "KMENS"])
            ? $_COOKIE["_ga_" . "VGC" . "KMENS"]
            : $_COOKIE["SA_" . "VGC" . "KMENS"]
        )
    ) {
        // Deprecated. _ga will be removed
        echo file_get_contents(SB_PATH . "/resources/sb.html");
        return false;
    }
    return true;
}
function sb_users_table_extra_fields()
{
    $extra_fields = sb_get_setting("user-table-extra-columns");
    $count =
        $extra_fields && !is_string($extra_fields)
        ? count($extra_fields)
        : false;
    if ($count) {
        $code = "";
        for ($i = 0; $i < $count; $i++) {
            $slug = $extra_fields[$i]["user-table-extra-slug"];
            $code .=
                '<th data-field="' .
                $slug .
                '" data-extra="true">' .
                sb_string_slug($slug, "string") .
                "</th>";
        }
        echo $code;
    }
}
function sb_dialogflow_languages_list()
{
    $languages = json_decode(
        file_get_contents(
            SB_PATH . "/apps/dialogflow/dialogflow_languages.json"
        ),
        true
    );
    $code =
        '<div data-type="select" class="sb-setting sb-type-select sb-dialogflow-languages"><div class="input"><select><option value="">' .
        sb_("Default") .
        "</option>";
    for ($i = 0; $i < count($languages); $i++) {
        $code .=
            '<option value="' .
            $languages[$i][1] .
            '">' .
            $languages[$i][0] .
            "</option>";
    }
    return $code . "</select></div></div>";
}
function sb_conversations_filter($cloud_active_apps)
{
    if (sb_get_multi_setting("disable", "disable-filters")) {
        return;
    }
    $is_cloud = sb_is_cloud();
    $departments =
        sb_is_agent(false, true, true) ||
        !sb_isset(sb_get_active_user(), "department")
        ? sb_get_setting("departments", [])
        : [];
    $sources = [
        ["em", "Email", true],
        ["tk", "Tickets", "SB_TICKETS"],
        ["wa", "WhatsApp", "SB_WHATSAPP"],
        ["fb", "Messenger", "SB_MESSENGER"],
        ["ig", "Instagram", "SB_MESSENGER"],
        ["tg", "Telegram", "SB_TELEGRAM"],
        ["tw", "Twitter", "SB_TWITTER"],
        ["vb", "Viber", "SB_VIBER"],
        ["ln", "LINE", "SB_LINE"],
        ["wc", "WeChat", "SB_WECHAT"],
        ["za", "Zalo", "SB_ZALO"],
        ["tm", "Text message", true],
    ];
    $tags = sb_get_multi_setting("disable", "disable-tags")
        ? []
        : sb_get_setting("tags", []);
    $count = is_array($departments) ? count($departments) : 0;
    $code =
        (count($tags) && sb_get_multi_setting("tags-settings", "tags-starred")
            ? '<i class="sb-icon sb-icon-tag-line sb-filter-star" data-color-text="' .
            $tags[0]["tag-color"] .
            '" data-value="' .
            $tags[0]["tag-name"] .
            '"></i>'
            : "") .
        '<div class="sb-filter-btn"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M20.7801 8.25167L18.2506 5.72294L18.2673 14.5714C18.2673 15.0452 17.8839 15.4286 17.4101 15.4286C16.938 15.4286 16.553 15.0452 16.553 14.5714L16.5363 5.7832L14.0687 8.25167C13.7338 8.58566 13.1914 8.58566 12.8566 8.25167C12.5218 7.91685 12.5218 7.37361 12.8566 7.03878L16.3839 3.51144C16.6183 3.2009 16.9916 3 17.4101 3C17.7366 3 18.0329 3.12053 18.2589 3.31976C18.2924 3.34486 18.3242 3.37249 18.3544 3.40263L21.9922 7.03878C22.327 7.37361 22.327 7.91685 21.9922 8.25167C21.6574 8.58566 21.115 8.58566 20.7801 8.25167Z" fill="black"/>
<path d="M1.71484 6.25708C1.71484 5.87873 2.0212 5.57153 2.40123 5.57153H9.1713C9.33536 5.57153 9.48604 5.62846 9.60322 5.72387C9.75896 5.84944 9.8577 6.04196 9.8577 6.25708V6.60028C9.8577 6.97862 9.55136 7.28582 9.1713 7.28582H2.40123C2.20201 7.28582 2.02288 7.20211 1.89732 7.06735C1.82701 6.99285 1.77511 6.90244 1.74498 6.802C1.72489 6.73839 1.71484 6.67059 1.71484 6.60028V6.25708Z" fill="black"/>
<path d="M2.14844 12.477C2.08315 12.503 2.02288 12.5381 1.96931 12.5816C1.81361 12.7072 1.71484 12.8989 1.71484 13.114V13.4572C1.71484 13.8356 2.0212 14.1428 2.40123 14.1428H13.0284C13.4085 14.1428 13.7148 13.8356 13.7148 13.4572V13.114C13.7148 12.7357 13.4085 12.4285 13.0284 12.4285H2.40123C2.3125 12.4285 2.22712 12.446 2.14844 12.477Z" fill="black"/>
<path d="M1.77846 19.684C1.73828 19.7711 1.71484 19.8682 1.71484 19.9712V20.3144C1.71484 20.5329 1.81696 20.7279 1.97601 20.8534C2.0932 20.9455 2.24051 20.9999 2.40123 20.9999H20.8092C21.1892 20.9999 21.4956 20.6927 21.4956 20.3144V19.9712C21.4956 19.5928 21.1892 19.2856 20.8092 19.2856H2.40123C2.125 19.2856 1.88728 19.4488 1.77846 19.684Z" fill="black"/>
</svg>
<div><div class="sb-select' .
        ($count ? "" : " sb-hide") .
        '"><p>' .
        sb_("All departments") .
        "</p><ul" .
        ($count > 8 ? ' class="sb-scroll-area"' : "") .
        '><li data-value="">' .
        sb_("All departments") .
        "</li>";
    for ($i = 0; $i < $count; $i++) {
        $code .=
            '<li data-value="' .
            $departments[$i]["department-id"] .
            '">' .
            ucfirst(sb_($departments[$i]["department-name"])) .
            "</li>";
    }
    $code .= "</ul></div>";
    if (!sb_get_multi_setting("disable", "disable-channels-filter")) {
        $count = count($sources);
        $code .=
            '<div class="sb-select"><p>' .
            sb_("All channels") .
            "</p><ul" .
            ($count > 8 ? ' class="sb-scroll-area"' : "") .
            '><li data-value="false">' .
            sb_("All channels") .
            '</li><li data-value="chat">' .
            sb_("Chat") .
            "</li>";
        for ($i = 0; $i < $count; $i++) {
            if (
                $sources[$i][2] === true ||
                (defined($sources[$i][2]) &&
                    (!$is_cloud ||
                        in_array(
                            strtolower(substr($sources[$i][2], 3)),
                            $cloud_active_apps
                        )))
            ) {
                $code .=
                    '<li data-value="' .
                    $sources[$i][0] .
                    '">' .
                    $sources[$i][1] .
                    "</li>";
            }
        }
        $code .= "</ul></div>";
    } else {
        $code .= '<div class="sb-select sb-hide"></div>';
    }
    $count = count($tags);
    if ($count) {
        $code .=
            '<div class="sb-select"><p>' .
            sb_("All tags") .
            "</p><ul" .
            ($count > 8 ? ' class="sb-scroll-area"' : "") .
            '><li data-value="">' .
            sb_("All tags") .
            "</li>";
        for ($i = 0; $i < $count; $i++) {
            $code .=
                '<li data-value="' .
                $tags[$i]["tag-name"] .
                '">' .
                $tags[$i]["tag-name"] .
                "</li>";
        }
        $code .= "</ul></div>";
    } else {
        $code .= '<div class="sb-select sb-hide"></div>';
    }
    echo $code .= "</div></div>";
}
function sb_docs_link($id = "", $class = "sb-docs")
{
    if (!sb_is_cloud() || defined("SB_CLOUD_DOCS")) {
        echo '<a href="' .
            (sb_is_cloud() ? SB_CLOUD_DOCS : "https://board.support/docs") .
            $id .
            '" class="' .
            $class .
            '" target="_blank"><i class="sb-icon-help"></i></a>';
    }
}
function sb_get_ticket_custom_fields()
{
    $query = "SELECT * FROM custom_fields ORDER BY `order_no`";
    return sb_db_get($query, false);
}
function sb_get_ticket_statuses()
{
    $query = "SELECT * FROM ticket_status ORDER BY `name`";
    return sb_db_get($query, false);
}
function ticket_custom_field_settings($id = "", $class = "sb-docs")
{
    // Get all custom fields
    $customFields = sb_get_ticket_custom_fields();
    $code = '<div id="tickets-custom-fields" data-type="multi-input" class="sb-setting sb-type-multi-input">
                <div class="sb-setting-content"><h2>Ticket Custom fields</h2><p>Choose which custom fields to include in the new ticket form.</p></div>
                <div class="input">
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <a class="sb-btn sb-icon sb-new-ticket-custom-field">
                                            <i class="sb-icon-sms"></i> Add New Field
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-fields-table">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Type</th>
                                                        <th>Required</th>
                                                        <th>Active</th>
                                                        <th>Order</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
    foreach ($customFields as $field) {
        $code .=
            '<tr data-id="custom_field_row_' .
            $field["id"] .
            '">
                                                        <td>' .
            $field["title"] .
            '</td>
                                                        <td>' .
            strtoupper($field["type"]) .
            '</td>
                                                        <td>' .
            ($field["required"] ? "Yes" : "No") .
            '</td>
                                                        <td>' .
            ($field["is_active"] ? "Yes" : "No") .
            '</td>
                                                        <td>' .
            $field["order_no"] .
            '</td>
                                                        <td>
                                                        <a class="sb-btn-icon sb-btn-red edit-custom-field" data-sb-tooltip="Edit custom field" data-id="' .
            $field["id"] .
            '">
                                                                <i class="sb-icon-edit"></i>
                                                            </a>

                                                            <a class="sb-btn-icon sb-btn-red delete-custom-field" data-sb-tooltip="Delete custom field" data-id="' .
            $field["id"] .
            '">
                                                                <i class="sb-icon-delete"></i>
                                                            </a>
                                                        </td>
                                                    </tr>';
    }
    $code .= '
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Field Modal -->
            <div class="sb-ticket-custom-fields-edit-box sb-lightbox">
                <div class="sb-info"></div>
                <div class="sb-top-bar">
                    <div>
                        <h2 style="margin-bottom: 0;">
                            Create Custom Field
                        </h2>   
                    </div>
                    <div>
                        <a class="sb-edit sb-btn sb-icon" data-button="toggle" id="save-custom-fields" data-hide="sb-profile-area" data-show="sb-edit-area">
                            <i class="sb-icon-sms"></i> Save Changes
                        </a>
                        <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                            <i class="sb-icon-close"></i>
                        </a>
                    </div>
                </div>

                <div class="sb-main sb-scroll-area">
                    <div class="sb-details">
                        <div class="sb-title">
                            <?php sb_e("Create Custom Field"); ?>
                        </div>
                        <div class="sb-edit-box sb-ticket-list" id="customFieldForm">
                            <div id="title" data-type="text" class="sb-input">
                                <span class="required-label">Title</span>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div id="type" data-type="select" class="sb-input sb-input-select">
                                <span class="required-label">Type</span>
                                <select class="form-control" name="type" required>
                                    <option value="text" selected>Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Select</option>
                                </select>
                            </div>

                            <div id="optionsContainer" data-type="textarea" class="sb-input" style="display: none;">
                                <span>Options</span>
                                <textarea class="form-control" name="options" rows="4" placeholder="Separate each option by a pipe `|`.  Example: Option1|Option2|Option3"></textarea>
                            </div>

                            <div id="required" data-type="checkbox" class="sb-input sb-input-checkbox">
                                <span>Required?</span>
                                    <input class="form-control" type="checkbox" name="required" value="">
                            </div>

                            <div id="add_to_frontend_form" data-type="checkbox" class="sb-input sb-input-checkbox">
                                <span>Add to frontend form?</span>
                                <input class="form-control" type="checkbox" name="add_to_frontend_form" value="">
                            </div>

                            <div id="default_value" data-type="text" class="sb-input">
                                <span>Default Value</span>
                                <input type="text" class="form-control" name="default_value">
                            </div>

                            <div id="order" data-type="number" class="sb-input">
                                <span class="required-label">Order</span>
                                <input type="number" class="form-control" name="order" value="0">
                            </div>

                            <div id="is_active" data-type="checkbox" class="sb-input">
                                <span>Active?</span>
                                    <input class="form-control" type="checkbox" name="is_active" value="1" checked>
                                    <input type="hidden" id="custom_field_id" name="field_id" value="">
                            </div>
                            <div id="customFieldsContainer">
                            </div>

                            <!--div class="sb-input">
                                <button type="button" id="save-custom-fields" class="btn btn-primary">Create</button>
                            </div-->
                        </div>
                    </div>
                </div>
            </div>
            <!--script>
            // Initialize custom fields data
            fetch("api/get-custom-fields.php")
                .then(response => response.json())
                .then(data => {
                    window.customFieldsData = data;
                    // Load and display custom fields
                    loadCustomFields();
                    //console.log(window.customFieldsData);
                })
                .catch(error => console.error("Error loading custom fields:", error));

            // Load custom fields data and display them
            async function loadCustomFields() {
                try {
                    const response = await fetch("api/get-custom-fields.php");
                    const fields = await response.json();
                    
                    const container = document.getElementById("customFieldsContainer");
                    container.innerHTML = ""; // Clear any existing fields

                    fields.forEach(field => {
                        const fieldHtml = getFieldHtml(field);
                        container.innerHTML += fieldHtml;
                    });
                } catch (error) {
                    console.error("Error loading custom fields:", error);
                }
            }

            // Function to generate HTML for a custom field
            function getFieldHtml(field) {
                console.log(field.required);
                let html = "<div class=\"form-group mb-3\">";
                html += `<label for="custom_${field.id}" class="${field.required == \"1\" ? "required-field" : ""}">${field.title}</label>`;

                switch(field.type) {
                    case "text":
                        html += `<input type="text" class="form-control" id="custom_${field.id}" 
                                        name="custom_fields[${field.id}]" 
                                        ${field.required == "1" ? "required" : ""} 
                                        placeholder="${field.title}">`;
                        break;
                    
                    case "textarea":
                        html += `<textarea class="form-control" id="custom_${field.id}" 
                                            name="custom_fields[${field.id}]" 
                                            rows="3" 
                                            ${field.required  == "1" ? "required" : ""} 
                                            placeholder="${field.title}"></textarea>`;
                        break;
                    
                    case "select":
                        // Split options by comma and trim whitespace
                        const options = (field.options || "").split("|").map(opt => opt.trim()).filter(opt => opt);
                        html += `<select class="form-control" id="custom_${field.id}" 
                                        name="custom_fields[${field.id}]" 
                                        ${field.required == "1" ? "required" : ""}>`;
                        html += "<option value="">Select " + field.title + "</option>";
                        options.forEach(option => {
                            html += `<option value="${option}">${option}</option>`;
                        });
                        html += "</select>";
                        break;
                    
                    case "checkbox":
                        html += `<div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="custom_${field.id}" 
                                            name="custom_fields[${field.id}]" 
                                            value="1" 
                                            ${field.required == "1" ? "required" : ""}>
                                    <label class="form-check-label" for="custom_${field.id}">${field.title}</label>
                                </div>`;
                        break;
                }
                html += "</div>";
                return html;
            }
        </script-->
            
            
        <!--script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script-->
        <!--script>
            function editField(id) {
                // Get field details
                fetch("api/get-custom-field.php?id=" + id)
                    .then(response => response.json())
                    .then(field => {
                        // Fill form with field data
                        document.getElementById("title").value = field.title;
                        document.getElementById("type").value = field.type;
                        document.getElementById("required").checked = field.required;
                        document.getElementById("default_value").value = field.default_value;
                        document.getElementById("order").value = field.order;
                        document.getElementById("is_active").checked = field.is_active === 1;
                        
                        // Add hidden field ID for editing
                        const fieldIdInput = document.getElementById("fieldId");
                        if (!fieldIdInput) {
                            const input = document.createElement("input");
                            input.type = "hidden";
                            input.id = "fieldId";
                            input.name = "id";
                            input.value = id;
                            document.getElementById("customFieldForm").appendChild(input);
                        } else {
                            fieldIdInput.value = id;
                        }
                        
                        // Handle options for select and checkbox fields
                        const optionsContainer = document.getElementById("optionsContainer");
                        if (field.type === "select" || field.type === "checkbox") {
                            optionsContainer.style.display = "block";
                            // Split options by comma and join with newlines
                            document.getElementById("options").value = field.options ? field.options.split(",").join("\n") : "";
                        } else {
                            optionsContainer.style.display = "none";
                        }
                    })
                    .catch(error => console.error("Error:", error));

                // Show the edit modal
                const modal = new bootstrap.Modal(document.getElementById("createFieldModal"));
                modal.show();
            }

            // Delete field
            function deleteField(id) {
                if (confirm("Are you sure you want to delete this field?")) {
                    fetch("api/delete-custom-field.php?id=" + id, { method: "DELETE" })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Redirect to custom fields listing page
                                window.location.href = "custom-fields.php";
                            } else {
                                alert(result.error);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("An error occurred while deleting the field");
                        });
                }
            }

            // Show/hide options input based on field type
            document.getElementById("type").addEventListener("change", function() {
                const optionsContainer = document.getElementById("optionsContainer");
                if (this.value === "select" || this.value === "checkbox") {
                    optionsContainer.style.display = "block";
                } else {
                    optionsContainer.style.display = "none";
                }
            });

            // Initialize form submission state
            let isSubmitting = false;
            const submitButton = document.getElementById("customFieldForm").querySelector("button[type=\"submit\"]");
            const originalText = submitButton.innerHTML;

            // Handle form submission
            document.getElementById("customFieldForm").addEventListener("submit", async (event) => {
                event.preventDefault();
                
                if (isSubmitting) {
                    alert("Please wait... The form is already being submitted.");
                    return;
                }

                isSubmitting = true;
                submitButton.disabled = true;
                submitButton.innerHTML = "<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> Saving...";

                try {
                    // Get form data
                    const formData = new FormData(event.target);
                    let formObject = [];
                    formObject["function"] = "ajax_calls";
                    formObject["calls[0][function]"] = "add-custom-field";
                    formData.forEach((value, key) => {
                        var innerObj = {};
                        innerObj["calls[0]"][key] = value;
                        formObject.push(innerObj)
                    });
                    

                

                    // Get the current field ID if editing
                    const fieldId = document.getElementById("fieldId");
                    if (fieldId) {
                    //  formObject.id = fieldId.value;
                    }

                    // Determine which endpoint to use based on presence of field ID
                    const endpoint = formObject.id ? "api/edit-custom-field.php" : "script/include/ajax.php";
                    
                    const response = await fetch(endpoint, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(formObject)
                    });

                    const result = await response.json();

                    console.log("oooooo",response);
                    if (response.success) {
                    console.log(response);
                    // alert(result.message);
                    // location.reload();
                    } else {
                        alert(result.error);
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                        isSubmitting = false;
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("An error occurred while saving the field");
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                    isSubmitting = false;
                }
            });

        
        </script-->
        ';
    return $code;
}

function ticket_statuses_settings($id = '', $class = 'sb-docs')
{
    // Get all custom fields
    $ticketStatuses = sb_get_ticket_statuses();
    $code = '<div id="tickets-statuses-fields" data-type="multi-input" class="sb-setting sb-type-multi-input">
                <div class="sb-setting-content">
                    <h2>Ticket Status</h2>
                    <p>Choose which custom fields to include in the new ticket form.</p>
                </div>
                <div class="input">
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <a class="sb-btn sb-icon sb-ticket-add-new-status">
                                            <i class="sb-icon-sms"></i> Add New Status
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped ticket-status-table">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Color</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
    foreach ($ticketStatuses as $status) {
        $code .=
            '<tr data-id="ticket_status_row_' .
            $status["id"] .
            '">
                                                        <td>' .
            $status["name"] .
            '</td>
                                                        <td class="sb-color-palette">
                                                            <span style="background-color:' .
            $status["color"] .
            '"></span>
                                                        </td>
                                                        <td>
                                                            <a class="sb-btn-icon sb-btn-red edit-ticket-status" data-sb-tooltip="Edit Ticket Status" data-id="' .
            $status["id"] .
            '">
                                                                <i class="sb-icon-edit"></i>
                                                            </a>';
        if ($status["id"] > 5) {
            $code .=
                '<a class="sb-btn-icon sb-btn-red delete-ticket-status" data-sb-tooltip="Delete Ticket Status" data-id="' .
                $status["id"] .
                '">
                                                                        <i class="sb-icon-delete"></i>
                                                                </a>';
        }
        $code .= '
                                                        </td>
                                                    </tr>';
    }
    $code .= '
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sb-ticket-statuses-edit-box sb-lightbox">
                <div class="sb-info"></div>
                <div class="sb-top-bar">
                    <div>
                        <h2 style="margin-bottom: 0;">
                            Create Custom Field
                        </h2>   
                    </div>
                    <div>
                        <a class="sb-edit sb-btn sb-icon" data-button="toggle" id="save-ticket-status" data-hide="sb-profile-area" data-show="sb-edit-area">
                            <i class="sb-icon-sms"></i> Save Changes
                        </a>
                        <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                            <i class="sb-icon-close"></i>
                        </a>
                    </div>
                </div>

                <div class="sb-main sb-scroll-area">
                    <div class="sb-details">
                        <div class="sb-title">
                            <?php sb_e("Add New Status"); ?>
                        </div>
                        <div class="sb-edit-box sb-ticket-list" id="ticketStatusesForm">
                            <div id="status_title" data-type="text" class="sb-input">
                                <span>Title</span>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div id="status_color" data-type="text" class="sb-input">
                                <span>Choose Color</span>
                                <input type="color" value="#000000" name="status_color" id="statuscolor" class="form-control form-control-color" style="height: auto;" required>
                                <input type="hidden" name="status_id" id="status_id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    return $code;
}
?>