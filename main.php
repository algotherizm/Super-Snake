<?php
session_start();

$link = new mysqli("localhost","sMove","","game");
if ($link->connect_errno) {
    printf("Connect failed: %s\n", $link->connect_error);
    exit();
}
if(isset($_SESSION['user'])){
	$email = $_SESSION["user"];
	$password = $_SESSION[$email];
	$result = $link->query("SELECT password FROM users where email='$email'");
	$row = $result->fetch_assoc();
	if($password != $row["password"]){
		header('Location: index.php');
	}
}else{
	header('Location: index.php');
}

$email = $_SESSION["user"];
$fullname="";
$result = $link->query("SELECT first_name, last_name, id FROM users where email='$email'");
$row = $result->fetch_assoc();
$first = $row['first_name'];
$last = $row['last_name'];
$fullname = $first." ".$last;
$userID = $row['id'];

if(isset($_REQUEST["action"]))
	$action = $_REQUEST["action"];
else
	$action = "none";

?>

<html lang="en">
	<head>
	    <title>Super-Snake</title>
	    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
	    <link href="../Super-Snake/css/main.css" rel="stylesheet">
	    <link rel="stylesheet" href="../Super-Snake/js/paperjs-v0/css/style.css">
		<!--<script type="text/javascript" src="../Super-Snake/js/paperjs-v0/dist/paper-full.js"></script>-->
		<!--<script type="text/javascript" src="../Super-Snake/js/game.js"/>-->
		<script type="text/paperscript" canvas="canvas">
		
			// Adapted from the following Processing example:
	        // http://processing.org/learning/topics/follow3.html

	        // The amount of points in the path:
	        var points = 25;

	        // The distance between the points:
	        var length = 35;

	        var path = new Path({
	            strokeColor: '#E4141B',
	            strokeWidth: 20,
	            strokeCap: 'round'
	        });

	        var start = view.center / [10, 1];
	        for (var i = 0; i < points; i++)
	            path.add(start + new Point(i * length, 0));

	        function onMouseMove(event) {
	            path.firstSegment.point = event.point;
	            for (var i = 0; i < points - 1; i++) {
	                var segment = path.segments[i];
	                var nextSegment = segment.next;
	                var vector = segment.point - nextSegment.point;
	                vector.length = length;
	                nextSegment.point = segment.point - vector;
	            }
	            path.smooth();
	        }

	        function onMouseDown(event) {
	            path.fullySelected = true;
	            path.strokeColor = '#e08285';
	        }

	        function onMouseUp(event) {
	            path.fullySelected = false;
	            path.strokeColor = '#e4141b';
	        }
	   

	    </script>

	    <script>
			function game()
			{
	    		var canvas = document.getElementById('canvas');
				var ctx = canvas.getContext('2d');
				var keys = 
				{
					37: 'left',
					38: 'up',
					39: 'right',
					40: 'down'
				};
				var pos = [[5,1],[4,1],[3,1],[2,1],[1,1]];
				var direction = 'right';
				var old_direction = 'right';
				var block = 10;
				for(var x=0; x<pos.length; x++)
				{
					var x_co = pos[x][0]*block;
					var y_co = pos[x][1]*block;
					ctx.beginPath();		
					ctx.fillRect(x_co,y_co,block,block);
					ctx.closePath();
				}
				window.onkeydown = function(event)
				{
					direction = keys[event.keyCode];
					if(direction)
					{
						setWay(direction);
						event.preventDefault();
					}
				}
				function setWay(direction)
				{
					if(old_direction != direction)
						old_direction = direction;
					console.log(direction);
				}
			}

			function drawthis()
			{
				var canvas = document.getElementById('canvas');
				var ctx = canvas.getContext('2d');
				ctx.fillRect(50,50,10,10);
			}
		</script>
	</head>
	<body onload="game()">
	   <div class="myContainer">
	        <div class="header">
	            <nav>
	                <ul class="main-nav nav nav-pills pull-right">
	                    <li role="presentation"><a class="cd-signin" href="dashboard.php"> Dashboard</a></li>
	                    <li role="presentation"><a class="cd-signin" href="logOut.php"> Sign Out</a></li>
	                </ul>
	            </nav>
	            <a href="main.php"><h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> Super-Snake</h1></a>
	        </div>
			    
			<canvas id="canvas" resize style="border:1px solid #000000";></canvas>
			<a href="#" onclick="game()">Click to draw a rectangle</a><br/>

	        <footer class="footer">
	            <p>&copy; Connor Smith and Kayla Holcomb 2016</p>
	        </footer>

	    </div>

	</body>
</html>