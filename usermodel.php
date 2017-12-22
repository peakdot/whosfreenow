<?php
session_start();

require_once("test_input.php");
require_once("conn.php");
require_once("block.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST["idtoken"])) {
		if(isset($_SESSION["user"])) {
			echo "You already signed in.";
		} else {
			// ToDo. Need to know idtoken's format. Add test_input if idtoken format is compatible.
			signinwithgoogle($_POST["idtoken"]);
		}
	}
}

function signinwithgoogle($googleidtoken){
	require_once('vendor/autoload.php');

	// Get $id_token via HTTPS POST.

	$client = new Google_Client(['client_id' => $CLIENT_ID]);
	$payload = $client->verifyIdToken($id_token);
	if ($payload) {
		$googleid = $payload['sub'];
		if(count($user = getFromDB("users", array("groupId"), "googleid = " + $googleid)) > 0) {
			getBlocks($user[0]["groupId"]);
			$_SESSION["user"] = $googleid;
			echo "You are signed in.";
		} else {
			if(insertUser($googleid)) {
				$_SESSION["user"] = $googleid;
				echo "You are signed in.";
			} else {
				echo "Failed to sign in.";
			}
		}
	} else {
		// Invalid ID token
		echo "Sorry, something went wrong. Please try again later.";
	}
}

function insertUser($googleid){
	$groupid = insertToDB("groups", array("name", "type"), array(array($googleid, 1)));
	if(insertToDB("users", array("googleid", "groupId"), array(array($googleid, $groupid))) !== false) {
		return true;
	} else {
		return false;
	}
}

?>