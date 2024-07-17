<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mario_todo_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions (create, update, delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'create') {
        $task = $_POST['task'];
        if (!empty($task)) {
            $stmt = $conn->prepare("INSERT INTO tasks (task) VALUES (?)");
            $stmt->bind_param("s", $task);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $task = $_POST['task'];
        if (!empty($task)) {
            $stmt = $conn->prepare("UPDATE tasks SET task = ? WHERE id = ?");
            $stmt->bind_param("si", $task, $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch tasks from the database
$result = $conn->query("SELECT * FROM tasks");
$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mario To-do App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/mario_logo.png" alt="Mario Logo">
        </div>
        <ul>
            <li><a href="index.html#overview">Overview</a></li>
            <li><a href="index.html#features">Features</a></li>
            <li><a href="home.html">Home</a></li>
        </ul>
    </nav>

    <center>
        <h1>Mario To-do App</h1>
        <p>Level up your task management!</p>
    </center>

    <main class="container">
        <form action="index.php" method="POST">
            <input type="text" name="task" placeholder="New task...">
            <input type="hidden" name="action" value="create">
            <button type="submit">Add Task</button>
        </form>

        <ul>
            <?php foreach ($tasks as $task): ?>
                <li>
                    <form action="index.php" method="POST" style="display:inline;">
                        <input type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                        <button type="submit">Update</button>
                    </form>
                    <form action="index.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                    <a href="display_task.php?id=<?php echo $task['id']; ?>">Details</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
