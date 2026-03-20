-- ============================================================
-- Full Lessons V2 - Rich Content with Video & Resource Links
-- Run ONCE in phpMyAdmin after existing lessons_upgrade.sql
-- Covers all courses not yet in lessons_upgrade.sql / more_lessons.sql
-- Also updates video_url for existing courses
-- ============================================================

USE cms_db;

-- ============================================================
-- UPDATE video_url for already-inserted courses (1-7, 8, 11-24)
-- ============================================================

-- Course 1: C Programming
UPDATE lessons SET video_url='https://www.youtube.com/embed/KJgsSFOSQv0' WHERE course_id=1 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/wXVFuECJX-k' WHERE course_id=1 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/Bz4MxDeEM6k' WHERE course_id=1 AND order_num=3;

-- Course 2: HTML & CSS Basics
UPDATE lessons SET video_url='https://www.youtube.com/embed/mU6anWqZJcc' WHERE course_id=2 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/yfoY53QXEnI' WHERE course_id=2 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/3elGSZSWTbM' WHERE course_id=2 AND order_num=3;

-- Course 3: C++ Mastery
UPDATE lessons SET video_url='https://www.youtube.com/embed/vLnPwxZdW4Y' WHERE course_id=3 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/ZzaPdXTrSb8' WHERE course_id=3 AND order_num=2;

-- Course 4: JavaScript Essentials
UPDATE lessons SET video_url='https://www.youtube.com/embed/W6NZfCO5SIk' WHERE course_id=4 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/5fb2aPlgoys' WHERE course_id=4 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/PoRJizFvM7s' WHERE course_id=4 AND order_num=3;

-- Course 5: PHP Web Development
UPDATE lessons SET video_url='https://www.youtube.com/embed/OK_JCtrrv-c' WHERE course_id=5 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/2eebptXfEvw' WHERE course_id=5 AND order_num=2;

-- Course 6: Advanced Web Frameworks
UPDATE lessons SET video_url='https://www.youtube.com/embed/mrHNSanmqQ4' WHERE course_id=6 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/pKd0Rpw7O48' WHERE course_id=6 AND order_num=2;

-- Course 7: Python
UPDATE lessons SET video_url='https://www.youtube.com/embed/rfscVS0vtbw' WHERE course_id=7 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/Rce_EiDFmmU' WHERE course_id=7 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/W8KRzm-HUcc' WHERE course_id=7 AND order_num=3;

-- Course 8: Data Structures
UPDATE lessons SET video_url='https://www.youtube.com/embed/RBSGKlAvoiM' WHERE course_id=8 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/wjI1WNcIntg' WHERE course_id=8 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/oSWTXtMglKE' WHERE course_id=8 AND order_num=3;

-- Course 11: DBMS
UPDATE lessons SET video_url='https://www.youtube.com/embed/HXV3zeQKqGY' WHERE course_id=11 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/HXV3zeQKqGY' WHERE course_id=11 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/4Z9KEBexzcM' WHERE course_id=11 AND order_num=3;

-- Course 12: React JS
UPDATE lessons SET video_url='https://www.youtube.com/embed/w7ejDZ8SWv8' WHERE course_id=12 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/35lXWvCuM8o' WHERE course_id=12 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/TNhaISOUy6Q' WHERE course_id=12 AND order_num=3;

-- Course 13: Node.js
UPDATE lessons SET video_url='https://www.youtube.com/embed/Oe421EPjeBE' WHERE course_id=13 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/L72fhGm1tfE' WHERE course_id=13 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/SccSCuHhOw0' WHERE course_id=13 AND order_num=3;

-- Course 14: Machine Learning
UPDATE lessons SET video_url='https://www.youtube.com/embed/7eh4d6sabA0' WHERE course_id=14 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/4b5d3muPQmA' WHERE course_id=14 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/aircAruvnKk' WHERE course_id=14 AND order_num=3;

-- Course 15: UI/UX Design
UPDATE lessons SET video_url='https://www.youtube.com/embed/c9Wg6Cb_YlU' WHERE course_id=15 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/II-6dDzc-80' WHERE course_id=15 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/B5F-mFEE7QQ' WHERE course_id=15 AND order_num=3;

-- Course 16: Cyber Security
UPDATE lessons SET video_url='https://www.youtube.com/embed/inWWhr5tnEA' WHERE course_id=16 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/E03gh1utvlk' WHERE course_id=16 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/3Kq1MIfTWCE' WHERE course_id=16 AND order_num=3;

-- Course 17: Android Development
UPDATE lessons SET video_url='https://www.youtube.com/embed/fis26HvvDII' WHERE course_id=17 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/TNh4WBBfzMw' WHERE course_id=17 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/bFug7PNNJ-Y' WHERE course_id=17 AND order_num=3;

-- Course 18: Software Engineering
UPDATE lessons SET video_url='https://www.youtube.com/embed/l1-gZoFar5I' WHERE course_id=18 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/pjnSEPqJqBg' WHERE course_id=18 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/USSkidmaS6w' WHERE course_id=18 AND order_num=3;

-- Course 19: Laravel
UPDATE lessons SET video_url='https://www.youtube.com/embed/MYyJ4PuL4pY' WHERE course_id=19 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/fYKv5OESZ8g' WHERE course_id=19 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/GijNNDdVUAE' WHERE course_id=19 AND order_num=3;

-- Course 20: Cloud Computing
UPDATE lessons SET video_url='https://www.youtube.com/embed/M988_fsOSWo' WHERE course_id=20 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/a9__D53WsMs' WHERE course_id=20 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/EOIja7yFScs' WHERE course_id=20 AND order_num=3;

-- Course 21: DevOps
UPDATE lessons SET video_url='https://www.youtube.com/embed/0yWAtQ6wYNM' WHERE course_id=21 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/1sVqfqRM-qA' WHERE course_id=21 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/3c-iBn73dDE' WHERE course_id=21 AND order_num=3;

-- Course 22: Artificial Intelligence
UPDATE lessons SET video_url='https://www.youtube.com/embed/ad79nYk2keg' WHERE course_id=22 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/aircAruvnKk' WHERE course_id=22 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/ysqpl6w6Wzg' WHERE course_id=22 AND order_num=3;

-- Course 23: HTML & CSS (Advanced)
UPDATE lessons SET video_url='https://www.youtube.com/embed/kMT54MPz9oE' WHERE course_id=23 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/OXGznpKZ_sA' WHERE course_id=23 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/srvUrASNj0s' WHERE course_id=23 AND order_num=3;

-- Course 24: Java
UPDATE lessons SET video_url='https://www.youtube.com/embed/eIrMbAQSU34' WHERE course_id=24 AND order_num=1;
UPDATE lessons SET video_url='https://www.youtube.com/embed/goTSPRvGr-g' WHERE course_id=24 AND order_num=2;
UPDATE lessons SET video_url='https://www.youtube.com/embed/17XbFSHNLpM' WHERE course_id=24 AND order_num=3;

-- ============================================================
-- UPDATE existing lesson content to add Resources section
-- ============================================================

-- Course 1 Lesson 1: C Programming Intro
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.learn-c.org/" target="_blank" rel="noopener">Learn-C.org — Interactive C Tutorial</a></li><li><a href="https://www.geeksforgeeks.org/c-programming-language/" target="_blank" rel="noopener">GeeksForGeeks — C Programming</a></li><li><a href="https://www.tutorialspoint.com/cprogramming/index.htm" target="_blank" rel="noopener">TutorialsPoint — C Programming</a></li><li><a href="https://cs50.harvard.edu/x/" target="_blank" rel="noopener">Harvard CS50 — Free Online Course</a></li></ul></div>') WHERE course_id=1 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/data-types-in-c/" target="_blank" rel="noopener">GeeksForGeeks — C Data Types</a></li><li><a href="https://en.cppreference.com/w/c/language/type" target="_blank" rel="noopener">cppreference.com — C Types Reference</a></li></ul></div>') WHERE course_id=1 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/decision-making-c-cpp/" target="_blank" rel="noopener">GeeksForGeeks — Control Structures</a></li><li><a href="https://www.w3schools.com/c/c_conditions.php" target="_blank" rel="noopener">W3Schools — C Conditions</a></li></ul></div>') WHERE course_id=1 AND order_num=3 AND content NOT LIKE '%lesson-resources%';

-- Course 2: HTML & CSS
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://developer.mozilla.org/en-US/docs/Learn/HTML/Introduction_to_HTML" target="_blank" rel="noopener">MDN Web Docs — HTML Introduction</a></li><li><a href="https://www.w3schools.com/html/" target="_blank" rel="noopener">W3Schools — HTML Tutorial</a></li><li><a href="https://www.freecodecamp.org/learn/2022/responsive-web-design/" target="_blank" rel="noopener">freeCodeCamp — Responsive Web Design</a></li></ul></div>') WHERE course_id=2 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://developer.mozilla.org/en-US/docs/Learn/CSS/First_steps" target="_blank" rel="noopener">MDN Web Docs — CSS First Steps</a></li><li><a href="https://www.w3schools.com/css/" target="_blank" rel="noopener">W3Schools — CSS Tutorial</a></li><li><a href="https://css-tricks.com/guides/" target="_blank" rel="noopener">CSS Tricks — Guides</a></li></ul></div>') WHERE course_id=2 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://css-tricks.com/snippets/css/a-guide-to-flexbox/" target="_blank" rel="noopener">CSS Tricks — Complete Guide to Flexbox</a></li><li><a href="https://css-tricks.com/snippets/css/complete-guide-grid/" target="_blank" rel="noopener">CSS Tricks — Complete Guide to Grid</a></li><li><a href="https://flexboxfroggy.com/" target="_blank" rel="noopener">Flexbox Froggy — Interactive Game</a></li></ul></div>') WHERE course_id=2 AND order_num=3 AND content NOT LIKE '%lesson-resources%';

-- Course 3: C++
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.learncpp.com/" target="_blank" rel="noopener">LearnCpp.com — Free C++ Tutorial</a></li><li><a href="https://www.geeksforgeeks.org/c-plus-plus/" target="_blank" rel="noopener">GeeksForGeeks — C++ OOP</a></li><li><a href="https://en.cppreference.com/w/" target="_blank" rel="noopener">cppreference.com — C++ Reference</a></li></ul></div>') WHERE course_id=3 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.learncpp.com/cpp-tutorial/function-templates/" target="_blank" rel="noopener">LearnCpp — Function Templates</a></li><li><a href="https://www.geeksforgeeks.org/the-c-standard-template-library-stl/" target="_blank" rel="noopener">GeeksForGeeks — STL Guide</a></li></ul></div>') WHERE course_id=3 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

-- Course 4: JavaScript
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://developer.mozilla.org/en-US/docs/Learn/JavaScript/First_steps" target="_blank" rel="noopener">MDN Web Docs — JS First Steps</a></li><li><a href="https://javascript.info/" target="_blank" rel="noopener">javascript.info — Modern JS Tutorial</a></li><li><a href="https://www.freecodecamp.org/learn/javascript-algorithms-and-data-structures/" target="_blank" rel="noopener">freeCodeCamp — JS Algorithms</a></li></ul></div>') WHERE course_id=4 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://developer.mozilla.org/en-US/docs/Web/API/Document_Object_Model/Introduction" target="_blank" rel="noopener">MDN — DOM Introduction</a></li><li><a href="https://javascript.info/document" target="_blank" rel="noopener">javascript.info — DOM</a></li></ul></div>') WHERE course_id=4 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://javascript.info/promise-basics" target="_blank" rel="noopener">javascript.info — Promises</a></li><li><a href="https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous" target="_blank" rel="noopener">MDN — Async JavaScript</a></li></ul></div>') WHERE course_id=4 AND order_num=3 AND content NOT LIKE '%lesson-resources%';

-- Course 5: PHP
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.php.net/manual/en/" target="_blank" rel="noopener">PHP.net — Official Manual</a></li><li><a href="https://www.w3schools.com/php/" target="_blank" rel="noopener">W3Schools — PHP Tutorial</a></li><li><a href="https://phptherightway.com/" target="_blank" rel="noopener">PHP The Right Way</a></li></ul></div>') WHERE course_id=5 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.php.net/manual/en/book.pdo.php" target="_blank" rel="noopener">PHP.net — PDO Documentation</a></li><li><a href="https://www.geeksforgeeks.org/php-mysql-database/" target="_blank" rel="noopener">GeeksForGeeks — PHP MySQL</a></li></ul></div>') WHERE course_id=5 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

-- Course 6: Web Frameworks
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.theodinproject.com/" target="_blank" rel="noopener">The Odin Project — Full-Stack Path</a></li><li><a href="https://roadmap.sh/frontend" target="_blank" rel="noopener">roadmap.sh — Frontend Roadmap</a></li><li><a href="https://roadmap.sh/backend" target="_blank" rel="noopener">roadmap.sh — Backend Roadmap</a></li></ul></div>') WHERE course_id=6 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://restfulapi.net/" target="_blank" rel="noopener">RESTful API — Tutorial</a></li><li><a href="https://www.youtube.com/playlist?list=PL4cUxeGkcC9iI_-SoOtgYGLVTrGxEhI13" target="_blank" rel="noopener">Net Ninja — REST API Tutorial (YouTube)</a></li></ul></div>') WHERE course_id=6 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

-- Course 7: Python
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://docs.python.org/3/tutorial/" target="_blank" rel="noopener">Python.org — Official Tutorial</a></li><li><a href="https://www.w3schools.com/python/" target="_blank" rel="noopener">W3Schools — Python Tutorial</a></li><li><a href="https://www.freecodecamp.org/learn/scientific-computing-with-python/" target="_blank" rel="noopener">freeCodeCamp — Python</a></li></ul></div>') WHERE course_id=7 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://docs.python.org/3/tutorial/controlflow.html" target="_blank" rel="noopener">Python Docs — Control Flow</a></li><li><a href="https://www.geeksforgeeks.org/functions-in-python/" target="_blank" rel="noopener">GeeksForGeeks — Python Functions</a></li></ul></div>') WHERE course_id=7 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://docs.python.org/3/tutorial/datastructures.html" target="_blank" rel="noopener">Python Docs — Data Structures</a></li><li><a href="https://realpython.com/python-data-structures/" target="_blank" rel="noopener">Real Python — Data Structures</a></li></ul></div>') WHERE course_id=7 AND order_num=3 AND content NOT LIKE '%lesson-resources%';

-- Course 8: Data Structures
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/data-structures/" target="_blank" rel="noopener">GeeksForGeeks — Data Structures</a></li><li><a href="https://visualgo.net/en" target="_blank" rel="noopener">VisuAlgo — Algorithm Visualizations</a></li><li><a href="https://leetcode.com/" target="_blank" rel="noopener">LeetCode — Practice Problems</a></li></ul></div>') WHERE course_id=8 AND order_num=1 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/stack-data-structure/" target="_blank" rel="noopener">GeeksForGeeks — Stacks</a></li><li><a href="https://www.cs.usfca.edu/~galles/visualization/Algorithms.html" target="_blank" rel="noopener">Algorithm Visualizations — USFCA</a></li></ul></div>') WHERE course_id=8 AND order_num=2 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/binary-tree-data-structure/" target="_blank" rel="noopener">GeeksForGeeks — Trees</a></li><li><a href="https://www.geeksforgeeks.org/graph-data-structure-and-algorithms/" target="_blank" rel="noopener">GeeksForGeeks — Graphs</a></li></ul></div>') WHERE course_id=8 AND order_num=3 AND content NOT LIKE '%lesson-resources%';

-- Courses 11-24 resource links (batch update)
UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.w3schools.com/sql/" target="_blank" rel="noopener">W3Schools — SQL Tutorial</a></li><li><a href="https://sqlzoo.net/" target="_blank" rel="noopener">SQLZoo — Interactive SQL</a></li><li><a href="https://www.geeksforgeeks.org/dbms/" target="_blank" rel="noopener">GeeksForGeeks — DBMS</a></li></ul></div>') WHERE course_id=11 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://react.dev/learn" target="_blank" rel="noopener">React.dev — Official Docs</a></li><li><a href="https://www.freecodecamp.org/learn/front-end-development-libraries/#react" target="_blank" rel="noopener">freeCodeCamp — React</a></li><li><a href="https://www.youtube.com/playlist?list=PLC3y8-rFHvwgg3vaYJgHGnModB54rxOk3" target="_blank" rel="noopener">Codevolution — React YouTube Playlist</a></li></ul></div>') WHERE course_id=12 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://nodejs.org/en/docs" target="_blank" rel="noopener">Node.js Official Docs</a></li><li><a href="https://expressjs.com/" target="_blank" rel="noopener">Express.js — Official Docs</a></li><li><a href="https://www.theodinproject.com/paths/full-stack-javascript" target="_blank" rel="noopener">The Odin Project — Node.js Path</a></li></ul></div>') WHERE course_id=13 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.kaggle.com/learn" target="_blank" rel="noopener">Kaggle Learn — Free ML Courses</a></li><li><a href="https://scikit-learn.org/stable/getting_started.html" target="_blank" rel="noopener">Scikit-Learn — Getting Started</a></li><li><a href="https://www.coursera.org/learn/machine-learning" target="_blank" rel="noopener">Coursera — ML by Andrew Ng (Audit Free)</a></li></ul></div>') WHERE course_id=14 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.figma.com/community/file/821003279990139365" target="_blank" rel="noopener">Figma Community — Free UI Kits</a></li><li><a href="https://www.nngroup.com/articles/" target="_blank" rel="noopener">Nielsen Norman Group — UX Articles</a></li><li><a href="https://uxtools.co/" target="_blank" rel="noopener">UXTools.co — Designer Survey & Resources</a></li></ul></div>') WHERE course_id=15 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.cybrary.it/" target="_blank" rel="noopener">Cybrary — Free Security Training</a></li><li><a href="https://tryhackme.com/" target="_blank" rel="noopener">TryHackMe — Learn Cybersecurity</a></li><li><a href="https://www.owasp.org/index.php/Main_Page" target="_blank" rel="noopener">OWASP — Security Best Practices</a></li></ul></div>') WHERE course_id=16 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://developer.android.com/get-started/codelabs" target="_blank" rel="noopener">Android Developer — Codelabs</a></li><li><a href="https://developer.android.com/courses" target="_blank" rel="noopener">Google — Android Courses</a></li><li><a href="https://www.udacity.com/course/android-basics-kotlin--ud9012" target="_blank" rel="noopener">Udacity — Android Basics (Free)</a></li></ul></div>') WHERE course_id=17 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/software-engineering/" target="_blank" rel="noopener">GeeksForGeeks — Software Engineering</a></li><li><a href="https://agilemanifesto.org/" target="_blank" rel="noopener">Agile Manifesto — Official Site</a></li><li><a href="https://git-scm.com/book/en/v2" target="_blank" rel="noopener">Pro Git — Free Book</a></li></ul></div>') WHERE course_id=18 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://laravel.com/docs/" target="_blank" rel="noopener">Laravel — Official Documentation</a></li><li><a href="https://laracasts.com/" target="_blank" rel="noopener">Laracasts — Laravel Video Tutorials</a></li><li><a href="https://www.youtube.com/playlist?list=PLpzy7FIRqpGBQ_aqz_hXDBch1aAA-lmgu" target="_blank" rel="noopener">Traversy Media — Laravel Crash Course</a></li></ul></div>') WHERE course_id=19 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://aws.amazon.com/training/digital/" target="_blank" rel="noopener">AWS Digital Training (Free)</a></li><li><a href="https://cloud.google.com/training" target="_blank" rel="noopener">Google Cloud Training</a></li><li><a href="https://learn.microsoft.com/en-us/training/azure/" target="_blank" rel="noopener">Microsoft Learn — Azure</a></li></ul></div>') WHERE course_id=20 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://roadmap.sh/devops" target="_blank" rel="noopener">roadmap.sh — DevOps Roadmap</a></li><li><a href="https://docs.docker.com/get-started/" target="_blank" rel="noopener">Docker — Official Getting Started</a></li><li><a href="https://www.katacoda.com/" target="_blank" rel="noopener">Katacoda — Free DevOps Labs</a></li></ul></div>') WHERE course_id=21 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.deeplearning.ai/" target="_blank" rel="noopener">DeepLearning.AI — Free Courses</a></li><li><a href="https://playground.tensorflow.org/" target="_blank" rel="noopener">TensorFlow Playground — Visual Neural Net</a></li><li><a href="https://huggingface.co/learn" target="_blank" rel="noopener">Hugging Face — NLP & AI Learning</a></li></ul></div>') WHERE course_id=22 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://developer.mozilla.org/en-US/docs/Web" target="_blank" rel="noopener">MDN Web Docs — HTML/CSS Reference</a></li><li><a href="https://web.dev/learn/css/" target="_blank" rel="noopener">web.dev — Learn CSS</a></li><li><a href="https://cssbattle.dev/" target="_blank" rel="noopener">CSS Battle — Practice by Challenges</a></li></ul></div>') WHERE course_id=23 AND content NOT LIKE '%lesson-resources%';

UPDATE lessons SET content=CONCAT(content, '<div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://docs.oracle.com/en/java/javase/17/docs/api/" target="_blank" rel="noopener">Oracle — Java SE 17 API Docs</a></li><li><a href="https://www.baeldung.com/" target="_blank" rel="noopener">Baeldung — Java Tutorials</a></li><li><a href="https://www.javatpoint.com/java-tutorial" target="_blank" rel="noopener">JavaTpoint — Java Tutorial</a></li></ul></div>') WHERE course_id=24 AND content NOT LIKE '%lesson-resources%';

-- ============================================================
-- Insert lessons for courses 9 and 10 if they exist in DB
-- (Network Basics & Algorithms — common in CS curriculum)
-- ============================================================

INSERT IGNORE INTO lessons (course_id, title, description, content_type, content, video_url, duration_minutes, order_num, status) VALUES
(9, 'Introduction to Computer Networks', 'Fundamentals of networking', 'article',
'<h2>What is a Computer Network?</h2><p>A computer network is a set of computers sharing resources located on or provided by network nodes.</p><h3>Types of Networks</h3><ul><li><strong>LAN</strong> - Local Area Network (home, office)</li><li><strong>WAN</strong> - Wide Area Network (internet)</li><li><strong>MAN</strong> - Metropolitan Area Network</li><li><strong>PAN</strong> - Personal Area Network (Bluetooth)</li></ul><h3>Network Topologies</h3><ul><li><strong>Bus</strong> - All nodes connected to a single cable</li><li><strong>Star</strong> - All nodes connected to a central hub</li><li><strong>Ring</strong> - Nodes connected in a circular chain</li><li><strong>Mesh</strong> - Every node connected to every other</li></ul><div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/computer-network-tutorials/" target="_blank" rel="noopener">GeeksForGeeks — Computer Networks</a></li><li><a href="https://www.cisco.com/c/en/us/products/switches/what-is-a-network.html" target="_blank" rel="noopener">Cisco — What is a Network?</a></li></ul></div>',
'https://www.youtube.com/embed/3QhU9jd03a0', 40, 1, 'active'),

(9, 'OSI Model and TCP/IP', '7-layer networking model explained', 'article',
'<h2>The OSI Model</h2><p>A conceptual framework that divides network communication into 7 distinct layers.</p><h3>The 7 Layers</h3><ol><li><strong>Physical</strong> - Cables, bits, hardware</li><li><strong>Data Link</strong> - Frames, MAC addresses</li><li><strong>Network</strong> - IP addressing, routing</li><li><strong>Transport</strong> - TCP/UDP, port numbers</li><li><strong>Session</strong> - Session management</li><li><strong>Presentation</strong> - Encoding, encryption</li><li><strong>Application</strong> - HTTP, FTP, DNS</li></ol><h3>TCP/IP Model (Practical)</h3><ul><li>Application Layer (HTTP, FTP)</li><li>Transport Layer (TCP, UDP)</li><li>Internet Layer (IP)</li><li>Network Access Layer</li></ul><div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/layers-of-osi-model/" target="_blank" rel="noopener">GeeksForGeeks — OSI Model</a></li><li><a href="https://www.computernetworkingnotes.com/networking-tutorials/osi-model/" target="_blank" rel="noopener">Computer Networking Notes — OSI</a></li></ul></div>',
'https://www.youtube.com/embed/LANW3m7UgAA', 50, 2, 'active'),

(9, 'IP Addressing and Subnetting', 'IPv4, IPv6, and subnet masks', 'article',
'<h2>IP Addressing</h2><p>Every device on a network needs a unique IP address for identification.</p><h3>IPv4</h3><pre>192.168.1.100
Format: 4 octets (8 bits each)
Range: 0.0.0.0 to 255.255.255.255</pre><h3>IPv6</h3><pre>2001:0db8:85a3:0000:0000:8a2e:0370:7334
128-bit address = ~340 undecillion addresses</pre><h3>Subnetting</h3><pre>IP Address: 192.168.1.0
Subnet Mask: 255.255.255.0 (/24)
Network: 192.168.1.0
Hosts: 192.168.1.1 - 192.168.1.254
Broadcast: 192.168.1.255</pre><div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.subnet-calculator.com/" target="_blank" rel="noopener">Subnet Calculator Tool</a></li><li><a href="https://www.geeksforgeeks.org/introduction-of-subnetting/" target="_blank" rel="noopener">GeeksForGeeks — Subnetting</a></li></ul></div>',
'https://www.youtube.com/embed/ecCuyq-Wprc', 55, 3, 'active'),

(10, 'Algorithm Analysis and Big-O', 'Measuring algorithm efficiency', 'article',
'<h2>What is an Algorithm?</h2><p>A step-by-step procedure to solve a problem. Efficiency matters as data scales.</p><h3>Big-O Notation</h3><ul><li><strong>O(1)</strong> - Constant time (array access)</li><li><strong>O(log n)</strong> - Logarithmic (binary search)</li><li><strong>O(n)</strong> - Linear (linear search)</li><li><strong>O(n log n)</strong> - Linearithmic (merge sort)</li><li><strong>O(n²)</strong> - Quadratic (bubble sort)</li><li><strong>O(2ⁿ)</strong> - Exponential (recursive Fibonacci)</li></ul><h3>Example Analysis</h3><pre>// O(n) - loops once through n items
for (int i = 0; i < n; i++) {
    process(arr[i]);
}

// O(n²) - nested loops
for (int i = 0; i < n; i++) {
    for (int j = 0; j < n; j++) {
        compare(arr[i], arr[j]);
    }
}</pre><div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.bigocheatsheet.com/" target="_blank" rel="noopener">Big-O Cheat Sheet</a></li><li><a href="https://visualgo.net/en/sorting" target="_blank" rel="noopener">VisuAlgo — Sort Visualization</a></li><li><a href="https://leetcode.com/explore/" target="_blank" rel="noopener">LeetCode — Algorithm Practice</a></li></ul></div>',
'https://www.youtube.com/embed/Mo4vesaut8g', 45, 1, 'active'),

(10, 'Sorting Algorithms', 'Bubble, Merge, Quick Sort explained', 'article',
'<h2>Sorting Algorithms</h2><p>Algorithms that arrange elements in a specific order. Critical for efficient data retrieval.</p><h3>Bubble Sort — O(n²)</h3><pre>def bubble_sort(arr):
    n = len(arr)
    for i in range(n):
        for j in range(0, n-i-1):
            if arr[j] > arr[j+1]:
                arr[j], arr[j+1] = arr[j+1], arr[j]</pre><h3>Merge Sort — O(n log n)</h3><pre>def merge_sort(arr):
    if len(arr) <= 1:
        return arr
    mid = len(arr) // 2
    left = merge_sort(arr[:mid])
    right = merge_sort(arr[mid:])
    return merge(left, right)</pre><h3>Quick Sort — O(n log n) avg</h3><pre>def quick_sort(arr):
    if len(arr) <= 1:
        return arr
    pivot = arr[len(arr) // 2]
    left = [x for x in arr if x < pivot]
    middle = [x for x in arr if x == pivot]
    right = [x for x in arr if x > pivot]
    return quick_sort(left) + middle + quick_sort(right)</pre><div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/sorting-algorithms/" target="_blank" rel="noopener">GeeksForGeeks — Sorting Algorithms</a></li><li><a href="https://visualgo.net/en/sorting" target="_blank" rel="noopener">VisuAlgo — Interactive Sorting</a></li></ul></div>',
'https://www.youtube.com/embed/kgBjXUE_Nwc', 60, 2, 'active'),

(10, 'Searching Algorithms and Recursion', 'Linear search, binary search, and recursion', 'article',
'<h2>Searching Algorithms</h2><h3>Linear Search — O(n)</h3><pre>def linear_search(arr, target):
    for i, val in enumerate(arr):
        if val == target:
            return i
    return -1</pre><h3>Binary Search — O(log n)</h3><pre>def binary_search(arr, target):
    left, right = 0, len(arr) - 1
    while left <= right:
        mid = (left + right) // 2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    return -1</pre><h2>Recursion</h2><pre>def factorial(n):
    if n == 0:
        return 1
    return n * factorial(n - 1)

def fibonacci(n):
    if n <= 1:
        return n
    return fibonacci(n-1) + fibonacci(n-2)</pre><div class="lesson-resources"><h3>📚 Learning Resources</h3><ul><li><a href="https://www.geeksforgeeks.org/binary-search/" target="_blank" rel="noopener">GeeksForGeeks — Binary Search</a></li><li><a href="https://www.geeksforgeeks.org/recursion/" target="_blank" rel="noopener">GeeksForGeeks — Recursion</a></li></ul></div>',
'https://www.youtube.com/embed/P3YID7liBug', 50, 3, 'active');
