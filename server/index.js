const app  = require('express')()
const server = require('http').Server(app);

const io = require('socket.io')(server);

const Redis = require('ioredis');

const redis = new Redis();

redis.psubscribe('*');

redis.on('pmessage', function (pattern, channel, message) {
  const notificacion = JSON.parse(message);
  console.log('message ' + notificacion.event + ' data '+ JSON.stringify(notificacion.data))
  io.emit(notificacion.event, notificacion.data);

});

server.listen(6001);