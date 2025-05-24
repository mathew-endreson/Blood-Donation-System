<?php
require 'config.php';

function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    // Get total donors
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM donors");
    $stats['totalDonors'] = $stmt->fetchColumn();
    
    // Get total donations
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM donations");
    $stats['totalDonations'] = $stmt->fetchColumn();
    
    // Get total blood in inventory
    $stmt = $pdo->query("SELECT SUM(amount_ml) AS total FROM blood_inventory");
    $stats['totalBlood'] = $stmt->fetchColumn();
    
    // Get recent activity
    $stmt = $pdo->query("SELECT CONCAT('Last donation: ', d.first_name, ' ', d.last_name, ' on ', dn.donation_date) AS activity 
                         FROM donations dn 
                         JOIN donors d ON dn.donor_id = d.donor_id 
                         ORDER BY dn.donation_date DESC LIMIT 1");
    $stats['recentActivity'] = $stmt->fetchColumn();
    
    return $stats;
}

function getRecentDonations($limit = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT dn.*, CONCAT(d.first_name, ' ', d.last_name) AS donor_name, d.blood_type 
                          FROM donations dn 
                          JOIN donors d ON dn.donor_id = d.donor_id
                          ORDER BY dn.donation_date DESC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getBloodInventory() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM blood_inventory ORDER BY blood_type");
    return $stmt->fetchAll();
}

function getBloodRequests($status = null, $limit = null) {
    global $pdo;
    
    $sql = "SELECT r.*, u.username 
            FROM blood_requests r 
            JOIN users u ON r.user_id = u.user_id";
    
    if ($status) {
        $sql .= " WHERE r.status = ?";
    }
    
    $sql .= " ORDER BY r.request_date DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if ($status && $limit) {
        $stmt->execute([$status, $limit]);
    } elseif ($status) {
        $stmt->execute([$status]);
    } elseif ($limit) {
        $stmt->execute([$limit]);
    } else {
        $stmt->execute();
    }
    
    return $stmt->fetchAll();
}
?>
