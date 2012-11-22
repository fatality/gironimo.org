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

function insertSHTBINScode() {

	ed = tinyMCEPopup.editor
	var langname_ddb = document.getElementById('shtb_ins_language');
	var langname = langname_ddb.value;
	var linenumbers = document.getElementById('shtb_ins_linenumbers').checked;
	var html = tinyMCE.activeEditor.selection.getContent();
	html = html.replace(/<p>/g,"").replace(/<\/p>/g,"<br \/>");

	var tagtext = '<pre class="brush: ';
	classAttribs = langname;

	if (linenumbers)
		classAttribs = classAttribs + '; gutter: true';
	else
		classAttribs = classAttribs + '; gutter: false';

	if(e = ed.dom.getParent(ed.selection.getNode(), 'pre')){
		ed.dom.setAttribs(e, {class : 'brush: '+classAttribs});
	} else {
	tinyMCEPopup.editor.execCommand('mceInsertContent', false, tagtext+classAttribs+'">'+html+'</pre>');
	}
	tinyMCEPopup.close();
	return;
}
