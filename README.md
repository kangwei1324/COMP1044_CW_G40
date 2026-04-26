# Internship Result Management System

## Project Overview
The Internship Result Management System is a web-based application designed to streamline the assessment process for university students undergoing industrial training. It allows Admin users to manage student profiles, assessor accounts, and internship assignments. Assessors (Lecturers/Industry Supervisors) can securely log in to view their assigned students, enter evaluation marks, and provide qualitative feedback.

## Technologies Used
* **Frontend:** HTML5, CSS3, JavaScript
* **Backend:** PHP
* **Database:** MySQL

## System Architecture & Handling
Our system uses modern practices to maintain security, modularity, and a seamless user experience:
* **Role-Based Authentication:** We use secure PHP sessions coupled with authentication guards (`auth_check.php`) to ensure that Admins and Assessors can only access their respective panels.
* **Component Modularity:** Reusable UI components like the header and retractable sidebar are extracted into a `components/` directory to keep the codebase clean and maintainable.
* **Database Security:** We utilize Object-Oriented `mysqli` with prepared statements (`?` parameters) to prevent SQL injection vulnerabilities.
* **Form Submissions:** The application implements the Post/Redirect/Get (PRG) pattern to handle form submissions, preventing duplicate data entries when users refresh pages.

## Data Validation Strategy
To ensure maximum data integrity and an optimal user experience, we employ a dual-layer validation strategy:
* **Client-Side Validation (JavaScript):** We implemented robust, real-time JavaScript validation that provides instant, inline error feedback to users before a form is submitted. This significantly improves the UX by preventing unnecessary server round trips.
* **Server-Side Validation (PHP):** While JavaScript improves the user experience, we maintain full server-side validation using PHP as a secure fail-safe. This guarantees that no invalid, incomplete, or out-of-range data can ever be inserted into the database, even if client-side scripts are disabled or bypassed.

## How to Run the System
To access the system, ensure your local server (e.g., MAMP, XAMPP) is running with Apache and MySQL enabled. 

1. Navigate to the root directory in your browser (e.g., `http://localhost/COMP1044_CW_G40/`).
2. The system will automatically serve `index.php`.
3. `index.php` acts as the entry point and will seamlessly direct you to the main login page at `auth/login.php`.

## Sample Login Credentials
You can use the following accounts to test the system's role-based access control:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `password123` |
| **Lecturer** | `dr.lim` | `password123` |
| **Industry Supervisor** | `raj.kumar` | `password123` |