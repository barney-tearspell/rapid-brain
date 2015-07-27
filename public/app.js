var neurons;

var dataurl = window.location.pathname || '/';
dataurl = dataurl.replace(/(.*?)\/$/, '$1');
dataurl = dataurl + '.json';

$.ajax(
	dataurl,
	{
		success: function(data){
			neurons = data;
		}
	}
);

function saveNeurons() {
	for(i in neurons) {
		neurons[i].value += '<span class="visited">' + Date.now() + '</span>';
		neurons[i].data = typeof neurons[i].data === 'object' ? neurons[i].data : {};
		neurons[i].data.lastVisit = Date.now();
		console.log(neurons[i]);
	}
	$.ajax(
		document.baseURI,
		{
			method: 'POST',
			data: neurons
		}
	);
	console.log('neurons', neurons);
}