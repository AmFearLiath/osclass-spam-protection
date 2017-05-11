<?php
if (!defined('ABS_PATH')) {
    exit('Direct access is not allowed.');
}

function sprot_style() {
    osc_enqueue_style('sp-styles', osc_plugin_url('spamprotection/assets/css/style.css').'style.css');
            
    osc_register_script('spam_protection-frontend', osc_plugin_url('spamprotection/assets/js/script.js') . 'script.js', array('jquery'));
    osc_enqueue_script('spam_protection-frontend');            
    osc_register_script('spam_protection-hideMail', osc_plugin_url('spamprotection/assets/js/jquery.hideMyEmail.min.js') . 'jquery.hideMyEmail.min.js', array('jquery'));
    osc_enqueue_script('spam_protection-hideMail');
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
        
        spam_prot::newInstance()->_increaseUserLogin($email);
        $ip = spam_prot::newInstance()->_IpUserLogin();
                
        $logins = spam_prot::newInstance()->_countLogin($email, 'user');
        $max_logins = spam_prot::newInstance()->_get('sp_security_login_count');
        
        if (!empty($data_token)) {
            ob_get_clean();
            osc_add_flash_error_message(__('<strong>Information!</strong> Your account is disabled due to too much of false login attempts. Please contact support.', 'spamprotection'));    
            header('Location: '.osc_base_url());
            exit;
        } elseif ($logins <= $max_logins && spam_prot::newInstance()->_checkUserLogin($email, $password)) {            
            spam_prot::newInstance()->_resetUserLogin($email);    
        } elseif (!spam_prot::newInstance()->_checkUserLogin($email, $password)) {
            if ($logins >= $max_logins) {
                spam_prot::newInstance()->_handleUserLogin($email, $ip);
                if ($logins == $max_logins) {
                    spam_prot::newInstance()->_informUser($email, 'user');    
                } if (spam_prot::newInstance()->_get('sp_security_login_inform') == '1') {                
                    ob_get_clean();
                    osc_add_flash_error_message(__('<strong>Information!</strong> Your account is disabled due to too much of false login attempts. Please contact support.', 'spamprotection'));
                }
            } elseif (empty($logins) || $login_trys < $max_logins) {
                if (spam_prot::newInstance()->_get('sp_security_login_inform') == '1') {
                    ob_get_clean();
                    osc_add_flash_error_message(sprintf(__('<strong>Warning!</strong> Only %d login attempts remaining', 'spamprotection'), ($max_logins-$logins)));    
                }
            }
            header('Location: '.osc_user_login_url());
            exit;   
        }
        /*
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
        */
    }
}

function sp_add_honeypot_security() {
    echo '<input id="token" type="text" name="token" value="" class="form-control sp_form_field" autocomplete="off">';    
}

function sp_check_registrations() {
    $email = Params::getParam('s_email');
    
    if (($email = filter_var($email, FILTER_VALIDATE_EMAIL)) !== false) {
        
        $check = spam_prot::newInstance()->_get('sp_check_registration');
        $mails = explode(",", spam_prot::newInstance()->_get('sp_check_registration_mails'));
        $domain = substr(strrchr($email, "@"), 1);
        $error = false;
        
        if ($check == '2') {
            if (!in_array($domain, $mails)) { $error = sprintf(__("Sorry, but you cannot use this email address: %s", "spamprotection"), $domain); }   
        } elseif ($check == '3') {
            if (in_array($domain, $mails)) { $error = sprintf(__("Sorry, but you cannot use this email address: %s", "spamprotection"), $domain); }            
        }
    } else { $error = __("Sorry, but you need to use a valid email address.", "spamprotection"); } 
    
    if ($error) {
        ob_get_clean();
        osc_add_flash_error_message($error);
        
        header('Location: '.osc_register_account_url());
        exit;
    }  
}

function sp_unban_cron() {
    spam_prot::newInstance()->_unbanUser();    
}
?>