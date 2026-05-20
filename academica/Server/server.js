var http = require('http').Server();
var io = require('socket.io')(http);
const { MongoClient, ObjectId } = require("mongodb");
var url = "mongodb://localhost:27017";
const client = new MongoClient(url);
const dbname = "chats_ugb";

async function conectarMongo() {
    await client.connect();
    return client.db(dbname);
}

io.on('connect', (socket) => {
    console.log('Un usuario se ha conectado');

    socket.on('mensajeRecibido', async (data) => {
        let db = await conectarMongo(),
            collection = db.collection('chats'),
            result = collection.insertOne({ titulo: data.titulo, mensaje: data.mensaje, fecha:new Date() });
        io.emit('mensajeEnviar', data);
    });

});

http.listen(3000, () => {
    console.log('Escuchando en el puerto 3000');
});

//agregar recuperacion de mongodb 