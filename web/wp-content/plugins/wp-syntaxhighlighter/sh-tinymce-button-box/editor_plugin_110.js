// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('shtb_box');
	 
	tinymce.create('tinymce.plugins.shtb_box', {
		
		init : function(ed, url) {
		// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('shtb_box_cmd', function() {
				ed.windowManager.open({
					file : url + '/window.php',
					width : 400 + ed.getLang('shtb_box.delta_width', 0),
					height : 374 + ed.getLang('shtb_box.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('shtb_box', {
				title : 'WP SyntaxHighlighter CodeBox',
				cmd : 'shtb_box_cmd',
				image : url + '/shtb_img.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('shtb_box', n.nodeName == 'IMG');
			});
		},

                createControl : function(n, cm) {
			return null;
		},

		getInfo : function() {
			return {
					longname  : 'WP SyntaxHighlighter SHTB CodeBox',
					author 	  : 'redcocker',
					authorurl : 'http://www.near-mint.com/blog',
					infourl   : 'http://www.near-mint.com/blog',
					version   : "0.3"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('shtb_box', tinymce.plugins.shtb_box);
})();


