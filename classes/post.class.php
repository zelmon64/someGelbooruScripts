<?php
	class post
	{
		function show($id)
		{
			global $db, $post_table;
			$id = $db->real_escape_string($id);
			$query = "SELECT * FROM $post_table WHERE id = '$id' LIMIT 1";
			$result = $db->query($query);
			if($result->num_rows == "0")
				return false;
			$row = $result->fetch_assoc();
			return $row;
		}
		
		function get_notes($id)
		{
			global $db, $note_table;
			$id = $db->real_escape_string($id);
			$query = "SELECT * FROM $note_table WHERE post_id='$id'";
			$result = $db->query($query);
			return $result;
		}
		
		function has_notes($id)
		{
			$result = $this->get_notes($id);
			if($result->num_rows == "0")
				return false;
			else
				return true;
		}

		function prev_next($id, $search = false)
		{
			if(isset($search) && $search !="" && $search)
				$tagged = true;
			else
				$tagged = false;
			global $db, $post_table;
			if($tagged){
				$search = $db->real_escape_string($search);
				require_once('search.class.php');
				$searchclassfnc = new search();
				$tags = '';
				$aliased_tags = '';
				$original_tags = '';
				$parent = '';
				$ttags = explode(" ",$search);
				$g_rating = '';
				$g_owner = '';
				$g_score = '';
				$g_tags = '';
				$g_parent = '';
				foreach($ttags as $current)
				{
					if(strpos(strtolower($current),'parent:') !== false)
					{
						$g_parent = str_replace("parent:","",$current);
						$parent = " AND id='$g_parent'";
						if(!is_numeric($g_parent))
							$g_parent = '';
						else
							$g_parent = " AND parent='$g_parent'";
						$current = '';
					}
					if($current != "" && $current != " ")
					{
						$len = strlen($current);
						$count = substr_count($current, '*', 0, $len);
						if(($len - $count) >= 2)
						{
							if(strpos(strtolower($current),'rating:')  !== false)
							{
								$rating = str_replace('rating:','',$current);
								if(substr($current,0,1) == "-")
								{
									$rating = substr($rating,1,strlen($rating)-1);
									$rating = ucfirst(strtolower($rating));
									$g_rating .= " AND rating != '$rating'";
								}
								else
								{
									$rating = ucfirst(strtolower($rating));
									$g_rating .= " AND rating = '$rating'";
								}
							}
							else if(strpos(strtolower($current),'user:')  !== false)
							{
								$owner = str_replace('user:','',$current);
								if(substr($current,0,1) == "-")
								{
									$owner = substr($owner,1,strlen($owner)-1);
									$g_owner = " AND owner != '$owner'";
								}
								else
									$g_owner = " AND owner = '$owner'";
							}
							else if(strpos(strtolower($current),'score:')  !== false)
							{
								$score = str_replace('score:','',$current);
								$score = htmlspecialchars_decode($score);
								$op = substr($score,0,1);
								switch ($op)
								{
									case '<':
									case '>':
									case '=':
										$score = substr($score, 1);
										break;
									default:
										$op = '=';
								}
								$score = (int) $score;
								$g_score = " AND score $op $score";
							}
							else
							{
								$g_tags .= $searchclassfnc->parse_tag($current);
							}
						}
					}
				}
				$blacklist = $searchclassfnc->blacklist_fragment();
				if($g_tags != "")
				{
					if($g_parent != "")
						$parent_patch = "OR (MATCH(tags) AGAINST('$g_tags' IN BOOLEAN MODE)>0.9) $parent $g_owner $g_score $g_rating";
					else
						$parent_patch = " AND parent='0'";
					$neg_search = !strpos($g_tags,"+");
					if ($neg_search) {
						$g_tags = preg_replace("/\-/", "", $g_tags);
						$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id < $id AND (NOT (MATCH(tags) AGAINST('$g_tags' IN BOOLEAN MODE)>0.9) $g_parent $g_owner $g_score $g_rating $blacklist $parent_patch) ORDER BY id DESC LIMIT 1";
					} else {
						$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id < $id AND ((MATCH(tags) AGAINST('$g_tags' IN BOOLEAN MODE)>0.9) $g_parent $g_owner $g_score $g_rating $blacklist $parent_patch) ORDER BY id DESC LIMIT 1";
					}
				}
				else if($g_parent != "" || $g_owner != "" || $g_rating != "" || $g_score != "")
				{
					if($g_parent != "")
					{
						$g_parent = str_replace('AND',"",$g_parent);
						$parent = substr($parent,4,strlen($parent));
						$parent_patch = "OR $parent $g_owner $g_score $g_rating";
					}
					else if($g_owner != "")
						$g_owner = str_replace('AND',"",$g_owner);
					else if($g_rating != "")
						$g_rating = substr($g_rating,4,strlen($g_rating));
					else if($g_score != "")
						$g_score = str_replace('AND',"",$g_score);
					else if($blacklist != "")
						$blacklist = str_replace('AND',"",$blacklist);
					if($g_parent == "")
						$parent_patch = " AND parent='0'";
					$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id < $id AND ($g_parent $g_owner $g_score $g_rating $blacklist $parent_patch) ORDER BY id DESC LIMIT 1";
				}
				else
				{
					$count = substr_count($search, '*', 0, strlen($search));
					if(strlen($search)-$count > 0)
					{
						$res = str_replace("*","",$search);
						$query = "SELECT * FROM $post_table WHERE id < $id AND tags LIKE '% $res %' ORDER BY id DESC LIMIT 1";
					}
					else
						$query = "SELECT * FROM $post_table WHERE id < $id ORDER BY id DESC LIMIT 1";
				}
			}
			else
				$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id < $id ORDER BY id DESC LIMIT 1";
			$result = $db->query($query);
			$row = $result->fetch_assoc();
			$prev_next[] = $row['id'];
			if($tagged){
				if($g_tags != "")
				{
					if ($neg_search) {
						$g_tags = preg_replace("/\-/", "", $g_tags);
						$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id > $id AND (NOT (MATCH(tags) AGAINST('$g_tags' IN BOOLEAN MODE)>0.9) $g_parent $g_owner $g_score $g_rating $blacklist $parent_patch) ORDER BY id ASC LIMIT 1";
					} else {
						$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id > $id AND ((MATCH(tags) AGAINST('$g_tags' IN BOOLEAN MODE)>0.9) $g_parent $g_owner $g_score $g_rating $blacklist $parent_patch) ORDER BY id ASC LIMIT 1";
					}
				}
				else if($g_parent != "" || $g_owner != "" || $g_rating != "" || $g_score != "")
				{
					$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id > $id AND ($g_parent $g_owner $g_score $g_rating $blacklist $parent_patch) ORDER BY id ASC LIMIT 1";
				}
				else
				{
					if(strlen($search)-$count > 0)
					{
						$query = "SELECT * FROM $post_table WHERE id > $id AND tags LIKE '% $res %' ORDER BY id ASC LIMIT 1";
					}
					else
						$query = "SELECT * FROM $post_table WHERE id > $id ORDER BY id ASC LIMIT 1";
				}
			}
			else
				$query = "SELECT SQL_NO_CACHE id FROM $post_table WHERE id > $id ORDER BY id ASC LIMIT 1";
			$result = $db->query($query);
			$row = $result->fetch_assoc();
			$prev_next[] = $row['id'];
			return $prev_next;
		}
		
		function has_children($id)
		{
			global $db, $parent_child_table;
			$query = "SELECT * FROM $parent_child_table WHERE parent = '$id' LIMIT 1";
			$result = $db->query($query);
			if($result->num_rows == "0")
				return false;
			else
				return true;
		}
		
		function index_count($current)
		{
			global $db, $tag_index_table;
			$current = $db->real_escape_string(htmlentities($current, ENT_QUOTES, "UTF-8"));
			$query = "SELECT index_count FROM $tag_index_table WHERE tag='$current' LIMIT 1";
			$result = $db->query($query);
			$row = $result->fetch_assoc();
			return $row;
		}

		function has_parent($id)
		{
			global $db, $parent_child_table;
			$query = "SELECT * FROM $parent_child_table WHERE child = '$id' LIMIT 1";
			$result = $db->query($query);
			if($result->num_rows == "0")
				return false;
			else
				return true;
		}

		function get_parent_id($id)
		{
			if(!$this->has_parent($id))
				return NULL;
			global $db, $parent_child_table;
			$query = "SELECT parent FROM $parent_child_table WHERE child = '$id' LIMIT 1";
			$result = $db->query($query);
			return $result->fetch_assoc()["parent"];
		}
	}
?>
