<?php
	session_start();
	include('db.php');
	include('general.php');

	site_head();

?>

<h1 class="head">Join the fun!</h1>

<?php

	if($_POST['submit']) {
		$query = mysql_query ("SELECT log_name FROM user WHERE log_name = '" . $_POST['lname'] . "' ");
		$query2 = mysql_query ("SELECT email FROM user WHERE lower(email) = lower('" . $_POST['email'] . "') ");

		if(!$_POST['fname'] || !$_POST['lname'] || !$_POST['password'] || !$_POST['email'])
			$msg = 'One or more fields where not filled in. Please fill in all fields and try again.';
		elseif(mysql_num_rows($query2) == 1)
			$msg = 'This email address is already taken. If you cannot log in your account may not have been activated yet. If you have forgotten your password please <a href="#">click here</a>.';
		elseif(mysql_num_rows($query) == 1) {
			$result = mysql_fetch_assoc ($query);
			$msg = 'The user name "' . $result['log_name'] . '" is already taken. Please try another.';
		}
		else {
			if($_POST['password'] == $_POST['password2']) {
				$fname = $_POST['fname'];
				$lname = $_POST['lname'];
				$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$email = $_POST['email'];

				$query = "INSERT INTO user (name, log_name, password, email, valid, type) VALUES ('$fname', '$lname', '$password', '$email', 0, 'S')";
				if($result = mysql_query($query)) {
					$query = mysql_query("SELECT id FROM user WHERE log_name = '$lname' ");
					$result = mysql_fetch_assoc ($query);
					$query2 = mysql_query("SELECT email FROM user WHERE type = 'P' ");
					$result2 = mysql_fetch_assoc ($query2);
					$msg = 'Account successfully created. Please send the confirmation number "' . $result['id'] . '" to the site admin: ' . $result2['email'] . '.';
				}
				else {
					$msg = 'Failed to insert user';
				}
			}
			else {
				$msg = 'Passwords entered do not match. Please try again.';
			}
		}
	}


	echo '<p>Please fill in all of the below fields. Once you sibmit your information you will recieve a confirmation number and email address. Please use your school email acount to send a message with the confrimation number to the address provided.</p>
	<p>' . $msg . '</p>

	<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
	<table width="100%" cellpadding="3px" cellspacing="0px" class="form_tb">
		<tr bgcolor="#DDA091">
			<td width="50%"><div class="right_align">Full Name:</div><span class="note">*please provide full name for account confirmation purposes</span></td>
			<td width="50%"><input name="fname" type="text" maxlength="75"></td>
		</tr>
		<tr>
			<td><div class="right_align">User Name:</div><span class="note">*used to log in and to sign comments/images</span></td>
			<td><input name="lname" type="text" maxlength="25"></td>
		</tr>
		<tr bgcolor="#DDA091">
			<td class="right_align">Password:</td>
			<td><input name="password" type="password" maxlength="25"></td>
		</tr>
		<tr>
			<td class="right_align">Confrim Password:</td>
			<td><input name="password2" type="password" maxlength="25"></td>
		</tr>
		<tr bgcolor="#DDA091">
			<td><div class="right_align">Email Address:</div><span class="note">*used for password reset if password is lost</span></td>
			<td><input name="email" type="text" maxlength="100"></td>
		</tr>
		<tr>
			<td colspan="2" class="right_align"><input name="submit" type="submit" value="CREATE ACCOUNT"></td>
		</tr>
	</table>
	</form>';

	site_footer();

?>
