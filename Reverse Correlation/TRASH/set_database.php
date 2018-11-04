<?php
	$servername = "csmysql.cs.cf.ac.uk";
	$username = "c1868219";
	$password = "gau";
	$dbname = "c1868219";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Create Evaluation Table
	$create_tbl = "CREATE TABLE Evaluation(
		image_id int,
		visitor_no int,
		response_time DATETIME,
		normal_resp_time int,
		rc_resp_time int,
		truth_val int,
		PRIMARY KEY(image_id, visitor_no, response_time),
		FOREIGN KEY Evaluation(image_id) REFERENCES Images(image_id)
	);";
	mysqli_query($conn, $create_tbl);

	if(!mysqli_error($conn)){
		echo "<br>" . "Created Evaluation Table Successfully";
	}

	mysqli_close($conn);
php?>