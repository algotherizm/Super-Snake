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
	    <link rel="stylesheet" href="../Super-Snake/js/paperjs-v0/examples/css/style.css">
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
	   
	   		// SNAKE MOVES AUTOMATICALLY IN SAME DIRECTION
	   		// GAME LOOP - START AND END FUNCTION (clear canvas, redraw and auto move, timeout from last arrow key press)
	   		// GROWTH AUTOMATICALLY
	   		// OVERLAP KILLS OR HIT WALL
	   		// REMAKE INDEX PAGE
	   		
	   		// MAIN MENU TO START NEW GAME
	   		// OPTION TO LEAVE GAME
	   		// FOOD = ADD TO SCORE
	   		// SCORE BASED ON LENGTH + FOOD
	   		// "HARD MODE" WHERE WE UP THE REFRESH RATE TO GIVE MORE SPEED
	   		// LOBBY
	   		// OPTION CREATE USER PROFILE (TRACK WINS & HIGH SCORES)
	   		// MULTIPLAYER (2+)
	   		// LOBBY SHOWS HIGH SCORES LIST 

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
				var endGame = false;

				function draw()
				{
					for(var x=0; x<pos.length; x++)
					{
						var x_co = pos[x][0]*block;
						var y_co = pos[x][1]*block;
						ctx.beginPath();		
						ctx.fillRect(x_co,y_co,block,block);
						ctx.closePath();
					}
				}
				window.onkeydown = function(event)
				{
					direction = keys[event.keyCode];
					if(direction)
					{
						setWay(direction);
						getOn();
						pos.pop();
						document.getElementById('canvas');
						canvas.width = canvas.width;
						draw();
						collideBody();
						collideWall();
						event.preventDefault();
					}
				}
				function setWay(direction)
				{
					if(old_direction != direction)
						old_direction = direction;
				}
				function getOn()
				{
					var next = pos[0].slice();
					switch(old_direction)
					{
						case 'left':
							next[0] += -1;
							break;

						case 'up':
							next[1] += -1;
							break;

						case 'right':
							next[0] += 1;
							break;

						case 'down':
							next[1] += 1;
							break;
					}

					pos.unshift(next);
				}

                function collideBody()
				{
					var head = pos[0];
					for(var a=1; a<pos.length; a++)
					{
						if(head[0] == pos[a][0] && head[1] == pos[a][1])
							console.log("You hit yourself in the head. You died.");
					}
				}
				function collideWall()
				{
					var walls = 
					{
						up: 0,
						right: canvas.width/block-1,
						down: canvas.height/block-1,
						left: 0
					};
					var head = pos[0];
					if(head[0]>walls.right || head[0]<walls.left || head[1]<walls.up || head[1]>walls.down)
						console.log("You bashed your head into a wall. You died.");
				}

				function gameLoop()
		    	{
		    		setTimeout(function () {

		    			var canvas = document.getElementById('canvas');
		    			getOn();
		    			if (collideBody || collideWall)
		    				endGame = true;

		    		}, 1000);
		    	}

				draw();
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

	        <div id="game-over-modal" class="game-over-modal">
	          	<div class="game-over-modal-container">
	                <ul class="modal-switcher">
	                    <li><a href="#">Sign In</a></li>
	                    <li><a href="#">New Account</a></li>
	                </ul>
	                <div id="modal-login"> <!-- log in form -->
	                	<div id="loginError" role="alert" class="alert alert-danger modal-alert alert-hide"><p>That username and password did not match our records. Please, try again.<p></div>
	                    <form class="modal-form" name="signIn" onsubmit="return validateSignIn()" method="post" action="#">
	                    	<input type="hidden" name="action" value="login">
	                        <p class="fieldset"><label class="modal-label">Email:</label><input required class="modal-input" id="logInEmail" name="email" type="email" placeholder="E-mail"></p>
	                        <p class="fieldset"><label class="modal-label">Password:</label><input required class="modal-input" id="logInPassword" name="password" type="password"  placeholder="Password"></p>
	                        <input class="modal-input" type="submit" value="Login">
	                    </form>
	                </div>
	                <div id="modal-signup"> <!-- sign up form -->
	                    <form class="modal-form" name="signUp" onsubmit="return validateSignUp()" method="post" action="#">
	                    	<input type="hidden" name="action" value="add_user">
	                        <p class="fieldset"><label class="modal-label">First Name:</label><input required class="modal-input" id="fname" name="fname" type="text" placeholder="First Name"></p>
	                        <p class="fieldset"><label class="modal-label">Last Name:</label><input required class="modal-input" id="lname" name="lname" type="text" placeholder="Last Name"></p>
	                        <p class="fieldset"><label class="modal-label">Email:</label><input required class="modal-input" id="email" name="email" type="email" placeholder="E-mail"></p>
	                        <p class="fieldset"><label class="modal-label">Password:</label><input required class="modal-input" id="password" name="password" type="password"  placeholder="Password"></p>
	                        <p class="fieldset"><label id="pass2Label" class="modal-label">Enter Password Again:</label><input required class="modal-input" id="password2" name="password2" type="password"  placeholder="Retype Password"></p>
	                        <input class="modal-input" type="submit" value="Create Account">
	                    </form>
	                </div>
	            </div>
          	</div>

           <footer class="footer">
	            <p>&copy; Connor Smith and Kayla Holcomb 2016</p>
	        </footer>

	    </div>

	</body>
</html>