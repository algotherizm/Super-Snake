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

$room = $_GET["room"];
$num = $_GET["num"];
?>

<html lang="en">
	<head>
	    <title>Super-Snake</title>
	    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
	    <link href="../Super-Snake/css/main.css" rel="stylesheet">
	   	<link href="../Super-Snake/css/modal.css" rel="stylesheet">
<!-- 	    <link rel="stylesheet" href="../Super-Snake/js/paperjs-v0/examples/css/style.css">
 -->		<!--<script type="text/javascript" src="../Super-Snake/js/paperjs-v0/dist/paper-full.js"></script>-->
		<script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
		<!--<script src="http://150.252.244.54:5000/socket.io/socket.io.js"></script>-->
		<script src="http://150.252.245.114:5000/socket.io/socket.io.js"></script>
        <script src="../Super-Snake/js/main.js"></script>
        <script type="text/paperscript" canvas="canvas"></script>
        <script>

		    //var socket = io.connect('http://150.252.244.54:5000');
		    var socket = io.connect('http://150.252.245.114:5000');
		    var player = "<?php echo $num?>";

            socket.on('connect', function(){
                socket.emit('connectGame', "<?php echo $first; ?>", "<?php echo $room?>");
            });

            socket.on('updatePosition', function(playernum, position){
            	if(playernum == player)
            		pos = position;
            	else
            		enemy = position;
            });

            $(function(){
                $('#datasend').click( function() {
                    var message = $('#data').val();
                    $('#data').val('');
                    socket.emit('sendchat', message);
                });

                $('#data').keypress(function(e) {
                    if(e.which == 13) {
                        $(this).blur();
                        $('#datasend').focus().click();
                    }
                });

                $('#roombutton').click(function(){
                    var name = $('#roomname').val();
                    $('#roomname').val('');
                    socket.emit('create', name)
                });
            });
	  		// better looking modal
	  		// better lobby
	  		// fruit in different shapes
	   		// "HARD MODE" WHERE WE UP THE REFRESH RATE TO GIVE MORE SPEED
	   		// OPTION CREATE USER PROFILE (TRACK WINS & HIGH SCORES)
	   		// MULTIPLAYER (2+) "web sockets"
	   		// LOBBY SHOWS HIGH SCORES LIST 
	   		// MUSIC WHILE PLAYING
	   		// better looking snake (paperJS snake)
	   		// pause button
	   		// carry over to the other side of the screen
	   		// choose colors for multiplayer
	   		
			//code from: http://codereview.stackexchange.com/questions/55323/snake-game-with-canvas-element-code
	    	//Much of the code is adapted from that site
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
				var pos;
				var direction;
				var old_direction;
				var enemy;
				var en_direction;
				var en_old;
				if(player == 1){
					pos = [[5,1],[4,1],[3,1],[2,1],[1,1]];
					direction = "right";
					old_direction = "right";
					enemy = [[20,14], [21,14], [22,14], [23,14], [24,14]];
					en_direction = "left";
					en_old = "left";
				}
				else{
					pos = [[20,13], [21,13], [22,13], [23,13], [24,13]];
					direction = "left";
					old_direction = "left";
					enemy = [[5,1],[4,1],[3,1],[2,1],[1,1]];
					en_direction = "right";
					en_old = "right";
				}
				var block = 10;
				var endGame = false;
				var body = false;
				var wall = false; 
				var notify = false;
				var lastMove = Date.now();
				var food = [0,0];
				var foodexists = false;
				var score = document.getElementById('score');
				var new_score = 0;
				//console.log(canvas.width/block);
				//console.log(canvas.height/block);

				function draw()
				{
					for(var x=0; x<pos.length; x++)
					{
						var x_co = pos[x][0]*block;
						var y_co = pos[x][1]*block;
						ctx.beginPath();
						ctx.fillStyle = 'black';
						ctx.fillRect(x_co,y_co,block,block);
						ctx.closePath();
					}
					for(var x=0; x<pos.length; x++)
					{
						var x_co = enemy[x][0]*block;
						var y_co = enemy[x][1]*block;
						ctx.beginPath();
						ctx.fillStyle = 'green';
						ctx.fillRect(x_co,y_co,block,block);
						ctx.closePath();
					}
					ctx.fillStyle = 'black';
				}

				window.onkeydown = function(event)
				{
					direction = keys[event.keyCode];
					if(direction)
					{
						var currentTime = Date.now();
						if(currentTime - lastMove >= 500)
						{
							setWay(direction);
							event.preventDefault();
							lastMove = Date.now();
						}
					}
				}

				function setWay(direction)
				{
					switch(direction)
					{
						case 'left':
							if(old_direction!='right')
								old_direction = direction;
							else
								direction = old_direction;
							break;
						case 'right':
							if(old_direction!='left')
								old_direction = direction;
							else
								direction = old_direction;
							break;
						case 'up':
							if(old_direction!='down')
								old_direction = direction;
							else
								direction = old_direction;
							break;
						case 'down':
							if(old_direction!='up')
								old_direction = direction;
							else
								direction = old_direction;
							break;
					}
				}

				function getOn()
				{
					if(!endGame)
					{
						setWay(direction);
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
						pos.pop();
					}
				}

				function auto()
				{
					if(!endGame)
					{
						setWay(direction);
						var next = pos[0].slice();
						switch(direction)
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

						pos.push(next);
					}
				}

                function collideBody()
				{
					var head = pos[0];
					for(var a=1; a<pos.length; a++)
					{
						if(head[0] == pos[a][0] && head[1] == pos[a][1])
						{
							body = true;
						}
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
					{
						wall = true;
					}
				}

				//move automatically every half second
				setInterval( function () 
				{
					//only move auto after half second from last auto or last manual move
					var currentTime = Date.now();

                       	document.getElementById('canvas');
                       	ctx.clearRect(0, 0, canvas.width, canvas.height);
                       	spawnfood();
		    			getOn();
		    			draw();
		    			collideWall();
		    			collideBody();
		    			noms();
		    			if(!endGame)
		    				new_score += 1;
		    			score.innerHTML = new_score;
		    			socket.emit('updatePlayer', player, pos);
		    			if (body || wall)
		    			{
		    				if(!notify)
		    				{
		    					endGame = true;
		    					location.href = "#"; //fix later
		    					alert("Game Over. Your Score is: " + new_score);
		    					notify = true;
		    				}
		    			}
		    			lastMove = currentTime;

	    		}, 400);
	    		function createfood()
	    		{//math help from: http://stackoverflow.com/questions/1527803/generating-random-numbers-in-javascript-in-a-specific-range
	    			food = [Math.round(Math.random()*(canvas.width/block)), Math.round(Math.random()*(canvas.height/block))];
	    			if(food[0] >= canvas.width/block)
	    				food[0] = food[0]-2;
	    			if(food[1] >= canvas.height/block)
	    				food[1] == food[1]-2;
	    		}
	    		function spawnfood()
	    		{
	    			if(foodexists == false)
	    				createfood();
	    			ctx.beginPath();
	    			ctx.fillStyle="#FF0000";
	    			ctx.fillRect(food[0]*block, food[1]*block, block, block);
	    			ctx.fill();
	    			ctx.closePath();
	    			foodexists=true;
	    		}
	    		function noms()
	    		{
	    			if(food[0]==pos[0][0] && food[1]==pos[0][1])
	    			{
	    				ctx.clearRect(food[0],food[1],block,block);
	    				foodexists=false;
	    				auto();
	    				new_score += 100;
	    				score.innerHTML = new_score;
	    			}
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
	                	<li role="presentation"><a class="cd-signin site-signUp" href="#"> End Game</a></li>
	                    <li role="presentation"><a class="cd-signin" href="lobby.php"> Lobby</a></li>
	                    <li role="presentation"><a class="cd-signin" href="logOut.php"> Sign Out</a></li>
	                </ul>
	            </nav>
	            <a href="main.php"><h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> Super-Snake</h1></a>
	        </div>
	        <h2 id="score_text" style="display:inline">Score:</h2>
	        <h2 id="score" style="display:inline">0</h2>
			<canvas id="canvas" resize style="border:1px solid #000000";></canvas>

        	<div id="sign-in-modal" class="sign-in-modal">
          		<div class="sign-in-modal-container">
                    <button type="button" class="btn btn-default" href="#" onclick="window.location.reload(true);">New Game</button>
                    <button type="button" class="btn btn-default" onclick="window.location.assign('http://52.10.103.58/Super-Snake/lobby.php');" href="lobby.php">Return to Lobby</button>
            	</div>
        	</div>

        	<div style="float:left;width:100px;border-right:1px solid black;height:300px;padding:10px;overflow:scroll-y;">
                <b>ROOMS</b>
                <div id="rooms"></div>
            </div>

            <div style="float:left;width:300px;height:250px;overflow:scroll-y;padding:10px;">
                <div id="conversation"></div>
                <input id="data" style="width:200px;" />
                <input type="button" id="datasend" value="send" />
            </div>

           	<footer class="footer">
	            <p>&copy; Connor Smith and Kayla Holcomb 2016</p>
	        </footer>

	    </div>

	</body>
</html>