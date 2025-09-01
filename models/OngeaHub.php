<?php

class OngeaHub
{
    private $pdo;

    public function __construct($pdo = null)
    {
        if ($pdo === null) {
            global $pdo;
        }
        $this->pdo = $pdo;
    }

    /**
     * Add a new report to the ongea_hub table
     * This follows the exact logic from the old code
     */
    public function addReport($fname, $sname, $phone, $region, $tarehe_ya_tukio, $msaada, $report, $report_date)
    {
        try {
            $sql = "INSERT INTO ongea_hub (fname, sname, phone, region, tarehe_ya_tukio, msaada, report, report_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                $fname,
                $sname,
                $phone,
                $region,
                $tarehe_ya_tukio,
                $msaada,
                $report,
                $report_date
            ]);

            return $result;
        } catch (Exception $e) {
            error_log('Error adding report to OngeaHub: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of reports (for admin dashboard)
     */
    public function getTotalCount()
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM ongea_hub");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log('Error getting total count from OngeaHub: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all reports (for admin purposes)
     */
    public function getAllReports()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM ongea_hub ORDER BY report_date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting all reports from OngeaHub: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get reports by date range (for admin purposes)
     */
    public function getReportsByDateRange($startDate, $endDate)
    {
        try {
            $sql = "SELECT * FROM ongea_hub WHERE DATE(report_date) BETWEEN ? AND ? ORDER BY report_date DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$startDate, $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting reports by date range from OngeaHub: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get reports by region (for admin purposes)
     */
    public function getReportsByRegion($region)
    {
        try {
            $sql = "SELECT * FROM ongea_hub WHERE region = ? ORDER BY report_date DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$region]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting reports by region from OngeaHub: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get reports by assistance type (for admin purposes)
     */
    public function getReportsByAssistanceType($msaada)
    {
        try {
            $sql = "SELECT * FROM ongea_hub WHERE msaada = ? ORDER BY report_date DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$msaada]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting reports by assistance type from OngeaHub: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent reports (for admin dashboard)
     */
    public function getRecentReports($limit = 10)
    {
        try {
            $sql = "SELECT * FROM ongea_hub ORDER BY report_date DESC LIMIT ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting recent reports from OngeaHub: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get statistics for admin dashboard
     */
    public function getStatistics()
    {
        try {
            $stats = [];
            
            // Total reports
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM ongea_hub");
            $stats['total'] = $stmt->fetchColumn();
            
            // Reports by region
            $stmt = $this->pdo->query("SELECT region, COUNT(*) as count FROM ongea_hub GROUP BY region ORDER BY count DESC");
            $stats['byRegion'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Reports by assistance type
            $stmt = $this->pdo->query("SELECT msaada, COUNT(*) as count FROM ongea_hub GROUP BY msaada ORDER BY count DESC");
            $stats['byAssistanceType'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Reports by month (last 12 months)
            $stmt = $this->pdo->query("SELECT DATE_FORMAT(report_date, '%Y-%m') as month, COUNT(*) as count FROM ongea_hub WHERE report_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month ORDER BY month DESC");
            $stats['byMonth'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (Exception $e) {
            error_log('Error getting statistics from OngeaHub: ' . $e->getMessage());
            return [
                'total' => 0,
                'byRegion' => [],
                'byAssistanceType' => [],
                'byMonth' => []
            ];
        }
    }
}
