<?php
if (!defined('OC_ADMIN'))
    exit('Direct access is not allowed.');
	 if (!osc_is_admin_user_logged_in()) {
    die;
}
$sp = new spam_prot;
$params = Params::getParamsAsArray();

$comment = $sp->_getRow('t_sp_comments', array('key' => 'pk_i_id', 'value' => $params['id']));
$comment_data = $sp->_getRow('t_comment', array('key' => 'pk_i_id', 'value' => $comment['fk_i_comment_id']));

$user = User::newInstance()->findByPrimaryKey($comment['fk_i_user_id']);
$comments = $sp->_countRows('t_comment', array('key' => 's_author_name', 'value' => $user['s_name']));
?>

<div id="view_comment">

    <h3><?php echo $comment['s_reason']; ?></h3>

    <div class="comment_wrapper">
        <div class="author_info infobox halfrow">
            <h4><?php _e("About the Author", "spamprotection"); ?></h4>
            <p><?php echo sprintf(__("Name: %s", "spamprotection"), $user['s_name']); ?></p>
            <p><?php echo sprintf(__("Registered since: %s", "spamprotection"), $user['dt_reg_date']); ?></p>
            <p><?php echo sprintf(__("Used E-Mail Address: %s", "spamprotection"), $comment['s_user_mail']); ?></p>
            <p><?php echo sprintf(__("Comments by user: %d", "spamprotection"), $comments); ?></p>
        </div>
        <div class="comment_info infobox halfrow">
            <h4><?php _e("About the Comment", "spamprotection"); ?></h4>
            <p class="comment_title"><?php echo sprintf(__("Title: %s", "spamprotection"), $comment_data['s_title']); ?></p>
            <p class="comment_body"><?php echo sprintf(__("Comment: %s", "spamprotection"), $comment_data['s_body']); ?></p>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="comment_actions halfrow">
        <a class="btn btn-submit" onclick="return confirm('<?php _e("Are you sure you want to activate this comment?", "spamprotection"); ?>');" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/comments_check.php?action=activate&id='.$params['id']); ?>"><?php _e('Activate comment', 'spamprotection'); ?></a>
        <a class="btn" onclick="return confirm('<?php _e("Are you sure you want to delete this comment?", "spamprotection"); ?>');" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/comments_check.php?action=delete&id='.$params['id']); ?>"><?php _e('Delete comment', 'spamprotection'); ?></a>
        <a class="btn btn-red" onclick="return confirm('<?php _e("Are you sure you want to delete this comment and block this user for comments?", "spamprotection"); ?>');" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/comments_check.php?action=block&id='.$params['id']); ?>"><?php _e('Delete comment and block user', 'spamprotection'); ?></a>
    </div>

</div>