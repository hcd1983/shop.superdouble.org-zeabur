<?php
/**
 * 中文檔名編碼處理函數庫
 * 處理 ZIP 檔案解壓時的中文編碼問題
 */

class ChineseFilenameHandler {
    
    /**
     * 修正中文檔名編碼
     * @param string $filename 原始檔名
     * @return string 修正後的檔名
     */
    public static function fixFilename($filename) {
        // 如果已經是 UTF-8，直接返回
        if (mb_check_encoding($filename, 'UTF-8')) {
            return $filename;
        }
        
        // 嘗試不同的編碼轉換
        $encodings = [
            'GBK',      // Windows 簡體中文
            'BIG5',     // Windows 繁體中文
            'GB2312',   // 簡體中文
            'GB18030',  // 中文編碼標準
            'CP950',    // Windows 繁體中文代碼頁
            'CP936',    // Windows 簡體中文代碼頁
            'ISO-8859-1' // 有時候會被誤判為此編碼
        ];
        
        foreach ($encodings as $encoding) {
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $filename);
            if ($converted && mb_check_encoding($converted, 'UTF-8')) {
                // 檢查轉換後是否包含有意義的中文字符
                if (preg_match('/[\x{4e00}-\x{9fff}]/u', $converted)) {
                    return $converted;
                }
            }
        }
        
        // 如果所有編碼都失敗，嘗試使用 mb_convert_encoding
        $converted = @mb_convert_encoding($filename, 'UTF-8', 'auto');
        if ($converted && mb_check_encoding($converted, 'UTF-8')) {
            return $converted;
        }
        
        // 最後的備用方案：生成安全的檔名
        return self::generateSafeFilename($filename);
    }
    
    /**
     * 生成安全的檔名（當無法轉換編碼時使用）
     * @param string $filename 原始檔名
     * @return string 安全的檔名
     */
    public static function generateSafeFilename($filename) {
        // 保留副檔名
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // 使用時間戳和隨機數生成唯一檔名
        $safeName = 'file_' . date('YmdHis') . '_' . substr(md5($name), 0, 8);
        
        if ($ext) {
            $safeName .= '.' . $ext;
        }
        
        return $safeName;
    }
    
    /**
     * 處理 ZIP 檔案解壓，自動修正中文檔名
     * @param string $zipFile ZIP 檔案路徑
     * @param string $extractTo 解壓目標目錄
     * @return array 解壓結果
     */
    public static function extractZipWithChineseFix($zipFile, $extractTo) {
        $result = [
            'success' => false,
            'files' => [],
            'errors' => []
        ];
        
        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== TRUE) {
            $result['errors'][] = '無法開啟 ZIP 檔案';
            return $result;
        }
        
        // 確保目標目錄存在
        if (!file_exists($extractTo)) {
            mkdir($extractTo, 0755, true);
        }
        
        // 逐個處理檔案
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $fileinfo = pathinfo($filename);
            
            // 修正檔名編碼
            $fixedFilename = self::fixFilename($filename);
            
            // 處理目錄結構
            $fixedPath = $extractTo . '/' . $fixedFilename;
            $fixedDir = dirname($fixedPath);
            
            // 建立目錄（確保權限）
            if (!file_exists($fixedDir)) {
                @mkdir($fixedDir, 0777, true);
                @chmod($fixedDir, 0777);
            }
            
            // 如果是目錄，跳過
            if (substr($filename, -1) == '/') {
                continue;
            }
            
            // 解壓單個檔案
            $fileContent = $zip->getFromIndex($i);
            if ($fileContent === false) {
                $result['errors'][] = "無法讀取檔案: $filename";
                continue;
            }
            
            // 寫入檔案（加入錯誤抑制）
            if (@file_put_contents($fixedPath, $fileContent) !== false) {
                $result['files'][] = [
                    'original' => $filename,
                    'fixed' => $fixedFilename,
                    'path' => $fixedPath
                ];
            } else {
                $result['errors'][] = "無法寫入檔案: $fixedFilename";
            }
        }
        
        $zip->close();
        $result['success'] = true;
        return $result;
    }
    
    /**
     * 掃描目錄並修正中文檔名
     * @param string $directory 要掃描的目錄
     * @param bool $dryRun 是否只測試不實際重命名
     * @return array 處理結果
     */
    public static function fixDirectoryFilenames($directory, $dryRun = true) {
        $result = [
            'fixed' => [],
            'errors' => [],
            'skipped' => []
        ];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            $oldPath = $file->getPathname();
            $oldName = $file->getFilename();
            
            // 檢查是否需要修正
            if (!mb_check_encoding($oldName, 'UTF-8') || 
                preg_match('/[\x80-\xFF]/', $oldName)) {
                
                $fixedName = self::fixFilename($oldName);
                $newPath = $file->getPath() . DIRECTORY_SEPARATOR . $fixedName;
                
                if ($oldPath !== $newPath) {
                    if (!$dryRun) {
                        // 實際重命名
                        if (@rename($oldPath, $newPath)) {
                            $result['fixed'][] = [
                                'old' => $oldPath,
                                'new' => $newPath
                            ];
                        } else {
                            $result['errors'][] = "無法重命名: $oldPath";
                        }
                    } else {
                        // 只記錄將要修改的檔案
                        $result['fixed'][] = [
                            'old' => $oldPath,
                            'new' => $newPath,
                            'dry_run' => true
                        ];
                    }
                }
            } else {
                $result['skipped'][] = $oldPath;
            }
        }
        
        return $result;
    }
}