<?php
include_once 'cfg.php';

function showPage($title)
{
  global $link;
  $page_title = htmlspecialchars($title);
  $query = "SELECT * FROM page_list WHERE page_title = '$page_title' LIMIT 1";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_array($result);

  if (empty($row['id'])) {
    $content = "Nie znaleziono strony";
    $title = "Nie znaleziono strony";
  } else {
    $content = $row["page_content"];
    $title = $row["page_title"];
  }

  return array($title, $content);
}

function showPageList($link) {
  $query = "SELECT id, page_title FROM page_list WHERE status = 1 ORDER BY id ASC LIMIT 10";
  $result = mysqli_query($link, $query);
  return $result;
}
