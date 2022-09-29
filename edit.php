<?php 
include "connect.php";
if(isset($_GET['city'])) {
    $city = $_GET['city'];    
    if(is_numeric($city) && $city > 0) {
        // $message = "";
        if (isset($_POST['submit'])) {
            $do_i_proceed = TRUE;
            $post_city = trim($_POST['city_name']);
            $post_province = trim($_POST['province']);
            $post_population = trim($_POST['population']);
            $message = "";

            // validation!!! same as insert stuff
            $post_population = filter_var($post_population, FILTER_SANITIZE_NUMBER_INT);
            if($post_population < 0 || !is_numeric($post_population) || $post_population == FALSE) {
                $do_i_proceed = FALSE;
                $message .= "<p>Population must be a positive number.</p>";
            }

            // is province real? - a select is a much better choice for the user but this check still is important after that change.
            $province_list = array('NL', 'PE','NS','NB','QC','ON','MB','SK','AB','BC','YT','NT','NU' );
            //  force upper case 
            $post_province = strtoupper($post_province);
            if (!in_array($post_province, $province_list)) {
                $do_i_proceed = FALSE;
                $message .= "<p>Not a valid province</p>";
            }

            $post_city = filter_var($post_city, FILTER_SANITIZE_STRING);                  
            if (strlen($post_city) < 2 || strlen($post_city) > 30) {
                $do_i_proceed = FALSE;
                $message .= "<p>Please enter a city name that is shorter than 30 characters.</p>";
            } else {
                if(str_contains($post_city, "'") or str_contains($post_city, '"')) {
                    $post_city = mysqli_real_escape_string($conn, $post_city);
                }        
            }

            if($do_i_proceed == TRUE) {         
                // $sql = "UPDATE cities SET population = $post_population, province = '$post_province', city_name = '$post_city' WHERE cid = $city";

                // if ($conn->query($sql)) {
                //     $message .= "<p>Updated " . $conn -> affected_rows;
                //     $message .= " row successfully</p>";

                // } else {
                //     $message .= "<p>there was a problem: " . $conn->error ."</p>";
                // }

                $sql = "UPDATE cities SET population = ?, province = ?, city_name = ? WHERE cid = ?";
                $update_stmt = $conn->prepare($sql);
                $update_stmt->bind_param("issi", $post_population, $post_province, $post_city, $city);
                $update_stmt->execute();
                if($update_stmt->error) { 
                    $message = "Error: " . $update_stmt->error;
                } else { 
                    $message = "Updated successfully";                    
                }
                $update_stmt->close();    

                
            }
        }

        // $get_sql = "SELECT * from cities WHERE cid = $city";
        // $result = mysqli_query($conn, $get_sql);
        // if(mysqli_error($conn)) {
        //     $message .= mysqli_error($conn);
        // } else {
        //     if (mysqli_num_rows($result) == 1) {                
        //         $row = mysqli_fetch_assoc($result);                
        //         $db_city_name = $row['city_name'];                
        //         $db_population = $row['population'];
        //         $db_province = $row['province'];
        //     } else {
        //         echo "Sorry there are no records available that match your query";
        //     }
        // }

        $get_sql = "SELECT * from cities WHERE cid = ?";    
        $stmt_get = $conn->prepare($get_sql);
        $stmt_get->bind_param("i", $city);
        $stmt_get->execute();

        if($stmt_get->error) { 
            $message = "Error: " . $stmt_get->error;
        } 
        else { 
            $get_result = $stmt_get->get_result();
            $get_row = mysqli_fetch_assoc($get_result);
            $db_city_name = $get_row['city_name'];                
            $db_population = $get_row['population'];
            $db_province = $get_row['province'];
        }
        $stmt_get->close();   
    }
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
    <h1>Edit the cities in our system</h1>
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
                        echo "<tr><td>$city_name</td><td>$province</td><td><a href=\"edit.php?city=$cid\">Edit</a></td><tr>\n";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Sorry there are no records available that match your query</p>";
                }
            }
            ?>
        </div>
        <?php if(isset($city) ): ?>
            <div>
                <?php if(isset($message)): ?>
                    <div class="message">
                        <?php echo $message; ?>
                    </div>
                <?php endif ?>
                <form action="" method="POST">        
                    <label for="city_name">Name</label>
                    <input type="text" name="city_name" id="city_name" value="<?php if(isset($post_city) && $post_city != "") echo $post_city; else echo $db_city_name;?>">
                    <label for="province">Province</label>
                    <input type="text" name="province" id="province" value="<?php if(isset($post_province) && $post_province != "") echo $post_province; else echo $db_province;?>">
                    <label for="population">Population</label>
                    <input type="text" name="population" id="population" value="<?php if(isset($post_population) && $post_population != "") echo $post_population; else echo $db_population; ?>">
                    <input type="submit" value="Save" name="submit">
                </form>
            </div>
        <?php endif ?>
    </main>
    
</body>
</html>
