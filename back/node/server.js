const express = require("express");
const WebSocket = require("ws");
const http = require("http");
const cors = require("cors");
const url = require("url");

const app = express();
app.use(
  cors({
    origin: process.env.FRONTEND_URL || "http://localhost:3000",
  })
);

const server = http.createServer(app);
const wss = new WebSocket.Server({ noServer: true }); // Importante para manejar múltiples rutas

// Mapa para almacenar butacas bloqueadas: { screeningId: Set(seatIds) }
const lockedSeats = new Map();

// Manejar upgrade de conexión HTTP a WebSocket
server.on("upgrade", (request, socket, head) => {
  const pathname = new URL(request.url, `http://${request.headers.host}`)
    .pathname;

  // Nueva validación de ruta
  if (!pathname.startsWith("/screening/")) {
    socket.write("HTTP/1.1 404 Not Found\r\n\r\n");
    socket.destroy();
    return;
  }

  const screeningId = pathname.split("/")[2]; // Extraer ID de /screening/3

  wss.handleUpgrade(request, socket, head, (ws) => {
    wss.emit("connection", ws, request, screeningId);
  });
});

wss.on("connection", (ws, req, screeningId) => {
  // Configurar tiempo máximo de inactividad (30 segundos)
  ws.isAlive = true;
  ws.on("pong", () => (ws.isAlive = true));

  // Inicializar conjunto para el screening
  if (!lockedSeats.has(screeningId)) {
    lockedSeats.set(screeningId, new Set());
  }

  // Enviar estado inicial al cliente
  ws.send(
    JSON.stringify({
      type: "INITIAL_STATE",
      lockedSeats: Array.from(lockedSeats.get(screeningId)),
    })
  );

  // Heartbeat para detectar conexiones caídas
  const interval = setInterval(() => {
    if (!ws.isAlive) return ws.terminate();
    ws.isAlive = false;
    ws.ping();
  }, 30000);

  ws.on("message", (message) => {
    try {
      const data = JSON.parse(message);
      switch (data.type) {
        case "LOCK_SEAT":
          handleLockSeat(screeningId, data.seatId, ws);
          break;
        case "UNLOCK_SEAT":
          handleUnlockSeat(screeningId, data.seatId);
          break;
        case "PURCHASE_COMPLETE":
          handlePurchase(screeningId, data.seatIds);
          break;
      }
    } catch (error) {
      console.error("Error processing message:", error);
    }
  });

  ws.on("close", () => {
    clearInterval(interval);
    // Liberar asientos al desconectar
    lockedSeats.get(screeningId).forEach((seatId) => {
      if (ws.seatSet?.has(seatId)) {
        handleUnlockSeat(screeningId, seatId);
      }
    });
  });
});

// Función para bloquear asiento
function handleLockSeat(screeningId, seatId, ws) {
  const seats = lockedSeats.get(screeningId);

  if (seats.has(seatId)) {
    ws.send(
      JSON.stringify({
        type: "SEAT_CONFLICT",
        seatId,
        message: "Asiento ya reservado",
      })
    );
    return;
  }

  seats.add(seatId);
  broadcast(screeningId, {
    type: "SEAT_LOCKED",
    seatId,
  });
}

// Función para desbloquear asiento
function handleUnlockSeat(screeningId, seatId) {
  const seats = lockedSeats.get(screeningId);
  if (seats.delete(seatId)) {
    broadcast(screeningId, {
      type: "SEAT_RELEASED",
      seatId,
    });
  }
}

// Función para finalizar compra
function handlePurchase(screeningId, seatIds) {
  seatIds.forEach((seatId) => handleUnlockSeat(screeningId, seatId));
}

// Difusión a clientes del mismo screening
function broadcast(screeningId, message) {
  wss.clients.forEach((client) => {
    if (
      client.readyState === WebSocket.OPEN &&
      client.screeningId === screeningId
    ) {
      client.send(JSON.stringify(message));
    }
  });
}

const PORT = process.env.PORT || 3001;
server.listen(PORT, "0.0.0.0", () => {
  console.log(`Servidor escuchando en puerto ${PORT}`);
});
