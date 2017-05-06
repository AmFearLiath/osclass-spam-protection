<?php
$path = osc_plugin_path('spamprotection/export');
$action = Params::getParam('chmod_path');

if (Params::getParam('chmod_path') == 'true') {
    $path = osc_plugin_path('spamprotection/export');
    if (!chmod($path, 0777)) {
        $chmod = __("Can't grant write access to path, please do it manually.", "spamprotection");
    }
}

?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link <?php echo ($action != 'true' ? 'current' : ''); ?>" data-tab="sp_config_settings"><a><?php _e('Settings', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo ($action == 'true' ? 'current' : ''); ?>" data-tab="sp_config_export"><a><?php _e('Export', 'spamprotection'); ?></a></li>
        <li class="subtab-link " data-tab="sp_config_import"><a><?php _e('Import', 'spamprotection'); ?></a></li>
    </ul>
    
    <div id="sp_config_options" class="sp_config_options">
    
        <div id="sp_config_settings" class="subtab-content <?php echo ($action != 'true' ? 'current' : ''); ?>">
            Settings
        </div>
    
        <div id="sp_config_export" class="subtab-content <?php echo ($action == 'true' ? 'current' : ''); ?>">
            <h2><?php _e("Export settings and data", "spamprotection"); ?></h2>
            <?php if (!is_writable($path)) { ?>
                <div class="sp_export_path_error">
                    <p>
                        <?php echo sprintf(__("<strong>Following path is not writable, please ensure that you grant write access to:</strong><br /><em>%s</em>", "spamprotection"), $path); ?>
                    </p>
                    <p>
                        <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&chmod_path=true'); ?>">
                            <button class="btn btn-green"><?php _e("Try to solve this", "spamprotection"); ?></button>
                        </a>
                    </p>
                    <?php if (isset($chmod)) { echo '<p>'.$chmod.'</p>'; } ?>
                </div>
            <?php } else { ?>                
                <p>
                    <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&export=settings'); ?>">
                        <button class="btn btn-green"><?php _e("Export plugin settings", "spamprotection"); ?></button>
                    </a>
                    <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&export=database'); ?>">
                        <button class="btn btn-blue"><?php _e("Export database", "spamprotection"); ?></button>
                    </a>
                </p>
            <?php } ?>
        </div>
    
        <div id="sp_config_import" class="subtab-content">
            <h2><?php _e("Import settings and data", "spamprotection"); ?></h2>                
            <p>
                <?php if (file_exists($path.'/settings.xml')) { ?>
                <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&export=settings'); ?>">
                    <button class="btn btn-green"><?php _e("Import plugin settings", "spamprotection"); ?></button>
                </a>
                <?php } if (file_exists($path.'/database.xml')) { ?>
                <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&export=database'); ?>">
                    <button class="btn btn-blue"><?php _e("Import database", "spamprotection"); ?></button>
                </a>
                <?php } if (!file_exists($path.'/settings.xml') && !file_exists($path.'/database.xml')) { ?>
                    <p><?php _e("No data to import available", "spamprotection"); ?></p>    
                <?php } ?>
            </p>
        </div>
        
    </div>
</div>