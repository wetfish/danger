<?php

// You gotta make a config file!
require("config.php");

// mysqli throws an exception when something goes wrong, so a failed query
// stops the page instead of showing an empty one
$Database = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

// The charset the old server used by default, so old posts read back as written
$Database->set_charset('utf8mb4');

// Runs a query with each ? replaced by the next value, and returns its
// result, or false for queries that return no rows
function Query($SQL, ...$Values)
{
	global $Database;

	$Statement = $Database->prepare($SQL);

	if($Values)
		$Statement->bind_param(str_repeat('s', count($Values)), ...$Values);

	$Statement->execute();
	return $Statement->get_result();
}

?>
