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
        <link href="../Super-Snake/css/modal.css" rel="stylesheet">
        <link rel="stylesheet" href="../Super-Snake/js/paperjs-v0/examples/css/style.css">
        <!--<script type="text/javascript" src="../Super-Snake/js/paperjs-v0/dist/paper-full.js"></script>-->
        <script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="../Super-Snake/js/main.js"/>
        <script type="text/paperscript" canvas="canvas"/>
        <script src="../Super-Snake/socket.io.js"/>
        <script>

            var socket = io.connect('http://52.10.103.58:8000');
                
            var uname = <?php echo $email?>;
            socket.emit('adduser', uname);

            socket.on('connect', function(){
                var uname = <?php echo $email?>;
                console.log(uname);
                console.log("Connected!");
                socket.emit('adduser', uname);
            });

            socket.on('updatechat', function (username, data) 
            {
                $('#conversation').append('<b>'+ username + ':</b> ' + data + '<br>');
            });

            socket.on('updaterooms', function (rooms, current_room)    
            {
                $('#rooms').empty();
                $.each(rooms, function(key, value) 
                {
                    if(value == current_room)
                    {
                        $('#rooms').append('<div>' + value + '</div>');
                    }
                    else 
                    {
                        $('#rooms').append('<div><a href="#" onclick="switchRoom(\''+value+'\')">' + value + '</a></div>');
                    }
                });
            });
       
            // END GAME MODAL
            
            // MAIN MENU TO START NEW GAME
            // OPTION TO LEAVE GAME
            // FOOD = ADD TO SCORE
            // SCORE BASED ON LENGTH + FOOD
            // "HARD MODE" WHERE WE UP THE REFRESH RATE TO GIVE MORE SPEED
            // OPTION CREATE USER PROFILE (TRACK WINS & HIGH SCORES)
            // MULTIPLAYER (2+)
            // LOBBY SHOWS HIGH SCORES LIST 
            // MUSIC WHILE PLAYING
            // better looking snake (paperJS snake)
            // pause button
            // carry over to the other side of the screen

        </script>
    </head>
    <body>
       <div class="myContainer">
            <div class="header">
                <nav>
                    <ul class="main-nav nav nav-pills pull-right">
                        <li role="presentation"><a class="cd-signin" href="main.php"> Start Game</a></li>
                        <li role="presentation"><a class="cd-signin" href="logOut.php"> Sign Out</a></li>
                    </ul>
                </nav>
                <a href="main.php"><h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> Super-Snake</h1></a>
            </div>

            <h1>Lobby: Press "Start Game" to play the best snake game ever!</h1>
                
           <footer class="footer">
                <p>&copy; Connor Smith and Kayla Holcomb 2016</p>
            </footer>

        </div>

    </body>
</html>