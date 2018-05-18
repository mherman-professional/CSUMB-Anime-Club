<?php
	session_start();
	include('db.php');
	include('general.php');
	authuser();

	// retain collection name and description when form rebuilds after user selects number of images to add or saves uploaded images
	if($_POST['num_images'] || $_POST['addimages']) {
		$coll_name = $_POST['coll_name'];
		$coll_desc = $_POST['coll_desc'];
	}

	// add images
	if($_POST['addimages']) {
		$num = $_POST['num'];
		$error = FALSE;
		$err_array = Array();
		$name_array = Array();
		$desc_array = Array();
		$image_array = Array();

		for($count = 1; $count <= $num; $count++) {
			if($_FILES['img' . $count]) {
				$size = $_FILES['img' . $count]['size'];
				// limit file size
				if($size > 2000000 || $size == o) {
					$error = TRUE;
					$err_array[] = $count;
				}
				else {
					if($image_array[$count]) {
						if (isset($image_array[$count]) && file_exists($image_array[$count]) && is_file($image_array[$count])) {
							unlink($image_array[$count]);
						}
					}

					$dir = 'userimg/';
					$file = $_FILES['img' . $count]['name'];
					$fullpath = $dir . $file . 'temp';
					$image_array[$count] = $fullpath;
					move_uploaded_file($_FILES['img' . $count]['tmp_name'], $fullpath);
					$image_array['img' . $count . '_name'] = $_FILES['img' . $count]['name'];
				}
			}
			else {
				$image_array[$count] = $_POST['img' . $count] ;
				$image_array['img' . $count . '_name'] = $_POST['img' . $count . '_name'] ;
			}

			$name_array[$count] = $_POST['img' . $count . '_name'];
			$desc_array[$count] = $_POST['img' . $count . '_desc'];
		}

		if(!$error) {
			// auto approve image collections uploaded by admins but require approval for "standard" users
			$apr = ($_SESSION['user_type'] == 'S')? 0 : 1;

			$user_id = $_SESSION['user_id'];
			$coll_insert = "INSERT INTO collection (user_id, name, coll_desc, apr) VALUES ('$user_id', '$coll_name', '$coll_desc', '$apr')";
			if($result = mysql_query($coll_insert)) {
				$msg1 = '<font color="green"><strong>Successfully inserted new collection</strong></font><br>';
			}
			else {
				$msg1 = '<font color="red"><strong>Failed to insert new collection</strong></font><br>';
			}
			$coll_id = mysql_insert_id();

			for ($count = 1; $count <= $num; $count++) {
				$name = $_POST['img' . $count . '_name'];
				$desc = $_POST['img' . $count . '_desc'];

				// enter image information into the database
				$img_insert = "INSERT INTO image (collect_id, user_id, name, img_desc, apr, flag) VALUES ('$coll_id', '$user_id', '$name', '$desc', '$apr', 0)";
				if($result = mysql_query($img_insert)) {
					if(!isset($msg2)) {
						$msg2 = '<font color="green"><strong>Successfully inserted 1 image</strong></font><br>';
						$success_count = 1;
					}
					else {
						$success_count++;
						$msg2 = '<font color="green"><strong>Successfully inserted ' . $success_count . ' images</strong></font><br>';
					}
				}
				else {
					if(!isset($msg3))
						$msg3 = '<font color="red"><strong>Failed to insert image: ' . $name . '</strong></font><br>';
					else
						$msg3 .= '<font color="red"><strong>Failed to insert image: ' . $name . '</strong></font><br>';
				}

				$id = mysql_insert_id();
				$dir = 'userimg/';
				$file = $id . $image_array['img' . $count . '_name'];
				$fullpath = $dir . $file;
				$thumbsize = 150;
				$fullsize = 470;

				if(rename($image_array[$count], $fullpath)) {
					//copy permanent with full size
					list($width, $height) = getimagesize($fullpath);
					$new_width = $fullsize;
					$new_height = $height / ($width / $fullsize);

					$image_p = imagecreatetruecolor($new_width, $new_height);
					$image = imagecreatefromjpeg($fullpath);

					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagejpeg($image_p, $fullpath, 60);

					//copy to thumb size
					list($width, $height) = getimagesize($fullpath);
					$new_width = $thumbsize;
					$new_height = $height / ($width / $thumbsize);

					$image_p = imagecreatetruecolor($new_width, $new_height);
					$image = imagecreatefromjpeg($fullpath);

					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagejpeg($image_p, $dir . 'THUMB' . $file, 60);
				}

				$img_update = "UPDATE image SET imgurl='$file' WHERE id=$id";
				if($result = mysql_query($img_update)) {
					echo 'item updated';
				}

				if(isset($image_array[$count]) && file_exists($image_array[$count]) && is_file($image_array[$count])) {
					unlink($image_array[$count]);
				}
			}
		}
	}

	function checkerror($id) {
		global $err_array;
		foreach($err_array as $key => $val) {
			if($val == $id)
			return TRUE;
		}
		return FALSE;
	}

	site_head();

	// make sure $_GET is an interger to protect against malicious SQL injection
	if(isset($_GET['new'])) {
		$new = validate_get($_GET['new']);
	}
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?new=' . $new . '" method="post" name="editcollection" enctype="multipart/form-data">';

	// generate form elements for new image collection data
	if($new == 1) {
		echo '<h1 class="head">Collection Information</h1>
		<p>' . $msg1 . $msg2 . $msg3 . '<br />
		<table width="100%" class="form_tb">
			<tr>
				<td width="70%" class="right_align">Name</td>
				<td width="30%"><input name="coll_name" type="text" maxlength="25" value="' . $coll_name . '"></td>
			</tr>
			<tr>
				<td colspan="2" class="center_align"><textarea name="coll_desc" cols="50" rows="5" maxlength="500">';
		if($coll_desc)
			echo $coll_desc;
		else
			echo 'Enter description here.';
		echo '</textarea></td>
			</tr>
		</table></p>';

	}

	// generate form elements for uploading images and entering a name and description
	if($_POST['num_images'] || $_POST['addimages']) {
		if($_POST['num_images'])
			$num = $_POST['number'];

		for($count = 1; $count <= $num; $count++) {
			$error = FALSE;
			if($_POST['addimages']) {
				$error = checkerror($count);
			}

			// display an error massage for failed uploads but retain entered form values
			if($error) {
				echo
				'<h1 class="head"><span class="error">Photo #' . $count .' could not upload</span></h1>
				<p><table width="100%" class="form_tb">
			  	<tr>
					<td width="70%" class="right_align">Name</td>
					<td width="30%"><input name="img' . $count . '_name" value="' . $name_array[$count] . '" type="text" maxlength="25"></td>
			 	</tr>
				<tr>
					<td colspan="2" class="center_align"><textarea name="img' . $count . '_desc" cols="50" rows="5" maxlength="500">';

				if($desc_array[$count]) {
					echo $desc_array[$count];
				}
				else {
					echo 'Enter description here.';
				}

				echo '</textarea></td>
				</tr>
				</table></p>
				<p class="center_align"><input name="img' . $count . '" type="file"></p>';
			}
			else {
				echo
				'<h1 class="head">Photo #' . $count .'</h1>
				<p><table width="100%" class="form_tb">
				<tr>
					<td width="70%" class="right_align">Name</td>
					<td width="30%"><input name="img' . $count . '_name" value="' . $name_array[$count] . '" type="text" maxlength="25"></td>
				</tr>
				<tr>
					<td colspan="2" class="center_align"><textarea name="img' . $count . '_desc" cols="50" rows="5" maxlength="500">';

				if($desc_array[$count]) {
					echo $desc_array[$count];
				}
				else {
					echo 'Enter description here.';
				}

				echo '</textarea></td>
				</tr>
				</table></p>';

				if($_POST['addimages']) {
					echo '<p class="center_align">This image has been saved</p>
					<input name="img' . $count . '" type="hidden" value="' . $image_array[$count] . '">
					<input name="img' . $count . '_name" type="hidden" value="' . $image_array['img' . $count . '_name'] . '">';
				}
				else {
					echo '<p class="center_align"><input name="img' . $count . '" type="file"></p>';
				}
			}
		}
		echo '<p class="center_align"><input name="num" type="hidden" value="' . $num . '">
		<input name="addimages" type="submit" value="FINISHED!"></p>';
	}
	// allows up to 10 images to be added to a collection
	else {
		echo '<h1 class="head">Add Photos</h1>
		<p class="right_align">Select number of images to add
		<select name="number">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
		</select></p>
		<p class="center_align"><input name="num_images" type="submit" value="BEGIN UPLOADING"></p>';
	}

	echo '</form>';

	site_footer();

?>
