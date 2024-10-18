import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Dashboard = () => {
  const [times, setTimes] = useState([]);

  useEffect(() => {
    const fetchTimes = async () => {
      const token = localStorage.getItem('token');
      const response = await axios.get('/api/user/times', {
        headers: { Authorization: `Bearer ${token}` }
      });
      setTimes(response.data);
    };
    fetchTimes();
  }, []);

  const handleAddTime = async () => {
    // Add logic to track time (start/end)
  };

  return (
    <div>
      <h2>User Dashboard</h2>
      <button onClick={handleAddTime}>Add Time Entry</button>
      <ul>
        {times.map((time) => (
          <li key={time.id}>
            {time.start_time} - {time.end_time} ({time.status})
          </li>
        ))}
      </ul>
    </div>
  );
};

export default Dashboard;
