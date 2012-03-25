<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/////////////////////////////////////////////////////////////
// Logging function to aid debugging and tracking activity //
/////////////////////////////////////////////////////////////

// Some pre-defined types/colours
//
define('TDOMF_LOG_ERROR',   "red");
define('TDOMF_LOG_GENERAL', "gray");
define('TDOMF_LOG_MEMORY',  "brown");
define('TDOMF_LOG_SYSTEM',  "blue");
define('TDOMF_LOG_GOOD',    "green");
define('TDOMF_LOG_BAD',     "black");

// Returns a formatted date and time stamp for log messages
//
function tdomf_get_log_timestamp(){
   return date('d-m-y')."(".date('G:i:s').")";
}

// Returns a formatted user-name stamp for log messages
//
function tdomf_get_log_userstamp(){
   global $current_user;

   if(!function_exists("get_currentuserinfo")) {
      return $_SERVER['REMOTE_ADDR'];
   }

   get_currentuserinfo();

   if(is_user_logged_in()) {
      $user_id = get_option(TDOMF_DEFAULT_AUTHOR);
      // if dummy author, use IP instead
      if($user_id != $current_user->ID) {
        return $current_user->user_login;
   	  }
   }

   return $_SERVER['REMOTE_ADDR'];

}

//////////////////////////////
// The actual logging function
//
function tdomf_log_message($message,$color=TDOMF_LOG_GENERAL){
   $max_lines = get_option(TDOMF_OPTION_LOG_MAX_SIZE);

   if($max_lines > 0) {
       $timestamp = tdomf_get_log_timestamp();
       $userstamp = tdomf_get_log_userstamp();
       $msg = "";
       if(isset($color)){
          $msg .= "<font color=\"".$color."\">";
       }
       $msg .= "[$userstamp][$timestamp] $message";
       if(isset($color)){
          $msg .= "</font>";
       }
       $msg .= "<br>";
       $current_log = get_option(TDOMF_LOG);
       if($current_log != false) {
          
          // do we need to limit log?
          
          $array_log = explode("\n",$current_log);
          $current_lines = count($array_log); 
          if( $current_lines > $max_lines) {
              $array_log = array_slice($array_log, $current_lines - ($max_lines + 2));
              $current_log = implode("\n",$array_log);
              $current_log = "[-][$timestamp] Log Exceeded Limit - Automatically Cut<br/>\n" . $current_log;
          }
              
          $current_log .= "\n".$msg;
          update_option(TDOMF_LOG,$current_log);
       } else {
          add_option(TDOMF_LOG,$msg);
       }
   }
}

// The actual logging function
//
function tdomf_log_message_extra($message,$color=TDOMF_LOG_GENERAL){
  if(get_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES)) {
    tdomf_log_message($message,$color);
  }
}

// For memory debugging
//
function tdomf_log_mem_usage($file,$line){
    if(function_exists('memory_get_usage')) {
        tdomf_log_message(basename($file).":".$line.": current memory footprint: ".memory_get_usage(),TDOMF_LOG_MEMORY);
    }
}

// Clear/Empty the log
//
function tdomf_clear_log(){
	if(get_option(TDOMF_LOG) != false) {
		delete_option(TDOMF_LOG);
	}
	tdomf_log_message('Log cleared');
}
//
// Get the log or the last X lines of log
//
function tdomf_get_log($limit=0){
  $log = get_option(TDOMF_LOG);
  if($log != false) {
     if($limit<=0) {
        return $log;
     } else {
        // limit the log to the last $limit lines
        $lines=explode("\n",$log);
        $lines=array_reverse($lines);
        $limited_log = join("\n",array_slice($lines,0,$limit));
        // undo reverse
        $lines=explode("\n",$limited_log);
        $lines=array_reverse($lines);
        $limited_log = join("\n",$lines);
        // pass back limited, correctly ordered log
        return $limited_log;
     }
  }
  return "The log is currently empty!";
}

function tdomf_print_var($var) {
    echo "<pre>".htmlentities(var_export($var,true))."</pre>";
}

function tdomf_print_backtrace() {
    $funcs = debug_backtrace();
    tdomf_print_var($funcs);
}

?>
