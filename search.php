<?php
error_reporting(E_ALL);
ini_set('display_errors',1);


DEFINE	('DB_HOST', 'localhost');
DEFINE  ('DB_USER', 'root');
DEFINE	('DB_PSWD', '');
DEFINE ('DB_DATABASE', 'a_database'); 

if(isset($_GET['south']))
{
	//connect to database,in this case its localhost 
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PSWD, DB_DATABASE);
	if($conn)
	{
		//get variables in HTTP get request 
		$cache_type = $_GET['cache_type'];
		$difficulty = $_GET['difficulty'];
		$south = $_GET['south'];
		$north = $_GET['north'];
		$west = $_GET['west'];
		$east = $_GET['east'];	
		
		//query string 
		$sql = "SELECT * FROM `test_data` JOIN `cache_types` 
		ON `test_data`.`cache_type_id` = `cache_types`.`type_id` WHERE `latitude` < '$north'
		AND `latitude` > '$south' AND `longitude` > '$west'  AND `longitude` < '$east'
		AND `difficulty_rating` = '$difficulty' AND `cache_types`.`cache_type`= '$cache_type'";	
		//get query request results within circle bounds 
		$result = mysqli_query($conn, $sql);
		$json_array = array(); ;
		while($row = mysqli_fetch_assoc($result))
		{
			$json_array[] = $row;
		}
		//echo array back to index file 
		echo json_encode($json_array);
	}
}


?>
