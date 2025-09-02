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
                        <span class="required-label">
                            <?php sb_e("Last name"); ?>
                        </span>
                        <input type="text" required />
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
                            <select id="select-customer" style="width:100%;" required></select>
                        </div>

                        <div class="sb-input two-divs d-flex">
                            <div id="cust_name" data-type="text" class="sb-input">
                                <span class="required-label"><?php sb_e(
                                    "Name"
                                ); ?></span>
                                <input type="text" name="name" value="" disabled="">
                            </div>
                            <div id="cust_email" data-type="text" class="sb-input">
                                <span><?php sb_e(
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
                        <span class="text-danger files-error mt-2 d-block"></span>
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

            const maxFileSizeMB = 5; // Maximum size in MB per file
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

            //const maxFileSizeMB = 5; // Maximum size in MB per file
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

    <script>
        jQuery(document).ready(function ($) {
            const today = new Date().toISOString().split('T')[0];
            $("#birthdate input").attr("max", today);
        });
    </script>

    <script>
        function updateSlider($tab) {
            const $container = $tab.closest(".my-tabs");
            const $slider = $container.find(".tab-slider");

            const containerOffsetLeft = $container.offset().left;
            const tabOffsetLeft = $tab.offset().left;

            const left = tabOffsetLeft - containerOffsetLeft + $container.scrollLeft();
            const width = $tab.outerWidth();

            $slider.css({
                left: left,
                width: width
            });
        }


        function initTabSlider() {
            setTimeout(function () {
                $(".my-tabs").each(function () {
                    const $activeTab = $(this).find(".my-tab.active");
                    if ($activeTab.length) {
                        updateSlider($activeTab);
                    }
                });
            }, 100);
        }

        function checkArrowVisibility($tabsContainer) {
            const scrollWidth = $tabsContainer[0].scrollWidth;
            const clientWidth = $tabsContainer[0].clientWidth;
            const $leftArrow = $tabsContainer.siblings(".tab-arrow.left");
            const $rightArrow = $tabsContainer.siblings(".tab-arrow.right");

            if (scrollWidth <= clientWidth) {
                $leftArrow.hide();
                $rightArrow.hide();
            } else {
                $leftArrow.show();
                $rightArrow.show();
            }
        }


        $(document).ready(function () {
            initTabSlider();
            const $tabs = $(".my-tabs");

            $(".my-tab").on("click", function () {
                const $container = $(this).closest(".my-tabs");
                $container.find(".my-tab").removeClass("active");
                $(this).addClass("active");
                updateSlider($(this));
            });

            $(".tab-arrow.left").on("click", function () {
                const $tabs = $(this).siblings(".my-tabs");
                $tabs.animate({ scrollLeft: 0 }, 300, () => {
                    setTimeout(() => {
                        checkArrowVisibility($tabs);
                    }, 300);
                });
            });

            $(".tab-arrow.right").on("click", function () {
                const $tabs = $(this).siblings(".my-tabs");
                const scrollWidth = $tabs[0].scrollWidth;
                const clientWidth = $tabs[0].clientWidth;
                const maxScroll = scrollWidth - clientWidth;

                $tabs.animate({ scrollLeft: maxScroll }, 300, () => {
                    setTimeout(() => {
                        checkArrowVisibility($tabs);
                    }, 300);
                });
            });


            setTimeout(() => {
                $tabs.each(function () {
                    checkArrowVisibility($(this));
                });
            }, 0);

            // Also update on window resize
            $(window).on("resize", function () {
                $tabs.each(function () {
                    checkArrowVisibility($(this));
                });
            });

            const targetNode = document.querySelector(".setting_sidebar");
            if (targetNode) {
                const observer = new MutationObserver((mutationsList) => {
                    for (const mutation of mutationsList) {
                        if (mutation.type === "attributes" && mutation.attributeName === "class") {
                            if (mutation.target.classList.contains("sb-active")) {
                                initTabSlider();
                                $tabs.each(function () {
                                    checkArrowVisibility($(this));
                                });
                            }
                        }
                    }
                });

                document.querySelectorAll(".setting_sidebar li").forEach((li) => {
                    observer.observe(li, {
                        attributes: true,
                        attributeFilter: ["class"]
                    });
                });
            }
        });

        window.initTabSlider = initTabSlider;


        $(".sidebar li a").on("click", function () {
            setTimeout(() => {
                initTabSlider();
            }, 100);
        });
    </script>

    <script>
        jQuery(document).ready(function ($) {
            $(".toggle-btn").click(function () {
                $(".sidebar.sb-admin-nav").toggleClass("side-open");
            })
            $(".sidebar nav li a").click(function () {
                $(".sidebar.sb-admin-nav").removeClass("side-open")
            })
        })
    </script>

    <script>
        $(".my-tab").on("click", function () {
            const $container = $(this).closest(".my-tabs");
            $container.find(".my-tab").removeClass("active");
            $(this).addClass("active");
            updateSlider($(this));

            // ✅ Smooth scroll to make clicked tab visible
            $container.animate({
                scrollLeft: $(this).position().left + $container.scrollLeft() - 20
            }, 300);
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

    <div id="add-conversation-to-ticket" class="sb-lightbox" data-type="convert-ticket">
        <div id="ticket-action-selector" class="popup-wrapper">
            <p><?php sb_e("Add Conversation to New or Existing Ticket"); ?></p>
            <div>
                <a class="sb-confirm sb-btn">
                    Link to new ticket
                </a>
                <a id="link-to-existing-ticket" class="sb-btn">
                    Link to existing ticket
                </a>
                <a class="sb-close sb-btn">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div id="ticket-selector" class="popup-wrapper" style="display:none">
            <p><?php sb_e("Add Conversation to New or Existing Ticket"); ?></p>
            <div>
                <div data-type="select" class="sb-input">
                    <select id="selected_ticket_id" style="width:100%;"></select>
                    <input type="hidden" id="selected_conversation_id">
                    <button id="link-ticket" class="sb-btn">Submit</button>
                </div>
                <a class="sb-close sb-btn">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
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
            <!-- <div class="sb-title">
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
            </div> -->

            <div class="mt-3 bulk-users-container">
                <label class="form-label select-user">Select Users (Max 10)</label>
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">Select All</label>
                </div>

                <div id="selectedCount" class="text-muted small mb-2">0 / 10 selected</div>

                <div class="border rounded p-2 bulk-users-wrapper" style="max-height: 200px; overflow-y: auto;">
                    <!-- Example Users -->
                </div>
                <input type="hidden" class="sb-setting sb-direct-message-users" id="selectedUserIds" name="selectedUserIds"
                    value="">
            </div>

            <div class="mt-3 sb-title sb-direct-message-subject">
                <label class="form-label"><?php sb_e("Subject"); ?></label>
                <div class="sb-setting sb-type-text sb-direct-message-subject">
                    <input type="text" placeholder="<?php sb_e(
                        "Email subject"
                    ); ?>" />
                </div>
            </div>
            <div class="mt-3 sb-setting sb-type-textarea">
                <label class="form-label">Message</label>
                <div class="sb-setting sb-type-textarea">
                    <textarea class="form-control" rows="3" placeholder="<?php sb_e("Write here your message..."); ?>"
                        required></textarea>
                </div>
            </div>
            <div class="mt-3 text-end">
                <a class="sb-send-direct-message sb-btn sb-icon">
                    <i class="sb-icon-plane"></i>
                    Send email now
                </a>
                <div>
                    <?php sb_docs_link("#direct-messages", "sb-btn-text"); ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            const maxSelection = 10;

            function updateSelected() {
                const checked = $('.user-checkbox:checked');
                const count = checked.length;

                $('#selectedCount').text(`${count} / ${maxSelection} selected`);

                // Update hidden field with selected IDs
                const ids = checked.map(function () {
                    return $(this).val();
                }).get().join(',');

                $('#selectedUserIds').val(ids);

                // Update select all checkbox state
                // $('#selectAll').prop('checked', $('.user-checkbox').length === count);
            }

            $('.bulk-users-wrapper').on('change', '.user-checkbox', function () {
                const checkedCount = $('.user-checkbox:checked').length;

                if (checkedCount > maxSelection) {
                    this.checked = false;
                    alert(`You can select up to ${maxSelection} users.`);
                    return;
                }

                updateSelected();

                if (checkedCount)
                    $('.bulk-users-wrapper').removeClass('sb-error');
                else
                    $('.bulk-users-wrapper').addClass('sb-error');
            });

            $('#selectAll').on('change', function () {
                if (this.checked) {
                    let checkedCount = $('.user-checkbox:checked').length;

                    $('.user-checkbox').each(function () {
                        if (!$(this).prop('checked') && checkedCount < maxSelection) {
                            $(this).prop('checked', true);
                            checkedCount++;
                        }
                    });

                    if (checkedCount)
                        $('.bulk-users-wrapper').removeClass('sb-error');
                    else
                        $('.bulk-users-wrapper').addClass('sb-error');

                } else {
                    // Allow unchecking all regardless of count
                    $('.user-checkbox').prop('checked', false);
                }

                updateSelected();
            });

            updateSelected(); // Initialize count on load
        });
    </script>
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
                        <button class="toggle-btn">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="d-md-none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4 5C3.44772 5 3 5.44772 3 6C3 6.55228 3.44772 7 4 7H20C20.5523 7 21 6.55228 21 6C21 5.44772 20.5523 5 20 5H4ZM7 12C7 11.4477 7.44772 11 8 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H8C7.44772 13 7 12.5523 7 12ZM13 18C13 17.4477 13.4477 17 14 17H20C20.5523 17 21 17.4477 21 18C21 18.5523 20.5523 19 20 19H14C13.4477 19 13 18.5523 13 18Z"
                                    fill="#000000"></path>
                            </svg>
                        </button>
                        <a id="sb-dashboard">
                            <img width="35"
                                src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>"
                                alt="Logo" class="logo-icon d-md-flex">
                        </a>
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
                                                <span class="icon-tooltip" data-tooltip="Customers & Agents">
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
                                        </i><span class="label">Customers & Agents</span></a></li>
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
                            <?php
                            if ($is_admin && $is_cloud) {
                                ?>
                                <li>
                                    <a id="sb-accout" href="<?php echo dirname(SB_URL); ?>/account/?tab=installation">
                                        <i>
                                            <div class="icon-wrapper">
                                                <span class="icon-tooltip" data-tooltip="Inbox">
                                                    <svg width="25" height="25" viewBox="0 0 26 27" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M16.5984 9.73794C16.5984 7.74972 14.9867 6.13794 12.9984 6.13794C11.0102 6.13794 9.39844 7.74972 9.39844 9.73794C9.39844 11.7262 11.0102 13.3379 12.9984 13.3379C14.9867 13.3379 16.5984 11.7262 16.5984 9.73794Z"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M25 13.3379C25 6.71047 19.6274 1.33789 13 1.33789C6.37258 1.33789 1 6.71047 1 13.3379C1 19.9653 6.37258 25.3379 13 25.3379C19.6274 25.3379 25 19.9653 25 13.3379Z"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M19 19.3379C19 16.0242 16.3137 13.3379 13 13.3379C9.6863 13.3379 7 16.0242 7 19.3379"
                                                            stroke="#5F6465" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </i>
                                        <span class="label">Account</span>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
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
                            <div class="header-left" style="gap: 2px;">
                            <button class="toggle-btn" type="button">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4 5C3.44772 5 3 5.44772 3 6C3 6.55228 3.44772 7 4 7H20C20.5523 7 21 6.55228 21 6C21 5.44772 20.5523 5 20 5H4ZM7 12C7 11.4477 7.44772 11 8 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H8C7.44772 13 7 12.5523 7 12ZM13 18C13 17.4477 13.4477 17 14 17H20C20.5523 17 21 17.4477 21 18C21 18.5523 20.5523 19 20 19H14C13.4477 19 13 18.5523 13 18Z" fill="#000000"/>
                                </svg>
                            </button>
                                <img width="35" src="' . SB_URL . '/media/nexleon-favicon-n.png" alt="Logo" class="logo-icon d-md-none">
                                <a class="sb-btn sb-icon ticket-back-btn sb_btn_new m-0 me-3 d-none" href="' . $ticketUrl . '" >
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
                                <div data-value="logout" class="logout" title="Logout">
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
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #F3EEFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #8252E9;">
                                                            <!--i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i-->
                                                            <img src="./script/media/total-conversations.svg"
                                                                alt="Total Tickets">
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
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EFF4FF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #487fff;">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M21.4859 10.5015C21.8494 10.4788 22.1767 10.7207 22.2617 11.0749C22.3659 11.5084 22.4344 11.9557 22.4643 12.4134C22.512 13.145 22.512 13.9016 22.4643 14.6333C22.3682 16.1066 21.7212 17.4294 21.0096 18.4949C20.8323 18.8418 20.9143 19.3954 21.3068 20.1324L21.3272 20.1705C21.4584 20.4167 21.5926 20.6686 21.6709 20.8857C21.7545 21.1176 21.8516 21.5142 21.6178 21.909C21.4051 22.2683 21.0669 22.3957 20.8084 22.4453C20.5968 22.4858 20.3401 22.4919 20.1105 22.4973L20.0686 22.4983C18.8373 22.528 17.9638 22.1749 17.2713 21.669C17.165 21.5914 17.0866 21.5343 17.0256 21.4913C16.9331 21.5265 16.811 21.5762 16.641 21.6455C16.1692 21.838 15.635 21.9528 15.1444 21.9851C13.901 22.067 12.6017 22.0672 11.3558 21.9851C9.85327 21.8862 8.46648 21.3651 7.31325 20.5388C7.02544 20.3326 6.92104 19.9532 7.06284 19.6287C7.20464 19.3043 7.55401 19.1233 7.90084 19.1944C9.79994 19.5842 12.8355 19.6543 15.4813 18.5777C18.0693 17.5247 20.3172 15.3699 20.7871 11.1667C20.8276 10.8047 21.1223 10.5241 21.4859 10.5015Z"
                                                                    fill="white" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M12.1443 1.06147C10.8984 0.979424 9.59906 0.979597 8.35571 1.06147C4.42185 1.32054 1.29316 4.46804 1.03579 8.41335C0.98807 9.14497 0.98807 9.90163 1.03579 10.6333C1.13191 12.1066 1.77884 13.4294 2.49047 14.4949C2.66772 14.8418 2.58576 15.3954 2.19322 16.1324L2.17291 16.1705C2.04166 16.4167 1.90744 16.6685 1.82916 16.8857C1.74552 17.1176 1.64847 17.5142 1.88225 17.909C1.96127 18.0425 2.07112 18.1704 2.22404 18.2719C2.37081 18.3692 2.52248 18.4189 2.65415 18.447C2.88549 18.4964 3.16753 18.4984 3.44071 18.4985C4.6671 18.5262 5.53802 18.1736 6.22877 17.669C6.33506 17.5913 6.41347 17.5342 6.47449 17.4913C6.56698 17.5265 6.68915 17.5762 6.85908 17.6455C7.33095 17.838 7.86507 17.9528 8.35571 17.9851C9.59905 18.067 10.8984 18.0672 12.1443 17.9851C16.0781 17.7261 19.2069 14.5785 19.4642 10.6332C19.5119 9.90161 19.5119 9.14493 19.4642 8.41334C19.2068 4.46803 16.0781 1.32054 12.1443 1.06147ZM6.75 7C6.33579 7 6 7.33579 6 7.75C6 8.16421 6.33579 8.5 6.75 8.5H10.75C11.1642 8.5 11.5 8.16421 11.5 7.75C11.5 7.33579 11.1642 7 10.75 7H6.75ZM6.75 12.5H13.75C14.1642 12.5 14.5 12.1642 14.5 11.75C14.5 11.3358 14.1642 11 13.75 11H6.75C6.33579 11 6 11.3358 6 11.75C6 12.1642 6.33579 12.5 6.75 12.5Z"
                                                                    fill="white" />
                                                                <circle cx="19.5001" cy="20.8553" r="3.14465"
                                                                    fill="rgb(255 132 132)" />
                                                            </svg>

                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Unread Conversation</h3>
                                                            <p class="total-unread-conversations"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="unread_conversation_chart">
                                                            <canvas class="mt-0" id="unread_conversation_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-unread-increase"></span><span>%</span>
                                                    </span>&nbsp;in last 7 days
                                                </div>
                                            </div>
                                            <!--  -->
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF5E9 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #f4941e;">
                                                            <!--i class="fa-solid fa-ticket" style="color: #ffffff;"></i-->
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
                                                            <h3>Total Tickets</h3>
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
                                            <!--  -->
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF2FE 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #DE3ACE;">
                                                            <!--i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i-->
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
                                                            <p class="tickets-pending"></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="ticket_pending_chart">
                                                            <canvas class="mt-0" id="ticket_pending_chart"></canvas>
                                                        </div>
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">
                                                    Increase by
                                                    <span class="increase-pill">
                                                        <span class="total-pending-tickets-increase"></span><span>%</span>
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
                                                        <h6 class="head mb-1">Monthly Ticket Activity</h6>
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
                                    <div class="px-3 ps-xl-2 mt-3 mt-xl-0 clmn-gap">
                                        <section class="main-charts mb-3">
                                            <div class="card p-3 tickets_activity_card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="head mb-1">Weekly Ticket Activity</h6>
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
                                                        <a id="view-all-conversations" class="mr-2" href="">View
                                                            All</a>
                                                    </p>
                                                </div>
                                                <div class="seprator"></div>
                                                <div class="recent card p-3">
                                                    <ul class="recent-messages list-unstyled" style="min-height:254px;">
                                                        No message found
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
                                    <!-- <p data-value="0" data-sb-tooltip="Unread Conversations"> -->
                                    <p data-value="0">

                                        <?php sb_e("Inbox"); ?><span data-sb-tooltip="Unread Conversations"
                                            style="background: #fff;height: 20px;width:20px;display:flex;justify-content: center;align-items:center;border-radius:50%;padding:0;margin-left:4px;"></span>
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
                                <ul id="inbox-list"></ul>
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
                                                data-sb-tooltip="Link to a ticket">
                                                <svg enable-background="new 0 0 50 50" height="50" class="d-xl-none"
                                                    viewBox="0 0 50 50" width="50" xmlns="http://www.w3.org/2000/svg">
                                                    <g id="Layer_29" />
                                                    <g id="Layer_28" />
                                                    <g id="Layer_27" />
                                                    <g id="Layer_26" />
                                                    <g id="Layer_25" />
                                                    <g id="Layer_24" />
                                                    <g id="Layer_23" />
                                                    <g id="Layer_22" />
                                                    <g id="Layer_21" />
                                                    <g id="Layer_20" />
                                                    <g id="Layer_19" />
                                                    <g id="Layer_18" />
                                                    <g id="Layer_17" />
                                                    <g id="Layer_16" />
                                                    <g id="Layer_15" />
                                                    <g id="Layer_14" />
                                                    <g id="Layer_13" />
                                                    <g id="Layer_12" />
                                                    <g id="Layer_11" />
                                                    <g id="Layer_10" />
                                                    <g id="Layer_9" />
                                                    <g id="Layer_8">
                                                        <path clip-rule="evenodd"
                                                            d="m29.164 17.501-11.664 11.664c-.344.344-.903.344-1.248 0-.346-.344-.345-.904 0-1.249l11.664-11.663c.344-.345.904-.345 1.249 0 .344.344.343.903-.001 1.248m.596 6.073c.344-.345.904-.345 1.249 0s.344.904 0 1.248l-6.187 6.187c-.344.344-.904.344-1.249 0s-.345-.904 0-1.249zm9.132-5.569-6.896-6.9c-1.553-1.553-3.739-1.418-5.16.002l-15.729 15.73c-1.42 1.42-1.417 3.742 0 5.159l6.897 6.896c1.419 1.419 3.738 1.42 5.158 0l2.886-2.886c.891-5.073 4.884-9.068 9.958-9.957l2.886-2.885c2.244-2.244.503-4.656 0-5.159zm-3.641 19.249h7.897c.488 0 .883.395.883.883s-.395.883-.883.883h-7.893l1.665 1.665c.345.345.345.904 0 1.249-.345.344-.904.344-1.249 0l-3.129-3.13c-.105-.091-.187-.206-.243-.344l-.005-.013-.047-.176-.001-.01-.002-.012-.001-.009-.002-.013-.001-.008-.002-.021-.001-.021-.001-.021v-.021-.021l.001-.022.001-.022.002-.023.002-.022.003-.023.004-.022.004-.021.005-.022.003-.013.002-.008.003-.012.003-.009.003-.012.003-.009c.041-.133.113-.258.219-.364l3.175-3.176c.345-.344.904-.344 1.249 0 .345.345.345.904 0 1.249zm13.655.881c0-5.946-4.826-10.771-10.772-10.771s-10.771 4.825-10.771 10.771c0 5.945 4.826 10.771 10.771 10.771 5.948 0 10.772-4.823 10.772-10.771zm-11.551-37.58c-.676-.676-1.74-.741-2.496-.158-2.131 1.647-5.179 1.648-7.31.001-.757-.586-1.816-.515-2.496.157l-24.496 24.501c-.676.677-.744 1.737-.16 2.494 1.647 2.131 1.646 5.179-.001 7.31-.584.755-.517 1.821.158 2.496l12.09 12.089c.676.677 1.739.74 2.496.158 2.136-1.65 5.175-1.65 7.31 0 .755.583 1.82.517 2.496-.158l3.625-3.625c-1.637-2.035-2.58-4.53-2.694-7.143l-1.465 1.465c-2.109 2.11-5.547 2.109-7.656 0l-6.897-6.897c-2.132-2.136-2.12-5.536 0-7.656l15.73-15.731c3.349-3.349 6.558-1.097 7.655 0l6.896 6.9c2.113 2.114 2.109 5.546.002 7.654l-1.464 1.464c2.611.114 5.111 1.058 7.142 2.695l3.625-3.628c.673-.677.742-1.737.158-2.493-1.647-2.133-1.648-5.177.001-7.31.586-.758.514-1.817-.158-2.496z"
                                                            fill-rule="evenodd" fill="#5e5e5e" />
                                                    </g>
                                                    <g id="Layer_7" />
                                                    <g id="Layer_6" />
                                                    <g id="Layer_5" />
                                                    <g id="Layer_4" />
                                                    <g id="Layer_3" />
                                                    <g id="Layer_2" />
                                                </svg>
                                                <span class="d-none d-xl-block">Link to ticket</span>
                                            </a>
                                        </li>
                                        <li id="view-profile-list" class="">
                                            <a id="view-profile-button" data-value="view-profile" class="sb-btn sb-icon"
                                                data-sb-tooltip="View Profile">
                                                <svg id="Layer" height="512" class="d-xl-none" viewBox="0 0 24 24" width="512"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g id="user-eye">
                                                        <path
                                                            d="m6.009 6.5a4 4 0 1 1 4 4 4 4 0 0 1 -4-4zm5.901 13.5a2.952 2.952 0 0 1 0-3.01 7.37 7.37 0 0 1 3.329-3.035.3.3 0 0 0 .066-.533 5.69 5.69 0 0 0 -3.305-.922h-4c-4.06 0-5.5 2.97-5.5 5.52 0 2.28 1.21 3.48 3.5 3.48h6.39a.3.3 0 0 0 .3-.3.374.374 0 0 0 -.1-.22 8.412 8.412 0 0 1 -.68-.98zm9.89-.76a5.17 5.17 0 0 1 -4.3 2.76 5.17 5.17 0 0 1 -4.3-2.76 1.453 1.453 0 0 1 0-1.48 5.17 5.17 0 0 1 4.3-2.76 5.17 5.17 0 0 1 4.3 2.76 1.453 1.453 0 0 1 0 1.48zm-3.05-.74a1.25 1.25 0 0 0 -1.25-1.25h-.01a1.25 1.25 0 1 0 1.26 1.25z"
                                                            fill="#5e5e5e" />
                                                    </g>
                                                </svg>
                                                <span class="d-none d-xl-block">View Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="archive" class="sb-btn-icon"
                                                style="display: flex; align-items: center; justify-content: center;"
                                                data-sb-tooltip="<?php sb_e(
                                                    "Archive conversation"
                                                ); ?>">
                                                <svg width="29" height="28" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.5303 17.5303C12.3897 17.671 12.1989 17.75 12 17.75C11.8011 17.75 11.6103 17.671 11.4697 17.5303L8.96967 15.0303C8.67678 14.7374 8.67678 14.2626 8.96967 13.9697C9.26256 13.6768 9.73744 13.6768 10.0303 13.9697L11.25 15.1893V11C11.25 10.5858 11.5858 10.25 12 10.25C12.4142 10.25 12.75 10.5858 12.75 11V15.1893L13.9697 13.9697C14.2626 13.6768 14.7374 13.6768 15.0303 13.9697C15.3232 14.2626 15.3232 14.7374 15.0303 15.0303L12.5303 17.5303Z"
                                                        fill="#5e5e5e" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12.0574 1.25H11.9426C9.63423 1.24999 7.82519 1.24998 6.41371 1.43975C4.96897 1.63399 3.82895 2.03933 2.93414 2.93414C2.03933 3.82895 1.63399 4.96897 1.43975 6.41371C1.24998 7.82519 1.24999 9.63422 1.25 11.9426V12H1.26092C1.25 12.5788 1.25 13.2299 1.25 13.9664V14.0336C1.25 15.4053 1.24999 16.4807 1.32061 17.3451C1.39252 18.2252 1.54138 18.9523 1.87671 19.6104C2.42799 20.6924 3.30762 21.572 4.38956 22.1233C5.04769 22.4586 5.7748 22.6075 6.65494 22.6794C7.51927 22.75 8.59469 22.75 9.96637 22.75H14.0336C15.4053 22.75 16.4807 22.75 17.3451 22.6794C18.2252 22.6075 18.9523 22.4586 19.6104 22.1233C20.6924 21.572 21.572 20.6924 22.1233 19.6104C22.4586 18.9523 22.6075 18.2252 22.6794 17.3451C22.75 16.4807 22.75 15.4053 22.75 14.0336V13.9664C22.75 13.2302 22.75 12.5787 22.7391 12H22.75V11.9426C22.75 9.63423 22.75 7.82519 22.5603 6.41371C22.366 4.96897 21.9607 3.82895 21.0659 2.93414C20.1711 2.03933 19.031 1.63399 17.5863 1.43975C16.1748 1.24998 14.3658 1.24999 12.0574 1.25ZM4.38956 5.87671C3.82626 6.16372 3.31781 6.53974 2.88197 6.98698C2.89537 6.85884 2.91012 6.73444 2.92637 6.61358C3.09825 5.33517 3.42514 4.56445 3.9948 3.9948C4.56445 3.42514 5.33517 3.09825 6.61358 2.92637C7.91356 2.75159 9.62177 2.75 12 2.75C14.3782 2.75 16.0864 2.75159 17.3864 2.92637C18.6648 3.09825 19.4355 3.42514 20.0052 3.9948C20.5749 4.56445 20.9018 5.33517 21.0736 6.61358C21.0899 6.73445 21.1046 6.85884 21.118 6.98698C20.6822 6.53975 20.1737 6.16372 19.6104 5.87671C18.9523 5.54138 18.2252 5.39252 17.3451 5.32061C16.4807 5.24999 15.4053 5.25 14.0336 5.25H9.96645C8.59472 5.25 7.51929 5.24999 6.65494 5.32061C5.7748 5.39252 5.04769 5.54138 4.38956 5.87671ZM5.07054 7.21322C5.48197 7.00359 5.9897 6.87996 6.77708 6.81563C7.57322 6.75058 8.58749 6.75 10 6.75H14C15.4125 6.75 16.4268 6.75058 17.2229 6.81563C18.0103 6.87996 18.518 7.00359 18.9295 7.21322C19.7291 7.62068 20.3793 8.27085 20.7868 9.07054C20.9964 9.48197 21.12 9.9897 21.1844 10.7771C21.2494 11.5732 21.25 12.5875 21.25 14C21.25 15.4125 21.2494 16.4268 21.1844 17.2229C21.12 18.0103 20.9964 18.518 20.7868 18.9295C20.3793 19.7291 19.7291 20.3793 18.9295 20.7868C18.518 20.9964 18.0103 21.12 17.2229 21.1844C16.4268 21.2494 15.4125 21.25 14 21.25H10C8.58749 21.25 7.57322 21.2494 6.77708 21.1844C5.9897 21.12 5.48197 20.9964 5.07054 20.7868C4.27085 20.3793 3.62068 19.7291 3.21322 18.9295C3.00359 18.518 2.87996 18.0103 2.81563 17.2229C2.75058 16.4268 2.75 15.4125 2.75 14C2.75 12.5875 2.75058 11.5732 2.81563 10.7771C2.87996 9.9897 3.00359 9.48197 3.21322 9.07054C3.62068 8.27085 4.27085 7.62069 5.07054 7.21322Z"
                                                        fill="#5e5e5e" />
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
                                            echo '<li>
                                                        <a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_("Delete conversation") . '">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M14.74 9.00003L14.394 18M9.606 18L9.26 9.00003M19.228 5.79003C19.57 5.84203 19.91 5.89703 20.25 5.95603M19.228 5.79003L18.16 19.673C18.1164 20.2383 17.8611 20.7662 17.445 21.1513C17.029 21.5364 16.4829 21.7502 15.916 21.75H8.084C7.5171 21.7502 6.97102 21.5364 6.55498 21.1513C6.13894 20.7662 5.88359 20.2383 5.84 19.673L4.772 5.79003M19.228 5.79003C18.0739 5.61555 16.9138 5.48313 15.75 5.39303M4.772 5.79003C4.43 5.84103 4.09 5.89603 3.75 5.95503M4.772 5.79003C5.92613 5.61555 7.08623 5.48313 8.25 5.39303M15.75 5.39303V4.47703C15.75 3.29703 14.84 2.31303 13.66 2.27603C12.5536 2.24067 11.4464 2.24067 10.34 2.27603C9.16 2.31303 8.25 3.29803 8.25 4.47703V5.39303M15.75 5.39303C13.2537 5.20011 10.7463 5.20011 8.25 5.39303" stroke="#5E5E5E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a data-value="empty-trash" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_("Empty trash") . '">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M14.74 9.00003L14.394 18M9.606 18L9.26 9.00003M19.228 5.79003C19.57 5.84203 19.91 5.89703 20.25 5.95603M19.228 5.79003L18.16 19.673C18.1164 20.2383 17.8611 20.7662 17.445 21.1513C17.029 21.5364 16.4829 21.7502 15.916 21.75H8.084C7.5171 21.7502 6.97102 21.5364 6.55498 21.1513C6.13894 20.7662 5.88359 20.2383 5.84 19.673L4.772 5.79003M19.228 5.79003C18.0739 5.61555 16.9138 5.48313 15.75 5.39303M4.772 5.79003C4.43 5.84103 4.09 5.89603 3.75 5.95503M4.772 5.79003C5.92613 5.61555 7.08623 5.48313 8.25 5.39303M15.75 5.39303V4.47703C15.75 3.29703 14.84 2.31303 13.66 2.27603C12.5536 2.24067 11.4464 2.24067 10.34 2.27603C9.16 2.31303 8.25 3.29803 8.25 4.47703V5.39303M15.75 5.39303C13.2537 5.20011 10.7463 5.20011 8.25 5.39303" stroke="#5E5E5E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </a>
                                                    </li>';
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
                                    <i class="hgi hgi-stroke hgi-user-add-02"></i>
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
                                            <?php sb_e("All Customers"); ?>
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
                                    <ul style="background: #fff;">
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
                        <div class="sb-scroll-area tickts-scroll-area">
                            <table id="customerTable"
                                class="sb-table-tickets table table-bordered table-hover align-middle text-nowrap bg-white w-100 sb-table-users">
                                <thead class="table-light">
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
                                            <?php sb_e("Registration date/time"); ?>
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
                        <div class="px-3">
                            <!-- Table -->
                            <div class="table-responsive" style="overflow: visible;">
                                <div class="sb-scroll-area scroll-table">
                                    <table id="ticketTable"
                                        class="sb-table-tickets table table-bordered table-hover align-middle text-nowrap bg-white w-100">
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
                    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />

                    <!-- Scripts -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

                    <script src="https://cdn.jsdelivr.net/npm/@iconify-json/hugeicons@1.2.6/index.min.js"></script>

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
                    <div class="tc_bg" style="max-height: calc(100vh - 18px);overflow-y: auto;">
                        <div class="tc_back">
                            <div class="container">
                                <div class="row tablet-sizee">
                                    <div class="col-md-12 p-0">
                                        <div class="row">
                                            <div class="col-md-8 p-0 col-lg-9">
                                                <h2 class="title mb-0 d-flex align-items-center gap-1"># <span
                                                        class="tno">TR-51</span> /
                                                    <span class="tsubject d-flex align-items-center w-100">
                                                        <svg class="w-6 h-6 text-gray-800 dark:text-white px-1"
                                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                                        </svg>
                                                        <input type="text" id="ticket-subject"
                                                            placeholder="Enter Ticket Subject" value=""
                                                            style="border: 1px solid #d5d5d5; border-radius: 0; padding-inline: 10px; width: 100%;" />

                                                    </span>
                                                </h2>
                                            </div>
                                            <div class="col-md-4 p-0 col-lg-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-end gap-3 pl-5 mt-3 mt-md-0">
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
                                                <h2 class="sub_title mt-4 mb-2">Description</h2>
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
                                                <ul class="nav nav-tabs mt-4" id="myTab">
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
                                                <div class="ps-md-5">
                                                    <div class="sidepanel">
                                                        <h4 class="sub_title mb-3 col-4 d-inline-block">Details</h4>
                                                        <span class="conversation-id d-none">Linked Conversation ID :
                                                            <span></span></span>
                                                        <div class="ticket-fields">
                                                            <!-- <div class="mb-3 without-contact">
                                                                <div class="field-label">Guest Ticket</div>
                                                                <div class="d-flex align-items-center gap-2"></div>
                                                                <div class="form-check form-switch mb-0 ml-2">
                                                                    <input class="form-check-input" name="without_contact"
                                                                        type="checkbox" role="switch"
                                                                        id="flexSwitchCheckDefault" style="width: 27px;">
                                                                </div>
                                                            </div> -->
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
                                                                <div class="field-label required-label">Customer</div>
                                                                <div class="d-flex align-items-center gap-2 ticket-reporter">
                                                                    <img class="reporter-img" src="" alt="Customer"
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
                                                        <h5 class="field-label">More Fields
                                                            <!-- <i class="fas fa-chevron-down"></i> -->
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
                        <div class="sb-tab sb-inner-tab px-3">
                            <div class="sb-nav sb-nav-only sb-scroll-area">
                                <ul class="ul-articles"></ul>
                                <div class="sb-add-article sb-btn sb-icon">
                                    <i class="sb-icon-plus"></i>
                                    <?php sb_e("Add new article"); ?>
                                </div>
                                <ul class="ul-categories"></ul>
                                <div class="sb-add-category sb-btn sb-icon">
                                    <i class="sb-icon-plus"></i>
                                    <?php sb_e("Add new category"); ?>
                                </div>
                            </div>

                            <!-- <div class="articleHEad">
                                        <div class="">
                                            <p class="head mb-4">Articles Settings</p>
                                            <p class="des mb-0">Manage preferences and options for your articles.</p>
                                        </div>
                                    </div> -->
                            <div class="sb-content sb-content-articles sb-scroll-area sb-loading bg-white mt-2">
                                <!-- <div class="content_article"> -->
                                <!-- <div> -->
                                <h2 id="sb-article-id"></h2>
                                <div class="input_container">
                                    <h2 class="sb-language-switcher-cnt">
                                        <?php sb_e("Title"); ?>
                                    </h2>
                                    <div class="sb-setting sb-type-text sb-article-title">
                                        <input type="text" />
                                    </div>
                                </div>
                                <div class="input_container">
                                    <h2>
                                        <?php sb_e("External link"); ?>
                                    </h2>
                                    <div class="sb-setting sb-type-text sb-article-link">
                                        <input type="text" />
                                    </div>
                                </div>
                                <div class="input_container">
                                    <h2>
                                        <?php sb_e(
                                            "Parent category"
                                        ); ?>
                                    </h2>
                                    <div class="sb-setting sb-type-select">
                                        <select id="article-parent-categories"></select>
                                    </div>
                                </div>
                                <div class="input_container">
                                    <h2>
                                        <?php sb_e("Categories"); ?>
                                    </h2>
                                    <div class="sb-setting sb-type-select">
                                        <select id="article-categories"></select>
                                    </div>
                                </div>
                                <div class="input_container">
                                    <h2>
                                        <?php sb_e("Content"); ?>
                                    </h2>
                                    <?php echo sb_get_setting(
                                        "disable-editor-js"
                                    )
                                        ? "<textarea></textarea>"
                                        : '<div id="editorjs"></div>'; ?>
                                </div>
                                <!-- </div> -->
                                <!-- </div> -->
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

                                        <div class="input_container">
                                            <h2 class="sb-language-switcher-cnt w-100">
                                                <?php sb_e("Name"); ?>
                                            </h2>
                                            <div class="sb-setting sb-type-text">
                                                <div>
                                                    <input id="category-title" type="text" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_container">
                                            <h2 class="category-parent" style="width: 100%; padding-bottom: 0;">
                                                <?php sb_e("Parent category"); ?>
                                            </h2>
                                            <div data-type="checkbox" class="sb-setting sb-type-checkbox category-parent">
                                                <div class="input">
                                                    <input id="category-parent" type="checkbox" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_container">
                                            <h2>
                                                <?php sb_e("Description"); ?>
                                            </h2>
                                            <div class="sb-setting sb-type-textarea">
                                                <div>
                                                    <textarea id="category-description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_container">
                                            <h2>
                                                <?php sb_e("Image"); ?>
                                            </h2>
                                            <div data-type="image" class="sb-input sb-setting sb-input-image">
                                                <div class="image" id="category-image-container">
                                                    <input type="file" id="category-image" accept="image/*" style="display: none;">
                                                    <div class="sb-icon-close"></div>
                                                </div>
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
                            <div class="top-bar-search">
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
                                    <li id="tab-articles">
                                        <?php echo $disable_translations
                                            ? "Articles"
                                            : sb_("Articles"); ?>
                                    </li>
                                    <?php /*
                                                                                                              <li id="tab-various">
                                                                                                                  <?php echo $disable_translations
                                                                                                                      ? "Miscellaneous"
                                                                                                                      : sb_("Miscellaneous"); ?>
                                                                                                              </li>
                                                                                                              */ ?>
                                    <?php for (
                                        $i = 0;
                                        $i < count($apps);
                                        $i++
                                    ) {
                                        // if ($apps[$i][1] != 'tickets')
                                        //     continue;
                        
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
                                    <?php /*
                                                                                                                                                                         <li id="tab-apps">
                                                                                                                                                                             <?php echo $disable_translations
                                                                                                                                                                                 ? "Apps"
                                                                                                                                                                                 : sb_("Apps"); ?>
                                                                                                                                                                         </li>
                                                                                                                                                                         */ ?>

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
                                <a id="sb-accout" href="<?php echo dirname(SB_URL); ?>/account/?tab=installation"
                                    data-tooltip="Account">
                                    Account
                                </a>
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
                                    <div class="sb-top-bar settings-header">
                                        <div>
                                            <p class="head">Chat Settings</p>
                                            <p class="des mb-0">The Chat settings contain modifiers for the chat widget that can be
                                                adjusted according to user preferences. Here, you may update the chat Availability,
                                                Features, and Management settings.</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="<?php sb_e('Search ...') ?>" />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new" style="float: right;">
                                                <i class="sb-icon-check"></i>Save changes</a>
                                        </div>
                                    </div>

                                    <!-- <div class="settings-card"> -->
                                    <div class="my-tabs-container">
                                        <button class="tab-arrow left">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.803 75.803"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_64" data-name="Group 64" transform="translate(-624.082 -383.588)">
                                                    <path id="Path_56" data-name="Path 56"
                                                        d="M660.313,383.588a1.5,1.5,0,0,1,1.06,2.561l-33.556,33.56a2.528,2.528,0,0,0,0,3.564l33.556,33.558a1.5,1.5,0,0,1-2.121,2.121L625.7,425.394a5.527,5.527,0,0,1,0-7.807l33.556-33.559A1.5,1.5,0,0,1,660.313,383.588Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                        <div class="my-tabs">
                                            <div class="my-tab active" data-target="availability-content">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Availability
                                            </div>
                                            <div class="my-tab" data-target="appearance-content">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>Appearance & Features
                                            </div>
                                            <div class="my-tab" data-target="management-content">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Management
                                            </div>
                                            <div class="tab-slider"></div>
                                        </div>
                                        <button class="tab-arrow right">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.804 75.804"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_65" data-name="Group 65" transform="translate(-831.568 -384.448)">
                                                    <path id="Path_57" data-name="Path 57"
                                                        d="M833.068,460.252a1.5,1.5,0,0,1-1.061-2.561l33.557-33.56a2.53,2.53,0,0,0,0-3.564l-33.557-33.558a1.5,1.5,0,0,1,2.122-2.121l33.556,33.558a5.53,5.53,0,0,1,0,7.807l-33.557,33.56A1.5,1.5,0,0,1,833.068,460.252Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>

                                    <div id="availability-content" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Chat Availability
                                            </p>
                                            <p class="des mb-0">Chat Availability allows the admin to adjust the visibility of the
                                                chat widget in accordance to the workplace’s office hours and agent availability. If
                                                these modifiers are turned on, the chat will be disabled and hidden if it is outside
                                                work hours or if there are no agents online.</p>
                                        </div>

                                        <?php sb_populate_settings("chat", $sb_settings, true, 'chat-availability'); ?>
                                    </div>

                                    <div id="appearance-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Appearance & Features
                                            </p>
                                            <p class="des mb-0">Appearance Availability allows users to adjust what initializes
                                                within the chat when visitors interact with it and adjust what visitors may have
                                                access to. This includes displaying the chat dashboard, restricting access to what
                                                visitors may send, language translation settings, and whether or not to show active
                                                agents.</p>
                                        </div>

                                        <?php sb_populate_settings("chat", $sb_settings, true, 'chat-appearance-and-features'); ?>
                                    </div>

                                    <div id="management-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Management Settings
                                            </p>
                                            <p class="des mb-0">Management controls access to the management widget’s availability
                                                to visitors and their ability to archive conversations with agents.</p>
                                        </div>

                                        <?php sb_populate_settings("chat", $sb_settings, true, 'chat-management'); ?>
                                    </div>
                                    <!-- </div> -->

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

                                    <div class="sb-top-bar settings-header">
                                        <div>
                                            <p class="head">Admin Settings</p>
                                            <p class="des mb-0">The Admin Settings provides access to customization options
                                                regarding modification or restrictions an admin may use for the platform. There are
                                                various different settings that can be altered to meet the admin’s needs, including
                                                creation of departments, adjusting themes, setting restrictions, and more.</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="<?php sb_e('Search ...') ?>" />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new" style="float: right;">
                                                <i class="sb-icon-check"></i>Save changes</a>
                                        </div>
                                    </div>
                                    <!-- <div class="settings-card"> -->
                                    <div class="my-tabs-container">
                                        <button class="tab-arrow left">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.803 75.803"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_64" data-name="Group 64" transform="translate(-624.082 -383.588)">
                                                    <path id="Path_56" data-name="Path 56"
                                                        d="M660.313,383.588a1.5,1.5,0,0,1,1.06,2.561l-33.556,33.56a2.528,2.528,0,0,0,0,3.564l33.556,33.558a1.5,1.5,0,0,1-2.121,2.121L625.7,425.394a5.527,5.527,0,0,1,0-7.807l33.556-33.559A1.5,1.5,0,0,1,660.313,383.588Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                        <div class="my-tabs">
                                            <div class="my-tab active" data-target="panel-setting-content">
                                                <svg width="17" height="19" viewBox="0 0 17 19" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.5 10.75V1M2.5 10.75C2.89782 10.75 3.27936 10.908 3.56066 11.1893C3.84196 11.4706 4 11.8522 4 12.25C4 12.6478 3.84196 13.0294 3.56066 13.3107C3.27936 13.592 2.89782 13.75 2.5 13.75M2.5 10.75C2.10218 10.75 1.72064 10.908 1.43934 11.1893C1.15804 11.4706 1 11.8522 1 12.25C1 12.6478 1.15804 13.0294 1.43934 13.3107C1.72064 13.592 2.10218 13.75 2.5 13.75M2.5 13.75V17.5M14.5 10.75V1M14.5 10.75C14.8978 10.75 15.2794 10.908 15.5607 11.1893C15.842 11.4706 16 11.8522 16 12.25C16 12.6478 15.842 13.0294 15.5607 13.3107C15.2794 13.592 14.8978 13.75 14.5 13.75M14.5 10.75C14.1022 10.75 13.7206 10.908 13.4393 11.1893C13.158 11.4706 13 11.8522 13 12.25C13 12.6478 13.158 13.0294 13.4393 13.3107C13.7206 13.592 14.1022 13.75 14.5 13.75M14.5 13.75V17.5M8.5 4.75V1M8.5 4.75C8.89782 4.75 9.27936 4.90804 9.56066 5.18934C9.84196 5.47064 10 5.85218 10 6.25C10 6.64782 9.84196 7.02936 9.56066 7.31066C9.27936 7.59196 8.89782 7.75 8.5 7.75M8.5 4.75C8.10218 4.75 7.72064 4.90804 7.43934 5.18934C7.15804 5.47064 7 5.85218 7 6.25C7 6.64782 7.15804 7.02936 7.43934 7.31066C7.72064 7.59196 8.10218 7.75 8.5 7.75M8.5 7.75V17.5"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                Panel Settings
                                            </div>
                                            <div class="my-tab" data-target="language">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7 8.37931H11.5M11.5 8.37931H14.5M11.5 8.37931V7M17 8.37931H14.5M14.5 8.37931C13.9725 10.2656 12.8679 12.0487 11.6071 13.6158M11.6071 13.6158C10.5631 14.9134 9.41205 16.0628 8.39286 17M11.6071 13.6158C10.9643 12.8621 10.0643 11.6426 9.80714 11.0909M11.6071 13.6158L13.5357 15.6207"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z"
                                                        stroke="black" stroke-width="1.5" />
                                                </svg>


                                                Language
                                            </div>
                                            <div class="my-tab" data-target="admin-chat-management-content">
                                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M18.75 6.761C19.634 7.045 20.25 7.889 20.25 8.858V13.144C20.25 14.28 19.403 15.244 18.27 15.337C17.93 15.364 17.59 15.389 17.25 15.409V18.5L14.25 15.5C12.896 15.5 11.556 15.445 10.23 15.337C9.94133 15.3137 9.66053 15.2313 9.405 15.095M18.75 6.761C18.5955 6.71127 18.4358 6.67939 18.274 6.666C15.5959 6.44368 12.9041 6.44368 10.226 6.666C9.095 6.76 8.25 7.723 8.25 8.858V13.144C8.25 13.981 8.71 14.724 9.405 15.095M18.75 6.761V4.887C18.75 3.266 17.598 1.861 15.99 1.652C13.9208 1.38379 11.8365 1.24951 9.75 1.25C7.635 1.25 5.552 1.387 3.51 1.652C1.902 1.861 0.75 3.266 0.75 4.887V11.113C0.75 12.734 1.902 14.139 3.51 14.348C4.087 14.423 4.667 14.488 5.25 14.542V19.25L9.405 15.095"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                Chat Management
                                            </div>
                                            <div class="my-tab" data-target="permission-setting-content">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20.5039 5.99855C20.504 6.62191 20.3745 7.23847 20.1238 7.80916C19.873 8.37985 19.5064 8.89222 19.0473 9.31379C18.5881 9.73537 18.0463 10.0569 17.4563 10.2581C16.8664 10.4593 16.241 10.5357 15.6199 10.4826C14.5439 10.3916 13.3559 10.5536 12.6699 11.3866L5.51793 20.0706C5.29232 20.3456 5.01163 20.5703 4.69392 20.7303C4.37622 20.8902 4.02854 20.9819 3.67326 20.9994C3.31798 21.0169 2.96298 20.9598 2.6311 20.8318C2.29922 20.7038 1.99782 20.5077 1.74629 20.2562C1.49477 20.0047 1.2987 19.7033 1.17069 19.3714C1.04269 19.0395 0.985594 18.6845 1.00308 18.3292C1.02057 17.9739 1.11225 17.6263 1.27222 17.3086C1.4322 16.9909 1.65692 16.7102 1.93193 16.4846L10.6159 9.33255C11.4489 8.64655 11.6109 7.45855 11.5199 6.38255C11.4528 5.60073 11.5913 4.81493 11.9216 4.10315C12.252 3.39137 12.7627 2.77834 13.4031 2.3249C14.0436 1.87146 14.7914 1.59338 15.5725 1.51824C16.3536 1.4431 17.1408 1.57352 17.8559 1.89655L14.5799 5.17255C14.7067 5.72065 14.9848 6.22214 15.3826 6.61993C15.7803 7.01772 16.2818 7.29582 16.8299 7.42255L20.1059 4.14655C20.3619 4.71155 20.5039 5.33855 20.5039 5.99855Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                Permission Settings
                                            </div>
                                            <div class="my-tab" data-target="settings-customization-content">
                                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.181 16.2665C16.0555 16.2665 16.7643 15.5577 16.7643 14.6832C16.7643 13.8087 16.0555 13.0999 15.181 13.0999C14.3065 13.0999 13.5977 13.8087 13.5977 14.6832C13.5977 15.5577 14.3065 16.2665 15.181 16.2665Z"
                                                        stroke="black" stroke-width="1.18257" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M16.2662 10.712L16.13 9.6167H14.23L14.0938 10.712C13.7581 10.8039 13.4383 10.9375 13.1407 11.1069L12.2698 10.429L10.924 11.7726L11.6048 12.6444C11.4338 12.9404 11.3008 13.26 11.2089 13.5966L10.1133 13.7333V15.6333L11.2089 15.7701C11.3008 16.1067 11.4338 16.4259 11.6048 16.7223L10.924 17.5941L12.2698 18.9377L13.1407 18.2598C13.4383 18.4289 13.755 18.5628 14.0938 18.6546L14.23 19.75H16.13L16.2662 18.6546C16.6018 18.5628 16.9217 18.4292 17.2193 18.2598L18.0902 18.9377L19.436 17.5941L18.7552 16.7223C18.9261 16.4263 19.0592 16.1067 19.151 15.7701L20.2467 15.6333V13.7333L19.151 13.5966C19.0592 13.26 18.9262 12.9407 18.7552 12.6444L19.436 11.7726L18.0902 10.429L17.2193 11.1069C16.9217 10.9378 16.605 10.8038 16.2662 10.712Z"
                                                        stroke="black" stroke-width="1.18257" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M5.99741 3.91669C6.87186 3.91669 7.58075 3.2078 7.58075 2.33334C7.58075 1.45889 6.87186 0.75 5.99741 0.75C5.12295 0.75 4.41406 1.45889 4.41406 2.33334C4.41406 3.2078 5.12295 3.91669 5.99741 3.91669Z"
                                                        stroke="black" stroke-width="1.18257" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M5.99741 13.4167C6.87186 13.4167 7.58075 12.7078 7.58075 11.8333C7.58075 10.9589 6.87186 10.25 5.99741 10.25C5.12295 10.25 4.41406 10.9589 4.41406 11.8333C4.41406 12.7078 5.12295 13.4167 5.99741 13.4167Z"
                                                        stroke="black" stroke-width="1.18257" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M10.431 8.66669C11.3055 8.66669 12.0143 7.9578 12.0143 7.08334C12.0143 6.20889 11.3055 5.5 10.431 5.5C9.55654 5.5 8.84766 6.20889 8.84766 7.08334C8.84766 7.9578 9.55654 8.66669 10.431 8.66669Z"
                                                        stroke="black" stroke-width="1.18257" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M12.0117 7.08325H15.1784" stroke="black" stroke-width="1.18257"
                                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M1.24609 2.33325H4.41274" stroke="black" stroke-width="1.18257"
                                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M1.24609 11.8333H4.41274" stroke="black" stroke-width="1.18257"
                                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M15.1781 2.33325H7.57812" stroke="black" stroke-width="1.18257"
                                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M10.9696 11.8333H7.57812" stroke="black" stroke-width="1.18257"
                                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M1.24609 7.08325H8.84608" stroke="black" stroke-width="1.18257"
                                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                Settings Customization
                                            </div>
                                            <div class="my-tab" data-target="customer-content">
                                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.5578 13.396C16.0256 13.0765 16.5742 12.8961 17.1403 12.8754C17.7064 12.8548 18.2668 12.9949 18.7566 13.2794C19.2464 13.564 19.6456 13.9814 19.9081 14.4834C20.1706 14.9854 20.2856 15.5514 20.2398 16.116C19.0399 16.5353 17.7655 16.6985 16.4988 16.595C16.4949 15.4616 16.1685 14.3518 15.5578 13.397C15.0157 12.5468 14.2679 11.847 13.3836 11.3625C12.4993 10.878 11.5071 10.6243 10.4988 10.625C9.4906 10.6245 8.49859 10.8782 7.61449 11.3627C6.7304 11.8473 5.98277 12.5469 5.44076 13.397M16.4978 16.594L16.4988 16.625C16.4988 16.85 16.4868 17.072 16.4618 17.291C14.6471 18.3321 12.5909 18.8783 10.4988 18.875C8.32876 18.875 6.29176 18.299 4.53576 17.291C4.51006 17.0596 4.4977 16.8269 4.49876 16.594M4.49876 16.594C3.23239 16.7013 1.95867 16.5387 0.759764 16.117C0.714115 15.5526 0.829159 14.9867 1.09159 14.4849C1.35403 13.9831 1.75313 13.5658 2.24277 13.2813C2.7324 12.9968 3.29256 12.8567 3.85848 12.8771C4.42441 12.8976 4.97297 13.0778 5.44076 13.397M4.49876 16.594C4.50236 15.4607 4.83039 14.3519 5.44076 13.397M13.4988 4.625C13.4988 5.42065 13.1827 6.18371 12.6201 6.74632C12.0575 7.30893 11.2944 7.625 10.4988 7.625C9.70311 7.625 8.94005 7.30893 8.37744 6.74632C7.81483 6.18371 7.49876 5.42065 7.49876 4.625C7.49876 3.82935 7.81483 3.06629 8.37744 2.50368C8.94005 1.94107 9.70311 1.625 10.4988 1.625C11.2944 1.625 12.0575 1.94107 12.6201 2.50368C13.1827 3.06629 13.4988 3.82935 13.4988 4.625ZM19.4988 7.625C19.4988 7.92047 19.4406 8.21306 19.3275 8.48604C19.2144 8.75902 19.0487 9.00706 18.8398 9.21599C18.6308 9.42492 18.3828 9.59066 18.1098 9.70373C17.8368 9.8168 17.5442 9.875 17.2488 9.875C16.9533 9.875 16.6607 9.8168 16.3877 9.70373C16.1147 9.59066 15.8667 9.42492 15.6578 9.21599C15.4488 9.00706 15.2831 8.75902 15.17 8.48604C15.057 8.21306 14.9988 7.92047 14.9988 7.625C14.9988 7.02826 15.2358 6.45597 15.6578 6.03401C16.0797 5.61205 16.652 5.375 17.2488 5.375C17.8455 5.375 18.4178 5.61205 18.8398 6.03401C19.2617 6.45597 19.4988 7.02826 19.4988 7.625ZM5.99876 7.625C5.99876 7.92047 5.94057 8.21306 5.82749 8.48604C5.71442 8.75902 5.54869 9.00706 5.33975 9.21599C5.13082 9.42492 4.88278 9.59066 4.6098 9.70373C4.33682 9.8168 4.04424 9.875 3.74876 9.875C3.45329 9.875 3.16071 9.8168 2.88773 9.70373C2.61474 9.59066 2.36671 9.42492 2.15777 9.21599C1.94884 9.00706 1.78311 8.75902 1.67004 8.48604C1.55696 8.21306 1.49876 7.92047 1.49876 7.625C1.49876 7.02826 1.73582 6.45597 2.15777 6.03401C2.57973 5.61205 3.15203 5.375 3.74876 5.375C4.3455 5.375 4.9178 5.61205 5.33975 6.03401C5.76171 6.45597 5.99876 7.02826 5.99876 7.625Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>

                                                Customer
                                            </div>
                                            <div class="my-tab" data-target="auto-saved-message-inbox-content">
                                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M18.7985 12.6323H14.1195C13.3195 12.6323 12.6911 13.3007 12.3386 14.0071C11.9555 14.7745 11.1886 15.4821 9.77424 15.4821C8.3599 15.4821 7.59298 14.7745 7.20998 14.0071C6.85744 13.3007 6.22893 12.6323 5.42899 12.6323H0.75"
                                                        stroke="black" stroke-width="1.5" stroke-linejoin="round" />
                                                    <path
                                                        d="M14.6758 5.02626C14.6758 5.02626 15.0339 5.13576 15.5313 5.86524C15.5313 5.86524 16.2608 4.29028 17.2721 3.74316"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M18.8144 11.2162C18.8144 15.4741 18.8144 17.6029 17.4917 18.9258C16.1689 20.2485 14.04 20.2485 9.78222 20.2485C5.52438 20.2485 3.39547 20.2485 2.07273 18.9258C0.75 17.6029 0.75 15.4741 0.75 11.2162C0.75 6.95845 0.75 4.82955 2.07273 3.50681C3.39547 2.18408 5.52438 2.18408 9.78222 2.18408"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M19.7511 5.03376C19.7511 7.12348 18.0571 8.81752 15.9673 8.81752C13.8777 8.81752 12.1836 7.12348 12.1836 5.03376C12.1836 2.94405 13.8777 1.25 15.9673 1.25C18.0571 1.25 19.7511 2.94405 19.7511 5.03376Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round" />
                                                </svg>

                                                Auto Saved Message Inbox
                                            </div>
                                            <div class="my-tab" data-target="customization-and-themes-content">
                                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.9986 6.13437L7.30799 7.76025C7.02657 7.84066 6.88586 7.88086 6.76963 7.95644C6.66673 8.02333 6.57822 8.11007 6.50926 8.2116C6.43135 8.32628 6.38831 8.46615 6.30224 8.74589L2.84593 19.9789M2.84593 19.9789L14.0789 16.5226C14.3587 16.4365 14.4985 16.3934 14.6132 16.3155C14.7147 16.2466 14.8015 16.1581 14.8684 16.0552C14.9439 15.939 14.9842 15.7982 15.0645 15.5168L16.6904 9.82624M2.84593 19.9789L8.9246 13.9001M3.7689 7.05734V1.51953M1 4.28843H6.5378M19.3381 6.93609L15.8888 3.48672C15.5233 3.1212 15.3404 2.93846 15.1297 2.86998C14.9444 2.80975 14.7446 2.80975 14.5593 2.86998C14.3486 2.93846 14.1658 3.1212 13.8003 3.48672L13.1199 4.16718C12.7544 4.5327 12.5715 4.71544 12.5031 4.92619C12.4429 5.11156 12.4429 5.31124 12.5031 5.49662C12.5715 5.70736 12.7544 5.89011 13.1199 6.25562L16.5692 9.70496C16.9347 10.0705 17.1175 10.2533 17.3282 10.3217C17.5135 10.382 17.7133 10.382 17.8986 10.3217C18.1093 10.2533 18.2922 10.0705 18.6577 9.70496L19.3381 9.02455C19.7036 8.65901 19.8864 8.47626 19.9548 8.26552C20.0151 8.08014 20.0151 7.88047 19.9548 7.69509C19.8864 7.48435 19.7036 7.3016 19.3381 6.93609ZM10.2297 10.7492C11.2492 10.7492 12.0756 11.5756 12.0756 12.5951C12.0756 13.6147 11.2492 14.4411 10.2297 14.4411C9.21016 14.4411 8.38374 13.6147 8.38374 12.5951C8.38374 11.5756 9.21016 10.7492 10.2297 10.7492Z"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                Customization & Themes
                                            </div>
                                            <div class="my-tab" data-target="switch-accounts-content">
                                                <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <mask id="mask0_1995_2948" style="mask-type:luminance"
                                                        maskUnits="userSpaceOnUse" x="0" y="0" width="19" height="20">
                                                        <path d="M0 0.249146H19.0001V19.2493H0V0.249146Z" fill="white" />
                                                    </mask>
                                                    <g mask="url(#mask0_1995_2948)">
                                                        <path
                                                            d="M9.50129 8.40773C7.77254 8.40773 6.37109 9.80918 6.37109 11.5379C6.37109 12.4023 7.0718 13.103 7.93619 13.103H11.0664C11.9308 13.103 12.6315 12.4023 12.6315 11.5379C12.6315 9.80918 11.23 8.40773 9.50129 8.40773Z"
                                                            stroke="black" stroke-width="1.11329" stroke-miterlimit="10"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M11.0638 6.84262C11.0638 7.70701 10.3631 8.40771 9.49869 8.40771C8.6343 8.40771 7.93359 7.70701 7.93359 6.84262C7.93359 5.97822 8.6343 5.27752 9.49869 5.27752C10.3631 5.27752 11.0638 5.97822 11.0638 6.84262Z"
                                                            stroke="black" stroke-width="1.11329" stroke-miterlimit="10"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M0.851049 12.0215C0.111194 9.19392 0.770409 6.06049 2.88221 3.73576C6.20333 0.0797596 11.8595 -0.191735 15.5155 3.12943L16.3425 3.88071"
                                                            stroke="black" stroke-width="1.11329" stroke-miterlimit="10"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M18.1515 7.47695C18.8914 10.3045 18.2322 13.4379 16.1204 15.7627C12.7993 19.4187 7.14313 19.6902 3.48713 16.369L2.46094 15.4368"
                                                            stroke="black" stroke-width="1.11329" stroke-miterlimit="10"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M16.7319 1.78922L16.8546 4.34697L14.2969 4.46973" stroke="black"
                                                            stroke-width="1.11329" stroke-miterlimit="10" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M2.26729 17.7092L2.14453 15.1515L4.70227 15.0287" stroke="black"
                                                            stroke-width="1.11329" stroke-miterlimit="10" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                </svg>

                                                Switch Accounts
                                            </div>
                                            <div class="my-tab" data-target="department-content">
                                                <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <mask id="mask0_1996_3079" style="mask-type:luminance"
                                                        maskUnits="userSpaceOnUse" x="0" y="0" width="19" height="20">
                                                        <path d="M0 0.249268H19V19.2493H0V0.249268Z" fill="white" />
                                                    </mask>
                                                    <g mask="url(#mask0_1996_3079)">
                                                        <path
                                                            d="M10.6133 4.14575C10.6133 4.76058 10.1148 5.25903 9.5 5.25903C8.88517 5.25903 8.38672 4.76058 8.38672 4.14575C8.38672 3.53092 8.88517 3.03247 9.5 3.03247C10.1148 3.03247 10.6133 3.53092 10.6133 4.14575Z"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M7.36719 7.96289C7.64091 7.0431 8.4929 6.37238 9.50161 6.37238C10.5103 6.37238 11.3623 7.0431 11.636 7.96289"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M13.3945 4.70239C13.3945 6.85437 11.65 8.59888 9.49805 8.59888C7.34607 8.59888 5.60156 6.85437 5.60156 4.70239C5.60156 2.55042 7.34607 0.805907 9.49805 0.805907C11.65 0.805907 13.3945 2.55042 13.3945 4.70239Z"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M5.60156 14.2024C5.60156 14.8172 5.10311 15.3157 4.48828 15.3157C3.87345 15.3157 3.375 14.8172 3.375 14.2024C3.375 13.5876 3.87345 13.0891 4.48828 13.0891C5.10311 13.0891 5.60156 13.5876 5.60156 14.2024Z"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M2.35547 18.0195C2.62919 17.0997 3.48118 16.429 4.48989 16.429C5.49856 16.429 6.35055 17.0997 6.62427 18.0195"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M8.38477 14.759C8.38477 16.911 6.64025 18.6926 4.48828 18.6926C2.33631 18.6926 0.554688 16.911 0.554688 14.759C0.554688 12.6071 2.33631 10.8625 4.48828 10.8625C6.64025 10.8625 8.38477 12.6071 8.38477 14.759Z"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M15.625 14.2024C15.625 14.8172 15.1265 15.3157 14.5117 15.3157C13.8969 15.3157 13.3984 14.8172 13.3984 14.2024C13.3984 13.5876 13.8969 13.0891 14.5117 13.0891C15.1265 13.0891 15.625 13.5876 15.625 14.2024Z"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M12.375 18.0195C12.6487 17.0997 13.5007 16.429 14.5094 16.429C15.5181 16.429 16.3701 17.0997 16.6438 18.0195"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path
                                                            d="M18.4434 14.759C18.4434 16.911 16.6617 18.6926 14.5098 18.6926C12.3578 18.6926 10.6133 16.911 10.6133 14.759C10.6133 12.6071 12.3578 10.8625 14.5098 10.8625C16.6617 10.8625 18.4434 12.6071 18.4434 14.759Z"
                                                            stroke="black" stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path d="M9.5 8.59888V10.8625" stroke="black" stroke-width="1.11328"
                                                            stroke-miterlimit="10" />
                                                        <path d="M7.57031 11.9758L9.49859 10.8625" stroke="black"
                                                            stroke-width="1.11328" stroke-miterlimit="10" />
                                                        <path d="M11.4283 11.9758L9.5 10.8625" stroke="black" stroke-width="1.11328"
                                                            stroke-miterlimit="10" />
                                                    </g>
                                                </svg>

                                                Department
                                            </div>
                                            <div class="tab-slider"></div>
                                        </div>
                                        <button class="tab-arrow right">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.804 75.804"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_65" data-name="Group 65" transform="translate(-831.568 -384.448)">
                                                    <path id="Path_57" data-name="Path 57"
                                                        d="M833.068,460.252a1.5,1.5,0,0,1-1.061-2.561l33.557-33.56a2.53,2.53,0,0,0,0-3.564l-33.557-33.558a1.5,1.5,0,0,1,2.122-2.121l33.556,33.558a5.53,5.53,0,0,1,0,7.807l-33.557,33.56A1.5,1.5,0,0,1,833.068,460.252Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>

                                    <div id="panel-setting-content" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Panel Setting
                                            </p>
                                            <p class="des mb-0">In Panel Setting you can adjust what information and features may be
                                                available for display in the conversation panel for the agent. This includes
                                                Filters, Tags, Notes, Attachment Lists, and Profile Pictures. Further customization
                                                may also include admin privy details such as Locations, Languages, or the visitor’s
                                                Site Origin.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'panel-setting'); ?>
                                    </div>

                                    <div id="language" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Language
                                            </p>
                                            <p class="des mb-0">The Language Settings allows the admin to turn the automatic
                                                language in conversations translation on or off.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'language'); ?>
                                    </div>

                                    <div id="admin-chat-management-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Chat Management
                                            </p>
                                            <p class="des mb-0">The Chat Management Settings provides settings that allow one to
                                                properly sort through chats by date and adjust whether or not they wish to
                                                automatically archive conversations after 24 hours.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'admin-chat-management'); ?>
                                    </div>

                                    <div id="permission-setting-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Permission Settings
                                            </p>
                                            <p class="des mb-0">The Permission Settings controls permissions and restrictions
                                                regarding how much access Agents and Supervisors may be granted authority to with
                                                their privileges and whether or not they are privy to certain information or admin
                                                control to customer settings.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'permission-setting'); ?>
                                    </div>

                                    <div id="settings-customization-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Settings Customization
                                            </p>
                                            <p class="des mb-0">Settings Customization provides access to creation of multiple
                                                Conversation Tags as well as to adjust Tag and Notes availability. Chat Transcript
                                                settings and its download format can also be adjusted here.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'settings-customization'); ?>
                                    </div>

                                    <div id="customer-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Customer
                                            </p>
                                            <p class="des mb-0">The Customer Settings allows for the creation of extra columns to
                                                customer information depending on what is needed.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'customer'); ?>
                                    </div>

                                    <div id="auto-saved-message-inbox-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Auto Saved Message Inbox
                                            </p>
                                            <p class="des mb-0">The Auto Saved Message Inbox allows users to create automated
                                                replies and store them for instant use. Automated responses will be available for
                                                use in the agent’s chatbox and can be sorted through using the #.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'auto-saved-message-inbox'); ?>
                                    </div>

                                    <div id="customization-and-themes-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Customization & Themes
                                            </p>
                                            <p class="des mb-0">Customization and Themes allows customization of the chatbox
                                                appearance.
                                                This can be done with either a .js file, CSS, or by adjusting the color themes.
                                                Adjustments to the admin logo, title, icon, and login message can be adjusted here
                                                as well.
                                            </p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'customization-and-themes'); ?>
                                    </div>

                                    <div id="switch-accounts-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Switch Accounts
                                            </p>
                                            <p class="des mb-0">The Switch Accounts settings allows the admin to adjust settings for
                                                switching accounts.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'switch-accounts'); ?>
                                    </div>

                                    <div id="department-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Department
                                            </p>
                                            <p class="des mb-0">Department settings allow the creation of Departments for specific
                                                areas that agents can be assigned and sorted into for easier appointment of tickets
                                                depending on what is needed.</p>
                                        </div>
                                        <?php sb_populate_settings("admin", $sb_settings, true, 'department'); ?>
                                    </div>
                                    <!-- </div> -->
                                </div>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Notification Settings</p>
                                            <p class="des mb-0">The Notification Settings provides modifiers that allow
                                                customization of alerts agents or users may receive with regards to emails,
                                                messages, or user availability. Feature settings such as tab notifications and push
                                                notifications can also be found here.</p>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="<?php sb_e('Search ...') ?>" />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- <div class="settings-card"> -->
                                    <div class="my-tabs-container">
                                        <button class="tab-arrow left">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.803 75.803"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_64" data-name="Group 64" transform="translate(-624.082 -383.588)">
                                                    <path id="Path_56" data-name="Path 56"
                                                        d="M660.313,383.588a1.5,1.5,0,0,1,1.06,2.561l-33.556,33.56a2.528,2.528,0,0,0,0,3.564l33.556,33.558a1.5,1.5,0,0,1-2.121,2.121L625.7,425.394a5.527,5.527,0,0,1,0-7.807l33.556-33.559A1.5,1.5,0,0,1,660.313,383.588Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                        <div class="my-tabs">
                                            <div class="my-tab active" data-target="panel-setting">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Panel Setting
                                            </div>
                                            <div class="my-tab" data-target="notifications-availability">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>Availability
                                            </div>
                                            <div class="my-tab" data-target="chat-management">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Chat Management
                                            </div>
                                            <div class="my-tab" data-target="appearance-feature">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Appearance & Features
                                            </div>
                                            <div class="my-tab" data-target="notifications-management">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Management
                                            </div>
                                            <div class="my-tab" data-target="notifications-email">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Email
                                            </div>
                                            <div class="my-tab" data-target="settings-customization">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Settings Customization
                                            </div>
                                            <div class="tab-slider"></div>
                                        </div>
                                        <button class="tab-arrow right">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.804 75.804"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_65" data-name="Group 65" transform="translate(-831.568 -384.448)">
                                                    <path id="Path_57" data-name="Path 57"
                                                        d="M833.068,460.252a1.5,1.5,0,0,1-1.061-2.561l33.557-33.56a2.53,2.53,0,0,0,0-3.564l-33.557-33.558a1.5,1.5,0,0,1,2.122-2.121l33.556,33.558a5.53,5.53,0,0,1,0,7.807l-33.557,33.56A1.5,1.5,0,0,1,833.068,460.252Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>

                                    <div id="panel-setting" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Panel Setting
                                            </p>
                                            <p class="des mb-0">Panel Settings allows you to activate or deactivate sound
                                                notifications for messages. Here, you are also allowed to change the notification
                                                sounds to your liking.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-panel-setting'); ?>
                                    </div>

                                    <div id="notifications-availability" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Availability
                                            </p>
                                            <p class="des mb-0">The availability settings allows control over notifications received
                                                regarding any user going online, as well as the option to set your status offline
                                                when you are idle.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-availability'); ?>
                                    </div>

                                    <div id="chat-management" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Chat Management
                                            </p>
                                            <p class="des mb-0">Chat management provides push notification settings for both Desktop
                                                and Texts when receiving messages.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-chat-management'); ?>
                                    </div>

                                    <div id="appearance-feature" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Appearance and Features
                                            </p>
                                            <p class="des mb-0">Appearance and Features Settings allows you to set flash
                                                notifications for received messages, as well as set automated Email signatures and
                                                headers for direct emails.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-appearance-features'); ?>
                                    </div>

                                    <div id="notifications-management" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Management
                                            </p>
                                            <p class="des mb-0">Management settings allow for automated emails to be set up as
                                                notifications for users and agents when either receives a message. They can be
                                                adjusted accordingly depending on what is needed.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-management'); ?>
                                    </div>

                                    <div id="notifications-email" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Email
                                            </p>
                                            <p class="des mb-0">The Email Settings allow you to provide email details to be prepared
                                                for automated responses.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-email'); ?>
                                    </div>

                                    <div id="settings-customization" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Setting Customizations
                                            </p>
                                            <p class="des mb-0">The settings customization allows you to set up a mobile account for
                                                automated text responses.</p>
                                        </div>

                                        <?php sb_populate_settings("notifications", $sb_settings, true, 'notifications-settings-customization'); ?>
                                    </div>
                                    <!-- </div> -->
                                </div>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Users Settings</p>
                                            <p class="des mb-0">User Settings allow you to configure user related settings such as
                                                requiring registration for the chat and adjusting the required fields. Access to
                                                registration can also be restricted from here depending on office hours or active
                                                agents.</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="<?php sb_e('Search ...') ?>" />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

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
                                            <p class="head mb-4">Message & Forms</p>
                                            <p class="des mb-0">The Message Settings enables customization of pop up messages
                                                depending on visitor activity, such as first entry, closing, agent ratings, and
                                                prompts to let the user know that all agents are offline. Email templates and
                                                Registration form details can also be edited here.</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="<?php sb_e('Search ...') ?>" />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- <div class="settings-card"> -->
                                    <div class="my-tabs-container">
                                        <button class="tab-arrow left">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.803 75.803"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_64" data-name="Group 64" transform="translate(-624.082 -383.588)">
                                                    <path id="Path_56" data-name="Path 56"
                                                        d="M660.313,383.588a1.5,1.5,0,0,1,1.06,2.561l-33.556,33.56a2.528,2.528,0,0,0,0,3.564l33.556,33.558a1.5,1.5,0,0,1-2.121,2.121L625.7,425.394a5.527,5.527,0,0,1,0-7.807l33.556-33.559A1.5,1.5,0,0,1,660.313,383.588Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                        <div class="my-tabs">
                                            <div class="my-tab active" data-target="message-content">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Message
                                            </div>
                                            <div class="my-tab" data-target="email-content">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>Email
                                            </div>
                                            <div class="my-tab" data-target="form-content">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Form
                                            </div>
                                            <div class="tab-slider"></div>
                                        </div>
                                        <button class="tab-arrow right">
                                            <svg width="18" height="18" viewBox="-19.04 0 75.804 75.804"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="Group_65" data-name="Group 65" transform="translate(-831.568 -384.448)">
                                                    <path id="Path_57" data-name="Path 57"
                                                        d="M833.068,460.252a1.5,1.5,0,0,1-1.061-2.561l33.557-33.56a2.53,2.53,0,0,0,0-3.564l-33.557-33.558a1.5,1.5,0,0,1,2.122-2.121l33.556,33.558a5.53,5.53,0,0,1,0,7.807l-33.557,33.56A1.5,1.5,0,0,1,833.068,460.252Z"
                                                        fill="#000" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>

                                    <div id="message-content" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Message
                                            </p>
                                            <p class="des mb-0">The Message Settings allow you to set up messages for visitors who
                                                may be exploring your website for the first time, if they are idle, or if a
                                                situation arises where no agent can respond to them. Rating settings can be adjusted
                                                here as well.</p>
                                        </div>

                                        <?php sb_populate_settings("messages", $sb_settings, true, 'messages-text'); ?>
                                    </div>

                                    <div id="email-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Email
                                            </p>
                                            <p class="des mb-0">The Email provides a form you can fill up to send an automated
                                                follow up to registered visitors who use the Chat.</p>
                                        </div>

                                        <?php sb_populate_settings("messages", $sb_settings, true, 'messages-email'); ?>
                                    </div>

                                    <div id="form-content" style="display: none;" class="settings-tab">
                                        <div class="settings-head">
                                            <p class="head">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2789)">
                                                        <path
                                                            d="M10.1003 13V11.8333C10.1003 11.2145 9.85443 10.621 9.41684 10.1834C8.97926 9.74583 8.38577 9.5 7.76693 9.5H4.26693C3.64809 9.5 3.0546 9.74583 2.61701 10.1834C2.17943 10.621 1.93359 11.2145 1.93359 11.8333V13"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M10.0977 2.57471C10.598 2.70442 11.0411 2.99661 11.3575 3.40541C11.6738 3.81421 11.8454 4.31648 11.8454 4.83337C11.8454 5.35027 11.6738 5.85254 11.3575 6.26134C11.0411 6.67014 10.598 6.96232 10.0977 7.09204"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M13.5977 13.0001V11.8334C13.5973 11.3164 13.4252 10.8142 13.1085 10.4056C12.7917 9.99701 12.3482 9.70518 11.8477 9.57593"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M6.01693 7.16667C7.30559 7.16667 8.35026 6.122 8.35026 4.83333C8.35026 3.54467 7.30559 2.5 6.01693 2.5C4.72826 2.5 3.68359 3.54467 3.68359 4.83333C3.68359 6.122 4.72826 7.16667 6.01693 7.16667Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2789">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Form
                                            </p>
                                            <p class="des mb-0">The Form settings provides a customizable set of messages for the
                                                registration and login forms that is sent to the user when it is properly filled up.
                                            </p>
                                        </div>

                                        <?php sb_populate_settings("messages", $sb_settings, true, 'messages-form'); ?>
                                    </div>
                                    <!-- </div> -->

                                </div>
                                <?php /*<div>
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
                                                                                                                                   </div>*/ ?>
                                <div>
                                    <div class="sb-top-bar save_settings settings-header">
                                        <div class="">
                                            <p class="head mb-4">Articles</p>
                                            <p class="des mb-0">The Articles settings allow the user to configure the visibility of
                                                the articles on the chat dashboard and adjust its properties.</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="<?php sb_e('Search ...') ?>" />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

                                            <a class="sb-btn sb-save-changes sb-icon sb_btn_new">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e("Save changes"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="settings-card articles-card">
                                        <div class="my-tabs">
                                            <div class="my-tab active" data-target="articles-display">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2783)">
                                                        <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                            stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2783">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.765625 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Display
                                            </div>
                                            <div class="my-tab" data-target="articles-setting">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1833_2786)">
                                                        <path
                                                            d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                            stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1833_2786">
                                                            <rect width="14" height="14" fill="white"
                                                                transform="translate(0.203125 0.75)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Settings
                                            </div>
                                            <div class="tab-slider"></div>
                                        </div>

                                        <div id="articles-display" class="settings-tab">
                                            <div class="settings-head">
                                                <p class="head">
                                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_1833_2783)">
                                                            <path d="M7.76562 4.25V7.75L10.099 8.91667" stroke="black"
                                                                stroke-width="1.16667" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                            </path>
                                                            <path
                                                                d="M7.76693 13.5834C10.9886 13.5834 13.6003 10.9717 13.6003 7.75008C13.6003 4.52842 10.9886 1.91675 7.76693 1.91675C4.54527 1.91675 1.93359 4.52842 1.93359 7.75008C1.93359 10.9717 4.54527 13.5834 7.76693 13.5834Z"
                                                                stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1833_2783">
                                                                <rect width="14" height="14" fill="white"
                                                                    transform="translate(0.765625 0.75)"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                    Display
                                                </p>
                                                <p class="des mb-0">The Display allows you to control when the article panels and
                                                    categories can be displayed on the dashboard.</p>
                                            </div>

                                            <?php sb_populate_settings("articles", $sb_settings, true, 'articles-display'); ?>
                                        </div>

                                        <div id="articles-setting" style="display: none;" class="settings-tab">
                                            <div class="settings-head">
                                                <p class="head">
                                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_1833_2786)">
                                                            <path
                                                                d="M1.40756 7.95297C1.35894 7.822 1.35894 7.67793 1.40756 7.54697C1.88105 6.39888 2.68477 5.41724 3.71684 4.72649C4.7489 4.03574 5.96283 3.66699 7.20472 3.66699C8.44661 3.66699 9.66054 4.03574 10.6926 4.72649C11.7247 5.41724 12.5284 6.39888 13.0019 7.54697C13.0505 7.67793 13.0505 7.822 13.0019 7.95297C12.5284 9.10105 11.7247 10.0827 10.6926 10.7734C9.66054 11.4642 8.44661 11.8329 7.20472 11.8329C5.96283 11.8329 4.7489 11.4642 3.71684 10.7734C2.68477 10.0827 1.88105 9.10105 1.40756 7.95297Z"
                                                                stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                            <path
                                                                d="M7.20312 9.5C8.16962 9.5 8.95312 8.7165 8.95312 7.75C8.95312 6.7835 8.16962 6 7.20312 6C6.23663 6 5.45312 6.7835 5.45312 7.75C5.45312 8.7165 6.23663 9.5 7.20312 9.5Z"
                                                                stroke="black" stroke-width="1.16667" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1833_2786">
                                                                <rect width="14" height="14" fill="white"
                                                                    transform="translate(0.203125 0.75)"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                    Setting
                                                </p>
                                                <p class="des mb-0">The Articles Settings allow you to adjust certain features on
                                                    the articles, such as the panel title or the Articles Landing Page URL.</p>
                                            </div>

                                            <?php sb_populate_settings("articles", $sb_settings, true, 'articles-settings'); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                sb_apps_area(
                                    $apps,
                                    $cloud_active_apps
                                );

                                ?>
                                <?php /*<div>
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
                                    </div>*/ ?>
                            <?php /*
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
                                </div>*/ ?>
                            </div>
                        </div>
                    </div>
                    <script>
                        $('.my-tab').click(function () {
                            $(this).siblings().removeClass('active');
                            $(this).addClass('active');
                            $(this).parent().parent().parent().find('.settings-tab').hide();
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
                '<div class="' . $apps[$i][1] . '">
                    <div class="sb-top-bar save_settings settings-header">
                        <div class="">
                            <p class="head mb-4">Tickets Settings</p>
                            <p class="des mb-0">The Tickets Settings allow you to adjust the ticket features available such as adding ticket status, disabling certain details from the tickets tab, and setting default departments for new tickets.</p>
                        </div>
                        
                                        <div class="d-flex align-items-center">
                                            <div class="sb-search-dropdown">
                                                <div class="sb-search-btn">
                                                    <i class="sb-icon sb-icon-search"></i>
                                                    <input id="sb-search-settings" type="text" autocomplete="false"
                                                        placeholder="Search ..." />
                                                </div>
                                                <div class="sb-search-dropdown-items"></div>
                                            </div>

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
    $code .= '<div class="apps-div"><div class="sb-apps">';
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
            ? '<i class="sb-icon sb-icon-tag-line sb-filter-star" title="Filter chat with tags" data-color-text="' .
            $tags[0]["tag-color"] .
            '" data-value="' .
            $tags[0]["tag-name"] .
            '"></i>'
            : "") .
        '<div class="sb-filter-btn"><svg class="toggle-filter" data-sb-tooltip="Filters" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M20.7801 8.25167L18.2506 5.72294L18.2673 14.5714C18.2673 15.0452 17.8839 15.4286 17.4101 15.4286C16.938 15.4286 16.553 15.0452 16.553 14.5714L16.5363 5.7832L14.0687 8.25167C13.7338 8.58566 13.1914 8.58566 12.8566 8.25167C12.5218 7.91685 12.5218 7.37361 12.8566 7.03878L16.3839 3.51144C16.6183 3.2009 16.9916 3 17.4101 3C17.7366 3 18.0329 3.12053 18.2589 3.31976C18.2924 3.34486 18.3242 3.37249 18.3544 3.40263L21.9922 7.03878C22.327 7.37361 22.327 7.91685 21.9922 8.25167C21.6574 8.58566 21.115 8.58566 20.7801 8.25167Z" fill="black"/>
<path d="M1.71484 6.25708C1.71484 5.87873 2.0212 5.57153 2.40123 5.57153H9.1713C9.33536 5.57153 9.48604 5.62846 9.60322 5.72387C9.75896 5.84944 9.8577 6.04196 9.8577 6.25708V6.60028C9.8577 6.97862 9.55136 7.28582 9.1713 7.28582H2.40123C2.20201 7.28582 2.02288 7.20211 1.89732 7.06735C1.82701 6.99285 1.77511 6.90244 1.74498 6.802C1.72489 6.73839 1.71484 6.67059 1.71484 6.60028V6.25708Z" fill="black"/>
<path d="M2.14844 12.477C2.08315 12.503 2.02288 12.5381 1.96931 12.5816C1.81361 12.7072 1.71484 12.8989 1.71484 13.114V13.4572C1.71484 13.8356 2.0212 14.1428 2.40123 14.1428H13.0284C13.4085 14.1428 13.7148 13.8356 13.7148 13.4572V13.114C13.7148 12.7357 13.4085 12.4285 13.0284 12.4285H2.40123C2.3125 12.4285 2.22712 12.446 2.14844 12.477Z" fill="black"/>
<path d="M1.77846 19.684C1.73828 19.7711 1.71484 19.8682 1.71484 19.9712V20.3144C1.71484 20.5329 1.81696 20.7279 1.97601 20.8534C2.0932 20.9455 2.24051 20.9999 2.40123 20.9999H20.8092C21.1892 20.9999 21.4956 20.6927 21.4956 20.3144V19.9712C21.4956 19.5928 21.1892 19.2856 20.8092 19.2856H2.40123C2.125 19.2856 1.88728 19.4488 1.77846 19.684Z" fill="black"/>
</svg>
<div style="padding-right: 42px;"><div class="sb-select' .
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

                            <div id="add_to_frontend_form" data-type="checkbox" class="sb-input sb-input-checkbox" style="display:none">
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