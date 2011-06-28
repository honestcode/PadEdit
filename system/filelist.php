<?php // %protect% 

/*
 * PadEdit
 *
 * Copyright (c) 2010 Honest Code.
 * Licensed under the GPL license.
 * http://www.gnu.org/licenses/gpl.txt
 *
 * Date: 2010-12-25
 * Version: 1.3
 *
 */


//if this page is trying to be accessed directly, exit.
if (!defined('PADEDIT_VERSION')){
	exit;
}

?>
<ul id="afilelist">
<?php
$files = $p->getFiles($path); // gets files based on the string passed in the URL. $path is set in index.php
$backups = array(); // creates an empty array for the backups stored in the folder $path
$padeditfolder = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], "/index.php")-1); // determines the name of the padedit folder part 1

foreach ($files as $file) {
	$filesize = round(filesize($path.$file['name']) / 1024,2); // determines file size
	
	if (strpos($file['name'], "dedit_backup")) { // determines if the file is a backup or not
		$backupfile = true;
	} else {
		$backupfile = false;
	}
	
	if (!$backupfile) { // if it is NOT a backup ...
		if ($file['type'] == "file") {
			if (strpos($file['name'], ".jpg") or strpos($file['name'], ".jpeg") or strpos($file['name'], ".gif") or strpos($file['name'], ".png")) { 
				// display an image in the file list
				echo ("<li id='a".substr($file['name'],0,strpos($file['name'], "."))."'>
						<a href='".$path.$file['name'] ."' rel='image' class='image filelink' title='".$filesize."K'>" . $file['name'] .'</a>
					   </li>');
			} else { 
				// display an editable file in the file list
				echo ("<li id='a".substr($file['name'],0,strpos($file['name'], "."))."'>
						<a href='".$path.$file['name'] ."' rel='file' class='file filelink' title='".$filesize."K'>" . $file['name'] ."</a>
					   </li>");
			}
		} else { 
			if ($file['name'] != $padeditfolder) { 
				// display a folder in the file list (so long as it isn't the padedit folder)
				echo ("<li id='a".substr($file['name'],0,strpos($file['name'], "."))."'><b>
				         <a href='" . $path.$file['name']."/' class='folder filelink' rel='folder'>" . $file['name']. "</a></b>
				       </li>");	
			}
		} // file types
	} else { 
		// if it IS a backup, take this item out of the backups array
	
		array_unshift($backups, $file);
	
	} // backup files
	 
} 
?>
	<li id="console"></li> <!-- this is for development use as a place to echo stuff to on the screen -->
</ul>