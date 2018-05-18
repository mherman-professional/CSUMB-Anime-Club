<?php
	session_start();
	include('db.php');
	include('general.php');

	//insert comment
	if($_POST['addcomment']) {
		if($body = mysql_real_escape_string($_POST['body'])) {
			// if valid user associate comment with user
			if($isloggedin) {
				$user_id = $_SESSION['user_id'];
				$user_nm = $_SESSION['name'];
			}
			// if they are using a temp name for comment then use the temp name
			elseif($_SESSION['name']) {
				$user_nm = $_SESSION['name'];
				$user_id = 0;
			}
			// if they are not logged in or using a temp name then set comment as anonymous
			else {
				$user_nm = 'ANONYMOUS';
				$user_id = 0;
			}

			// make sure $_GET is an interger to protect against malicious SQL injection
			if(isset($_GET['img'])) {
				$img_id = validate_get($_GET['img']);
			}
			$query = "INSERT INTO comment (img_id, user_id, user_nm, body, flag) VALUES ('$img_id', '$user_id', '$user_nm', '$body', 0)";
			mysql_query($query);
		}
		else {
			$msg = '<p>No comment was entered.</p>';
		}
	}

	site_head();

	// make sure $_GET is an interger to protect against malicious SQL injection
	if(isset($_GET['nav'])) {
		$nav = validate_get($_GET['nav']);
	}
	// display the selected image collection
	if(isset($nav) && $nav !== false) {
		$content = mysql_query("SELECT * FROM collection WHERE  id = " . $nav);
		$row = mysql_fetch_assoc($content);

		echo '<h1 class="head">' . $row['name'] . '</h1>
		<p>' . $row['coll_desc'] . '</p>
		<h1 class="head">images</h1>';

		$query = mysql_query("SELECT * FROM image WHERE collect_id = " . $nav . " AND apr = 1 AND flag = 0");

		while($row = mysql_fetch_assoc($query)) {
			echo '<div class="thumbnail"><a href="collection.php?img=' . $row['id'] . '&col=' . $nav . '"><img src="userimg/THUMB' . $row['imgurl'] . '"></a></div>';
		}
	}
	// make sure $_GET is an interger to protect against malicious SQL injection
	if(isset($_GET['img'])) {
		$img = validate_get($_GET['img']);
	}
	if(isset($img) && $img !== false) {
		$query = mysql_query("SELECT * FROM image WHERE id = " . $img . " AND apr = 1 AND flag = 0");
		$row = mysql_fetch_assoc($query);

		echo '<h1 class="head">' . $row['name'] . '</h1>
		<img src="userimg/' . $row['imgurl'] . '">
		<p>' . $row['img_desc'] . '</p>
		<h1 class="head">Comments</h1>' . $msg;

		// display comments for selected image
		$query = mysql_query("SELECT * FROM comment WHERE img_id = " . $img . " AND flag = 0 ORDER BY id DESC");
		while($row = mysql_fetch_assoc($query)) {
			echo '<p>' . $row['body'] . '</p><p class="right_align">signed: ' . $row['user_nm'] . '</p><hr />';
		}

		// make sure $_GET is an interger to protect against malicious SQL injection
		if(isset($_GET['col'])) {
			$col = validate_get($_GET['col']);
		}
		// generate form for entering a comment
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?img=' . $img . '&col=' . $col . '" method="post" name="addcomment"><p class="center_align">
		<textarea name="body" cols="50" rows="5" maxlength="2000"></textarea>
		<p><input name="addcomment" type="submit" value="POST COMMENT"></p>
		</p></form>';
	}

	site_footer();

?>
