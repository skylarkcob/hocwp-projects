/**
 * Last updated: 25/02/2016
 */
window.hocwp = window.hocwp || {};

jQuery(document).ready(function($) {
    tinymce.create('tinymce.plugins.hocwp_shortcode_plugin', {
        init: function(ed, url) {
            ed.addCommand('hocwp_insert_shortcode', function() {
                var selected = tinyMCE.activeEditor.selection.getContent(),
                    content = '';
            });
            var shortcode_values = [],
                shortcodes_button = hocwp.shortcodes;
            $.each(shortcodes_button, function(key, i) {
                shortcode_values.push({text: key, value: key});
            });
            ed.addButton('hocwp_shortcode', {
                type: 'listbox',
                text: 'Shortcodes',
                title: 'Insert shortcode',
                cmd: 'hocwp_insert_shortcode',
                onselect: function(e) {
                    var selected = tinyMCE.activeEditor.selection.getContent(),
                        shortcode = e.control.settings.value;
                    tinyMCE.activeEditor.selection.setContent('[' + shortcode + ']' + selected + '[/' + shortcode + ']');
                },
                values: shortcode_values
            });
        }
    });
    tinymce.PluginManager.add('hocwp_shortcode', tinymce.plugins.hocwp_shortcode_plugin);
});