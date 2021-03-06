<?php include_once 'header.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"])) {
  $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  if ($_POST["action"] == "insert") {
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
      $stmt = $conn->prepare("INSERT INTO TMSITE.TASK (title, detail, `status`, `priority`, deadlineness, deadline)
      VALUES (?, ?, ?, ?, TRUE, ?)");
      $stmt->bind_param("sssss", $task, $description, $status, $priority, $deadline);
      if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
      }
      $stmt->close();
      $task = $description = $status = $priority = $deadline = "";
    }
  } else if ($_POST["action"] == "delete") {
    $stmt = $conn->prepare("DELETE FROM TMSITE.TASK WHERE id = ?");
    $stmt->bind_param("i", $id);
    $id = input_safe($_POST["id"]);
    if (!$stmt->execute()) {
      echo "Error: " . $stmt->error;
    }
    $stmt->close();
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
      <div class="col">
        <p><a class="btn btn-dark" data-toggle="collapse" href="#addtaskform" role="button" onclick="myFunction(this)">Hide form</a></p>
        <!--Javascript button-->
        <script>
          function myFunction(x) {
            var y = document.getElementById("addtaskform");
            if (x.innerHTML === "Hide form" && y.classList.contains("show")) {
              x.innerHTML = "Show form";
            }
            if (x.innerHTML === "Show form" && y.classList.value == "collapse") {
              x.innerHTML = "Hide form";
            }
          }
        </script>
        <div class="collapse show" id="addtaskform">
          <form class="alert alert-secondary shadow-sm rounded" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="row">
              <div class="form-group col-sm-2">
                <label class="col-form-label pl-1">Task<span class="error badge badge-danger ml-1"><?php echo $Err_msg_task; ?></span></label>
                <input type="text" class="form-control form-control-sm" name="task" value="<?php echo $task; ?>">
              </div>
              <div class="form-group col-sm-3">
                <label class="col-form-label pl-1">Description</label>
                <input type="text" class="form-control form-control-sm" name="description" value="<?php echo $description; ?>">
              </div>
              <div class="form-group col-sm-2">
                <label class="col-form-label pl-1">Status<span class="error badge badge-danger ml-1"><?php echo $Err_msg_status; ?></span></label>
                <select name="status" class="form-control form-control-sm">
                  <option value="To do" <?php if ($status == "To do") echo "selected"; ?>>To do</option>
                  <option value="In progress" <?php if ($status == "In progress") echo "selected"; ?>>In progress</option>
                  <option value="Done" <?php if ($status == "Done") echo "selected"; ?>>Done</option>
                </select>
              </div>
              <div class="form-group col-sm-2">
                <div><label class="col-form-label pl-1">Priority<span class="error badge badge-danger ml-1"><?php echo $Err_msg_priority; ?></span></label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" class="form-check-input position-static" name="priority" value="1" checked>1
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" class="form-check-input position-static" name="priority" value="2" <?php if ($priority == "2") echo "checked"; ?>>2
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" class="form-check-input position-static" name="priority" value="3" <?php if ($priority == "3") echo "checked"; ?>>3
                </div>
              </div>
              <div class="form-group col-sm-2">
                <label class="col-form-label pl-1">Deadline<span class="error badge badge-danger ml-1"><?php echo $Err_msg_deadline; ?></span></label>
                <input type="date" class="form-control form-control-sm" name="deadline" value="<?php if (isset($deadline)) {
                                                                                                  echo $deadline;
                                                                                                } else {
                                                                                                  echo date("Y-m-d");
                                                                                                } ?>">
              </div>
              <div class="form-group col-sm-1 pt-4">
                <input type="submit" class="btn btn-dark btn-sm" value="Add">
              </div>
              <input type="hidden" name="action" value="insert">
            </div>
          </form>
        </div>
      </div>
    </div>


    <?php
    //sortby
    $sort_by_status_mark = $sort_by_priority_mark = $sort_by_deadline_mark = "";
    if (isset($_GET['sortby'])) {
      $sortby = $_GET['sortby'];
      if ($sortby == "Priority") {
        $subsort1 = "Status";
        $subsort2 = "Deadline";
        $sort_by_priority_mark = "<span class='material-icons'>arrow_drop_down</span>";
      }
      if ($sortby == "Deadline") {
        $subsort1 = "Status";
        $subsort2 = "Priority";
        $sort_by_deadline_mark = "<span class='material-icons'>arrow_drop_down</span>";
      }
      if ($sortby == "Status") {
        $subsort1 = "Priority";
        $subsort2 = "Deadline";
        $sort_by_status_mark = "<span class='material-icons'>arrow_drop_down</span>";
      }
    } else {
      $sortby = "Status";
      $subsort1 = "Priority";
      $subsort2 = "Deadline";
      $sort_by_status_mark = "<span class='material-icons'>arrow_drop_down</span>";
    }

    //pagination
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    $records_per_page = 6;
    $offset = ($page - 1) * $records_per_page;

    $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    //sortby & pagination
    $total_results_sql = "SELECT COUNT(*) FROM  TMSITE.TASK";
    $res = $conn->query($total_results_sql);
    $total_results = $res->fetch_array()[0];
    $total_pages = ceil($total_results / $records_per_page);

    $sql = "SELECT * FROM TMSITE.TASK ORDER BY $sortby, $subsort1, $subsort2 LIMIT $offset, $records_per_page";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      //sortby
      echo "<div class='dropdown' align='right'>Sort by 
         <a class='btn btn-light dropdown-toggle mb-2 mx-1' href='#' role='button' id='dropdownMenuLink' data-toggle='dropdown'>
         Select
         </a>
          <div class='dropdown-menu'>
          <a class='dropdown-item";
      if ($sortby == 'Status') {
        echo " active";
      }
      echo "' href='?sortby=Status'>Status</a>
          <a class='dropdown-item";
      if ($sortby == 'Priority') {
        echo " active";
      }
      echo "' href='?sortby=Priority'>Priority</a>
          <a class='dropdown-item";
      if ($sortby == 'Deadline') {
        echo " active";
      }
      echo "' href='?sortby=Deadline'>Deadline</a>
          </div>
        </div>

          <div class='table-responsive'>
          <table class='table table-hover table-sm'>
          <thead class='thead-dark'>
          <tr>
          <th>Task</th>
          <th>Description</th>
          <th>Status" . $sort_by_status_mark . "</th>
          <th>Priority" . $sort_by_priority_mark . "</th>
          <th>Deadline" . $sort_by_deadline_mark . "</th>
          <th> </th>
          <th> </th>
          </tr>
          </thead>";
      while ($row = $result->fetch_assoc()) {
        echo "<tr";
        if ($row["status"] == "Done") {
          echo " class='table-secondary'";
        } else if (substr($row["deadline"], 0, -9) < date("Y-m-d")) {
          echo " class='table-danger'";
        }
        echo "><td>";
        if ($row['status'] == 'Done') {
          echo "<s>";
        }
        echo $row["title"];
        if ($row['status'] == 'Done') {
          echo "</s>";
        }
        if ($row['status'] != 'Done' && substr($row["deadline"], 0, -9) < date("Y-m-d")) {
          echo "<span class='badge badge-danger ml-2'>due</span>";
        }
        echo "</td><td>" . $row["detail"] .
          "</td><td>" . $row["status"] .
          "</td><td>" . $row["priority"] .
          "</td><td>" . substr($row["deadline"], 0, -9) .
          "</td><td><form method='get' action='modify.php'>
                    <button type='submit' class='btn btn-primary btn-sm'><i class='material-icons' style='font-size: 20px'>edit</i></button>
                    <input type='hidden' name='action' value='modify'> 
                    <input type='hidden' name='id' value='" . $row["id"] . "'> </form>" .
          "</td><td><form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                    <button type='submit' class='btn btn-danger btn-sm'><i class='material-icons' style='font-size: 20px'>delete</i></button>
                    <input type='hidden' name='action' value='delete'> 
                    <input type='hidden' name='id' value='" . $row["id"] . "'> </form>" .
          "</td></tr>";
      }
      echo "</table></div>";
      echo "<nav aria-label='Task result pages'>
        <ul class='pagination justify-content-center'>
        <li class='page-item'><a class='page-link' href='?sortby=" . $sortby . "&page=1'>First</a></li>
        <li class='page-item";
      if ($page <= 1) {
        echo " disabled";
      }
      echo "'><a class='page-link' href='";
      if ($page <= 1) {
        echo "#' tableindex='-1";
      } else {
        echo "?sortby=" . $sortby . "&page=" . ($page - 1);
      }
      echo "'>Previous</a></li>
        <li class='page-item";
      if ($page >= $total_pages) {
        echo " disabled";
      }
      echo "'><a class='page-link' href='";
      if ($page >= $total_pages) {
        echo "#' tableindex='-1";
      } else {
        echo "?sortby=" . $sortby . "&page=" . ($page + 1);
      }
      echo "'>Next</a></li>
        <li class='page-item'><a class='page-link' href='?sortby=" . $sortby . "&page=" . $total_pages . "'>Last</a></li>
        </ul></nav>";
    } else {
      echo "No tasks at the moment.";
    }
    $conn->close();
    ?>


  </div>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>