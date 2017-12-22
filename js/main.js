$(function() {
	var MINIMUM_BLOCK_WIDTH = 200;

	var doc = $(document);
	var jwindow = $(window);
	var body = $(document.body);
	var block_container = $(".block-container");
	var status = 0;
	var viewingDates = [];
	var headers = $(".timetable-grid .head");
	var block_container = $(".block-container");
	var bc_width = parseInt(getComputedStyle(document.getElementById("block-container"), null).getPropertyValue("width"));

	var today = new Date();

	var user_status = 0;
	/*
	0 - doing nothing
	1 - creating block
	2 - 
	*/

	setTimetableGrid(today, Math.floor(bc_width / MINIMUM_BLOCK_WIDTH));

	$(".block-creation-area").mousedown(createBlock);
	$(".block").mousedown(moveBlock);

	function setTimetableGrid(date, col_num){
		var tommorow = date;
		var columns = "";
		var col_with_ca = "";
		var timegrid = $(".timetable-grid .body tbody");

		for (var i = 0; i < col_num; i++) {
			columns += "<td></td>";
			col_with_ca += '<td><div class="block-creation-area" num="' + i + '"></div></td>';
		}

		viewingDates = [];
		timegrid.html("");
		headers.find(".allday").html("");
		headers.find(".date").html("");
		block_container.find("tbody").html("");

		headers.find(".allday").append(columns);
		block_container.find("tbody").append('<tr>' + col_with_ca + '</tr>');

		for (var i = 0; i < 24; i++) {
			timegrid.append("<tr>" + columns + "</tr>");
		}

		for (var i = 0; i < col_num; i++) {
			headers.find(".date").append("<td>" + tommorow.getDate() + ", " + getWeekdayName(tommorow.getDay()) + "</td>");
			viewingDates.push(new Date(tommorow.getTime()));
			tommorow.setDate(tommorow.getDate() + 1);
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

	function createBlock(event) {
		event.preventDefault();
		var jthis = $(this);
		var y =  event.clientY - block_container.offset().top + doc.scrollTop();

		// Add 15px step
		y -= y % 15;

		var newBlock = $('<div class = "block" style = "top: ' + y + 'px; height: ' + 30 + 'px; background-color: blue;"><b><span>No title</span></b> <span><span class="time1">' + Math.floor(y / 60 + 5) + ':' + ("0" + (y % 60)).slice(-2) + '</span>-<span class="time2">' + Math.floor((y + 30) / 60 + 5) + ':' + ("0" + ((y + 30) % 60)).slice(-2) + '</span></span></div>');
		var scale_area = $('<div class="scale-area"></div>');

		scale_area.mousedown(function(event) {
			scalingBlock(newBlock, "scale_area");
			event.stopPropagation();
		});

		newBlock.append(scale_area);
		newBlock.mousedown(moveBlock);

		jthis.parent().append(newBlock);

		scalingBlock(newBlock, "createBlock");
	}

	function moveBlock(event) {
		event.preventDefault();
		var jthis = $(this);

		var y = parseInt(jthis.css("top"));
		var scroll = doc.scrollTop();
		var py =  event.clientY - block_container.offset().top + scroll;

		// Add 15px step
		py -= py % 15;

		var mouse_offset = py - y;
		var tds = block_container.find("td");
		var time1 = jthis.find(".time1");
		var time2 = jthis.find(".time2");

		block_container.find("td").mouseenter(function (event){
			event.preventDefault();
			jthis.appendTo($(this));
		});

		doc.mouseup(function(event) {
			tds.unbind("mouseenter");
			doc.unbind("mouseup");
			doc.unbind("mousemove");
			doc.unbind("scroll");
			event.preventDefault();
		});

		doc.mousemove(function(event) {
			py = event.clientY - block_container.offset().top + scroll - mouse_offset;

			// Add 15px step
			py -= py % 15;

			if(py < 0){
				py = 0;
			} else if(py > block_container.height() - jthis.height()){
				py = block_container.height() - jthis.height();
			}

			jthis.css("top", py);
			time1.html(getStrTime(py));
			time2.html(getStrTime(py + jthis.height()));

			event.preventDefault();
		});

		doc.scroll(function() {
			py -= scroll;
			scroll = doc.scrollTop();
			py += scroll;

			if(py < 0){
				py = 0;
			} else if(py > block_container.height() - jthis.height()){
				py = block_container.height() - jthis.height();
			}
			jthis.css('top', py);
		});

	}

	function scalingBlock(block_element, caller = null) {
		var y = parseInt(block_element.css("top"));
		var py = y;
		var scroll = doc.scrollTop();
		var time1 = block_element.find(".time1");
		var time2 = block_element.find(".time2");

		body.css("cursor", "s-resize");
		block_element.css("cursor", "s-resize");

		doc.mousemove(function(event) {
			event.preventDefault();
			py = event.clientY - block_container.offset().top + scroll;

			// Add 15px step
			py -= py % 15;

			if(py > y)	{
				if(py > block_container.height()){
					py = block_container.height();
				}
				block_element.css('height', py - y);
				block_element.css('top', y);
				time1.html(getStrTime(y));
				time2.html(getStrTime(py));
			} else {
				if(py < 0){
					py = 0;
				}
				block_element.css('height', y - py);
				block_element.css('top', py);
				time1.html(getStrTime(py));
				time2.html(getStrTime(y));
			}

		});

		doc.mouseup(function(event) {
			event.preventDefault();
			body.css("cursor", "initial");
			block_element.css("cursor", "pointer");
			doc.unbind("mousemove");
			doc.unbind("scroll");
			doc.unbind("mouseup");
			if (caller == "createBlock") {
				popupblockform(block_element);
			}
		});

		doc.scroll(function() {
			py -= scroll;
			scroll = doc.scrollTop();
			py += scroll;

			// Add 15px step
			py -= py % 15;

			if(py > y)	{
				if(py > block_container.height()){
					py = block_container.height();
				}
				block_element.css('height', py - y);
				block_element.css('top', y);
			} else {
				if(py < 0){
					py = 0;
				}
				block_element.css('height', y - py);
				block_element.css('top', py);
			}
		});
	}

	function getStrTime(position) {
		var time = Math.floor(position / 60 + 4) % 24 + ":" + ("0" + (position % 60)).slice(-2);
		return time;
	}

	function getblockTime(block_top, block_height) {
		var time1 = calcTime(block_top);
		var time2 = calcTime(block_top + block_height);
		return [time1, time2];
	}

	function calcTime(position) {
		var time = {hour: Math.floor(position / 60 + 4) % 24, min: position % 60};
		return time;
	}

	function popupblockform(block_element){
		var block_form_cont = $(".block-form");
		var block_form = $("#block-form");
		var height = block_element.height();
		var offset = block_element.offset();
		var width = block_element.width();
		var bfheight = block_form_cont.height();
		var bfwidth = block_form_cont.width();
		var special = 0;

		// Get block start time, end time and date
		var times = getblockTime(parseInt(block_element.css("top")), height);
		var date = viewingDates[parseInt(block_element.siblings(".block-creation-area").attr("num"))];
		if(times[0].hour >= 0 && times[0].hour < 4) {
			date = new Date(date.getTime() + 86400000);
		}

		// Autofill clock and date area in form
		block_form.children("input[name=hour1]").val(times[0].hour);
		block_form.children("input[name=hour2]").val(times[1].hour);
		block_form.children("input[name=minute1]").val(times[0].min);
		block_form.children("input[name=minute2]").val(times[1].min);
		block_form.children("input[name=date1]").val(date.toISOString().slice(0, 10));
		console.log(viewingDates);

		// Placing block form;
		// Calculating block form x position. Default is block's right side.
		if(doc.width() - offset.left - width > bfwidth) {
			block_form_cont.css("left", offset.left + width + "px");
		} else if(offset.left - bfwidth >= 0){
			block_form_cont.css("left", offset.left - bfwidth + "px");			
		} else {
			block_form_cont.css("left", doc.width() - bfwidth+ "px");
			special = 50;
		}

		//Calculating block form y position. Default is block's top;
		if(offset.top + height / 2 - bfheight / 2 + special < doc.scrollTop()) {
			block_form_cont.css("top", doc.scrollTop() + special + "px");
		} else if(offset.top + height / 2 + bfheight / 2  + special >= window.innerHeight + doc.scrollTop()){
			block_form_cont.css("top", window.innerHeight - bfheight + doc.scrollTop() + special + "px");
		} else {
			block_form_cont.css("top", offset.top + height / 2 - bfheight / 2 + special + "px");
		}

		block_form_cont.css("visibility", "visible");
	}

	$(".block_form").submit(function(event) {

        // Prevent default posting of form - put here to work in case of errors
        event.preventDefault();

        // setup some local variables
        var $form = $(this);

        // Let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");

        // Serialize the data in the form
        var serializedData = $form.serialize();

        // Let's disable the inputs for the duration of the Ajax request.
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

        // Fire off the request to /blockmodel.php
        request = $.ajax({
        	url: "blockmodel.php",
        	type: "post",
        	data: serializedData
        });

        // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
        	if(response == "0"){
        		popupnotf("Block added.");
        	} else { 
        		popupnotf("Can't add block.");
        	}
        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error("Connection Failed");
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
        });
    });

    function popupnotf(text) {
    	var div = $('<div class="notf">' + text + '</div>');
    	div.click(function() {
    		div.fadeOut(300, function() {
    			div.remove();
    		});
    	});
    	setTimeout(function(){
    		div.fadeOut(300, function() {
    			div.remove();
    		});
    	}, 5000);
    	$('#notf-container').append(div);
    }

	function onSignIn(googleUser) {
		var profile = googleUser.getBasicProfile();
		var id_token = googleUser.getAuthResponse().id_token;

		console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
		console.log('Name: ' + profile.getName());
		console.log('Image URL: ' + profile.getImageUrl());
		console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.

		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'usermodel.php');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
			popupnotf(xhr.responseText);
			
		};
		xhr.send('idtoken=' + id_token);
	}

	function signOut() {
		var auth2 = gapi.auth2.getAuthInstance();
		auth2.signOut().then(function () {
			console.log('User signed out.');
		});
	}

});