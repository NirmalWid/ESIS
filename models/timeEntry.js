module.exports = (sequelize, DataTypes) => {
    const TimeEntry = sequelize.define('TimeEntry', {
      user_id: DataTypes.INTEGER,
      start_time: DataTypes.DATE,
      end_time: DataTypes.DATE,
      status: DataTypes.ENUM('pending', 'approved', 'rejected'),
    });
    return TimeEntry;
  };
  