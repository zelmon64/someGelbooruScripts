<?php
	require "header.php";
	if(!defined('_IN_ADMIN_HEADER_'))
		die;
	if($_GET['page'] == "alias")
			require "alias.php";
	else if($_GET['page'] == "alias_edit")
			require "alias_edit.php";
	else if($_GET['page'] == "reported_posts")
			require "reported_posts.php";
	else if($_GET['page'] == "reported_comments")
			require "reported_comments.php";
	else if($_GET['page'] == "add_group")
			require "add_group.php";
	else if($_GET['page'] == "edit_group")
			require "edit_group_permission.php";
	else if($_GET['page'] == "ban_user")
			require "ban_user.php";					
	else if($_GET['page'] == "remove_posts")
			require "remove_posts.php";
	else if($_GET['page'] == "tag_ops")
			require "tag_ops.php";
	else if($_GET['page'] == "tag_categories")
			require "tag_categories.php";
	else if($_GET['page'] == "tag_category_change")
			require "tag_category_edit.php";
	else if($_GET['page'] == "jpie")
			require "jpie.php";
	else if($_GET['page'] == "jpie_open")
			require "includes/open/openfile.php";

?>
<br></body></html>
