const express = require('express');
const cors = require('cors');
const authRoutes = require('./routes/auth');
const userRoutes = require('./routes/user');
const managerRoutes = require('./routes/manager');
const adminRoutes = require('./routes/admin');
const { authenticateToken } = require('./middleware/auth');

const app = express();

app.use(cors());
app.use(express.json());

app.use('/api/auth', authRoutes);
app.use('/api/user', authenticateToken, userRoutes);
app.use('/api/manager', authenticateToken, managerRoutes);
app.use('/api/admin', authenticateToken, adminRoutes);

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
