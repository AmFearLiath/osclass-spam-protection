<?php
class spam_prot extends DAO {
    
    private static $instance ;
    
    public static function newInstance() {
        if( !self::$instance instanceof self ) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }
    
    function __construct() {
        $this->_table_user              = '`'.DB_TABLE_PREFIX.'t_user`';
        $this->_table_admin             = '`'.DB_TABLE_PREFIX.'t_admin`';
        $this->_table_item              = '`'.DB_TABLE_PREFIX.'t_item`';
        $this->_table_comment           = '`'.DB_TABLE_PREFIX.'t_item_comment`';
        $this->_table_desc              = '`'.DB_TABLE_PREFIX.'t_item_description`';
        $this->_table_bans              = '`'.DB_TABLE_PREFIX.'t_ban_rule`';
        $this->_table_pref              = '`'.DB_TABLE_PREFIX.'t_preference`';
        $this->_table_sp_ban_log        = '`'.DB_TABLE_PREFIX.'t_spam_protection_ban_log`';
        $this->_table_sp_items          = '`'.DB_TABLE_PREFIX.'t_spam_protection_items`';
        $this->_table_sp_comments       = '`'.DB_TABLE_PREFIX.'t_spam_protection_comments`';
        $this->_table_sp_contacts       = '`'.DB_TABLE_PREFIX.'t_spam_protection_contacts`';
        $this->_table_sp_logins         = '`'.DB_TABLE_PREFIX.'t_spam_protection_logins`';
        
        parent::__construct();
    }
    
    function _install() {
        $file = osc_plugin_resource('spamprotection/assets/create_table.sql');
        $sql = file_get_contents($file);
        
        if (!$this->dao->importSQL($sql)) {
            throw new Exception( "Error importSQL::spam_prot<br>".$file ) ;
        }
        
        $opts = self::newInstance()->_opt(); $pref = self::newInstance()->_sect();       
        foreach ($opts AS $k => $v) {
            if (!osc_set_preference($k, $v[0], $pref, $v[1])) {
                return false;    
            }
        }
        return true;            
    }
    
    function _uninstall() {
        $pref = $this->_sect();                
        Preference::newInstance()->delete(array("s_section" => $pref));    
        $this->dao->query(sprintf('DROP TABLE %s', $this->_table_sp_items));    
        $this->dao->query(sprintf('DROP TABLE %s', $this->_table_sp_comments));    
        $this->dao->query(sprintf('DROP TABLE %s', $this->_table_sp_contacts));    
    }
    
    function _sect() {
        return 'plugin_spamprotection';
    }
    
    function _opt($key = false) {                
        $opts = array(
            'sp_activate'                   => array('1', 'BOOLEAN'),
            'sp_comment_activate'           => array('1', 'BOOLEAN'),
            'sp_contact_activate'           => array('1', 'BOOLEAN'),
            'sp_security_activate'          => array('1', 'BOOLEAN'),
            'sp_admin_activate'             => array('1', 'BOOLEAN'),
            'sp_duplicates'                 => array('1', 'BOOLEAN'),
            'sp_duplicates_as'              => array('1', 'BOOLEAN'),
            'sp_duplicate_type'             => array('0', 'BOOLEAN'),
            'sp_duplicate_perc'             => array('85', 'BOOLEAN'),
            'sp_duplicates_time'            => array('30', 'BOOLEAN'),
            'sp_honeypot'                   => array('0', 'BOOLEAN'),
            'sp_contact_honeypot'           => array('0', 'BOOLEAN'),
            'honeypot_name'                 => array('sp_price_range', 'STRING'),
            'contact_honeypot_name'         => array('yourDate', 'STRING'),
            'contact_honeypot_value'        => array('asap', 'STRING'),
            'sp_blocked'                    => array('0', 'BOOLEAN'),
            'sp_blocked_tld'                => array('0', 'BOOLEAN'),
            'sp_blockedtype'                => array('strpos', 'STRING'),
            'sp_comment_blocked'            => array('0', 'BOOLEAN'),
            'sp_comment_blocked_tld'        => array('0', 'BOOLEAN'),
            'sp_comment_blockedtype'        => array('strpos', 'STRING'),
            'sp_contact_blocked'            => array('0', 'BOOLEAN'),
            'sp_contact_blocked_tld'        => array('0', 'BOOLEAN'),
            'sp_contact_blockedtype'        => array('strpos', 'STRING'),
            'sp_mxr'                        => array('0', 'BOOLEAN'),
            'blocked'                       => array('', 'STRING'),
            'blocked_tld'                   => array('', 'STRING'),
            'comment_blocked'               => array('', 'STRING'),
            'comment_blocked_tld'           => array('', 'STRING'),
            'contact_blocked'               => array('', 'STRING'),
            'contact_blocked_tld'           => array('', 'STRING'),
            'sp_stopwords'                  => array('', 'STRING'),
            'sp_comment_stopwords'          => array('', 'STRING'),
            'sp_contact_stopwords'          => array('', 'STRING'),
            'sp_comment_links'              => array('', 'STRING'),
            'sp_contact_links'              => array('', 'STRING'),
            'sp_security_login_count'       => array('', 'STRING'),
            'sp_admin_login_count'          => array('', 'STRING'),
            'sp_security_login_time'        => array('', 'STRING'),
            'sp_admin_login_time'           => array('', 'STRING'),
            'sp_security_login_action'      => array('', 'STRING'),
            'sp_admin_login_action'         => array('', 'STRING'),
            'sp_security_login_inform'      => array('0', 'BOOLEAN'),
            'sp_admin_login_inform'         => array('0', 'BOOLEAN'),
            'sp_security_login_hp'          => array('0', 'BOOLEAN'),
            'sp_admin_login_hp'             => array('0', 'BOOLEAN'),
            'sp_security_register_hp'       => array('0', 'BOOLEAN'),
            'sp_security_recover_hp'        => array('0', 'BOOLEAN'),
            'sp_security_login_unban'       => array('0', 'STRING'),
            'sp_admin_login_unban'          => array('0', 'STRING'),
            'sp_admin_login_cron'           => array('0', 'STRING'),
            'sp_activate_topbar'            => array('1', 'BOOLEAN'),
            'sp_activate_menu'              => array('1', 'BOOLEAN'),
            'sp_menu_after'                 => array('menu_dash', 'STRING'),
            'sp_update_check'               => array('1', 'BOOLEN'),
            'sp_check_registrations'        => array('1', 'BOOLEN'),
            'sp_check_registration_mails'   => array('1', 'BOOLEN'),
        );
        
        if ($key) { return $opts[$key]; }
                
        return $opts;
    }

    function _get($opt = false, $type = false) {
        $pref = $this->_sect();
        if ($opt) {        
            return osc_get_preference($opt, $pref);
        } else {
            if (!$type) {
                $opts = array(
                    'sp_activate'                   => osc_get_preference('sp_activate', $pref),
                    'sp_comment_activate'           => osc_get_preference('sp_comment_activate', $pref),
                    'sp_contact_activate'           => osc_get_preference('sp_contact_activate', $pref),
                    'sp_security_activate'          => osc_get_preference('sp_security_activate', $pref),
                    'sp_admin_activate'             => osc_get_preference('sp_admin_activate', $pref),
                    'sp_duplicates'                 => osc_get_preference('sp_duplicates', $pref),
                    'sp_duplicates_as'              => osc_get_preference('sp_duplicates_as', $pref),
                    'sp_duplicate_type'             => osc_get_preference('sp_duplicate_type', $pref),
                    'sp_duplicate_perc'             => osc_get_preference('sp_duplicate_perc', $pref),
                    'sp_duplicates_time'            => osc_get_preference('sp_duplicates_time', $pref),
                    'sp_honeypot'                   => osc_get_preference('sp_honeypot', $pref),
                    'sp_contact_honeypot'           => osc_get_preference('sp_contact_honeypot', $pref),
                    'honeypot_name'                 => osc_get_preference('honeypot_name', $pref),
                    'contact_honeypot_name'         => osc_get_preference('contact_honeypot_name', $pref),
                    'contact_honeypot_value'        => osc_get_preference('contact_honeypot_value', $pref),
                    'sp_blocked'                    => osc_get_preference('sp_blocked', $pref),
                    'sp_blocked_tld'                => osc_get_preference('sp_blocked_tld', $pref),
                    'sp_blockedtype'                => osc_get_preference('sp_blockedtype', $pref),
                    'sp_comment_blocked'            => osc_get_preference('sp_comment_blocked', $pref),
                    'sp_comment_blocked_tld'        => osc_get_preference('sp_comment_blocked_tld', $pref),
                    'sp_comment_blockedtype'        => osc_get_preference('sp_comment_blockedtype', $pref),
                    'sp_contact_blocked'            => osc_get_preference('sp_contact_blocked', $pref),
                    'sp_contact_blocked_tld'        => osc_get_preference('sp_contact_blocked_tld', $pref),
                    'sp_contact_blockedtype'        => osc_get_preference('sp_contact_blockedtype', $pref),
                    'sp_mxr'                        => osc_get_preference('sp_mxr', $pref),
                    'blocked'                       => osc_get_preference('blocked', $pref),
                    'blocked_tld'                   => osc_get_preference('blocked_tld', $pref),
                    'comment_blocked'               => osc_get_preference('comment_blocked', $pref),
                    'comment_blocked_tld'           => osc_get_preference('comment_blocked_tld', $pref),
                    'contact_blocked'               => osc_get_preference('contact_blocked', $pref),
                    'contact_blocked_tld'           => osc_get_preference('contact_blocked_tld', $pref),
                    'sp_stopwords'                  => osc_get_preference('sp_stopwords', $pref),
                    'sp_comment_stopwords'          => osc_get_preference('sp_comment_stopwords', $pref),
                    'sp_contact_stopwords'          => osc_get_preference('sp_contact_stopwords', $pref),
                    'sp_comment_links'              => osc_get_preference('sp_comment_links', $pref),
                    'sp_contact_links'              => osc_get_preference('sp_contact_links', $pref),
                    'sp_security_login_count'       => osc_get_preference('sp_security_login_count', $pref),
                    'sp_admin_login_count'          => osc_get_preference('sp_admin_login_count', $pref),
                    'sp_security_login_time'        => osc_get_preference('sp_security_login_time', $pref),
                    'sp_admin_login_time'           => osc_get_preference('sp_admin_login_time', $pref),
                    'sp_security_login_action'      => osc_get_preference('sp_security_login_action', $pref),
                    'sp_admin_login_action'         => osc_get_preference('sp_admin_login_action', $pref),
                    'sp_security_login_inform'      => osc_get_preference('sp_security_login_inform', $pref),
                    'sp_admin_login_inform'         => osc_get_preference('sp_admin_login_inform', $pref),
                    'sp_security_login_hp'          => osc_get_preference('sp_security_login_hp', $pref),
                    'sp_admin_login_hp'             => osc_get_preference('sp_admin_login_hp', $pref),
                    'sp_security_register_hp'       => osc_get_preference('sp_security_register_hp', $pref),
                    'sp_security_recover_hp'        => osc_get_preference('sp_security_recover_hp', $pref),
                    'sp_security_login_unban'       => osc_get_preference('sp_security_login_unban', $pref),
                    'sp_admin_login_unban'          => osc_get_preference('sp_admin_login_unban', $pref),
                    'sp_security_login_cron'        => osc_get_preference('sp_security_login_cron', $pref),
                    'sp_admin_login_cron'           => osc_get_preference('sp_admin_login_cron', $pref),
                    'sp_admin_login_cron'           => osc_get_preference('sp_admin_login_cron', $pref),
                    'sp_check_registrations'        => osc_get_preference('sp_check_registrations', $pref),
                    'sp_check_registration_mails'   => osc_get_preference('sp_check_registration_mails', $pref),
                );
            } else {
                $opts = array(                
                    'sp_activate_topbar'        => osc_get_preference('sp_activate_topbar', $pref),
                    'sp_activate_menu'          => osc_get_preference('sp_activate_menu', $pref),
                    'sp_menu_after'             => osc_get_preference('sp_menu_after', $pref),
                    'sp_update_check'           => osc_get_preference('sp_update_check', $pref),
                );    
            }
            return $opts;
        }
    }
    
    function _admin_menu_draw() {
        $count = $this->_countRows('t_item', array('key' => 'b_spam', 'value' => '1'));
        $comments = $this->_countRows('t_comment', array('key' => 'b_spam', 'value' => '1'));
        $contacts = $this->_countRows('t_sp_contacts');
        $bans = $this->_countRows('t_sp_ban_log');
        $topbar = false;
        
        if ($count > 0 || $comments > 0 || $contacts > 0 || $bans > 0) {
            osc_add_admin_submenu_divider('spamprotection', __('Actions', 'spamprotection'), 'spamprotection_separator', 'administrator');    
        } if (spam_prot::newInstance()->_get('sp_activate_topbar') == '1') {
            $topbar = true;   
        }
        if ($count > 0) {
            osc_add_admin_submenu_page('spamprotection', sprintf(__('%s Spam found', 'spamprotection'), $count), osc_admin_base_url(true).'?page=items&b_spam=1', 'spamprotection_spam_found', 'administrator');
            
            if ($topbar) {
                AdminToolbar::newInstance()->add_menu( array(
                     'id'        => 'spamprotection',
                     'title'     => '<i class="circle circle-gray">'.$count.'</i>'.__('Spam found', 'spamprotection'),
                     'href'      => osc_admin_base_url(true).'?page=items&b_spam=1',
                     'meta'      => array('class' => 'action-btn action-btn-black'),
                     'target'    => '_self'
                 ));
            }
        }
        if ($comments > 0) {
            osc_add_admin_submenu_page('spamprotection', sprintf(__('%s Spam comment found', 'spamprotection'), $comments), osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/comments_check.php'), 'spamprotection_spamcomment_found', 'administrator');
            
            if ($topbar) {
                AdminToolbar::newInstance()->add_menu( array(
                     'id'        => 'spamprotection_comments',
                     'title'     => '<i class="circle circle-gray">'.$comments.'</i>'.__('Spam comment found', 'spamprotection'),
                     'href'      => osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/comments_check.php'),
                     'meta'      => array('class' => 'action-btn action-btn-black'),
                     'target'    => '_self'
                 ));
            }
        }
        if ($contacts > 0) {
            osc_add_admin_submenu_page('spamprotection', sprintf(__('%s Spam Contact Mail found', 'spamprotection'), $contacts), osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/contact_check.php'), 'spamprotection_spamcontact_found', 'administrator');
            
            if ($topbar) {
                AdminToolbar::newInstance()->add_menu( array(
                     'id'        => 'spamprotection_contacts',
                     'title'     => '<i class="circle circle-gray">'.$contacts.'</i>'.__('Spam Contact Mail found', 'spamprotection'),
                     'href'      => osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/contact_check.php'),
                     'meta'      => array('class' => 'action-btn action-btn-black'),
                     'target'    => '_self'
                 ));
            }
        }
        if ($bans > 0) {
            osc_add_admin_submenu_page('spamprotection', sprintf(__('%s Banned user', 'spamprotection'), $bans), osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/ban_log.php'), 'spamprotection_bans_found', 'administrator');
            
            if ($topbar) {
                AdminToolbar::newInstance()->add_menu( array(
                     'id'        => 'spamprotection_bans',
                     'title'     => '<i class="circle circle-gray">'.$bans.'</i>'.__('Banned user', 'spamprotection'),
                     'href'      => osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/ban_log.php'),
                     'meta'      => array('class' => 'action-btn action-btn-black'),
                     'target'    => '_self'
                 ));
            }
        }
    }
    
    function _getRow($table, $where = false, $orderBy = false, $orderDir = 'DESC') {
        
        if ($table == 't_item') { $table = $this->_table_item; }
        elseif ($table == 't_desc') { $table = $this->_table_desc; }
        elseif ($table == 't_user')  { $table = $this->_table_user; }
        elseif ($table == 't_comment')  { $table = $this->_table_comment; }
        elseif ($table == 't_sp_ban_log')  { $table = $this->_table_sp_ban_log; }
        elseif ($table == 't_sp_items')  { $table = $this->_table_sp_items; }
        elseif ($table == 't_sp_comments')  { $table = $this->_table_sp_comments; }
        elseif ($table == 't_sp_contacts')  { $table = $this->_table_sp_contacts; }
        
        $this->dao->select('*');
        $this->dao->from($table);
        
        if (is_array($where)) {
            $this->dao->where($where['key'], $where['value']);    
        }
        
        if ($orderBy) {
            $this->dao->orderBy($orderBy, $orderDir);    
        }          
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        return $result->row();
    }
    
    function _getResult($table, $where = false, $orderBy = false, $orderDir = 'DESC') {
        
        if ($table == 't_item') { $table = $this->_table_item; }
        elseif ($table == 't_item_description')  { $table = $this->_table_desc; }
        elseif ($table == 't_user')  { $table = $this->_table_user; }
        elseif ($table == 't_comment')  { $table = $this->_table_comment; }
        elseif ($table == 't_sp_ban_log')  { $table = $this->_table_sp_ban_log; }
        elseif ($table == 't_sp_items')  { $table = $this->_table_sp_items; }
        elseif ($table == 't_sp_comments')  { $table = $this->_table_sp_comments; }
        elseif ($table == 't_sp_contacts')  { $table = $this->_table_sp_contacts; }
        
        $this->dao->select('*');
        $this->dao->from($table);
        
        if (is_array($where)) {
            $this->dao->where($where['key'], $where['value']);    
        }
        
        if ($orderBy) {
            $this->dao->orderBy($orderBy, $orderDir);    
        }                
        
        $result = $this->dao->get();
        if ($result->numRows() <= 0) { return false; }
        
        return $result->result();
    }
    
    function _countRows($table, $where = false) {
        
        if ($table == 't_item') { $table = $this->_table_item; }
        elseif ($table == 't_user')  { $table = $this->_table_user; }
        elseif ($table == 't_bans')  { $table = $this->_table_bans; }
        elseif ($table == 't_sp_items')  { $table = $this->_table_sp_items; }
        elseif ($table == 't_comment')  { $table = $this->_table_comment; }
        elseif ($table == 't_sp_ban_log')  { $table = $this->_table_sp_ban_log; }
        elseif ($table == 't_sp_contacts')  { $table = $this->_table_sp_contacts; }
        
        $this->dao->select('count(*) as count');
        $this->dao->from($table);
        
        if (is_array($where)) {
            $this->dao->where($where['key'], $where['value']);    
        }        
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        $row = $result->row();
        return $row['count'];
    }
    
    function _sort($sort) {
        $sort = explode(",", $sort);
        sort($sort);
        return implode(",", $sort);    
    }
    
    function _saveSettings($params, $type = false) {
            
        $pref = $this->_sect();
        $forbidden = array('CSRFName', 'CSRFToken', 'page', 'file', 'action', 'tab', 'subtab', 'settings', 'plugin_settings');
    
        if (!$type) {        
            $data = array(
                'sp_activate'                   => (isset($params['sp_activate']) ? $params['sp_activate'] : ''),
                'sp_comment_activate'           => (isset($params['sp_comment_activate']) ? $params['sp_comment_activate'] : ''),
                'sp_contact_activate'           => (isset($params['sp_contact_activate']) ? $params['sp_contact_activate'] : ''),
                'sp_security_activate'          => (isset($params['sp_security_activate']) ? $params['sp_security_activate'] : ''),
                'sp_admin_activate'             => (isset($params['sp_admin_activate']) ? $params['sp_admin_activate'] : ''),
                'sp_duplicates'                 => (isset($params['sp_duplicates']) ? $params['sp_duplicates'] : ''),
                'sp_duplicates_as'              => (isset($params['sp_duplicates_as']) ? $params['sp_duplicates_as'] : ''),
                'sp_duplicate_type'             => (isset($params['sp_duplicate_type']) ? $params['sp_duplicate_type'] : ''),
                'sp_duplicate_perc'             => (isset($params['sp_duplicate_perc']) ? $params['sp_duplicate_perc'] : ''),
                'sp_duplicates_time'            => (isset($params['sp_duplicates_time']) ? $params['sp_duplicates_time'] : ''),
                'sp_honeypot'                   => (isset($params['sp_honeypot']) ? $params['sp_honeypot'] : ''),
                'sp_contact_honeypot'           => (isset($params['sp_contact_honeypot']) ? $params['sp_contact_honeypot'] : ''),
                'honeypot_name'                 => (isset($params['honeypot_name']) ? $params['honeypot_name'] : ''),
                'contact_honeypot_name'         => (isset($params['contact_honeypot_name']) ? $params['contact_honeypot_name'] : ''),
                'contact_honeypot_value'        => (isset($params['contact_honeypot_value']) ? $params['contact_honeypot_value'] : ''),
                'sp_blocked'                    => (isset($params['sp_blocked']) ? $params['sp_blocked'] : ''),
                'sp_blocked_tld'                => (isset($params['sp_blocked_tld']) ? $params['sp_blocked_tld'] : ''),
                'sp_blockedtype'                => (isset($params['sp_blockedtype']) ? $params['sp_blockedtype'] : ''),
                'sp_comment_blocked'            => (isset($params['sp_comment_blocked']) ? $params['sp_comment_blocked'] : ''),
                'sp_comment_blocked_tld'        => (isset($params['sp_comment_blocked_tld']) ? $params['sp_comment_blocked_tld'] : ''),
                'sp_comment_blockedtype'        => (isset($params['sp_comment_blockedtype']) ? $params['sp_comment_blockedtype'] : ''),
                'sp_contact_blocked'            => (isset($params['sp_contact_blocked']) ? $params['sp_contact_blocked'] : ''),
                'sp_contact_blocked_tld'        => (isset($params['sp_contact_blocked_tld']) ? $params['sp_contact_blocked_tld'] : ''),
                'sp_contact_blockedtype'        => (isset($params['sp_contact_blockedtype']) ? $params['sp_contact_blockedtype'] : ''),
                'sp_mxr'                        => (isset($params['sp_mxr']) ? $params['sp_mxr'] : ''),
                'blocked'                       => (isset($params['blocked']) ? $this->_sort($params['blocked']) : ''),
                'blocked_tld'                   => (isset($params['blocked_tld']) ? $this->_sort($params['blocked_tld']) : ''),
                'comment_blocked'               => (isset($params['comment_blocked']) ? $this->_sort($params['comment_blocked']) : ''),
                'comment_blocked_tld'           => (isset($params['comment_blocked_tld']) ? $this->_sort($params['comment_blocked_tld']) : ''),
                'contact_blocked'               => (isset($params['contact_blocked']) ? $this->_sort($params['contact_blocked']) : ''),
                'contact_blocked_tld'           => (isset($params['contact_blocked_tld']) ? $this->_sort($params['contact_blocked_tld']) : ''),
                'sp_stopwords'                  => (isset($params['sp_stopwords']) ? $this->_sort($params['sp_stopwords']) : ''),
                'sp_comment_stopwords'          => (isset($params['sp_comment_stopwords']) ? $this->_sort($params['sp_comment_stopwords']) : ''),
                'sp_contact_stopwords'          => (isset($params['sp_contact_stopwords']) ? $this->_sort($params['sp_contact_stopwords']) : ''),
                'sp_comment_links'              => (isset($params['sp_comment_links']) ? $params['sp_comment_links'] : ''),
                'sp_contact_links'              => (isset($params['sp_contact_links']) ? $params['sp_contact_links'] : ''),
                'sp_security_login_count'       => (isset($params['sp_security_login_count']) ? $params['sp_security_login_count'] : ''),
                'sp_admin_login_count'          => (isset($params['sp_admin_login_count']) ? $params['sp_admin_login_count'] : ''),
                'sp_security_login_time'        => (isset($params['sp_security_login_time']) ? $params['sp_security_login_time'] : ''),
                'sp_admin_login_time'           => (isset($params['sp_admin_login_time']) ? $params['sp_admin_login_time'] : ''),
                'sp_security_login_action'      => (isset($params['sp_security_login_action']) ? $params['sp_security_login_action'] : ''),
                'sp_admin_login_action'         => (isset($params['sp_admin_login_action']) ? $params['sp_admin_login_action'] : ''),
                'sp_security_login_inform'      => (isset($params['sp_security_login_inform']) ? $params['sp_security_login_inform'] : ''),
                'sp_admin_login_inform'         => (isset($params['sp_admin_login_inform']) ? $params['sp_admin_login_inform'] : ''),
                'sp_security_login_hp'          => (isset($params['sp_security_login_hp']) ? $params['sp_security_login_hp'] : ''),
                'sp_admin_login_hp'             => (isset($params['sp_admin_login_hp']) ? $params['sp_admin_login_hp'] : ''),
                'sp_security_register_hp'       => (isset($params['sp_security_register_hp']) ? $params['sp_security_register_hp'] : ''),
                'sp_security_recover_hp'        => (isset($params['sp_security_recover_hp']) ? $params['sp_security_recover_hp'] : ''),
                'sp_security_login_unban'       => (isset($params['sp_security_login_unban']) ? $params['sp_security_login_unban'] : ''),
                'sp_admin_login_unban'          => (isset($params['sp_admin_login_unban']) ? $params['sp_admin_login_unban'] : ''),
                'sp_security_login_cron'        => (isset($params['sp_security_login_cron']) ? $params['sp_security_login_cron'] : ''),
                'sp_admin_login_cron'           => (isset($params['sp_admin_login_cron']) ? $params['sp_admin_login_cron'] : ''),
                'sp_check_registrations'        => (isset($params['sp_check_registrations']) ? $params['sp_check_registrations'] : ''),
                'sp_check_registration_mails'   => (isset($params['sp_check_registration_mails']) ? $this->_sort($params['sp_check_registration_mails']) : ''),
            );
            
            if (!empty($params['sp_htaccess'])) {
                $htaccess_file = ABS_PATH.'/.htaccess';
                $htaccess_writable = is_writable($htaccess_file);
                if ($htaccess_writable) {
                    if (!file_put_contents($htaccess_file, $params['sp_htaccess'])) {
                        return false;                       
                    }    
                }
            }
        } else {
            $data = array(
                'sp_activate_topbar'        => (isset($params['sp_activate_topbar']) ? $params['sp_activate_topbar'] : ''),
                'sp_activate_menu'          => (isset($params['sp_activate_menu']) ? $params['sp_activate_menu'] : ''),
                'sp_menu_after'             => (isset($params['sp_menu_after']) ? $params['sp_menu_after'] : ''),
                'sp_update_check'           => (isset($params['sp_update_check']) ? $params['sp_update_check'] : ''),
            );    
        }
        
        foreach($data as $k => $v) {
            if (!in_array($k, $forbidden)) {
                $opt = $this->_opt($k);
                if (empty($v)) { 
                    osc_delete_preference($k, $pref); 
                } else {
                    if (!osc_set_preference($k, $v, $pref, $opt[1])) {
                        return false;
                    }   
                }
                
            }
        }
        return true;
    }
    
    function _markAsSpam($data, $reason) {        
        $this->dao->update($this->_table_item, array('b_active' => '0', 'b_spam' => '1'), array('pk_i_id' => $data['pk_i_id']));        
        $this->dao->insert($this->_table_sp_items, array(
            'fk_i_item_id'  => $data['fk_i_item_id'], 
            'fk_i_user_id'  => $data['fk_i_user_id'], 
            's_reason'      => $reason, 
            's_user_mail'   => $data['s_contact_email']));
    }
    
    function _markCommentAsSpam($data, $reason) {        
        $this->dao->update($this->_table_comment, array('b_active' => '0', 'b_enabled' => '0', 'b_spam' => '1'), array('pk_i_id' => $data['pk_i_id']));        
        $this->dao->insert($this->_table_sp_comments, array(
            'fk_i_comment_id'   => $data['pk_i_id'], 
            'fk_i_item_id'      => $data['fk_i_item_id'], 
            'fk_i_user_id'      => $data['fk_i_user_id'], 
            's_reason'          => $reason, 
            's_user_mail'       => $data['s_author_email']));        
    }
    
    function _markContactAsSpam($data, $reason, $token) {        
        $this->dao->insert($this->_table_sp_contacts, array( 
            'fk_i_item_id'      => $data['id'], 
            's_user'            => $data['yourName'], 
            'fk_i_user_id'      => $data['user_id'], 
            's_user_mail'       => $data['yourEmail'], 
            's_user_phone'      => $data['phoneNumber'], 
            's_user_message'    => $data['message'],
            's_reason'          => $reason,
            's_token'           => $token,
            )
        );       
    }
    
    function _spamAction($type, $id, $ip = false) {        
        if ($type == 'activate') {
            $this->dao->update($this->_table_item, array('b_spam' => '0', 'b_enabled' => '1', 'b_active' => '1'), array('pk_i_id' => $id));    
        } elseif ($type == 'block') {
            $this->dao->update($this->_table_user, array('b_enabled' => '0'), array('s_email' => $id));
            $this->_addBanLog('block', 'spam', $id, $ip);    
        } elseif ($type == 'ban') {
            $reason = __("Spam Protection - Banned because of spam ads", "spamprotection");
            $this->dao->insert($this->_table_bans, array('s_name' => $reason, 's_email' => $id));
            $this->_addBanLog('ban', 'spam', $id, $ip);    
        }        
        return false;
    }
    
    function _spamActionComments($type, $id) {    
        $comment = $this->_getRow('t_sp_comments', array('key' => 'pk_i_id', 'value' => $id));
            
        if ($type == 'activate') {
            $this->dao->update($this->_table_comment, array('b_active' => '1', 'b_enabled' => '1', 'b_spam' => '0'), array('pk_i_id' => $comment['fk_i_comment_id']));    
            $this->dao->delete($this->_table_sp_comments, 'pk_i_id = '.$comment['pk_i_id']);    
        } elseif ($type == 'delete') {
            $this->dao->delete($this->_table_comment, 'pk_i_id = '.$comment['fk_i_comment_id']);    
            $this->dao->delete($this->_table_sp_comments, 'pk_i_id = '.$comment['pk_i_id']);    
        } elseif ($type == 'block') {                    
            $blocked = explode(",", $this->_get('comment_blocked'));            
            if (!in_array($comment['s_user_mail'], $blocked)) { $blocked[] = $comment['s_user_mail']; }            
            $blocked = implode(",", array_filter($blocked));            
            osc_set_preference('comment_blocked', $blocked, $this->_sect(), 'STRING');
            
            $this->dao->delete($this->_table_comment, 'pk_i_id = '.$comment['fk_i_comment_id']);    
            $this->dao->delete($this->_table_sp_comments, 'pk_i_id = '.$comment['pk_i_id']);    
        }        
        return false;
    }
    
    function _spamActionContacts($type, $id) {
        $contact = $this->_getRow('t_sp_contacts', array('key' => 'pk_i_id', 'value' => $id));        
        if ($type == 'forward') {
            if ($this->_forwardMail($contact, $contact['fk_i_item_id'])) {
                $this->dao->delete($this->_table_sp_contacts, 'pk_i_id = '.$id);
            }                
        } elseif ($type == 'delete') {                
            $this->dao->delete($this->_table_sp_contacts, 'pk_i_id = '.$id);    
        } elseif ($type == 'block') {                    
            $blocked = explode(",", $this->_get('contact_blocked'));            
            if (!in_array($contact['s_user_mail'], $blocked)) { $blocked[] = $contact['s_user_mail']; }            
            $blocked = implode(",", array_filter($blocked));            
            osc_set_preference('contact_blocked', $blocked, $this->_sect(), 'STRING');
            $this->dao->delete($this->_table_sp_contacts, 'pk_i_id = '.$id);
        }
        
        return true;
        
    }
    
    function _checkForSpam($item, $type = 'mail') {
        $params = Params::getParamsAsArray(); 
        
        if ($type == 'mail') { $user = $item['s_contact_email']; } 
        else { $user = $item['fk_i_user_id']; }
        
        // Check for Honeypot
        if ($this->_get('sp_honeypot') == '1') {
            if ($this->_checkHoneypot($params)) {
                return array('params' => $item, 'reason' => 'Bot detected. The Honeypot was filled while creating an ad');    
            }
        }         
        
        // Check for blocked mailaddresses
        $blocked = $this->_get('blocked');
        if ($this->_get('sp_blocked') == '1' && !empty($blocked)) {
            if ($this->_checkBlocked($item['s_contact_email'])) {
                return array('params' => $item, 'reason' => 'Blocked E-Mail-Address found. Please check this ad manually');    
            }
        }         
        
        // Check for blocked mailaddress tld
        $blocked_tld = $this->_get('blocked_tld');
        if ($this->_get('sp_blocked_tld') == '1' && !empty($blocked_tld)) {
            if ($this->_checkBlockedTLD($item['s_contact_email'])) {
                return array('params' => $item, 'reason' => 'Blocked E-Mail-Address TLD found. Please check this ad manually');    
            }
        }         
        
        // Check for MX Record
        if ($this->_get('sp_mxr') == '1') {
            $check = $this->_validateMail($item['s_contact_email'], true);
            if ($check['status'] == false) {
                return array('params' => $item, 'reason' => $check['message']);    
            }
        }
        
        // Check for stopwords
        if ($this->_get('sp_stopwords')) {
            $stopwords = $this->_checkStopwords($item);
            if ($stopwords) {
                return array('params' => $item, 'reason' => 'Bad/Stopword found ('.$stopwords.'). Please check this ad manually');    
            }
        }          
        
        // Check for duplicates
        $locale = $item['locale'];        
        
        $sia = $this->_get('sp_duplicates_as');
        $sim = $this->_get('sp_duplicates');
        
        if ($sia == '1') {
            $items  = $this->_getItemsByUser($user);            
        } elseif ($sia == '2') {
            $items  = $this->_getItemsByAll($user);    
        }
        
        if ($sia == '1' || $sia == '2') {
            foreach ($locale as $lk => $lv) {
                foreach ($items as $ik => $iv) {
                    $checkTitle = $this->_checkForTitle($iv['pk_i_id'], $item['fk_i_item_id'], $lk);
                    if ($checkTitle && $item['pk_i_id'] != $iv['pk_i_id']) {
                        return array('params' => $item, 'reason' => '<a href="'.osc_admin_base_url(true).'?page=items&action=item_edit&id='.$iv['pk_i_id'].'">Duplicate title found for ItemID: '.$iv['pk_i_id'].'. '.(is_numeric($checkTitle) ? 'Similarity: '.$checkTitle.'%' : '').'</a> Please check this ad manually');    
                    }
                    
                    if ($this->_get('sp_duplicates') == '1') {
                        $checkDescription = $this->_checkForDescription($iv['pk_i_id'], $item['fk_i_item_id'], $lk);
                        if ($checkDescription && $item['pk_i_id'] != $iv['pk_i_id']) {
                            return array('params' => $item, 'reason' => '<a href="'.osc_admin_base_url(true).'?page=items&action=item_edit&id='.$iv['pk_i_id'].'">Duplicate description found for ItemID: '.$iv['pk_i_id'].'. '.(is_numeric($checkDescription) ? 'Similarity: '.$checkDescription.'%' : '').'</a> Please check this ad manually');    
                        }    
                    }       
                }    
            }
        }
        
        // No spam found
        return false;        
    }
    
    function _checkComment($id) {
        $comment = $this->_getComment($id);        
        $spcs = $this->_get('sp_comment_links');
        
        // Check for url's
        if ($spcs == '1' || $spcs == '2') {                    
            $regex = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";                        
            if (preg_match($regex, $comment['s_title'], $url)) { 
                return array('params' => $comment, 'reason' => 'URL found in title: '.$url[0]); 
            }            
            if ($spcs == '2' && preg_match($regex, $comment['s_body'], $url)) { 
                return array('params' => $comment, 'reason' => 'URL found in comment: '.$url[0]); 
            }                               
        }         
        
        // Check for blocked mailaddresses
        $blocked = $this->_get('comment_blocked');
        if ($this->_get('sp_comment_blocked') == '1' && !empty($blocked)) {
            if ($this->_checkBlocked($comment['s_author_email'], 'comment')) {
                return array('params' => $comment, 'reason' => 'Blocked E-Mail-Address found.');    
            }
        }         
        
        // Check for blocked mailaddress tld
        $blocked_tld = $this->_get('comment_blocked_tld');
        if ($this->_get('sp_comment_blocked_tld') == '1' && !empty($blocked_tld)) {
            if ($this->_checkBlockedTLD($comment['s_author_email'], 'comment')) {
                return array('params' => $comment, 'reason' => 'Blocked E-Mail-Address TLD found.');    
            }
        }
        
        // Check for stopwords
        if ($this->_get('sp_comment_stopwords')) {
            $stopwords = $this->_checkStopwordsComments($comment);
            if ($stopwords) {
                return array('params' => $comment, 'reason' => 'Bad/Stopword found ('.$stopwords.').');    
            }
        }
        
        // No spam found            
        return false;
    }
    
    function _checkContact($data) {
        $params = Params::getParamsAsArray();
        $spcs = $this->_get('sp_contact_links');
        
        // Check for url's
        if ($spcs == '1') {                    
            $regex = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";                        
            if (preg_match($regex, $params['message'], $url)) { 
                return array('params' => $params, 'reason' => 'URL found in message: '.$url[0]); 
            }                               
        } 
        
        // Check for Honeypot
        if ($this->_get('sp_contact_honeypot') == '1') {
            if ($this->_checkHoneypotContact($params)) {
                return array('params' => $item, 'reason' => 'Honeypot failed. Maybe bot spam?');    
            }
        }         
        
        // Check for blocked mailaddresses
        $blocked = $this->_get('contact_blocked');
        if ($this->_get('sp_contact_blocked') == '1' && !empty($blocked)) {
            if ($this->_checkBlocked($params['yourEmail'], 'contact')) {
                return array('params' => $params, 'reason' => 'Blocked E-Mail-Address found.');    
            }
        }         
        
        // Check for blocked mailaddress tld
        $blocked_tld = $this->_get('contact_blocked_tld');
        if ($this->_get('sp_contact_blocked_tld') == '1' && !empty($blocked_tld)) {
            if ($this->_checkBlockedTLD($params['yourEmail'], 'contact')) {
                return array('params' => $params, 'reason' => 'Blocked E-Mail-Address TLD found.');    
            }
        }
        
        // Check for stopwords
        $stopword = $this->_get('sp_contact_stopwords');
        if (!empty($stopword)) {
            $stopwords = $this->_checkStopwordsContact($params);
            if ($stopwords) {
                return array('params' => $params, 'reason' => 'Bad/Stopword found ('.$stopwords.').');    
            }
        }
        
        // No spam found
        return false;
        
    }
    
    function _checkHoneypot($params) {
        if ($this->_get('honeypot_name')) {
            $hp = $this->_get('honeypot_name');
        } else {
            $hp = 'sp_price_range';
        }
                
        if (!empty($params[$hp])) { return true; }
        return false;
    }
    
    function _checkHoneypotContact($params) {
        $name = $this->_get('contact_honeypot_name');
        $name1 = 'captcha';
        $value = $this->_get('contact_honeypot_value');
        
        if (empty($name)) {
            $name = 'yourDate';
        } if (empty($value)) {
            $value = 'asap';
        }
                
        if ($params[$name] != $value) { return true; }
        if (!empty($params[$name1])) { return true; }
        
        return false;
    }
    
    function _checkBlocked($mail, $type = false) {
        if ($type == 'comment') {
            $check = explode(",", $this->_get('comment_blocked'));    
        } elseif ($type == 'contact') {
            $check = explode(",", $this->_get('contact_blocked'));    
        } else {
            $check = explode(",", $this->_get('blocked'));    
        }
        
        if (in_array($mail, $check)) {
            return true;
        }
        return false;    
    }
    
    function _checkBlockedTLD($mail, $type = false) {
        if ($type == 'comment') {
            $check = explode(",", $this->_get('comment_blocked_tld'));    
        } elseif ($type == 'contact') {
            $check = explode(",", $this->_get('contact_blocked_tld'));    
        } else {
            $check = explode(",", $this->_get('blocked_tld'));    
        }
        $check = explode(",", $this->_get('blocked_tld'));
        $tld = explode("@", $mail);
        if (in_array($tld[1], $check)) {
            return true;
        }
        return false;    
    }
    
    function _validateMail($email, $test_mx = true) {
        $email = trim($email);   
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            if ($test_mx) {  
                list($username, $domain) = explode("@", $email);  
                if (getmxrr($domain, $mxrecords)) {
                    return array('status' => true);        
                } else {
                    return array('status' => false, 'message' => 'MX Record not found. Please check this ad manually');    
                } 
            } else {
                return array('status' => true);    
            } 
        } else {
            return array('status' => false, 'message' => 'No valid E-Mail-Address. Please check this ad manually');    
        }              
    }
    
    function _checkStopwords($item) {
        $stopwords = explode(',',$this->_get('sp_stopwords'));
        
        foreach ($item['locale'] as $k => $v) {
            
            $title = strtolower($v['s_title']);
            $description = strtolower($v['s_description']);
            
            foreach ($stopwords as $sk => $sv) {
            
                $search = strtolower($sv);
                            
                if ($this->_get('sp_blockedtype') == 'words') {
                    if (!!preg_match('#\b' . preg_quote($search, '#') . '\b#i', $title)) {
                        return $sv;
                    } if (!!preg_match('#\b' . preg_quote($search, '#') . '\b#i', $description)) {
                        return $sv;
                    }    
                } else {
                    if (strpos($title, $search) !== false) {
                        return $sv;
                    } if (strpos($description, $search) !== false) {
                        return $sv;
                    }    
                }                
            }    
        }
        return false;    
    }
    
    function _checkStopwordsComments($comment) {        
        $stopwords = explode(',',$this->_get('sp_comment_stopwords'));            
        $title = strtolower(trim($comment['s_title']));
        $description = strtolower(trim($comment['s_body']));
        
        foreach ($stopwords as $sk => $sv) {        
            $search = strtolower(trim($sv));                        
            if ($this->_get('sp_comment_blockedtype') == 'words') {
                if (!!preg_match('#\b' . preg_quote($search, '#') . '\b#i', $title)) {
                    return $sv;
                } if (!!preg_match('#\b' . preg_quote($search, '#') . '\b#i', $description)) {
                    return $sv;
                }    
            } else {
                if (strpos($title, $search) !== false) {
                    return $sv;
                } if (strpos($description, $search) !== false) {
                    return $sv;
                }    
            }                
        }
            
        return false;    
    }
    
    function _checkStopwordsContact($comment) {        
        $stopwords = explode(',',$this->_get('sp_contact_stopwords'));            
        $message = strtolower(trim($comment['message']));
        
        foreach ($stopwords as $sk => $sv) {        
            $search = strtolower(trim($sv));                        
            if ($this->_get('sp_contact_blockedtype') == 'words') {
                if (!!preg_match('#\b' . preg_quote($search, '#') . '\b#i', $message)) {
                    return $sv;
                }   
            } else {
                if (strpos($message, $search) !== false) {
                    return $sv;
                }    
            }                
        }
            
        return false;    
    }
    
    function _checkForTitle($item1, $item2, $locale) {
        $check1 = $this->_getItemData($item1, $locale);
        $check2 = $this->_getItemData($item2, $locale);
        
        if ($this->_get('sp_duplicate_type') == '1') {
            
            $percent = null;
            $similar = similar_text($check1['s_title'], $check2['s_title'], $percent);
            
            if ($percent >= $this->_get('sp_duplicate_perc')) {
                return number_format($percent, 0);    
            } else {
                return false;
            }    
        } else {
            if (strtolower(trim($check1['s_title'])) == strtolower(trim($check2['s_title']))) {
                return true;    
            } else {
                return false;
            }   
        }
    } 
    
    function _checkForDescription($item1, $item2, $locale) {        
        $check1 = $this->_getItemData($item1, $locale);
        $check2 = $this->_getItemData($item2, $locale);
        
        if ($this->_get('sp_duplicate_type') == '1') {
            
            $percent = null;
            $similar = similar_text($check1['s_description'], $check2['s_description'], $percent);
            
            if ($percent >= $this->_get('sp_duplicate_perc')) {
                return number_format($percent, 0);    
            } else {
                return false;
            }    
        } else {
            if (md5(strtolower(trim($check1['s_description']))) == md5(strtolower(trim($check2['s_description'])))) {
                return true;    
            } else {
                return false;
            }    
        }
    }
    
    function _getItemsByUser($user) {
        $this->dao->select('*');
        $this->dao->from($this->_table_item);
        
        if (is_numeric($user)) { $this->dao->where("fk_i_user_id", $user); } 
        else { $this->dao->where("s_contact_email", $user); }       
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        return $result->result();
    }
    
    function _getItemsByAll() {
        $this->dao->select('*');
        $this->dao->from($this->_table_item);       
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        return $result->result();
    }
    
    function _getItemData($item, $locale) {
        
        $time = $this->_get('sp_duplicates_time');
        
        $this->dao->select('d.*');
        $this->dao->from($this->_table_desc.' as d');
        
        if ($time >= '0') {
            $this->dao->join($this->_table_item.' as i','d.fk_i_item_id = i.pk_i_id','LEFT');
            $this->dao->where("i.dt_pub_date >= '".date("Y-m-d H:i:s", (time()-($time*24*60*60)))."'");    
            $this->dao->orWhere("i.dt_mod_date >= '".date("Y-m-d H:i:s", (time()-($time*24*60*60)))."'");    
        }
        
        $this->dao->where("d.fk_i_item_id", $item);
        $this->dao->where("d.fk_c_locale_code", $locale);
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        return $result->row();
    }
    
    function _getComment($id) {
        $this->dao->select('*');
        $this->dao->from($this->_table_comment);
        $this->dao->where("pk_i_id", $id);
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        return $result->row();
    }
    
    function _checkContactUser($user) {
        $this->dao->select('*');
        $this->dao->from($this->_table_user);
        $this->dao->where("s_name", $user);
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        return $result->row();
    }
    
    function _searchSpamContact($uniqid) {        
        $this->dao->select('*');
        $this->dao->from($this->_table_sp_contacts);
        $this->dao->where("s_token", $uniqid);
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        $return = $result->row();
        return $return['pk_i_id'];
        
    }
    
    function _deleteMailByUser($id, $token) {
        $contact = $this->_getRow('t_sp_contacts', array('key' => 'pk_i_id', 'value' => $id));        
        if ($contact['s_token'] == $token) {
            $this->dao->delete($this->_table_sp_contacts, 'pk_i_id = '.$id);    
        }  
    }
    
    function _forwardMail($data, $itemid) {
        $id             = $itemid;
        $yourEmail      = $data['s_user_mail'];
        $yourName       = $data['s_user'];
        $phoneNumber    = $data['s_user_phone'];
        $message        = nl2br(strip_tags($data['s_user_message']));

        $path = null;
        $item = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);

        $mPages = new Page();
        $aPage  = $mPages->findByInternalName('email_item_inquiry');
        $locale = osc_current_user_locale();

        if( isset($aPage['locale'][$locale]['s_title']) ) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $item_url = osc_item_url();
        $item_link = '<a href="' . $item_url . '" >' . $item_url . '</a>';

        $words   = array();
        $words[] = array(
            '{CONTACT_NAME}',
            '{USER_NAME}',
            '{USER_EMAIL}',
            '{USER_PHONE}',
            '{ITEM_TITLE}',
            '{ITEM_URL}',
            '{ITEM_LINK}',
            '{COMMENT}'
        );

        $words[] = array(
            $item['s_contact_name'],
            $yourName,
            $yourEmail,
            $phoneNumber,
            $item['s_title'],
            $item_url,
            $item_link,
            $message
        );

        $title = osc_apply_filter('email_item_inquiry_title_after', osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_item_inquiry_title', $content['s_title'], $data)), $words), $data);
        $body  = osc_apply_filter('email_item_inquiry_description_after', osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_item_inquiry_description', $content['s_text'], $data)), $words), $data);

        $from      = osc_contact_email();
        $from_name = osc_page_title();

        $emailParams = array(
            'from'      => $from,
            'from_name' => $from_name,
            'subject'   => $title,
            'to'        => $item['s_contact_email'],
            'to_name'   => $item['s_contact_name'],
            'body'      => $body,
            'alt_body'  => $body,
            'reply_to'  => $yourEmail
        );

        if (osc_notify_contact_item()) {
            $emailParams['add_bcc'] = osc_contact_email();
        }

        if (osc_item_attachment()) {
            $attachment   = Params::getFiles('attachment');
            $resourceName = $attachment['name'];
            $tmpName      = $attachment['tmp_name'];
            $path         = osc_uploads_path() . time() . '_' . $resourceName;

            if (!is_writable(osc_uploads_path())) {
                osc_add_flash_error_message(_m('There has been some errors sending the message'));
            }

            if (!move_uploaded_file($tmpName, $path)) {
                unset($path);
            }
        }

        if (isset($path)) {
            $emailParams['attachment'] = $path;
        }
        
        $return = false;
        if (osc_sendMail($emailParams)) {
            $return = true;
        }
        @unlink($path);
        
        return $return;
    }
    
    
    // Functions for login protection
    function _checkAccount($search, $type = 'user') {
        $this->dao->select('*');

        if ($type == 'user') {
            $this->dao->from($this->_table_user);
            $this->dao->where("s_email", $search);    
        } elseif ($type == 'admin') {
            $this->dao->from($this->_table_admin);
            $this->dao->where("s_username", $search);    
        }
        
        $result = $this->dao->get();
        if ($result->numRows() > 0) { return true; }
        
        return false;        
    }
    
    function _checkUserLogin($email, $password) {        
        if (!$this->_checkAccount($email, 'user')) {
            return false;
        } else {
            $user = User::newInstance()->findByEmail($email);
            if (!osc_verify_password($password, (isset($user['s_password']) ? $user['s_password'] : ''))) {
                return false;
                
            } else {
                return true;
            }
        }        
    }
    
    function _checkAdminLogin($admin, $password) {
        if (!osc_verify_password($password, (isset($admin['s_password']) ? $admin['s_password'] : ''))) {
            return false;
            
        } else {
            return true;
        }        
    }
    
    function _checkAdminBan($ip) {
    
        $debug = new Debugger;
            
        $this->dao->select('*');
        $this->dao->from($this->_table_bans);
        $this->dao->where("s_ip", $ip);
        $this->dao->like("s_name", "Admin/Mod");
        
        $result = $this->dao->get();
        if ($result->numRows() > 0) {
            $debug->do_log("debug", "Admin Ban Rule found: ".$ip);
            return true; 
        }
        
        $debug->do_log("debug", "No Admin Ban Rule found: ".$ip);
        return false;        
    }
    
    function _handleUserLogin($email, $ip) {
        
        $action = $this->_get('sp_security_login_action');
        $reason = __("Spam Protection - Too many false login attempts", "spamprotection");
        $debug = new Debugger;
        
        if ($action == '1') {
            $debug->do_log("debug", "User blocked");
            $this->dao->update($this->_table_user, array('b_enabled' => '0'), array('s_email' => $email));            
            $this->_addBanLog('block', 'falselogin', $email, $ip);    
        } elseif ($action == '2') {
            $debug->do_log("debug", "User banned");
            $this->dao->insert($this->_table_bans, array('s_name' => $reason, 's_ip' => $ip));
            $this->_addBanLog('ban', 'falselogin', $email, $ip);    
        } elseif ($action == '3') {
            $debug->do_log("debug", "User blocked and banned");
            $this->dao->update($this->_table_user, array('b_enabled' => '0'), array('s_email' => $email));
            $this->dao->insert($this->_table_bans, array('s_name' => $reason, 's_ip' => $ip));
            $this->_addBanLog('blockban', 'falselogin', $email, $ip);    
        }
    }
    
    function _handleAdminLogin($name, $ip) {
        $action = $this->_get('sp_admin_login_action');
        $reason = sprintf(__("Spam Protection - Admin/Mod %s blocked in due to too many false login attempts", "spamprotection"), $name);
        $debug = new Debugger;
        
        if ($action == '1') {
            $debug->do_log("debug", "Admin blocked");            
            $this->_addBanLog('block', 'falselogin', $name, $ip, 'admin');    
        } elseif ($action == '2') {
            $debug->do_log("debug", "Admin banned");
            $this->dao->delete($this->_table_sp_logins, '`s_name` = "'.$name.'"');
            $this->dao->insert($this->_table_bans, array('s_name' => $reason, 's_ip' => $ip));
            $this->_addBanLog('ban', 'falselogin', $name, $ip, 'admin');    
        } elseif ($action == '3') {
            $debug->do_log("debug", "Admin blocked and banned");
            $this->dao->insert($this->_table_bans, array('s_name' => $reason, 's_ip' => $ip));
            $this->_addBanLog('blockban', 'falselogin', $name, $ip, 'admin');    
        }
    }
    
    function _countLogin($search, $type = 'user') {    
        $time = $this->_get('sp_security_login_time')*60;
        $ip = $this->_IpUserLogin();
            
        $this->dao->select('*');
        $this->dao->from($this->_table_sp_logins);
        $this->dao->where("dt_date_login > ".(time()-$time));
        
        if ($type == 'user') {    
            $this->dao->where("s_type", $type);
            $this->dao->where("s_email", $search);    
        } elseif ($type == 'admin') {
            $this->dao->where("s_type", $type);
            $this->dao->where("s_name", $search);    
        }        
        
        $this->dao->orWhere("s_ip", $ip);
        
        $result = $this->dao->get();
        $rows = $result->numRows();
        if ($rows > 0) { return $rows; }
        
        return false;       
    }
    
    function _increaseUserLogin($email) {        
        $ip = $this->_IpUserLogin();                
        if ($this->dao->insert($this->_table_sp_logins, array('s_email' => $email, 's_ip' => $ip, 's_type' => 'user', 'dt_date_login' => time()))) {
            return true;    
        } else {
            return false;
        }      
    }
    
    function _increaseAdminLogin($name) {
                
        $ip = $this->_IpUserLogin();                
        if ($this->dao->insert($this->_table_sp_logins, array('s_name' => $name, 's_ip' => $ip, 's_type' => 'admin', 'dt_date_login' => time()))) {
            return true;    
        } else {
            return false;
        }      
    }
    
    function _resetUserLogin($email) {
        $time = $this->_get('sp_security_login_time')*60;
        $ip = $this->_IpUserLogin();
        
        $this->dao->update($this->_table_user, array('b_enabled' => '1'), array('s_email' => $email));
        $this->dao->delete($this->_table_bans, '`s_ip` = "'.$ip.'"');
        $this->dao->delete($this->_table_sp_logins, '`s_email` = "'.$email.'"');
        $this->dao->delete($this->_table_sp_logins, '`s_ip` = "'.$ip.'"');
        $this->dao->delete($this->_table_sp_logins, '`dt_date_login` < "'.(time()-$time).'"');
    }
    
    function _resetAdminLogin($name) {
        $time = $this->_get('sp_security_login_time')*60;
        $ip = $this->_IpUserLogin();
        
        $this->dao->delete($this->_table_bans, '`s_ip` = "'.$ip.'"');
        $this->dao->delete($this->_table_sp_logins, '`s_name` = "'.$name.'"');
        $this->dao->delete($this->_table_sp_logins, '`s_ip` = "'.$ip.'"');
        $this->dao->delete($this->_table_sp_logins, '`dt_date_login` < "'.(time()-$time).'"');
    }
    
    function _unbanUser() {
        
        $time = $this->_get('sp_security_login_unban')*60;
        
        $this->dao->select('*');
        $this->dao->from($this->_table_sp_logins);
        $this->dao->where("dt_date_login < ".(time()-$time));
        
        $result = $this->dao->get();
        if ($result->numRows() <= 0) { return false; }
        
        $bans = $result->result();
        
        foreach ($bans AS $k => $v) {
            $this->dao->update($this->_table_user, array('b_enabled' => '1'), array('s_email' => $v['s_email']));
            $this->dao->delete($this->_table_bans, '`s_ip` = "'.$v['s_ip'].'"');
            $this->dao->delete($this->_table_sp_logins, '`pk_i_id` < "'.$v['pk_i_id'].'"');    
        }
    }
    
    function _unbanAdmin() {
        /*
        $time = $this->_get('sp_security_login_unban')*60;
        
        $this->dao->select('*');
        $this->dao->from($this->_table_sp_logins);
        $this->dao->where("dt_date_login < ".(time()-$time));
        
        $result = $this->dao->get();
        if ($result->numRows() <= 0) { return false; }
        
        $bans = $result->result();
        
        foreach ($bans AS $k => $v) {
            $this->dao->update($this->_table_user, array('b_enabled' => '1'), array('s_email' => $v['s_email']));
            $this->dao->delete($this->_table_bans, '`s_ip` = "'.$v['s_ip'].'"');
            $this->dao->delete($this->_table_sp_logins, '`pk_i_id` < "'.$v['pk_i_id'].'"');    
        }
        */
    }
    
    function _addBanLog($type, $reason, $email = false, $ip = false, $type = 'user') {
        
        if (!$ip) { $ip = $this->_IpUserLogin(); }
        if ($email && $type == 'user') { $user = User::newInstance()->findByEmail($email); }
        elseif ($email && $type == 'admin') { $user = Admin::newInstance()->findByUsername($email); }
        
        if ($type == 'block') {
            $reason_sql = __("User was blocked because of", "spamprotection");
        } elseif ($type == 'blockban') {
            $reason_sql = __("User was blocked and banned because of", "spamprotection");
        } else {
            $reason_sql = __("User was banned because of", "spamprotection");
        } if ($reason == 'falselogin') {
            $reason_sql = $reason_sql.'&nbsp;'.__("too many false logins", "spamprotection");
        } elseif ($reason == 'spam') {
            $reason_sql = $reason_sql.'&nbsp;'.__("spam ads", "spamprotection");
        }
        
        if ($this->dao->insert($this->_table_sp_ban_log, array('i_user_id' => (isset($user['pk_i_id']) ? $user['pk_i_id'] : false), 's_user_email' => $email, 's_user_ip' => $ip, 's_reason' => $reason_sql))) {
            return true;    
        } else {
            return false;
        }  
    }
    
    function _handleBanLog($action, $id) {        
        if ($action == 'activate') {
            $log = $this->_getRow('t_sp_ban_log', array('key' => 'pk_i_id', 'value' => $id));                        
            if (isset($log['s_user_email'])) {
                $this->dao->update($this->_table_user, array('b_enabled' => '1'), array('s_email' => $log['s_user_email']));
                $this->dao->delete($this->_table_bans, '`s_email` = "'.$log['s_user_email'].'"');    
                $this->dao->delete($this->_table_sp_logins, '`s_email` = "'.$log['s_user_email'].'"');    
            } if (isset($log['s_user_ip'])) {
                $this->dao->delete($this->_table_bans, '`s_ip` = "'.$log['s_user_ip'].'"');
                $this->dao->delete($this->_table_sp_logins, '`s_ip` = "'.$log['s_user_ip'].'"');
            }  if (isset($id)) {
                $this->dao->delete($this->_table_sp_ban_log, '`pk_i_id` = "'.$id.'"');
            }
        } elseif ($action == 'delete') {
            if (isset($id)) {
                $this->dao->delete($this->_table_sp_ban_log, '`pk_i_id` = "'.$id.'"');
            }                
        }
    }
    
    function _IpUserLogin() {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
           $ip = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            return false;
        }
        
        //localhost fix
        return getenv('REMOTE_ADDR');      
        //return $ip;      
    }
    
    function _informUser($search, $type = 'user') {
        
        if ($this->_checkAccount($search, $type)) {
            $ip = $this->_IpUserLogin();
            
            if ($type == 'user') {
                $user = User::newInstance()->findByEmail($search);
                
                $email = $search;
                $content   = array();
                $content[] = array('{PAGE_NAME}', '{MAIL_USER}', '{MAIL_USED}', '{MAIL_DATE}', '{MAIL_IP}', '{UNBAN_LINK}', '{PASSWORD_LINK}');
                $content[] = array(osc_page_title(), $user['s_name'], $search, date("Y/m/d H:i", time()), $ip, osc_base_url(true).'?page=sp_activate_account&email='.$search.'&token='.md5($user['s_secret']), osc_recover_user_password_url());    
            } elseif ($type == 'admin') {
                $user = Admin::newInstance()->findByUsername($search);
                
                $email = $user['s_email'];
                $content   = array();
                $content[] = array('{PAGE_NAME}', '{MAIL_USER}', '{MAIL_USED}', '{MAIL_DATE}', '{MAIL_IP}', '{UNBAN_LINK}', '{PASSWORD_LINK}');
                $content[] = array(osc_page_title(), $user['s_name'], $search, date("Y/m/d H:i", time()), $ip, osc_base_url(true).'?page=sp_activate_account&name='.$search.'&token='.md5($user['s_secret']), osc_admin_base_url(true).'?page=login&action=recover');    
            }
            
            $mail_title = __("False logins on {PAGE_NAME}", "spamprotection");
            
            $mail_body_plain = __('Hello {MAIL_USER},','spamprotection').'\r\n\r\n
'.__('We have detected some false logins for your account {MAIL_USED} on {PAGE_NAME}. Last false login was on {MAIL_DATE} from IP {MAIL_IP}','spamprotection').'\r\n\r\n
'.__('In order to our security policy, we have temporarily disabled your account and banned the used IP in our System. You can use following link to unban and reactivate your Account. If this was not you, please contact the support and change your password. You can use the password recovery function, if you don\'t remember your password.','spamprotection').'\r\n\r\n\r\n
'.__('Unban your account: {UNBAN_LINK} ','spamprotection').'\r\n\r\n
'.__('Password recovery: {PASSWORD_LINK} ','spamprotection').'\r\n\r\n
'.__('Best regards','spamprotection').'\r\n
{PAGE_NAME}';
            
            $mail_body_html = __('Hello {MAIL_USER},','spamprotection').'<br /><br />
'.__('We have detected some false logins for your account {MAIL_USED} on {PAGE_NAME}. Last false login was on {MAIL_DATE} from IP {MAIL_IP}','spamprotection').'<br /><br />
'.__('In order to our security policy, we have temporarily disabled your account and banned the used IP in our System. You can use following link to unban and reactivate your Account. If this was not you, please contact the support and change your password. You can use the password recovery function, if you don\'t remember your password.','spamprotection').'<br /><br /><br />
'.__('Unban your account: <a href="{UNBAN_LINK}">{UNBAN_LINK}</a> ','spamprotection').'<br /><br />
'.__('Password recovery: <a href="{PASSWORD_LINK}">{PASSWORD_LINK}</a> ','spamprotection').'<br /><br />
'.__('Best regards','spamprotection').'<br />
{PAGE_NAME}';

            $title = osc_mailBeauty($mail_title, $content);
            $body_plain  = osc_mailBeauty($mail_body_plain, $content);
            $body_html  = osc_mailBeauty($mail_body_html, $content);

            $params = array(
                'from'      => osc_contact_email(),
                'from_name' => osc_page_title(),
                'subject'   => $title,
                'to'        => $email,
                'to_name'   => $user['s_name'],
                'body'      => $body_html,
                'alt_body'  => $body_plain,
                'reply_to'  => osc_contact_email()
            );
            
            $return = false;
            if (osc_sendMail($params)) {
                $return = true;
            }
            
            return $return;
        }    
    }
    
    function _upgradeDatabaseInfo($error) {
        $params = Params::getParamsAsArray();
        if (isset($params['file'])) { }
        $file = osc_plugin_resource('spamprotection/assets/create_table.sql');
        
        $tables = file_get_contents($file);
        $tables = str_replace( '/*TABLE_PREFIX*/', DB_TABLE_PREFIX, $tables);
        $tables = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#','',($tables));
        
        echo '
            <div id="spamprot_upgrade_overlay" style="display: none;">
                <div id="spamprot_upgrade" style="display: none;">
                    <div id="spamprot_upgrade_close">x</div>
                    <div id="spamprot_upgrade_info">
                        <h2>'.__("Spam Protection - Database need upgrade!", "spamprotection").'</h2>
                        '.$error.'    
                        <p>'.__("Since you have upgraded this plugin, the database need an upgrade also.<br /><strong>To upgrade it now, press the button below.</strong>", "spamprotection").'</p>    
                        <p>'.__("If you want to backup your data before, you can use the export function.<br />You will find an import function in the plugin settings.", "spamprotection").'</p>    
                        <p>'.__("Of course you can do the upgrade manually.<br /><em>For this case you will find the database tables here</em>", "spamprotection").'</p>
                        
                        <div style="height: 180px; text-align: left; margin: 15px 0;">
                            <div id="spamprot_upgrade_tables">
                                <small><strong><textarea style="width: 100%; height: 100%; resize: none; overflow: overlay;">'.$tables.'</textarea></strong></small>
                            </div>                            
                        </div>                            
                    </div>
                    
                    <div id="spamprot_upgrade_buttons">
                        <a class="btn btn-blue" href="?sp_upgrade=export">'.__("Export Data", "spamprotection").'</a>
                        <a class="btn btn-green" href="?sp_upgrade=upgrade">'.__("Upgrade now", "spamprotection").'</a>
                        <a class="btn btn-red" href="">'.__("Test again", "spamprotection").'</a>
                        <div style="clear: both;"></div>
                    </div>    
                </div>
            </div>
        ';        
    }
    
    function _upgradeNow() {
        $file = osc_plugin_resource('spamprotection/assets/create_table.sql');        
        $sql = file_get_contents($file);
        
        if (!$this->dao->importSQL($sql)) {
            throw new Exception( "Error importSQL::spam_prot<br>".$file ) ;
        }    
    }
    
    function _upgradeCheck() {        
        $file = osc_plugin_resource('spamprotection/assets/upgrade_check.sql');
        
        $sql = file_get_contents($file);
        $sql = str_replace( '/*TABLE_PREFIX*/', DB_TABLE_PREFIX, $sql);
        $sql = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#','',($sql));
        
        $queries = $this->_upgradeCheckSplit($sql, ';');
        $exec = array(); $return = true;
        
        foreach($queries as $query) {
            $query = trim($query);
            
            if (!empty($query)) {
                $execute = $this->dao->_execute($query);
                if (!$execute) {
                    //$regex = '^.*`\/\*TABLE_PREFIX\*\/(.*)`.*^';  //this
                    //$regex = '^(.*)`\/\*TABLE_PREFIX\*\/(.*)`.*$';  //or this
                    //$exec[] = preg_match($regex, $execute, &$matches);
                    
                    // return table
                    //$matches[1]
                    $return = false;
                }
            }
        }
        
        if (!$return) {
            return false;
        }
        
        return true;    
    }
    
    private function _upgradeCheckSplit($sql, $explodeChars) {
        if (preg_match('|^(.*)DELIMITER (\S+)\s(.*)$|isU', $sql, $matches)) {
            $queries = explode($explodeChars, $matches[1]);
            $recursive = $this->splitSQL($matches[3], $matches[2]);

            return array_merge($queries, $recursive);
        }
        else {
            return explode($explodeChars, $sql);
        }
    }
    
    function _selectExport($table, $where = false) {
        $this->dao->select('*');
        $this->dao->from($table);
        
        if (is_array($where)) { $this->dao->where($where['key'], $where['value']); }
        
        $result = $this->dao->get();
        if ($result->numRows() <= 0) { return false; }
        
        return $result->result();    
    }
    
    function _prepareExport($type = 'database') {
        if ($type == 'database') {        
            $export = array(
                DB_TABLE_PREFIX.'t_ban_rule'                  => $this->_selectExport($this->_table_bans),
                DB_TABLE_PREFIX.'t_spam_protection_ban_log'   => $this->_selectExport($this->_table_sp_ban_log),
                DB_TABLE_PREFIX.'t_spam_protection_items'     => $this->_selectExport($this->_table_sp_items),
                DB_TABLE_PREFIX.'t_spam_protection_comments'  => $this->_selectExport($this->_table_sp_comments),
                DB_TABLE_PREFIX.'t_spam_protection_contacts'  => $this->_selectExport($this->_table_sp_contacts),
                DB_TABLE_PREFIX.'t_spam_protection_logins'    => $this->_selectExport($this->_table_sp_logins)
            );
        } else {
            $export = $this->_selectExport($this->_table_pref, array('key' => 's_section', 'value' => $this->_sect()));    
        }
        
        return $export;    
    }
    function _export($type = 'database') {
        
        if ($type == 'database') {
            $xmlFile = osc_plugin_path(dirname(dirname(__FILE__))) . '/export/database.xml';
            $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><database />");
            $this->array_to_xml($this->_prepareExport('database'),$xml_info);
            $xml_file = $xml_info->asXML($xmlFile);    
        } else {
            $xmlFile = osc_plugin_path(dirname(dirname(__FILE__))) . '/export/settings.xml';
            $xml_info = new SimpleXMLElement("<?xml version=\"1.0\"?><settings />");
            $this->array_to_xml($this->_prepareExport('settings'),$xml_info);
            $xml_file = $xml_info->asXML($xmlFile);
        }        
        
        if (file_exists($xmlFile)) {
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dl = $dom->load($xmlFile);
            
            if (!$dl) { return __("The export file could not be created", "spamprotection"); }
            $dom->save($xmlFile);
        }
        
        if ($xml_file){
            return sprintf(__("Export file %s was created succesfully", "spamprotection"), ($type == 'database' ? __("for database", "spamprotection") : __("for plugin settings", "spamprotection")));
        } else{
            return __("There was an error while creating the export file", "spamprotection");
        }
    }
    
    function _import($data, $type = 'server') {   
        
        if ($type == 'upload') {
            $xmlFile = $data;    
        } else {
            $xmlFile = osc_plugin_path(dirname(dirname(__FILE__))).'/export/'.$data.'.xml';    
        } 
        
        $xml = simplexml_load_file($xmlFile); $return = array();
        $type = $xml->getName();
        
        if (!in_array($type, array('settings', 'database'))) { return __("This is not a valid import file", "spamprotection"); }
        
        $json = json_encode($xml);    
        $array = json_decode($json, TRUE);
        $error = '';
        
        if ($type == 'settings') {
            if (!empty($array['data'])) {
                foreach($array['data'] as $v) {
                    if (!osc_set_preference($v['s_name'], $v['s_value'], $v['s_section'], $v['e_type'])) {
                        $error .= sprintf(__("Error while importing settings: %s cannot be saved correctly", "spamprotection"), $v['s_name']);    
                    }    
                }
            }                
        } elseif ($type == 'database') {
            foreach($array as $table => $data) {
                if (!empty($data['data'])) {
                    $insert = '';
                    foreach($data['data'] as $row) {
                        $insert = array();
                        foreach($row as $k => $v) {
                            if ($k != 'pk_i_id') {
                                if (empty($v)) { $v = ''; }
                                $insert[$k] = $v;
                            }
                        }
                        if (!$this->dao->insert($table, $insert)) {
                            $error .= sprintf(__("Error while importing data to table %s", "spamprotection"), $table)."\n";
                        }
                    }    
                }
            }                
        }
            
        if (!empty($error)) {
            return $error;    
        }
                    
        return __("Import done", "spamprotection");
    }
    
    function array_to_xml($array, &$xml_info) {
        foreach($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)){
                    $subnode = $xml_info->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml_info->addChild("data");
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml_info->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }   
}
?>