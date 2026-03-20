# API Documentation

The EduManage backend uses a set of PHP-based API endpoints located in the `/api` directory to handle dynamic actions and data processing.

## 📡 Endpoints Overview

### 📋 1. Course Management (`/api/course_action.php`)
Handles all administrative and instructor actions related to courses.
- **POST `action=add`**: Creates a new course record with image upload support.
- **POST `action=edit`**: Updates an existing course record.
- **POST `action=delete`**: Permanently removes a course.

### 🎲 2. Instructor Randomization (`/api/randomize_instructors.php`)
An administrative tool to shuffle assignments for testing.
- **GET**: Safely reassigns all active courses to a random pool of active instructors.

### 📥 3. Data Import (`/api/import_courses.php`, `/api/import_users.php`)
Supports bulk data operations via CSV files.
- **POST**: Validates CSV structure, sanitizes input, and performs bulk inserts into the database.

### 🎓 4. Student Enrollment (`/api/enroll.php`)
Manages the relationship between students and academic content.
- **POST**: Validates student session, checks for duplicate enrollment, and records the new entry.

---

## 🛠️ Security for APIs

Every endpoint is protected by:
- **Role Verification**: Calls to `requireRole()` ensure only authorized users can trigger API actions.
- **Input Sanitization**: All incoming data is passed through the `sanitize()` helper to prevent XSS and tag injection.
- **Prepared Statements**: All data persistence is handled via PDO to prevent SQL injection.
