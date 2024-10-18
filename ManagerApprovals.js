import React, { useState, useEffect } from 'react';
import axios from 'axios';

const ManagerApprovals = () => {
  const [pendingApprovals, setPendingApprovals] = useState([]);

  useEffect(() => {
    const fetchApprovals = async () => {
      const token = localStorage.getItem('token');
      const response = await axios.get('/api/manager/approvals', {
        headers: { Authorization: `Bearer ${token}` }
      });
      setPendingApprovals(response.data);
    };
    fetchApprovals();
  }, []);

  const handleApproval = async (id, status) => {
    const token = localStorage.getItem('token');
    await axios.put(`/api/manager/approvals/${id}`, { status }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    // Refresh the approvals list after approval/rejection
  };

  return (
    <div>
      <h2>Pending Approvals</h2>
      <ul>
        {pendingApprovals.map((approval) => (
          <li key={approval.id}>
            {approval.time_entry_id}: 
            <button onClick={() => handleApproval(approval.id, 'approved')}>Approve</button>
            <button onClick={() => handleApproval(approval.id, 'rejected')}>Reject</button>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default ManagerApprovals;
