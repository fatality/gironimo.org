tinyMCEPopup.requireLangPack();

var SHTBINSDialog = {

	init : function() {
		var f = document.forms[0], ed = tinyMCEPopup.editor;

		if (e = ed.dom.getParent(ed.selection.getNode(), 'pre')) {
			str = ed.dom.getAttrib(e, 'class').split(";");
			f.shtb_ins_language.value = str[0].replace("brush: ", "");
			if(ed.dom.getAttrib(e, 'class').match(/true/)){
				f.shtb_ins_linenumbers.checked = true;
			}
			if(ed.dom.getAttrib(e, 'class').match(/false/)){
				f.shtb_ins_linenumbers.checked = false;
			}
			f.insert.value=ed.getLang('update');
		}
	},
};

tinyMCEPopup.onInit.add(SHTBINSDialog.init, SHTBINSDialog);
