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

$num_images_per_method = 10;
$methods = 4;
$num_images = $num_images_per_method * $methods;

function method_encoder($method_name){
	if($method_name == "dpid"){
		return 1;
	}
	else if($method_name == "lanc"){
		return 2;
	}
	else if($method_name == "pix"){
		return 3;
	}
	else if($method_name == "rc"){
		return 4;
	}
}

// Choose $quantity distinct random numbers in the range [min, max]
function UniqueRandomNumbersWithinRange($min, $max, $quantity) {
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}

function random2_images($conn, $num_images, $num_images_per_method){
	$array_ids = UniqueRandomNumbersWithinRange(1, $num_images, $num_images);

	$ds_images = array();
	$orig_images = array();
	for($k=1; $k<=$num_images; $k++){
		$img_id = $array_ids[$k-1];
		$orig_images[$img_id] = UniqueRandomNumbersWithinRange(1, $num_images_per_method, $num_images_per_method);
		$result = mysqli_query($conn, "SELECT image_name FROM TestImages WHERE image_id=$img_id;");
		$row = mysqli_fetch_row($result);
		$ds_images[$k] = $row[0];
	}
	return [$ds_images, $orig_images];
}

// $ds_images stores the names of the 40 (10x4) downsampled images to be displayed
// $orig_images stores the ids (ordered) of the 10 full size images for each
// $orig_images[5] stores the order of full-size images for image with image_id 5 in 
// the TestImages table
// downsampled image. The full size images will be displayed in the order
// specified by the elements of orig_images

function func($conn, $num_images, $images, $num_images_per_method){
	$ds_images = $images[0];
	$orig_images = $images[1];

    $path = "images_all/";
	for($k = 1; $k <= $num_images; $k++){
		$img_name = $ds_images[$k];
		$pieces = explode("_", $img_name);
		$method_name = $pieces[1];
		$method_id = method_encoder($method_name);
		############################################# SMALL IMAGE #############################################################
		echo "<div class=\"tab\">";
			echo "<h2><center>Image $k/$num_images<center></h2>";
			echo "<img src=" . "\"" . "$path/" . "$img_name.png\" class=\"central\" width=\"21px\" height=\"29px\">";
		echo "</div>";
		############################################# BLANK SCREEN ############################################################
		echo "<div class=\"tab\">";
			echo "<h2> </h2>";
		echo "</div>";
		############################################# BIG IMAGES ##############################################################
		echo "<div class=\"tab\">";
			$img_order = $orig_images[$k];
			echo "<table >";
				$m = 1;
				echo "<tr>";
				while($m <= 5){
					$img_id = $img_order[$m-1];
					$result = mysqli_query($conn, "SELECT image_name FROM TrainImages WHERE image_id=$img_id;");
					$row = mysqli_fetch_row($result);
					$path_a = "images/". $row[0].".png";
					echo "	<td>";
					echo "<img id=\"im$k-$m\" onclick=\"myFunction($method_id, $k, $m, $img_id, '$img_name');\" src=" . "\"" . $path_a . "\"" . "width=\"138px\" height=\"189px\"/>";
					echo "<input id=\"$k-$m\" name=\"$k-$m\" type=\"hidden\" value=\"0\"></input>";
					echo "</td>";
					$m++;
				}
				echo "</tr>";

				echo "<tr>";
				while($m <= $num_images_per_method){
					$img_id = $img_order[$m-1];
					$result = mysqli_query($conn, "SELECT image_name FROM TrainImages WHERE image_id=$img_id;");
					$row = mysqli_fetch_row($result);
					$path_a = "images/". $row[0].".png";
					echo "	<td>";
					echo "<img id=\"im$k-$m\" onclick=\"myFunction($method_id, $k, $m, $img_id, '$img_name');\" src=" . "\"" . $path_a . "\"" . "width=\"138px\" height=\"189px\"/>";
					echo "<input id=\"$k-$m\" name=\"$k-$m\" type=\"hidden\" value=\"0\"></input>";
					echo "</td>";
					$m++;
				}
				echo "</tr>";
			echo "</table>";
		echo "</div>";
		######################################################################################################################
	}
}

#### Page 1
echo "<div class=\"tab\">";
	echo "<h1 id=\"header1\">Evaluating Reverse Correlation</h1>";
	echo "<h3>In this study, you would be shown 40 low resolution images, each for a period of 3 seconds. You need to remember
			the image, and on the next screen you would be asked to select the higher resolution image which best resembles
			the image you saw.
				<br>
				<br>
		 </h3>";
	echo "<br>";
echo "</div>";

echo "<div style=\"float:right; position: relative; left: -50%;  text-align: left;\">";
	echo "<button type=\"button\" id=\"my_begin_button\" onclick=\"myFunction3();\" >BEGIN</button>";
echo "</div>";

$images = random2_images($conn, $num_images, $num_images_per_method);
func($conn, $num_images, $images, $num_images_per_method);

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
var num_images = 40;
var start = 0;
var elapsed = 0;

// Called on clicking BEGIN button
function myFunction3(){
  var x = document.getElementsByClassName("tab");
  x[currentTab].style.display = "none";
  currentTab = currentTab + 1;
  document.getElementById("my_begin_button").style.display = "none";
  showTab(currentTab);
  return false;
}

function myFunction2() {
    return "Write something clever here...";
}


// Called by myFunction()
function postData(input) {
    $.ajax({
        type: "POST",
        url: "../cgi-bin/avg_rc_new.py",
        async: true,
        dataType: "json",
        data: {
         	method_id: input[0],
         	visitor_no: visitorNo,
         	img_id: input[1],
         	resp_time: input[2],
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

// Called on choosing an image
function myFunction(method_id, k, m, img_id, ds_img_name){
	resp_time = new Date().getTime() - start;
	document.getElementById(k + "-" + m).value = "1";

	postData([method_id, img_id, resp_time, ds_img_name]);
	nextPrev(1);
}

function showTab(n) {
	// This function will display the specified tab of the form...
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
	start = new Date().getTime();

	// Voting has begun!
	if(n >= 1){
		var m = n-1;
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

</script>

</body>
</html>
