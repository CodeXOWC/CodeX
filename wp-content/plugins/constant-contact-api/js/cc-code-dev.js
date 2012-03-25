jQuery.noConflict();

jQuery(document).ready(function($) {
	$('#examplewrapper').scrollFollow({
		speed: 10,
		offset: 30,
		container: 'nav-menus-frame',
		killSwitch: 'stopFollowingMe'
	});
 
	$('input.colorwell').each(function() { 
		var $input = $(this);
		var intcolor = '#000'; 
		$(this).ColorPicker({
			color: jQuery(this).val(),
			livePreview: false,
			onBeforeShow: function(colpkr) {
				return;
			},
			onShow: function (colpkr) {
				jQuery(colpkr).fadeIn(500);
				$('body').disableSelection();
				return false;
			},
			onHide: function (colpkr) {
				jQuery(colpkr).fadeOut(500);
				updateCode('style', $input, 'colorwell onHide');
				$('body').enableSelection();
				return false;
			},
			onLoad: function (hsb, hex, rgb) {
				if(hsb.b > 50) { intcolor = '#000'; } else { intcolor = '#fff'; }
				$input.val('#'+hex).css({"background-color": '#' + hex, "color": intcolor});
				return;
			},
			onChange: function (hsb, hex, rgb) {
				$('body').disableSelection();
				if(hsb.b > 50) { intcolor = '#000'; } else { intcolor = '#fff'; }
				$input.val('#'+hex).css({"background-color": '#' + hex, "color": intcolor}).trigger('colorchange');
				return;
			},
			onSubmit: function(hsb, hex, rgb, el) {
				$('body').enableSelection();
				updateCode('style', $input, 'colorwell onSubmit');
			}
		});
	});

$('textarea.tinymce').live('keyup change', function() {
	if($('.mceEditor').length > 0) { return; }
//	triggerTextUpdate();
	tinyMCE.execCommand('mceAddControl', false,'intro_default');
}).tinymce({
	// Location of TinyMCE script
	script_url : ScriptParams.path + 'tiny_mce/tiny_mce.js',
	theme : "advanced",
	plugins : "spellchecker,advhr,tabfocus",
	// Theme options - button# indicated the row# only
	theme_advanced_buttons1 : "bold,italic,|,justifyleft,justifycenter,justifyright,|,link,unlink,bullist,numlist,formatselect,|,code",
	theme_advanced_buttons2 : false,
	theme_advanced_buttons3 : false,	
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom", //(n.b. no trailing comma in last line of code)
	theme_advanced_resizing : true, //leave this out as there is an intermittent bug.
	inline_styles: true,
	tab_focus : ':prev,:next',
	setup: function(ed){
		ed.onKeyUp.add(triggerTextUpdate);
		ed.onChange.add(triggerTextUpdate);
	}
});


function triggerTextUpdate(inst) {
	if($('.kws_form .cc_intro').length > 0) {
		$('.kws_form .cc_intro').html($('#intro_default').html());
	}
	updateFormFields(true, $('#intro_default'), 'triggerTextUpdate');
}


// Tabbed interface
$("#tabs").tabs({ cookie: { expires: 60 } }).addClass('ui-helper-clearfix');
$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-left');
$("#tabs li li").removeClass('ui-corner-left');

$('.selectall, .selectall input').live('click', function() { $(this).select(); });



// Select dropdowns
$('#borderwidth').bind('change', function() { updateWidthCalculator(); });
$('#borderstyle').change(function() { updateBorderStyle(); });

$('#borderradius').bind('change', function() { updateBorderRadius(); });
$('#paddingwidth').bind('change', function() { /* updateBoxWidthandPadding(); */ });
$('#lpad').bind('change', function() { updateLabelPadding(); });
$('#gradheight').bind('change', function() { updateBackgroundType(); });

$('#lpad').bind('change', function() { 
	updateLabelPadding($('#lpad').val());
});

$('#size').change(function() { updateTextInputSize($('#size').val()); });
$('#tfont,#tsize,input[name=talign]').bind('click change', function() { 
	updateStyleAndColors();
});

// Doing this prevents us from having to do bind('click change'), which runs twice and slows things down.
if(jQuery.browser.msie6 || jQuery.browser.msie7) {
	$('#lfont,input[name="labelweight"],#lsize,input[name="lalign"]').bind('click', function() { updateStyleAndColors(); });
} else {
	$('#lfont,#lsize,input[name="lalign"]').bind('change', function() { updateLabelStyle(); });
}
$('#gradheight,#gradtype').bind('change', function() { updateBackgroundType(); });
$('#bordercolor').bind('colorchange', function() { updateBorderColor($(this).val()); });
$('#color2').bind('colorchange', function() { updateBackgroundType(); });
$('#tcolor').bind('colorchange', function() { updateTextColor($(this).val(), $(this).css('color')); });
$('#color6').bind('colorchange', function() { updateBackgroundType(); });
$('#lcolor').bind('colorchange', function() { updateLabelColor($(this).val()); });
$('#lpad').change(function() { updateLabelPadding($(this).val()); });

$('#presets').change(function() { 
	var x = window.confirm("Selecting a preset form design will overwrite all of your form customizations. Continue?");
	if (x)	{
		updatePresets();
	} else {
		return false;
	}
});

$('#bgpos,#bgrepeat').change(function() { updateBackgroundURL(); });

// Radio Buttons & Checkboxes
// Doing this prevents us from having to do bind('click change'), which runs twice and slows things down.
$('input:checkbox[name^="formfields"]').bind('change', function() { showHideFormFields($(this)); });
$('input[name=safesubscribe]').bind('change', function() { updateSafeSubscribe(); });
$('input[name="backgroundtype"]').bind('change', function() { updateBackgroundStyle(); updateBackgroundType(); });
$('input:checkbox[name^="f"]').bind('change', function() { updateFormFields(false, false, 'input:checkbox[f] bind'); });
$('input[id^=lus]').bind('change', function() { updateLabelSame(); });

$('label.labelStyle').bind('click', function() {
	if($('input[type=checkbox]:checked', $(this)).length > 0) { $(this).addClass('checked'); } else { $(this).removeClass('checked'); }
});


$('#form-fields ul.menu').bind('sortstop', function() { updateFormFields(false, false, 'formfields drop'); triggerTextUpdate(); });

// Text inputs
//$("#email_address_default").bind('change keyup', function() { updateEmailInput(); });
//$("#uid").bind('change keyup', function() { updateUID(); });
//$("#intro_default").bind('change keyup', function() { updateFormText($(this).val());});
$("input#bgimage").bind('change keyup', function() { updateBackgroundURL($(this).val()); /* updateCode('style'); */ });
$('input.labelValue,input.labelDefault', $('#form-fields')).live('change keyup', function() { 
	updateFormFields(true, $(this), 'labelDefault change keyup'); 
});

$("#defaultbuttontext,input[name=submitdisplay],input[name=submitposition]").bind('change keyup', function() { updateDefaultButton(); });

// Pattern selection
$("ul#patternList li").click(function(){ 
	updatePattern($(this));
});

$("#paddingwidth,.input input[name=widthtype],#width").bind('change keyup', function(e) { 
	if(eventKeys(e) || e.keyCode === 46 || e.keyCode === 8) { 
		// If it's not an arrow, tab, etc. and not delete or backspace, process the sucker!
		/* updateBoxWidthandPadding();   */
		updateWidthCalculator(); 
		$('#cc-form-settings').trigger('stylechange'); //updateCode('style');
	}
});
//$('input[name=widthtype]').click(function() { /* updateBoxWidthandPadding(); */ updateWidthCalculator(); /* updateCode('style'); */  });

	
	$('input.menu-save').live('click submit', function() {
		$('#examplewrapper').hide();
	});
	
	$('li.menu-item .item-edit').live('click', function(e) {
		e.preventDefault();
		$('.menu-item-settings', $(this).parents('li.formfield')).toggle()
		return false;
	});
	
	function updatePattern($clickedLI) {
		var val = '';
		if(empty($clickedLI)) {
			if($("ul#patternList li.selected").length > 0) {
				$clickedLI = $("ul#patternList li.selected");
				val = $clickedLI.attr('title'); 
			} else {				
				val = $('#patternurl').val(); 
				$clickedLI = $("ul#patternList li[title*="+val+"]");
			}
		}
		$("ul#patternList li").removeClass('selected'); 
		$clickedLI.addClass('selected'); 
		var url = $clickedLI.attr("title"); 
		$('#patternurl').val(url);
		
		updateBackgroundURL(url); 
		updateCode('style', $('#patternurl'), 'updatePattern');
	}

	function generateForm(textOnly, $changed) {
		var changedLink = '';
		var textOnlyLink = '';
		var styleOnly = false;
		var formFields = '&'+$('input,textarea,select',$('#form-fields')).serialize();
		var styleFields = '';
		$('#side-sortables div.inside').each(function() {
			if($(this).parents('#formfields_select').length === 0) {
				styleFields =  $('input,textarea,select,textarea', $(this)).serialize() + '&'+styleFields;
			}
		});
		if($changed && $($changed,$('.grabber')).length > 0) {	changedLink = '&changed='+$changed.attr('id'); }
		
		if(textOnly === 'style') {
			textOnlyLink = '&styleOnly='+textOnly;
			styleOnly = true;
			textOnly = false;
			formFields = false;
		} else if(textOnly) {	
			textOnlyLink = '&textOnly='+textOnly; 
		}
		var fullFormFields = $('form#cc-form-settings').serialize();
		
		var dataString = 'rand='+ScriptParams.rand+'&'+formFields+'&'+styleFields+textOnlyLink+changedLink+'&path='+ScriptParams.path; //+'&action=cc_get_form
		
		$.ajax({
			type: 'POST',
			url: ScriptParams.path + 'form.php', // ScriptParams.adminajax was too slow!
			processData: false,
			data:  dataString,
			success: function(data, textStatus, XMLHttpRequest){
				if(data) {
					var form = false;
					var css = false;
					var input = false;
					var pre = false;
					
					data = jQuery.parseJSON(data);
					
					// If we want to pass debug info, this works
					if(!empty(data.pre)) {
						$('body').prepend(data.pre);
					}
					
					if(!empty(data.input)) {
						input = $(data.input);
						
						if(input[0].length) {
							inputclass = input[1];	
						} else {
							inputclass = input;
						}
						var replaceClass = $(inputclass).attr('class').replace(' kws_input_container', '');
						$('.grabber .kws_form div.'+replaceClass).replaceWith($(input));
						return;
					}
					if(!empty(data.form)) {
						form = data.form; 
						if($('.grabber .kws_form').length > 0) {
							$('.grabber .kws_form').replaceWith(form);
						} else {
							$('.grabber').append(form);
						}
						if(textOnly) { return; }
					}
					if(!empty(data.css)) {
						css = data.css; 
						if($('.grabber style').length > 0) {
							$('.grabber style').addClass('remove').after(css);
							$('.grabber style.remove').remove();
						} else {
							$('.grabber').prepend(css);
						}
						if(styleOnly) { return; }
					}
					
				} else {
					$('.grabber').html('<p>Form generation error: something went wrong!</p>');
					return false;
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('.grabber').css('width','80%').css('margin','0 auto').css('text-align', 'left').html('<h2><em>Form Designer needs your help!</em></h2><h3>Your web server thinks that Form Designer is an unwelcome script. Obviously, it\'s not.</h3><p style="font-size:14px;">For Form Designer to work, you need to <strong>contact your web host</strong> and request that they "whitelist your domain for ModSecurity." This shouldn\'t pose any risk to your website; it just lets the host know you want the Form Designer to do what it does best!</p>');
				return false;
			},
			dataType: 'text'
		});
		
		return false;
		
	}
	
	// When we're changing multiple settings, we don't want to run the process each time.
	function unbindSettings() {
		$("#form-fields ul.menu input, #cc-form-settings .formfields textarea").unbind('change');
		$("#cc-form-settings input, #cc-form-settings textarea, #cc-form-settings select").not('#form-fields ul.menu input').not('#form-fields ul.menu textarea').unbind('change');
	}
	
	function bindSettings() {
		//unbindSettings();
		
		// For text only
		$("#form-fields ul.menu input, #cc-form-settings .inside textarea").bind('change', function() {
			updateFormFields(true, $(this), '#cc-form-settings text - '+$(this).attr('id')); 
		});
//		$("#settings:not(.formfields input,.formfields textarea)").bind('change', function() {  updateCode(false, false, '#settings'); });
		
		$("#cc-form-settings").bind('stylechange', function() {
			updateCode(false, false, 'stylechange trigger');
		});
		
		$("#cc-form-settings input, #cc-form-settings textarea, #cc-form-settings select")
		.not('.inside input')
		.not('.inside textarea')
		.bind('change',	
			function() {  
				updateFormFields('style', false, '#cc-form-settings style - '+$(this).attr('id')); 
			}
		);
	}
	
	function updatePresets(preset) {
		if(!preset) {
			preset = $('#presets').val();
		}
		
		//unbindSettings();
		
		switch(preset) {
				
			case 'Army':
				updateTextColor('#f2d99f','#000000');
				updateBorderColor('#400f0f','#FFFFFF');
				updateBorderRadius(6);
				updateBorderWidth(7);
				updateBorderStyle('solid');
				updateBackgroundStyle('gradient');
				updateBackgroundType('#498a2f','#472c0b', '#000000', '#FFFFFF', '', 100);
				updateSafeSubscribe('white');
				updateFormText("<h2>Receive our newsletter.</h2>\n<p>That&rsquo;s an order!</p>", true);
				updateTextStyle('courier', '24', 'bold');
				updateLabelStyle('courier', '16', 'bold');
				updateEmailInput('soldier@yourdivision.com');
				updateDefaultButton('Enlist');
				break;
			
			case 'Apple':
				updateTextColor('#333333','#000000');
				updateBorderColor('#ccc','#FFFFFF');
				updateBorderRadius(20);
				updateBorderWidth(6);
				updateBorderStyle('solid');
				updateBackgroundStyle('gradient');
				updateBackgroundType('#ffffff','#cfcfcf', '#cfcfcf', '#cfcfcf', '', 100);
				updateSafeSubscribe('gray');
				updateFormText("<h2>Newsletter signup:</h2>", true);
				updateTextStyle('helvetica', '24', 'bold');
				updateLabelStyle('helvetica', '16', 'bold');
				updateEmailInput('john.appleseed@apple.com');
				updateDefaultButton('iSignUp');
				break;
			
			case 'Jazz':
				updateTextStyle('palatino', '20', 'normal');
				updateLabelStyle('palatino', '16', 'normal');
				updateTextColor('#cfd1b3', '#000000');
				updateBorderColor('#FFFFFF','#000000');
				updateBorderRadius(9);
				updateBorderWidth(4);
				updateBorderStyle('solid');
				updateBackgroundStyle('gradient');
				updateBackgroundType('#595187','#000000', '#000000', '#FFFFFF', '', 100);
				updateFormText('<h2>Scratch below to catch our newsletter, daddy-o.</h2>', true);
				updateEmailInput('jazzlover@npr.org');
				updateDefaultButton('Yeah');
				updateSafeSubscribe('white');
				break;
				
			case 'Impact':
				updateTextStyle('impact', '30', 'normal');
				updateLabelStyle('impact', '20', 'normal');
				updateTextColor('#e61010', '#000000');
				updateBorderColor('#FFFFFF','#000000');
				updateBorderRadius(3);
				updateBorderWidth(10);
				updateBorderStyle('solid');
				updateBackgroundStyle('gradient');
				updateBackgroundType('#707070','#000000', '#FFFFFF', '#FFFFFF', '', 100);
				updateFormText('<h2>Our newsletter rocks!</h2><p>Get updates by email that will rock your world.</p>', true);
				updateEmailInput('uknowuwanna@signup.com');
				updateDefaultButton('ADD ME');
				break;
				
			case 'Barbie':
				updateTextStyle('comicsans', '24', 'bold');
				updateLabelStyle('comicsans', '16', 'bold');
				updateTextColor('#12748c', '#000000');
				updateBorderColor('#f5f7b4','#000000');
				updateBorderRadius(20);
				updateBorderWidth(6);
				updateBorderStyle('solid');
				updateBackgroundStyle('gradient');
				updateBackgroundType('#d911d9','#d7cde6', '#000000', '#000000', '', 100);
				updateFormText('<h2>Like, do you want updates?</h2><p>You should <em>totally</em> sign up for our newsletter below!</p>', true);
				updateEmailInput('have@fun.com');
				updateSafeSubscribe('white');
				updateDefaultButton('Totally!');
				break;
			
			case 'NYC':
				updateTextStyle('georgia', '24', 'normal');
				updateLabelStyle('georgia', '16', 'normal');
				updateTextColor('#000000', '#ffffff');
				updateBorderColor('#000000','#ffffff');
				updateBorderRadius(15);
				updateBorderWidth(6);
				updateBorderStyle('dashed');
				updateBackgroundStyle('gradient');
				updateBackgroundType('#ffffff','#f2fa05','#000000', '#000000', '', 100);
				updateFormText('<h2>Hey, ye gonna sign up or wat?</h2><p>Dis is our newslettah signup:</p>', true);
				updateEmailInput('take@thexpressway.com');
				updateSafeSubscribe('black');
				updateDefaultButton('Beep!');
				break;
				
			default:
				updateTextStyle('helvetica', '20', 'normal');
				updateLabelStyle('helvetica', '16', 'normal');
				updateTextColor('#accbf7', '#000000');
				updateBorderColor('#000000','#FFFFFF');
				updateBorderRadius(14);
				updateBackgroundType('#ad0c0c','#000001', '#000000', '#FFFFFF', '', 100);
				updateFormText('<h2>Sign up for Email Newsletters</h2>', true);
				updateEmailInput('signmeup@example.com');
				updateSafeSubscribe('gray');
				updateDefaultButton('Go');
				break;
		}
		//updateBackgroundType();
		//updateFormFields('style', false, 'updatePresets');
		
		//bindSettings();
	}
	
	function eventKeys(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code === 37 || code === 38 || code === 39 || code === 40 || code === 46 || code === 8 || code === 16) {
			return false;
		}else {
			return true;
		}
	}
		
	function updateCode(textOnly, $changed, from) {
		$('#codeSwapLink').remove();
		updateWidthCalculator();
		updateDisabled();
		generateForm(textOnly, $changed);
	}
	
	function updateBoxAlign(align) {
		if(empty(align)) {
			align = $('input[name=formalign]:checked').val();
		}
		
	}

	function updateBoxWidthandPadding(padding) {
		if(empty(padding)) {
			var paddingwidth = $('#paddingwidth').val() + 'px';
			$('.kws_form').css('padding', paddingwidth);
		} else {
			$('#paddingwidth').val(padding);
		}
		return;
	}
	
	function updateWidthCalculator() {
		if($('input[name=widthtype]:checked').val() === 'px') {
		var borderwidth = $('#borderwidth').val() * 2;
		var paddingwidth = $('#paddingwidth').val() * 2;
		var rawwidth = $('#width').val();
		var setwidth = Math.floor(rawwidth) - Math.floor(paddingwidth) - Math.floor(borderwidth);
		var realwidth = Math.floor(rawwidth) + Math.floor(paddingwidth) + Math.floor(borderwidth);
			$('#actualwidth').html('<div class="wrap"><p class="link-to-original"><strong>Actual width is '+ realwidth + 'px.</strong> <em>For an form that is '+rawwidth+'px wide, set Form Width to '+setwidth+'px</em><br />	Width: '+ $('#width').val()+ 'px<br />Padding: '+ paddingwidth + 'px ('+$('#paddingwidth').val() + 'px'+' * 2)<br />Border Width: '+  borderwidth + 'px ('+$('#borderwidth').val() + 'px * 2)<br /><span style="display:block; clear:both; border-top:1px solid #888; float:left; width:30%; margin-top:5px; padding-top:5px;">'+rawwidth+'px + '+paddingwidth+'px + '+borderwidth+'px = '+realwidth+'px</span></p></div>');
		} else {
			$('#actualwidth').html('');
		}
	}
	
	function updateInputSize($inputID, size) {
		$inputID.attr('size', size);
	}
		
	function mySorter(a,b){  
		return $(a).find("input.position").val() > $(b).find("input.position").val() ? 1 : -1;
	}
	
	function sortFieldMenu() {
		$('#form-fields ul.menu li.menu-item').sort(mySorter).appendTo('#form-fields ul.menu');
	}
	function showHideFormFields($clicked) {
		
		if(!$clicked) {
			$clicked = $('#formfields_select input:checkbox');
		}
		
		$clicked.each(function() {
				//$('#form-fields .menu li').has('#'+$(this).val()).find('input.checkbox').attr('checked', $(this).attr('checked'));
				var targetLI = $('#form-fields .menu li').has('#'+$(this).val());
				var checked = $(this).attr('checked');
				if(checked === true) {
					targetLI.remove().appendTo($('#form-fields .menu')).show()
					.find('input.checkbox').attr('checked', checked)
					.find('input').each(function() { $(this).attr('disabled', false); })
				} else {
					targetLI.hide()
					.find('input.checkbox').attr('checked', checked)
					.find('input').each(function() { $(this).attr('disabled', true); });
				}
		});

		// console.log($clicked);

		//$('#formfields_select').trigger('change');
	}
	
	function updateFormFields(textOnly, $changed, from) {
		/* console.log('textOnly: '+textOnly + ', changed: '+$changed +', from: '+ from); */
		if(empty(textOnly) || textOnly === 'style') {
			updateStyleAndColors();
		} 
		if(textOnly !== 'style') {
			if(empty($changed)) {
				$('ul.menu li.formfield').each(function() {
					updateFormField(textOnly, $(this));
				});
			} else {
				updateFormField(textOnly, $changed);
			}
		}
		
		updateCode(textOnly, $changed, 'updateFormFields');
	}
	
	function updateFormField(textOnly, $item) {
		// Get the <li> we're working within.
		if($item.not('li')) { $item = $item.parents('li.formfield'); }
		
		var checkbox = $('input.checkbox', $item);
		if(checkbox.attr('checked')) {
			$('.menu-item-settings', $item).show();
			
			// Set values
			check = new Object();
				check.id = checkbox.attr('id');
				check.val = checkbox.val();
				check.name = checkbox.attr('name');
				check.rel = checkbox.attr('rel');
			
			input = new Object();
				input.textarea = '';
				input.label = '';
				input.value = '';
				input.html = '';
				input.bold = '';
				input.italic = '';
				input.required = '';
				input.labelHTML = '';
				input.size = $('input.labelSize', $item).val();
							
			if($('input.labelValue', $item).length > 0) {
				var tempInput = $('input.labelValue', $item);	
				input.label = tempInput.val();
			}
						
			if(check.rel === 'textarea' || $item.hasClass('tinymce') || $('textarea.labelValue', $item).length > 0 || $('body.mceContentBody', $item).length > 0) { 
				input.textarea = true; 
			}
			
			// Update classes
			$item.addClass('checked').addClass('ui-state-active');
			
			// For textareas, we need to do it differently
			if($('input.labelDefault', $item).length > 0) {
				input.value = $('input.labelDefault', $item).val();
			} else {
				input.value = $('textarea.labelDefault', $item).html();
			}
			
			//alert(check.id);
			if($('#'+check.id+'_bold').is(':checked')) { input.bold = true; }
			if($('#'+check.id+'_italic').is(':checked')) { input.italic = true; }
			if($('#'+check.id+'_required').is(':checked')) { input.required = true; }
			
			//console.debug(input);
			if(check.rel === 'text') {
				input.html = '<input type="text" value="'+input.value+'" size="' + input.size + '" name="'+check.name+'" class="text" id="cc_'+check.id+'" />';
			}
			if(check.rel === 'button' || check.rel === 'submit') {
				input.html = '<input type="submit" value="'+input.value+'" name="'+check.name+'" id="cc_'+check.id+'" />';
			}
			if(check.rel === 'textarea') {
				input.html = $('<textarea>'+input.value+'</textarea>').attr('name', check.name).attr('id', check.id).attr('disabled', false);
			}
						
		} else { // If not checked
			//console.debug('Not checked');
			if(textOnly !== 'style' || empty(textOnly)) { // This might save some time?
				$item.removeClass('checked').removeClass('ui-state-active');
				$('.menu-item-settings',$item).hide();
			}
		}
	}
	
	function updateDisabled() {
		$('ul.menu li.formfield').each(function() {
			if($('input.checkbox', $(this)).attr("checked") === true) {
				$('input[type=hidden],li input, li textarea', $(this)).attr('disabled', false);
			} else {
				$('input[type=hidden],li input, li textarea', $(this)).attr('disabled', true);
			}
			$('input.position[type=hidden]', $(this)).val($('ul.menu li.formfield input.position').index($('input.position', $(this))));
		});
	}		
	
		
	$('#form-fields ul.menu').sortable({
		handle: '.menu-item-handle',
		forceHelperSize: true,
		placeholder: 'sortable-placeholder',
		start: function(e, ui) {
			var taHeight = $('textarea', $(this)).outerHeight();
			tinyMCE.execCommand('mceRemoveControl', false,'intro_default');
			$('textarea', $(this)).css('display','block').height(taHeight+'px').width('90%').attr('disabled', true);
			updateSharedVars( ui );
			$(this).disableSelection();
			$('body,#menu-to-edit').disableSelection();
		},
		change: function(e, ui) {
			if( ! ui.placeholder.parent().hasClass('menu') ) {
				(prev.length) ? prev.after( ui.placeholder ) : api.menuList.prepend( ui.placeholder );
			}
			updateSharedVars( ui );
		},
		update: function(e, ui) {   
			tinyMCE.execCommand('mceAddControl', false,'intro_default');
			$('textarea', $(this)).css('display','none').width('90%').attr('disabled', false);
			//updateAll();  
		},
		sort: function(e, ui) {
			updateSharedVars( ui );
		},
		stop: function(e, ui) {
			$('body,#menu-to-edit').enableSelection();
			$(this).enableSelection();
			generateForm(true, false, '.sortable');
			$('textarea', $(this)).attr('disabled', false);
		}
	});
	
	function updateSharedVars(ui) {
		var depth;

		prev = ui.placeholder.prev();
		next = ui.placeholder.next();

		// Make sure we don't select the moving item.
		if( prev[0] === ui.item[0] ) prev = prev.prev();
		if( next[0] === ui.item[0] ) next = next.next();

	}
	
	function updateFormText(defaultformtext, preset) {
		if((preset && $('#pupt').is(':checked')) || (empty(preset) && !empty(defaultformtext))) {
			$('#intro_default').html(defaultformtext);
		}
		return;
	}
	
	function updateDefaultButton(defaultbuttontext) {
		$('#Go_default').val(defaultbuttontext);
	}
	
	function updateEmailInput(defaulttext) {
		if(!defaulttext) {
			var defaulttext = $("#email_address_default").val();
		}
		$('#ea').val(defaulttext).attr('defaultValue', defaulttext);
		$('#email_address_default').val(defaulttext);
	}
	
	function updateLabelStyle(textfont,textsize,fontweight,textpadding,textalign) {
		if(!textfont) { textfont = $('input[name="lfont"]').val();}
		if(!textsize) { textsize = $('input[name="lsize"]').val();}
		if(!fontweight){ fontweight = ''; /* $('input[name="labelweight"]').val(); */ }
		if(!textpadding){ textpadding = $('input[name="lpad"]').val();}
		if(!textalign){ textalign = $('input[name="lalign"]').val();}
		//console.debug('label align: '+textalign);
		updateTextStyle(textfont,textsize,fontweight,textpadding,textalign,'l');
	}
	
	function updateTextStyle(textfont,textsize,fontweight,textpadding,textalign,prefix) {
		if(!prefix) { var prefix = 't';}
		if(!textfont) {
			var textfont = $('#'+prefix+'font').val();
		} else {
			var textfont = $('select[name="'+prefix+'font"] option[id="'+textfont+'"]').val();
			$('select[name="'+prefix+'font"] option[value="'+textfont+'"]').attr('selected','selected');
		}
		textfont = findFont(textfont);
		if(!textsize) {	var textsize = $('#'+prefix+'size').val(); } else {
			$('select#'+prefix+'size option[value="'+textsize+'"]').attr('selected','selected');
		}
		
		if(!textalign) {
			var textalign = $('input[name="'+prefix+'align"]').val();
		}
		
		if(!fontweight){
			var fontweight = $('input[name="'+prefix+'weight"]').val();
		} else {
			$('input[name="'+prefix+'weight"][value="'+fontweight+'"]').attr('checked','checked');
		}
		if($('#textfont option:selected:contains("*")').length > 0) { 
			$('#'+prefix+'options .asterix').show();
		} else {
			$('#'+prefix+'options .asterix').hide();
		}
	}
	
	
	function findFont(id) {
		switch(id) {
		
			case 'times':
				return "'Times New Roman', Times, Georgia, serif";
			case 'georgia':
				return "Georgia,'Times New Roman', Times, serif";
			case 'palatino':
				return "'Palatino Linotype', Palatino, 'Book Antiqua',Garamond, Bookman, 'Times New Roman', Times, Georgia, serif";
			case 'garamond':
				return "Garamond,'Palatino Linotype', Palatino, Bookman, 'Book Antiqua', 'Times New Roman', Times, Georgia, serif";
			case 'bookman':
				return "Bookman,'Palatino Linotype', Palatino, Garamond, 'Book Antiqua','Times New Roman', Times, Georgia, serif";
			case 'helvetica':
				return "'Helvetica Neue',HelveticaNeue, Helvetica, Arial, Geneva, sans-serif";
			case 'arial':
				return "Arial, Helvetica, sans-serif";
			case 'lucida':
				return "'Lucida Grande', 'LucidaGrande', 'Lucida Sans Unicode', Lucida, Verdana, sans-serif";
			case 'verdana':
				return "Verdana, 'Lucida Grande', Lucida, TrebuchetMS, 'Trebuchet MS',Geneva, Helvetica, Arial, sans-serif";
			case 'trebuchet':
				return "'Trebuchet MS', Trebuchet, Verdana, sans-serif";
			case 'tahoma':
				return "Tahoma, Verdana, Arial, sans-serif";
			case 'franklin':
				return "'Franklin Gothic Medium','FranklinGotITC','Arial Narrow Bold',Arial,sans-serif";
			case 'impact':
				return "Impact, Chicago, 'Arial Black', sans-serif";
			case 'arialblack':
				return "'Arial Black',Impact, Arial, sans-serif";
			case 'gillsans':
				return "'Gill Sans','Gill Sans MT', 'Trebuchet MS', Trebuchet, Verdana, sans-serif";
			case 'courier':
				return "'Courier New', Courier, Monaco, monospace";
			case 'lucidaconsole':
				return "'Lucida Console', Monaco, 'Courier New', Courier, monospace";
			case 'comicsans':
				return "'Comic Sans MS','Comic Sans', Sand, 'Trebuchet MS', cursive";
			case 'papyrus':
				return "Papyrus,'Palatino Linotype', Palatino, Bookman, fantasy";
		}
	}
	
	function updateTextInputSize(textinputsize) {
		if(textinputsize != 0) {
			$('#defaulttext, label[for=defaulttext], li.defaulttext').show();
			if($('.grabber .kws_form input[type=text]').length > 0) {
				$('.grabber .kws_form input[type=text]').attr('size',textinputsize);
			}
		}
	}
	
	function updateBorderStyle(borderstyle) {
		if(!borderstyle || borderstyle === '') {
			var borderstyle =  $('#borderstyle option:selected').val();
		} else {
			 $('#borderstyle option[value='+borderstyle+']').attr('selected','selected');
		}
		
//		$('#kwd-constant-contact').css('background-clip','border-box').css("border-style", borderstyle);
	}
	function updateBorderWidth(borderwidth) {
		if(!empty(borderwidth)) {
			$('#borderwidth').val(borderwidth);
			return;
		}
		$('.kws_form').css({'border-width':$('#borderwidth').val()+'px'});
		return;
	}
	
	$('#menu-settings-column').bind('click', function(e) {
		var selectAreaMatch, panelId, wrapper, items,
			target = $(e.target);
		if ( target.hasClass('nav-tab-link') ) {
			panelId = /#(.*)$/.exec(e.target.href);
			if ( panelId && panelId[1] )
				panelId = panelId[1]
			else
				return false;
	
			wrapper = target.parents('.inside').first();
	
			// upon changing tabs, we want to uncheck all checkboxes
			//$('input', wrapper).removeAttr('checked');
	
			$('.tabs-panel-active', wrapper).removeClass('tabs-panel-active').addClass('tabs-panel-inactive');
			$('#' + panelId, wrapper).removeClass('tabs-panel-inactive').addClass('tabs-panel-active');
	
			$('.tabs', wrapper).removeClass('tabs');
			target.parent().addClass('tabs');
	
			return false;
		} else if ( target.hasClass('select-all') ) {
			selectAreaMatch = /#(.*)$/.exec(e.target.href);
			if ( selectAreaMatch && selectAreaMatch[1] ) {
				items = $('#' + selectAreaMatch[1] + ' .tabs-panel-active .menu-item-title input');
				if( items.length === items.filter(':checked').length )
					items.removeAttr('checked');
				else
					items.attr('checked', 'checked');
				return false;
			}
		} else if ( target.hasClass('submit-add-to-menu') ) {
			api.registerChange();
	
			if ( e.target.id && -1 != e.target.id.indexOf('submit-') )
				$('#' + e.target.id.replace(/submit-/, '')).addSelectedToMenu( api.addMenuItemToBottom );
			return false;
		} else if ( target.hasClass('page-numbers') ) {
			$.post( ajaxurl, e.target.href.replace(/.*\?/, '').replace(/action=([^&]*)/, '') + '&action=menu-get-metabox',
				function( resp ) {
					if ( -1 === resp.indexOf('replace-id') )
						return;
	
					var metaBoxData = $.parseJSON(resp),
					toReplace = document.getElementById(metaBoxData['replace-id']),
					placeholder = document.createElement('div'),
					wrap = document.createElement('div');
	
					if ( ! metaBoxData['markup'] || ! toReplace )
						return;
	
					wrap.innerHTML = metaBoxData['markup'] ? metaBoxData['markup'] : '';
	
					toReplace.parentNode.insertBefore( placeholder, toReplace );
					placeholder.parentNode.removeChild( toReplace );
	
					placeholder.parentNode.insertBefore( wrap, placeholder );
	
					placeholder.parentNode.removeChild( placeholder );
	
				}
			);
	
			return false;
		}
	});
	
	function updateBackgroundColor(bordercolor, color2,textbordercolor,textcolor2) {
		if(empty(bordercolor)) { var bordercolor = $('#color6').val().replace(/#/g,''); }
		bordercolor.replace(/#/g,'');
		$('#color6').val('#'+bordercolor).css('background-color','#'+bordercolor).ColorPickerSetColor(bordercolor);
		
		if(empty(color2)) { var color2 = $('#color2').val(); }
		
		$('#color2').val(color2).css('background-color',color2).ColorPickerSetColor(color2);
		
		if(empty(textbordercolor)) { $('#color6').css('color',textbordercolor); }
		if(empty(textcolor2)) { $('#color2').css('color',textcolor2); }
		
		if($("input[name=backgroundtype]").val() === 'gradient') {
			$('#safesubscribelightimg,#safesubscribedarkimg,#safesubscribeblackimg').css("background-color", color2).css("background-image", 'none');
		} else {
			$('#safesubscribelightimg,#safesubscribedarkimg,#safesubscribeblackimg').css("background-color", color2).css("background-image", 'none');
		}
	}
	function updateBackgroundStyle(style) {
		if(style) {
			$('input[name=backgroundtype][value='+style+']').attr('checked','checked').parents('label').click();
			//updateCode('style', false, 'updateBackgroundStyle');
		}
		return;
	}
	function updateBackgroundType(bordercolor,color2,textbordercolor,textcolor2,url, height, gradtype){
		if(empty(gradtype)) { gradtype = $('#gradtype').val(); }
		var selection = $("input[name=backgroundtype]:checked").val();
		if(selection === 'gradient') {
			if(empty(height)) { height = $('#gradheight').val(); }
//			console.debug('gradient');
			$("#bgtop,#gradheightli,#gradtypeli").show();
			$("#bgpattern,#bgurl").hide();
			if($('#gradtype').val() === 'vertical') { 
				$("#bgbottom label").text('Bottom Color:');
				$("#bgtop label").text('Top Color:');
				$("#gradheightli label span").text('Gradient Height:');
			} else {
				$("#bgbottom label").text('Right Color:');
				$("#bgtop label").text('Left Color:');
				$("#gradheightli label span").text('Gradient Width:');
			}
			$('#patternurl,#bgimage').attr('disabled', true);
			$('#color2,#gradheight,#gradwidth,#gradtype,#bgrepeat,#bgpos').attr('disabled', false);
			updateBackgroundColor(bordercolor,color2,textbordercolor,textcolor2);
			updateGradient(bordercolor,color2,textbordercolor,textcolor2,url,height,gradtype);
		} else if(selection === 'solid') {
			//console.debug('solid');
				updateBackgroundColor(bordercolor,color2,textbordercolor,textcolor2);
			$("#bgtop,#gradheightli,#gradtypeli,#bgpattern,#bgurl").hide();
			$("#bgbottom").show();
			$("#bgbottom label").text('Background Color:');
			$('#patternurl,#bgimage,#gradwidth,#gradtype,#gradheight,#bgrepeat,#bgpos').attr('disabled', true);
			$('#color2').attr('disabled', false);
		} else if(selection === 'pattern') {
			$("#bgtop,#gradheightli,#gradtypeli,#bgbottom,#bgurl").hide();
			$("#bgpattern").show();
			$('#color2,#bgimage').attr('disabled', true);
			$('#patternurl,#bgrepeat,#bgpos').attr('disabled', false);
			
			// If the saved input has a value, use it
			if($('#patternurl').val() != '') { bgTitle = $('#patternurl').val();}
			else if($("#bgpattern ul li.selected").length > 0) { var bgTitle = $("#bgpattern ul li.selected").attr('title'); }
			else { var bgTitle = $("#bgpattern ul li:first").attr('title'); }
				updatePattern();
				updateBackgroundURL(bgTitle, 'transparent', 'repeat');
		} else if(selection === 'url') {
			//console.debug('url');
			$('#patternurl').attr('disabled', true);
			$('#color2,#bgimage,#bgrepeat,#bgpos').attr('disabled', false);
			
			$("#bgtop,#gradheightli,#bgpattern").hide();
			$("#bgurl,#bgbottom").show();
			$("#bgbottom label").text('Background Color:');
			updateBackgroundURL();
		}
		updateCode('style');
		// alert('c1: '+typeof(bordercolor) + ', c2: '+typeof(color2) + ', tc1: '+typeof(textbordercolor) + ', tc2: '+typeof(textcolor2));
	}
	
	
	function updateBackgroundURL(url, color, repeat, position) {
		if(!repeat) { var repeat = $('#bgrepeat').val();}
		if(!url || url === 'undefined') {
			//console.debug('we are undefined');
			var url = $('input#bgimage').val(); 
		} 
		if(url === '' || url === 'http://') { url = ''; } else { url = 'url('+url+')'; }
		
		if(!color) { var color = $('#color2').val(); }
		if(!position) { var position = $('#bgpos').val(); }
		if($('#backgroundurl').is(':checked')) { 
			$('.kws_form').css("background", color+' '+url+' '+position+' '+repeat);
		}
	}
	function ajaxGradient(bordercolor, color2) {
		
		
		$.ajax({
		  type: "POST",
		  url: ScriptParams.path + 'ozhgradient.php',
		  dataType: "text",
		  data: 'start='+bordercolor+'&end='+color2+'&height='+$('#gradheight').val(),
		  async: true,
		  error: function() { /* console.error('error generating gradient'); */ return false; },
		  success: function(msg){
			var getImage = msg;
//			var bgRule = '#'+color2+' url(gradients/'+getImage+') left top repeat-x';
			var bgRule = '#'+color2+' url('+getImage+') left top repeat-x';
//			updateCode('style', false, 'ajaxGradient');
			$('#gradtype').trigger('stylechange');
			return true;
//			 $('.kws_form').css('background', bgRule);
		   }
		});
	}
	
	function updateGradient(bordercolor,color2,textbordercolor,textcolor2,url,gradheight, gradtype) {
		if(bordercolor === '1') { return false; }
			if(!bordercolor) {
				var bordercolor = $('#color6').val().replace(/#+?/g,'');	
			} else {
				$('#color6').val(bordercolor).css('background-color',bordercolor).ColorPickerSetColor(bordercolor);
			}
			if(!color2) {
				var color2 = $('#color2').val().replace(/#+?/g,'');	
			} else {
				$('#color2').val(color2).css('background-color',color2).ColorPickerSetColor(color2);
			}
			if(textbordercolor) {
				$('#color6').css('color',textbordercolor);
			}
			if(textcolor2) {
				$('#color2').css('color',textcolor2);
			}
			//console.debug('in updateGradient. typeof(gradheight) is '+typeof(gradheight));
			if(empty(gradheight) || typeof(gradheight) === 'object') {
				gradheight = $('#gradheight').val();
			} else {
				$('#gradheight').val(gradheight);
			}
			if(empty(gradtype)) {
				gradtype = $('#gradtype').val();
			} else {
				$('#gradtype').val(gradtype);
			}
			if($('#gradtype').val() === 'vertical') {
				gradwidth = 1;
				$('#gradwidth').val(1);
			} else {	
				gradwidth =$('.kws_form').width();
				$('#gradwidth').val(gradwidth);
			}
		
//		$('.kws_form').css('background', '#'+color2+' url(ozhgradient.php?start='+bordercolor+'&end='+color2+'&height='+gradheight+'&type='+gradtype+'&width='+gradwidth+') left top repeat-x')
		
		if(!empty($('.kws_form').css('backgroundColor')) && rgb2hex($('.kws_form').css('backgroundColor')) != color2) { 
//			updateBackgroundType(bordercolor,color2,textbordercolor,textcolor2,url,gradheight);
//			updateBackgroundType(bordercolor,color2,textbordercolor,textcolor2);
			//updateGradient(); 
			//updateCode('style');
//			console.debug('Before Refresh: CSS: '+rgb2hex($('.kws_form').css('backgroundColor'))+' / '+$('#color2').val()); 
//			updateBackgroundType(bordercolor,color2,textbordercolor,textcolor2,url,gradheight);
			ajaxGradient(bordercolor, color2);
//			console.debug('After Refresh: CSS: '+rgb2hex($('.kws_form').css('backgroundColor'))+' / '+$('#color2').val()); 
			
			//updateGradient(); 
		} else {
			ajaxGradient(bordercolor, color2);
			return;
		}
	}
	function updateSafeSubscribe(safesubscribe) {
		if(!empty(safesubscribe)) {
			if(safesubscribe === 'white') { safesubscribe = 'dark'; }
			if(safesubscribe === 'gray' || safesubscribe === 'grey') { safesubscribe = 'light'; }
			$('input[name="safesubscribe"][value='+safesubscribe+']').click().attr('checked', true);
		}
		else { 
			$('#cc-form-settings').trigger('stylechange');
		}
	}
	
	
	function updateExampleWrapper(examplebgcolor){ 
		$('#examplewrapper').css('background-color', examplebgcolor);
	}
		
	function updateLabelSame() {
		
		// Same Color
		if($('input#lusc').is(':checked')) {
			$('#labelcolorli').hide();
			$('#labelcolorli input').attr('disabled', true);
		} else {
			$('#labelcolorli').show();
			$('#labelcolorli input').attr('disabled', false);
		}
		
		// Same Font
		if($('input#lusf').is(':checked')) {
			$('#lfontli').hide();
			$('#lfont').val($('#tfont').val());
		} else {
			$('#lfontli').show();
		}
		
		//updateStyleAndColors();
		//updateLabelStyle();
		//updateTextStyle();
		//updateLabelStyle();
	}
	
/*
	$('input#lusp').bind('change', function() { updateLabelPaddingHide(); });
	$('input#labelsusesamealign').bind('change', function() { updateLabelAlignHide(); });
	$('input#labelsusesamefont').bind('change', function() { updateLabelFontHide(); });
*/
	
	function updateLabelColor(color) {
		if(!color) {
			color = $('#lcolor').val();	
		} else {
			$('#lcolor').val(color).css('background-color',color).ColorPickerSetColor(color);
			$('.kws_form .kws_input_container label').css('color', color);
		}
	}
		
	
	function updateLabelPadding(val) {
		if(empty(val)) {
			val = $('#lpad').val(); 
		} 
		$('.kws_form .kws_input_container label').css("padding-top",val+'em');
	}
	
/*
	function updateTextPadding(val) {
		if(val) {
			$('.kws_form div.cc_intro').css("padding-bottom",val+'px');
			return;
		}
		var rawtextpaddingwidth = $('#tpad').val(); 
		var textpaddingwidth = $('#tpad').val() + 'px'; 
		
		if($('#lusp').is(":checked")) {
			updateLabelPadding(rawtextpaddingwidth);
		}
		
		$('.kws_form div.cc_intro').css("padding-bottom",textpaddingwidth);
	}
*/
	
	function updateTextColor(color,textcolor) {
		if(!color) {
			color = $('#tcolor').val();	
		} 
		$('#tcolor').val(color).css('background-color',color).ColorPickerSetColor(color);
		
//		$('.kws_form .cc_intro').css('color', color);
		
		if(textcolor) {
			$('#tcolor').css('color',textcolor);
		}
		
		if($('#lusc').is(":checked")) {
			updateLabelColor(color); 
			if(textcolor) { $('#lcolor').css('color', textcolor); } 
		}
	}
	function updateBorderRadius(borderradius) {
		if(!empty(borderradius)) {
			$("#borderradius").val(borderradius);
			return;
		}
		$('.kws_form').css({
			'border-radius':$('#borderradius').val()+'px '+ $('#borderradius').val()+'px',
			'-moz-border-radius':$('#borderradius').val()+'px '+ $('#borderradius').val()+'px',
			'-webkit-border-radius':$('#borderradius').val()+'px '+ $('#borderradius').val()+'px'
		});
		return;
	}
	function updateBorderColor(bordercolor,textcolor) {
		if(!bordercolor) {
			bordercolor = $('#bordercolor').val();	
		}
		
		$('#bordercolor').val(bordercolor).css('background-color',bordercolor).ColorPickerSetColor(bordercolor);
		$('.kws_form').css('border-color', bordercolor)
		
		if(textcolor) {
			$('#bordercolor').css('color',textcolor);
		}
	};
	
function setPatterns() {
	$('#patternList li').each(function() {
		$(this).css('background', 'url('+ScriptParams.path+$(this).attr('title')+') left top repeat');
	});
	return;
}

function updateStyleAndColors() {
	updateBackgroundType();
	updateTextColor();
	updateLabelColor();
	updateTextStyle();
	updateLabelStyle();
	updateLabelPadding();
	updateLabelSame();
	updateBorderColor();
	updateBorderStyle();
}	

function updateAll() {
	setPatterns();
	sortFieldMenu();
	updateTextInputSize($('#size').val());
	updateStyleAndColors();
	showHideFormFields();
	sortFieldMenu();
	updateFormFields(false, false, 'updateAll');
}

updateAll();
bindSettings();



// processStyle();
$('form label.error').hide();

   $('label img').click(function(){
		$(this).closest('input[type=radio]').click();
   });
   
   
	$("a.toggleMore").live('click', function() { 
		$(this).parents('ul').find('.toggleMore:not(a):not(:has(input[type=checkbox]:checked))').toggle('fast'); 
		
		var text = $(this).text();
		var text2 = text.replace('Show', 'Hide');
		if(text2 === text) {$(this).text(text.replace('Hide', 'Show'))} else { $(this).text(text2); }
		return false; 
	});
   
   jQuery('.toggleMore:not(a)').hide();
		
});

	jQuery.fn.clearText = function() {
	return this.focus(function() {
		if( this.value === this.defaultValue ) {
			this.value = "";
		}
	}).blur(function() {
		if( !this.value.length ) {
			this.value = this.defaultValue;
		}
		});
	};
	
	jQuery.fn.processColor = function(black, sat) {
	if(sat > 40) { black = black - 10;}
	
	if(black < 25) {
		jQuery(this).css('color', 'white');
	} else if(black < 50) {
		jQuery(this).css('color', '#cccccc');
	} else if(black < 60) {
		jQuery(this).css('color', '#333333');
	} else {
		jQuery(this).css('color', 'black');
	}
	
	};
	
	(function (jQuery) {
		
		jQuery.fn.vAlign = function() {
			return this.each(function(i){
			var ah = jQuery(this).height();
			var ph = jQuery(this).parent().height();
			var mh = (ph - ah) / 2;
			jQuery(this).css('margin-top', mh);
			});
		};
	})(jQuery);
	
	(function (jQuery) {
		
		jQuery.fn.hAlign = function() {
			return this.each(function(i){
			var ah = jQuery(this).width();
			var ph = jQuery(this).parent().width();
			var mh = (ph - ah) / 2;
			jQuery(this).css('padding-left', mh);
			});
		};
	})(jQuery);
	
	(function(jQuery) {
  jQuery.fn.stripHtml = function() {
	var regexp = /<("[^"]*"|'[^']*'|[^'">])*>/gi;
	this.each(function() {
		jQuery(this).html(
			jQuery(this).html().replace(regexp,"")
		);
	});
	return jQuery(this);
  }
	})(jQuery);
function empty (mixed_var) {
	// http://kevin.vanzonneveld.net
		
	var key;
	
	if (mixed_var === "" ||
		mixed_var === 0 ||
		mixed_var === "0" ||
		mixed_var === null ||
		mixed_var === false ||
		typeof mixed_var === 'undefined'
	){
		return true;
	}

	if (typeof mixed_var === 'object') {
		for (key in mixed_var) {
			return false;
		}
		return true;
	}

	return false;
}

/*
function
if(typeof window.console!="undefined"&&typeof console.log==="function"){
	console.log(msg);
}else{alert(msg)}}
*/

function rgb2hex(rgb) {
	rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
	function hex(x) {
		return ("0" + parseInt(x).toString(16)).slice(-2);
	}
	return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}


jQuery.fn.sort = function() {  
	return this.pushStack( [].sort.apply( this, arguments ), []);  
};