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

class padedit {

	// checks for the various action conditions passed as variables in the URL.
	
	function login(){
	
	    //only need to load this file once per session to check for password
	    if (file_exists("system/pwd.php")){
	    	include ("system/pwd.php"); // where the password is stored.
	    } else {
	    	$this->logout();
	    	die('Your PadEdit installation is damaged. Replace it with <a href="http://honestcode.com/padedit/">a known-good copy</a> as soon as possible.');
	    }
	
		if (isset($_GET['login'])) { // user is trying to log in.
			//Using sha1 due to the popularity of reverse md5 lookup databases
			if (sha1($_POST['password']) == $pwd) { // does their password match?
				$_SESSION['authorised'] = true;
				$_SESSION['lastActive'] = time();
				session_regenerate_id();
				header ("Location: index.php?path=../");	// and take them to the editor.
				exit;
			} else {
				header ("Location: index.php?fail=true"); // wrong password.
				exit;
			}
		}
	}
	
	
	function logout(){
	
		// Unset all of the session variables.
		$_SESSION = array();
		
	    if (ini_get("session.use_cookies")) {
	        $params = session_get_cookie_params();
	        setcookie(session_name(), '', time() - 42000,
	            $params["path"], $params["domain"],
	            $params["secure"], $params["httponly"]
	        );
	    }
		
	    // Finally, destroy the session.
		session_destroy();
	    
		//redirect them back to login and they will be logged out.
		header ("Location: index.php");
		exit;
	
	
	}
	
	function savePassword($password) {
	
		// Edits a file called system/pwd.php (which is blank as part of the distro) where it stores an 
		// MD5-hashed version of the password that the user enters during the setup process. PadEdit will 
		// look at this file when the user logs in and check it against what the user enters.
	
		$filename = "system/pwd.php";
		$contents = "<?php ";
		$contents .= "// %protect% \n\n";
		$contents .= "$";
		$contents .= "pwd = '";
		$contents .= sha1($password);
		$contents .= "'; ?>";
		if (is_writable($filename)) {
		    if (!$handle = fopen($filename, 'w+')) {
		         echo "Cannot open file ($filename)";
		         exit;
		    }
		    if (fwrite($handle, $contents) === FALSE) {
		        echo "Cannot write to file ($filename)";
		        exit;
		    }	
		    fclose($handle);
		    return true;
		} else {
		   	return false;
		}
	} // fn savePassword
	
	function getFiles($path) {
	
		// gets a list of the files in the folder specified in $path.
		// returns an array of the files' attributes ('size', 'name', 'type') sorted alphabetically (ascending) by name.
	
		$files = array();
		$fileNames = array();
		$i = 0;  
		if (is_dir($path)) {
	       if ($dh = opendir($path)) {
	           while (($file = readdir($dh)) !== false) {
	               if ($file == "." || $file == "..") continue;
	               $fullpath = $path . "/" . $file;
	               $fkey = strtolower($file);
	               while (array_key_exists($fkey,$fileNames)) $fkey .= " ";
	               $a = stat($fullpath);
	               $files[$fkey]['size'] = $a['size'];
	               if ($a['size'] == 0) $files[$fkey]['sizetext'] = "-";
	               else if ($a['size'] > 1024) $files[$fkey]['sizetext'] = (ceil($a['size']/1024*100)/100) . " K";
	               else if ($a['size'] > 1024*1024) $files[$fkey]['sizetext'] = (ceil($a['size']/(1024*1024)*100)/100) . " Mb";
	               else $files[$fkey]['sizetext'] = $a['size'] . " bytes";
	               $files[$fkey]['name'] = $file;
	               $files[$fkey]['type'] = filetype($fullpath);
	               $fileNames[$i++] = $fkey;
	           }
	           closedir($dh);
	       } else die ("Cannot open directory:  $path"); // todo - make pretty
	   } else die ("Path is not a directory:  $path"); // todo - make pretty
	   sort($fileNames,SORT_STRING);
	   $sortedFiles = array();
	   $i = 0;
	   foreach($fileNames as $f) $sortedFiles[$i++] = $files[$f];
	   return $sortedFiles;
	} // fn getFiles
	
	
	
	// returns file details inclusing file source, unless it is protected with the %protect% string.
	function getFileDetails($path, $filename, $entities = true){
	
		if (file_exists($path . $filename)){
		
			$fileArray = pathinfo($path . $filename);
			$fileArray['filename'] = basename($path . $filename);
		
			
	    	$source = file_get_contents($path . $filename);
	    	if ($entities) {
	    		$source = htmlentities($source);
	    	}
	    	
	    	if (strpos($source, "%protect%")) {
	    		$source = null;
	    		$fileArray['protected'] = true;
	    		$fileArray['source'] = null;
	    	} else {
	    		$fileArray['protected'] = false;
	    		$fileArray['source'] = $source;
	    	}
	    	
	    	$fileArray['is_dir'] = is_dir($path . $filename);
	    	$fileArray['is_file'] = is_file($path . $filename);
			$fileArray['is_link'] = is_link($path . $filename);
			$fileArray['is_writable'] = is_writable($path . $filename);
			$fileArray['is_readable'] = is_readable($path . $filename);
	
			return $fileArray;
			
		}
	
		return null;
	
	}
	
	
	
	//uploads a file to the server.
	function uploadFile(){
	
		//lets assume the authenticated user isn't trying to hack their own server, for now.
	
		$ctrlname    = 'uploadfile';
		$target_path = $_GET['path'];
		if ($target_path == "") {
			$target_path = "../";
		}
		
		$filename    = explode(".", $_FILES[$ctrlname]['name']);
		$filename    = $filename[0].".".$filename[1];
		$target_path = $target_path . basename($filename);
		
		if (move_uploaded_file($_FILES[$ctrlname]['tmp_name'], $target_path))
		{
			if (strpos($filename, ".jpg") or strpos($filename, ".jpeg") or strpos($filename, ".gif") or strpos($filename, ".png")) {
				header ("Location: index.php?path=".$_GET['path']."&file=".$filename."&saved=true&image=true");
				exit;
			} else {
				header ("Location: index.php?path=".$_GET['path']."&file=".$filename."&saved=true");
				exit;
			}
		}
	
		//else 
		$message = "There was an error uploading the file &mdash; did you make sure to select a file first?.";
		return $message;
	
	} 
	
	
	
	//renames a file on the server
	function renameFile(){
	
		$filename = str_replace("/", "-", $_POST['filename']);
		$oldname = $_GET['path'] . $_GET['file'];
	    $newname = $_GET['path'] . $filename;
		if (is_writable($oldname)) {	
		    rename($oldname, $newname);
		    if (strpos($newname, ".jpg") or strpos($newname, ".jpeg") or strpos($newname, ".gif") or strpos($newname, ".png")) {
				header ("Location: index.php?path=".$_GET['path']."&file=".$filename."&renamed=true&image=true");
				exit;
			} else {
				header ("Location: index.php?path=".$_GET['path']."&file=".$filename."&renamed=true");
				exit;
			}
		}
		
		
		$message = "Could not rename the file $filename &mdash; make sure permissions for this folder are at least octal 755.";
		return $message;
	}
	
	
	
	//create a directory on the server
	function createFolder(){
		$target_path = $_GET['path'];
		if ($target_path == "") {
			$target_path = "../";
		}
		if (!mkdir($target_path.$_POST['newfoldername'], 0755)) {
	    	$message = "Cannot create folder ".$_POST['newfoldername']." &mdash; make sure permissions for this folder are at least octal 755.";
	    } else {
	    	header ("Location: index.php?path=".$target_path."&foldered=true");
	    	exit;
	    }
	    
	    return $message;
	}
	
	
	//creates a file on the server
	function createFile(){
	
		//lets assume the authenticated user isn't trying to hack their own server, for now, but in  the future, maybe....
		//TODO need security to prevent files being created outside the current web root. eg ../../../../../etc/passwd
		//TODO need to check if file exsts before trying to create it.
		
		$target_path = $_GET['path'];
		if ($target_path == "") {
			$target_path = "../";
		}
		if (!$handle = fopen($target_path.$_POST['newfilename'], 'a')) {
	         $message = "Cannot create file".$_POST['newfilename']." &mdash; make sure permissions for this folder are at least octal 755.";
	         return $message;
	    }
	    if (fwrite($handle, "") === FALSE) {
	        $message = "Cannot create file ".$_POST['newfilename']." &mdash; make sure permissions for this folder are at least octal 755.";
	        return $message;
	    }
	    fclose($handle);
	    header ("Location: index.php?path=".$target_path."&file=".$_POST['newfilename']."&saved=true");
		exit;
	}
	
	
	//deletes a file form the server
	function deleteFile(){
	
		$filename = $_GET['path'] . $_GET['file'];
		if (is_writable($filename)) {	
		    unlink($filename);
		    header ("Location: index.php?path=".$_GET['path']."&deleted=true");
		    exit;
		} else {
		    $message = "$filename can't be deleted &mdash; make sure permissions for this folder are at least octal 755.";
		    return $message;
		}
	
	}
	
	
	//restores a backup version to the current file
	function restoreBackup(){
	
		$filename = $_GET['path'] . $_GET['file'];
		$bakname = $_GET['path'] . "padedit_backup_" . date("U") . "_" . $_GET['file'];
		$rstrname = $_GET['path'] . "padedit_backup_" . $_GET['restore'] . "_" . $_GET['file'];
		$existing = file_get_contents($filename);
		$restored = file_get_contents($rstrname);
	    if (!$backup = fopen($bakname, 'a')) {
	         $message = "Cannot open file ($filename)";
	         exit;
	         //return $message;
	    }
		if (fwrite($backup, stripslashes(utf8_encode($existing))) === FALSE) {
	        $message = "Cannot back up file ($filename)";
	        exit;
	    }
	    fclose($backup);
		if (is_writable($filename)) {	
		    if (!$handle = fopen($filename, 'w+')) {
		         $message = "Cannot open file ($filename)";
		         exit;
		    }
		    if (fwrite($handle, stripslashes(utf8_encode($restored))) === FALSE) {
		        $message = "Cannot write to file ($filename)";
		        exit;
		    }
		    fclose($handle);
		    header ("Location: index.php?path=".$_GET['path']."&file=".$_GET['file']."&restored=true");
		    exit;
		} else {
		    $message = "Could not restore the file $filename.";
		}
		return $message;
	
	}
	
	
	//saves the files contents.
	function saveFile(){
	
		$filename = $_GET['path'] . $_GET['file'];
		$bakname = $_GET['path'] . "padedit_backup_" . date("U") . "_" . $_GET['file'];
		$existing = file_get_contents($filename);
		if ($existing) {
		    if (!$backup = fopen($bakname, 'a')) {
		         $message = "Cannot open file ($filename)";
		         exit;
		    } 
			if (fwrite($backup, stripslashes(utf8_encode($existing))) === FALSE) {
		        $message = "Cannot back up file ($filename)";
		        exit;
		    }
		    fclose($backup);
	    }
		if (is_writable($filename)) {	
		    if (!$handle = fopen($filename, 'w+')) {
		         $message = "Cannot open file ($filename)";
		         exit;
		    }
		    if (fwrite($handle, stripslashes(utf8_encode($_POST['filetxt']))) === FALSE) {
		        $message = "Cannot write to file ($filename)";
		        exit;
		    }
		    fclose($handle);
		    header ("Location: index.php?path=".$_GET['path']."&file=".$_GET['file']."&saved=true");
		    exit;
		} else {
		    $message = "Could not save the file $filename &mdash; make sure permissions for this folder are at least octal 755.";
		}
		
		return $message;
		
	}
	
	
	
	
	// check to see if the initial setup has been completed. 
	function checkSetup(){
	
	    //check for password file
	    if (file_exists("system/pwd.php")){
	    	include ("system/pwd.php"); // where the password is stored.
	    } else {
	    	die('Your PadEdit installation is damaged. Replace it with <a href="http://honestcode.com/padedit/">a known-good copy</a> as soon as possible.');
	    }
	
	
		if (isset($pwd)) { 
			$setup = false; 
			return $setup; 
		} else {
			$setup = true;
			
			if (isset($_GET['setpassword'])) { // run the password setup process.
				if ($_POST['password'] == $_POST['confirmpassword']) { // check to make sure they match.
					if ($this->savePassword($_POST['password'])) {
						//send them back to the login screen to login.
						header ("Location: index.php");
						exit;
					} else { // the passwords don't match
						header ("Location: index.php?perm=true"); // display an error.
						exit;
					}
					
				} else { // something went wrong.
					header ("Location: index.php?fail=true");
					exit;
				}
			}
		}
		
		return $setup;
	}
	
	// checks to make sure the user isn't in a folder above PadEdit's parent folder:
	
	function checkPath ($path, $rootPath = "../"){
		$path = realpath($path); //remove ../'s and get the full path
		$rootPath = realpath ($rootPath);
		if (strpos ($path, $rootPath)  === false){
		   //rootPath is not in the string path, meaning they are trying to go outside the rootPath
		   return false;
		}
	
	//else
	//path is within the root path or below, so let them continue.
	return true;
	
	}
	
	// set a list of file types that can be viewed/edited.
	function canEdit($extension){
	
		//the type of files that can be edited/viewed
		$editableFileTypes = array( 'php', 'txt', 'xml',
									'html', 'htm', 'js', 
									'css', 'htaccess'
									);
		if ( in_array($extension, $editableFileTypes) ){
			return true;
		}
		return false;
	}

}


?>