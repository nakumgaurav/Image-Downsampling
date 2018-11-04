<!DOCTYPE html>
<html>
<head>
<title>Reverse Correlation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

<style type="text/css">
	.central{
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
* {
  box-sizing: border-box;
}

body {
  background-color: #E6E6FA;
}

#ResponseForm {
  background-color: #ffffff;
  margin: 100px auto;
  font-family: Raleway;
  padding: 40px;
  width: 70%;
  min-width: 300px;
}

table
{ 
	margin-left: auto;
	margin-right: auto;
}

h1{
  text-align: center;  
}

input {
  padding: 10px;
  width: 100%;
  font-size: 17px;
  font-family: Raleway;
  border: 1px solid #aaaaaa;
}

/* Mark input boxes that gets an error on validation: */
input.invalid {
  background-color: #ffdddd;
}

/* Hide all steps by default: */
.tab, #my_submit_button {
  display: none;
}

button {
  background-color: #4CAF50;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
}

button:hover {
  opacity: 0.8;
}

#prevBtn {
  background-color: #0000FF;
}
</style>
</head>

 <body bgcolor="#E6E6FA">
	<h1 id="header1">Image Selection for Generating Dataset for Downsampling Algorithms</h1>
	<h2 id="header2"> For each of the images shown below, there are 5 pairs of random down-scaled images. For each pair, select the one which best resembles the original image.</h2>
	<form id="ResponseForm" action="cgi-bin/average.py" method="post">
	

<?php
// echo "Site Under Maintenance. Please try again between 09:00 and 22:00";
// exit();

class VisiterCounter {
    public static function incrementPageVisits($page){
        $counter_file = fopen("counter_file.txt","r+");
        if(flock($counter_file, LOCK_EX)){
	        $count = (int)fgets($counter_file);
	        rewind($counter_file);
        	$count = $count + 1;
    		ftruncate($counter_file, 0);
        	fwrite($counter_file, (string)$count);
        	// fflush($counter_file);
            flock($counter_file, LOCK_UN);
        }        
        else{
            echo "HEAVY TRAFFIC!";
            return 0;
        }
		fclose($counter_file);

		return $count;
    }
}

$visitor_no = VisiterCounter::incrementPageVisits('image_select.php');
if($visitor_no==0){
	exit("PLEASE TRY AGAIN AFTER A MOMENT!");
}

// Define useful whitespace variables
$_1 = "&nbsp;";
$_2 = str_repeat('&nbsp;', 2);
$_3 = str_repeat('&nbsp;', 3);
$_4 = str_repeat('&nbsp;', 4);
$_5 = str_repeat('&nbsp;', 5);
$_10 = str_repeat('&nbsp;', 10);
$_20 = str_repeat('&nbsp;', 20);
$_40 = str_repeat('&nbsp;', 40);


	# No. of images to be diplayed to each user
	$alpha = 20;
	# No. of versions of each landmark pair
	$versions = 5;
	# No. of landmarks per person (excluding the background)
	$landmarks = 1;
	# Pairs per image to be displayed to each user
	$pairs = $versions * $landmarks;
	# Just because the image data is so named
	$expressions = 1;
	# No. of men
	$M = 70;
	# No. of women
	$W = 60;

	$servername = "csmysql.cs.cf.ac.uk";
	$username = "c1868219";
	$password = "gau";
	$dbname = "c1868219";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

  if ( !$conn ) {
    die( 'connect error: '.mysqli_connect_error() );
  }

	# No. of images in the database
	$result = mysqli_query($conn, "SELECT COUNT(*) FROM Images;");
	$row = mysqli_fetch_row($result);
	$num_images = $row[0];


	function landmark_decoder($num){
		if($num==1){
			return "all";
		}
	}

	// Choose $quantity distinct random numbers in the range [min, max]
	function UniqueRandomNumbersWithinRange($min, $max, $quantity) {
	    $numbers = range($min, $max);
	    shuffle($numbers);
	    return array_slice($numbers, 0, $quantity);
	}

	function random2_images($conn, $num_images, $alpha){
		$array_ids = UniqueRandomNumbersWithinRange(1, $num_images, $alpha);
		$all_images = array();
		for($k=1; $k<=$alpha; $k++){
			$img_id = $array_ids[$k-1];
			$result = mysqli_query($conn, "SELECT image_name FROM Images WHERE image_id=$img_id;");
			$row = mysqli_fetch_row($result);
			$all_images[$k] = $row[0];
		}
		return $all_images;
	}

	$all_images = random2_images($conn, $num_images, $alpha);
	// $all_images = [1 => "m-1-1"];

	// var_dump($all_images);

	$all_images[0] = [$versions, $visitor_no];

	$rand_img_loc = shell_exec('python generate_random.py ' . escapeshellarg(json_encode($all_images)));
	$rand_img_loc = json_decode($rand_img_loc, true);

	echo "<br><br>";
	$responses = array();
	
	for($k = 1; $k <= $alpha; $k++){
		$i_temp = 1;
		$range_i = UniqueRandomNumbersWithinRange(1, $versions, $versions);
		foreach ($range_i as $i) {
			$j_temp = 1;
			$range_j = UniqueRandomNumbersWithinRange(1, $landmarks, $landmarks);
			foreach ($range_j as $j) {
			echo "<div class=\"tab\">";
				echo "<h2><center>Image $k/$alpha<center></h2>";
				$path = "images";
				echo "<img src=" . "\"" . "$path/" . "$all_images[$k].png\" class=\"central\" width=\"336px\" height=\"240px\">";
				$x = ($i_temp-1)*$landmarks + $j_temp;
				echo "<br>";
				echo "<h3> <center>Pair $x/$pairs </center> </h3>";
				// echo "<h3> <center>Pair $x" .landmark_decoder($j). "version $i". "</center> </h3>";

				$path_ab = $rand_img_loc[$k][$i][$j];

				$path_a = $path_ab[0];
				$path_b = $path_ab[1];

				$result = mysqli_query($conn, "SELECT image_id FROM Images WHERE image_name='$all_images[$k]';");
				$row = mysqli_fetch_row($result);
				$img_id = (int)$row[0];

				// $my_path is used to find the rand_dir_name (later to be passed to average computation script)
				if($i_temp == 1 && $j_temp == 1){
					$my_path = $path_a;
				}
				$x = ($i-1)*$landmarks + $j;
				$l = ($x-1)*2 + 1;
	
				echo "<table >";
					echo "<tr  id=\"divElement\">";
						echo "	<td>";
							echo "<img id=\"im$k-$l\" onclick=\"myFunction(1, $k, $x, '$path_a', $img_id, '$all_images[$k]');\" src=" . "\"" . $path_a . "\"" . "width=\"84px\" height=\"116px\"/>";
							echo "<input id=\"A$k-$x\" name=\"A$k-$x\" type=\"hidden\" value=\"0\"></input>";
						echo "</td>";

				$l++;

						echo "	<td>";
							echo "<img id=\"im$k-$l\" onclick=\"myFunction(2, $k, $x, '$path_b', $img_id, '$all_images[$k]');\" src=" . "\"" . $path_b . "\"" . "width=\"84px\" height=\"116px\"/>";
							echo "<input id=\"B$k-$x\" name=\"B$k-$x\" type=\"hidden\" value=\"0\"></input>";
						echo "</td>";
					echo "</tr>";
				echo "</table>";

			echo "</div>";
			$j_temp++;
			}
			$i_temp++;
		}
		$pieces = explode("/", $my_path);
		$rand_dir_name = $pieces[1];
		echo "<input name=\"rand_dir_name$k\" type=\"hidden\" value=\"$rand_dir_name\" />";
	}
	echo "<input name=\"visitor_no\" type=\"hidden\" value=\"$visitor_no\" />";

	// Commit the transaction
	mysqli_commit($conn);

	// Close the connection
	mysqli_close($conn);


    // Prev button
echo "<div style=\"overflow:auto;\">".
     "<div style=\"float:right;\">".
	      "<button type=\"button\" id=\"prevBtn\" onclick=\"nextPrev(-1)\">Previous</button>".
    "</div>".
    "</div>";

?>
</form>


<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the crurrent tab
var pairs = 5;
var choicesList = [];


function postData(input) {
    $.ajax({
        type: "POST",
        url: "cgi-bin/avg.py",
        async: true,
        dataType: "json",
        data: {
         	path: input[0],
         	img_id: input[1],
         	img_name: input[2],
         	choice_list: '[' + choicesList + ']'
        },
        success: callbackFunc,
         error: function (err) {
        	console.log("AJAX error in request: " + JSON.stringify(err, null, 2));
    	}
	});
}

function callbackFunc(response) {
    // do something with the response
    console.log(response);
}

function myFunction(one_two, k, x, path, img_id, img_name){
	// choicesList.push(x);
	if(one_two == 1){
		document.getElementById("A" + k + "-" + x).value = "1";
		document.getElementById("B" + k + "-" + x).value = "0";
		choicesList.push([x,1]);
		// choicesList.push(1);
	}

	else if(one_two == 2){
		document.getElementById("B" + k + "-" + x).value = "1";
		document.getElementById("A" + k + "-" + x).value = "0";
		choicesList.push([x,2]);
		// choicesList.push(2);
	}

	// If one image's responses have been recorded, calc average and store it
	if((currentTab+1)%pairs==0){
		postData([path, img_id, img_name]);
		choicesList = [];
	}
	nextPrev(1);
}

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.visibility = "hidden";
  } else {
    document.getElementById("prevBtn").style.visibility = "visible";
  }
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
	document.getElementById("header1").style.display = "none";
	document.getElementById("header2").innerHTML = "Submitting your responses";	
	document.getElementById("header2").style.textAlign = "center";	
  	document.getElementById("prevBtn").style.display = "none";
    // and the form gets submitted:
    document.getElementById("ResponseForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

</script>

</body>
</html>
