-- Insert demo categories
INSERT INTO course_category (categoryname) VALUES ('Cloud'), ('Programming'), ('DevOps');

-- Insert demo courses (if not already present)
INSERT INTO course (coursename, image, page_slug, description) VALUES
('AWS Solution Architect', 'AWS.jpeg', '../courses/Aws/SAA.php', 'Cloud Mastery for Modern IT'),
('Azure Fundamentals', 'Azure.jpeg', '../courses/Azure/AzureFundamentals.php', 'Empower Your Cloud Career'),
('DevOps', 'DevOps.png', '../courses/DevOps/devops.php', 'Automate. Integrate. Accelerate.'),
('Python Basics', 'placeholder.jpeg', '#', 'Learn Python from scratch.'),
('Linux Essentials', 'placeholder.jpeg', '#', 'Linux for beginners.');

-- Link courses to categories (categoryid and courseid may need to be adjusted based on your DB)
-- For demo, assuming auto-increment IDs start at 1 and are in order
INSERT INTO course_category_link (courseid, categoryid) VALUES
(1, 1), -- AWS Solution Architect -> Cloud
(2, 1), -- Azure Fundamentals -> Cloud
(3, 3), -- DevOps -> DevOps
(4, 2), -- Python Basics -> Programming
(5, 3); -- Linux Essentials -> DevOps
