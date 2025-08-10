<?php
require_once __DIR__ . '/../config/database.php';

class OngeaHub
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Submit a new report to Ongea Hub
     */
    public function submitReport($data)
    {
        try {
            $conn = $this->db->getConnection();
            
            $stmt = $conn->prepare("
                INSERT INTO ongea_hub (fname, sname, phone, region, tarehe_ya_tukio, msaada, report, report_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bindParam(1, $data['fname'], PDO::PARAM_STR);
            $stmt->bindParam(2, $data['sname'], PDO::PARAM_STR);
            $stmt->bindParam(3, $data['phone'], PDO::PARAM_STR);
            $stmt->bindParam(4, $data['region'], PDO::PARAM_STR);
            $stmt->bindParam(5, $data['tarehe_ya_tukio'], PDO::PARAM_STR);
            $stmt->bindParam(6, $data['msaada'], PDO::PARAM_STR);
            $stmt->bindParam(7, $data['report'], PDO::PARAM_STR);
            $stmt->bindParam(8, $data['report_date'], PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error submitting Ongea Hub report: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all reports (for admin purposes)
     */
    public function getAllReports($page = 1, $perPage = 20)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT id, fname, sname, phone, region, tarehe_ya_tukio, msaada, report, report_date 
                FROM ongea_hub 
                ORDER BY report_date DESC 
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bindParam(1, $perPage, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching Ongea Hub reports: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of reports
     */
    public function getTotalReportsCount()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM ongea_hub");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting Ongea Hub reports: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get report by ID
     */
    public function getReportById($id)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT id, fname, sname, phone, region, tarehe_ya_tukio, msaada, report, report_date 
                FROM ongea_hub 
                WHERE id = ?
            ");
            
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching Ongea Hub report: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get reports by region
     */
    public function getReportsByRegion($region)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT id, fname, sname, phone, region, tarehe_ya_tukio, msaada, report, report_date 
                FROM ongea_hub 
                WHERE region = ? 
                ORDER BY report_date DESC
            ");
            
            $stmt->bindParam(1, $region, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching Ongea Hub reports by region: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get reports by type of assistance needed
     */
    public function getReportsByAssistanceType($msaada)
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                SELECT id, fname, sname, phone, region, tarehe_ya_tukio, msaada, report, report_date 
                FROM ongea_hub 
                WHERE msaada = ? 
                ORDER BY report_date DESC
            ");
            
            $stmt->bindParam(1, $msaada, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching Ongea Hub reports by assistance type: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Format date for display
     */
    public function formatDate($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('d M Y');
        } catch (Exception $e) {
            return $dateString;
        }
    }

    /**
     * Validate phone number format
     */
    public function validatePhone($phone)
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Tanzanian phone number (10 digits starting with 0)
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return $phone;
        }
        
        // Check if it's a valid Tanzanian phone number (12 digits starting with 255)
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '255') {
            return '0' . substr($phone, 3);
        }
        
        return false;
    }

    /**
     * Validate report text length
     */
    public function validateReportText($text, $maxWords = 150)
    {
        $wordCount = str_word_count(strip_tags($text));
        return $wordCount <= $maxWords;
    }

    /**
     * Get assistance types
     */
    public function getAssistanceTypes()
    {
        return [
            'kisheria' => 'Kisheria',
            'kijamii' => 'Kijamii',
            'kisaikolojia' => 'Kisaikolojia'
        ];
    }

    /**
     * Get regions
     */
    public function getRegions()
    {
        return [
            'Arusha', 'Dar es Salaam', 'Dodoma', 'Geita', 'Iringa', 'Kagera', 'Katavi', 
            'Kigoma', 'Kilimanjaro', 'Lindi', 'Manyara', 'Mara', 'Mbeya', 'Mjini Magharibi', 
            'Morogoro', 'Mtwara', 'Mwanza', 'Njombe', 'Pemba Kaskazini', 'Pemba Kusini', 
            'Pwani', 'Rukwa', 'Ruvuma', 'Shinyanga', 'Simiyu', 'Singida', 'Songwe', 
            'Tabora', 'Tanga', 'Unguja Kaskazini', 'Unguja Kusini', 'Unguja Mjini Magharibi'
        ];
    }
}
