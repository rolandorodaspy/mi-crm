const express = require('express');
const router = express.Router();
const db = require('../db/database');

// Obtener todos los leads
router.get('/leads', (req, res) => {
  try {
    const rows = db.prepare('SELECT * FROM leads ORDER BY fecha_creacion DESC').all();
    res.json({ leads: rows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Crear un nuevo lead
router.post('/leads', (req, res) => {
  const { nombre, email, telefono, empresa } = req.body;
  
  if (!nombre || !email) {
    return res.status(400).json({ error: 'Nombre y email son requeridos' });
  }

  try {
    const stmt = db.prepare('INSERT INTO leads (nombre, email, telefono, empresa) VALUES (?, ?, ?, ?)');
    const info = stmt.run(nombre, email, telefono, empresa);
    res.json({ 
      id: info.lastInsertRowid, 
      message: 'Lead creado exitosamente',
      lead: { id: info.lastInsertRowid, nombre, email, telefono, empresa }
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Actualizar estado de un lead
router.put('/leads/:id', (req, res) => {
  const { estado } = req.body;
  const { id } = req.params;

  try {
    const stmt = db.prepare('UPDATE leads SET estado = ? WHERE id = ?');
    stmt.run(estado, id);
    res.json({ message: 'Lead actualizado exitosamente' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Eliminar un lead
router.delete('/leads/:id', (req, res) => {
  const { id } = req.params;

  try {
    const stmt = db.prepare('DELETE FROM leads WHERE id = ?');
    stmt.run(id);
    res.json({ message: 'Lead eliminado exitosamente' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Obtener todos los clientes
router.get('/clientes', (req, res) => {
  try {
    const rows = db.prepare('SELECT * FROM clientes ORDER BY fecha_registro DESC').all();
    res.json({ clientes: rows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Convertir lead a cliente
router.post('/clientes/convertir/:leadId', (req, res) => {
  const { leadId } = req.params;
  const { valor_contrato } = req.body;

  try {
    const lead = db.prepare('SELECT * FROM leads WHERE id = ?').get(leadId);
    
    if (!lead) {
      return res.status(404).json({ error: 'Lead no encontrado' });
    }

    const stmt = db.prepare('INSERT INTO clientes (lead_id, nombre, email, telefono, empresa, valor_contrato) VALUES (?, ?, ?, ?, ?, ?)');
    const info = stmt.run(leadId, lead.nombre, lead.email, lead.telefono, lead.empresa, valor_contrato || 0);
    
    // Actualizar estado del lead
    db.prepare('UPDATE leads SET estado = ? WHERE id = ?').run('convertido', leadId);
    
    res.json({ 
      message: 'Lead convertido a cliente exitosamente',
      clienteId: info.lastInsertRowid
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
