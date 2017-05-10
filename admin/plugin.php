<?php
if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
} if (!osc_is_admin_user_logged_in()) {
    die;
}

$path = osc_plugin_path('spamprotection/export');
$url = osc_base_url().'oc-content/plugins/spamprotection';
$js_path = osc_plugin_path('spamprotection/assets/js');
$data = $sp->_get(false, true);

if (Params::getParam('subtab')) {
    $subtab = Params::getParam('subtab');        
} if (Params::getParam('create_path') == 'true') {
    $path = osc_plugin_path('spamprotection/export/');
    if (!mkdir($path, 0755)) {
        $create_error = __("Can't create folder, please create manually and grant write access.", "spamprotection");
    }        
} elseif (Params::getParam('chmod_path') == 'true') {
    $path = osc_plugin_path('spamprotection/export/');
    $chmod = Params::getParam('chmod');
    
    if (is_numeric($chmod)) {
        if (!chmod($path, $chmod)) {
            $chmod_error = __("Can't grant write access to path, please change permissions manually.", "spamprotection");
        }
    } else {
        $chmod_error = __("Please enter which chmod settings should be applied for export path", "spamprotection");    
    }
} elseif (Params::getParam('export')) {
    $subtab = 'export';
    $export = spam_prot::newInstance()->_export(Params::getParam('export'));
} elseif (Params::getParam('import')) {
    $subtab = 'import';
    $import = spam_prot::newInstance()->_import(Params::getParam('import'), 'server');
} elseif (Params::getParam('upload_exportfile')) {
    $subtab = 'import';
    $ext = pathinfo($_FILES['sp_import']['name'], PATHINFO_EXTENSION);                
    if (!in_array($ext, array('xml'))) { 
        $import =  __("Only xml files generated through this plugin are allowed", "spamprotection"); 
    } else {
        $import = spam_prot::newInstance()->_import($_FILES['sp_import']['tmp_name'], 'upload');    
    }    
} elseif (Params::getParam('delete')) {
    $delete = Params::getParam('delete');
    if (!empty($delete)) {
        if (!unlink($path.'/'.$delete)) {
            $delete_error = sprintf(__("Can't delete %s. Maybe missing write permission?", "spamprotection"), $delete);    
        }
    } else {
        $delete_error = sprintf(__("There is no filename given to delete!", "spamprotection"), $delete);
    }
}

if (Params::getParam('plugin_settings') == 'save') {
    $params = Params::getParamsAsArray('', false);
    if ($sp->_saveSettings($params, true)) {
        ob_get_clean();
        osc_add_flash_ok_message(__('<strong>All Settings saved.</strong>', 'spamprotection'), 'admin');
        osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'config.php&tab='.$params['tab']);    
    } else {
        ob_get_clean();
        osc_add_flash_error_message(__('<strong>Error.</strong> Your settings can not be saved.', 'spamprotection'), 'admin');
        osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'config.php&tab='.$params['tab']);    
    }      
}

$import_files = array_diff(scandir($path), array('..', '.', 'index.php')); 
?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link <?php echo (!isset($subtab) || $subtab == 'settings' ? 'current' : ''); ?>" data-tab="sp_config_settings"><a><?php _e('Settings', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (isset($subtab) && $subtab == 'export' ? 'current' : ''); ?>" data-tab="sp_config_export"><a><?php _e('Export', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (isset($subtab) && $subtab == 'import' ? 'current' : ''); ?>" data-tab="sp_config_import"><a><?php _e('Import', 'spamprotection'); ?></a></li>
    </ul>

    <div id="sp_config_options" class="sp_config_options">

        <div id="sp_config_settings" class="subtab-content <?php echo (!isset($subtab) || $subtab == 'settings' ? 'current' : ''); ?>">
            <form action="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php'); ?>" method="post">
                <input type="hidden" name="page" value="plugins" />
                <input type="hidden" name="tab" id="sp_tab" value="sp_config" />
                <input type="hidden" name="subtab" id="sp_subtab" value="settings" />
                <input type="hidden" name="action" value="renderplugin" />
                <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>config.php" />
                <input type="hidden" name="plugin_settings" value="save" />    
            
                <button type="submit" class="btn btn-info" style="float: right;margin-top: 15px;margin-bottom: -15px;"><?php _e('Save', 'spamprotection'); ?></button>
                <div style="clear:both;"></div>
                
                <fieldset>
                    <legend><?php _e("Menu appearance", "spamprotection"); ?></legend>
                    <div class="row form-group">
                        <div class="halfrow"style="width: 50%; padding: 0;">
                            <label>
                                <input type="checkbox" name="sp_activate_menu" value="1"<?php if (!empty($data['sp_activate_menu'])) { echo ' checked="checked"'; } ?> />
                                <?php _e('Show icon in sidebar', 'spamprotection'); ?>
                            </label><br />
                            <small><?php _e('This option allows to show an icon for this plugin in your admin sidebar.', 'spamprotection'); ?></small>
                        </div>
                        <div id="sp_menu_appearance_cont" class="halfrow"style="width: 50%; padding: 0;<?php if (empty($data['sp_activate_menu']) || $data['sp_activate_menu'] != '1') { echo ' display: none;'; } ?>">
                            <label>
                                <?php _e('Show icon after', 'spamprotection'); ?>
                            </label><br />
                            <select id="sp_menu_after" name="sp_menu_after">
                                <option value="menu_dash"<?php if (empty($data['sp_menu_after']) || $data['sp_menu_after'] == 'menu_dash') { echo ' selected="selected"'; } ?>><?php _e('Dashboard', 'spamprotection'); ?></option>
                                <option value="menu_items"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_items') { echo ' selected="selected"'; } ?>><?php _e('Items', 'spamprotection'); ?></option>
                                <option value="menu_market"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_market') { echo ' selected="selected"'; } ?>><?php _e('Market', 'spamprotection'); ?></option>
                                <option value="menu_appearance"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_appearance') { echo ' selected="selected"'; } ?>><?php _e('Appearance', 'spamprotection'); ?></option>
                                <option value="menu_plugins"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_plugins') { echo ' selected="selected"'; } ?>><?php _e('Plugins', 'spamprotection'); ?></option>
                                <option value="menu_stats"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_stats') { echo ' selected="selected"'; } ?>><?php _e('Statistics', 'spamprotection'); ?></option>
                                <option value="menu_settings"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_settings') { echo ' selected="selected"'; } ?>><?php _e('Settings', 'spamprotection'); ?></option>
                                <option value="menu_pages"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_pages') { echo ' selected="selected"'; } ?>><?php _e('Pages', 'spamprotection'); ?></option>
                                <option value="menu_users"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'menu_users') { echo ' selected="selected"'; } ?>><?php _e('Users', 'spamprotection'); ?></option>
                                <option value="anywhere"<?php if (!empty($data['sp_menu_after']) && $data['sp_menu_after'] == 'anywhere') { echo ' selected="selected"'; } ?>><?php _e('Anywhere', 'spamprotection'); ?></option>
                            </select>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend><?php _e("Show buttons", "spamprotection"); ?></legend>
                    <div class="row form-group">
                        <label>
                            <input type="checkbox" name="sp_activate_topbar" value="1"<?php if (!empty($data['sp_activate_topbar'])) { echo ' checked="checked"'; } ?> />
                            <?php _e('Show buttons in top menu', 'spamprotection'); ?>
                        </label><br />
                        <small><?php _e('This option activates buttons in your dashboard top bar, everytime if some spam or ban was found.', 'spamprotection'); ?></small>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend><?php _e("Update check", "spamprotection"); ?></legend>
                    <div class="row form-group">
                        <label>
                            <input type="checkbox" name="sp_update_check" value="1"<?php if (!empty($data['sp_update_check'])) { echo ' checked="checked"'; } ?> />
                            <?php _e('Check database after update', 'spamprotection'); ?>
                        </label><br />
                        <small><?php _e('This option checks after each manually or automatically update the database for needed changes.', 'spamprotection'); ?></small>
                    </div>
                </fieldset>    
            
                <button type="submit" class="btn btn-info" style="float: right;margin-top: -5px;"><?php _e('Save', 'spamprotection'); ?></button>
                <div style="clear:both;"></div>
                
            </form>
        </div>

        <div id="sp_config_export" class="subtab-content <?php echo (isset($subtab) && $subtab == 'export' ? 'current' : ''); ?>">
            <h2><?php _e("Export settings and data", "spamprotection"); ?></h2>
            <fieldset id="#sp_export_file" style="border: 1px solid #bbb; padding: 15px; margin: 15px 0;">
            <?php if (!is_dir($path)) { ?>
                <legend><?php _e("Error", "spamprotection"); ?></legend>
                <div class="sp_export_path_error">
                    <p>
                        <i class="sp-icon attention margin-right float-left"></i>
                        <?php echo sprintf(__("<strong>Following path does not exist, please create and grant write access to:</strong><br /><em>%s</em>", "spamprotection"), $path); ?>
                    </p>
                    <p>
                        <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&&create_path=true&tab=sp_config&subtab=export'); ?>"><button class="btn btn-green"><?php _e("Create Folder", "spamprotection"); ?></button></a>    
                    </p>
                    <?php if (isset($create_error)) { echo '<pre>'.$create_error.'</pre>'; } ?>
            <?php } elseif (!is_writable($path)) { ?>
                <legend><?php _e("Error", "spamprotection"); ?></legend>
                <div class="sp_export_path_error">
                    <p>
                        <i class="sp-icon attention margin-right float-left"></i>
                        <?php echo sprintf(__("<strong>Following path is not writable, please ensure that you grant write access to:</strong><br /><em>%s</em>", "spamprotection"), $path); ?>
                    </p>
                    <p>
                        <form action="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php'); ?>" method="post">
                            <input type="hidden" name="page" value="plugins" />
                            <input type="hidden" name="tab" id="sp_tab" value="sp_config" />
                            <input type="hidden" name="subtab" id="sp_subtab" value="export" />
                            <input type="hidden" name="action" value="renderplugin" />
                            <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>config.php" />
                            <input type="hidden" name="chmod_path" value="true" />
                            <div class="form-group" style="margin: 25px 0 0 46px;;">
                                <label><small><?php _e("Here you can define which chmod should be set and try to fix this problem. Otherwise you have to change the permissions manually"); ?></small></label><br />
                                <input type="text" name="chmod" style="margin-right: 10px; width: 75px; height: 24px;" value="<?php echo Params::getParam('chmod'); ?>" placeholder="0755">
                                <button class="btn btn-green"><?php _e("Chmod Folder", "spamprotection"); ?></button>
                            </div>
                        </form>
                    </p>
                    <?php if (isset($chmod_error)) { echo '<pre>'.$chmod_error.'</pre>'; } ?>
                </div>
                <?php } else { ?>
                <legend><?php _e("Select for export", "spamprotection"); ?></legend>                
                <div class="halfrow" style="width: 50%; padding: 0;">
                    <p>
                        <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&export=settings'); ?>">
                            <button class="btn btn-green"><?php _e("Export plugin settings", "spamprotection"); ?></button>
                        </a>
                        <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&export=database'); ?>">
                            <button class="btn btn-blue"><?php _e("Export database", "spamprotection"); ?></button>
                        </a>
                    </p>
                    <?php if (isset($export)) { echo '<pre>'.$export.'</pre>'; } ?>
                </div>                
                <div class="halfrow" style="width: 50%; padding: 0;">
                    <?php if (!empty($import_files)) { ?>
                    <table style="width: 100%;">
                        <thead style="padding-bottom: 5px; margin-bottom: 5px;">
                            <tr>
                                <td style="border-bottom: 1px solid #aaa;"><h3 style="padding: 0px; margin: 0px;"><?php _e("Created exports", "spamprotection"); ?></h3></td>
                                <td style="width: 60px; text-align: center; border-bottom: 1px solid #aaa;"><?php _e("Size", "spamprotection"); ?></td>
                                <td style="width: 75px; text-align: center; border-bottom: 1px solid #aaa;"><?php _e("Date", "spamprotection"); ?></td>
                                <td style="width: 70px; border-bottom: 1px solid #aaa;"></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($import_files as $v) { ?>
                            <tr>
                                <td><strong><em><a href="<?php echo $url.'/export/'.$v; ?>"><?php echo $v; ?></a></em></strong></td>
                                <td style="width: 60px; text-align: center;"><?php echo number_format(filesize($path.'/'.$v)/1000,2).'kb'; ?></td>
                                <td style="width: 75px; text-align: center;"><?php echo date("d.m.Y", filectime($path.'/'.$v)); ?></td>
                                <td style="width: 70px; text-align: right;">
                                    <a class="sp-icon delete small float-right" href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&subtab=export&delete='.$v); ?>" title="<?php _e("Delete", "spamprotection"); ?>">
                                        
                                    </a>
                                    <a class="sp-icon download small float-right" href="<?php echo $url.'/export/'.$v; ?>" title="<?php _e("Download", "spamprotection"); ?>" download="<?php echo str_replace(".xml", "", $v).'_'.date("d.m.Y\_H.i.s", time()).'.xml'; ?>">
                                        
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
                <?php if (isset($delete_error)) { echo '<pre>'.$delete_error.'</pre>'; } ?>
            <?php } ?>
            </fieldset>
        </div>

        <div id="sp_config_import" class="subtab-content <?php echo (isset($subtab) && $subtab == 'import' ? 'current' : ''); ?>">
            <h2><?php _e("Import settings and data", "spamprotection"); ?></h2>                
            
            <fieldset id="#sp_import_file" style="border: 1px solid #bbb; padding: 15px; margin: 15px 0;">
            <legend><?php _e("Files on Server", "spamprotection"); ?></legend>
            <?php if (!empty($import_files)) { ?>
                    <p><?php echo sprintf(__("This files can be used for quick import.", "spamprotection"), $path); ?></p>                
                <?php if (file_exists($path.'/settings.xml')) { ?>
                    <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&import=settings'); ?>">
                        <button class="btn btn-green"><?php _e("Import plugin settings", "spamprotection"); ?></button>
                    </a>
                <?php } if (file_exists($path.'/database.xml')) { ?>
                    <a href="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php&tab=sp_config&import=database'); ?>">
                        <button class="btn btn-blue"><?php _e("Import database", "spamprotection"); ?></button>
                    </a>
                <?php } ?>
                <?php } else { ?>                    
                <p><?php echo sprintf(__("There is no file to import. If you want to import your previous saved files, copy them to:<br /><em>%s</em>", "spamprotection"), $path); ?></p>    
                <?php } ?>
            </fieldset>
            
            <fieldset id="#sp_import_drop" style="border: 1px solid #bbb; padding: 15px; margin: 15px 0;">
                <legend><?php _e("Upload file for import", "spamprotection"); ?></legend>
                <form action="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php'); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="tab" id="sp_tab" value="sp_config" />
                    <input type="hidden" name="subtab" id="sp_subtab" value="import" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>config.php" />
                    <input type="hidden" name="upload_exportfile" value="true" />
                    
                    <h3 id="file-upload-info"><?php _e("Only xml files generated through this plugin allowed", "spamprotection"); ?></h3>
                    <div class="file-upload-container">                        
                        <div class="file-upload-override-button file-button">
                            <?php _e("Choose file for import", "spamprotection"); ?>
                            <input type="file" name="sp_import" class="file-upload-button" id="file-upload-button" />
                        </div>
                        <div id="file-upload-button2"></div>
                        <div style="clear: both;"></div>
                    </div>
                </form>
                <script>
                $("#file-upload-button").change(function () {
                    var fileName = $(this).val().replace('C:\\fakepath\\', '');
                    $("#file-upload-info").html('<?php _e("File ready to import: ", "spamprotection"); ?>'+fileName);
                    $("#file-upload-button2").html('<button type="submit" class="btn btn-green"><?php _e("Upload & Import", "spamprotection"); ?></button>');
                });
                </script>        
            </fieldset>
            <?php if (!empty($import)) { 
                echo $import;                 
            } ?>
        </div>
    </div>
</div>

