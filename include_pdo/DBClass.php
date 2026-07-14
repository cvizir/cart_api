<?php
// DomainObjectList類別  
class DBClass
{
  public $page = 1;
  public $pageSize = 20;
  public $menuSize = 10;
  public $total = 0;
  public $pageTotal = 0;
  public $db_table;

  // 新增：用來存放 PDO 連線物件的屬性
  protected $pdo;

  // 1. 現代化建構子 (PHP 7/8 標準)，並要求傳入 PDO 連線
  public function __construct(PDO $pdo, $db_table, $page = 1, $pageSize = 20)
  {
    $this->pdo = $pdo;
    $this->db_table = $db_table;
    $this->page = $page;
    $this->pageSize = $pageSize;
  }

  public function getAdminList($where = "", $order = "", $trace = 0)
  {
    // 加上反引號保護資料表名稱，避免保留字報錯
    $sqlTotal = "SELECT COUNT(*) AS total FROM `" . $this->db_table . "` " . $where;
    $stmt2 = $this->pdo->prepare($sqlTotal);
    $stmt2->execute();
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    $this->total = $row["total"] ? $row["total"] : 0;
    echo '$this->total=' . $this->total;


    $sql = "SELECT * FROM `" . $this->db_table . "` " . $where . " " . $order;
    if ($this->total > 0) {
      $start = ($this->page - 1) * $this->pageSize;
      if ($start < $this->total) {
        $start = max(0, $start);
      } else {
        $calcPageTotal = ceil($this->total / $this->pageSize);
        $start = max(0, ($calcPageTotal - 1) * $this->pageSize);
      }
      $sql = $sql . " LIMIT " . $start . ", " . $this->pageSize;
    }
    if ($trace == "1") {
      echo "&nbsp;&nbsp;&nbsp;getAdminList_SQL=" . $sql . "<br>";
    }
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    // fetchAll() 用來抓取所有符合條件的結果，回傳一個二維陣列
    return $stmt->fetchAll();
  }

  public function query($sql, $sqlTotal, $pageFlag = true)
  {
    // 2. 使用 PDO 執行總數查詢
    $stmtTotal = $this->pdo->query($sqlTotal);
    if ($stmtTotal) {
      $row = $stmtTotal->fetch(PDO::FETCH_ASSOC);
      $this->total = $row["total"] ? $row["total"] : 0;
    } else {
      $this->total = 0;
    }

    if ($this->total > 0) {
      if ($pageFlag) {
        $start = ($this->page - 1) * $this->pageSize;
        if ($start < $this->total) {
          $start = max(0, $start);
        } else {
          // 修復原本程式碼中不存在的 getPageTotal() bug
          $calcPageTotal = ceil($this->total / $this->pageSize);
          $start = max(0, ($calcPageTotal - 1) * $this->pageSize);
        }
        $sql = $sql . " LIMIT " . $start . ", " . $this->pageSize;
      }

      // 3. 回傳 PDOStatement 物件 (外部可以直接用 foreach 或 fetch 讀取)
      return $this->pdo->query($sql);
    } else {
      return [];
    }
  }

  // ==========================================
  // 以下為 HTML 分頁選單產生器，邏輯無須修改，僅調整排版
  // ==========================================

  public function showPageMenu($argument = "")
  {
    if (empty($this->pageSize))
      return;

    $pageTotal = ceil($this->total / $this->pageSize);

    if ($this->page % $this->menuSize == 0) {
      $pageStart = $this->page - ($this->menuSize) + 1;
    } else {
      $pageStart = $this->page - ($this->page % $this->menuSize) + 1;
    }

    $pageEnd = $pageStart + $this->menuSize - 1;
    $pageEnd = ($pageEnd > $pageTotal) ? $pageTotal : $pageEnd;

    $htm = "";
    if ($this->page > 1) {
      $htm .= "<a class=\"linka\" href=\"?page=" . ($this->page - 1) . $argument . "\">上一頁</a> ";
    } else {
      $htm .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    for ($i = $pageStart; $i <= $pageEnd; $i++) {
      if ($i == $this->page) {
        $htm .= " <strong>" . $i . "</strong>";
      } else {
        $htm .= " <a class=\"linka\" href=\"?page=" . $i . $argument . "\">" . $i . "</a>";
      }
    }

    if ($this->page < $pageTotal) {
      $htm .= " <a class=\"linka\" href=\"?page=" . ($this->page + 1) . $argument . "\">下一頁</a>";
    } else {
      if ($pageTotal != "1") {
        $htm .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      }
    }
    return $htm;
  }

  public function showPageDMenu($argument = "", $list_mode = 'all')
  {
    if (empty($this->pageSize))
      return;

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
      if ($pageTotal != "1") {
        $htm .= "<li style=\"float:left;width:60px;\">&nbsp;</li>";
      }
    }
    $htm .= "</ul></div>";
    return $htm;
  }

  public function showPageDMenu2($argument = "", $list_mode = 'all')
  {
    if (empty($this->pageSize))
      return;

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

    $htm = '<div style="width:' . $div_width . 'px;margin: 0 auto;"><ul>';
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
      if ($pageTotal != "1") {
        $htm .= "<li style=\"float:left;width:60px;\">&nbsp;</li>";
      }
    }
    $htm .= "</ul></div>";

    return $htm;
  }
}
?>