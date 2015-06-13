$(function(){
	$('<link>').attr({
		'rel':'stylesheet',
		'href':'js/vendor/skins/codemirror.css'
	}).appendTo('head').load(function(e){
		console.log('css loaded', e);
		var o = $('body').get(0);
		bindCodeMirror(o);
		loadData();
	});
});

function loadData(){
	$.ajax({
		url: 'data.json',
		success: function(data){
			console.log('OK',data);
		},
		error: function(data){
			console.log('NOK',data);
		}
	});
}

function bindCodeMirror( o ){
	o.htmlcode = o.innerHTML;
	o.innerHTML = '';
	o.codemirror = CodeMirror( o, {
		lineNumbers: true,
		autofocus: true,
		mode: "htmlmixed",
		indentUnit: 4,
		indentWithTabs: true,
		keyMap: 'sublime',
		autoCloseBrackets: true,
		theme: 'monokai',
		viewportMargin: Infinity,
		value: o.htmlcode
	});

	var f = { 
		'/': { 
			value: ' <!doctype html> <html> <head data-synapse="$site/head">{{$site/head}}</head> <body data-synapse="$page/body">{{$page/body}}</body> </html> ', 
			properties: { 'body': { value: '' } } }, 
			'http://localhost/rapidbrain': { 
				properties: { 
					'url': { 
						value: 'http://localhost/rapidbrain' 
					}, 
					'title': { value: 'RapidBrain demo' }, 
					'head': { value: ' <meta charset="UTF-8"> <title data-synapse="$site/title">{{$site/title}}</title> <style data-synapse="$site/css">{{$site/css}}</style> <script data-synapse="$site/script">{{$site/script}}</script> ' }, 
					'css': { value: ' body { font-family: sans-serif; color: #333; bacground: #EEE; }  a {} a:link, a:visited { text-decoration: none; color: #  } a:hover, a:focus, a:active { text-decoration: underline; outline: none; } ' }, 
					'script': { value: ' (function(){})(); ' } 
				} 
			} 
		}  
}
