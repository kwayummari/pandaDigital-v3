<?php
require_once 'config/init.php';
require_once 'models/Expert.php';

try {
    $expert = new Expert();
    $experts = $expert->getAllExperts();
    $stats = $expert->getExpertStats();

    echo "Total experts: " . count($experts) . "\n";
    echo "Stats: " . print_r($stats, true) . "\n";

    if (!empty($experts)) {
        echo "First expert: " . $experts[0]['first_name'] . " " . $experts[0]['last_name'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
