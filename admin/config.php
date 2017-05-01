<?php 
require_once(ABS_PATH.'/oc-load.php');
require_once(osc_plugin_path('spamprotection/classes/class.spamprotection.php'));

$sp = new spam_prot;
$settings = false; $help = false;
 
if (Params::getParam('spam') == 'activate') {
    $sp->_spamAction('activate', Params::getParam('item'));
    osc_redirect_to(osc_admin_base_url(true).'?page=items');    
} elseif (Params::getParam('spam') == 'delete') {
    $sp->_spamAction('delete', Params::getParam('item'));
    osc_redirect_to(osc_admin_base_url(true).'?page=items');    
} elseif (Params::getParam('spam') == 'block') {
    $sp->_spamAction('block', Params::getParam('user'));
    osc_redirect_to(osc_admin_base_url(true).'?page=items');    
} elseif (Params::getParam('htaccess') == 'save') {
    osc_set_preference('htaccess_warning', '1', 'plugin_spamprotection', 'BOOLEAN');
    return true;    
}

if (Params::getParam('tab') == 'settings') {
    $settings = true;    
} elseif (Params::getParam('tab') == 'contact') {
    $contact = true;    
} elseif (Params::getParam('tab') == 'comments') {
    $comments = true;    
}

if (Params::getParam('settings') == 'save') {
    $params = Params::getParamsAsArray('', false);
    if ($sp->_saveSettings($params)) {
        ob_get_clean();
        osc_add_flash_ok_message(__('<strong>All Settings saved.</strong> Your plugin ist now configured', 'spamprotection'), 'admin');
        osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'config.php&tab=settings');    
    } else {
        ob_get_clean();
        osc_add_flash_error_message(__('<strong>Error.</strong> Your settings can not be saved, please try again', 'spamprotection'), 'admin');
        osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'config.php&tab=settings');    
    }
    
    $settings = true;      
} elseif (Params::getParam('comments') == 'save') {
    $params = Params::getParamsAsArray('', false);
    if ($sp->_saveComments($params)) {
        ob_get_clean();
        osc_add_flash_ok_message(__('<strong>All Settings saved.</strong> Your plugin ist now configured', 'spamprotection'), 'admin');
        osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'config.php&tab=comments');    
    } else {
        ob_get_clean();
        osc_add_flash_error_message(__('<strong>Error.</strong> Your settings can not be saved, please try again', 'spamprotection'), 'admin');
        osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'config.php&tab=comments');    
    }
    
    $settings = true;      
}
    
?>
<div id="spamprot">
    <div class="container">
        <ul class="tabs">
            <li class="tab-link<?php if (isset($settings) && $settings) { echo ' current'; } ?>" data-tab="sp_settings"><a><?php _e('Ad Settings', 'spamprotection'); ?></a></li>
            <li class="tab-link<?php if (isset($comments) && $comments) { echo ' current'; } ?>" data-tab="sp_comments"><a><?php _e('Comment Settings', 'spamprotection'); ?></a></li>
            <li class="tab-link<?php if (isset($contact) && $contact) { echo ' current'; } ?>" data-tab="sp_contact"><a><?php _e('Contact Settings', 'spamprotection'); ?></a></li>
            <li class="tab-link<?php if (!$settings && !$comments) { echo ' current'; } ?>" data-tab="sp_help"><a><?php _e('Help', 'spamprotection'); ?></a></li>
        </ul>
        
        <form id="sp_save_settings" action="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php'); ?>" method="POST">
            <input type="hidden" name="page" value="plugins" />
            <input type="hidden" name="tab" value="settings" />
            <input type="hidden" name="action" value="renderplugin" />
            <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>config.php" />
            <input type="hidden" name="settings" value="save" />

            <div id="sp_settings" class="tab-content<?php if (isset($settings) && $settings) { echo ' current'; } ?>">
                <?php include_once(osc_plugin_path('spamprotection/admin/settings.php')); ?>    
            </div>

            <div id="sp_comments" class="tab-content<?php if (isset($comments) && $comments) { echo ' current'; } ?>">
                <?php include_once(osc_plugin_path('spamprotection/admin/comments.php')); ?>    
            </div>

            <div id="sp_contact" class="tab-content<?php if (isset($contact) && $contact) { echo ' current'; } ?>">
                <?php include_once(osc_plugin_path('spamprotection/admin/contact.php')); ?>    
            </div>
            
        </form>

        <div id="sp_help" class="tab-content<?php if (!$settings && !$comments) { echo ' current'; } ?>">
            <?php include_once(osc_plugin_path('spamprotection/admin/help.php')); ?>
        </div>        
    </div>   
</div>