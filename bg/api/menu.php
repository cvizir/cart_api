<?php
require 'include/config.inc.php';

// 1. 允許跨域請求 (CORS)，如果你的 Vue 專案跟 PHP 不同網域/連接埠，這必填
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 2. 設定回應內容為 JSON 格式
header('Content-Type: application/json; charset=utf-8');

// 2. 取得所有分類資料
// 確保層級由小到大排序，這樣在組裝樹狀結構時父節點會先被處理
$sql = "SELECT id, category_code, name, parent_id, level 
        FROM categories 
        ORDER BY level ASC, sort_order ASC, id ASC";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll();

// 3. 組裝樹狀結構 (Tree) 演算法
$tree = [];
$references = []; // 用於快速查找節點的索引

// 初始化每個節點，加上 children 陣列，並建立索引
foreach ($categories as $cat) {
    $cat['subcat'] = [];
    $references[$cat['id']] = $cat;
}

// 利用 PHP 的傳址特性 (&) 將子節點掛載到父節點的 children 陣列中
foreach ($references as $id => &$cat) {
    if ($cat['parent_id'] === null) {
        // 如果沒有 parent_id，代表是第一層，直接加入樹的根節點
        $tree[] = &$cat;
    } else {
        // 如果有 parent_id，將自己加入父節點的 children 陣列中
        if (isset($references[$cat['parent_id']])) {
            $references[$cat['parent_id']]['subcat'][] = &$cat;
        }
    }
}

// 4. 輸出 JSON
// JSON_UNESCAPED_UNICODE: 防止中文變成 \uXXXX 編碼
// JSON_PRETTY_PRINT: 讓輸出的 JSON 具有縮排格式，方便除錯
echo json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
