<?php
// 1. 允許跨域請求 (CORS)，如果你的 Vue 專案跟 PHP 不同網域/連接埠，這必填
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 2. 設定回應內容為 JSON 格式
header('Content-Type: application/json; charset=utf-8');

$name = $_GET['name'];

// 模擬從資料庫抓取的資料
$data = [
    [
        "id" => 0,
        "name" => "$name",
        "category" => "GET"
    ],
    [
        "id" => 1,
        "name" => "Vue 3 入門教學",
        "category" => "Frontend"
    ],
    [
        "id" => 2,
        "name" => "PHP & Axios 整合",
        "category" => "Backend"
    ],
    [
        "id" => 3,
        "name" => "Vite 部署指南",
        "category" => "DevOps"
    ]
];

// 3. 將陣列轉為 JSON 字串並輸出
echo json_encode($data);
?>