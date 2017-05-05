<?php
require_once(ABS_PATH.'/oc-load.php');
require_once(osc_plugin_path('spamprotection/classes/class.spamprotection.php'));

$sp = new spam_prot;
$data = $sp->_get();
?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link current" data-tab="sp_security_mainfeatures_user"><a><?php _e('User Protection', 'spamprotection'); ?></a></li>
        <li class="subtab-link" data-tab="sp_security_mainfeatures_admin"><a><?php _e('Admin Protection', 'spamprotection'); ?></a></li>
        <li class="subtab-link"><button type="submit" class="btn btn-info"><?php _e('Save', 'spamprotection'); ?></button></li>
    </ul>

    <div id="sp_security_options" class="sp_security_options">
        <div id="sp_security_mainfeatures_user" class="subtab-content current <?php echo (empty($data['sp_security_activate']) || $data['sp_security_activate'] == '0' ? 'disabled' : 'enabled'); ?>">

            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_security_activate" value="1"<?php if (!empty($data['sp_security_activate'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Activate the Form Protection', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option activates the whole form protection. Some features are optional and can be de/activated separetely', 'spamprotection'); ?></small>
            </div>

            <div class="row form-group">
                <div class="halfrow" style="padding: 0;">
                    <label>
                        <?php _e('Max amount of wrong logins', 'spamprotection'); ?>
                    </label><br />
                    <input type="text" name="sp_security_login_count" style="width: 50px;" value="<?php echo (empty($data['sp_security_login_count']) ? '5' : $data['sp_security_login_count']); ?>" />
                    <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">in</span>
                    <input type="text" name="sp_security_login_time" style="width: 50px;" value="<?php echo (empty($data['sp_security_login_time']) ? '5' : $data['sp_security_login_time']); ?>" />
                    <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">min</span>
                </div>
                <div class="halfrow" style="padding: 0;">
                    <div class="floating">                        
                        <label>
                            <?php _e('Unban accounts after', 'spamprotection'); ?>
                        </label><br />
                        <input type="text" name="sp_security_login_unban" style="width: 50px;" value="<?php echo (empty($data['sp_security_login_unban']) ? '180' : $data['sp_security_login_unban']); ?>" />        
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
            
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_security_login_inform" value="1"<?php if (!empty($data['sp_security_login_inform'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Inform user how many tries are remaining', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This option allows to inform the user after each false login, how many tries are remainig, before your choosen action is done', 'spamprotection'); ?></small>
            </div>

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
                            <i class="fa fa-exclamation-triangle"></i>
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
                    </div>

                    <div id="sp_security_recover_hp_cont" style="float: left; width: calc(50% - 5px); margin: 0; padding: 0;<?php if ((empty($data['sp_security_recover_hp']) || $data['sp_security_recover_hp'] == '0')) { echo ' display: none;'; } ?>">
                        <strong>../oc-content/themes/yourtheme/user-recover.php</strong>
                        <pre><code>&lt;?php osc_run_hook('user_recover_form'); ?&gt;</code></pre>
                    </div>
                    
                    <br /><div style="clear: both;"></div><br /><br />
                    
                    <div style="padding: 0;">
                        <strong style="font-size: 20px;"><?php _e('Example', 'spamprotection'); ?></strong>
                        <pre>
    ...
    &lt;?php osc_run_hook('user_login_form'); ?&gt;
&lt;/form&gt;
                        </pre>
                    </div>
                    <br /><hr />
                </div>
                            
            </div>            

        </div>
    </div>
    
    <div id="sp_admin_options" class="sp_admin_options">
        <div id="sp_security_mainfeatures_admin" class="subtab-content <?php echo (empty($data['sp_admin_activate']) || $data['sp_admin_activate'] == '0' ? 'disabled' : 'enabled'); ?>">
            
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_admin_activate" value="1"<?php if (!empty($data['sp_admin_activate'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Activate the Admin Protection', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option activates the whole admin protection. Some features are optional and can be de/activated separetely', 'spamprotection'); ?></small>
            </div>

            <div class="row form-group">
                <div class="halfrow" style="padding: 0;">
                    <label>
                        <?php _e('Max amount of wrong logins', 'spamprotection'); ?>
                    </label><br />
                    <input type="text" name="sp_admin_login_count" style="width: 50px;" value="<?php echo (empty($data['sp_admin_login_count']) ? '5' : $data['sp_admin_login_count']); ?>" />
                    <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">in</span>
                    <input type="text" name="sp_admin_login_time" style="width: 50px;" value="<?php echo (empty($data['sp_admin_login_time']) ? '5' : $data['sp_admin_login_time']); ?>" />
                    <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">min</span>
                </div>
                <div class="halfrow" style="padding: 0;">
                    <div class="floating">                        
                        <label>
                            <?php _e('Unban accounts after', 'spamprotection'); ?>
                        </label><br />
                        <input type="text" name="sp_admin_login_unban" style="width: 50px;" value="<?php echo (empty($data['sp_admin_login_unban']) ? '180' : $data['sp_admin_login_unban']); ?>" />        
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
            
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_admin_login_inform" value="1"<?php if (!empty($data['sp_admin_login_inform'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Inform admin how many tries are remaining', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This option allows to inform the admin after each false login, how many tries are remainig, before your choosen action is done', 'spamprotection'); ?></small>
            </div>
            
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_admin_login_hp" value="1"<?php if (!empty($data['sp_admin_login_hp'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Add Honeypot to admin login form', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option ads a hidden form field to the admin login forms. After a bot tap into your trap, action following rules you have set above.', 'spamprotection'); ?></small><br />
            </div>
            
        </div>
    </div>
</div>