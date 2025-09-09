<?php
require_once __DIR__ . "/../config/database.php";

class Ranking
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllRankingsForAdmin($period = 'monthly', $page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $dateFilter = $this->getDateFilter($period);

            $stmt = $conn->prepare("
                SELECT 
                    r.id, r.user_id, r.total_score, r.level, r.rank_position, r.date_updated,
                    u.first_name, u.last_name, u.email,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name,
                    (SELECT COUNT(*) FROM course_progress cp WHERE cp.user_id = r.user_id AND cp.status = 'completed') as courses_completed,
                    (SELECT COUNT(*) FROM video_views vv WHERE vv.user_id = r.user_id) as videos_watched,
                    (SELECT COUNT(*) FROM question_attempts qa WHERE qa.user_id = r.user_id AND qa.is_correct = 1) as questions_answered,
                    (SELECT COUNT(*) FROM blogs b WHERE b.author_id = r.user_id AND b.status = 'published') as blogs_published
                FROM user_rankings r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE 1=1 $dateFilter
                ORDER BY r.total_score DESC, r.date_updated DESC 
                LIMIT ? OFFSET ?
            ");

            $params = [$perPage, $offset];
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all rankings for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalRankings($period = 'monthly')
    {
        try {
            $conn = $this->db->getConnection();

            $dateFilter = $this->getDateFilter($period);

            $stmt = $conn->prepare("
                SELECT COUNT(*) as total 
                FROM user_rankings r 
                WHERE 1=1 $dateFilter
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total rankings: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallRankingStats($period = 'monthly')
    {
        try {
            $conn = $this->db->getConnection();

            $dateFilter = $this->getDateFilter($period);

            // Get average score
            $stmt = $conn->prepare("
                SELECT AVG(total_score) as average_score, MAX(total_score) as top_score, COUNT(*) as total_users
                FROM user_rankings r 
                WHERE 1=1 $dateFilter
            ");
            $stmt->execute();
            $scoreResult = $stmt->fetch();

            // Get active users count
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT user_id) as active_users 
                FROM user_rankings r 
                WHERE 1=1 $dateFilter AND total_score > 0
            ");
            $stmt->execute();
            $activeResult = $stmt->fetch();

            return [
                'average_score' => round($scoreResult['average_score'] ?? 0),
                'top_score' => $scoreResult['top_score'] ?? 0,
                'total_users' => $scoreResult['total_users'] ?? 0,
                'active_users' => $activeResult['active_users'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall ranking stats: " . $e->getMessage());
            return [
                'average_score' => 0,
                'top_score' => 0,
                'total_users' => 0,
                'active_users' => 0
            ];
        }
    }

    public function getTopPerformers($period = 'monthly', $limit = 10)
    {
        try {
            $conn = $this->db->getConnection();

            $dateFilter = $this->getDateFilter($period);

            $stmt = $conn->prepare("
                SELECT 
                    r.user_id, r.total_score, r.level,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM user_rankings r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE 1=1 $dateFilter
                ORDER BY r.total_score DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top performers: " . $e->getMessage());
            return [];
        }
    }

    public function getRankingTrends($period = 'monthly', $days = 30)
    {
        try {
            $conn = $this->db->getConnection();

            $dateFilter = $this->getDateFilter($period);

            $stmt = $conn->prepare("
                SELECT 
                    DATE(r.date_updated) as date,
                    AVG(r.total_score) as average_score,
                    COUNT(*) as participants
                FROM user_rankings r
                WHERE 1=1 $dateFilter
                GROUP BY DATE(r.date_updated)
                ORDER BY date DESC
                LIMIT ?
            ");
            $stmt->execute([$days]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting ranking trends: " . $e->getMessage());
            return [];
        }
    }

    public function calculateUserScore($userId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return false;
            }

            $totalScore = 0;

            // Course completion points (100 points per course)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as completed_courses 
                FROM course_progress 
                WHERE user_id = ? AND status = 'completed'
            ");
            $stmt->execute([$userId]);
            $courseResult = $stmt->fetch();
            $totalScore += ($courseResult['completed_courses'] * 100);

            // Video watching points (10 points per video)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as watched_videos 
                FROM video_views 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $videoResult = $stmt->fetch();
            $totalScore += ($videoResult['watched_videos'] * 10);

            // Correct question answers (20 points per correct answer)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as correct_answers 
                FROM question_attempts 
                WHERE user_id = ? AND is_correct = 1
            ");
            $stmt->execute([$userId]);
            $questionResult = $stmt->fetch();
            $totalScore += ($questionResult['correct_answers'] * 20);

            // Blog publications (50 points per published blog)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as published_blogs 
                FROM blogs 
                WHERE author_id = ? AND status = 'published'
            ");
            $stmt->execute([$userId]);
            $blogResult = $stmt->fetch();
            $totalScore += ($blogResult['published_blogs'] * 50);

            // Feedback submissions (5 points per feedback)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as feedback_count 
                FROM feedback 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $feedbackResult = $stmt->fetch();
            $totalScore += ($feedbackResult['feedback_count'] * 5);

            // Opportunity applications (15 points per application)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as applications 
                FROM opportunity_applications 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $applicationResult = $stmt->fetch();
            $totalScore += ($applicationResult['applications'] * 15);

            // Calculate level based on score
            $level = $this->calculateLevel($totalScore);

            return [
                'total_score' => $totalScore,
                'level' => $level,
                'breakdown' => [
                    'courses' => $courseResult['completed_courses'] * 100,
                    'videos' => $videoResult['watched_videos'] * 10,
                    'questions' => $questionResult['correct_answers'] * 20,
                    'blogs' => $blogResult['published_blogs'] * 50,
                    'feedback' => $feedbackResult['feedback_count'] * 5,
                    'applications' => $applicationResult['applications'] * 15
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error calculating user score: " . $e->getMessage());
            return false;
        }
    }

    public function recalculateUserScore($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $scoreData = $this->calculateUserScore($userId);
            if (!$scoreData) {
                return false;
            }

            // Update or insert ranking
            $stmt = $conn->prepare("
                INSERT INTO user_rankings (user_id, total_score, level, date_updated) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    total_score = VALUES(total_score),
                    level = VALUES(level),
                    date_updated = NOW()
            ");

            $result = $stmt->execute([$userId, $scoreData['total_score'], $scoreData['level']]);

            if ($result) {
                // Update rank positions for all users
                $this->updateRankPositions();
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error recalculating user score: " . $e->getMessage());
            return false;
        }
    }

    public function adjustUserScore($userId, $adjustment, $reason = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user ranking exists
            $stmt = $conn->prepare("SELECT total_score FROM user_rankings WHERE user_id = ?");
            $stmt->execute([$userId]);
            $currentRanking = $stmt->fetch();

            if (!$currentRanking) {
                // Create ranking if it doesn't exist
                $this->recalculateUserScore($userId);
                $stmt->execute([$userId]);
                $currentRanking = $stmt->fetch();
            }

            $newScore = max(0, $currentRanking['total_score'] + intval($adjustment));
            $newLevel = $this->calculateLevel($newScore);

            // Update score
            $stmt = $conn->prepare("
                UPDATE user_rankings 
                SET total_score = ?, level = ?, date_updated = NOW()
                WHERE user_id = ?
            ");

            $result = $stmt->execute([$newScore, $newLevel, $userId]);

            if ($result) {
                // Log the adjustment
                $stmt = $conn->prepare("
                    INSERT INTO score_adjustments (user_id, adjustment, reason, admin_id, date_created)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$userId, $adjustment, $reason, $_SESSION['user_id'] ?? 1]);

                // Update rank positions
                $this->updateRankPositions();
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error adjusting user score: " . $e->getMessage());
            return false;
        }
    }

    public function updateRankPositions()
    {
        try {
            $conn = $this->db->getConnection();

            // Update rank positions based on total score
            $stmt = $conn->prepare("
                SET @rank = 0;
                UPDATE user_rankings 
                SET rank_position = (@rank := @rank + 1)
                ORDER BY total_score DESC, date_updated ASC;
            ");

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating rank positions: " . $e->getMessage());
            return false;
        }
    }

    public function getUserRanking($userId, $period = 'monthly')
    {
        try {
            $conn = $this->db->getConnection();

            $dateFilter = $this->getDateFilter($period);

            $stmt = $conn->prepare("
                SELECT 
                    r.*, u.first_name, u.last_name, u.email,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM user_rankings r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.user_id = ? AND 1=1 $dateFilter
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting user ranking: " . $e->getMessage());
            return false;
        }
    }

    public function getRankingsByLevel($level, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    r.*, u.first_name, u.last_name, u.email,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM user_rankings r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.level = ?
                ORDER BY r.total_score DESC, r.date_updated DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$level, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting rankings by level: " . $e->getMessage());
            return [];
        }
    }

    public function searchRankings($searchTerm, $level = null, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT 
                    r.*, u.first_name, u.last_name, u.email,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM user_rankings r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)
            ";
            $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

            if ($level) {
                $sql .= " AND r.level = ?";
                $params[] = $level;
            }

            $sql .= " ORDER BY r.total_score DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching rankings: " . $e->getMessage());
            return [];
        }
    }

    public function getRankingReport($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    level,
                    COUNT(*) as user_count,
                    AVG(total_score) as avg_score,
                    MIN(total_score) as min_score,
                    MAX(total_score) as max_score
                FROM user_rankings
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " WHERE date_updated BETWEEN ? AND ?";
                $params = [$startDate, $endDate];
            }

            $sql .= " GROUP BY level ORDER BY level";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting ranking report: " . $e->getMessage());
            return [];
        }
    }

    private function calculateLevel($score)
    {
        if ($score >= 10000) return 10;
        if ($score >= 8000) return 9;
        if ($score >= 6000) return 8;
        if ($score >= 4000) return 7;
        if ($score >= 2500) return 6;
        if ($score >= 1500) return 5;
        if ($score >= 800) return 4;
        if ($score >= 400) return 3;
        if ($score >= 150) return 2;
        return 1;
    }

    private function getDateFilter($period)
    {
        switch ($period) {
            case 'weekly':
                return "AND r.date_updated >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case 'monthly':
                return "AND r.date_updated >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case 'all_time':
            default:
                return "";
        }
    }

    public function getAllPowerRankings()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    CONCAT(u.first_name, ' ', u.last_name) AS fullname,
                    COUNT(a.id) AS total_correct_answers
                FROM 
                    algorithm al
                JOIN 
                    answers a ON al.ans_id = a.id
                JOIN 
                    users u ON al.user_id = u.id
                WHERE 
                    a.status = 'true'
                GROUP BY 
                    u.id, u.first_name, u.last_name
                ORDER BY 
                    total_correct_answers DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting power rankings: " . $e->getMessage());
            return [];
        }
    }

    public function getPowerRankingStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total participants
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT al.user_id) as total_participants
                FROM algorithm al
                JOIN answers a ON al.ans_id = a.id
                WHERE a.status = 'true'
            ");
            $stmt->execute();
            $participantsResult = $stmt->fetch();

            // Get total correct answers
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total_correct_answers
                FROM algorithm al
                JOIN answers a ON al.ans_id = a.id
                WHERE a.status = 'true'
            ");
            $stmt->execute();
            $answersResult = $stmt->fetch();

            // Get top score
            $stmt = $conn->prepare("
                SELECT MAX(correct_count) as top_score
                FROM (
                    SELECT COUNT(a.id) as correct_count
                    FROM algorithm al
                    JOIN answers a ON al.ans_id = a.id
                    WHERE a.status = 'true'
                    GROUP BY al.user_id
                ) as user_scores
            ");
            $stmt->execute();
            $topScoreResult = $stmt->fetch();

            return [
                'total_participants' => $participantsResult['total_participants'] ?? 0,
                'total_correct_answers' => $answersResult['total_correct_answers'] ?? 0,
                'top_score' => $topScoreResult['top_score'] ?? 0,
                'average_score' => $participantsResult['total_participants'] > 0 ?
                    round($answersResult['total_correct_answers'] / $participantsResult['total_participants']) : 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting power ranking stats: " . $e->getMessage());
            return [
                'total_participants' => 0,
                'total_correct_answers' => 0,
                'top_score' => 0,
                'average_score' => 0
            ];
        }
    }
}
