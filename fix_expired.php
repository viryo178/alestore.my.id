<?php
$host = 'localhost';
$user = 'alestore_user';
$pass = 'alestore@123456789';
$db = 'alestore_aledatabase';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$query = "SELECT id, variation, sold_at, expired_at FROM digital_accounts";
$result = $mysqli->query($query);
$updated = 0;

while ($row = $result->fetch_assoc()) {
    $variation = $row['variation'];
    $soldAt = $row['sold_at'];
    $currentExpiredAt = $row['expired_at'];
    
    if (empty($variation) || empty($soldAt) || $soldAt === '0000-00-00 00:00:00') {
        continue;
    }

    // Only backfill if currently empty or invalid
    if (!empty($currentExpiredAt) && $currentExpiredAt !== '0000-00-00') {
        // Option: we could skip, but let's recalculate if we want to ensure it matches
        // Actually it's safer to just overwrite if it matches the pattern
    }

    if (preg_match('/(\d+)\s*(bulan|month|hari|day|tahun|year)/i', $variation, $matches)) {
        $num = (int)$matches[1];
        $unit = strtolower($matches[2]);
        
        $date = new DateTime($soldAt);
        
        if (strpos($unit, 'bulan') === 0 || strpos($unit, 'month') === 0) {
            $date->modify("+$num months");
        } elseif (strpos($unit, 'hari') === 0 || strpos($unit, 'day') === 0) {
            $date->modify("+$num days");
        } elseif (strpos($unit, 'tahun') === 0 || strpos($unit, 'year') === 0) {
            $date->modify("+$num years");
        }
        
        $newExpiredAt = $date->format('Y-m-d');
        
        // If it's different or empty, update it
        if ($currentExpiredAt !== $newExpiredAt) {
            $updateStmt = $mysqli->prepare("UPDATE digital_accounts SET expired_at = ? WHERE id = ?");
            $updateStmt->bind_param('si', $newExpiredAt, $row['id']);
            $updateStmt->execute();
            $updated++;
        }
    }
}

echo "Updated $updated accounts.";
$mysqli->close();
