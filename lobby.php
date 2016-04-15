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

$room = "Lobby";
?>

<html lang="en">
    <head>
        <title>Super-Snake</title>
        <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../Super-Snake/css/main.css" rel="stylesheet">
        <link href="../Super-Snake/css/modal.css" rel="stylesheet">
        <!-- <link rel="stylesheet" href="../Super-Snake/js/paperjs-v0/examples/css/style.css"> -->
        <!--<script type="text/javascript" src="../Super-Snake/js/paperjs-v0/dist/paper-full.js"></script>-->
        <script src="https://code.jquery.com/jquery-2.1.0.min.js"></script>
        <script src="http://150.252.244.54:5000/socket.io/socket.io.js"></script>
        <script src="../Super-Snake/js/main.js"></script>
        <script type="text/paperscript" canvas="canvas"></script>
        <script>

            var socket = io.connect('http://150.252.244.54:5000');
            var gameRoom = "Lobby";

            socket.on('connect', function(){
                socket.emit('adduser', "<?php echo $first; ?>");
            });

            socket.on('updatechat', function (username, data) {
                $('#conversation').append('<b>'+ username + ':</b> ' + data + '<br>');
            });


            socket.on('updaterooms', function (rooms, current_room) {
                $('#rooms').empty();
                $.each(rooms, function(key, value) {
                    if(value == current_room){
                        $('#rooms').append('<div>' + value + '</div>');
                    }
                    else {
                        $('#rooms').append('<div><a href="#" onclick="switchRoom(\''+value+'\')">' + value + '</a></div>');
                    }
                });
            });

            function switchRoom(room){
                socket.emit('switchRoom', room);
                gameRoom = room;
            }

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

                $('#gamebutton').click(function(){
                    window.location.href = "main.php?room=" + gameRoom;
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
                        <li role="presentation"><a class="cd-signin" href="main.php" > Start Single-Player Game</a></li>
                        <li role="presentation"><a class="cd-signin" href="logOut.php"> Sign Out</a></li>
                    </ul>
                </nav>
                <a href="main.php"><h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> Super-Snake LOBBY</h1></a>
            </div>

            <h2>Join an existing game or create your own!</h2>

            <div style="float:left;width:100px;border-right:1px solid black;height:500px;padding:10px;overflow:scroll-y;">
                <p><u>ROOMS</u></p>
                <div id="rooms"></div>
            </div>

            <div style="float:left;width:500px;height:250px;overflow:scroll-y;padding:10px;">
                <div id="conversation"></div>
                <input id="data" style="width:200px;" />
                <input type="button" id="datasend" value="Send Chat" />
            </div>

            <div style="float:left;width:500px;height:250px;overflow:scroll-y;padding:10px;">
                <div id="room creation"></div>
                <input id="roomname" style="width:200px;" />
                <input type="button" id="roombutton" value="Create New Game" />
             </div>

            <div style="float:left;width:500px;height:250px;overflow:scroll-y;padding:10px;">
                <div id="joingame"></div>
                <input type="button" id="gamebutton" value="Start Game" />
             </div>
        </div>
       <!--  <footer class="footer">
            <p>&copy; Connor Smith and Kayla Holcomb 2016</p>
        </footer> -->
    </body>
</html>