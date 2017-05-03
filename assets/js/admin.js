$(document).ready(function(){
    
    if ($("input[name=sp_activate]").is(":checked")) {            
        $("#sp_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);
    } if ($("input[name=sp_comment_activate]").is(":checked")) {            
        $("#sp_comment_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);
    } if ($("input[name=sp_contact_activate]").is(":checked")) {            
        $("#sp_contact_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);
    } if ($("input[name=sp_security_activate]").is(":checked")) {            
        $("#sp_security_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);
    } if ($("input[name=sp_honeypot]").is(":checked")) {
        $("#honeypot").addClass("visible");        
    } if ($("input[name=sp_blocked]").is(":checked")) {
        $("#blocked").addClass("visible");        
    } if ($("input[name=sp_blocked_tld]").is(":checked")) {
        $("#blocked_tld").addClass("visible");        
    } if ($("input[name=sp_comment_blocked]").is(":checked")) {
        $("#comment_blocked").addClass("visible");        
    } if ($("input[name=sp_comment_blocked_tld]").is(":checked")) {
        $("#comment_blocked_tld").addClass("visible");        
    } if ($("input[name=sp_contact_blocked]").is(":checked")) {
        $("#contact_blocked").addClass("visible");        
    } if ($("input[name=sp_contact_blocked_tld]").is(":checked")) {
        $("#contact_blocked_tld").addClass("visible");        
    } if ($("input[name=sp_security_login_check]").is(":checked")) {
        $("#sp_security_login_count").addClass("visible");        
    }
    
    $(document).on("click", "input[name=sp_activate]", function(){
        if ($("input[name=sp_activate]").is(":checked")) {
            $("#sp_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);        
        } else {
            $("#sp_options").removeClass("enabled").addClass("disabled").find("input, textarea").prop("disabled", true);
            $("input[name=sp_activate]").prop("disabled", false);
        }  
    });
    
    $(document).on("click", "input[name=sp_comment_activate]", function(){
        if ($("input[name=sp_comment_activate]").is(":checked")) {
            $("#sp_comment_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);        
        } else {
            $("#sp_comment_options").removeClass("enabled").addClass("disabled").find("input, textarea").prop("disabled", true);
            $("input[name=sp_comment_activate]").prop("disabled", false);
        }  
    });
    
    $(document).on("click", "input[name=sp_contact_activate]", function(){
        if ($("input[name=sp_contact_activate]").is(":checked")) {
            $("#sp_contact_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);        
        } else {
            $("#sp_contact_options").removeClass("enabled").addClass("disabled").find("input, textarea").prop("disabled", true);
            $("input[name=sp_contact_activate]").prop("disabled", false);
        }  
    });
    
    $(document).on("click", "input[name=sp_security_activate]", function(){
        if ($("input[name=sp_security_activate]").is(":checked")) {
            $("#sp_security_options").removeClass("disabled").addClass("enabled").find("input, textarea").prop("disabled", false);        
        } else {
            $("#sp_security_options").removeClass("enabled").addClass("disabled").find("input, textarea").prop("disabled", true);
            $("input[name=sp_security_activate]").prop("disabled", false);
        }  
    });
    
    
    $(document).on("click", "input[name=sp_honeypot]", function(){
        if ($("input[name=sp_honeypot]").is(":checked")) {
            $("#honeypot").addClass("visible");        
        } else {
            $("#honeypot").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_contact_honeypot]", function(){
        if ($("input[name=sp_contact_honeypot]").is(":checked")) {
            $("#contact_honeypot").addClass("visible");        
        } else {
            $("#contact_honeypot").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_blocked]", function(){
        if ($("input[name=sp_blocked]").is(":checked")) {
            $("#blocked").addClass("visible");        
        } else {
            $("#blocked").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_comment_blocked]", function(){
        if ($("input[name=sp_comment_blocked]").is(":checked")) {
            $("#comment_blocked").addClass("visible");        
        } else {
            $("#comment_blocked").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_contact_blocked]", function(){
        if ($("input[name=sp_contact_blocked]").is(":checked")) {
            $("#contact_blocked").addClass("visible");        
        } else {
            $("#contact_blocked").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_blocked_tld]", function(){
        if ($("input[name=sp_blocked_tld]").is(":checked")) {
            $("#blocked_tld").addClass("visible");        
        } else {
            $("#blocked_tld").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_comment_blocked_tld]", function(){
        if ($("input[name=sp_comment_blocked_tld]").is(":checked")) {
            $("#comment_blocked_tld").addClass("visible");        
        } else {
            $("#comment_blocked_tld").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_contact_blocked_tld]", function(){
        if ($("input[name=sp_contact_blocked_tld]").is(":checked")) {
            $("#contact_blocked_tld").addClass("visible");        
        } else {
            $("#contact_blocked_tld").removeClass("visible");
        }    
    });
    
    $(document).on("click", "input[name=sp_security_login_hp], input[name=sp_security_recover_hp]", function(){
        
        var login   = $("input[name=sp_security_login_hp]"),
            recover = $("input[name=sp_security_recover_hp]");
        
        if (login.is(":checked")) {
            $("#sp_security_login_honeypots, #sp_security_login_hp_cont").fadeIn("slow");        
        } else {
            if (!recover.is(":checked")) {
                $("#sp_security_login_honeypots").fadeOut("slow");
            }
            $("#sp_security_login_hp_cont").fadeOut("slow");
        }
            
        if (recover.is(":checked")) {
            $("#sp_security_login_honeypots, #sp_security_recover_hp_cont").fadeIn("slow");        
        } else {
            if (!login.is(":checked")) {
                $("#sp_security_login_honeypots").fadeOut("slow");
            }
            $("#sp_security_recover_hp_cont").fadeOut("slow");
        }    
    });
    
    $(document).on("click", "ul.tabs li", function(){
        var tab_id = $(this).attr('data-tab');

        $("input#sp_tab").val(tab_id);
        
        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#"+tab_id).addClass('current');
    });
    
    $(document).on("click", "ul.subtabs li", function(){
        var tab_id      = $(this).attr('data-tab'),
            currentid   = $(this).closest('div').parent().prop('id');
        
        $('#'+currentid+' ul.subtabs li').removeClass('current');
        $('#'+currentid+' .subtab-content').removeClass('current');

        $(this).addClass('current');
        $("#"+tab_id).addClass('current');
    });
    
    $(document).on("focusout", "input[name=honeypot_name]", function(){
        $(this).removeClass("valid, invalid");
        $("#validname").html("").css("color", "");    
    });
    
    $(document).on("focusout", "input[name=contact_honeypot_name]", function(){
        $(this).removeClass("valid, invalid");
        $("#contact_validname").html("").css("color", "");    
    });
    
    $(document).on("keyup", "input[name=honeypot_name]", function(){
        var string  = $(this).val(),
            reg     = new RegExp("^[A-z0-9_-]+$");
            
        if (string.length < 1) {
            $(this).removeClass("valid invalid");
            $("#validname").html("").css("color", "");
        } else if (reg.test(string)) {
            $(this).removeClass("invalid").addClass("valid");            
            $("#validname").html("valid").css("color", "green");
        } else {
            $(this).removeClass("valid").addClass("invalid");
            $("#validname").html("invalid").css("color", "red");
        }    
    });
    
    $(document).on("keyup", "input[name=contact_honeypot_name]", function(){
        var string  = $(this).val(),
            reg     = new RegExp("^[A-z0-9_-]+$");
            
        if (string.length < 1) {
            $(this).removeClass("valid invalid");
            $("#contact_validname").html("").css("color", "");
        } else if (reg.test(string)) {
            $(this).removeClass("invalid").addClass("valid");            
            $("#contact_validname").html("valid").css("color", "green");
        } else {
            $(this).removeClass("valid").addClass("invalid");
            $("#contact_validname").html("invalid").css("color", "red");
        }    
    });
    
    $(document).on("keyup", "textarea[name=sp_htaccess]", function(){
        if ($("input[name=changed_htaccess]").length < 1) {
            $('<input type="hidden" name="changed_htaccess" value="1" />').insertAfter("textarea[name=sp_htaccess]");
        }    
    });
    
    $(document).on("focus", "textarea[name=sp_htaccess]", function(){
        if ($("input[name=attention_htaccess]").length < 1) {
            $("#attention").fadeToggle(1000, function(){
                $("#attention_content").fadeToggle(800);                
            });
        }
            
    });
    
    $(document).on("click", "#attention_ok", function(event){
        event.preventDefault();
        $("#attention_content").fadeToggle(500, function(){
            $("#attention").fadeToggle(400);
            if ($("input[name=attention_htaccess]").length < 1) {
                $('<input type="hidden" name="attention_htaccess" value="1" />').insertAfter("textarea[name=sp_htaccess]");
            }
        });    
    });
    
    $(document).on("click", "#attention_save", function(event){
        event.preventDefault();
        var file = $(this).data("file");
        $("#attention_content").fadeToggle(500, function(){
            $("#attention").fadeToggle(400);
            if ($("input[name=attention_htaccess]").length < 1) {
                $('<input type="hidden" name="attention_htaccess" value="1" />').insertAfter("textarea[name=sp_htaccess]");
                $.get(file+'?htaccess=save', function(result){
                    
                });
            }
        });    
    });
    
    $(document).on("submit", "#sp_save_settings", function(event) {
        event.preventDefault();
        var changed = $("input[name=changed_htaccess]").val();
        if (changed && changed == '1') {
            if (confirm('Your .htaccess was modified. You really want to save?')) {
                $("input[name=changed_htaccess]").remove();
                this.submit();   
            }
        } else {
            this.submit();
        }
    })
    
    $(document).on("click", ".viewToggle", function(event){
        event.preventDefault();
        console.log("clicked");
        
        if ($("textarea#descriptionCode").is(":visible")) {
            console.log("code visible");
            var icon = $(this).children("i");
            $("textarea#descriptionCode").fadeOut("slow", function(){
                $(icon).removeClass("fa-eye").addClass("fa-code");
                $("div#descriptionHTML").fadeIn("slow");
            })
        } else if ($("div#descriptionHTML").is(":visible")) {
            console.log("html visible");
            var icon = $(this).children("i");
            $("div#descriptionHTML").fadeOut("slow", function(){
                $(icon).removeClass("fa-code").addClass("fa-eye");
                $("textarea#descriptionCode").fadeIn("slow");
            })
        }
    });
    
    $(document).on("change", "#sp_duplicates_as", function(event){
        
        event.preventDefault();        
        var value = $(this).val(),
            type  = $("#sp_duplicate_type").val();
        
        if (value && value == '1' || value == '2') {
            $("#sp_duplicates_cont, #sp_duplicate_type_cont").fadeIn("slow");
            if (type && type == '1') {
                $("#sp_duplicate_percent_cont").fadeIn("slow");    
            }                
        } else {
            $("#sp_duplicates_cont, #sp_duplicate_type_cont, #sp_duplicate_percent_cont").fadeOut("slow");    
        }    
    });
    
    $(document).on("change", "#sp_duplicate_type", function(event){
        
        event.preventDefault();        
        var type = $(this).val();
        
        if (type && type == '1') {
            $("#sp_duplicate_percent_cont").fadeIn("slow");    
        } else if (type && type == '0') {
            $("#sp_duplicate_percent_cont").fadeOut("slow");    
        }    
    });
    
    $(document).on("change", "#sp_security_login_check", function(event){
        
        event.preventDefault();        
        var type = $(this).val();
        
        if (type && type == '1') {
            $("#sp_security_login_count_cont, #sp_security_login_action_cont").fadeIn("slow");    
        } else if (type && type == '0') {
            $("#sp_security_login_count_cont, #sp_security_login_action_cont").fadeOut("slow");    
        }    
    });
    
    $(document).on("click", "#sp_review", function(event){
        event.preventDefault();
        $("#sp_review_wrap").fadeToggle("slow");  
    });
    
    $(document).on("click", ".sp_review_close", function(event){
        event.preventDefault();
        $("#sp_review_wrap").fadeOut("slow");  
    });    
    
});