<html>
<body>
<head>
<!-- 	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<style type="text/css">
	#divElement{
	    position: relative;
	    display: block;
	    margin-left: 0px;
	    margin-right: auto;
	}​


</style> -->
</head>

<?php
$return = 0;
shell_exec('python clean_up.py &> test.txt');
// $str = "\x07";
// print $str;
	// $servername = "csmysql.cs.cf.ac.uk";
	// $username = "c1868219";
	// $password = "gau";
	// $dbname = "c1868219";

	// // Create connection
	// $conn = mysqli_connect($servername, $username, $password, $dbname);

	





// $name = "gaurav";
// echo exec("echo gaurav");
##################################################################################################
// // This is the data you want to pass to Python
// $data = array();
// $data[0] = [2, 3];
// $data[1] = "m-1";
// $data[2] = "w-19";
// $data[3] = "m-23";

// // Execute the python script with the JSON data
// $result = shell_exec('python try.py ' . escapeshellarg(json_encode($data)));

// // Decode the result
// $resultData = json_decode($result);

// // echo $resultData;
// // This will contain: array('status' => 'Yes!')
// // var_dump($resultData);
// echo json_encode($resultData);
##################################################################################################
// $message = exec("python try.py 2>&1");
// print_r($message);
##################################################################################################
	
// $k = 1;
// $x = 1;
// $all_images = array(1 => "m-1-1");
// // echo "<h2><center>Image $k:<center></h2>";
// // $path = "images";
// // echo "<img src=" . "\"" . "$path/" . "$all_images[$k].png\" class=\"central\" width=\"384px\" height=\"288px\">";
// // echo "<br>";
// echo "<h3> <center>Pair $x</center> </h3>";

// $path_a = "images_rand/m-1-1_1.5278578325e+12_96/m-1-1-1a1.png";
// $path_b = "images_rand/m-1-1_1.5278578325e+12_96/m-1-1-1b1.png";

// $l = ($x-1)*2 + 1;

// echo "<table>";
// 	echo "<tr  id=\"divElement\">";
// 		echo "	<td>";
// 			echo "<img id=\"im$k-$l\" onclick=\"myFunction(1, $k, $x);\" src=" . "\"" . $path_a . "\"" . "width=\"84px\" height=\"116px\"/>";
// 			echo "<input id=\"A$k-$x\" name=\"A$k-$x\" type=\"hidden\" value=\"0\"></input>";
// 		echo "</td>";

// $l++;

// 		echo "	<td>";
// 			echo "<img id=\"im$k-$l\" onclick=\"myFunction(2, $k, $x);\" src=" . "\"" . $path_b . "\"" . "width=\"84px\" height=\"116px\"/>";
// 			echo "<input id=\"B$k-$x\" name=\"B$k-$x\" type=\"hidden\" value=\"0\"></input>";
// 		echo "</td>";
// 	echo "</tr>";
// echo "</table>";

?>

<!-- <p id="demo"></p>

<script type="text/javascript">
	
	  var browser_width = parseInt($(window).width());
	  var small_img_width = parseInt($('#im1-1').width());
	  var newMargin = (browser_width - 2*small_img_width - 5)/2 + "px";
	  $('#divElement').css('margin-left', newMargin);

	  document.getElementById("demo").innerHTML = small_img_width;
</script> -->


</body>
</html>







<!-- 
2742660_Cardiff_University
QueensImage@1505

MySQL 5.7 -->