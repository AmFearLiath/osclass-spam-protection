<?php
class spam_prot extends DAO {
    
    private static $instance ;
    
    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }
    
    function __construct() {
        $this->_table_user              = '`'.DB_TABLE_PREFIX.'t_user`';
        $this->_table_item              = '`'.DB_TABLE_PREFIX.'t_item`';
        $this->_table_comment           = '`'.DB_TABLE_PREFIX.'t_item_comment`';
        $this->_table_desc              = '`'.DB_TABLE_PREFIX.'t_item_description`';
        $this->_table_sp_items          = '`'.DB_TABLE_PREFIX.'t_spam_protection_items`';
        $this->_table_sp_comments       = '`'.DB_TABLE_PREFIX.'t_spam_protection_comments`';
        $this->_table_sp_contacts       = '`'.DB_TABLE_PREFIX.'t_spam_protection_contacts`';
        
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
    
    function _opt($key = false) {                
        $opts = array(
            'sp_activate'           => array('1', 'BOOLEAN'),
            'sp_comment_activate'   => array('1', 'BOOLEAN'),
            'sp_contact_activate'   => array('1', 'BOOLEAN'),
            'sp_duplicates'         => array('1', 'BOOLEAN'),
            'sp_duplicates_as'      => array('1', 'BOOLEAN'),
            'sp_duplicate_type'     => array('0', 'BOOLEAN'),
            'sp_duplicate_perc'     => array('85', 'BOOLEAN'),
            'sp_honeypot'           => array('0', 'BOOLEAN'),
            'sp_contact_honeypot'   => array('0', 'BOOLEAN'),
            'honeypot_name'         => array('sp_price_range', 'STRING'),
            'contact_honeypot_name' => array('yourDate', 'STRING'),
            'contact_honeypot_value'=> array('asap', 'STRING'),
            'sp_blocked'            => array('0', 'BOOLEAN'),
            'sp_blocked_tld'        => array('0', 'BOOLEAN'),
            'sp_blockedtype'        => array('strpos', 'STRING'),
            'sp_comment_blocked'    => array('0', 'BOOLEAN'),
            'sp_comment_blocked_tld'=> array('0', 'BOOLEAN'),
            'sp_comment_blockedtype'=> array('strpos', 'STRING'),
            'sp_contact_blocked'    => array('0', 'BOOLEAN'),
            'sp_contact_blocked_tld'=> array('0', 'BOOLEAN'),
            'sp_contact_blockedtype'=> array('strpos', 'STRING'),
            'sp_mxr'                => array('0', 'BOOLEAN'),
            'blocked'               => array('', 'STRING'),
            'blocked_tld'           => array('', 'STRING'),
            'comment_blocked'       => array('', 'STRING'),
            'comment_blocked_tld'   => array('', 'STRING'),
            'contact_blocked'       => array('', 'STRING'),
            'contact_blocked_tld'   => array('', 'STRING'),
            'sp_stopwords'          => array('', 'STRING'),
            'sp_comment_stopwords'  => array('', 'STRING'),
            'sp_contact_stopwords'  => array('', 'STRING'),
            'sp_comment_links'      => array('', 'STRING'),
            'sp_contact_links'      => array('', 'STRING'),
        );
        
        if ($key) { return $opts[$key]; }
                
        return $opts;
    }
    
    function _sect() {
        return 'plugin_spamprotection';
    }

    function _get($opt = false) {
        $pref = $this->_sect();
        if ($opt) {        
            return osc_get_preference($opt, $pref);
        } else {
            $opts = array(
                'sp_activate'           => osc_get_preference('sp_activate', $pref),
                'sp_comment_activate'   => osc_get_preference('sp_comment_activate', $pref),
                'sp_contact_activate'   => osc_get_preference('sp_contact_activate', $pref),
                'sp_duplicates'         => osc_get_preference('sp_duplicates', $pref),
                'sp_duplicates_as'      => osc_get_preference('sp_duplicates_as', $pref),
                'sp_duplicate_type'     => osc_get_preference('sp_duplicate_type', $pref),
                'sp_duplicate_perc'     => osc_get_preference('sp_duplicate_perc', $pref),
                'sp_honeypot'           => osc_get_preference('sp_honeypot', $pref),
                'sp_contact_honeypot'   => osc_get_preference('sp_contact_honeypot', $pref),
                'honeypot_name'         => osc_get_preference('honeypot_name', $pref),
                'contact_honeypot_name' => osc_get_preference('contact_honeypot_name', $pref),
                'contact_honeypot_value'=> osc_get_preference('contact_honeypot_value', $pref),
                'sp_blocked'            => osc_get_preference('sp_blocked', $pref),
                'sp_blocked_tld'        => osc_get_preference('sp_blocked_tld', $pref),
                'sp_blockedtype'        => osc_get_preference('sp_blockedtype', $pref),
                'sp_comment_blocked'    => osc_get_preference('sp_comment_blocked', $pref),
                'sp_comment_blocked_tld'=> osc_get_preference('sp_comment_blocked_tld', $pref),
                'sp_comment_blockedtype'=> osc_get_preference('sp_comment_blockedtype', $pref),
                'sp_contact_blocked'    => osc_get_preference('sp_contact_blocked', $pref),
                'sp_contact_blocked_tld'=> osc_get_preference('sp_contact_blocked_tld', $pref),
                'sp_contact_blockedtype'=> osc_get_preference('sp_contact_blockedtype', $pref),
                'sp_mxr'                => osc_get_preference('sp_mxr', $pref),
                'blocked'               => osc_get_preference('blocked', $pref),
                'blocked_tld'           => osc_get_preference('blocked_tld', $pref),
                'comment_blocked'       => osc_get_preference('comment_blocked', $pref),
                'comment_blocked_tld'   => osc_get_preference('comment_blocked_tld', $pref),
                'contact_blocked'       => osc_get_preference('contact_blocked', $pref),
                'contact_blocked_tld'   => osc_get_preference('contact_blocked_tld', $pref),
                'sp_stopwords'          => osc_get_preference('sp_stopwords', $pref),
                'sp_comment_stopwords'  => osc_get_preference('sp_comment_stopwords', $pref),
                'sp_contact_stopwords'  => osc_get_preference('sp_contact_stopwords', $pref),
                'sp_comment_links'      => osc_get_preference('sp_comment_links', $pref),
                'sp_contact_links'      => osc_get_preference('sp_contact_links', $pref),
            );
            return $opts;
        }
    }
    
    function _admin_menu_draw() {
        $count = $this->_countRows('t_item', array('key' => 'b_spam', 'value' => '1'));
        $comments = $this->_countRows('t_comment', array('key' => 'b_spam', 'value' => '1'));
        $contacts = $this->_countRows('t_sp_contacts');
        
        if ($count > 0) {
            AdminToolbar::newInstance()->add_menu( array(
                 'id'        => 'spamprotection',
                 'title'     => '<i class="circle circle-gray">'.$count.'</i>'.__('Spam found', 'spamprotection'),
                 'href'      => osc_admin_base_url(true).'?page=items&b_spam=1',
                 'meta'      => array('class' => 'action-btn action-btn-black'),
                 'target'    => '_self'
             ));
        }
        if ($comments > 0) {
            AdminToolbar::newInstance()->add_menu( array(
                 'id'        => 'spamprotection_comments',
                 'title'     => '<i class="circle circle-gray">'.$comments.'</i>'.__('Spam comment found', 'spamprotection'),
                 'href'      => osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/comments_check.php'),
                 'meta'      => array('class' => 'action-btn action-btn-black'),
                 'target'    => '_self'
             ));
        }
        if ($contacts > 0) {
            AdminToolbar::newInstance()->add_menu( array(
                 'id'        => 'spamprotection_contacts',
                 'title'     => '<i class="circle circle-gray">'.$contacts.'</i>'.__('Spam Contact Mail found', 'spamprotection'),
                 'href'      => osc_admin_render_plugin_url(osc_plugin_folder(dirname(__FILE__)).'admin/contact_check.php'),
                 'meta'      => array('class' => 'action-btn action-btn-black'),
                 'target'    => '_self'
             ));
        }
    }
    
    function _getRow($table, $where = false, $orderBy = false, $orderDir = 'DESC') {
        
        if ($table == 't_item') { $table = $this->_table_item; }
        elseif ($table == 't_desc') { $table = $this->_table_desc; }
        elseif ($table == 't_user')  { $table = $this->_table_user; }
        elseif ($table == 't_comment')  { $table = $this->_table_comment; }
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
        
        return $result->result();
    }
    
    function _countRows($table, $where = false) {
        
        if ($table == 't_item') { $table = $this->_table_item; }
        elseif ($table == 't_user')  { $table = $this->_table_user; }
        elseif ($table == 't_sp_items')  { $table = $this->_table_sp_items; }
        elseif ($table == 't_comment')  { $table = $this->_table_comment; }
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
    
    function _saveSettings($params) {
        
        if (isset($params['sp_stopwords'])) {
            $sort = explode(",", $params['sp_stopwords']);
            sort($sort);
            $params['sp_stopwords'] = implode(",", $sort);
        }
        
        $data = array(
            'sp_activate'           => (isset($params['sp_activate']) ? $params['sp_activate'] : ''),
            'sp_comment_activate'   => (isset($params['sp_comment_activate']) ? $params['sp_comment_activate'] : ''),
            'sp_contact_activate'   => (isset($params['sp_contact_activate']) ? $params['sp_contact_activate'] : ''),
            'sp_duplicates'         => (isset($params['sp_duplicates']) ? $params['sp_duplicates'] : ''),
            'sp_duplicates_as'      => (isset($params['sp_duplicates_as']) ? $params['sp_duplicates_as'] : ''),
            'sp_duplicate_type'     => (isset($params['sp_duplicate_type']) ? $params['sp_duplicate_type'] : ''),
            'sp_duplicate_perc'     => (isset($params['sp_duplicate_perc']) ? $params['sp_duplicate_perc'] : ''),
            'sp_honeypot'           => (isset($params['sp_honeypot']) ? $params['sp_honeypot'] : ''),
            'sp_contact_honeypot'   => (isset($params['sp_contact_honeypot']) ? $params['sp_contact_honeypot'] : ''),
            'honeypot_name'         => (isset($params['honeypot_name']) ? $params['honeypot_name'] : ''),
            'contact_honeypot_name' => (isset($params['contact_honeypot_name']) ? $params['contact_honeypot_name'] : ''),
            'contact_honeypot_value'=> (isset($params['contact_honeypot_value']) ? $params['contact_honeypot_value'] : ''),
            'sp_blocked'            => (isset($params['sp_blocked']) ? $params['sp_blocked'] : ''),
            'sp_blocked_tld'        => (isset($params['sp_blocked_tld']) ? $params['sp_blocked_tld'] : ''),
            'sp_blockedtype'        => (isset($params['sp_blockedtype']) ? $params['sp_blockedtype'] : ''),
            'sp_comment_blocked'    => (isset($params['sp_comment_blocked']) ? $params['sp_comment_blocked'] : ''),
            'sp_comment_blocked_tld'=> (isset($params['sp_comment_blocked_tld']) ? $params['sp_comment_blocked_tld'] : ''),
            'sp_comment_blockedtype'=> (isset($params['sp_comment_blockedtype']) ? $params['sp_comment_blockedtype'] : ''),
            'sp_contact_blocked'    => (isset($params['sp_contact_blocked']) ? $params['sp_contact_blocked'] : ''),
            'sp_contact_blocked_tld'=> (isset($params['sp_contact_blocked_tld']) ? $params['sp_contact_blocked_tld'] : ''),
            'sp_contact_blockedtype'=> (isset($params['sp_contact_blockedtype']) ? $params['sp_contact_blockedtype'] : ''),
            'sp_mxr'                => (isset($params['sp_mxr']) ? $params['sp_mxr'] : ''),
            'blocked'               => (isset($params['blocked']) ? $this->_sort($params['blocked']) : ''),
            'blocked_tld'           => (isset($params['blocked_tld']) ? $this->_sort($params['blocked_tld']) : ''),
            'comment_blocked'       => (isset($params['comment_blocked']) ? $this->_sort($params['comment_blocked']) : ''),
            'comment_blocked_tld'   => (isset($params['comment_blocked_tld']) ? $this->_sort($params['comment_blocked_tld']) : ''),
            'contact_blocked'       => (isset($params['contact_blocked']) ? $this->_sort($params['contact_blocked']) : ''),
            'contact_blocked_tld'   => (isset($params['contact_blocked_tld']) ? $this->_sort($params['contact_blocked_tld']) : ''),
            'sp_stopwords'          => (isset($params['sp_stopwords']) ? $this->_sort($params['sp_stopwords']) : ''),
            'sp_comment_stopwords'  => (isset($params['sp_comment_stopwords']) ? $this->_sort($params['sp_comment_stopwords']) : ''),
            'sp_contact_stopwords'  => (isset($params['sp_contact_stopwords']) ? $this->_sort($params['sp_contact_stopwords']) : ''),
            'sp_comment_links'      => (isset($params['sp_comment_links']) ? $this->_sort($params['sp_comment_links']) : ''),
            'sp_contact_links'      => (isset($params['sp_contact_links']) ? $this->_sort($params['sp_contact_links']) : ''),
        );
        
        $pref = $this->_sect();
        $forbidden = array('CSRFName', 'CSRFToken', 'page', 'file', 'action');
        
        if (!empty($params['sp_htaccess'])) {
            $htaccess_file = ABS_PATH.'/.htaccess';
            $htaccess_writable = is_writable($htaccess_file);
            if ($htaccess_writable) {
                if (!file_put_contents($htaccess_file, $params['sp_htaccess'])) {
                    return false;                       
                }    
            }
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
    
    function _spamAction($type, $id) {        
        if ($type == 'activate') {
            $this->dao->update($this->_table_item, array('b_spam' => '0', 'b_enabled' => '1', 'b_active' => '1'), array('pk_i_id' => $id));    
        } elseif ($type == 'block') {
            $this->dao->update($this->_table_user, array('b_enabled' => '0'), array('s_email' => $id));    
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
        $this->dao->select('*');
        $this->dao->from($this->_table_desc);
        $this->dao->where("fk_i_item_id", $item);
        $this->dao->where("fk_c_locale_code", $locale);
        
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

        if( osc_notify_contact_item() ) {
            $emailParams['add_bcc'] = osc_contact_email();
        }

        if( osc_item_attachment() ) {
            $attachment   = Params::getFiles('attachment');
            $resourceName = $attachment['name'];
            $tmpName      = $attachment['tmp_name'];
            $path         = osc_uploads_path() . time() . '_' . $resourceName;

            if( !is_writable(osc_uploads_path()) ) {
                osc_add_flash_error_message( _m('There has been some errors sending the message'));
            }

            if( !move_uploaded_file($tmpName, $path) ) {
                unset($path);
            }
        }

        if( isset($path) ) {
            $emailParams['attachment'] = $path;
        }
        
        $return = false;
        if (osc_sendMail($emailParams)) {
            $return = true;
        }
        @unlink($path);
        
        return $return;
    }    
}
?>
