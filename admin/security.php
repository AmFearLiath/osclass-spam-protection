<?php
require_once(ABS_PATH.'/oc-load.php');
require_once(osc_plugin_path('spamprotection/classes/class.spamprotection.php'));

$sp = new spam_prot;
$data = $sp->_get();

?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link current" data-tab="sp_security_mainfeatures"><a><?php _e('Main Settings', 'spamprotection'); ?></a></li>
        <li class="subtab-link"><button type="submit" class="btn btn-info"><?php _e('Save', 'spamprotection'); ?></button></li>
    </ul>
    
    <div id="sp_security_options" class="sp_security_options <?php echo (empty($data['sp_security_activate']) || $data['sp_security_activate'] == '0' ? 'disabled' : 'enabled'); ?>">
    
        <div id="sp_security_mainfeatures" class="subtab-content current">
        
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_security_activate" value="1"<?php if (!empty($data['sp_security_activate'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Activate the Security system', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option activates the Security system. Some features are optional and can be de/activated separetely', 'spamprotection'); ?></small>
            </div>
        
            <div class="row form-group">
                <div class="floating" id="sp_security_login_check_cont">
                    <label>
                        <?php _e('De/Activate Login protection', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_security_login_check" name="sp_security_login_check">
                        <option value="0"<?php if (empty($data['sp_security_login_check']) || $data['sp_security_login_check'] == '0') { echo ' selected="selected"'; } ?>><?php _e('Deactivated', 'spamprotection'); ?></option>
                        <option value="1"<?php if (!empty($data['sp_security_login_check']) && $data['sp_security_login_check'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Activated', 'spamprotection'); ?></option>
                    </select>
                </div>
                <div class="floating" id="sp_security_login_count_cont"<?php if ((empty($data['sp_security_activate']) || $data['sp_security_activate'] == '0') || (empty($data['sp_security_login_check']) || $data['sp_security_login_check'] == '0')) { echo ' style="display: none;"'; } ?>>
                    <label>
                        <?php _e('Max amount of wrong logins', 'spamprotection'); ?>
                    </label><br />
                    <input type="text" name="sp_security_login_count" style="width: 50px;" value="<?php echo (empty($data['sp_security_login_count']) ? '5' : $data['sp_security_login_count']); ?>" />
                    <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">in</span>
                    <input type="text" name="sp_security_login_time" style="width: 50px;" value="<?php echo (empty($data['sp_security_login_time']) ? '5' : $data['sp_security_login_time']); ?>" />
                    <span style="display: inline-block;height: 28px;line-height: 28px;vertical-align: middle;">min</span>
                </div>
                <div class="floating" id="sp_security_login_action_cont"<?php if ((empty($data['sp_security_activate']) || $data['sp_security_activate'] == '0') || (empty($data['sp_security_login_check']) || $data['sp_security_login_check'] == '0')) { echo ' style="display: none;"'; } ?>>
                    <label>
                        <?php _e('Action done after false logins', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_security_login_action" name="sp_security_login_action">
                        <option value="0"<?php if (empty($data['sp_security_login_action']) || $data['sp_security_login_action'] == '0') { echo ' selected="selected"'; } ?>><?php _e('Disable user account', 'spamprotection'); ?></option>
                        <option value="1"<?php if (!empty($data['sp_security_login_action']) && $data['sp_security_login_action'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Add IP to Banlist', 'spamprotection'); ?></option>
                        <option value="2"<?php if (!empty($data['sp_security_login_action']) && $data['sp_security_login_action'] == '2') { echo ' selected="selected"'; } ?>><?php _e('Both', 'spamprotection'); ?></option>
                    </select>
                </div>
                <div style="clear: both;"></div>
                <small><?php _e('This option protects your system against many false login attempts. If a user enters a wrong password too often, you can choose how to continue. In all cases, the user will be notified by e-mail.', 'spamprotection'); ?></small>
            </div>
                    
        </div>            
        
    </div> 

</div>