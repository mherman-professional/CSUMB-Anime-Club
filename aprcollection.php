<?php
	/*
	* This page allows admins to approve or reject image collections queued by the community
	*/

	// start session and verify that the user is an admin
	session_start();
	include('db.php');
	include('general.php');
	authadmin();

	// approve collection
	if($_POST['valid']) {
		$coll_query = "UPDATE collection SET apr = 1 WHERE id = {$_POST['cid']}";
		$img_query = "UPDATE image SET apr = 1 WHERE collect_id = {$_POST['cid']}";

		if($result = mysql_query($coll_query)) {
			$msg = '<p class="center_align">Successfully updated collection</p>';
		}
		else {
			$msg = '<p class="center_align">Failed to update collection</p>';
		}

		if($result = mysql_query($img_query)) {
			$msg = $msg . '<p class="center_align">Successfully updated images</p>';
		}
		else {
			$msg = $msg . '<p class="center_align">Failed to update images</p>';
		}
	}
	// allow user to verify action before deleting a collection
	else if($_POST['invalid']) {
		$msg = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="invalid">
		<p class="center_align">Are you sure that you want to delete this collection? <br />This action can\'t be undone.</p>
		<p class="center_align"><input name="delete" type="submit" value="YES">
		<input name="cancel" type="submit" value="NO">
		<input type="hidden" value="' . $_POST['cid'] . '" name="cid"></p>
		</form>';
	}
	// if approve or reject has not yet been selected then get the collection number and present approve or reject options
	else {
		if(isset($_GET['col'])) {
			$col = validate_get($_GET['col']);
		}
		if (isset($_GET['col']) && $col !== false) {
			$msg = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="valid">
			<p class="center_align">Approve or Reject this collection?</p>
			<p class="center_align"><input name="valid" type="submit" value="Approve">
			<input name="invalid" type="submit" value="Reject">
			<input type="hidden" value="' . $col . '" name="cid"></p>
			</form>';
		}
	}

	function display_image_collection($col, $msg) {
		$collection = mysql_query("SELECT * FROM collection WHERE id = " . $col);
		$row = mysql_fetch_assoc($collection);

		echo '<h1 class="head">' . $row['name'] . '</h1>
		<p>' . $row['coll_desc'] . '</p>';

		$img_query = "SELECT * FROM image WHERE collect_id = " . $col;

		// display thumbnail preview of the images
		$img_thumb = mysql_query($img_query);
		while($row = mysql_fetch_assoc($img_thumb)) {
			echo '<div class="thumbnail"><img src="userimg/THUMB' . $row['imgurl'] . '"></div>';
		}
		echo $msg;

		// display full size images with links
		$img_full = mysql_query($img_query);
		while($row = mysql_fetch_assoc($img_full)) {
			echo '<h1 class="head">' . $row['name'] . '</h1>
			<img src="userimg/' . $row['imgurl'] . '">
			<p>' . $row['img_desc'] . '</p>';
		}
	}

	site_head();

	// make sure $_GET is an interger to protect against malicious SQL injection
	if(isset($_GET['col'])) {
		$col = validate_get($_GET['col']);
	}
	// display the selected image collection and Approve/Reject option
	if(isset($col) && $col !== false) {
		display_image_collection($col, $msg);
	}
	// display the selected image collection and approved result or confirm delete option
	else if($_POST['valid'] || $_POST['invalid']) {
		$col = $_POST['cid'];
		display_image_collection($col, $msg);
	}
	// display list of image collections waiting approval
	else {
		$collection = mysql_query("SELECT * FROM collection WHERE apr = 0");

		while($row = mysql_fetch_assoc($collection)) {
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?col=' . $row['id'] . '"><p>' . $row['name'] . '</p></a>';
		}
	}

	site_footer();

?>
