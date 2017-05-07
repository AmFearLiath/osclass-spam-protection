<?php

if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
}








/*
  FUNCTIONS
 */

function sprot_admin_page_header($message = false) {
    $info = osc_plugin_get_info("spamprotection/index.php");
    echo '
        <h1>' . ($message ? $message : __('Spam Protection', 'spamprotection') . ' <span style="float: right;">v' . $info['version']) . '</span></h1>
        <div style="float: right;">
            <a href="https://forums.osclass.org/plugins/(plugin)-spam-protection/msg148758/#msg148758" target="_blank">OSClass Forum</a> - <a id="sp_review" href="https://market.osclass.org/plugins/security/spam-protection_787" target="_blank">Please Review</a> - <a href="https://github.com/AmFearLiath/osclass-spam-protection" target="_blank">Github</a>
            <div id="sp_review_wrap" style="display: none;">
                <div id="sp_review_inner">
                    <span class="sp_review_close">x</span>
                    <h3 style="text-align: center;">' . __("Thank you for rating this plugin", "spamprotection") . '</h3>
                    <p>' . __("This plugin is and will always be free of charge! If you found any errors, please contact me in OSClass Forum to solve your problems before rating this plugin.", "spamprotection") . '</p>
                    <p>' . __("If you are happy with this plugin and love to use it, please rate it now on OSClass Market!", "spamprotection") . '</p>
                    <p>' . __("Thanks in advance.<br />Liath", "spamprotection") . '</p>
                    <br />
                    <p style="text-align: center;">
                        <a href="https://market.osclass.org/plugins/security/spam-protection_787" target="_blank">
                            <button class="btn btn-submit">' . __("Rate now", "spamprotection") . '</button>
                        </a>
                        <a href="https://forums.osclass.org/plugins/(plugin)-spam-protection/msg148758/#msg148758" target="_blank">
                            <button class="btn">' . __("OSClass Forum", "spamprotection") . '</button>
                        </a>
                        <button class="btn btn-red sp_review_close">' . __("Not now", "spamprotection") . '</button>
                    </p>
                </div>
            </div>
        </div>';
}

function sprot_style_admin() {
    $params = Params::getParamsAsArray();

    osc_enqueue_style('spam_protection-upgrade_css', osc_plugin_url('spamprotection/assets/css/upgrade.css') . 'upgrade.css');
    osc_register_script('spam_protection-upgrade_js', osc_plugin_url('spamprotection/assets/js/upgrade.js') . 'upgrade.js', array('jquery'));
    osc_enqueue_script('spam_protection-upgrade_js');

    if (isset($params['file'])) {
        $plugin = explode("/", $params['file']);
        if ($plugin[0] == 'spamprotection') {

            osc_enqueue_style('spam_protection-styles_admin', osc_plugin_url('spamprotection/assets/css/admin.css') . 'admin.css');

            osc_register_script('spam_protection-admin', osc_plugin_url('spamprotection/assets/js/admin.js') . 'admin.js', array('jquery'));
            osc_enqueue_script('spam_protection-admin');

            osc_add_hook('admin_page_header', 'sprot_admin_page_header');
            osc_remove_hook('admin_page_header', 'customPageHeader');
        }
    }
}

function sprot_admin_menu_init() {
    osc_add_admin_submenu_page('tools', __('Spam Protection', 'spamprotection'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/config.php&tab=settings'), 'sprot_admin_settings', 'administrator');
}

function sprot_admin_menu() {
    echo '<h3><a href="#">' . __('Spam Protection', 'spamprotection') . '</a></h3>
    <ul>
        <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/config.php&tab=settings') . '">&raquo; ' . __('Settings', 'spamprotection') . '</a></li>
        <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/config.php&tab=help') . '">&raquo; ' . __('Help', 'spamprotection') . '</a></li>
    </ul>';
}

osc_add_hook('admin_header', 'sprot_style_admin');
osc_add_hook('init_admin', 'sprot_init');

if (osc_version() >= 300) {
    osc_add_hook('admin_menu_init', 'sprot_admin_menu_init');
} else {
    osc_add_hook('admin_menu', 'sprot_admin_menu');
}
