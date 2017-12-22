//Groups: {id1: {hash: "", groupId: 1, blocks: [{blockId: "", startTime: "", ...}, {} ...]}, {} ...}

var MINIMUM_BLOCK_WIDTH = 120;

var blocks = null;
var groupIDs = [{id: 1, childs: [2, 3, 4]}];
var groupHashes = [{id: 1, childs: [2, 3, 4], hash: "asdfhalkjdfhlakj"}];
var localBlocks = JSON.parse(localStorage.blocks);
var ajaxreqs = [];
var viewingDates = [];
var headers = $(".timetable-grid .head");
var block_container = $(".block_container");

var today = new Date();

getUserGroupIDs(function(){
	if(ajaxreqs.length == 0) {
		getGroupHash(function(){
			if(ajaxreqs.length == 0) {
				getGroupBlocks(function(){
					if(ajaxreqs.length == 0) {
						placeBlocks(blocks);
					}
				});
			}			
		});
	}
});

function setTimetableGrid(date, col_num){
	var tommorow = date;
	var columns = "";
	var col_with_ca = "";

	for (var i = 0; i < col_num; i++) {
		columns += "<td></td>";
		col_with_ca += '<td><div class="block-creation-area"></div></td>';
	}

	headers.children(".allday").append(columns);
	block_container.append('<tr>' + col_with_ca + '</tr>');

	var timegrid = $(".timetable-grid .body tbody");
	for (var i = 0; i < 24; i++) {
		timegrid.append("<tr>" + columns + "</tr>");
	}


	for (var i = 0; i < col_num; i++) {
		headers.children(".date").append("<td>" + tommorow.getDate() + ", " + getWeekdayName(tommorow.getDay()) + "</td>");
		viewingDates.push(tommorow);
		tommorow.setDate(tommorow.getDate + 1);
	}
}

function getWeekdayName(num) {
	switch(num) {
		case 0: return "Monday";
		case 1: return "Tuesday";
		case 2: return "Wednesday";
		case 3: return "Thursday";
		case 4: return "Friday";
		case 5: return "Saturday";
		case 6: return "Sunday";
		default: return "Dunno";
	}
}

function placeBlocks(temp_blocks) {
	viewingDates;
	var tds = block_container.find("td");

	// Uses blocks and places them
	for (var i = 0; i < viewingDates.length; i++) {
		for (var j = 0; j < temp_blocks.length; j++) {
			if(checkDatesForBlock(temp_blocks[j], viewingDates[i])) {
				block_container.find("td")[i].append(createBlockElement(temp_blocks[j]));
			}
		}
	}
}

function checkDatesForBlock(block_data, date) {
	var startDay = new Date(block_data.startDay);
	var endDay = new Date(block_data.endDay);
	var repeatDay = new Date(block_data.repeatDay);
	if(startDay <= date && endDay >= date && (date.getTime() - startDay.getTime()) / 86400000 % repeatDay == 0) {
		return true;
	} else {
		return false;
	}
}

function createBlockElement(block_data){
	var startTime = block_data.startTime.split(":");
	var duration = parseInt(block_data.duration);
	var endTime = parseInt(startTime[0]) * 60 + parseInt(startTime[1]) + duration;
	endTime = (endTime / 60) % 24 + ":" + endTime % 60;
	var newBlock = $('<div class = "block""><b><span>' + block_data.name + '</span></b><span>' + block_data.startTime + ' - ' + endTime + '</span></div>');
	newBlock.css("top", parseInt(startTime[0]) * 60 + startTime[1]);
	newBlock.css("height", duration);
	newBlock.mousedown(moveBlock);

	return newBlock;
}

function getUserGroupIDs(callback = function(){}){
	var req = $.ajax({
		type: "GET",
		url: "ajax.php",
		data: "usergroups",
		success: function(msg) {
			groupIDs.push(JSON.parse(msg));

			var index = ajaxreqs.indexOf(this);			
			if (index > -1) {
				array.splice(index, 1);
			}

			callback();
		},
		error: function() {
			alert("An error occured while updating. Try again in a while");
		}
	});

	ajaxreqs.push(req);
}

function getGroupsHash(callback = function(){}) {
	for (var i = 0; i < groupIDs.length; i++) {
		getGroupHash(groupIDs[i], callback);
	}
}

function getBlocks(callback = function(){}) {
	blocks = null;

	while(ajaxreqs.length > 0) {
		ajaxreqs.pop().abort();
	}

	if (typeof(Storage) !== "undefined") {
		if (localStorage.blocks !== "undefined") {
			localStorage.blocks = "[]";
		}

		for (var i = 0; i < groupHashes.length; i++) {
			var index = "id" + groupHashes[i].id;
			if (localBlocks[index] !== "undefined") {
				if (groupHashes[i].hash == localBlocks[index].hash) {
					blocks.push(localBlocks[index]);
				} else {
					getGroupBlocks(groupHashes[i].id, callback);
				}
			} 
		}

	} else {
		for (var i = 0; i < groupHashes.length; i++) {
			getGroupBlocks(groupHashes[i].id, callback);
		}
	}
}

//Receive hash of the group 
function getGroupHash(group_id, callback = function(){}) {
	var req = $.ajax({
		type: "GET",
		url: "ajax.php",
		data: "group_id=" + group_id + "&type=blocks",
		success: function(msg) {
			groupHashes.push(JSON.parse(msg));

			var index = ajaxreqs.indexOf(this);			
			if (index > -1) {
				array.splice(index, 1);
			}

			callback();
		},
		error: function() {
			alert("An error occured while updating. Try again in a while");
		}
	});

	ajaxreqs.push(req);
}

//Receive blocks referred to group id 
function getGroupBlocks(group_id, callback = function(){}) {
	var req = $.ajax({
		type: "GET",
		url: "ajax.php",
		data: "group_id=" + group_id + "&type=blocks",
		success: function(msg) {
			blocks.push(JSON.parse(msg));

			var index = ajaxreqs.indexOf(this);			
			if (index > -1) {
				array.splice(index, 1);
			}

			callback();
		},
		error: function() {
			alert("An error occured while updating. Try again in a while");
		}
	});

	ajaxreqs.push(req);
}