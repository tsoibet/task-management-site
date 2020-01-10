<?php
$mysql_host = getenv("MYSQL_HOST");
$mysql_user = getenv("MYSQL_USER");
$mysql_pass = getenv("MYSQL_PASS");
$mysql_db = getenv("MYSQL_DB");
$mysql_port = getenv("MYSQL_PORT");
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"])) {
  $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  if ($_POST["action"] == "insert") {
    $stmt = $conn->prepare("INSERT INTO TMSITE.TASK (title, detail, `status`, `priority`, deadlineness, deadline)
                                VALUES (?, ?, ?, ?, TRUE, ?)");
    $stmt->bind_param("sssss", $task, $description, $status, $priority, $deadline);

    $task = $_POST["task"];
    $description = $_POST["description"];
    $status = $_POST["status"];
    $priority = $_POST["priority"];
    $deadline = $_POST["deadline"];
    if (!$stmt->execute()) {
      echo "Error: " . $stmt->error;
    }
    $stmt->close();
  } else if ($_POST["action"] == "delete") {
    $stmt = $conn->prepare("DELETE FROM TMSITE.TASK WHERE id=?");
    $stmt->bind_param("i", $id);

    $id = $_POST["id"];
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
  <title>Task Management Site</title>
</head>

<body>
  <div class="container">
    <h1>Task Management Site</h1>

    <table>
      <tr>
        <th>Task</th>
        <th>Description</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Deadline</th>
        <th></th>
      </tr>
      <tr>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <td><input type="text" name="task"></td>
          <td><input type="text" name="description"></td>
          <td><select name="status">
              <option value="To do">To do</option>
              <option value="In progress">In progress</option>
              <option value="Done">Done</option>
            </select></td>
          <td><select name="priority">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select></td>
          <td><input type="date" name="deadline"></td>
          <td><input type="submit" value="Add"></td>
          <input type="hidden" name="action" value="insert">
        </form>
      </tr>
    </table>

    <?php
    $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM TMSITE.TASK";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      echo "<table>
          <tr>
          <th>Task</th>
          <th>Description</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Deadline</th>
          <th> </th>
          <th> </th>
          </tr>";
      while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["title"] .
          "</td><td>" . $row["detail"] .
          "</td><td>" . $row["status"] .
          "</td><td>" . $row["priority"] .
          "</td><td>" . $row["deadline"] .
          "</td><td><button>Modify</button>" .
          "</td><td><form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                    <input type='submit' value='Delete'>
                    <input type='hidden' name='action' value='delete'> 
                    <input type='hidden' name='id' value='" . $row["id"] . "'> </form>" .
          "</td></tr>";
      }
      echo "</table>";
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