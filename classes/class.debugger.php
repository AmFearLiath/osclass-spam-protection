<?php
class sp_debug {
    
    private static $instance;
    
    public static function newInstance() {
        if( !self::$instance instanceof self ) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }
    
    function __construct() {
        $this->path = ABS_PATH.'/oc-content/';
        $this->checkPath($this->path);
        $this->file = $this->path.'debug.log';
        $this->checkFile($this->file);            
    }
        
    function checkPath($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }
    }
    
    function checkFile($file) {
        if (!file_exists($file)) {
            $fp = fopen($file, "w+");
            fputs($fp, "==== no entry ====" . "\n");
            fclose($fp);
        }
    }
    
    function do_log($log) {
        
        $debug_active = osc_get_preference('sp_debug_active', 'plugin_spamprotection');
        $debug_type = osc_get_preference('sp_debug_type', 'plugin_spamprotection');
        
        if ($debug_active == '1') {
            if ($debug_type == 'print') {
                $this->_printDebug($log);    
            } elseif ($debug_type == 'write') {
                $this->_writeDebug($log);    
            }
        }
    }
    
    function _printDebug($print) {

        if (is_array($print)) {
            $output = '<pre>'.print_r($print).'</pre>';
        } else {
            $output = $print;
        }
        
        echo '
        <div id="spamprot_debug_overlay" style="display: none;">
            <div id="spamprot_debug" style="display: none;">
                <div id="spamprot_debug_close">x</div>
                <div id="spamprot_debug_info">'.$output.'</div>                
                <div id="spamprot_debug_buttons">
                    <a class="btn btn-red" href="">'.__("Close", "spamprotection").'</a>
                    <div style="clear: both;"></div>
                </div>    
            </div>
        </div>
        ';
        //debug_print_backtrace();    
    }
    
    function _writeDebug($write) {
        
        $line = 'DEBUG|' . date("d.m.Y H:i:s", time()) . '|' . $message;                    
        if ($write || is_array($message)) {
            error_log(print_r($message, true) . "\n", 3, $this->file); 
        } else {
            error_log($line . "\n", 3, $this->file);    
        }    
    }
    
}