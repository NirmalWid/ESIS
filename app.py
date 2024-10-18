import os
from flask import Flask, render_template, request, redirect, url_for, session
import firebase_admin
from firebase_admin import credentials, auth, db
from datetime import datetime
from google_auth_oauthlib.flow import Flow
from googleapiclient.discovery import build

app = Flask(__name__)
app.secret_key = os.urandom(24)  # Secret key for session management

# Firebase Admin SDK setup
base_dir = os.path.dirname(os.path.abspath(__file__))
cred_path = os.path.join(base_dir, "firebase_admin_credentials.json")

cred = credentials.Certificate(cred_path)
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://employeetimetrackingsystem-default-rtdb.firebaseio.com/'
})


@app.route('/')
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        
        try:
            # Authenticate user
            user = auth.get_user_by_email(email)
            session['user_id'] = user.uid
            session['email'] = user.email

            # Retrieve user details to store username
            ref = db.reference(f'employees/{user.uid}')
            employee_data = ref.get()
            
            if employee_data:
                session['role'] = 'employee'
                session['name'] = employee_data['name']  # Store employee name
                return redirect(url_for('employee_home'))
            else:
                ref = db.reference(f'managers/{user.uid}')
                manager_data = ref.get()
                if manager_data:
                    session['role'] = 'manager'
                    session['name'] = manager_data['name']  # Store manager name
                    return redirect(url_for('dashboard'))  # Redirect to approvals
                else:
                    session['role'] = 'admin'
                    session['name'] = 'Admin'  # Handle admin case
                    return redirect(url_for('dashboard'))

        except Exception as e:
            return str(e), 401

    return render_template('index.html')



@app.route('/dashboard')
def dashboard():
    return render_template('dashboard.html')


# Manager Approvals Route
@app.route('/manager/approvals', methods=['GET'])
def manager_approvals():
    # Retrieve all time entries from the database
    time_entries_ref = db.reference('time_entries')
    all_time_entries = time_entries_ref.get() or {}  # Get all entries or an empty dict

    # Prepare list to hold pending entries
    pending_entries = []

    # Loop through all entries and filter by status
    for user_id, entries in all_time_entries.items():  # Iterate through users
        for entry_id, entry_data in entries.items():  # Iterate through their time entries
            if entry_data.get('status') == 'pending':
                pending_entries.append({
                    'id': entry_id,  # Entry ID for later approval or rejection
                    'employee_name': entry_data.get('employee_name'),
                    'date': entry_data.get('date'),
                    'start_time': entry_data.get('start_time'),
                    'end_time': entry_data.get('end_time'),
                    'hours_worked': entry_data.get('hours_worked'),
                })

    return render_template('manager_approvals.html', time_entries=pending_entries)

# Approval Route
@app.route('/manager/approve_entry/<entry_id>', methods=['POST'])
def approve_entry(entry_id):
    # Reference the time entry in the database
    time_entry_ref = db.reference('time_entries')

    # Iterate over each employee's entries to find the specific entry to approve
    all_time_entries = time_entry_ref.get() or {}
    for user_id, entries in all_time_entries.items():
        for entry_key, entry_data in entries.items():
            if entry_key == entry_id:
                # Update the status to approved
                entry_data['status'] = 'approved'
                # Save the updated entry back to the database
                time_entry_ref.child(user_id).child(entry_key).update({'status': 'approved'})
                return redirect(url_for('manager_approvals'))

    return "Entry not found", 404

# Rejection Route
@app.route('/manager/reject_entry/<entry_id>', methods=['POST'])
def reject_entry(entry_id):
    # Reference the time entry in the database
    time_entry_ref = db.reference('time_entries')

    # Iterate over each employee's entries to find the specific entry to reject
    all_time_entries = time_entry_ref.get() or {}
    for user_id, entries in all_time_entries.items():
        for entry_key, entry_data in entries.items():
            if entry_key == entry_id:
                # Update the status to rejected
                entry_data['status'] = 'rejected'
                # Save the updated entry back to the database
                time_entry_ref.child(user_id).child(entry_key).update({'status': 'rejected'})
                return redirect(url_for('manager_approvals'))

    return "Entry not found", 404

@app.route('/view/reports')
def view_reports():
    return render_template('view_reports.html')


# Route for SignUp
@app.route('/signup', methods=['GET', 'POST'])
def signup():
    if request.method == 'POST':
        name = request.form['name']
        email = request.form['email']
        password = request.form['password']
        role = request.form['role']
        
        try:
            # Create user in Firebase Authentication
            user = auth.create_user(
                email=email,
                password=password
            )

            # Save user to the appropriate collection in Firebase DB
            if role == 'employee':
                db.reference(f'employees/{user.uid}').set({
                    'name': name,
                    'email': email,
                    'role': role
                })
            elif role == 'manager':
                db.reference(f'managers/{user.uid}').set({
                    'name': name,
                    'email': email,
                    'role': role
                })

            return redirect(url_for('login'))

        except Exception as e:
            return str(e), 400

    return render_template('signup.html')


@app.route('/employee/home', methods=['GET', 'POST'])
def employee_home():
    user_id = session.get('user_id')  # Use .get() to avoid KeyError
    if user_id is None:
        return redirect(url_for('login'))  # Redirect to login if user_id is not set

    if request.method == 'POST':
        date = request.form['date']
        start_time = request.form['start_time']
        end_time = request.form['end_time']

        # Calculate hours worked
        hours_worked = calculate_hours(start_time, end_time)

        # Save to database without manager_id
        db.reference(f'time_entries/{user_id}').push({
            'date': date,
            'start_time': start_time,
            'end_time': end_time,
            'hours_worked': hours_worked,
            'status': 'pending',  # Set the initial status to pending
            'employee_name': session['name'],  # Add employee name for manager view
        })

        return redirect(url_for('employee_home'))

    # Retrieve time entries for the logged-in user
    time_entries_ref = db.reference(f'time_entries/{user_id}')
    time_entries = time_entries_ref.get() or {}

    # Format time entries for display
    formatted_entries = []
    for key, entry in time_entries.items():
        formatted_entries.append({
            'id': key,  # Include the ID for deletion purposes
            'date': entry['date'],
            'start_time': entry['start_time'],
            'end_time': entry['end_time'],
            'hours_worked': entry['hours_worked'],
            'status': entry.get('status', 'pending')  # Default status to pending if not set
        })

    return render_template('employee_home.html', time_entries=formatted_entries)




# Helper function to calculate hours worked
def calculate_hours(start_time, end_time):
    fmt = '%H:%M'
    start_dt = datetime.strptime(start_time, fmt)
    end_dt = datetime.strptime(end_time, fmt)
    total_hours = (end_dt - start_dt).seconds / 3600
    return total_hours


@app.route('/employee/delete/<entry_id>', methods=['POST'])
def delete_time_entry(entry_id):
    user_id = session['user_id']  # Get the user ID from the session
    time_entry_ref = db.reference(f'time_entries/{user_id}/{entry_id}')
    
    # Check if the entry exists before attempting to delete
    entry = time_entry_ref.get()
    if entry is not None:
        time_entry_ref.delete()  # This deletes the entry
    return redirect(url_for('employee_home'))  # Redirect back to employee home




if __name__ == '__main__':
    app.run(debug=True)