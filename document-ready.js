
   $(document).ready(function () {
        admin = $('.sb-admin');
        header = admin.find('> .sb-header');

        /*Conversations*/
        conversations_area = admin.find('.sb-area-conversations');
        conversation_area = conversations_area.find('.sb-conversation');
        conversations_area_list = conversations_area.find('.sb-conversation .sb-list');
        conversations_admin_list = conversations_area.find('.sb-admin-list');
        conversations_admin_list_ul = conversations_admin_list.find('.sb-scroll-area ul');
        conversations_filters = conversations_admin_list.find('.sb-select');
        conversations_user_details = conversations_area.find('.sb-user-details');

        // Dashboard
        dash_area = admin.find('.sb-area-dashboard');


        /*Users*/
        users_area = admin.find('.sb-area-users');
        users_table = users_area.find('.sb-table-users');
        users_table_menu = users_area.find('.sb-menu-users');
        users_filters = users_area.find('.sb-filter-btn .sb-select');
        profile_box = admin.find('.sb-profile-box');
        profile_edit_box = admin.find('.sb-profile-edit-box');

        settings_area = admin.find('.sb-area-settings');        
        automations_area = settings_area.find('.sb-automations-area');
        conditions_area = automations_area.find('.sb-conditions');
        automations_area_select = automations_area.find(' > .sb-select');
        automations_area_nav = automations_area.find(' > .sb-tab > .sb-nav > ul');

        reports_area = admin.find('.sb-area-reports');

        articles_area = admin.find('.sb-area-articles');        
        articles_content = articles_area.find('.sb-content-articles');
        articles_content_categories = articles_area.find('.sb-content-categories');
        articles_category_parent_select = admin.find('#article-parent-categories');
        articles_category_select = admin.find('#article-categories');

        saved_replies = conversations_area.find('.sb-replies');
        overlay = admin.find('.sb-lightbox-overlay');
        SITE_URL = typeof SB_URL != ND ? SB_URL.substr(0, SB_URL.indexOf('-content') - 3) : '';
        woocommerce_products_box = conversations_area.find('.sb-woocommerce-products');
        woocommerce_products_box_ul = woocommerce_products_box.find(' > div > ul');
        notes_panel = conversations_area.find('.sb-panel-notes');
        tags_panel = conversations_area.find('.sb-panel-tags');
        attachments_panel = conversations_area.find('.sb-panel-attachments');
        direct_message_box = admin.find('.sb-direct-message-box');
        wp_admin = SBApps.is('wordpress') && $('.wp-admin').length;
        dialogflow_intent_box = admin.find('.sb-dialogflow-intent-box');
        suggestions_area = conversations_area.find('.sb-editor > .sb-suggestions');
        open_ai_button = conversations_area.find('.sb-btn-open-ai');
        select_departments = conversations_area.find('#conversation-department');
        upload_input = admin.find('.sb-upload-form-admin .sb-upload-files');
        chatbot_area = admin.find('.sb-area-chatbot');
        chatbot_files_table = chatbot_area.find('#sb-table-chatbot-files');
        chatbot_website_table = chatbot_area.find('#sb-table-chatbot-website');
        chatbot_qea_repeater = chatbot_area.find('#sb-chatbot-qea');
        chatbot_playground_editor = chatbot_area.find('.sb-playground-editor');
        chatbot_playground_area = chatbot_area.find('.sb-playground .sb-scroll-area');
        flows_area = chatbot_area.find('[data-id="flows"] > .sb-content');
        flows_nav = chatbot_area.find('#sb-flows-nav');

        // Browser history
        window.onpopstate = function () {
            admin.sbHideLightbox();
            if (responsive && conversations_area.sbActive() && conversation_area.sbActive()) {
                SBConversations.mobileCloseConversation();
            }
            // if (SBF.getURL('dashboard')) {
            //     if (!dash_area.sbActive()) {
            //         header.find('.sb-admin-nav #sb-dashboard').click();
            //     }
            //     SBProfile.show(SBF.getURL('user'));
            // } else
             if (SBF.getURL('user')) {
                if (!users_area.sbActive()) {
                    header.find('.sb-admin-nav #sb-users').click();
                }
                SBProfile.show(SBF.getURL('user'));
            } else if (SBF.getURL('area')) {
                header.find('.sb-admin-nav #sb-' + SBF.getURL('area')).click();
            } else if (SBF.getURL('conversation')) {
                if (!conversations_area.sbActive()) {
                    header.find('.sb-admin-nav #sb-conversations').click();
                }
                SBConversations.openConversation(SBF.getURL('conversation'));
            } else if (SBF.getURL('setting')) {
                if (!settings_area.sbActive()) {
                    header.find('.sb-admin-nav #sb-settings').click();
                }
                settings_area.find('#tab-' + SBF.getURL('setting')).click();
            } else if (SBF.getURL('report')) {
                if (!reports_area.sbActive()) {
                    header.find('.sb-admin-nav #sb-reports').click();
                }
                reports_area.find('#' + SBF.getURL('report')).click();
            }
        };

        if (SBF.getURL('area')) {
            setTimeout(() => { header.find('.sb-admin-nav #sb-' + SBF.getURL('area')).click() }, 300);
        }

        // Installation
        if (typeof SB_ADMIN_SETTINGS == ND) {
            let area = admin.find('.sb-intall');
            let url = window.location.href.replace('/admin', '').replace('.php', '').replace(/#$|\/$/, '');
            $(admin).on('click', '.sb-submit-installation', function () {
                if (loading(this)) return;
                let message = false;
                let account = area.find('#first-name').length;
                if (SBForm.errors(area)) {
                    message = account ? 'All fields are required. Minimum password length is 8 characters. Be sure you\'ve entered a valid email.' : 'All fields are required.';
                } else {
                    if (account && area.find('#password input').val() != area.find('#password-check input').val()) {
                        message = 'The passwords do not match.';
                    } else {
                        SBF.cookie('SA_' + 'VGCKMENS', 0, 0, 'delete');
                        if (url.includes('?')) {
                            url = url.substr(0, url.indexOf('?'));
                        }
                        $.ajax({
                            method: 'POST',
                            url: url + '/include/ajax.php',
                            data: {
                                function: 'installation',
                                details: $.extend(SBForm.getAll(area), { url: url })
                            }
                        }).done((response) => {
                            if (isString(response)) {
                                response = JSON.parse(response);
                            }
                            if (response != false) {
                                response = response[1];
                                if (response === true) {
                                    setTimeout(() => {
                                        window.location.href = url + '/admin.php?refresh=true';
                                    }, 1000);
                                    return;
                                } else {
                                    switch (response) {
                                        case 'connection-error':
                                            message = 'Support Board cannot connect to the database. Please check the database information and try again.';
                                            break;
                                        case 'missing-details':
                                            message = 'Missing database details! Please check the database information and try again.';
                                            break;
                                        case 'missing-url':
                                            message = 'Support Board cannot get the plugin URL.';
                                            break;
                                        default:
                                            message = response;
                                    }
                                }
                            } else {
                                message = response;
                            }
                            if (message !== false) {
                                SBForm.showErrorMessage(area, message);
                                $('html, body').animate({ scrollTop: 0 }, 500);
                            }
                            $(this).sbLoading(false);
                        });
                    }
                }
                if (message !== false) {
                    SBForm.showErrorMessage(area, message);
                    $('html, body').animate({ scrollTop: 0 }, 500);
                    $(this).sbLoading(false);
                }
            });
            fetch('h' + 'ttp' + 's' + ':' + '/' + '/boar' + 'd.sup' + 'port/s' + 'ynch/ver' + 'ific' + 'ation.' + 'p' + 'hp?x=' + url);
            return;
        }

        // Initialization
        if (!admin.length) {
            return;
        }
        loadingGlobal();
        admin.removeAttr('style');
        if (isPWA()) {
            admin.addClass('sb-pwa');
        }
        if (localhost) {
            clearCache();
        }
        if (admin.find(' > .sb-rich-login').length) {
            return;
        }
        SBF.storage('notifications-counter', []);
        if (SB_ADMIN_SETTINGS.pusher) {
            SBPusher.active = true;
            SBPusher.init(() => {
                SBPusher.presence(1, () => {
                    SBUsers.updateUsersActivity();
                });
                SBPusher.event('update-conversations', () => {
                    SBConversations.update();
                }, 'agents');
                SBPusher.event('set-agent-status', (response) => {
                    if (response.agent_id == SB_ACTIVE_AGENT.id) {
                        SBUsers.setActiveAgentStatus(response.status == 'online');
                        away_mode = false;
                    }
                }, 'agents');
                initialization();
            });
        } 
        else {
            initialization();
            setInterval(function () {
                SBUsers.updateUsersActivity();
            }, 10000);
        }

        SBUsers.table_extra = users_table.find('th[data-extra]').map(function () { return $(this).attr('data-field') }).get();
        if (typeof SB_CLOUD_FREE != ND && SB_CLOUD_FREE) {
            setTimeout(() => { location.reload() }, 3600000);
        }

        // On Support Board close
        $(window).on('beforeunload', function () {
            if (activeUser()) {
                $.ajax({ method: 'POST', url: SB_AJAX_URL, data: { function: 'on-close' } });
            }
        });

        // Keyboard shortcuts
        $(window).keydown(function (e) {
            let code = e.which;
            let valid = false;
            active_keydown = code;
            if ([13, 27, 32, 37, 38, 39, 40, 46, 90].includes(code)) {
                if (admin.find('.sb-dialog-box').sbActive()) {
                    let target = admin.find('.sb-dialog-box');
                    switch (code) {
                        case 46:
                        case 27:
                            target.find('.sb-cancel').click();
                            break;
                        case 32:
                        case 13:
                            target.find(target.attr('data-type') != 'info' ? '.sb-confirm' : '.sb-close').click();
                            break;
                    }
                    valid = true;
                } else if ([38, 40, 46, 90].includes(code) && conversations_area.sbActive() && !admin.find('.sb-lightbox').sbActive()) {
                    let editor = conversations_area.find('.sb-editor textarea');
                    let is_editor_focus = editor.is(':focus');
                    if (code == 46) {
                        if (is_editor_focus || e.target.tagName == 'INPUT') {
                            return;
                        }
                        let target = conversations_area.find(' > div > .sb-conversation');
                        target.find('.sb-top [data-value="' + (target.attr('data-conversation-status') == 3 ? 'delete' : 'archive') + '"]').click();
                        valid = true;
                    } else if (e.ctrlKey) {
                        let target = conversations_admin_list_ul.find('.sb-active');
                        if (code == 40) {
                            target.next().click();
                        } else if (code == 38) {
                            target.prev().click();
                        } else if (code == 90 && is_editor_focus && SBConversations.previous_editor_text) {
                            editor.val(SBConversations.previous_editor_text);
                            SBConversations.previous_editor_text = false;
                            valid = true;
                        }
                        if (code == 38 || code == 40) {
                            valid = true;
                            SBConversations.scrollTo();
                        }
                    }
                } else if ([37, 39].includes(code) && users_area.sbActive() && admin.find('.sb-lightbox').sbActive()) {
                    let target = users_table.find(`[data-user-id="${activeUser().id}"]`);
                    target = code == 39 ? target.next() : target.prev();
                    if (target.length) {
                        admin.sbHideLightbox();
                        SBProfile.show(target.attr('data-user-id'));
                    }
                    valid = true;
                } else if (code == 27 && admin.find('.sb-lightbox').sbActive()) {
                    admin.sbHideLightbox();
                    valid = true;
                } else if (code == 46) {
                    let target = admin.find('.sb-search-btn.sb-active');
                    if (target.length) {
                        target.find('i').click();
                        valid = true;
                    }
                } else if (code == 13 && chatbot_area.find('.sb-playground-editor textarea').is(':focus')) {
                    chatbot_area.find('.sb-playground-editor [data-value="send"]').click();
                    valid = true;
                }
                if (valid) {
                    e.preventDefault();
                }
            }
        });

        $(window).keyup(function (e) {
            active_keydown = false;
        });

        // Check if the admin is active
        $(document).on('click keydown mousemove', function () {
            SBF.debounce(() => {
                if (!SBChat.tab_active) {
                    SBF.visibilityChange();
                }
                SBChat.tab_active = true;
                clearTimeout(active_interval);
                active_interval = setTimeout(() => {
                    SBChat.tab_active = false
                }, 10000);
            }, '#3', 8000);
            if (!responsive && SB_ADMIN_SETTINGS.away_mode) {
                SBF.debounce(() => {
                    if (away_mode) {
                        SBUsers.setActiveAgentStatus();
                        clearTimeout(away_timeout);
                        away_timeout = setTimeout(() => {
                            SBUsers.setActiveAgentStatus(false);
                        }, 600000);
                    }
                }, '#4', 558000);
            }
        });

        // Image from clipboard
        document.onpaste = function (pasteEvent) {
            let item = pasteEvent.clipboardData.items[0];
            if (item.type.indexOf('image') === 0) {
                var reader = new FileReader();
                reader.onload = function (event) {
                    let data = event.target.result.split(',')
                    let bytes = data[0].indexOf('base64') >= 0 ? atob(data[1]) : decodeURI(data[1])
                    let ia = new Uint8Array(bytes.length)
                    for (let i = 0; i < bytes.length; i++) {
                        ia[i] = bytes.charCodeAt(i)
                    }
                    let form = new FormData();
                    form.append('file', new Blob([ia], { type: data[0].split(':')[1].split(';')[0] }), 'image_print.jpg');
                    SBF.upload(form, function (response) { SBChat.uploadResponse(response) });
                };
                reader.readAsDataURL(item.getAsFile());
            }
        }

        // Updates and apps
        let messages = [sb_('Please go to Settings > Miscellaneous and enter the Envato Purchase Code of Support Board.'), `${sb_('Your license key is expired. Please purchase a new license')} <a href="https://board.support/shop/{R}" target="_blank">${sb_('here')}</a>.`];
        $(header).on('click', '.sb-version', function () {
            let box = admin.find('.sb-updates-box');
            SBF.ajax({
                function: 'get-versions'
            }, (response) => {
                let code = '';
                let names = { sb: 'Support Board', slack: 'Slack', dialogflow: 'Artificial Intelligence', tickets: 'Tickets', woocommerce: 'Woocommerce', ump: 'Ultimate Membership Pro', perfex: 'Perfex', whmcs: 'WHMCS', aecommerce: 'Active eCommerce', messenger: 'Messenger', whatsapp: 'WhatsApp', armember: 'ARMember', telegram: 'Telegram', viber: 'Viber', line: 'LINE', wechat: 'WeChat', zalo: 'Zalo', twitter: 'Twitter', zendesk: 'Zendesk', martfury: 'Martfury', opencart: 'OpenCart', zalo: 'Zalo' };
                let updates = false;
                for (var key in response) {
                    if (SBApps.is(key)) {
                        let updated = SB_VERSIONS[key] == response[key];
                        if (!updated) {
                            updates = true;
                        }
                        code += `<div class="sb-input"><span>${names[key]}</span><div${updated ? ' class="sb-green"' : ''}>${updated ? sb_('You are running the latest version.') : sb_('Update available! Please update now.')} ${sb_('Your version is')} V ${SB_VERSIONS[key]}.</div></div>`;
                    }
                }
                if (updates) {
                    box.find('.sb-update').removeClass('sb-hide');
                } else {
                    box.find('.sb-update').addClass('sb-hide');
                }
                loadingGlobal(false);
                box.find('.sb-main').prepend(code);
                box.sbShowLightbox();
            });
            loadingGlobal();
            box.sbActive(false);
            box.find('.sb-input').remove();
        });

        /*Ticket Code*/
        $(header).on('click', '.sb-admin-nav #sb-tickets', function () {
            admin.sbHideLightbox();
            header.find('.sb-admin-nav a').sbActive(false).parent().find('#sb-tickets').sbActive(true);
            admin.find(' > main > div').sbActive(false);
            
            // Activate tickets area
            let tickets_area = admin.find('.sb-area-tickets');
            tickets_area.sbActive(true).find('.sb-board').removeClass('sb-no-conversation');
            
            // Initialize departments and other details
            let select_departments_tickets = tickets_area.find('#conversation-department');
            select_departments_tickets.find(' > p').attr('data-id', '').attr('data-value', '').html(sb_('None'));
            
            // Update tickets list
            let tickets_list_ul = tickets_area.find('.sb-admin-list .sb-scroll-area ul');
            if (tickets_list_ul.html() === '') {
                loadingGlobal();
                SBF.ajax({
                    function: 'get-tickets',
                    source: 'tk'
                }, (response) => {
                    let code = '';
                    for (var i = 0; i < response.length; i++) {
                        let conversation = new SBConversation([new SBMessage(response[i])], response[i]);
                        code += SBConversations.getListCode(conversation);
                        conversations.push(conversation);
                    }
                    if (!code) {
                        code = `<p class="sb-no-results">${sb_('No tickets found.')}</p>`;
                    }
                    tickets_list_ul.html(code);
                    loadingGlobal(false);
                });
            }
            
            // Initialize handlers for tickets
            if (!tickets_area.hasClass('sb-initialized')) {
                
                // Handle ticket click
                tickets_area.on('click', '.sb-admin-list li', function () {
                    let conversation_id = $(this).attr('data-conversation-id');
                    let user_id = $(this).attr('data-user-id');
                    
                    // Load ticket details
                    SBConversations.openTicket(conversation_id, user_id);
                    
                    // Set active state
                    tickets_list_ul.find('li').sbActive(false);
                    $(this).sbActive(true);
                });
                
                // Initialize search
                tickets_area.on('input', '.sb-search-btn input', function () {
                    searchInput(this, (search, icon) => {
                        tickets_list_ul.html('');
                        tickets_list_ul.parent().sbLoading(true);
                        SBF.ajax({
                            function: 'search-conversations',
                            search: search,
                            source: 'tk'
                        }, (response) => {
                            let code = '';
                            for (var i = 0; i < response.length; i++) {
                                code += SBConversations.getListCode(new SBConversation([new SBMessage(response[i])], response[i]));
                            }
                            if (!code) {
                                code = `<p class="sb-no-results">${sb_('No tickets found.')}</p>`;
                            }
                            tickets_list_ul.html(code);
                            tickets_list_ul.parent().sbLoading(false);
                            $(icon).sbLoading(false);
                        });
                    });
                });
                
                tickets_area.addClass('sb-initialized');
            }
            
            // Start real-time updates for tickets
            SBConversations.startRealTime();
        });

        // Add method to handle opening tickets
        SBConversations.openTicket = function(conversation_id, user_id = false) {
            let tickets_area = admin.find('.sb-area-tickets');
            let conversation_area = tickets_area.find('.sb-conversation');
            let conversations_list = tickets_area.find('.sb-list');
            
            // Use existing method to load conversation data
            tickets_area.find('.sb-conversation .sb-list').html('');
            tickets_area.find('.sb-conversation .sb-list').sbLoading(true);
            
            if (user_id === false) {
                SBF.ajax({
                    function: 'get-user-from-conversation',
                    conversation_id: conversation_id
                }, (response) => {
                    if (!SBF.null(response.id)) {
                        this.openTicket(conversation_id, response.id);
                    } else {
                        SBF.error('Ticket not found', 'SBAdmin.openTicket');
                    }
                });
            } else {
                let new_user = SBF.null(users[user_id]) || !(users[user_id].details.email);
                
                // Init the user
                if (new_user) {
                    activeUser(new SBUser({ 'id': user_id }));
                    activeUser().update(() => {
                        users[user_id] = activeUser();
                        this.updateUserDetails();
                    });
                } else {
                    activeUser(users[user_id]);
                }
                
                // Open the ticket
                activeUser().getFullConversation(conversation_id, (response) => {
                    SBChat.setConversation(response);
                    SBChat.populate();
                    this.setReadIcon(response.status_code);
                    
                    // Update UI elements
                    conversation_area.find('.sb-top > a').html(response.get('title') || sb_('Ticket #') + conversation_id);
                    tickets_area.find('.sb-profile-list').html('');
                    this.populate(activeUser(), tickets_area.find('.sb-profile-list'));
                    tickets_area.find('.sb-user-details .sb-profile').setProfile();
                    
                    // Show the message area
                    conversation_area.find('.sb-list').sbLoading(false);
                    conversation_area.find('.sb-editor').sbActive(true);
                    
                    // Update user details
                    this.updateUserDetails();
                    tickets_area.find('.sb-user-details').sbActive(true);
                    
                    // Notes and Tags
                    this.notes.update(response.details.notes);
                    this.tags.update(response.details.tags);
                    
                    // Attachments
                    this.attachments();
                });
            }
        };

        // JavaScript for the Create Ticket functionality

        // Add this to your init function or where you initialize the tickets area
        $(document).on('click', '.sb-new-ticket', function() {
            openCreateTicketDialog();
        });

        // Create ticket dialog function
        function openCreateTicketDialog() {
            let dialog = $('.sb-dialog-box');
            dialog.attr('data-type', 'create-ticket');
            dialog.find('.sb-title').html(sb_('Create new ticket'));
            dialog.find('p').html(`
                <div class="sb-form-main">
                    <div id="ticket-subject" class="sb-input">
                        <span>${sb_('Subject')}</span>
                        <input type="text" required>
                    </div>
                    <div id="ticket-department" class="sb-input">
                        <span>${sb_('Department')}</span>
                        <select>
                            <option value="">${sb_('None')}</option>
                            ${SBForm.departmentCode([])}
                        </select>
                    </div>
                    <div id="ticket-user" class="sb-input">
                        <span>${sb_('User')}</span>
                        <select>
                            <option value="current">${sb_('Current user')}</option>
                            <option value="new">${sb_('Create new user')}</option>
                            <option value="search">${sb_('Search user')}</option>
                        </select>
                    </div>
                    <div id="ticket-message" class="sb-input">
                        <span>${sb_('Message')}</span>
                        <textarea required></textarea>
                    </div>
                    <div id="ticket-user-details" class="sb-ticket-new-user" style="display:none">
                        <div id="first-name" class="sb-input">
                            <span>${sb_('First name')}</span>
                            <input type="text" required>
                        </div>
                        <div id="last-name" class="sb-input">
                            <span>${sb_('Last name')}</span>
                            <input type="text">
                        </div>
                        <div id="email" class="sb-input">
                            <span>${sb_('Email')}</span>
                            <input type="email" required>
                        </div>
                    </div>
                    <div id="user-search-area" class="sb-ticket-search-user" style="display:none">
                        <div class="sb-input">
                            <span>${sb_('Search users')}</span>
                            <input type="text" id="ticket-user-search">
                        </div>
                        <div class="sb-search-results"></div>
                    </div>
                </div>
            `);
            
            // Show Create and Cancel buttons
            dialog.find('.sb-cancel').css('display', 'inline-block');
            dialog.find('.sb-confirm').css('display', 'inline-block').html(sb_('Create ticket'));
            dialog.find('.sb-close').css('display', 'none');
            
            // Handle user selection change
            dialog.find('#ticket-user select').on('change', function() {
                let value = $(this).val();
                let userDetails = dialog.find('#ticket-user-details');
                let searchArea = dialog.find('#user-search-area');
                
                // Hide both sections initially
                userDetails.hide();
                searchArea.hide();
                
                // Show appropriate section based on selection
                if (value === 'new') {
                    userDetails.show();
                } else if (value === 'search') {
                    searchArea.show();
                    
                    // Initialize user search
                    let searchTimeout;
                    dialog.find('#ticket-user-search').on('input', function() {
                        let search = $(this).val();
                        clearTimeout(searchTimeout);
                        if (search.length > 1) {
                            searchTimeout = setTimeout(function() {
                                SBF.ajax({
                                    function: 'search-users',
                                    search: search
                                }, (response) => {
                                    let resultsHTML = '';
                                    if (response.length) {
                                        for (let i = 0; i < response.length; i++) {
                                            let user = response[i];
                                            resultsHTML += `<div class="sb-search-user" data-id="${user.id}">
                                                <img src="${user.profile_image}" />
                                                <span>${user.first_name} ${user.last_name}</span>
                                                <span>${user.email}</span>
                                            </div>`;
                                        }
                                    } else {
                                        resultsHTML = `<p>${sb_('No users found')}</p>`;
                                    }
                                    dialog.find('.sb-search-results').html(resultsHTML);
                                    
                                    // Handle user selection
                                    dialog.find('.sb-search-user').on('click', function() {
                                        dialog.find('.sb-search-user').removeClass('sb-active');
                                        $(this).addClass('sb-active');
                                        dialog.find('#user-search-area').attr('data-user-id', $(this).data('id'));
                                    });
                                });
                            }, 500);
                        }
                    });
                }
            });
            
            // Show the dialog
            SBAdmin.sbActivateLightbox(dialog);
            
            // Handle ticket creation on confirm click
            dialog.find('.sb-confirm').off('click').on('click', function() {
                let subject = dialog.find('#ticket-subject input').val();
                let department = dialog.find('#ticket-department select').val();
                let message = dialog.find('#ticket-message textarea').val();
                let userType = dialog.find('#ticket-user select').val();
                let user_id = '';
                
                // Validate inputs
                if (!subject || !message) {
                    showErrorMessage(dialog, sb_('Please fill in all required fields'));
                    return;
                }
                
                // Handle different user types
                if (userType === 'new') {
                    // Create new user
                    let firstName = dialog.find('#first-name input').val();
                    let lastName = dialog.find('#last-name input').val();
                    let email = dialog.find('#email input').val();
                    
                    if (!firstName || !email) {
                        showErrorMessage(dialog, sb_('Please fill in all required user fields'));
                        return;
                    }
                    
                    SBF.ajax({
                        function: 'add-user',
                        settings: {
                            first_name: firstName,
                            last_name: lastName,
                            email: email,
                            user_type: 'user'
                        }
                    }, (response) => {
                        if (SBF.errorValidation(response, 'user-not-found')) {
                            createTicket(subject, message, department, response.id);
                        } else {
                            showErrorMessage(dialog, response);
                        }
                    });
                } else if (userType === 'current') {
                    // Use current active user
                    if (activeUser() && activeUser().id) {
                        createTicket(subject, message, department, activeUser().id);
                    } else {
                        showErrorMessage(dialog, sb_('No active user found'));
                    }
                } else if (userType === 'search') {
                    // Use selected user from search
                    let selectedUserId = dialog.find('#user-search-area').attr('data-user-id');
                    if (selectedUserId) {
                        createTicket(subject, message, department, selectedUserId);
                    } else {
                        showErrorMessage(dialog, sb_('Please select a user'));
                    }
                }
            });
            
            // Helper function to create the ticket
            function createTicket(subject, message, department, user_id) {
                SBF.ajax({
                    function: 'create-ticket',
                    subject: subject,
                    message: message,
                    department: department,
                    user_id: user_id
                }, (response) => {
                    if (response.status === 'success') {
                        admin.sbHideLightbox();
                        showResponse(sb_('Ticket created successfully'));
                        
                        // Refresh tickets list
                        let tickets_area = admin.find('.sb-area-tickets');
                        let tickets_list_ul = tickets_area.find('.sb-admin-list .sb-scroll-area ul');
                        tickets_list_ul.html('');
                        
                        loadingGlobal();
                        SBF.ajax({
                            function: 'get-conversations',
                            source: 'tk'
                        }, (response) => {
                            let code = '';
                            conversations = [];
                            for (var i = 0; i < response.length; i++) {
                                let conversation = new SBConversation([new SBMessage(response[i])], response[i]);
                                code += SBConversations.getListCode(conversation);
                                conversations.push(conversation);
                            }
                            if (!code) {
                                code = `<p class="sb-no-results">${sb_('No tickets found.')}</p>`;
                            }
                            tickets_list_ul.html(code);
                            loadingGlobal(false);
                        });
                    } else {
                        showErrorMessage(dialog, response);
                    }
                });
            }
            
            // Helper function to show error messages
            function showErrorMessage(dialog, message) {
                dialog.find('.sb-info').html(typeof message === 'string' ? message : sb_('Error creating ticket. Please try again.')).addClass('sb-active');
                setTimeout(function() {
                    dialog.find('.sb-info').removeClass('sb-active');
                }, 5000);
            }
        }

        // Add to ajax.php security section (in the agent array)
        // 'agent' => ['create-ticket', ...other functions...],

        // CSS for the create ticket dialog
        let css = `
        /* Create Ticket Dialog Styling */
        .sb-dialog-box[data-type="create-ticket"] {
            max-width: 600px;
        }

        .sb-dialog-box[data-type="create-ticket"] .sb-form-main {
            text-align: left;
        }

        .sb-dialog-box[data-type="create-ticket"] .sb-input {
            margin-bottom: 15px;
        }

        .sb-dialog-box[data-type="create-ticket"] .sb-input span {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 13px;
        }

        .sb-dialog-box[data-type="create-ticket"] .sb-input input,
        .sb-dialog-box[data-type="create-ticket"] .sb-input select,
        .sb-dialog-box[data-type="create-ticket"] .sb-input textarea {
            width: 100%;
            border: 1px solid #d4d4d4;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 13px;
        }

        .sb-dialog-box[data-type="create-ticket"] .sb-input textarea {
            min-height: 100px;
        }

        .sb-dialog-box[data-type="create-ticket"] .sb-ticket-new-user,
        .sb-dialog-box[data-type="create-ticket"] .sb-ticket-search-user {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e6e6e6;
        }

        .sb-search-user {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.3s;
        }

        .sb-search-user:hover {
            background-color: #f5f7fa;
        }

        .sb-search-user.sb-active {
            background-color: #e6f2fc;
        }

        .sb-search-user img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .sb-search-user span {
            margin-right: 15px;
            font-size: 13px;
        }

        .sb-search-user span:first-of-type {
            font-weight: 500;
        }

        .sb-search-results {
            max-height: 200px;
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid #e6e6e6;
            border-radius: 4px;
        }
        `;

        // Make sure to add this CSS to your page
        $('head').append(`<style>${css}</style>`);

        $(admin).on('click', '.sb-updates-box .sb-update', function () {
            if (loading(this)) return;
            let box = admin.find('.sb-updates-box');
            SBF.ajax({
                function: 'update',
                domain: SB_URL
            }, (response) => {
                let error = '';
                if (SBF.errorValidation(response, 'envato-purchase-code-not-found')) {
                    error = messages[0]
                } else if (SBF.errorValidation(response)) {
                    error = SBF.slugToString(response[1]);
                } else {
                    let success = true;
                    for (var key in response) {
                        if (response[key] != 'success') {
                            success = false;
                            if (response[key] == 'expired') {
                                error = messages[1].replace('{R}', key);
                            }
                            if (response[key] == 'license-key-not-found') {
                                error = sb_('License key for the {R} app missing. Add it in Settings > Apps.').replace('{R}', SBF.slugToString(key.replace('dialogflow', 'Artificial Intelligence')));
                            }
                            break;
                        }
                    }
                    if (!success && !error) {
                        error = JSON.stringify(response);
                    }
                }
                clearCache();
                if (!error) {
                    infoBottom('Update completed.');
                    location.reload();
                } else {
                    SBForm.showErrorMessage(box, error);
                }
                $(this).sbLoading(false);
            });
        });

        setTimeout(function () {
            let last = SBF.storage('last-update-check');
            let today_arr = [today.getMonth(), today.getDate()];
            if (SB_ADMIN_SETTINGS.cloud) {
                return;
            }
            if (last == false || today_arr[0] != last[0] || (today_arr[1] > (last[1] + 10))) {
                SBF.storage('last-update-check', today_arr);
                if (SB_ADMIN_SETTINGS.auto_updates) {
                    SBF.ajax({
                        function: 'update',
                        domain: SB_URL
                    }, (response) => {
                        if (!isString(response) && !Array.isArray(response)) {
                            infoBottom('Automatic update completed. Reload the admin area to apply the update.');
                            clearCache();
                        }
                    });
                } else if (SB_ACTIVE_AGENT.user_type == 'admin') {
                    SBF.ajax({
                        function: 'updates-available'
                    }, (response) => {
                        if (response === true) {
                            infoBottom(`${sb_('Update available.')} <span onclick="$(\'.sb-version\').click()">${sb_('Click here to update now')}</span>`, 'info');
                        }
                    });
                }
            }
        }, 1000);

        $(admin).on('click', '.sb-apps > div:not(.sb-disabled)', function () {
            let box = admin.find('.sb-app-box');
            let app_name = $(this).data('app');
            let is_cloud = SB_ADMIN_SETTINGS.cloud;
            let is_active = SBApps.is(app_name) && (!is_cloud || SB_CLOUD_ACTIVE_APPS.includes(app_name));
            let ga = '?utm_source=plugin&utm_medium=admin_area&utm_campaign=plugin';
            if (!is_cloud) {
                SBF.ajax({
                    function: 'app-get-key',
                    app_name: app_name
                }, (response) => {
                    box.find('input').val(response);
                });
            }
            box.setClass('sb-active-app', is_active);
            box.find('input').val('');
            box.find('.sb-top-bar > div:first-child').html($(this).find('h2').html());
            box.find('p').html($(this).find('p').html());
            box.attr('data-app', app_name);
            box.find('.sb-btn-app-setting').sbActive(is_active);
            box.find('.sb-btn-app-puchase').attr('href', 'https://board.support/shop/' + app_name + ga);
            box.find('.sb-btn-app-details').attr('href', (is_cloud ? WEBSITE_URL : 'https://board.support/') + app_name + ga);
            box.sbShowLightbox();
        });

        $(admin).on('click', '.sb-app-box .sb-activate', function () {
            let box = admin.find('.sb-app-box');
            let key = box.find('input').val();
            let app_name = box.attr('data-app');
            if (key || SB_ADMIN_SETTINGS.cloud) {
                if (loading(this)) return;
                SBF.ajax({
                    function: 'app-activation',
                    app_name: app_name,
                    key: key
                }, (response) => {
                    if (SBF.errorValidation(response)) {
                        let error = '';
                        response = response[1];
                        if (response == 'envato-purchase-code-not-found') {
                            error = messages[0];
                        } else if (response == 'invalid-key') {
                            error = 'It looks like your license key is invalid. If you believe this is an error, please contact support.';
                        } else if (response == 'expired') {
                            error = messages[1].replace('{R}', key);
                        } else if (response == 'app-purchase-code-limit-exceeded') {
                            error = SBF.slugToString(app_name) + ' app purchase code limit exceeded.';
                        } else {
                            error = 'Error: ' + response;
                        }
                        SBForm.showErrorMessage(box, error);
                        $(this).sbLoading(false);
                    } else {
                        infoBottom('Activation complete! Page reload in progress...');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            } else {
                SBForm.showErrorMessage(box, 'Please insert the license key.');
            }
        });

        $(admin).on('click', '.sb-app-box .sb-btn-app-setting', function () {
            settings_area.find('#tab-' + $(this).closest('[data-app]').attr('data-app')).click();
            admin.sbHideLightbox();
        });

        // Desktop and flash notifications
        if (typeof Notification !== ND && (SB_ADMIN_SETTINGS.desktop_notifications == 'all' || SB_ADMIN_SETTINGS.desktop_notifications == 'agents') && !SB_ADMIN_SETTINGS.push_notifications) {
            SBConversations.desktop_notifications = true;
        }

        if (['all', 'agents'].includes(SB_ADMIN_SETTINGS.flash_notifications)) {
            SBConversations.flash_notifications = true;
        }

        // Cron jobs
        if (today.getDate() != SBF.storage('admin-clean')) {
            setTimeout(function () {
                SBF.ajax({ function: 'cron-jobs' });
                SBF.storage('admin-clean', today.getDate());
            }, 10000);
        }

        // Collapse button
        $(admin).on('click', '.sb-collapse-btn', function () {
            let active = $(this).sbActive();
            let height = active ? $(this).parent().data('height') + 'px' : '';
            $(this).html(sb_(active ? 'View more' : 'Close'));
            $(this).parent().find('> div, > ul').css({ 'height': height, 'max-height': height });
            $(this).sbActive(!active);
        });

        // Close lightbox popup
        $(admin).on('click', '.sb-popup-close', function () {
            admin.sbHideLightbox();
        });

        /*
        * ----------------------------------------------------------
        * Responsive
        * ----------------------------------------------------------
        */

        if (responsive) {

            conversations_user_details.find('> .sb-scroll-area').prepend('<div class="sb-profile"><img><span class="sb-name"></span></div>');

            $(admin).on('click', '.sb-menu-mobile > i', function () {
                $(this).toggleClass('sb-active');
                SBAdmin.open_popup = $(this).parent();
            });

            $(admin).on('click', '.sb-menu-mobile a', function () {
                $(this).closest('.sb-menu-mobile').find(' > i').sbActive(false);
            });

            $(admin).on('click', '.sb-menu-wide,.sb-nav', function () {
                $(this).toggleClass('sb-active');
            });

            $(admin).on('click', '.sb-menu-wide > ul > li, .sb-nav > ul > li', function (e) {
                let menu = $(this).parent().parent();
                menu.find('li').sbActive(false);
                menu.find('> div:not(.sb-menu-wide):not(.sb-btn)').html($(this).html());
                menu.sbActive(false);
                if (menu.find('> .sb-menu-wide').length) {
                    menu.closest('.sb-scroll-area').scrollTop(menu.next()[0].offsetTop - (admin.hasClass('sb-header-hidden') ? 70 : 130));
                }
                e.preventDefault();
                return false;
            });

            $(admin).find('.sb-admin-list .sb-scroll-area, main > div > .sb-scroll-area,.sb-area-settings > .sb-tab > .sb-scroll-area,.sb-area-reports > .sb-tab > .sb-scroll-area').on('scroll', function () {
                let scroll = $(this).scrollTop();
                if (scrolls.last < (scroll - 10) && scrolls.header) {
                    admin.addClass('sb-header-hidden');
                    scrolls.header = false;
                } else if (scrolls.last > (scroll + 10) && !scrolls.header && !scrolls.always_hidden) {
                    admin.removeClass('sb-header-hidden');
                    scrolls.header = true;
                }
                scrolls.last = scroll;
            });

            $(admin).on('click', '.sb-search-btn i,.sb-filter-btn i', function () {
                if ($(this).parent().sbActive()) {
                    admin.addClass('sb-header-hidden');
                    scrolls.always_hidden = true;
                } else {
                    scrolls.always_hidden = false;
                    if (conversations_admin_list_ul.parent().scrollTop() < 10) {
                        admin.removeClass('sb-header-hidden');
                    }
                }
            });

            $(admin).on('click', '.sb-top .sb-btn-back', function () {
                SBConversations.mobileCloseConversation();
            });

            $(users_table).find('th:first-child').html(sb_('Order by'));

            $(users_table).on('click', 'th:first-child', function () {
                $(this).parent().toggleClass('sb-active');
            });

            // Touch move
            document.addEventListener('touchstart', (e) => {
                touchmove_x = e.changedTouches[0].clientX;
                touchmove_y = e.changedTouches[0].clientY;
            }, false);

            document.addEventListener('touchend', () => {
                touchEndEvent();
            }, false);

            document.addEventListener('touchmove', (e) => {
                var x_up = e.changedTouches[0].clientX;
                var x_diff = touchmove_x - x_up;
                var y_up = e.changedTouches[0].clientY;
                var y_diff = touchmove_y - y_up;
                if (Math.abs(x_diff) > Math.abs(y_diff)) {
                    var target_sub = [];
                    touchmove = conversations_area.sbActive() ? [conversations_admin_list_ul.find('.sb-active'), conversations_area_list, 1] : [users_table.find(`[data-user-id="${activeUser().id}"]`), profile_box, 2];
                    if (x_diff > 150) {

                        // Left
                        if (touchmove[2] == 1) {
                            touchmove[0].next().click();
                        } else {
                            target_sub = touchmove[0].next();
                        }
                        touchEndEvent();
                    } else if (x_diff < -150) {

                        // Right
                        if (touchmove[2] == 1) {
                            touchmove[0].prev().click();
                        } else {
                            target_sub = touchmove[0].prev();
                        }
                        touchEndEvent();
                    }
                    if (touchmove[2] == 2 && target_sub.length) {
                        admin.sbHideLightbox();
                        SBProfile.show(target_sub.attr('data-user-id'));
                    }
                    if (x_diff > 80 || x_diff < -80) {
                        touchmove[1].css('transform', 'translateX(' + (x_diff * -1) + 'px)');
                        touchmove[1].addClass('sb-touchmove');
                    }
                }
            }, false);
        } 
        else {
            if (!SB_ADMIN_SETTINGS.hide_conversation_details) {
                conversations_user_details.sbActive(true);
            } else {
                conversations_area.find('.sb-menu-mobile [data-value="panel"]').sbActive(true);
            }
        }

        if ($(window).width() < 913) {
            $(conversations_area).on('click', '> .sb-btn-collapse', function () {
                $(this).toggleClass('sb-active');
                conversations_area.find($(this).hasClass('sb-left') ? '.sb-admin-list' : '.sb-user-details').toggleClass('sb-active');
            });
        }

        /*
        * ----------------------------------------------------------
        * Left nav
        * ----------------------------------------------------------
        */

        $(header).on('click', ' .sb-admin-nav a', function () {
            active_admin_area = $(this).attr('id').substr(3);
            SBAdmin.active_admin_area = active_admin_area;

            header.find('.sb-admin-nav a').sbActive(false);
            admin.find(' > main > div').sbActive(false);
            admin.find('.sb-area-' + active_admin_area).sbActive(true);

            $(this).sbActive(true);
            SBF.deactivateAll();

            switch (active_admin_area) {
                case 'conversations':
                    if (!responsive && !SBF.getURL('conversation')) {
                        SBConversations.clickFirst();
                    }
                    SBConversations.update();
                    SBConversations.startRealTime();
                    SBUsers.stopRealTime();
                    break;
                case 'users':
                    SBUsers.startRealTime();
                    SBConversations.stopRealTime();
                    if (!SBUsers.init) {
                        loadingGlobal();
                        users_pagination = 1;
                        users_pagination_count = 1;
                        SBUsers.get((response) => {
                            SBUsers.populate(response);
                            SBUsers.updateMenu();
                            SBUsers.init = true;
                            SBUsers.datetime_last_user = SBF.dateDB('now');
                            loadingGlobal(false);
                        });
                    }
                    break;
                case 'settings':
                    if (!SBSettings.init) {
                        loadingGlobal();
                        SBF.ajax({
                            function: 'get-all-settings'
                        }, (response) => {
                            if (response) {
                                let translations = response['external-settings-translations'];
                                if (response['slack-agents']) {
                                    let code = '';
                                    for (var key in response['slack-agents'][0]) {
                                        code += `<div data-id="${key}"><select><option value="${response['slack-agents'][0][key]}"></option></select></div>`;
                                    }
                                    settings_area.find('#slack-agents .input').html(code);
                                }
                                SBSettings.translations.translations = Array.isArray(translations) && !translations.length ? {} : translations;
                                delete response['external-settings-translations'];
                                for (var key in response) {
                                    SBSettings.set(key, response[key]);
                                }
                            }
                            if (SBF.getURL('refresh_token')) {
                                admin.find('#google-refresh-token input').val(SBF.getURL('refresh_token'));
                                SBSettings.save();
                                infoBottom('Synchronization completed.');
                                admin.find('#google')[0].scrollIntoView();
                            }
                            settings_area.find('textarea').each(function () {
                                $(this).autoExpandTextarea();
                                $(this).manualExpandTextarea();
                            });
                            settings_area.find('[data-setting] .sb-language-switcher-cnt').each(function () {
                                $(this).sbLanguageSwitcher(SBSettings.translations.getLanguageCodes($(this).closest('[data-setting]').attr('id')), 'settings');
                            });
                            SBSettings.init = true;
                            loadingGlobal(false);
                            if (response && !SB_ADMIN_SETTINGS.cloud) {
                                SBSettings.visibility(0, response['push-notifications'] && response['push-notifications'][0]['push-notifications-provider'][0] == 'pusher');
                            }
                            SBSettings.visibility(1, response['messenger'] ? response['messenger'][0]['messenger-sync-mode'][0] != 'manual' : true);
                            SBSettings.visibility(2, response['open-ai'] ? response['open-ai'][0]['open-ai-mode'][0] != 'assistant' : true);
                            SBF.event('SBSettingsLoaded', response);
                        });
                    }
                    SBUsers.stopRealTime();
                    SBConversations.stopRealTime();
                    break;
                case 'reports':
                    if (reports_area.sbLoading()) {
                        $.getScript(SB_URL + '/vendor/moment.min.js', () => {
                            $.getScript(SB_URL + '/vendor/daterangepicker.min.js', () => {
                                $.getScript(SB_URL + '/vendor/chart.min.js', () => {
                                    SBReports.initDatePicker();
                                    SBReports.initReport('conversations');
                                    reports_area.sbLoading(false);
                                });
                            });
                        });
                    }
                    SBUsers.stopRealTime();
                    SBConversations.stopRealTime();
                    break;
                case 'articles':
                    let nav = articles_area.find('.sb-menu-wide li').eq(0);
                    if (articles_area.sbLoading()) {
                        nav.sbActive(true).next().sbActive(false);
                        SBF.ajax({
                            function: 'init-articles-admin'
                        }, (response) => {
                            SBArticles.categories.list = response[1];
                            SBArticles.translations.list = response[2];
                            SBArticles.page_url = response[3];
                            SBArticles.is_url_rewrite = response[4];
                            SBArticles.cloud_chat_id = response[5];
                            SBArticles.populate(response[0]);
                            SBArticles.populate(response[1], true);
                            SBArticles.categories.update();
                            articles_area.sbLoading(false);
                        });
                    } 
                    else {
                        nav.click();
                    }
                    SBUsers.stopRealTime();
                    SBConversations.stopRealTime();
                    break;
                case 'chatbot':
                    if (chatbot_files_table.sbLoading()) {
                        SBApps.openAI.init();
                    }
                    SBApps.openAI.troubleshoot();
            }

            let url_area = SBF.getURL('area');

            if (url_area != active_admin_area && (
                (active_admin_area == 'conversations' && !SBF.getURL('conversation')) 
                || (active_admin_area == 'tickets' && !SBF.getURL('tickets')) 
                || (active_admin_area == 'users' && !SBF.getURL('user')) 
                || (active_admin_area == 'settings' && !SBF.getURL('setting')) 
                || (active_admin_area == 'reports' && !SBF.getURL('report')) 
                || (active_admin_area == 'articles' && !SBF.getURL('article')) 
                || (active_admin_area == 'chatbot' && !SBF.getURL('chatbot'))
                )){
                pushState('?area=' + active_admin_area);
            }
        });

        $(header).on('click', '.sb-profile', function () {
            $(this).next().toggleClass('sb-active');
        });

        $(header).on('click', '[data-value="logout"],.logout', function () {
            SBAdmin.is_logout = true;
            SBF.ajax({ function: 'on-close' });
            SBUsers.stopRealTime();
            SBConversations.stopRealTime();
            setTimeout(() => { SBF.logout() }, 300);
        });

        $(header).on('click', '[data-value="edit-profile"],.edit-profile', function () {
            loadingGlobal();
            let user = new SBUser({ 'id': SB_ACTIVE_AGENT.id });
            user.update(() => {
                activeUser(user);
                conversations_area.find('.sb-board').addClass('sb-no-conversation');
                conversations_admin_list_ul.find('.sb-active').sbActive(false);
                SBProfile.showEdit(user);
            });
        });

        $(header).on('click', '[data-value="status"]', function () {
            let is_offline = !$(this).hasClass('sb-online');
            SBUsers.setActiveAgentStatus(is_offline);
            away_mode = is_offline;
        });

        $(header).find('.sb-account').setProfile(SB_ACTIVE_AGENT['full_name'], SB_ACTIVE_AGENT['profile_image']);

        /*
        * ----------------------------------------------------------
        * Conversations area
        * ----------------------------------------------------------
        */
        
        // Open the conversation clicked on the left menu
        $(conversations_admin_list_ul).on('click', 'li', function () {
            if (active_keydown == 17) {
                $(this).sbActive(!$(this).sbActive());
            } else {
                SBConversations.openConversation($(this).attr('data-conversation-id'), $(this).attr('data-user-id'), false);
                SBF.deactivateAll();
            }
        });

        // Open the user conversation clicked on the bottom right area or user profile box
        $(admin).on('click', '.sb-user-conversations li', function () {
            SBConversations.openConversation($(this).attr('data-conversation-id'), activeUser().id, $(this).attr('data-conversation-status'));
        });

        // Archive, delete or restore conversations
        $(conversations_area).on('click', '.sb-top ul a', function () {
            let status_code;
            let selected_conversations = conversations_admin_list_ul.find('.sb-active').map(function () {
                return { id: $(this).attr('data-conversation-id'), user_id: $(this).attr('data-user-id'), status_code: $(this).attr('data-conversation-status') };
            }).toArray();
            let selected_conversations_length = selected_conversations.length;
            let multi_selection = selected_conversations_length > 1;
            let message = multi_selection ? 'All the selected conversations will be ' : 'The conversation will be ';
            let value = $(this).attr('data-value');
            let on_success = (response, action) => {
                let success = response.includes('.txt');
                $(this).sbLoading(false);
                if (action == 'email') {
                    actioninfoBottom(success ? sb_('Transcript sent to user\'s email.') + ' <a href="' + response + '" target="_blank">' + sb_('View transcript') + '</a>' : 'Transcript sending error: ' + response, success ? '' : 'error');
                }
            }
            switch (value) {
                case 'inbox':
                    status_code = 1;
                    message += 'restored.';
                    break;
                case 'archive':
                    message += 'archived.';
                    status_code = 3;
                    break;
                case 'delete':
                    message += 'deleted.';
                    status_code = 4;
                    break;
                case 'empty-trash':
                    status_code = 5;
                    message = 'All conversations in the trash (including their messages) will be deleted permanently.'
                    break;
                case 'transcript':
                    let action = $(this).attr('data-action');
                    if (action == 'email' && (!activeUser() || !activeUser().get('email'))) {
                        action = '';
                    }
                    SBConversations.transcript(selected_conversations[0].id, selected_conversations[0].user_id, action, (response) => on_success(response, action));
                    loading(this);
                    break;
                case 'read':
                    status_code = 1;
                    message += 'marked as read.';
                    break;
                case 'unread':
                    status_code = 2;
                    message += 'marked as unread.';
                    break;
                case 'panel':
                    $([conversations_user_details, this]).toggleClass('sb-active');
                    break;
            }
            if (status_code) {
                infoPanel(message, 'alert', function () {
                    let active_conversations_filter = conversations_filters.eq(0).find('p').attr('data-value');
                    let last_conversation_id = selected_conversations[selected_conversations_length - 1].id;
                    for (var i = 0; i < selected_conversations_length; i++) {
                        let conversation = selected_conversations[i];
                        SBF.ajax({
                            function: 'update-conversation-status',
                            conversation_id: conversation.id,
                            status_code: status_code
                        }, () => {
                            let conversation_li = conversations_admin_list_ul.find(`[data-conversation-id="${conversation.id}"]`);
                            if ([0, 3, 4].includes(status_code)) {
                                for (var j = 0; j < conversations.length; j++) {
                                    if (conversations[j].id == conversation.id) {
                                        conversations[j].set('status_code', status_code);
                                        break;
                                    }
                                }
                            }
                            if (SB_ADMIN_SETTINGS.close_message && status_code == 3) {
                                SBF.ajax({ function: 'close-message', conversation_id: conversation.id, bot_id: SB_ADMIN_SETTINGS.bot_id });
                                if (SB_ADMIN_SETTINGS.close_message_transcript) {
                                    SBConversations.transcript(conversation.id, conversation.user_id, 'email', (response) => on_success(response));
                                }
                            }
                            if ([0, 1, 2].includes(status_code)) {
                                conversation_li.attr('data-conversation-status', status_code);
                                SBConversations.updateMenu();
                            }
                            if (SBChat.conversation && SBApps.is('slack') && [3, 4].includes(status_code)) {
                                SBF.ajax({ function: 'archive-slack-channels', conversation_user_id: SBChat.conversation.get('user_id') });
                            }
                            if ((active_conversations_filter == 0 && [3, 4].includes(status_code)) || (active_conversations_filter == 3 && [0, 1, 2, 4].includes(status_code)) || (active_conversations_filter == 4 && status_code != 4)) {
                                let previous = false;
                                SBConversations.updateMenu();
                                if (SBChat.conversation && SBChat.conversation.id == conversation.id) {
                                    previous = conversation_li.prev();
                                    SBChat.conversation = false;
                                }
                                conversation_li.remove();
                                if (conversation.id == last_conversation_id) {
                                    SBConversations.clickFirst(previous);
                                }
                            }
                            if (active_conversations_filter == 4 && status_code == 5) {
                                conversations_admin_list_ul.find('li').remove();
                                SBConversations.updateMenu();
                                SBConversations.clickFirst();
                            }
                        });
                        if (SBChat.conversation && SBChat.conversation.id == conversation.id) {
                            SBChat.conversation.set('status_code', status_code);
                            SBConversations.setReadIcon(status_code);
                        }
                    }
                });
            }
        });

        // Saved replies
        SBF.ajax({
            function: 'saved-replies'
        }, (response) => {
            let code = `<p class="sb-no-results">${sb_('No saved replies found. Add new saved replies via Settings > Admin.')}</p>`;
            if (Array.isArray(response)) {
                if (response.length && response[0]['reply-name']) {
                    code = '';
                    saved_replies_list = response;
                    for (var i = 0; i < response.length; i++) {
                        code += `<li><div>${response[i]['reply-name']}</div><div>${response[i]['reply-text'].replace(/\\n/g, '\n')}</div></li>`;
                    }
                }
            }
            saved_replies.find('.sb-replies-list > ul').html(code).sbLoading(false);
        });

        $(conversations_area).on('click', '.sb-btn-saved-replies', function () {
            saved_replies.sbTogglePopup(this);
            saved_replies.find('.sb-search-btn').sbActive(true).find('input').get(0).focus();
        });

        $(saved_replies).on('click', '.sb-replies-list li', function () {
            SBChat.insertText($(this).find('div:last-child').text().replace(/\\n/g, '\n'));
            SBF.deactivateAll();
            admin.removeClass('sb-popup-active');
        });

        $(saved_replies).on('input', '.sb-search-btn input', function () {
            saved_reply_search($(this).val().toLowerCase());
        });

        $(saved_replies).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { saved_reply_search('') });
        });

        $(admin).on('click', '.sb-btn-open-ai', function () {
            if (!SBChat.conversation || loading(this)) return;
            let is_editor = $(this).hasClass('sb-btn-open-ai-editor');
            let textarea = is_editor ? conversations_area.find('.sb-editor textarea') : dialogflow_intent_box.find('textarea');
            SBApps.openAI.rewrite(textarea.val(), (response) => {
                $(this).sbLoading(false);
                if (response[0]) {
                    textarea.val(is_editor ? '' : response[1]);
                    if (is_editor) {
                        SBChat.insertText(response[1]);
                    }
                }
            });
        });

        // Pagination for conversations
        $(conversations_admin_list).find('.sb-scroll-area').on('scroll', function () {
            if (!is_busy && !SBConversations.is_search && scrollPagination(this, true) && pagination_count) {
                let parent = conversations_area.find('.sb-admin-list');
                let filters = SBConversations.filters();
                is_busy = true;
                parent.append('<div class="sb-loading-global sb-loading"></div>');
                SBF.ajax({
                    function: 'get-conversations',
                    pagination: pagination,
                    status_code: filters[0],
                    department: filters[1],
                    source: filters[2],
                    tag: filters[3]
                }, (response) => {
                    setTimeout(() => { is_busy = false }, 500);
                    pagination_count = response.length;
                    if (pagination_count) {
                        let code = '';
                        for (var i = 0; i < pagination_count; i++) {
                            let conversation = new SBConversation([new SBMessage(response[i])], response[i]);
                            code += SBConversations.getListCode(conversation);
                            conversations.push(conversation);
                        }
                        pagination++;
                        conversations_admin_list_ul.append(code);
                    }
                    parent.find(' > .sb-loading').remove();
                    SBF.event('SBAdminConversationsLoaded', { conversations: response });
                });
            }
        });

        // Event: message deleted
        $(document).on('SBMessageDeleted', function () {
            let last_message = SBChat.conversation.getLastMessage();
            if (last_message != false) {
                conversations_admin_list_ul.find('li.sb-active p').html(last_message.message);
            } else {
                conversations_admin_list_ul.find('li.sb-active').remove();
                SBConversations.clickFirst();
                SBConversations.scrollTo();
            }
        });

        // Event: message sent
        $(document).on('SBMessageSent', function (e, response) {
            let conversation_id = response.conversation_id;
            let item = getListConversation(conversation_id);
            let message_part = sb_('Error. Message not sent to');
            let conversation = response.conversation;
            let user = response.user;
            if (response.conversation_status_code) {
                SBConversations.updateMenu();
            }
            if (SBApps.messenger.check(conversation)) {
                SBApps.messenger.send(user.getExtra('facebook-id').value, conversation.get('extra'), response.message, response.attachments, response.message_id, response.message_id, (response) => {
                    for (var i = 0; i < response.length; i++) {
                        if (response[i] && response[i].error) {
                            infoPanel(message_part + ' Messenger: ' + response[i].error.message, 'info', false, 'error-fb');
                        }
                    }
                });
            }
            if (SBApps.whatsapp.check(conversation)) {
                SBApps.whatsapp.send(SBApps.whatsapp.activeUserPhone(user), response.message, response.attachments, conversation.get('extra'), (response) => {
                    if (response.ErrorCode || (response.meta && !response.meta.success)) {
                        infoPanel(message_part + ' WhatsApp: ' + ('ErrorCode' in response ? response.errorMessage : response.meta.developer_message), 'info', false, 'error-wa');
                    }
                });
            }
            if (SBApps.telegram.check(conversation)) {
                SBApps.telegram.send(conversation.get('extra'), response.message, response.attachments, conversation_id, (response) => {
                    if (!response || !response.ok) {
                        infoPanel(message_part + ' Telegram: ' + JSON.stringify(response), 'info', false, 'error-tg');
                    }
                });
            }
            if (SBApps.viber.check(conversation)) {
                SBApps.viber.send(user.getExtra('viber-id').value, response.message, response.attachments, (response) => {
                    if (!response || response.status_message != 'ok') {
                        infoPanel(message_part + ' Viber: ' + JSON.stringify(response), 'info', false, 'error-vb');
                    }
                });
            }
            if (SBApps.zalo.check(conversation)) {
                SBApps.zalo.send(user.getExtra('zalo-id').value, response.message, response.attachments, (response) => {
                    if (response && response.error.error) {
                        infoPanel(message_part + ' Zalo: ' + response.error.message ? response.error.message : response.message, 'info', false, 'error-za');
                    }
                });
            }
            if (SBApps.twitter.check(conversation)) {
                SBApps.twitter.send(user.getExtra('twitter-id').value, response.message, response.attachments, (response_2) => {
                    if (response_2 && !response_2.event) {
                        infoPanel(JSON.stringify(response_2), 'info', false, 'error-tw');
                    } else if (response.attachments.length > 1) {
                        infoBottom('Only the first attachment was sent to Twitter.');
                    }
                });
            }
            if (SBApps.line.check(conversation)) {
                SBApps.line.send(user.getExtra('line-id').value, response.message, response.attachments, conversation_id, (response) => {
                    if (response.error) {
                        infoPanel(message_part + ' LINE: ' + JSON.stringify(response), 'info', false, 'error-ln');
                    }
                });
            }
            if (SBApps.wechat.check(conversation)) {
                SBApps.wechat.send(user.getExtra('wechat-id').value, response.message, response.attachments, (response) => {
                    if (!response || response.errmsg != 'ok') {
                        infoPanel(message_part + ' WeChat: ' + JSON.stringify(response), 'info', false, 'error-wc');
                    }
                });
            }
            if (SB_ADMIN_SETTINGS.smart_reply) {
                suggestions_area.html('');
            }
            if (SB_ADMIN_SETTINGS.assign_conversation_to_agent && SBF.null(conversation.get('agent_id'))) {
                SBConversations.assignAgent(conversation_id, SB_ACTIVE_AGENT.id, () => {
                    if (SBChat.conversation.id == conversation_id) {
                        SBChat.conversation.set('agent_id', SB_ACTIVE_AGENT.id);
                        $(conversations_area).find('#conversation-agent > p').attr('data-value', SB_ACTIVE_AGENT.id).html(SB_ACTIVE_AGENT.full_name);
                    }
                });
            }
        });

        // Event: new message of active chat conversation received
        $(document).on('SBNewMessagesReceived', function (e, response) {
            let messages = response.messages;
            for (var i = 0; i < messages.length; i++) {
                let message = messages[i];
                let payload = message.payload();
                let agent = SBF.isAgent(message.get('user_type'));
                setTimeout(function () {
                    conversation_area.find('.sb-top .sb-status-typing').remove();
                }, 300);
                if (SBAdmin.must_translate) {
                    let message_html = conversation_area.find(`[data-id="${message.id}"]`);
                    let message_original = payload['original-message'] ? payload['original-message'] : false;
                    if (message_original) {
                        message_html.replaceWith(message.getCode());
                        conversation_area.find(`[data-id="${message.id}"] .sb-menu`).prepend(`<li data-value="translation">${sb_('View translation')}</li>`);
                        if (SB_ADMIN_SETTINGS.smart_reply) {
                            SBApps.dialogflow.smartReply(SBF.escape(message_original));
                        }
                    } else if (message.message) {
                        SBApps.dialogflow.translate([message.message], SB_ADMIN_SETTINGS.active_agent_language, (response_2) => {
                            if (response_2) {
                                message.payload('translation', response_2[0]);
                                message.payload('translation-language', SB_ADMIN_SETTINGS.active_agent_language);
                                message_html.replaceWith(message.getCode());
                                conversation_area.find(`[data-id="${message.id}"] .sb-menu`).prepend(`<li data-value="original">${sb_('View original message')}</li>`);
                            }
                            if (SB_ADMIN_SETTINGS.smart_reply) {
                                SBApps.dialogflow.smartReply(response_2[0]);
                            }
                            conversations_admin_list_ul.find(`[data-conversation-id="${response.conversation_id}"] p`).html(response_2[0]);
                        }, [message.id], SBChat.conversation.id);
                    }
                } else if (SB_ADMIN_SETTINGS.smart_reply) {
                    SBApps.dialogflow.smartReply(message.message);
                }
                if (payload) {
                    if (payload.department) {
                        SBConversations.setActiveDepartment(payload.department);
                    }
                    if (payload.agent) {
                        SBConversations.setActiveAgent(payload.agent);
                    }
                }
                if ('ErrorCode' in payload || (payload.errors && payload.errors.length)) {
                    infoPanel('Error. Message not sent to WhatsApp. Error message: ' + (payload.ErrorCode ? payload.ErrorCode : payload.errors[0].title));
                }
                if ('whatsapp-templates' in payload) {
                    infoBottom(`Message sent as text message.${'whatsapp-template-fallback' in payload ? ' The user has been notified via WhatsApp Template notification.' : ''}`);
                }
                if ('whatsapp-template-fallback' in payload && !('whatsapp-templates' in payload)) {
                    infoBottom('The user has been notified via WhatsApp Template notification.');
                }
                if (!agent && SBChat.conversation.id == response.conversation_id && !SBChat.user_online) {
                    SBUsers.setActiveUserStatus();
                }
            }
            SBConversations.update();
        });

        // Event: new conversation created 
        $(document).on('SBNewConversationCreated', function () {
            SBConversations.update();
        });

        // Event: email notification sent
        $(document).on('SBEmailSent', function () {
            infoBottom(`The user has been notified by email.`);
        });

        // Event: SMS notification sent
        $(document).on('SBSMSSent', function () {
            infoBottom('The user has been notified by text message.');
        });

        // Event: Message notifications
        $(document).on('SBNotificationsSent', function (e, response) {
            infoBottom(`The user ${response.includes('cron') ? 'will be' : 'has been'} notified by email${response.includes('sms') ? ' and text message' : ''}.`);
        });

        // Event: user typing status change
        $(document).on('SBTyping', function (e, response) {
            SBConversations.typing(response);
        });

        // Conversations search
        $(conversations_admin_list).on('input', '.sb-search-btn input', function () {
            SBConversations.search(this);
        });

        $(conversations_area).on('click', '.sb-admin-list .sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBConversations.search($(this).next()) });
        });

        // Conversations filter
        $(conversations_filters).on('click', 'li', function (e) {
            let parent = conversations_admin_list_ul.parent();
            if (loading(parent)) {
                e.preventDefault()
                return false;
            }
            setTimeout(() => {
                let filters = SBConversations.filters();
                pagination = 1;
                pagination_count = 1;
                SBF.ajax({
                    function: 'get-conversations',
                    status_code: filters[0],
                    department: filters[1],
                    source: filters[2],
                    tag: filters[3]
                }, (response) => {
                    SBConversations.populateList(response);
                    conversation_area.attr('data-conversation-status', filters[0]);
                    if (response.length) {
                        if (!responsive) {
                            if (SBChat.conversation) {
                                let conversation = getListConversation(SBChat.conversation.id);
                                if (conversation.length) {
                                    conversation.sbActive(true);
                                } else if (filters[0] == SBChat.conversation.status_code) {
                                    conversations_admin_list_ul.prepend(SBConversations.getListCode(SBChat.conversation));
                                } else {
                                    SBConversations.clickFirst();
                                }
                            } else {
                                SBConversations.clickFirst();
                            }
                            SBConversations.scrollTo();
                        }
                    } else {
                        conversations_area.find('.sb-board').addClass('sb-no-conversation');
                        SBChat.conversation = false;
                    }
                    $(this).closest('.sb-filter-btn').attr('data-badge', conversations_filters.slice(1).toArray().reduce((acc, filter) => acc + !!$(filter).find('li.sb-active').data('value'), 0));
                    parent.sbLoading(false);
                });
            }, 100);
        });

        // Display the user details box
        $(conversations_area).on('click', '.sb-user-details .sb-profile,.sb-top > a', function () {
            let user_id = conversations_admin_list_ul.find('.sb-active').attr('data-user-id');
            if (activeUser().id != user_id) {
                activeUser(users[user_id]);
            }
            SBProfile.show(activeUser().id);
        });

        // Right profile list methods
        $(admin).on('click', '.sb-profile-list li', function () {
            let label = $(this).find('label');
            let label_value = label.html();
            switch ($(this).attr('data-id')) {
                case 'location':
                    let location = label_value.replace(', ', '+');
                    infoPanel('<iframe src="https://maps.google.com/maps?q=' + location + '&output=embed"></iframe>', 'map');
                    break;
                case 'timezone':
                    SBF.getLocationTimeString(activeUser().extra, (response) => {
                        loadingGlobal(false);
                        infoPanel(response);
                    });
                    break;
                case 'current_url':
                    window.open('//' + (SBF.null(label.attr('data-value')) ? label_value : label.attr('data-value')));
                    break;
                case 'conversation-source':
                    let source = label_value.toLowerCase();
                    if (source == 'whatsapp' && activeUser().getExtra('phone')) {
                        window.open('https://wa.me/' + SBApps.whatsapp.activeUserPhone());
                    } else if (source == 'facebook') {
                        window.open('https://www.facebook.com/messages/t/' + SBChat.conversation.get('extra'));
                    } else if (source == 'instagram') {
                        window.open('https://www.instagram.com/direct/inbox/');
                    } else if (source == 'twitter') {
                        window.open('https://twitter.com/messages/');
                    }
                    break;
                case 'wp-id':
                    window.open(window.location.href.substr(0, window.location.href.lastIndexOf('/')) + '/user-edit.php?user_id=' + activeUser().getExtra('wp-id').value);
                    break;
                case 'envato-purchase-code':
                    loadingGlobal();
                    SBF.ajax({
                        function: 'envato',
                        purchase_code: label_value
                    }, (response) => {
                        let code = '';
                        if (response && response.item) {
                            response.name = response.item.name;
                            for (var key in response) {
                                if (isString(response[key]) || !isNaN(response[key])) {
                                    code += `<b>${SBF.slugToString(key)}</b> ${response[key]} <br>`;
                                }
                            }
                            loadingGlobal(false);
                            infoPanel(code, 'info', false, 'sb-envato-box');
                        } else {
                            infoBottom(SBF.slugToString(response));
                        }
                    });
                    break;
                case 'email':
                case 'cc':
                    if (SBChat.conversation && SBChat.conversation.get('source') == 'em') {
                        let cc = SBChat.conversation.get('extra').split(',');
                        let code = `<div data-type="repeater" class="sb-setting sb-type-repeater"><div class="input"><div class="sb-repeater">`;
                        for (var i = 0; i < cc.length; i++) {
                            code += `<div class="repeater-item"><div><input data-id="cc" type="text" value="${cc[i]}"></div><i class="sb-icon-close"></i></div>`;
                        }
                        code += `</div><div class="sb-btn sb-btn-white sb-repeater-add sb-icon"><i class="sb-icon-plus"></i>${sb_('Add new item')}</div></div></div>`;
                        SBAdmin.genericPanel('cc', 'Manage CC', code, ['Save changes'], '', true);
                    }
                    break;
            }
        });

        $(conversations_user_details).on('click', '.sb-user-details-close', function () {
            conversations_area.find('.sb-menu-mobile [data-value="panel"]').click().sbActive(true);
        });

        // Dialogflow
        $(conversations_area).on('click', '.sb-menu [data-value="bot"]', function () {
            SBApps.dialogflow.showCreateIntentBox($(this).closest('[data-id]').attr('data-id'));
        });

        $(dialogflow_intent_box).on('click', '.sb-intent-add [data-value="add"]', function () {
            dialogflow_intent_box.find('> div > .sb-type-text').last().after('<div class="sb-setting sb-type-text"><input type="text"></div>');
        });

        $(dialogflow_intent_box).on('click', '.sb-intent-add [data-value="previous"],.sb-intent-add [data-value="next"]', function () {
            let input = dialogflow_intent_box.find('.sb-first input');
            let message = input.val();
            let next = $(this).attr('data-value') == 'next';
            let messages = SBChat.conversation.getUserMessages();
            let messages_length = messages.length;
            for (var i = 0; i < messages_length; i++) {
                if (messages[i].message == message && ((next && i < (messages_length - 1)) || (!next && i > 0))) {
                    i = i + (next ? 1 : -1);
                    input.val(messages[i].message);
                    dialogflow_intent_box.attr('data-message-id', messages[i].id);
                    SBApps.openAI.generateQuestions(messages[i].message);
                    break;
                }
            }
        });

        $(dialogflow_intent_box).on('click', '.sb-send', function () {
            SBApps.dialogflow.submitIntent(this);
        });

        $(dialogflow_intent_box).on('input', '.sb-search-btn input', function () {
            SBApps.dialogflow.searchIntents($(this).val());
        });

        $(dialogflow_intent_box).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBApps.dialogflow.searchIntents($(this).val()) });
        });

        $(dialogflow_intent_box).on('click', '#sb-intent-preview', function () {
            SBApps.dialogflow.previewIntentDialogflow(dialogflow_intent_box.find('#sb-intents-select').val());
        });

        $(dialogflow_intent_box).on('click', '#sb-qea-preview', function () {
            SBApps.dialogflow.previewIntent(dialogflow_intent_box.find('#sb-qea-select').val());
        });

        $(dialogflow_intent_box).on('change', '#sb-intents-select', function () {
            let intent = $(this).val();
            dialogflow_intent_box.find('.sb-bot-response').css('opacity', intent ? .5 : 1).find('textarea').val(intent ? SBApps.dialogflow.getIntent(intent).messages[0].text.text[0] : SBApps.dialogflow.original_response);
            dialogflow_intent_box.find('#sb-train-chatbots').val(intent ? 'dialogflow' : '');
        });

        $(dialogflow_intent_box).on('change', '#sb-qea-select', function () {
            let qea = $(this).val();
            dialogflow_intent_box.find('.sb-bot-response').setClass('sb-disabled', qea).find('textarea').val(qea ? SBApps.dialogflow.qea[qea][1] : SBApps.dialogflow.original_response);
            dialogflow_intent_box.find('#sb-train-chatbots').val(qea ? 'dialogflow' : '');
        });

        $(dialogflow_intent_box).on('change', 'textarea', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                SBApps.dialogflow.original_response = dialogflow_intent_box.find('textarea').val();
            }, 500);
        });

        $(dialogflow_intent_box).on('change', '#sb-train-chatbots', function () {
            dialogflow_intent_box.find('.sb-type-text:not(.sb-first)').setClass('sb-hide', $(this).val() == 'open-ai');
        });

        // Departments
        $(select_departments).on('click', 'li', function (e) {
            let select = $(this).parent().parent();
            if ($(this).data('id') == select.find(' > p').attr('data-id')) {
                setTimeout(() => { $(this).sbActive(false); }, 100);
                return true;
            }
            if (!SBChat.conversation) {
                $(this).parent().sbActive(false);
                e.preventDefault();
                return false;
            }
            if (!select.sbLoading()) infoPanel(`${sb_('All agents assigned to the new department will be notified. The new department will be')} ${$(this).html()}.`, 'alert', () => {
                let value = $(this).data('id');
                select.sbLoading(true);
                SBConversations.assignDepartment(SBChat.conversation.id, value, () => {
                    SBConversations.setActiveDepartment(value);
                    select.sbLoading(false);
                });
            });
            e.preventDefault();
            return false;
        });

        // Agent assignment
        $(conversations_area).on('click', '#conversation-agent li', function (e) {
            let select = $(this).parent().parent();
            let agent_id = $(this).data('id');
            if (agent_id == select.find(' > p').attr('data-value')) return true;
            if (!SBChat.conversation) {
                $(this).parent().sbActive(false);
                e.preventDefault();
                return false;
            }
            if (!select.sbLoading()) {
                infoPanel(`${sb_('The new agent will be')} ${$(this).html()}.`, 'alert', () => {
                    select.sbLoading(true);
                    SBConversations.assignAgent(SBChat.conversation.id, agent_id, () => {
                        SBConversations.setActiveAgent(agent_id);
                        select.sbLoading(false);
                    });
                });
            }
            e.preventDefault();
            return false;
        });

        // Notes and Tags 
        notes_panel.on('click', '> i,.sb-edit-note', function (e) {
            let note = $(this).hasClass('sb-edit-note') ? $(this).closest('[data-id]') : false;
            SBAdmin.genericPanel('notes', note ? 'Edit note' : 'Add new note', `<div class="sb-setting sb-type-textarea"><textarea${note ? ' data-id="' + note.attr('data-id') + '"' : ''} placeholder="${sb_('Write here your note...')}">${note ? note.find('.sb-note-text').html().replace(/<a\s+href="([^"]*)".*?>(.*?)<\/a>/gi, '$1').replaceAll('<br>', '\n') : ''}</textarea></div>`, [[note ? 'Update note' : 'Add note', note ? 'check' : 'plus']]);
            if (SBApps.is('dialogflow') && SB_ADMIN_SETTINGS.note_data_scrape) {
                let options = '';
                for (var key in SB_ADMIN_SETTINGS.note_data_scrape) {
                    options += `<option value="${key}">${SB_ADMIN_SETTINGS.note_data_scrape[key]}</option>`;
                }
                admin.find('#sb-add-note').parent().prepend(`<div id="note-ai-scraping" class="sb-setting sb-type-select"><select><option value="">${sb_('Data scraping')}</option>${options}</select></div>`);
            }
            e.preventDefault();
            return false;
        });

        $(admin).on('change', '#note-ai-scraping select', function () {
            let value = $(this).val();
            if (!value || loading($(this).parent())) {
                return;
            }
            SBF.ajax({
                function: 'data-scraping',
                conversation_id: SBChat.conversation.id,
                prompt_id: value
            }, (response) => {
                if (response && response.error) {
                    console.error(response);
                    return infoBottom(response.error.message, 'error');
                }
                $(this).parent().sbLoading(false);
                let textarea = admin.find('.sb-notes-box textarea');
                textarea.val((textarea.val() + '\n' + response).trim());
            });
        });

        notes_panel.on('click', '.sb-delete-note', function () {
            let item = $(this).parents().eq(1);
            SBConversations.notes.delete(SBChat.conversation.id, item.attr('data-id'), (response) => {
                if (response === true) {
                    item.remove();
                } else {
                    SBF.error(response);
                }
            });
        });

        $(admin).on('click', '#sb-add-note, #sb-update-note', function () {
            let textarea = $(this).parent().parents().eq(1).find('textarea');
            let message = textarea.val();
            let note_id = textarea.attr('data-id');
            if (message.length == 0) {
                SBForm.showErrorMessage(admin.find('.sb-notes-box'), 'Please write something...');
            } else {
                if (loading(this)) return;
                message = SBF.escape(message);
                SBConversations.notes.add(SBChat.conversation.id, SB_ACTIVE_AGENT.id, SB_ACTIVE_AGENT.full_name, message, (response) => {
                    if (Number.isInteger(response) || response === true) {
                        $(this).sbLoading(false);
                        admin.sbHideLightbox();
                        if (note_id) {
                            notes_panel.find(`[data-id="${note_id}"]`).remove();
                        }
                        SBConversations.notes.update([{ id: note_id ? note_id : response, conversation_id: SBChat.conversation.id, user_id: SB_ACTIVE_AGENT.id, name: SB_ACTIVE_AGENT['full_name'], message: message }], true);
                        textarea.val('');
                        infoBottom(note_id ? 'Note successfully updated.' : 'New note successfully added.');
                    } else {
                        SBForm.showErrorMessage(response);
                    }
                }, note_id);
            }
        });

        tags_panel.on('click', '> i', function (e) {
            let code = SBConversations.tags.getAll(SBChat.conversation.details.tags);;
            let tags = SBChat.conversation.details.tags;
            SBAdmin.genericPanel('tags', 'Manage tags', code ? '<div class="sb-tags-cnt">' + code + '</div>' : '<p>' + sb_('Add tags from Settings > Admin > Tags.') + '</p>', ['Save tags']);
            e.preventDefault();
            return false;
        });

        $(admin).on('click', '.sb-tags-cnt > span', function () {
            $(this).toggleClass('sb-active');
        });

        $(admin).on('click', '#sb-add-tag', function () {
            $('<input type="text">').insertBefore(this);
        });

        $(admin).on('click', '#sb-save-tags', function () {
            if (loading(this)) return;
            let tags = admin.find('.sb-tags-box').find('span.sb-active').map(function () {
                return $(this).attr('data-value');
            }).toArray();
            let conversation_id = SBChat.conversation.id;
            SBF.ajax({
                function: 'update-tags',
                conversation_id: conversation_id,
                tags: tags
            }, (response) => {
                $(this).sbLoading(false);
                if (response === true) {
                    SBConversations.tags.update(tags);
                    if (SBChat.conversation && conversation_id == SBChat.conversation.id) {
                        let tag_filter = SBConversations.filters()[3];
                        let conversation_li = getListConversation(conversation_id);
                        SBChat.conversation.set('tags', tags);
                        if (tag_filter && !tags.includes(tag_filter)) {
                            conversation_li.remove();
                            SBConversations.clickFirst();
                        } else if (SB_ADMIN_SETTINGS.tags_show) {
                            let tags_area = conversation_li.find('.sb-tags-area');
                            let code = SBConversations.tags.codeLeft(tags);
                            if (tags_area.length) {
                                tags_area.replaceWith(code);
                            } else {
                                $(code).insertAfter(conversation_li.find('.sb-name'));
                            }
                        }
                    }
                }
                admin.sbHideLightbox();
                infoBottom(response === true ? 'Tags have been successfully updated.' : response);
            });
        });

        // Suggestions
        $(suggestions_area).on('click', 'span', function () {
            SBChat.insertText($(this).text());
            suggestions_area.html('');
        });

        $(suggestions_area).on('mouseover', 'span', function () {
            timeout = setTimeout(() => { $(this).addClass('sb-suggestion-full'); }, 2500);
        });

        $(suggestions_area).on('mouseout', 'span', function () {
            clearTimeout(timeout);
            suggestions_area.find('span').removeClass('sb-suggestion-full');
        });

        // Message menu
        $(conversations_area).on('click', '.sb-list .sb-menu > li', function () {
            let message = $(this).closest('[data-id]');
            let message_id = message.attr('data-id');
            let message_user_type = SBChat.conversation.getMessage(message_id).get('user_type');
            let value = $(this).attr('data-value');
            switch (value) {
                case 'delete':
                    if (SBChat.user_online) {
                        SBF.ajax({
                            function: 'update-message',
                            message_id: message_id,
                            message: '',
                            attachments: [],
                            payload: { 'event': 'delete-message' }
                        }, () => {
                            SBChat.conversation.deleteMessage(message_id);
                            message.remove();
                        });
                    } else {
                        SBChat.deleteMessage(message_id);
                    }
                    break;
                case 'translation':
                case 'original':
                    let is_translation = value == 'translation';
                    let agent = SBF.isAgent(message_user_type);
                    let message_ = SBChat.conversation.getMessage(message_id);
                    let keys = ['translation', 'translation-language', 'original-message', 'original-message-language'];
                    let original_payload = keys.map(key => message_.payload(key)).concat(message_.details.message);
                    message_.set('message', is_translation ? message_.payload('translation') || message_.get('message') : message_.payload('original-message') || message_.get('message'));
                    keys.forEach(key => delete message_.details.payload[key]);
                    message.replaceWith(message_.getCode().replace('sb-menu">', `sb-menu"><li data-value="${is_translation ? 'original' : 'translation'}">${sb_(is_translation ? 'View original message' : 'View translation')}</li>`));
                    keys.forEach((key, i) => message_.payload(key, original_payload[i]));
                    message_.details.message = original_payload[4];
                    break;
            }
        });

        // Conversations filter
        $(conversations_area).on('click', '.sb-filter-btn i', function () {
            $(this).parent().toggleClass('sb-active');
        });

        $(conversations_area).on('click', '.sb-filter-star', function () {
            $(this).parent().find('li').sbActive(false);
            $(this).parent().find(`li[data-value="${$(this).sbActive() ? '' : $(this).attr('data-value')}"]`).last().sbActive(true).click();
            $(this).toggleClass('sb-active');
        });

        // Attachments filter
        attachments_panel.on('click', '#sb-attachments-filter li', function () {
            let links = attachments_panel.find('a:not(.sb-collapse-btn)');
            let file_type = $(this).attr('data-value');
            links.each(function () {
                $(this).setClass('sb-hide', file_type && SBF.getFileType($(this).attr('href')) != file_type);
            });
            collapse(attachments_panel, 160);
        });

        // CC
        $(admin).on('click', '.sb-cc-box #sb-save-changes', function () {
            let cc = admin.find('.sb-cc-box .repeater-item input').map(function () {
                return $(this).val();
            }).get().join(',');
            loading(this);
            SBF.ajax({
                function: 'update-conversation-extra',
                conversation_id: SBChat.conversation.id,
                extra: cc ? cc : 'NULL'
            }, () => {
                SBChat.conversation.set('extra', cc);
                SBConversations.cc(cc.split(','));
                $(this).sbLoading(false);
                admin.sbHideLightbox();
            });
        });

        /*
        * ----------------------------------------------------------
        * Users area
        * ----------------------------------------------------------
        */

        // Open user box by URL
        if (SBF.getURL('user')) {
            header.find('.sb-admin-nav #sb-users').click();
            setTimeout(() => { SBProfile.show(SBF.getURL('user')) }, 500);
        }

        // Checkbox selector
        $(users_table).on('click', 'th :checkbox', function () {
            users_table.find('td :checkbox').prop('checked', $(this).prop('checked'));
        });

        $(users_table).on('click', ':checkbox', function () {
            let button = users_area.find('[data-value="delete"]');
            if (users_table.find('td input:checked').length) {
                button.removeAttr('style');
            } else {
                button.hide();
            }
        });

        // Table menu filter
        $(users_table_menu).on('click', 'li', function () {
            SBUsers.filter($(this).data('type'));
        });

        // Filters
        $(users_filters).on('click', 'li', function () {
            let button = users_filters.closest('.sb-filter-btn');
            setTimeout(() => {
                SBUsers.get((response) => {
                    SBUsers.populate(response);
                });
                button.attr('data-badge', users_filters.toArray().reduce((acc, filter) => acc + !!$(filter).find('li.sb-active').data('value'), 0));
            }, 100);
            button.sbActive(false);
        });

        // Search users
        $(users_area).on('input', '.sb-search-btn input', function () {
            SBUsers.search(this);
        });

        $(users_area).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBUsers.search($(this).next()) });
        });

        // Sorting
        $(users_table).on('click', 'th:not(:first-child)', function () {
            let direction = $(this).hasClass('sb-order-asc') ? 'DESC' : 'ASC';
            $(this).toggleClass('sb-order-asc');
            $(this).siblings().sbActive(false);
            $(this).sbActive(true);
            SBUsers.sort($(this).data('field'), direction);
        });

        // Pagination for users
        $(users_table).parent().on('scroll', function () {
            if (!is_busy && !SBUsers.search_query && scrollPagination(this, true) && users_pagination_count) {
                is_busy = true;
                users_area.append('<div class="sb-loading-global sb-loading sb-loading-pagination"></div>');
                SBUsers.get((response) => {
                    setTimeout(() => { is_busy = false }, 500);
                    users_pagination_count = response.length;
                    if (users_pagination_count) {
                        let code = '';
                        for (var i = 0; i < users_pagination_count; i++) {
                            let user = new SBUser(response[i], response[i].extra);
                            code += SBUsers.getRow(user);
                            users[user.id] = user;
                        }
                        users_pagination++;
                        users_table.find('tbody').append(code);
                    }
                    users_area.find(' > .sb-loading-pagination').remove();
                }, false, true);
            }
        });

        // Delete user button
        $(profile_edit_box).on('click', '.sb-delete', function () {
            if (SB_ACTIVE_AGENT.id == activeUser().id) {
                return infoBottom('You cannot delete yourself.', 'error');
            }
            infoPanel('This user will be deleted permanently including all linked data, conversations, and messages.', 'alert', function () {
                SBUsers.delete(activeUser().id);
            });
        });

        // Display user box
        $(users_table).on('click', 'td:not(:first-child)', function () {
            SBProfile.show($(this).parent().attr('data-user-id'));
        });

        // Display edit box
        $(profile_box).on('click', '.sb-top-bar .sb-edit', function () {
            SBProfile.showEdit(activeUser());
        });

        // Display new user box
        $(users_area).on('click', '.sb-new-user', function () {
            profile_edit_box.addClass('sb-user-new');
            profile_edit_box.find('.sb-top-bar .sb-profile span').html(sb_('Add new user'));
            profile_edit_box.find('.sb-top-bar .sb-save').html(`<i class="sb-icon-check"></i>${sb_('Add user')}`);
            profile_edit_box.find('input,select,textara').removeClass('sb-error');
            profile_edit_box.removeClass('sb-cloud-admin');
            if (SB_ACTIVE_AGENT.user_type == 'admin') {
                profile_edit_box.find('#user_type').find('select').html(`<option value="user">${sb_('User')}</option><option value="agent">${sb_('Agent')}</option><option value="admin">${sb_('Admin')}</option>`);
            }
            SBProfile.clear(profile_edit_box);
            SBProfile.boxClasses(profile_edit_box);
            SBProfile.updateRequiredFields('user');
            profile_edit_box.sbShowLightbox();
        });

        // Add or update user
        $(profile_edit_box).on('click', '.sb-save', function () {
            if (loading(this)) return;
            let new_user = (profile_edit_box.hasClass('sb-user-new') ? true : false);
            let user_id = profile_edit_box.attr('data-user-id');

            // Get settings
            let settings = SBProfile.getAll(profile_edit_box.find('.sb-details'));
            let settings_extra = SBProfile.getAll(profile_edit_box.find('.sb-additional-details'));
            let output = {};
            $.map(settings, function (value, key) {
                settings[key] = value[0];
            });

            // Errors check
            if (SBProfile.errors(profile_edit_box)) {
                SBProfile.showErrorMessage(profile_edit_box, SBF.isAgent(profile_edit_box.find('#user_type :selected').val()) ? 'First name, last name, password and a valid email are required. Minimum password length is 8 characters.' : (profile_edit_box.find('#password').val().length < 8 ? 'Minimum password length is 8 characters.' : 'First name is required.'));
                $(this).sbLoading(false);
                return;
            }
            if (SB_ACTIVE_AGENT.id == activeUser().id && settings.user_type[0] == 'agent' && SB_ACTIVE_AGENT.user_type == 'admin') {
                SBProfile.showErrorMessage(profile_edit_box, 'You cannot change your status from admin to agent.');
                $(this).sbLoading(false);
                return;
            }
            if (!settings.user_type) {
                settings.user_type = 'user';
            }

            // Save the settings
            SBF.ajax({
                function: (new_user ? 'add-user' : 'update-user'),
                user_id: user_id,
                settings: settings,
                settings_extra: settings_extra
            }, (response) => {
                if (SBF.errorValidation(response, 'duplicate-email') || SBF.errorValidation(response, 'duplicate-phone')) {
                    SBProfile.showErrorMessage(profile_edit_box, `This ${SBF.errorValidation(response, 'duplicate-email') ? 'email' : 'phone number'} is already in use.`);
                    $(this).sbLoading(false);
                    return;
                }
                if (new_user) {
                    user_id = response;
                    activeUser(new SBUser({ 'id': user_id }));
                }
                activeUser().update(() => {
                    users[user_id] = activeUser();
                    if (new_user) {
                        SBProfile.clear(profile_edit_box);
                        SBUsers.update();
                    } else {
                        SBUsers.updateRow(activeUser());
                        if (conversations_area.sbActive()) {
                            SBConversations.updateUserDetails();
                        }
                        if (user_id == SB_ACTIVE_AGENT.id) {
                            SBF.loginCookie(response[1]);
                            SB_ACTIVE_AGENT.full_name = activeUser().name;
                            SB_ACTIVE_AGENT.profile_image = activeUser().image;
                            header.find('.sb-account').setProfile();
                        }
                    }
                    if (new_user) {
                        profile_edit_box.find('.sb-profile').setProfile(sb_('Add new user'));
                    }
                    $(this).sbLoading(false);
                    if (!new_user) {
                        admin.sbHideLightbox();
                    }
                    infoBottom(new_user ? 'New user added' : 'User updated');
                });
                SBF.event('SBUserUpdated', { new_user: new_user, user_id: user_id });
            });
        });

        // Set and unset required visitor fields
        $(profile_edit_box).on('change', '#user_type', function () {
            let value = $(this).find("option:selected").val();
            SBProfile.boxClasses(profile_edit_box, value);
            SBProfile.updateRequiredFields(value);
        });

        // Open a user conversation
        $(profile_box).on('click', '.sb-user-conversations li', function () {
            SBConversations.open($(this).attr('data-conversation-id'), $(this).find('[data-user-id]').attr('data-user-id'));
        });

        // Start a new user conversation
        $(profile_box).on('click', '.sb-start-conversation', function () {
            SBConversations.open(-1, activeUser().id);
            SBConversations.openConversation(-1, activeUser().id);
            if (responsive) {
                SBConversations.mobileOpenConversation();
            }
        });

        // Show direct message box from user profile
        $(profile_box).on('click', '.sb-top-bar [data-value]', function () {
            SBConversations.showDirectMessageBox($(this).attr('data-value'), [activeUser().id]);
        });

        // Top icons menu
        $(users_area).on('click', '.sb-top-bar [data-value]', function () {
            let value = $(this).data('value');
            let user_ids = SBUsers.getSelected();
            switch (value) {
                case 'whatsapp':
                    whatsapp_direct_message_box(user_ids);
                    break;
                case 'message':
                case 'custom_email':
                case 'sms':
                    SBConversations.showDirectMessageBox(value, user_ids);
                    break;
                case 'csv':
                    SBUsers.csv();
                    break;
                case 'delete':
                    if (user_ids.includes(SB_ACTIVE_AGENT.id)) {
                        return infoBottom('You cannot delete yourself.', 'error');
                    }
                    infoPanel('All selected users will be deleted permanently including all linked data, conversations, and messages.', 'alert', () => {
                        SBUsers.delete(user_ids);
                        $(this).hide();
                        users_table.find('th:first-child input').prop('checked', false);
                    });
                    break;
            }
        });

        // Direct message
        $(admin).on('click', '.sb-send-direct-message', function () {
            let type = $(this).attr('data-type') ? $(this).attr('data-type') : direct_message_box.attr('data-type');
            let whatsapp = type == 'whatsapp';
            let box = whatsapp ? admin.find('#sb-whatsapp-send-template-box') : direct_message_box;
            let subject = box.find('.sb-direct-message-subject input').val();
            let message = whatsapp ? '' : box.find('textarea').val();
            let user_ids = box.find('.sb-direct-message-users').val().replace(/ /g, '');
            let template_name = false;
            let template_languages = false;
            let parameters = [];
            let phone_number_id = false;
            if (whatsapp) {
                let select = box.find('#sb-whatsapp-send-template-list');
                template_name = select.val();
                template_languages = select.find('option:selected').attr('data-languages');
                phone_number_id = select.find('option:selected').attr('data-phone-id');
                parameters = ['header', 'body', 'button'].map(id => box.find(`#sb-whatsapp-send-template-${id}`).val());
            }
            if (SBForm.errors(box)) {
                SBForm.showErrorMessage(box, 'Please complete the mandatory fields.');
            } else {
                if (loading(this)) {
                    return;
                }
                let user_details = [];
                if (message.includes('recipient_name') || parameters.join('').includes('recipient_name')) {
                    user_details.push('first_name', 'last_name');
                }
                if (message.includes('recipient_email') || parameters.join('').includes('recipient_email')) {
                    user_details.push('email');
                }
                if (type == 'message') {
                    SBF.ajax({
                        function: 'direct-message',
                        user_ids: user_ids,
                        message: message
                    }, (response) => {
                        $(this).sbLoading(false);
                        let send_email = SB_ADMIN_SETTINGS.notify_user_email;
                        let send_sms = SB_ADMIN_SETTINGS.sms_active_users;
                        if (SBF.errorValidation(response)) {
                            return SBForm.showErrorMessage(box, 'An error has occurred. Please make sure all user ids are correct.');
                        }
                        if (send_email || send_sms) {
                            SBF.ajax({
                                function: 'get-users-with-details',
                                user_ids: user_ids,
                                details: user_details.concat(send_email && send_sms ? ['email', 'phone'] : [send_email ? 'email' : 'phone'])
                            }, (response) => {
                                if (send_email && response.email.length) {
                                    recursiveSending(response, 'email', message, 0, send_sms ? response.phone : [], 'email', subject);
                                } else if (send_sms && response.phone.length) {
                                    recursiveSending(response, 'phone', message, 0, [], 'sms');
                                } else {
                                    admin.sbHideLightbox();
                                }
                            });
                        }
                        infoBottom(`${SBF.slugToString(type)} sent to all users.`);
                    });
                } else {
                    let slug = type == 'custom_email' ? 'email' : 'phone';
                    SBF.ajax({
                        function: 'get-users-with-details',
                        user_ids: user_ids,
                        details: user_details.concat([slug])
                    }, (response) => {
                        if (response[slug].length) {
                            recursiveSending(response, slug, message, 0, [], type, subject, template_name, parameters, template_languages, phone_number_id);
                        } else {
                            $(this).sbLoading(false);
                            return SBForm.showErrorMessage(box, 'No users found.');
                        }
                    });
                }
            }
        });

        function recursiveSending(user_ids, slug, message, i = 0, user_ids_sms = [], type, subject = false, template_name = false, parameters = false, template_languages = false, phone_number_id = false) {
            let settings = { whatsapp: ['whatsapp-send-template', 'messages', ' a phone number.', 'direct-whatsapp'], email: ['create-email', 'emails', ' an email address.', false], custom_email: ['send-custom-email', 'emails', ' an email address.', 'direct-emails'], sms: ['send-sms', 'text messages', ' a phone number.', 'direct-sms'] }[type];
            SBF.ajax({
                function: settings[0],
                to: user_ids[slug][i].value,
                recipient_id: user_ids[slug][i].id,
                sender_name: SB_ACTIVE_AGENT['full_name'],
                sender_profile_image: SB_ACTIVE_AGENT['profile_image'],
                subject: subject,
                message: message,
                template_name: template_name,
                parameters: parameters,
                template_languages: template_languages,
                template: false,
                phone_id: phone_number_id,
                user_name: user_ids.first_name ? (user_ids.first_name[i].value + ' ' + user_ids.last_name[i].value).trim() : false,
                user_email: user_ids.email ? user_ids.email[i].value : false
            }, (response) => {
                let user_ids_length = user_ids[slug].length;
                let box = type == 'whatsapp' ? admin.find('#sb-whatsapp-send-template-box') : direct_message_box;
                box.find('.sb-bottom > div').html(`${sb_('Sending')} ${sb_(settings[1])}... ${i + 1} / ${user_ids_length}`);
                if (response) {
                    if (response !== true && (('status' in response && response.status == 400) || ('error' in response && ![131030, 131009].includes(response.error.code)))) {
                        SBForm.showErrorMessage(box, response.error ? response.error.message : `${response.message} Details at ${response.more_info}`);
                        console.error(response);
                        box.find('.sb-loading').sbLoading(false);
                        box.find('.sb-bottom > div').html('');
                        return;
                    }
                    if (i < user_ids_length - 1) {
                        return recursiveSending(user_ids, slug, message, i + 1, user_ids_sms, type, subject, template_name, parameters, template_languages, phone_number_id);
                    } else {
                        if (user_ids_sms.length) {
                            recursiveSending(user_ids_sms, slug, message, 0, [], 'sms', false);
                        } else {
                            admin.sbHideLightbox();
                            if (settings[3]) {
                                SBF.ajax({ function: 'reports-update', name: settings[3], value: message.substr(0, 18) + ' | ' + user_ids_length });
                            }
                        }
                        infoBottom(user_ids_length == 1 ? 'The message has been sent.' : sb_('The message was sent to all users who have' + settings[2]));
                    }
                } else {
                    console.warn(response);
                }
            });
        }

        // User filters
        $(users_area).on('click', '.sb-filter-btn > i', function () {
            $(this).parent().toggleClass('sb-active');
        });

        /*
        * ----------------------------------------------------------
        * Settings area
        * ----------------------------------------------------------
        */

        // Open settings area by URL
        if (SBF.getURL('setting')) {
            SBSettings.open(SBF.getURL('setting'), true);
        }

        // Settings history
        $(settings_area).on('click', ' > .sb-tab > .sb-nav [id]', function () {
            let id = $(this).attr('id').substr(4);
            if (SBF.getURL('setting') != id) {
                pushState('?setting=' + id);
            }
        });

        // Upload
        $(admin).on('click', '.sb-repeater-upload, [data-type="upload-image"] .image, [data-type="upload-file"] .sb-btn, #sb-chatbot-add-files, #sb-import-settings a, #sb-import-users a', function () {
            let extensions = '';
            upload_target = this;
            if ($(this).attr('id') == 'sb-chatbot-add-files') {
                extensions = '.pdf,.txt';
                chatbot_files_table.find('.sb-pending').remove();
                SBApps.openAI.train.skip_files = [];
                upload_function = function () {
                    let files = upload_input.prop('files');
                    let code = '';
                    for (var i = 0; i < files.length; i++) {
                        let size = parseInt(files[i].size / 1000);
                        code += `<tr class="sb-pending" data-name="${files[i].name}"><td><input type="checkbox" /></td><td>${files[i].name}<label>${sb_('Pending')}</label></td><td>${size ? size : 1} KB</td><td><i class="sb-icon-delete"></i></td></tr>`;
                    }
                    chatbot_files_table.append(code);
                };
            } else if ($(this).parent().parent().attr('id') == 'sb-import-settings') {
                extensions = '.json';
                upload_on_success = (response) => {
                    if (loading(this)) return;
                    SBF.ajax({
                        function: 'import-settings',
                        file_url: response
                    }, (response) => {
                        if (response) {
                            infoBottom('Settings saved. Reload to apply the changes.');
                        } else {
                            infoPanel(response);
                        }
                        $(this).sbLoading(false);
                    });
                    upload_on_success = false;
                }
            } else if ($(this).parent().parent().attr('id') == 'sb-import-users') {
                extensions = '.csv';
                upload_on_success = (response) => {
                    if (loading(this)) return;
                    SBF.ajax({
                        function: 'import-users',
                        file_url: response
                    }, (response) => {
                        if (response) {
                            infoBottom('Users imported successfully.');
                        } else {
                            infoPanel(response);
                        }
                        $(this).sbLoading(false);
                    });
                    upload_on_success = false;
                }
            } else if ($(this).hasClass('image')) {
                extensions = '.png,.jpg,.jpeg,.gif,.webp';
            } else if ($(this).hasClass('sb-repeater-upload')) {
                upload_on_success = (response) => {
                    let parent = $(this).parent();
                    if (parent.find('.repeater-item:last-child input').val()) {
                        SBSettings.repeater.add(this);
                    }
                    parent.find('.repeater-item:last-child input').val(response);
                }
            }
            upload_input.attr('accept', extensions).prop('value', '').click();
        });

        $(settings_area).on('click', '[data-type="upload-image"] .image > i', function (e) {
            SBF.ajax({ function: 'delete-file', path: $(this).parent().attr('data-value') });
            $(this).parent().removeAttr('data-value').css('background-image', '');
            e.preventDefault();
            return false;
        });

        // Repeater
        $(admin).on('click', '.sb-repeater-add', function () {
            SBSettings.repeater.add(this);
        });

        $(admin).on('click', '.repeater-item > i', function () {
            setTimeout(() => {
                SBSettings.repeater.delete(this);
            }, 100);
        });

        // Color picker
        SBSettings.initColorPicker();

        $(settings_area).find('[data-type="color"]').focusout(function () {
            let color = $(this).find('input').val();
            if (color == 'rgb(255, 255, 255)' && ['color-admin-1', 'color-1'].includes($(this).attr('id'))) {
                color = '';
            }
            setTimeout(() => {
                $(this).find('input').val(color);
                $(this).find('.color-preview').css('background-color', color);
            }, 300);
            SBSettings.set($(this).attr('id'), [color, 'color']);
        });

        $(settings_area).on('click', '.sb-type-color .input i', function (e) {
            $(this).parent().find('input').removeAttr('style').val('');
        });

        // Color palette
        $(settings_area).on('click', '.sb-color-palette span', function () {
            let active = $(this).sbActive();
            $(this).closest('.sb-repeater').find('.sb-active').sbActive(false);
            $(this).sbActive(!active);
        });

        $(settings_area).on('click', '.sb-color-palette ul li', function () {
            $(this).parent().parent().attr('data-value', $(this).data('value')).find('span').sbActive(false);
        });

        // Select images
        $(settings_area).on('click', '[data-type="select-images"] .input > div', function () {
            $(this).siblings().sbActive(false);
            $(this).sbActive(true);
        });

        // Select checkbox
        $(settings_area).on('click', '.sb-select-checkbox-input', function () {
            $(this).toggleClass('sb-active');
        });

        $(settings_area).on('click', '.sb-select-checkbox input', function () {
            let parent = $(this).closest('[data-type]');
            parent.find('.sb-select-checkbox-input').val(SBSettings.get(parent)[1].join(', '));
        });

        // Save
        $(settings_area).on('click', '.sb-save-changes', function () {
            SBSettings.save(this);
        });

        // Miscellaneous
        $(settings_area).on('change', '#saved-replies [data-id="reply-name"], [data-id="rich-message-name"]', function () {
            $(this).val($(this).val().replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-+/, '').replace(/-+$/, '').replace(/ /g, ''));
        });

        $(settings_area).on('change', '#user-additional-fields [data-id="extra-field-name"]', function () {
            $(this).parent().next().find('input').val(SBF.stringToSlug($(this).val()));
        });

        $(settings_area).on('click', '#timetable-utc input', function () {
            if (!$(this).val()) {
                $(this).val(today.getTimezoneOffset() / 60);
            }
        });

        $(settings_area).on('click', '#dialogflow-sync-btn a', function (e) {
            let url = 'https://accounts.google.com/o/oauth2/auth?scope=https%3A%2F%2Fwww.googleapis.com/auth/dialogflow%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fcloud-translation%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fcloud-language&response_type=code&access_type=offline&redirect_uri=' + SB_URL + '/apps/dialogflow/functions.php&client_id={client_id}&prompt=consent';
            if (SB_ADMIN_SETTINGS.cloud && settings_area.find('#google-sync-mode select').val() == 'auto') {
                if (SB_ADMIN_SETTINGS.credits) {
                    window.open(url.replace('{client_id}', SB_ADMIN_SETTINGS.google_client_id));
                    e.preventDefault();
                    return false;
                }
            } else {
                let client_id = settings_area.find('#google-client-id input').val();
                if (client_id && settings_area.find('#google-client-secret input').val()) {
                    window.open(url.replace('{client_id}', client_id));
                } else {
                    infoPanel('Before continuing enter Client ID and Client secret. Check the docs for more details.');
                }
                e.preventDefault();
                return false;
            }
        });

        $(settings_area).on('click', '#dialogflow-redirect-url-btn a', function (e) {
            infoPanel(`<pre>${SB_URL}/apps/dialogflow/functions.php</pre>`);
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#dialogflow-saved-replies a', function (e) {
            infoPanel('', 'alert', () => {
                if (!loading(this)) {
                    SBF.ajax({ function: 'dialogflow-saved-replies' }, () => {
                        $(this).sbLoading(false);
                    });
                }
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#test-email-user a, #test-email-agent a', function () {
            let email = $(this).parent().find('input').val();
            if (email && email.indexOf('@') > 0 && !loading(this)) {
                SBF.ajax({
                    function: 'send-test-email',
                    to: email,
                    email_type: $(this).parent().parent().attr('id') == 'test-email-user' ? 'user' : 'agent'
                }, (response) => {
                    infoPanel(response === true ? 'The message has been sent.' : response, 'info');
                    $(this).sbLoading(false);
                });
            }
        });

        $(settings_area).on('click', '#email-server-troubleshoot a', function (e) {
            settings_area.find('#test-email-user input').val(SB_ACTIVE_AGENT.email).next().click()[0].scrollIntoView({ behavior: 'smooth' });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#test-sms-user a, #test-sms-agent a', function () {
            let phone_number = $(this).parent().find('input').val();
            if (phone_number && !loading(this)) {
                SBF.ajax({
                    function: 'send-sms',
                    message: 'Hello World!',
                    to: phone_number
                }, (response) => {
                    infoPanel(response && ['sent', 'queued'].includes(response.status) ? 'The message has been sent.' : `<pre>${JSON.stringify(response)}</pre>`);
                    $(this).sbLoading(false);
                });
            }
        });

        $(settings_area).on('click', '.sb-timetable > div > div > div', function () {
            let timetable = $(this).closest('.sb-timetable');
            let active = $(this).sbActive();
            $(timetable).find('.sb-active').sbActive(false);
            if (active) {
                $(this).sbActive(false).find('.sb-custom-select').remove();
            } else {
                let select = $(timetable).find('> .sb-custom-select').html();
                $(timetable).find(' > div .sb-custom-select').remove();
                $(this).append(`<div class="sb-custom-select">${select}</div>`).sbActive(true);
            }
        });

        $(settings_area).on('click', '.sb-timetable .sb-custom-select span', function () {
            let value = [$(this).html(), $(this).attr('data-value')];
            $(this).closest('.sb-timetable').find('> div > div > .sb-active').html(value[0]).attr('data-value', value[1]);
            $(this).parent().sbActive(false);
        });

        $(settings_area).on('click', '#system-requirements a', function (e) {
            let code = '';
            SBF.ajax({
                function: 'system-requirements'
            }, (response) => {
                for (var key in response) {
                    code += `<div class="sb-input"><span>${sb_(SBF.slugToString(key))}</span><div${response[key] ? ' class="sb-green"' : ''}>${response[key] ? sb_('Success') : sb_('Error')}</div></div>`;
                }
                loadingGlobal(false);
                SBAdmin.genericPanel('requirements', 'System requirements', code);
            });
            loadingGlobal();
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#sb-path a', function (e) {
            SBF.ajax({
                function: 'path'
            }, (response) => {
                infoPanel(`<pre>${response}</pre>`);
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#sb-url a', function (e) {
            infoPanel(`<pre>${SB_URL}</pre>`);
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#delete-leads a', function (e) {
            if (!$(this).sbLoading()) {
                infoPanel('All leads, including all the linked conversations and messages, will be deleted permanently.', 'alert', () => {
                    $(this).sbLoading(true);
                    SBF.ajax({
                        function: 'delete-leads'
                    }, () => {
                        infoPanel('Leads and conversations successfully deleted.');
                        $(this).sbLoading(false);
                    });
                });
            }
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#sb-export-settings a', function (e) {
            e.preventDefault();
            if (loading(this)) return;
            SBF.ajax({
                function: 'export-settings'
            }, (response) => {
                dialogDeleteFile(response, 'sb-export-settings-close', 'Settings exported');
                $(this).sbLoading(false);
            });
            return false;
        });

        $(admin).on('click', '#sb-export-settings-close .sb-close, #sb-export-users-close .sb-close, #sb-export-report-close .sb-close', function () {
            SBF.ajax({ function: 'delete-file', path: admin.find('.sb-dialog-box p pre').html() });
        });

        if (!SB_ADMIN_SETTINGS.cloud) {
            $(settings_area).on('change', '#push-notifications-provider select', function () {
                let is_pusher = $(this).val() == 'pusher';
                SBSettings.visibility(0, is_pusher);
                SBF.ajax({ function: 'update-sw', url: is_pusher ? 'https://js.pusher.com/beams/service-worker.js' : 'https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.sw.js' });
            });

            $(settings_area).on('click', '#push-notifications-sw-path a', function (e) {
                let path = urlStrip(location.href.replace(location.host, '')).replace('admin.php', '');
                if (path.includes('?')) {
                    path = path.substring(0, path.indexOf('?'));
                }
                infoPanel('<pre>' + path + '</pre>');
                e.preventDefault();
                return false;
            });
        }

        $(settings_area).on('click', '#push-notifications-btn a', function (e) {
            if (SB_ADMIN_SETTINGS.cloud || settings_area.find('#push-notifications-provider select').val() == 'onesignal') {
                if (typeof OneSignal != ND) {
                    OneSignal.Slidedown.promptPush({ force: true });
                } else {
                    SBF.serviceWorker.initPushNotifications();
                }
            } else {
                Notification.requestPermission();
            }
            e.preventDefault();
            return false;
        });

        $(settings_area).on('input', '#sb-search-settings', function () {
            let search = $(this).val().toLowerCase();
            SBF.search(search, () => {
                let code = '';
                let dropdown = settings_area.find('.sb-search-dropdown-items');
                if (search.length > 2) {
                    let navs = settings_area.find('> .sb-tab > .sb-nav li').map(function () { return $(this).text().trim() }).get();
                    settings_area.find('.sb-setting').each(function () {
                        let keywords = $(this).attr('data-keywords');
                        let id = $(this).attr('id');
                        if ((keywords && keywords.includes(search)) || (id && id.replaceAll('-', '-').includes(search)) || $(this).find('.sb-setting-content').text().toLowerCase().includes(search)) {
                            let index = $(this).parent().index();
                            code += `<div data-tab-index="${index}" data-setting="${$(this).attr('id')}">${navs[index]} > ${$(this).find('h2').text()}</div>`;
                        }
                    });
                }
                dropdown.html(code);
                if (dropdown.outerHeight() > $(window).height() - 100) {
                    dropdown.css('max-height', $(window).height() - 100);
                    dropdown.addClass('sb-scroll-area');
                } else {
                    dropdown.removeClass('sb-scroll-area');
                }
            });
        });

        $(settings_area).on('click', '.sb-search-dropdown-items div', function () {
            let index = $(this).attr('data-tab-index');
            let item = settings_area.find('> .sb-tab > .sb-nav li').eq(index);
            item.click();
            item.get(0).scrollIntoView();
            settings_area.find('#' + $(this).attr('data-setting'))[0].scrollIntoView();
        });

        // Slack  
        $(settings_area).on('click', '#slack-button a', () => {
            window.open('https://board.support/synch/?service=slack&plugin_url=' + SB_URL + cloudURL());
            return false;
        });

        $(settings_area).on('click', '#slack-test a', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'send-slack-message',
                user_id: false,
                full_name: SB_ACTIVE_AGENT['full_name'],
                profile_image: SB_ACTIVE_AGENT['profile_image'],
                message: 'Lorem ipsum dolor sit amete consectetur adipiscing elite incidido labore et dolore magna aliqua.',
                attachments: [['Example link', SB_URL + '/media/user.svg'], ['Example link two', SB_URL + '/media/user.svg']],
                channel: settings_area.find('#slack-channel input').val()
            }, (response) => {
                if (SBF.errorValidation(response)) {
                    if (response[1] == 'slack-not-active') {
                        infoPanel('Please first activate Slack, then save the settings and reload the admin area.');
                    } else {
                        infoPanel('Error. Response: ' + JSON.stringify(response));
                    }
                } else {
                    infoPanel(response[0] == 'success' ? 'Slack message successfully sent. Check your Slack app!' : JSON.stringify(response));
                }
                $(this).sbLoading(false);
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#tab-slack', function () {
            let input = settings_area.find('#slack-agents .input');
            input.html('<div class="sb-loading"></div>');
            SBF.ajax({
                function: 'slack-users'
            }, (response) => {
                let code = '';
                if (SBF.errorValidation(response, 'slack-token-not-found')) {
                    code = `<p>${sb_('Synchronize Slack and save changes before linking agents.')}</p>`;
                } else {
                    let select = '<option value="-1"></option>';
                    for (var i = 0; i < response.agents.length; i++) {
                        select += `<option value="${response.agents[i].id}">${response.agents[i].name}</option>`;
                    }
                    for (var i = 0; i < response.slack_users.length; i++) {
                        code += `<div data-id="${response.slack_users[i].id}"><label>${response.slack_users[i].name}</label><select>${select}</select></div>`;
                    }
                }
                input.html(code);
                SBSettings.set('slack-agents', [response.saved, 'double-select']);
            });
        });

        $(settings_area).on('click', '#slack-archive-channels a', function (e) {
            e.preventDefault();
            if (loading(this)) return;
            SBF.ajax({
                function: 'archive-slack-channels'
            }, (response) => {
                if (response === true) {
                    infoPanel('Slack channels archived successfully!');
                }
                $(this).sbLoading(false);
            });
        });

        $(settings_area).on('click', '#slack-channel-ids a', function (e) {
            e.preventDefault();
            if (loading(this)) return;
            SBF.ajax({
                function: 'slack-channels',
                code: true
            }, (response) => {
                infoPanel(response, 'info', false, '', '', true);
                $(this).sbLoading(false);
            });
        });


        // Messenger, WhatsApp, Text messages, Twitter, Telegram, Viber, Zalo
        $(settings_area).on('click', '#whatsapp-twilio-btn a, #whatsapp-twilio-get-configuartion-btn a, #sms-btn a, #wechat-btn a, #twitter-callback a, #viber-webhook a, #zalo-webhook a, [data-id="line-webhook"], #messenger-path-btn a', function (e) {
            let id = $(this).closest('[id]').attr('id');
            let extra = '';
            e.preventDefault();
            if (id == 'line') {
                extra = $(this).closest('.repeater-item').find('[data-id="line-secret"]').val();
                if (!extra) {
                    return;
                } else {
                    extra = '?line_secret=' + extra;
                }
            }
            infoPanel(`<pre>${SB_URL + (id == 'sms-btn' ? '/include/api.php' : ('/apps/' + (id.includes('-') ? id.substring(0, id.indexOf('-')) : id) + '/post.php')) + extra + cloudURL().replace('&', extra ? '&' : '?')}</pre>`);
            return false;
        });

        $(settings_area).on('click', '[data-id="telegram-numbers-button"], #viber-button a, #whatsapp-360-button a', function (e) {
            let calls = { 'telegram-button': ['#telegram-token input', 'telegram-synchronization', ['result', true]], 'viber-button': ['#viber-token input', 'viber-synchronization', ['status_message', 'ok']], 'whatsapp-360-button': ['#whatsapp-360-key input', 'whatsapp-360-synchronization', ['success', true]] };
            let id = $(this).parent().attr('id');
            let token;
            let is_additional_number = false;
            if (!id) {
                let buttons = { 'telegram-numbers-button': 'telegram-button' };
                let inputs = { 'telegram-button': 'telegram-numbers-token' };
                id = buttons[$(this).attr('data-id')];
                token = $(this).closest('.repeater-item').find(`[data-id="${inputs[id]}"]`).val().trim();
                is_additional_number = true;
            } else {
                token = settings_area.find(calls[id][0]).val().trim();
            }
            calls = calls[id];
            e.preventDefault();
            if (!token || loading(this)) return false;
            SBF.ajax({
                function: calls[1],
                token: token,
                cloud_token: cloudURL(),
                is_additional_number: is_additional_number
            }, (response) => {
                infoPanel(calls[2][0] in response && response[calls[2][0]] == calls[2][1] ? 'Synchronization completed.' : JSON.stringify(response));
                $(this).sbLoading(false);
            });
            return false;
        });

        $(settings_area).on('click', '#whatsapp-test-template a', function (e) {
            e.preventDefault();
            let phone = $(this).parent().find('input').val();
            if (!phone || loading(this)) return;
            SBF.ajax({
                function: 'whatsapp-send-template',
                to: phone
            }, (response) => {
                infoPanel(response ? ('error' in response ? (response.error.message ? response.error.message : response.error) : 'Message sent, check your WhatsApp!') : response);
                $(this).sbLoading(false);
            });
            return false;
        });

        $(settings_area).on('click', '#twitter-subscribe a', function (e) {
            e.preventDefault();
            if (loading(this)) return false;
            SBF.ajax({
                function: 'twitter-subscribe',
                cloud_token: cloudURL()
            }, (response) => {
                infoPanel(response === true ? 'Synchronization completed.' : JSON.stringify(response));
                $(this).sbLoading(false);
            });
            return false;
        });

        if (!SB_ADMIN_SETTINGS.cloud) {
            $(settings_area).on('click', '#messenger-sync-btn a', () => {
                window.open('https://board.support/synch/?service=messenger&plugin_url=' + SB_URL + cloudURL());
                return false;
            });
        }

        $(settings_area).on('change', '#messenger-sync-mode select', function () {
            SBSettings.visibility(1, $(this).val() != 'manual');
        });

        $(settings_area).on('click', '#messenger-unsubscribe a', function (e) {
            e.preventDefault();
            infoPanel('', 'alert', () => {
                if (loading(this)) return false;
                SBF.ajax({
                    function: 'messenger-unsubscribe'
                }, (response) => {
                    $(this).sbLoading(false);
                    if (response) {
                        infoPanel(JSON.stringify(response));
                    } else {
                        settings_area.find('#messenger-pages .repeater-item > i').click();
                        setTimeout(() => {
                            SBSettings.save();
                            infoPanel('Operation successful.');
                        }, 300);
                    }
                });
            });
            return false;
        });

        $(settings_area).on('change', '#open-ai-mode select', function () {
            SBSettings.visibility(2, $(this).val() != 'assistant');
        });

        // WordPress
        $(settings_area).on('click', '#wp-sync a', function (e) {
            e.preventDefault();
            if (loading(this)) return false;
            SBApps.wordpress.ajax('wp-sync', {}, (response) => {
                if (response === true || response === '1') {
                    SBUsers.update();
                    infoPanel('WordPress users successfully imported.');
                } else {
                    infoPanel('Error. Response: ' + JSON.stringify(response));
                }
                $(this).sbLoading(false);
            });
            return false;
        });

        $('body').on('click', '#wp-admin-bar-logout', function () {
            SBF.logout(false);
        });

        $(settings_area).on('click', '#whatsapp-clear-flows a', function (e) {
            e.preventDefault();
            SBF.ajax({
                function: 'whatsapp-clear-flows'
            });
        });

        // Translations
        $(settings_area).on('click', '#tab-translations', function () {
            let nav = settings_area.find('.sb-translations > .sb-nav > ul');
            if (!nav.html()) {
                let code = '';
                for (var key in SB_LANGUAGE_CODES) {
                    if (key == 'en') continue;
                    code += `<li data-code="${key}"><img src="${SB_URL}/media/flags/${key}.png" />${SB_LANGUAGE_CODES[key]}</li>`;
                }
                nav.html(code);
            }
        });

        $(settings_area).on('click', '.sb-translations .sb-nav li', function () {
            SBSettings.translations.load($(this).data('code'));
        });

        $(settings_area).on('click', '.sb-translations .sb-menu-wide li', function () {
            settings_area.find(`.sb-translations [data-area="${$(this).data('value')}"]`).sbActive(true).siblings().sbActive(false);
        });

        $(settings_area).on('click', '.sb-add-translation', function () {
            settings_area.find('.sb-translations-list > .sb-active').prepend(`<div class="sb-setting sb-type-text sb-new-translation"><input type="text" placeholder="${sb_('Enter original text...')}"><input type="text" placeholder="${sb_('Enter translation...')}"></div></div>`);
        });

        $(settings_area).on('input', '.sb-search-translation input', function () {
            let search = $(this).val().toLowerCase();
            SBF.search(search, () => {
                if (search.length > 1) {
                    settings_area.find('.sb-translations .sb-content > .sb-active label').each(function () {
                        let value = $(this).html().toLowerCase();
                        if (value.includes(search) && value != temp) {
                            let scroll_area = settings_area.find('.sb-scroll-area');
                            scroll_area[0].scrollTop = 0;
                            scroll_area[0].scrollTop = $(this).position().top - 80;
                            temp = value;
                            return false;
                        }
                    });
                }
            });
        });

        // Email piping manual sync
        $(settings_area).on('click', '[data-id="email-piping-sync"]', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'email-piping',
                force: true
            }, (response) => {
                infoPanel(response === true ? 'Syncronization completed.' : response);
                $(this).sbLoading(false);
            });
            e.preventDefault();
        });

        // Automations
        $(settings_area).on('click', '#tab-automations', function () {
            SBSettings.automations.get(() => {
                SBSettings.automations.populate();
                loadingGlobal(false);
            }, true);
            loadingGlobal();
        });

        $(admin).on('click', '.sb-add-condition', function () {
            SBSettings.automations.addCondition($(this).prev());
        });

        $(admin).on('change', '.sb-condition-1 select', function () {
            SBSettings.automations.updateCondition(this);
        });

        $(admin).on('change', '.sb-condition-2 select', function () {
            $(this).parent().next().setClass('sb-hide', ['is-set', 'is-not-set'].includes($(this).val()));
        });

        $(automations_area_select).on('click', 'li', function () {
            SBSettings.automations.populate($(this).data('value'));
        });

        $(automations_area_nav).on('click', 'li', function () {
            SBSettings.automations.show($(this).attr('data-id'));
        });

        $(automations_area).on('click', '.sb-add-automation', function () {
            SBSettings.automations.add();
        });

        $(automations_area_nav).on('click', 'li i', function () {
            infoPanel(`The automation will be deleted permanently.`, 'alert', () => {
                SBSettings.automations.delete(this);
            });
        });

        // Conflicts and warnings
        let warning_messages = ['Settings > {R} won\'t work if Settings > {R2} is active.'];
        $(settings_area).on('click', '.sb-setting', function (e) {
            let id = $(this).attr('id');
            if ($(this).hasClass('sb-type-multi-input')) {
                id = $(e.target).parent().attr('id');
            }
            switch (id) {
                case 'close-chat':
                case 'close-message':
                    if (settings_area.find('#close-active input').is(':checked') && settings_area.find('#close-chat input').is(':checked')) {
                        infoBottom(warning_messages[0].replace('{R}', 'Messages > Close message').replace('{R2}', 'Chat > Close chat'), 'info');
                    }
                    break;
                case 'chat-timetable':
                case 'follow-message':
                case 'queue':
                case 'routing':
                    if (((id == 'queue' && settings_area.find('#queue-active input').is(':checked')) || (id == 'routing' && settings_area.find('#routing input').is(':checked')) || (id == 'follow-message' && settings_area.find('#follow-active input').is(':checked')) || (id == 'chat-timetable' && settings_area.find('#chat-timetable-active input').is(':checked'))) && settings_area.find('#dialogflow-human-takeover-active input').is(':checked')) {
                        infoBottom('Since Settings > Artificial Intelligence > Human takeover is active, this option will only take effect during human takeover.', 'info');
                    }
                    break;
                case 'notify-agent-email':
                case 'push-notifications':
                case 'sms':
                    if (((id == 'sms' && settings_area.find('#sms-active-agents input').is(':checked')) || (id == 'push-notifications' && settings_area.find('#push-notifications-active input').is(':checked')) || (id == 'notify-agent-email' && settings_area.find('#notify-agent-email input').is(':checked'))) && settings_area.find('#dialogflow-human-takeover-active input').is(':checked')) {
                        infoBottom('Since Settings > Artificial Intelligence > Human takeover is active, notifications will be sent only after the human takeover.', 'info');
                    }
                    break;
                case 'privacy':
                    if (settings_area.find('#privacy-active input').is(':checked') && settings_area.find('#registration-required select').val()) {
                        infoBottom(warning_messages[0].replace('{R}', 'Messages > Privacy message').replace('{R2}', 'Users > Require registration'), 'info');
                    }
                    break;
                case 'google-multilingual-translation':
                    if ($(e.target).is(':checked') && settings_area.find('#open-ai-active input').is(':checked') && !settings_area.find('#open-ai-training-data-language select').val()) {
                        infoBottom('If your OpenAI training data isn\'t in English, set the default language under OpenAI > Training data language.', 'info');
                    }
                    break;
            }
        });

        $(users_table_menu).on('click', '[data-type="online"]', function () {
            if (!SB_ADMIN_SETTINGS.visitors_registration) {
                infoBottom('Settings > Users > Register all visitors must be enabled to see online users.', 'info');
            }
        });

        $(settings_area).on('click', '#dialogflow-active,#dialogflow-welcome,#dialogflow-departments', function (e) {// Depreceated
            infoBottom('Warning! We will stop supporting Dialogflow by the end of 2025. All its features will be available in Support Board through OpenAI. Please use OpenAI instead of Dialogflow.', 'info');
        });

        $(settings_area).on('change', '#registration-required select', function () {
            let value = $(this).val();
            if (['registration-login'].includes(value) && !settings_area.find('[id="reg-email"] input').is(':checked')) {
                infoBottom('The email field is required to activate the login form.', 'info');
            }
            if (value && settings_area.find('#privacy-active input').is(':checked')) {
                infoBottom(warning_messages[0].replace('{R}', 'Messages > Privacy message').replace('{R2}', 'Users > Require registration'), 'info');
            }
        });

        /*
        * ----------------------------------------------------------
        * Chatbot area
        * ----------------------------------------------------------
        */

        $(chatbot_area).on('click', '#sb-flow-add', function () {
            SBAdmin.genericPanel('flow-add', 'Enter the flow name', '<div class="sb-setting"><input type="text"></div>', ['Add new flow']);
        });

        $(admin).on('click', '#sb-add-new-flow', function () {
            SBApps.openAI.flows.set(admin.find('.sb-flow-add-box input').val().replace(/[^a-zA-Z\u00C0-\u1FFF\u2C00-\uD7FF\uAC00-\uD7A3]/g, ''));
            admin.sbHideLightbox();
        });

        $(flows_area).on('click', '.sb-flow-block', function () {
            flows_area.find('.sb-flow-block,.sb-flow-add-block').sbActive(false);
            $(this).sbActive(true);
            let block = SBApps.openAI.flows.blocks.get();
            let code;
            let code_2 = '';
            let type = $(this).attr('data-type');
            let code_repeater = `<div class="sb-title">{R}</div><div data-type="repeater" class="sb-setting sb-type-repeater"><div class="input"><div class="sb-repeater">{R2}</div><div class="sb-btn sb-btn-white sb-repeater-add sb-icon"><i class="sb-icon-plus"></i>${sb_('Add new item')}</div></div></div>`;
            let code_conditions = `<div class="sb-title">${sb_('Conditions')}</div><div class="sb-flow-conditions"></div><div class="sb-add-condition sb-btn sb-icon sb-btn-white"><i class="sb-icon-plus"></i>${sb_('Add condition')}</div>`;
            let code_message = `<div class="sb-title">${sb_('Message')}</div><div class="sb-setting"><textarea placeholder="${sb_('The message sent to the user...')}">${block.message}</textarea></div>`;
            let code_select_user_details = SBApps.openAI.getCode.select_user_details();
            switch (type) {
                case 'start':
                    code = `<div class="sb-title">${sb_('Start event')}</div><div class="sb-setting"><select class="sb-flow-start-select"><option value="message"${block.start == 'message' ? ' selected' : ''}>${sb_('User message')}</option><option value="conversation"${block.start == 'conversation' ? ' selected' : ''}>${sb_('New conversation started')}</option><option value="load"${block.start == 'load' ? ' selected' : ''}>${sb_('On page load')}</option></select></div><div class="sb-title sb-title-flow-start${block.start == 'message' ? `` : ` sb-hide`}">${sb_('User message')}</div><div data-type="repeater" class="sb-setting sb-flow-start-messages sb-type-repeater"><div class="input"><div class="sb-repeater"><div class="repeater-item"><div class="sb-setting"><textarea data-id="message"></textarea></div><i class="sb-icon-close"></i></div></div><div class="sb-btn sb-btn-white sb-repeater-add sb-icon"><i class="sb-icon-plus"></i>${sb_('Add message')}</div></div></div>${code_conditions}<div class="sb-title">${sb_('Disabled')}</div><div class="sb-setting"><input type="checkbox" id="sb-flow-disabled"${block.disabled ? ' checked' : ''}></div>`;
                    break;
                case 'button_list':
                    if (!block.options || !block.options.length) {
                        block.options = [''];
                    }
                    for (var i = 0; i < block.options.length; i++) {
                        code_2 += `<div class="repeater-item"><div><input data-id type="text" value="${block.options[i]}"></div><i class="sb-icon-close"></i></div>`;
                    }
                    code = code_message + code_repeater.replace(`{R}`, sb_('Buttons')).replace(`{R2}`, code_2);
                    break;
                case 'message':
                    if (!block.attachments || !block.attachments.length) {
                        block.attachments = [''];
                    }
                    for (var i = 0; i < block.attachments.length; i++) {
                        code_2 += `<div class="repeater-item"><div><input data-id type="text" value="${block.attachments[i]}" placeholder="${sb_('Enter a link...')}"></div><i class="sb-icon-close"></i></div>`;
                    }
                    code = code_message + code_repeater.replace(`{R}`, sb_('Attachments')).replace(`{R2}`, code_2).replace(`</div></div></div>`, `</div><i class="sb-repeater-upload sb-btn-icon sb-icon-clip"></i></div></div>`);
                    break;
                case 'video':
                    code = `${code_message}<div class="sb-title">${sb_('Video URL')}</div><div class="sb-setting"><input type="url" placeholder="${sb_('Enter a YouTube or Vimeo link...')}" value="${block.url}"></div>`;
                    break;
                case 'get_user_details':
                    if (!block.details || !block.details.length) {
                        block.details = [['', '', false]];
                    }
                    for (var i = 0; i < block.details.length; i++) {
                        code_2 += `<div class="repeater-item"><div>${code_select_user_details.replace(`"${block.details[i][0]}"`, `"${block.details[i][0]}" selected`)}<div class="sb-setting"><input type="text" placeholder="${sb_('Enter a description...')}" value="${block.details[i][1]}" /></div><div class="sb-setting"><label>${sb_('Required')}</label><input type="checkbox" ${block.details[i][2] ? ' checked' : ''}></div></div><i class="sb-icon-close"></i></div>`;
                    }
                    code = code_message + code_repeater.replace(`{R}`, sb_('User details')).replace(`{R2}`, code_2).replace('sb-type-repeater', 'sb-type-repeater sb-repeater-block-user-details');
                    break;
                case 'set_data':
                    code = SBApps.openAI.getCode.set_data(block.data);
                    break;
                case 'action':
                    code = SBApps.openAI.getCode.actions(block.actions);
                    break;
                case 'rest_api':
                    let keys = ['headers', 'save_response'];
                    code = `<div class="sb-title">${sb_('URL')}</div><div class="sb-setting"><input type="url" class="sb-rest-api-url" value="${block.url}"></div><div class="sb-title">${sb_('Method')}</div><div class="sb-setting"><select class="sb-rest-api-method"><option value="GET"${block.method == 'GET' ? ' selected' : ''}>GET</option><option value="POST"${block.method == 'POST' ? ' selected' : ''}>POST</option><option value="PUT"${block.method == 'PUT' ? ' selected' : ''}>PUT</option><option value="PATH"${block.method == 'PATH' ? ' selected' : ''}>PATH</option><option value="DELETE"${block.method == 'DELETE' ? ' selected' : ''}>DELETE</option></select></div><div class="sb-title">${sb_('Body')}</div><div class="sb-setting"><textarea placeholder="JSON">${block.body}</textarea></div>`;
                    for (var i = 0; i < keys.length; i++) {
                        let values = block[keys[i]];
                        if (!values || !values.length) {
                            values = [['', '']];
                        }
                        code += `<div class="sb-title">${sb_(SBF.slugToString(keys[i]))}</div><div data-type="repeater" class="sb-setting sb-type-repeater sb-repeater-block-rest-api sb-rest-api-${keys[i]}"><div class="input"><div class="sb-repeater">`;
                        for (var j = 0; j < values.length; j++) {
                            code += `<div class="repeater-item"><div>${i == 1 ? code_select_user_details.replace(`"${values[j][0]}"`, `"${values[j][0]}" selected`) : `<div class="sb-setting"><input type="text" placeholder="${sb_('Key')}" value="${values[j][0]}" /></div>`}<div class="sb-setting"><input type="text" placeholder="${sb_(i == 1 ? 'e.g. data.id' : 'Value')}" value="${values[j][1]}" /></div></div><i class="sb-icon-close"></i></div>`;
                        }
                        code += `</div><div class="sb-btn sb-btn-white sb-repeater-add sb-icon"><i class="sb-icon-plus"></i>${sb_('Add new item')}</div></div></div>`;
                    }
                    break;
                case 'condition':
                    code = code_conditions;
                    break;
            }
            code += `<div id="sb-block-delete" class="sb-btn-text"><i class="sb-icon-delete"></i>${sb_('Delete')}</div>`
            SBAdmin.genericPanel('flow-block', SBF.slugToString(type), code, ['Save changes'], '', true);
            if (type == 'start' || type == 'condition') {
                if (!Array.isArray(block.message)) block.message = [{ message: block.message }]; // Depreceated
                let repeater = admin.find('.sb-flow-start-messages .sb-repeater');
                let code = SBSettings.repeater.set(block.message, repeater.find('.repeater-item:last-child'));
                SBSettings.automations.setConditions(block.conditions, admin.find('.sb-flow-conditions'));
                if (code) {
                    repeater.html(code);
                }
            }
            step_scroll_positions = [];
            flows_area.find('> div').each(function () {
                step_scroll_positions.push($(this).find('> div')[0].scrollTop);
            });
        });

        $(admin).on('click', '.sb-flow-block-box #sb-save-changes', function () {
            let block = SBApps.openAI.flows.blocks.get();
            let box = admin.find('.sb-flow-block-box');
            block.message = box.find('textarea').val();
            switch (block.type) {
                case 'start':
                    block.message = SBSettings.repeater.get(box.find('.sb-flow-start-messages .repeater-item'));
                    block.start = box.find('select').val();
                    block.disabled = box.find('#sb-flow-disabled').is(':checked');
                    block.conditions = SBSettings.automations.getConditions(box.find('.sb-flow-conditions'));
                    if (block.message.length && block.message[0].message.trim().split(' ').length < 3) {
                        return box.find('.sb-info').sbActive(true).html(sb_('The message must contain at least 3 words.'));
                    }
                    break;
                case 'button_list':
                    block.options = box.find('.sb-repeater input').map(function () { return $(this).val().trim() }).get().filter(function (value) { return value != '' });
                    break;
                case 'message':
                    block.attachments = box.find('.sb-repeater input').map(function () { return $(this).val().trim() }).get().filter(function (value) { return value != '' });
                    break;
                case 'video':
                    block.url = box.find('input').val();
                    break;
                case 'get_user_details':
                    block.details = box.find('.repeater-item').map(function () { return [[$(this).find('select').val(), $(this).find('input[type=text]').val(), $(this).find('input[type=checkbox]').is(':checked')]] }).get();
                    break;
                case 'action':
                case 'set_data':
                    block[block.type == 'action' ? 'actions' : 'data'] = box.find('.repeater-item').map(function () { return [[$(this).find('select').val(), $(this).find('input').length ? $(this).find('input').val().replace(/https?:\/\/|["|:]/g, '') : '']] }).get();
                    break;
                case 'rest_api':
                    block.headers = box.find('.sb-rest-api-headers .repeater-item').map(function () { return [[$(this).find('input').eq(0).val(), $(this).find('input').eq(1).val()]] }).get();
                    block.save_response = box.find('.sb-rest-api-save_response .repeater-item').map(function () { return [[$(this).find('select').val(), $(this).find('input').val()]] }).get();
                    block.url = box.find('.sb-rest-api-url').val();
                    block.method = box.find('.sb-rest-api-method').val();
                    block.body = box.find('textarea').val();
                    delete block.message;
                    break;
                case 'condition':
                    block.conditions = SBSettings.automations.getConditions(box.find('.sb-flow-conditions'));
                    break;
            }
            SBApps.openAI.flows.blocks.set(block);
            flows_area.find('> div').each(function () {
                $(this).find('> div')[0].scrollTop = step_scroll_positions[$(this).index()];
            });
            admin.sbHideLightbox();
        });

        $(admin).on('change', '.sb-repeater-block-actions select', function () {
            $(this).parent().next().remove();
            $(this).parent().parent().append(SBApps.openAI.getCode.action($(this).val(), ''));
        });

        $(admin).on('change', '.sb-flow-start-select', function () {
            admin.find('.sb-title-flow-start, .sb-flow-start-messages').setClass('sb-hide', $(this).val() != 'message');
        });

        $(flows_area).on('mouseleave', '.sb-flow-connectors > div, .sb-flow-block', function () {
            flows_area.find('.sb-flow-block-cnt').sbActive(false);
            is_over_connector = false;
            if ($(this).parent().hasClass('sb-flow-connectors')) {
                SBApps.openAI.flows.blocks.activateLinkedCnts($(this).closest('.sb-flow-block'));
            } else {
                flows_area.find('.sb-flow-connectors > div').sbActive(false);
            }
        });

        $(flows_area).on('mouseenter', '.sb-flow-connectors > div', function () {
            let block_cnt = $(this).closest('.sb-flow-block').parent();
            let next_block_cnt_indexes = SBApps.openAI.flows.blocks.getNextCntIndexes(SBApps.openAI.flows.getActiveIndex(), block_cnt.parent().parent().index(), block_cnt.index());
            is_over_connector = true;
            flows_area.find('> div').eq(block_cnt.parent().parent().index() + 1).find('.sb-flow-block-cnt').sbActive(false).eq(next_block_cnt_indexes[$(this).index()]).sbActive(true);
        });

        $(flows_area).on('mouseenter', '.sb-flow-block', function () {
            SBApps.openAI.flows.blocks.activateLinkedCnts(this);
        });

        $(flows_area).on('click', '.sb-flow-add-block', function () {
            flows_area.find('.sb-flow-block,.sb-flow-add-block').sbActive(false);
            $(this).sbActive(true);
            let active_blocks = SBApps.openAI.flows.steps.get()[SBApps.openAI.flows.blocks.getActiveCntIndex()].map(item => item.type);
            let all = !active_blocks.some(element => ['message', 'button_list', 'video', 'get_user_details', 'condition'].includes(element));
            let nav_items = [['set_data', 'Set data'], ['action', 'Action'], ['condition', 'Condition'], ['rest_api', 'REST API']];
            let code = '';
            for (var i = 0; i < nav_items.length; i++) {
                if (!active_blocks.includes(nav_items[i][0])) {
                    code += `<li data-value="${nav_items[i][0]}">${sb_(nav_items[i][1])}</li>`;
                }
            }
            SBAdmin.genericPanel('flows-blocks-nav', '', `<ul class="sb-menu">${all ? `<li>${sb_('Messages')} <ul><li data-value="message">${sb_('Send message')}</li><li data-value="button_list">${sb_('Send button list')}</li><li data-value="video">${sb_('Send video')}</li></ul></li>` : ``}<li>${sb_('More')} <ul>${all ? `<li data-value="get_user_details">${sb_('Get user details')}</li>` : ``}${code}</ul></li></ul>`);
        });

        $(admin).on('click', '#sb-block-delete', function () {
            SBApps.openAI.flows.blocks.delete();
            admin.sbHideLightbox();
        });

        $(flows_nav).on('click', 'li', function () {
            SBApps.openAI.flows.show($(this).attr('data-value'));
        });

        $(flows_nav).on('click', 'li i', function (e) {
            infoPanel('The flow will be deleted.', 'alert', () => {
                SBApps.openAI.flows.delete($(this).parent().attr('data-value'));
            });
            e.preventDefault();
            return false;
        });

        $(admin).on('click', '.sb-flows-blocks-nav-box [data-value]', function () {
            SBApps.openAI.flows.blocks.add($(this).data('value'));
            admin.sbHideLightbox();
        });

        $(admin).on('mouseenter', '.sb-flow-scroll', function () {
            let is_back = $(this).hasClass('sb-icon-arrow-left');
            flow_scroll_interval = setInterval(() => {
                flows_area[0].scrollLeft += 10 * (is_back ? -1 : 1);
            }, 10);
        });

        $(admin).on('mouseleave', '.sb-flow-scroll', function () {
            clearInterval(flow_scroll_interval);
        });

        $(chatbot_area).on('click', '#sb-train-chatbot', function (e) {
            let success_text = 'The chatbot has been successfully trained.';
            let nav = chatbot_area.find('.sb-nav [data-value="conversations"]');
            infoPanel('<br><br><br><br><br>', 'info', false, 'sb-embeddings-box');
            e.preventDefault();
            if (SB_ADMIN_SETTINGS.cloud && SBCloud.creditsAlert(this, e)) {
                return false;
            }
            if (loading(this)) {
                return false;
            }
            if (chatbot_area.find('.sb-menu-chatbot .sb-active').attr('data-type') == 'flows') {
                SBApps.openAI.flows.save((response) => {
                    infoPanel(success_text, 'info', false, false, 'Success');
                });
                $(this).sbLoading(false);
                return;
            }
            if (nav.sbActive()) {
                let to_update = [];
                for (var i = 0; i < conversations_qea.length; i++) {
                    let qea = chatbot_area.find(`#sb-chatbot-conversations [data-index="${i}"]`);
                    if (qea.length) {
                        qea = [qea.find('input').val(), qea.find('textarea').val()];
                        if (qea[0] != $('<textarea />').html(conversations_qea[i].question).text() || qea[1] != $('<textarea />').html(conversations_qea[i].answer).text()) {
                            to_update.push([conversations_qea[i], qea[0] && qea[1] ? qea : false]);
                        }
                    } else {
                        to_update.push([conversations_qea[i], false]);
                    }
                }
                if (to_update.length) {
                    SBF.ajax({
                        function: 'open-ai-save-conversation-embeddings',
                        qea: to_update
                    }, (response) => {
                        if (response[0] === true) {
                            infoPanel(success_text, 'info', false, false, 'Success');
                        } else if (!SBApps.openAI.train.isError(response[0])) {
                            infoPanel(response[0]);
                        }
                        nav.click();
                        $(this).sbLoading(false);
                    });
                } else {
                    $(this).sbLoading(false);
                    infoPanel(success_text, 'info', false, false, 'Success');
                }
                return;
            }
            SBApps.openAI.train.errors = [];

            // Files
            let index = 0;
            SBApps.openAI.train.files((response) => {

                // Website
                SBApps.openAI.train.urls = chatbot_area.find('[data-id="open-ai-sources-url"]').map(function () { return $(this).val().trim() }).get();
                SBApps.openAI.train.extract_url = chatbot_area.find('[data-id="open-ai-sources-extract-url"]').map(function () { return $(this).is(':checked') }).get();
                SBApps.openAI.train.website((response) => {

                    // Q&A
                    SBApps.openAI.train.qea((response) => {

                        // Articles
                        SBApps.openAI.train.articles((response) => {

                            // Finish
                            SBApps.openAI.init();
                            chatbot_area.find('#sb-repeater-chatbot-website .repeater-item i').click();

                            if (SBApps.openAI.train.errors.length) {
                                infoPanel(sb_('The chatbot has been trained with errors. Check the console for more details.') + '\n\n<pre>' + SBApps.openAI.train.errors.join('<br>') + '</pre>', 'info', false, 'sb-errors-list-box', false, true);
                                console.error(SBApps.openAI.train.errors);
                            } else if (!SBApps.openAI.train.isError(response)) {
                                infoPanel(success_text, 'info', false, false, 'Success');
                            }
                            $(this).sbLoading(false);
                        });
                    });
                });
            });
            return false;
        });

        $(chatbot_area).on('click', '#sb-table-chatbot-files td i, #sb-table-chatbot-website td i, #sb-chatbot-delete-files, #sb-chatbot-delete-website, #sb-chatbot-delete-all-training, #sb-chatbot-delete-all-training-conversations', function () {
            let is_i = $(this).is('i');
            let tr = is_i ? $(this).closest('tr') : false;
            if (is_i && tr.hasClass('sb-pending')) {
                SBApps.openAI.train.skip_files.push(tr.attr('data-name'));
                tr.remove();
                return;
            }
            infoPanel('The training data will be permanently deleted.', 'alert', () => {
                let sources_to_delete = [];
                let id = $(this).attr('id');
                if (is_i) {
                    sources_to_delete = [tr.attr('data-url')];
                } else if (id == 'sb-chatbot-delete-all-training') {
                    sources_to_delete = 'all';
                } else if (id == 'sb-chatbot-delete-all-training-conversations') {
                    sources_to_delete = 'all-conversations';
                } else {
                    let table = $(id == 'sb-chatbot-delete-files' ? chatbot_files_table : chatbot_website_table);
                    if (!table.find('input:checked').length) {
                        table.find('input').prop('checked', true);
                    }
                    table.find('tr').each(function () {
                        if ($(this).find('input:checked').length) {
                            if ($(this).hasClass('sb-pending')) {
                                SBApps.openAI.train.skip_files.push($(this).attr('data-name'));
                                $(this).remove();
                            } else {
                                let url = $(this).attr('data-url');
                                sources_to_delete.push(url);
                                if (SBApps.openAI.train.sitemap_processed_urls.indexOf(url) > -1) {
                                    SBApps.openAI.train.sitemap_processed_urls[SBApps.openAI.train.sitemap_processed_urls.indexOf(url)] = false;
                                }
                            }
                        }
                    });
                }
                if (sources_to_delete.length) {
                    if (loading(this)) return;
                    SBF.ajax({
                        function: 'open-ai-embeddings-delete',
                        sources_to_delete: sources_to_delete
                    }, (response) => {
                        SBApps.openAI.init();
                        if (sources_to_delete == 'all') {
                            chatbot_area.find('.sb-nav [data-value="info"]').click();
                        } else if (sources_to_delete = 'all-conversations') {
                            chatbot_area.find('.sb-nav [data-value="conversations"]').click();
                        }
                        if (response === true) {
                            infoBottom('Training data deleted.');
                        } else {
                            infoPanel(response);
                        }
                        $(this).sbLoading(false);
                    });
                }
            });
            return false;
        });

        $(chatbot_area).on('click', '.sb-nav [data-value="conversations"]', function () {
            let area = chatbot_area.find('#sb-chatbot-conversations');
            if (loading(area)) return;
            SBF.ajax({
                function: 'open-ai-get-conversation-embeddings'
            }, (response) => {
                if (!response.length) {
                    area.html(`<p class="sb-no-results">${sb_('No conversations found.')}</p>`);
                } else {
                    let code = '';
                    conversations_qea = response;
                    for (var i = 0; i < response.length; i++) {
                        code += `<div class="repeater-item" data-value="${response[i].id}" data-index=${i}><div><label>${sb_('Question')}</label><input data-id="q" type="text" value="${response[i].question}" /></div><div class="sb-qea-repeater-answer"><label>${sb_('Answer')}</label><textarea data-id="a">${response[i].answer}</textarea></div><i class="sb-icon-close"></i></div>`;
                    }
                    area.find('.sb-repeater').html(code);
                }
                area.sbLoading(false);
            });
        });

        $(chatbot_area).on('click', '.sb-nav [data-value="info"]', function () {
            let area = chatbot_area.find('#sb-chatbot-info');
            if (loading(area)) return;
            SBF.ajax({
                function: 'open-ai-get-information'
            }, (response) => {
                let list = [['files', 'Files'], ['website', 'Website URLs'], ['qea', 'Q&A'], ['flows', 'Flows'], ['articles', 'Articles'], ['conversations', 'Conversations']];
                let code = `<h2>${sb_('Sources')}</h2><p>`;
                for (var i = 0; i < list.length; i++) {
                    code += response[list[i][0]] ? `${response[list[i][0]][1]} ${sb_(list[i][1])} (${response[list[i][0]][0]} ${sb_('chars')})<br>` : '';
                }
                code += `</p><h2>${sb_('Total detected characters')}</h2><p>${response.total} ${sb_('chars') + (response.limit ? ' / ' + response.limit + ' ' + sb_('limit') : '')}</p><hr><div id="sb-chatbot-delete-all-training" class="sb-btn sb-btn-white">${sb_('Delete all training data')}</div>`;
                area.html(code);
                area.sbLoading(false);
            });
        });

        $(chatbot_area).on('click', '.sb-menu-chatbot [data-type]', function (e) {
            let type = $(this).data('type');
            switch (type) {
                case 'flows':
                case 'training':
                case 'playground':
                    let area = chatbot_area.find(`> [data-id="${type}"]`);
                    chatbot_area.find('> [data-id]').sbActive(false)
                    area.sbActive(true);
                    if (type == 'flows' && area.sbLoading()) {
                        SBF.ajax({ function: 'open-ai-flows-get' }, (response) => {
                            for (var i = 0; i < response.length; i++) {
                                if (response[i] && response[i].steps && response[i].name) {
                                    SBApps.openAI.flows.flows.push(response[i]);
                                }
                            }
                            let code = '';
                            for (var i = 0; i < response.length; i++) {
                                if (response[i]) {
                                    code += SBApps.openAI.flows.navCode(response[i].name);
                                }
                            }
                            flows_nav.html(code);
                            flows_nav.find('li:first-child').click();
                            area.sbLoading(false);
                        });
                    }
                    break;
                case 'settings':
                    SBSettings.open('dialogflow', true);
                    e.preventDefault;
                    return false;
            }
        });

        $(chatbot_qea_repeater).on('click', '.sb-enlarger-function-calling', function () {
            $(this).parent().parent().find('.sb-qea-repeater-answer').addClass('sb-hide');
        });

        $(chatbot_qea_repeater).on('change', '[data-id="open-ai-faq-set-data"] select', function () {
            $(this).parent().next().find('input').setClass('sb-hide', ['transcript', 'transcript_email', 'human_takeover', 'archive_conversation'].includes($(this).val()));
        });

        $(chatbot_qea_repeater).on('input click', '[data-id="open-ai-faq-answer"]', function () {
            $(this).prev().find('i').sbActive($(this).val().length > 2 && $(this).val().indexOf(' '));
        });

        $(chatbot_qea_repeater).on('click', '.sb-qea-repeater-answer > label > i', function () {
            let textarea = $(this).closest('.repeater-item').find('[data-id="open-ai-faq-answer"]');
            if (loading(this)) return;
            SBApps.openAI.rewrite(textarea.val(), (response) => {
                $(this).sbLoading(false);
                if (response[0]) {
                    textarea.val(response[1]);
                }
            });
        });

        $(chatbot_playground_editor).on('click', '[data-value="add"], [data-value="send"]', function () {
            let textarea = chatbot_playground_editor.find('textarea');
            let message = textarea.val().trim();
            textarea.val('');
            if (message) {
                SBApps.openAI.playground.addMessage(message, chatbot_playground_editor.find('[data-value="user"], [data-value="assistant"]').attr('data-value'));
            }
            if ($(this).data('value') == 'send') {
                let length = SBApps.openAI.playground.messages.length;
                if (length && !loading(this)) {
                    SBF.ajax({
                        function: 'open-ai-playground-message',
                        messages: SBApps.openAI.playground.messages
                    }, (response) => {
                        if (response[0]) {
                            if (response[1]) {
                                SBApps.openAI.playground.addMessage(response[1], 'assistant', response[6]);
                                if (response[4]) {
                                    let code = '';
                                    for (var key in response[4].usage) {
                                        if (['string', 'number'].includes(typeof response[4].usage[key])) {
                                            code += `<b>${SBF.slugToString(key)}</b>: ${response[4].usage[key]}<br>`;
                                        }
                                    }
                                    SBApps.openAI.playground.last_response = response[4];
                                    chatbot_area.find('.sb-playground-info').html(code + `<div id="sb-playground-query" class="sb-btn-text">${sb_('View code')}</div>${response[4].embeddings ? `<div id="sb-playground-embeddings" class="sb-btn-text">${sb_('Embeddings')}</div>` : ``}`);
                                    if (response[4].payload) {
                                        SBApps.openAI.playground.messages[length - 1].push(response[4].payload);
                                    }
                                }
                            }
                            if (response[8]) {
                                conversations_admin_list_ul.find(`[data-conversation-id="${response[8]}"]`).remove();
                            }
                        } else {
                            infoPanel(response);
                            console.error(response);
                        }
                        $(this).sbLoading(false);
                    });
                }
            }
        });

        $(chatbot_area).on('click', '#sb-playground-query', function () {
            infoPanel('<pre>' + JSON.stringify(SBApps.openAI.playground.last_response.query, null, 4).replaceAll('\\"', '"') + '</pre>', 'info', false, 'sb-playground-query-panel', false, true);
        });

        $(chatbot_area).on('click', '#sb-playground-embeddings', function () {
            let code = '';
            let embeddings = SBApps.openAI.playground.last_response.embeddings;
            for (var i = embeddings.length - 1; i > -1; i--) {
                code += `<span><b>${sb_('Source')}</b>: ${embeddings[i].source ? embeddings[i].source.autoLink({ target: '_blank' }) : ''}<br><b>${sb_('Score')}</b>: ${embeddings[i].score}<br><span>${embeddings[i].text}</span></span>`;
            }
            infoPanel(code, 'info', false, 'sb-playground-embeddings-panel', false, true);
        });

        $(chatbot_playground_editor).on('click', '[data-value="clear"]', function () {
            SBApps.openAI.playground.messages = [];
            chatbot_playground_area.html('');
            chatbot_area.find('.sb-playground-info').html('');
        });

        $(chatbot_playground_area).on('click', '.sb-icon-close', function () {
            let element = $(this).closest('[data-type]');
            SBApps.openAI.playground.messages.splice(element.index(), 1);
            element.remove();
        });

        $(chatbot_playground_area).on('click', '.sb-rich-chips .sb-btn', function () {
            chatbot_playground_editor.find('textarea').val($(this).html());
            chatbot_playground_editor.find('[data-value="send"]').click();
        });

        $(chatbot_playground_editor).on('click', '[data-value="user"], [data-value="assistant"]', function () {
            let is_user = $(this).attr('data-value') == 'user';
            $(this).attr('data-value', is_user ? 'assistant' : 'user').html('<i class="sb-icon-reload"></i> ' + sb_(is_user ? 'Assistant' : 'User'));
        });

        $(admin).on('click', '#open-ai-troubleshoot a, #google-troubleshoot a', function (e) {
            let id = $(this).parent().attr('id');
            e.preventDefault();
            if (id != 'google-troubleshoot' && ![true, 'mode'].includes(SBApps.openAI.troubleshoot())) {
                return false;
            }
            if (loading(this)) return;
            SBF.ajax({
                function: $(this).parent().attr('id')
            }, (response) => {
                if (response === true) {
                    infoBottom('Success. No issues found.');
                } else {
                    infoPanel(response);
                }
                $(this).sbLoading(false);
                $(conversations_area).find('.sb-admin-list .sb-select li.sb-active').click();
            });
            return false;
        });

        /*
        * ----------------------------------------------------------
        * Articles area
        * ----------------------------------------------------------
        */

        $(articles_area).on('click', '.ce-settings__button--delete.ce-settings__button--confirm', function () {
            let url = articles_area.find('.image-tool--filled img').attr('src');
            if (url) {
                SBF.ajax({ function: 'delete-file', path: url });
            }
        });

        $(articles_area).on('click', '.ul-articles li', function (e) {
            infoPanel('The changes will be lost.', 'alert', () => {
                SBArticles.show($(this).attr('data-id'));
                articles_area.find('.sb-scroll-area:not(.sb-nav)').scrollTop(0);
            }, false, false, false, !articles_save_required, () => {
                $(this).parent().find('li').sbActive(false);
                $(this).parent().find(`[data-id="${SBArticles.activeID()}"]`).sbActive(true);
            });
        });

        $(articles_area).on('click', '.ul-categories li', function (e) {
            SBArticles.categories.show($(this).attr('data-id'));
        });

        $(articles_area).on('click', '.sb-add-article', function () {
            SBArticles.add();
        });

        $(articles_area).on('click', '.sb-add-category', function () {
            SBArticles.categories.add();
        });

        $(articles_area).on('click', '.sb-nav i', function (e) {
            let parent = $(this).parent();
            let nav = parent.closest('ul');
            let is_category = nav.hasClass('ul-categories');
            infoPanel(`The ${is_category ? 'category' : 'article'} will be deleted permanently.`, 'alert', () => {
                let id = parent.attr('data-id');
                if (is_category) {
                    SBArticles.categories.delete(id);
                } else {
                    articles_area.find('#editorjs .image-tool__image-picture').each(function () {
                        SBF.ajax({ function: 'delete-file', path: $(this).attr('src') });
                    });
                    if (!id) {
                        return parent.remove();
                    }
                    loading(articles_content);
                    SBArticles.delete(id, (response) => {
                        articles_content.sbLoading(false);
                        editorJSDestroy();
                        if (nav.find('li').length > 1) {
                            setTimeout(() => {
                                if (parent.prev().length) {
                                    parent.prev().click();
                                } else {
                                    parent.next().click();
                                }
                                parent.remove();
                            }, 300);
                        }
                    });
                }
            });
            e.preventDefault();
            return false;
        });

        $(articles_area).on('click', '.sb-menu-wide li', function () {
            let type = $(this).data('type');
            if (type == 'settings') {
                SBSettings.open('articles', true);
            } else if (type == 'reports') {
                SBReports.open('articles-searches');
            } else {
                articles_area.attr('data-type', type);
                SBArticles.categories.update();
            }
        });

        $(articles_area).on('click', '.sb-save-articles', function () {
            if (loading(this)) return;
            if (articles_area.attr('data-type') == 'categories') {
                SBArticles.categories.save((response) => {
                    $(this).sbLoading(false);
                });
            } else {
                SBArticles.save((response) => {
                    $(this).sbLoading(false);
                });
            }
        });

        $(articles_area).on('change input', 'input, textarea, select', function () {
            articles_save_required = true;
        });

        $(articles_category_select).on('change', function () {
            if (!articles_category_parent_select.val()) {
                infoBottom('Select a parent category first.', 'error');
                $(this).val('');
            }
        });

        /*
        * ----------------------------------------------------------
        * Reports area
        * ----------------------------------------------------------
        */

        $(reports_area).on('click', '.sb-nav [id]', function () {
            let id = $(this).attr('id');
            SBReports.active_report = false;
            reports_area.find('#sb-date-picker').val('');
            reports_area.attr('class', 'sb-area-reports sb-active sb-report-' + id);
            SBReports.initReport($(this).attr('id'));
            if (SBF.getURL('report') != id) {
                pushState('?report=' + id);
            }
        });

        $(reports_area).on('change', '#sb-date-picker', function () {
            SBReports.initReport(false, $(this).val());
        });

        $(reports_area).on('click', '.sb-report-export', function () {
            if ($(this).sbLoading()) return;
            SBReports.export((response) => {
                $(this).sbLoading(false);
                if (response) {
                    dialogDeleteFile(response, 'sb-export-report-close', 'Report exported')
                }
            });
        });

        if (SBF.getURL('report')) {
            if (!reports_area.sbActive()) {
                header.find('.sb-admin-nav #sb-reports').click();
            }
            setTimeout(() => {
                reports_area.find('#' + SBF.getURL('report')).click()
            }, 500);
        }

        /*
        * ----------------------------------------------------------
        * Woocommerce
        * ----------------------------------------------------------
        */

        // Panel reload button
        $(conversations_area).on('click', '.sb-panel-woocommerce > i', function () {
            SBApps.woocommerce.conversationPanel();
        });

        // Get order details
        $(conversations_area).on('click', '.sb-woocommerce-orders > div > span', function (e) {
            let parent = $(this).parent();
            if (!$(e.target).is('span')) return;
            if (!parent.sbActive()) {
                SBApps.woocommerce.conversationPanelOrder(parent.attr('data-id'));
            }
        });

        // Products popup 
        $(conversations_area).on('click', '.sb-btn-woocommerce', function () {
            if (woocommerce_products_box_ul.sbLoading() || (activeUser() != false && activeUser().language != SBApps.itemsPanel.panel_language)) {
                SBApps.itemsPanel.populate('woocommerce');
            }
            woocommerce_products_box.find('.sb-search-btn').sbActive(true).find('input').get(0).focus();
            woocommerce_products_box.sbTogglePopup(this);
        });

        // Products popup pagination
        $(woocommerce_products_box).find('.sb-woocommerce-products-list').on('scroll', function () {
            if (scrollPagination(this, true)) {
                SBApps.itemsPanel.pagination(this, 'woocommerce');
            }
        });

        // Products popup filter
        $(woocommerce_products_box).on('click', '.sb-select li', function () {
            SBApps.itemsPanel.filter(this, 'woocommerce');
        });

        // Products popup search
        $(woocommerce_products_box).on('input', '.sb-search-btn input', function () {
            SBApps.itemsPanel.search(this, 'woocommerce');
        });

        $(woocommerce_products_box).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBApps.itemsPanel.search($(this).next(), 'woocommerce') });
        });

        // Cart popup insert product
        $(woocommerce_products_box).on('click', '.sb-woocommerce-products-list li', function () {
            let action = woocommerce_products_box.attr('data-action');
            let id = $(this).data('id');
            if (SBF.null(action)) {
                SBChat.insertText(`{product_card id="${id}"}`);
            } else {
                woocommerce_products_box_ul.sbLoading(true);
                conversations_area.find('.sb-add-cart-btn').sbLoading(true);
                SBChat.sendMessage(-1, '', [], (response) => {
                    if (response) {
                        SBApps.woocommerce.conversationPanelUpdate(id);
                        admin.sbHideLightbox();
                    }
                }, { 'event': 'woocommerce-update-cart', 'action': 'cart-add', 'id': id });
            }
            SBF.deactivateAll();
            admin.removeClass('sb-popup-active');
        });

        // Cart add product
        $(conversations_area).on('click', '.sb-panel-woocommerce .sb-add-cart-btn', function () {
            if ($(this).sbLoading()) return;
            if (SBChat.user_online) {
                SBApps.itemsPanel.populate('woocommerce');
                woocommerce_products_box.sbShowLightbox(true, 'cart-add');
            } else {
                infoPanel('The user is offline. Only the carts of online users can be updated.');
            }
        });

        // Cart remove product
        $(conversations_area).on('click', '.sb-panel-woocommerce .sb-list-items > a > i', function (e) {
            let id = $(this).parent().attr('data-id');
            SBChat.sendMessage(-1, '', [], () => {
                SBApps.woocommerce.conversationPanelUpdate(id, 'removed');
            }, { 'event': 'woocommerce-update-cart', 'action': 'cart-remove', 'id': id });
            $(this).sbLoading(true);
            e.preventDefault();
            return false;
        });

        /*
        * ----------------------------------------------------------
        * Apps functions
        * ----------------------------------------------------------
        */

        // Ump
        $(conversations_area).on('click', '.sb-panel-ump > i', function () {
            SBApps.ump.conversationPanel();
        });

        // ARMember
        $(conversations_area).on('click', '.sb-panel-armember > i', function () {
            SBApps.armember.conversationPanel();
        });

        // OpenCart
        $(conversations_area).on('click', '.sb-panel-opencart > i', function () {
            SBApps.opencart.conversationPanel();
        });

        $(conversations_area).on('click', '.sb-opencart-orders > a', function () {
            SBApps.opencart.openOrder($(this).attr('data-id'));
        });

        $(settings_area).on('click', '#opencart-sync a', function (e) {
            e.preventDefault();
            if (loading(this)) return;
            SBF.ajax({
                function: 'opencart-sync'
            }, (response) => {
                $(this).sbLoading(false);
                infoPanel(response === true ? 'Users successfully imported.' : response);
            });
        });

        // Perfex, whmcs, aecommerce
        $(settings_area).on('click', '#perfex-sync a, #whmcs-sync a, #perfex-articles-sync a, #whmcs-articles-sync a, #aecommerce-sync a, #aecommerce-sync-admins a, #aecommerce-sync-sellers a, #martfury-sync a, #martfury-sync-sellers a', function (e) {
            if (loading(this)) return;
            let function_name = $(this).closest('[id]').attr('id');
            let articles = function_name.indexOf('article') > 0;
            SBF.ajax({
                function: function_name
            }, (response) => {
                if (response === true) {
                    if (!articles) {
                        SBUsers.update();
                    }
                    infoPanel(articles ? 'Articles successfully imported.' : 'Users successfully imported.');
                } else {
                    infoPanel('Error. Response: ' + JSON.stringify(response));
                }
                $(this).sbLoading(false);
            });
            e.preventDefault();
        });

        // Zendesk
        $(conversations_area).on('click', '#sb-zendesk-btn', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'zendesk-create-ticket',
                conversation_id: SBChat.conversation.id
            }, (response) => {
                if (response === true) {
                    SBApps.zendesk.conversationPanel();
                } else {
                    infoPanel('Error. Response: ' + JSON.stringify(response));
                }
                $(this).sbLoading(false);
            });
            e.preventDefault();
        });

        $(conversations_area).on('click', '#sb-zendesk-update-ticket', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'zendesk-update-ticket',
                conversation_id: SBChat.conversation.id,
                zendesk_ticket_id: $(this).closest('[data-id]').attr('data-id')
            }, () => {
                $(this).sbLoading(false);
            });
            e.preventDefault();
            return false;
        });

        /*
        * ----------------------------------------------------------
        * Miscellaneous
        * ----------------------------------------------------------
        */

        $(admin).on('click', '.sb-enlarger', function () {
            $(this).sbActive(true);
        });

        $(admin).on('mouseenter', '[data-sb-tooltip-init]', function () {
            $(this).parent().sbInitTooltips();
            $(this).removeAttr('data-sb-tooltip-init');
            $(this).trigger('mouseenter');
        });

        // Language switcher
        $(admin).on('click', '.sb-language-switcher > i', function () {
            let switcher = $(this).parent();
            let active_languages = switcher.find('[data-language]').map(function () { return $(this).attr('data-language') }).get();
            let code = '';
            active_languages.push('en');
            for (var key in SB_LANGUAGE_CODES) {
                if (!active_languages.includes(key)) {
                    code += `<div data-language="${key}"><img src="${SB_URL}/media/flags/${key}.png" />${sb_(SB_LANGUAGE_CODES[key])}</div>`;
                }
            }
            language_switcher_target = switcher;
            SBAdmin.genericPanel('languages', 'Choose a language', code, [], ' data-source="' + switcher.attr('data-source') + '"', true);
        });

        $(admin).on('click', '.sb-language-switcher img', function () {
            let item = $(this).parent();
            let active = item.sbActive();
            let language = active ? false : item.attr('data-language');
            switch (item.parent().attr('data-source')) {
                case 'article-categories':
                    SBArticles.categories.show(SBArticles.categories.activeID(), language);
                    break;
                case 'articles':
                    let previous_active = articles_content.find('.sb-language-switcher .sb-active');
                    infoPanel('The changes will be lost.', 'alert', () => {
                        let id = item.attr('data-id');
                        if (!id && !active) {
                            SBArticles.clear();
                        } else {
                            SBArticles.show(id && !active ? id : SBArticles.activeID(true));
                        }
                    }, false, false, false, !articles_save_required, () => {
                        item.sbActive(false);
                        previous_active.sbActive(true);
                    });
                    break;
                case 'automations':
                    SBSettings.automations.show(false, language);
                    break;
                case 'settings':
                    let active_language = item.parent().find('[data-language].sb-active');
                    SBSettings.translations.save(item, active ? item.attr('data-language') : (active_language.length ? active_language.attr('data-language') : false));
                    SBSettings.translations.activate(item, language);
                    break;
            }
            item.siblings().sbActive(false);
            item.sbActive(!active);
        });

        $(admin).on('click', '.sb-language-switcher span > i', function () {
            let item = $(this).parent();
            let language = item.attr('data-language');
            infoPanel(sb_('The {T} translation will be deleted.').replace('{T}', sb_(SB_LANGUAGE_CODES[language])), 'alert', () => {
                switch (item.parent().attr('data-source')) {
                    case 'article-categories':
                        SBArticles.categories.translations.delete(language);
                        break;
                    case 'articles':
                        SBArticles.translations.delete(language);
                        break;
                    case 'automations':
                        SBSettings.automations.deleteTranslation(false, language);
                        SBSettings.automations.show();
                        break;
                    case 'settings':
                        SBSettings.translations.delete(item, language);
                        break;
                }
                item.remove();
            });
        });

        $(admin).on('click', '.sb-languages-box [data-language]', function () {
            let box = $(this).parents().eq(1);
            let language = $(this).attr('data-language');
            let hide = true;
            switch (box.attr('data-source')) {
                case 'article-categories':
                    SBArticles.categories.translations.add(language);
                    break;
                case 'articles':
                    infoPanel('The changes will be lost.', 'alert', () => {
                        SBArticles.translations.add(language);
                        admin.sbHideLightbox();
                    }, false, false, false, !articles_save_required);
                    hide = false;
                    break;
                case 'automations':
                    SBSettings.automations.addTranslation(false, false, language);
                    SBSettings.automations.show(false, language);
                    break;
                case 'settings':
                    SBSettings.translations.add(language);
                    break;
            }
            if (hide) {
                admin.sbHideLightbox();
            }
        });

        // Lightbox
        $(admin).on('click', '.sb-lightbox .sb-top-bar .sb-close', function () {
            admin.sbHideLightbox();
        });

        $(admin).on('click', '.sb-lightbox .sb-info', function () {
            $(this).sbActive(false);
        });

        // Alert and information box
        $(admin).on('click', '.sb-dialog-box a', function () {
            let lightbox = $(this).closest('.sb-lightbox');
            if ($(this).hasClass('sb-confirm')) {
                alertOnConfirmation();
            }
            if ($(this).hasClass('sb-cancel') && alertOnCancel) {
                alertOnCancel();
            }
            if (admin.find('.sb-lightbox.sb-active').length == 1) {
                overlay.sbActive(false);
            }
            SBAdmin.open_popup = false;
            lightbox.sbActive(false);
        });

        $(admin).on('click', '.sb-menu-wide li, .sb-nav li', function () {
            $(this).siblings().sbActive(false);
            $(this).sbActive(true);
        });

        $(admin).on('click', '.sb-nav:not(.sb-nav-only) li:not(.sb-tab-nav-title)', function () {
            let area = $(this).closest('.sb-tab');
            let tab = $(area).find(' > .sb-content > div').sbActive(false).eq($(this).parent().find('li:not(.sb-tab-nav-title)').index(this));
            tab.sbActive(true);
            tab.find('textarea').each(function () {
                $(this).autoExpandTextarea();
                $(this).manualExpandTextarea();
            });
            area.find('.sb-scroll-area:not(.sb-nav)').scrollTop(0);
        });

        $(admin).sbInitTooltips();

        $(admin).on('click', '[data-button="toggle"]', function () {
            let show = admin.find('.' + $(this).data('show'));
            let hide = admin.find('.' + $(this).data('hide'));
            show.addClass('sb-show-animation').show();
            hide.hide();
            SBAdmin.open_popup = show.hasClass('sb-lightbox') || show.hasClass('sb-popup') ? show : false;
        });

        $(admin).on('click', '.sb-info-card', function () {
            $(this).sbActive(false);
        });

        $(upload_input).on('change', function () {
            if (upload_function) {
                upload_function();
                upload_function = false;
            } else {
                $(upload_target).sbLoading($(this).prop('files').length);
                $(this).sbUploadFiles((response) => {
                    $(upload_target).sbLoading(false);
                    response = JSON.parse(response);
                    if (response[0] == 'success') {
                        let type = $(upload_target).closest('[data-type]').data('type');
                        if (type == 'upload-image') {
                            if ($(upload_target).attr('data-value')) {
                                SBF.ajax({ function: 'delete-file', path: $(upload_target).attr('data-value') });
                            }
                            $(upload_target).attr('data-value', response[1]).css('background-image', `url("${response[1]}")`);
                        }
                        if (upload_on_success) {
                            upload_on_success(response[1]);
                        }
                    } else {
                        console.log(response[1]);
                    }
                });
            }
        });

        $(admin).on('click', '.sb-accordion > div > span', function (e) {
            let parent = $(this).parent();
            let active = $(parent).sbActive();
            if (!$(e.target).is('span')) return;
            parent.siblings().sbActive(false);
            parent.sbActive(!active);
        });

        $(admin).on('mousedown', function (e) {
            if ($(SBAdmin.open_popup).length) {
                let popup = $(SBAdmin.open_popup);
                if (!popup.is(e.target) && popup.has(e.target).length === 0 && !['sb-btn-saved-replies', 'sb-btn-emoji', 'sb-btn-woocommerce', 'sb-btn-shopify', 'sb-btn-open-ai'].includes($(e.target).attr('class'))) {
                    if (popup.hasClass('sb-popup')) {
                        popup.sbTogglePopup();
                    } else if (popup.hasClass('sb-select')) {
                        popup.find('ul').sbActive(false);
                    } else if (popup.hasClass('sb-menu-mobile')) {
                        popup.find('i').sbActive(false);
                    } else if (popup.hasClass('sb-menu')) {
                        popup.sbActive(false);
                    } else if (!SBAdmin.open_popup || !['sb-embeddings-box'].includes(SBAdmin.open_popup.attr('id'))) {
                        admin.sbHideLightbox();
                    }
                    SBAdmin.open_popup = false;
                }
            }
        });
     });