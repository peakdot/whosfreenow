<?php
require("test_input.php");
require("conn.php");

function addblock($name, $startTime, ...) {
	insertToDB("blocks", ["_name", "_startTime", ...], [[$name, $startTime, ...]]);
}

function editblock($id, $name, $startTime, ...) {
	editFromDBSecure("blocks", ["_name", "_startTime", ...], [$name, $startTime, ...], "id = ".$id);
}

function removeblock($id) {
	removeFromDBSecure("block_object_links", "block_id=".$id);
	removeFromDBSecure("blocks", "id = ".$id);
}

?>