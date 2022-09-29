<?php 
include "connect.php";
if(isset($_GET['city']))
{
    $city = $_GET['city'];    
    if(is_numeric($city) && $city > 0)
    {
        $sql = "DELETE FROM cities WHERE cid = ?";
        $del_stmt = $conn->prepare($sql);
        $del_stmt->bind_param("i", $city);
        $del_stmt->execute();
        if($del_stmt->error) { 
            $message = "<p>Error: " . $del_stmt->error . "</p>";
        } 
        else { 
            $message = "<p>Deleted successfully.</p>";
        }   

        $del_stmt->close();

        // $sql = "DELETE FROM cities WHERE cid = $city";
        // if ($conn->query($sql)) {
        //     $message = "Deleted " . $conn -> affected_rows;
        //     $message .= " row successfully";        
        // } else {
        //     $message = "there was a problem: " . $conn->error;
        // }         
    }
}
else
{
    $city = '';    
}
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
    <h1>Delete a city in our system</h1>
    <main class="flex">
        <div class="list">
            <!-- same code as home page almost -->
            <?php 
            $sql = "SELECT cid, city_name, province from cities";
            $result = $conn->query($sql);
            if($conn->error) {
                echo $conn->error;
            }
            else{
                if ($result->num_rows > 0) {
                    echo "<table>";
                    while ($row = $result->fetch_assoc()) {
                        extract($row);
                        echo "<tr><td>$city_name</td><td>$province</td><td><a href=\"delete.php?city=$cid\">Delete</a></td><tr>\n";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Sorry there are no records available that match your query</p>";
                }
            }
            ?>
        </div>
        <?php if($city): ?>
        <div>
            <?php if(isset($message)): ?>
                <div class="message">
                    <?php echo $message; ?>
                </div>
            <?php endif ?>
             
        </div>
        <?php endif ?>
    </main>
    
</body>
</html>
