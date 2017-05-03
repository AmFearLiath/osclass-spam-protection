<?php
require_once(ABS_PATH.'/oc-load.php');
require_once(osc_plugin_path('spamprotection/classes/class.spamprotection.php'));

$sp = new spam_prot;
$data = $sp->_get();
$htaccess_file = ABS_PATH.'/.htaccess';
$htaccess_writable = is_writable($htaccess_file);
if ($htaccess_writable) {
    $htaccess_content = file_get_contents($htaccess_file);     
}
?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link current" data-tab="sp_mainfeatures"><a><?php _e('Main Features', 'spamprotection'); ?></a></li>
        <li class="subtab-link" data-tab="sp_emailblock"><a><?php _e('E-Mail Block', 'spamprotection'); ?></a></li>
        <li class="subtab-link" data-tab="sp_stopwords"><a><?php _e('Stopwords', 'spamprotection'); ?></a></li>
        <li class="subtab-link" data-tab="sp_htaccess"><a><?php _e('.htaccess Editor', 'spamprotection'); ?></a></li>
        <li class="subtab-link"><button type="submit" class="btn btn-info"><?php _e('Save', 'spamprotection'); ?></button></li>
    </ul>
    
    <div id="sp_options" class="sp_options <?php echo (empty($data['sp_activate']) || $data['sp_activate'] == '0' ? 'disabled' : 'enabled'); ?>">
        <div id="sp_mainfeatures" class="subtab-content current">
        
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_activate" value="1"<?php if (!empty($data['sp_activate'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Activate the Spam Protection', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option activates the whole Spam Protection. Some features are optional and can be de/activated separately', 'spamprotection'); ?></small>
            </div>
        
            <div class="row form-group">
                <div class="floating">
                    <label>
                        <?php _e('Check for duplicates', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_duplicates_as" name="sp_duplicates_as">
                        <option value="0"<?php if (empty($data['sp_duplicates_as']) || $data['sp_duplicates_as'] == '0') { echo ' selected="selected"'; } ?>><?php _e('Deactivated', 'spamprotection'); ?></option>
                        <option value="1"<?php if (!empty($data['sp_duplicates_as']) && $data['sp_duplicates_as'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Per user', 'spamprotection'); ?></option>
                        <option value="2"<?php if (!empty($data['sp_duplicates_as']) && $data['sp_duplicates_as'] == '2') { echo ' selected="selected"'; } ?>><?php _e('All items', 'spamprotection'); ?></option>
                    </select>                        
                </div>
                <div class="floating" id="sp_duplicates_cont"<?php if (empty($data['sp_duplicates_as']) || $data['sp_duplicates_as'] == '0') { echo ' style="display: none;"'; } ?>>
                    <label>
                        <?php _e('Search in', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_duplicates" name="sp_duplicates">
                        <option value="0"<?php if (empty($data['sp_duplicates']) || $data['sp_duplicates'] == '0') { echo ' selected="selected"'; } ?>><?php _e('Only title', 'spamprotection'); ?></option>
                        <option value="1"<?php if (!empty($data['sp_duplicates']) && $data['sp_duplicates'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Title and description', 'spamprotection'); ?></option>
                    </select>                        
                </div>
                <div class="floating" id="sp_duplicate_type_cont"<?php if (empty($data['sp_duplicates_as']) || $data['sp_duplicates_as'] == '0') { echo ' style="display: none;"'; } ?>>
                    <label>
                        <?php _e('Type of search', 'spamprotection'); ?>
                    </label><br />
                    <select id="sp_duplicate_type" name="sp_duplicate_type">
                        <option value="0"<?php if (empty($data['sp_duplicate_type']) || $data['sp_duplicate_type'] == '0') { echo ' selected="selected"'; } ?>><?php _e('md5/string comparition', 'spamprotection'); ?></option>
                        <option value="1"<?php if (!empty($data['sp_duplicate_type']) && $data['sp_duplicate_type'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Similar text', 'spamprotection'); ?></option>
                    </select>
                </div>
                <div class="floating" id="sp_duplicate_percent_cont"<?php if ((empty($data['sp_duplicates_as']) || $data['sp_duplicates_as'] == '0') || (empty($data['sp_duplicate_type']) || $data['sp_duplicate_type'] == '0')) { echo ' style="display: none;"'; } ?>>
                    <label>
                        <?php _e('Similar percent', 'spamprotection'); ?>
                    </label><br />
                    <input type="text" name="sp_duplicate_perc" value="<?php echo (empty($data['sp_duplicate_perc']) ? '85' : $data['sp_duplicate_perc']); ?>" />
                </div>
                <div style="clear: both;"></div>
                <small><?php _e('This Option enables the System to check new ads for duplicates and mark them as spam', 'spamprotection'); ?></small>
            </div>
        
            <div class="row form-group">                    
                <label>
                    <input type="checkbox" name="sp_mxr" value="1"<?php if (!empty($data['sp_mxr'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Check MX Record of used Mail', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This option enables the System to check the MX Record of the submitted Email address', 'spamprotection'); ?></small>
            </div>
            
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_honeypot" value="1"<?php if (!empty($data['sp_honeypot'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Activate the Honeypot form field', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option ads a hidden form field to the post page. Bots tap into your trap and can be banned or ignored.', 'spamprotection'); ?></small>
                <div id="honeypot" class="hiddeninput<?php if (!empty($data['sp_honeypot'])) { echo ' visible'; } ?>">
                    <label for="honeypot_name"><?php _e('Enter the name of the hidden honeypot field', 'spamprotection'); ?> <span id="validname"></span></label><br />
                    <input type="text" class="form-control" name="honeypot_name" value="<?php if (!empty($data['honeypot_name'])) { echo $data['honeypot_name']; } ?>" /><br />
                    <small><?php _e('Good names would be "item_runtime, user_age, price_range or something else, dont name it honeypot ;)', 'spamprotection'); ?></small>
                    
                </div>
            </div>        
        </div>
        
        <div id="sp_emailblock" class="subtab-content">
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_blocked" value="1"<?php if (!empty($data['sp_blocked'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Block banned E-Mailadresses', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This option enables the System to block ads from banned Email addresses', 'spamprotection'); ?></small>
                <br /><br />
                <div id="blocked" class="hiddeninput<?php if (!empty($data['sp_blocked'])) { echo ' visible'; } ?>">
                    <label for="blocked"><?php _e('Enter the blocked E-Mailadresses, separated by ,', 'spamprotection'); ?></label><br />
                    <textarea class="form-control" name="blocked" style="height: 150px;"><?php if (!empty($data['blocked'])) { echo $data['blocked']; } ?></textarea>
                </div>
            </div>
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_blocked_tld" value="1"<?php if (!empty($data['sp_blocked_tld'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Block banned E-Mail TLD', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option enables the System to block ads from banned E-Mail Top-Level-Domains (e.g. mail.ru,gmail.com)', 'spamprotection'); ?></small>
                <br /><br />
                <div id="blocked_tld" class="hiddeninput<?php if (!empty($data['sp_blocked_tld'])) { echo ' visible'; } ?>">
                    <label for="blocked"><?php _e('Enter the blocked E-Mail TLD, separated by ,', 'spamprotection'); ?></label><br />
                    <textarea class="form-control" name="blocked_tld" style="height: 150px;"><?php if (!empty($data['blocked_tld'])) { echo $data['blocked_tld']; } ?></textarea>
                </div>
            </div>        
        </div>
        
        <div id="sp_stopwords" class="subtab-content">
            <div class="row form-group">
                <h3><?php _e('Stop Words', 'spamprotection'); ?></h3>
                <p>
                    <?php _e('Here you can define the search mechanism, how stopwords are checked. You can search for substrings or particular words', 'spamprotection'); ?>
                </p>
                <ol style="list-style: disc;">
                    <li>
                        <?php _e('Search for Substrings', 'spamprotection'); ?><br />
                        <?php _e('<small>This method searches in Title/Description for substrings and if found, marks ads as spam (e.g. <em>`are`</em> will be found in <em>`care`</em>)</small>', 'spamprotection'); ?>
                    </li>
                    <li>
                        <?php _e('Search for Words', 'spamprotection'); ?><br />
                        <?php _e('<small>This method searches in Title/Description for particular words and if found, marks ads as spam (e.g. <em>`are`</em> won\'t be found in <em>`care`</em>).</small>', 'spamprotection'); ?>
                    </li>
                </ol>
                <select name="sp_blockedtype">
                    <option value="substr"<?php if (empty($data['sp_blockedtype']) || !empty($data['sp_blockedtype']) && $data['sp_blockedtype'] == 'substr') { echo ' selected="selected"'; } ?>>Substrings</option>
                    <option value="words"<?php if (!empty($data['sp_blockedtype']) && $data['sp_blockedtype'] == 'words') { echo ' selected="selected"'; } ?>>Words</option>
                </select>
                <br /><br />
                <?php _e('<strong>Enter here the words to be blocked in title or descriptions (separated by ,)</strong>', 'spamprotection'); ?>
                <textarea class="form-control" name="sp_stopwords" style="height: 200px;"><?php if (!empty($data['sp_stopwords'])) { echo $data['sp_stopwords']; } ?></textarea>
            </div>         
        </div>
        
        <div id="sp_htaccess" class="subtab-content">            
            <div class="row form-group">
                <div id="attention">
                    <div id="attention_content">
                        <h2><?php _e('ATTENTION!!!', 'spamprotection'); ?></h2>
                        <p><?php _e('Do not edit this file, unless you know what you do! Corrupt .htaccess files can cause errors for your whole webpage!', 'spamprotection'); ?></p>
                        <p>
                        <button id="attention_ok" class="btn btn-green"><?php _e('Ok', 'spamprotection'); ?></button>
                        <button id="attention_save" class="btn btn-green" data-file="<?php echo '../oc-content/plugins/'.osc_plugin_folder(__FILE__).'config.php'; ?>"><?php _e('Don\'t remember', 'spamprotection'); ?></button>
                        </p>
                    </div>
                </div>
                <h3><?php _e('.htaccess Editor', 'spamprotection'); ?> <?php if (!$htaccess_writable) { _e('(File is not writable)', 'spamprotection'); } ?></h3>
                <small><?php _e('Beware of editing this file, unless you know what you\'re doing!!!', 'spamprotection'); ?></small>
                <textarea class="form-control" name="sp_htaccess" style="height: 200px;"<?php //if (!$htaccess_writable) { echo ' disabled="disabled"'; } ?>><?php if (!empty($htaccess_content)) { echo $htaccess_content; } ?></textarea>
                <?php if (osc_get_preference('htaccess_warning', 'plugin_spamprotection') == '1') { echo '<input type="hidden" name="attention_htaccess" value="1" />'; } ?>
                
            </div>        
        </div>
    </div> 
    
</div>