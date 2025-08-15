<?php
require_once __DIR__ . "/../config/database.php";

class Blog
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllBlogsForAdmin($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            // Simple query matching the old system structure
            $stmt = $conn->prepare("
                SELECT 
                    id, name, maelezo, photo, date_created
                FROM blog 
                ORDER BY id DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all blogs for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalBlogs()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM blog");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total blogs: " . $e->getMessage());
            return 0;
        }
    }

    public function getOverallBlogStats()
    {
        try {
            $conn = $this->db->getConnection();

            // Get total blogs
            $stmt = $conn->prepare("SELECT COUNT(*) as total_blogs FROM blog");
            $stmt->execute();
            $blogsResult = $stmt->fetch();

            // For now, return only what we can get from the blog table
            // Other stats can be added when those tables exist
            return [
                'total_blogs' => $blogsResult['total_blogs'] ?? 0,
                'total_views' => 0, // Will implement when blog_views table exists
                'total_comments' => 0, // Will implement when blog_comments table exists
                'total_authors' => 0 // Will implement when author_id field exists
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall blog stats: " . $e->getMessage());
            return [
                'total_blogs' => 0,
                'total_views' => 0,
                'total_comments' => 0,
                'total_authors' => 0
            ];
        }
    }

    public function deleteBlog($blogId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if blog exists
            $stmt = $conn->prepare("SELECT id FROM blog WHERE id = ?");
            $stmt->execute([$blogId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete blog (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM blog WHERE id = ?");
            return $stmt->execute([$blogId]);
        } catch (PDOException $e) {
            error_log("Error deleting blog: " . $e->getMessage());
            return false;
        }
    }

    public function toggleBlogStatus($blogId)
    {
        try {
            $conn = $this->db->getConnection();

            // For now, we'll just return true as the status toggle logic
            // would need to be implemented based on your database structure
            // You might want to add a status field to the blog table

            return true;
        } catch (PDOException $e) {
            error_log("Error toggling blog status: " . $e->getMessage());
            return false;
        }
    }

    public function addBlog($authorId, $title, $excerpt, $content, $category, $status = 'draft')
    {
        try {
            $conn = $this->db->getConnection();

            // Check if author exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$authorId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Insert new blog
            $stmt = $conn->prepare("
                INSERT INTO blog (author_id, title, excerpt, content, category, status, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([$authorId, $title, $excerpt, $content, $category, $status]);
        } catch (PDOException $e) {
            error_log("Error adding blog: " . $e->getMessage());
            return false;
        }
    }

    public function updateBlog($blogId, $blogData)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if blog exists
            $stmt = $conn->prepare("SELECT id FROM blog WHERE id = ?");
            $stmt->execute([$blogId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Update blog with simple fields matching the old system
            $stmt = $conn->prepare("
                UPDATE blog 
                SET name = ?, maelezo = ?, photo = ?
                WHERE id = ?
            ");

            return $stmt->execute([
                $blogData['name'],
                $blogData['maelezo'],
                $blogData['photo'],
                $blogId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating blog: " . $e->getMessage());
            return false;
        }
    }

    public function getBlogById($blogId)
    {
        try {
            $conn = $this->db->getConnection();

            // Simple query matching the old system structure
            $stmt = $conn->prepare("
                SELECT 
                    id, name, maelezo, photo, date_created
                FROM blog 
                WHERE id = ?
            ");
            $stmt->execute([$blogId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting blog by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getPublishedBlogs($page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.title, b.excerpt, b.category, b.date_created,
                    u.first_name, u.last_name,
                    COUNT(DISTINCT v.id) as views,
                    COUNT(DISTINCT c.id) as comments
                FROM blog b
                LEFT JOIN users u ON b.author_id = u.id
                LEFT JOIN blog_views v ON b.id = v.blog_id
                LEFT JOIN blog_comments c ON b.id = c.blog_id
                WHERE b.status = 'published'
                GROUP BY b.id
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting published blogs: " . $e->getMessage());
            return [];
        }
    }

    public function getBlogsByCategory($category, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.title, b.excerpt, b.category, b.date_created,
                    u.first_name, u.last_name,
                    COUNT(DISTINCT v.id) as views,
                    COUNT(DISTINCT c.id) as comments
                FROM blog b
                LEFT JOIN users u ON b.author_id = u.id
                LEFT JOIN blog_views v ON b.id = v.blog_id
                LEFT JOIN blog_comments c ON b.id = c.blog_id
                WHERE b.status = 'published' AND b.category = ?
                GROUP BY b.id
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$category, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting blogs by category: " . $e->getMessage());
            return [];
        }
    }

    public function incrementViews($blogId)
    {
        try {
            $conn = $this->db->getConnection();

            // Insert or update view count
            $stmt = $conn->prepare("
                INSERT INTO blog_views (blog_id, ip_address, user_agent, date_created)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE date_created = NOW()
            ");

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            return $stmt->execute([$blogId, $ipAddress, $userAgent]);
        } catch (PDOException $e) {
            error_log("Error incrementing blog views: " . $e->getMessage());
            return false;
        }
    }

    public function addComment($blogId, $userId, $comment)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if blog exists and is published
            $stmt = $conn->prepare("SELECT id FROM blog WHERE id = ? AND status = 'published'");
            $stmt->execute([$blogId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Insert comment
            $stmt = $conn->prepare("
                INSERT INTO blog_comments (blog_id, user_id, comment, date_created) 
                VALUES (?, ?, ?, NOW())
            ");

            return $stmt->execute([$blogId, $userId, $comment]);
        } catch (PDOException $e) {
            error_log("Error adding blog comment: " . $e->getMessage());
            return false;
        }
    }

    public function getBlogComments($blogId, $page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    c.id, c.comment, c.date_created,
                    u.first_name, u.last_name, u.role
                FROM blog_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.blog_id = ?
                ORDER BY c.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$blogId, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting blog comments: " . $e->getMessage());
            return [];
        }
    }

    public function getLatestPosts($limit = 6)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name,
                    0 as views,
                    0 as comments
                FROM blog b
                ORDER BY b.date_created DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting latest blog posts: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentBlogs($limit = 5)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name
                FROM blog b
                ORDER BY b.date_created DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting recent blogs: " . $e->getMessage());
            return [];
        }
    }

    public function getPostById($postId)
    {
        try {
            $conn = $this->db->getConnection();



            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.maelezo as content, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name, 'unknown@example.com' as author_email,
                    0 as views,
                    0 as comments
                FROM blog b
                WHERE b.id = ?
            ");
            $stmt->execute([$postId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting blog post by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getRelatedPosts($postId, $limit = 3)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name,
                    0 as views
                FROM blog b
                WHERE b.id != ?
                ORDER BY b.date_created DESC 
                LIMIT ?
            ");
            $stmt->execute([$postId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting related blog posts: " . $e->getMessage());
            return [];
        }
    }

    public function getImageUrl($imageName)
    {
        // Check if it's a full URL or just filename
        if (filter_var($imageName, FILTER_VALIDATE_URL)) {
            return $imageName;
        }

        // Check if image exists in uploads directory
        $imagePath = __DIR__ . '/../uploads/Blog/' . $imageName;
        if (file_exists($imagePath)) {
            return 'uploads/Blog/' . $imageName;
        }

        // Fallback to a default image
        return 'images/blog/default-blog.jpg';
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

    public function getLatestBlogPosts($limit = 12)
    {
        try {
            $conn = $this->db->getConnection();

            // Use the actual database structure
            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name,
                    0 as views,
                    0 as comments
                FROM blog b
                ORDER BY b.date_created DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $results = $stmt->fetchAll();

            error_log("Found " . count($results) . " blog posts");
            return $results;
        } catch (PDOException $e) {
            error_log("Error getting latest blog posts: " . $e->getMessage());
            return [];
        }
    }

    public function searchBlogs($searchTerm, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name,
                    0 as views,
                    0 as comments
                FROM blog b
                WHERE (b.name LIKE ? OR b.maelezo LIKE ?)
                ORDER BY b.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $searchPattern = "%$searchTerm%";
            $stmt->execute([$searchPattern, $searchPattern, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching blogs: " . $e->getMessage());
            return [];
        }
    }

    public function getAllBlogsForDebug($limit = 20)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    b.id, b.name as title, b.maelezo as excerpt, b.photo, b.date_created,
                    'Unknown' as first_name, 'Author' as last_name
                FROM blog b
                ORDER BY b.date_created DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all blogs for debug: " . $e->getMessage());
            return [];
        }
    }
}
