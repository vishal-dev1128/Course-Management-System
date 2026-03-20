# Product Requirements Document (PRD)
## EduManage — Course Management System

**Version:** 3.0  
**Date:** March 2026  
**Status:** Delivered (Updated)

---

## 1. Product Overview

**EduManage** is a web-based Course Management System (CMS) designed to manage academic courses, instructors, and student enrollments within an educational institution. The platform provides a centralized digital hub for administrators, instructors, and students to perform their respective tasks efficiently.

**Technology Stack:**

| Component   | Technology              |
|-------------|-------------------------|
| Frontend    | HTML, Tailwind CSS, JavaScript |
| Backend     | PHP (procedural + PDO)  |
| Database    | MySQL                   |
| Server      | Apache (XAMPP)          |
| Icons       | Google Material Symbols |
| Fonts       | Google Fonts (Inter)    |

---

## 2. Problem Statement

Many educational institutions rely on manual processes (spreadsheets, paperwork) to manage courses and student records. This creates:

- Difficulty managing large numbers of students and courses
- Data redundancy and human errors
- Inefficient communication between instructors and students
- Lack of centralized access to course and enrollment information

EduManage addresses all of these by providing a structured, secure, and role-based digital platform.

---

## 3. Objectives

- Provide a centralized system for managing courses, students, and instructors
- Allow students to browse and enroll in courses online
- Enable instructors to manage their courses and view enrolled students
- Allow administrators to control the entire platform from a single dashboard
- Maintain secure, session-based authentication for all users

---

## 4. Target Users & Roles

### 4.1 Administrator
Manages the entire platform.
- Manage users (add, edit, delete)
- Create, edit, and delete courses
- Assign instructors to courses
- Monitor enrollments and system activity
- View system logs

### 4.2 Instructor
Manages assigned courses.
- View assigned courses with enrollment counts
- View list of enrolled students per course
- Update course details and descriptions

### 4.3 Student
Accesses and enrolls in courses.
- Register and log in
- Browse the course catalog with images and filters
- Enroll in available courses
- View enrolled and recommended courses on dashboard

---

## 5. System Scope

### In Scope
- User authentication (register/login/logout) with role-based access
- Admin dashboard with live stats and enrollment trend chart
- Full course CRUD (Create, Read, Update, Delete) with image upload
- Student enrollment system with duplicate prevention
- Instructor portal (assigned courses, student lists)
- Student portal (catalog, my courses, dashboard with progress)
- Feedback/contact system
- Dark/light mode toggle across all pages
- Responsive design for all screen sizes

### Out of Scope
- Video streaming or LMS content delivery
- Payment gateway integration
- AI-based course recommendations
- Mobile native applications

---

## 6. Functional Requirements

### 6.1 User Registration & Login
- Registration form with: name, email, password, role selection
- Password hashing with `password_hash()`
- Email uniqueness validation
- Session-based login with role-based redirection
- Logout functionality that destroys session

### 6.2 Admin Dashboard
- Live stat cards: Total Users, Total Courses, Enrollments, Active Instructors
- Enrollment Trends bar chart (last 7 days) with visible counts
- System Activity feed (recent enrollments)
- Randomize Instructors: Bulk assignment tool for testing and data distribution
- Recent Enrollments table with student name, course, and date

### 6.3 Course Management
- Admin and instructors can create/edit/delete courses
- Course fields: title, description, category, academic year, status, instructor, image
- Course image upload (JPG/PNG/WEBP, max 5MB)
- Course listing with filtering by category, year, status

### 6.4 Student Enrollment
- Students can browse course catalog with images and metadata
- One-click enrollment with duplicate prevention
- Enrolled courses shown on dashboard and "My Courses" page
- Recommended and newly added courses displayed on student dashboard
- Interactive course listing with visual search and category filtering: Total Students, Active Courses, Avg Completion, Rating
- Grid view of assigned courses with images, year badges, and student counts
- Recent students table per course

### 6.5 Instructor Dashboard
- Overview stats: Total Students, Active Courses, Avg Completion, Rating
- Grid view of assigned courses with images, year badges, and student counts
- Recent students table per course

### 6.6 Student Dashboard
- Enrolled courses grid with images, progress, and year badges
- Recommended courses list with images
- New arrivals horizontal scroll section
- Real-time search filter on all course sections

### 6.7 Public Pages
- **Home (index.php):** Hero section with dynamic course info + featured courses grid
- **Courses (courses.php):** Full course listing with images, filters (category, year), search
- **About:** Platform and team overview
- **Contact:** Feedback/inquiry form
- **Help Center, Privacy Policy:** Static informational pages

---

## 7. Non-Functional Requirements

### Performance
- Page load time under 3 seconds under normal load
- Efficient SQL queries using PDO prepared statements

### Security
- `password_hash()` / `password_verify()` for credentials
- PDO prepared statements to prevent SQL injection
- `htmlspecialchars()` on all output to prevent XSS
- Session-based authentication checked on every protected page
- Role enforcement via `requireRole()` function
- File upload validation: type whitelist (jpg, png, webp) and max size

### Usability
- Clean, modern UI using Tailwind CSS
- Dark/light mode support across all pages
- Responsive design (mobile → desktop)
- Accessible icons via Material Symbols

### Scalability
- Modular PHP file structure (auth/, api/, admin/, student/, instructor/, includes/, config/)
- Centralized DB connection via `config/db.php`
- Reusable sidebar includes for each role

---

## 8. Database Design

### `users`
| Field      | Type                                   |
|------------|----------------------------------------|
| id         | INT (PK, AUTO_INCREMENT)               |
| name       | VARCHAR(100)                           |
| email      | VARCHAR(150) UNIQUE                    |
| password   | VARCHAR(255)                           |
| role       | ENUM('admin','instructor','student')   |
| created_at | DATETIME                               |

### `courses`
| Field          | Type                             |
|----------------|----------------------------------|
| id             | INT (PK, AUTO_INCREMENT)         |
| title          | VARCHAR(200)                     |
| description    | TEXT                             |
| category       | VARCHAR(100)                     |
| academic_year  | VARCHAR(50)                      |
| status         | ENUM('active','inactive','draft')|
| instructor_id  | INT (FK → users.id)              |
| image          | VARCHAR(255)                     |
| created_at     | DATETIME                         |

### `enrollments`
| Field       | Type                    |
|-------------|-------------------------|
| id          | INT (PK, AUTO_INCREMENT)|
| student_id  | INT (FK → users.id)     |
| course_id   | INT (FK → courses.id)   |
| enrolled_at | DATETIME                |

### `customer_feedback`
| Field        | Type            |
|--------------|-----------------|
| id           | INT (PK)        |
| visitor_name | VARCHAR(100)    |
| email        | VARCHAR(150)    |
| category     | VARCHAR(100)    |
| comments     | TEXT            |
| created_at   | DATETIME        |

---

## 9. System Architecture

**Three-Tier Architecture:**

```
[Browser / Tailwind CSS / JS]
         ↓ HTTP
[Apache → PHP Application Layer]
         ↓ PDO
[MySQL Database Layer]
```

**Folder Structure:**
```
CMS/
├── admin/          Admin panel pages
├── api/            API handlers (course actions, enrollment, etc.)
├── assets/         CSS, JS, images (logo)
├── auth/           Login & Register
├── config/         db.php, session.php
├── includes/       Shared sidebar components per role
├── instructor/     Instructor panel pages
├── student/        Student panel pages
├── uploads/        Course image uploads
├── index.php       Public landing page
├── courses.php     Public course listing
├── about.php       About page
├── contact.php     Contact/feedback form
├── help_center.php Help docs
└── privacy.php     Privacy policy
```

---

## 10. User Flows

### Student Flow
1. Register on login/register page → redirected to student dashboard
2. Browse course catalog → filter by category/year/search
3. Click "Enroll" → confirmed enrollment
4. View enrolled courses on "My Courses" with progress indicators
5. Discover recommended and new courses on dashboard

### Instructor Flow
1. Login → redirected to instructor dashboard
2. View assigned courses with enrollment stats
3. Click into student list for a course
4. Navigate to "My Courses" for full course management

### Admin Flow
1. Login → redirected to admin dashboard (stats + chart + activity)
2. Manage Courses → add/edit/delete courses with image upload
3. Manage Users → add/edit/delete users and assign roles
4. Monitor Enrollments → view all enrollment records
5. System Logs → review platform activity
6. Feedback → review submitted user feedback

---

## 11. Pages Delivered

| Area       | Page                    | Description                            |
|------------|-------------------------|----------------------------------------|
| Public     | index.php               | Landing page with featured courses     |
| Public     | courses.php             | Public course catalog with search      |
| Public     | about.php               | About the platform                     |
| Public     | contact.php             | Feedback/contact form                  |
| Public     | help_center.php         | Help documentation                     |
| Public     | privacy.php             | Privacy policy                         |
| Auth       | auth/login.php          | Login + Register (tabs)                |
| Admin      | admin/dashboard.php     | Stats, enrollment chart, activity      |
| Admin      | admin/courses.php       | Full course CRUD                       |
| Admin      | admin/users.php         | User management                        |
| Admin      | admin/enrollments.php   | Enrollment records                     |
| Admin      | admin/feedback.php      | Student feedback viewer                |
| Instructor | instructor/dashboard.php| Stats, course grid, student table      |
| Instructor | instructor/courses.php  | Instructor's course list               |
| Instructor | instructor/students.php | Enrolled students per course           |
| Student    | student/dashboard.php   | Enrolled, recommended, new courses     |
| Student    | student/catalog.php     | Browse all courses                     |
| Student    | student/my_courses.php  | Enrolled courses with images           |

---

## 12. Security Measures

- Passwords hashed with `password_hash()` (bcrypt)
- All SQL uses PDO prepared statements
- All output escaped with `htmlspecialchars()`
- Sessions verified on every protected page via `requireRole()`
- File uploads: type-checked (whitelist) and size-limited (5MB max)
- Input sanitized via custom `sanitize()` helper

---

## 13. Future Enhancements

- Online video lectures per course
- Course completion certificates (PDF generation)
- Student progress tracking with percentage
- Payment gateway for paid courses
- Email notifications for enrollment confirmation
- AI-based course recommendations
- Mobile application (React Native / Flutter)
- Attendance tracking module

---

## 14. Success Metrics

| Metric                   | Target       |
|--------------------------|--------------|
| Registered Users         | 500+         |
| Course Enrollments       | 1,000+       |
| Avg. Page Load Time      | < 3 seconds  |
| System Uptime            | 99%+         |
| User Satisfaction Score  | 4.5 / 5      |
