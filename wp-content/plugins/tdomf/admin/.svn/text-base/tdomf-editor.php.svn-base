<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

// Display editor page
//
function tdomf_show_editor_menu() {
  ?>

  <div class="wrap">
     <form name="theme" action="" method="post">

		<?php _e('Select type of files to edit:','tdomf'); ?>

		<select name="type" id="type">

	<option value="stylesheet" ><?php _e('Form Style Sheet','tdomf'); ?></option>
	<option value="widgets"><?php _e('Widgets','tdomf'); ?></option>
	<option value="core"><?php _e('TDOMF Core Files','tdomf'); ?></option>

	    </select>

 		<input type="submit" name="Submit" value="<?php _e('Select &raquo;','tdomf'); ?>" class="button" />
     </form>
 </div>

  <div class="wrap">

    <h2>Editing <code>xyz</code></h2>

    <div id="templateside">
		<h3><strong>Widget</strong> files</h3>
        <ul>
           <li><a href="link">xyz</a></li>
		</ul>
    </div>

    <form name="codefile" id="codefile" action="" method="post">

	  <!-- TODO: wponce -->

	   <div>
	   <textarea cols="70" rows="25" name="newcontent" id="newcontent" tabindex="1">

	   <!-- TODO: file -->

	   </textarea>

	   <input type="hidden" name="action" value="update" />
	   <input type="hidden" name="file" value="TODO" />
	   <input type="hidden" name="type" value="TODO" />

	   </div>

	   <p class="submit">
	   <input type='submit' name='submit' value='	Update File &raquo;' tabindex='2' /></p>
	   </form>
	   <div class="clear"> &nbsp; </div>
   </div>

<?php
}
?>
