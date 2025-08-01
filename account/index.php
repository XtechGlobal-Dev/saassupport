<?php

use Componere\Value;

global $cloud_settings;
require_once('functions.php');
sb_cloud_load();
$account = account();
$cloud_settings = super_get_settings();
$rtl = defined('SB_CLOUD_DEFAULT_RTL') || sb_is_rtl();
$custom_code = db_get('SELECT value FROM settings WHERE name = "custom-code-admin"');
if (!function_exists('sup' . 'er_adm' . 'in_con' . 'fig')) {
    die();
}
if (isset($_GET['login_email'])) {
    $account = false;
}
if (sb_isset($_GET, 'payment_type') == 'credits' && PAYMENT_PROVIDER == 'stripe') {
    $required_action = super_get_user_data('stripe_next_action', $account['user_id']);
    if ($required_action) {
        $required_action = explode('|', $required_action);
        if ($required_action[0] > (time() - 86400)) {
            super_delete_user_data($account['user_id'], 'stripe_next_action');
            header('Location: ' . $required_action[1]);
            die();
        }
    }
}
?>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <title>
        <?php echo SB_CLOUD_BRAND_NAME ?>
    </title>
    <script src="https://kit.fontawesome.com/b472bd70ee.js" crossorigin="anonymous"></script>
    <script src="../script/js/min/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@iconify-json/hugeicons@1.2.6/index.min.js"></script>
    <script id="sbinit"
        src="../script/js/<?php echo sb_is_debug() ? 'main' : 'min/main.min' ?>.js?v=<?php echo SB_VERSION ?>"></script>
    <link rel="stylesheet" href="../script/css/admin.css?v=<?php echo SB_VERSION ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="../script/css/responsive-admin.css?v=<?php echo SB_VERSION ?>"
        media="(max-width: 464px)" />
    <!-- Manrope font cdn link  -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Inter:wght@500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />
    <!-- Bootstrap cdn link  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/hgi-stroke-rounded.css?v=<?php echo SB_VERSION ?>" type="text/css" media="all" />
    <?php
    if ($rtl) {
        echo '<link rel="stylesheet" href="../script/css/rtl-admin.css?v=' . SB_VERSION . '" />';
    }
    ?>
    <link rel="stylesheet" href="css/skin.min.css?v=<?php echo SB_VERSION ?>" type="text/css" media="all" />
    <link rel="shortcut icon" href="<?php echo SB_CLOUD_BRAND_ICON ?>" />
    <link rel="apple-touch-icon" href="<?php echo SB_CLOUD_BRAND_ICON_PNG ?>" />
    <link rel="manifest" href="<?php echo SB_CLOUD_MANIFEST_URL ?>" />
    <?php account_js() ?>
</head>

<body class="on-load<?php echo $rtl ? ' sb-rtl' : '' ?>">
    <div id="preloader"></div>
    <?php
    cloud_custom_code();
    if ($account && !empty($_COOKIE['sb-cloud'])) {
        if (empty($account['owner']) && db_get('SELECT id FROM agents WHERE admin_id = ' . db_escape($account['user_id'], true) . ' AND email = "' . $account['email'] . '"')) { // Deprecated. Remove && db_get('....
            echo '<script>document.location = "' . CLOUD_URL . '"</script>';
        } else {
            box_account();
        }
    } else {
        $GLOBALS['SB_LANGUAGE'] = [sb_defined('SB_CLOUD_DEFAULT_LANGUAGE_CODE'), 'front'];
        box_registration_login();
    }
    ?>
    <footer>
        <script src="js/cloud<?php echo (sb_is_debug() ? '' : '.min') ?>.js?v=<?php echo SB_VERSION ?>"></script>
        <?php sb_cloud_css_js() ?>
    </footer>
</body>

</html>

<?php function box_account()
{
    global $cloud_settings;
    $membership = membership_get_active(false);
    $expiration = DateTime::createFromFormat('d-m-y', $membership['expiration']);
    $expired = $membership['price'] != 0 && (!$expiration || time() > $expiration->getTimestamp());
    $shopify = defined('SHOPIFY_CLIENT_ID') ? super_get_user_data('shopify_shop', get_active_account_id()) : false;
    echo '<script>var messages_volume = [' . implode(',', membership_volume()) . ']; var membership = { quota: ' . $membership['quota'] . ', count: ' . $membership['count'] . ', expired: ' . ($expired ? 'true' : 'false') . (isset($membership['quota_agents']) ? (', quota_agents: ' . $membership['quota_agents'] . ', count_agents: ' . $membership['count_agents']) : '') . ', credits: ' . $membership['credits'] . ' }; var CLOUD_USER_ID = ' . account()['user_id'] . '; var CLOUD_CURRENCY = "' . strtoupper(membership_currency()) . '"; var TWILIO_SMS = ' . (defined('CLOUD_TWILIO_SID') && !empty(CLOUD_TWILIO_SID) ? 'true' : 'false') . '; var external_integration = "' . ($shopify ? 'shopify' : '') . '";</script>' . PHP_EOL; ?>
    <div class="sb-account-box sb-admin sb-loading">
        <div class="sb-top-bar py-4">
            <div>
                <h2>
                    <img src="<?php echo SB_CLOUD_BRAND_ICON ?>" />
                    <?php sb_e('Account') ?>
                </h2>
            </div>
            <div>
                <a class="sb-btn sb-btn-dashboard" href="<?php echo CLOUD_URL ?>">
                    <i class="fa-solid fa-gauge mr-2" aria-hidden="true"></i> <?php sb_e('Go to Dashboard') ?>
                </a>
            </div>
        </div>
        <div class="sb-tab">
            <div class="sb-nav">
                <div>
                    <?php sb_e('Installation') ?>
                </div>
                <ul class="ul_account">
                    <li id="nav-installation" class="sb-active">
                        <?php sb_e('Installation') ?>
                    </li>
                    <li id="nav-membership">
                        <?php sb_e('Membership') ?>
                    </li>
                    <li id="nav-invoices">
                        <?php sb_e(PAYMENT_PROVIDER == 'stripe' ? 'Invoices' : 'Payments') ?>
                    </li>
                    <li id="nav-profile">
                        <?php sb_e('Profile') ?>
                    </li>
                    <?php
                    if (!empty($cloud_settings['referral-commission'])) {
                        echo '<li id="nav-referral">' . sb_('Refer a friend') . '</li>';
                    }
                    ?>
                    <li id="nav-logout">
                        <?php sb_e('Logout') ?>
                    </li>
                </ul>
                <?php
                if (defined('SB_CLOUD_DOCS')) {
                    echo '<a href=" ' . SB_CLOUD_DOCS . '" target="_blank" class="sb-docs sb-btn-text"><i class="sb-icon-help"></i> ' . sb_('Help') . '</a>';
                }
                ?>
            </div>
            <div class="sb-content sb-scroll-area">
                <div id="tab-installation" class="sb-active">
                    <h2 class="addons-title first-title">
                        <?php sb_e($shopify ? 'Installation' : 'Embed code') ?>
                    </h2>
                    <?php
                    if ($shopify) {
                        echo '<p>' . str_replace('{R}', SB_CLOUD_BRAND_NAME, sb_('Customize your store and enable {R} in the app embeds section.')) . '</p><a class="sb-btn sb-btn-white" href="https://' . $shopify . '/admin/themes/current/editor?context=apps&activateAppId=' . SHOPIFY_APP_ID . '/sb" target="_blank">' . sb_('Preview in theme') . '</a>';
                    } else {
                        echo '<p>' . htmlspecialchars(sb_(sb_isset($cloud_settings, 'text_embed_code', 'To add the chat to your website, paste this code before the closing </body> tag on each page. Then, reload your website to see the chat in the bottom-right corner. Click the dashboard button in the top-right to access the admin area.'))) . '</p><div class="sb-setting"><textarea id="embed-code" readonly></textarea><button id="copy-btn" class="copy-button sb-btn ml-2" type="button">Copy</button><span id="copy-tooltip" class="tooltipcode">Copied!</span></div>';
                    }
                    if (defined('DIRECT_CHAT_URL')) {
                        $link = DIRECT_CHAT_URL . '/' . account_chat_id(account()['user_id']);
                        echo '<h2 class="addons-title">' . sb_('Chat direct link') . '</h2><p>' . sb_('Use this unique URL to access the chat widget directly. Include the attribute ?ticket in the URL to view the tickets area.') . '</p><div class="sb-setting sb-direct-link"><input onclick="window.open(\'' . $link . '\')" value="' . $link . '" readonly /></div>';
                    }
                    if (defined('ARTICLES_URL')) {
                        $link = ARTICLES_URL . '/' . account_chat_id(account()['user_id']);
                        echo '<h2 class="addons-title">' . sb_('Articles link') . '</h2><p>' . sb_('Use this unique URL to access the articles page. See the docs for other display options.') . '</p><div class="sb-setting sb-direct-link"><input onclick="window.open(\'' . $link . '\')" value="' . $link . '" readonly /></div>';
                    }
                    ?>
                    <div class="api_token" style="display:none">
                        <h2 class="addons-title">
                            <?php sb_e('API token') ?>
                        </h2>
                        <p>
                            <?php echo str_replace('{R}', SB_CLOUD_BRAND_NAME, sb_('The API token is a required for using the {R} WEB API.')) ?>
                        </p>
                        <div class="sb-setting">
                            <input value="<?php echo account()['token'] ?>" readonly name="token" id="token-input" />
                            <button id="copy-token-btn" class="copy-button sb-btn ml-2" type="button">Copy</button>
                            <span id="token-tooltip" class="token-tooltip">Copied!</span>
                        </div>
                    </div>
                </div>
                <div id="tab-membership">
                    <h2 class="addons-title first-title">
                        <?php sb_e('Membership') ?>
                    </h2>
                    <?php box_membership($membership) ?>
                    <hr />
                    <?php box_membership_plans($membership['id'], $expired) ?>
                    <?php box_credits(!$shopify) ?>
                    <?php box_addons() ?>
                    <?php box_chart() ?>
                    <hr />
                    <hr />
                    <?php
                    button_cancel_membership($membership);
                    ?>
                </div>
                <div id="tab-invoices" class="sb-loading">
                    <h2 class="addons-title first-title">
                        <?php sb_e(PAYMENT_PROVIDER == 'stripe' ? 'Invoices' : 'Payments') ?>
                    </h2>
                    <p>
                        <?php sb_e(PAYMENT_PROVIDER == 'stripe' ? 'Download your invoices here.' : 'View your payments here.') ?>
                    </p>
                    <table class="sb-table">
                        <tbody></tbody>
                    </table>
                </div>
                <div id="tab-profile">
                    <h2 class="addons-title first-title">
                        <?php sb_e('Manage profile') ?>
                    </h2>
                    <p>
                        <?php sb_e('Update here your profile information.') ?>
                    </p>
                    <div id="first_name" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e('First name') ?>
                        </span>
                        <input type="text" placeholder="First name" />
                    </div>
                    <div id="last_name" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e('Last name') ?>
                        </span>
                        <input type="text" placeholder="Last name" />
                    </div>
                    <div id="email" data-type="text" class="sb-input sb-type-input-button">
                        <span>
                            <?php sb_e('Email') ?>
                        </span>
                        <input type="email" readonly placeholder="Email" />
                        <a class="sb-btn btn-verify-email">
                            <?php sb_e('Verify Email') ?>
                        </a>
                    </div>
                    <div id="phone" data-type="text" class="sb-input sb-type-input-button">
                        <span>
                            <?php sb_e('Phone') ?>
                        </span>
                        <input type="tel" placeholder="Phone number" />
                        <a class="sb-btn btn-verify-phone">
                            <?php sb_e('Verify') ?>
                        </a>
                    </div>
                    <div id="password" data-type="text" class="sb-input sb-type-input-button">
                        <span>
                            <?php sb_e('Password') ?>
                        </span>
                        <input type="password" value="12345678" placeholder="Password" />
                    </div>
                    <div id="company_details" data-type="text" class="sb-input">
                        <span>
                            <?php sb_e('Company details') ?>
                        </span>
                        <input type="text" placeholder="Company or organization name" />
                    </div>
                    <hr />
                    <div class="sb-flex">
                        <a id="save-profile" class="sb-btn sb-btn-white sb-icon">
                            <i class="sb-icon-check"></i>
                            <?php sb_e('Save changes') ?>
                        </a>
                        <a id="delete-account" class="sb-btn sb-btn-white sb-icon">
                            <i class="sb-icon-delete"></i>
                            <?php sb_e('Delete account') ?>
                        </a>
                    </div>

                </div>
                <?php box_referral() ?>
            </div>
        </div>
    </div>

<?php } ?>

<?php

function box_membership($membership)
{
    $membership_type = sb_defined('SB_CLOUD_MEMBERSHIP_TYPE', 'messages');
    $membership_type_ma = $membership_type == 'messages-agents';
    $name = $membership_type_ma ? 'messages' : $membership_type;
    $box_two = $membership_type_ma ? '<div class="detail-box"><span>' . sb_('Agents') . '</span><span>' . $membership['count_agents'] . ' / <span class="membership-quota">' . ($membership['quota_agents'] == 9999 ? '∞' : $membership['quota_agents']) . '</span></span> 
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: ' . ($membership["quota_agents"] > 0 ? ($membership["count_agents"] / $membership["quota_agents"]) * 100 : 0) . '%;" aria-valuenow=' . $membership['count_agents'] . ' aria-valuemin=' . $membership['count_agents'] . ' aria-valuemax=' . $membership['quota_agents'] . '></div>
        </div></div>' : '';
    $price_string = $membership['price'] == 0 ? '' : (substr($membership['expiration'], -2) == '37' ? '<span id="membership-appsumo" data-id="' . account_get_payment_id() . '"></span>' : (mb_strtoupper($membership['currency']) . ' ' . $membership['price'] . ' ' . membership_get_period_string($membership['period'])));
    echo '
    <div class="box-maso box-membership">
        <div class="box-black">
            <h2>' . sb_(date('F')) . ', ' . date('Y') . '</h2>
            <div class="membership-detail-list">
                <div class="detail-box">
                    <span>' . sb_($name) . '</span>
                    <span>' . $membership['count'] . ' / <span class="membership-quota">' . $membership['quota'] . '</span></span>
                    <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: ' . ($membership['quota'] > 0 ? ($membership['count'] / $membership['quota']) * 100 : 0) . '%;" aria-valuenow=' . $membership['count'] . ' aria-valuemin=' . $membership['count'] . ' aria-valuemax=' . $membership['quota'] . '></div>
        </div>
                </div>' . $box_two .
        '</div>
        </div>
        <div class="box-black">
            <h2>Current Status</h2>
            <div class="membership-detail-list">
                <div class="detail-box">
                    <p>
                        <span>' . sb_('Active Membership') . '</span>
                        <span class="membership-name">' . sb_($membership['name']) . '</span> 
                    </p>
                    <div>
                        <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32.2803 0.805664C49.9626 0.805664 64.2968 15.14 64.2969 32.8223C64.2969 50.5046 49.9626 64.8389 32.2803 64.8389C14.598 64.8388 0.263672 50.5046 0.263672 32.8223C0.263707 15.14 14.598 0.805699 32.2803 0.805664Z" fill="#22C55E"/>
                            <path d="M32.2803 0.805664C49.9626 0.805664 64.2968 15.14 64.2969 32.8223C64.2969 50.5046 49.9626 64.8389 32.2803 64.8389C14.598 64.8388 0.263672 50.5046 0.263672 32.8223C0.263707 15.14 14.598 0.805699 32.2803 0.805664Z" stroke="#E5E7EB"/>
                            <path d="M41.6182 48.8306H22.9414V16.814H41.6182V48.8306Z" stroke="#E5E7EB"/>
                            <g clip-path="url(#clip0_1485_2470)">
                                <path d="M41.2251 26.5441C41.7463 27.0653 41.7463 27.9115 41.2251 28.4326L30.5529 39.1049C30.0318 39.626 29.1855 39.626 28.6644 39.1049L23.3283 33.7687C22.8072 33.2476 22.8072 32.4014 23.3283 31.8803C23.8494 31.3592 24.6957 31.3592 25.2168 31.8803L29.6108 36.27L39.3408 26.5441C39.8619 26.023 40.7082 26.023 41.2293 26.5441H41.2251Z" fill="white"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_1485_2470">
                                    <path d="M22.9414 22.1504H41.6178V43.4948H22.9414V22.1504Z" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                </div>
                <div style="margin-left: 0;">
                    <span class="membership-price" data-currency="' . $membership['currency'] . '">' . $price_string . '</span>
                </div>
            </div>
        </div>
    </div>';
}

function box_membership_plans($active_membership_id, $expired = false)
{
    $plans = memberships();
    $code = '<h2 class="addons-title">Current Plan</h2><div id="plans" class="plans-box">';
    $menu_items = [];
    $membership_type = sb_defined('SB_CLOUD_MEMBERSHIP_TYPE', 'messages');
    $membership_type_ma = $membership_type == 'messages-agents';
    for ($i = 1; $i < count($plans); $i++) {
        $plan = $plans[$i];
        $plan_period = $plan['period'];
        $menu = $plan_period == 'month' ? 'Monthly' : ($plan_period == 'year' ? 'Annually' : 'More');

        $period_top = membership_get_period_string($plan_period);
        $period = $membership_type_ma || $membership_type == 'messages' ? $period_top : '';
        if (!in_array($menu, $menu_items)) {
            array_push($menu_items, $menu);
        }
        $code .= '<div data-menu="' . $menu . '" data-id="' . $plan['id'] . '"' . ($active_membership_id == $plan['id'] ? ' data-active-membership="true"' : '') . ($active_membership_id == $plan['id'] && $expired ? ' data-expired="true"' : '') . '>' . ($active_membership_id == $plan['id'] ? '<div class="active-membership-info">' . sb_('Active Membership') . ($expired ? ' ' . sb_('Expired') : '') . '</div>' : '') . '<h4>' . $plan['name'] . '</h4><h3>' . mb_strtoupper($plan['currency']) . ' ' . $plan['price'] . ' <span>' . $period_top . '</span></h3>
        <ul>
            <li>' . $plan['quota'] . ' ' . sb_($membership_type_ma ? 'messages' : $membership_type) . ' '. $period .' </li>
            <li>' . ($membership_type_ma ? (($plan['quota_agents'] == 9999 ? sb_('unlimited') : $plan['quota_agents']) . ' ' . sb_('agents')) : '') . '</li>
            <li>' . cloud_embeddings_chars_limit($plan) . ' ' . sb_('characters to train the chatbot') . '</li>
        </ul><button type="button" class="label_blue mb-0">Manage Plan</button></div>';
    }
    $code .= '</div>';
    if (count($menu_items) > 1) {
        $menu = ['Monthly', 'Annually', 'More'];
        $code_menu = '<div class="plans-box-menu sb-menu-wide"><div>' . sb_($menu_items[0]) . '</div><ul>';
        for ($i = 0; $i < count($menu); $i++) {
            if (in_array($menu[$i], $menu_items)) {
                $code_menu .= '<li data-type="' . $menu[$i] . '">' . sb_($menu[$i]) . '</li>';
            }
        }
        $code = $code_menu . '</ul></div>' . $code;
    }
    echo $code;
}

function box_credits($auto_recharge = true)
{
    if (!sb_defined('GOOGLE_CLIENT_ID') && !sb_defined('OPEN_AI_KEY') && !sb_defined('WHATSAPP_APP_ID')) {
        return false;
    }
    $prices = [5, 10, 20, 50, 100, 250, 500, 1000, 3000];
    $code_prices = '';
    $currency = strtoupper(membership_currency());
    $exchange_rate = $currency == 'USD' ? 1 : sb_usd_rates($currency);
    for ($i = 0; $i < count($prices); $i++) {
        $prices[$i] = intval($prices[$i] * $exchange_rate);
        $code_prices .= '<option value="' . $prices[$i] . '">' . $currency . ' ' . $prices[$i] . '</option>';
    }
    $user_id = db_escape(account()['user_id'], true);
    $credits = sb_isset(db_get('SELECT credits FROM users WHERE id = ' . $user_id), 'credits', 0);
    $checked = super_get_user_data('auto_recharge', $user_id) ? ' checked' : '';
    echo '<h2 id="credits" class="addons-title">' . sb_('Credits') . '</h2><p>' . str_replace('{R}', '<a href="' . (defined('SB_CLOUD_DOCS') ? SB_CLOUD_DOCS : '') . '#cloud-credits" target="_blank" class="sb-link-text">' . sb_('here') . '</a>', sb_('Credits are required to use some features in automatic sync mode. If you don\'t want to buy credits, switch to manual sync mode and use your own API key. For more details click {R}.')) . '</p><div class="box-maso maso-box-credits"><div class="box-black"><h2>' . sb_('Active credits') . '</h2><div>' . ($credits ? $credits : '0') . '</div></div><div><h2>' . sb_('Add credits') . '</h2><div><div id="add-credits" data-type="text" class="sb-input"><select><option></option>' . $code_prices . '</select></div></div></div>' . (in_array(PAYMENT_PROVIDER, ['stripe', 'yoomoney']) && $auto_recharge ? '<div><h2>' . sb_('Auto recharge') . '</h2><div><div id="credits-recharge" data-type="checkbox" class="sb-input"><input type="checkbox"' . $checked . '></div></div></div>' : '') . '</div>';
}

function box_addons()
{
    $white_label_price = super_get_white_label();
    $addons = sb_defined('CLOUD_ADDONS');
    if ($white_label_price || $addons) {
        $account = account();
        // $code = '<h2 class="addons-title">' . sb_('Add-ons') . '</h2><p>' . sb_('Add-ons are optional features with a fixed subscription cost.') . '</p><div id="addons" class="plans-box">';
        $code = '<h2 class="addons-title">' . sb_('Add-ons') . '</h2><div id="addons" class="plans-box">';
        if ($white_label_price) {
            $code .= '<div class="sb-visible' . (membership_is_white_label($account['user_id']) ? ' sb-plan-active' : '') . '" id="purchase-white-label"><h4>' . sb_('White Label') . '</h4><h3>' . strtoupper(membership_currency()) . ' ' . $white_label_price . ' <span>' . sb_('a year') . '</span></h3><p>' . sb_('Remove our branding and logo from the chat widget.') . '</p></div>';
        }
        if ($addons) {
            for ($i = 0; $i < count($addons); $i++) {
                $addon = $addons[$i];
                $code .= '<div class="sb-visible sb-custom-addon" data-index="' . $i . '" data-id="' . sb_string_slug($addon['title']) . '"><h4>' . sb_($addon['title']) . '</h4><h3>' . strtoupper(membership_currency()) . ' ' . $addon['price'] . '</h3><p>' . sb_($addon['description']) . '</p></div>';
            }
        }
        echo $code . '</div>';
    }
}

function button_cancel_membership($membership)
{
    if ($membership['price'] != 0) {
        if (super_get_user_data('subscription_cancelation', get_active_account_id())) {
            echo '<p>' . sb_('Your membership renewal has been canceled. Your membership is set to expire on') . ' ' . membership_get_active()['expiration'] . '.</p>';
        } else {
            echo '<a id="cancel-subscription" class="sb-btn-text sb-icon sb-btn-red"><i class="sb-icon-close"></i>' . sb_('Cancel subscription') . '</a>';
        }
    }
}

function account_js()
{
    global $cloud_settings;
    $account = account();
    $reset_code = '<script>document.cookie="sb-login=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/;";document.cookie="sb-cloud=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/;";location.reload();</script>';
    if ($account) {
        $path = '../script/config/config_' . $account['token'] . '.php';
        if (file_exists($path)) {
            require_once($path);
        } else {
            die($reset_code);
        }
    } else {
        echo '<script>var SB_URL = "' . CLOUD_URL . '/script"; var SB_CLOUD_SW = true; var SB_DISABLED = true; (function() { SBF.serviceWorker.init(); }())</script>';
    }
    if ($cloud_settings) {
        unset($cloud_settings['js']);
        unset($cloud_settings['js-front']);
        unset($cloud_settings['css']);
        unset($cloud_settings['css-front']);
    }
    if (isset($_GET['appsumo']) && sb_is_agent()) {
        die($reset_code);
    }
    $language = sb_get_admin_language();
    $translations = ($language && $language != 'en' ? file_get_contents(SB_PATH . '/resources/languages/admin/js/' . $language . '.json') : '[]');
    echo '<script>var CLOUD_URL = "' . CLOUD_URL . '"; var BRAND_NAME = "' . SB_CLOUD_BRAND_NAME . '"; var PUSHER_KEY = "' . sb_pusher_get_details()[0] . '"; var LANGUAGE = "' . sb_get_admin_language() . '"; var SETTINGS = ' . ($cloud_settings ? json_encode($cloud_settings, JSON_INVALID_UTF8_IGNORE) : '{}') . '; var SB_TRANSLATIONS = ' . ($translations ? $translations : '[]') . '; var PAYMENT_PROVIDER = "' . PAYMENT_PROVIDER . '"; var MEMBERSHIP_TYPE = "' . sb_defined('SB_CLOUD_MEMBERSHIP_TYPE', 'messages') . '";' . (defined('PAYMENT_MANUAL_LINK') ? 'var PAYMENT_MANUAL_LINK = "' . PAYMENT_MANUAL_LINK . '"' : '') . '</script>';
}

function box_chart()
{
    if (in_array(sb_defined('SB_CLOUD_MEMBERSHIP_TYPE', 'messages'), ['messages', 'messages-agents'])) {
        echo '<div class="chart-box"><div><h2 class="addons-title">' . sb_('Monthly usage in') . ' ' . date('Y') . '</h2><p>' . sb_('The number of messages sent monthly, all messages are counted, including messages from agents, administrators and chatbot.') . '</p></div></div><canvas id="chart-usage" class="sb-loading" height="100"></canvas>';
    }
}

?>

<?php function box_registration_login()
{
    $appsumo = base64_decode(sb_isset($_GET, 'appsumo'));
    global $cloud_settings; ?>
    <div
        class="sb-registration-box sb-cloud-box sb-admin-box sb-admin-box_new<?php echo !isset($_GET['login']) && !isset($_GET['reset']) ? ' active' : '' ?>">
        <!-- <div class="sb-info"></div>
        <div class="sb-top-bar">
            <img src="<?php echo SB_CLOUD_BRAND_LOGO ?>" />
            <div class="sb-title">
                <?php sb_e('New account') ?>
            </div>
            <div class="sb-text">
                <?php sb_e($appsumo ? 'Complete the AppSumo registration.' : 'Create your free account. No payment information required.') ?>
            </div>
        </div>
        <div class="sb-main">
            <div id="first_name" class="sb-input">
                <span>
                    <?php sb_e('First name') ?>
                </span>
                <input type="text" required />
            </div>
            <div id="last_name" class="sb-input">
                <span>
                    <?php sb_e('Last name') ?>
                </span>
                <input type="text" required />
            </div>
            <div id="email" class="sb-input">
                <span>
                    <?php sb_e('Email') ?>
                </span>
                <input type="email" <?php echo $appsumo ? 'value="' . $appsumo . '" readonly="true" style="color:#989898"' : '' ?> required />
            </div>
            <div id="password" class="sb-input">
                <span>
                    <?php sb_e('Password') ?>
                </span>
                <input type="password" required />
            </div>
            <div id="password_2" class="sb-input">
                <span>
                    <?php sb_e('Repeat password') ?>
                </span>
                <input type="password" required />
            </div>
            <?php
            $code = '';
            for ($i = 1; $i < 5; $i++) {
                $name = sb_isset($cloud_settings, 'registration-field-' . $i);
                if ($name) {
                    $code .= '<div id="' . sb_string_slug($name) . '" class="sb-input"><span>' . sb_($name) . '</span><input type="text" required /></div>';
                }
            }
            echo $code;
            ?>
            <div class="sb-bottom">
                <div class="sb-btn btn-register">
                    <?php sb_e('Create account') ?>
                </div>
                <div class="sb-text">
                    <?php sb_e('Already have an account?') ?>
                </div>
                <div class="sb-text sb-btn-login-box">
                    <?php sb_e('Sign In') ?>
                </div>
            </div>
            <div class="sb-errors-area"></div>
        </div>
        <div class="loading-screen">
            <i class="sb-loading"></i>
            <p>
                <?php sb_e('We are creating your account...') ?>
            </p>
        </div> -->
        <div class="container">
            <div class="row">
                <div class="col-md-6 top_left">
                    <div class="left_section">
                        <div class="logo-container">
                            <img src="../account/media/logo-new-2.svg" style="width: 100%; alt=" logo">
                            <!-- <div class="logo-text">Nexleon Helpdesk</div> -->
                        </div>
                        <div class="laptop-image">
                            <img src="../account/media/Group.png" alt="dash">
                        </div>
                        <div class="welcome-title">Welcome to Nexleon Helpdesk</div>
                        <div class="welcome-description">
                            Access your tickets, track progress, and connect with our support team — all in one place. Log
                            in to get
                            started.
                        </div>
                    </div>
                </div>
                <div class="col-md-6 top_right">
                    <div class="right_section">
                        <div class="login-form sb-main">
                            <h1 class="login-title">Sign Up</h1>
                            <p class="login-description mb-4">
                                Enter your details to create a new account
                            </p>
                            <div class="form-fields">
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">First
                                            Name</label></div>
                                    <div id="first_name" class="input-wrapper sb-input">
                                        <input type="text" placeholder="Enter first name" class="form-input" />
                                    </div>
                                </div>
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">Last
                                            Name</label></div>
                                    <div id="last_name" class="input-wrapper sb-input">
                                        <input type="text" placeholder="Enter last name" class="form-input" />
                                    </div>
                                </div>
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">Business
                                            email</label></div>
                                    <div id="email" class="input-wrapper sb-input">
                                        <input type="email" placeholder="Business email" class="form-input" />
                                    </div>
                                </div>
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">Password</label>
                                    </div>
                                    <div id="password" class="input-wrapper sb-input">
                                        <input type="password" placeholder="8 Characters or more" class="form-input"
                                            id="password-field-signup" />
                                        <i class="far fa-eye" id="togglePasswordsignup"
                                            style="margin-left: -30px;margin-right: 10px; cursor: pointer;"></i>
                                        <!-- <div class="eye-icon" onclick="togglePasswordVisibility()">
                                            <svg width="24" height="24" fill="none" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 0C12.787 0 15.263 1.257 17.026 2.813C17.911 3.594 18.64 4.471 19.154 5.344C19.659 6.201 20 7.13 20 8C20 8.87 19.66 9.799 19.154 10.656C18.64 11.529 17.911 12.406 17.026 13.187C15.263 14.743 12.786 16 10 16C7.213 16 4.737 14.743 2.974 13.187C2.089 12.406 1.36 11.529 0.846 10.656C0.34 9.799 0 8.87 0 8C0 7.13 0.34 6.201 0.846 5.344C1.36 4.471 2.089 3.594 2.974 2.813C4.737 1.257 7.214 0 10 0ZM10 2C7.816 2 5.792 2.993 4.298 4.312C3.554 4.968 2.966 5.685 2.569 6.359C2.163 7.049 2 7.62 2 8C2 8.38 2.163 8.951 2.569 9.641C2.966 10.315 3.554 11.031 4.298 11.688C5.792 13.007 7.816 14 10 14C12.184 14 14.208 13.007 15.702 11.688C16.446 11.031 17.034 10.315 17.431 9.641C17.837 8.951 18 8.38 18 8C18 7.62 17.837 7.049 17.431 6.359C17.034 5.685 16.446 4.969 15.702 4.312C14.208 2.993 12.184 2 10 2ZM10 5C10.088 5 10.175 5.00367 10.261 5.011C10.0439 5.39185 9.95792 5.8335 10.0163 6.26798C10.0747 6.70246 10.2743 7.10572 10.5843 7.41571C10.8943 7.7257 11.2975 7.92525 11.732 7.98366C12.1665 8.04208 12.6081 7.95611 12.989 7.739C13.0416 8.34117 12.911 8.94518 12.6145 9.47189C12.3179 9.9986 11.8692 10.4234 11.327 10.6907C10.7849 10.958 10.1746 11.0553 9.57622 10.9699C8.97784 10.8844 8.41922 10.6202 7.97357 10.2118C7.52792 9.80343 7.21603 9.26995 7.07876 8.68129C6.94149 8.09262 6.98524 7.47621 7.20429 6.91284C7.42334 6.34946 7.80746 5.8654 8.30633 5.52407C8.8052 5.18275 9.39554 5.00008 10 5Z"
                                                    fill="#194BC1" />
                                            </svg>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="field-container">
                                    <div class="field-label"><label class="required-label" for="first_name">Confirm
                                            Password</label></div>
                                    <div id="password_2" class="input-wrapper sb-input">
                                        <input type="password" placeholder="Enter confirm password" class="form-input"
                                            id="password-field-confirm" />
                                        <i class="far fa-eye" id="togglePasswordconfirm"
                                            style="margin-left: -30px;margin-right: 10px; cursor: pointer;"></i>
                                        <!-- <div class="eye-icon" onclick="togglePasswordVisibility()">
                                            <svg width="24" height="24" fill="none" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 0C12.787 0 15.263 1.257 17.026 2.813C17.911 3.594 18.64 4.471 19.154 5.344C19.659 6.201 20 7.13 20 8C20 8.87 19.66 9.799 19.154 10.656C18.64 11.529 17.911 12.406 17.026 13.187C15.263 14.743 12.786 16 10 16C7.213 16 4.737 14.743 2.974 13.187C2.089 12.406 1.36 11.529 0.846 10.656C0.34 9.799 0 8.87 0 8C0 7.13 0.34 6.201 0.846 5.344C1.36 4.471 2.089 3.594 2.974 2.813C4.737 1.257 7.214 0 10 0ZM10 2C7.816 2 5.792 2.993 4.298 4.312C3.554 4.968 2.966 5.685 2.569 6.359C2.163 7.049 2 7.62 2 8C2 8.38 2.163 8.951 2.569 9.641C2.966 10.315 3.554 11.031 4.298 11.688C5.792 13.007 7.816 14 10 14C12.184 14 14.208 13.007 15.702 11.688C16.446 11.031 17.034 10.315 17.431 9.641C17.837 8.951 18 8.38 18 8C18 7.62 17.837 7.049 17.431 6.359C17.034 5.685 16.446 4.969 15.702 4.312C14.208 2.993 12.184 2 10 2ZM10 5C10.088 5 10.175 5.00367 10.261 5.011C10.0439 5.39185 9.95792 5.8335 10.0163 6.26798C10.0747 6.70246 10.2743 7.10572 10.5843 7.41571C10.8943 7.7257 11.2975 7.92525 11.732 7.98366C12.1665 8.04208 12.6081 7.95611 12.989 7.739C13.0416 8.34117 12.911 8.94518 12.6145 9.47189C12.3179 9.9986 11.8692 10.4234 11.327 10.6907C10.7849 10.958 10.1746 11.0553 9.57622 10.9699C8.97784 10.8844 8.41922 10.6202 7.97357 10.2118C7.52792 9.80343 7.21603 9.26995 7.07876 8.68129C6.94149 8.09262 6.98524 7.47621 7.20429 6.91284C7.42334 6.34946 7.80746 5.8654 8.30633 5.52407C8.8052 5.18275 9.39554 5.00008 10 5Z"
                                                    fill="#194BC1" />
                                            </svg>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="sb-errors-area m-0 text-end"></div>
                            <div class="auth-options mt-2">
                                <label class="remember-me">
                                    <input type="checkbox" class="checkbox" checked style="margin-top:5px;" />
                                    <span class="remember-text">Click Here To Accept The Platform’s Terms Of Services And
                                        Privacy Policy</span>
                                </label>
                            </div>
                            <button class="login-button btn-register">Sign Up</button>
                            <div class="register-prompt">
                                <div class="no-account">Already have an account?</div>
                                <div class="register-link sb-btn-login-box">Sign In</div>
                            </div>
                        </div>
                        <div class="loading-screen">
                            <i class="sb-loading"></i>
                            <p>
                                <?php sb_e('We are creating your account...') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-login-box sb-cloud-box sb-admin-box sb-admin-box_new<?php if (isset($_GET['login']))
        echo ' active' ?>">
            <!-- <div class="sb-info"></div>
        <div class="sb-top-bar">
            <img src="<?php echo SB_CLOUD_BRAND_LOGO ?>" />
            <div class="sb-title">
                <?php sb_e('Sign in to your account') ?>
            </div>
            <div class="sb-text">
                <?php echo sb_('To continue to') . ' ' . SB_CLOUD_BRAND_NAME ?>
            </div>
        </div>
        <div class="sb-main">
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
            <div class="sb-text btn-forgot-password">
                <?php sb_e('Forgot your password?') ?>
            </div>
            <div class="sb-bottom">
                <div class="sb-btn btn-login">
                    <?php sb_e('Sign in') ?>
                </div>
                <div class="sb-text">
                    <?php sb_e('Need new account?') ?>
                </div>
                <div class="sb-text btn-registration-box">
                    <?php sb_e('Sign up free') ?>
                </div>
            </div>
            <div class="sb-errors-area"></div>
        </div> -->

        <div class="container">
            <div class="row">
                <div class="col-md-6 top_left">
                    <div class="left_section">
                        <div class="logo-container">
                            <img src="../account/media/logo-new-2.svg" style="width: 100%; alt=" logo">
                            <!-- <div class="logo-text">Nexleon Helpdesk</div> -->
                        </div>
                        <div class="laptop-image">
                            <img src="../account/media/Group.png" alt="dash">
                        </div>
                        <div class="welcome-title">Welcome to Nexleon Helpdesk</div>
                        <div class="welcome-description">Effortless, AI-powered support. Track tickets, get updates, and
                            resolve issues fast. Log in to begin.</div>
                    </div>
                </div>
                <div class="col-md-6 top_right">
                    <div class="right_section">
                        <div class="login-form sb-main">
                            <h1 class="login-title">Sign In</h1>
                            <p class="login-description">
                                Enter the Business Email Address and Password to log in to your account
                            </p>
                            <div class="form-fields">
                                <div class="field-container mb-2">
                                    <div class="field-label"><label class="required-label" for="email">Business
                                            Email</label></div>
                                    <div id="email" class="input-wrapper sb-input">
                                        <input type="email" placeholder="name@work-email.com" id="businessemail"
                                            class="form-input" />
                                    </div>
                                </div>
                                <div class="sb-errors-area m-0 text-end emailerror"></div>
                                <div class="field-container mb-2">
                                    <div class="field-label"> <label class="required-label" id="password-field"
                                            for="password-field">Password</label></div>
                                    <div id="password" class="input-wrapper sb-input">
                                        <input type="password" placeholder="8 Characters or more" minlength="8"
                                            class="form-input" id="passwordfield" />
                                        <i class="far fa-eye" id="togglePassword"
                                            style="margin-left: -30px;margin-right: 10px; cursor: pointer;"></i>
                                        <!-- <div class="eye-icon" onclick="togglePasswordVisibility()">
                                            <svg width="24" height="24" fill="none" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 0C12.787 0 15.263 1.257 17.026 2.813C17.911 3.594 18.64 4.471 19.154 5.344C19.659 6.201 20 7.13 20 8C20 8.87 19.66 9.799 19.154 10.656C18.64 11.529 17.911 12.406 17.026 13.187C15.263 14.743 12.786 16 10 16C7.213 16 4.737 14.743 2.974 13.187C2.089 12.406 1.36 11.529 0.846 10.656C0.34 9.799 0 8.87 0 8C0 7.13 0.34 6.201 0.846 5.344C1.36 4.471 2.089 3.594 2.974 2.813C4.737 1.257 7.214 0 10 0ZM10 2C7.816 2 5.792 2.993 4.298 4.312C3.554 4.968 2.966 5.685 2.569 6.359C2.163 7.049 2 7.62 2 8C2 8.38 2.163 8.951 2.569 9.641C2.966 10.315 3.554 11.031 4.298 11.688C5.792 13.007 7.816 14 10 14C12.184 14 14.208 13.007 15.702 11.688C16.446 11.031 17.034 10.315 17.431 9.641C17.837 8.951 18 8.38 18 8C18 7.62 17.837 7.049 17.431 6.359C17.034 5.685 16.446 4.969 15.702 4.312C14.208 2.993 12.184 2 10 2ZM10 5C10.088 5 10.175 5.00367 10.261 5.011C10.0439 5.39185 9.95792 5.8335 10.0163 6.26798C10.0747 6.70246 10.2743 7.10572 10.5843 7.41571C10.8943 7.7257 11.2975 7.92525 11.732 7.98366C12.1665 8.04208 12.6081 7.95611 12.989 7.739C13.0416 8.34117 12.911 8.94518 12.6145 9.47189C12.3179 9.9986 11.8692 10.4234 11.327 10.6907C10.7849 10.958 10.1746 11.0553 9.57622 10.9699C8.97784 10.8844 8.41922 10.6202 7.97357 10.2118C7.52792 9.80343 7.21603 9.26995 7.07876 8.68129C6.94149 8.09262 6.98524 7.47621 7.20429 6.91284C7.42334 6.34946 7.80746 5.8654 8.30633 5.52407C8.8052 5.18275 9.39554 5.00008 10 5Z"
                                                    fill="#194BC1" />
                                            </svg>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="sb-errors-area m-0 text-end passworderror"></div>
                                <div class="sb-errors-area m-0 text-end error"></div>
                            </div>
                            <div class="auth-options">
                                <label class="remember-me">
                                    <input type="checkbox" class="checkbox" checked />
                                    <span class="remember-text">Remember Me</span>
                                </label>
                                <div class="forgot-password btn-forgot-password">
                                    Forgot Password?
                                </div>
                            </div>
                            <button class="login-button btn-login">Login</button>
                            <div class="register-prompt">
                                <div class="no-account">Don't have an account?</div>
                                <div class="register-link btn-registration-box">Register</div>
                            </div>
                            <p class="disclaimer mt-2">
                                <?php sb_e(sb_isset($cloud_settings, 'disclaimer', 'By creating an account you agree to our <a target="_blank" href="">Terms Of Service</a> and <a target="_blank" href="">Privacy Policy</a>.<br />&copy; 2025 Nexleon. All rights reserved.')) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-reset-password-box sb-cloud-box sb-admin-box sb-admin-box_new">
        <!-- <div class="sb-info"></div>
        <div class="sb-top-bar">
            <img src="<?php echo SB_CLOUD_BRAND_LOGO ?>" />
            <div class="sb-title">
                <?php sb_e('Reset password') ?>
            </div>
            <div class="sb-text">
                <?php sb_e('Enter your email below, you will receive an email with instructions on how to reset your password.') ?>
            </div>
        </div>
        <div class="sb-main">
            <div class="sb-input">
                <span>
                    <?php sb_e('Email') ?>
                </span>
                <input id="reset-password-email" type="email" required />
            </div>
            <div class="sb-bottom">
                <div class="sb-btn btn-reset-password">
                    <?php sb_e('Reset password') ?>
                </div>
                <div class="sb-text btn-cancel-reset-password">
                    <?php sb_e('Cancel') ?>
                </div>
            </div>
        </div> -->
        <div class="container">
            <div class="row">
                <div class="col-md-6 top_left">
                    <div class="left_section">
                        <div class="logo-container">
                            <img src="../account/media/logo-new-2.svg" style="width: 100%; alt=" logo">
                            <!-- <div class="logo-text">Nexleon Helpdesk</div> -->
                        </div>
                        <div class="laptop-image">
                            <img src="../account/media/Group.png" alt="dash">
                        </div>
                        <div class="welcome-title">Welcome to Nexleon Helpdesk</div>
                        <div class="welcome-description">
                            No worries — reset it here to regain access to your tickets, track progress, and reconnect with
                            our support team.
                        </div>
                    </div>
                </div>
                <div class="col-md-6 top_right">
                    <div class="right_section">
                        <div class="login-form sb-main">
                            <h1 class="login-title">Forget Password?</h1>
                            <p class="login-description">
                                No worries! Just enter your email and we’ll send you login instructions
                            </p>
                            <div class="form-fields">
                                <div class="field-container">
                                    <div class="field-label"><label class="required-label" for="email">Business
                                            Email</label></div>
                                    <div id="email" class="input-wrapper sb-input">
                                        <input type="email" placeholder="Business Email" id="email-forgot"
                                            class="form-input" />
                                    </div>
                                </div>
                            </div>
                            <div class="forgot-password btn-cancel-reset-password mt-2">
                                Back to Sign In
                            </div>
                            <button class="login-button sb-btn btn-reset-password">Send Reset Email</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-reset-password-box-2 sb-cloud-box sb-admin-box<?php if (isset($_GET['reset']))
        echo ' active' ?>">
            <div class="sb-info"></div>
            <div class="sb-top-bar">
                <img src="<?php echo SB_CLOUD_BRAND_LOGO ?>" />
            <div class="sb-title">
                <?php sb_e('Reset password') ?>
            </div>
            <div class="sb-text">
                <?php sb_e('Enter your new password here.') ?>
            </div>
        </div>
        <div class="sb-main">
            <div class="sb-input">
                <span>
                    <?php sb_e('Password') ?>
                </span>
                <input id="reset-password-1" type="password" required />
            </div>
            <div class="sb-input">
                <span>
                    <?php sb_e('Repeat password') ?>
                </span>
                <input id="reset-password-2" type="password" required />
            </div>
            <div class="sb-bottom">
                <div class="sb-btn btn-reset-password-2">
                    <?php sb_e('Reset password') ?>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php function box_referral()
{
    global $cloud_settings;
    if (isset($cloud_settings['referral-commission'])) { ?>
        <div id="tab-referral">
            <h2 class="addons-title first-title">
                <?php sb_e('Refer a friend') ?>
            </h2>
            <p>
                <?php echo sb_isset($cloud_settings, 'referral-text', '') ?>
            </p>
            <div class="sb-input">
                <input value="<?php echo CLOUD_URL . '?ref=' . sb_encryption('encrypt', account()['user_id']) ?>" type="text"
                    readonly />
            </div>
            <hr class="space" />
            <h2 class="addons-title">
                <?php sb_e('Your current earnings') ?>
            </h2>
            <div class="text-earnings">
                <?php echo strtoupper(membership_currency()) . ' ' . super_get_user_data('referral', account()['user_id'], 0) ?>
            </div>
            <hr class="space" />
            <h2 class="addons-title">
                <?php sb_e('Your payment information') ?>
            </h2>
            <div data-type="text" class="sb-input">
                <span><?php sb_e('Method') ?></span>
                <select id="payment_method">
                    <option></option>
                    <option value="paypal">PayPal</option>
                    <option value="bank"><?php sb_e('Bank Transfer') ?></option>
                </select>
            </div>
            <div data-type="text" class="sb-input sb-input">
                <span id="payment_information_label"></span>
                <textarea id="payment_information"></textarea>
            </div>
            <hr class="space-sm" />
            <a id="save-payment-information" class="sb-btn sb-btn-white sb-icon">
                <i class="sb-icon-check"></i><?php sb_e('Save changes') ?>
            </a>
        </div>
    <?php }
} ?>