<?php 
include "connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="add.php">Add</a>
        <a href="edit.php">Edit</a>
        <a href="delete.php">Delete</a>
    </nav>
    <h1>Cities in our System</h1>    
    <?php 
    $sql = "SELECT city_name, population, province from cities";
    $result = $conn->query($sql);
    if($conn->error) {
        echo $conn->error;
    }
    else{
        if ($result->num_rows > 0) {
            echo "<table>";
            while ($row = $result->fetch_assoc()) {
                extract($row);
                echo "<tr><td>$city_name</td><td>$province</td><td>$population</td><tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Sorry there are no records available that match your query</p>";
        }
    }
    ?>
</body>
</html>