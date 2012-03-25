<?php 

#ob_start();

if(!function_exists('tempty')) {
	function tempty($val) { 
		$val = trim($val);
    	return empty($val) && $val !== 0; 
	}
}


function wp_get_cc_form_object( $form ) {
	return wp_get_cc_form( $form, 'object');
}

function wp_get_cc_form( $form, $type = 'array', $forms = array()) {
	if ($form != 0 && ! $form )
		return false;
	
	if(empty($forms)) {	$forms = wp_get_cc_forms(); }

	if ( !isset($forms[$form]) ) {
		foreach($forms as $key => $f) {
			if($f['cc-form-id'] == $form) { wp_get_cc_form($key, $type, $forms); }
		}
		return false;
	} else {
		if(strtolower($type) != 'array') {
			return (object)$forms[$form];
		} else {
			return (array)$forms[$form];
		}
	}
}

function is_cc_form( $form ) {
	if ( ! $form )
		return false;

	$form_obj = wp_get_cc_form_object( $form );
	
	if($form_obj && ! is_wp_error( $form_obj ) && isset($form_obj->$form) && !empty($form_obj[$form])) {
		return true;
	}

	return false;
}

function wp_create_cc_form() {
	return wp_update_cc_form_object( -1, $_REQUEST );
}

function wp_delete_cc_form( $menu ) {
	$forms = wp_get_cc_forms();
	
	if(isset($forms[$menu]) && !empty($forms[$menu])) {
		unset($forms[$menu]);
		return wp_set_cc_forms($forms);
	}
	return false;
}

function esc_attr_recursive($array) {
	if(is_array($array)) {
		foreach($array as $key => $item) {
			$array[$key] = esc_attr_recursive($item);
		}
	} else {
		$array = htmlspecialchars($array);
	}
	return $array;
}

function generate_form_from_request($r, $forms) {
	if(!is_array($r)) { return false; }
	
	// We don't want to save this extranneous stuff into the DB
	unset($r['_wp_http_referer'], $r['action'], $r['save_form'], $r['page'], $r['form-style'], $r['closedpostboxesnonce'], $r['meta-box-order-nonce'], $r['update-cc-form-nonce']);
	
	$r = esc_attr_recursive($r);
	
	if(!isset($r['cc-form-id']) || $r['cc-form-id']  === '' || $r['cc-form-id'] == -1) {
		$r['cc-form-id'] = sizeof($forms);
	}
	if($r['form-name'] == 'Enter form name here') { $r['form-name'] = 'Form #'.$r['cc-form-id']; }
	
	return $r;
}

function wp_update_cc_form_object( $form_id = -1, $data = array()) {
	$form_id = (int) $form_id;

	
	// Get existing forms
	$forms = wp_get_cc_forms();
		
	// Whittle down submitted form into just the fields we want to save
	$form = generate_form_from_request($data, $forms);	
	
	// form doesn't already exist, so create a new form
	if ($form_id == -1 || $form_id === '' || !isset($form['cc-form-id']) || $form['cc-form-id']  === '') {
		// Add the form to the forms array
		$forms[] = $form;

	} elseif(isset($forms[$form_id])) {
		// Hook into the form saving process if you want
		$form = apply_filters("wp_update_cc_form_$form_id", $form );
		$forms[$form_id] = $form;
	} else {
		return new WP_Error('wp_update_cc_form_object_failed', __('The form both does not exist and does exist. Can not process!','constant-contact-api'));
	}
	// That cached version's gotta go.
	delete_transient("cc_form_$form_id");
	// Update forms array to DB
	wp_set_cc_forms($forms);
	
	// Return the new form id
	return (int) $form['cc-form-id'];
	
}

function wp_set_cc_forms($forms) {
	// Hook into the data savd in the form
	$forms = apply_filters("wp_set_cc_forms", $forms );
	
	return update_option('cc_form_design', $forms);
}


function wp_get_cc_forms() {
	
	$cc_forms = get_option('cc_form_design');
	
	if(!$cc_forms) { $cc_forms = array(); }
	
	// Generate truncated menu names
	$previous_names = array();
	foreach( (array) $cc_forms as $key => $_cc_form ) {
		$name = isset($_cc_form['form-name']) ? $_cc_form['form-name'] : 'Form '+$key;
		
		$_cc_form['truncated_name'] = trim( wp_html_excerpt( $name, 30 ) );
		if ( isset($_cc_form['form-name']) && $_cc_form['truncated_name'] != $_cc_form['form-name'])
			$_cc_form['truncated_name'] .= '&hellip;';
		
		if(!in_array(sanitize_user( $name ), $previous_names)) { 
			$previous_names[] = sanitize_user( $name );
		} else {
			$namekey = sanitize_user( $name );
			$previous_names[$namekey] = isset($previous_names[$namekey]) ? ($previous_names[$namekey] + 1) : 1;
			$_cc_form['truncated_name'] .= ' ('.$previous_names[$namekey].')';
		}
		
		$cc_forms[$key]['truncated_name'] = $_cc_form['truncated_name'];
	}
	
	return $cc_forms;
}


if(!function_exists('r')) {
function r($content, $die = false, $echo=true) {
	try {
		if(!function_exists('htmlentities_recursive')) {
			function htmlentities_recursive($data) {
			    foreach($data as $key => $value) {
			        if (is_array($value)) {
			            $data[$key] = htmlentities_recursive($value);
			        } else {
			        	if(is_string($value)) {
			            $data[$key] = htmlentities($value);
			            }
			        }
			    }
			    return $data;
			}
		}
		if(is_array($content)) { $content = htmlentities_recursive($content); }
		$output = '<pre style="text-align:left; margin:10px; padding:10px; background-color: rgba(255,255,255,.95); border:3px solid rgba(100,100,100,.95); overflow:scroll; max-height:400px; float:left; width:90%; max-width:800px; white-space:pre;">';
		$output .= print_r($content, true); //print_r(mixed expression [, bool return])
		$output .= '</pre>';
		if($echo) {	echo $output; } else { return $output; }
		if($die)  { die(); }
	} catch(Exception $e) {	}
}
}
#r($_GET['f']['email_address']);

function check_default($form, $name, $id, $value) {
	$inputValue = '';
	if(isset($value)) {
	$inputValue = $value;
	}
	if(isset($form[$name]) && is_array($form[$name])) {
		$inputValue = isset($form[$name][$id]) ? $form[$name][$id] : $value;
	} elseif(isset($form[$name]) && !is_array($form[$name])){
		$inputValue = isset($form[$name]) ? $form[$name] : $value;
	} else {
		$inputValue = isset($form[$id]) ? $form[$id] : $value;
	}
	return html_entity_decode(stripslashes($inputValue));
}

global $formfield_num;
$formfield_num = 0;

function make_formfield_list_items($array, $checkedArray) {
	$out = '';
	foreach($array as $a) {
		$out .= make_formfield_list_item($a[0], $a[1], !empty($checkedArray) ? in_array($a[0], $checkedArray) : $a[2]);
	}
	return $out;
}

function make_formfield_list_item($id, $title, $checked = false) {
	if($checked) { $checked = ' checked="checked"';}
	if($id == 'email_address') { $checked = ' checked="checked" disabled="disabled"'; }
	return '<li>
		<label class="menu-item-title"><input type="checkbox" class="menu-item-checkbox" name="formfields['.$id.']" value="'.$id.'"'.$checked.' /> '.$title.'</label>
	</li>';
}

function make_formfield($_form_object = array(), $class, $id, $value, $checked, $default = '', $type="text", $labeldefault = '') { 
	global $formfield_num;
	
	$out = $position = $emailWidth = $hide = '';
	
	$name = 'f';
	$class = trim($class .' ui-state-default menu-item ui-state-default formfield');
	if((isset($_form_object['f'][$formfield_num]) && isset($_form_object['f'][$formfield_num]['n']))) {
		$checked = 'checked="checked"'; 
	} else {
		$hide = ' style="display:none;"';
	}
	 
	$defaultAlign = 'Align';
	$defaultSize = 'Input Size';
	$button = $textarea = false;
	if($type == 'text' || $type=='t') {
		$t = 't';
		$labelLabel = 'Label text';
		$defaultLabel = 'Input placeholder text';
		$inputValue = check_default($_form_object, $name, $id, $value);
		//$value = 'Form Text';
	} elseif($type=='button' || $type=='submit' || $type=='b' || $type=='s') {
		$t = 'b';
		$button = true;
		$labelLabel = 'Button label';
		$inputValue = '';
		$defaultLabel = 'Button text';
	} elseif($type=='textarea' || $type=='ta') {
		$t = 'ta';
		$textarea = true;
		$labelLabel = 'Headline';
		$default = $labeldefault;
		$inputValue = check_default($_form_object,$name, $id, $value);
		$defaultLabel = 'Form Text';
	}
	$defaultRequired = 'Required';
	
	$position = (isset($_form_object['f'][$formfield_num]['pos']) && !empty($_form_object['f'][$formfield_num]['pos'])) ? $_form_object['f'][$formfield_num]['pos'] : '';
	$size = (isset($_form_object['f'][$formfield_num]['size']) && !empty($_form_object['f'][$formfield_num]['size'])) ? $_form_object['f'][$formfield_num]['size'] : '';	
	$required = (isset($_form_object['f'][$formfield_num]['required']) && !empty($_form_object['f'][$formfield_num]['required'])) ? ' checked="checked"' : '';
	$bold = (isset($_form_object['f'][$formfield_num]['bold']) && !empty($_form_object['f'][$formfield_num]['bold'])) ? ' checked="checked"' : '';
	$italic = (isset($_form_object['f'][$formfield_num]['italic']) && !empty($_form_object['f'][$formfield_num]['italic'])) ? ' checked="checked"' : '';
	
	if(isset($_form_object['f'][$formfield_num]['val'])) {
		$default = html_entity_decode( stripslashes($_form_object['f'][$formfield_num]['val']) );
	} 
	
	
	if(isset($_form_object['f'][$formfield_num]['label'])) {
		$inputValue = html_entity_decode( stripslashes($_form_object['f'][$formfield_num]['label']) );
	} elseif(isset($labeldefault) && !empty($labeldefault)) { 
		$inputValue = $labeldefault;
	}
	
	$name = $name.'['.$formfield_num.']';
	$formfield_num++;
	$out .= '
		<li class="'.$class.'"'.$hide.'>
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title">'.$value.'</span>
					<span class="item-controls">
						<span class="item-type"></span>
						<input type="checkbox" name="'.$name.'[n]" id="'.$id.'" value="'.$name.'" '.$checked.' class="checkbox hide-if-js" rel="'.$type.'" />
						<a class="item-edit" id="edit-'.$id.'" title="Edit '.$name.'" href="#">Edit Menu Item</a>
					</span>
				</dt>
			</dl>
			<div class="menu-item-settings"><div class="wrap">
				<input type="hidden" name="'.$name.'[id]" value="'.$id.'" />
				<input type="hidden" name="'.$name.'[t]" value="'.$t.'" />
				<input type="hidden" name="'.$name.'[pos]" id="'.$id.'_pos" value="'.$position.'" class="position" />';
	if(!$textarea) {
		$out .= "\n".'<p><label for="'.$id.'_label" class="labelValue howto"><span class="description">'.$labelLabel.'</span><input name="'.$name.'[label]" type="text" id="'.$id.'_label" value="'.$inputValue.'" class="labelValue widefat"  /></label></p>
						<p class="labelStyle defaultSkin">
							<label for="'.$id.'_bold" class="labelStyle mce_bold">
								<a class="mceIcon" title="Make label bold"><input type="checkbox" name="'.$name.'[bold]" id="'.$id.'_bold" value="bold"'.$bold.' /> Bold</a>
							</label>
							<label for="'.$id.'_italic"'.$italic.' class="labelStyle mce_italic">
								<a class="mceIcon" title="Make label italic"><input type="checkbox" name="'.$name.'[italic]" id="'.$id.'_italic" value="italic" /> Italic</a>
							</label>';
			if($id == 'email_address' || $t == 'b') {
				$out .= '<input type="hidden" name="'.$name.'[required]" id="'.$id.'_required" value="required" />';
			} else {
				$out .= '<label for="'.$id.'_required" class="labelStyle howto"><span>'.$defaultRequired.'</span>&nbsp;<input type="checkbox" name="'.$name.'[required]" id="'.$id.'_required" value="required"'.$required.' class="labelRequired"  /></label>';
			}
		$out .= '</p>';
		$out .= "\n".'<div class="clear"><label for="'.$id.'_default" class="labelDefault howto"><span class="description">'.$defaultLabel.'</span><input type="text" name="'.$name.'[val]" id="'.$id.'_default" value="'.$default.'" class="labelDefault widefat"  /></label></div>';
	} else {
		$out .= "\n".'<div><label for="'.$id.'_default" class="labelDefault howto"><span class="description">'.$defaultLabel.'</span><textarea name="'.$name.'[val]" id="'.$id.'_default" class="labelDefault tinymce widefat">'.$default.'</textarea></label></div>';		
	}
	$out .='</div>
			</div>
			<ul class="menu-item-transport"></ul>
		</li>';
	return $out;
}

function input_value($form, $name, $default) {
	echo check_default($form, $name, '', $default);
}

function check_select($form, $name, $value, $default = false) {
	echo " value='$value'";
	$check = (isset($form[$name]) && !empty($form[$name]) && $form[$name] == $value);
	if($check || !(isset($form[$name]) && !empty($form[$name])) && $default) {
		echo ' selected="selected"';
	}
}

function check_radio($form, $name, $value, $default = false) {
	echo " value='$value'";
	$check = (isset($form[$name]) && $form[$name] !== '' && $form[$name] === $value);
	if($check || (empty($form) && $default === true)) {
		echo ' checked="checked"';
	}
}

function check_checkbox($form, $name = '', $value = '', $default = false) {
	check_radio($form, $name, $value, $default);
}

function get_check_field($form, $name, $value, $echo = ' selected="selected"', $default = false, $type = 'select') {
	if(is_array($value)) {
		foreach($value as $val) {
			if(get_check_field($name, $val, $echo)) { exit; } // If one is true, stop processing
		}
	}
	if(isset($form[$name]) && !empty($form[$name]) && (
			$type != 'select' ||
			($type == 'select' && $form[$name] == $value || strtolower($form[$name]) == strtolower($value))
		)
	) {
		return stripslashes(html_entity_decode($echo));
	} elseif($default) {
		return $echo;
	}
	return false;
}
function check_field($form, $name, $value, $echo = ' selected="selected"', $default = false, $type = 'select') {
	echo get_check_field($form, $name, $value, $echo, $default, $type); 
}



?>