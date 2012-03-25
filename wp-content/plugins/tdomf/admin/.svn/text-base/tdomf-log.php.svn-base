<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

////////////////////
// Log Admin Page //
////////////////////

// Handle post actions
//
function tdomf_log_handle_actions() {
   if(isset($_REQUEST['action'])) {
      $action = $_REQUEST['action'];
      if($action == "clear") {
         check_admin_referer('tdomf-log-empty');
         tdomf_clear_log(); ?>
         <div id="message" class="updated fade"><p>
         <?php _e("Log cleared."); ?>
         </p></div>
      <?php
      }
   }
}

// Display the log!
//
function tdomf_show_log_menu() {

  tdomf_log_handle_actions();

  ?>

  <div class="wrap">

    <h2><?php _e('Latest Activity', 'tdomf') ?></h2>

    <table border="0"><tr>

    <td>
    <form method="post" action="<?php /* echo $_SERVER['REQUEST_URI']; */ ?>">
    <input type="submit" name="refresh" value="Refresh" />
    </form>
    </td>

    <td>
    <form method="post" action="<?php /* echo $_SERVER['REQUEST_URI']; */ ?>">
    <input type="hidden" name="action" value="clear" />
    <input type="submit" name="clear" value="Empty Log" />
    <?php if(function_exists('wp_nonce_field')){ wp_nonce_field('tdomf-log-empty'); } ?>
    </form>
    </td>

    </tr></table>

    <br/>
    
    <div style="overflow: auto; height: 500px;"> <?php echo tdomf_get_log(0); ?> </div>

</div>

<?php
}
?>
