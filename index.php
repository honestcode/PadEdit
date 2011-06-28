<?php // %protect%
/**
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
 
//check for PHP 5
$phpversion = floatval(phpversion());
if ($phpversion < 5) {
	die("Sorry, but PHP version 5 or above is required to use PadEdit.");
}

//define a version, we can also use this to prevent direct access to componant scripts.
define('PADEDIT_VERSION', '1.3');
$version = PADEDIT_VERSION;

//set some values to avoid undefined notices
$editfile = null;
$safety = false;

//Start session
session_set_cookie_params('0','/', null , false, true); 
session_start(); 

require_once("system/functions.php"); // core system object
$p = new padedit;

//are we logged in?
if (@$_SESSION["authorised"] === true ) { 
	//yes we are, but....
	//have they timed out?
	$lastActive = $_SESSION['lastActive'];
	// time limit in which they must log in again.
	$timeLimit = 1800; // 30 minutes. 
	
	//if they havent used the app in 30 minutes, make them log in again
	if (time() > ($lastActive + $timeLimit)   ){
		$loggedin = false;
	}else {
    	$loggedin = true; 
    	//update the last active time
    	$_SESSION['lastActive'] = time();
	}
}else {
	$loggedin = false;
}


if (!$loggedin) { 
	//checks to see if we need to run the setup, and runs it if we do.
	$setup = $p->checkSetup();

	if ($setup == true){
		//true, we need to run the setup
		$action = 'setup';
	} else {
		//otherwise make the user login.
		$p->login();
		$action = 'login';
	}
}


//controller section. works out what the user is trying to do and performs the necessary functions




// things we can only do if we are logged in
if ($loggedin){

	// save file
    if (isset($_GET['save'])) { 
    	$message = $p->saveFile();
    }
    
    // restore file from a backup
    if (isset($_GET['restore'])) { 
    	$message = $p->restoreBackup();
    } 
    
    // delete a file.
    if (isset($_GET['delete'])) { 
    	$message = $p->deleteFile();
    }
    
    // create a new blank file.
    if (isset($_GET['newfile'])) { 
    	$message = $p->createFile();
    }
    
    // create a new empty folder.
    if (isset($_GET['newfolder'])) { 
    	$message = $p->createFolder();
    }
    
    // rename a file.
    if (isset($_GET['rename'])) { 
    	$message = $p->renameFile();
    } 
    
    // upload a file.
    if (isset($_GET['upload'])) { 
    	$message = $p->uploadFile();
    }

	// logout    
    if (isset($_GET['logout'])) {
		$p->logout();
	}
    
    // confirmation messages
    if (isset($_GET['saved'])) {
    	$message = "File saved. <a href='".$_GET['path'].$_GET['file']."' target='_blank'>View file.</a>";
    }
    
    if (isset($_GET['deleted'])) {
    	$message = "File deleted.";
    }
    
    if (isset($_GET['renamed'])) {
    	$message = "File renamed. <a href='".$_GET['path'].$_GET['file']."' target='_blank'>View file.</a>";
    }
    
    if (isset($_GET['foldered'])) {
    	$message = "Folder created.";
    }
    
    if (isset($_GET['restored'])) {
    	$message = "Backup restored.";
    }
    
	// end confirmation messages
    
    //display editor
	$action = 'editor';
	
	//make sure we have a path
    if (isset($_GET['path'])) { 
    	$path = $_GET['path']; 
    } else { 
    	$path = "../"; 
    }
    if (!$p->checkPath($path)) { 
    	header("Location: index.php?path=../"); 
    	exit;
    } 
    
    //if the user wants to load a file
	if (isset($_GET['file']) and isset($_GET['path'])) {
		//get file details 
		$fileDetails = $p->getFileDetails($_GET['path'],$_GET['file']);
	}

	//check if they should be editing this file
	if ($fileDetails['protected'] == true ) { 
		$safety = true;
		$fileLoaded = false;
	} else { 
		$safety = false;
		$fileLoaded = true;
		$editfile =  $fileDetails['source'];
	}
	
	if ($fileDetails['is_file']  && $fileDetails['is_writable']){
		//Need to check the type of file and only allow files that can be edited to be loaded in editor.
		$fileEditable = $p->canEdit($fileDetails['extension']);

		if (!$fileEditable){
    		//$safety = true;
    		$fileLoaded = false;
    		$editfile =  '';
    		$message = "Sorry, You can't edit this type of file";
		}
		
	}
	
}

// load the template
require_once("system/templates/padedit.tpl.php");
?>