<?php
	include("redis.php");

	if ($_REQUEST["keys"]) {
		Redis::delete($_REQUEST["keys"]);

		header("Location:../index.php");
	}
?>