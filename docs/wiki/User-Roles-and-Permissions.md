# User Roles & Permissions

EduManage CMS follows a strict Role-Based Access Control (RBAC) model to ensure data security and operational efficiency. There are three primary user roles: **Administrator**, **Instructor**, and **Student**.

## 🛠️ Role Breakdown

### 👑 1. Administrator
The Administrator has full control over the platform's configuration and management.

- **User Management**: Add, edit, remove users; assign roles (Instructor, Student).
- **Course Administration**: Create, modify, or delete any course.
- **System Monitoring**: View live enrollment statistics, system activity feeds, and feedback reports.
- **Data Tools**: Access to the "Randomize All" instructor assignment tool for testing and data distribution.

### 👨‍🏫 2. Instructor
Instructors manage their assigned courses and monitor student performance.

- **Course Dashboard**: View a specialized grid of all assigned courses with real-time student counts.
- **Student Playlists**: Access a full list of enrolled students for each of their courses.
- **Resource Management**: Update descriptions and content for their specific assigned courses.

### 🎓 3. Student
Students are the primary users of the platform's learning features.

- **Course Catalog**: Browse the entire library with advanced filtering by category and year.
- **One-Click Enrollment**: Seamlessly join any active course.
- **My Courses**: A personalized dashboard showing progress, registered courses, and recommendations.

---

## 🔒 Security Enforcement

Permissions are enforced at the server level via the `requireRole()` function in `config/session.php`. Any attempt to access a restricted page without the required role results in an automatic redirection to the login portal.
