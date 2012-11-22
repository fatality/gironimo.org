// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('shtb_ins');
	 
	tinymce.create('tinymce.plugins.shtb_ins', {
		
		init : function(ed, url) {
		// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('shtb_ins_cmd', function() {
				ed.windowManager.open({
					file : url + '/window.php',
					width : 396 + ed.getLang('shtb_ins.delta_width', 0),
					height : 99 + ed.getLang('shtb_ins.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('shtb_ins', {
				title : 'WP SyntaxHighlighter Select & Insert',
				cmd : 'shtb_ins_cmd',
				image : url + '/shtb_img.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('shtb_ins', n.nodeName == 'IMG');
			});
		},

                createControl : function(n, cm) {
			return null;
		},

		getInfo : function() {
			return {
					longname  : 'WP SyntaxHighlighter SHTB Select & Insert',
					author 	  : 'redcocker',
					authorurl : 'http://www.near-mint.com/blog',
					infourl   : 'http://www.near-mint.com/blog',
					version   : "0.2.9"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('shtb_ins', tinymce.plugins.shtb_ins);
})();


