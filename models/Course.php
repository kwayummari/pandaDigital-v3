<?php
require_once __DIR__ . "/../config/database.php";

class Course
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all courses with enrollment status for a user
     */
    public function getAllCourses($userId = null, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            if ($userId) {
                $stmt = $conn->prepare("
                    SELECT c.*, 
                           CASE WHEN e.user_id IS NOT NULL THEN 1 ELSE 0 END as is_enrolled,
                           '0' as payment_status,
                           COUNT(DISTINCT v.id) as total_videos,
                           COUNT(DISTINCT q.id) as total_questions,
                           c.price,
                           c.courseIsPaidStatusId
                    FROM course c
                    LEFT JOIN video v ON v.course_id = c.id
                    LEFT JOIN questions q ON q.video_id = v.id
                    LEFT JOIN enrolled e ON e.course_id = c.id AND e.user_id = ?
                    GROUP BY c.id
                    ORDER BY c.id DESC
                    LIMIT ?
                ");
                $stmt->execute([$userId, $limit]);
            } else {
                $stmt = $conn->prepare("
                    SELECT c.*, 
                           COUNT(DISTINCT v.id) as total_videos,
                           COUNT(DISTINCT q.id) as total_questions,
                           c.price,
                           c.courseIsPaidStatusId
                    FROM course c
                    LEFT JOIN video v ON v.course_id = c.id
                    LEFT JOIN questions q ON q.video_id = v.id
                    GROUP BY c.id
                    ORDER BY c.id DESC
                    LIMIT ?
                ");
                $stmt->execute([$limit]);
            }

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching courses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get course by ID
     */
    public function getCourseById($courseId, $userId = null)
    {
        try {
            $conn = $this->db->getConnection();

            if ($userId) {
                $stmt = $conn->prepare("
                    SELECT c.*, 
                           CASE WHEN e.user_id IS NOT NULL THEN 1 ELSE 0 END as is_enrolled,
                           '0' as payment_status,
                           COUNT(DISTINCT v.id) as total_videos,
                           COUNT(DISTINCT q.id) as total_questions,
                           c.price,
                           c.courseIsPaidStatusId
                    FROM course c
                    LEFT JOIN video v ON v.course_id = c.id
                    LEFT JOIN questions q ON q.video_id = v.id
                    LEFT JOIN enrolled e ON e.course_id = c.id AND e.user_id = ?
                    WHERE c.id = ?
                    GROUP BY c.id
                ");
                $stmt->execute([$userId, $courseId]);
            } else {
                $stmt = $conn->prepare("
                    SELECT c.*, 
                           COUNT(DISTINCT v.id) as total_videos,
                           COUNT(DISTINCT q.id) as total_questions,
                           c.price,
                           c.courseIsPaidStatusId
                    FROM course c
                    LEFT JOIN video v ON v.course_id = c.id
                    LEFT JOIN questions q ON q.video_id = v.id
                    WHERE c.id = ?
                    GROUP BY c.id
                ");
                $stmt->execute([$courseId]);
            }

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching course: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get course videos
     */
    public function getCourseVideos($courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT * FROM video 
                WHERE course_id = ? 
                ORDER BY id ASC
            ");

            $stmt->execute([$courseId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching course videos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get video questions
     */
    public function getVideoQuestions($videoId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT q.*, GROUP_CONCAT(a.id) as answer_ids, GROUP_CONCAT(a.name) as answer_names
                FROM questions q
                LEFT JOIN answers a ON a.qn_id = q.id
                WHERE q.video_id = ?
                GROUP BY q.id
                ORDER BY q.id ASC
            ");

            $stmt->execute([$videoId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching video questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Enroll user in a course
     */
    public function enrollUser($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if already enrolled
            $stmt = $conn->prepare("SELECT id FROM enrolled WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$userId, $courseId]);

            if ($stmt->fetch()) {
                return false; // Already enrolled
            }

            // Enroll user
            $stmt = $conn->prepare("
                INSERT INTO enrolled (user_id, course_id, date_created) 
                VALUES (?, ?, NOW())
            ");

            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            error_log("Error enrolling user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user is enrolled in course
     */
    public function isUserEnrolled($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT e.*, ct.status as payment_status, c.courseIsPaidStatusId
                FROM enrolled e 
                JOIN course c ON c.id = e.course_id
                LEFT JOIN courseTransactions ct ON ct.courseId = c.id AND ct.studentId = e.user_id
                WHERE e.course_id = ? AND e.user_id = ?
            ");

            $stmt->execute([$courseId, $userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error checking enrollment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's enrolled courses
     */
    public function getUserEnrolledCourses($userId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT c.*, e.date_created as enrollment_date,
                       COUNT(DISTINCT v.id) as total_videos,
                       COUNT(DISTINCT q.id) as total_questions
                FROM enrolled e
                JOIN course c ON c.id = e.course_id
                LEFT JOIN video v ON v.course_id = c.id
                LEFT JOIN questions q ON q.video_id = v.id
                WHERE e.user_id = ?
                GROUP BY c.id
                ORDER BY e.date_created DESC
                LIMIT ?
            ");

            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching enrolled courses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get courses user has actually interacted with (based on quiz activity)
     */
    public function getUserActiveCourses($userId, $limit = 50)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT DISTINCT c.*, 
                       COUNT(DISTINCT v.id) as total_videos,
                       COUNT(DISTINCT q.id) as total_questions,
                       MAX(a.date_created) as last_activity
                FROM course c
                JOIN video v ON v.course_id = c.id
                JOIN questions q ON q.video_id = v.id
                JOIN algorithm a ON a.qn_id = q.id
                WHERE a.user_id = ?
                GROUP BY c.id
                ORDER BY last_activity DESC
                LIMIT ?
            ");

            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching active courses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate course progress for a user
     */
    public function calculateCourseProgress($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    COUNT(DISTINCT q.id) as total_questions,
                    COUNT(DISTINCT a.qn_id) as answered_questions,
                    COUNT(DISTINCT CASE WHEN ans.status = 'true' THEN a.qn_id END) as correct_answers
                FROM video v
                LEFT JOIN questions q ON q.video_id = v.id
                LEFT JOIN algorithm a ON a.qn_id = q.id AND a.user_id = ?
                LEFT JOIN answers ans ON ans.id = a.ans_id
                WHERE v.course_id = ?
            ");

            $stmt->execute([$userId, $courseId]);
            $result = $stmt->fetch();

            $totalQuestions = $result['total_questions'] ?: 1;
            $answeredQuestions = $result['answered_questions'] ?: 0;
            $correctAnswers = $result['correct_answers'] ?: 0;

            return [
                'total_questions' => $totalQuestions,
                'answered_questions' => $answeredQuestions,
                'correct_answers' => $correctAnswers,
                'completion_percentage' => ($answeredQuestions / $totalQuestions) * 100,
                'accuracy_percentage' => $answeredQuestions > 0 ? ($correctAnswers / $answeredQuestions) * 100 : 0
            ];
        } catch (PDOException $e) {
            error_log("Error calculating course progress: " . $e->getMessage());
            return [
                'total_questions' => 0,
                'answered_questions' => 0,
                'correct_answers' => 0,
                'completion_percentage' => 0,
                'accuracy_percentage' => 0
            ];
        }
    }

    /**
     * Get course statistics
     */
    public function getCourseStats($courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Get total lessons
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total_lessons
                FROM video 
                WHERE course_id = ?
            ");
            $stmt->execute([$courseId]);
            $lessonsResult = $stmt->fetch();

            // Get total questions
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total_questions
                FROM questions q
                JOIN video v ON q.video_id = v.id
                WHERE v.course_id = ?
            ");
            $stmt->execute([$courseId]);
            $questionsResult = $stmt->fetch();

            // Get total students
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT user_id) as total_students
                FROM enrolled 
                WHERE course_id = ?
            ");
            $stmt->execute([$courseId]);
            $studentsResult = $stmt->fetch();

            // Get average score
            $stmt = $conn->prepare("
                SELECT AVG(score_percentage) as average_score
                FROM quiz_attempts 
                WHERE course_id = ?
            ");
            $stmt->execute([$courseId]);
            $scoreResult = $stmt->fetch();

            return [
                'total_lessons' => $lessonsResult['total_lessons'] ?? 0,
                'total_questions' => $questionsResult['total_questions'] ?? 0,
                'total_students' => $studentsResult['total_students'] ?? 0,
                'average_score' => $scoreResult['average_score'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting course stats: " . $e->getMessage());
            return [
                'total_lessons' => 0,
                'total_questions' => 0,
                'total_students' => 0,
                'average_score' => 0
            ];
        }
    }

    /**
     * Track course view by user
     */
    public function trackCourseView($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if view already exists for today
            $stmt = $conn->prepare("
                SELECT id FROM course_views 
                WHERE user_id = ? AND course_id = ? AND DATE(view_date) = CURDATE()
            ");
            $stmt->execute([$userId, $courseId]);

            if (!$stmt->fetch()) {
                // Insert new view for today
                $stmt = $conn->prepare("
                    INSERT INTO course_views (user_id, course_id, view_date, view_count) 
                    VALUES (?, ?, NOW(), 1)
                ");
                $stmt->execute([$userId, $courseId]);
            } else {
                // Update existing view count for today
                $stmt = $conn->prepare("
                    UPDATE course_views 
                    SET view_count = view_count + 1, last_view = NOW()
                    WHERE user_id = ? AND course_id = ? AND DATE(view_date) = CURDATE()
                ");
                $stmt->execute([$userId, $courseId]);
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error tracking course view: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has completed a course (eligible for certificate)
     */
    public function hasCompletedCourse($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Get all videos for the course
            $stmt = $conn->prepare("SELECT id FROM video WHERE course_id = ?");
            $stmt->execute([$courseId]);
            $videos = $stmt->fetchAll();

            if (empty($videos)) {
                return false; // No videos in course
            }

            $totalVideos = count($videos);
            $completedVideos = 0;

            foreach ($videos as $video) {
                // Check if user has completed this video's quiz
                $stmt = $conn->prepare("
                    SELECT COUNT(*) as completed
                    FROM algorithm a
                    JOIN questions q ON a.qn_id = q.id
                    WHERE a.user_id = ? AND q.video_id = ?
                ");
                $stmt->execute([$userId, $video['id']]);
                $result = $stmt->fetch();

                if ($result['completed'] > 0) {
                    $completedVideos++;
                }
            }

            // Course is completed if user has completed at least 80% of videos
            $completionRate = ($completedVideos / $totalVideos) * 100;
            return $completionRate >= 80;
        } catch (PDOException $e) {
            error_log("Error checking course completion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate certificate for completed course
     */
    public function generateCertificate($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user has completed the course
            if (!$this->hasCompletedCourse($userId, $courseId)) {
                return false;
            }

            // Check if certificate already exists
            $stmt = $conn->prepare("
                SELECT id FROM course_certificates 
                WHERE user_id = ? AND course_id = ?
            ");
            $stmt->execute([$userId, $courseId]);

            if ($stmt->fetch()) {
                return true; // Certificate already exists
            }

            // Get completion percentage
            $userProgress = $this->calculateCourseProgress($userId, $courseId);
            $completionPercentage = round($userProgress['completion_percentage']);

            // Generate new certificate
            $stmt = $conn->prepare("
                INSERT INTO course_certificates (user_id, course_id, completion_date, completion_percentage, certificate_number, status) 
                VALUES (?, ?, NOW(), ?, ?, 'active')
            ");

            $certificateNumber = 'CERT-' . strtoupper(uniqid());
            $stmt->execute([$userId, $courseId, $completionPercentage, $certificateNumber]);
            return $certificateNumber;
        } catch (PDOException $e) {
            error_log("Error generating certificate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's certificates
     */
    public function getUserCertificates($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT cc.*, c.name as course_name, c.description as course_description
                FROM course_certificates cc
                JOIN course c ON cc.course_id = c.id
                WHERE cc.user_id = ? AND cc.status = 'active'
                ORDER BY cc.completion_date DESC
            ");
            $stmt->execute([$userId]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user certificates: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalCourses()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM course");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total courses: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllCoursesForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    c.id, c.name, c.description, c.courseIsPaidStatusId,
                    COUNT(DISTINCT v.id) as total_videos,
                    COUNT(DISTINCT q.id) as total_questions,
                    COUNT(DISTINCT e.user_id) as total_students
                FROM course c
                LEFT JOIN video v ON c.id = v.course_id
                LEFT JOIN question q ON v.id = q.video_id
                LEFT JOIN enrolled e ON c.id = e.course_id
                GROUP BY c.id
                ORDER BY c.id DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all courses for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getOverallCourseStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total lessons
            $stmt = $conn->prepare("SELECT COUNT(*) as total_lessons FROM video");
            $stmt->execute();
            $lessonsResult = $stmt->fetch();

            // Get total questions
            $stmt = $conn->prepare("SELECT COUNT(*) as total_questions FROM question");
            $stmt->execute();
            $questionsResult = $stmt->fetch();

            // Get total students
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as total_students FROM enrolled");
            $stmt->execute();
            $studentsResult = $stmt->fetch();

            return [
                'total_lessons' => $lessonsResult['total_lessons'] ?? 0,
                'total_questions' => $questionsResult['total_questions'] ?? 0,
                'total_students' => $studentsResult['total_students'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall course stats: " . $e->getMessage());
            return [
                'total_lessons' => 0,
                'total_questions' => 0,
                'total_students' => 0
            ];
        }
    }

    public function deleteCourse($courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if course exists
            $stmt = $conn->prepare("SELECT id FROM course WHERE id = ?");
            $stmt->execute([$courseId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete course (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM course WHERE id = ?");
            return $stmt->execute([$courseId]);
        } catch (PDOException $e) {
            error_log("Error deleting course: " . $e->getMessage());
            return false;
        }
    }

    public function toggleCourseStatus($courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // For now, we'll just return true as the status toggle logic
            // would need to be implemented based on your database structure
            // You might want to add a status field to the course table

            return true;
        } catch (PDOException $e) {
            error_log("Error toggling course status: " . $e->getMessage());
            return false;
        }
    }

    // Video Management Methods
    public function getAllVideosForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    v.id, v.title, v.description, v.video_url, v.duration,
                    c.name as course_name,
                    COUNT(DISTINCT q.id) as total_questions
                FROM video v
                LEFT JOIN course c ON v.course_id = c.id
                LEFT JOIN question q ON v.id = q.video_id
                GROUP BY v.id
                ORDER BY v.id DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all videos for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalVideos()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM video");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total videos: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallVideoStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total courses with videos
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT course_id) as total_courses FROM video");
            $stmt->execute();
            $coursesResult = $stmt->fetch();

            // Get total questions across all videos
            $stmt = $conn->prepare("SELECT COUNT(*) as total_questions FROM question");
            $stmt->execute();
            $questionsResult = $stmt->fetch();

            // Get total students enrolled in courses with videos
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT e.user_id) as total_students 
                FROM enrolled e
                JOIN video v ON e.course_id = v.course_id
            ");
            $stmt->execute();
            $studentsResult = $stmt->fetch();

            return [
                'total_courses' => $coursesResult['total_courses'] ?? 0,
                'total_questions' => $questionsResult['total_questions'] ?? 0,
                'total_students' => $studentsResult['total_students'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall video stats: " . $e->getMessage());
            return [
                'total_courses' => 0,
                'total_questions' => 0,
                'total_students' => 0
            ];
        }
    }

    public function deleteVideo($videoId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if video exists
            $stmt = $conn->prepare("SELECT id FROM video WHERE id = ?");
            $stmt->execute([$videoId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete video (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM video WHERE id = ?");
            return $stmt->execute([$videoId]);
        } catch (PDOException $e) {
            error_log("Error deleting video: " . $e->getMessage());
            return false;
        }
    }

    public function toggleVideoStatus($videoId)
    {
        try {
            $conn = $this->db->getConnection();

            // For now, we'll just return true as the status toggle logic
            // would need to be implemented based on your database structure
            // You might want to add a status field to the video table

            return true;
        } catch (PDOException $e) {
            error_log("Error toggling video status: " . $e->getMessage());
            return false;
        }
    }

    public function addVideo($courseId, $title, $description, $videoUrl, $duration = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if course exists
            $stmt = $conn->prepare("SELECT id FROM course WHERE id = ?");
            $stmt->execute([$courseId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Insert new video
            $stmt = $conn->prepare("
                INSERT INTO video (course_id, title, description, video_url, duration, date_created) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([$courseId, $title, $description, $videoUrl, $duration]);
        } catch (PDOException $e) {
            error_log("Error adding video: " . $e->getMessage());
            return false;
        }
    }

    // Question Management Methods
    public function getAllQuestionsForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    q.id, q.question_text, q.question_type, q.correct_answer,
                    v.title as video_title,
                    c.name as course_name,
                    COUNT(DISTINCT qa.id) as total_answers
                FROM question q
                LEFT JOIN video v ON q.video_id = v.id
                LEFT JOIN course c ON v.course_id = c.id
                LEFT JOIN question_answers qa ON q.id = qa.question_id
                GROUP BY q.id
                ORDER BY q.id DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all questions for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalQuestions()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM question");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total questions: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallQuestionStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total courses with questions
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT c.id) as total_courses 
                FROM course c
                JOIN video v ON c.id = v.course_id
                JOIN question q ON v.id = q.video_id
            ");
            $stmt->execute();
            $coursesResult = $stmt->fetch();

            // Get total videos with questions
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT v.id) as total_videos 
                FROM video v
                JOIN question q ON v.id = q.video_id
            ");
            $stmt->execute();
            $videosResult = $stmt->fetch();

            // Get total students enrolled in courses with questions
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT e.user_id) as total_students 
                FROM enrolled e
                JOIN video v ON e.course_id = v.course_id
                JOIN question q ON v.id = q.video_id
            ");
            $stmt->execute();
            $studentsResult = $stmt->fetch();

            return [
                'total_courses' => $coursesResult['total_courses'] ?? 0,
                'total_videos' => $videosResult['total_videos'] ?? 0,
                'total_students' => $studentsResult['total_students'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall question stats: " . $e->getMessage());
            return [
                'total_courses' => 0,
                'total_videos' => 0,
                'total_students' => 0
            ];
        }
    }

    public function deleteQuestion($questionId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if question exists
            $stmt = $conn->prepare("SELECT id FROM question WHERE id = ?");
            $stmt->execute([$questionId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete question (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM question WHERE id = ?");
            return $stmt->execute([$questionId]);
        } catch (PDOException $e) {
            error_log("Error deleting question: " . $e->getMessage());
            return false;
        }
    }

    public function toggleQuestionStatus($questionId)
    {
        try {
            $conn = $this->db->getConnection();

            // For now, we'll just return true as the status toggle logic
            // would need to be implemented based on your database structure
            // You might want to add a status field to the question table

            return true;
        } catch (PDOException $e) {
            error_log("Error toggling question status: " . $e->getMessage());
            return false;
        }
    }

    public function addQuestion($videoId, $questionText, $questionType, $correctAnswer, $options = [])
    {
        try {
            $conn = $this->db->getConnection();

            // Check if video exists
            $stmt = $conn->prepare("SELECT id FROM video WHERE id = ?");
            $stmt->execute([$videoId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Insert new question
            $stmt = $conn->prepare("
                INSERT INTO question (video_id, question_text, question_type, correct_answer, date_created) 
                VALUES (?, ?, ?, ?, NOW())
            ");

            $result = $stmt->execute([$videoId, $questionText, $questionType, $correctAnswer]);

            if ($result && $questionType === 'multiple_choice' && !empty($options)) {
                $questionId = $conn->lastInsertId();

                // Insert question options
                foreach ($options as $option) {
                    if (!empty(trim($option))) {
                        $stmt = $conn->prepare("
                            INSERT INTO question_answers (question_id, answer_text, is_correct, date_created)
                            VALUES (?, ?, ?, NOW())
                        ");

                        $isCorrect = (strtolower(trim($option)) === strtolower($correctAnswer)) ? 1 : 0;
                        $stmt->execute([$questionId, trim($option), $isCorrect]);
                    }
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error adding question: " . $e->getMessage());
            return false;
        }
    }

    public function getAllVideosWithCourses()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    v.id, v.title, v.description,
                    c.name as course_name
                FROM video v
                LEFT JOIN course c ON v.course_id = c.id
                ORDER BY c.name, v.title
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all videos with courses: " . $e->getMessage());
            return [];
        }
    }

    public function getCourseCategories()
    {
        try {
            $conn = $this->db->getConnection();

            // Try to get categories without status filter first
            $stmt = $conn->prepare("
                SELECT 
                    category,
                    COUNT(*) as course_count
                FROM course 
                GROUP BY category 
                ORDER BY course_count DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting course categories: " . $e->getMessage());
            return [];
        }
    }

    public function getFeaturedCourses($limit = 8)
    {
        try {
            $conn = $this->db->getConnection();

            // Simplified query without complex joins that might not exist
            $stmt = $conn->prepare("
                SELECT 
                    c.id, 
                    c.name,
                    c.description,
                    c.category_id as category,
                    c.price,
                    c.photo,
                    c.difficulty_level,
                    c.estimated_duration,
                    c.date_created,
                    c.total_enrollments,
                    c.average_rating
                FROM course c
                ORDER BY c.id DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $results = $stmt->fetchAll();

            // Debug logging
            error_log("getFeaturedCourses: Found " . count($results) . " courses");
            if (!empty($results)) {
                error_log("getFeaturedCourses: First course fields: " . implode(', ', array_keys($results[0])));
            }

            return $results;
        } catch (PDOException $e) {
            error_log("Error getting featured courses: " . $e->getMessage());
            return [];
        }
    }

    public function formatDate($dateString)
    {
        if (empty($dateString)) {
            return 'Hivi karibuni';
        }

        $timestamp = strtotime($dateString);
        if ($timestamp === false) {
            return 'Hivi karibuni';
        }

        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'Hivi karibuni';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "Mda wa dakika $minutes";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "Mda wa saa $hours";
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return "Mda wa siku $days";
        } else {
            return date('d/m/Y', $timestamp);
        }
    }

    public function truncateText($text, $length = 100)
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '...';
    }

    public function getImageUrl($imageName)
    {
        // Check if it's a full URL or just filename
        if (filter_var($imageName, FILTER_VALIDATE_URL)) {
            return $imageName;
        }

        // Check if image exists in uploads directory
        $imagePath = __DIR__ . '/../uploads/courses/' . $imageName;
        if (file_exists($imagePath)) {
            return 'uploads/courses/' . $imageName;
        }

        // Fallback to a default image
        return 'images/courses/default-course.jpg';
    }

    // Payment Transaction Methods - Using existing courseTransactions table
    public function createPaymentTransaction($transactionData)
    {
        try {
            $conn = $this->db->getConnection();

            // Map provider names to database format
            $providerMapping = [
                'mpesa' => 'mpesa',
                'tigo' => 'tigo', // Store as 'tigo' in database (Mix by YAS)
                'airtel' => 'airtel'
            ];

            $dbProvider = $providerMapping[$transactionData['provider']] ?? $transactionData['provider'];

            $stmt = $conn->prepare("
                INSERT INTO courseTransactions (
                    studentId, courseId, quantity, amount, phone, 
                    referenceNo, mobileType, status, dateCreated
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $transactionData['userId'],
                $transactionData['courseId'],
                1, // quantity
                $transactionData['amount'],
                $transactionData['phone'],
                $transactionData['referenceNumber'],
                $dbProvider,
                0 // status: 0 = pending, 1 = completed
            ]);

            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating payment transaction: " . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentTransaction($transactionId, $status)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE courseTransactions 
                SET status = ? 
                WHERE id = ?
            ");

            return $stmt->execute([$status, $transactionId]);
        } catch (PDOException $e) {
            error_log("Error updating payment transaction: " . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentTransactionByReference($referenceNumber, $status)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                UPDATE courseTransactions 
                SET status = ? 
                WHERE referenceNo = ?
            ");

            return $stmt->execute([$status, $referenceNumber]);
        } catch (PDOException $e) {
            error_log("Error updating payment transaction by reference: " . $e->getMessage());
            return false;
        }
    }

    public function getPendingPaymentTransaction($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT * FROM courseTransactions 
                WHERE studentId = ? AND courseId = ? 
                AND status IN (0, 2) -- 0 = pending, 2 = processing
                ORDER BY dateCreated DESC 
                LIMIT 1
            ");

            $stmt->execute([$userId, $courseId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting pending payment transaction: " . $e->getMessage());
            return false;
        }
    }

    public function hasPaidCourseAccess($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT id FROM paidCourse 
                WHERE userId = ? AND courseId = ?
            ");

            $stmt->execute([$userId, $courseId]);
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Error checking paid course access: " . $e->getMessage());
            return false;
        }
    }

    public function createPaidCourseAccess($userId, $courseId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if access already exists
            $stmt = $conn->prepare("
                SELECT id FROM paidCourse 
                WHERE userId = ? AND courseId = ?
            ");

            $stmt->execute([$userId, $courseId]);
            if ($stmt->fetch()) {
                return true; // Access already exists
            }

            // Create new access record
            $stmt = $conn->prepare("
                INSERT INTO paidCourse (userId, courseId, dateCreated) 
                VALUES (?, ?, NOW())
            ");

            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            error_log("Error creating paid course access: " . $e->getMessage());
            return false;
        }
    }

    public function logPaymentCallback($callbackData)
    {
        try {
            // Log to a simple text file for now, or you can create a callbacks table if needed
            $logFile = __DIR__ . '/../user/payment_callback_log.txt';
            $logData = date('Y-m-d H:i:s') . " - Callback: " . json_encode($callbackData) . "\n";
            file_put_contents($logFile, $logData, FILE_APPEND);

            return true;
        } catch (Exception $e) {
            error_log("Error logging payment callback: " . $e->getMessage());
            return false;
        }
    }
}
