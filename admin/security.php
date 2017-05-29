<?php
if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
} if (!osc_is_admin_user_logged_in()) {
    die;
}

$sp = new spam_prot;
$sub = Params::getParam('sub');
$table = Params::getParam('table');

?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link <?php echo (empty($sub) || $sub == 'user' ? 'current' : ''); ?>" data-tab="sp_security_mainfeatures_user"><a><?php _e('User Protection', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (!isset($sub) || $sub == 'admin' ? 'current' : ''); ?>" data-tab="sp_security_mainfeatures_admin"><a><?php _e('Admin Protection', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (!isset($sub) || $sub == 'register' ? 'current' : ''); ?>" data-tab="sp_security_mainfeatures_register"><a><?php _e('Registrations', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (!isset($sub) || $sub == 'badtrusted' ? 'current' : ''); ?>" data-tab="sp_security_badtrusted"><a><?php _e('Bad/Trusted User', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (!isset($sub) || $sub == 'ipban' ? 'current' : ''); ?>" data-tab="sp_security_ipban"><a><?php _e('IP Ban', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (!isset($sub) || $sub == 'cleaner' ? 'current' : ''); ?>" data-tab="sp_security_cleaner"><a><?php _e('Cleaner', 'spamprotection'); ?></a></li>
        <li class="subtab-link"><button type="submit" class="btn btn-info"><?php _e('Save', 'spamprotection'); ?></button></li>
    </ul>

    <div id="sp_security_options" class="sp_security_options">
        <div id="sp_security_mainfeatures_user" class="subtab-content <?php echo (empty($sub) || $sub == 'user' ? 'current' : ''); ?> <?php echo (empty($data['sp_security_activate']) || $data['sp_security_activate'] == '0' ? 'disabled' : 'enabled'); ?>">

            <fieldset>
                <legend><?php _e("User Protection", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <input type="checkbox" name="sp_security_activate" value="1"<?php if (!empty($data['sp_security_activate'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Activate the Form Protection', 'spamprotection'); ?>
                    </label><br />
                    <small><?php _e('This Option activates the whole form protection. Some features are optional and can be de/activated separetely', 'spamprotection'); ?></small>
                </div>
            </fieldset>

            <fieldset>
                <legend><?php _e("False logins", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <div class="halfrow" style="padding: 0;">
                        <label>
                            <?php _e('Max amount of wrong logins', 'spamprotection'); ?>
                        </label><br />
                        <input type="text" name="sp_security_login_count" style="width: 50px;" value="<?php echo (isset($data['sp_security_login_count']) ? $data['sp_security_login_count'] : '3'); ?>" />
                        <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">in</span>
                        <input type="text" name="sp_security_login_time" style="width: 50px;" value="<?php echo (isset($data['sp_security_login_time']) ? $data['sp_security_login_time'] : '30'); ?>" />
                        <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">min</span>
                    </div>
                    <div class="halfrow" style="padding: 0;">
                        <div class="floating">                        
                            <label>
                                <?php _e('Unban accounts after', 'spamprotection'); ?>
                            </label><br />
                            <input type="text" name="sp_security_login_unban" style="width: 50px;" value="<?php echo (isset($data['sp_security_login_unban']) ? $data['sp_security_login_unban'] : '180'); ?>" />        
                            <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">mins</span>
                        </div>
                        <div class="floating">
                            <label>
                                <?php _e('Run cron...', 'spamprotection'); ?>
                            </label><br />
                            <select id="sp_security_login_cron" name="sp_security_login_cron">
                                <option value="1"<?php if (empty($data['sp_security_login_cron']) || $data['sp_security_login_cron'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Every hour', 'spamprotection'); ?></option>
                                <option value="2"<?php if (!empty($data['sp_security_login_cron']) && $data['sp_security_login_cron'] == '2') { echo ' selected="selected"'; } ?>><?php _e('One time per day', 'spamprotection'); ?></option>
                                <option value="3"<?php if (!empty($data['sp_security_login_cron']) && $data['sp_security_login_cron'] == '3') { echo ' selected="selected"'; } ?>><?php _e('One time per week', 'spamprotection'); ?></option>
                            </select>        
                        </div>                
                        <div style="clear: both;"></div>
                        <small><?php _e('Use 0 minutes to disable auto unban', 'spamprotection'); ?></small>
                    </div>
                    
                    <div style="clear: both;"></div>
                    
                </div>
            </fieldset>
            
            <fieldset>
                <legend><?php _e("Login limit reached", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <?php _e('Action done after false logins', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_security_login_action" name="sp_security_login_action">
                        <option value="1"<?php if (empty($data['sp_security_login_action']) || $data['sp_security_login_action'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Disable user account', 'spamprotection'); ?></option>
                        <option value="2"<?php if (!empty($data['sp_security_login_action']) && $data['sp_security_login_action'] == '2') { echo ' selected="selected"'; } ?>><?php _e('Add IP to Banlist', 'spamprotection'); ?></option>
                        <option value="3"<?php if (!empty($data['sp_security_login_action']) && $data['sp_security_login_action'] == '3') { echo ' selected="selected"'; } ?>><?php _e('Both', 'spamprotection'); ?></option>
                    </select>
                </div>
            </fieldset>            
            
            <fieldset>
                <legend><?php _e("Inform user", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <input type="checkbox" name="sp_security_login_inform" value="1"<?php if (!empty($data['sp_security_login_inform'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Inform user how many tries are remaining', 'spamprotection'); ?>
                    </label><br />
                    <small><?php _e('This option allows to inform the user after each false login, how many tries are remainig, before your choosen action is done', 'spamprotection'); ?></small>
                </div>
            </fieldset>

            <fieldset>
                <legend><?php _e("Honeypot", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label><?php _e('Add Honeypot to login/register/recover forms', 'spamprotection'); ?></label><br />
                    <small><?php _e('This Option ads a hidden form field to the login/register/recovery forms. After a bot tap into your trap, action following rules you have set above.', 'spamprotection'); ?></small><br />
                    <div class="floating">
                        <label>
                            <input type="checkbox" name="sp_security_login_hp" value="1"<?php if (!empty($data['sp_security_login_hp'])) { echo ' checked="checked"'; } ?> />
                            <?php _e('Login', 'spamprotection'); ?>
                        </label>
                    </div>
                    
                    <div class="floating">
                        <label>
                            <input type="checkbox" name="sp_security_register_hp" value="1"<?php if (!empty($data['sp_security_register_hp'])) { echo ' checked="checked"'; } ?> />
                            <?php _e('Registration', 'spamprotection'); ?>
                        </label>
                    </div>
                    
                    <div class="floating">
                        <label>
                            <input type="checkbox" name="sp_security_recover_hp" value="1"<?php if (!empty($data['sp_security_recover_hp'])) { echo ' checked="checked"'; } ?> />
                            <?php _e('Password recovery', 'spamprotection'); ?>
                        </label>
                    </div>
                    
                    <div style="clear: both;"></div>
       
                    <br />
                    <div id="sp_security_login_honeypots"<?php if ((empty($data['sp_security_login_hp']) || $data['sp_security_login_hp'] == '0') && (empty($data['sp_security_recover_hp']) || $data['sp_security_recover_hp'] == '0')) { echo ' style="display: none;"'; } ?>>    
                        <br /><hr /><br />
                        <div style="margin-bottom: 15px;">
                            <div class="floating">
                                <i class="sp-icon attention margin-right"></i>
                            </div> 
                            <div class="floating">
                                <?php _e('To make this honeypot works for login and recover pages, you need to add one line of code in each of this files.', 'spamprotection'); ?><br />
                                <strong><?php _e('Insert it right before the closing <em>form</em> tag &lt;/form&gt;', 'spamprotection'); ?></strong>
                            </div>
                            <div style="clear: both;"></div> 
                        </div>   
                        
                        <div id="sp_security_login_hp_cont" style="float: left; width: calc(50% - 5px); margin: 0px; padding: 0px 10px 0px 0px;<?php if ((empty($data['sp_security_login_hp']) || $data['sp_security_login_hp'] == '0')) { echo ' display: none;'; } ?>">
                            <strong>../oc-content/themes/yourtheme/user-login.php</strong>
                            <pre><code>&lt;?php osc_run_hook('user_login_form'); ?&gt;</code></pre>
                        
                            <br /><div style="clear: both;"></div><br /><br />
                        
                            <strong style="font-size: 20px;"><?php _e('Example', 'spamprotection'); ?></strong>
                            <pre>
    ...
    &lt;?php osc_run_hook('user_login_form'); ?&gt;
&lt;/form&gt;
                            </pre>
                        </div>

                        <div id="sp_security_recover_hp_cont" style="float: left; width: calc(50% - 5px); margin: 0; padding: 0;<?php if ((empty($data['sp_security_recover_hp']) || $data['sp_security_recover_hp'] == '0')) { echo ' display: none;'; } ?>">
                            <strong>../oc-content/themes/yourtheme/user-recover.php</strong>
                            <pre><code>&lt;?php osc_run_hook('user_recover_form'); ?&gt;</code></pre>
                        
                            <br /><div style="clear: both;"></div><br /><br />
                        
                            <strong style="font-size: 20px;"><?php _e('Example', 'spamprotection'); ?></strong>
                            <pre>
    ...
    &lt;?php osc_run_hook('user_recover_form'); ?&gt;
&lt;/form&gt;
                            </pre>
                        </div>
                        

                    </div>
                                
                </div>
            </fieldset>            

        </div>
    </div>
    
    <div id="sp_admin_options" class="sp_admin_options">
        <div id="sp_security_mainfeatures_admin" class="subtab-content <?php echo (isset($sub) && $sub == 'admin' ? 'current' : ''); ?> <?php echo (empty($data['sp_admin_activate']) || $data['sp_admin_activate'] == '0' ? 'disabled' : 'enabled'); ?>">
            
            <fieldset>
                <legend><?php _e("Admin Protection", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <input type="checkbox" name="sp_admin_activate" value="1"<?php if (!empty($data['sp_admin_activate'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Activate the Admin Protection', 'spamprotection'); ?>
                    </label><br />
                    <small><?php _e('This Option activates the whole admin protection. Some features are optional and can be de/activated separetely', 'spamprotection'); ?></small>
                </div>
            </fieldset>

            <fieldset>
                <legend><?php _e("False logins", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <div class="halfrow" style="padding: 0;">
                        <label>
                            <?php _e('Max amount of wrong logins', 'spamprotection'); ?>
                        </label><br />
                        <input type="text" name="sp_admin_login_count" style="width: 50px;" value="<?php echo (isset($data['sp_admin_login_count']) ? $data['sp_admin_login_count'] : '3'); ?>" />
                        <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">in</span>
                        <input type="text" name="sp_admin_login_time" style="width: 50px;" value="<?php echo (isset($data['sp_admin_login_time']) ? $data['sp_admin_login_time'] : '30'); ?>" />
                        <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">min</span>
                    </div>
                    <div class="halfrow" style="padding: 0;">
                        <div class="floating">                        
                            <label>
                                <?php _e('Unban accounts after', 'spamprotection'); ?>
                            </label><br />
                            <input type="text" name="sp_admin_login_unban" style="width: 50px;" value="<?php echo (isset($data['sp_admin_login_unban']) ? $data['sp_admin_login_unban'] : '180'); ?>" />        
                            <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">mins</span>
                        </div>
                        <div class="floating">
                            <label>
                                <?php _e('Run cron...', 'spamprotection'); ?>
                            </label><br />
                            <select id="sp_admin_login_cron" name="sp_admin_login_cron">
                                <option value="1"<?php if (empty($data['sp_admin_login_cron']) || $data['sp_admin_login_cron'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Every hour', 'spamprotection'); ?></option>
                                <option value="2"<?php if (!empty($data['sp_admin_login_cron']) && $data['sp_admin_login_cron'] == '2') { echo ' selected="selected"'; } ?>><?php _e('One time per day', 'spamprotection'); ?></option>
                                <option value="3"<?php if (!empty($data['sp_admin_login_cron']) && $data['sp_admin_login_cron'] == '3') { echo ' selected="selected"'; } ?>><?php _e('One time per week', 'spamprotection'); ?></option>
                            </select>        
                        </div>                
                        <div style="clear: both;"></div>
                        <small><?php _e('Use 0 minutes to disable auto unban', 'spamprotection'); ?></small>
                    </div>
                    
                    <div style="clear: both;"></div>
                    
                </div>
            </fieldset>
            
            <fieldset>
                <legend><?php _e("Login limit reached", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <?php _e('Action done after false logins', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_admin_login_action" name="sp_admin_login_action">
                        <option value="1"<?php if (empty($data['sp_admin_login_action']) || $data['sp_admin_login_action'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Disable user account', 'spamprotection'); ?></option>
                        <option value="2"<?php if (!empty($data['sp_admin_login_action']) && $data['sp_admin_login_action'] == '2') { echo ' selected="selected"'; } ?>><?php _e('Add IP to Banlist', 'spamprotection'); ?></option>
                        <option value="3"<?php if (!empty($data['sp_admin_login_action']) && $data['sp_admin_login_action'] == '3') { echo ' selected="selected"'; } ?>><?php _e('Both', 'spamprotection'); ?></option>
                    </select>
                </div>
            </fieldset>            
            
            <fieldset>
                <legend><?php _e("Inform user", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <input type="checkbox" name="sp_admin_login_inform" value="1"<?php if (!empty($data['sp_admin_login_inform'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Inform admin how many tries are remaining', 'spamprotection'); ?>
                    </label><br />
                    <small><?php _e('This option allows to inform the admin after each false login, how many tries are remainig, before your choosen action is done', 'spamprotection'); ?></small>
                </div>
            </fieldset>
            
            <fieldset>
                <legend><?php _e("Honeypot", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <input type="checkbox" name="sp_admin_login_hp" value="1"<?php if (!empty($data['sp_admin_login_hp'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Add Honeypot to admin login form', 'spamprotection'); ?>
                    </label><br />
                    <small><?php _e('This Option ads a hidden form field to the admin login forms. After a bot tap into your trap, action following rules you have set above.', 'spamprotection'); ?></small><br />
                </div>
            </fieldset>
            
        </div>
    </div>
    
    <div id="sp_register_options" class="sp_register_options">
        <div id="sp_security_mainfeatures_register" class="subtab-content <?php echo (isset($sub) && $sub == 'register' ? 'current' : ''); ?>">
            
            <fieldset>
                <legend><?php _e("Check registrations", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <?php _e('Select type of registrations check', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_check_registrations" name="sp_check_registrations">
                        <option value="1"<?php if (empty($data['sp_check_registrations']) || $data['sp_check_registrations'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Deactivate', 'spamprotection'); ?></option>
                        <option value="2"<?php if (!empty($data['sp_check_registrations']) && $data['sp_check_registrations'] == '2') { echo ' selected="selected"'; } ?>><?php _e('Allow only following hoster', 'spamprotection'); ?></option>
                        <option value="3"<?php if (!empty($data['sp_check_registrations']) && $data['sp_check_registrations'] == '3') { echo ' selected="selected"'; } ?>><?php _e('Disallow following hoster', 'spamprotection'); ?></option>
                    </select><br />
                    <small><?php _e('This option allows to define which emails can be used for registering an account on your page.', 'spamprotection'); ?></small>
                    
                    <div id="sp_check_registration_mails" class="hiddeninput<?php if (isset($data['sp_check_registrations']) && $data['sp_check_registrations'] == '2' || $data['sp_check_registrations'] == '3') { echo ' visible'; } ?>">
                        <label for="sp_check_registration_mails"><?php _e('Enter email hoster, separated by , (e.g. mail.ru,gmail.com,yahoo.com)', 'spamprotection'); ?></label><br>
                        <textarea class="form-control" name="sp_check_registration_mails" style="height: 150px;"><?php if (!empty($data['sp_check_registration_mails'])) { echo $data['sp_check_registration_mails']; } ?></textarea>
                    </div>
                </div>
            </fieldset>
            
            <fieldset>
                <legend><?php _e("StopForumSpam", "spamprotection"); ?></legend>               
                <div class="row form-group">
                    <div class="halfrow">
                        <label>
                            <input type="checkbox" name="sp_check_stopforumspam_mail" value="1"<?php if (isset($data['sp_check_stopforumspam_mail']) && $data['sp_check_stopforumspam_mail'] == '1') { echo ' checked="checked"'; } ?> />
                            <?php _e('Check email address', 'spamprotection'); ?>
                        </label>
                        <br />
                        <label>
                            <input type="checkbox" name="sp_check_stopforumspam_ip" value="1"<?php if (isset($data['sp_check_stopforumspam_ip']) && $data['sp_check_stopforumspam_ip'] == '1') { echo ' checked="checked"'; } ?> />
                            <?php _e('Check IP', 'spamprotection'); ?>
                        </label>
                        <br />
                        <small><?php _e('This options allows to check used emails and IP\'s against StopForumSpam for your registrations', 'spamprotection'); ?></small>
                        <br /><br /><br />                        
                        <label>
                            <input type="checkbox" name="sp_autoban_stopforumspam" value="1"<?php if (isset($data['sp_autoban_stopforumspam']) && $data['sp_autoban_stopforumspam'] = '1') { echo ' checked="checked"'; } ?> />
                            <?php _e('Add Email or IP to Ban list if found on StopForumSpam', 'spamprotection'); ?>
                        </label>
                        <br />
                        <small><?php _e('This will prevent too much traffic on StopForumSpam and minimize your requests.', 'spamprotection'); ?></small>
                        <br /><br />
                        <div>
                            <div class="floating">                        
                                <label>
                                    <?php _e('Unban accounts after', 'spamprotection'); ?>
                                </label><br />
                                <input type="text" name="sp_stopforum_unban" style="width: 50px;" value="<?php echo (isset($data['sp_stopforum_unban']) ? $data['sp_admin_login_unban'] : '180'); ?>" />        
                                <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">mins</span>
                            </div>
                            <div class="floating" style="float: right;">
                                <label>
                                    <?php _e('Run cron...', 'spamprotection'); ?>
                                </label><br />
                                <select id="sp_stopforum_cron" name="sp_stopforum_cron">
                                    <option value="1"<?php if (empty($data['sp_stopforum_cron']) || $data['sp_stopforum_cron'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Every hour', 'spamprotection'); ?></option>
                                    <option value="2"<?php if (!empty($data['sp_stopforum_cron']) && $data['sp_stopforum_cron'] == '2') { echo ' selected="selected"'; } ?>><?php _e('One time per day', 'spamprotection'); ?></option>
                                    <option value="3"<?php if (!empty($data['sp_stopforum_cron']) && $data['sp_stopforum_cron'] == '3') { echo ' selected="selected"'; } ?>><?php _e('One time per week', 'spamprotection'); ?></option>
                                </select>        
                            </div>                
                            <div style="clear: both;"></div>
                            <small><?php _e('Use 0 minutes to disable auto unban', 'spamprotection'); ?></small>
                        </div>
                    
                    </div>
                    <div id="sp_stopforumspam_settings" class="halfrow"<?php if (!isset($data['sp_check_stopforumspam_mail']) && $data['sp_check_stopforumspam_mail'] != '1' && !isset($data['sp_check_stopforumspam_ip']) && $data['sp_check_stopforumspam_ip'] != '1') { echo ' style="display: none;'; } ?>>
                        <label for="sp_stopforumspam_freq">
                            <?php _e('Max frequency of reports', 'spamprotection'); ?>
                        </label><br />
                        <input type="text" name="sp_stopforumspam_freq" style="width: 50px;" value="<?php echo (isset($data['sp_stopforumspam_freq']) ? $data['sp_stopforumspam_freq'] : '3'); ?>" />
                        <span style="line-height: 28px;"><small><?php _e("(0 - 255)", "spamprotection"); ?></small></span>
                        <br />
                        <label for="sp_stopforumspam_susp">
                            <?php _e('Max percentage of suspiciousness', 'spamprotection'); ?>
                        </label><br />
                        <input type="text" name="sp_stopforumspam_susp" style="width: 50px;" value="<?php echo (isset($data['sp_stopforumspam_susp']) ? $data['sp_stopforumspam_susp'] : '50'); ?>" />
                        <span style="line-height: 28px;"><small><?php _e("(0 = high confidence, 100 = low confidence)", "spamprotection"); ?></small></span>
                        <br /><br />
                        
                        <small><?php _e('Here you can define the max frequency of reports and the percentage of max suspiciousness', 'spamprotection'); ?></small>
                    </div>
                    <div style="clear: both;"></div>                
                </div>
            </fieldset>
            
        </div>
    </div>
    
    <div id="sp_badtrusted_options" class="sp_badtrusted_options">
        <div id="sp_security_badtrusted" class="subtab-content <?php echo (isset($sub) && $sub == 'badtrusted' ? 'current' : ''); ?>">
            
            <fieldset>
                <legend><?php _e("Bad/Trusted User", "spamprotection"); ?></legend>
                <div class="row form-group">
                    <label>
                        <input type="checkbox" name="sp_badtrusted_activate" value="1"<?php if (!empty($data['sp_badtrusted_activate'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Activate the Bad/Trusted User Feature', 'spamprotection'); ?>
                    </label><br />
                    <small>
                        <?php _e('This Features allows you to set user to bad- or trusted lists. There you can define actions they are always trusted or forbidden.', 'spamprotection'); ?>
                        <ul>
                            <li>&raquo; <strong><?php _e('Trusted User', 'spamprotection'); ?></strong> - <?php _e('This actions are always trusted and won\'t checked for spam', 'spamprotection'); ?></li>
                            <li>&raquo; <strong><?php _e('Bad User', 'spamprotection'); ?></strong> - <?php _e('This actions cannot be done anymore, the user will always be blocked for this.', 'spamprotection'); ?></li>
                        </ul>
                    </small>
                </div>
            </fieldset>
            
            <fieldset id="bot_table"<?php echo (empty($data['sp_badtrusted_activate']) || $data['sp_badtrusted_activate'] == '0' ? ' style="display: none;"' : ''); ?>>
                <legend><?php _e("Bad/Trusted User Lists", "spamprotection"); ?></legend>
                <div class="row form-group" style="position: relative;">
                    <ul class="langtabs" style="padding: 0;">
                        <li class="langtab-link <?php echo (empty($table) || $table == 'trusteduser' ? 'current' : ''); ?>" data-tab="trusteduser"><a><?php _e('Trusted User', 'spamprotection'); ?></a></li>
                        <li class="langtab-link <?php echo (isset($table) && $table == 'baduser' ? 'current' : ''); ?>" data-tab="baduser"><a><?php _e('Bad User', 'spamprotection'); ?></a></li>
                    </ul>
                    
                    <a id="add_bad_or_trusted" class="btn btn-green"><?php _e("Organize", "spamprotection"); ?></a>
                    
                    <div id="trusteduser" class="langtab-content <?php echo (empty($table) || $table == 'trusteduser' ? 'current' : ''); ?>">
                        <table class="badtrusted">
                            <thead>
                                <tr>
                                    <td class="name"><?php _e('Name', 'spamprotection'); ?></td>
                                    <td class="email"><?php _e('Email', 'spamprotection'); ?></td>
                                    <td class="ads"><?php _e('Ads', 'spamprotection'); ?></td>
                                    <td class="comments"><?php _e('Comm.', 'spamprotection'); ?></td>
                                    <td class="actions">
                                        <?php _e('Trusted', 'spamprotection'); ?><br />
                                        <?php _e('Ads', 'spamprotection'); ?>
                                        <?php _e('Comm.', 'spamprotection'); ?>
                                        <?php _e('Cont.', 'spamprotection'); ?>
                                    </td>
                                <tr>
                            </thead>
                            <tbody>
                                <?php
                                    $trusted = $sp->_getResult('t_sp_users', array('key' => 'i_reputation', 'value' => '2'));
                                    
                                    if ($trusted) {
                                        foreach($trusted as $v) { 
                                            $user = User::newInstance()->findByPrimaryKey($v['pk_i_id']);
                                            if (isset($v['s_reputation']) && !empty($v['s_reputation'])) {
                                                $rep = unserialize($v['s_reputation']);
                                            }  ?>
                                            <tr>
                                                <td class="name"><?php echo $user['s_name']; ?></td>
                                                <td class="email"><?php echo $user['s_email']; ?></td>
                                                <td class="ads"><?php echo $user['i_items']; ?></td>
                                                <td class="comments"><?php echo $user['i_comments']; ?></td>
                                                <td class="actions">
                                                    <input type="checkbox" name="trusted[<?php echo $v['pk_i_id']; ?>][trustedads]" value="1"<?php echo (isset($rep['trustedads']) ? ' checked="checked"' : ''); ?> />
                                                    <input type="checkbox" name="trusted[<?php echo $v['pk_i_id']; ?>][trustedcomments]" value="1"<?php echo (isset($rep['trustedcomments']) ? ' checked="checked"' : ''); ?> />
                                                    <input type="checkbox" name="trusted[<?php echo $v['pk_i_id']; ?>][trustedcontacts]" value="1"<?php echo (isset($rep['trustedcontacts']) ? ' checked="checked"' : ''); ?> />
                                                </td>
                                            <tr>    
                                        <?php }   
                                    }
                                ?>                                
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="baduser" class="langtab-content <?php echo (isset($table) && $table == 'baduser' ? 'current' : ''); ?>">
                        <table class="badtrusted">
                            <thead>
                                <tr>
                                    <td class="name"><?php _e('Name', 'spamprotection'); ?></td>
                                    <td class="email"><?php _e('Email', 'spamprotection'); ?></td>
                                    <td class="ads"><?php _e('Ads', 'spamprotection'); ?></td>
                                    <td class="comments"><?php _e('Comm.', 'spamprotection'); ?></td>
                                    <td class="actions">
                                        <?php _e('Forbidden', 'spamprotection'); ?><br />
                                        <?php _e('Ads', 'spamprotection'); ?>
                                        <?php _e('Comm.', 'spamprotection'); ?>
                                        <?php _e('Cont.', 'spamprotection'); ?>
                                    </td>
                                <tr>
                            </thead>
                            <tbody>
                                <?php
                                    $bad = $sp->_getResult('t_sp_users', array('key' => 'i_reputation', 'value' => '1'));
                                    if ($bad) {
                                        foreach($bad as $v) { 
                                            $user = User::newInstance()->findByPrimaryKey($v['pk_i_id']);
                                            if (isset($v['s_reputation']) && !empty($v['s_reputation'])) {
                                                $rep = unserialize($v['s_reputation']);
                                            } ?>
                                            <tr>
                                                <td class="name"><?php echo $user['s_name']; ?></td>
                                                <td class="email"><?php echo $user['s_email']; ?></td>
                                                <td class="ads"><?php echo $user['i_items']; ?></td>
                                                <td class="comments"><?php echo $user['i_comments']; ?></td>
                                                <td class="actions">
                                                    <input type="checkbox" name="bad[<?php echo $v['pk_i_id']; ?>][badads]" value="1"<?php echo (isset($rep['badads']) ? ' checked="checked"' : ''); ?> />
                                                    <input type="checkbox" name="bad[<?php echo $v['pk_i_id']; ?>][badcomments]" value="1"<?php echo (isset($rep['badcomments']) ? ' checked="checked"' : ''); ?> />
                                                    <input type="checkbox" name="bad[<?php echo $v['pk_i_id']; ?>][badcontacts]" value="1"<?php echo (isset($rep['badcontacts']) ? ' checked="checked"' : ''); ?> />
                                                </td>
                                            <tr>    
                                        <?php }   
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="addBadOrTrustedUser" class="addBadOrTrusted" style="display: none;">
                        <div id="BadOrTrusted-inner">
                            <span><?php _e("Organize bad and trusted user", "spamprotection"); ?></span>
                            <a href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/config.php&tab=sp_security&sub=badtrusted'); ?>" id="BadOrTrusted-close">x</a>
                        
                            <div id="BadOrTrusted-head">
                                <div class="form-group" style="width: 50%; float: right; padding-right: 20px;">
                                    <label><?php _e("Search for name, email or location", "spamprotection"); ?></label>
                                    <input type="text" name="searchNewTrusted" />
                                    <input type="hidden" id="search_file" value="<?php echo osc_ajax_plugin_url('spamprotection/functions/search.php'); ?>" />
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                        
                            <div id="BadOrTrusted-body">
                                <table style="width: calc(100% - 40px);margin: 20px auto;">
                                    <thead id="tableTrustedUser">
                                        <tr>
                                            <td style="min-width: 30px"></td>
                                            <td style="width: 250px"><?php _e('Name', 'spamprotection'); ?></td>
                                            <td style="width: calc(100% - 370px)"><?php _e('Email', 'spamprotection'); ?></td>
                                            <td style="width: 90px"></td>
                                        </tr>
                                    </thead>
                                    <tbody id="trusted-body">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>                                        
                                </table>
                            </div>
                            
                        </div>
                    </div>                    
                    
                </div>
            </fieldset>
            
        </div>
    </div>
    
    <div id="sp_ipban_options" class="sp_ipban_options">
        <div id="sp_security_ipban" class="subtab-content <?php echo (isset($sub) && $sub == 'ipban' ? 'current' : ''); ?>">
            
            <fieldset>
                <legend><?php _e("IP Ban", "spamprotection"); ?></legend>
                <div class="halfrow">
                    <label>
                        <input type="checkbox" name="sp_ipban_activate" value="1"<?php if (!empty($data['sp_ipban_activate'])) { echo ' checked="checked"'; } ?> />
                        <?php _e('Activate the IP Ban Function', 'spamprotection'); ?>
                    </label><br />
                    <small>
                        <?php _e("Check this option to activate the IP Ban. Choose your favorite action to the right and add unwanted IP's to the table.", "spamprotection"); ?>
                    </small>
                </div>
                <div class="halfrow">
                
                    <div id="IpBanCreateFile" style="width: 55%; float:left; padding: 0;">
                    
                        <?php if (file_exists(osc_base_path().'forbidden.php')) { ?>
                        
                        <label for="sp_ipban_redirect">
                            <input type="radio" name="sp_ipban_redirect" value="1"<?php if (!isset($data['sp_ipban_redirect']) || $data['sp_ipban_redirect'] == '1') { echo ' checked="checked"'; } ?> />
                            <?php _e('Use standard file', 'spamprotection'); ?>
                        </label><br />
                        <small><?php echo osc_base_url().'forbidden.php'; ?></small>
                                                        
                        <?php } else { ?>
                        
                        <div>
                            <?php _e('Create standard file', 'spamprotection'); ?><br />
                            <a id="openCreateFile" class="btn btn-blue" href="<?php echo osc_ajax_plugin_url('spamprotection/functions/ipban.php&createFile=1'); ?>"><?php _e('Create', 'spamprotection'); ?></a>
                            <div style="clear: both;"></div>   
                        </div>
                        
                        <?php } ?>
                    </div>
                    <div style="width: 45%; float:left; padding: 0;">
                        <label for="sp_ipban_redirect">
                            <input type="radio" name="sp_ipban_redirect" value="404"<?php if (!isset($data['sp_ipban_redirect']) || $data['sp_ipban_redirect'] == '404') { echo ' checked="checked"'; } ?> />
                            <?php _e('Cause 404 Error', 'spamprotection'); ?>
                        </label><br />
                        <label for="sp_ipban_redirect">
                            <input type="radio" name="sp_ipban_redirect" value="500"<?php if (!isset($data['sp_ipban_redirect']) || $data['sp_ipban_redirect'] == '500') { echo ' checked="checked"'; } ?> />
                            <?php _e('Cause 500 Error', 'spamprotection'); ?>
                        </label><br />
                    </div>
                    
                    <div style="clear: both;"></div>
                                       
                    <br /><br />                            
                    <label for="sp_ipban_redirectURL">
                        <input type="radio" name="sp_ipban_redirect" value="2"<?php if (!file_exists(osc_base_path().'forbidden.php') || (isset($data['sp_ipban_redirect']) && $data['sp_ipban_redirect'] == '2')) { echo ' checked="checked"'; } ?> />
                        <?php _e('Or redirect banned users to', 'spamprotection'); ?><br />
                        <input type="text" name="sp_ipban_redirectURL" placeholder="Enter URL" value="<?php if (isset($data['sp_ipban_redirectURL'])) { echo $data['sp_ipban_redirectURL']; } ?>" />
                    </label><br />
                    <small>
                        <?php _e('If you want to redirect users to another location, enter URL here.', 'spamprotection'); ?><br />
                        <strong><?php _e('Don\'t use your base domain, it will cause redirect loops.', 'spamprotection'); ?></strong>
                    </small>
                </div>
            </fieldset>
            
            <fieldset style="position: relative;">
                <legend><?php _e("IP Ban Table", "spamprotection"); ?></legend>
                <div style="position: absolute; top: 10px; right: 0;">
                    <a id="addIpToBan" href="<?php echo osc_ajax_plugin_url('spamprotection/functions/ipban.php&do=add'); ?>"><i class="btn btn-green ico ico-32 ico-add-white float-right" style="float: right;width: 11px;height: 16px;transform: scale(0.8);"></i></a>
                    <input id="addIpBan" name="addIpBan" placeholder="<?php _e('Enter IP', 'spamprotection'); ?>" style="float: right;margin-top: 4px;height: 22px;border-radius: 3px;border: 1px solid #999;padding: 2px;" />    
                </div>                                                                                        
                <div class="row form-group">
                    <table class="ipban" style="margin-top: 30px;">
                        <thead>
                            <td style="width: 40px;"></td>
                            <td style="width: 200px;"><?php _e("IP", "spamprotection"); ?></td>
                            <td><?php _e("Date added", "spamprotection"); ?></td>
                        </thead>
                        <tbody id="dataIpBan">
                        <?php
                            $ips = spam_prot::newInstance()->_listIpBanTable();
                            if (isset($ips) && is_array($ips)) {
                                foreach($ips as $k => $v) {
                                    echo '
                                    <tr>
                                        <td><a class="deleteIpBan" href="'.osc_ajax_plugin_url('spamprotection/functions/ipban.php&do=delete').'" data-ip="'.$k.'"><i class="sp-icon delete xs"></i></a></td>
                                        <td>'.$k.'</td>
                                        <td>'.date("d.m.Y H:i:s", $v).'</td>
                                    </tr>
                                    ';
                                }
                            } else {
                                echo '<tr><td colspan="3"><h3>No IP\'s saved</h3></td></tr>';
                            }    
                        ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
            
            <div id="IpBanFlash" style="display: none;"></div>
        </div>
    </div>
    
    <div id="sp_cleaner_options" class="sp_cleaner_options">
        <div id="sp_security_cleaner" class="subtab-content <?php echo (isset($sub) && $sub == 'cleaner' ? 'current' : ''); ?>">
            <fieldset>
                <legend><?php _e("Delete not activated user accounts", "spamprotection"); ?></legend>                
                <div class="row form-group">                
                    <div style="float: left; width: calc(50% - 20px); padding: 10px;">
                        <label>
                            <input type="checkbox" name="sp_user_unactivated" value="1"<?php if (!empty($data['sp_user_unactivated'])) { echo ' checked="checked"'; } ?> />
                            <?php _e('Delete not activated user', 'spamprotection'); ?>
                        </label><br />                    
                        <small><?php _e('Here you can define if not activated user should be deleted automatically after x days.', 'spamprotection'); ?></small>
                    </div>                
                    <div style="float: left; width: calc(50% - 20px); padding: 10px;">
                        <div style="float: left; width: calc(50% - 20px); padding: 10px;">
                            <label style="line-height: 28px;">
                                <?php _e('after', 'spamprotection'); ?>
                                <input type="text" class="form-control" name="sp_user_unactivated_after" style="width: 50px;" value="<?php if (!empty($data['sp_user_unactivated_after'])) { echo $data['sp_user_unactivated_after']; } ?>" /> <span>Days</span>
                            </label>
                        </div>
                        <div style="float: left; width: calc(50% - 20px); padding: 10px;">
                            <label style="line-height: 28px;">
                                <?php _e('Max.', 'spamprotection'); ?>
                                <input type="text" class="form-control" name="sp_user_unactivated_limit" style="width: 50px;" value="<?php if (!empty($data['sp_user_unactivated_limit'])) { echo $data['sp_user_unactivated_limit']; } ?>" /> <span>at once</span>
                            </label>
                        </div>                    
                    </div>                    
                </div>
            </fieldset>
            
            <fieldset id="settingsUnwantedUser">
                <legend><?php _e("Delete unused user accounts", "spamprotection"); ?></legend>                
                <div class="row form-group">            
                    <div style="float: left; width: calc(45% - 20px); padding: 0 10px;">
                        <br />
                        <label for="sp_user_minAge"><?php _e('Select last login date', 'spamprotection'); ?></label>
                        <input type="text" name="sp_user_minAge" value="<?php echo date('Y-m-d', strtotime(date('Y-m-d', time()).' -1 year')); ?>" />
                        <br /><br />
                        <label for="sp_user_maxAcc"><?php _e('Show max. x accounts', 'spamprotection'); ?></label>
                        <input type="text" name="sp_user_maxAcc" value="25" />
                        <script>
                            $("input[name=sp_user_minAge]").datepicker({
                                maxDate: "-1y",
                                dateFormat: "yy-mm-dd"
                            });
                        </script>
                    </div>          
                    <div style="float: left; width: calc(45% - 20px); padding: 0 10px;"> 
                        <br /><br />
                        <label for="sp_user_activated">
                            <input type="checkbox" name="sp_user_activated" />
                            <?php _e('Must be an activated account', 'spamprotection'); ?>
                        </label><br />
                        <label for="sp_user_enabled">
                            <input type="checkbox" name="sp_user_enabled" />
                            <?php _e('Must be an enabled account', 'spamprotection'); ?>
                        </label><br />
                        <label for="sp_user_zeroads">
                            <input type="checkbox" name="sp_user_zeroads" checked="checked" />
                            <?php _e('Must have 0 ads', 'spamprotection'); ?>
                        </label><br />
                        <label for="sp_user_noAdmin">
                            <input type="checkbox" name="sp_user_noAdmin" checked="checked" />
                            <?php _e('User has no admin account', 'spamprotection'); ?>
                        </label><br />
                        <label for="sp_user_neverlogged">
                            <input type="checkbox" name="sp_user_neverlogged" checked="checked" />
                            <?php _e('User has never logged in', 'spamprotection'); ?>
                        </label>          
                    </div>                    
                    <div style="float: right; width: 10%;">
                        <br />
                        <label>&nbsp;</label><br />
                        <a id="searchUnwantedUser" class="btn btn-blue" data-link="<?php echo osc_ajax_plugin_url('spamprotection/functions/searchUser.php'); ?>"><?php _e("Search", "spamprotection"); ?></a>
                    </div>
                    
                    <div style="clear: both;"></div>
                                        
                </div>
                
                <div class="row for-group">
                    <div id="printUnwantedUser">
                    
                    </div>
                </div>
            </fieldset>            
        </div>
    </div>
    
    
</div>