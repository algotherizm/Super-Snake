// Require dependencies
var clients = [];
var app = require('http').createServer(), fs = require('fs'), io = require('socket.io').listen(app);

//http://stackoverflow.com/questions/19156636/node-js-and-socket-io-creating-room
var usernames = {};
var rooms = ['Lobby'];
var playercount = [['Lobby', 0]];
var player1 = [];
var player2 = [];
// creating the server ( localhost:5000 )
app.listen(5000);
 io.sockets.on('connection', function(socket) {
  socket.on('adduser', function (username) {
        socket.username = username;
        socket.room = 'Lobby';         
        playercount[0][1] += 1;
        usernames[username] = username;
        socket.join('Lobby');          
        socket.emit('updatechat', 'SERVER', 'you have connected to Lobby');
        socket.broadcast.to('Lobby').emit('updatechat', 'SERVER', username + ' has connected to this room');
        socket.emit('updaterooms', rooms, 'Lobby', playercount);
        socket.broadcast.to('Lobby').emit('updaterooms', rooms, socket.room, playercount);
    });
  socket.on('create', function(room) {
        rooms.push(room);
        playercount.push([room, 0]);
        socket.emit('updaterooms', rooms, socket.room, playercount);
        socket.broadcast.to('Lobby').emit('updaterooms', rooms, socket.room, playercount);
    });
  socket.on('sendchat', function(data) {
        io.sockets["in"](socket.room).emit('updatechat', socket.username, data);
    });

    socket.on('switchRoom', function(newroom) {
        var oldroom;
        var playernumber;
        oldroom = socket.room;
        for (var i = 0; i < playercount.length; i++) {
            if(playercount[i][0] == oldroom){
                playercount[i][1] -= 1;
            }
        }
        socket.leave(socket.room);
        socket.join(newroom);
        socket.emit('updatechat', 'SERVER', 'you have connected to ' + newroom);
        socket.broadcast.to(oldroom).emit('updatechat', 'SERVER', socket.username + ' has left this room');
        socket.room = newroom;
        for (var i = 0; i < playercount.length; i++) {
            if(playercount[i][0] == newroom){ 
                if(playercount[i][1] == 0)
                    playernumber = 1;
                else
                    playernumber = 2;
                playercount[i][1] += 1;
            }
        }
        socket.broadcast.to(newroom).emit('updatechat', 'SERVER', socket.username + ' has joined this room');
        socket.emit('updaterooms', rooms, newroom, playercount, playernumber);
        for(var i = 0; i < playercount.length; i++)
            socket.broadcast.to('Lobby').emit('updaterooms', rooms, socket.room, playercount);
        //console.log(playercount);
    });

    socket.on('disconnect', function() {
        delete usernames[socket.username];
        for (var i = 0; i < playercount.length; i++) {
            if(playercount[i][0] == socket.room){
                if(playercount[i][1]!=0)
                    playercount[i][1] -= 1;
            }
        }
        io.sockets.emit('updateusers', usernames);
        socket.broadcast.emit('updatechat', 'SERVER', socket.username + ' has disconnected');
        socket.leave(socket.room);
    });

    socket.on('connectGame', function(username, gameRoom){
        socket.username = username;
        socket.room = gameRoom;
        usernames[username] = username;
        socket.join(gameRoom);
        socket.emit('updaterooms', rooms, gameRoom, playercount);
    });

    socket.on('updatePlayer', function(player, position, food){
        if(player == 1)
        {
            player1 = position;
            socket.broadcast.to(socket.room).emit('updatePosition', player, player1, food);
        }
        else
        {
            player2 = position;
            socket.broadcast.to(socket.room).emit('updatePosition', player, player2, food);
        }
        //console.log(position);
    });

    socket.on('gameOver', function(player){
        if(player == 1)
            socket.braodcast.to(socket.room).emit('Winner', 2);
        else
            socket.braodcast.to(socket.room).emit('Winner', 1);
    });
 });