<?php
if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
} if (!osc_is_admin_user_logged_in()) {
    die;
}

function sprot_admin_page_header($message = false) {
    $info = osc_plugin_get_info("spamprotection/index.php");   
        echo '
        <h1>'.($message ? $message : __('Spam Protection', 'spamprotection'). ' <span style="float: right;">v'.$info['version']).'</span></h1>
        <div style="float: right;">
            <a href="https://forums.osclass.org/plugins/(plugin)-spam-protection/msg148758/#msg148758" target="_blank">OSClass Forum</a> - <a id="sp_review" href="https://market.osclass.org/plugins/security/spam-protection_787" target="_blank">Please Review</a> - <a href="https://github.com/AmFearLiath/osclass-spam-protection" target="_blank">Github</a>
            <div id="sp_review_wrap" style="display: none;">
                <div id="sp_review_inner">
                    <span class="sp_review_close">x</span>
                    <h3 style="text-align: center;">'.__("Thank you for rating this plugin", "spamprotection").'</h3>
                    <p>'.__("This plugin is and will always be free of charge! If you found any errors, please contact me in OSClass Forum to solve your problems before rating this plugin.", "spamprotection").'</p>    
                    <p>'.__("If you are happy with this plugin and love to use it, please rate it now on OSClass Market!", "spamprotection").'</p>    
                    <p>'.__("Thanks in advance.<br />Liath", "spamprotection").'</p>
                    <br />    
                    <p style="text-align: center;">
                        <a href="https://market.osclass.org/plugins/security/spam-protection_787" target="_blank">
                            <button class="btn btn-submit">'.__("Rate now", "spamprotection").'</button>
                        </a>
                        <a href="https://forums.osclass.org/plugins/(plugin)-spam-protection/msg148758/#msg148758" target="_blank">
                            <button class="btn">'.__("OSClass Forum", "spamprotection").'</button>
                        </a>
                        <button class="btn btn-red sp_review_close">'.__("Not now", "spamprotection").'</button>
                    </p>                        
                </div>
            </div>
        </div>';    
}

function sprot_install() {
    spam_prot::newInstance()->_install();
}

function sprot_uninstall() {
    spam_prot::newInstance()->_uninstall();
}

function sprot_style_admin() {
    $params = Params::getParamsAsArray();
    
    osc_enqueue_style('spam_protection-upgrade_css', osc_plugin_url('spamprotection/assets/css/upgrade.css').'upgrade.css');
    osc_register_script('spam_protection-upgrade_js', osc_plugin_url('spamprotection/assets/js/upgrade.js') . 'upgrade.js', array('jquery'));
    osc_enqueue_script('spam_protection-upgrade_js');
        
    if (isset($params['file'])) {
        $plugin = explode("/", $params['file']);
        $file = explode(".", $plugin[2]);
        if ($plugin[0] == 'spamprotection') {
            
            osc_enqueue_style('spam_protection-styles_admin', osc_plugin_url('spamprotection/assets/css/admin.css').'admin.css');
            
            osc_register_script('spam_protection-admin', osc_plugin_url('spamprotection/assets/js/admin.js') . 'admin.js', array('jquery'));
            osc_enqueue_script('spam_protection-admin');
            
            osc_add_hook('admin_page_header','sprot_admin_page_header');
            osc_remove_hook('admin_page_header', 'customPageHeader');    
        }
        
        if ($file[0] == 'config') {
            /*osc_enqueue_style('spam_protection-uploader', osc_plugin_url('spamprotection/assets/css/filedrop.css').'filedrop.css');
            osc_register_script('spam_protection-uploader', osc_plugin_url('spamprotection/assets/js/custom-file-input.js') . 'custom-file-input.js', array('jquery'));
            osc_enqueue_script('spam_protection-uploader');
            osc_register_script('spam_protection-uploader_jquery', osc_plugin_url('spamprotection/assets/js/jquery.custom-file-input.js') . 'jquery.custom-file-input.js', array('jquery'));
            osc_enqueue_script('spam_protection-uploader_jquery');*/       
        }   
    }    
}

function sprot_configuration() {
    osc_admin_render_plugin(SPP_PATH . '/admin/config.php&tab=settings');
}

function sprot_init() {      
    spam_prot::newInstance()->_admin_menu_draw();
    $check = spam_prot::newInstance()->_upgradeCheck();
        
    if (!$check) {        
        spam_prot::newInstance()->_upgradeDatabaseInfo($check);
    }
}

function sprot_admin_menu_init() {
    osc_add_admin_submenu_page('tools', __('Spam Protection', 'spamprotection'), osc_admin_render_plugin_url(SPP_PATH . 'admin/config.php&tab=settings'), 'sprot_admin_settings', 'administrator');
}

function sprot_admin_menu() {
    echo '<h3><a href="#">'.__('Spam Protection', 'spamprotection').'</a></h3>
    <ul>
        <li><a href="'.osc_admin_render_plugin_url(SPP_PATH.'admin/config.php&tab=settings') . '">&raquo; '.__('Settings', 'spamprotection').'</a></li>
        <li><a href="'.osc_admin_render_plugin_url(SPP_PATH.'admin/config.php&tab=help') . '">&raquo; '.__('Help', 'spamprotection').'</a></li>
    </ul>';
}

function sp_compare_items($options, $item) {
    $return = $options;
    if ($item['b_spam'] == '1') {        
        $return[] = '<a href="'.osc_admin_render_plugin_url(SPP_PATH . 'admin/check.php&itemid='.$item['pk_i_id']).'">'.__('Check Spam', 'spamprotection').'</a>';
    }
    return $return;
}

function sp_check_admin_login() {    
    $data = Params::getParamsAsArray();
    $data_token = $data['token']; $data_user = $data['user']; $data_pass = $data['password'];
    
    $admin = Admin::newInstance()->findByUsername($data_user);
    $admin_name = $admin['s_username']; $admin_email = $admin['s_email'];
    $ip = spam_prot::newInstance()->_IpUserLogin();
    $max_logins = spam_prot::newInstance()->_get('sp_admin_login_count');
    $login_trys = spam_prot::newInstance()->_countLogin((!empty($admin_name) ? $admin_name : $data_user), 'admin');
    
    if (!$admin) {            
        spam_prot::newInstance()->_increaseAdminLogin($data_user);
        
        if (!empty($login_trys) && $login_trys > $max_logins) {
            spam_prot::newInstance()->_handleAdminLogin($data_user, $ip);    
        } if (spam_prot::newInstance()->_get('sp_admin_login_inform') == '1') {                
            ob_get_clean();
            osc_add_flash_error_message(__('<strong>Information!</strong> Your account is disabled due to too much of false login attempts. Please contact the webmaster.', 'spamprotection'), 'admin');
        }
        
        header('Location: '.osc_admin_base_url(true)."?page=login");
        exit;    
    } elseif ($login_trys >= $max_logins || (!spam_prot::newInstance()->_checkAdminLogin($admin, $data_pass) && !spam_prot::newInstance()->_checkAdminBan($ip)) || !empty($data_token)) {
        
        if (empty($login_trys) || $login_trys <= $max_logins) {            
            spam_prot::newInstance()->_increaseAdminLogin($admin_name);
        }        
        if (empty($login_trys) || $login_trys < $max_logins) {            
            if (spam_prot::newInstance()->_get('sp_admin_login_inform') == '1') {
                ob_get_clean();
                osc_add_flash_error_message(sprintf(__('<strong>Warning!</strong> Only %d login attempts remaining', 'spamprotection'), ($max_logins-$login_trys)), 'admin');    
            }            
        } else {
            spam_prot::newInstance()->_handleAdminLogin($admin_name, $ip);
            if ($login_trys == $max_logins) {
                spam_prot::newInstance()->_increaseAdminLogin($data_user);
                spam_prot::newInstance()->_informUser($data_user, 'admin');    
            } if (spam_prot::newInstance()->_get('sp_admin_login_inform') == '1') {                
                ob_get_clean();
                osc_add_flash_error_message(__('<strong>Information!</strong> Your account is disabled due to too much of false login attempts. Please contact the webmaster.', 'spamprotection'), 'admin');
            }               
        }
        header('Location: '.osc_admin_base_url(true)."?page=login");
        exit;   
    } else {            
        spam_prot::newInstance()->_resetAdminLogin($data_user);    
    }
}

function sp_admin_login() {
    echo '
    <style>
    .sp_form_field {
        z-index: 999;
        position: absolute;
        height: 0 !important;
        width: 0 !important;
        border: none;
        background: none;
        margin: 0;
        top: -9999;
        left: -9999;
        clear: both;
        font-size: 0px;
        line-height: 0px;
    }
    </style>
    <script>
    jQuery(function($){
        $(document).ready(function(){
            $("#token").addClass("sp_form_field");
        });
    });
    </script>
    <input id="token" type="text" name="token" value="" class="form-control" autocomplete="off">
';    
}

function sp_unban_cron_admin() {
    spam_prot::newInstance()->_unbanAdmin();    
}
?>