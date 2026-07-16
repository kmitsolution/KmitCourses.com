CREATE TABLE IF NOT EXISTS inquiry_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  status VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default statuses
INSERT IGNORE INTO inquiry_status (status) VALUES ('on hold'), ('close'), ('not interested');