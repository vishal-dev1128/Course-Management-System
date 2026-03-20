-- Lessons table for course content
-- Run this file in phpMyAdmin or MySQL CLI to add lessons functionality

USE cms_db;

-- Lessons table
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content_type ENUM('video', 'article', 'quiz') DEFAULT 'article',
    content TEXT,
    video_url VARCHAR(500) DEFAULT NULL,
    duration_minutes INT DEFAULT NULL,
    order_num INT DEFAULT 0,
    status ENUM('active', 'draft') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quiz questions table
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT NOT NULL,
    question TEXT NOT NULL,
    options JSON NOT NULL,
    correct_answer INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Course progress table
CREATE TABLE IF NOT EXISTS lesson_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed TINYINT(1) DEFAULT 0,
    completed_at DATETIME DEFAULT NULL,
    UNIQUE KEY unique_progress (student_id, lesson_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert sample lessons for existing courses
INSERT INTO lessons (course_id, title, description, content_type, content, duration_minutes, order_num) VALUES
-- C Programming (course_id: 1)
(1, 'Introduction to C Programming', 'Learn the basics of C programming language', 'article', '<h2>Welcome to C Programming!</h2><p>C is a powerful general-purpose programming language. In this lesson, we will cover the basics of C programming including variables, data types, and your first program.</p><h3>Your First C Program</h3><pre>#include <stdio.h>\n\nint main() {\n    printf("Hello, World!\\n");\n    return 0;\n}</pre><p>Key points to remember:</p><ul><li>Every C program starts with the main() function</li><li>printf() is used to display output</li><li>Statements end with a semicolon</li></ul>', 30, 1),
(1, 'Variables and Data Types', 'Understanding variables and data types in C', 'article', '<h2>Variables and Data Types</h2><p>Variables are containers for storing data values. In C, you must declare the type of variable before using it.</p><h3>Common Data Types</h3><ul><li><strong>int</strong> - Integer values (1, 42, -10)</li><li><strong>float</strong> - Floating-point numbers (3.14, -0.5)</li><li><strong>char</strong> - Single characters (''A'', ''z'')</li><li><strong>double</strong> - Double precision floating-point</li></ul><h3>Example</h3><pre>int age = 25;\nfloat price = 19.99;\nchar grade = ''A'';</pre>', 45, 2),
(1, 'Control Structures', 'If-else, loops, and conditional statements', 'article', '<h2>Control Structures in C</h2><p>Control structures allow you to control the flow of program execution.</p><h3>If-Else Statement</h3><pre>if (condition) {\n    // code to execute if true\n} else {\n    // code to execute if false\n}</pre><h3>Loops</h3><p>C supports several types of loops: for, while, and do-while.</p><pre>for (int i = 0; i < 10; i++) {\n    printf("%d\\n", i);\n}</pre>', 60, 3),

-- HTML & CSS Basics (course_id: 2)
(2, 'Introduction to HTML', 'Learn the structure of web pages with HTML', 'article', '<h2>What is HTML?</h2><p>HTML stands for HyperText Markup Language. It is the standard markup language for creating web pages.</p><h3>Basic HTML Structure</h3><pre><!DOCTYPE html>\n<html>\n<head>\n    <title>My Page</title>\n</head>\n<body>\n    <h1>Hello World</h1>\n    <p>This is my first webpage.</p>\n</body>\n</html></pre><h3>Key HTML Elements</h3><ul><li><strong><html></strong> - Root element</li><li><strong><head></strong> - Contains meta information</li><li><strong><body></strong> - Contains visible content</li><li><strong><h1> to <h6></strong> - Headings</li><li><strong><p></strong> - Paragraphs</li></ul>', 25, 1),
(2, 'CSS Fundamentals', 'Styling your web pages with CSS', 'article', '<h2>Introduction to CSS</h2><p>CSS (Cascading Style Sheets) is used to style and layout web pages.</p><h3>CSS Syntax</h3><pre>selector {\n    property: value;\n}</pre><h3>Example</h3><pre>h1 {\n    color: blue;\n    font-size: 24px;\n}\n\np {\n    color: gray;\n    font-family: Arial, sans-serif;\n}</pre><h3>Ways to Add CSS</h3><ol><li>Inline CSS - Using style attribute</li><li>Internal CSS - Using <style> tag</li><li>External CSS - Using separate .css file</li></ol>', 40, 2),
(2, 'Flexbox and Grid', 'Modern CSS layout techniques', 'article', '<h2>Modern CSS Layout</h2><p>Flexbox and Grid are powerful CSS layout systems.</p><h3>Flexbox</h3><pre>.container {\n    display: flex;\n    justify-content: space-between;\n    align-items: center;\n}</pre><h3>CSS Grid</h3><pre>.grid {\n    display: grid;\n    grid-template-columns: repeat(3, 1fr);\n    gap: 20px;\n}</pre><p>Both Flexbox and Grid make it easy to create responsive layouts without using floats or positioning.</p>', 55, 3),

-- C++ Mastery (course_id: 3)
(3, 'Object-Oriented Programming in C++', 'Introduction to OOP concepts', 'article', '<h2>Object-Oriented Programming</h2><p>C++ supports OOP concepts including classes, objects, inheritance, and polymorphism.</p><h3>Classes and Objects</h3><pre>class Car {\npublic:\n    string brand;\n    int year;\n    \n    void drive() {\n        cout << "Driving " << brand << endl;\n    }\n};</pre><h3>Key OOP Concepts</h3><ul><li><strong>Encapsulation</strong> - Bundling data and methods</li><li><strong>Inheritance</strong> - Creating new classes from existing</li><li><strong>Polymorphism</strong> - Same interface, different behavior</li><li><strong>Abstraction</strong> - Hiding complexity</li></ul>', 60, 1),
(3, 'Templates and STL', 'Generic programming with templates', 'article', '<h2>C++ Templates and STL</h2><p>Templates allow you to write generic, reusable code.</p><h3>Function Template</h3><pre>template <typename T>\nT max(T a, T b) {\n    return (a > b) ? a : b;\n}</pre><h3>STL Containers</h3><ul><li><strong>vector</strong> - Dynamic array</li><li><strong>map</strong> - Key-value pairs</li><li><strong>set</strong> - Unique elements</li><li><strong>queue</strong> - FIFO data structure</li></ul>', 75, 2),

-- JavaScript Essentials (course_id: 4)
(4, 'JavaScript Basics', 'Getting started with JavaScript', 'article', '<h2>Introduction to JavaScript</h2><p>JavaScript is the programming language of the web. It can make your web pages interactive and dynamic.</p><h3>Variables</h3><pre>let name = "John";\nconst age = 25;\nvar isStudent = true;</pre><h3>Data Types</h3><ul><li>String - "Hello"</li><li>Number - 42, 3.14</li><li>Boolean - true, false</li><li>Array - [1, 2, 3]</li><li>Object - {name: "John"}</li></ul><h3>Your First Script</h3><pre>console.log("Hello, World!");\nalert("Welcome to my page!");</pre>', 30, 1),
(4, 'DOM Manipulation', 'Interacting with HTML elements', 'article', '<h2>Document Object Model (DOM)</h2><p>The DOM represents your HTML document as a tree of objects.</p><h3>Selecting Elements</h3><pre>// By ID\nlet element = document.getElementById("myId");\n\n// By class\nlet elements = document.getElementsByClassName("myClass");\n\n// Using querySelector\nlet first = document.querySelector(".myClass");</pre><h3>Modifying Elements</h3><pre>element.textContent = "New text";\nelement.innerHTML = "<strong>Bold text</strong>";\nelement.style.color = "blue";</pre>', 45, 2),
(4, 'Async JavaScript', 'Promises and async/await', 'article', '<h2>Asynchronous JavaScript</h2><p>JavaScript is single-threaded but uses callbacks, promises, and async/await for asynchronous operations.</p><h3>Promises</h3><pre>fetch(url)\n    .then(response => response.json())\n    .then(data => console.log(data))\n    .catch(error => console.error(error));</pre><h3>Async/Await</h3><pre>async function getData() {\n    try {\n        const response = await fetch(url);\n        const data = await response.json();\n        return data;\n    } catch (error) {\n        console.error(error);\n    }\n}</pre>', 60, 3),

-- PHP Web Development (course_id: 5)
(5, 'PHP Basics', 'Introduction to PHP programming', 'article', '<h2>What is PHP?</h2><p>PHP (Hypertext Preprocessor) is a server-side scripting language designed for web development.</p><h3>PHP Syntax</h3><pre><?php\n// PHP code goes here\necho "Hello, World!";\n$name = "Student";\necho "Welcome, $name!";\n?></pre><h3>Variables and Data Types</h3><pre>$integer = 42;\n$float = 3.14;\n$string = "Hello";\n$boolean = true;\n$array = ["apple", "banana", "cherry"];</pre><p>PHP variables start with the $ symbol.</p>', 35, 1),
(5, 'PHP and MySQL', 'Connecting PHP to databases', 'article', '<h2>PHP MySQL Connection</h2><p>Learn how to connect PHP to a MySQL database.</p><h3>PDO Connection</h3><pre>$host = "localhost";\n$dbname = "cms_db";\n$username = "root";\n$password = "";\n\ntry {\n    $pdo = new PDO("mysql:host=$host;dbname=$dbname", \n                   $username, $password);\n    $pdo->setAttribute(PDO::ATTR_ERRMODE, \n                       PDO::ERRMODE_EXCEPTION);\n    echo "Connected successfully!";\n} catch (PDOException $e) {\n    echo "Connection failed: " . $e->getMessage();\n}</pre>', 50, 2),

-- Advanced Web Frameworks (course_id: 6)
(6, 'Introduction to Web Frameworks', 'Understanding modern web frameworks', 'article', '<h2>What is a Web Framework?</h2><p>A web framework is a software framework designed to simplify web development.</p><h3>Popular Frameworks</h3><ul><li><strong>Frontend:</strong> React, Vue.js, Angular</li><li><strong>Backend:</strong> Node.js, Django, Laravel, Spring Boot</li></ul><h3>Why Use Frameworks?</h3><ol><li> Faster development</li><li> Built-in best practices</li><li> Security features</li><li> Community support</li><li> Scalability</li></ol><h3>MVC Architecture</h3><p>Most modern frameworks follow the Model-View-Controller pattern:</p><ul><li><strong>Model</strong> - Data and business logic</li><li><strong>View</strong> - User interface</li><li><strong>Controller</strong> - Request handling</li></ul>', 40, 1),
(6, 'RESTful API Design', 'Building APIs for web services', 'article', '<h2>RESTful API Concepts</h2><p>REST (Representational State Transfer) is an architectural style for designing networked applications.</p><h3>HTTP Methods</h3><ul><li><strong>GET</strong> - Retrieve data</li><li><strong>POST</strong> - Create new resource</li><li><strong>PUT</strong> - Update resource</li><li><strong>DELETE</strong> - Remove resource</li></ul><h3>REST Principles</h3><ol><li> Client-Server architecture</li><li> Statelessness</li><li> Cacheability</li><li> Uniform Interface</li></ol><h3>Example API Endpoint</h3><pre>GET /api/courses - Get all courses\nGET /api/courses/1 - Get course with ID 1\nPOST /api/courses - Create new course\nPUT /api/courses/1 - Update course 1\nDELETE /api/courses/1 - Delete course 1</pre>', 55, 2);