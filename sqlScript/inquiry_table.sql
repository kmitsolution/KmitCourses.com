CREATE TABLE IF NOT EXISTS inquiry (
  id INT AUTO_INCREMENT PRIMARY KEY,
  query TEXT NOT NULL,
  country VARCHAR(100) NOT NULL,
  std_code VARCHAR(10) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  courseid INT NOT NULL,
  deleteflag TINYINT(1) NOT NULL DEFAULT 0,
  status_id INT NOT NULL DEFAULT 1,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('close', 'on hold', 'not interested') DEFAULT 'on hold',
  remarks TEXT,
  FOREIGN KEY (courseid) REFERENCES course(courseid),
  FOREIGN KEY (status_id) REFERENCES inquiry_status(id)
);