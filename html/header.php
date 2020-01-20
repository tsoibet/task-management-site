<?php
$mysql_host = getenv("MYSQL_HOST");
$mysql_user = getenv("MYSQL_USER");
$mysql_pass = getenv("MYSQL_PASS");
$mysql_db = getenv("MYSQL_DB");
$mysql_port = getenv("MYSQL_PORT");

$Err_msg_task = "";
$Err_msg_status = "";
$Err_msg_priority = "";
$Err_msg_deadline = "";

function input_safe($data)
{
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}
