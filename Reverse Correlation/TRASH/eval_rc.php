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

table
{ 
	margin-top: 100px;
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

button {
  background-color: #4CAF50;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
}

/*#my_begin_button{
	display: flex; 
	justify-content: center;
}*/

/* Hide all steps by default: */
.tab{
  display: none;
}
#visit_time{
	display: none;
}
#visitor_no{
	display: none;
}

</style>
</head>

<body bgcolor="#E6E6FA">
	<form id="ResponseForm" action="../cgi-bin/average_rc.py" method="post">

<?php
// $visit_time = date('Y-m-d H:i:s');
// echo "<p id=\"visit_time\">$visit_time</p>";

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
            echo "HEAVY TRAFFIC!<br>";
            return 0;
        }
		fclose($counter_file);

		return $count;
    }
}

$visitor_no = VisiterCounter::incrementPageVisits('image_select.php');
if($visitor_no==0){
	exit("PLEASE TRY AGAIN AFTER A MOMENT.");
}

echo "<p id=\"visitor_no\">$visitor_no</p>";

// Define useful whitespace variables
$_1 = "&nbsp;";
$_2 = str_repeat('&nbsp;', 2);
$_3 = str_repeat('&nbsp;', 3);
$_4 = str_repeat('&nbsp;', 4);
$_5 = str_repeat('&nbsp;', 5);
$_10 = str_repeat('&nbsp;', 10);
$_20 = str_repeat('&nbsp;', 20);
$_40 = str_repeat('&nbsp;', 40);


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
// $result = mysqli_query($conn, "SELECT COUNT(*) FROM Images;");
// $row = mysqli_fetch_row($result);
// $num_images = $row[0];

$num_images = 10;

// Choose $quantity distinct random numbers in the range [min, max]
function UniqueRandomNumbersWithinRange($min, $max, $quantity) {
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}

function random2_images($conn, $num_images){
	$array_ids = UniqueRandomNumbersWithinRange(1, $num_images, $num_images);

	$ds_images = array();
	$orig_images = array();
	for($k=1; $k<=$num_images; $k++){
		$img_id = $array_ids[$k-1];
		$orig_images[$img_id] = UniqueRandomNumbersWithinRange(1, $num_images, $num_images);
		$result = mysqli_query($conn, "SELECT image_name FROM Images WHERE image_id=$img_id;");
		$row = mysqli_fetch_row($result);
		$ds_images[$k] = $row[0];
	}
	return [$ds_images, $orig_images];
}

// $ds_images stores the names of the 20 downsampled images to be displayed
// $orig_images stores the ids (ordered) of the 20 full size images for each
// downsampled image. The full size images will be displayed in the order
// specified by the elements of orig_images

function funcA($conn, $num_images, $imagesA){
	$ds_imagesA = $imagesA[0];
	$orig_imagesA = $imagesA[1];

	// normal downsampling
    $path = "images_ds/";
	for($k = 1; $k <= $num_images; $k++){
		############################################# SMALL IMAGE ############################################################
		echo "<div class=\"tab\">";
			echo "<h2><center>Image $k/$num_images<center></h2>";
			echo "<img src=" . "\"" . "$path/" . "$ds_imagesA[$k].png\" class=\"central\" width=\"21px\" height=\"29px\">";
		echo "</div>";
		############################################# BLANK SCREEN ############################################################
		echo "<div class=\"tab\">";
			echo "<h2> </h2>";
		echo "</div>";
		############################################# BIG IMAGES ##############################################################
		echo "<div class=\"tab\">";
			$img_order = $orig_imagesA[$k];
			echo "<table >";
				$m = 1;
				echo "<tr>";
				while($m <= 5){
					$img_id = $img_order[$m-1];
					$result = mysqli_query($conn, "SELECT image_name FROM Images WHERE image_id=$img_id;");
					$row = mysqli_fetch_row($result);
					$path_a = "images/". $row[0].".png";
					echo "	<td>";
					echo "<img id=\"imA$k-$m\" onclick=\"myFunction('A', $k, $m, $img_id, '$ds_imagesA[$k]');\" src=" . "\"" . $path_a . "\"" . "width=\"138px\" height=\"189px\"/>";
					echo "<input id=\"A$k-$m\" name=\"A$k-$m\" type=\"hidden\" value=\"0\"></input>";
					echo "</td>";
					$m++;
				}
				echo "</tr>";

				echo "<tr>";
				while($m <= $num_images){
					$img_id = $img_order[$m-1];
					$result = mysqli_query($conn, "SELECT image_name FROM Images WHERE image_id=$img_id;");
					$row = mysqli_fetch_row($result);
					$path_a = "images/". $row[0].".png";					
					echo "	<td>";
					echo "<img id=\"imA$k-$m\" onclick=\"myFunction('A', $k, $m, $img_id, '$ds_imagesA[$k]');\" src=" . "\"" . $path_a . "\"" . "width=\"138px\" height=\"189px\"/>";
					echo "<input id=\"A$k-$m\" name=\"A$k-$m\" type=\"hidden\" value=\"0\"></input>";
					echo "</td>";
					$m++;
				}
				echo "</tr>";
			echo "</table>";
		echo "</div>";
		###################################################################################################################
	}
}

function funcB($conn, $num_images, $imagesB){
	$ds_imagesB = $imagesB[0];
	$orig_imagesB = $imagesB[1];

	// reverse correlation
    $path = "images_avg/";
	for($k = 1; $k <= $num_images; $k++){
		############################################# SMALL IMAGE ############################################################
		echo "<div class=\"tab\">";
			echo "<h2><center>Image $k/$num_images<center></h2>";
			echo "<img src=" . "\"" . "$path/" . "$ds_imagesB[$k].png\" class=\"central\" width=\"21px\" height=\"29px\">";
		echo "</div>";
		############################################# BLANK SCREEN ############################################################
		echo "<div class=\"tab\">";
			echo "<h2> </h2>";
		echo "</div>";
		############################################# BIG IMAGES ##############################################################
		echo "<div class=\"tab\">";
			$img_order = $orig_imagesB[$k];
			echo "<table >";
				$m = 1;

				echo "<tr>";
				while($m <= 5){
					$img_id = $img_order[$m-1];
					$result = mysqli_query($conn, "SELECT image_name FROM Images WHERE image_id=$img_id;");
					$row = mysqli_fetch_row($result);
					$path_a = "images/". $row[0].".png";					
					echo "	<td>";
					echo "<img id=\"imB$k-$m\" onclick=\"myFunction('B', $k, $m, $img_id, '$ds_imagesB[$k]');\" src=" . "\"" . $path_a . "\"" . "width=\"138px\" height=\"189px\"/>";
					echo "<input id=\"B$k-$m\" name=\"B$k-$m\" type=\"hidden\" value=\"0\"></input>";
					echo "</td>";
					$m++;
				}
				echo "</tr>";

				echo "<tr>";
				while($m <= $num_images){
					$img_id = $img_order[$m-1];
					$result = mysqli_query($conn, "SELECT image_name FROM Images WHERE image_id=$img_id;");
					$row = mysqli_fetch_row($result);
					$path_a = "images/". $row[0].".png";					
					echo "	<td>";
					echo "<img id=\"imB$k-$m\" onclick=\"myFunction('B', $k, $m, $img_id, '$ds_imagesB[$k]');\" src=" . "\"" . $path_a . "\"" . "width=\"138px\" height=\"189px\"/>";
					echo "<input id=\"B$k-$m\" name=\"B$k-$m\" type=\"hidden\" value=\"0\"></input>";
					echo "</td>";
					$m++;
				}
				echo "</tr>";
			echo "</table>";
		echo "</div>";
		###################################################################################################################
	}
}

#### Page 1
echo "<div class=\"tab\">";
	echo "<h1 id=\"header1\">Evaluating Reverse Correlation</h1>";
	echo "<h3>In this study, you would be shown 20 low resolution images, each for a period of 3 seconds. You need to remember
			the image, and on the next screen you would be asked to select the higher resolution image which best resembles
			the image you saw.
				<br>
				<br>
			The experiment has two phases, each meant to evaluate a specific downsampling method.
		 </h3>";
	echo "<br>";
echo "</div>";

echo "<div style=\"float:right; position: relative; left: -50%;  text-align: left;\">";
	echo "<button type=\"button\" id=\"my_begin_button\" onclick=\"myFunction3(1);\" >BEGIN</button>";
echo "</div>";

### Phase-1
echo "<div id=\"Phase-1\" class=\"tab\">";
	echo "<h1>Phase-1</h1>";
echo "</div>";

$imagesA = random2_images($conn, $num_images);
$imagesB = random2_images($conn, $num_images);

$A_B = rand(1,2);
if($A_B == 1){
	funcA($conn, $num_images, $imagesA);
	### Phase-2
	echo "<div id=\"Phase-2\" class=\"tab\">";
		echo "<h1>Phase-2</h1>";
	echo "</div>";
	funcB($conn, $num_images, $imagesB);
}
else{
	funcB($conn, $num_images, $imagesB);
	### Phase-2
	echo "<div id=\"Phase-2\" class=\"tab\">";
		echo "<h1>Phase-2</h1>";
	echo "</div>";
	funcA($conn, $num_images, $imagesA);
}
// Commit the transaction
mysqli_commit($conn);

// Close the connection
mysqli_close($conn);
?>

<script>
// var visitTime = document.getElementById('visit_time').innerHTML;
var visitorNo = document.getElementById('visitor_no').innerHTML;
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the crurrent tab
var num_images = 10;
var start = 0;
var elapsed = 0;

function myFunction3(phase_no){
  var x = document.getElementsByClassName("tab");
  x[currentTab].style.display = "none";
  currentTab = currentTab + 2;

  if(phase_no==1){
  	$("#Phase-1").show();
    document.getElementById("my_begin_button").style.display = "none";
  	setTimeout(function() { $("#Phase-1").hide(); showTab(currentTab);}, 1500);
  }
  else{
  	$("#Phase-2").show();
  	setTimeout(function() { $("#Phase-2").hide(); showTab(currentTab);}, 1500);
  }
  return false;
}

function myFunction2() {
    return "Write something clever here...";
}

function postData(input) {
    $.ajax({
        type: "POST",
        url: "../cgi-bin/avg_rc.py",
        async: true,
        dataType: "json",
        data: {
         	img_id: input[0],
         	visitor_no: visitorNo,
         	normal_resp_time: input[1],
         	rc_resp_time: input[2],
         	ds_img_name: input[3]
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

function myFunction(A_B, k, m, img_id, ds_img_name){
	elapsed = new Date().getTime() - start;
	if(A_B == 'A'){
		document.getElementById("A" + k + "-" + m).value = "1";
		normal_resp_time = elapsed;
		rc_resp_time = -1;
	}

	else if(A_B == 'B'){
		document.getElementById("B" + k + "-" + m).value = "1";
		rc_resp_time = elapsed;
		normal_resp_time = -1;
	}

	postData([img_id, normal_resp_time, rc_resp_time, ds_img_name]);
	nextPrev(1);
}

function showTab(n) {
	// This function will display the specified tab of the form...
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
	start = new Date().getTime();

	// Phase-1 has begun
	if(n >= 2 && n <= 3*num_images){
		var m = n-2;
		if(m%3 == 0){
			setTimeout(function() {nextPrev(1); }, 3000);
		}
		else if(m%3 == 1){
			setTimeout(function() {nextPrev(1); }, 1500);
		}
	}

	// Phase-2 has begun
	else if(n >= 3*num_images+3){
		var m = n;
		if(m%3 == 0){
			setTimeout(function() {nextPrev(1); }, 3000);
		}
		else if(m%3 == 1){
			setTimeout(function() {nextPrev(1); }, 1500);
		}		
	}
}

function nextPrev(n) {
	// This function will figure out which tab to display
	
	// Final tab of Phase-1; Phase-2 header tab should be shown now
	if(currentTab == 3*num_images+1){
		myFunction3(2);
	}

	else{
		var x = document.getElementsByClassName("tab");

		// Hide the current tab:
		x[currentTab].style.display = "none";

		// Increase or decrease the current tab by 1:
		currentTab = currentTab + n;
		// if you have reached the end of the form...
		if (currentTab >= x.length) {
		// and the form gets submitted:
		document.getElementById("ResponseForm").submit();
		return false;
		}
		// Otherwise, display the correct tab:
		showTab(currentTab);
	}
}

</script>

</body>
</html>
