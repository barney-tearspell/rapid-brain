var workingon = {};

$(function(){
	$('<link>').attr({
		'rel':'stylesheet',
		'href':'js/vendor/skins/codemirror.css'
	}).appendTo('head').load(function(e){
		// console.log('css loaded', e);

		// create json editor as JSONEditor
		window.JSONEditor = $('<div>').attr({
			'id': 'JSONEditor'
		}).appendTo('body').before('<p>JSON Editor</p>').get(0);
		bindCodeMirror(JSONEditor, 'javascript');

		// create code editor as HTMLEditor
		window.HTMLEditor = $('<div>').attr({
			'id': 'HTMLEditor'
		}).appendTo('body').before('<p>HTML Editor</p>').get(0);
		bindCodeMirror(HTMLEditor);

		// create iframe polygon as Polygon
		window.Polygon = $('<iframe>').attr({
			'id': 'Polygon'
		}).appendTo('body').before('<p>Polygon</p>').get(0);

		// create iframe preview as Preview
		window.Preview = $('<iframe>').attr({
			'id': 'Preview'
		}).appendTo('body').before('<p>Preview</p>').get(0);

		// broadcast environmentready
		loadData();
	});
});

function loadData(){

	var dataurl = window.location.search || '?';
	dataurl = dataurl.substr(1).replace(/\/?(.*?)\/$/, '$1');
	dataurl = '/'+ dataurl + '.json';

	$.ajax({
		url: dataurl,
		success: function(data, status, xhr){
			// console.log('OK',data);
			workingon = data;
			JSONEditor.codemirror.doc.setValue( xhr.responseText );

			HTMLEditor.codemirror.doc.setValue( render( data ) );
		},
		error: function(data){
			console.log('NOK',data);
		}
	});
}

function bindCodeMirror( o, mode ){
	o.codemirror = CodeMirror( o, {
		lineNumbers: true,
		autofocus: true,
		mode: mode || "htmlmixed",
		indentUnit: 4,
		indentWithTabs: true,
		keyMap: 'sublime',
		autoCloseBrackets: true,
		theme: 'monokai',
		viewportMargin: Infinity,
		value: ''
	});
}

/*
function render( data ){
	var dataurl = window.location.search || '?';
	dataurl = dataurl.substr(1).replace(/\/?(.*?)\/$/, '$1');

	var r = data[dataurl].value;

	var p = Polygon.contentWindow.document;
	p.write(r);
	while( $('[data-synapse]',p).length ){
		$('[data-synapse]',p).each(function(i,o){
			var s = $(o).attr('data-synapse');
			console.log( s );
			$(o).attr({
				'data-synapse-used':s
			}).removeAttr('data-synapse').html( typeof data[s]=='undefined' ? '' : data[s].value )
		});		
	}

	return r;
}
*/

function render( data ){
	// extract root neuron from url
	var dataurl = window.location.search || '?';
	dataurl = dataurl.substr(1).replace(/\/?(.*?)\/$/, '$1');
	var r = data[dataurl].value;

	// create virtual container
	var dof = document.createDocumentFragment();

	// fill container
	$(r).appendTo(dof);

	// unpack synapses
	var synapses = dof.querySelectorAll('[data-synapse]');
	while( synapses.length ){
		$.each(synapses, function(i,o){
			var s = $(o).attr('data-synapse');
			console.log( s );
			$(o).attr({
				'data-synapse-used':s
			}).removeAttr('data-synapse').html( typeof data[s]=='undefined' ? '' : data[s].value )
		});		
		synapses = dof.querySelectorAll('[data-synapse]');
	}

	var synapses = dof.querySelectorAll('[data-synapse-used]');
	$.each(synapses, function(i,o){
		var s = $(o).attr('data-synapse-used');
		console.log( 'clean', s );
		$(o).attr({
			'data-synapse':s
		}).removeAttr('data-synapse-used')
	});		
	/*
	r = $('<div>').append(dof).html();
	HTMLEditor.codemirror.doc.setValue( r );

	window.open('data:text/html;charset=utf-8,' + encodeURI(r) );
	*/
	var p = Polygon.contentWindow.document;
	p.open();

	console.log( dof.querySelectorAll('head') );
	console.log( dof.querySelectorAll('body') );
	console.log( dof );


	//p.appendChild( dof.cloneNode(true) );
	
	HTMLEditor.codemirror.doc.setValue( p.documentElement.outerHTML );
}
