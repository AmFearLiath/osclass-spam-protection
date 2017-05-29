<?php
if (!defined('ABS_PATH')) {
    exit('Direct access is not allowed.');
}

$sp = new spam_prot;
$page = Params::getParam('page');
$ipBan = Params::getParam('addIpBan');
if (($page == 'items' || $page == 'users')) {
    if ($page == 'items') {
        $item = Item::newInstance()->findByPrimaryKey($ipBan);
    } if (isset($item['s_ip']) && !empty($item['s_ip'])) {
        $sp->_doIpBan('add', $item['s_ip']);    
    }
    
       
}

if (Params::getParam('sp_upgrade') == 'upgrade') {
    $sp->_upgradeNow();   
}

if (Params::getParam('spam') == 'activate') {
    $ip = spam_prot::newInstance()->_IpUserLogin();
    $sp->_spamAction('activate', Params::getParam('item'));    
} elseif (Params::getParam('spam') == 'block') {
    $ip = spam_prot::newInstance()->_IpUserLogin();
    $sp->_spamAction('block', Params::getParam('mail'), $ip);    
} elseif (Params::getParam('spam') == 'ban') {
    $ip = spam_prot::newInstance()->_IpUserLogin();
    $sp->_spamAction('ban', Params::getParam('mail'), $ip);    
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
        spam_prot::newInstance()->_addGlobalLog('User account reactivated by user: ', $email, 'User');
        header('Location: '.osc_user_login_url());
        exit;    
    }        
}

if (Params::getParam('adduser') && Params::getParam('user')) {
    $sp->_userManage(Params::getParam('adduser'), Params::getParam('user'));    
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
?>
