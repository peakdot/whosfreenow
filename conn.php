<?php
function initcon() {
	require("dbinfo.php");
	global $conn;

	$conn = new mysqli($servername, $username, $pw, $dbname);

	if ($conn->connect_error) {
		//array_replace(array, array1)
		return false;
		die("Connection failed: " . $conn->connect_error);
	} 

	if (!$conn->set_charset("utf8")) {
		//Replace
		return false;
		die("Error loading character set utf8: ".$mysqli->error);
	} 
}

function closecon() {
	global $conn;
	if (!$conn->close()){
		//Replace
		return false;
		die("Can't close connection");
	}
}

function insertToDB($tablename, $columnnames, $data_array){
	global $conn;

	//If no data transmitted then exit
	if(count($data_array) == 0) {
		echo "No data transmitted. Table name: ".$tablename."<br>";
		return false;
	}

	$datas = $data_array[0];

	$datatype = createDatatypeString($tablename, $columnnames);
	
	initcon();

	$len = count($columnnames);

	$query1 = "INSERT INTO ".$tablename."(";
	$query2 = implode(",", $columnnames);
	$query3 = " VALUES (?".str_repeat(", ?", $len - 1);

	//Building complete query
	$query = $query1.$query2.")".$query3.")";

	
	if(!$stmt = $conn->prepare($query)){
		//Replace
		return false;
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	//Adding parameters to bind_param method. Almost equal to bind_param("isss",[params]);
	$params = array_merge(array($datatype), $datas); 

	$refArr = array();
	foreach($params as $key => $value) {
		$refArr[$key] = &$params[$key];
	}

	if(!call_user_func_array(array($stmt, 'bind_param'), $refArr)){
		//Replace
		//return false;
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	} 

	$len = count($data_array);
	for ($i = 0; $i < $len; $i++) {
		$datas = &$data_array[$i];
		$tempparams = array_merge(array($datatype),$datas); 

		foreach($params as $key => $value) {
			$params[$key] = $tempparams[$key];
		}

		if(!$stmt->execute()){
			//Replace
			//return false;
			echo $data_array[0][0];
			die("Error on executing query:".$query."<br>".$conn->error);
		}
	}

	$id = $conn->insert_id;

	$stmt->close();
	closecon();

	return $id;
}

function &getFromDB($tablename, $columnnames, $condition = "1=1"){
	global $conn;

	initcon();

	$subquery = implode(",", $columnnames);

	if($result = $conn->query("SELECT ".$subquery." FROM ".$tablename." WHERE ".$condition)){
		$res = $result->fetch_all(MYSQLI_ASSOC); 
		
		$result->close();
		closecon();

		return $res;
	} else {
		//Replace
		die("Failed to retrieve data: ".$conn->error);
	}
}

function editFromDB($tablename, $columnnames, $data, $condition = "1=1"){
	global $conn;

	initcon();

	$subquery = "";

	foreach($columnnames as $i => $columnname) {
		if($i == 0) {
			$subquery .= $columnname."='".$data[$i]."'";
		} else {
			$subquery .= ",".$columnname."='".$data[$i]."'";
		}
	}

	if($result = $conn->query("UPDATE ".$tablename." SET ".$subquery." WHERE ".$condition)){
		closecon();

		return true;
	} else {
		//Replace
		die("UPDATE ".$tablename." SET ".$subquery." WHERE ".$condition.", Failed to retrieve data from edit: ".$conn->error);
	}
}

function removeFromDB($tablename, $condition = "1=1"){
	global $conn;

	initcon();

	if($result = $conn->query("DELETE FROM ".$tablename." WHERE ".$condition)){		
		
		closecon();

		return true;
	} else {
		//Replace
		die("Failed to retrieve data: ".$conn->error);
	}
}

/*
condition argument must be like this:
id = ? and username = ? or username = ?

Data is the value for the question mark in the condition above. So number of data must be equal to number of question mark in condition
*/
function &getFromDBSecure($tablename, $columnnames, $inConditionColumnnames, $condition = null, $data = null) {
	global $conn;

	if($condition == null) {
		return getFromDB($tablename, $columnnames);
	}

	$subquery = implode(",", $columnnames);

	$query = "SELECT ".$subquery." FROM ".$tablename." WHERE ".$condition.";";

	$datatype = createDatatypeString($tablename, $inConditionColumnnames);
	
	initcon();

	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	//Adding parameters to bind_param method. Equal to bind_param("isss",[params]);
	$params = array_merge(array($datatype), $data); 

	$refArr = array();
	foreach($params as $key => $value) {
		$refArr[$key] = &$params[$key];
	}

	if(!call_user_func_array(array($stmt, 'bind_param'), $refArr)){
		//Replace
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	} 

	if(!$stmt->execute()){
			//Replace
		die("Error on executing query:".$query."<br>".$conn->error);
	}


	$result = $stmt->get_result();

	$res = $result->fetch_all(MYSQLI_ASSOC);

	$result->close();
	$stmt->close();

	closecon();

	return $res;
}

function editFromDBSecure($tablename, $columnnames, $data, $condition, $inConditionColumnnames, $inConditionData) {
	global $conn;

	if($condition == null) {
		return ;
	}

	$subquery = implode("=?,", $columnnames);

	$query = "UPDATE ".$tablename." SET ".$subquery." WHERE ".$condition.";";

	$datatype = createDatatypeString($tablename, array_merge($columnnames, $inConditionColumnnames));

	$data = array_merge($data, $inConditionData);
	
	initcon();

	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	//Adding parameters to bind_param method. Equal to bind_param("isss",[params]);
	$params = array_merge(array($datatype), $data); 

	$refArr = array();
	foreach($params as $key => $value) {
		$refArr[$key] = &$params[$key];
	}

	if(!call_user_func_array(array($stmt, 'bind_param'), $refArr)){
		//Replace
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	} 

	if(!$stmt->execute()){
			//Replace
		die("Error on executing query:".$query."<br>".$conn->error);
	}

	$result->close();
	$stmt->close();

	closecon();
}

//Unfinished
function removeFromDBSecure($tablename, $inConditionColumnnames, $condition = null, $data = null) {
	global $conn;

	if($condition == null) {
		return ;
	}

	$query = "DELETE FROM ".$tablename." WHERE ".$condition.";";

	$datatype = createDatatypeString($tablename, $inConditionColumnnames);
	
	initcon();

	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	//Adding parameters to bind_param method. Equal to bind_param("isss",[params]);
	$params = array_merge(array($datatype), $data); 

	$refArr = array();
	foreach($params as $key => $value) {
		$refArr[$key] = &$params[$key];
	}

	if(!call_user_func_array(array($stmt, 'bind_param'), $refArr)){
		//Replace
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	} 

	if(!$stmt->execute()){
			//Replace
		die("Error on executing query:".$query."<br>".$conn->error);
	}

	$result->close();
	$stmt->close();

	closecon();
}


function &createDatatypeString(&$tablename, &$columnnames){
	global $conn;
	initcon();

	$result = null;
	$datatype = "";
	if($result = $conn->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tablename."';")) {
		$res = $result->fetch_all(MYSQLI_NUM); 

		foreach($columnnames as $name) {
			$temptype = -1;
			foreach($res as list($tempname, $type)) {
				if($name == $tempname) {
					switch($type) {
						case "int": $temptype = "i"; break;
						case "float": 
						case "double": $temptype = "d"; break;
						default: $temptype = "s"; break;
					}
					break;
				}
			}

			if($temptype == -1) {
				//Replace
				die("Undefined column name in data. Table name: ".$tablename.", columnname:".$name);
			} else {
				$datatype .= $temptype;
			}
		}
	} else {
		//Replace
		die("Failed to retrieve table column info: ".$conn->error);
	}

	closecon();

	return $datatype;
}

?>