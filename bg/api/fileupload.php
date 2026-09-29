<?php
// 1. 允許來源 (開發環境可用 *，正式環境建議指定如 http://localhost:5173)
header("Access-Control-Allow-Origin: *");

// 2. 允許的請求方法
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// 3. 允許的標頭 (這點最重要！必須包含 Content-Type)
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// 處理預檢請求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2. 設定上傳目錄
$uploadDir = 'uploads/' . date('Y-m-d') . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 3. 接收文字欄位 (來自 Axios toFormData)
$title = $_POST['title'];
$tags = $_POST['tags']; // PHP 會自動解析為陣列

$response = [
    "status" => "success",
    "files" => [],
    "message" => ""
];

// 4. 處理多檔案上傳
if (isset($_FILES['images'])) {
    $fileData = $_FILES['images'];
    
    // PHP 的多檔案結構是：$fileData['name'][0], $fileData['name'][1]...
    // 我們跑迴圈依序處理
    foreach ($fileData['name'] as $index => $originalName) {
        
        // 檢查是否有上傳錯誤
        if ($fileData['error'][$index] !== UPLOAD_ERR_OK) {
            $response['files'][] = [
                "name" => $originalName,
                "status" => "error",
                "error_code" => $fileData['error'][$index]
            ];
            continue;
        }

        // 安全性：取得副檔名並檢查 (防止上傳 .php 檔)
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        
        if (!in_array($ext, $allowedExts)) {
            $response['files'][] = [
                "name" => $originalName,
                "status" => "error",
                "message" => "不支援的檔案格式"
            ];
            continue;
        }

        // 安全性：重新命名檔案，避免檔名衝突與中文亂碼
        $newFileName = uniqid() . '_' . $index . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;

        // 執行搬移
        if (move_uploaded_file($fileData['tmp_name'][$index], $targetPath)) {
            $response['files'][] = [
                "name" => $originalName,
                "save_path" => $targetPath,
                "status" => "success"
            ];
        } else {
            $response['files'][] = [
                "name" => $originalName,
                "status" => "error",
                "message" => "無法移動暫存檔"
            ];
        }
    }
} else {
    $response['message'] = "無檔案上傳";
}

// 5. 回傳結果給 Axios
echo json_encode($response);

?>