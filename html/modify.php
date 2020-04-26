<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"])) {
  if ($_POST["action"] == "update") {
    $id = input_safe($_POST["id"]);
    $task = input_safe($_POST["task"]);
    $description = input_safe($_POST["description"]);
    $status = input_safe($_POST["status"]);
    $priority = input_safe($_POST["priority"]);
    $deadline = input_safe($_POST["deadline"]);
    if ($task == "") {
      $Err_msg_task = " required ";
    }
    if ($status == "") {
      $Err_msg_status = " required ";
    }
    if ($priority == "") {
      $Err_msg_priority = " required ";
    }
    if ($deadline == "") {
      $Err_msg_deadline = " required ";
    }
    if (strlen($task) > 50) {
      $Err_msg_task = " too long ";
    }
    if ($Err_msg_task == "" && $Err_msg_status == "" && $Err_msg_priority == "" && $Err_msg_deadline == "") {
      $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      $stmt = $conn->prepare("UPDATE TMSITE.TASK SET title=?, detail=?, `status`=?, `priority`=?, deadlineness=TRUE, deadline=?
                                  WHERE id= ?");
      $stmt->bind_param("sssssi", $task, $description, $status, $priority, $deadline, $id);
      if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
      }
      $stmt->close();
      $conn->close();
      header('Location: index.php');
      exit;
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET["action"])) {
  $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  if ($_GET["action"] == "modify") {
    $stmt = $conn->prepare("SELECT * FROM TMSITE.TASK WHERE id=?");
    $stmt->bind_param("i", $id);
    $id = input_safe($_GET["id"]);
    if (!$stmt->execute()) {
      echo "Error: " . $stmt->error;
    }
    $stmt->bind_result($id, $task, $description, $status, $priority, $deadlineness, $deadline, $createdat);
    $stmt->fetch();
    $stmt->close();
    $deadline = substr($deadline, 0, -9);
  }
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <!-- Google icon -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <title>Task Management Site</title>
</head>

<body>
  <div class="container-fluid">
    <div class="pt-2">
      <h1><b>Task Management Site</b></h1>
    </div>
    <div class="row py-1 px-3">
      <div class="col alert alert-primary shadow-sm rounded" style="max-width:720px">
        <form class="mb-n1" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

          <div class="form-group">
            <label class="col-form-label pl-1">Task<span class="error badge badge-danger ml-1"><?php echo $Err_msg_task; ?></span></label>
            <input type="text" class="form-control form-control-sm" name="task" value="<?php echo $task; ?>"></td>
          </div>
          <div class="form-group">
            <label class="col-form-label pl-1">Description</label>
            <input type="text" class="form-control form-control-sm" name="description" value="<?php echo $description; ?>"></td>
          </div>
          <div class="form-group">
            <label class="col-form-label pl-1">Status<span class="error badge badge-danger ml-1"><?php echo $Err_msg_status; ?></span></label>
            <select class="form-control form-control-sm" name="status">
              <option value="To do" <?php if ($status == "To do") echo "selected"; ?>>To do</option>
              <option value="In progress" <?php if ($status == "In progress") echo "selected"; ?>>In progress</option>
              <option value="Done" <?php if ($status == "Done") echo "selected"; ?>>Done</option>
            </select></div>
          <div class="form-group">
            <div><label class="col-form-label pl-1">Priority<span class="error badge badge-danger ml-1"><?php echo $Err_msg_priority; ?></span></label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" class="form-check-input position-static" name="priority" value="1" <?php if ($priority == "1") echo "checked"; ?>>1
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" class="form-check-input position-static" name="priority" value="2" <?php if ($priority == "2") echo "checked"; ?>>2
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" class="form-check-input position-static" name="priority" value="3" <?php if ($priority == "3") echo "checked"; ?>>3
            </div>
          </div>
          <div class="form-group">
            <label class="col-form-label pl-1">Deadline<span class="error badge badge-danger ml-1"><?php echo $Err_msg_deadline; ?></span></label>
            <input type="date" class="form-control form-control-sm" name="deadline" value="<?php echo $deadline ?>">
          </div>
          <div class="form-group pt-2">
            <input type="submit" class="btn btn-primary btn-sm" value="Send">
          </div>
          <input type="hidden" name="action" value="update">

          <input type="hidden" name="id" value="<?php echo $id; ?>">
        </form>
      </div>
    </div>
  </div>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>