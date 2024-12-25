
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $enteredusername = $_POST['username'];
  $enteredpassword = $_POST['password'];

  $enteredusername = $conn->real_escape_string($enteredusername);
  $enteredpassword = $conn->real_escape_string($enteredpassword);

  $sql = "SELECT id, username, Role FROM MyAccounts WHERE username = '$enteredusername' AND Password = '$enteredpassword'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['Role'] = $row['Role'];

        echo "Role: ". $row['Role']; 

        if ($row['Role'] == 'admin') {
            header("Location: main.php");
            exit;
        } else {
            header("Location: members.php");
            exit;
        }
    } else {
        echo "Invalid username or password.";
    }
}

$conn->close();
?>

<form method="post" action="delete.php">
    <input type="hidden" name="event_id" value="<?php echo $row["id"]; ?>">
    <input type="submit" name="delete_event" value="Delete">
</form>