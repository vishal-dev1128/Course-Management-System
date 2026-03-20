# Database Architecture

EduManage uses a relational MySQL database (`cms_db`) optimized for academic workflows and data integrity.

## 🗃️ Core Tables

The system revolves around four primary tables:

1. **`users`**: Central store for all authenticated users (Admin, Instructor, Student).
   - *Key Fields*: `id`, `name`, `email`, `password` (hashed), `role`, `status`.

2. **`courses`**: Stores the metadata for all academic offerings.
   - *Key Fields*: `id`, `title`, `description`, `category`, `academic_year`, `instructor_id` (FK), `status`, `image`.

3. **`enrollments`**: Manages the many-to-many relationship between Students and Courses.
   - *Key Fields*: `id`, `student_id` (FK), `course_id` (FK), `enrolled_at`.

4. **`customer_feedback`**: Stores inquiries and feedback from the public contact form.
   - *Key Fields*: `id`, `visitor_name`, `email`, `category`, `comments`.

## 🔄 Relationships

- **One-to-Many**: One Instructor can be assigned to multiple Courses (`users.id` → `courses.instructor_id`).
- **Many-to-Many**: Students and Courses are linked through the `enrollments` junction table.

## 🛡️ Implementation

Data integrity is maintained using:
- **Foreign Key Logic**: Relationships are strictly maintained through structured SQL queries.
- **PDO Prepared Statements**: Used for ALL database interactions to eliminate the risk of SQL injection.
- **Atomic Transactions**: Utilized for bulk operations (e.g., the Randomize feature) to ensure system stability during updates.
