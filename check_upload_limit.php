<?php
/**
 * 檢查 PHP 上傳限制
 */
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>PHP 上傳限制檢查</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .warning { color: #ff6600; font-weight: bold; }
        .good { color: #00aa00; font-weight: bold; }
    </style>
</head>
<body>
    <h1>PHP 上傳設定檢查</h1>
    
    <table>
        <tr>
            <th>設定項目</th>
            <th>當前值</th>
            <th>建議值</th>
            <th>狀態</th>
        </tr>
        <?php
        $settings = [
            'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'recommended' => '500M'],
            'post_max_size' => ['current' => ini_get('post_max_size'), 'recommended' => '500M'],
            'max_execution_time' => ['current' => ini_get('max_execution_time'), 'recommended' => '600'],
            'max_input_time' => ['current' => ini_get('max_input_time'), 'recommended' => '600'],
            'memory_limit' => ['current' => ini_get('memory_limit'), 'recommended' => '512M']
        ];
        
        foreach ($settings as $key => $value):
            $current = $value['current'];
            $recommended = $value['recommended'];
            
            // 轉換為數字比較
            $current_bytes = return_bytes($current);
            $recommended_bytes = return_bytes($recommended);
            
            $status = ($current_bytes >= $recommended_bytes) ? 
                '<span class="good">✓ 足夠</span>' : 
                '<span class="warning">⚠ 可能需要調整</span>';
        ?>
        <tr>
            <td><?php echo $key; ?></td>
            <td><?php echo $current; ?></td>
            <td><?php echo $recommended; ?></td>
            <td><?php echo $status; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>大檔案清單（超過 100MB）</h2>
    <ul>
        <li>uploads_2018_08.zip - 262M</li>
        <li>uploads_2018_10.zip - 346M</li>
        <li>uploads_2019_03.zip - 104M</li>
        <li>uploads_2020_01.zip - 351M</li>
        <li>uploads_2020_08.zip - 198M</li>
        <li>uploads_2020_10.zip - 156M</li>
        <li>uploads_2020_12.zip - 167M</li>
        <li>uploads_2021_06.zip - 778M ⚠️ 特別大</li>
        <li>uploads_2022_07.zip - 408M</li>
        <li>uploads_2024_08.zip - 121M</li>
    </ul>
    
    <h2>建議上傳順序</h2>
    <ol>
        <li><strong>第一批：小檔案測試</strong> - 先上傳幾個 166B 的檔案測試</li>
        <li><strong>第二批：中等檔案</strong> - 上傳 1-50MB 的檔案</li>
        <li><strong>第三批：大檔案</strong> - 依序上傳 100-200MB 的檔案</li>
        <li><strong>第四批：超大檔案</strong> - 最後處理 300MB+ 的檔案</li>
    </ol>
    
    <?php
    function return_bytes($val) {
        $val = trim($val);
        if (empty($val)) return 0;
        
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        
        return $val;
    }
    ?>
</body>
</html>