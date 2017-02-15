/*tinymce.PluginManager.add("anchor", function(a) {
    function b() {
        var b = a.selection.getNode(),
            c = "";
        "A" == b.tagName && (c = b.name || b.id || ""), a.windowManager.open({
            title: "Anchor cho",
            body: {
                type: "textbox",
                name: "name",
                size: 40,
                label: "Name",
                value: c
            },
            onsubmit: function(b) {
        		a.selection.setContent('<a id="'+b.data.name+'" class="mce-nr-anchor">'+$(a.selection.getNode()).text()+'</a>');
        		
            }
        })
    }
    a.addCommand("mceAnchor", b), a.addButton("anchor", {
        icon: "anchor",
        tooltip: "Anchor",
        onclick: b,
        stateSelector: "a:not([href])"
    }), a.addMenuItem("anchor", {
        icon: "anchor",
        text: "Anchor",
        context: "insert",
        onclick: b
    })
});*/

tinymce.init({
	selector:'textarea.tinymcefull',
	plugins: [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"template paste textcolor"
	],
	toolbar1: "insertfile undo redo | styleselect fontselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | copy cut paste",
	toolbar2: "print preview fullscreen | forecolor backcolor textcolor charmap removeformat | link image media ",
	style_formats: [       
    {title: 'Header 1', block: 'h1',classes: 'content-event-h1'},
	{title: 'Header 2', block: 'h2',classes: 'content-event-h2'},
    {title: 'First characters', inline: 'span', classes: 'content-event-first'},
	{title: 'Bold text', inline: 'b'},
	{title: 'Box text', block:'fieldset',classes:'fieldset-note',wrapper: true},
	{title: 'Box title text', block:'legend'}	,
	{title: 'Link' ,block : 'a',classes: 'link'},
	{title: 'Tab' ,block :  'div',classes: 'tab-news'},
	{title: 'remove Box Image', block :  'div', classes: 'box-none'}
	],
	file_browser_callback:  function (fieldName, url, type, win) {
		ckFileBrowser(fieldName, url, type, win);
	},
	fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
	min_height:300,
	convert_urls: false
	
});
var tinymceFull = function(element){
	element.tinymce({
		selector:'textarea.tinymcefull',
		plugins: [
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media nonbreaking save table contextmenu directionality",
			"template paste textcolor"
		],
		toolbar1: "insertfile undo redo | styleselect fontselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | copy cut paste",
		toolbar2: "print preview fullscreen | forecolor backcolor textcolor charmap removeformat | link image media ",
		style_formats: [       
	    {title: 'Header 1', block: 'h1',classes: 'content-event-h1'},
		{title: 'Header 2', block: 'h2',classes: 'content-event-h2'},
	    {title: 'First characters', inline: 'span', classes: 'content-event-first'},
		{title: 'Bold text', inline: 'b'},
		{title: 'Box text', block:'fieldset',classes:'fieldset-note',wrapper: true},
		{title: 'Box title text', block:'legend'}	,
		{title: 'Link' ,block : 'a',classes: 'link'},
		{title: 'Tab' ,block :  'div',classes: 'tab-news'},
		{title: 'remove Box Image', block :  'div', classes: 'box-none'}
		],
		file_browser_callback:  function (fieldName, url, type, win) {
			ckFileBrowser(fieldName, url, type, win);
		},
		fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
		min_height:300,
		convert_urls: false
	})
}