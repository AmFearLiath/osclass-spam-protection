<?php
if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
} if (!osc_is_admin_user_logged_in()) {
    die;
}

$path = osc_plugin_path('spamprotection/export');
$url = osc_base_url().'oc-content/plugins/spamprotection';
$js_path = osc_plugin_path('spamprotection/assets/js');

if (Params::getParam('chmod_path') == 'true') {
    $path = osc_plugin_path('spamprotection/export/');
    $chmod = Params::getParam('chmod');
    
    if (is_numeric($chmod)) {
        if (!chmod($path, $chmod)) {
            $chmod_error = __("Can't grant write access to path, please change permissions manually.", "spamprotection");
        }
    } else {
        $chmod_error = __("Please enter which chmod settings should be applied for export path", "spamprotection");    
    }
} if (Params::getParam('export')) {
    $export = spam_prot::newInstance()->_export(Params::getParam('export'));
} if (Params::getParam('import')) {
    $import = spam_prot::newInstance()->_import(Params::getParam('import'));
}

$import_files = array_diff(scandir($path), array('..', '.'));
 
?>
<div class="settings">

    <ul class="subtabs sp_tabs">
        <li class="subtab-link <?php echo (!isset($export) && !isset($import) ? 'current' : ''); ?>" data-tab="sp_config_settings"><a><?php _e('Settings', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (isset($export) ? 'current' : ''); ?>" data-tab="sp_config_export"><a><?php _e('Export', 'spamprotection'); ?></a></li>
        <li class="subtab-link <?php echo (isset($import) ? 'current' : ''); ?>" data-tab="sp_config_import"><a><?php _e('Import', 'spamprotection'); ?></a></li>
    </ul>

    <div id="sp_config_options" class="sp_config_options">

        <div id="sp_config_settings" class="subtab-content <?php echo (!isset($export) && !isset($import) ? 'current' : ''); ?>">
            <?php print_r($import_files); ?>
        </div>

        <div id="sp_config_export" class="subtab-content <?php echo (isset($export) ? 'current' : ''); ?>">
            <h2><?php _e("Export settings and data", "spamprotection"); ?></h2>
            <fieldset id="#sp_export_file" style="border: 1px solid #bbb; padding: 15px; margin: 15px 0;">
            <?php if (!is_writable($path)) { ?>
                <legend><?php _e("Error", "spamprotection"); ?></legend>
                <div class="sp_export_path_error">
                    <p>
                        <i class="sp-icon attention margin-right float-left"></i>
                        <?php echo sprintf(__("<strong>Following path is not writable, please ensure that you grant write access to:</strong><br /><em>%s</em>", "spamprotection"), $path); ?>
                    </p>
                    <p>
                        <form action="<?php echo osc_admin_render_plugin_url('spamprotection/admin/config.php'); ?>" type="post">
                            <input type="hidden" name="page" value="plugins" />
                            <input type="hidden" name="tab" id="sp_tab" value="sp_config" />
                            <input type="hidden" name="action" value="renderplugin" />
                            <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>config.php" />
                            <input type="hidden" name="chmod_path" value="true" />
                            <div class="form-group" style="margin: 25px 0 0 46px;;">
                                <label><small><?php _e("Here you can define which chmod should be set and try to fix this problem. Otherwise you have to change the permissions manually"); ?></small></label><br />
                                <input type="text" name="chmod" style="margin-right: 10px; width: 75px; height: 24px;" value="<?php echo Params::getParam('chmod'); ?>" placeholder="0755">
                                <button class="btn btn-green"><?php _e("Chmod path", "spamprotection"); ?></button>
                            </div>
                        </form>
                    </p>
                    <?php if (isset($chmod_error)) { echo '<p>'.$chmod_error.'</p>'; } ?>
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
                    <?php if (isset($export)) { echo '<p>'.$export.'</p>'; } ?>
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
                                <td style="width: 70px; text-align: right;"><a href="<?php echo $url.'/export/'.$v; ?>" download="download"><?php _e("Download", "spamprotection"); ?></a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
            <?php } ?>
            </fieldset>
        </div>

        <div id="sp_config_import" class="subtab-content <?php echo (isset($import) ? 'current' : ''); ?>">
            <h2><?php _e("Import settings and data", "spamprotection"); ?></h2>                
            
            <fieldset id="#sp_import_file" style="border: 1px solid #bbb; padding: 15px; margin: 15px 0;">
            <legend><?php _e("Files on Server", "spamprotection"); ?></legend>
            <?php if (!empty($import_files)) { ?>                
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
                <div class="file-upload-container">
                    <div class="file-upload-override-button file-button">
                        <?php _e("Choose file for import", "spamprotection"); ?>
                        <input type="file" class="file-upload-button" id="file-upload-button"/>
                    </div>
                    <div class="file-upload-filename" id="file-upload-filename"><?php _e("No file selected", "spamprotection"); ?></div>
                    <div style="clear: both;"></div>
                </div>
                <script>
                $("#file-upload-button").change(function () {
                    var fileName = $(this).val().replace('C:\\fakepath\\', '');
                    $("#file-upload-filename").html(fileName);
                });
                </script>        
            </fieldset>
            
            <?php //if (isset($import)) { echo '<p>'.$import.'</p>'; } ?>
            <?php if (isset($import)) { print_r($import); } ?>
        </div>

    </div>
</div>

