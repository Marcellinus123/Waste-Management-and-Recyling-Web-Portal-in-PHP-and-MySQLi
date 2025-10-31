const WebSocket = require('ws');
const http = require('http');
const mysql = require('mysql2/promise');

const server = http.createServer();
const wss = new WebSocket.Server({ server });

const pool = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'waste_wizard_db',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

const clients = new Map();

wss.on('connection', (ws, req) => {
  const userId = new URL(req.url, `http://${req.headers.host}`).searchParams.get('userId');
  
  if (userId) {
    clients.set(userId, ws);
    console.log(`Client connected: ${userId}`);
  }

  ws.on('close', () => {
    clients.forEach((value, key) => {
      if (value === ws) {
        clients.delete(key);
        console.log(`Client disconnected: ${key}`);
      }
    });
  });
});

// Check for new messages every 3 seconds
setInterval(async () => {
  try {
    if (clients.size === 0) return;

    const [rows] = await pool.query(`
      SELECT m.*, t.user_id 
      FROM support_messages m
      JOIN support_tickets t ON m.ticket_id = t.ticket_id
      WHERE m.sender_type = 'user'
      AND m.is_delivered = FALSE
      AND t.user_id IN (${Array.from(clients.keys()).join(',') || '0'})
    `);
    
    rows.forEach(async row => {
      const ws = clients.get(row.user_id);
      if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
          type: 'new_message',
          data: {
            ticket_id: row.ticket_id,
            message_id: row.message_id,
            message: row.message,
            created_at: row.created_at,
            sender_name: 'User'.row.user_id
          }
        }));

        // Mark as delivered
        await pool.query('UPDATE support_messages SET is_delivered = TRUE WHERE message_id = ?', [row.message_id]);
      }
    });
  } catch (err) {
    console.error('Message polling error:', err);
  }
}, 3000);

server.listen(8082, () => {
  console.log('Message WebSocket server running on port 8082');
});