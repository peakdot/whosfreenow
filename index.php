<?php
session_start();

if(!isset($_SESSION["user"])) {
	//header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>WAAAAAAHAHAHA!?!?!?</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<meta name="google-signin-client_id" content="998025862112-kbdis3t6ihqi78q1r0n8tn3l5r756h1i.apps.googleusercontent.com">
</head>
<body>	
	<nav>
		<a href="index.php">Who's free now?</a>
		<input type="text" name="searchfield">
		<button>Search</button>
		<button>Notifications</button>
		<button>My Profile</button>
	</nav>
	<div id=main-container>
		<div id="sidenav">
			<h4>Now</h4>
			<div class="now">
				<p>My subject 1</p>
			</div>
			<h4>Groups</h4>
			<div id="groups">
				<ul>
					<li>Group 1 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 2 <button>See Calendar</button><button>Expand</button>
						<ul>
							<li>Group 3 <button>See Calendar</button><button>Expand</button></li>
							<li>Group 4 <button>See Calendar</button><button>Expand</button></li>
							<li>Group 5 <button>See Calendar</button><button>Expand</button></li>
						</ul>
					</li>
					<li>Group 3 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 4 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 5 <button>See Calendar</button><button>Expand</button></li>
				</ul>
				<ul>
					<li>Group 1 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 2 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 3 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 4 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 5 <button>See Calendar</button><button>Expand</button></li>
				</ul>
				<ul>
					<li>Group 1 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 2 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 3 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 4 <button>See Calendar</button><button>Expand</button></li>
					<li>Group 5 <button>See Calendar</button><button>Expand</button></li>
				</ul>
			</div>
		</div>
		<div id="content">
			<div id="content-head">
				<time>November, 2017</time>
				<select>
					<option>Day</option>
					<option>3 days</option>
					<option>Week</option>
					<option>Month</option>
					<option>Year</option>
				</select>
				<br>
				<button>See people</button>
			</div>
			<div class="block-form">
				<form id="block-form" method = "POST">
					<label for="name">Title</label>
					<input type="text" name="name"><br>
					<label for="desc">Tags</label>
					<input type="text" name="desc"><br>
					<label for="desc">Description</label>
					<input type="text" name="desc"><br>
					<label for="repeat">Repeat</label>
					<input type="checkbox" name="repeat">
					<select name="repeatday">
						<option value="1">Daily</option>
						<option value="-1">Weekly</option>
						<option value="-8">Monthly</option>
						<option value="-9">Year</option>
						<option value="0">Custom</option>
					</select><br>
					<label for="date1">Date</label>
					<input type="date" name="date1">
					<input type="date" name="date2"><br>
					<label for="allday">All day</label>
					<input type="checkbox" name="allday">
					<label for="hour1">Time</label>
					<input type="number" name="hour1" min="0" max="23">:
					<input type="number" name="minute1" min="0" max="59"> - 
					<input type="number" name="hour2" min="0" max="23">:
					<input type="number" name="minute2" min="0" max="59"><br>
					<input type="submit" name="submit">
				</form>
			</div>
			<div id="content-body">
				<div class="times">
					<div id="regulator"></div>
					<div>4am</div>
					<div>5am</div>
					<div>6am</div>
					<div>7am</div>
					<div>8am</div>
					<div>9am</div>
					<div>10am</div>
					<div>11am</div>
					<div>12am</div>
					<div>1pm</div>
					<div>2pm</div>
					<div>3pm</div>
					<div>4pm</div>
					<div>5pm</div>
					<div>6pm</div>
					<div>7pm</div>
					<div>8pm</div>
					<div>9pm</div>
					<div>10pm</div>
					<div>11pm</div>
					<div>12pm</div>
					<div>1pm</div>
					<div>2pm</div>
					<div>3pm</div>
				</div>
				<div class="timetable-grid">
					<table class="head">
						<tr class="date">
						</tr>
						<tr class="allday">
						</tr>
					</table>
					<table class="body">
						<tbody>
						</tbody>
					</table>
					<table id="block-container" class="block-container unselectable">
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div id="notf-container" class="unselectable">
	</div>
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	<script src="https://apis.google.com/js/platform.js" async defer></script>
</body>
</html>