<?php
	session_start();
	include('db.php');

	// user login
	if($_POST['login']) {
		$password = $_POST['password'];
		$query = mysql_query("SELECT password FROM user WHERE log_name = '" . $_POST['name'] . "' AND valid = 1");
		$hash = mysql_fetch_assoc($query);
		if (password_verify( $password , $hash['password'])) {
			$query = mysql_query("SELECT * FROM user WHERE log_name = '" . $_POST['name'] . "' AND valid = 1");
			$result = mysql_fetch_assoc($query);
			$_SESSION['user_type'] = $result['type'];
			$_SESSION['user_id'] = $result['id'];
			$_SESSION['name'] = $result['log_name'];
			$_SESSION['user_valid'] = $result['valid'];
		}
		else {
			$error_login = 'you failed to authenticate';
		}
	}

	if($_POST['temp_login']) {
		$query = mysql_query ("SELECT log_name FROM user WHERE log_name = '" . $_POST['name'] . "' AND valid = 1");
		// don't allow users to use the same temp user name as an existing registred user
		if (mysql_num_rows($query) == 1)
			$error_login = 'The name "' . $_POST['name'] . '" is being used by a registered user. Please try another.';
		else
			$_SESSION['name'] = $_POST['name'];
	}

	if($_GET['err'] == 1)
		$error_login = 'Please log in';
	if($_GET['err'] == 2)
		$error_login = 'You are not logged in as an admin';

	include('general.php');

	site_head();

	echo "\t" . '<h1 class="head">welcome ' . $_SESSION['name'] . '</h1>';

?>

	<p>Welcome to the CSUMB Anime Club Website. This site serves as a photo album for club activities. Club members can also post their own photos from anime related events they attend throughout the year. They can even upload their own fan art for the entire Club to admire. Please post ONLY original work. We all love to see cool fan art from our favorite series but that is not the purpose of this site. We also ask that you limit photos to pictures that you and your friends took at the event or pictures that others took of your amazing cosplay and skits.</p>
	<p>If you have an acount please sign in on this page.</p>


<?php

	// if user is not logged in allow them to log in with their account or with a temporary user name or create an account
	if (!$isloggedin) {
		echo "\t" . '<h1 class="head">login</h1>';

		if($error_login)
			echo $error_login;

		echo "\n\t" . '<form action="index.php" method="post"  name="login">
		<table width="100%" border="0">
			<tr>
				<td class="right_align">User Name</td>
				<td><input name="name" type="text" maxlength="25"></td>
			</tr>
			<tr>
				<td class="right_align">Password</td>
				<td><input name="password" type="password" maxlength="25"></td>
			</tr>
			<tr>
				<td colspan="2" class="right_align"><input name="login" type="submit" value="LOGIN"></td>
			</tr>
		</table>
	</form>

	<p class="center_align">If you are a student at CSUMB and a member of the Anime Club join us by creating a <a href="register.php">new account!</a></p>

	<h1 class="head">temp login</h1>

	<p>Or create a temporary signature name for your comments. This name will only be saved for the current session and will need to be re-entered the next time you visit.</p>

		<form action="index.php" method="post" name="temp_login">
		<table width="100%" border="0">
			<tr>
				<td class="right_align">Temporary Name</td>
				<td><input name="name" type="text" maxlength="25"></td>
			</tr>
			<tr>
				<td colspan="2" class="right_align"><input name="temp_login" type="submit"></td>
			</tr>
		</table>
		</form>' . "\n";
	}
	else {
		echo '<h1 class="head">logout</h1>';
		if($error_login)
			echo $error_login;
		echo '<p class="center_align">click here to (<a href="index.php?logout=1">logout</a>)</p>';
	}

	site_footer();

?>
