<?php 
include "connect.php";

if(isset($_POST['submit']))
{
    $message = "";
    extract($_POST);
    
    // makes our 3 variables for us based on name
    $do_i_proceed = TRUE;

    // is population positive?
    $population = filter_var($population, FILTER_SANITIZE_NUMBER_INT);
    if($population < 0 || !is_numeric($population) || $population == FALSE)
    {
        $do_i_proceed = FALSE;
        $message = "<p>Population must be a positive number.</p>";
    }

    // is province real? - a select is a much better choice for the user but this check still is important after that change.
    $province_list = array('NL', 'PE','NS','NB','QC','ON','MB','SK','AB','BC','YT','NT','NU' );
    //  force upper case 
    $province = strtoupper($province);
    if (!in_array($province, $province_list)) {
        $do_i_proceed = FALSE;
        $message .= "<p>Not a valid province</p>";
    }

    $city_name = filter_var($city_name, FILTER_SANITIZE_STRING);
    if (strlen($city_name) < 2 || strlen($city_name) > 30) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter a city name that is shorter than 30 characters.</p>";
    } else {
        if(str_contains($city_name, "'") or str_contains($city_name, '"')) {
            $city_name = mysqli_real_escape_string($conn, $city_name);
        }        
    }

    if($do_i_proceed == TRUE)
    {
        // Method 1 - We have sanitized and validated - don't forget quotes around strings. 
        $sql = "INSERT INTO cities (city_name, province, population) VALUES ('$city_name', '$province', $population)";
        if ($conn->query($sql)) {
            $message .= "inserted recorded successfully";
            $message .= "id of record is " . $conn->insert_id;
        $city_name = $province = $population = "";
        } else {
            $message .= "there was a problem: " . $conn->error;
        }

        // Method 2 - We don't need to do real_esacape_string to do this method or worry about which is a string for the quotes - could use $sql inside prepare if like
        $stmt_insert = $conn->prepare("INSERT INTO cities (city_name, province, population) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("ssi", $city_name, $province, $population);

        $stmt_insert->execute();

        if($stmt_insert->error) { 
            $message = "Error: " . $stmt_insert->error;
        } 
        else { 
            $message = "Inserted";
            $city_name = $province = $population = "";             
        }
        $stmt_insert->close();    
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
    <?php if(isset($message)): ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php endif ?>
    <form action="" method="POST">
        
        <label for="city_name">Name</label>
		<input type="text" name="city_name" id="city_name" value="<?php if(isset($_POST['city_name'])) echo $_POST['city_name']; ?>">
		<label for="province">Province</label>
		<input type="text" name="province" id="province" value="<?php if(isset($_POST['province'])) echo $_POST['province']; ?>">
		<label for="population">Population</label>
		<input type="text" name="population" id="population" value="<?php if(isset($_POST['population'])) echo $_POST['population']; ?>">
        <input type="submit" value="Save" name="submit">
    </form>
</body>
</html>

