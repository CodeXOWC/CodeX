<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

///////////////////////
// TDOMF Debug Info  //
///////////////////////

// http://ie2.php.net/time
function timeDiff($time, $opt = array()) {
    // The default values
    $defOptions = array(
        'to' => 0,
        'parts' => 1,
        'precision' => 'second',
        'distance' => TRUE,
        'separator' => ', '
    );
    $opt = array_merge($defOptions, $opt);
    // Default to current time if no to point is given
    (!$opt['to']) && ($opt['to'] = time());
    // Init an empty string
    $str = '';
    // To or From computation
    $diff = ($opt['to'] > $time) ? $opt['to']-$time : $time-$opt['to'];
    // An array of label => periods of seconds;
    $periods = array(
        __('decade','tdomf') => 315569260,
        __('year','tdomf') => 31556926,
        __('month','tdomf') => 2629744,
        __('week','tdomf') => 604800,
        __('day','tdomf') => 86400,
        __('hour','tdomf') => 3600,
        __('minute','tdomf') => 60,
        __('second','tdomf') => 1
    );
    // Round to precision
    if ($opt['precision'] != 'second') 
        $diff = round(($diff/$periods[$opt['precision']])) * $periods[$opt['precision']];
    // Report the value is 'less than 1 ' precision period away
    (0 == $diff) && ($str = 'less than 1 '.$opt['precision']);
    // Loop over each period
    foreach ($periods as $label => $value) {
        // Stitch together the time difference string
        (($x=floor($diff/$value))&&$opt['parts']--) && $str.=($str?$opt['separator']:'').($x.' '.$label.($x>1?'s':''));
        // Stop processing if no more parts are going to be reported.
        if ($opt['parts'] == 0 || $label == $opt['precision']) break;
        // Get ready for the next pass
        $diff -= $x*$value;
    }
    $opt['distance'] && $str.=($str&&$opt['to']>$time)? __(' ago','tdomf'):__(' away','tdomf');
    return $str;
}

function tdomfinfo_html_display() { ?>
    <table border="0">
      <?php $alloptions = wp_load_alloptions();
            foreach($alloptions as $id => $val) {
              if($id == TDOMF_LOG) { ?>
                <tr>
                   <td><?php echo $id; ?></td>
                   <td><a href="admin.php?page=tdomf_show_log_menu"><?php _e("View Log","tdomf"); ?></td>
                </tr>
              <?php } else if(preg_match('#^tdomf_.+#',$id)) { ?>
                <tr>
                   <td><?php echo $id; ?></td>
                   <td><?php echo htmlentities(strval($val)); ?></td>
                </tr>
              <?php }
            } ?>
      <?php $form_ids = tdomf_get_form_ids();
      foreach($form_ids as $form_id) {
        $name = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id->form_id); ?>
        <tr><td colspan="2"><b><center>Form <?php echo $form_id->form_id ?></center></b></td></tr>
        <tr>
          <td>Name</td>
          <td><?php echo $name; ?></td>
          </tr>
        <?php $options = tdomf_get_options_form($form_id->form_id);
        foreach($options as $option => $value) { ?>
          <tr>
          <td><?php echo $option; ?></td>
          <td><?php echo htmlentities(var_export($value,true)); ?></td>
          </tr>
        <?php } 
        $widgets = tdomf_get_widgets_form($form_id->form_id);
      if(!empty($widgets)) { ?>
        <tr><td colspan="2"><center>Widgets for Form <?php echo $form_id->form_id ?></center></td></tr>
      <?php foreach($widgets as $widget) { ?>
        <tr>
          <td><?php echo $widget->widget_key; ?></td>
          <td><?php echo htmlentities($widget->widget_value); ?></td>
          </tr>
      <?php } }
      } ?>
      </table>
      
      <?php $sessions = tdomf_get_sessions(); if($sessions != false && !empty($sessions)) { ?>
        <h2><?php _e('Active Sessions', 'tdomf') ?></h2>
        <p><?php printf(__("There is currently %d active sessions.","tdomf"),count($sessions)); ?></p>
        <table border="1">
          <tr>
          <td><?php _e("Session Key","tdomf"); ?></td>
          <td><?php _e("Idle","tdomf"); ?></td>
          <td><?php _e("Session Data","tdomf"); ?></td>
          </tr>
        <?php foreach($sessions as $session) { ?>
          <tr>
          <td><?php echo $session->session_key; ?></td>
          <td><?php echo timeDiff($session->session_timestamp); ?></td>
          <td><?php echo htmlentities($session->session_data); ?></td>
          </tr>
        <?php } ?>
        </table>
      <?php }?>
      
      <?php
}

function tdomfinfo_html_text() { ?>
    <textarea rows="200" cols="100"><table border="0">
      <?php $alloptions = wp_load_alloptions();
            foreach($alloptions as $id => $val) {
              if($id == TDOMF_LOG) { ?>
                <tr>
                   <td><?php echo $id; ?></td>
                   <td><a href="admin.php?page=tdomf_show_log_menu"><?php _e("View Log","tdomf"); ?></td>
                </tr>
              <?php } else if(preg_match('#^tdomf_.+#',$id)) { ?>
                <tr>
                   <td><?php echo $id; ?></td>
                   <td><?php echo htmlentities(strval($val)); ?></td>
                </tr>
              <?php }
            } ?>
      <?php $form_ids = tdomf_get_form_ids();
      foreach($form_ids as $form_id) {
        $name = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id->form_id); ?>
        <tr><td colspan="2"><b><center>Form <?php echo $form_id->form_id ?></center></b></td></tr>
        <tr>
          <td>Name</td>
          <td><?php echo $name; ?></td>
          </tr>
        <?php $options = tdomf_get_options_form($form_id->form_id);
        foreach($options as $option => $value) { ?>
          <tr>
          <td><?php echo $option; ?></td>
          <td><?php echo htmlentities(htmlentities(var_export($value,true))); ?></td>
          </tr>
        <?php } 
        $widgets = tdomf_get_widgets_form($form_id->form_id);
      if(!empty($widgets)) { ?>
        <tr><td colspan="2"><center>Widgets for Form <?php echo $form_id->form_id ?></center></td></tr>
      <?php foreach($widgets as $widget) { ?>
        <tr>
          <td><?php echo $widget->widget_key; ?></td>
          <td><?php echo htmlentities($widget->widget_value); ?></td>
          </tr>
      <?php } }
      } ?>
      </table></textarea>
      
      <?php
}

function tdomfinfo_text_display() { ?>
    <pre>
^**Option** ^ **Value** ^ <?php $alloptions = wp_load_alloptions(); foreach($alloptions as $id => $val) {
      if(preg_match('#^tdomf_.+#',$id) && $id != TDOMF_LOG) { ?> 
| <?php echo $id; ?> | <?php echo htmlentities(strval($val)); ?> | <?php } } 
      $form_ids = tdomf_get_form_ids();
      foreach($form_ids as $form_id) {
        $name = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id->form_id); ?>

        
== Form <?php echo $form_id->form_id ?> ==

= Name = 
<?php echo $name; ?>

<?php $options = tdomf_get_options_form($form_id->form_id);
        foreach($options as $option => $value) { ?>
= <?php echo $option; ?> =
<?php echo htmlentities(var_export($value,true)); ?>

<?php } 
        $widgets = tdomf_get_widgets_form($form_id->form_id);
      if(!empty($widgets)) { ?>
          
== Widgets for Form ==

<?php foreach($widgets as $widget) { ?>
= <?php echo $widget->widget_key; ?> =
<?php echo htmlentities($widget->widget_value); ?>


<?php } }
      } ?>
      </pre> <?php
}

 ?>

 <?php if(current_user_can('manage_options')) { ?>
 
   <div class="wrap">
  
      <h2><?php _e('TDOMF Debug', 'tdomf') ?></h2>
  
      <?php if(isset($_REQUEST['html'])) { 
               tdomfinfo_html_text(); 
            } else if(isset($_REQUEST['text'])) {
               tdomfinfo_text_display();
            } else { ?>
<br/><br/>
<a href="admin.php?page=<?php echo TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR; ?>tdomf-info.php&text"><?php _e("View as Text (useful for pasting into emails)","tdomf"); ?></a>
<br/><br/>
<a href="admin.php?page=<?php echo TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR; ?>tdomf-info.php&html"><?php _e("View as HTML raw (useful for pasting into html supported forums and emails)","tdomf"); ?></a>
<br/><br/>
            <?php tdomfinfo_html_display();
            } ?>
      
      </div>

 <?php } ?>

