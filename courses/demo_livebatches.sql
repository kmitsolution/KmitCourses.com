-- Demo data for livebatches table
-- Assumes courseid 1 = AWS Solution Architect, 2 = Azure Fundamentals, 3 = DevOps

INSERT INTO livebatches (mode, courseid, startdate, enddate) VALUES
('Online', 1, '2025-08-20', '2025-09-20'),
('Online', 2, '2025-08-25', '2025-10-01'),
('Online', 3, '2025-09-01', '2025-10-15'),
('Online', 1, '2025-09-10', '2025-10-10');
