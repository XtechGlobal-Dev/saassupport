<?php

/*
 * ==========================================================
 * COMPONENTS.PHP
 * ==========================================================
 *
 * Library of static html components for the admin area. This file must not be executed directly. � 2017-2025 board.support. All rights reserved.
 *
 */
function sb_profile_box() { ?>
    <div class="sb-profile-box sb-lightbox">
        <div class="sb-top-bar">
            <div class="sb-profile">
                <img src="<?php echo SB_URL ?>/media/user.svg" />
                <span class="sb-name"></span>
            </div>
            <div>
                <a data-value="custom_email" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send email') ?>">
                    <i class="sb-icon-envelope"></i>
                </a>
                <?php
                if (sb_get_multi_setting('sms', 'sms-user')) {
                    echo '<a data-value="sms" class="sb-btn-icon" data-sb-tooltip="' . sb_('Send text message') . '"><i class="sb-icon-sms"></i></a>';
                }
                if (defined('SB_WHATSAPP') && (!function_exists('sb_whatsapp_active') || sb_whatsapp_active())) {
                    echo '<a data-value="whatsapp" class="sb-btn-icon" data-sb-tooltip="' . sb_('Send a WhatsApp message template') . '"><i class="sb-icon-social-wa"></i></a>'; // Deprecated: remove function_exists('sb_whatsapp_active')
                }
                if (((sb_is_agent(false, true, true) && !sb_supervisor()) || sb_get_multi_setting('agents', 'agents-edit-user')) || (sb_supervisor() && sb_get_multi_setting('supervisor', 'supervisor-edit-user'))) {
                    echo ' <a class="sb-edit sb-btn sb-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-edit-area"><i class="sb-icon-user"></i>' . sb_('Edit user') . '</a>';
                }
                ?>
                <a class="sb-start-conversation sb-btn sb-icon">
                    <i class="sb-icon-message"></i>
                    <?php sb_e('Start a conversation') ?>
                </a>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div>
                <div class="sb-title">
                    <?php sb_e('Details') ?>
                </div>
                <div class="sb-profile-list"></div>
                <div class="sb-agent-area"></div>
            </div>
            <div>
                <div class="sb-title">
                    <?php sb_e('User conversations') ?>
                </div>
                <ul class="sb-user-conversations"></ul>
            </div>
        </div>
    </div>
<?php } ?>
<?php
function sb_profile_edit_box() { ?>
    <div class="sb-profile-edit-box sb-lightbox">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div class="sb-profile">
                <img src="<?php echo SB_URL ?>/media/user.svg" />
                <span class="sb-name"></span>
            </div>
            <div>
                <a class="sb-save sb-btn sb-icon">
                    <i class="sb-icon-check"></i>
                    <?php sb_e('Save changes') ?>
                </a>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-details">
                <div class="sb-title">
                    <?php sb_e('Edit details') ?>
                </div>
                <div class="sb-edit-box">
                    <div id="profile_image" data-type="image" class="sb-input sb-input-image sb-profile-image">
                        <span>
                            <?php sb_e('Profile image') ?>
                        </span>
                        <div class="image">
                            <div class="sb-icon-close"></div>
                        </div>
                    </div>
                    <div id="user_type" data-type="select" class="sb-input sb-input-select">
                        <span>
                            <?php sb_e('Type') ?>
                        </span>
                        <select>
                            <option value="agent">
                                <?php sb_e('Agent') ?>
                            </option>
                            <option value="admin">
                                <?php sb_e('Admin') ?>
                            </option>
                        </select>
                    </div>
                    <?php sb_departments('select') ?>
                    <div id="first_name" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e('First name') ?>
                        </span>
                        <input type="text" required />
                    </div>
                    <div id="last_name" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e('Last name') ?>
                        </span>
                        <input type="text" />
                    </div>
                    <div id="password" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e('Password') ?>
                        </span>
                        <input type="password" />
                    </div>
                    <div id="email" data-type="email" class="sb-input">
                        <span>
                            <?php sb_e('Email') ?>
                        </span>
                        <input type="email" />
                    </div>
                </div>
                <a class="sb-delete sb-btn-text sb-btn-red">
                    <i class="sb-icon-delete"></i>
                    <?php sb_e('Delete user') ?>
                </a>
            </div>
            <div class="sb-additional-details">
                <div class="sb-title">
                    <?php sb_e('Edit additional details') ?>
                </div>
                <div class="sb-edit-box">
                    <?php
                    $code = '';
                    $fields = sb_users_get_fields();
                    foreach ($fields as $field) {
                        $id = $field['id'];
                        $type = $id == 'country' || $id == 'language' ? 'select' : ($id == 'birthdate' ? 'date' : 'text');
                        $code .= '<div id="' . $id . '" data-type="' . $type . '" class="sb-input"><span>' . sb_($field['name']) . '</span>';
                        if ($type == 'date' || $type == 'text') {
                            $code .= '<input type="' . $type . '" />';
                        } else if ($id == 'country') {
                            $code .= sb_select_html('countries');
                        } else if ($id == 'language') {
                            $code .= sb_select_html('languages');
                        }
                        $code .= '</div>';
                    }
                    echo $code;
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php }
function sb_ticket_box() { ?>
    <div class="sb-lightbox">
        <div class="sb-top-bar">
            
            <div>
                
                <?php
                if (((sb_is_agent(false, true, true) && !sb_supervisor()) || sb_get_multi_setting('agents', 'agents-edit-user')) || (sb_supervisor() && sb_get_multi_setting('supervisor', 'supervisor-edit-user'))) {
                    echo ' <a class="sb-edit sb-btn sb-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-edit-area"><i class="sb-icon-user"></i>' . sb_('Edit user') . '</a>';
                }
                ?>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div>
                <div class="sb-title">
                    <?php sb_e('Details') ?>
                </div>
                <div class="sb-ticket-list"></div>
            </div>
        </div>
    </div>
<?php }
function sb_ticket_edit_box() { ?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!--script src="https://cdn.quilljs.com/1.3.6/quill.js"></script-->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <div class="sb-ticket-edit-box sb-lightbox">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div class="sb-ticket">
                <span class="sb-name"></span>
            </div>
            <div>
                <a class="sb-save sb-btn sb-icon">
                    <i class="sb-icon-check"></i>
                    <?php sb_e('Save changes') ?>
                </a>
                <a class="sb-close sb-btn-icon sb-btn-red" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area" >
            <div class="first-section" style="display: flex;">
                <div class="sb-details">
                    <div class="sb-edit-box">
                        <div id="subject" data-type="text" class="sb-input">
                            <span class="required-label"><?php sb_e('Subject') ?></span>
                            <input type="text" name="subject" required />
                        </div>

                        <div id="without_contact" data-type="checkbox" class="sb-input">
                            <span><?php sb_e('Guest Ticket') ?></span>
                            <input type="checkbox" name="without_contact" value="1" />
                        </div>

                        <div id="contact_id" data-type="select" class="sb-input">
                            <span class="left-sec"><?php sb_e('Customer') ?></span>
                            <div class="right-sec">
                                <select id="select-customer" style="width:100%;" ></select>
                            </div>
                        </div>

                        <div id="cust_name" data-type="text" class="sb-input" >
                            <span class="required-label"><?php sb_e('Name') ?></span>
                            <input type="text" name="name" required value="" disabled />
                        </div>

                        <div id="cust_email" data-type="text" class="sb-input" >
                            <span class="required-label"><?php sb_e('Email') ?></span>
                            <input type="email" name="email" required value="" disabled />
                        </div>

                        <div id="assigned_to" data-type="select" class="sb-input">
                            <span class="left-sec"><?php sb_e('Assigned To') ?></span>
                            <div class="right-sec">
                                <select id="select-agent" style="width:100%;"></select>
                            </div>
                        </div>

                        <div id="priority_id" data-type="select" class="sb-input">
                            <span class="required-label"><?php sb_e('Priority') ?></span>
                            <select required>
                                <option value=""><?php sb_e('Select Priority') ?></option>
                                <option value="1" data-color="danger">Critical</option>
                                <option value="2" data-color="danger">High</option>
                                <option value="4" data-color="secondary">Low</option>
                                <option value="3" data-color="warning">Medium</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="sb-additional-details">
                    <div class="sb-edit-box">
                        <!--div id="service_id" data-type="select" class="sb-input">
                            <span><?php sb_e('Service') ?></span>
                            <select>
                                <option value=""><?php sb_e('Select Service') ?></option>
                                <option value="1" selected="selected">Hardware Issue Fixing</option>
                                <option value="2">Network issue fix</option>
                                <option value="3">Software Development</option>
                            </select>
                        </div-->

                        <?php
                        $departments = sb_get_departments();
                        $department_settings = sb_get_setting('departments-settings');
                        if (isset($department_settings['departments-show-list']) && $department_settings['departments-show-list'] == 1 && !empty($departments)) {
                        ?>
                        <div id="department_id" data-type="select" class="sb-input">
                            <span>Department</span>
                            <select>
                                <option value=""><?php echo sb_('Select Department');?></option>
                                <?php
                                $code = '';
                                foreach ($departments as $key => $value) {
                                    $code .= '<option value="' . $key . '">' . sb_($value['name']) . '</option>';
                                }
                                echo $code;
                                ?>
                            </select>
                        </div>
                        <?php } ?>
                        
                       <!--div id="cc" data-type="select" class="sb-input">
                            <span><?php sb_e('CC') ?></span>
                            <select>
                                <option value="1">System Admin</option>
                            </select>
                        </div-->

                        <?php 
                        $tags = sb_get_multi_setting('disable', 'disable-tags') ? [] : sb_get_setting('tags', []);
                        $tagsHtml = '';
                        $count = count($tags);
                        if ($count > 0) {
                        ?>
                        <div id="tags" data-type="select" class="sb-input">
                            <span><?php sb_e('Tags') ?></span>
                            <select>
                                <option value="" >Select Tag</option>
                                <?php
                                for ($i = 0; $i < $count; $i++) {
                                    $tagsHtml .= '<option value="' . $tags[$i]['tag-name'] . '">' . $tags[$i]['tag-name'] . '</option>';
                                }
                                echo $tagsHtml;
                                ?>
                            </select>
                        </div>
                        <?php } ?>
                        
                        <div id="status_id" data-type="select" class="sb-input">
                            <span class="required-label"><?php sb_e('Status') ?></span>
                            <select required>
                                <option value="">Select Status</option>
                                <option value="1">Open</option>
                                <option value="2">In Progress</option>
                                <option value="3">Hold</option>
                                <option value="4">Waiting for Customer Response</option>
                                <option value="5">Resolved</option>
                                <option value="6">Closed</option>
                            </select>
                        </div>
                        <!--div data-type="file" class="sb-input">
                            <span><?php sb_e('Attachments') ?></span>
                            <input type="file" name="attachments[]" multiple />
                            <div id="file-preview"></div>
                        </div-->
                    </div>
                </div>
            </div>
            <div id="description" class="description sb-input" data-type="textarea" style="margin: 10px 0 0 0;display: block;">
                <div style="width:15%;display: inline-block;padding:0 4px 0 0;vertical-align: top;">
                    <span style="font-weight: 600;font-size: 14px;line-height: 25px;color: #566069;"><?php sb_e('Description') ?></span></div>
                <div style="width:84%;display: inline-block;padding:0">
                    <div id="ticketdescription" style="height: 180px;"></div>
                </div>
                <input id="ticket_id" type="hidden" name="ticket_id" />
                <input id="conversation_id" type="hidden" name="conversation_id" />
                <!-- Hidden input to store uploaded file data -->
                <input type="hidden" id="uploaded_files" name="uploaded_files" value="">
            </div>
            <div id="ticketCustomFieldsContainer" style="margin: 10px 0 0 0;"></div>
            <!-- File Attachments Section -->
            <div id="ticketFileAttachments" style="margin: 10px 0 0 0;">
                <div class="sb-input">
                    <span >Attachments</span>
                    <div class="custom-file">
                        <input type="file" class="form-control" id="ticket-attachments" multiple>
                        <small class="form-text text-muted mt-2" style="display:block">You can select multiple files. Maximum file size: 10MB</small>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <!-- Upload Progress -->
                <div class="progress mt-2 d-none" id="upload-progress-container">
                    <div class="progress-bar" id="upload-progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                
                <!-- Existing File Preview Container -->
                <div class="mt-2 d-none" id="existing-file-preview-container">
                    <span>Current Attachments</span>
                    <div class="row" id="current-attachments"></div>
                </div>

                <!-- File Preview Container -->
                 <div class="mt-2">
                    <span>New Attachments</span>
                    <div class="mt-2" id="file-preview-container">
                        <div class="row" id="file-preview-list"></div>
                    </div>
                 </div>
                
            </div>
        </div>
    </div>
    <style>
    #ticketCustomFieldsContainer, #ticketFileAttachments, .first-section {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    #ticketCustomFieldsContainer > .sb-input, #ticketFileAttachments > div, .first-section > div {
        flex: 0 0 calc(50% - 10px); /* 2 columns with spacing */
        box-sizing: border-box;
    }
    #ticketCustomFieldsContainer .sb-input{margin-top:0}
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
        padding: 6px 12px; /* Increase horizontal padding */
        text-align: center;
    }

    #tickets-custom-fields .sb-new-ticket-custom-field{
        height: 30px;
        line-height: 30px;
        padding: 0 8px 0 25px;
        margin-left: 13px;
    }
    .sb-table-tickets tr {line-height: 25px;}
    span.left-sec {width: 15%;}
    div.right-sec {width: 84%;padding: 0;}
    
    #file-preview-list .col-md-2,#current-attachments .col-md-2  {padding:0}
    #file-preview-list .card, #current-attachments .card{margin: 6px;height: 100%;}
    #file-preview-list .card-body, #current-attachments .card-body{display: flex;
    flex-direction: column;
    justify-content: center;
    /* align-items: center; */
    height: 100%;
    }

    .custom-file #ticket-attachments {font-size: 12px;}
    </style>
    <script>
        $('#select-customer').select2({
            placeholder: 'Type and search...',
            ajax: {
            url: 'http://localhost/saassupport/script/include/ajax.php',  // Your endpoint
            method: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                    return {
                        function: 'ajax_calls',
                        'calls[0][function]': 'search-get-users',
                        'login-cookie': SBF.loginCookie(),
                        'q': params.term,   // ✅ Pass search term
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

        
        $('#select-customer').on('select2:select', function (e) {
            const selectedCustomer = e.params.data;

            console.log(selectedCustomer);
            // Now fill other input fields
            document.querySelector('#cust_name input').value = selectedCustomer.name;
            document.querySelector('#cust_email input').value = selectedCustomer.email;
            //$('#user-name').val(selectedCustomer.first_name + ' ' + selectedUser.last_name);
        });
        
        $('#select-agent').select2({
            placeholder: 'Type and search...',
            ajax: {
            url: 'http://localhost/saassupport/script/include/ajax.php',  // Your endpoint
            method: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                    return {
                        function: 'ajax_calls',
                        'calls[0][function]': 'search-get-users',
                        'login-cookie': SBF.loginCookie(),
                        'q': params.term,   // ✅ Pass search term
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
    </script>
    <!-- Include Bootstrap JS and dependencies -->
    <!--script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- File Upload Handling -->
    <script>
        jQuery(document).ready(function($) {

        $('#without_contact input').on('click',function() {
            const isChecked = $(this).is(':checked');
            $('#cust_name input, #cust_email input').prop('disabled', !isChecked);
            if (isChecked) {
                $('#contact_id').hide();
                $('#select-customer').removeAttr('required');
            } else {
                 $('#contact_id').show();
                $('#select-customer').attr('required');
                // Optionally, you can set focus back to the name field
                $('#cust_name input').focus();
            }
        });

       
        // Array to store uploaded files
        let uploadedFiles = [];
        
        // File upload handling
            document.getElementById('ticket-attachments').addEventListener('change', function(event) {
            const files = event.target.files;
            if (files.length === 0) return;
            
            // Create FormData object
            const formData = new FormData();
            uploadedFiles = [];
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

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
            xhr.open('POST', 'http://localhost/saassupport/script/include/ajax.php', true);
            
            // Track upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressBar.setAttribute('aria-valuenow', percentComplete);
                    progressBar.textContent = percentComplete + '%';
                }
            });
            
            // Handle response
            xhr.onload = function() {
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
            xhr.onerror = function() {
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
 
        
        // Function to display file previews
        function displayFilePreviews(files) {
            const previewList = document.getElementById('file-preview-list');
            
            files.forEach(file => {
                const col = document.createElement('div');
                col.className = 'col-md-2 mb-2';
                
                const card = document.createElement('div');
                card.className = 'card';
                
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-2';
                
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
                        <div class="text-center mb-2">
                            <img src="${file.file_path}" class="img-thumbnail" style="max-height: 100px;" alt="${file.original_filename}">
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 text-truncate">
                                <div class="text-truncate">${file.original_filename}</div>
                                <small class="text-muted">${formatFileSize(file.file_size)}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-file" data-index="${uploadedFiles.indexOf(file)}">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                }
                
                cardBody.innerHTML = previewContent;
                card.appendChild(cardBody);
                col.appendChild(card);
                previewList.appendChild(col);
                
                // Add event listener to remove button
                const removeBtn = cardBody.querySelector('.remove-file');
                removeBtn.addEventListener('click', function() {
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
            $(document).on('click','.delete-attachment', function() {
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
                    xhr.open('POST', 'http://localhost/saassupport/script/include/ajax.php', true);
                    
                    // Handle response
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Delete response:', response);
                            if (response[0][1].success) {
                                // Remove the attachment from the DOM
                                const card = self.closest('.col-md-2');
                                card.remove();

                                // Hide Current Attachments section if no attachments left
                                if($('#current-attachments').children().length === 0) {
                                    $('#existing-file-preview-container').addClass('d-none');
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
<?php } ?>
<?php
function sb_login_box() { ?>
    <form class="sb sb-rich-login sb-admin-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <img src="<?php echo sb_get_setting('login-icon', SB_URL . '/media/logo.svg') ?>" />
            <div class="sb-title">
                <?php sb_e('Sign In') ?>
            </div>
            <div class="sb-text">
                <?php echo sb_sanatize_string(sb_get_setting('login-message', defined('SB_WP') ? sb_('Please insert email and password of your WordPress account') : sb_('Enter your login details below'))) ?>
            </div>
        </div>
        <div class="sb-main">
            <div id="email" class="sb-input">
                <span>
                    <?php sb_e('Email') ?>
                </span>
                <input type="text" />
            </div>
            <div id="password" class="sb-input">
                <span>
                    <?php sb_e('Password') ?>
                </span>
                <input type="password" />
            </div>
            <div class="sb-bottom">
                <div class="sb-btn sb-submit-login">
                    <?php sb_e('Login') ?>
                </div>
            </div>
        </div>
    </form>
    <img id="sb-error-check" style="display:none" src="<?php echo SB_URL . '/media/logo.svg' ?>" />
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
<?php } ?>
<?php
function sb_dialog() { ?>
    <div class="sb-dialog-box sb-lightbox">
        <div class="sb-title"></div>
        <p></p>
        <div>
            <a class="sb-confirm sb-btn">
                <?php sb_e('Confirm') ?>
            </a>
            <a class="sb-cancel sb-btn sb-btn-red">
                <?php sb_e('Cancel') ?>
            </a>
            <a class="sb-close sb-btn">
                <?php sb_e('Close') ?>
            </a>
        </div>
    </div>
<?php } ?>
<?php
function sb_updates_box() { ?>
    <div class="sb-lightbox sb-updates-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div>
                <?php sb_e('Update center') ?>
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
                    <?php sb_e('Update now') ?>
                </a>
                <a href="https://board.support/changes" target="_blank" class="sb-btn-text">
                    <i class="sb-icon-clock"></i>
                    <?php sb_e('Change Log') ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<?php
function sb_app_box() { ?>
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
                <?php sb_e('License key') ?>
            </div>
            <div class="sb-setting sb-type-text">
                <input type="text" required />
            </div>
            <div class="sb-bottom">
                <a class="sb-btn sb-icon sb-btn-app-setting">
                    <i class="sb-icon-settings"></i>
                    <?php sb_e('Settings') ?>
                </a>
                <a class="sb-activate sb-btn sb-icon">
                    <i class="sb-icon-check"></i>
                    <?php sb_e('Activate') ?>
                </a>
                <a class="sb-btn-red sb-btn sb-icon sb-btn-app-disable">
                    <i class="sb-icon-close"></i>
                    <?php sb_e('Disable') ?>
                </a>
                <a class="sb-btn sb-icon sb-btn-app-puchase" target="_blank" href="#">
                    <i class="sb-icon-plane"></i>
                    <?php sb_e('Purchase license') ?>
                </a>
                <a class="sb-btn-text sb-btn-app-details" target="_blank" href="#">
                    <i class="sb-icon-help"></i>
                    <?php sb_e('Read more') ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<?php
function sb_direct_message_box() { ?>
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
                <?php sb_e('User IDs') ?>
            </div>
            <div class="sb-setting sb-type-text sb-first">
                <input class="sb-direct-message-users" type="text" placeholder="<?php sb_e('User IDs separated by commas') ?>" required />
            </div>
            <div class="sb-title sb-direct-message-subject">
                <?php sb_e('Subject') ?>
            </div>
            <div class="sb-setting sb-type-text sb-direct-message-subject">
                <input type="text" placeholder="<?php sb_e('Email subject') ?>" />
            </div>
            <div class="sb-title sb-direct-message-title-subject">
                <?php sb_e('Message') ?>
            </div>
            <div class="sb-setting sb-type-textarea">
                <textarea placeholder="<?php sb_e('Write here your message...') ?>" required></textarea>
            </div>
            <div class="sb-bottom">
                <a class="sb-send-direct-message sb-btn sb-icon">
                    <i class="sb-icon-plane"></i>
                    <?php sb_e('Send message now') ?>
                </a>
                <div></div>
                <?php sb_docs_link('#direct-messages', 'sb-btn-text') ?>
            </div>
        </div>
    </div>
<?php } ?>
<?php
function sb_routing_select($exclude_id = false) {
    $agents = sb_db_get('SELECT id, first_name, last_name FROM sb_users WHERE (user_type = "agent" OR user_type = "admin")' . ($exclude_id ? (' AND id <> ' . sb_db_escape($exclude_id)) : ''), false);
    $code = '<div class="sb-inline sb-inline-agents"><h3>' . sb_('Agent') . '</h3><div id="conversation-agent" class="sb-select"><p>' . sb_('None') . '</p><ul><li data-id="" data-value="">' . sb_('None') . '</li>';
    for ($i = 0; $i < count($agents); $i++) {
        $code .= '<li data-id="' . $agents[$i]['id'] . '">' . $agents[$i]['first_name'] . ' ' . $agents[$i]['last_name'] . '</li>';
    }
    echo $code . '</ul></div></div>';
}
?>
<?php
function sb_installation_box($error = false) {
    global $SB_LANGUAGE;
    $SB_LANGUAGE = isset($_GET['lang']) ? $_GET['lang'] : strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    ?>
    <div class="sb-main sb-admin sb-admin-start">
        <form class="sb-intall sb-admin-box">
            <?php if ($error === false || $error == 'installation')
                echo '<div class="sb-info"></div>';
            else
                die('<div class="sb-info sb-active">' . sb_('We\'re having trouble connecting to your database. Please edit the file config.php and check your database connection details. Error: ') . $error . '.</div>'); ?>
            <div class="sb-top-bar">
                <img src="<?php echo (!SB_URL || SB_URL == '[url]' ? '' : SB_URL . '/') ?>media/logo.svg" />
                <div class="sb-title">
                    <?php sb_e('Installation') ?>
                </div>
                <div class="sb-text">
                    <?php sb_e('Please complete the installation process by entering your database connection details below. If you are not sure about this, contact your hosting provider for support.') ?>
                </div>
            </div>
            <div class="sb-main">
                <div id="db-name" class="sb-input">
                    <span>
                        <?php sb_e('Database Name') ?>
                    </span>
                    <input type="text" required />
                </div>
                <div id="db-user" class="sb-input">
                    <span>
                        <?php sb_e('Username') ?>
                    </span>
                    <input type="text" required />
                </div>
                <div id="db-password" class="sb-input">
                    <span>
                        <?php sb_e('Password') ?>
                    </span>
                    <input type="text" />
                </div>
                <div id="db-host" class="sb-input">
                    <span>
                        <?php sb_e('Host') ?>
                    </span>
                    <input type="text" required />
                </div>
                <div id="db-port" class="sb-input">
                    <span>
                        <?php sb_e('Port') ?>
                    </span>
                    <input type="text" placeholder="Default" />
                </div>
                <?php if ($error === false || $error == 'installation') { ?>
                    <div class="sb-text">
                        <?php sb_e('Enter the user details of the main account you will use to login into the administration area. You can update these details later.') ?>
                    </div>
                    <div id="first-name" class="sb-input">
                        <span>
                            <?php sb_e('First name') ?>
                        </span>
                        <input type="text" required />
                    </div>
                    <div id="last-name" class="sb-input">
                        <span>
                            <?php sb_e('Last name') ?>
                        </span>
                        <input type="text" required />
                    </div>
                    <div id="email" class="sb-input">
                        <span>
                            <?php sb_e('Email') ?>
                        </span>
                        <input type="email" required />
                    </div>
                    <div id="password" class="sb-input">
                        <span>
                            <?php sb_e('Password') ?>
                        </span>
                        <input type="password" required />
                    </div>
                    <div id="password-check" class="sb-input">
                        <span>
                            <?php sb_e('Repeat password') ?>
                        </span>
                        <input type="password" required />
                    </div>
                <?php } ?>
                <div class="sb-bottom">
                    <div class="sb-btn sb-submit-installation">
                        <?php sb_e('Complete installation') ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } ?>
<?php
/*
 * ----------------------------------------------------------
 * ADMIN AREA
 * ----------------------------------------------------------
 *
 * Display the administration area
 *
 */

function sb_component_admin() { 
    $is_cloud = sb_is_cloud();
    $cloud_active_apps = $is_cloud ? sb_get_external_setting('active_apps', []) : [];
    $sb_settings = sb_get_json_resource('json/settings.json');
    $active_user = sb_get_active_user(false, true);
    $collapse = sb_get_setting('collapse') ? ' sb-collapse' : '';
    $apps = [
        ['SB_WP', 'wordpress', 'WordPress'],
        ['SB_DIALOGFLOW', 'dialogflow', 'Artificial Intelligence', 'Connect smart chatbots and automate conversations by using one of the most advanced forms of artificial intelligence in the world.'],
        ['SB_TICKETS', 'tickets', 'Tickets', 'Provide help desk support to your customers by including a ticket area, with all chat features included, on any web page in seconds.'],
        ['SB_MESSENGER', 'messenger', 'Messenger', 'Read, manage and reply to all messages sent to your Facebook pages and Instagram accounts directly from {R}.'],
        ['SB_WHATSAPP', 'whatsapp', 'WhatsApp', 'Lets your users reach you via WhatsApp. Read and reply to all messages sent to your WhatsApp Business account directly from {R}.'],
        ['SB_TWITTER', 'twitter', 'Twitter', 'Lets your users reach you via Twitter. Read and reply to messages sent to your Twitter account directly from {R}.'],
        ['SB_TELEGRAM', 'telegram', 'Telegram', 'Connect your Telegram bot to {R} to read and reply to all messages sent to your Telegram bot directly in {R}.'],
        ['SB_VIBER', 'viber', 'Viber', 'Connect your Viber bot to {R} to read and reply to all messages sent to your Viber bot directly in {R}.'],
        ['SB_LINE', 'line', 'Line', 'Connect your LINE bot to {R} to read and reply to all messages sent to your LINE bot directly in {R}.'],
        ['SB_WECHAT', 'wechat', 'WeChat', 'Lets your users reach you via WeChat. Read and reply to all messages sent to your WeChat official account directly from {R}.'],
        ['SB_ZALO', 'zalo', 'Zalo', 'Connect your Zalo Official Account to {R} to read and reply to all messages sent to your Zalo Official Account directly in {R}.'],
        ['SB_WOOCOMMERCE', 'woocommerce', 'WooCommerce', 'Increase sales, provide better support, and faster solutions, by integrating WooCommerce with {R}.'],
        ['SB_SLACK', 'slack', 'Slack', 'Communicate with your users right from Slack. Send and receive messages and attachments, use emojis, and much more.'],
        ['SB_ZENDESK', 'zendesk', 'Zendesk', 'Automatically sync Zendesk customers with {R}, view Zendesk tickets, or create new ones without leaving {R}.'],
        ['SB_UMP', 'ump', 'Ultimate Membership Pro', 'Enable ticket and chat support for subscribers only, view member profile details and subscription details in the admin area.'],
        ['SB_PERFEX', 'perfex', 'Perfex', 'Synchronize your Perfex customers in real-time and let them contact you via chat! View profile details, proactively engage them, and more.'],
        ['SB_WHMCS', 'whmcs', 'Whmcs', 'Synchronize your customers in real-time, chat with them and boost their engagement, or provide a better and faster support.'],
        ['SB_OPENCART', 'opencart', 'OpenCart', 'Integrate OpenCart with {R} for real-time syncing of customers, order history access, and customer cart visibility.'],
        ['SB_AECOMMERCE', 'aecommerce', 'Active eCommerce', 'Increase sales and connect you and sellers with customers in real-time by integrating Active eCommerce with {R}.'],
        ['SB_ARMEMBER', 'armember', 'ARMember', 'Synchronize customers, enable ticket and chat support for subscribers only, view subscription plans in the admin area.'],
        ['SB_MARTFURY', 'martfury', 'Martfury', 'Increase sales and connect you and sellers with customers in real-time by integrating Martfury with {R}.'],
    ];



    $logged = $active_user && sb_is_agent($active_user) && (!defined('SB_WP') || !sb_get_setting('wp-force-logout') || sb_wp_verify_admin_login());
    $supervisor = sb_supervisor();
    $is_admin = $active_user && sb_is_agent($active_user, true, true) && !$supervisor;
    $sms = sb_get_multi_setting('sms', 'sms-user');
    $css_class = ($logged ? 'sb-admin' : 'sb-admin-start') . (sb_get_setting('rtl-admin') || ($is_cloud && defined('SB_CLOUD_DEFAULT_RTL')) ? ' sb-rtl' : '') . ($is_cloud ? ' sb-cloud' : '') . ($supervisor ? ' sb-supervisor' : '');
    $active_areas = [
    'users' => $is_admin || (!$supervisor && sb_get_multi_setting('agents','agents-users-area')) || ($supervisor && $supervisor['supervisor-users-area']), 
    'settings' => $is_admin || ($supervisor && $supervisor['supervisor-settings-area']), 
    'reports' => ($is_admin && !sb_get_multi_setting('performance', 'performance-reports')) || ($supervisor && $supervisor['supervisor-reports-area']), 
    'articles' => ($is_admin && !sb_get_multi_setting('performance', 'performance-articles')) || ($supervisor && sb_isset($supervisor, 'supervisor-articles-area')) || (!$supervisor && !$is_admin && sb_get_multi_setting('agents', 'agents-articles-area')), 
    'chatbot' => defined('SB_DIALOGFLOW') && ($is_admin || ($supervisor && $supervisor['supervisor-settings-area'])) && (!$is_cloud || in_array('dialogflow', $cloud_active_apps)) ,
    'tickets' => $is_admin,
    'dashboard' => $is_admin,
];
    
    
    $disable_translations = sb_get_setting('admin-disable-settings-translations');
    
    $admin_colors = [sb_get_setting('color-admin-1'), sb_get_setting('color-admin-2')];
    if ($supervisor && !$supervisor['supervisor-send-message']) {
        echo '<style>.sb-board .sb-conversation .sb-editor,#sb-start-conversation,.sb-top-bar [data-value="sms"],.sb-top-bar [data-value="email"],.sb-menu-users [data-value="message"],.sb-menu-users [data-value="sms"],.sb-menu-users [data-value="email"] { display: none !important; }</style>';
    }
    if ($is_cloud) {
        require_once(SB_CLOUD_PATH . '/account/functions.php');
        $sb_settings = sb_cloud_merge_settings($sb_settings);
        cloud_custom_code();
    } else if (!sb_box_ve()) {
        return;
    }
    if ($admin_colors[0]) {
        $css = '.sb-menu-wide ul li.sb-active, .sb-tab > .sb-nav > ul li.sb-active,.sb-table input[type="checkbox"]:checked, .sb-table input[type="checkbox"]:hover { border-color: ' . $admin_colors[0] . '; }';
        $css .= '.sb-board > .sb-admin-list .sb-scroll-area li.sb-active,.sb-user-conversations > li.sb-active { border-left-color: ' . $admin_colors[0] . '; }';
        $css .= '.sb-setting input:focus, .sb-setting select:focus, .sb-setting textarea:focus, .sb-setting input:focus, .sb-setting select:focus, .sb-setting textarea:focus,.sb-setting.sb-type-upload-image .image:hover, .sb-setting [data-type="upload-image"] .image:hover, .sb-setting.sb-type-upload-image .image:hover, .sb-setting [data-type="upload-image"] .image:hover,.sb-input > input:focus, .sb-input > input.sb-focus, .sb-input > select:focus, .sb-input > select.sb-focus, .sb-input > textarea:focus, .sb-input > textarea.sb-focus,.sb-search-btn > input,.sb-search-btn > input:focus { border-color: ' . $admin_colors[0] . '; box-shadow: 0 0 5px rgb(108 108 108 / 20%);}';
        $css .= '.sb-menu-wide ul li.sb-active, .sb-menu-wide ul li:hover, .sb-tab > .sb-nav > ul li.sb-active, .sb-tab > .sb-nav > ul li:hover,.sb-admin > .sb-header > .sb-admin-nav > div > a:hover, .sb-admin > .sb-header > .sb-admin-nav > div > a.sb-active,.sb-setting input[type="checkbox"]:checked:before, .sb-setting input[type="checkbox"]:checked:before,.sb-language-switcher > i:hover,.sb-admin > .sb-header > .sb-admin-nav-right .sb-account .sb-menu li:hover, .sb-admin > .sb-header > .sb-admin-nav-right .sb-account .sb-menu li.sb-active:hover,.sb-admin > .sb-header > .sb-admin-nav-right > div > a:hover,.sb-search-btn i:hover, .sb-search-btn.sb-active i, .sb-filter-btn i:hover, .sb-filter-btn.sb-active i,.sb-loading:before,.sb-board .sb-conversation > .sb-top a:hover i,.sb-panel-details > i:hover,.sb-board .sb-conversation > .sb-top > a:hover,.sb-btn-text:hover,.sb-table input[type="checkbox"]:checked:before,.sb-profile-list [data-id="wp-id"]:hover, .sb-profile-list [data-id="wp-id"]:hover label, .sb-profile-list [data-id="conversation-source"]:hover, .sb-profile-list [data-id="conversation-source"]:hover label, .sb-profile-list [data-id="location"]:hover, .sb-profile-list [data-id="location"]:hover label, .sb-profile-list [data-id="timezone"]:hover, .sb-profile-list [data-id="timezone"]:hover label, .sb-profile-list [data-id="current_url"]:hover, .sb-profile-list [data-id="current_url"]:hover label, .sb-profile-list [data-id="envato-purchase-code"]:hover, .sb-profile-list [data-id="envato-purchase-code"]:hover label,.sb-board > .sb-admin-list .sb-scroll-area li[data-conversation-status="2"] .sb-time,.sb-select p:hover,div ul.sb-menu li.sb-active:not(:hover), .sb-select ul li.sb-active:not(:hover),.sb-board .sb-conversation .sb-list > div .sb-menu-btn:hover { color: ' . $admin_colors[0] . '; }';
        $css .= '.sb-btn:not(.sb-btn-white), a.sb-btn:not(.sb-btn-white),.sb-area-settings .sb-tab .sb-btn:hover, .daterangepicker td.active, .daterangepicker td.active:hover, .daterangepicker .ranges li.active,div ul.sb-menu li:hover, .sb-select ul li:hover,div.sb-select.sb-select-colors > p:hover,.sb-board > .sb-admin-list .sb-scroll-area li > .sb-notification-counter { background-color: ' . $admin_colors[0] . '; }';
        $css .= '.sb-board > .sb-admin-list.sb-departments-show li.sb-active:before { background-color: ' . $admin_colors[0] . ' !important;}';
        $css .= '.sb-btn-icon:hover,.sb-tags-cnt > span:hover { border-color: ' . $admin_colors[0] . '; color: ' . $admin_colors[0] . '; }';
        $css .= '.sb-btn-icon:hover,.daterangepicker td.in-range { background-color: rgb(151 151 151 / 8%); }';
        $css .= '.sb-board .sb-user-details,.sb-admin > .sb-header,.sb-select.sb-select-colors > p:not([data-value]),.sb-table tr:hover td,.sb-board .sb-user-details .sb-user-conversations li:hover, .sb-board .sb-user-details .sb-user-conversations li.sb-active, .sb-select.sb-select-colors > p[data-value=""], .sb-select.sb-select-colors > p[data-value="-1"] {background-color: #f5f5f5  }';
        $css .= '.sb-board > .sb-admin-list .sb-scroll-area li:hover, .sb-board > .sb-admin-list .sb-scroll-area li.sb-active {background-color: #f5f5f5 !important; }';
        $css .= '.sb-profile-list > ul > li .sb-icon, .sb-profile-list > ul > li > img { color: #424242 }';
        $css .= '.sb-area-settings .sb-tab .sb-btn:hover, .sb-btn-white:hover, .sb-lightbox .sb-btn-white:hover { background-color: ' . $admin_colors[0] . '; border-color: ' . $admin_colors[0] . ';}';
        if ($admin_colors[1]) {
            $css .= '.sb-btn:hover, .sb-btn:active, a.sb-btn:hover, a.sb-btn:active { background-color: ' . $admin_colors[1] . '}';
        }
        echo '<style>' . $css . '</style>';
    }
    ?>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="sb-main <?php echo $css_class ?>" style="opacity: 0">
        <?php if ($logged) { ?>
            <div class="sb-header header_new">
                <aside class="sidebar sb-admin-nav">
                    <div class="logo">
                        <img width="35" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="Logo" class="logo-icon">
                        <div class="logo-text">
                            <h1>Nexon Helpdesk</h1>
                            <p>Agent Admin</p>
                        </div>
                    </div>
                    <div class="search-bar">
                        <input type="text" placeholder="Search">
                    </div>
                    <nav>
                        <ul>
                            <li class="sb-active"><a id="sb-dashboard"><i class="fa-solid fa-gauge"></i><span> Dashboard</span></a></li>
                            <li><a id="sb-conversations"><i class="fa-solid fa-inbox"></i><span> Inbox</span></a></li>
                            <li><a id="sb-tickets"><i class="fa-solid fa-ticket"></i><span> Tickets</span></a></li>
                            <li><a id="sb-users"><i class="fa-solid fa-users"></i><span> Customers</span></a></li>
                            <!-- <li><a id="sb-chatbot"><i class="fa-solid fa-robot"></i><span> Chatbot</span></a></li> -->
                            <li><a id="sb-articles"><i class="fa-solid fa-newspaper"></i><span> Articles</span></a></li>
                            <li><a id="sb-reports"><i class="fa-solid fa-flag"></i><span> Reports</span></a></li>
                            <li><a id="sb-settings"><i class="fa-solid fa-gear"></i><span> Settings</span></a></li>
                            <!-- <li><a href="#"><i class="fa-solid fa-circle-info"></i><span> Help & Support</span></a></li> -->
                        </ul>
                    </nav>
                    <div class="powered-by">
                        <p>POWERED BY</p>
                        <div class="powered-logo">
                           <svg width="108" height="32" viewBox="0 0 108 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M23.9784 6.60156V17.3942L16.9277 24.9963V14.2037L23.9784 6.61469V6.60156Z" fill="url(#paint0_linear_55_2458)"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M39.981 5.959L39.1539 6.82556L37.749 6.35289L38.5762 5.48633L39.981 5.959ZM36.9087 6.91747L36.0815 7.78403L34.6766 7.31136L35.5038 6.4448L36.9087 6.91747ZM41.0314 8.65059L39.8891 9.8454L37.9459 9.20204L39.0882 8.00724L41.0314 8.65059ZM37.9459 9.20204L36.3441 10.8695L33.6394 9.9767L32.1951 11.4866L34.913 12.3926L36.3572 10.8826L38.7337 11.6704L21.9802 29.6975L16.9121 24.9971L31.5255 9.26769L33.6131 9.96357L35.215 8.29609L37.9328 9.20204H37.9459Z" fill="url(#paint1_linear_55_2458)"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.62849 26.2964L2.4294 25.4036L1.85169 24.0381L1.05078 24.9309L1.62849 26.2964ZM2.36375 23.1584L3.16467 22.2656L2.58696 20.9001L1.78605 21.7929L2.36375 23.1584ZM4.38573 27.1498L5.48863 25.9288L4.70085 24.0381L3.59795 25.2592L4.38573 27.1498ZM4.70085 24.0381L6.25015 22.3181L5.14726 19.6922L6.53901 18.1428L7.64191 20.795L6.25015 22.3444L7.20862 24.6552L23.9753 6.60181L18.9072 1.90137L4.29382 17.6308L5.14726 19.6659L3.59795 21.3859L4.70085 24.0381Z" fill="url(#paint2_linear_55_2458)"/>
                                <path d="M50.382 8.46521V17.9473H49.2709L44.1039 10.5024H44.0113V17.9473H42.8631V8.46521H43.9742L49.1597 15.9286H49.2523V8.46521H50.382ZM52.6796 17.9473V8.46521H58.4022V9.48379H53.8278V12.6877H58.1059V13.7063H53.8278V16.9287H58.4763V17.9473H52.6796ZM61.0274 8.46521L63.4719 12.4099H63.546L65.9906 8.46521H67.3425L64.3609 13.2062L67.3425 17.9473H65.9906L63.546 14.0767H63.4719L61.0274 17.9473H59.6754L62.7312 13.2062L59.6754 8.46521H61.0274ZM76.6845 13.2062C76.6845 14.2063 76.504 15.0705 76.1428 15.799C75.7817 16.5274 75.2863 17.0892 74.6566 17.4843C74.027 17.8794 73.3078 18.0769 72.4991 18.0769C71.6904 18.0769 70.9712 17.8794 70.3416 17.4843C69.7119 17.0892 69.2165 16.5274 68.8554 15.799C68.4942 15.0705 68.3137 14.2063 68.3137 13.2062C68.3137 12.2062 68.4942 11.3419 68.8554 10.6135C69.2165 9.88505 69.7119 9.32329 70.3416 8.9282C70.9712 8.53312 71.6904 8.33557 72.4991 8.33557C73.3078 8.33557 74.027 8.53312 74.6566 8.9282C75.2863 9.32329 75.7817 9.88505 76.1428 10.6135C76.504 11.3419 76.6845 12.2062 76.6845 13.2062ZM75.5734 13.2062C75.5734 12.3852 75.436 11.6923 75.1613 11.1274C74.8897 10.5626 74.5208 10.1351 74.0548 9.84493C73.5918 9.55478 73.0732 9.40971 72.4991 9.40971C71.925 9.40971 71.4049 9.55478 70.9388 9.84493C70.4758 10.1351 70.107 10.5626 69.8323 11.1274C69.5607 11.6923 69.4248 12.3852 69.4248 13.2062C69.4248 14.0273 69.5607 14.7202 69.8323 15.2851C70.107 15.8499 70.4758 16.2774 70.9388 16.5676C71.4049 16.8577 71.925 17.0028 72.4991 17.0028C73.0732 17.0028 73.5918 16.8577 74.0548 16.5676C74.5208 16.2774 74.8897 15.8499 75.1613 15.2851C75.436 14.7202 75.5734 14.0273 75.5734 13.2062ZM86.1342 8.46521V17.9473H85.023L79.856 10.5024H79.7634V17.9473H78.6152V8.46521H79.7264L84.9119 15.9286H85.0045V8.46521H86.1342Z" fill="#111111"/>
                                <path d="M65.8856 23.0769V19.2955H66.3435V20.9794H68.3598V19.2955H68.8177V23.0769H68.3598V21.3856H66.3435V23.0769H65.8856ZM69.7344 23.0769V19.2955H72.0165V19.7017H70.1923V20.9794H71.8983V21.3856H70.1923V22.6707H72.046V23.0769H69.7344ZM72.8418 23.0769V19.2955H73.2997V22.6707H75.0575V23.0769H72.8418ZM75.7665 23.0769V19.2955H77.0442C77.3408 19.2955 77.5833 19.3491 77.7716 19.4562C77.9612 19.562 78.1015 19.7054 78.1926 19.8864C78.2837 20.0673 78.3292 20.2692 78.3292 20.492C78.3292 20.7148 78.2837 20.9173 78.1926 21.0994C78.1027 21.2816 77.9637 21.4269 77.7753 21.5352C77.587 21.6423 77.3457 21.6958 77.0515 21.6958H76.1357V21.2896H77.0368C77.2399 21.2896 77.403 21.2545 77.5261 21.1844C77.6492 21.1142 77.7384 21.0194 77.7938 20.9C77.8504 20.7794 77.8787 20.6434 77.8787 20.492C77.8787 20.3406 77.8504 20.2052 77.7938 20.0858C77.7384 19.9664 77.6485 19.8728 77.5242 19.8051C77.3999 19.7362 77.235 19.7017 77.0294 19.7017H76.2244V23.0769H75.7665ZM80.2338 23.0769H79.0669V19.2955H80.2855C80.6523 19.2955 80.9662 19.3712 81.2271 19.5226C81.4881 19.6728 81.6881 19.8888 81.8272 20.1707C81.9663 20.4514 82.0358 20.7874 82.0358 21.1788C82.0358 21.5727 81.9657 21.9118 81.8253 22.1962C81.685 22.4793 81.4807 22.6972 81.2123 22.8498C80.944 23.0012 80.6178 23.0769 80.2338 23.0769ZM79.5248 22.6707H80.2042C80.5169 22.6707 80.776 22.6104 80.9815 22.4898C81.1871 22.3691 81.3404 22.1974 81.4413 21.9746C81.5422 21.7518 81.5927 21.4866 81.5927 21.1788C81.5927 20.8736 81.5428 20.6108 81.4431 20.3904C81.3434 20.1689 81.1945 19.999 80.9963 19.8808C80.7981 19.7614 80.5513 19.7017 80.2559 19.7017H79.5248V22.6707ZM82.8039 23.0769V19.2955H85.086V19.7017H83.2618V20.9794H84.9679V21.3856H83.2618V22.6707H85.1156V23.0769H82.8039ZM87.9793 20.2409C87.9571 20.0538 87.8673 19.9085 87.7097 19.8051C87.5522 19.7017 87.3589 19.65 87.1299 19.65C86.9625 19.65 86.8161 19.6771 86.6905 19.7313C86.5662 19.7854 86.4689 19.8599 86.3988 19.9547C86.3299 20.0495 86.2954 20.1572 86.2954 20.2778C86.2954 20.3787 86.3194 20.4655 86.3674 20.5381C86.4166 20.6095 86.4794 20.6692 86.5557 20.7172C86.632 20.764 86.7121 20.8028 86.7958 20.8336C86.8795 20.8631 86.9564 20.8871 87.0266 20.9056L87.4106 21.009C87.5091 21.0348 87.6186 21.0705 87.7392 21.1161C87.8611 21.1616 87.9774 21.2238 88.0882 21.3025C88.2002 21.3801 88.2925 21.4798 88.3652 21.6017C88.4378 21.7235 88.4741 21.8731 88.4741 22.0503C88.4741 22.2547 88.4206 22.4393 88.3135 22.6042C88.2076 22.7692 88.0525 22.9003 87.8482 22.9975C87.6451 23.0948 87.3983 23.1434 87.1078 23.1434C86.837 23.1434 86.6025 23.0997 86.4043 23.0123C86.2074 22.9249 86.0523 22.803 85.939 22.6467C85.827 22.4904 85.7636 22.3088 85.7489 22.102H86.2215C86.2338 22.2448 86.2818 22.363 86.3656 22.4565C86.4505 22.5488 86.5576 22.6178 86.6868 22.6633C86.8173 22.7076 86.9576 22.7298 87.1078 22.7298C87.2826 22.7298 87.4395 22.7015 87.5786 22.6449C87.7177 22.587 87.8279 22.507 87.9091 22.4048C87.9904 22.3014 88.031 22.1808 88.031 22.0429C88.031 21.9174 87.9959 21.8152 87.9257 21.7364C87.8556 21.6577 87.7633 21.5937 87.6488 21.5444C87.5343 21.4952 87.4106 21.4521 87.2777 21.4152L86.8124 21.2822C86.517 21.1973 86.2831 21.0761 86.1108 20.9185C85.9384 20.7609 85.8523 20.5548 85.8523 20.3C85.8523 20.0882 85.9095 19.9036 86.024 19.7461C86.1397 19.5873 86.2948 19.4642 86.4893 19.3768C86.685 19.2882 86.9035 19.2438 87.1447 19.2438C87.3884 19.2438 87.6051 19.2875 87.7946 19.3749C87.9842 19.4611 88.1344 19.5793 88.2452 19.7294C88.3572 19.8796 88.4163 20.0501 88.4224 20.2409H87.9793ZM89.227 23.0769V19.2955H89.6849V21.1715H89.7292L91.4278 19.2955H92.0261L90.4382 21.0016L92.0261 23.0769H91.4721L90.1575 21.3192L89.6849 21.8509V23.0769H89.227Z" fill="#121212"/>
                                <defs>
                                <linearGradient id="paint0_linear_55_2458" x1="26.753" y1="21.7536" x2="11.8257" y2="7.88237" gradientUnits="userSpaceOnUse">
                                <stop offset="0.3" stop-color="#08CCF7"/>
                                <stop offset="0.4" stop-color="#0CA5F8"/>
                                <stop offset="0.7" stop-color="#1653FD"/>
                                </linearGradient>
                                <linearGradient id="paint1_linear_55_2458" x1="40.3163" y1="0.99443" x2="13.3333" y2="38.8232" gradientUnits="userSpaceOnUse">
                                <stop offset="0.4" stop-color="#08C8F7"/>
                                <stop offset="0.5" stop-color="#09BCF7"/>
                                <stop offset="0.6" stop-color="#0BA9F8"/>
                                <stop offset="0.7" stop-color="#136CFB"/>
                                <stop offset="0.8" stop-color="#1653FD"/>
                                </linearGradient>
                                <linearGradient id="paint2_linear_55_2458" x1="35.4801" y1="-4.9724" x2="0.507163" y2="23.8571" gradientUnits="userSpaceOnUse">
                                <stop offset="0.4" stop-color="#08C8F7"/>
                                <stop offset="0.5" stop-color="#09BCF7"/>
                                <stop offset="0.6" stop-color="#0BA9F8"/>
                                <stop offset="0.7" stop-color="#136CFB"/>
                                <stop offset="0.8" stop-color="#1653FD"/>
                                </linearGradient>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </aside>
                <!-- <div class="sb-admin-nav">
                        <a id="sb-conversations" class="sb-active">
                            <span>
                                <?php sb_e('Conversations') ?>
                            </span>
                        </a>
                        <?php
                        if ($active_areas['users']) {
                            echo '<a id="sb-users"><span>' . sb_('Users') . '</span></a>';
                        }
                        if ($active_areas['tickets']) {
                            echo '<a id="sb-tickets"><span>' . sb_('Tickets') . '</span></a>';
                        }
                        if ($active_areas['chatbot']) {
                            echo '<a id="sb-chatbot"><span>' . sb_('Chatbot') . '</span></a>'; 
                        }
                        if ($active_areas['articles']) {
                            echo '<a id="sb-articles"><span>' . sb_('Articles') . '</span></a>';
                        }
                        if ($active_areas['reports']) {
                            echo '<a id="sb-reports"><span>' . sb_('Reports') . '</span></a>';
                        }
                        if ($active_areas['settings']) {
                            echo '<a id="sb-settings"><span>' . sb_('Settings') . '</span></a>';
                        }
                        ?>
                </div> -->
                <!-- <div class="sb-admin-nav-right sb-menu-mobile">
                    <i class="sb-icon-menu"></i>
                    <div class="sb-desktop">
                        <div class="sb-account">
                            <img src="<?php echo SB_URL ?>/media/user.svg" />
                            <div>
                                <a class="sb-profile">
                                    <img src="<?php echo SB_URL ?>/media/user.svg" />
                                    <span class="sb-name"></span>
                                </a>
                                <ul class="sb-menu">
                                    <li data-value="status" class="sb-online">
                                        <?php sb_e('Online') ?>
                                    </li>
                                    <?php
                                    if ($is_admin) {
                                        echo '<li data-value="edit-profile">' . sb_('Edit profile') . '</li>' . ($is_cloud ? sb_cloud_account_menu() : '');
                                    }
                                    ?>
                                    <li data-value="logout">
                                        <?php sb_e('Logout') ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php
                        if ($is_admin) {
                            sb_docs_link();
                            echo '<a href="#" class="sb-version">' . SB_VERSION . '</a>';
                        }
                        ?>
                    </div>
                    <div class="sb-mobile">
                        <?php
                        if ($is_admin || (!$supervisor && sb_get_multi_setting('agents', 'agents-edit-user')) || ($supervisor && $supervisor['supervisor-edit-user'])) {
                            echo '<a href="#" class="edit-profile">' . sb_('Edit profile') . '</a>' . ($is_cloud ? sb_cloud_account_menu('a') : '<a href="#" class="sb-docs">' . sb_('Docs') . '</a>') . '<a href="#" class="sb-version">' . sb_('Updates') . '</a>';
                        }
                        ?>
                        <a href="#" class="sb-online" data-value="status">
                            <?php sb_e('Online') ?>
                        </a>
                        <a href="#" class="logout">
                            <?php sb_e('Logout') ?>
                        </a>
                    </div>
                </div> -->
            </div>
            <main>

                <!-- sahil start -->
                <div class="sb-active sb-area-dashboard">
                    <main>
                        <header>
                                <div class="header-left">
                                    <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                        <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                        <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                                    </svg>
                                    <h2 class="title">Dashboard</h2>
                                </div>
                                <div class="header-right">
                                    <div class="notification">
                                        <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                        <span class="badge">0</span>
                                    </div>
                                    <div class="notification">
                                        <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                        <span class="badge">0</span>
                                    </div>
                                    <div class="user-profile sb-account">
                                        <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                        <div class="user-info">
                                            <p class="sb-name"></p>
                                            <span>Super Admin</span>
                                        </div>
                                    </div>
                                    <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                        <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                                    </div>
                                </div>
                        </header>
                        <div class="container new_container">
                            <div class="row">
                                <div class="col-md-8 p-0">
                                    <section class="dashboard-metrics">
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EFF4FF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #487fff;">
                                                            <i class="fa-solid fa-user-plus" style="color: #ffffff;"></i>
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
                                                <div class="metric-increase">Increase by <span>0</span> this week</div>
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EAFFF9 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #45b369;">
                                                            <i class="fa-solid fa-user-plus" style="color: #ffffff;"></i>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Active Users</h3>
                                                            <p>8,000</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="active_users_chart">
                                                            <canvas class="mt-0" id="active_users_chart"></canvas>
                                                        </div>
                                                        <script>
                                                            const active_usersCtx = document.getElementById('active_users_chart').getContext('2d');
                                                            const gradient2 = active_usersCtx.createLinearGradient(0, 0, 0, 200);
                                                            gradient2.addColorStop(0, 'rgba(72, 255, 112, 0.2)');
                                                            gradient2.addColorStop(1, 'rgba(72, 255, 112, 0)');
                                                            new Chart(active_usersCtx, {
                                                            type: 'line',
                                                            data: {
                                                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                                                                datasets: [{
                                                                data: [0, 5, 12, 3, 5, 7],
                                                                borderColor: '#45B369',
                                                                backgroundColor:gradient2,
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
                                                                legend: { display: false },
                                                                tooltip: { enabled: false }
                                                                },
                                                                scales: {
                                                                x: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                },
                                                                y: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                }}
                                                            }});
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">Increase by <span>+200</span> this week</div>
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF5E9 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #f4941e;">
                                                            <i class="fa-solid fa-ticket" style="color: #ffffff;"></i>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Tickets Created</h3>
                                                            <p>3,200</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="ticket_created_chart">
                                                            <canvas class="mt-0" id="ticket_created_chart"></canvas>
                                                        </div>
                                                        <script>
                                                            const ticket_createdCtx = document.getElementById('ticket_created_chart').getContext('2d');
                                                            const gradient3 = ticket_createdCtx.createLinearGradient(0, 0, 0, 200);
                                                            gradient3.addColorStop(0, 'rgba(255, 182, 72, 0.2)');
                                                            gradient3.addColorStop(1, 'rgba(72, 182, 72, 0)');
                                                            new Chart(ticket_createdCtx, {
                                                            type: 'line',
                                                            data: {
                                                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                                                                datasets: [{
                                                                data: [0, 5, 12, 3, 5, 7],
                                                                borderColor: '#f4941e',
                                                                backgroundColor: gradient3,
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
                                                                legend: { display: false },
                                                                tooltip: { enabled: false }
                                                                },
                                                                scales: {
                                                                x: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                },
                                                                y: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                }}
                                                            }});
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">Increase by <span>18%</span> this week</div>
                                            </div>
                                    </section>
                                    <section class="dashboard-metrics">
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #F3EEFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #8252E9;">
                                                        <i class="fa-solid fa-calendar-check" style="color: #ffffff;"></i>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Ticket Resolved</h3>
                                                            <p>2,700</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="ticket_resolved_chart">
                                                            <canvas class="mt-0" id="ticket_resolved_chart"></canvas>
                                                        </div>
                                                        <script>
                                                            const ticket_resolvedCtx = document.getElementById('ticket_resolved_chart').getContext('2d');
                                                            const gradient4 = ticket_resolvedCtx.createLinearGradient(0, 0, 0, 200);
                                                            gradient4.addColorStop(0, 'rgba(231, 110, 241, 0.2)');
                                                            gradient4.addColorStop(1, 'rgba(231, 110, 241, 0)');
                                                            new Chart(ticket_resolvedCtx, {
                                                            type: 'line',
                                                            data: {
                                                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                                                                datasets: [{
                                                                data: [0, 5, 12, 3, 5, 7],
                                                                borderColor: '#8252E9',
                                                                backgroundColor: gradient4,
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
                                                                legend: { display: false },
                                                                tooltip: { enabled: false }
                                                                },
                                                                scales: {
                                                                x: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                },
                                                                y: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                }}
                                                            }});
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">Increase by <span>+200</span> this week</div>
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #FFF2FE 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #DE3ACE;">
                                                            <i class="fa-solid fa-hourglass-start" style="color: #ffffff;"></i>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3> Avg. Response Time</h3>
                                                            <p>4m 30s</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
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
                                                                legend: { display: false },
                                                                tooltip: { enabled: false }
                                                                },
                                                                scales: {
                                                                x: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                },
                                                                y: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                }}
                                                            }});
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">Improved by <span>12%</span> this week</div>
                                            </div>
                                            <div class="metric-card"
                                                style="background: linear-gradient(90deg, #FFFFFF 0%, #EEFBFF 100%);">
                                                <div class="graph_tabs">
                                                    <div class="metric-card-upper">
                                                        <div class="metric-icon" style="background-color: #00B8F2;">
                                                            <i class="fa-solid fa-ticket" style="color: #ffffff;"></i>
                                                        </div>
                                                        <div class="metric-info">
                                                            <h3>Agent Satisfaction</h3>
                                                            <p>92%</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="agent_chart">
                                                            <canvas class="mt-0" id="agent_chart"></canvas>
                                                        </div>
                                                        <script>
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
                                                                legend: { display: false },
                                                                tooltip: { enabled: false }
                                                                },
                                                                scales: {
                                                                x: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                },
                                                                y: {
                                                                    grid: { display: false },
                                                                    ticks: { display: false },
                                                                    border: { display: false }
                                                                }}
                                                            }});
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="metric-increase">Consistent this week</div>
                                            </div>
                                    </section>
                                    <section class="main-charts">
                                        <div class="card p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="head mb-1">Ticket Support Board</h6>
                                                    <p class="sub_head">Monthly overview of support ticket activity</p>
                                                </div>
                                                <select class="form-select form-select-sm w-auto">
                                                <option>Yearly</option>
                                                <!-- <option>Monthly</option> -->
                                                </select>
                                            </div>
                                            <div class="d-flex justify-content-center gap-3 mb-3">
                                                     <div class="button_ext">
                                                        <i class="fa-solid fa-ticket" style="color: #000;"></i>
                                                        <div>
                                                            <div><strong>Created</strong></div>
                                                            <div>1,200</div>
                                                        </div>
                                                    </div>
                                                    <div class="button_ext">
                                                        <i class="fa-solid fa-ticket" style="color: #000;"></i>
                                                        <div>
                                                            <div><strong>Resolved</strong></div>
                                                            <div>9,50</div>
                                                        </div>
                                                    </div>
                                                     <div class="button_ext">
                                                        <i class="fa-solid fa-ticket" style="color: #000;"></i>
                                                        <div>
                                                            <div><strong>Pending</strong></div>
                                                            <div>1,500</div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <!-- <div class="chart-placeholder" style="height: 350px;">Bar Chart Placeholder</div> -->
                                            <div class="monthlyBarChart">
                                                <canvas id="monthlyBarChart"></canvas>
                                            </div>
                                            <script>
                                                const barCtx = document.getElementById('monthlyBarChart').getContext('2d');
                                                new Chart(barCtx, {
                                                    type: 'bar',
                                                    data: {
                                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                                    datasets: [{
                                                        label: 'Users',
                                                        data: [85000, 68000, 39000, 47000, 59000, 49000, 41000, 47000, 42000, 60000, 29000, 51000],
                                                        backgroundColor: '#4285F4',
                                                        borderRadius: 6,
                                                        barThickness: 10
                                                    }]
                                                    },
                                                    options: {
                                                    responsive: true,
                                                    plugins: {
                                                        legend: { display: false },
                                                        tooltip: {
                                                        callbacks: {
                                                            label: function(context) {
                                                            return `${context.raw.toLocaleString()} users`;
                                                            }
                                                        }
                                                        }
                                                    },
                                                    scales: {
                                                        x: {
                                                        grid: { display: false },
                                                        ticks: {
                                                            color: '#888',
                                                            font: { size: 12 }
                                                        }
                                                        },
                                                        y: {
                                                        grid: {
                                                            drawBorder: false,
                                                            color: '#eee',
                                                            lineWidth: 1
                                                        },
                                                        ticks: {
                                                            callback: value => value / 1000 + 'k',
                                                            color: '#aaa'
                                                        },
                                                        beginAtZero: true
                                                        }
                                                    }
                                                    }
                                                });
                                            </script>
                                        </div>
                                    </section>
                                </div>

                                <div class="col-md-4 p-0">
                                    <section class="main-charts mb-0" style="padding: 15px 25px 0 0;">
                                        <div class="card p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="head mb-1">Ticket Activity</h6>
                                                    <p class="sub_head">Week support summary</p>
                                                </div>
                                                <div class="d-flex flex-column align-items-end">
                                                    <h6 class="head mb-1">120 Tickets</h6>
                                                    <p class="green_badge">+25 new</p>
                                                </div>
                                            </div>
                                            <div class="ticket_activity_chart">
                                                <canvas id="ticket_activity_chart"></canvas>
                                            </div>
                                            <script>
                                                const ticket_activityCtx = document.getElementById('ticket_activity_chart').getContext('2d');
                                                new Chart(ticket_activityCtx, {
                                                    type: 'line',
                                                    data: {
                                                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                                                    datasets: [{
                                                        label: 'Weekly Activity',
                                                        data: [0, 5, 12, 3, 5, 7],
                                                        borderColor: '#487FFF',
                                                        backgroundColor: '#E4ECFF',
                                                        pointRadius: 4,
                                                        // borderWidth: 0, // hides the line
                                                        // pointRadius: 0, // hides the dots
                                                        fill: true,
                                                        tension: 0.4,
                                                        pointBackgroundColor: '#487FFF'
                                                    }]
                                                    },
                                                    options: {
                                                    responsive: true,
                                                    plugins: {
                                                        legend: { display: false }
                                                    },
                                                    scales: {
                                                        x: {
                                                            grid: {
                                                                display: false
                                                            }
                                                        },
                                                        y: {
                                                            beginAtZero: true,
                                                            grid: {
                                                                display: false
                                                            }
                                                        }
                                                    }
                                                    }
                                                });
                                            </script>
                                        </div>
                                    </section>
                                    <section class="main-charts" style="padding: 15px 25px 0 0;">
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
                                    <section class="main-charts" style="padding: 15px 25px 0 0;">
                                        <div class="card p-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold">Customer Overview</h6>
                                                <select class="form-select form-select-sm w-auto">
                                                <option>Yearly</option>
                                                <!-- <option>Monthly</option> -->
                                                </select>
                                            </div>
                                            <!-- Donut Chart Block -->
                                            <div class="overview_chart">
                                                <ul class="legend" style="list-style: none; padding: 0;">
                                                    <li><span class="total"></span> Total: 500</li>
                                                    <li><span class="new"></span> New: 500</li>
                                                    <li><span class="active"></span> Active: 1500</li>
                                                </ul>
                                                <div id="chart-container">
                                                    <canvas id="donutChart" style="max-height: 150px; max-width: 300px;"></canvas>
                                                    <div class="chart-center">
                                                        <p class="mb-1"><strong>Customer Report</strong></p>
                                                        <pre>1500</pre>
                                                    </div>
                                                </div>
                                                <script>
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
                                                                    legend: { display: false },
                                                                    tooltip: { enabled: true }
                                                                }
                                                            }
                                                        });
                                                </script> 
                                              </div>
                                            </div>
                                    </section>
                                </div>
                            </div>               
                            <div class="row">
                                <div class="col-md-5 p-0">
                                    <div class="pl-3 pr-3 pt-0 main-charts">
                                        <div class="bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="head mb-1">Recent Messages</h6>
                                            </div>
                                            <button class="reply-btn">+ New Message</button>
                                        </div>
                                        <div class="seprator"></div>
                                        <div class="recent card p-3">
                                            <ul class="list-unstyled">
                                                <li class="d-flex g-3 mb-6">
                                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Chandara" class="avatar" />
                                                <div>
                                                    <div class="head2 mb-2">Chandara Kiev</div>
                                                    <div class="sub_head2 mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut...</div>
                                                    <small class="text-muted">5m ago</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button class="reply-btn">Reply</button>
                                                </div>
                                                </li>
                                                <li class="d-flex g-3 mb-6">
                                                <img src="https://randomuser.me/api/portraits/men/55.jpg" alt="Samuel" class="avatar" />
                                                <div>
                                                    <div class="head2 mb-2">Samuel Queueee</div>
                                                    <div class="sub_head2 mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut...</div>
                                                    <small class="text-muted">41m ago</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button class="reply-btn">Reply</button>
                                                </div>
                                                </li>
                                                <li class="d-flex g-3 mb-6">
                                                <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Laurenz" class="avatar" />
                                                <div>
                                                    <div class="head2 mb-2">Laurenz Jumowa</div>
                                                    <div class="sub_head2 mb-3">Nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum....</div>
                                                    <small class="text-muted">2h ago</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button class="reply-btn">Reply</button>
                                                </div>
                                                </li>
                                                <li class="d-flex g-3">
                                                <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="Chandara" class="avatar" />
                                                <div>
                                                    <div class="head2 mb-2">Chandara Kiev</div>
                                                    <div class="sub_head2 mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut...</div>
                                                    <small class="text-muted">5m ago</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button class="reply-btn">Reply</button>
                                                </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="div"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 p-0">
                                    <div class="pl-3 pr-3 pt-0 main-charts">
                                        <div class="p-3 card">
                                            <div class="mb-5 d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="head mb-1">Chat Volume</h6>
                                                    <p class="sub_head">Weekly Report</p>
                                                </div>
                                            </div>
                                            <!-- <div class="chart-placeholder" style="height: 250px;">Line Chart Placeholder</div> -->
                                            <div class="chatVolChart">
                                                <canvas id="chatVolChart"></canvas>
                                            </div>
                                            <script>
                                                const chatVolCtx = document.getElementById('chatVolChart').getContext('2d');
                                                new Chart(chatVolCtx, {
                                                    type: 'bar',
                                                    data: {
                                                    labels: ['Mon', 'Tues', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                                    datasets: [
                                                        {
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
                                                        grid: { display: false },
                                                        ticks: {
                                                            color: '#555',
                                                            font: { size: 12 }
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
                                </div>
                                <div class="col-md-3 p-0">
                                    <div class="pt-0 main-charts" style="padding: 0 25px 0 0 !important;">
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
                                </div>
                                <div class="col-md-12 p-0 ">
                                    <div class="pl-3 pr-3 pt-0 main-charts tables">
                                        <div class="bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="head mb-1">All Tickets</h6>
                                            </div>
                                            <p class="label_blue">View All</p>
                                        </div>
                                        <!-- tickets_table = tickets_area.find('.sb-table-tickets');
                                        tickets_table_menu = tickets_area.find('.sb-menu-tickets'); -->
                                        <div class="seprator"></div>
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
                                                            <th data-field="due-date">
                                                                Due Date   
                                                            </th>
                                                            <th data-field="status">
                                                                <?php sb_e('Status') ?> 
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
                                </div>
                                <!-- <div class="col-md-6 p-0">
                                    <div class="pl-3 pr-3 pt-0 main-charts tables" style="padding: 0 25px 0 0 !important;">
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
                                                                <?php sb_e('Status') ?> 
                                                            </th>
                                                            <th data-field="priority">
                                                                <?php sb_e('Priority') ?>   
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
                                </div> -->
                            </div>
                            <div class="row mt-40">
                        </div>
                    </main>
                </div>
                <!-- sahil end -->
                
                <div class="sb-area-conversations">
                    <header>
                        <div class="header-left">
                            <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                            </svg>
                            <h2 class="title">Inbox</h2>
                        </div>
                        <div class="header-right">
                            <div class="notification">
                                <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                <span class="badge">0</span>
                            </div>
                            <div class="notification">
                                <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                <span class="badge">0</span>
                            </div>
                            <div class="user-profile sb-account">
                                <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                <div class="user-info">
                                    <p class="sb-name"></p>
                                    <span>Super Admin</span>
                                </div>
                            </div>
                            <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                            </div>
                        </div>
                    </header>
                    <div class="sb-board">
                        <div class="sb-admin-list<?php echo sb_get_multi_setting('departments-settings', 'departments-show-list') ? ' sb-departments-show' : '' ?>">
                            <div class="sb-top">
                                <div class="sb-select">
                                    <p data-value="0">
                                        <?php sb_e('Inbox') ?><span></span>
                                    </p>
                                    <ul>
                                        <li data-value="0" class="sb-active">
                                            <?php sb_e('Inbox') ?>
                                            <span></span>
                                        </li>
                                        <li data-value="3">
                                            <?php sb_e('Archive') ?>
                                        </li>
                                        <li data-value="4">
                                            <?php sb_e('Trash') ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="sb-flex">
                                    <?php sb_conversations_filter($cloud_active_apps) ?>
                                    <div class="sb-search-btn">
                                        <i class="sb-icon sb-icon-search"></i>
                                        <input type="text" autocomplete="false" placeholder="<?php sb_e('Search for keywords or users...') ?>" />
                                    </div>
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
                                        <li id="convert-to-ticket-list" class="sb-convert-to-ticket-list">
                                            <a id="convert-to-ticket" data-value="convert-to-ticket" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Convert to a ticket') ?>">
                                                <i id="sb-icon-refresh" class="sb-icon-refresh"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="archive" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Archive conversation') ?>">
                                                <i class="sb-icon-check"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="read" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Mark as read') ?>">
                                                <i class="sb-icon-check-circle"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="transcript" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Transcript') ?>" data-action="<?php echo sb_get_multi_setting('transcript', 'transcript-action') ?>">
                                                <i class="sb-icon-download"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="inbox" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send to inbox') ?>">
                                                <i class="sb-icon-back"></i>
                                            </a>
                                        </li>
                                        <?php
                                        if ($is_admin || (!$supervisor && sb_get_multi_setting('agents', 'agents-delete-conversation')) || ($supervisor && $supervisor['supervisor-delete-conversation'])) {
                                            echo '<li><a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_('Delete conversation') . '"><i class="sb-icon-delete"></i></a></li><li><a data-value="empty-trash" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_('Empty trash') . '"><i class="sb-icon-delete"></i></a></li>';
                                        }
                                        ?>
                                        <li>
                                            <a data-value="panel" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Details') ?>">
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
                                        <?php sb_e('Select a conversation') ?>
                                    </label>
                                    <p>
                                        <?php sb_e('Select a conversation from the left menu.') ?>
                                    </p>
                                </div>
                            </div>
                            <audio id="sb-audio" preload="auto"><source src="<?php echo sb_get_multi_setting('sound-settings', 'sound-settings-file-admin', SB_URL . '/media/sound.mp3') ?>" type="audio/mpeg"></audio>
                        </div>
                        <div class="sb-user-details">
                            <div class="sb-top">
                                <div class="sb-profile">
                                    <img src="<?php echo SB_URL ?>/media/user.svg" />
                                    <span class="sb-name"></span>
                                </div>
                            </div>
                            <div class="sb-scroll-area">
                                <a class="sb-user-details-close sb-close sb-btn-icon sb-btn-red">
                                    <i class="sb-icon-close"></i>
                                </a>
                                <div class="sb-profile-list sb-profile-list-conversation<?php echo $collapse ?>"></div>
                                <?php
                                sb_apps_panel();
                                sb_departments('custom-select');
                                if (sb_get_multi_setting('routing', 'routing-active') || (sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-menu'))) {
                                    sb_routing_select();
                                }
                                if (!sb_get_multi_setting('disable', 'disable-notes')) {
                                    echo '<div class="sb-panel-details sb-panel-notes' . $collapse . '"><i class="sb-icon-plus"></i><h3>' . sb_('Notes') . '</h3><div></div></div>';
                                }
                                if (!sb_get_multi_setting('disable', 'disable-tags')) {
                                    echo '<div class="sb-panel-details sb-panel-tags"><i class="sb-icon-settings"></i><h3>' . sb_('Tags') . '</h3><div></div></div>';
                                }
                                if (!sb_get_multi_setting('disable', 'disable-attachments')) {
                                    echo '<div class="sb-panel-details sb-panel-attachments sb-collapse"></div>';
                                }
                                ?>
                                <h3 class="sb-hide">
                                    <?php sb_e('User conversations') ?>
                                </h3>
                                <ul class="sb-user-conversations"></ul>
                            </div>
                            <div class="sb-no-conversation-message"></div>
                        </div>
                    </div>
                    <i class="sb-btn-collapse sb-left sb-icon-arrow-left"></i>
                    <i class="sb-btn-collapse sb-right sb-icon-arrow-right"></i>
                </div>
                <?php if ($active_areas['users']) { ?>
                    <div class="sb-area-users">
                        <header>
                            <div class="header-left">
                                <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                    <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                    <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                                </svg>
                                <h2 class="title">Customers</h2>
                            </div>
                            <div class="header-right">
                                <div class="notification">
                                    <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="notification">
                                    <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="user-profile sb-account">
                                    <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                    <div class="user-info">
                                        <p class="sb-name"></p>
                                        <span>Super Admin</span>
                                    </div>
                                </div>
                                <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </header>
                        <div class="sb-top-bar">
                            <div>
                                <a class="sb-btn sb-icon sb-new-user sb_btn_new mr-7">
                                    <i class="fa-solid fa-user-plus pr-1"></i>
                                    <?php sb_e('New Customer') ?>
                                </a>
                                <div class="sb-menu-wide sb-menu-users sb-menu-wide_new">
                                    <div>
                                        <?php sb_e('All') ?>
                                        <span data-count="0"></span>
                                    </div>
                                    <ul>
                                        <li data-type="all" class="sb-active">
                                            <span data-count="0">(0)</span>
                                            <?php sb_e('All') ?>
                                        </li>
                                        <li data-type="user">
                                            <span data-count="0">(0)</span>
                                            <?php sb_e('Users') ?>
                                        </li>
                                        <li data-type="lead">
                                            <span data-count="0">(0)</span>
                                            <?php sb_e('Leads') ?>
                                        </li>
                                        <li data-type="visitor">
                                            <span data-count="0">(0)</span>
                                            <?php sb_e('Visitors') ?>
                                        </li>
                                        <li data-type="online">
                                            <?php sb_e('Online') ?>
                                        </li>
                                        <?php
                                        if ($is_admin || (!$supervisor && sb_get_multi_setting('agents', 'agents-tab')) || ($supervisor && $supervisor['supervisor-agents-tab'])) {
                                            echo '<li data-type="agent">' . sb_('Agents & Admins') . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="sb-menu-mobile">
                                    <i class="sb-icon-menu"></i>
                                    <ul>
                                        <?php
                                        if ($is_admin) {
                                            echo '<li><a data-value="csv" class="sb-btn-icon" data-sb-tooltip="' . sb_('Download CSV') . '"><i class="sb-icon-download"></i></a></li>';
                                        }
                                        ?>
                                        <li>
                                            <a data-value="message" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send a message') ?>">
                                                <i class="sb-icon-chat"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-value="custom_email" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send an email') ?>">
                                                <i class="sb-icon-envelope"></i>
                                            </a>
                                        </li>
                                        <?php
                                        if ($sms) {
                                            echo '<li><a data-value="sms" class="sb-btn-icon" data-sb-tooltip="' . sb_('Send a text message') . '"><i class="sb-icon-sms"></i></a><li>';
                                        }
                                        if (defined('SB_WHATSAPP') && (!function_exists('sb_whatsapp_active') || sb_whatsapp_active())) {
                                            echo '<li><a data-value="whatsapp" class="sb-btn-icon" data-sb-tooltip="' . sb_('Send a WhatsApp message template') . '"><i class="sb-icon-social-wa"></i></a><li>'; // Deprecated: remove function_exists('sb_whatsapp_active')
                                        }
                                        if ($is_admin) {
                                            echo '<li><a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_('Delete users') . '" style="display: none;"><i class="sb-icon-delete"></i></a></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <?php sb_conversations_filter($cloud_active_apps) ?>
                                <!-- <div class="sb-search-btn">
                                    <i class="sb-icon sb-icon-search"></i>
                                    <input type="text" autocomplete="false" placeholder="<?php sb_e('Search users ...') ?>" />
                                </div> -->
                            </div>
                        </div>
                        <div class="sb-scroll-area">
                            <table class="sb-table sb_table_new sb-table-users">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" />
                                        </th>
                                        <th data-field="first_name">
                                            <?php sb_e('Full name') ?>
                                        </th>
                                        <?php sb_users_table_extra_fields() ?>
                                        <th data-field="email">
                                            <?php sb_e('Email') ?>
                                        </th>
                                        <th data-field="user_type">
                                            <?php sb_e('Type') ?>
                                        </th>
                                        <th data-field="last_activity">
                                            <?php sb_e('Last activity') ?>
                                        </th>
                                        <th data-field="creation_time" class="sb-active">
                                            <?php sb_e('Registration date') ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                <?php
                if ($active_areas['chatbot']) {
                    require_once(SB_PATH . '/apps/dialogflow/components.php');
                    sb_dialogflow_chatbot_area();
                }
                ?>
                <?php if ($active_areas['tickets']) { ?>
                    <style>
                        .sb-table .span-border {
                            text-align: center;
                            padding: 3px 7px;
                            border-radius: 6px;
                        }
                    </style>
                    <div class="sb-area-tickets">
                        <header>
                            <div class="header-left">
                                <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                    <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                    <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                                </svg>
                                <h2 class="title">Tickets</h2>
                            </div>
                            <div class="header-right">
                                <div class="notification">
                                    <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="notification">
                                    <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="user-profile sb-account">
                                    <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                    <div class="user-info">
                                        <p class="sb-name"></p>
                                        <span>Super Admin</span>
                                    </div>
                                </div>
                                <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </header>
                        <div class="sb-top-bar">
                            <div>
                                <a class="sb-btn sb-icon sb-new-ticket sb_btn_new mr-7">
                                    <i class="fa-solid fa-plus pr-1"></i>
                                    <?php sb_e('New Ticket') ?>
                                </a>
                                <div class="sb-menu-wide sb-menu-tickets sb-menu-wide_new">
                                    <div>
                                        <?php sb_e('All') ?>
                                        <span data-count="0"></span>
                                    </div>
                                    <ul>
                                        <li data-type="all" class="sb-active">
                                            <span data-count="0">0</span>
                                            <?php sb_e('All') ?>
                                        </li>
                                        <li data-type="open">
                                            <span data-count="0">0</span>
                                            <?php sb_e('Open') ?>
                                        </li>
                                        <li data-type="in-progress">
                                            <span data-count="0">0</span>
                                            <?php sb_e('In Progress') ?>
                                        </li>
                                        <li data-type="answered">
                                            <span data-count="0">0</span>
                                            <?php sb_e('Answered') ?>
                                        </li>
                                        <li data-type="hold">
                                            <span data-count="0">0</span>
                                            <?php sb_e('On Hold') ?>
                                        </li>
                                        <li data-type="closed">
                                            <span data-count="0">0</span>
                                            <?php sb_e('Closed') ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="sb-menu-mobile">
                                    <i class="sb-icon-menu"></i>
                                    <ul>
                                        <?php
                                        if ($is_admin) {
                                            echo '<li><a data-value="csv" class="sb-btn-icon" data-sb-tooltip="' . sb_('Download CSV') . '"><i class="sb-icon-download"></i></a></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <!-- <div class="sb-search-btn">
                                    <i class="sb-icon sb-icon-search"></i>
                                    <input type="text" autocomplete="false" placeholder="<?php sb_e('Search tickets ...') ?>" />
                                </div> -->
                            </div>
                        </div>
                        <div class="sb-scroll-area">
                            <table class="sb-table sb_table_new sb-table-tickets">
                                <thead>
                                    <tr>
                                        <th data-field="id">
                                            <!--input type="checkbox" /-->
                                            <?php sb_e('ID') ?>
                                        </th>
                                        <th data-field="subject">
                                            <?php sb_e('Subject') ?>
                                        </th>
                                        <th data-field="tags">
                                            <?php sb_e('Tags') ?>
                                        </th>
                                        <?php 
                                        $department_settings = sb_get_setting('departments-settings');
                                        if(isset($department_settings['departments-show-list']) && $department_settings['departments-show-list'] == '1')
                                        {
                                        ?>
                                        <th data-field="department">
                                            <?php sb_e('Department') ?>
                                        </th>
                                        <?php } ?>
                                        <!--th data-field="service">
                                            <?php //sb_e('Service') ?>
                                        </th-->
                                        <th data-field="contact">
                                            <?php sb_e('Contact') ?>
                                        </th>
                                        <th data-field="status">
                                            <?php sb_e('Status') ?> 
                                        </th>
                                        <th data-field="priority">
                                            <?php sb_e('Priority') ?>   
                                        </th>
                                        <th data-field="last_reply">
                                            <?php sb_e('Last Reply') ?>   
                                        </th>
                                        <th data-field="creation_time">
                                            <?php sb_e('Created') ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($active_areas['articles']) { ?>
                    <div class="sb-area-articles sb-loading">
                        <header>
                            <div class="header-left">
                                <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                    <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                    <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                                </svg>
                                <h2 class="title">Articles</h2>
                            </div>
                            <div class="header-right">
                                <div class="notification">
                                    <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="notification">
                                    <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="user-profile sb-account">
                                    <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                    <div class="user-info">
                                        <p class="sb-name"></p>
                                        <span>Super Admin</span>
                                    </div>
                                </div>
                                <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </header>
                        <!-- <div class="sb-top-bar">
                            <div>
                                <h2>
                                    <?php sb_e('Articles') ?>
                                </h2>
                                <div class="sb-menu-wide sb-menu-articles">
                                    <div>
                                        <?php sb_e('Articles') ?>
                                    </div>
                                    <ul>
                                        <li data-type="articles" class="sb-active">
                                            <?php sb_e('Articles') ?>
                                        </li>
                                        <li data-type="categories">
                                            <?php sb_e('Categories') ?>
                                        </li>
                                        <li data-type="settings">
                                            <?php sb_e('Settings') ?>
                                        </li>
                                        <?php
                                        if ($active_areas['reports']) {
                                            echo '<li data-type="reports">' . sb_('Reports') . '</li>';
                                        }
                                        sb_docs_link('#articles');
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <a class="sb-btn sb-save-articles sb-icon">
                                    <i class="sb-icon-check"></i>
                                    <?php sb_e('Save changes') ?>
                                </a>
                                <a class="sb-btn-icon sb-view-article" href="" target="_blank">
                                    <i class="sb-icon-next"></i>
                                </a>
                            </div>
                        </div> -->
                        <div class="sb-tab sb-inner-tab">
                            <div class="sb-nav sb-nav-only sb-scroll-area">
                                <ul class="ul-articles"></ul>
                                <ul class="ul-categories"></ul>
                                <div class="sb-add-category sb-btn sb-icon sb-btn-white">
                                    <i class="sb-icon-plus"></i>
                                    <?php sb_e('Add new category') ?>
                                </div>
                                <div class="sb-add-article sb-btn sb-icon sb-btn-white">
                                    <i class="sb-icon-plus"></i>
                                    <?php sb_e('Add new article') ?>
                                </div>
                            </div>
                            <div class="sb-content sb-content-articles sb-scroll-area sb-loading">
                                <div class="sb-top-bar">
                                    <div class="topbar_menu">
                                        <div class="sb-menu-wide sb-menu-articles">
                                            <ul class="mb-4">
                                                <li data-type="articles" class="sb-active">
                                                    <?php sb_e('Articles') ?>
                                                </li>
                                                <li data-type="categories">
                                                    <?php sb_e('Categories') ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="content_article">
                                    <div class="articleHEad">
                                        <div class="">
                                            <p class="head mb-4">Articles Settings</p>
                                            <p class="des mb-0">Manage preferences and options for your articles.</p>
                                        </div>
                                        <div>
                                            <a class="sb-btn sb-save-articles sb-icon">
                                                <i class="sb-icon-check"></i>
                                                <?php sb_e('Save changes') ?>
                                            </a>
                                            <a class="sb-btn-icon sb-view-article" href="" target="_blank">
                                                <i class="sb-icon-next"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="articles_bg">
                                        <h2 class="sb-language-switcher-cnt">
                                            <?php sb_e('Title') ?>
                                        </h2>
                                        <div class="sb-setting sb-type-text sb-article-title">
                                            <div>
                                                <input type="text" />
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e('Content') ?>
                                        </h2>
                                        <div class="sb-setting sb-type-textarea sb-article-content">
                                            <div>
                                                <?php echo sb_get_setting('disable-editor-js') ? '<textarea></textarea>' : '<div id="editorjs"></div>' ?>
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e('External link') ?>
                                        </h2>
                                        <div class="sb-setting sb-type-text sb-article-link">
                                            <div>
                                                <input type="text" />
                                            </div>
                                        </div>
                                        <div class="sb-article-categories sb-grid">
                                            <div>
                                                <h2>
                                                    <?php sb_e('Parent category') ?>
                                                </h2>
                                                <div class="sb-setting sb-type-select">
                                                    <div>
                                                        <select id="article-parent-categories"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h2>
                                                    <?php sb_e('Categories') ?>
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
                                <div class="sb-top-bar">
                                    <div class="topbar_menu">
                                        <div class="sb-menu-wide sb-menu-articles">
                                            <ul class="mb-4">
                                                <li data-type="articles">
                                                    <?php sb_e('Articles') ?>
                                                </li>
                                                <li data-type="categories" class="sb-active">
                                                    <?php sb_e('Categories') ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <h2 class="sb-language-switcher-cnt">
                                    <?php sb_e('Name') ?>
                                </h2>
                                <div class="sb-setting sb-type-text">
                                    <div>
                                        <input id="category-title" type="text" />
                                    </div>
                                </div>
                                <h2>
                                    <?php sb_e('Description') ?>
                                </h2>
                                <div class="sb-setting sb-type-textarea">
                                    <div>
                                        <textarea id="category-description"></textarea>
                                    </div>
                                </div>
                                <h2>
                                    <?php sb_e('Image') ?>
                                </h2>
                                <div data-type="image" class="sb-input sb-setting sb-input-image">
                                    <div id="category-image" class="image">
                                        <div class="sb-icon-close"></div>
                                    </div>
                                </div>
                                <h2 class="category-parent">
                                    <?php sb_e('Parent category') ?>
                                </h2>
                                <div data-type="checkbox" class="sb-setting sb-type-checkbox category-parent">
                                    <div class="input">
                                        <input id="category-parent" type="checkbox" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($active_areas['reports']) { ?>
                    <div class="sb-area-reports sb-loading">
                        <header>
                            <div class="header-left">
                                <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                    <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                    <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                                </svg>
                                <h2 class="title">Reports</h2>
                            </div>
                            <div class="header-right">
                                <div class="notification">
                                    <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="notification">
                                    <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="user-profile sb-account">
                                    <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                    <div class="user-info">
                                        <p class="sb-name"></p>
                                        <span>Super Admin</span>
                                    </div>
                                </div>
                                <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </header>
                        <div class="sb-top-bar">
                            <div>
                                <h2>
                                    <?php sb_e('Reports') ?>
                                </h2>
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
                                    <?php sb_e('Reports') ?>
                                </div>
                                <ul>
                                    <li class="sb-tab-nav-title">
                                        <?php sb_e('Conversations') ?>
                                    </li>
                                    <li id="conversations" class="sb-active">
                                        <?php sb_e('Conversations') ?>
                                    </li>
                                    <li id="missed-conversations">
                                        <?php sb_e('Missed conversations') ?>
                                    </li>
                                    <li id="conversations-time">
                                        <?php sb_e('Conversations time') ?>
                                    </li>
                                    <li class="sb-tab-nav-title">
                                        <?php sb_e('Direct messages') ?>
                                    </li>
                                    <li id="direct-messages">
                                        <?php sb_e('Chat messages') ?>
                                    </li>
                                    <li id="direct-emails">
                                        <?php sb_e('Emails') ?>
                                    </li>
                                    <li id="direct-sms">
                                        <?php sb_e('Text messages') ?>
                                    </li>
                                    <li class="sb-tab-nav-title">
                                        <?php sb_e('Users and agents') ?>
                                    </li>
                                    <li id="visitors">
                                        <?php sb_e('Visitors') ?>
                                    </li>
                                    <li id="leads">
                                        <?php sb_e('Leads') ?>
                                    </li>
                                    <li id="users">
                                        <?php sb_e('Users') ?>
                                    </li>
                                    <li id="registrations">
                                        <?php sb_e('Registrations') ?>
                                    </li>
                                    <li id="agents-response-time">
                                        <?php sb_e('Agent response time') ?>
                                    </li>
                                    <li id="agents-conversations">
                                        <?php sb_e('Agent conversations') ?>
                                    </li>
                                    <li id="agents-conversations-time">
                                        <?php sb_e('Agent conversations time') ?>
                                    </li>
                                    <li id="agents-ratings">
                                        <?php sb_e('Agent ratings') ?>
                                    </li>
                                    <li id="countries">
                                        <?php sb_e('Countries') ?>
                                    </li>
                                    <li id="languages">
                                        <?php sb_e('Languages') ?>
                                    </li>
                                    <li id="browsers">
                                        <?php sb_e('Browsers') ?>
                                    </li>
                                    <li id="os">
                                        <?php sb_e('Operating systems') ?>
                                    </li>
                                    <li class="sb-tab-nav-title">
                                        <?php sb_e('Automation') ?>
                                    </li>
                                    <li id="follow-up">
                                        <?php sb_e('Follow up') ?>
                                    </li>
                                    <li id="message-automations">
                                        <?php sb_e('Message automations') ?>
                                    </li>
                                    <li id="email-automations">
                                        <?php sb_e('Email automations') ?>
                                    </li>
                                    <?php
                                    if ($sms) {
                                        echo '<li id="sms-automations">' . sb_('Text message automations') . '</li>';
                                    }
                                    ?>
                                    <li class="sb-tab-nav-title">
                                        <?php sb_e('Articles') ?>
                                    </li>
                                    <li id="articles-searches">
                                        <?php sb_e('Searches') ?>
                                    </li>
                                    <li id="articles-views">
                                        <?php sb_e('Article views') ?>
                                    </li>
                                    <li id="articles-views-single">
                                        <?php sb_e('Article views by article') ?>
                                    </li>
                                    <li id="articles-ratings">
                                        <?php sb_e('Article ratings') ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="sb-content sb-scroll-area">
                                <div class="sb-reports-chart">
                                    <div class="chart-cnt">
                                        <canvas></canvas>
                                    </div>
                                </div>
                                <div class="sb-reports-sidebar">
                                    <div class="sb-title sb-reports-title"></div>
                                    <p class="sb-reports-text"></p>
                                    <div class="sb-collapse">
                                        <div>
                                            <table class="sb-table"></table>
                                        </div>
                                    </div>
                                </div>
                                <p class="sb-no-results">
                                    <?php echo sb_('No data found.') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($active_areas['settings']) { ?>
                    <div class="sb-area-settings">
                        <header>
                            <div class="header-left">
                                <svg width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="6" y="6" width="20" height="3" rx="1.5" fill="#155CFD" />
                                    <rect y="15" width="26" height="3" rx="1.5" fill="#155CFD" />
                                    <rect x="4" y="24" width="22" height="3" rx="1.5" fill="#155CFD" />
                                </svg>
                                <h2 class="title">Settings</h2>
                            </div>
                            <div class="header-right">
                                <div class="notification">
                                    <i class="fa-solid fa-bell" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="notification">
                                    <i class="fa-solid fa-envelope-open-text" style="font-size: 28px;"></i>
                                    <span class="badge">0</span>
                                </div>
                                <div class="user-profile sb-account">
                                    <img data-value="edit-profile" src="<?php echo $is_cloud ? SB_CLOUD_BRAND_ICON : sb_get_setting('admin-icon', SB_URL . '/media/icon.svg') ?>" alt="User">
                                    <div class="user-info">
                                        <p class="sb-name"></p>
                                        <span>Super Admin</span>
                                    </div>
                                </div>
                                <div class="logout" data-value="logout" data-toggle="tooltip" data-placement="right" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </header>
                        <div class="sb-top-bar">
                            <div>
                                <h2>
                                    <?php sb_e('Settings') ?>
                                </h2>
                            </div>
                            <div>
                                <div class="sb-search-dropdown">
                                    <div class="sb-search-btn">
                                        <i class="sb-icon sb-icon-search"></i>
                                        <input id="sb-search-settings" type="text" autocomplete="false" placeholder="<?php sb_e('Search ...') ?>" />
                                    </div>
                                    <div class="sb-search-dropdown-items"></div>
                                </div>
                                <a class="sb-btn sb-save-changes sb-icon">
                                    <i class="sb-icon-check"></i>
                                    <?php sb_e('Save changes') ?>
                                </a>
                            </div>
                        </div>
                        <div class="sb-tab">
                            <div class="sb-nav sb-scroll-area">
                                <div>
                                    <?php sb_e('Settings') ?>
                                </div>
                                <ul>
                                    <li id="tab-chat" class="sb-active">
                                        <?php echo $disable_translations ? 'Chat' : sb_('Chat') ?>
                                    </li>
                                    <li id="tab-admin">
                                        <?php echo $disable_translations ? 'Admin' : sb_('Admin') ?>
                                    </li>
                                    <li id="tab-notifications">
                                        <?php echo $disable_translations ? 'Notifications' : sb_('Notifications') ?>
                                    </li>
                                    <li id="tab-users">
                                        <?php echo $disable_translations ? 'Users' : sb_('Users') ?>
                                    </li>
                                    <li id="tab-design">
                                        <?php echo $disable_translations ? 'Design' : sb_('Design') ?>
                                    </li>
                                    <li id="tab-messages">
                                        <?php echo $disable_translations ? 'Messages & Forms' : sb_('Messages & Forms') ?>
                                    </li>
                                    <li id="tab-various">
                                        <?php echo $disable_translations ? 'Miscellaneous' : sb_('Miscellaneous') ?>
                                    </li>
                                    <?php
                                    for ($i = 0; $i < count($apps); $i++) {
                                        if (defined($apps[$i][0]) && (!$is_cloud || in_array($apps[$i][1], $cloud_active_apps))) {
                                            echo '<li id="tab-' . $apps[$i][1] . '">' . sb_($apps[$i][2]) . '</li>';
                                        }
                                    }
                                    ?>
                                    <li id="tab-apps">
                                        <?php echo $disable_translations ? 'Apps' : sb_('Apps') ?>
                                    </li>
                                    <li id="tab-articles">
                                        <?php echo $disable_translations ? 'Articles' : sb_('Articles') ?>
                                    </li>
                                    <li id="tab-automations">
                                        <?php echo $disable_translations ? 'Automations' : sb_('Automations') ?>
                                    </li>
                                    <li id="tab-translations">
                                        <?php echo $disable_translations ? 'Translations' : sb_('Translations') ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="sb-content sb-scroll-area">
                                <div class="sb-active">
                                    <?php sb_populate_settings('chat', $sb_settings) ?>
                                </div>
                                <div>
                                    <?php sb_populate_settings('admin', $sb_settings) ?>
                                </div>
                                <div>
                                    <?php sb_populate_settings('notifications', $sb_settings) ?>
                                </div>
                                <div>
                                    <?php sb_populate_settings('users', $sb_settings) ?>
                                </div>
                                <div>
                                    <?php sb_populate_settings('design', $sb_settings) ?>
                                </div>
                                <div>
                                    <?php sb_populate_settings('messages', $sb_settings) ?>
                                </div>
                                <div>
                                    <?php sb_populate_settings('miscellaneous', $sb_settings) ?>
                                </div>
                                <?php sb_apps_area($apps, $cloud_active_apps) ?>
                                <div>
                                    <?php sb_populate_settings('articles', $sb_settings) ?>
                                </div>
                                <div>
                                    <div class="sb-automations-area">
                                        <div class="sb-select">
                                            <p data-value="messages">
                                                <?php sb_e('Messages') ?>
                                            </p>
                                            <ul>
                                                <li data-value="messages" class="sb-active">
                                                    <?php sb_e('Messages') ?>
                                                </li>
                                                <li data-value="emails">
                                                    <?php sb_e('Emails') ?>
                                                </li>
                                                <?php if ($sms)
                                                    echo '<li data-value="sms">' . sb_('Text messages') . '</li>' ?>
                                                    <li data-value="popups">
                                                    <?php sb_e('Pop-ups') ?>
                                                </li>
                                                <li data-value="design">
                                                    <?php sb_e('Design') ?>
                                                </li>
                                                <li data-value="more">
                                                    <?php sb_e('More') ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="sb-inner-tab sb-tab">
                                            <div class="sb-nav sb-nav-only">
                                                <ul></ul>
                                                <div class="sb-add-automation sb-btn sb-icon">
                                                    <i class="sb-icon-plus"></i>
                                                    <?php sb_e('Add new automation') ?>
                                                </div>
                                            </div>
                                            <div class="sb-content sb-hide">
                                                <div class="sb-automation-values">
                                                    <h2 class="sb-language-switcher-cnt">
                                                        <?php sb_e('Name') ?>
                                                    </h2>
                                                    <div class="sb-setting sb-type-text">
                                                        <div>
                                                            <input data-id="name" type="text" />
                                                        </div>
                                                    </div>
                                                    <h2>
                                                        <?php sb_e('Message') ?>
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
                                                        <?php sb_e('Conditions') ?>
                                                    </h2>
                                                    <div class="sb-conditions"></div>
                                                    <div class="sb-add-condition sb-btn sb-icon">
                                                        <i class="sb-icon-plus"></i>
                                                        <?php sb_e('Add condition') ?>
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
                                                        <?php sb_e('Front End') ?>
                                                    </div>
                                                    <ul>
                                                        <li data-value="front" class="sb-active">
                                                            <?php sb_e('Front End') ?>
                                                        </li>
                                                        <li data-value="admin">
                                                            <?php sb_e('Admin') ?>
                                                        </li>
                                                        <li data-value="admin/js">
                                                            <?php sb_e('Client side admin') ?>
                                                        </li>
                                                        <li data-value="admin/settings">
                                                            <?php sb_e('Settings') ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <a class="sb-btn sb-icon sb-add-translation">
                                                    <i class="sb-icon-plus"></i>
                                                    <?php sb_e('New translation') ?>
                                                </a>
                                            </div>
                                            <div class="sb-translations-list"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            if (defined('SB_DIALOGFLOW')) {
                require_once(SB_PATH . '/apps/dialogflow/components.php');
                sb_dialogflow_intent_box();
            }
            if (defined('SB_WHATSAPP')) {
                sb_whatsapp_send_template_box();
            }
            if ($is_admin && !$is_cloud) {
                sb_updates_box();
            }
            ?>
            <div id="sb-generic-panel"></div>
            <form class="sb-upload-form-admin sb-upload-form" action="<?php echo sb_sanatize_string($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="files[]" class="sb-upload-files" multiple />
            </form>
            <div class="sb-info-card"></div>
            <?php
        } else {
            if ($is_cloud) {
                sb_cloud_reset_login();
            } else {
                sb_login_box();
            }
        }
        ?>
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
    if (!empty(sb_get_setting('custom-js')) && !$is_cloud) {
        echo '<script id="sb-custom-js" src="' . sb_get_setting('custom-js') . '"></script>';
    }
    if (!empty(sb_get_setting('custom-css')) && !$is_cloud) {
        echo '<link id="sb-custom-css" rel="stylesheet" type="text/css" href="' . sb_get_setting('custom-css') . '" media="all">';
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

function sb_apps_area($apps, $cloud_active_apps) {
    $apps_wp = ['SB_WP', 'SB_WOOCOMMERCE', 'SB_UMP', 'SB_ARMEMBER'];
    $apps_php = [];
    $apps_cloud_excluded = ['whmcs', 'martfury', 'aecommerce', 'perfex', 'opencart'];
    $wp = defined('SB_WP');
    $code = '';
    $is_cloud = sb_is_cloud();
    for ($i = 0; $i < count($apps); $i++) {
        if (defined($apps[$i][0]) && (!$is_cloud || in_array($apps[$i][1], $cloud_active_apps))) {
            $code .= '<div>' . sb_populate_app_settings($apps[$i][1]) . '</div>';
        }
    }
    $code .= '<div><div class="sb-apps">';
    for ($i = 1; $i < count($apps); $i++) {
        if ((($wp && !in_array($apps[$i][0], $apps_php)) || (!$wp && !in_array($apps[$i][0], $apps_wp))) && (!$is_cloud || !in_array($apps[$i][1], $apps_cloud_excluded))) {
            $code .= '<div data-app="' . $apps[$i][1] . '">' . (defined($apps[$i][0]) && (!$is_cloud || in_array($apps[$i][1], $cloud_active_apps)) ? '<i class="sb-icon-check"></i>' : '') . ' <img src="' . SB_URL . '/media/apps/' . $apps[$i][1] . '.svg" /><h2>' . $apps[$i][2] . '</h2><p>' . str_replace('{R}', $is_cloud ? SB_CLOUD_BRAND_NAME : 'Support Board', sb_s($apps[$i][3])) . '</p></div>';
        }
    }
    echo $code . '</div></div>';
}

function sb_apps_panel() {
    $code = '';
    $collapse = sb_get_setting('collapse') ? ' sb-collapse' : '';
    $panels = [['SB_UMP', 'ump'], ['SB_WOOCOMMERCE', 'woocommerce'], ['SB_PERFEX', 'perfex'], ['SB_WHMCS', 'whmcs'], ['SB_AECOMMERCE', 'aecommerce'], ['SB_ARMEMBER', 'armember'], ['SB_ZENDESK', 'zendesk'], ['SB_MARTFURY', 'martfury'], ['SB_OPENCART', 'opencart']];
    for ($i = 0; $i < count($panels); $i++) {
        if (defined($panels[$i][0])) {
            $code .= '<div class="sb-panel-details sb-panel-' . $panels[$i][1] . $collapse . '"></div>';
        }
    }
    if (sb_is_cloud()) {
        $code .= '<div class="sb-panel-details sb-panel-shopify' . $collapse . '"></div>';
    }
    echo $code;
}

function sb_box_ve() {
    if ((!isset($_COOKIE['SA_' . 'VGC' . 'KMENS']) && !isset($_COOKIE['_ga_' . 'VGC' . 'KMENS'])) || !password_verify('VGC' . 'KMENS', isset($_COOKIE['_ga_' . 'VGC' . 'KMENS']) ? $_COOKIE['_ga_' . 'VGC' . 'KMENS'] : $_COOKIE['SA_' . 'VGC' . 'KMENS'])) { // Deprecated. _ga will be removed
        echo file_get_contents(SB_PATH . '/resources/sb.html');
        return false;
    }
    return true;
}

function sb_users_table_extra_fields() {
    $extra_fields = sb_get_setting('user-table-extra-columns');
    $count = $extra_fields && !is_string($extra_fields) ? count($extra_fields) : false;
    if ($count) {
        $code = '';
        for ($i = 0; $i < $count; $i++) {
            $slug = $extra_fields[$i]['user-table-extra-slug'];
            $code .= '<th data-field="' . $slug . '" data-extra="true">' . sb_string_slug($slug, 'string') . '</th>';
        }
        echo $code;
    }
}

function sb_dialogflow_languages_list() {
    $languages = json_decode(file_get_contents(SB_PATH . '/apps/dialogflow/dialogflow_languages.json'), true);
    $code = '<div data-type="select" class="sb-setting sb-type-select sb-dialogflow-languages"><div class="input"><select><option value="">' . sb_('Default') . '</option>';
    for ($i = 0; $i < count($languages); $i++) {
        $code .= '<option value="' . $languages[$i][1] . '">' . $languages[$i][0] . '</option>';
    }
    return $code . '</select></div></div>';
}

function sb_conversations_filter($cloud_active_apps) {
    if (sb_get_multi_setting('disable', 'disable-filters')) {
        return;
    }
    $is_cloud = sb_is_cloud();
    $departments = sb_is_agent(false, true, true) || !sb_isset(sb_get_active_user(), 'department') ? sb_get_setting('departments', []) : [];
    $sources = [['em', 'Email', true], ['tk', 'Tickets', 'SB_TICKETS'], ['wa', 'WhatsApp', 'SB_WHATSAPP'], ['fb', 'Messenger', 'SB_MESSENGER'], ['ig', 'Instagram', 'SB_MESSENGER'], ['tg', 'Telegram', 'SB_TELEGRAM'], ['tw', 'Twitter', 'SB_TWITTER'], ['vb', 'Viber', 'SB_VIBER'], ['ln', 'LINE', 'SB_LINE'], ['wc', 'WeChat', 'SB_WECHAT'], ['za', 'Zalo', 'SB_ZALO'], ['tm', 'Text message', true]];
    $tags = sb_get_multi_setting('disable', 'disable-tags') ? [] : sb_get_setting('tags', []);
    $count = is_array($departments) ? count($departments) : 0;
    $code = (count($tags) && sb_get_multi_setting('tags-settings', 'tags-starred') ? '<i class="sb-icon sb-icon-tag-line sb-filter-star" data-color-text="' . $tags[0]['tag-color'] . '" data-value="' . $tags[0]['tag-name'] . '"></i>' : '') . '<div class="sb-filter-btn"><i class="sb-icon sb-icon-filter"></i><div><div class="sb-select' . ($count ? '' : ' sb-hide') . '"><p>' . sb_('All departments') . '</p><ul' . ($count > 8 ? ' class="sb-scroll-area"' : '') . '><li data-value="">' . sb_('All departments') . '</li>';
    for ($i = 0; $i < $count; $i++) {
        $code .= '<li data-value="' . $departments[$i]['department-id'] . '">' . ucfirst(sb_($departments[$i]['department-name'])) . '</li>';
    }
    $code .= '</ul></div>';
    if (!sb_get_multi_setting('disable', 'disable-channels-filter')) {
        $count = count($sources);
        $code .= '<div class="sb-select"><p>' . sb_('All channels') . '</p><ul' . ($count > 8 ? ' class="sb-scroll-area"' : '') . '><li data-value="false">' . sb_('All channels') . '</li><li data-value="chat">' . sb_('Chat') . '</li>';
        for ($i = 0; $i < $count; $i++) {
            if ($sources[$i][2] === true || (defined($sources[$i][2]) && (!$is_cloud || in_array(strtolower(substr($sources[$i][2], 3)), $cloud_active_apps)))) {
                $code .= '<li data-value="' . $sources[$i][0] . '">' . $sources[$i][1] . '</li>';
            }
        }
        $code .= '</ul></div>';
    } else {
        $code .= '<div class="sb-select sb-hide"></div>';
    }
    $count = count($tags);
    if ($count) {
        $code .= '<div class="sb-select"><p>' . sb_('All tags') . '</p><ul' . ($count > 8 ? ' class="sb-scroll-area"' : '') . '><li data-value="">' . sb_('All tags') . '</li>';
        for ($i = 0; $i < $count; $i++) {
            $code .= '<li data-value="' . $tags[$i]['tag-name'] . '">' . $tags[$i]['tag-name'] . '</li>';
        }
        $code .= '</ul></div>';
    } else {
        $code .= '<div class="sb-select sb-hide"></div>';
    }
    echo $code .= '</div></div>';
}

function sb_docs_link($id = '', $class = 'sb-docs') {
    if (!sb_is_cloud() || defined('SB_CLOUD_DOCS')) {
        echo '<a href="' . (sb_is_cloud() ? SB_CLOUD_DOCS : 'https://board.support/docs') . $id . '" class="' . $class . '" target="_blank"><i class="sb-icon-help"></i></a>';
    }
}

function sb_get_ticket_custom_fields() {
   $query = "SELECT * FROM custom_fields ORDER BY `order`";
   return sb_db_get($query,false);
}

function sb_get_ticket_statuses() {
   $query = "SELECT * FROM ticket_status ORDER BY `name`";
   return sb_db_get($query,false);
}


function ticket_custom_field_settings($id = '', $class = 'sb-docs') {
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
                                                    foreach($customFields as $field) {
                                                    $code .= '<tr data-id="custom_field_row_'.$field["id"].'">
                                                        <td>'.$field["title"].'</td>
                                                        <td>'.strtoupper($field["type"]).'</td>
                                                        <td>'.($field["required"] ? "Yes" : "No") .'</td>
                                                        <td>'.($field["is_active"] ? "Yes" : "No") .'</td>
                                                        <td>'.($field["order"]) .'</td>
                                                        <td>
                                                            <button data-id="'.$field["id"].'" class="btn btn-sm btn-primary edit-custom-field">
                                                                <i class="bi bi-pencil">Edit</i>
                                                            </button>
                                                            <button data-id="'.$field["id"].'" class="btn btn-sm btn-danger delete-custom-field">
                                                                <i class="bi bi-trash">Delete</i>
                                                            </button>
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
                                <span>Title</span>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div id="type" data-type="select" class="sb-input sb-input-select">
                                <span>Type</span>
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

                            <div id="default_value" data-type="text" class="sb-input">
                                <span>Default Value</span>
                                <input type="text" class="form-control" name="default_value">
                            </div>

                            <div id="order" data-type="number" class="sb-input">
                                <span>Order</span>
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

function ticket_statuses_settings($id = '', $class = 'sb-docs') {
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
                                                    foreach($ticketStatuses as $status) {
                                                    $code .= '<tr data-id="ticket_status_row_'.$status["id"].'">
                                                        <td>'.$status["name"].'</td>
                                                        <td>'.$status["color"].'</td>
                                                        <td>
                                                            <button data-id="'.$status["id"].'" class="btn btn-sm btn-primary edit-ticket-status">
                                                                <i class="bi bi-pencil">Edit</i>
                                                            </button>';

                                                        if($status['id'] > 5) {
                                                        $code .='
                                                            <button data-id="'.$status["id"].'" class="btn btn-sm btn-danger delete-ticket-status">
                                                                <i class="bi bi-trash">Delete</i>
                                                            </button>';
                                                            }

                                                    $code .='
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