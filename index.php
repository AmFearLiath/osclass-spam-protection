<?php
/*
Plugin Name: Spam Protection
Plugin URI: http://amfearliath.tk/osclass-spam-protection/
Description: Spam Protection for Osclass. Checks in ads, comments and contact mails for duplicates, banned e-mail addresses and stopwords. Includes a honeypot and many other features. 
Version: 1.6.0 Beta
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

1.5.1 - Removed Email ban for form protection

1.5.2 - Added User ban to check ads page, fix problem with clicking id on check ads page, added time range for search in duplicates, added cron to automatically unban user after defined time 

1.5.3 - Added Ban overview, some code cleanings, correcting translations 

1.6.0 - Redesign configuration area, added admin login protection (need some fixes), Import/Export for settings and database (import need to be completed) 
*/

define('SPP_PATH', dirname(__FILE__) . '/');
define('SPP_URL', osc_plugin_url(__FILE__));

require('classes/class.spamprotection.php'); 
require('functions/index.php');
 
$sp = new spam_prot;

/* HOOKS */
osc_register_plugin(osc_plugin_path(__FILE__), 'sprot_install');

if (OC_ADMIN) {
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'sprot_uninstall');    
    osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'sprot_configuration');
    
    osc_add_hook('admin_header', 'sprot_style_admin');
    osc_add_hook('admin_footer', 'sprot_style_admin_footer');
    osc_add_hook('init_admin', 'sprot_init');
    
    if (osc_version() >= 300) {
        osc_add_hook('admin_menu_init', 'sprot_admin_menu_init');
    } else {
        osc_add_hook('admin_menu', 'sprot_admin_menu');
    }    
}


osc_add_hook('header', 'sprot_style');

if (spam_prot::newInstance()->_get('sp_activate') == '1') {
    osc_add_hook('posted_item', 'sp_check_item');                        
    osc_add_hook('edited_item', 'sp_check_item');
}

if (spam_prot::newInstance()->_get('sp_comment_activate') == '1') {
    osc_add_hook('add_comment', 'sp_check_comment');                        
    osc_add_hook('edit_comment', 'sp_check_comment');
}
if (osc_is_ad_page()) {
    if (spam_prot::newInstance()->_get('sp_honeypot') == '1') {
        osc_add_hook('item_form', 'sp_add_honeypot');
        osc_add_hook('item_edit', 'sp_add_honeypot');
    }
    osc_add_hook('item_contact_form', 'sp_contact_form');
}


osc_add_hook('delete_comment', 'sp_delete_comment');
osc_add_hook('actions_manage_items', 'sp_compare_items');
osc_add_hook('hook_email_item_inquiry', 'sp_check_contact_item', 1);
osc_add_hook('hook_email_contact_user', 'sp_check_contact_user', 1);

if ($sp->_get('sp_security_activate') == '1') {
    
    osc_add_hook('before_validating_login', 'sp_check_user_login', 1);
    
    if (spam_prot::newInstance()->_get('sp_security_login_hp') == '1') {
        osc_add_hook('user_login_form', 'sp_add_honeypot_security', 1);
    }
    if (spam_prot::newInstance()->_get('sp_security_register_hp') == '1') {
        osc_add_hook('user_register_form', 'sp_add_honeypot_security', 1);
    }
    if (spam_prot::newInstance()->_get('sp_security_recover_hp') == '1') {
        osc_add_hook('user_recover_form', 'sp_add_honeypot_security', 1);
    }
    
    if (spam_prot::newInstance()->_get('sp_security_login_unban') > '0') {
        if (spam_prot::newInstance()->_get('sp_security_login_cron') == '1') {
            osc_add_hook('cron_hourly', 'sp_unban_cron');
        } elseif (spam_prot::newInstance()->_get('sp_security_login_cron') == '2') {
            osc_add_hook('cron_daily', 'sp_unban_cron');
        } elseif (spam_prot::newInstance()->_get('sp_security_login_cron') == '3') {
            osc_add_hook('cron_weekly', 'sp_unban_cron');
        }
    }
}
    
if ($sp->_get('sp_admin_activate') == '1') {    
    osc_add_hook('before_login_admin', 'sp_check_admin_login', 0);
    
    if (spam_prot::newInstance()->_get('sp_admin_login_hp') == '1') {
        osc_add_hook('login_admin_form', 'sp_admin_login', 1);
    }
    
    if (spam_prot::newInstance()->_get('sp_admin_login_unban') > '0') {
        if (spam_prot::newInstance()->_get('sp_admin_login_cron') == '1') {
            osc_add_hook('cron_hourly', 'sp_unban_cron_admin');
        } elseif (spam_prot::newInstance()->_get('sp_admin_login_cron') == '2') {
            osc_add_hook('cron_daily', 'sp_unban_cron_admin');
        } elseif (spam_prot::newInstance()->_get('sp_admin_login_cron') == '3') {
            osc_add_hook('cron_weekly', 'sp_unban_cron_admin');
        }
    }    
}


$debug = new Debugger;
    
if (Params::getParam('action') == 'register_post' && $sp->_get('sp_check_registrations') >= '2') {        
    osc_add_hook('before_user_register', 'sp_check_user_registrations', 1);    
}

?>