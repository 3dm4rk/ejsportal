# EJS Portal – Employee Job System

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## 📌 Overview

**EJS Portal** is a full-featured **HR Management System** built with PHP, MySQL, and a modern Glassmorphism UI. It supports three distinct user roles – **Admin**, **HR**, and **Worker** – each with tailored dashboards and permissions.

Workers can log their daily activities (Job Login, Break In/Out, Lunch In/Out, Job Logout) with biometric simulation. HR and Admin can view attendance records in a clean calendar, generate reports, and manage users.

The system is secure, responsive, and ready for real‑company deployment.

---

## ✨ Features

### 👑 Admin
- Full user management (CRUD)
- Role management (create, delete custom roles)
- View all attendance logs
- Analytics & reports (statistics)
- System settings (company name, timezone, work hours, etc.)
- Activity audit logs
- Dashboard with real‑time stats

### 👔 HR
- Calendar view of attendance (daily status, login, logout, breaks, lunch)
- Filter by employee, month, year
- Export reports to PDF / Excel (placeholder – ready for integration)
- View list of workers
- Monitor attendance summaries (total hours, overtime, late arrivals)

### 👷 Worker
- Biometric‑simulated attendance (fingerprint icon)
- Record:
  - **Job Login** (date/time)
  - **Break In / Break Out**
  - **Lunch In / Lunch Out**
  - **Job Logout** (date/time)
- View personal attendance history
- See today’s activity with live status

### 🎨 UI/UX
- Glassmorphism design with smooth animations
- Dark / Light mode toggle
- Responsive sidebar navigation
- Dashboard cards with key metrics
- Mobile‑friendly layout
- Clean typography & Font Awesome icons

---

## 🛠️ Technologies

- **Backend**: PHP 7.4+ (PDO, password_hash, sessions)
- **Database**: MySQL 5.7+ (normalized schema, foreign keys)
- **Frontend**: HTML5, CSS3 (custom variables, flexbox, grid), JavaScript (ES6)
- **Icons**: Font Awesome 6
- **Security**: Prepared statements, CSRF tokens, RBAC, audit logging

---


   ```bash
   git clone https://github.com/yourusername/ejs-portal.git
   cd ejs-portal
