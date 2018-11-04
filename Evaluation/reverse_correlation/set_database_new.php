<?php
	$servername = "csmysql.cs.cf.ac.uk";
	$username = "c1868219";
	$password = "gau";
	$dbname = "c1868219";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Create TrainImages Table
	$create_tbl = "CREATE TABLE TrainImages(
		image_id int AUTO_INCREMENT PRIMARY KEY,
		image_name VARCHAR(20)
	);";
	mysqli_query($conn, $create_tbl);

	if(!mysqli_error($conn)){
		echo "<br>" . "Created TrainImages Table Successfully";
	}

	// Create TestImages Table
	$create_tbl = "CREATE TABLE TestImages(
		image_id int AUTO_INCREMENT PRIMARY KEY,
		image_name VARCHAR(20)
	);";
	mysqli_query($conn, $create_tbl);


	if(!mysqli_error($conn)){
		echo "<br>" . "Created TestImages Table Successfully";
	}

	// Create Evaluation Table
	### response_time is the time in YYYY-MM-DD HH:MM:SS format at which response was made
	### resp_time is the time taken in milliseconds to chose the correct image
	$create_tbl = "CREATE TABLE Evaluation(
		visitor_no int,
		response_time DATETIME,
		ds_image_id int,
		orig_image_id int,
		resp_time int,
		truth_val int,
		PRIMARY KEY(visitor_no, ds_image_id),
		FOREIGN KEY Evaluation(ds_image_id) REFERENCES TestImages(image_id)
	);";
	mysqli_query($conn, $create_tbl);

	if(!mysqli_error($conn)){
		echo "<br>" . "Created Evaluation Table Successfully";
	}

	mysqli_close($conn);
php?>