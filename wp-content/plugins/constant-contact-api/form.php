<?php
@define('DOING_AJAX', true);
@header('Content-Type: application/json;');
@header('Status Code: 200 OK;');

	# header('HTTP/1.1 403 Forbidden'); // For testing ModSecurity issues
	function findFont($id = '') {
			switch($id) {
			
				case 'times':
					return "'Times New Roman', Times, Georgia, serif";
					break;
				case 'georgia':
					return "Georgia,'Times New Roman', Times, serif";
					break;
				case 'palatino':
					return "'Palatino Linotype', Palatino, 'Book Antiqua',Garamond, Bookman, 'Times New Roman', Times, Georgia, serif";
					break;
				case 'garamond':
					return "Garamond,'Palatino Linotype', Palatino, Bookman, 'Book Antiqua', 'Times New Roman', Times, Georgia, serif";
					break;
				case 'bookman':
					return "Bookman,'Palatino Linotype', Palatino, Garamond, 'Book Antiqua','Times New Roman', Times, Georgia, serif";
					break;
				case 'helvetica':
					return "'Helvetica Neue',HelveticaNeue, Helvetica, Arial, Geneva, sans-serif";
					break;
				case 'arial':
					return "Arial, Helvetica, sans-serif";
					break;
				case 'lucida':
					return "'Lucida Grande', 'LucidaGrande', 'Lucida Sans Unicode', Lucida, Verdana, sans-serif";
					break;
				case 'verdana':
					return "Verdana, 'Lucida Grande', Lucida, TrebuchetMS, 'Trebuchet MS',Geneva, Helvetica, Arial, sans-serif";
					break;
				case 'trebuchet':
					return "'Trebuchet MS', Trebuchet, Verdana, sans-serif";
					break;
				case 'tahoma':
					return "Tahoma, Verdana, Arial, sans-serif";
					break;
				case 'franklin':
					return "'Franklin Gothic Medium','FranklinGotITC','Arial Narrow Bold',Arial,sans-serif";
					break;
				case 'impact':
					return "Impact, Chicago, 'Arial Black', sans-serif";
					break;
				case 'arialblack':
					return "'Arial Black',Impact, Arial, sans-serif";
					break;
				case 'gillsans':
					return "'Gill Sans','Gill Sans MT', 'Trebuchet MS', Trebuchet, Verdana, sans-serif";
					break;
				case 'courier':
					return "'Courier New', Courier, Monaco, monospace";
					break;
				case 'lucidaconsole':
					return "'Lucida Console', Monaco, 'Courier New', Courier, monospace";
					break;
				case 'comicsans':
					return "'Comic Sans MS','Comic Sans', Sand, 'Trebuchet MS', cursive";
					break;
				case 'papyrus':
					return "Papyrus,'Palatino Linotype', Palatino, Bookman, fantasy";
					break;
			}
		}
	
	function makeFormField($field, $cc_request = array(), $currentform = false) {
		$asterisk = $bold = $italic = $required = $val = $size = '';
		$fields = '';
		if(is_array($field)) { extract($field); }
		
		if(isset($cc_request['fields'][$field['id']]['value']) && $currentform) {
			$placeholder = '';
			$val = stripslashes(stripslashes($cc_request['fields'][$field['id']]['value']));
		} else {
			$placeholder = ' placeholder=\''.htmlentities(stripslashes(stripslashes($val))).'\'';
			if(!($t == 'b' || $t == 's')) {
				$val = '';
			}
		}
		
		
		if(isset($label)) { $label = stripslashes(stripslashes($label)); }
		if($t == 'b' || $t == 's') { $fields .= "<!-- %%LISTSELECTION%% -->"; }
		$fields .= "<div class='cc_$id kws_input_container'>";
		
		$class = '';
		if(!empty($bold)) { $class .= ' kws_bold'; }
		if(!empty($italic)) { $class .= ' kws_italic'; }
		$name = "fields[$id]";
		$requiredFields = '';
		if(!empty($required)) { 
			$required = " required"; 
			$asterisk = isset($reqast) ? '<span class="req" title="The '.$label.' field is required">*</span>' : '';
			if($t != 'b' && $t == 'ta') { 
				$requiredFields = '<input type="hidden" name="'.$name.'[req]" value="1" />';
			}
		}
		
		if($t == 'ta') {
			if(isset($_REQUEST['output']) && $_REQUEST['output'] == 'html') { $fields .= html_entity_decode($val); } else { $fields .=  $val; }
		} else if($t == 'b' || $t == 's') {
			if(!empty($label)) { $fields .= "<label for='cc_$id' class='$class'>$label{$asterisk}</label>"; }
			$fields .= "<input type='submit' value='$val' class='$t' id='cc_$id' name='constant-contact-signup-submit' ><div class='kws_clear'></div>"; 
		} else {
			if(!empty($label)) { $fields .= "<label for='cc_$id' class='$class'>$label{$asterisk}</label>"; }
			if(!empty($size) && $t == 't') { $size = " size=\"$size\"";}
			$fields .= "<input type='text' value='$val'$size $placeholder name='".$name."[value]' class='{$t} $class{$required}' id='cc_$id' >"; 
		}
		if(!empty($label)) { $fields .= '<input type="hidden" name="'.$name.'[label]" value="'.htmlentities($label).'" />'; }
		$fields .= $requiredFields;
		$fields .= "</div>";
		return $fields;
	}
	
	function processForm() {
		$f = $uid = $required = $t = $label = $givethanks = $safesubscribe = $size = $name = $id = $fields = $labelsusesamecolor = $labelsusesamealign = $labelsusesamefont = $labelsusesamepadding = $bgrepeat = $lfont = $tfont = $widthtype = $backgroundtype = $blockalign = $intro = $size = $uniqueformid = '';
		$cc_request = array();
	
		if(isset($_REQUEST['f'])) {
			extract($_REQUEST);
		}
		
		$currentform = (isset($cc_request['uniqueformid']) && $cc_request['uniqueformid'] == $uniqueformid);
		
		if(isset($form)) { $selector = ' id="cc_form_'.$form.'"'; } else { $selector = ''; }

		// Only process one to speed up things.
		if(!empty($changed)) {
			foreach($f as $field) {
				$field['size'] = $size;
				if($field['id'] == str_replace('_default', '', $changed) || $field['id'] == str_replace('_label', '', $changed)) {
					return makeFormField($field, $cc_request);
				};
			};
		}
		if(is_array($f)) {
			foreach($f as $field) {
				$field['size'] = $size;
				$fields .= makeFormField($field, $cc_request, $currentform);
			}
			$fields = '<div class="kws_input_fields">'.$fields.'</div>';
		}
		if($safesubscribe != 'no') {
			$safesubscribelink = '<a href="http://katz.si/safesubscribe" target="_blank" class="cc_safesubscribe" rel="nofollow">Privacy by SafeSubscribe</a>';
		} else {
			$safesubscribelink = '';
		}
		$haserror = $errors = $success = $hidden = $action = '';
		if(isset($_REQUEST['output']) && $_REQUEST['output'] == 'html') {
			$haserror = ' %%HASERROR%%';
			$action = '%%ACTION%%';
			$errors = '<!-- %%ERRORS%% -->';
			$success = '<!-- %%SUCCESS%% -->';
			$hidden = '<!-- %%HIDDEN%% -->';
		}
		if(empty($cc_success)) {
			$formInner = $errors . $success . $fields . $safesubscribelink . $hidden;
		} else {
			$formInner = $success;
		}
		
		$form = <<<EOD
	<div class="kws_form$haserror"$selector>
		<form id="constant-contact-signup" action="$action" method="post" autocomplete="on">
			$formInner	
			<div class="kws_clear"></div>
			<!-- Generated by Constant Contact Form Generator by Katz Web Services, Inc. -->
		</form>
	</div>
EOD;
		$form = str_replace("\n", ' ', $form);
		$form = str_replace("\r", ' ', $form);
		$form = str_replace("\t", ' ', $form);
		return $form;
	}
	function processStyle() {
		$required = $color2 = $tcolor = $lcolor = $bordercolor = $color6 = $color5 = $t = $label = $size = $name = $id = $fields = $labelsusesamecolor = $labelsusesamealign = $labelsusesamefont = $labelsusesamepadding = $givethanks = $safesubscribe = $blockalign = $bgcss = $gradheight = $lpad = $lalign = $bgimage = $bgpos = $bgrepeat = $lfont = $tfont = $f = $lsize = $talign = $width = $widthtype = $borderradius = $borderwidth = $paddingwidth = $formalign = $talign = $backgroundtype = $widthtype = $borderstyle = $tsize = $lsize = '';	
		
		extract($_REQUEST);
		
		if(isset($form)) { $selector = 'div#cc_form_'.$form; } else { $selector = 'div.kws_form'; }
		
		$bgtop = $color6;
		$bgtopraw = str_replace('#', '', $bgtop);
		$bgbottom = $color2;
		$bgbottomraw = str_replace('#', '', $bgbottom);
		$bordercolor = $bordercolor;
		$textcolor = $tcolor;
		$labelcolor = $lcolor;	
		$tfont = findFont($tfont);
		$lfont = findFont($lfont);
		if($widthtype == 'per') { $widthtype = '%'; }
		
		if($backgroundtype == 'gradient') {
	#		$bgcss = "background: $bgbottom url('gradients/$bgtopraw-$bgbottomraw-2x$gradheight.png') left top repeat-x;";
			if($gradtype == 'horizontal') { 
				$bgrepeat = 'left top repeat-y'; 
				$dimensions = "width=$gradheight&height=1";
				$bgback = $bgtop;
			} else { 
				$dimensions = "height=$gradheight&width=1";
				$bgrepeat = 'left top repeat-x'; 
				$bgback = $bgbottom;
			}
			$bgcss = "background: $bgbottom url('{$path}ozhgradient.php?start=$bgtopraw&end=$bgbottomraw&&type=$gradtype&$dimensions') $bgrepeat;";
		} elseif($backgroundtype == 'solid') { 
			$bgcss = "background-color: $bgbottom; background-image:none;";
		} elseif($backgroundtype == 'pattern') { 
			$bgcss = "background: $bgbottom url('{$path}$patternurl') left top repeat;";
		} else { // URL
			$bgcss = "background: $bgbottom url('$bgimage') $bgpos $bgrepeat;";
		}
		
#		if($labelsusesamealign == 'yes') { $lalign = $talign; }
	/* 	if($labelsusesamepadding == 'yes') { $lpad = $tpad; } */
		if($labelsusesamefont) { $lfont = $tfont; $lsize = $tsize; }
		if($labelsusesamecolor) { $labelcolor = $textcolor; }
		if($talign == 'center') { $blockalign = 'margin:0 auto;'; } elseif($talign == 'right') { $blockalign = 'clear:both; float:right;';}
		if($formalign == 'center') { $formalign = 'margin:0 auto;'; } elseif($formalign == 'right') { $formalign = 'clear:both; float:right;';} elseif($formalign == 'left') { $formalign = 'clear:both; float:left;';}
		if($givethanks) { $formalign .= 'margin-bottom: .5em;';}
		
		$safesubscribecss = '';
//		echo $safesubscribe; die();
		if(!empty($safesubscribe) && $safesubscribe != 'no') {
			$safesubscribecss = "$selector a.cc_safesubscribe { 
			background: transparent url({$path}images/safesubscribe-$safesubscribe.gif) left top no-repeat;
			$blockalign
			width:168px;
			height:14px;
			display:block;
			text-align:left!important;
			overflow:hidden!important;
			text-indent: -9999px!important;
			margin-top: {$lpad}em;
		}";
		}
	
	$paddingwidth = (int)$paddingwidth;
	$borderradius = (int)$borderradius;
	$width = (int)$width;
	

$css = <<<EOD
<style type="text/css">
	
	<!--[if IE lte 9]>$selector { behavior: url(border-radius.htc); }<![endif]-->
	
	.has_errors .cc_intro { display:none;} 
	$selector .cc_success {
		margin:0!important;
		padding:10px;
		color: $textcolor!important;
	}
	
	$selector {
		line-height: 1;
	}
	$selector ol, $selector ul {
		list-style: none;
	}
	$selector blockquote, $selector q {
		quotes: none;
	}
	$selector blockquote:before, $selector blockquote:after,
	$selector q:before, $selector q:after {
		content: '';
		content: none;
	}
	
	/* remember to define focus styles! */
	$selector :focus {
		outline: 0;
	}
	
	/* remember to highlight inserts somehow! */
	$selector ins {
		text-decoration: none;
	}
	$selector del {
		text-decoration: line-through;
	}
	
	/* tables still need 'cellspacing="0"' in the markup */
	$selector table {
		border-collapse: collapse;
		border-spacing: 0;
	}
	
	$selector .req { cursor: help; }
	
	$selector {
		$bgcss
		padding: {$paddingwidth}px;
		$formalign
		-webkit-background-clip: border-box;
		-moz-background-clip: border-box;
		background-clip:border-box;
		background-origin: border-box;
		-webkit-background-origin: border-box;
		-moz-background-origin: border-box;
		border: $borderstyle $bordercolor {$borderwidth}px;
		-moz-border-radius: {$borderradius}px {$borderradius}px;
		-webkit-border-radius: {$borderradius}px {$borderradius}px;
		border-radius: {$borderradius}px {$borderradius}px {$borderradius}px {$borderradius}px;
		width: {$width}{$widthtype};
		color: $textcolor!important;
		font-family: $tfont!important;
		font-size: $tsize!important;
		text-align: $talign!important;
	}
	#content $selector { margin-bottom: 1em; margin-top: 1em; } 
	.kws_input_fields {
		text-align: $talign;
	}
	$selector .cc_newsletter li {
		margin:.5em 0;
	}
	$selector .cc_newsletter ul label,
	$selector .cc_newsletter ul input {
		margin: 0;
		padding:0;
		line-height:1;
	}
	$selector .cc_intro, $selector .cc_intro * {
		font-family: $tfont;
		margin:0;
		padding:0;
		line-height:1;
		color: $textcolor;
	}
	$selector .cc_intro * { 
		padding: .5em 0;
		margin: 0;
	}
	$selector .cc_intro {
		padding-bottom:.5em;
	}
	$selector .kws_input_container { padding-top: {$lpad}em; }
	$selector label {
		text-align: $lalign;
		color: $labelcolor;
		font-size: {$lsize}px!important;
		font-family: $lfont;
		display:block;
	}
	$safesubscribecss
	$selector .submit { display:block; padding-top: {$lpad}px; $blockalign }
	$selector label.kws_bold { font-weight:bold; } label.kws_bold input { font-weight:normal; }
	$selector label.kws_italic { font-style:italic; } label.kws_italic input { text-style:normal; }
	
	.kws_clear { clear:both;}
	</style>
EOD;
		$css = str_replace("\n", ' ', $css);
		$css = str_replace("\r", ' ', $css);
		$css = str_replace("\t", ' ', $css);
		return $css;
	}

function printForm() {

	if(isset($_REQUEST['f']) && is_array($_REQUEST['f'])) {
		foreach($_REQUEST['f'] as $key => $field) {
			if(!isset($field['n'])) { unset($_REQUEST['f'][$key]); }
		}
	}
	if(isset($_REQUEST['output']) && $_REQUEST['output'] == 'html') {
		if(!isset($_REQUEST['form'])  && !isset($_REQUEST['cc-form-id'])) { 
			$output = '<!-- Constant Contact: The form you requested does not exist -->'; 
		} else {		
			if(isset($_REQUEST['formOnly']) && !empty($_REQUEST['formOnly'])) {
				$output = processForm(); 
			} else if(isset($_REQUEST['styleOnly']) && !empty($_REQUEST['styleOnly'])) { 
				$output = processStyle(); 
			} else { 
				$output = processStyle().processForm(); 
			}
		}
		if(isset($_REQUEST['echo']) && !empty($_REQUEST['echo'])) {
			echo $output;
			return;
		} else {
			return $output;
		}
	} else {
		if(isset($_REQUEST['changed']) && isset($_REQUEST['textOnly'])) {
			print json_encode(array('input' => processForm()));
		} elseif(isset($_REQUEST['textOnly'])) {
			print json_encode(array('form' => processForm()));
		} elseif(isset($_REQUEST['styleOnly'])) {
			print json_encode(array('css' => processStyle()));
		} else {
			print json_encode(array('css' => processStyle(), 'form' => processForm()));
		}
		return;
	}
}

printForm();

exit();
?>