-- ============================================================================
-- Ministry of Health and Population - Nepal
-- Patient Record Management System
-- SECURE Database Setup Script
--
-- Security controls applied:
-- 1. Password hashes in staff table (PHP bcrypt, $2y$ compatible with password_verify()).
-- 2. Least-privilege DB account for web app (moh_app_user).
-- 3. No root credentials in application runtime.
-- ============================================================================

DROP DATABASE IF EXISTS moh_nepal_lab;
CREATE DATABASE moh_nepal_lab;
USE moh_nepal_lab;

-- ============================================================================
-- TABLE 1: staff
-- SECURED: password column stores hashes, not plaintext credentials.
-- ============================================================================

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    personal_email VARCHAR(100),
    clearance_level VARCHAR(30),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO staff (username, password, full_name, role, personal_email, clearance_level, notes) VALUES
('admin', '$2y$10$EIKOPoI9oevKgxdje7ujBubeAg4JeXVmYoAOp01MDq4pPf.ftKIUm', 'Bikram Thapa', 'System Administrator', 'bikram.thapa@gmail.com', 'Top Secret', 'Has full SSH access and database root credentials. Emergency contact: 9841234567'),
('drpandey', '$2y$10$/xd4jbeNxezCsyDfbYGrQe/SBNCfxBh2iPRaZwlQ6ftjkAiKB/nge', 'Dr. Sunita Pandey', 'Senior Medical Officer', 'sunita.pandey@hospital.gov.np', 'Level 2', 'Authorised to prescribe controlled substances. TB Ward In-charge.'),
('finance01', '$2y$10$PptrpupQyYSq5HFv3qCQZuBqCcpFIbj5QseLzsV5pWq6cD.X9uDeq', 'Rajesh Maharjan', 'Finance Manager', 'rajesh.m@mohp.gov.np', 'Level 3', 'Has access to all patient billing and insurance records. Handles NHIP claims.');

-- ============================================================================
-- TABLE 2: patients
-- NOTE: credit_card remains plaintext in this training lab dataset.
--       Production systems should encrypt cardholder data at rest.
-- ============================================================================

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(20) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    age INT,
    gender VARCHAR(10),
    district VARCHAR(100),
    diagnosis VARCHAR(100),
    contact VARCHAR(20),
    national_id VARCHAR(50),
    hiv_status VARCHAR(20),
    credit_card VARCHAR(50),
    psychiatric_notes TEXT,
    admission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO patients (patient_id, full_name, age, gender, district, diagnosis, contact, national_id, hiv_status, credit_card, psychiatric_notes) VALUES
('PAT-2024-001', 'Ram Bahadur Tamang', 45, 'Male', 'Kathmandu', 'Pulmonary TB', '9841567890', '27-01-75-12345', 'Negative', '4532-8721-6543-9012', 'No psychiatric history. Patient cooperative.'),
('PAT-2024-002', 'Sita Kumari Gurung', 32, 'Female', 'Lalitpur', 'MDR-TB', '9812345678', '25-02-68-54321', 'Positive', '5421-9876-3210-8765', 'History of depression. Currently on sertraline 50mg. Monitor for drug interactions with TB regimen.'),
('PAT-2024-003', 'Krishna Prasad Sharma', 58, 'Male', 'Bhaktapur', 'Extrapulmonary TB', '9867543210', '23-03-64-98765', 'Negative', '4916-5432-1098-7654', 'Anxiety disorder. Claustrophobic - requires sedation for MRI procedures.'),
('PAT-2024-004', 'Ganga Maya Rai', 28, 'Female', 'Pokhara', 'Latent TB', '9845678901', '33-04-94-11111', 'Unknown', '5532-1234-5678-9012', 'Postpartum depression history. Social worker referral recommended.'),
('PAT-2024-005', 'Hari Bahadur Magar', 67, 'Male', 'Chitwan', 'Drug-Resistant TB', '9823456789', '35-05-55-22222', 'Positive', '4716-9999-8888-7777', 'Severe depression with suicidal ideation. Requires psychiatric clearance before starting bedaquiline. Family notified.'),
('PAT-2024-006', 'Parbati Devi Tharu', 41, 'Female', 'Biratnagar', 'Pulmonary TB', '9878901234', '01-06-83-33333', 'Negative', '5212-4444-5555-6666', 'PTSD from domestic violence. Currently in shelter. Confidential address - do not disclose to family.');

-- ============================================================================
-- TABLE 3: internal_comms
-- ============================================================================

CREATE TABLE internal_comms (
    msg_id INT AUTO_INCREMENT PRIMARY KEY,
    sender VARCHAR(50) NOT NULL,
    recipient VARCHAR(50) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    body TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO internal_comms (sender, recipient, subject, body, sent_at) VALUES
('admin', 'all_staff', 'URGENT: Password Policy Reminder', 'Dear Team,\n\nPlease be reminded that passwords must be changed every 90 days. I know several of you have complained about this policy, but it is mandatory.\n\nAlso, STOP writing passwords on sticky notes! I found three stuck to monitors during my last walkthrough.\n\nFor those who forgot their passwords, the temporary reset password is: TempPass@2024\n\nRegards,\nBikram Thapa\nSystem Administrator', '2024-01-15 09:30:00'),
('finance01', 'admin', 'RE: Credit Card Data Storage Issue', 'Bikram ji,\n\nAs discussed in the meeting, we really need to move patient credit cards to encrypted storage. The current system stores everything in plaintext which is a serious compliance violation.\n\nI have raised this issue three times now. The Health Ministry audit is coming up in March and we will fail if this is not fixed.\n\nThe insurance companies are also asking about our PCI-DSS compliance status.\n\nPlease escalate this urgently.\n\nRajesh Maharjan\nFinance Manager', '2024-02-20 14:45:00'),
('drpandey', 'admin', 'Login Issues - System Too Slow', 'Bikram,\n\nThe patient search system is extremely slow today. Sometimes it takes 30 seconds just to load results.\n\nAlso, why does the search page show the database query at the bottom? Several nurses have asked me about it. Isnt that a security risk?\n\nI tried to access the system from home yesterday and got an error about SQL syntax. Not sure what that means but thought you should know.\n\nDr. Sunita Pandey\nSenior Medical Officer\nTB Ward', '2024-03-01 11:20:00');

-- ============================================================================
-- Least-Privilege Application Account (SECURED)
-- ============================================================================

DROP USER IF EXISTS 'moh_app_user'@'localhost';
CREATE USER 'moh_app_user'@'localhost' IDENTIFIED BY 'Str0ng!AppPass';

GRANT SELECT, INSERT, UPDATE ON moh_nepal_lab.patients TO 'moh_app_user'@'localhost';
GRANT SELECT ON moh_nepal_lab.staff TO 'moh_app_user'@'localhost';
GRANT SELECT, INSERT ON moh_nepal_lab.internal_comms TO 'moh_app_user'@'localhost';

-- Explicitly ensure dangerous privileges are not available to the app account.
REVOKE DROP, ALTER, CREATE, DELETE ON moh_nepal_lab.* FROM 'moh_app_user'@'localhost';

FLUSH PRIVILEGES;

SELECT 'Secure database moh_nepal_lab created successfully!' AS Status;
SELECT COUNT(*) AS 'Staff Records' FROM staff;
SELECT COUNT(*) AS 'Patient Records' FROM patients;
SELECT COUNT(*) AS 'Internal Messages' FROM internal_comms;
