<?php
function addobject($name, $description, ...){
	insertToDB("objects", ["_name", "_description", ...]);
}

function editobject($id, $name, $description, ...){
	editFromDBSecure("objects", ["_name", "_description", ...], [$name, $description, ...], "id = ".$id);
}

function removeobject($id, $name, $description, ...) {
	removeFromDBSecure("block_object_links", "object_id = ".$id);
	removeFromDBSecure("objects", "id = ".$id);
}

function bindobject($object_id, $block_id) {
	insertToDB("block_object_links", ["block_id", "object_id"], [[$block_id, $object_id]]);
}

function unbindobject($object_id, $block_id) {
	removeFromDBSecure("block_object_links", "object_id = ".$object_id." AND block_id = ".$block_id);	
}
?>