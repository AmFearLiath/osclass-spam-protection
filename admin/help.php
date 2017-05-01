<?php
$info = osc_plugin_get_info("spamprotection/index.php");
?>
<div class="help">

    <ul class="helptabs sp_tabs">
        <li class="helptab-link current" data-tab="sp_help_general"><a><?php _e('General', 'spamprotection'); ?></a></li>
        <li class="helptab-link" data-tab="sp_help_ads"><a><?php _e('Ad Settings', 'spamprotection'); ?></a></li>
        <li class="helptab-link" data-tab="sp_help_comments"><a><?php _e('Comment Settings', 'spamprotection'); ?></a></li>
        <li class="helptab-link" data-tab="sp_help_contacts"><a><?php _e('Contact Settings', 'spamprotection'); ?></a></li>
        <li class="helptab-link" data-tab="sp_help_about"><a><?php _e('About', 'spamprotection'); ?></a></li>
    </ul>
    
    <div id="sp_help_section" class="sp_help_section enabled">
    
        <div id="sp_help_general" class="helptab-content current">        
            
            <h2><?php echo sprintf(__("Welcome to Spam Protection %s", "spamprotection"), "v".$info['version']); ?></h2>
            
            <p><?php _e('Since you can read this help, you have already installed the plugin. So let\'s continue with the functions.', 'spamprotection'); ?></p>
            <p><?php _e('You can configure this plugin in two ways.', 'spamprotection'); ?></p>
            <ul>
                <li><?php _e('Through the plugin page and check the option "configure"', 'spamprotection'); ?></li>
                <li><?php _e('or through the Tools-Tab that you can find on the button "More" to the left side.', 'spamprotection'); ?></li>
            </ul>
            <p><?php _e('After your Settings, the Spam Protection is ready to work, you dont have to modify your files or something else.', 'spamprotection'); ?></p>
        
            <p><?php _e('This plugin gives you different options to stop spam.', 'spamprotection'); ?></p>
            <ul>
                <li>
                    <?php _e('Add Honeypot', 'spamprotection'); ?>
                    <p>
                        <small><?php _e('This option adds a "honeypot" and protect against bots.', 'spamprotection'); ?></small><br />        
                        <small><?php _e('You can set Honeypots for ads or contact mails', 'spamprotection'); ?></small>
                    </p>        
                </li>
                <li>
                    <?php _e('Search for duplicates', 'spamprotection'); ?>
                    <p><small><?php _e('This option searches for duplicate posts from the user.', 'spamprotection'); ?></small></p>        
                </li>
                <li>
                    <?php _e('Check MX Record from user email-addresses', 'spamprotection'); ?>
                    <p><small><?php _e('This option enables you to check if the sended mail address has an authentical MX Record', 'spamprotection'); ?></small></p>        
                </li>
                <li>
                    <?php _e('Block banned email-addresses', 'spamprotection'); ?>
                    <p>
                        <small><?php _e('This option enables you to block email-addresses, so all ads are automatically marked as spam from this mail-address', 'spamprotection'); ?></small><br />                
                        <small><?php _e('Available for ads, comments and contact mails', 'spamprotection'); ?></small>
                    </p>
                </li>
                <li>
                    <?php _e('Stopwords', 'spamprotection'); ?>
                    <p>
                        <small><?php _e('This option gives you the option to define words they are blocked. When an ad is posted with one or more of this words, its automatically marked as spam', 'spamprotection'); ?></small><br />                
                        <small><?php _e('Available for ads, comments and contact mails', 'spamprotection'); ?></small>
                    </p>        
                </li>
                <li>
                    <?php _e('.htaccess Editor', 'spamprotection'); ?>
                    <p><small><?php _e('Here you can modify your .htaccess to include some protections against bots. But beware, edit this only if you know what you do!', 'spamprotection'); ?></small></p>        
                </li>
            </ul>
            
            <h3><?php _e('Thats all', 'spamprotection'); ?></h3>
            <p><?php _e('Have fun with this little plugin', 'spamprotection'); ?></p>
        </div>
        
        <div id="sp_help_ads" class="helptab-content">
        
            <h2><?php _e("Main Features", "spamprotection"); ?></h2>
            
            <h3><?php _e("Activate the Spam Protection", "spamprotection"); ?></h3>
            <p><?php _e("Here you can de/activate the whole Spam Protection to secure your ads. You can deactivate some features optionally, but deactivate this checkbox will deactivate the whole system.", "spamprotection"); ?></p>
            
            <hr />
            
            <h3><?php _e("Check for duplicates", "spamprotection"); ?></h3>            
            <p><?php _e("1.) Here you can define where to search for duplicates. Searching in all items can cause heavy server load, if you have a lot of ads.", "spamprotection"); ?></p>
            <ul>
                <li><?php _e("Per user", "spamprotection"); ?><br /><small><?php _e("This will only search in ads from same user.", "spamprotection"); ?> <?php _e("<strong>(recommended)</strong>", "spamprotection"); ?></small></li>
                <li><?php _e("All items", "spamprotection"); ?><br /><small><?php _e("This will search in all ads in your database.", "spamprotection"); ?></small></li>
            </ul>
            
            <p><?php _e("2.) Here you can define what is checked while searching for duplicates", "spamprotection"); ?></p>
            <ul>
                <li><?php _e("Only title", "spamprotection"); ?><br /><small><?php _e("This will only search in title.", "spamprotection"); ?></small></li>
                <li><?php _e("Title and description", "spamprotection"); ?><br /><small><?php _e("This will search in title and description.", "spamprotection"); ?> <?php _e("<strong>(recommended)</strong>", "spamprotection"); ?></small></li>
            </ul>
            
            <p><?php _e("3.) Here you can define the algorythm which is used for duplicates search and where you want to search for duplicates.", "spamprotection"); ?></p>
            <ul>
                <li><?php _e("md5/string comparition", "spamprotection"); ?><br /><small><?php _e("This algorythm works as following: removing all blanks, convert text to lowercase, then get <a href=\"http://php.net/manual/en/function.md5.php\">md5 hash</a> of text and compare with other ads.", "spamprotection"); ?></small></li>
                <li><?php _e("Similar text", "spamprotection"); ?><br /><small><?php _e("This algorythm is using the <em><a href=\"http://php.net/manual/en/function.similar-text.php\">similar_text()</a></em> function: the whole text would be compared with other ads and gives back a percentage for its similarity. You can define how high the similarity can be at maximum.", "spamprotection"); ?> <?php _e("<strong>(recommended)</strong>", "spamprotection"); ?></small></li>
            </ul>
            
            <hr />
            
            <h3><?php _e("Check <a href=\"https://en.wikipedia.org/wiki/MX_record\">MX Record</a> of used Mail", "spamprotection"); ?></h3>            
            <p><?php _e("Sometimes mails from fake-domains are used. With this option you can check, if there is an authentically mailbox behind the used Email address.", "spamprotection"); ?></p>
            
            <hr />
            
            <h3><?php _e("Activate the Honeypot form field", "spamprotection"); ?></h3>            
            <p><?php _e("<a href=\"https://en.wikipedia.org/wiki/Honeypot_(computing)\">Honeypots</a> are a trap for bots. Normal users can't see them and don't fill it out. If this form field is filled out, the system knows that this is a bot and marks the ad as spam.", "spamprotection"); ?></p>
        
            <hr /><br />             
        
            <h2><?php _e("E-Mail Block", "spamprotection"); ?></h2>
            
            <h3><?php _e("Here you can block Emails on two ways:", "spamprotection"); ?></h3>
            <ul>
                <li><?php _e("Block banned E-Mailadresses", "spamprotection"); ?><br /><small><?php _e("Here you can save blocked Email addresses which are not allowed to use for new ads.", "spamprotection"); ?></small></li>
                <li><?php _e("Block banned E-Mail TLD", "spamprotection"); ?><br /><small><?php _e("Here you can block Email providers which are not allowed to use for new ads.", "spamprotection"); ?></small></li>
            </ul>
            
            <hr />             
        
            <h2><?php _e("Stopwords", "spamprotection"); ?></h2>            
            <p><?php _e("Save here your \"Badwords\" where you don't want, that they can be used in your ads.", "spamprotection"); ?></p>             
        
            <hr />
            
            <h2><?php _e("<a href=\"https://en.wikipedia.org/wiki/.htaccess\">.htaccess</a> Editor", "spamprotection"); ?></h2>            
            <p><?php _e("This is only for experienced users who know what they are doing. Don't use this option because it can cause heavy errors on you whole page!", "spamprotection"); ?></p>

        </div>
        
        <div id="sp_help_comments" class="helptab-content">
        
            <h2><?php _e("Main Settings", "spamprotection"); ?></h2>
            
            <h3><?php _e("Activate the Comment Spam Protection", "spamprotection"); ?></h3>
            <p><?php _e("Here you can de/activate the whole Spam Protection to secure your comments. You can deactivate some features optionally, but deactivate this checkbox will deactivate the whole system.", "spamprotection"); ?></p>
            
            <hr />
            
            <h3><?php _e("Check for Links", "spamprotection"); ?></h3>            
            <p><?php _e("Here you can define where to search for links/urls in comments.", "spamprotection"); ?></p>
            <ul>
                <li><?php _e("Deactivated", "spamprotection"); ?><br /><small><?php _e("Deactivate the search for links/urls.", "spamprotection"); ?></small></li>
                <li><?php _e("Only title", "spamprotection"); ?><br /><small><?php _e("Search only in comment title for links/urls.", "spamprotection"); ?></small></li>
                <li><?php _e("Title and description", "spamprotection"); ?><br /><small><?php _e("Search in comment title and description for links/urls.", "spamprotection"); ?> <?php _e("<strong>(recommended)</strong>", "spamprotection"); ?></small></li>
            </ul>
            
            <hr /><br />             
        
            <h2><?php _e("E-Mail Block", "spamprotection"); ?></h2>
            
            <h3><?php _e("Here you can block Emails on two ways:", "spamprotection"); ?></h3>
            <ul>
                <li><?php _e("Block banned E-Mailadresses", "spamprotection"); ?><br /><small><?php _e("Here you can save blocked Email addresses which are not allowed to use for new comments.", "spamprotection"); ?></small></li>
                <li><?php _e("Block banned E-Mail TLD", "spamprotection"); ?><br /><small><?php _e("Here you can block Email providers which are not allowed to use for new comments.", "spamprotection"); ?></small></li>
            </ul>
            
            <hr />             
        
            <h2><?php _e("Stopwords", "spamprotection"); ?></h2>            
            <p><?php _e("Save here your \"Badwords\" where you don't want, that they can be used in your comments.", "spamprotection"); ?></p>
        
        </div>
        
        <div id="sp_help_contacts" class="helptab-content">
        
            <h2><?php _e("Main Settings", "spamprotection"); ?></h2>
            
            <h3><?php _e("Activate the Contact Form Spam Protection", "spamprotection"); ?></h3>
            <p><?php _e("Here you can de/activate the whole Spam Protection to secure your contact mails. You can deactivate some features optionally, but deactivate this checkbox will deactivate the whole system.", "spamprotection"); ?></p>
            
            <hr />
            
            <h3><?php _e("Check for Links", "spamprotection"); ?></h3>            
            <p><?php _e("Here you can define where to search for links/urls in contact mails.", "spamprotection"); ?></p>
            <ul>
                <li><?php _e("Activated", "spamprotection"); ?><br /><small><?php _e("Activate the search for links/urls.", "spamprotection"); ?> <?php _e("<strong>(recommended)</strong>", "spamprotection"); ?></small></li>
                <li><?php _e("Deactivated", "spamprotection"); ?><br /><small><?php _e("Deactivate the search for links/urls.", "spamprotection"); ?></small></li>
            </ul>
            
            <hr />
            
            <h3><?php _e("Activate the Honeypot form field", "spamprotection"); ?></h3>            
            <p><?php _e("Honeypots are a trap for bots. Normal users can't see them and don't fill it out. If this form field is filled out, the system knows that this is a bot and marks the ad as spam.", "spamprotection"); ?></p>
            
            <hr /><br />             
        
            <h2><?php _e("E-Mail Block", "spamprotection"); ?></h2>
            
            <h3><?php _e("Here you can block Emails on two ways:", "spamprotection"); ?></h3>
            <ul>
                <li><?php _e("Block banned E-Mailadresses", "spamprotection"); ?><br /><small><?php _e("Here you can save blocked Email addresses which are not allowed to use for new contact mails.", "spamprotection"); ?></small></li>
                <li><?php _e("Block banned E-Mail TLD", "spamprotection"); ?><br /><small><?php _e("Here you can block Email providers which are not allowed to use for new contact mails.", "spamprotection"); ?></small></li>
            </ul>
            
            <hr />             
        
            <h2><?php _e("Stopwords", "spamprotection"); ?></h2>            
            <p><?php _e("Save here your \"Badwords\" where you don't want, that they can be used in your contact mails.", "spamprotection"); ?></p>
                    
        </div>
        
        <div id="sp_help_about" class="helptab-content">
            <h2><?php _e("About this plugin", "spamprotection"); ?></h2>
            <p><?php _e("This plugin I've developed at the request of the community in the OSClass forum. There I was given lots of ideas and some have participated in beta tests.<br /><br /><strong>Thanks to all for your help!</strong><br /><br />I hope this plugin will be useful to many of you and it helps you to reduce the spam on your pages to a minimum. Keep in mind, however, that it was never intended to fight spam automatically. It is just a tool to mark the spam and to check it manually.<br /><br />An automated system would always involve the risk that even realistic ads will be marked as spam and not published.<br /><br />If you find any errors, please report it in the forum and tell it to me. I will try to fix this immediately, so that everyone can use this plugin properly.", "spamprotection"); ?></p>
        </div>
        
    </div>
    
</div>
<div style="display: none;">                             
    <br />
    <p><?php _e('If an ad was marked as spam, you will be informed on your admin page.', 'spamprotection'); ?></p>
    <img src="<?php echo osc_plugin_url('spamprotection/assets/img/adminmenu.jpg').'adminmenu.jpg'; ?>" />
    <p><?php _e('This button redirect you to the table, where all spam ads are listed.', 'spamprotection'); ?></p>
    <p><?php _e('Here you can find a new option to check this ad and choose some action.', 'spamprotection'); ?></p>
    <img src="<?php echo osc_plugin_url('spamprotection/assets/img/list.jpg').'list.jpg'; ?>" />
    <br /><br />
    <p><?php _e('Here you can see all informations about this ad or user and choose your preferred action.', 'spamprotection'); ?></p>
    <img src="<?php echo osc_plugin_url('spamprotection/assets/img/check.jpg').'check.jpg'; ?>" />
    <br /><br /><br />
    
</div>