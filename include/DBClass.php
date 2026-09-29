<?php
class DBClass
{
  public $page = 1;        // 目前頁碼
  public $pageSize = 20;   // 每頁顯示筆數
  public $menuSize = 10;   // 分頁條顯示的頁碼數量
  public $total = 0;       // 總資料筆數
  public $pageTotal = 0;   // 計算後的總頁數
  public $db_table;        // 查詢的資料表名稱
  protected $pdo;          // PDO 資料庫實例

  /**
   * 建構函式 (PHP 5.4 標準寫法)
   */
  public function __construct($db_table, $page = 1, $pageSize = 20)
  {
    global $pdo;
    // 注入全域 PDO 連線物件
    $this->pdo = $pdo ? $pdo : startDB();
    $this->db_table = $db_table;
    $this->page = max(1, (int)$page);
    $this->pageSize = max(1, (int)$pageSize);
  }

  /**
   * 取得分頁資料與總數
   * 
   * @param string $where 篩選條件 SQL 字串 (需搭配佔位符 ?)
   * @param string $order 排序條件 SQL 字串
   * @param int $trace 除錯開關
   * @param array $params Prepared Statement 綁定的參數陣列
   * @return array 包含 total 與 data 的陣列
   */
  public function getAdminList($where = "", $order = "", $trace = 0, $params = [])
  {
    // 查詢符合條件的總筆數
    $sqlTotal = "SELECT COUNT(*) AS total FROM `" . $this->db_table . "` " . $where;
    
    // 計算 LIMIT 的起始位移 (Offset)
    $start = max(0, ($this->page - 1) * $this->pageSize);
    // 組合分頁 SQL (LIMIT 數值已轉為 int，確保安全)
    $sql = "SELECT * FROM `" . $this->db_table . "` " . $where . " " . $order . " LIMIT " . (int)$start . ", " . (int)$this->pageSize;

    if ($trace == 1) {
      echo "&nbsp;&nbsp;&nbsp;getAdminList_SQL=" . $sql . "<br>";
    }

    try {
      // 1. 取得資料總筆數
      $stmtTotal = $this->pdo->prepare($sqlTotal);
      $stmtTotal->execute($params);
      $this->total = (int)$stmtTotal->fetchColumn();
      $this->pageTotal = ceil($this->total / $this->pageSize);

      // 2. 取得分頁清單
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($params);
      $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return [
        'total' => $this->total,
        'data'  => $list
      ];
    } catch (PDOException $e) {
      die("執行錯誤: " . $e->getMessage());
    }
  }

  /**
   * 輸出標準分頁導航列 HTML
   */
  public function showPageMenu($argument = "")
  {
    if (empty($this->pageSize)) {
      return "";
    }

    $pageTotal = ceil($this->total / $this->pageSize);

    // 計算分頁選單區間起始頁
    if ($this->page % $this->menuSize == 0) {
      $pageStart = $this->page - ($this->menuSize) + 1;
    } else {
      $pageStart = $this->page - ($this->page % $this->menuSize) + 1;
    }

    // 計算分頁選單區間結束頁
    $pageEnd = $pageStart + $this->menuSize - 1;
    $pageEnd = ($pageEnd > $pageTotal) ? $pageTotal : $pageEnd;

    $htm = "";
    
    // 上一頁按鈕
    if ($this->page > 1) {
      $htm .= "<a class=\"linka\" href=\"?page=" . ($this->page - 1) . $argument . "\">上一頁</a> ";
    } else {
      $htm .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }
    
    // 中間頁碼按鈕
    for ($i = $pageStart; $i <= $pageEnd; $i++) {
      if ($i == $this->page) {
        $htm .= " <strong>" . $i . "</strong>";
      } else {
        $htm .= " <a class=\"linka\" href=\"?page=" . $i . $argument . "\">" . $i . "</a>";
      }
    }
    
    // 下一頁按鈕
    if ($this->page < $pageTotal) {
      $htm .= " <a class=\"linka\" href=\"?page=" . ($this->page + 1) . $argument . "\">下一頁</a>";
    } else {
      if ($pageTotal != 1) {
        $htm .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      }
    }
    return $htm;
  }

  /**
   * 輸出列表模式分頁清單 HTML (樣式一)
   */
  public function showPageDMenu($argument = "", $list_mode = 'all')
  {
    if (empty($this->pageSize)) {
      return "";
    }

    $pageTotal = ceil($this->total / $this->pageSize);
    if ($this->page % $this->menuSize == 0) {
      $pageStart = $this->page - ($this->menuSize) + 1;
    } else {
      $pageStart = $this->page - ($this->page % $this->menuSize) + 1;
    }
    $pageEnd = $pageStart + $this->menuSize - 1;
    $pageEnd = ($pageEnd > $pageTotal) ? $pageTotal : $pageEnd;

    $htm = '<div class="menu_div"><ul>';
    if ($this->page > 1) {
      $htm .= "<li style=\"float:left;width:60px;\"><a class=\"linka\" href=\"?page=" . ($this->page - 1) . $argument . "\">上一頁</a></li>";
    } else {
      $htm .= "<li style=\"float:left;width:60px;\">&nbsp;</li>";
    }
    if ($list_mode == "all") {
      for ($i = $pageStart; $i <= $pageEnd; $i++) {
        if ($i == $this->page) {
          $htm .= "<li style=\"float:left;width:30px;\"><strong>" . $i . "</strong></li>";
        } else {
          $htm .= "<li style=\"float:left;width:30px;\"><a class=\"linka\" href=\"?page=" . $i . $argument . "\">" . $i . "</a></li>";
        }
      }
    }
    if ($this->page < $pageTotal) {
      $htm .= "<li style=\"float:left;width:60px;\"><a class=\"linka\" href=\"?page=" . ($this->page + 1) . $argument . "\">下一頁</a></li>";
    } else {
      if ($pageTotal != 1) {
        $htm .= "<li style=\"float:left;width:60px;\">&nbsp;</li>";
      }
    }
    $htm .= "</ul></div>";
    return $htm;
  }

  /**
   * 輸出圖形化按鈕分頁清單 HTML (樣式二)
   */
  public function showPageDMenu2($argument = "", $list_mode = 'all')
  {
    if (empty($this->pageSize)) {
      return "";
    }

    $pageTotal = ceil($this->total / $this->pageSize);
    if ($this->page % $this->menuSize == 0) {
      $pageStart = $this->page - ($this->menuSize) + 1;
    } else {
      $pageStart = $this->page - ($this->page % $this->menuSize) + 1;
    }
    $pageEnd = $pageStart + $this->menuSize - 1;
    $pageEnd = ($pageEnd > $pageTotal) ? $pageTotal : $pageEnd;

    if ($list_mode == "all") {
      $div_width = ($pageEnd - $pageStart + 1) * 30 + 120;
    } else {
      $div_width = 400 + 120;
    }
    
    $htm = '<div style=" width:' . $div_width . 'px;margin: 0 auto;"><ul>';
    if ($this->page > 1) {
      $htm .= "<li style=\"float:left;width:60px;\"><a class=\"linka\" href=\"?page=" . ($this->page - 1) . $argument . "\"><img src=\"btn/bt_prev.jpg\" width=\"51\" height=\"34\" /></a></li>";
    } else {
      $htm .= "<li style=\"float:left;width:60px;\">&nbsp;</li>";
    }
    if ($list_mode == "all") {
      for ($i = $pageStart; $i <= $pageEnd; $i++) {
        if ($i == $this->page) {
          $htm .= "<li style=\"float:left;width:30px;\"><strong>" . $i . "</strong></li>";
        } else {
          $htm .= "<li style=\"float:left;width:30px;\"><a class=\"linka\" href=\"?page=" . $i . $argument . "\">" . $i . "</a></li>";
        }
      }
    } else {
      $htm .= "<li style=\"float:left;width:400px;\">&nbsp;</li>";
    }
    if ($this->page < $pageTotal) {
      $htm .= "<li style=\"float:left;width:60px;\"><a class=\"linka\" href=\"?page=" . ($this->page + 1) . $argument . "\"><img src=\"btn/bt_next.jpg\" width=\"51\" height=\"34\" /></a></li>";
    } else {
      if ($pageTotal != 1) {
        $htm .= "<li style=\"float:left;width:60px;\">&nbsp;</li>";
      }
    }
    $htm .= "</ul></div>";
    return $htm;
  }
}