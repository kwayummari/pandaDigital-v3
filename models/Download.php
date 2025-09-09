<?php
require_once __DIR__ . "/../config/database.php";

class Download
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllDownloadsForAdmin($startDate = null, $endDate = null, $page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT 
                    d.id, d.file_name, d.file_path, d.file_size, d.ip_address, d.status, d.date_created,
                    u.first_name, u.last_name, u.email, u.id as user_id
                FROM downloads d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE 1=1
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(d.date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " ORDER BY d.date_created DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all downloads for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalDownloads($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT COUNT(*) as total FROM downloads WHERE 1=1";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total downloads: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total count from downloadHistory table
     */
    public function getTotalDownloadHistory()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM downloadHistory");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total download history: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallDownloadStats($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    COUNT(*) as total_downloads,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(DISTINCT file_path) as unique_files,
                    SUM(file_size) as total_size
                FROM downloads 
                WHERE status = 'completed'
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();

            return [
                'total_downloads' => $result['total_downloads'] ?? 0,
                'unique_users' => $result['unique_users'] ?? 0,
                'unique_files' => $result['unique_files'] ?? 0,
                'total_size' => $result['total_size'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall download stats: " . $e->getMessage());
            return [
                'total_downloads' => 0,
                'unique_users' => 0,
                'unique_files' => 0,
                'total_size' => 0
            ];
        }
    }

    public function getDownloadTrends($startDate = null, $endDate = null, $groupBy = 'day')
    {
        try {
            $conn = $this->db->getConnection();

            $dateFormat = 'Y-%m-%d';
            if ($groupBy === 'month') {
                $dateFormat = 'Y-%m';
            } elseif ($groupBy === 'year') {
                $dateFormat = 'Y';
            }

            $sql = "
                SELECT 
                    DATE_FORMAT(date_created, ?) as date,
                    COUNT(*) as downloads
                FROM downloads 
                WHERE status = 'completed'
            ";
            $params = [$dateFormat];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " GROUP BY DATE_FORMAT(date_created, ?) ORDER BY date ASC";
            $params[] = $dateFormat;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting download trends: " . $e->getMessage());
            return [];
        }
    }

    public function getTopDownloadedFiles($startDate = null, $endDate = null, $limit = 5)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    file_name,
                    file_path,
                    file_size,
                    COUNT(*) as download_count
                FROM downloads 
                WHERE status = 'completed'
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " GROUP BY file_path, file_name, file_size ORDER BY download_count DESC LIMIT ?";
            $params[] = $limit;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top downloaded files: " . $e->getMessage());
            return [];
        }
    }

    public function addDownload($userId, $fileName, $filePath, $fileSize, $ipAddress = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return false;
            }

            $stmt = $conn->prepare("
                INSERT INTO downloads (user_id, file_name, file_path, file_size, ip_address, status, date_created) 
                VALUES (?, ?, ?, ?, ?, 'completed', NOW())
            ");

            return $stmt->execute([$userId, $fileName, $filePath, $fileSize, $ipAddress]);
        } catch (PDOException $e) {
            error_log("Error adding download: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDownload($downloadId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if download exists
            $stmt = $conn->prepare("SELECT id FROM downloads WHERE id = ?");
            $stmt->execute([$downloadId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete download record (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM downloads WHERE id = ?");
            return $stmt->execute([$downloadId]);
        } catch (PDOException $e) {
            error_log("Error deleting download: " . $e->getMessage());
            return false;
        }
    }

    public function blockUserDownloads($userId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Add user to blocked downloads list
            $stmt = $conn->prepare("
                INSERT INTO blocked_downloads (user_id, reason, date_blocked) 
                VALUES (?, 'Blocked by admin', NOW())
                ON DUPLICATE KEY UPDATE reason = VALUES(reason), date_blocked = NOW()
            ");

            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error blocking user downloads: " . $e->getMessage());
            return false;
        }
    }

    public function unblockUserDownloads($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("DELETE FROM blocked_downloads WHERE user_id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error unblocking user downloads: " . $e->getMessage());
            return false;
        }
    }

    public function isUserBlocked($userId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("SELECT id FROM blocked_downloads WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Error checking if user is blocked: " . $e->getMessage());
            return false;
        }
    }

    public function getDownloadById($downloadId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    d.*, u.first_name, u.last_name, u.email
                FROM downloads d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE d.id = ?
            ");
            $stmt->execute([$downloadId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting download by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getUserDownloads($userId, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT * FROM downloads 
                WHERE user_id = ?
                ORDER BY date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user downloads: " . $e->getMessage());
            return [];
        }
    }

    public function getDownloadsByFile($filePath, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    d.*, u.first_name, u.last_name, u.email
                FROM downloads d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE d.file_path = ?
                ORDER BY d.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$filePath, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting downloads by file: " . $e->getMessage());
            return [];
        }
    }

    public function getDownloadsByStatus($status, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    d.*, u.first_name, u.last_name, u.email
                FROM downloads d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE d.status = ?
                ORDER BY d.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$status, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting downloads by status: " . $e->getMessage());
            return [];
        }
    }

    public function searchDownloads($searchTerm, $startDate = null, $endDate = null, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT 
                    d.*, u.first_name, u.last_name, u.email
                FROM downloads d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE (d.file_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)
            ";
            $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(d.date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " ORDER BY d.date_created DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching downloads: " . $e->getMessage());
            return [];
        }
    }

    public function getDownloadReport($startDate = null, $endDate = null, $groupBy = 'day')
    {
        try {
            $conn = $this->db->getConnection();

            $dateFormat = 'Y-%m-%d';
            if ($groupBy === 'month') {
                $dateFormat = 'Y-%m';
            } elseif ($groupBy === 'year') {
                $dateFormat = 'Y';
            }

            $sql = "
                SELECT 
                    DATE_FORMAT(date_created, ?) as period,
                    COUNT(*) as downloads,
                    COUNT(DISTINCT user_id) as users,
                    SUM(file_size) as total_size
                FROM downloads 
                WHERE status = 'completed'
            ";
            $params = [$dateFormat];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " GROUP BY DATE_FORMAT(date_created, ?) ORDER BY period ASC";
            $params[] = $dateFormat;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting download report: " . $e->getMessage());
            return [];
        }
    }

    public function getFileTypeStats($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    CASE 
                        WHEN file_name LIKE '%.pdf' THEN 'PDF'
                        WHEN file_name LIKE '%.doc%' THEN 'Word'
                        WHEN file_name LIKE '%.xls%' THEN 'Excel'
                        WHEN file_name LIKE '%.ppt%' THEN 'PowerPoint'
                        WHEN file_name LIKE '%.jpg' OR file_name LIKE '%.jpeg' OR file_name LIKE '%.png' THEN 'Picha'
                        WHEN file_name LIKE '%.mp4' OR file_name LIKE '%.avi' OR file_name LIKE '%.mov' THEN 'Video'
                        WHEN file_name LIKE '%.mp3' OR file_name LIKE '%.wav' THEN 'Sauti'
                        WHEN file_name LIKE '%.zip' OR file_name LIKE '%.rar' THEN 'Zipped'
                        ELSE 'Nyingine'
                    END as file_type,
                    COUNT(*) as count,
                    SUM(file_size) as total_size
                FROM downloads 
                WHERE status = 'completed'
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " GROUP BY file_type ORDER BY count DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting file type stats: " . $e->getMessage());
            return [];
        }
    }
}
