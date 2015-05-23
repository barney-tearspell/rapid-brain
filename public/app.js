var neurons;

$.ajax(
	document.baseURI + '.json',
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