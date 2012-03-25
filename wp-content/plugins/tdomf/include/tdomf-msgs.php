<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

function tdomf_get_message($key,$form_id = false) {
    $message = "";
    $mode = false;
    if($form_id === false) {
        $message = get_option($key);
    } else {
        $mode = tdomf_generate_default_form_mode($form_id);
        $message = tdomf_get_option_form($key,$form_id);
    }
    if($message === false) {
        $message = tdomf_get_message_default($key,$mode);
    }
    return $message;
}

function tdomf_protect_input($message) {
    # This function passes the string through Wordpress kses filters,
    # this should pull out javascript hacks and php code. It should be used
    # on any input
    #
    global $allowedposttags;
    #if(!current_user_can('unfiltered_html')) {
        $message = wp_kses($message,$allowedposttags);
    #}   
    return $message;
}

function tdomf_prepare_string($message, $form_id = false, $mode = "", $post_id = false, $errors = "", $post_args = array()) {
    global $current_user;
    if($post_id !== false) {
        $post = &get_post($post_id);
        
        // "post_date" is now only updated when a post is published
        // so now submission date is captured in a custom field
        // Failing that, go back to the old method of post_modified
        //
        if($post->post_status == 'publish' || $post->post_status =='future') {
            $submission_date = mysql2date(get_option('date_format'),$post->post_date_gmt);
            $submission_time = mysql2date(get_option('time_format'),$post->post_date_gmt);
        } else if(get_post_meta($post_id,TDOMF_KEY_SUBMISSION_DATE_GMT,true)) {
            $date = get_post_meta($post_id,TDOMF_KEY_SUBMISSION_DATE_GMT,true);
            $submission_date = mysql2date(get_option('date_format'),$date);
            $submission_time = mysql2date(get_option('time_format'),$date);
        } else {
            $submission_date = mysql2date(get_option('date_format'),$post->post_modified_gmt);
            $submission_time = mysql2date(get_option('time_format'),$post->post_modified_gmt);
        }
               
        // url, date and time are safe but title is not: scrub
        $patterns = array ( '/'.TDOMF_MACRO_SUBMISSIONURL.'/',
                            '/'.TDOMF_MACRO_SUBMISSIONDATE.'/',
                            '/'.TDOMF_MACRO_SUBMISSIONTIME.'/',
                            '/'.TDOMF_MACRO_SUBMISSIONTITLE.'/');
        $replacements = array( get_permalink($post_id),
                               $submission_date,
                               $submission_time,
                               tdomf_protect_input($post->post_title));
                
        $message = preg_replace($patterns,$replacements,$message);
    }
    
    if(!empty($errors)) {
        $message = preg_replace('/'.TDOMF_MACRO_SUBMISSIONERRORS.'/',$errors,$message);
    }
    
    if(is_user_logged_in()) {
        get_currentuserinfo();
        // might not be safe
        $message = preg_replace('/'.TDOMF_MACRO_USERNAME.'/',tdomf_protect_input($current_user->display_name),$message);
    } else if( $post_id !== false) {
        // may not be safe at all
        $message = preg_replace('/'.TDOMF_MACRO_USERNAME.'/',tdomf_protect_input(get_post_meta($post_id,TDOMF_KEY_NAME,true)),$message);
    } else {
        $message = preg_replace('/'.TDOMF_MACRO_USERNAME.'/',__("Unregistered","tdomf"),$message);
    }
    
    $message = preg_replace('/'.TDOMF_MACRO_IP.'/',$_SERVER['REMOTE_ADDR'],$message);
    
    if($form_id !== false) {
         
        // these macros are inputed by form admin so are considered safe
        $patterns = array ( '/'.TDOMF_MACRO_FORMURL.'/',
                            '/'.TDOMF_MACRO_FORMID.'/',
                            '/'.TDOMF_MACRO_FORMNAME.'/',
                            '/'.TDOMF_MACRO_FORMDESCRIPTION.'/' );
        $replacements = array ( $_SERVER['REQUEST_URI'].'#tdomf_form'.$form_id,
                                $form_id,
                                tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id),
                                tdomf_get_option_form(TDOMF_OPTION_DESCRIPTION,$form_id) );
        $message = preg_replace($patterns,$replacements,$message);
    }

    // A lot of people use the ID uppercase format
    $post_ID = $post_id;
    
    // execute any PHP code in the message    
    ob_start();
    extract($post_args,EXTR_PREFIX_INVALID,"tdomf_");
    $message = @eval("?>".$message);
    $message = ob_get_contents();
    ob_end_clean();
    
    return $message;
}

function tdomf_get_message_instance($key, $form_id = false, $mode = false, $post_id = false, $errors = "") {
    global $current_user;
    $message = tdomf_get_message($key,$form_id);
    if(!empty($message) || $message !== false) {
        return tdomf_prepare_string($message, $form_id, $mode, $post_id, $errors);
    }
    return "";
}

function tdomf_get_message_default($key,$mode) {
    
    switch($key) {
        case TDOMF_OPTION_MSG_SUB_PUBLISH:
            $retVal = __("Your submission \"%%SUBMISSIONTITLE%%\" has been automatically published. You can see it <a href='%%SUBMISSIONURL%%'>here</a>. Thank you for using this service.","tdomf");
            break;
        case TDOMF_OPTION_MSG_SUB_FUTURE:
            $retVal = __("Your submission has been accepted and will be published on %%SUBMISSIONDATE%% at %%SUBMISSIONTIME%%. Thank you for using this service.","tdomf");
            break;
        case TDOMF_OPTION_MSG_SUB_SPAM:
            $retVal = __("Your submission is being flagged as spam! Sorry","tdomf");
            break;
        case TDOMF_OPTION_MSG_SUB_MOD:
            $retVal = __("Your post submission has been added to the moderation queue. It should appear in the next few days. Thank you for using this service.","tdomf");
            break;
        case TDOMF_OPTION_MSG_SUB_ERROR:
            $retVal = __("Your submission contained errors:<br/><br/>%%SUBMISSIONERRORS%%<br/><br/>Please correct and resubmit.","tdomf");
            break;
        case TDOMF_OPTION_MSG_PERM_BANNED_USER:
            $retVal = __("You (%%USERNAME%%) are banned from using this form.","tdomf");
            break;
        case TDOMF_OPTION_MSG_PERM_BANNED_IP:
            $retVal = __("Your IP %%IP%% does not currently have permissions to use this form.","tdomf");
            break;
        case TDOMF_OPTION_MSG_PERM_THROTTLE:
            $retVal = __("You have hit your submissions quota. Please wait until your existing submissions are approved.","tdomf");
            break;
        case TDOMF_OPTION_MSG_PERM_INVALID_USER:
            $retVal = __("You (%%USERNAME%%) do not currently have permissions to use this form.","tdomf");
            break;
        case TDOMF_OPTION_MSG_PERM_INVALID_NOUSER:
            $retVal = __("Unregistered users do not currently have permissions to use this form.","tdomf");
            break;
        case TDOMF_OPTION_ADD_EDIT_LINK_TEXT:
            $retVal = __("Edit","tdomf");
            break;
        case TDOMF_OPTION_MSG_INVALID_POST:
            $retVal = __("That post you are attempting to edit is invalid",'tdomf');
            break;
        case TDOMF_OPTION_MSG_INVALID_FORM:
            $retVal = __("You cannot use this form to edit this post",'tdomf');
            break;
        case TDOMF_OPTION_MSG_SPAM_EDIT_ON_POST:
            $retVal = __("You cannot edit this post as there is a pending contribution to be resolved.",'tdomf');
            break;
        case TDOMF_OPTION_MSG_UNAPPROVED_EDIT_ON_POST:
            $retVal = __("You cannot edit this post as there is a pending contribution to be approved.",'tdomf');
            break;
        case TDOMF_OPTION_MSG_LOCKED_POST:
            $retVal = __("You cannot edit this post as it has been locked from editing.",'tdomf');
            break;
        default:
            $retVal = "";
            break;
    }
    
    // Edit form changes some of the defaults
    
    if($mode && TDOMF_Widget::isEditForm($mode)) {
        switch($key) {
            case TDOMF_OPTION_MSG_SUB_PUBLISH:
                $retVal = __("Your contribution on post \"%%SUBMISSIONTITLE%%\" has been automatically approved. You can see it <a href='%%SUBMISSIONURL%%'>here</a>. Thank you for using this service.","tdomf");
                break;
            case TDOMF_OPTION_MSG_SUB_FUTURE:
                $retVal = __("Your contribution has been approved and will be published on %%SUBMISSIONDATE%% at %%SUBMISSIONTIME%%. Thank you for using this service.","tdomf");
                break;
            case TDOMF_OPTION_MSG_SUB_SPAM:
                $retVal = __("Your contribution has being flagged as spam! Sorry","tdomf");
                break;
            case TDOMF_OPTION_MSG_SUB_MOD:
                $retVal = __("Your contribution has been added to the moderation queue. It should appear in the next few days. Thank you for using this service.","tdomf");
                break;
            case TDOMF_OPTION_MSG_SUB_ERROR:
                $retVal = __("Your contribution contained errors:<br/><br/>%%SUBMISSIONERRORS%%<br/><br/>Please correct and resubmit.","tdomf");
                break;
            case TDOMF_OPTION_MSG_PERM_THROTTLE:
                $retVal = __("You have hit your contributions quota. Please wait until your existing contributions are approved.","tdomf");
                break;            
        }
    }
    
    return $retVal;
}

?>
