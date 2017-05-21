<?php

$u = Params::getParam("user");
$set = Params::getParam("set");

if (!empty($u) && !empty($set)) {
    spam_prot::newInstance()->_userManageAjax($set, $u);    
}


$search = Params::getParam("searchNewTrusted");
$user = spam_prot::newInstance()->_searchBadOrTrustedUser($search);

if ($user) {
    foreach($user as $v) {
        echo '
        <tr>
            <td>'.($v['i_reputation'] == '1' ? '<i class="sp-icon thumbsdown xs"></i>' : ($v['i_reputation'] == '2' ? '<i class="sp-icon thumbsup xs"></i>' : '')).'</td>
            <td>'.$v['s_name'].'</td>
            <td>'.$v['s_email'].'</td>
            <td>
                '.($v['i_reputation'] == '1' ? '
                    <a class="action_bot" href="'.osc_ajax_plugin_url('spamprotection/functions/search.php&user='.$v['pk_i_id'].'&set=2').'"><i class="sp-icon thumbsup xs"></i></a>
                    <a class="action_bot" href="'.osc_ajax_plugin_url('spamprotection/functions/search.php&user='.$v['pk_i_id'].'&set=remove').'"><i class="sp-icon delete xs"></i></a>
                ' : ($v['i_reputation'] == '2' ? '
                    <a class="action_bot" href="'.osc_ajax_plugin_url('spamprotection/functions/search.php&user='.$v['pk_i_id'].'&set=1').'"><i class="sp-icon thumbsdown xs"></i></a>
                    <a class="action_bot" href="'.osc_ajax_plugin_url('spamprotection/functions/search.php&user='.$v['pk_i_id'].'&set=remove').'"><i class="sp-icon delete xs"></i></a>
                ' : '
                    <a class="action_bot" href="'.osc_ajax_plugin_url('spamprotection/functions/search.php&user='.$v['pk_i_id'].'&set=2').'"><i class="sp-icon thumbsup xs"></i></a>
                    <a class="action_bot" href="'.osc_ajax_plugin_url('spamprotection/functions/search.php&user='.$v['pk_i_id'].'&set=1').'"><i class="sp-icon thumbsdown xs"></i></a>                
                ')).'
            </td>
        </tr>
        ';
    }
}
?>