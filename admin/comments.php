<?php
require_once(ABS_PATH.'/oc-load.php');
require_once(osc_plugin_path('spamprotection/classes/class.spamprotection.php'));

$sp = new spam_prot;
$data = $sp->_get();

?>
<div class="settings">

    <ul class="commtabs sp_tabs">
        <li class="commtab-link current" data-tab="sp_comm_mainfeatures"><a><?php _e('Main Settings', 'spamprotection'); ?></a></li>
        <li class="commtab-link " data-tab="sp_comm_emailblock"><a><?php _e('E-Mail Block', 'spamprotection'); ?></a></li>
        <li class="commtab-link " data-tab="sp_comm_stopwords"><a><?php _e('Stopwords', 'spamprotection'); ?></a></li>
        <li class="commtab-link"><button type="submit" class="btn btn-info"><?php _e('Save', 'spamprotection'); ?></button></li>
    </ul>
    
    <div id="sp_comment_options" class="sp_comment_options enabled">
    
        <div id="sp_comm_mainfeatures" class="commtab-content current">
        
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_comment_activate" value="1"<?php if (!empty($data['sp_comment_activate'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Activate the Comment Spam Protection', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option activates the Comment Spam Protection. Some features are optional and can be de/activated separetely', 'spamprotection'); ?></small>
            </div>
        
            <div class="row form-group">
                <div>
                    <label>
                        <?php _e('Check for Links', 'spamprotection'); ?>
                    </label><br />
                    <select name="sp_comment_links">
                        <option value="0"<?php if (empty($data['sp_comment_links']) || $data['sp_comment_links'] == '0') { echo ' selected="selected"'; } ?>><?php _e('Deactivated', 'spamprotection'); ?></option>
                        <option value="1"<?php if (!empty($data['sp_comment_links']) && $data['sp_comment_links'] == '1') { echo ' selected="selected"'; } ?>><?php _e('Only title', 'spamprotection'); ?></option>
                        <option value="2"<?php if (!empty($data['sp_comment_links']) && $data['sp_comment_links'] == '2') { echo ' selected="selected"'; } ?>><?php _e('Title and description', 'spamprotection'); ?></option>
                    </select><br />
                    <small><?php _e('This Option enables the System to check for links in comments and if found, disable it', 'spamprotection'); ?></small>
                </div>
            </div>
                    
        </div>
        
        <div id="sp_comm_emailblock" class="commtab-content">
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_comment_blocked" value="1"<?php if (!empty($data['sp_comment_blocked'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Block banned E-Mailadresses', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This option enables the System to block comments from banned Email addresses', 'spamprotection'); ?></small>
                <br /><br />
                <div id="comment_blocked" class="hiddeninput<?php if (!empty($data['sp_comment_blocked'])) { echo ' visible'; } ?>">
                    <label for="comment_blocked"><?php _e('Enter the blocked E-Mailadresses, separated by ,', 'spamprotection'); ?></label><br />
                    <textarea class="form-control" name="comment_blocked" style="height: 150px;"><?php if (!empty($data['comment_blocked'])) { echo $data['comment_blocked']; } ?></textarea>
                </div>
            </div>
            <div class="row form-group">
                <label>
                    <input type="checkbox" name="sp_comment_blocked_tld" value="1"<?php if (!empty($data['sp_comment_blocked_tld'])) { echo ' checked="checked"'; } ?> />
                    <?php _e('Block banned E-Mail TLD', 'spamprotection'); ?>
                </label><br />
                <small><?php _e('This Option enables the System to block comments from banned E-Mail Top-Level-Domains (e.g. mail.ru,gmail.com)', 'spamprotection'); ?></small>
                <br /><br />
                <div id="comment_blocked_tld" class="hiddeninput<?php if (!empty($data['sp_comment_blocked_tld'])) { echo ' visible'; } ?>">
                    <label for="comment_blocked_tld"><?php _e('Enter the blocked E-Mail TLD, separated by ,', 'spamprotection'); ?></label><br />
                    <textarea class="form-control" name="comment_blocked_tld" style="height: 150px;"><?php if (!empty($data['comment_blocked_tld'])) { echo $data['comment_blocked_tld']; } ?></textarea>
                </div>
            </div>        
        </div>
        
        <div id="sp_comm_stopwords" class="commtab-content">
            <div class="row form-group">
                <h3><?php _e('Stop Words', 'spamprotection'); ?></h3>
                <p>
                    <?php _e('Here you can define the search mechanism, how stopwords are checked. You can search for substrings or particular words', 'spamprotection'); ?>
                </p>
                <ol style="list-style: disc;">
                    <li>
                        <?php _e('Search for Substrings', 'spamprotection'); ?><br />
                        <?php _e('<small>This method searches in Title/Comment for substrings and if found, disable comments (e.g. <em>`are`</em> will be found in <em>`care`</em>)</small>', 'spamprotection'); ?>
                    </li>
                    <li>
                        <?php _e('Search for Words', 'spamprotection'); ?><br />
                        <?php _e('<small>This method searches in Title/Comment for particular words and if found, disable comments (e.g. <em>`are`</em> won\'t be found in <em>`care`</em>).</small>', 'spamprotection'); ?>
                    </li>
                </ol>
                <select name="sp_comment_blockedtype">
                    <option value="substr"<?php if (empty($data['sp_comment_blockedtype']) || !empty($data['sp_comment_blockedtype']) && $data['sp_comment_blockedtype'] == 'substr') { echo ' selected="selected"'; } ?>>Substrings</option>
                    <option value="words"<?php if (!empty($data['sp_comment_blockedtype']) && $data['sp_comment_blockedtype'] == 'words') { echo ' selected="selected"'; } ?>>Words</option>
                </select>
                <br /><br />
                <?php _e('<strong>Enter here the words to be blocked in title or comments (separated by ,)</strong>', 'spamprotection'); ?>
                <textarea class="form-control" name="sp_comment_stopwords" style="height: 200px;"><?php if (!empty($data['sp_comment_stopwords'])) { echo $data['sp_comment_stopwords']; } ?></textarea>
            </div>         
        </div>            
        
    </div> 

</div>