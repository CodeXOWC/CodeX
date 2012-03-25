<?php
/*
Name: "Append to Content"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: Add to post content
Version: 1
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

  /** 
   * Append to Content Widget
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 2 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetAppend extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetAppend() {
          $this->enablePost();
          $this->enableControl(true,500, 520);
          $this->setDisplayName(__('Append to Post Content','tdomf')); 
          // include a seperator because the first instances does not use an
          // index
          $this->setInternalName('append','-');
          $this->setOptionKey('tdomf_append_widget','_');
          $this->enableMultipleInstances(true,__('Append to Post Content %d','tdomf'),'tdomf_append_widget_count');
          $this->start();
      }

      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return array
       */
      function getOptions($form_id,$postfix='') {
          $defaults = array( 'message' =>  "");
          $options = TDOMF_Widget::getOptions($form_id,$postfix); 
          $options = wp_parse_args($options, $defaults);
          return $options;
      }   
      
      /**
       * Configuration panel for widget
       * 
       * @access public
       */      
      function control($options,$form_id,$postfixOptionKey='',$postfixInternalName='') {
          
          // Store settings for this widget
          //
          if ( $_POST[$this->internalName.$postfixInternalName.'-submit'] ) {
                 $newoptions['message'] = $_POST["append-message$postfixOptionKey"];
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id,$postfixOptionKey);
          }

          // Display control panel for this widget
          //
          extract($options);
        ?>
<div>

<?php $this->controlCommon($options,$postfixOptionKey); ?>

<br/>

<p><?php _e("This Widget allows you to add text to the created post. Widgets are processed top-down so this widget can be used to add seperators between other widget contexts. It uses the Form Hacker backend so supports all Form Hacker macros. It also supports PHP code - which can be used to create powerful post-submission processing. However, this widget will be ran <i>before</i> the submission is automatically published.","tdomf"); ?></p>

<br/><br/>

<label for="append-message<?php echo $postfixOptionKey; ?>" ><?php _e("Message to append to post content:","tdomf"); ?><br/>
</label>
<textarea cols="50" rows="6" id="append-message<?php echo $postfixOptionKey; ?>" name="append-message<?php echo $postfixOptionKey; ?>" ><?php echo htmlentities($options['message'],ENT_NOQUOTES,get_bloginfo('charset')); ?></textarea>

</div>
<?php
      }
      
    /** 
     * What the widget does when the submission is being posted
     * 
     * @return Mixed 
     * @access public
     */        
    function post($args,$options,$postfix='') {
        extract($args);
        $message = tdomf_prepare_string($options['message'], $tdomf_form_id, $mode, $post_ID, "", $args); 
        $post = wp_get_single_post($post_ID, ARRAY_A);
        if(!empty($post['post_content'])) {
            $post = add_magic_quotes($post);
        }
        $postdata = array (
            "ID"                      => $post_ID,
            "post_content"            => $post['post_content'].$message,
            );
        sanitize_post($postdata,"db");
        wp_update_post($postdata);
        return NULL;
    }         
      
  }

  // Create and start the widget
  //
  global $tdomf_widget_append;
  $tdomf_widget_append = new TDOMF_WidgetAppend();

?>
