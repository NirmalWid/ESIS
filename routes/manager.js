const express = require('express');
const { TimeEntry } = require('../models');
const router = express.Router();

// Get pending approvals
router.get('/approvals', async (req, res) => {
  const approvals = await TimeEntry.findAll({ where: { status: 'pending' } });
  res.json(approvals);
});

// Approve or reject time entry
router.put('/approvals/:id', async (req, res) => {
  const { status } = req.body;
  await TimeEntry.update({ status }, { where: { id: req.params.id } });
  res.status(200).json({ message: 'Time entry updated' });
});

module.exports = router;
