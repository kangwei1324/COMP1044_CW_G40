-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2026 at 07:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comp1044_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

CREATE TABLE `assessment` (
  `assessment_id` int(11) NOT NULL,
  `internship_id` int(11) NOT NULL,
  `assessor_id` int(11) NOT NULL,
  `task_projects` int(11) NOT NULL,
  `task_projects_comment` text DEFAULT NULL,
  `health_safety` int(11) NOT NULL,
  `health_safety_comment` text DEFAULT NULL,
  `theoretical_knowledge` int(11) NOT NULL,
  `theoretical_knowledge_comment` text DEFAULT NULL,
  `report_presentation` int(11) NOT NULL,
  `report_presentation_comment` text DEFAULT NULL,
  `clarity_of_language` int(11) NOT NULL,
  `clarity_of_language_comment` text DEFAULT NULL,
  `lifelong_learning` int(11) NOT NULL,
  `lifelong_learning_comment` text DEFAULT NULL,
  `project_management` int(11) NOT NULL,
  `project_management_comment` text DEFAULT NULL,
  `time_management` int(11) NOT NULL,
  `time_management_comment` text DEFAULT NULL,
  `overall_comments` text NOT NULL,
  `total_marks` int(11) GENERATED ALWAYS AS (`task_projects` + `health_safety` + `theoretical_knowledge` + `report_presentation` + `clarity_of_language` + `lifelong_learning` + `project_management` + `time_management`) VIRTUAL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment`
--

INSERT INTO `assessment` (`assessment_id`, `internship_id`, `assessor_id`, `task_projects`, `task_projects_comment`, `health_safety`, `health_safety_comment`, `theoretical_knowledge`, `theoretical_knowledge_comment`, `report_presentation`, `report_presentation_comment`, `clarity_of_language`, `clarity_of_language_comment`, `lifelong_learning`, `lifelong_learning_comment`, `project_management`, `project_management_comment`, `time_management`, `time_management_comment`, `overall_comments`, `last_updated`) VALUES
(1, 1, 2, 8, 'Completed assigned modules ahead of schedule.', 9, 'Consistently followed all workplace safety protocols.', 7, 'Applied university concepts well but needs more depth in networking.', 11, 'Report structure is clear; minor formatting inconsistencies.', 7, 'Writing is mostly clear; some technical terms misused.', 12, 'Attended two external seminars on cloud computing voluntarily.', 11, 'Managed tasks using Jira effectively.', 13, 'Rarely missed deadlines throughout the internship.', 'Ahmad is a reliable and self-motivated intern. Shows strong potential in backend development.', '2026-04-26 17:01:15'),
(2, 1, 4, 7, 'Tasks completed satisfactorily based on supervisor report.', 8, 'No safety incidents reported.', 8, 'Good linkage between database concepts and real-world use.', 12, 'Final report is well-structured with a proper literature review.', 7, 'Language is clear but academic writing style needs improvement.', 12, 'Proactively engaged in additional learning activities.', 11, 'Demonstrated adequate planning skills for given tasks.', 12, 'All university submission deadlines met on time.', 'Ahmad has performed well overall. His report reflects a solid understanding of his internship experience.', '2026-04-26 17:01:15'),
(3, 2, 3, 9, 'Exceeded expectations in the UI/UX redesign project.', 9, 'Always followed digital security and safety protocols.', 9, 'Demonstrated strong grasp of human-computer interaction principles.', 14, 'Outstanding report — well-researched, neatly formatted.', 9, 'Articulate and precise in both written and verbal communication.', 13, 'Completed an online UX certification on own initiative.', 13, 'Organised work using Trello; met all sprint goals.', 14, 'Punctual and proactive in meeting every deadline.', 'Nurul Ain is one of the best interns we have had. Professional, talented, and a joy to work with.', '2026-04-26 17:01:15'),
(4, 2, 4, 9, 'High quality deliverables confirmed by supervisor feedback.', 8, 'Complied with all reported safety requirements.', 9, 'Excellent connection between HCI coursework and internship tasks.', 14, 'Best written report submitted this semester — a model for others.', 8, 'Clear, professional writing throughout.', 13, 'Showed impressive self-directed learning beyond requirements.', 12, 'Strong project planning evident in logbook entries.', 13, 'All university submission deadlines met well in advance.', 'Nurul Ain has demonstrated outstanding academic and professional growth during this internship.', '2026-04-26 17:01:15'),
(5, 3, 2, 6, 'Completed tasks but required frequent guidance.', 7, 'Followed safety protocols after initial reminders.', 6, 'Struggled to connect theoretical knowledge to practical tasks.', 9, 'Report is adequate but lacks depth in technical sections.', 6, 'Writing is understandable but informal in tone.', 10, 'Attended one workshop during the internship period.', 9, 'Task management was inconsistent in the first half.', 8, 'Missed two internal deadlines in the first month.', 'Kevin showed improvement over time but needs to work on initiative and independent problem-solving.', '2026-04-26 17:01:15'),
(6, 4, 3, 7, 'Completed data pipeline tasks with moderate supervision.', 8, 'Good awareness of data security and privacy requirements.', 8, 'Applied machine learning concepts from coursework effectively.', 11, 'Report is well-written but the methodology section is thin.', 7, 'Clear writing; occasional grammatical errors.', 11, 'Completed an online Python data science course mid-internship.', 10, 'Planning was adequate; some scope creep in final weeks.', 11, 'Generally punctual; one late submission noted.', 'Priya is a competent intern with a good grasp of data concepts. Would benefit from stronger documentation habits.', '2026-04-26 17:01:15'),
(7, 4, 5, 8, 'Deliverables aligned well with internship objectives.', 8, 'No safety concerns raised during supervision visits.', 8, 'Strong theoretical foundation in data analysis is evident.', 12, 'Report demonstrates clear understanding of the internship scope.', 7, 'Writing is competent; recommend proofreading before final submission.', 11, 'Evidence of self-improvement through additional coursework.', 11, 'Logbook shows reasonable project planning throughout.', 11, 'Met all university deadlines consistently.', 'Priya has done well and her report reflects a mature understanding of data engineering practices.', '2026-04-26 17:01:15'),
(8, 5, 2, 8, 'Successfully implemented data visualization dashboards.', 8, 'Adhered to all office ergonomic guidelines.', 8, 'Strong understanding of regression analysis.', 12, 'Presentation was clear and well-organized.', 8, 'Used professional terminology correctly.', 12, 'Showed interest in learning new Python libraries.', 12, 'Met all project milestones on time.', 12, 'Consistently punctual for meetings.', 'Good performance in data science tasks.', '2026-04-26 17:01:15'),
(9, 6, 5, 9, 'Excellent progress noted in academic logbook.', 9, 'Followed all university reporting requirements.', 9, 'Integrated classroom theories into practical work.', 13, 'Final presentation was professional and thorough.', 9, 'High standard of written communication.', 13, 'Demonstrated continuous learning through research.', 13, 'Managed project timelines as per initial proposal.', 13, 'Met all internal university deadlines.', 'Excellent work in digital marketing analytics from an academic perspective.', '2026-04-26 17:01:15'),
(10, 7, 6, 7, 'Completed AI modules successfully with some guidance.', 8, 'Followed all laboratory safety standards.', 7, 'Good grasp of machine learning basics.', 11, 'Presentation was satisfactory but could be more engaging.', 7, 'Language was clear but minor grammatical errors were present.', 11, 'Explored neural networks beyond the basic requirements.', 11, 'Handled project tasks with steady progress.', 11, 'Arrived on time for most shifts.', 'Completed AI modules successfully.', '2026-04-26 17:01:15'),
(11, 7, 10, 8, 'Demonstrated sound application of AI algorithms.', 8, 'No safety concerns reported.', 8, 'Strong theoretical understanding of data ethics.', 12, 'Comprehensive report with clear technical diagrams.', 8, 'Well-written report with formal academic tone.', 12, 'Proactive in attending AI ethics seminars.', 12, 'Planning documents were detailed and realistic.', 12, 'All university submissions were on time.', 'Muhammad Luqman has shown great promise in Artificial Intelligence.', '2026-04-26 17:01:15'),
(12, 8, 7, 10, 'Exceptional understanding of cyber security protocols.', 10, 'Proactively identified potential security risks in the office.', 10, 'Outstanding knowledge of encryption techniques.', 15, 'Report was of professional publication quality.', 10, 'Flawless technical writing skills.', 15, 'Certified in CompTIA Security+ during the internship.', 15, 'Led a small team to resolve a network breach simulation.', 15, 'Highly disciplined with time and tasks.', 'Exceptional understanding of cyber security protocols.', '2026-04-26 17:01:15'),
(13, 8, 11, 9, 'Very strong theoretical application of security frameworks.', 9, 'No safety issues noted during supervision.', 10, 'Excellent depth in cryptography theories.', 14, 'Final report is a model for security documentation.', 9, 'Clear and professional language throughout.', 14, 'Outstanding self-directed research into zero-trust architectures.', 14, 'Detailed project planning and risk mitigation strategies.', 14, 'Exemplary punctuality in all submissions.', 'Siti Nurhaliza is an outstanding student with a very bright future in cyber security.', '2026-04-26 17:01:15'),
(14, 9, 12, 8, 'Solid academic performance and application of game design.', 8, 'Followed all university lab safety guidelines.', 8, 'Good understanding of game physics and logic.', 12, 'Presentation demo was polished and clear.', 8, 'Professional communication skills in progress.', 12, 'Actively sought feedback on technical reports.', 12, 'Logbook entries were detailed and consistent.', 12, 'Met all deadlines for university requirements.', 'Solid game development skills demonstrated academically.', '2026-04-26 17:01:15'),
(15, 10, 9, 9, 'Proactive in mobile application testing and bug reporting.', 9, 'Aware of mobile device handling and safety.', 9, 'Excellent grasp of iOS and Android development life cycles.', 14, 'Final presentation was engaging and professional.', 9, 'Highly articulate in explaining technical bugs.', 14, 'Mastered React Native within a short period.', 14, 'Took initiative in planning the testing phase.', 14, 'Always early for daily stand-up meetings.', 'Proactive in mobile application testing.', '2026-04-26 17:01:15'),
(16, 11, 6, 8, 'Successfully migrated legacy systems to the cloud.', 8, 'Understood cloud infrastructure safety measures.', 8, 'Competent in AWS and Azure service management.', 12, 'Report clearly detailed the migration process.', 8, 'Good technical documentation skills.', 12, 'Attended cloud computing webinars regularly.', 12, 'Managed cloud resources effectively to reduce costs.', 12, 'Delivered milestones as per the project plan.', 'Successfully migrated legacy systems to the cloud.', '2026-04-26 17:01:15'),
(17, 12, 13, 7, 'Sound understanding of networking protocols.', 7, 'Complied with all academic reporting standards.', 7, 'Strong grasp of network security theory.', 10, 'Final presentation was clear and well-paced.', 7, 'Academic writing style is developing well.', 10, 'Actively participated in network architecture forums.', 10, 'Project management skills are satisfactory.', 10, 'All submissions were on time.', 'Balanced network engineering tasks well with a strong focus on theory.', '2026-04-26 17:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `internships`
--

CREATE TABLE `internships` (
  `internship_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `industry_supervisor_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `semester` enum('Autumn','Spring','Summer') NOT NULL,
  `internship_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internships`
--

INSERT INTO `internships` (`internship_id`, `student_id`, `industry_supervisor_id`, `lecturer_id`, `company_name`, `semester`, `internship_year`) VALUES
(1, 20701234, 2, 4, 'TechCorp Solutions Sdn. Bhd.', 'Summer', '2024'),
(2, 20702345, 3, 4, 'Innovate Digital Sdn. Bhd.', 'Summer', '2024'),
(3, 20703456, 2, 5, 'TechCorp Solutions Sdn. Bhd.', 'Summer', '2024'),
(4, 20704567, 3, 5, 'Innovate Digital Sdn. Bhd.', 'Summer', '2024'),
(5, 20805123, 2, 4, 'Axiata Group Berhad', 'Autumn', '2024'),
(6, 20806234, 3, 5, 'Grab Holdings Malaysia', 'Autumn', '2024'),
(7, 20901001, 6, 10, 'Intel Malaysia', 'Spring', '2025'),
(8, 20901002, 7, 11, 'Microsoft Malaysia', 'Spring', '2025'),
(9, 20901003, 8, 12, 'Petronas Digital', 'Spring', '2025'),
(10, 20901004, 9, 13, 'Maybank Group', 'Spring', '2025'),
(11, 20901005, 6, 11, 'Shopee Malaysia', 'Summer', '2025'),
(12, 20901006, 7, 12, 'Maxis Berhad', 'Summer', '2025'),
(13, 20901007, 8, 13, 'CelcomDigi', 'Summer', '2025'),
(14, 20901008, 9, 10, 'Dyson Malaysia', 'Summer', '2025');

-- --------------------------------------------------------

--
-- Table structure for table `programme`
--

CREATE TABLE `programme` (
  `programme_id` int(11) NOT NULL,
  `programme_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programme`
--

INSERT INTO `programme` (`programme_id`, `programme_name`) VALUES
(5, 'Bachelor of Artificial Intelligence'),
(12, 'Bachelor of Business Information Systems'),
(9, 'Bachelor of Cloud Computing'),
(1, 'Bachelor of Computer Science'),
(6, 'Bachelor of Cyber Security'),
(4, 'Bachelor of Data Science'),
(7, 'Bachelor of Game Development'),
(2, 'Bachelor of Information Technology'),
(8, 'Bachelor of Mobile Computing'),
(11, 'Bachelor of Multimedia Design'),
(10, 'Bachelor of Network Engineering'),
(3, 'Bachelor of Software Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `programme_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `student_name`, `programme_id`) VALUES
(20701234, 'Ahmad Faris Bin Zulkifli', 1),
(20702345, 'Nurul Ain Binti Roslan', 2),
(20703456, 'Kevin Tan Jia Hao', 3),
(20704567, 'Priya Subramaniam', 1),
(20805123, 'Muhammad Haziq Bin Ismail', 4),
(20806234, 'Chloe Wong Mei Ling', 2),
(20901001, 'Muhammad Luqman Bin Hakim', 5),
(20901002, 'Siti Nurhaliza Binti Tarudin', 6),
(20901003, 'Tan Wei Seng', 7),
(20901004, 'Kavitha Devi A/P Muthu', 8),
(20901005, 'Adam Smith', 9),
(20901006, 'Nurul Izzah Binti Anwar', 10),
(20901007, 'Wong Kah Woh', 11),
(20901008, 'Sarah Lim Mei Yee', 12);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `role` enum('admin','industry_supervisor','lecturer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `fullname`, `role`) VALUES
(1, 'admin', 'admin@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'System Administrator', 'admin'),
(2, 'raj.kumar', 'raj.kumar@techcorp.com.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Raj Kumar', 'industry_supervisor'),
(3, 'siti.rahimah', 'siti.rahimah@innovate.com.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Siti Rahimah Binti Aziz', 'industry_supervisor'),
(4, 'dr.lim', 'lim.weikang@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Dr. Lim Wei Kang', 'lecturer'),
(5, 'dr.amirah', 'amirah.hassan@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Dr. Amirah Hassan', 'lecturer'),
(6, 'david.wong', 'david.wong@intel.com.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'David Wong', 'industry_supervisor'),
(7, 'sarah.jenkins', 'sarah.j@microsoft.com', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Sarah Jenkins', 'industry_supervisor'),
(8, 'michael.chen', 'm.chen@petronas.com.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Michael Chen', 'industry_supervisor'),
(9, 'fatimah.zahra', 'fatimah@maybank.com.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Fatimah Zahra', 'industry_supervisor'),
(10, 'dr.smith', 'robert.smith@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Dr. Robert Smith', 'lecturer'),
(11, 'dr.rossi', 'elena.rossi@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Dr. Elena Rossi', 'lecturer'),
(12, 'dr.tan', 'tan.ahkow@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Dr. Tan Ah Kow', 'lecturer'),
(13, 'dr.azmi', 'noor.azmi@university.edu.my', '$2y$10$2/B46qGpWFS1MgelqELrJ.tkyZn9RSHeuDtKCuRj1aQ7dAtzppmR6', 'Dr. Noor Azmi', 'lecturer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`assessment_id`),
  ADD UNIQUE KEY `internship_id` (`internship_id`,`assessor_id`),
  ADD KEY `assessor_id` (`assessor_id`);

--
-- Indexes for table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`internship_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`semester`,`internship_year`),
  ADD KEY `industry_supervisor_id` (`industry_supervisor_id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `programme`
--
ALTER TABLE `programme`
  ADD PRIMARY KEY (`programme_id`),
  ADD UNIQUE KEY `programme_name` (`programme_name`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `programme_id` (`programme_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessment`
--
ALTER TABLE `assessment`
  MODIFY `assessment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `internships`
--
ALTER TABLE `internships`
  MODIFY `internship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `programme`
--
ALTER TABLE `programme`
  MODIFY `programme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment`
--
ALTER TABLE `assessment`
  ADD CONSTRAINT `assessment_ibfk_1` FOREIGN KEY (`internship_id`) REFERENCES `internships` (`internship_id`),
  ADD CONSTRAINT `assessment_ibfk_2` FOREIGN KEY (`assessor_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `internships`
--
ALTER TABLE `internships`
  ADD CONSTRAINT `internships_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `internships_ibfk_2` FOREIGN KEY (`industry_supervisor_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `internships_ibfk_3` FOREIGN KEY (`lecturer_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`programme_id`) REFERENCES `programme` (`programme_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
