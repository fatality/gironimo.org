function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertSHTBBOXcode() {

	var langname_ddb = document.getElementById('shtb_box_language');
	var langname = langname_ddb.value;
	var linenumbers = document.getElementById('shtb_box_linenumbers').checked;
	var code = document.getElementById('shtb_box_code').value.replace(/&amp;/g,'&amp;amp;').replace(/&lt;/g,'&amp;lt;').replace(/</g,'&lt;').replace(/\r\n/g,'<br />');
	code = code.replace(/\n|\r/g,'<br />');
	code = code.replace(/&gt;/g,'&amp;gt;').replace(/&quot;/g,'&amp;quot;').replace(/&#039;/g,'&amp;#039;');

	var tagtext = '<pre class="brush: ';
	classAttribs = langname;

	if (linenumbers)
		classAttribs = classAttribs + '; gutter: true';
	else
		classAttribs = classAttribs + '; gutter: false';

	if (code == '') {
		alert("Your code is empty");
	} else {
	tinyMCEPopup.editor.execCommand('mceInsertContent', false, tagtext+classAttribs+'">'+code+'</pre>');
	}
	tinyMCEPopup.close();
	return;
}
