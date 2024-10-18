const express = require('express');
const { TimeEntry } = require('../models');
const router = express.Router();

// Get user time entries
router.get('/times', async (req, res) => {
  const userId = req.user.userId;
  const times = await TimeEntry.findAll({ where: { user_id: userId } });
  res.json(times);
});

// Add time entry
router.post('/times', async (req, res) => {
  const { start_time, end_time } = req.body;
  const userId = req.user.userId;
  const newTimeEntry = await TimeEntry.create({ user_id: userId, start_time, end_time, status: 'pending' });
  res.status(201).json(newTimeEntry);
});

module.exports = router;
