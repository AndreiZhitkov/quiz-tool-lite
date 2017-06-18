// JavaScript Document
(function() {
	tinymce.create('tinymce.plugins.AI_Quiz_Button', {
		init : function(ed, url) {
			
			// Register commands
			ed.addCommand('AI_Quiz_AddEditorButton', function() {
				ed.windowManager.open({
					file : url + '/AI_Quiz_button_popup.php',
					width : 800 + parseInt(ed.getLang('AIquizButtonAdd.delta_width', 0)),
					height : 450 + parseInt(ed.getLang('AIquizButtonAdd.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('AIquizButtonAdd', {title : 'Quiz Tool Lite Plugin Editor Button', cmd : 'AI_Quiz_AddEditorButton', image: url + '/quiz_icon.png' });
			
			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
					cm.setActive('AIquizButtonAdd', n.nodeName == 'IMG');
			});
		},

		getInfo : function() {
			return {
				longname : 'Quiz Tool Lite Plugin Editor Button',
				author : 'Alex Furr',
				authorurl : 'http://www.cite.soton.ac.uk',
				infourl : 'http://www.cite.soton.ac.uk',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('AIquizButtonAdd', tinymce.plugins.AI_Quiz_Button);
})();


// INSERT THE Shortcode
function insertAI_shortcode(thisShortcode)
{
	var shortcode = "["+thisShortcode+"]";
	tinyMCEPopup.execCommand('mceReplaceContent', false, shortcode);
	tinyMCEPopup.close();
}

