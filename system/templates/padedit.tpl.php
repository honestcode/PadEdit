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

switch ($action) {

	case 'setup' :
	// HTML to set up new password
	?>	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name = "viewport" content = "user-scalable=no, width=device-width">
		<title>Welcome to PadEdit</title>
		<link href="system/styles.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#password").focus();
			});
		</script>
		</head>
		<body>
		<div style="width: 350px; margin: 30px auto;">
			<form id="login" action="index.php?setpassword=true" method="post">
				<div align="center"><img src="system/images/padedit_logo.png" alt="PadEdit" width="370" height="166" /></div>
				<?php if (isset($_GET['fail'])) { ?>
					<div class="error">Oops! Make sure both passwords match.</div>
				<?php } ?>
				
				<?php if (isset($_GET['perm'])) { ?>
					<div class="error">Before setting a password, make sure permissions for the PadEdit's parent folder is at least octal 755.</div>
				<?php } ?>
				<p align="center" style="margin-bottom: 20px;">Howdy! Choose a password, and you're all ready to go.</p>
				<fieldset>
					<p><label for="password">Password</label><br/>
					<input type="password" class="title" name="password" id="password"/></p>	
					<p><label for="confirmpassword">Confirm Password</label><br/>
					<input type="password" class="title" name="confirmpassword" id="confirmpassword"/></p>			
					<p><input type="submit" value="Set Password"/></p>
				</fieldset>
			</form>
			<p align="center" style="color:gray;"><?php echo "v" . $version; ?></p>
		
		</div>
		</body>
		</html>
		
		<?php 
	
		break;
	
	
	case 'login' :  
	// HTML for a regular log in (that is, the user is already set up)
	 ?>	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name = "viewport" content = "user-scalable=no, width=device-width">
		<title>PadEdit &middot; <?php echo $_SERVER['HTTP_HOST']; ?></title>
		<link href="system/styles.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#password").focus();
			});
		</script>
		</head>
		<body>
		<div style="width: 350px; margin: 30px auto;">
			<form id="login" action="index.php?login=true" method="post">
				<div align="center"><img src="system/images/padedit_logo.png" alt="PadEdit" width="370" height="166" /></div>
				<?php if (isset($_GET['fail'])) { ?>
					<div class="error">Sorry, your username and/or password was incorrect.</div>
				<?php } ?>
				<p align="center" style="margin-bottom: 20px;">Type your password to log in.</p>
				<fieldset>
					<p><label for="password">Password</label><br/>
					<input type="password" class="title" name="password" id="password"/></p>			
					<p><input type="submit" value="Log in"/></p>
				</fieldset>
			</form>
			<p align="center" style="color:gray;"><?php echo "v". $version; ?></p>
		</div>
		</body>
		</html>
		<?php 
	
		break;
	 // end of login bits.
	
	
	
	
	
		
	// user is logged in; editor	
	
		
	case 'editor' : 	
		
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name = "viewport" content = "user-scalable=no, width=device-width">
	<title>PadEdit <?php if (isset($_GET['file'])) echo " &middot; ". $_GET['file']; echo " &middot; " . $_SERVER['HTTP_HOST'];  ?></title>
	<link href="system/styles.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	<script type="text/javascript" src="system/js/jquery-linedtextarea.js"></script>
	<script type="text/javascript" src="system/js/editor.js"></script>
	<script type="text/javascript">
		var ipad = <?php if (strpos($_SERVER['HTTP_USER_AGENT'], "iPad")) { echo "true"; $ipad = true; } else { echo "false"; $ipad = false; } ?>;
		<?php 	if (isset($_GET['file'])) { 
				echo "var thisfile = '". substr($_GET['file'],0,strpos($_GET['file'], ".")) . "';"; 
			} else {
				echo "var thisfile = 'filelist';"; 
			}?> 
	</script>
	<style type="text/css">
		<?php 	if (isset($_COOKIE["pnl"])) {
				$panelwd = explode("^", $_COOKIE["pnl"]);
				echo ('#sidebar { width: '.$panelwd[0].'%; } ');
				echo ('#editor  { width: '.$panelwd[1].'%; } ');
			}
		?>
	</style>
	
	</head>
	<body>
	
	<div id="container">
		<div id="sidebar">
			<h1><?php if ($path != "../") { $pos = strrpos(substr($path, 0, -1), "/"); 
					?><a href="index.php?path=<?php echo substr($path, 0, $pos);?>/" class="btn"><img src="system/images/arrow.svg" alt="Parent Folder" title="Parent Folder" width="14" height="14" /></a><?php
				 } 
					$displaypath = substr($path, strpos($path, "../")+3); 
					if ($displaypath) { echo $displaypath; } else { echo "&nbsp;"; } ?></h1>
			<div class="toolbar">
				<div id="resize" unselectable="on" onclick="void(0)">&nbsp;</div>
				<a href="#" id="newfolder"><img src="system/images/newfolder.svg" alt="new folder" title="New Folder" width="41" height="41" /></a>
				<a href="#" id="newfile"><img src="system/images/newfile.svg" alt="new file" title="New File" width="41" height="41" /></a>
				<?php if (!$ipad) { ?><a href="#" id="upload"><img src="system/images/upload.svg" alt="upload" title="Upload File" width="41" height="41" /></a> <?php } ?>
			</div>
			<?php include("system/filelist.php"); ?>
			<?php 
				$thisb = array();
				foreach ($backups as $b) {
					$info = explode("_", $b['name']); 
					if ($info[3] == $_GET['file']) {
						array_push($thisb, $b);
					}
				}
			 ?>
		</div> <!-- sidebar -->
		<div id="editor">
			<h1 class="inactive"><?php if (isset($_GET['file'])) { echo $_GET['file']; } else { echo "PadEdit " . $version; } ?></h1>
			
			<div class="toolbar" style="text-align:center">
				<span style="float: left; line-height: 41px;">
					<?php if (!$safety && $fileEditable ) { ?>
					<a href="#" id="save"><img src="system/images/save.svg" alt="Save" title="Save" /></a> 
					<?php } ?>
					<?php if (!$safety && $fileLoaded && $fileEditable) { ?>
					<a href="<?php echo $_GET['path'].$_GET['file'];?>" target="_blank"><img src="system/images/open.svg" alt="View" title="View" /></a>
					<?php } ?>
				</span>
				<span style="margin-left: -41px;">
					<?php if (!$safety && $fileLoaded && $fileEditable) { ?>
					<a href="#" id="delete"><img src="system/images/delete.svg" alt="Delete" title="Delete" /></a>
					<?php } ?>
					<?php if (!$safety && $fileLoaded && $fileEditable) { ?>
					<a href="#" id="rename"><img src="system/images/rename.svg" alt="Rename" title="Rename" /></a>
					<?php } ?>
					<?php if (!$safety && $fileEditable ) { ?>
					<a href="#" id="snip"><img src="system/images/snippets.svg" alt="Snippets" title="Snippets" /></a>
					<?php } ?>
					<?php if (count($thisb)) { ?>
					<a href="#" id="restore"><img src="system/images/restore.svg" alt="Restore an old version" title="Restore an old version" /></a>
					<?php } ?>
				</span>
				<span style="float: right; line-height: 41px;">
					<a href="index.php?logout=true"><img src="system/images/logout.svg" alt="logout" title="Log out"/></a>
				</span>
			</div>
			
			<?php if (isset($message) or $safety) { ?>
				<div id="message">
					<?php if (isset($message)) { echo $message; } ?>
					<?php if ($safety) { echo "Sorry, but that file is protected. You can't edit it."; } ?>
				</div>
			<?php } ?>
			<?php if (isset($_GET['image'])) { ?>			
				<div align="center" style="margin: 20px auto;"><img src="<?php echo $_GET['path'].$_GET['file'];?>" alt="<?php echo $_GET['file'] ?>"></div>
			<?php } else if (!$safety) { ?>
				<form name="editor" id="editform" action="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;save=true" method="post">
					<textarea name="filetxt" id="filetxt" cols="25" rows="200"><?php if ($editfile) { echo $editfile; } ?></textarea>
				</form>
				<br style="clear:both;" />
			<?php } ?>
		</div> <!-- editor -->
	</div> <!-- container -->
	
	<!-- popups -->
	
	<div id="newfolderinfo" class="popup" style="display:none;">
		<form action="index.php?path=<?php echo $_GET['path'];?>&amp;newfolder=true" method="post">
			<input id="newfoldername" name="newfoldername" type="text"/>
			<input name="submit" type="submit" value="Create New Folder"/> &nbsp; <a href="#" id="newfoldercancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	<div id="newfileinfo" class="popup" style="display:none;">
		<form action="index.php?path=<?php echo $_GET['path'];?>&amp;newfile=true" method="post">
			<input id="newfilename" name="newfilename" type="text"/>
			<input name="submit" type="submit" value="Create New File"/> &nbsp; <a href="#" id="newfilecancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	<div id="uploadfileinfo" class="popup" style="display:none;">
		<form action="index.php?path=<?php echo $_GET['path'];?>&amp;upload=true" enctype="multipart/form-data" method="post">
			<input name="uploadfile" type="file"/>
			<input name="submit" type="submit" value="Upload"/> &nbsp; <a href="#" id="uploadcancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	
	<div id="areyousure" class="popup" style="display: none;">
		<strong style="color: #ffe856;">This file will be deleted permanently. </strong>
		<a href="#" id="deleteCancel" style="float:right;">Cancel</a> 
		<a href="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;delete=true" class="doDelete">Delete</a>
	</div>
	
	<div id="renamefile" class="popup" style="display: none;">
		<form action="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;rename=true" method="post">
			<input id="filename" name="filename" type="text" value="<?php echo $_GET['file'] ?>"/>
			<input name="submit" type="submit" value="Rename File"/> &nbsp; <a href="#" id="renameCancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	
	<div id="snippets" class="popup" style="display: none;">
		<p style="margin-bottom: 20px; float: right;"><a href="#" id="snippetsCancel" style="margin-left:0; margin-top: 3px;">Cancel</a></p>
		<form action="" method="post">
			Clips XML File URL: <input id="snipfile" name="filename" type="text" value="<?php echo $_COOKIE["snip"];?>"/> <a href="http://honestcode.com/padedit/#howdoiimportmycodaclips" target="_blank"><strong>?</strong></a>
			<input id="snipimport" name="submit" type="button" value="Import Snippets"/>
		</form>
	</div>
	
	<div id="restorefile" class="popup" style="display: none;">
		<?php	if (count($thisb)) {
				echo '<p style="margin-bottom: 20px; float: right;"><a href="#" id="restoreCancel" style="margin-left:0; margin-bottom: 20px;">Cancel</a></p>'; 
				echo '<br style="clear:both;" />';
				echo "<table>";  
				foreach ($thisb as $b) {
					$info = explode("_", $b['name']); 
					?>
					<tr id="v<?php echo $info[2];?>">				
					<td>Version saved <?php echo date("Y F j, g:i A", $info[2]);?></td>
					<td align="right"><a class="viewbackup" rel="v<?php echo $info[2];?>" href="<?php echo $_GET['path'].$b['name'];?>">View</a></td>
					<td align="right"><a href="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;restore=<?php echo $info[2];?>">Restore</a></td>
					</tr>
			<?php	}
			echo "</table>"; 
			} ?>
	</div>
	
	<!-- end popups -->
	
	</body>
	</html>
	<?php 
	
	break;

}

?>