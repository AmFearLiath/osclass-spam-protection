<?php if ( (!defined('ABS_PATH')) ) exit('Direct access is not allowed.');


function sprot_style() {
    osc_enqueue_style('sp-styles', osc_plugin_url('spamprotection/assets/css/style.css').'style.css');

    osc_register_script('spam_protection-frontend', osc_plugin_url('spamprotection/assets/js/script.js') . 'script.js', array('jquery'));
    osc_enqueue_script('spam_protection-frontend');
    osc_register_script('spam_protection-hideMail', osc_plugin_url('spamprotection/assets/js/jquery.hideMyEmail.min.js') . 'jquery.hideMyEmail.min.js', array('jquery'));
    osc_enqueue_script('spam_protection-hideMail');
}


function sprot_configuration() {
    osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/admin/config.php&tab=settings');
}

function sprot_init() {
    spam_prot::newInstance()->_admin_menu_draw();
    $check = spam_prot::newInstance()->_upgradeCheck();

    if (!$check) {
        spam_prot::newInstance()->_upgradeDatabaseInfo($check);
    }
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
        $logins = spam_prot::newInstance()->_countLogin($email, 'user');
        $max_logins = spam_prot::newInstance()->_get('sp_security_login_count');

        if (!empty($logins) && ($logins >= $max_logins || $logins == $max_logins)) {
            $ip = spam_prot::newInstance()->_IpUserLogin();
            spam_prot::newInstance()->_handleUserLogin($email, $logins, $ip);

            if ($logins == $max_logins) {
                spam_prot::newInstance()->_increaseUserLogin($email);
                spam_prot::newInstance()->_informUser($email, 'user');
            }

            if (spam_prot::newInstance()->_get('sp_security_login_inform') == '1') {
                ob_get_clean();
                osc_add_flash_error_message(__('<strong>Information!</strong> Your account is disabled due to too much of false login attempts. Please contact support.', 'spamprotection'));
            }

            header('Location: '.osc_user_login_url());
            exit;
        } elseif (!spam_prot::newInstance()->_checkUserLogin($email, $password) || !empty($token)) {

            spam_prot::newInstance()->_increaseUserLogin($email);
            $logins = spam_prot::newInstance()->_countLogin($email, 'user');

            if (spam_prot::newInstance()->_get('sp_security_login_inform') == '1') {
                ob_get_clean();
                if ($logins == $max_logins) {
                    osc_add_flash_error_message(__('<strong>Information!</strong> Your account is disabled due to too much of false login attempts. Please contact support.', 'spamprotection'));
                } else {
                    osc_add_flash_error_message(sprintf(__('<strong>Warning!</strong> Only %d login attempts remaining', 'spamprotection'), ($max_logins-$logins)));
                }
            }

            header('Location: '.osc_user_login_url());
            exit;
        } else {
            spam_prot::newInstance()->_resetUserLogin($email);
        }
    }
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

function sp_add_honeypot_security() {
    echo '<input id="token" type="text" name="token" value="" class="form-control sp_form_field" autocomplete="off">';
}

function sp_unban_cron() {
    spam_prot::newInstance()->_unbanUser();
}

function sp_unban_cron_admin() {
    spam_prot::newInstance()->_unbanAdmin();
}


