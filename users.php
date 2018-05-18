<?php
	session_start();
	include('db.php');
	include('general.php');
	authadmin();

	// Updates a single field for a user and returns a success or failure message
	function update_user($uid, $field, $field_value) {
		$query = "UPDATE user SET " . $field . " = '$field_value' WHERE id = '$uid'";
		if($result = mysql_query($query)) {
			return '<p class="center_align">Successfully updated user</p>';
		}
		else {
			return '<p class="center_align">Failed to update user</p>';
		}
	}

	// Edit User
	if($_POST['save']) {
		$fname = $_POST['username'];
		$lname = $_POST['logname'];
		$email = $_POST['email'];
		$query = "UPDATE user SET name = '$fname', log_name = '$lname', email = '$email' WHERE id = {$_POST['uid']}";

		if($result = mysql_query($query)) {
			$msg = '<p class="center_align">Successfully updated user</p>';
		}
		else {
			$msg = '<p class="center_align">Failed to update user</p>';
		}
	}

	// Delete User
	if($_POST['delete']) {
		$query = "DELETE FROM user WHERE id = {$_POST['uid']}";

		if($result = mysql_query($query)) {
			$msg = '<p class="center_align">Successfully deleted user</p>';
		}
		else {
			$msg = '<p class="center_align">Failed to delete user</p>';
		}
	}

	// Validate User
	if($_GET['valid']) {
		$msg = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="valid">
		<p class="center_align">Verify or Reject user ID ' . $_GET['valid'] . '?</p>
		<p class="center_align"><input name="valid" type="submit" value="Verify">
		<input name="invalid" type="submit" value="Reject">
		<input type="hidden" value="' . $_GET['valid'] . '" name="uid"></p>
		</form>';
	}
	if($_POST['valid']) {
		$msg = update_user($_POST['uid'], 'valid', 1);
	}
	// Request action confirmation before deleting user
	if($_POST['invalid']) {
		$msg = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="invalid">
		<p class="center_align">Are you sure that you want to delete this user? <br />This action can\'t be undone.</p>
		<p class="center_align"><input name="delete" type="submit" value="YES">
		<input name="cancel" type="submit" value="NO">
		<input type="hidden" value="' . $_POST['uid'] . '" name="uid"></p>
		</form>';
	}
	// Promote user to admin
	if($_SESSION['user_type'] == 'P' && $_POST['promote']) {
		$msg = update_user($_POST['uid'], 'type', 'A');
	}
	// Demote user to standard user
	if($_SESSION['user_type'] == 'P' && $_POST['demote']) {
		$msg = update_user($_POST['uid'], 'type', 'S');
	}

	site_head();

	if($_SESSION['user_type'] == 'P') {
		echo '<h1 class="head">Current Admins</h1>
		<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="admin">';

		$query = mysql_query("SELECT * FROM user WHERE type = 'A'");

		echo '<table width="100%">
			<tr>
				<th>ID#</th>
				<th>Full Name</th>
				<th>Login Name</th>
				<th>email</th>
			</tr>';
		while($row = mysql_fetch_assoc($query)) {
			echo '<tr>
				<td><a href="' . $_SERVER['PHP_SELF'] . '?admin=' . $row['id'] . '">' . $row['id'] . '</a></td>
				<td>' . $row['name'] . '</td>
				<td>' . $row['log_name'] . '</td>
				<td>' . $row['email'] . '</td>
			</tr>';
		}
		echo '</table>';

		if($_GET['admin'])
			echo '<input type="submit" value="DEMOTE" name="demote">
			<input type="submit" value="SAVE CHANGES" name="save">
			<input name="cancel" type="submit" value="CANCEL">
			<input type="hidden" value="' . $_GET['admin'] . '" name="uid">';

		echo '</form>';
	}

	echo '<h1 class="head">New Users</h1>';

	if($_GET['valid'] || $_POST['valid'] || $_POST['invalid'] || $_POST['delete']) {
		echo $msg;
	}
	else {
		echo '<p class="center_align">Click on a user ID to validate or reject the account.</p>';
	}

	echo '<table class="form_tb" width="100%">
		<tr>
			<th>ID#</th>
			<th>Full Name</th>
			<th>Login Name</th>
			<th>email</th>
		</tr>';

	$query = mysql_query("SELECT * FROM user WHERE valid = 0 AND type = 'S'");
	while($row = mysql_fetch_assoc($query)) {
		echo '<tr>
			<td><a href="' . $_SERVER['PHP_SELF'] . '?valid=' . $row['id'] . '">' . $row['id'] . '</a></td>
		 	<td>' . $row['name'] . '</td>
		 	<td>' . $row['log_name'] . '</td>
			<td>' . $row['email'] . '</td>
		</tr>';
	}

	echo '</table>
	<h1 class="head">Current Users</h1>';

	if($_POST['save']) echo $msg;
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="edit">';

	echo '<div class="form_tb"><table width="100%">
		<tr>
			<th>Edit</th>
			<th>Full Name</th>
			<th>Login Name</th>
			<th>email</th>
		</tr>';

	$query = mysql_query ("SELECT * FROM user WHERE valid = 1 AND type = 'S'");
	while($row = mysql_fetch_assoc($query)) {
		if($_GET['edit'] == $row['id']) {
			echo
			'<tr>
				<td><a href="' . $_SERVER['PHP_SELF'] . '?edit=' . $row['id'] . '">' . $row['id'] . '</a></td>
				<td><input name="username" type="text" value="' . $row['name'] . '" maxlength="75"></td>
				<td><input name="logname" type="text" value="' . $row['log_name'] . '" maxlength="25"></td>
				<td><input name="email" type="text" value="' . $row['email'] . '" maxlength="100"></td>
			  </tr>';
		}
		else {
			echo
			'<tr>
				<td><a href="' . $_SERVER['PHP_SELF'] . '?edit=' . $row['id'] . '">' . $row['id'] . '</a></td>
				<td>' . $row['name'] . '</td>
				<td>' . $row['log_name'] . '</td>
				<td>' . $row['email'] . '</td>
			  </tr>';
		}
	}

	echo '</table></div>';

	if($_GET['edit']){
		if($_SESSION['user_type'] == 'P') {
			echo '<input type="submit" value="PROMOTE" name="promote">';
		}
		echo '<input type="submit" value="SAVE CHANGES" name="save">
		<input name="cancel" type="submit" value="CANCEL">
		<input type="hidden" value="' . $_GET['edit'] . '" name="uid">';
	}
	echo '</form>';

	site_footer();

?>
