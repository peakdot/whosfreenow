<?php
function test_input($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}


function get_input_ex($name, $type, $null_allowed = true, $req_type = "POST", $binary = false) {
	if($binary) {
		return get_input_bin($name, $req_type);
	} 

	if($req_type == "GET") {
		return get_input_get($name, $type, $null_allowed);
	} else if($req_type == "POST") {
		return get_input_post($name, $type, $null_allowed);
	} else {
		die("Unknown server request type: ".$req_type);
	}
}

function get_input_bin($name, $req_type = "POST") {
	if($req_type == "POST") {
		if(!isset($_POST[$name]) || $_POST[$name]==null) {
		//Replace
			return 0;
		} else {
			return 1;
		}
	} else if($req_type == "GET") {
		if(!isset($_GET[$name]) || $_GET[$name]==null) {
		//Replace
			return 0;
		} else {
			return 1;
		}
	} else {
		die("Unknown server request type: ".$req_type);
	}
}

function get_input_get($name, $type, $null_allowed) {
	if(!isset($_GET[$name]) || $_GET[$name]==null || $_GET[$name]=="") {
		if(!$null_allowed){
			//Replace
			die("<br>No input for ".$name.$_GET[$name]);
		} else {
			return null;
		}
	}

	$data = $_GET[$name];

	if($type == 0 && (string)(int)$data != $data) {
		//Replace
		die("<br>Invalid input (expected integer) for ".$name.$_GET[$name]);
	}

	return test_input($data);
}

function get_input_post($name, $type, $null_allowed) {
	if(!isset($_POST[$name]) || $_POST[$name]==null || $_POST[$name]=="") {
		if($null_allowed)
			return null;

		//Replace
		die("<br>No input for ".$name.$_POST[$name]);
	}

	$data = $_POST[$name];

	if($type == 0 && (string)(int)$data != $data) {
		//Replace
		die("<br>Invalid input (expected integer) for ".$name.$_POST[$name]);
	}

	return test_input($data);
}

?>