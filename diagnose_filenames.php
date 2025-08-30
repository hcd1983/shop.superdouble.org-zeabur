<?php
/**
 * 診斷檔名問題
 * 查看 PHP 實際看到的檔名格式
 */

header('Content-Type: text/html; charset=UTF-8');
set_time_limit(300);

$upload_dir = __DIR__ . '/wp-content/uploads/';
$year = $_GET['year'] ?? '2022';
$month = $_GET['month'] ?? '08';
$search_dir = $upload_dir . $year . '/' . $month . '/';

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>檔名診斷工具</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .controls {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .controls input {
            padding: 8px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .controls button {
            padding: 8px 20px;
            background: #0073aa;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .controls button:hover {
            background: #005a87;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #f0f0f0;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            position: sticky;
            top: 0;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
            word-break: break-all;
            font-family: monospace;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .filename {
            color: #d73502;
            font-weight: bold;
        }
        .hex {
            color: #666;
            font-size: 11px;
        }
        .decoded {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            flex: 1;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
        }
        .special-char {
            background: #ffeb3b;
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔬 檔名診斷工具</h1>
        
        <div class="info-box">
            <strong>說明：</strong>此工具顯示 PHP 實際看到的檔名，包括原始字節和各種編碼格式。
        </div>
        
        <div class="controls">
            <form method="GET">
                <label>年份：<input type="text" name="year" value="<?php echo htmlspecialchars($year); ?>" size="4"></label>
                <label>月份：<input type="text" name="month" value="<?php echo htmlspecialchars($month); ?>" size="2"></label>
                <button type="submit">掃描目錄</button>
            </form>
            <div style="margin-top: 10px; color: #666;">
                掃描目錄：<?php echo htmlspecialchars($search_dir); ?>
            </div>
        </div>
        
        <?php
        if (is_dir($search_dir)) {
            $files = scandir($search_dir);
            $problem_files = [];
            $normal_files = [];
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $full_path = $search_dir . $file;
                if (is_dir($full_path)) continue;
                
                // 分析檔名
                $analysis = [
                    'name' => $file,
                    'length' => strlen($file),
                    'mb_length' => mb_strlen($file),
                    'hex' => bin2hex($file),
                    'has_quotes' => strpos($file, "'") !== false || strpos($file, '"') !== false,
                    'has_dollar' => strpos($file, '$') !== false,
                    'has_backslash' => strpos($file, '\\') !== false,
                    'has_octal' => preg_match('/\\\\[0-7]{3}/', $file),
                    'has_ansi_c' => preg_match('/\$\'/', $file),
                    'is_utf8' => mb_check_encoding($file, 'UTF-8'),
                    'decoded' => ''
                ];
                
                // 嘗試各種解碼
                if ($analysis['has_octal']) {
                    // 嘗試解碼八進位
                    $decoded = preg_replace_callback(
                        '/\\\\([0-7]{3})/',
                        function($matches) {
                            return chr(octdec($matches[1]));
                        },
                        $file
                    );
                    if (mb_check_encoding($decoded, 'UTF-8')) {
                        $analysis['decoded'] = $decoded;
                    }
                }
                
                // 檢查是否為 ANSI-C quoting
                if (preg_match('/^\$\'(.*)\'$/', $file, $matches)) {
                    $inner = $matches[1];
                    $decoded = preg_replace_callback(
                        '/\\\\([0-7]{3})/',
                        function($matches) {
                            return chr(octdec($matches[1]));
                        },
                        $inner
                    );
                    if (mb_check_encoding($decoded, 'UTF-8')) {
                        $analysis['decoded'] = $decoded;
                        $analysis['has_ansi_c'] = true;
                    }
                }
                
                // 檢查是否包含中文或特殊字符
                if (preg_match('/[\x80-\xFF]/', $file) || 
                    $analysis['has_quotes'] || 
                    $analysis['has_dollar'] || 
                    $analysis['has_backslash'] ||
                    $analysis['has_octal']) {
                    $problem_files[] = $analysis;
                } else {
                    $normal_files[] = $analysis;
                }
            }
            
            // 統計
            $total = count($problem_files) + count($normal_files);
            ?>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total; ?></div>
                    <div>總檔案數</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($problem_files); ?></div>
                    <div>問題檔案</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($normal_files); ?></div>
                    <div>正常檔案</div>
                </div>
            </div>
            
            <?php if (!empty($problem_files)): ?>
            <h2>🚨 問題檔案（<?php echo count($problem_files); ?> 個）</h2>
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">PHP 看到的檔名</th>
                        <th width="15%">特徵</th>
                        <th width="30%">Hex (前50字節)</th>
                        <th width="25%">可能的原始檔名</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($problem_files as $i => $file): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td class="filename">
                            <?php 
                            $display = htmlspecialchars($file['name']);
                            // 高亮特殊字符
                            $display = str_replace('$', '<span class="special-char">$</span>', $display);
                            $display = str_replace("'", '<span class="special-char">\'</span>', $display);
                            $display = str_replace('\\', '<span class="special-char">\\</span>', $display);
                            echo $display;
                            ?>
                            <div class="hex">長度: <?php echo $file['length']; ?> bytes</div>
                        </td>
                        <td>
                            <?php
                            $features = [];
                            if ($file['has_quotes']) $features[] = "引號";
                            if ($file['has_dollar']) $features[] = "$符號";
                            if ($file['has_backslash']) $features[] = "反斜線";
                            if ($file['has_octal']) $features[] = "八進位";
                            if ($file['has_ansi_c']) $features[] = "ANSI-C";
                            if (!$file['is_utf8']) $features[] = "非UTF-8";
                            echo implode(', ', $features);
                            ?>
                        </td>
                        <td class="hex">
                            <?php echo substr($file['hex'], 0, 100); ?>...
                        </td>
                        <td>
                            <?php if ($file['decoded']): ?>
                                <span class="decoded"><?php echo htmlspecialchars($file['decoded']); ?></span>
                            <?php else: ?>
                                <span class="error">無法解碼</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
            <?php if (!empty($normal_files)): ?>
            <h2>✅ 正常檔案（<?php echo count($normal_files); ?> 個）</h2>
            <details>
                <summary>點擊展開查看</summary>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="70%">檔名</th>
                            <th width="25%">編碼</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($normal_files as $i => $file): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($file['name']); ?></td>
                            <td><?php echo $file['is_utf8'] ? 'UTF-8' : '未知'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
            <?php endif; ?>
            
            <?php
        } else {
            echo '<div class="error">目錄不存在：' . htmlspecialchars($search_dir) . '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>💡 診斷說明</h3>
            <ul>
                <li><strong>$'...'</strong>：ANSI-C Quoting（Shell 特殊格式）</li>
                <li><strong>\xxx</strong>：八進位編碼</li>
                <li><strong>''</strong>：檔名包含引號</li>
                <li>如果 PHP 看到的檔名包含 <code>$</code> 或 <code>'</code>，表示這些字符是檔名的一部分</li>
            </ul>
        </div>
    </div>
</body>
</html>