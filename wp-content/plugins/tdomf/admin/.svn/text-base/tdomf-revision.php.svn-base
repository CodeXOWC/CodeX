<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

$tdomf_revision_page = TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR.'tdomf-revision.php';

if(current_user_can('manage_options')) { ?>
 
   <div class="wrap">
   <?php $edit_id = false;
         if(isset($_REQUEST['edit'])) { $edit_id = intval($_REQUEST['edit']); }
         else { ?>
             <div class="updated"><p><?php _e('No edit id set!','tdomf'); ?></p></div>
             </div> <!-- wrap -->
   <?php return; } 
         $edit = tdomf_get_edit($edit_id);
         if($edit == NULL){ ?>
             <div class="updated"><p><?php _e('Invalid edit id!','tdomf'); ?></p></div>
             </div> <!-- wrap -->
   <?php return; }
         $post_id = $edit->post_id;
         $post = @get_post($post_id);
         if($post == NULL) { ?>
             <div class="updated"><p><?php _e('Post does not exist!','tdomf'); ?></p></div>
             </div> <!-- wrap -->
         <?php return; }
                  
         $edits = tdomf_get_edits(array('post_id' => $post_id));
         
         $last_edit = tdomf_get_edits(array('post_id' => $post_id, 'limit' => 1));
         if(!empty($last_edit)) { $last_edit = $last_edit[0]; }
         else { $last_edit = false; }
         
         $last_approved_edit = tdomf_get_edits(array('post_id' => $post_id, 'limit' => 1, 'state' => 'approved' ));
         if(!empty($last_approved_edit)) { $last_approved_edit = $last_approved_edit[0]; }
         else { $last_approved_edit = false; }
         
         $first_edit = tdomf_get_edits(array('post_id' => $post_id, 'limit' => 1, 'sort' => 'ASC'));
         if(!empty($first_edit)) { $first_edit = $first_edit[0]; }
         else { $first_edit = false; }
         
         $doCompare = false;
         $left = $edit_id;
         if(isset($_REQUEST['left'])){ $left = $_REQUEST['left']; $doCompare = true; }
         $right = 'current';
         if(isset($_REQUEST['right'])){ $right = $_REQUEST['right']; $doCompare = true; }
         $fields = get_post_meta($post_id, TDOMF_KEY_FIELDS, true);
         if(!is_array($fields)) { $fields = array( 'post_content' => __('Post Content','tdomf'),
                                                   'post_title' => __('Post Title','tdomf')); }
         $customFields = get_post_meta($post_id, TDOMF_KEY_CUSTOM_FIELDS, true);
         if(!is_array($customFields)) { $customFields = array(); };
         $render = 'wp';
         if(isset($_REQUEST['render'])) { $render = $_REQUEST['render']; }
         ?>
   
         <?php if($doCompare) { ?>
             <h2><?php printf(__('TDO Mini Forms Revisions for "%s" (post id: %d)', 'tdomf'),$post->post_title,$post_id); ?></h2>
         <?php } else { ?>
             <h2><?php printf(__('TDO Mini Forms Revisions for "%s" (post id: %d, edit id: %d)', 'tdomf'),$post->post_title,$post_id,$edit_id); ?></h2>
         <?php } ?>
  
      <br class='clear' />
      
      <?php if($doCompare) {
          if($left == $right) { ?>
              <div class="updated"><p><?php _e('These edits are identical.','tdomf'); ?></p></div>
          <?php }
          if($left == 'current') { $left_revision = $post; }
          else if($left == 'first') {
            $left_revision = @get_post($first_edit->current_revision_id); 
          } else if($left == 'previous') {
            $left_revision = @get_post($edit->current_revision_id);
          } else { 
            $left_edit = tdomf_get_edit($left);
            $left_revision = @get_post($left_edit->revision_id); 
          }
          if($right == 'current') { $right_revision = $post; }
          else if($right == 'first') {
            $right_revision = @get_post($first_edit->current_revision_id); 
          } else if($right == 'previous') {
            $right_revision = @get_post($edit->current_revision_id);
          } else { 
            $right_edit = tdomf_get_edit($right);
            $right_revision = @get_post($right_edit->revision_id); 
          }
          $table_code = "";
          $identical = true;

          foreach($fields as $field => $title) {
              if($render == 'wp') {
                  if ( $content = wp_text_diff( $left_revision->$field, $right_revision->$field ) ) {
                      $identical = false;
                      $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th>\n<td><div class='pre'>\n".$content."\n</div></td></tr>";
                  } else {
                      $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th><td><div class='pre'>".htmlentities($left_revision->$field)."</div></td></tr>\n";
                  }
              } else {
                  set_include_path(get_include_path() . PATH_SEPARATOR . ABSPATH.PLUGINDIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'include' );
                  include_once "Text/Diff.php";
                  $left_field = explode("\n",$left_revision->$field);
                  $right_field = explode("\n",$right_revision->$field);
                  $diff = &new Text_Diff('auto',array($left_field, $right_field));
                  if($diff->isEmpty()) {
                      $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th><td><div class='pre'>".htmlentities($left_revision->$field)."</div></td></tr>\n";
                  } else {
                      $identical = false;
                      if($render == 'unified') {
                          include_once "Text/Diff/Renderer/unified.php";
                          $renderer = &new Text_Diff_Renderer_unified();
                          $content = htmlentities($renderer->render($diff),ENT_NOQUOTES,get_bloginfo('charset'));
                      } else if($render == 'inline') {
                          include_once "Text/Diff/Renderer/inline.php";
                          $renderer = &new Text_Diff_Renderer_inline();
                          $content = $renderer->render($diff);
                      } else if($render == 'context') {
                          include_once "Text/Diff/Renderer/context.php";
                          $renderer = &new Text_Diff_Renderer_context();
                          $content = htmlentities($renderer->render($diff),ENT_NOQUOTES,get_bloginfo('charset'));
                      } else {
                          include_once "Text/Diff/Renderer.php";
                          $renderer = &new Text_Diff_Renderer();
                          $content = htmlentities($renderer->render($diff),ENT_NOQUOTES,get_bloginfo('charset'));
                      }
                      $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th>\n<td><div class='pre'>\n".$content."\n</div></td></tr>";
                  }
              }
          }
          
          if(is_array($customFields) && !empty($customFields)) {
              foreach($customFields as $field => $title) {
                  $left_field = get_post_meta($left_revision->ID, $field, true);
                  if(is_array($left_field)) { $left_field = var_export($left_field,true); }
                  $right_field = get_post_meta($right_revision->ID, $field, true);
                  if(is_array($right_field)) { $right_field = var_export($right_field,true); }
                  if($render == 'wp') {
                      if ( $content = wp_text_diff( $left_field, $right_field ) ) {
                          $identical = false;
                          $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th>\n<td><div class='pre'>\n".$content."\n</div></td></tr>";
                      } else {
                          $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th><td><div class='pre'>".htmlentities($left_field)."</div></td></tr>\n";
                      }
                  } else {
                      set_include_path(get_include_path() . PATH_SEPARATOR . ABSPATH.PLUGINDIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'include' );
                      include_once "Text/Diff.php";
                      $left_field = explode("\n",$left_field);
                      $right_field = explode("\n",$right_field);
                      $diff = &new Text_Diff('auto',array($left_field, $right_field));
                      if($diff->isEmpty()) {
                          $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th><td><div class='pre'>".htmlentities($left_revision->$field)."</div></td></tr>\n";
                      } else {
                          $identical = false;
                          if($render == 'unified') {
                              include_once "Text/Diff/Renderer/unified.php";
                              $renderer = &new Text_Diff_Renderer_unified();
                              $content = htmlentities($renderer->render($diff),ENT_NOQUOTES,get_bloginfo('charset'));
                          } else if($render == 'inline') {
                              include_once "Text/Diff/Renderer/inline.php";
                              $renderer = &new Text_Diff_Renderer_inline();
                              $content = $renderer->render($diff);
                          } else if($render == 'context') {
                              include_once "Text/Diff/Renderer/context.php";
                              $renderer = &new Text_Diff_Renderer_context();
                              $content = htmlentities($renderer->render($diff),ENT_NOQUOTES,get_bloginfo('charset'));
                          } else {
                              include_once "Text/Diff/Renderer.php";
                              $renderer = &new Text_Diff_Renderer();
                              $content = htmlentities($renderer->render($diff),ENT_NOQUOTES,get_bloginfo('charset'));
                          }
                          $table_code .= "<tr id='revision-field-".$field."'>\n<th scope='row'>".$title."</th>\n<td><div class='pre'>\n".$content."\n</div></td></tr>";
                      }
                  }
              }
          }
          
          if(!$identical) {
              // todo Older: ... Newer: ...
              ?>
              <table class="form-table ie-fixed">
                <col class="th" />
                <?php echo $table_code; ?>
              </table> <?php
          } else { ?>
              <div class="updated"><p><?php _e('These edits are identical.','tdomf'); ?></p></div> <?php
          }
      } else {
          $post = @get_post($edit->revision_id);
          ?>
          <table class="form-table ie-fixed">
            <col class="th" />
            <tr id="revision-field-post_title">
                <th scope="row"><?php _e('Title','tdomf'); ?></th>
                <td><div class="pre"><?php echo htmlentities($post->post_title); ?></div></td>
            </tr>
            <tr id="revision-field-post_content">
                <th scope="row"><?php _e('Content','tdomf'); ?></th>
                <td><div class="pre"><?php echo htmlentities($post->post_content); ?></div></td>
            </tr>            
          </table>
      <?php } ?>

      <?php 
        if(count($edits) > 0) { ?>
            <form>
            <div class="tablenav">
            <div class="alignleft">
               <select id="render" name="render"> 
               <option value="wp" <?php if($render == 'wp') { echo 'selected'; } ?> ><?php _e('Wordpress','tdomf') ?><br/>
               <option value="default" <?php if($render == 'default') { echo 'selected'; } ?> ><?php _e('Default','tdomf') ?><br/>
               <option value="unified" <?php if($render == 'unified') { echo 'selected'; } ?> ><?php _e('Unified','tdomf') ?><br/>
               <option value="inline" <?php if($render == 'inline') { echo 'selected'; } ?> ><?php _e('Inline','tdomf') ?><br/>
               <option value="context" <?php if($render == 'context') { echo 'selected'; } ?> ><?php _e('Context','tdomf') ?><br/>
               </select>
                <input type="submit" class="button-secondary" value="<?php _e('Compare','tdomf'); ?>" />
            </div>
            </div>
            <input type='hidden' id='page' name='page' value='<?php echo $tdomf_revision_page; ?>' />
            <input type='hidden' id='edit' name='edit' value='<?php echo $edit_id; ?>' />
            <br class="clear" />
            
            <table class="widefat post-revisions" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"><?php _e('Edit ID','tdomf'); ?></th>
                    <th scope="col"><?php _e('Date Created','tdomf'); ?></th>
                    <th scope="col"><?php _e('Author','tdomf'); ?></th>
                    <th scope="col"><?php _e('Form','tdomf'); ?></th>
                    <th scope="col"><?php _e('IP','tdomf'); ?></th>
                    <?php if($last_edit->revision_id != 0) { ?>
                        <th scope="col" class="action-links"><?php _e('Actions','tdomf'); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            
            <tr>
                <th style='white-space: nowrap' scope='row'>
                    <input type='radio' name='left'  value='current' <?php if($left == 'current'){ ?> checked="checked" <?php } ?> />
                    <input type='radio' name='right' value='current' <?php if($right == 'current'){ ?> checked="checked" <?php } ?> />
                </th>
                <td></td>
                <td><?php echo mysql2date(__('d F, Y @ H:i'), $post->post_modified_gmt); ?> <?php _e('[Current]','tdomf'); ?></td>
                <td></td>
                <td></td>
                <td></td>
                 
                <td class='action-links'></td>                
            </tr>
            
            <?php $i = 1; foreach($edits as $e) { ?>

            <tr<?php $i++; if($i%2==0) { ?> class='alternate' <?php }?>>
                <th style='white-space: nowrap' scope='row'>
                    <input type='radio' name='left'  value='<?php echo $e->edit_id; ?>' <?php if($left == $e->edit_id){ ?> checked="checked" <?php } ?> />
                    <input type='radio' name='right' value='<?php echo $e->edit_id; ?>' <?php if($right == $e->edit_id){ ?> checked="checked" <?php } ?> />
                </th>
                <td><?php echo $e->edit_id; ?></td>
                <td>
                    <?php if($edit_id != $e->edit_id || $doCompare){ ?>
                        <a href="admin.php?page=<?php echo $tdomf_revision_page; ?>&edit=<?php echo $e->edit_id; ?>">
                    <?php } ?>
                    <?php echo mysql2date(__('d F, Y @ H:i'), $e->date_gmt); ?>
                    <?php if($edit_id != $e->edit_id || $doCompare){ ?>
                        </a>
                    <?php } ?>
                    <?php if($e->state == 'unapproved') {
                            _e(' [Pending]',"tdomf");
                          } else if($e->state == 'spam') {
                            _e(' [Spam]',"tdomf");
                          } ?>
                </td>
                <td>
                <?php 
                 $name = __("N/A","tdomf");
                if(isset($e->data[TDOMF_KEY_NAME])) {
                   $name = $ledit->data[TDOMF_KEY_NAME];
                }
                $email = __("N/A","tdomf");
                if(isset($e->data[TDOMF_KEY_EMAIL])) {
                   $email = $e->data[TDOMF_KEY_EMAIL];
                }
                  
                if($e->user_id != 0) { ?>
                 <!-- <a href="user-edit.php?user_id=<?php echo $e->user_id;?>" class="edit"> -->
                 <a href="<?php tdomf_get_mod_posts_url(array('user_id' => $e->user_id, 'echo' => true)); ?>">
                 <?php $u = get_userdata($e->user_id);
                       echo $u->user_login; ?></a>
                 <?php } else if(!empty($name) && !empty($email)) {
                       echo $name." (".$email.")";
                       } else if(!empty($name)) {
                   echo $name;
                 } else if(!empty($email)) {
                   echo $email;
                 } else {
                   _e("N/A","tdomf");
                 } ?>
                </td>
                <td>
                <?php if(tdomf_form_exists($e->form_id) != false) {
                 #$form_edit_url = "admin.php?page=tdomf_show_form_options_menu&form=$e->form_id";
                 $form_edit_url = tdomf_get_mod_posts_url(array('form_id' => $e->form_id));
                 $form_name = tdomf_get_option_form(TDOMF_OPTION_NAME,$e->form_id);
                 echo '<a href="'.$form_edit_url.'">'.sprintf(__('Form #%d: %s','tdomf'),$e->form_id,$form_name).'</a>';
                } ?>
                </td>
                <td>
                <a href="<?php tdomf_get_mod_posts_url(array('ip' => $e->ip, 'echo' => true)); ?>">
                    <?php echo $e->ip; ?>
                </a>
                </td>
                 
                <td class='action-links'>
                <?php if($e->revision_id != 0) { ?>
                        <a href="admin.php?page=<?php echo $tdomf_revision_page; ?>&edit=<?php echo $e->edit_id; ?>&right=previous"><?php _e('Diff','tdomf'); ?></a>
                    <?php if($e->state == 'approved' && $e->edit_id == $last_edit->edit_id) { ?>
                        | <a href="<?php tdomf_get_mod_posts_url(array('echo'=> true, 'action' => 'revert_edit', 'edit_id' => $e->edit_id, 'nonce' => 'tdomf-revert_edit_' . $e->edit_id)) ?>"><?php _e('Rollback','tdomf'); ?></a>
                    <?php } else if($e->state == 'unapproved' || $e->state == 'spam') {?>
                        | <a href="<?php tdomf_get_mod_posts_url(array('echo'=> true, 'action' => 'delete_edit', 'edit_id' => $e->edit_id, 'nonce' => 'tdomf-delete_edit_' . $e->edit_id)) ?>"><?php _e('Delete','tdomf'); ?></a> |
                        <a href="<?php tdomf_get_mod_posts_url(array('echo'=> true, 'action' => 'approve_edit', 'edit_id' => $e->edit_id, 'nonce' => 'tdomf-approve_edit_' . $e->edit_id)) ?>"><?php _e('Approve','tdomf'); ?></a>
                    <?php } ?>       
                    <?php if(get_option(TDOMF_OPTION_SPAM)) { ?> | <?php
                        if($e->state == 'spam') { ?>
                            <a href="<?php tdomf_get_mod_posts_url(array('echo'=> true, 'action' => 'hamit_edit', 'edit_id' => $e->edit_id, 'nonce' => 'tdomf-hamit_edit_' . $e->edit_id)) ?>" title="<?php echo htmlentities(__('Flag contributation as not being spam','tdomf')); ?>" ><?php _e('Not Spam','tdomf'); ?>
                        <?php } else { ?>
                            <a href="<?php tdomf_get_mod_posts_url(array('echo'=> true, 'action' => 'spamit_edit', 'edit_id' => $e->edit_id, 'nonce' => 'tdomf-spamit_edit_' . $e->edit_id)) ?>" title="<?php echo htmlentities(__('Flag contributation as being spam','tdomf')); ?>" onclick="if ( confirm('<?php echo js_escape(__("You are about to flag this contribution as spam\n \'Cancel\' to stop, \'OK\' to delete.",'tdomf')); ?>') ) { return true;}return false;"><?php _e('Spam','tdomf');  ?></a>
                        <?php } 
                         }
                    } ?>
                </td>                
            </tr>
            <?php
            } ?>
            
            <tr>
                <th style='white-space: nowrap' scope='row'>
                    <input type='radio' name='left'  value='first' <?php if($left == 'first'){ ?> checked="checked" <?php } ?> />
                    <input type='radio' name='right' value='first' <?php if($right == 'first'){ ?> checked="checked" <?php } ?> />
                </th>
                <td></td>
                <td><?php echo mysql2date(__('d F, Y @ H:i'), $post->post_date_gmt); ?> <?php _e('[Original]','tdomf'); ?></td>
                <td></td>
                <td></td>
                <td></td>
                 
                <td class='action-links'></td>                
            </tr>
            
            <?php echo "</tbody></table></form>\n";
        } ?>
        <br class="clear" />
      
   </div>

 <?php } ?>

