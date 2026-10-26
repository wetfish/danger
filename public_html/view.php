<?php

include("functions.php");

$Hash = trim($_GET['hash'] ?? '', '/');


$Query = Query("Select `Content` from `Posts` where `Hash`=?", $Hash);
list($Content) = $Query->fetch_row();

if($Content)
	echo stripslashes($Content);

?>
