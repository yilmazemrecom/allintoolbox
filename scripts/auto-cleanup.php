<?php
/**
 * Simple Auto Cleanup - Uploads klasörünü otomatik temizle
 * Her araç çağrıldığında çalışır, 24 saatten eski dosyaları siler
 */

function autoCleanupUploads() {
    $uploadDir = dirname(__DIR__) . '/uploads/';
    
    // Upload dizini yoksa çık
    if (!is_dir($uploadDir)) {
        return;
    }
    
    $cutoffTime = time() - (24 * 3600); // 24 saat önce
    $files = glob($uploadDir . '*');
    
    if (!$files) {
        return;
    }
    
    $deleted = 0;
    $kept = 0;
    
    // Dosyaları yaşa göre sırala (en yeni önce)
    $fileList = [];
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileList[] = [
                'path' => $file,
                'time' => filemtime($file)
            ];
        }
    }
    
    // En yeni dosyalar önce
    usort($fileList, function($a, $b) {
        return $b['time'] - $a['time'];
    });
    
    // Son 5 dosyayı koru, geri kalanları yaş kontrolü yap
    foreach ($fileList as $index => $fileInfo) {
        if ($index < 5) {
            // Son 5 dosyayı her zaman koru
            $kept++;
            continue;
        }
        
        // 24 saatten eskiyse sil
        if ($fileInfo['time'] < $cutoffTime) {
            if (unlink($fileInfo['path'])) {
                $deleted++;
            }
        } else {
            $kept++;
        }
    }
    
    // Log sadece bir şeyler silinmişse
    if ($deleted > 0) {
        $logFile = dirname(__DIR__) . '/logs/cleanup.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = date('Y-m-d H:i:s') . " - Deleted: $deleted files, Kept: $kept files\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Sadece %10 ihtimalle çalışsın (her araç çağrısında değil)
if (rand(1, 10) === 1) {
    autoCleanupUploads();
}