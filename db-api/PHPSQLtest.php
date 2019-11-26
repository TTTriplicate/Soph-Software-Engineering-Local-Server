<?PHP
	$con=mysqli_connect("localhost", "root", "");
	$sql="USE test_DB";
	if (mysqli_query($con, $sql)){
		echo "Database created successfully.";
	}
	else{
		echo "something went wrong.";
	}
?>