<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="description" content="A process that retrieves PHP repository information.">
	<meta name="author" content="carlreedw@gmail.com">

	<title>GitHub PHP Repositories</title>
	
	<!-- Let's use JQuery here to make dom traversal and manipulation easier -->
	<!-- Normally I would target the CDN, but having a local copy gives me just a little more certainty -->
	<!-- Using production, minified, v3.3.1 -->
	<script src="js/jquery.js"></script>
	<!-- Using Materialize, minified v0.100.2 -->
	<script src="materialize/js/materialize.min.js"></script>
	<link rel="stylesheet" href="materialize/css/materialize.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- adding our own stylesheet -->
	<link rel="stylesheet" href="css/style.css">
	
</head>
<body>
	<div class="mainContainer">
		<div class="card white topRow">
			<div class="card-content">
				<span class="card-title" style="text-align: center">GitHub PHP Repositories</span>
				<a class="waves-effect waves-light blue darken-2 btn" onclick="refresh_client()" id="button1">
					<i class="material-icons right">refresh</i>
					REFRESH CLIENT
				</a>
				<a class="waves-effect waves-light blue darken-2 btn" onclick="refresh_database()" id="button2">
					<i class="material-icons right">refresh</i>
					REFRESH DATABASE
				</a>
			</div>
		</div>
		<div id="mainContent">
			
		</div>
	</div>
	<script src="js/requestor.js"></script>
</body>
</html>