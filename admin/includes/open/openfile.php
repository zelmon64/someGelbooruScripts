<?php
	require "../header.php";
	if(!defined('_IN_ADMIN_HEADER_'))
		die;
	include('../configure.php');
	include('functions.php');
session_start();
error_reporting(1);


if (isset($_GET['open'])){
    $open_what = $_GET['open'];
    $open_pos = strrpos($open_what, "/");
    $open_tmp = substr($open_what, $open_pos);
    $open_path = substr($open_what, 0, $open_pos);
	if (!copy(IMAGE_PATH . $open_what,TMP_IMAGE_PATH . $open_tmp)) {
	    echo "failed to open $file...\n";
	}
	echo "<script>window.opener.openImage('".$open_tmp."', '".$open_path."')</script>";
	exit(0);
}

if(isset($_GET['field_name'])) {
	$_SESSION['field_name'] = $_GET['field_name'];
}

if(isset($_GET['path'])) {
	$_SESSION['path'] = $_GET['path'];
} else {
 	if (!isset($_SESSION['path'])){
 		 $_SESSION['path']="";
 	}
}

// Load all files and directories in its arrays

$files = array();
$dirs = array();
$overwrite = 1;

//Operaciones con los fichero del directorio

  $dirpath= IMAGE_PATH . $_SESSION['path'];
  if (!is_dir($dirpath)) {
    $dirpath= IMAGE_PATH;
    $_SESSION['path']="";
  }

// Remove directory
if (isset($_GET['delDir'])) {
	if (deleteDir($dirpath . "/" . $_GET['delDir'])) {
        echo "Deleted folder " . $_GET['delDir'];
	} else { echo "Failed to delet folder " . $dirpath . "/" . $_GET['delDir']; }
}

if (isset($_GET['delFile'])) {
	if ( unlink($dirpath . "/" . $_GET['delFile']) ) {
        echo "Deleted ".$_GET['delFile'];
	} else { echo "Failed to delete " . $dirpath . "/" . $_GET['delFile']; }
}

if (isset($_POST['newFolderField'])) {
	if (mkdir($dirpath ."/". $_POST['newFolderField'] , 0777)) {
 	    echo "Created folder " . $_POST['newFolderField'];
 	} else { echo "Failed to created folder " . $dirpath . "/" . $_POST['newFolderField']; }
}

if($_FILES['file']) {
	$upload = true;
	if(!$overwrite) {
		if(file_exists($dirpath.'/'.$_FILES['file']['name'])) {
			$upload = false;
		}
	}

	if (isset($_POST['anchura'])) {
		$max_width = $_POST['anchura'];
	} else {
		$max_width = "";
	}

	if (isset($_POST['altura'])) {
		$max_height = $_POST['altura'];
	} else {
		$max_height= "";
	}

	$x = uploadFile($_FILES,$dirpath,$max_width,$max_height);
}

$file_type = $_GET['type'];


$dh = opendir($dirpath);
while (false !== ($file = readdir($dh))) {
	// Comprobamos si es un directorio o un fichero
	if (is_dir($dirpath."/".$file)) { //es directorio
		$dirs[]=$file;
	} else { //es fichero
		$files[]=$file;
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Image Browser</title>
<link rel="stylesheet" type="text/css" href="../../stylesheet.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
body {
	font-family:Arial;
	font-size:10pt;
}
</style>
<script type="text/javascript" src="open.js"></script>
</head>
<body>
<div id="container">
  <div id="fileList" style="overflow:auto;height:300px">
    <?php
	//Show all directories

	echo "<table id='fileListTable'>";
	echo "<tr><th onclick=\"sortTable(0)\">Filename</th><th onclick=\"sortTable(1)\">Size</th></tr>";

	for ($i=0;$i<count($dirs);$i++) {
	 	echo "<tr><td colspan=\"2\">";

	 	$dirOk = false;
		 if (($dirs[$i] != ".") && ($dirs[$i] != "..")) {
	 	 	$thisDir = $_SESSION['path']."/".$dirs[$i];
	 	 	$dirOk = true;
		} elseif ($dirs[$i] == "..") {
		 	$dirnames = split('/', $_SESSION['path']);
		 	$thisDir = "/";
		 	for($di=1; $di<(sizeof($dirnames)-1); $di++) {
                  $thisDir.=  $dirnames[$di] . '/';
            }
            $thisDir=rtrim($thisDir, "/");
			$dirOk = true;
		}

		if ($dirOk) { //El directorio es un directorio válido (no está en la lista de prohibidos)
			echo "<a href=\"".$_SERVER['PHP_SELF']."?path=".$thisDir."\">";
			echo "<img src=\"folder.gif\" border=\"0\"> ";

			if (strlen($dirs[$i])<25) {
			 	echo $dirs[$i];
			} else {
				echo substr($dirs[$i], 0, 22) . "...";
			}
			echo "</a>";
			echo "</td><td align=\"right\">";
			if ($dirs[$i]!="..") {
				echo "<div><a href=\"openfile.php?delDir=". urlencode($dirs[$i] ) ."\" onclick=\"return confirm('Are you sure?')\"> <img src=\"delete.gif\" border=\"0\" alt=\"Eliminar la carpeta\"></a></div>";
			}

		}
		echo "</td></tr>";
	}

	for ($i=0;$i<count($files);$i++) {
			echo "<tr><td>";
			echo "<img src=\"file.gif\" border=\"0\">";
			if (strlen($files[$i])<56) {
			 	echo "<a href=\"openfile.php?open=" .substr($_SESSION['path'], 1)."/". $files[$i] ."\">". $files[$i] ."</a>";
			} else {
				echo "<a href=\"openfile.php?open=" .substr($_SESSION['path'], 1)."/". $files[$i] ."\">". substr($files[$i], 0, 52) . "...</a>";
			}
			echo "</td><td align=\"right\">";
			$fileinfo = stat($dirpath.'/'.$files[$i]);
			echo number_format(( $fileinfo[ "size" ] / 1024 ), 2, '.', ',') . "kb";
			echo "</td><td align=\"right\">";
			if ($files[$i] != null) {
			    echo "<a href=\"openfile.php?delFile=". $files[$i] ."\" onclick=\"return confirm('Are you sure?')\"><img src=\"delete.gif\" border=\"0\" alt=\"Delete\"></a>";
			}
			echo "</td></tr>";
	}
	echo "</table>";

	closedir($dh);
	?>
  </div>
	<form name="form_file2upload" id="form_file2upload" action="openfile.php" method="POST" enctype="multipart/form-data">
		<input type="file" name="file"><input type="submit" value="Upload File">
	</form>
	<form name="form_folder2create" id="form_folder2create" action="openfile.php" method="POST" enctype="multipart/form-data">
		<input type="submit" value="Create folder:"><input type="text" name="newFolderField" value="New Folder">
	</form>
</div>
</body>
</html>