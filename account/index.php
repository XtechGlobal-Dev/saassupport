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
        media="(max-width: 767px)" />
    <!-- Manrope font cdn link  -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Inter:wght@500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SB_VERSION ?>/css/hgi-stroke-rounded.css" />
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
    <script>
        jQuery(document).ready(function ($) {
            $(".account-toggle-btn").click(function () {
                $(".sb-tab .sb-nav").toggleClass("side-open");
            })
            $(".ul_account li").click(function () {
                $(".sb-tab .sb-nav").removeClass("side-open")
            })
        })
    </script>
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
    echo '<script>var messages_volume = [' . implode(',', membership_volume()) . ']; var membership = { type: "' . SB_CLOUD_MEMBERSHIP_TYPE . '", quota: ' . $membership['quota'] . ', count: ' . $membership['count'] . ', expired: ' . ($expired ? 'true' : 'false') . (isset($membership['quota_agents']) ? (', quota_agents: ' . $membership['quota_agents'] . ', count_agents: ' . $membership['count_agents']) : '') . ', credits: ' . $membership['credits'] . ' }; var CLOUD_USER_ID = ' . account()['user_id'] . '; var CLOUD_CURRENCY = "' . strtoupper(membership_currency()) . '"; var TWILIO_SMS = ' . (defined('CLOUD_TWILIO_SID') && !empty(CLOUD_TWILIO_SID) ? 'true' : 'false') . '; var external_integration = "' . ($shopify ? 'shopify' : '') . '";</script>' . PHP_EOL; ?>
    <div class="sb-account-box sb-admin sb-loading">
        <div class="sb-top-bar py-4">
            <div>
                <h2>
                    <button class="account-toggle-btn me-3" type="button">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4 5C3.44772 5 3 5.44772 3 6C3 6.55228 3.44772 7 4 7H20C20.5523 7 21 6.55228 21 6C21 5.44772 20.5523 5 20 5H4ZM7 12C7 11.4477 7.44772 11 8 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H8C7.44772 13 7 12.5523 7 12ZM13 18C13 17.4477 13.4477 17 14 17H20C20.5523 17 21 17.4477 21 18C21 18.5523 20.5523 19 20 19H14C13.4477 19 13 18.5523 13 18Z"
                                fill="#000000"></path>
                        </svg>
                    </button>
                    <img src="<?php echo SB_CLOUD_BRAND_ICON ?>" class="d-md-none" />
                    <?php sb_e('Account') ?>
                </h2>
            </div>
            <div>
                <a class="sb-btn sb-btn-dashboard" href="<?php echo CLOUD_URL ?>?area=dashboard">
                    <i class="fa-solid fa-gauge mr-2" aria-hidden="true"></i> <?php sb_e('Go to Dashboard') ?>
                </a>
            </div>
        </div>
        <div class="sb-tab">
            <div class="sb-nav">
                <span class="logo">
                    <button class="account-toggle-btn d-md-none" type="button">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4 5C3.44772 5 3 5.44772 3 6C3 6.55228 3.44772 7 4 7H20C20.5523 7 21 6.55228 21 6C21 5.44772 20.5523 5 20 5H4ZM7 12C7 11.4477 7.44772 11 8 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H8C7.44772 13 7 12.5523 7 12ZM13 18C13 17.4477 13.4477 17 14 17H20C20.5523 17 21 17.4477 21 18C21 18.5523 20.5523 19 20 19H14C13.4477 19 13 18.5523 13 18Z"
                                fill="#000000"></path>
                        </svg>
                    </button>
                    <img src="<?php echo SB_CLOUD_BRAND_ICON ?>" class="d-none d-md-flex" />
                </span>
                <div>
                    <?php sb_e('Installation') ?>
                </div>
                <ul class="ul_account">
                    <li id="nav-installation" class="sb-active">
                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.6957 1.41166C12.6957 0.67378 12.0996 0.0776367 11.3617 0.0776367C10.6238 0.0776367 10.0276 0.67378 10.0276 1.41166V11.5294L6.96772 8.46951C6.44662 7.9484 5.60034 7.9484 5.07924 8.46951C4.55813 8.99061 4.55813 9.83688 5.07924 10.358L10.4153 15.6941C10.9365 16.2152 11.7827 16.2152 12.3038 15.6941L17.6399 10.358C18.161 9.83688 18.161 8.99061 17.6399 8.46951C17.1188 7.9484 16.2726 7.9484 15.7515 8.46951L12.6957 11.5294V1.41166ZM3.35751 14.7519C1.88591 14.7519 0.689453 15.9484 0.689453 17.42V18.754C0.689453 20.2256 1.88591 21.4221 3.35751 21.4221H19.3658C20.8374 21.4221 22.0339 20.2256 22.0339 18.754V17.42C22.0339 15.9484 20.8374 14.7519 19.3658 14.7519H15.1345L13.246 16.6404C12.2038 17.6826 10.5154 17.6826 9.47319 16.6404L7.58888 14.7519H3.35751ZM18.6988 17.0865C18.9642 17.0865 19.2187 17.1919 19.4063 17.3795C19.5939 17.5672 19.6993 17.8217 19.6993 18.087C19.6993 18.3524 19.5939 18.6069 19.4063 18.7945C19.2187 18.9821 18.9642 19.0875 18.6988 19.0875C18.4335 19.0875 18.179 18.9821 17.9914 18.7945C17.8037 18.6069 17.6983 18.3524 17.6983 18.087C17.6983 17.8217 17.8037 17.5672 17.9914 17.3795C18.179 17.1919 18.4335 17.0865 18.6988 17.0865Z"
                                fill="#374151" />
                        </svg>
                        <?php sb_e('Installation') ?>
                    </li>
                    <li id="nav-membership">
                        <svg width="25" height="19" viewBox="0 0 25 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.5692 3.24656C14.0445 2.95474 14.3613 2.4253 14.3613 1.82916C14.3613 0.907843 13.6151 0.161621 12.6938 0.161621C11.7724 0.161621 11.0262 0.907843 11.0262 1.82916C11.0262 2.42947 11.343 2.95474 11.8183 3.24656L9.42955 8.02405C9.05019 8.78278 8.06634 8.99956 7.4035 8.47011L3.68906 5.49773C3.8975 5.21842 4.02257 4.87241 4.02257 4.49721C4.02257 3.5759 3.27635 2.82968 2.35503 2.82968C1.43372 2.82968 0.6875 3.5759 0.6875 4.49721C0.6875 5.41852 1.43372 6.16475 2.35503 6.16475C2.36337 6.16475 2.37588 6.16475 2.38422 6.16475L4.28938 16.6452C4.51866 17.9125 5.6234 18.838 6.91574 18.838H18.4718C19.7599 18.838 20.8647 17.9167 21.0981 16.6452L23.0033 6.16475C23.0116 6.16475 23.0241 6.16475 23.0325 6.16475C23.9538 6.16475 24.7 5.41852 24.7 4.49721C24.7 3.5759 23.9538 2.82968 23.0325 2.82968C22.1112 2.82968 21.3649 3.5759 21.3649 4.49721C21.3649 4.87241 21.49 5.21842 21.6984 5.49773L17.984 8.47011C17.3212 8.99956 16.3373 8.78278 15.9579 8.02405L13.5692 3.24656Z"
                                fill="#374151" />
                        </svg>

                        <?php sb_e('Membership') ?>
                    </li>
                    <li id="nav-invoices">
                        <svg width="25" height="22" viewBox="0 0 25 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1485_2407)">
                                <path
                                    d="M3.35751 1.82275C1.88591 1.82275 0.689453 3.01921 0.689453 4.49081V5.82484H24.702V4.49081C24.702 3.01921 23.5055 1.82275 22.0339 1.82275H3.35751ZM24.702 9.82692H0.689453V17.8311C0.689453 19.3027 1.88591 20.4991 3.35751 20.4991H22.0339C23.5055 20.4991 24.702 19.3027 24.702 17.8311V9.82692ZM5.35855 15.163H8.02661C8.39346 15.163 8.69362 15.4632 8.69362 15.83C8.69362 16.1969 8.39346 16.4971 8.02661 16.4971H5.35855C4.99169 16.4971 4.69154 16.1969 4.69154 15.83C4.69154 15.4632 4.99169 15.163 5.35855 15.163ZM10.0276 15.83C10.0276 15.4632 10.3278 15.163 10.6947 15.163H16.0308C16.3976 15.163 16.6978 15.4632 16.6978 15.83C16.6978 16.1969 16.3976 16.4971 16.0308 16.4971H10.6947C10.3278 16.4971 10.0276 16.1969 10.0276 15.83Z"
                                    fill="#374151" />
                            </g>
                            <defs>
                                <clipPath id="clip0_1485_2407">
                                    <path d="M0.689453 0.48877H24.702V21.8332H0.689453V0.48877Z" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>

                        <?php sb_e(PAYMENT_PROVIDER == 'stripe' ? 'Invoices' : 'Payments') ?>
                    </li>

                    <li id="nav-profile">
                        <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.0276 10.867C11.4429 10.867 12.8001 10.3049 13.8008 9.30414C14.8016 8.30342 15.3638 6.94616 15.3638 5.53094C15.3638 4.11571 14.8016 2.75845 13.8008 1.75774C12.8001 0.75702 11.4429 0.194824 10.0276 0.194824C8.61242 0.194824 7.25516 0.75702 6.25445 1.75774C5.25373 2.75845 4.69154 4.11571 4.69154 5.53094C4.69154 6.94616 5.25373 8.30342 6.25445 9.30414C7.25516 10.3049 8.61242 10.867 10.0276 10.867ZM8.12249 12.8681C4.01619 12.8681 0.689453 16.1948 0.689453 20.3011C0.689453 20.9848 1.24391 21.5393 1.9276 21.5393H18.1277C18.8114 21.5393 19.3658 20.9848 19.3658 20.3011C19.3658 16.1948 16.0391 12.8681 11.9328 12.8681H8.12249Z"
                                fill="#374151" />
                        </svg>
                        <?php sb_e('Profile') ?>
                    </li>
                    <?php
                    if (!empty($cloud_settings['referral-commission'])) {
                        echo '<li id="nav-referral" style="display:none;">
                            <svg width="28" height="23" viewBox="0 0 28 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1485_2417)">
                                    <path d="M4.69154 6.23601C4.69154 4.82079 5.25373 3.46353 6.25445 2.46281C7.25516 1.4621 8.61242 0.899902 10.0276 0.899902C11.4429 0.899902 12.8001 1.4621 13.8008 2.46281C14.8016 3.46353 15.3638 4.82079 15.3638 6.23601C15.3638 7.65124 14.8016 9.0085 13.8008 10.0092C12.8001 11.0099 11.4429 11.5721 10.0276 11.5721C8.61242 11.5721 7.25516 11.0099 6.25445 10.0092C5.25373 9.0085 4.69154 7.65124 4.69154 6.23601ZM0.689453 21.0062C0.689453 16.8999 4.01619 13.5732 8.12249 13.5732H11.9328C16.0391 13.5732 19.3658 16.8999 19.3658 21.0062C19.3658 21.6899 18.8114 22.2443 18.1277 22.2443H1.9276C1.24391 22.2443 0.689453 21.6899 0.689453 21.0062ZM21.7004 13.9067V11.2386H19.0323C18.4779 11.2386 18.0318 10.7926 18.0318 10.2381C18.0318 9.68364 18.4779 9.23758 19.0323 9.23758H21.7004V6.56952C21.7004 6.01507 22.1465 5.569 22.7009 5.569C23.2554 5.569 23.7014 6.01507 23.7014 6.56952V9.23758H26.3695C26.9239 9.23758 27.37 9.68364 27.37 10.2381C27.37 10.7926 26.9239 11.2386 26.3695 11.2386H23.7014V13.9067C23.7014 14.4611 23.2554 14.9072 22.7009 14.9072C22.1465 14.9072 21.7004 14.4611 21.7004 13.9067Z" fill="#374151"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_1485_2417">
                                        <path d="M0.689453 0.899902H27.37V22.2443H0.689453V0.899902Z" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        ' . sb_('Refer a friend') . '</li>';
                    }
                    ?>
                    <li id="nav-logout">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1485_2422)">
                                <path
                                    d="M15.754 4.41476L20.8734 9.53409C21.1735 9.83424 21.3444 10.247 21.3444 10.6722C21.3444 11.0974 21.1735 11.5101 20.8734 11.8103L15.754 16.9296C15.4872 17.1964 15.1287 17.3423 14.7535 17.3423C13.9739 17.3423 13.3403 16.7087 13.3403 15.9291V13.3402H8.00417C7.26628 13.3402 6.67014 12.7441 6.67014 12.0062V9.33815C6.67014 8.60027 7.26628 8.00412 8.00417 8.00412H13.3403V5.41528C13.3403 4.6357 13.9739 4.00204 14.7535 4.00204C15.1287 4.00204 15.4872 4.15212 15.754 4.41476ZM6.67014 4.00204H4.00208C3.2642 4.00204 2.66806 4.59818 2.66806 5.33607V16.0083C2.66806 16.7462 3.2642 17.3423 4.00208 17.3423H6.67014C7.40802 17.3423 8.00417 17.9385 8.00417 18.6763C8.00417 19.4142 7.40802 20.0104 6.67014 20.0104H4.00208C1.7926 20.0104 0 18.2178 0 16.0083V5.33607C0 3.12658 1.7926 1.33398 4.00208 1.33398H6.67014C7.40802 1.33398 8.00417 1.93013 8.00417 2.66801C8.00417 3.4059 7.40802 4.00204 6.67014 4.00204Z"
                                    fill="#374151" />
                            </g>
                            <defs>
                                <clipPath id="clip0_1485_2422">
                                    <path d="M0 0H21.3444V21.3444H0V0Z" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>

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
                        echo '<p>' . htmlspecialchars(sb_(sb_isset($cloud_settings, 'text_embed_code', 'To add the chat to your website, paste this code before the closing </body> tag on each page. Then, reload your website to see the chat in the bottom-right corner. Click the dashboard button in the top-right to access the admin area.'))) . '</p><div class="sb-setting" style="flex-wrap: wrap;align-items: center;"><p>' . sb_("Chatbot embed code") . '</p><textarea id="embed-code" class="embed-code" style="width: calc(100% - 126px);" readonly></textarea><button id="copy-btn" class="copy-button sb-btn ml-2" type="button">Copy</button></div><div class="sb-setting" style="flex-wrap: wrap;align-items: center;"><p>' . sb_("Ticket panel embed code") . '</p><textarea id="embed-code2" class="embed-code" style="width: calc(100% - 126px);" readonly></textarea><button id="copy-btn" class="copy-button sb-btn ml-2" type="button">Copy</button></div>';
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
                    <h2 class="addons-title first-title ">
                        <?php sb_e('Membership') ?>
                    </h2>
                    <p class="first-p">Manage your subscription and billing</p>
                    <?php box_membership($membership) ?>
                    <hr />
                    <?php box_membership_plans($membership['id'], $expired) ?>
                    <?php box_credits(!$shopify) ?>
                    <?php //box_addons() ?>
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
                        <?php sb_e('Update your Profile Information here.') ?>
                    </p>
                    <div id="first_name" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('First name') ?>
                        </span>
                        <input type="text" placeholder="First name" />
                    </div>
                    <div id="last_name" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('Last name') ?>
                        </span>
                        <input type="text" placeholder="Last name" />
                    </div> 
                    <div id="email" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('Email') ?>
                        </span>
                        <div class="d-flex flex-wrap sb-input w-100 mw-100 flex-row">
                            <input type="email" readonly placeholder="Email" />
                            <a class="sb-btn btn-verify-email">
                                <?php sb_e('Verify Email') ?>
                            </a> 
                        </div>
                    </div>
                    <div id="phone" data-type="text" class="sb-input sb-type-input-button">
                        <span class="required-label">
                            <?php sb_e('Phone') ?>
                        </span>
                        <input type="tel" placeholder="Phone number" />
                        <a class="sb-btn btn-verify-phone">
                            <?php sb_e('Verify') ?>
                        </a>
                    </div>
                    <div id="password" data-type="text" class="sb-input sb-type-input-button">
                        <span class="required-label">
                            <?php sb_e('Password') ?>
                        </span>
                        <input type="password" value="12345678" placeholder="Password" />
                    </div>
                    <h2 class="addons-title" style="width: 100%;"><?php sb_e('Billing details') ?></h2>
                    <style>
                        .required-label::after {
                            content: " *";
                            color: red;
                        }
                    </style>
                    <div id="company_name" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('Name') ?>
                        </span>
                        <input type="text" />
                    </div>
                    <div id="company_address" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('Address') ?>
                        </span>
                        <input type="text" />
                    </div>
                    <div id="company_postal_code" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('Postal code') ?>
                        </span>
                        <input type="text" />
                    </div>
                    <div id="company_city" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('City') ?>
                        </span>
                        <input type="text" />
                    </div>
                    <div id="company_country" data-type="select" class="sb-input">
                        <span>
                            <?php sb_e('Country') ?>
                        </span>
                        <?php echo sb_select_html('countries') ?>
                    </div>
                    <div id="company_tax_id" data-type="text" class="sb-input">
                        <span class="required-label">
                            <?php sb_e('Tax ID') ?>
                        </span>
                        <input type="text" />
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
    $agentsQuotaColor = $membership['quota_agents'] < $membership['count_agents'] ? 'background-color:red;' : '';
    $messageQuotaColor = $membership['quota'] < $membership['count'] ? 'background-color:red;' : '';
    $box_two = $membership_type_ma ? '<div class="detail-box"><span>' . sb_('Agents') . '</span><span>' . $membership['count_agents'] . ' / <span class="membership-quota">' . ($membership['quota_agents'] == 9999 ? '∞' : $membership['quota_agents']) . '</span></span> 
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: ' . ($membership["quota_agents"] > 0 ? ($membership["count_agents"] / $membership["quota_agents"]) * 100 : 0) . '%;' . $agentsQuotaColor . '" aria-valuenow=' . $membership['count_agents'] . ' aria-valuemin=' . $membership['count_agents'] . ' aria-valuemax=' . $membership['quota_agents'] . '></div>
        </div></div>' : '';
    $markeplace = false;
    if (substr($membership['expiration'], -2) == '37') {
        $extra = sb_isset(db_get('SELECT extra FROM users WHERE id = ' . account()['user_id']), 'extra');
        $markeplace = 'appsumo';
        if (strpos($extra, '-tw-')) {
            $markeplace = 'tw';
        }
    }

    if (isset($membership['expiration']) && $membership['expiration'] != '') {
        $date = $membership['expiration']; // original date in dd-mm-yy format
        // Convert to DateTime object
        $dateObj = DateTime::createFromFormat('d-m-y', $date);
        // Format to "10 October 2025"
        $formattedDate = $dateObj->format('d F Y');
    }


    $price_string = $membership['price'] == 0 ? '' : ($markeplace ? '<span id="membership-markeplace" data-markeplace="' . $markeplace . '" data-id="' . account_get_payment_id() . '"></span>' : (mb_strtoupper($membership['currency']) . ' ' . $membership['price'] . ' ' . membership_get_period_string($membership['period'])));
    echo '
    <div class="box-maso box-membership">
        <div class="box-black">';
    if (isset($formattedDate)) {
        echo '<h2>' . sb_('Next renewal date') . ': ' . $formattedDate . '</h2>';
    }
    echo '<div class="membership-detail-list">
                <div class="detail-box">
                    <span>' . sb_($name) . '</span>
                    <span>' . $membership['count'] . ' / <span class="membership-quota">' . $membership['quota'] . '</span></span>
                    <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: ' . ($membership['quota'] > 0 ? ($membership['count'] / $membership['quota']) * 100 : 0) . '%;' . $messageQuotaColor . '" aria-valuenow=' . $membership['count'] . ' aria-valuemin=' . $membership['count'] . ' aria-valuemax=' . $membership['quota'] . '></div>
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
                        <span class="check"></span>
                    </div>
                </div>
                <div style="margin-left: 0;">
                    <span class="membership-price" data-currency="' . $membership['currency'] . '">' . $price_string . '</span>
                </div>
            </div>
        </div>
    </div>';
    //echo '<div class="box-maso box-membership"><div class="box-black"><h2>' . sb_(date('F')) . ', ' . date('Y') . '</h2><div><div><span>' . $membership['count'] . ' / <span class="membership-quota">' . $membership['quota'] . '</span></span> <span>' . sb_($name) . '</span></div>' . $box_two . '</div></div><div class="box-black"><h2>' . sb_('Active Membership') . '</h2><div><div><span class="membership-name">' . sb_($membership['name']) . '</span> <span class="membership-price" data-currency="' . $membership['currency'] . '">' . $price_string . '</span></div></div></div></div>';
}

function box_membership_plans($active_membership_id, $expired = false)
{
    $plans = memberships();
    $currentPlanText = sb_("Current") . " " . sb_("Plan");
    $code = '<h2 class="addons-title">' . $currentPlanText . '</h2><div id="plans" class="plans-box">';
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
        $code .= '<div style=" ' . ($active_membership_id == $plan['id'] ? ' --borderColor: #00c17161 ' : '--borderColor:#E5E7EB') . ' " data-menu="' . $menu . '" data-id="' . $plan['id'] . '"' . ($active_membership_id == $plan['id'] ? ' data-active-membership="true"' : '') . ($active_membership_id == $plan['id'] && $expired ? ' data-expired="true"' : '') . '> <h4>' . $plan['name'] . '</h4><h3>' . mb_strtoupper($plan['currency']) . ' ' . $plan['price'] . ' <span>' . $period_top . '</span></h3>
        <ul>
            <li>' . $plan['quota'] . ' ' . sb_($membership_type_ma ? 'messages' : $membership_type) . ' ' . $period . ' </li>
            <li>' . ($membership_type_ma ? (($plan['quota_agents'] == 9999 ? sb_('unlimited') : $plan['quota_agents']) . ' ' . sb_('agents')) : '') . '</li>
            <li>' . cloud_embeddings_chars_limit($plan) . ' ' . sb_('characters to train the chatbot') . '</li>
        </ul> 
        '
            . ($active_membership_id == $plan['id'] ? '<button type="button" class="label_blue mb-0" style="background-color: #1a9260;">Current Plan</button>' : '<button type="button" class="label_blue mb-0">Upgrade Plan</button>') .
            '
        </div>';
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

function box_credits($auto_recharge = true) {
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

function box_addons() {
    $white_label_price = super_get_white_label();
    $addons = sb_defined('CLOUD_ADDONS');
    if ($white_label_price || $addons) {
        $account = account();
        // $code = '<h2 class="addons-title">' . sb_('Add-ons') . '</h2><p>' . sb_('Add-ons are optional features with a fixed subscription cost.') . '</p><div id="addons" class="plans-box">';
        $code = '<h2 class="addons-title">' . sb_('Add-ons') . '</h2><div id="addons" class="plans-box">';
        if ($white_label_price) {
            $code .= '<div class="sb-visible' . (membership_is_white_label($account['user_id']) ? ' sb-plan-active' : '') . '" id="purchase-white-label"><h4>' . sb_('White Label') . '</h4><h3>' . strtoupper(membership_currency()) . ' ' . $white_label_price . ' <span>' . sb_('a year') . '</span></h3><p>' . sb_('Remove our branding and logo from the chat widget.') . '</p><p class="d-flex align-items-center" style="gap:20px; margin-top: 10px;""><svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21.696 10.7325C21.696 10.77 21.696 10.8076 21.696 10.8451C21.6793 12.3667 20.2953 13.4006 18.7737 13.4006H14.6924C13.5876 13.4006 12.6913 14.2969 12.6913 15.4016C12.6913 15.5434 12.708 15.6809 12.733 15.8143C12.8206 16.2396 13.004 16.6481 13.1832 17.0608C13.4375 17.6361 13.6877 18.2072 13.6877 18.8117C13.6877 20.1374 12.7872 21.3422 11.4615 21.3964C11.3156 21.4006 11.1697 21.4047 11.0196 21.4047C5.12905 21.4047 0.351562 16.6273 0.351562 10.7325C0.351562 4.83779 5.12905 0.0603027 11.0238 0.0603027C16.9185 0.0603027 21.696 4.83779 21.696 10.7325ZM5.68767 12.0666C5.68767 11.7127 5.54712 11.3734 5.29695 11.1233C5.04677 10.8731 4.70745 10.7325 4.35365 10.7325C3.99984 10.7325 3.66052 10.8731 3.41035 11.1233C3.16017 11.3734 3.01962 11.7127 3.01962 12.0666C3.01962 12.4204 3.16017 12.7597 3.41035 13.0099C3.66052 13.26 3.99984 13.4006 4.35365 13.4006C4.70745 13.4006 5.04677 13.26 5.29695 13.0099C5.54712 12.7597 5.68767 12.4204 5.68767 12.0666ZM5.68767 8.06447C6.04148 8.06447 6.38079 7.92392 6.63097 7.67374C6.88115 7.42356 7.0217 7.08425 7.0217 6.73044C7.0217 6.37664 6.88115 6.03732 6.63097 5.78714C6.38079 5.53696 6.04148 5.39641 5.68767 5.39641C5.33387 5.39641 4.99455 5.53696 4.74437 5.78714C4.49419 6.03732 4.35365 6.37664 4.35365 6.73044C4.35365 7.08425 4.49419 7.42356 4.74437 7.67374C4.99455 7.92392 5.33387 8.06447 5.68767 8.06447ZM12.3578 4.06239C12.3578 3.70858 12.2173 3.36926 11.9671 3.11909C11.7169 2.86891 11.3776 2.72836 11.0238 2.72836C10.67 2.72836 10.3307 2.86891 10.0805 3.11909C9.83031 3.36926 9.68976 3.70858 9.68976 4.06239C9.68976 4.41619 9.83031 4.75551 10.0805 5.00569C10.3307 5.25587 10.67 5.39641 11.0238 5.39641C11.3776 5.39641 11.7169 5.25587 11.9671 5.00569C12.2173 4.75551 12.3578 4.41619 12.3578 4.06239ZM16.3599 8.06447C16.7137 8.06447 17.053 7.92392 17.3032 7.67374C17.5534 7.42356 17.6939 7.08425 17.6939 6.73044C17.6939 6.37664 17.5534 6.03732 17.3032 5.78714C17.053 5.53696 16.7137 5.39641 16.3599 5.39641C16.0061 5.39641 15.6668 5.53696 15.4166 5.78714C15.1664 6.03732 15.0259 6.37664 15.0259 6.73044C15.0259 7.08425 15.1664 7.42356 15.4166 7.67374C15.6668 7.92392 16.0061 8.06447 16.3599 8.06447Z" fill="#A855F7"/>
</svg>
Custom branding options</p></div>';
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

function button_cancel_membership($membership) {
    if ($membership['price'] != 0) {
        if (super_get_user_data('subscription_cancelation', get_active_account_id())) {
            echo '<p>' . sb_('Your membership renewal has been canceled. Your membership is set to expire on') . ' ' . membership_get_active()['expiration'] . '.</p>';
        } else {
            echo '<a id="cancel-subscription" class="sb-btn-text sb-icon sb-btn-red"><i class="sb-icon-close"></i>' . sb_('Cancel subscription') . '</a>';
        }
    }
}

function account_js() {
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

function box_chart() {
    if (in_array(sb_defined('SB_CLOUD_MEMBERSHIP_TYPE', 'messages'), ['messages', 'messages-agents'])) {
        echo '<div class="chart-box"><div><h2 class="addons-title">' . sb_('Monthly usage in') . ' ' . date('Y') . '</h2><p>' . sb_('The number of messages sent monthly, all messages are counted, including messages from agents, administrators and chatbot.') . '</p></div></div><canvas id="chart-usage" class="sb-loading" height="100"></canvas>';
    }
}

?>

<?php function box_registration_login() {
    $appsumo = base64_decode(sb_isset($_GET, 'appsumo'));
    $apps_login_code = '';
    if (defined('GOOGLE_LOGIN_CLIENT_ID')) {
        $apps_login_code = '<div class="sb-apps-login" data-text="' . sb_('Or') . '">
                <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=' . GOOGLE_LOGIN_CLIENT_ID . '&redirect_uri=' . urlencode(CLOUD_URL . '/account/google.php') . '&response_type=code&scope=openid%20email%20profile&access_type=offline&prompt=select_account" class="sb-btn sb-btn-white" data-app="google">
                    <img src="' . CLOUD_URL . '/script/media/apps/google.svg" /> ' . sb_('Continue with') . ' Google
                </a>
            </div>';
    }
    global $cloud_settings;
    if (isset($_GET['auto_login'])) {
        echo '<style>body { display: none; }</style>';
    }
    ?>
    <div class="sb-registration-box sb-cloud-box sb-admin-box sb-admin-box_new<?php echo !isset($_GET['login']) && !isset($_GET['reset']) ? ' active' : '' ?>">
        <div class="sb-info"></div>
        <!-- <div class="sb-top-bar">
            <img src="<?php echo SB_CLOUD_BRAND_LOGO ?>" />
            <div class="sb-title">
                <?php sb_e('New account') ?>
            </div>
            <div class="sb-text">
                <?php sb_e($appsumo ? 'Complete the AppSumo registration.' : 'Create your free account. No payment information required.') ?>
            </div>
        </div> -->
        <?php echo $apps_login_code ?>
        <!-- <div class="sb-main">
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
                        <div class="auth-options mt-2">
                                <label class="remember-me">
                                    <input type="checkbox" id="terms" class="checkbox form-input" checked
                                        style="margin-top:5px;" />
                                    <span class="remember-text">Click Here To Accept The Platform’s Terms Of Services And
                                        Privacy Policy</span>
                                </label>
                            </div>
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
        </div> -->
        <!-- <div class="loading-screen">
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
                                        <p class="error-msg text-danger small m-0"></p>
                                    </div>
                                </div>
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">Last
                                            Name</label></div>
                                    <div id="last_name" class="input-wrapper sb-input">
                                        <input type="text" placeholder="Enter last name" class="form-input" />
                                        <p class="error-msg text-danger small m-0"></p>
                                    </div>
                                </div>
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">Business
                                            email</label></div>
                                    <div id="email" class="input-wrapper sb-input">
                                        <input type="email" placeholder="Business email" class="form-input" />
                                        <p class="error-msg text-danger small m-0"></p>
                                    </div>
                                </div>
                                <div class="field-container mb-3">
                                    <div class="field-label"><label class="required-label" for="first_name">Password</label>
                                    </div>
                                    <div id="password" class="input-wrapper sb-input">
                                        <input type="password" placeholder="8 Characters or more" class="form-input"
                                            id="password-field-signup" />
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                            color="#000000" fill="none"
                                            style="margin-left: -30px;margin-right: 10px; cursor: pointer;"
                                            id="togglePasswordsignup">
                                            <path
                                                d="M21.544 11.045C21.848 11.4713 22 11.6845 22 12C22 12.3155 21.848 12.5287 21.544 12.955C20.1779 14.8706 16.6892 19 12 19C7.31078 19 3.8221 14.8706 2.45604 12.955C2.15201 12.5287 2 12.3155 2 12C2 11.6845 2.15201 11.4713 2.45604 11.045C3.8221 9.12944 7.31078 5 12 5C16.6892 5 20.1779 9.12944 21.544 11.045Z"
                                                stroke="#141B34" stroke-width="1.5" />
                                            <path
                                                d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                                                stroke="#141B34" stroke-width="1.5" />
                                        </svg>
                                        <p class="error-msg text-danger small m-0"></p>
                                    </div>
                                </div>
                                <div class="field-container">
                                    <div class="field-label"><label class="required-label" for="first_name">Confirm
                                            Password</label></div>
                                    <div id="password_2" class="input-wrapper sb-input">
                                        <input type="password" placeholder="Enter confirm password" class="form-input"
                                            id="password-field-confirm" />
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                            color="#000000" fill="none"
                                            style="margin-left: -30px;margin-right: 10px; cursor: pointer;"
                                            id="togglePasswordconfirm">
                                            <path
                                                d="M21.544 11.045C21.848 11.4713 22 11.6845 22 12C22 12.3155 21.848 12.5287 21.544 12.955C20.1779 14.8706 16.6892 19 12 19C7.31078 19 3.8221 14.8706 2.45604 12.955C2.15201 12.5287 2 12.3155 2 12C2 11.6845 2.15201 11.4713 2.45604 11.045C3.8221 9.12944 7.31078 5 12 5C16.6892 5 20.1779 9.12944 21.544 11.045Z"
                                                stroke="#141B34" stroke-width="1.5" />
                                            <path
                                                d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                                                stroke="#141B34" stroke-width="1.5" />
                                        </svg>
                                        <p class="error-msg text-danger small m-0"></p>

                                    </div>
                                </div>
                            </div>

                            <div class="auth-options mt-2">
                                <label class="remember-me">
                                    <input type="checkbox" id="terms" class="checkbox form-input" checked
                                        style="margin-top:5px;" />
                                    <span class="remember-text">Click Here To Accept The Platform’s Terms Of Services And
                                        Privacy Policy</span>
                                </label>
                            </div>
                            <div class="sb-errors-area m-0 mb-2 text-end"></div>
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
        <?php echo $apps_login_code ?>
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
            <div class="sb-errors-area"></div></div> -->

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
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                            color="#000000" fill="none"
                                            style="margin-left: -30px;margin-right: 10px; cursor: pointer;"
                                            id="togglePassword">
                                            <path
                                                d="M21.544 11.045C21.848 11.4713 22 11.6845 22 12C22 12.3155 21.848 12.5287 21.544 12.955C20.1779 14.8706 16.6892 19 12 19C7.31078 19 3.8221 14.8706 2.45604 12.955C2.15201 12.5287 2 12.3155 2 12C2 11.6845 2.15201 11.4713 2.45604 11.045C3.8221 9.12944 7.31078 5 12 5C16.6892 5 20.1779 9.12944 21.544 11.045Z"
                                                stroke="#141B34" stroke-width="1.5" />
                                            <path
                                                d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                                                stroke="#141B34" stroke-width="1.5" />
                                        </svg>

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
        <div class="sb-info"></div>
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
                    <div class="right_section forgot-password-section">
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
                                        <input type="email" placeholder="Business Email" id="reset-password-email"
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
                <!-- <img src="<?php echo SB_CLOUD_BRAND_LOGO ?>" /> -->
            <img src="../account/media/logo-new-2.svg" style="width: 100%; alt=" logo">
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
                <input id="reset-password-1" type="password" required autocomplete="off" />
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000"
                    fill="none" style="margin-left: -30px;margin-right: 10px; cursor: pointer;" id="toggleResetPassword">
                    <path
                        d="M21.544 11.045C21.848 11.4713 22 11.6845 22 12C22 12.3155 21.848 12.5287 21.544 12.955C20.1779 14.8706 16.6892 19 12 19C7.31078 19 3.8221 14.8706 2.45604 12.955C2.15201 12.5287 2 12.3155 2 12C2 11.6845 2.15201 11.4713 2.45604 11.045C3.8221 9.12944 7.31078 5 12 5C16.6892 5 20.1779 9.12944 21.544 11.045Z"
                        stroke="#141B34" stroke-width="1.5" />
                    <path
                        d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                        stroke="#141B34" stroke-width="1.5" />
                </svg>
            </div>
            <div class="sb-input">
                <span>
                    <?php sb_e('Repeat password') ?>
                </span>
                <input id="reset-password-2" type="password" required autocomplete="off" />
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000"
                    fill="none" style="margin-left: -30px;margin-right: 10px; cursor: pointer;"
                    id="toggleResetRepeatPassword">
                    <path
                        d="M21.544 11.045C21.848 11.4713 22 11.6845 22 12C22 12.3155 21.848 12.5287 21.544 12.955C20.1779 14.8706 16.6892 19 12 19C7.31078 19 3.8221 14.8706 2.45604 12.955C2.15201 12.5287 2 12.3155 2 12C2 11.6845 2.15201 11.4713 2.45604 11.045C3.8221 9.12944 7.31078 5 12 5C16.6892 5 20.1779 9.12944 21.544 11.045Z"
                        stroke="#141B34" stroke-width="1.5" />
                    <path
                        d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                        stroke="#141B34" stroke-width="1.5" />
                </svg>
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
                <input value="<?php echo CLOUD_URL . '?ref=' . sb_encryption('encrypt', account()['user_id']) ?>" type="text" readonly />
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

<script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
            // Force reload to re-run PHP and reset loader
            window.location.reload();
        } else {
            // Ensure loader is hidden normally
            document.querySelector('.loader')?.classList.add('hidden');
        }
    });
</script>
