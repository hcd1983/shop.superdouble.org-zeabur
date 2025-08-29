<?php
/**
 * 分塊上傳工具 - 繞過 Nginx 上傳限制
 * 支援大檔案分塊上傳
 */

session_start();

// 設定
$upload_dir = __DIR__ . '/wp-content/uploads/';
$temp_dir = __DIR__ . '/wp-content/uploads/temp/';
$chunk_size = 1024 * 1024; // 1MB 分塊

// 確保目錄存在
if (!file_exists($temp_dir)) {
    mkdir($temp_dir, 0755, true);
}

// 處理分塊上傳
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'upload_chunk') {
        // 上傳分塊
        $filename = $_POST['filename'];
        $chunk_index = intval($_POST['chunk_index']);
        $total_chunks = intval($_POST['total_chunks']);
        
        $target_file = $temp_dir . $filename . '.part' . $chunk_index;
        
        if (move_uploaded_file($_FILES['chunk']['tmp_name'], $target_file)) {
            // 檢查是否所有分塊都已上傳
            $all_chunks_uploaded = true;
            for ($i = 0; $i < $total_chunks; $i++) {
                if (!file_exists($temp_dir . $filename . '.part' . $i)) {
                    $all_chunks_uploaded = false;
                    break;
                }
            }
            
            if ($all_chunks_uploaded) {
                // 合併分塊
                $final_file = $temp_dir . $filename;
                $out = fopen($final_file, 'wb');
                
                for ($i = 0; $i < $total_chunks; $i++) {
                    $chunk_file = $temp_dir . $filename . '.part' . $i;
                    $in = fopen($chunk_file, 'rb');
                    stream_copy_to_stream($in, $out);
                    fclose($in);
                    unlink($chunk_file); // 刪除分塊
                }
                
                fclose($out);
                
                // 解壓 ZIP 檔案
                if (pathinfo($filename, PATHINFO_EXTENSION) === 'zip') {
                    $zip = new ZipArchive();
                    if ($zip->open($final_file) === TRUE) {
                        $zip->extractTo($upload_dir);
                        $zip->close();
                        unlink($final_file); // 刪除 ZIP 檔案
                        echo json_encode(['status' => 'completed', 'message' => '檔案已成功解壓']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => '無法解壓檔案']);
                    }
                } else {
                    echo json_encode(['status' => 'completed', 'message' => '檔案已上傳']);
                }
            } else {
                echo json_encode(['status' => 'chunk_uploaded', 'chunk' => $chunk_index]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => '分塊上傳失敗']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>分塊上傳工具</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #f0f0f0;
            border-radius: 5px;
            overflow: hidden;
            margin: 20px 0;
            display: none;
        }
        .progress-fill {
            height: 100%;
            background: #4CAF50;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .status {
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        button {
            background: #007cba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #005a87;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>WordPress Uploads 分塊上傳工具</h1>
    
    <div class="upload-area">
        <p>選擇 ZIP 檔案上傳（支援大檔案）</p>
        <input type="file" id="fileInput" accept=".zip">
        <br><br>
        <button id="uploadBtn" onclick="startUpload()">開始上傳</button>
    </div>
    
    <div class="progress-bar" id="progressBar">
        <div class="progress-fill" id="progressFill">0%</div>
    </div>
    
    <div id="status"></div>
    
    <script>
        const CHUNK_SIZE = 1024 * 1024; // 1MB 分塊
        
        async function startUpload() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            
            if (!file) {
                showStatus('請選擇檔案', 'error');
                return;
            }
            
            if (!file.name.endsWith('.zip')) {
                showStatus('請選擇 ZIP 檔案', 'error');
                return;
            }
            
            document.getElementById('uploadBtn').disabled = true;
            document.getElementById('progressBar').style.display = 'block';
            
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            
            for (let i = 0; i < totalChunks; i++) {
                const start = i * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);
                
                const formData = new FormData();
                formData.append('action', 'upload_chunk');
                formData.append('filename', file.name);
                formData.append('chunk_index', i);
                formData.append('total_chunks', totalChunks);
                formData.append('chunk', chunk);
                
                try {
                    const response = await fetch('chunked_upload.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.status === 'error') {
                        throw new Error(result.message);
                    }
                    
                    // 更新進度條
                    const progress = Math.round(((i + 1) / totalChunks) * 100);
                    updateProgress(progress);
                    
                    if (result.status === 'completed') {
                        showStatus('檔案已成功上傳並解壓！', 'success');
                        document.getElementById('uploadBtn').disabled = false;
                        return;
                    }
                } catch (error) {
                    showStatus('上傳失敗: ' + error.message, 'error');
                    document.getElementById('uploadBtn').disabled = false;
                    return;
                }
            }
        }
        
        function updateProgress(percent) {
            const progressFill = document.getElementById('progressFill');
            progressFill.style.width = percent + '%';
            progressFill.textContent = percent + '%';
        }
        
        function showStatus(message, type) {
            const statusDiv = document.getElementById('status');
            statusDiv.className = 'status ' + type;
            statusDiv.textContent = message;
        }
    </script>
</body>
</html>