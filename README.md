# 🎓 Student Result Management System (SRMS Pro)

> **A modern, secure, and automated solution for managing academic records.**

---

## 📖 Overview

**SRMS ** is a comprehensive web application designed to digitize the manual process of student result management. It bridges the gap between administrators, teachers, and students by providing a centralized platform for data handling, automated grade calculations, and secure result declaration.

Key features include **Role-Based Access Control (RBAC)**, **Bulk CSV Import**, **Analytics Dashboards**, and **Automated ID Card Generation**.

---

## 🚀 Key Features

### 👨‍💻 Administrator Module
* **Dashboard Analytics:** Real-time visual charts for student distribution and subject performance.
* **Class & Subject Management:** Full CRUD operations for academic structures.
* **Student Management:** Register students manually or via **Bulk CSV Import**.
* **Result Declaration:** Automated grading logic (A-F grading system).
* **Notice Board:** Post digital notices visible to all students.
* **Leaderboard:** View top-performing students instantly.

### 👩‍🎓 Student Portal
* **Secure Login:** Authentication via Roll ID and Password.
* **Result Card:** View and print detailed mark sheets with grades.
* **Profile Dashboard:** Access personal details and ID card.
* **Change Password:** Self-service password management for account security.
* **Digital Notices:** Stay updated with the latest school announcements.

---

## 🛠️ Technology Stack

| Component | Technology |
| :--- | :--- |
| **Frontend** | HTML5, CSS3 (Glassmorphism UI), JavaScript, Chart.js |
| **Backend** | PHP 8.0+ (PDO Extension) |
| **Database** | MySQL (Relational Schema) |
| **Server** | Apache (XAMPP/WAMP) |
| **Tools** | VS Code, Git, Mermaid.js (UML) |

---


## ⚙️ Installation & Setup

Follow these steps to run the project locally:

### 1. Prerequisites
* Install **XAMPP** (or WAMP/MAMP).
* Ensure **Apache** and **MySQL** services are running.

### 2. Clone the Repository
```bash
git clone https://github.com/lazina-25/student-result-management-system.git

```

### 3. Database Configuration

1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named **`srms`**.
3. Import the `srms.sql` file provided in the `database/` folder.

### 4. Configure Connection

Open `includes/config.php` and verify the credentials:

```php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS',''); // Default XAMPP password is empty
define('DB_NAME','srms');

```

### 5. Run the Project

* Move the project folder to `C:/xampp/htdocs/`.
* Open your browser and navigate to:
  `http://localhost/srms_modern/`

---

## 🧪 Testing Credentials

| Role | Username / Roll ID | Password |
| --- | --- | --- |
| **Admin** | `admin` | `admin` |
| **Student** | `101` | `101` (Default) |




---


<div align="center">
<sub>Built with ❤️ for PBL Project</sub>
</div>

```

```
