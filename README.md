The default administrator login credentials should be:

Username: admin
Password: admin123
User Roles

The system should support three different user roles:

Admin (Highest Level)
Can create, edit, and delete all user accounts.
Can create other Admin, HR, and Worker accounts.
Has full access to every feature of the system.
Can manage system settings and view all employee records and reports.
HR
Can view reports and attendance records for Worker accounts only.
Cannot create or manage Admin accounts.
Can generate attendance reports, view employee time logs, and monitor worker activity.
Worker
Can log in using their assigned account.
Can use biometric authentication (such as a fingerprint scanner, if available) to record attendance.
Can perform the following attendance actions:
Job Login (records date and time)
Break In / Break Out
Lunch In / Lunch Out
Job Logout (records date and time)
Can view their own attendance history.
HR Dashboard

The HR dashboard should display attendance records in a clean and professional calendar view. Each day should clearly show:

Login time
Break time
Lunch time
Logout time
Total hours worked
Overtime hours (if applicable)
Late arrivals
Attendance status (Present, Late, Absent, Leave)

HR should also be able to:

Filter records by employee, date, week, month, or year.
Export attendance reports to PDF or Excel.
Search employees quickly.
View summaries such as total work hours, total overtime, absences, and late occurrences.
Admin Dashboard

The Admin dashboard should include:

User management
Role management
Attendance monitoring
Reports and analytics
Calendar overview
Employee list
System settings
Activity logs
Dashboard statistics showing:
Total employees
Employees currently working
Employees on break
Employees at lunch
Employees logged out
Design Requirements

The website should have a modern, professional, and responsive design with:

Glassmorphism or modern UI styling
Smooth animations and transitions
Professional color palette
Dashboard cards with statistics
Sidebar navigation
Dark and light mode support
Mobile-friendly responsive layout
Clean typography and icons

The interface should look polished and comparable to a commercial HR management system.

Database

Use MySQL as the database and organize it with proper relational tables for:

Users
Roles
Attendance logs
Break records
Lunch records
Activity logs
System settings

The database should be normalized and follow best practices for scalability and security.

Security

The system should implement:

Password hashing using password_hash()
Secure PHP sessions
SQL injection protection using prepared statements
CSRF protection
Role-based access control (RBAC)
Secure login and logout functionality
Audit logs for important administrative actions

The overall system should be professional, secure, scalable, and suitable for deployment in a real company environment.
