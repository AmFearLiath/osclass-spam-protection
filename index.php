<?php
/*
Plugin Name: Spam Protection
Plugin URI: http://amfearliath.tk/osclass-spam-protection/
Description: Spam Protection for Osclass. Checks in ads, comments and contact mails for duplicates, banned e-mail addresses and stopwords. Includes a honeypot and many other features. 
Version: 1.5.0
Author: Liath
Author URI: http://amfearliath.tk
Short Name: spamprotection
Plugin update URI: spam-protection
Support URI: https://forums.osclass.org/plugins/(plugin)-spam-protection/

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
Version 2, December 2004

Copyright (C) 2004 Sam Hocevar
14 rue de Plaisance, 75014 Paris, France
Everyone is permitted to copy and distribute verbatim or modified
copies of this license document, and changing it is allowed as long
as the name is changed.

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

0. You just DO WHAT THE FUCK YOU WANT TO.

Changelog

1.0.0 - first published version

1.0.1 - removed non existing class

1.1.0 - Configuration page enhanced for better visuality

1.2.0 - System can now check the MX Record from user e-mail address

1.2.1 - Fix of Stopword search

1.2.2 - PHP Warnings removed, Stopwords and blocked Emails are sorted when settings are saved 

1.3.0 - Changed deprecated functions eregi and split. Improved the stopword mechanism

1.3.1 - Adding extra warning, before editing htaccess file. Little changes to some url's for saving the settings.

1.3.2 - Added optional search for duplicates in descriptions. Searchalgorythm improved. Configuration page changed.

1.3.3 – Global var changed, to prevent error messages

1.3.4 - Stopwords now shown, if ad was blocked for this reason, Search for duplicates improved, translations corrected

1.4.0 - New selectable method added in search for duplicates, item comment protection added, translations corrected. Help section redesigned.

1.4.1 - Wrong button in check ads page removed

1.5.0 - Security settings for login and form protection added 
*/

require_once('classes/class.spamprotection.php');
$sp = new spam_prot;

if (Params::getParam('spam') == 'activate') {
    $sp->_spamAction('activate', Params::getParam('item'));    
} elseif (Params::getParam('spam') == 'block') {
    $sp->_spamAction('block', Params::getParam('mail'));    
}

if (Params::getParam('spamcomment') == 'activate') {
    $sp->_spamActionComments('activate', Params::getParam('id'));    
} elseif (Params::getParam('spam') == 'block') {
    $sp->_spamActionComments('spamcomment', Params::getParam('id'));    
} 

if (Params::getParam('page') == 'sp_activate_account') {
    $email = Params::getParam('email');
    $token = Params::getParam('token');
    $user = User::newInstance()->findByEmail($email);
    
    if (md5($user['s_secret']) == $token) {
        $sp->_resetUserLogin($email);
        
        ob_get_clean();
        osc_add_flash_ok_message(__('<strong>Info!</strong> Your account has been reactivated, you can login as usual.', 'spamprotection'));
    
        header('Location: '.osc_user_login_url());
        exit;    
    }        
}

// User wants to delete his contact mail
$delete_mail = Params::getParam('delete_contact_mail');
if (is_numeric($delete_mail)) {
    $token = Params::getParam('token');
    if ($sp->_deleteMailByUser($delete_mail, $token)) {
        header('Location: '.osc_item_url());
        exit;
    }    
}

/* HOOKS */
osc_register_plugin(osc_plugin_path(__FILE__), 'sprot_install');
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'sprot_uninstall');    
osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'sprot_configuration');

osc_add_hook('header', 'sprot_style');
osc_add_hook('admin_header', 'sprot_style_admin');
osc_add_hook('init_admin', 'sprot_init');

if (osc_version() >= 300) {
    osc_add_hook('admin_menu_init', 'sprot_admin_menu_init');
} else {
    osc_add_hook('admin_menu', 'sprot_admin_menu');
}

if (spam_prot::newInstance()->_get('sp_activate') == '1') {
    osc_add_hook('posted_item', 'sp_check_item');                        
    osc_add_hook('edited_item', 'sp_check_item');
}

if (spam_prot::newInstance()->_get('sp_comment_activate') == '1') {
    osc_add_hook('add_comment', 'sp_check_comment');                        
    osc_add_hook('edit_comment', 'sp_check_comment');
}

if (spam_prot::newInstance()->_get('sp_honeypot') == '1') {
    osc_add_hook('item_form', 'sp_add_honeypot');
    osc_add_hook('item_edit', 'sp_add_honeypot');
}

osc_add_hook('item_contact_form', 'sp_contact_form');
osc_add_hook('delete_comment', 'sp_delete_comment');
osc_add_hook('actions_manage_items', 'sp_compare_items');
osc_add_hook('hook_email_item_inquiry', 'sp_check_contact_item', 1);
osc_add_hook('hook_email_contact_user', 'sp_check_contact_user', 1);

if ($sp->_get('sp_security_activate') == '1') {
    osc_add_hook('before_validating_login', 'sp_check_user_login', 1);
}

if ($sp->_get('sp_security_activate') == '1') {
    if (spam_prot::newInstance()->_get('sp_security_login_hp') == '1') {
        osc_add_hook('user_login_form', 'sp_add_honeypot_security');
    }
    if (spam_prot::newInstance()->_get('sp_security_register_hp') == '1') {
        osc_add_hook('user_register_form', 'sp_add_honeypot_security');
    }
    if (spam_prot::newInstance()->_get('sp_security_recover_hp') == '1') {
        osc_add_hook('user_recover_form', 'sp_add_honeypot_security');
    }
}

/*
    FUNCTIONS
*/
function sprot_admin_page_header($message = false) {
    $info = osc_plugin_get_info("spamprotection/index.php");   
        echo '
        <h1>'.($message ? $message : __('Spam Protection', 'spamprotection'). ' <span style="float: right;">v'.$info['version']).'</span></h1>
        <div style="float: right;">
            <a href="https://forums.osclass.org/plugins/(plugin)-spam-protection/msg148758/#msg148758" target="_blank">OSClass Forum</a> - <a id="sp_review" href="https://market.osclass.org/plugins/security/spam-protection_787" target="_blank">Please Review</a>
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

function sprot_style() {
    osc_enqueue_style('sp-styles', osc_plugin_url('spamprotection/assets/css/style.css').'style.css');
            
    osc_register_script('spam_protection-frontend', osc_plugin_url('spamprotection/assets/js/script.js') . 'script.js', array('jquery'));
    osc_enqueue_script('spam_protection-frontend');            
    osc_register_script('spam_protection-hideMail', osc_plugin_url('spamprotection/assets/js/jquery.hideMyEmail.min.js') . 'jquery.hideMyEmail.min.js', array('jquery'));
    osc_enqueue_script('spam_protection-hideMail');
}

function sprot_style_admin() {
    $params = Params::getParamsAsArray();    
    if (isset($params['file'])) {
        $plugin = explode("/", $params['file']);
        if ($plugin[0] == 'spamprotection') {
            
            osc_enqueue_style('spam_protection-styles_admin', osc_plugin_url('spamprotection/assets/css/admin.css').'admin.css');
            
            osc_register_script('spam_protection-admin', osc_plugin_url('spamprotection/assets/js/admin.js') . 'admin.js', array('jquery'));
            osc_enqueue_script('spam_protection-admin');
            
            osc_add_hook('admin_page_header','sprot_admin_page_header');
            osc_remove_hook('admin_page_header', 'customPageHeader');    
        }    
    }    
}

function sprot_configuration() {
    osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/admin/config.php&tab=settings');
}

function sprot_init() {
    spam_prot::newInstance()->_admin_menu_draw();
}

function sprot_admin_menu_init() {
    osc_add_admin_submenu_page('tools', __('Spam Protection', 'spamprotection'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/config.php&tab=settings'), 'sprot_admin_settings', 'administrator');
}

function sprot_admin_menu() {
    echo '<h3><a href="#">'.__('Spam Protection', 'spamprotection').'</a></h3>
    <ul>
        <li><a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/config.php&tab=settings') . '">&raquo; '.__('Settings', 'spamprotection').'</a></li>
        <li><a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/config.php&tab=help') . '">&raquo; '.__('Help', 'spamprotection').'</a></li>
    </ul>';
}

function sp_check_item($item) {
    $user = osc_logged_user_id();
    $check = spam_prot::newInstance()->_checkForSpam($item);
    if (is_array($check)) {
        spam_prot::newInstance()->_markAsSpam($check['params'], $check['reason']);
    }
}

function sp_check_comment($id) {
    $user = osc_logged_user_id();
    $check = spam_prot::newInstance()->_checkComment($id);
    if (is_array($check)) {
        spam_prot::newInstance()->_markCommentAsSpam($check['params'], $check['reason']);
    }
}

function sp_delete_comment($id) {
    $table = spam_prot::newInstance()->_table_sp_comments;
    spam_prot::newInstance()->dao->delete($table, 'fk_i_comment_id = '.$id);
}

function sp_add_honeypot() {
    $hp = spam_prot::newInstance()->_get('honeypot_name');
    if (empty($hp)) {
        $hp = 'sp_price_range';
    }
    echo '<input type="text" name="'.$hp.'" class="sp_form_field" value="" />';    
}

function sp_contact_form() {
    if (spam_prot::newInstance()->_get('sp_contact_honeypot') == '1') {
        $hpn = spam_prot::newInstance()->_get('contact_honeypot_name');
        $hpv = spam_prot::newInstance()->_get('contact_honeypot_value');
        
        if (empty($hpn)) {
            $hp = 'yourDate';
        } if (empty($hpv)) {
            $hp = 'asap';
        }
        echo '
            <input type="text" id="'.$hpn.'" name="'.$hpn.'" class="sp_form_field" value="" />
            <label class="sp_form_field">
                Please Solve: 3+4
                <input type="text" id="bot_check" name="captcha" value="" />
            </label>
            <script>
                $(document).ready(function(){
                    $("#'.$hpn.'").val("'.$hpv.'");
                });
            </script>
            ';
    }
    if (osc_logged_user_id()) {
        echo '<input type="text" name="user_id" class="sp_form_field" value="'.osc_logged_user_id().'" />';
    }    
}

function sp_compare_items($options, $item) {
    $return = $options;
    if ($item['b_spam'] == '1') {        
        $return[] = '<a href="'.osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/check.php&itemid='.$item['pk_i_id']).'">'.__('Check Spam', 'spamprotection').'</a>';
    }
    return $return;
}

function sp_check_contact_item($data) {
                    
    $item  = Item::newInstance()->findByPrimaryKey($data['item']['fk_i_item_id']);
    View::newInstance()->_exportVariableToView('item', $item);
    
    $params = Params::getParamsAsArray();    
    $check = spam_prot::newInstance()->_checkContact($params);
        
    if (is_array($check)) {        
        $uniqid = uniqid();
        spam_prot::newInstance()->_markContactAsSpam($check['params'], $check['reason'], $uniqid);
        
        $contactID = spam_prot::newInstance()->_searchSpamContact($uniqid);
        osc_add_flash_error_message(sprintf(__("Your email must be verified by a moderator because it has been identified as spam. After successful verification we will forward your e-mail to the user. Click Delete if you do not want your message to be moderated. <a href='%s'>Delete</a>", "spamprotection"), '/?delete_contact_mail='.$contactID.'&token='.$uniqid));
        
        header('Location: '.osc_item_url());
        exit;
    }       
}

function sp_check_contact_user($id, $yourEmail, $yourName, $phoneNumber, $message) {
    $data = array(
        'id'          => $id,
        'yourEmail'   => $yourEmail,
        'yourName'    => $yourName,
        'message'     => $message,
        'phoneNumber' => $phoneNumber
    );
    sp_check_contact_item($data);    
}

function sp_check_user_login() {
    $token = Params::getParam('token');
    $action = Params::getParam('action');
    $email = Params::getParam('email');
    $password = Params::getParam('password', false, false);

    if ($action == 'login_post' && !empty($email) && !empty($password)) {        
        $logins = spam_prot::newInstance()->_countUserLogin($email);
        $max_logins = spam_prot::newInstance()->_get('sp_security_login_count');
        
        if (!empty($logins) && $logins >= $max_logins) {
            
            spam_prot::newInstance()->_handleUserLogin($email, $logins);
            
            if (spam_prot::newInstance()->_get('sp_security_login_inform') == '1') {                
                ob_get_clean();
                osc_add_flash_error_message(sprintf(__('<strong>Information!</strong> Your account is suspended due to too much of false login attempts. Please contact support.', 'spamprotection'), ($max_logins-$logins)));
            }
            
            spam_prot::newInstance()->_informUser($email);
            header('Location: '.osc_user_login_url());
            exit;                
        } if (!spam_prot::newInstance()->_checkUserLogin($email, $password) || !empty($token)) {
            
            spam_prot::newInstance()->_increaseUserLogin($email, $logins);
            $logins = spam_prot::newInstance()->_countUserLogin($email);
            
            if (spam_prot::newInstance()->_get('sp_security_login_inform') == '1') {
                ob_get_clean();
                osc_add_flash_error_message(sprintf(__('<strong>Warning!</strong> Only %d login attempts remaining', 'spamprotection'), ($max_logins-$logins)));
            }
            
            header('Location: '.osc_user_login_url());
            exit;
        } else {            
            spam_prot::newInstance()->_resetUserLogin($email);    
        }
    }
}

function sp_add_honeypot_security() {
    echo '<input id="token" type="text" name="token" value="" class="form-control sp_form_field" autocomplete="off">';    
}
?>
