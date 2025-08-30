# Matendeni ECD Management System

A web-based management system designed for Matendeni Early Childhood Development (ECD) center. This system allows administrators to manage students, fees, marks, and track completed students, with an easy-to-use dashboard that displays real-time statistics.

---

## Features

### Dashboard
- Displays total students, fees collected, and balances for the active term.
- Graphical representation of income vs balance using Chart.js.
- Highlights current active term (name + year).

### Student Management
- Add, view, and manage students.
- Track completed students separately with their completion year.
- Ability to view payment history and marks history for each student.

### Fees Management
- Add student fee payments.
- View fee history for active and completed students.
- Calculates total income and outstanding balances automatically.

### Marks Management
- Add and view student marks.
- Generate PDF reports of marks filtered by class, term, and exam.
- Handles students without marks gracefully.

### Additional Features
- Responsive sidebar for easy navigation.
- Color-coded interface: Purple primary, Orange accents, White background.
- Logout functionality.

---

## Technologies Used
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL / MariaDB
- **PDF Generation:** Dompdf
- **Charts:** Chart.js

---

## Installation
1. Clone or download the repository into your local server directory (e.g., `htdocs` for XAMPP).
2. Import the provided database into MySQL using phpMyAdmin.
3. Update `db.php` with your database credentials.
4. Ensure the `vendor` folder is present for Dompdf functionality.
5. Access the project via `http://localhost/MATENDENI%20ECD%20MANAGEMENT/admin/`.

---

## Screenshots

| Screenshot | Description |
|------------|-------------|
| ![1](screenshots/1.jpg) | Dashboard overview with total students and income |
| ![2](screenshots/2.png) | Adding a new student |
| ![3](screenshots/3.png) | Student list view |
| ![4](screenshots/4.png) | Adding fees for a student |
| ![5](screenshots/5.png) | Fee history for active term |
| ![6](screenshots/6.png) | Adding marks for a student |
| ![7](screenshots/7.png) | Marks history view |
| ![8](screenshots/8.png) | Completed students list |
| ![9](screenshots/9.png) | Viewing payment history for completed student |
| ![10](screenshots/10.png) | Viewing marks history for completed student |
| ![11](screenshots/10.png) | Viewing marks history for completed student |
| ![12](screenshots/10.png) | Viewing marks history for completed student |
| ![13](screenshots/10.png) | Viewing marks history for completed student |


---

## Notes
- Ensure PHP sessions are enabled for proper login/logout functionality.
- The system dynamically calculates totals and balances for active terms only.
- Completed students’ payments and marks are viewable but cannot be edited.

---

## License
This project is developed for educational purposes. No commercial license is provided.
