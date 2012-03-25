<?php
  /* 
   * This is a modified copy of Alex King's JS QuickTags. 
   * 30/10/2007 by Mark Cunningham (http://thedeadone.net)
   */

  /* 
   * Mods:
   * - Wrapped in PHP (to enable integration and other modifications)
   * - Can restrict, dynamically, what tags are supported 
   * - Can supply a "postfix" so that it can be used with multiple textareas 
   */
   
  # Tags to restrict
  $restrict_tags = false;
  if(isset($_REQUEST['allowed_tags'])) {
   $restrict_tags = true;
   $allowed_tags = strtolower($_REQUEST['allowed_tags']);
  }
  
  # Postfix to enable multiple instances on the same page
  $postfix = '';
  if(isset($_REQUEST['postfix'])){
    $postfix = $_REQUEST['postfix'];
  }
  
  /* TODO: some way to easily add their own tags, either via widget or UI */
?>
// JS QuickTags version 1.2
//
// Copyright (c) 2002-2005 Alex King
// http://www.alexking.org/
//
// Licensed under the LGPL license
// http://www.gnu.org/copyleft/lesser.html
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
//
// This JavaScript will insert the tags below at the cursor position in IE and
// Gecko-based browsers (Mozilla, Camino, Firefox, Netscape). For browsers that
// do not support inserting at the cursor position (Safari, OmniWeb) it appends
// the tags to the end of the content.
//
// The variable 'edCanvas' must be defined as the <textarea> element you want
// to be editing in. See the accompanying 'index.html' page for an example.

var edButtons<?php echo $postfix; ?> = new Array();
var edLinks<?php echo $postfix; ?> = new Array();
var edOpenTags<?php echo $postfix; ?> = new Array();

function edButton<?php echo $postfix; ?>(id, display, tagStart, tagEnd, access, open) {
	this.id = id;				// used to name the toolbar button
	this.display = display;		// label on button
	this.tagStart = tagStart; 	// open tag
	this.tagEnd = tagEnd;		// close tag
	this.access = access;			// set to -1 if tag does not need to be closed
	this.open = open;			// set to -1 if tag does not need to be closed
}

<?php if(!$restrict_tags || substr_count($allowed_tags,"<strong>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_bold'
		,'Bold'
		,'<strong>'
		,'</strong>'
		,'b'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<em>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_italic'
		,'Italics'
		,'<em>'
		,'</em>'
		,'i'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<a>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_link'
		,'Link'
		,''
		,'</a>'
		,'a'
	)
); // special case

edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_ext_link'
		,'Ext. Link'
		,''
		,'</a>'
		,'e'
	)
); // special case
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<img>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_img'
		,'Ext. Image'
		,''
		,''
		,'m'
		,-1
	)
); // special case
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<ul>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_ul'
		,'List'
		,'<ul>\n'
		,'</ul>\n\n'
		,'u'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<ol>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_ol'
		,'Numbered List'
		,'<ol>\n'
		,'</ol>\n\n'
		,'o'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<li>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_li'
		,'List Item'
		,'\t<li>'
		,'</li>\n'
		,'l'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<blockquote>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_block'
		,'Quote'
		,'<blockquote>'
		,'</blockquote>'
		,'q'
	)
);
<?php } ?>

var extendedStart<?php echo $postfix; ?> = edButtons<?php echo $postfix; ?>.length;

// below here are the extended buttons

<?php if(!$restrict_tags || substr_count($allowed_tags,"<h1>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_h1'
		,'Heading 1'
		,'<h1>'
		,'</h1>\n\n'
		,'1'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<h2>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_h2'
		,'Heading 2'
		,'<h2>'
		,'</h2>\n\n'
		,'2'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<h3>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_h3'
		,'Heading 3'
		,'<h3>'
		,'</h3>\n\n'
		,'3'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<h4>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_h4'
		,'Heading 4'
		,'<h4>'
		,'</h4>\n\n'
		,'4'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<p>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_p'
		,'Paragraph'
		,'<p>'
		,'</p>\n\n'
		,'p'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<code>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_code'
		,'Code'
		,'<code>'
		,'</code>'
		,'c'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<pre>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_pre'
		,'No formatting'
		,'<pre>'
		,'</pre>'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<dl>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_dl'
		,'Def. List'
		,'<dl>\n'
		,'</dl>\n\n'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<dt>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_dt'
		,'Def. List Item Title'
		,'\t<dt>'
		,'</dt>\n'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<dd>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_dd'
		,'Def. List Item Desc.'
		,'\t<dd>'
		,'</dd>\n'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<table>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_table'
		,'Table'
		,'<table>\n<tbody>'
		,'</tbody>\n</table>\n'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<tr>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_tr'
		,'Table Row'
		,'\t<tr>\n'
		,'\n\t</tr>\n'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<td>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_td'
		,'Table Col.'
		,'\t\t<td>'
		,'</td>\n'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<u>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_under'
		,'Underline'
		,'<u>'
		,'</u>'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<s>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_strike'
		,'Strikethrough'
		,'<s>'
		,'</s>'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<nobr>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_nobr'
		,'No breaks'
		,'<nobr>'
		,'</nobr>'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<center>") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_center'
		,'Center'
		,'<center>'
		,'</center>'
	)
);
<?php } ?>

<?php if(!$restrict_tags || substr_count($allowed_tags,"<!--more-->") >= 1) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_more'
		,'Read More...'
		,'<!--more-->'
		,''
	)
);
<?php } ?>

<?php if(!$restrict_tags 
      || (substr_count($allowed_tags,"<ol>")  >= 1
       && substr_count($allowed_tags,"<li>")  >= 1
       && substr_count($allowed_tags,"<sup>") >= 1
       && substr_count($allowed_tags,"<a>")   >= 1)) { ?>
edButtons<?php echo $postfix; ?>.push(
	new edButton<?php echo $postfix; ?>(
		'ed_footnote'
		,'Footnote'
		,''
		,''
		,'f'
	)
);
<?php } ?>

function edLink<?php echo $postfix; ?>(display, URL, newWin) {
	this.display = display;
	this.URL = URL;
	if (!newWin) {
		newWin = 0;
	}
	this.newWin = newWin;
}


edLinks<?php echo $postfix; ?>[edLinks<?php echo $postfix; ?>.length] = new edLink<?php echo $postfix; ?>('alexking.org'
                                    ,'http://www.alexking.org/'
                                    );

function edShowButton<?php echo $postfix; ?>(button, i) {
	if (button.access) {
		var accesskey = ' accesskey = "' + button.access + '"'
	}
	else {
		var accesskey = '';
	}
	switch (button.id) {
		case 'ed_img':
			document.write('<input type="button" id="' + button.id + '" ' + accesskey + ' class="ed_button" onclick="edInsertImage<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>);" value="' + button.display + '" />');
			break;
		case 'ed_link':
			document.write('<input type="button" id="' + button.id + '" ' + accesskey + ' class="ed_button" onclick="edInsertLink<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>, ' + i + ');" value="' + button.display + '" />');
			break;
		case 'ed_ext_link':
			document.write('<input type="button" id="' + button.id + '" ' + accesskey + ' class="ed_button" onclick="edInsertExtLink<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>, ' + i + ');" value="' + button.display + '" />');
			break;
		case 'ed_footnote':
			document.write('<input type="button" id="' + button.id + '" ' + accesskey + ' class="ed_button" onclick="edInsertFootnote<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>);" value="' + button.display + '" />');
			break;
		default:
			document.write('<input type="button" id="' + button.id + '" ' + accesskey + ' class="ed_button" onclick="edInsertTag<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>, ' + i + ');" value="' + button.display + '"  />');
			break;
	}
}

function edShowLinks<?php echo $postfix; ?>() {
	var tempStr = '<select onchange="edQuickLink<?php echo $postfix; ?>(this.options[this.selectedIndex].value, this);"><option value="-1" selected>(Quick Links)</option>';
	for (i = 0; i < edLinks.length; i++) {
		tempStr += '<option value="' + i + '">' + edLinks[i].display + '</option>';
	}
	tempStr += '</select>';
	document.write(tempStr);
}

function edAddTag<?php echo $postfix; ?>(button) {
	if (edButtons<?php echo $postfix; ?>[button].tagEnd != '') {
		edOpenTags<?php echo $postfix; ?>[edOpenTags<?php echo $postfix; ?>.length] = button;
		document.getElementById(edButtons<?php echo $postfix; ?>[button].id).value = '/' + document.getElementById(edButtons<?php echo $postfix; ?>[button].id).value;
	}
}

function edRemoveTag<?php echo $postfix; ?>(button) {
	for (i = 0; i < edOpenTags<?php echo $postfix; ?>.length; i++) {
		if (edOpenTags<?php echo $postfix; ?>[i] == button) {
			edOpenTags<?php echo $postfix; ?>.splice(i, 1);
			document.getElementById(edButtons<?php echo $postfix; ?>[button].id).value = 		document.getElementById(edButtons<?php echo $postfix; ?>[button].id).value.replace('/', '');
		}
	}
}

function edCheckOpenTags<?php echo $postfix; ?>(button) {
	var tag = 0;
	for (i = 0; i < edOpenTags<?php echo $postfix; ?>.length; i++) {
		if (edOpenTags<?php echo $postfix; ?>[i] == button) {
			tag++;
		}
	}
	if (tag > 0) {
		return true; // tag found
	}
	else {
		return false; // tag not found
	}
}

function edCloseAllTags<?php echo $postfix; ?>() {
	var count = edOpenTags<?php echo $postfix; ?>.length;
	for (o = 0; o < count; o++) {
		edInsertTa<?php echo $postfix; ?>g(edCanvas<?php echo $postfix; ?>, edOpenTags<?php echo $postfix; ?>[edOpenTags<?php echo $postfix; ?>.length - 1]);
	}
}

function edQuickLink<?php echo $postfix; ?>(i, thisSelect) {
	if (i > -1) {
		var newWin = '';
		if (edLinks<?php echo $postfix; ?>[i].newWin == 1) {
			newWin = ' target="_blank"';
		}
		var tempStr = '<a href="' + edLinks[i].URL + '"' + newWin + '>'
		            + edLinks<?php echo $postfix; ?>[i].display
		            + '</a>';
		thisSelect.selectedIndex = 0;
		edInsertContent<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>, tempStr);
	}
	else {
		thisSelect.selectedIndex = 0;
	}
}

function edSpell<?php echo $postfix; ?>(myField) {
	var word = '';
	if (document.selection) {
		myField.focus();
	    var sel = document.selection.createRange();
		if (sel.text.length > 0) {
			word = sel.text;
		}
	}
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		if (startPos != endPos) {
			word = myField.value.substring(startPos, endPos);
		}
	}
	if (word == '') {
		word = prompt('Enter a word to look up:', '');
	}
	if (word != '') {
		window.open('http://www.answers.com/' + escape(word));
	}
}

function edToolbar<?php echo $postfix; ?>() {
	document.write('<div id="ed_toolbar"><span>');
	for (i = 0; i < extendedStart<?php echo $postfix; ?>; i++) {
		edShowButton<?php echo $postfix; ?>(edButtons<?php echo $postfix; ?>[i], i);
	}
	if (edShowExtraCookie<?php echo $postfix; ?>()) {
		document.write(
			'<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags<?php echo $postfix; ?>();" value="Close Tags" />'
			+ '<input type="button" id="ed_spell" class="ed_button" onclick="edSpell<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>);" value="Dict." />'
			+ '<input type="button" id="ed_extra_show<?php echo $postfix; ?>" class="ed_button" onclick="edShowExtra<?php echo $postfix; ?>()" value="&raquo;" style="visibility: hidden;" />'
			+ '</span><br />'
			+ '<span id="ed_extra_buttons<?php echo $postfix; ?>">'
			+ '<input type="button" id="ed_extra_hide" class="ed_button" onclick="edHideExtra<?php echo $postfix; ?>();" value="&laquo;" />'
		);
	}
	else {
		document.write(
			'<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags<?php echo $postfix; ?>();" value="Close Tags" />'
			+ '<input type="button" id="ed_spell" class="ed_button" onclick="edSpell<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>);" value="Dict." />'
			+ '<input type="button" id="ed_extra_show<?php echo $postfix; ?>" class="ed_button" onclick="edShowExtra<?php echo $postfix; ?>()" value="&raquo;" />'
			+ '</span><br />'
			+ '<span id="ed_extra_buttons<?php echo $postfix; ?>" style="display: none;">'
			+ '<input type="button" id="ed_extra_hide" class="ed_button" onclick="edHideExtra<?php echo $postfix; ?>();" value="&laquo;" />'
		);
	}
	for (i = extendedStart<?php echo $postfix; ?>; i < edButtons<?php echo $postfix; ?>.length; i++) {
		edShowButton<?php echo $postfix; ?>(edButtons<?php echo $postfix; ?>[i], i);
	}
	document.write('</span>');
//	edShowLinks<?php echo $postfix; ?>();
	document.write('</div>');
}

function edShowExtra<?php echo $postfix; ?>() {
	document.getElementById('ed_extra_show<?php echo $postfix; ?>').style.visibility = 'hidden';
	document.getElementById('ed_extra_buttons<?php echo $postfix; ?>').style.display = 'block';
	edSetCookie<?php echo $postfix; ?>(
		'js_quicktags_extra'
		, 'show'
		, new Date("December 31, 2100")
	);
}

function edHideExtra<?php echo $postfix; ?>() {
	document.getElementById('ed_extra_buttons<?php echo $postfix; ?>').style.display = 'none';
	document.getElementById('ed_extra_show<?php echo $postfix; ?>').style.visibility = 'visible';
	edSetCookie<?php echo $postfix; ?>(
		'js_quicktags_extra'
		, 'hide'
		, new Date("December 31, 2100")
	);
}

// insertion code

function edInsertTag<?php echo $postfix; ?>(myField, i) {
	//IE support
	if (document.selection) {
		myField.focus();
	    sel = document.selection.createRange();
		if (sel.text.length > 0) {
			sel.text = edButtons<?php echo $postfix; ?>[i].tagStart + sel.text + edButtons<?php echo $postfix; ?>[i].tagEnd;
		}
		else {
			if (!edCheckOpenTags<?php echo $postfix; ?>(i) || edButtons<?php echo $postfix; ?>[i].tagEnd == '') {
				sel.text = edButtons<?php echo $postfix; ?>[i].tagStart;
				edAddTag<?php echo $postfix; ?>(i);
			}
			else {
				sel.text = edButtons<?php echo $postfix; ?>[i].tagEnd;
				edRemoveTag<?php echo $postfix; ?>(i);
			}
		}
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = myField.scrollTop;
		if (startPos != endPos) {
			myField.value = myField.value.substring(0, startPos)
			              + edButtons<?php echo $postfix; ?>[i].tagStart
			              + myField.value.substring(startPos, endPos)
			              + edButtons<?php echo $postfix; ?>[i].tagEnd
			              + myField.value.substring(endPos, myField.value.length);
			cursorPos += edButtons<?php echo $postfix; ?>[i].tagStart.length + edButtons<?php echo $postfix; ?>[i].tagEnd.length;
		}
		else {
			if (!edCheckOpenTags<?php echo $postfix; ?>(i) || edButtons<?php echo $postfix; ?>[i].tagEnd == '') {
				myField.value = myField.value.substring(0, startPos)
				              + edButtons<?php echo $postfix; ?>[i].tagStart
				              + myField.value.substring(endPos, myField.value.length);
				edAddTag<?php echo $postfix; ?>(i);
				cursorPos = startPos + edButtons<?php echo $postfix; ?>[i].tagStart.length;
			}
			else {
				myField.value = myField.value.substring(0, startPos)
				              + edButtons<?php echo $postfix; ?>[i].tagEnd
				              + myField.value.substring(endPos, myField.value.length);
				edRemoveTag<?php echo $postfix; ?>(i);
				cursorPos = startPos + edButtons<?php echo $postfix; ?>[i].tagEnd.length;
			}
		}
		myField.focus();
		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
		myField.scrollTop = scrollTop;
	}
	else {
		if (!edCheckOpenTags<?php echo $postfix; ?>(i) || edButtons<?php echo $postfix; ?>[i].tagEnd == '') {
			myField.value += edButtons<?php echo $postfix; ?>[i].tagStart;
			edAddTag(i);
		}
		else {
			myField.value += edButtons<?php echo $postfix; ?>[i].tagEnd;
			edRemoveTag<?php echo $postfix; ?>(i);
		}
		myField.focus();
	}
}

function edInsertContent<?php echo $postfix; ?>(myField, myValue) {
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var scrollTop = myField.scrollTop;
		myField.value = myField.value.substring(0, startPos)
		              + myValue
                      + myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
		myField.scrollTop = scrollTop;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}

function edInsertLink<?php echo $postfix; ?>(myField, i, defaultValue) {
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags<?php echo $postfix; ?>(i)) {
		var URL = prompt('Enter the URL' ,defaultValue);
		if (URL) {
			edButtons<?php echo $postfix; ?>[i].tagStart = '<a href="' + URL + '">';
			edInsertTag<?php echo $postfix; ?>(myField, i);
		}
	}
	else {
		edInsertTag<?php echo $postfix; ?>(myField, i);
	}
}

function edInsertExtLink<?php echo $postfix; ?>(myField, i, defaultValue) {
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags<?php echo $postfix; ?>(i)) {
		var URL = prompt('Enter the URL' ,defaultValue);
		if (URL) {
			edButtons<?php echo $postfix; ?>[i].tagStart = '<a href="' + URL + '" rel="external">';
			edInsertTag<?php echo $postfix; ?>(myField, i);
		}
	}
	else {
		edInsertTag<?php echo $postfix; ?>(myField, i);
	}
}

function edInsertImage<?php echo $postfix; ?>(myField) {
	var myValue = prompt('Enter the URL of the image', 'http://');
	if (myValue) {
		myValue = '<img src="'
				+ myValue
				+ '" alt="' + prompt('Enter a description of the image', '')
				+ '" />';
		edInsertContent<?php echo $postfix; ?>(myField, myValue);
	}
}

function edInsertFootnote<?php echo $postfix; ?>(myField) {
	var note = prompt('Enter the footnote:', '');
	if (!note || note == '') {
		return false;
	}
	var now = new Date;
	var fnId = 'fn' + now.getTime();
	var fnStart = edCanvas<?php echo $postfix; ?>.value.indexOf('<ol class="footnotes">');
	if (fnStart != -1) {
		var fnStr1 = edCanvas<?php echo $postfix; ?>.value.substring(0, fnStart)
		var fnStr2 = edCanvas<?php echo $postfix; ?>.value.substring(fnStart, edCanvas<?php echo $postfix; ?>.value.length)
		var count = countInstances(fnStr2, '<li id="') + 1;
	}
	else {
		var count = 1;
	}
	var count = '<sup><a href="#' + fnId + 'n" id="' + fnId + '" class="footnote">' + count + '</a></sup>';
	edInsertContent<?php echo $postfix; ?>(edCanvas<?php echo $postfix; ?>, count);
	if (fnStart != -1) {
		fnStr1 = edCanvas<?php echo $postfix; ?>.value.substring(0, fnStart + count.length)
		fnStr2 = edCanvas<?php echo $postfix; ?>.value.substring(fnStart + count.length, edCanvas<?php echo $postfix; ?>.value.length)
	}
	else {
		var fnStr1 = edCanvas<?php echo $postfix; ?>.value;
		var fnStr2 = "\n\n" + '<ol class="footnotes">' + "\n"
		           + '</ol>' + "\n";
	}
	var footnote = '	<li id="' + fnId + 'n">' + note + ' [<a href="#' + fnId + '">back</a>]</li>' + "\n"
				 + '</ol>';
	edCanvas<?php echo $postfix; ?>.value = fnStr1 + fnStr2.replace('</ol>', footnote);
}

function countInstances<?php echo $postfix; ?>(string, substr) {
	var count = string.split(substr);
	return count.length - 1;
}

function edSetCookie<?php echo $postfix; ?>(name, value, expires, path, domain) {
	document.cookie= name + "=" + escape(value) +
		((expires) ? "; expires=" + expires.toGMTString() : "") +
		((path) ? "; path=" + path : "") +
		((domain) ? "; domain=" + domain : "");
}

function edShowExtraCookie<?php echo $postfix; ?>() {
	var cookies = document.cookie.split(';');
	for (var i=0;i < cookies.length; i++) {
		var cookieData = cookies[i];
		while (cookieData.charAt(0) ==' ') {
			cookieData = cookieData.substring(1, cookieData.length);
		}
		if (cookieData.indexOf('js_quicktags_extra') == 0) {
			if (cookieData.substring(19, cookieData.length) == 'show') {
				return true;
			}
			else {
				return false;
			}
		}
	}
	return false;
}
