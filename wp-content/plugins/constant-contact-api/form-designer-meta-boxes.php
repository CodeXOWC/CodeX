<?php
include_once('form-designer-functions.php');


function cc_form_meta_box_presetoptions($post, $metabox=array()) {
	$form = $metabox['args'][0];
?>
	<p class="block">
	<label for="presets" class="howto"><span>Form Design</span>
		<select id="presets" name="presets">
			<option value="">Select a Design&hellip;</option>	
			<option value="Army"<?php check_select($form,'presets', 'Army'); ?>>Army</option>
			<option value="Apple"<?php check_select($form,'presets', 'Apple'); ?>>Apple, Inc.</option>
			<option value="Jazz"<?php check_select($form,'presets', 'Jazz'); ?>>Jazz Blue</option>
			<option value="Impact"<?php check_select($form,'presets', 'Impact'); ?>>Red IMPACT</option>
			<option value="Barbie"<?php check_select($form,'presets', 'Barbie'); ?>>Barbie World</option>
			<option value="NYC"<?php check_select($form,'presets', 'NYC'); ?>>New York Taxi</option>
		</select>
	</label>
	</p>
	<div class="block">
		<label for="pupt" class="checkbox howto"><input type="checkbox" name="pupt" id="pupt" <?php check_checkbox($form, 'pupt', 'yes', false); ?> /> <span>Update label &amp; placeholder text.</span>
		<p class="description">When changing Form Designs, update form text with presets. <strong>Will overwrite text modifications.</strong></p>
		</label>
	</div>
	
	<div>
	<label class="howto block"><span>SafeSubscribe</span></label>
		<ul>
				  	<li><label for="safesubscribelight"><input type="radio" <?php check_radio($form,'safesubscribe', 'light', true); ?> name="safesubscribe" id="safesubscribelight" /> <img src="<?php echo CC_FORM_GEN_PATH; ?>images/safesubscribe-light.gif" alt="SafeSubscribe Light" width="168" height="14" id="safesubscribelightimg" title="Gray"/></label></li>
				  	<li><label for="safesubscribedark"><input type="radio" <?php check_radio($form,'safesubscribe', 'dark'); ?> name="safesubscribe" id="safesubscribedark" /> <img src="<?php echo CC_FORM_GEN_PATH; ?>images/safesubscribe-dark.gif" alt="SafeSubscribe Dark" width="168" height="14" id="safesubscribedarkimg" title="White"/></label></li>
				  	<li><label for="safesubscribeblack"><input type="radio" <?php check_radio($form,'safesubscribe', 'black'); ?> name="safesubscribe" id="safesubscribeblack" /> <img src="<?php echo CC_FORM_GEN_PATH; ?>images/safesubscribe-black.gif" alt="SafeSubscribe Black" width="168" height="14" id="safesubscribeblackimg" title="Black"/></label></li>
				  	<li><label for="safesubscribeno"><input type="radio" <?php check_radio($form,'safesubscribe', 'no'); ?> name="safesubscribe" id="safesubscribeno" /> Do Not Display</label></li>
	</div>
<?php 
}

function cc_form_meta_box_formfields_select($post, $metabox=array()) {
	
	$form = $metabox['args'][0];
	$checkedArray = !empty($form['formfields']) ? $form['formfields'] : array();
	$checkedArray['email_address'] = 'email_address';
?>
<div class="posttypediv">
	<ul id="formfields-select-tabs" class="formfields-select-tabs add-menu-item-tabs">
		<li class="tabs"><a href="#formfields-select-most" class="nav-tab-link">Most Used</a></li>
		<li><a href="#formfields-select-all" class="nav-tab-link">Other Fields</a></li>
	</ul>
	<div id="formfields-select-most" class="tabs-panel tabs-panel-active">
		<ul id="formfieldslist-most" class="categorychecklist form-no-clear">
		<?php
			$formfields = array();
			$formfields[] = array('email_address', 'Email Address', true);
			$formfields[] = array('intro', 'Form Text', true);
			$formfields[] = array('first_name', 'First Name', true);
			$formfields[] = array('last_name', 'Last Name', true);
			$formfields[] = array('Go', 'Submit', true);
			$formfields[] = array('home_number', 'Home Number', false);
			$formfields[] = array('work_number', 'Work Number', false);
			echo make_formfield_list_items($formfields, $checkedArray);
		?>
		</ul>
	</div>
	<div id="formfields-select-all" class="tabs-panel">
		<ul id="formfieldslist-all" class="categorychecklist form-no-clear">
		<?php
			$formfields = array();
			$formfields[] = array('middle_name', 'Middle Name', false);
			$formfields[] = array('company_name', 'Company Name', false);
			$formfields[] = array('job_title', 'Job Title', false);
			$formfields[] = array('address_line_1', 'Address Line 1', false);
			$formfields[] = array('address_line_2', 'Address Line 2', false);
			$formfields[] = array('address_line_3', 'Address Line 3', false);
			$formfields[] = array('city_name', 'City Name', false);
			$formfields[] = array('state_code', 'State Code', false);
			$formfields[] = array('state_name', 'State Name', false);
			$formfields[] = array('country_code', 'Country Code', false);
			$formfields[] = array('zip_code', 'ZIP Code', false);
			$formfields[] = array('sub_zip_code', 'Sub ZIP Code', false);
			$formfields[] = array('custom_field_1', 'Custom Field 1', false);
			$formfields[] = array('custom_field_2', 'Custom Field 2', false);
			$formfields[] = array('custom_field_3', 'Custom Field 3', false);
			$formfields[] = array('custom_field_4', 'Custom Field 4', false);
			$formfields[] = array('custom_field_5', 'Custom Field 5', false);
			$formfields[] = array('custom_field_6', 'Custom Field 6', false);
			$formfields[] = array('custom_field_7', 'Custom Field 7', false);
			$formfields[] = array('custom_field_8', 'Custom Field 8', false);
			$formfields[] = array('custom_field_9', 'Custom Field 9', false);
			$formfields[] = array('custom_field_10', 'Custom Field 10', false);
			$formfields[] = array('custom_field_11', 'Custom Field 11', false);
			$formfields[] = array('custom_field_12', 'Custom Field 12', false);
			$formfields[] = array('custom_field_13', 'Custom Field 13', false);
			$formfields[] = array('custom_field_14', 'Custom Field 14', false);
			$formfields[] = array('custom_field_15', 'Custom Field 15', false);
			echo make_formfield_list_items($formfields, $checkedArray);
		?>
		</ul>
	</div>
</div>
<?php
}

function cc_form_meta_box_formfields($_form_object) {
	?>
		<ul class="menu" id="menu-to-edit">
		<?php
			
			$formfields[] = make_formfield($_form_object, '', 'intro', 'Form Text', true, '', 'textarea', '&lt;h3&gt;Sign up for Email Newsletters&lt;/h3&gt;');
			$formfields[] = make_formfield($_form_object, '', 'email_address', 'Email Address', true, 'example@tryme.com');
			$formfields[] = make_formfield($_form_object, '', 'first_name', 'First Name', true);
			$formfields[] = make_formfield($_form_object, '', 'last_name', 'Last Name', true);
			$formfields[] = make_formfield($_form_object, '', 'Go', 'Submit', true, 'Go', 'submit');
			
			$formfields[] = make_formfield($_form_object, 'more', 'middle_name', 'Middle Name', false);
			$formfields[] = make_formfield($_form_object, 'more', 'company_name', 'Company Name', false);
			$formfields[] = make_formfield($_form_object, 'more', 'job_title', 'Job Title', false);
			$formfields[] = make_formfield($_form_object, 'more', 'home_number', 'Home Number', false);
			$formfields[] = make_formfield($_form_object, 'more', 'work_number', 'Work Number', false);
			$formfields[] = make_formfield($_form_object, 'more', 'address_line_1', 'Address Line 1', false);
			$formfields[] = make_formfield($_form_object, 'more', 'address_line_2', 'Address Line 2', false);
			$formfields[] = make_formfield($_form_object, 'more', 'address_line_3', 'Address Line 3', false);
			$formfields[] = make_formfield($_form_object, 'more', 'city_name', 'City Name', false);
			$formfields[] = make_formfield($_form_object, 'more', 'state_code', 'State Code', false);
			$formfields[] = make_formfield($_form_object, 'more', 'state_name', 'State Name', false);
			$formfields[] = make_formfield($_form_object, 'more', 'country_code', 'Country Code', false);
			$formfields[] = make_formfield($_form_object, 'more', 'zip_code', 'ZIP Code', false);
			$formfields[] = make_formfield($_form_object, 'more', 'sub_zip_code', 'Sub ZIP Code', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_1', 'Custom Field 1', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_2', 'Custom Field 2', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_3', 'Custom Field 3', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_4', 'Custom Field 4', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_5', 'Custom Field 5', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_6', 'Custom Field 6', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_7', 'Custom Field 7', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_8', 'Custom Field 8', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_9', 'Custom Field 9', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_10', 'Custom Field 10', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_11', 'Custom Field 11', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_12', 'Custom Field 12', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_13', 'Custom Field 13', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_14', 'Custom Field 14', false);
			$formfields[] = make_formfield($_form_object, 'more', 'custom_field_15', 'Custom Field 15', false);
			
			foreach($formfields as $formfield) { echo $formfield; }
		?>	
	</ul>	
<?php
}

function cc_form_meta_box_backgroundoptions($post, $metabox=array()) {
	$form = $metabox['args'][0];
	?>
				<input type="hidden" name="backgroundgradienturl" id="backgroundgradienturl" value="" />
				<label for="backgroundtype" class="howto hide"><span>Background Type:</span></label>
					<div class="tabs-panel tabs-panel-active clear">
						<ul class="categorychecklist">
							<li><label for="backgroundgradient" class="menu-item-title backgroundtype"><input type="radio" class="menu-item-checkbox" name="backgroundtype" id="backgroundgradient" <?php check_radio($form,'backgroundtype', 'gradient', true); ?> /> <span>Gradient</span></label></li>
							<li><label for="backgroundsolid" class="backgroundtype"><input type="radio" class="menu-item-checkbox" <?php check_radio($form,'backgroundtype', 'solid'); ?>  name="backgroundtype" id="backgroundsolid" /> <span>Solid Color</span></label></li>
							<li><label for="backgroundpattern" class="backgroundtype"><input type="radio" class="menu-item-checkbox" <?php check_radio($form,'backgroundtype', 'pattern'); ?> name="backgroundtype" id="backgroundpattern" /> <span>Image Pattern</span></label></li>
							<li><label for="backgroundurl" class="backgroundtype"><input type="radio" class="menu-item-checkbox" <?php check_radio($form,'backgroundtype', 'url'); ?> name="backgroundtype" id="backgroundurl" /> <span>URL (External Image)</span></label></li>
						</ul>
					</div>
				
				<div id="gradtypeli">
					<label class="howto" for="gradtype"><span>Gradient Type:</span>
						<select id="gradtype" name="gradtype">
						  <option <?php check_select($form,'gradtype', 'vertical'); ?>>Vertical</option>
						  <option <?php check_select($form,'gradtype', 'horizontal'); ?>>Horizontal</option>
						</select>
					</label>
					<input type="hidden" id="gradwidth" name="gradwidth" value="1" />
				</div>
				<div id="gradheightli">
						<label class="howto" for="gradheight"><span>Gradient Height:</span>
							<select name="gradheight" id="gradheight">
							  <option <?php check_select($form, 'gradheight', '10',false); ?>>10 px</option>
							  <option <?php check_select($form, 'gradheight', '20',false); ?>>20 px</option>
							  <option <?php check_select($form, 'gradheight', '30',false); ?>>30 px</option>
							  <option <?php check_select($form, 'gradheight', '40',false); ?>>40 px</option>
							  <option <?php check_select($form, 'gradheight', '50',false); ?>>50 px</option>
							  <option <?php check_select($form, 'gradheight', '60',false); ?>>60 px</option>
							  <option <?php check_select($form, 'gradheight', '70',false); ?>>70 px</option>
							  <option <?php check_select($form, 'gradheight', '80',false); ?>>80 px</option>
							  <option <?php check_select($form, 'gradheight', '90',false); ?>>90 px</option>
							  <option <?php check_select($form, 'gradheight', '100',true); ?>>100 px</option>
							  <option <?php check_select($form, 'gradheight', '110',false); ?>>110 px</option>
							  <option <?php check_select($form, 'gradheight', '120',false); ?>>120 px</option>
							  <option <?php check_select($form, 'gradheight', '130',false); ?>>130 px</option>
							  <option <?php check_select($form, 'gradheight', '140',false); ?>>140 px</option>
							  <option <?php check_select($form, 'gradheight', '150',false); ?>>150 px</option>
							  <option <?php check_select($form, 'gradheight', '175',false); ?>>175 px</option>
							  <option <?php check_select($form, 'gradheight', '200',false); ?>>200 px</option>
							  <option <?php check_select($form, 'gradheight', '225',false); ?>>225 px</option>
							  <option <?php check_select($form, 'gradheight', '250',false); ?>>250 px</option>
							  <option <?php check_select($form, 'gradheight', '275',false); ?>>275 px</option>
							  <option <?php check_select($form, 'gradheight', '300',false); ?>>300 px</option>
							  <option <?php check_select($form, 'gradheight', '350',false); ?>>350 px</option>
							  <option <?php check_select($form, 'gradheight', '400',false); ?>>400 px</option>
							  <option <?php check_select($form, 'gradheight', '450',false); ?>>450 px</option>
							  <option <?php check_select($form, 'gradheight', '500',false); ?>>500 px</option>
							</select>
						</label>
				</div>
				
				<div class="block" id="bgtop">
						<label for="color6" class="howto inline"><span>Top Color:</span></label>
						<input type="text" id="color6" name="color6" class="colorwell" value="<?php input_value($form, 'color6', '#ad0c0c'); ?>" />
				</div>
				<div class="block" id="bgbottom">
						<label class="howto inline"><span>Bottom Color:</span></label>
						<input type="text" id="color2" name="color2" class="colorwell" value="<?php input_value($form, 'color2', '#000001'); ?>" />
				</div>
				<div class="form-item" id="bgurl">
						<p class="link-to-original">For inspiration, check out <a href="http://www.colourlovers.com/patterns/most-loved/all-time/meta">Colourlovers Patterns</a>.</p>
						<p><label for="bgimage"><span class="howto">Background Image:</span>
						<input type="text" class="code widefat" id="bgimage" name="bgimage" value="<?php input_value($form, 'bgimage', 'http://colourlovers.com.s3.amazonaws.com/images/patterns/90/90096.png'); ?>" />
						</label></p>
						
						<p><label class="howto" for="bgrepeat"><span>Background Repeat:</span>
							<select name="bgrepeat" id="bgrepeat">
								<option <?php check_select($form,'bgrepeat', 'repeat',true); ?> value="repeat">Repeat</option>
								<option <?php check_select($form,'bgrepeat', 'no-repeat'); ?> value="no-repeat">No Repeat</option>
								<option <?php check_select($form,'bgrepeat', 'repeat-x'); ?> value="repeat-x">Repeat-X (Horizontal)</option>
								<option <?php check_select($form,'bgrepeat', 'repeat-y'); ?> value="repeat-y">Repeat-Y (Vertical)</option>
							</select>
						</label></p>
						<!-- <p class="howto">Choose the background alignment: Horizontal / Vertical</p> -->
						<p><label class="howto" for="bgpos"><span>Background Position:</span>
							<select name="bgpos" id="bgpos">
								<option <?php check_select($form,'bgpos', 'left top',true); ?> value="left top">Left/Top</option>
								<option <?php check_select($form,'bgpos', 'center top'); ?> value="center top">Center/Top</option>
								<option <?php check_select($form,'bgpos', 'right top'); ?> value="right top">Right/Top</option>
								<option <?php check_select($form,'bgpos', 'left center'); ?> value="left center">Left/Center</option>
								<option <?php check_select($form,'bgpos', 'center center'); ?> value="center center">Center/Center</option>
								<option <?php check_select($form,'bgpos', 'right center'); ?> value="right center">Right/Center</option>
								<option <?php check_select($form,'bgpos', 'left bottom'); ?> value="left bottom">Left/Bottom</option>
								<option <?php check_select($form,'bgpos', 'center bottom'); ?> value="center bottom">Center/Bottom</option>
								<option <?php check_select($form,'bgpos', 'right bottom'); ?> value="right bottom">Right/Bottom</option>
							</select>
						</label></p>
					</div>
					<div class="form-item block" id="bgpattern">
						<label class="howto">Background Image Pattern:</label>
						<p class="description">Click a pattern to apply. Patterns by <a href="http://www.squidfingers.com/patterns/" rel="nofollow">Squidfingers</a>.</p>
						<input type="hidden" id="patternurl" name="patternurl" value="<?php input_value($form, 'patternurl', '');?>" />
						<ul id="patternList">
							<li title="patterns/pattern_001.gif"></li>
							<li title="patterns/pattern_002.gif"></li>
							<li title="patterns/pattern_003.gif"></li>
							<li title="patterns/pattern_004.gif"></li>
							<li title="patterns/pattern_005.gif"></li>
							<li title="patterns/pattern_006.gif"></li>
							<li title="patterns/pattern_007.gif"></li>
							<li title="patterns/pattern_008.gif"></li>
							<li title="patterns/pattern_009.gif"></li>
							<li title="patterns/pattern_010.gif"></li>
							<li title="patterns/pattern_011.gif"></li>
							<li title="patterns/pattern_012.gif"></li>
							<li title="patterns/pattern_013.gif"></li>
							<li title="patterns/pattern_014.gif"></li>
							<li title="patterns/pattern_015.gif"></li>
							<li title="patterns/pattern_016.gif"></li>
							<li title="patterns/pattern_017.gif"></li>
							<li title="patterns/pattern_018.gif"></li>
							<li title="patterns/pattern_019.gif"></li>
							<li title="patterns/pattern_020.gif"></li>
							<li title="patterns/pattern_021.gif"></li>
							<li title="patterns/pattern_022.gif"></li>
							<li title="patterns/pattern_023.gif"></li>
							<li title="patterns/pattern_024.gif"></li>
							<li title="patterns/pattern_025.gif"></li>
							<li title="patterns/pattern_026.gif"></li>
							<li title="patterns/pattern_027.gif"></li>
							<li title="patterns/pattern_028.gif"></li>
							<li title="patterns/pattern_029.gif"></li>
							<li title="patterns/pattern_030.gif"></li>
							<li title="patterns/pattern_031.gif"></li>
							<li title="patterns/pattern_032.gif"></li>
							<li title="patterns/pattern_033.gif"></li>
							<li title="patterns/pattern_034.gif"></li>
							<li title="patterns/pattern_035.gif"></li>
							<li title="patterns/pattern_036.gif"></li>
							<li title="patterns/pattern_037.gif"></li>
							<li title="patterns/pattern_038.gif"></li>
							<li title="patterns/pattern_039.gif"></li>
							<li title="patterns/pattern_040.gif"></li>
							<li title="patterns/pattern_041.gif"></li>
							<li title="patterns/pattern_042.gif"></li>
							<li title="patterns/pattern_043.gif"></li>
							<li title="patterns/pattern_044.gif"></li>
							<li title="patterns/pattern_045.gif"></li>
							<li title="patterns/pattern_046.gif"></li>
							<li title="patterns/pattern_047.gif"></li>
							<li title="patterns/pattern_048.gif"></li>
							<li title="patterns/pattern_049.gif"></li>
							<li title="patterns/pattern_050.gif"></li>
							<li title="patterns/pattern_051.gif"></li>
							<li title="patterns/pattern_052.gif"></li>
							<li title="patterns/pattern_053.gif"></li>
							<li title="patterns/pattern_054.gif"></li>
							<li title="patterns/pattern_055.gif"></li>
							<li title="patterns/pattern_056.gif"></li>
							<li title="patterns/pattern_057.gif"></li>
							<li title="patterns/pattern_058.gif"></li>
							<li title="patterns/pattern_059.gif"></li>
							<li title="patterns/pattern_060.gif"></li>
							<li title="patterns/pattern_061.gif"></li>
							<li title="patterns/pattern_062.gif"></li>
							<li title="patterns/pattern_063.gif"></li>
							<li title="patterns/pattern_064.gif"></li>
							<li title="patterns/pattern_065.gif"></li>
							<li title="patterns/pattern_066.gif"></li>
							<li title="patterns/pattern_067.gif"></li>
							<li title="patterns/pattern_068.gif"></li>
							<li title="patterns/pattern_069.gif"></li>
							<li title="patterns/pattern_070.gif"></li>
							<li title="patterns/pattern_071.gif"></li>
							<li title="patterns/pattern_072.gif"></li>
							<li title="patterns/pattern_073.gif"></li>
							<li title="patterns/pattern_074.gif"></li>
							<li title="patterns/pattern_075.gif"></li>
							<li title="patterns/pattern_076.gif"></li>
							<li title="patterns/pattern_077.gif"></li>
							<li title="patterns/pattern_078.gif"></li>
							<li title="patterns/pattern_079.gif"></li>
							<li title="patterns/pattern_080.gif"></li>
							<li title="patterns/pattern_081.gif"></li>
							<li title="patterns/pattern_082.gif"></li>
							<li title="patterns/pattern_083.gif"></li>
							<li title="patterns/pattern_084.gif"></li>
							<li title="patterns/pattern_085.gif"></li>
							<li title="patterns/pattern_086.gif"></li>
							<li title="patterns/pattern_087.gif"></li>
<!-- 						   if)" title="patterns/pattern_088.gif"></li> -->
							<li title="patterns/pattern_089.gif"></li>
							<li title="patterns/pattern_090.gif"></li>
							<li title="patterns/pattern_091.gif"></li>
							<li title="patterns/pattern_092.gif"></li>
							<li title="patterns/pattern_093.gif"></li>
							<li title="patterns/pattern_094.gif"></li>
							<li title="patterns/pattern_095.gif"></li>
							<li title="patterns/pattern_096.gif"></li>
							<li title="patterns/pattern_097.gif"></li>
							<li title="patterns/pattern_098.gif"></li>
							<li title="patterns/pattern_099.gif"></li>
							<li title="patterns/pattern_100.gif"></li>
							<li title="patterns/pattern_101.gif"></li>
							<li title="patterns/pattern_102.gif"></li>
							<li title="patterns/pattern_103.gif"></li>
							<li title="patterns/pattern_104.gif"></li>
							<li title="patterns/pattern_105.gif"></li>
							<li title="patterns/pattern_106.gif"></li>
							<li title="patterns/pattern_107.gif"></li>
							<li title="patterns/pattern_108.gif"></li>
							<li title="patterns/pattern_109.gif"></li>
							<li title="patterns/pattern_110.gif"></li>
							<li title="patterns/pattern_111.gif"></li>
							<li title="patterns/pattern_112.gif"></li>
							<li title="patterns/pattern_113.gif"></li>
							<li title="patterns/pattern_114.gif"></li>
							<li title="patterns/pattern_115.gif"></li>
							<li title="patterns/pattern_116.gif"></li>
							<li title="patterns/pattern_117.gif"></li>
							<li title="patterns/pattern_118.gif"></li>
							<li title="patterns/pattern_119.gif"></li>
							<li title="patterns/pattern_120.gif"></li>
							<li title="patterns/pattern_121.gif"></li>
							<li title="patterns/pattern_122.gif"></li>
							<li title="patterns/pattern_123.gif"></li>
							<li title="patterns/pattern_124.gif"></li>
							<li title="patterns/pattern_125.gif"></li>
							<li title="patterns/pattern_126.gif"></li>
							<li title="patterns/pattern_127.gif"></li>
							<li title="patterns/pattern_128.gif"></li>
							<li title="patterns/pattern_129.gif"></li>
							<li title="patterns/pattern_130.gif"></li>
							<li title="patterns/pattern_131.gif"></li>
							<li title="patterns/pattern_132.gif"></li>
							<li title="patterns/pattern_133.gif"></li>
							<li title="patterns/pattern_134.gif"></li>
							<li title="patterns/pattern_135.gif"></li>
							<li title="patterns/pattern_136.gif"></li>
							<li title="patterns/pattern_137.gif"></li>
							<li title="patterns/pattern_138.gif"></li>
							<li title="patterns/pattern_139.gif"></li>
							<li title="patterns/pattern_140.gif"></li>
							<li title="patterns/pattern_141.gif"></li>
							<li title="patterns/pattern_142.gif"></li>
							<li title="patterns/pattern_143.gif"></li>
							<li title="patterns/pattern_144.gif"></li>
							<li title="patterns/pattern_145.gif"></li>
							<li title="patterns/pattern_146.gif"></li>
							<li title="patterns/pattern_147.gif"></li>
							<li title="patterns/pattern_148.gif"></li>
							<li title="patterns/pattern_149.gif"></li>
							<li title="patterns/pattern_150.gif"></li>
							<li title="patterns/pattern_151.gif"></li>
							<li title="patterns/pattern_152.gif"></li>
							<li title="patterns/pattern_153.gif"></li>
							<li title="patterns/pattern_154.gif"></li>
							<li title="patterns/pattern_155.gif"></li>
							<li title="patterns/pattern_156.gif"></li>
							<li title="patterns/pattern_157.gif"></li>
							<li title="patterns/pattern_158.gif"></li>			
					</ul>
				</div>
<?php
}

function cc_form_meta_box_border($post, $metabox=array()) {
	$form = $metabox['args'][0];
?>
	<p id="borderstyle">
		<label for="borderstyle" class="howto"><span>Border Style</span>
			<select id="borderstyle" name="borderstyle">
				<option <?php check_select($form,'borderstyle', 'solid',true); ?>>Solid</option>
				<option <?php check_select($form,'borderstyle', 'dotted'); ?>>Dotted</option>
				<option <?php check_select($form,'borderstyle', 'dashed'); ?>>Dashed</option>
				<option <?php check_select($form,'borderstyle', 'double'); ?>>Double</option>
				<option <?php check_select($form,'borderstyle', 'groove'); ?>>Groove</option>
				<option <?php check_select($form,'borderstyle', 'ridge'); ?>>Ridge</option>
				<option <?php check_select($form,'borderstyle', 'inset'); ?>>Inset</option>
				<option <?php check_select($form,'borderstyle', 'outset'); ?>>Outset</option>
			</select>
		</label>
		<input type="hidden" id="bordertype" value="" />
	</p>
	<div id="bordercoloritem">
		<label for="bordercolor" class="howto inline"><span>Border Color:</span></label>
		<div class="input"><input type="text" id="bordercolor" name="bordercolor" class="colorwell" value="<?php input_value($form, 'bordercolor', '#000000'); ?>" style="color:#fff;" /></div>
	</div>
	<div id="borderitem">
		<label for="borderwidth" class="howto block">Border Width
				<select id="borderwidth" name="borderwidth">
				  <option <?php check_select($form, 'borderwidth', '1',false); ?>>1 px</option>
				  <option <?php check_select($form, 'borderwidth', '2',false); ?>>2 px</option>
				  <option <?php check_select($form, 'borderwidth', '3',false); ?>>3 px</option>
				  <option <?php check_select($form, 'borderwidth', '4',true); ?>>4 px</option>
				  <option <?php check_select($form, 'borderwidth', '5',false); ?>>5 px</option>
				  <option <?php check_select($form, 'borderwidth', '6',false); ?>>6 px</option>
				  <option <?php check_select($form, 'borderwidth', '7',false); ?>>7 px</option>
				  <option <?php check_select($form, 'borderwidth', '8',false); ?>>8 px</option>
				  <option <?php check_select($form, 'borderwidth', '9',false); ?>>9 px</option>
				  <option <?php check_select($form, 'borderwidth', '10',false); ?>>10 px</option>
				  <option <?php check_select($form, 'borderwidth', '11',false); ?>>11 px</option>
				  <option <?php check_select($form, 'borderwidth', '12',false); ?>>12 px</option>
				  <option <?php check_select($form, 'borderwidth', '13',false); ?>>13 px</option>
				  <option <?php check_select($form, 'borderwidth', '14',false); ?>>14 px</option>
				  <option <?php check_select($form, 'borderwidth', '15',false); ?>>15 px</option>
				  <option <?php check_select($form, 'borderwidth', '16',false); ?>>16 px</option>
				  <option <?php check_select($form, 'borderwidth', '17',false); ?>>17 px</option>
				  <option <?php check_select($form, 'borderwidth', '18',false); ?>>18 px</option>
				  <option <?php check_select($form, 'borderwidth', '19',false); ?>>19 px</option>
				  <option <?php check_select($form, 'borderwidth', '20',false); ?>>20 px</option>
				  <option <?php check_select($form, 'borderwidth', '25',false); ?>>25 px</option>
				  <option <?php check_select($form, 'borderwidth', '30',false); ?>>30 px</option>
				  <option <?php check_select($form, 'borderwidth', '35',false); ?>>35 px</option>
				  <option <?php check_select($form, 'borderwidth', '40',false); ?>>40 px</option>
				  <option <?php check_select($form, 'borderwidth', '45',false); ?>>45 px</option>
				  <option <?php check_select($form, 'borderwidth', '50',false); ?>>50 px</option>
				</select>
		</label>
	</div>
	
	<div class="borderradius"><label for="borderradius" class="howto block"><span>Rounded Corner Radius*</span>
				<select id="borderradius" name="borderradius">
				  <option <?php check_select($form, 'borderradius', '1',false); ?>>1 px</option>
				  <option <?php check_select($form, 'borderradius', '2',false); ?>>2 px</option>
				  <option <?php check_select($form, 'borderradius', '3',false); ?>>3 px</option>
				  <option <?php check_select($form, 'borderradius', '4',true); ?>>4 px</option>
				  <option <?php check_select($form, 'borderradius', '5',false); ?>>5 px</option>
				  <option <?php check_select($form, 'borderradius', '6',false); ?>>6 px</option>
				  <option <?php check_select($form, 'borderradius', '7',false); ?>>7 px</option>
				  <option <?php check_select($form, 'borderradius', '8',false); ?>>8 px</option>
				  <option <?php check_select($form, 'borderradius', '9',false); ?>>9 px</option>
				  <option <?php check_select($form, 'borderradius', '10',false); ?>>10 px</option>
				  <option <?php check_select($form, 'borderradius', '11',false); ?>>11 px</option>
				  <option <?php check_select($form, 'borderradius', '12',false); ?>>12 px</option>
				  <option <?php check_select($form, 'borderradius', '13',false); ?>>13 px</option>
				  <option <?php check_select($form, 'borderradius', '14',true); ?>>14 px</option>
				  <option <?php check_select($form, 'borderradius', '15',false); ?>>15 px</option>
				  <option <?php check_select($form, 'borderradius', '16',false); ?>>16 px</option>
				  <option <?php check_select($form, 'borderradius', '17',false); ?>>17 px</option>
				  <option <?php check_select($form, 'borderradius', '18',false); ?>>18 px</option>
				  <option <?php check_select($form, 'borderradius', '19',false); ?>>19 px</option>
				  <option <?php check_select($form, 'borderradius', '20',false); ?>>20 px</option>
				  <option <?php check_select($form, 'borderradius', '25',false); ?>>25 px</option>
				  <option <?php check_select($form, 'borderradius', '30',false); ?>>30 px</option>
				  <option <?php check_select($form, 'borderradius', '35',false); ?>>35 px</option>
				  <option <?php check_select($form, 'borderradius', '40',false); ?>>40 px</option>
				  <option <?php check_select($form, 'borderradius', '45',false); ?>>45 px</option>
				  <option <?php check_select($form, 'borderradius', '50',false); ?>>50 px</option>
				  <option <?php check_select($form, 'borderradius', '50',false); ?>>50 px</option>
				  <option <?php check_select($form, 'borderradius', '75',false); ?>>75 px</option>
				  <option <?php check_select($form, 'borderradius', '100',false); ?>>100 px</option>
				</select>							 
		</label>
		<small class="asterix"><strong>* Displays as rounded in modern browsers</strong> (Firefox, Chrome, Safari), <em>but not Internet Explorer</em></small>
	</div>
<?php
}

function cc_form_meta_box_formdesign($post, $metabox=array()) {
	$form = $metabox['args'][0];
	?>		<div>
				<label for="isize" class="howto block"><span>Form Padding</span>
					<p class="description">Padding is the space between the outside of the form and the content inside the form; it's visual insulation.</p>
					<select id="paddingwidth" name="paddingwidth">
						<option<?php check_select($form,'paddingwidth', '0',false); ?> value="0">No Padding</option>
						<option<?php check_select($form,'paddingwidth', '1',false); ?> value="1">1px</option>
						<option<?php check_select($form,'paddingwidth', '2',false); ?> value="2">2px</option>
						<option<?php check_select($form,'paddingwidth', '3',false); ?> value="3">3px</option>
						<option<?php check_select($form,'paddingwidth', '4',false); ?> value="4">4px</option>
						<option<?php check_select($form,'paddingwidth', '5',false); ?> value="5">5px</option>
						<option<?php check_select($form,'paddingwidth', '6',false); ?> value="6">6px</option>
						<option<?php check_select($form,'paddingwidth', '7',false); ?> value="7">7px</option>
						<option<?php check_select($form,'paddingwidth', '8',false); ?> value="8">8px</option>
						<option<?php check_select($form,'paddingwidth', '9',false); ?> value="9">9px</option>
						<option<?php check_select($form,'paddingwidth', '10', true); ?> value="10">10px</option>
						<option<?php check_select($form,'paddingwidth', '11', false); ?> value="11">11px</option>
						<option<?php check_select($form,'paddingwidth', '12', false); ?> value="12">12px</option>
						<option<?php check_select($form,'paddingwidth', '13', false); ?> value="13">13px</option>
						<option<?php check_select($form,'paddingwidth', '14', false); ?> value="14">14px</option>
						<option<?php check_select($form,'paddingwidth', '15', false); ?> value="15">15px</option>
						<option<?php check_select($form,'paddingwidth', '16', false); ?> value="16">16px</option>
						<option<?php check_select($form,'paddingwidth', '17', false); ?> value="17">17px</option>
						<option<?php check_select($form,'paddingwidth', '18', false); ?> value="18">18px</option>
						<option<?php check_select($form,'paddingwidth', '19', false); ?> value="19">19px</option>
						<option<?php check_select($form,'paddingwidth', '20', false); ?> value="20">20px</option>
						<option<?php check_select($form,'paddingwidth', '25', false); ?> value="25">25px</option>
						<option<?php check_select($form,'paddingwidth', '30', false); ?> value="30">30px</option>
						<option<?php check_select($form,'paddingwidth', '35', false); ?> value="35">35px</option>
						<option<?php check_select($form,'paddingwidth', '40', false); ?> value="40">40px</option>
						<option<?php check_select($form,'paddingwidth', '45', false); ?> value="45">45px</option>
						<option<?php check_select($form,'paddingwidth', '50', false); ?> value="50">50px</option>
						<option<?php check_select($form,'paddingwidth', '60', false); ?> value="60">60px</option>
						<option<?php check_select($form,'paddingwidth', '70', false); ?> value="70">70px</option>
						<option<?php check_select($form,'paddingwidth', '80', false); ?> value="80">80px</option>
					</select>							 
				</label>
			</div>
			<div class="alignleft">
				<label for="width" class="howto block"><span>Form Width</span></label>
				<input type="text" class="" id="width" name="width" value="<?php input_value($form, 'width', '300'); ?>" size="12" />
				<label for="widthtypeper" style="display:inline;"><input type="radio" name="widthtype" id="widthtypeper" <?php check_radio($form,'widthtype', 'per'); ?>/>%</label>
				<label for="widthtypepx" style="display:inline;"><input type="radio" name="widthtype" id="widthtypepx" <?php check_radio($form,'widthtype', 'px', true); ?> />px</label>
			</div>
						
			<span id="actualwidth"></span>

		<div>
			<label for="lalign" class="howto block"><span>Form Content Alignment</span></label>
			<p class="description">Align the form fields and labels inside the form. <strong>Note:</strong> you can change the alignment of the Form Text separately inside the Form Text editor.</p>
			<ul class="categorychecklist form-no-clear">
				<li><label for="lalignleft" class="menu-item-title"><span><input type="radio" id="lalignleft" name="talign" <?php check_radio($form,'talign', 'left'); ?> /> Left</span></label></li>
				<li><label for="laligncenter" class="menu-item-title"><span><input type="radio" id="laligncenter" name="talign" <?php check_radio($form,'talign', 'center',true); ?> /> Center</span></label></li>
				<li><label for="lalignright" class="menu-item-title"><span><input type="radio" id="lalignright" name="talign" <?php check_radio($form,'talign', 'right'); ?> /> Right</span></label></li>
			</ul>
		</div>
		<div>
			<label for="formalign" class="howto block"><span>Form Alignment</span></label>
			<p class="description">Align the form inside your widget or page content. Also called "floating" to the left or right.</p>
			<ul>
				<li><label class="menu-item-title" for='formalignleft'><input type="radio" id="formalignleft" name="formalign" <?php check_radio($form,'formalign', 'left'); ?> /> Left</label></li>
				<li><label class="menu-item-title" for='formaligncenter'><input type="radio" id="formaligncenter" name="formalign" <?php check_radio($form,'formalign', 'center',true); ?> /> Center</label></li>
				<li><label class="menu-item-title" for='formalignright'><input type="radio" id="formalignright" name="formalign" <?php check_radio($form,'formalign', 'right'); ?> /> Right</label></li>
			</ul>
		</div>
<?php
}


function cc_form_meta_box_fontstyles($post, $metabox=array()) {
	$form = $metabox['args'][0];
?>
<fieldset>
				<legend>Text</legend>
				<p class="description">These settings are for the Form Text field. If the checkboxes are checked, the settings also apply to the input labels.</p>
				<div class="block">
					<label for="tcolor" class="howto inline"><span>Text Color:</span></label>
					<div class="input"><input type="text" id="tcolor" name="tcolor" class="colorwell" value="<?php input_value($form, 'tcolor', '#accbf7'); ?>" /></div>
					
					<label for="lusc" class="howto checkbox block"><input type="checkbox" class="checkbox" name="lusc" id="lusc" <?php check_checkbox($form, 'lusc', 'yes', true); ?> /> <span>Use Same Color for Labels</span></label>
				</div>
				
				<p>
					<label for="tfont" class="howto block"><span>Text Font &amp; Size</span></label>
					<select id="tfont" name="tfont" class="inline">
							<optgroup label="Serif">
								<option <?php check_select($form,'tfont', 'times'); ?> style="font-family: 'Times New Roman', Times, Georgia, serif;" id="times">Times New Roman</option>
								<option <?php check_select($form,'tfont', 'georgia'); ?> style="font-family: Georgia, 'Times New Roman', Times, serif;" id="georgia">Georgia</option>
								<option <?php check_select($form,'tfont', 'palatino'); ?> style="font-family: 'Palatino Linotype', Palatino, 'Book Antiqua',Garamond, Bookman, 'Times New Roman', Times, Georgia, serif" id="palatino">Palatino *</option>
								<option <?php check_select($form,'tfont', 'garamond'); ?> style="font-family: Garamond,'Palatino Linotype', Palatino, Bookman, 'Book Antiqua', 'Times New Roman', Times, Georgia, serif" id="garamond">Garamond *</option>
								<option <?php check_select($form,'tfont', 'bookman'); ?> style="font-family: Bookman,'Palatino Linotype', Palatino, Garamond, 'Book Antiqua','Times New Roman', Times, Georgia, serif" id="bookman">Bookman *</option>
							</optgroup>
							<optgroup label="Sans-Serif">
								<option <?php check_select($form,'tfont', 'helvetica',true); ?> style="font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif;" id="helvetica">Helvetica</option>
								<option <?php check_select($form,'tfont', 'arial'); ?> style="font-family:Arial, Helvetica, sans-serif;" id="arial">Arial</option>
								<option <?php check_select($form,'tfont', 'lucidagrande'); ?> style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Lucida, Verdana, sans-serif;" id="lucida">Lucida Grande</option>
								<option <?php check_select($form,'tfont', 'verdana'); ?> style="font-family: Verdana, 'Lucida Grande', Lucida, TrebuchetMS, 'Trebuchet MS', Helvetica, Arial, sans-serif;" id="bookman">Verdana</option>
								<option <?php check_select($form,'tfont', 'trebuchet'); ?> style="font-family:'Trebuchet MS', Trebuchet, sans-serif;" id="trebuchet">Trebuchet MS</option>
								<option <?php check_select($form,'tfont', 'tahoma'); ?> style="font-family:Tahoma, Verdana, Arial, sans-serif;" id="tahoma">Tahoma</option>
								<option <?php check_select($form,'tfont', 'franklin'); ?> style="font-family:'Franklin Gothic Medium','Arial Narrow Bold',Arial,sans-serif;" id="franklin">Franklin Gothic</option>
								<option <?php check_select($form,'tfont', 'impact'); ?> style="font-family:Impact, Chicago, 'Arial Black', Arial, sans-serif;" id="impact">Impact *</option>
							  	<option <?php check_select($form,'tfont', 'arialblack'); ?> style="font-family:'Arial Black',Impact, Arial, sans-serif;" id="arial-black">Arial Black</option>
								<option <?php check_select($form,'tfont', 'gillsans'); ?> style="font-family:'Gill Sans','Gill Sans MT', 'Trebuchet MS', Trebuchet, Verdana, sans-serif;" id="gill">Gill Sans *</option>
							</optgroup>
							<optgroup label="Mono">
								<option <?php check_select($form,'tfont', 'courier'); ?> style="font-family: 'Courier New', Courier, monospace;" id="courier">Courier New</option>
								<option <?php check_select($form,'tfont', 'lucidaconsole'); ?> style="font-family: 'Lucida Console', Monaco, monospace;" id="lucida-console">Lucida Console</option>
							</optgroup>
							<optgroup label="Cursive">
								<option <?php check_select($form,'tfont', 'comicsans'); ?> style="font-family:'Comic Sans MS','Comic Sans', Sand, 'Trebuchet MS', Verdana, sans-serif" id="comicsans">Comic Sans MS</option>
							</optgroup>
							<optgroup label="Fantasy">
								<option <?php check_select($form,'tfont', 'papyrus'); ?> style="font-family: Papyrus, 'Palatino Linotype', Palatino, Bookman, fantasy" id="papyrus">Papyrus</option>
							</optgroup>
						</select>
						
						<select id="tsize" class="nomargin" name="tsize">
							  <option<?php check_select($form,'tsize', '7'); ?> value="7">7 px</option>
							  <option<?php check_select($form,'tsize', '8'); ?> value="8">8 px</option>
							  <option<?php check_select($form,'tsize', '9'); ?> value="9">9 px</option>
							  <option<?php check_select($form,'tsize', '10'); ?> value="10">10 px</option>
							  <option<?php check_select($form,'tsize', '11'); ?> value="11">11 px</option>
							  <option<?php check_select($form,'tsize', '12'); ?> value="12">12 px</option>
							  <option<?php check_select($form,'tsize', '13'); ?> value="13">13 px</option>
							  <option<?php check_select($form,'tsize', '14',true); ?> value="14">14 px</option>
							  <option<?php check_select($form,'tsize', '15'); ?> value="15">15 px</option>
							  <option<?php check_select($form,'tsize', '16'); ?> value="16">16 px</option>
							  <option<?php check_select($form,'tsize', '17'); ?> value="17">17 px</option>
							  <option<?php check_select($form,'tsize', '18'); ?> value="18">18 px</option>
							  <option<?php check_select($form,'tsize', '19'); ?> value="19">19 px</option>
							  <option<?php check_select($form,'tsize', '20'); ?> value="20">20 px</option>
							  <option<?php check_select($form,'tsize', '22'); ?> value="22">22 px</option>
							  <option<?php check_select($form,'tsize', '24'); ?> value="24">24 px</option>
							  <option<?php check_select($form,'tsize', '28'); ?> value="28">28 px</option>
							  <option<?php check_select($form,'tsize', '32'); ?> value="32">32 px</option>	  
							  <option<?php check_select($form,'tsize', '36'); ?> value="36">36 px</option>
							  <option<?php check_select($form,'tsize', '40'); ?> value="40">40 px</option>
							  <option<?php check_select($form,'tsize', '48'); ?> value="48">48 px</option>
						</select>
					<small class="asterix"><strong>* This font is popular, but not a "web-safe" font.</strong> If not available on an user's computer, it will default to a similar font.</small>
					<label for="lusf" class="howto checkbox"><input type="checkbox" name="lusf" id="lusf" rel="lfont" <?php check_checkbox($form, 'lusf', 'yes', true); ?> /> <span>Use Same Font for Labels</span></label>
				</p>
			</fieldset>
			<fieldset>
				<legend>Label</legend>
				
				<p class="description">These settings apply to the label text above the inputs.</p>
				<div id="labelcolorli" class="block">
					<label for="tcolor" class="howto inline"><span>Label Color:</span></label>
					<div class="input"><input type="text" id="lcolor" name="lcolor" class="colorwell" value="<?php input_value($form, 'lcolor', '#accbf7'); ?>" /></div>
				</div>
				
				<label for="lpad" class="howto block"><span>Label Padding</span>
				<select id="lpad" name="lpad">
				  <option<?php check_select($form,'lpad', '0'); ?> value="0">None<option>
				  <option<?php check_select($form,'lpad', '.1'); ?> value=".1">.1 em<option>
				  <option<?php check_select($form,'lpad', '.25'); ?> value=".2">.2 em<option>
				  <option<?php check_select($form,'lpad', '.5'); ?> value=".5">.5 em</option>
				  <option<?php check_select($form,'lpad', '1'); ?> value="1">1 em</option>
				  <option<?php check_select($form,'lpad', '1.25'); ?> value="1.25">1.25 em</option>
				  <option<?php check_select($form,'lpad', '1.5'); ?> value="1.5">1.5 em</option>
				  <option<?php check_select($form,'lpad', '2',true); ?> value="2">2 em</option>
				  <option<?php check_select($form,'lpad', '2.5'); ?> value="2.5">2.5 em</option>
				  <option<?php check_select($form,'lpad', '3'); ?> value="3">3 em</option>
				</select>
				</label>
				
				<div class="block">
				<label for="reqast" class="howto checkbox block"><input type="checkbox" class="checkbox" name="reqast" id="reqast" <?php check_checkbox($form, 'reqast', '1', true); ?> /> <span>Add asterisk if field is required.</span></label>
				</div>
								
				<div id="lfontli">
					<label for="lfont" class="howto block"><span>Label Font &amp; Size</span></label>
					<select id="lfont" name="lfont" class="inline">
						<optgroup label="Serif">
							<option <?php check_select($form,'lfont', 'times'); ?> style="font-family: 'Times New Roman', Times, Georgia, serif;" id="times">Times New Roman</option>
							<option <?php check_select($form,'lfont', 'georgia'); ?> style="font-family: Georgia, 'Times New Roman', Times, serif;" id="georgia">Georgia</option>
							<option <?php check_select($form,'lfont', 'palatino'); ?> style="font-family: 'Palatino Linotype', Palatino, 'Book Antiqua',Garamond, Bookman, 'Times New Roman', Times, Georgia, serif" id="palatino">Palatino *</option>
							<option <?php check_select($form,'lfont', 'garamond'); ?> style="font-family: Garamond,'Palatino Linotype', Palatino, Bookman, 'Book Antiqua', 'Times New Roman', Times, Georgia, serif" id="garamond">Garamond *</option>
							<option <?php check_select($form,'lfont', 'bookman'); ?> style="font-family: Bookman,'Palatino Linotype', Palatino, Garamond, 'Book Antiqua','Times New Roman', Times, Georgia, serif" id="bookman">Bookman *</option>
						</optgroup>
						<optgroup label="Sans-Serif">
							<option <?php check_select($form,'lfont', 'helvetica'); ?> style="font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif;" id="helvetica">Helvetica</option>
							<option <?php check_select($form,'lfont', 'arial'); ?> style="font-family:Arial, Helvetica, sans-serif;" id="arial">Arial</option>
							<option <?php check_select($form,'lfont', 'lucidagrande'); ?> style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Lucida, Verdana, sans-serif;" id="lucida">Lucida Grande</option>
							<option <?php check_select($form,'lfont', 'verdana'); ?> style="font-family: Verdana, 'Lucida Grande', Lucida, TrebuchetMS, 'Trebuchet MS', Helvetica, Arial, sans-serif;" id="bookman">Verdana</option>
							<option <?php check_select($form,'lfont', 'trebuchet'); ?> style="font-family:'Trebuchet MS', Trebuchet, sans-serif;" id="trebuchet">Trebuchet MS</option>
							<option <?php check_select($form,'lfont', 'tahoma'); ?> style="font-family:Tahoma, Verdana, Arial, sans-serif;" id="tahoma">Tahoma</option>
							<option <?php check_select($form,'lfont', 'franklin'); ?> style="font-family:'Franklin Gothic Medium','Arial Narrow Bold',Arial,sans-serif;" id="franklin">Franklin Gothic</option>
							<option <?php check_select($form,'lfont', 'impact'); ?> style="font-family:Impact, Chicago, 'Arial Black', Arial, sans-serif;" id="impact">Impact *</option>
						  	<option <?php check_select($form,'lfont', 'arialblack'); ?> style="font-family:'Arial Black',Impact, Arial, sans-serif;" id="arial-black">Arial Black</option>
							<option <?php check_select($form,'lfont', 'gillsans'); ?> style="font-family:'Gill Sans','Gill Sans MT', 'Trebuchet MS', Trebuchet, Verdana, sans-serif;" id="gill">Gill Sans *</option>
						</optgroup>
						<optgroup label="Mono">
							<option <?php check_select($form,'lfont', 'courier'); ?> style="font-family: 'Courier New', Courier, monospace;" id="courier">Courier New</option>
							<option <?php check_select($form,'lfont', 'lucidaconsole'); ?> style="font-family: 'Lucida Console', Monaco, monospace;" id="lucida-console">Lucida Console</option>
						</optgroup>
						<optgroup label="Cursive">
							<option <?php check_select($form,'lfont', 'comicsans'); ?> style="font-family:'Comic Sans MS','Comic Sans', Sand, 'Trebuchet MS', Verdana, sans-serif" id="comicsans">Comic Sans MS</option>
						</optgroup>
						<optgroup label="Fantasy">
							<option <?php check_select($form,'lfont', 'papyrus'); ?> style="font-family: Papyrus, 'Palatino Linotype', Palatino, Bookman, fantasy" id="papyrus">Papyrus</option>
						</optgroup>
					</select>
					<select id="lsize" class="nomargin" name="lsize">
						  <option<?php check_select($form,'lsize', '7'); ?> value="7">7 px</option>
						  <option<?php check_select($form,'lsize', '8'); ?> value="8">8 px</option>
						  <option<?php check_select($form,'lsize', '9'); ?> value="9">9 px</option>
						  <option<?php check_select($form,'lsize', '10'); ?> value="10">10 px</option>
						  <option<?php check_select($form,'lsize', '11'); ?> value="11">11 px</option>
						  <option<?php check_select($form,'lsize', '12',true); ?> value="12">12 px</option>
						  <option<?php check_select($form,'lsize', '13'); ?> value="13">13 px</option>
						  <option<?php check_select($form,'lsize', '14'); ?> value="14">14 px</option>
						  <option<?php check_select($form,'lsize', '15'); ?> value="15">15 px</option>
						  <option<?php check_select($form,'lsize', '16'); ?> value="16">16 px</option>
						  <option<?php check_select($form,'lsize', '17'); ?> value="17">17 px</option>
						  <option<?php check_select($form,'lsize', '18'); ?> value="18">18 px</option>
						  <option<?php check_select($form,'lsize', '19'); ?> value="19">19 px</option>
						  <option<?php check_select($form,'lsize', '20'); ?> value="20">20 px</option>
						  <option<?php check_select($form,'lsize', '22'); ?> value="22">22 px</option>
						  <option<?php check_select($form,'lsize', '24'); ?> value="24">24 px</option>
						  <option<?php check_select($form,'lsize', '28'); ?> value="28">28 px</option>
						  <option<?php check_select($form,'lsize', '32'); ?> value="32">32 px</option>	  
						  <option<?php check_select($form,'lsize', '36'); ?> value="36">36 px</option>
						  <option<?php check_select($form,'lsize', '40'); ?> value="40">40 px</option>
						  <option<?php check_select($form,'lsize', '48'); ?> value="48">48 px</option>
					</select>
				<small class="asterix"><strong>* This font is popular, but not a "web-safe" font.</strong> If not available on an user's computer, it will default to a similar font.</small>
				</div>
		</fieldset>
		<fieldset>
			<legend>Inputs</legend>
			<label for="isize" class="howto block"><span>Input Size</span>
			<p class="description">This setting changes how many characters wide the form inputs are.</p>
				<select id="size" name="size">
				  <option<?php check_select($form,'size', '20'); ?> value="20">20</option>
				  <option<?php check_select($form,'size', '25'); ?> value="25">25</option>
				  <option<?php check_select($form,'size', '30', true); ?> value="30">30</option>
				  <option<?php check_select($form,'size', '35'); ?> value="35">35</option>
				  <option<?php check_select($form,'size', '40'); ?> value="40">40</option>
				  <option<?php check_select($form,'size', '45'); ?> value="45">45</option>
				  <option<?php check_select($form,'size', '50'); ?> value="50">50</option>
				  <option<?php check_select($form,'size', '60'); ?> value="60">60</option>
				  <option<?php check_select($form,'size', '70'); ?> value="70">70</option>
				  <option<?php check_select($form,'size', '80'); ?> value="80">80</option>
				</select>
				</label>
		</fieldset>
<!--
				<li id="labelweightli" class="form-item"><label>Font Weight</label>
					<div class="input"><ul>
					  	<li><label for="labelweightboldno"><input type="radio" name="labelweight" id="labelweightboldno" <?php check_radio($form,'labelweight', 'normal', true); ?> /> Normal</label></li>
						<li><label for="labelweightboldyes"><input type="radio" name="labelweight" id="labelweightboldyes" <?php check_radio($form,'labelweight', 'bold'); ?> /> Bold</label></li>
					</ul></div>
				 </li>
-->
<?php 
} 

?>