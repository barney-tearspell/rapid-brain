<!doctype html>
<html class="no-js" lang="">
	<head>
		<title>404</title>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="/codemirror.css"/>
		<link rel="stylesheet" href="/xq-light.css"/>
	</head>
	<body>
		<h1>404 Page Not Found</h1>
		<p>The synapse you requested does not exist. Do you want to create it?</p>
		<form action="" method="post">
			<fieldset>
				<legend>Neuron</legend>
				<textarea id="neuron" name="neuron" style="width:100%;height:40em;">
<!doctype html>
<html>
	<head>
		<title>Title</title>
		<base href="/" />
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="app.css"/>
	</head>
	<body>
	</body>
</html>
				</textarea>
				<button type="submit">Save</button>
			</fieldset>
		</form>
		<script src="/codemirror-compressed.js"></script>
		<script>
			CodeMirror.fromTextArea(document.getElementById('neuron'), {
				mode: "htmlmixed"
			});
		</script>
	</body>
</html>