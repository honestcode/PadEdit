// %protect%

/*
 * PadEdit
 * JavaScript Repository
 *
 * Copyright (c) 2010 Honest Code.
 * Licensed under the GPL license.
 * http://www.gnu.org/licenses/gpl.txt
 *
 * Date: 2010-7-8
 * Version 1.2
 *
 */

$(document).ready(function(){

	// functions

	$.fn.extend({
		insertAtCaret: function(myValue){
		
		  // inserts a particular bit of text at the current cursor position in a textarea. 
		  // from: http://stackoverflow.com/questions/946534/insert-text-into-textarea-with-jquery
		
		  this.each(function(i) {
		    if (document.selection) {
		      this.focus();
		      sel = document.selection.createRange();
		      sel.text = myValue;
		      this.focus();
		    }
		    else if (this.selectionStart || this.selectionStart == '0') {
		      var startPos = this.selectionStart;
		      var endPos = this.selectionEnd;
		      var scrollTop = this.scrollTop;
		      myValue = myValue.replace("<<**SelectionInsertionPlaceholder**>>", this.value.substring(startPos,endPos));
		      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
		      this.focus();
		      this.selectionStart = startPos + myValue.length;
		      this.selectionEnd = startPos + myValue.length;
		      this.scrollTop = scrollTop;
		    } else {
		      this.value += myValue;
		      this.focus();
		    }
		  })
		}
	}); // insertAtCaret
	
	function getSnippets(){
		
		// reads Coda snippets from an XML file.
	
		$.get($("#snipfile").val(), function(xml){
			$("#snippets form").hide();
			$("#snippets").append('<p style="margin-right: 20px; float: right;"><a href="#" class="usediffsnip">Use Different File</a></p>');
			$("#snippets").append('<ul id="mysnips"></ul>');
			var data = new Array();
			$(xml).find("plist > dict > array > string").each(function() {
				data.push($(this).text());
			});
			for (d in data) {
				if (d%2 == 0 && d > 1) {
					var dd = (d*1) + 1;
					$("#mysnips").append('<li><a href="#" class="addsnip" rel="snip'+dd+'">'+data[d]+'</a></li>');
				} else {
					$("#mysnips").append('<li style="display:none;"><code id="snip'+d+'">'+ escape(data[d])+'</code></li>');
				}
			}
			setCookie("snip", $("#snipfile").val(), 14);
		});
	} // fn getSnippets

	function setCookie(cookieName,cookieValue,nDays) {
	
		// sets a cookie with the specified parameters.
	
		var today = new Date();
		var expire = new Date();
		if (nDays==null || nDays==0) nDays=1;
		expire.setTime(today.getTime() + 3600000*24*nDays);
		document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString();
	} // fn setCookie

	function fitElements(adj) {
	
		// adjusts the UI based on the user's screen size (helpful, for example, when
		// an iPad user rotates the device).
	
		if (typeof adj == "object") { adj = 0; }
		$("#container").height($(window).height()-(adj*2));
		$(document).height($(window).height()-(adj*2));
		var ht = window.innerHeight;
		var wd = $("#editor").width();
		$("#sidebar ul").height(ht-98-adj);
		$("div.linedwrap").height(ht-98-adj);
		$("div.lines").height(ht-98-adj);
		$("#editor textarea").height(ht-100-adj);
		$("div.linedwrap").width(wd);
		$("#editor textarea").width(wd-60);
		$("#editor textarea").scroll();
	} // fitElements
		
	function loadDefaultEditor(already){
	
		// loads the text editor, as you might surmise.
		
		//	todo: wysiwyg editing
		//
		// 	if (already == true) {
		// 		var code = $('#filemce').tinymce().getContent();
		// 		$('#filemce').remove();
		// 		$("#editform").empty().append('<textarea name="filetxt" id="filetxt" cols="25" rows="5"></textarea>');
		// 		$("#filetxt").val(code);	
		// 	} 
					
		$("#filetxt").linedtextarea({
			selectedLine: 1
		});
		
		var ht = window.innerHeight;
		$("#sidebar ul").height(ht-96);
		$("#editor textarea").height(ht-96);
		$("div.linedwrap").height(ht-96);
		$("div.lines").height(ht-96);
		$("#editor textarea").blur();
		
	} // fn loadDefaultEditor
	
	
//	// onLoad
	
	$("#password").focus();
	$(window).bind("resize", fitElements);
	loadDefaultEditor();
	$("#afilelist").scrollTop($("#a"+thisfile).position().top-100);	
	var snippetsimported = false;	
	
	// enable resize handle for desktop browsers
	
    var resizing = false;    
    $(document).mouseup(function(e) {
        resizing = false;
    });

    $("#resize").mousedown(function(e) {
        e.preventDefault();
        resizing = true;
    });

    $(document).mousemove(function(e) {
        if(resizing) {
			var mousepos = window.event.clientX;
			var winwidth = window.innerWidth;
			var sidebarwd = Math.round((mousepos/winwidth)*100);
			var editorwd = 100 - sidebarwd;
			if (sidebarwd < 2) sidebarwd = 2;
			if (editorwd > 98) editorwd = 98;
			if (sidebarwd > 78) sidebarwd = 78;
			if (editorwd < 22) editorwd = 22;
			$("#sidebar").width(sidebarwd+"%");
			$("#editor").width(editorwd+"%");
			var pref = sidebarwd + "^" + editorwd;
			setCookie("pnl", pref , 14);
	        fitElements();
        }
    });
    
    // enable resize handle for iPad

    $("#resize").bind("touchstart", function (event) {
    	event.preventDefault();
        resizing = true;
    });

    $("#resize").bind("touchmove", function (event) {
    	event.preventDefault();
        if(resizing) {
			var mousepos = event.originalEvent.touches[0].pageX;
			var winwidth = window.innerWidth;
			var sidebarwd = Math.round((mousepos/winwidth)*100);
			var editorwd = 100 - sidebarwd;
			if (sidebarwd < 2) sidebarwd = 2;
			if (editorwd > 98) editorwd = 98;
			if (sidebarwd > 78) sidebarwd = 78;
			if (editorwd < 22) editorwd = 22;
			$("#sidebar").width(sidebarwd+"%");
			$("#editor").width(editorwd+"%");
			var pref = sidebarwd + "^" + editorwd;
			setCookie("pnl", pref , 14);
	        fitElements();
        }
    });
    
    $("#resize").bind("touchend", function (event) {
        resizing = false;
    });
    
//    // swipe gestures - to do
//    
//    var swipe = false;
//    var startX = 0;
//    var startY = 0;
//    
//    $("#filetxt").bind("touchstart", function (event) {
//        if (event.originalEvent.touches.length == 3) {
//        	swipe = true;
//        	startX = event.originalEvent.touches[0].pageX;
//    		startY = event.originalEvent.touches[0].pageY;
//        }
//    });
//
//    $("#filetxt").bind("touchmove", function (event) {
//    	var x = event.originalEvent.touches[0].pageX;
//		var y = event.originalEvent.touches[0].pageY;
//        if (swipe) {
//        	$("#test").html(event.originalEvent.touches.length);
//			var curX = event.originalEvent.touches[0].pageX - startX;
//    		var curY = event.originalEvent.touches[0].pageY - startY;
//    		if (curY < 0) {
//    			// up
//    			document.editor.submit();
//    		} else if (curY > 0) {
//    			// down
//    			$("#snip").click();
//    		}
//        }
//    });
//    
//    $("#filetxt").bind("touchend", function (event) {
//        swipe = false;
//        $("#test").html("0");
//    });
   

	// actions
	
	// various jQuery actions that happen when users do things
    		
	$("#editor").focus(function(){
		$("#message").slideUp();
	});
	
	$("#editor").click(function(){
		$("#message").slideUp();
	});
	
	$("#newfile").click(function(){
		$("div.popup").fadeOut("fast");
		$("#newfileinfo").fadeIn("fast");
		$("#newfilename").focus();
	});
	
	$("#newfilecancel").click(function(){
		$("#newfileinfo").fadeOut("fast");
	});
	
	$("#newfolder").click(function(){
		$("div.popup").fadeOut("fast");
		$("#newfolderinfo").fadeIn("fast");
		$("#newfoldername").focus();
	});
	
	$("#newfoldercancel").click(function(){
		$("#newfolderinfo").fadeOut("fast");
	});
	
	$("#upload").click(function(){
		$("div.popup").fadeOut("fast");
		$("#uploadfileinfo").fadeIn("fast");
		$("#uploadfile").focus();
	});
	
	$("#uploadcancel").click(function(){
		$("#uploadfileinfo").fadeOut("fast");
	});
	
	$("#delete").click(function(){
		$("div.popup").fadeOut("fast");
		$("#areyousure").fadeIn("fast");
		$("#deleteCancel").focus();
	});
	
	$("#deleteCancel").click(function(){
		$("#areyousure").fadeOut("fast");
	});
	
	$("#rename").click(function(){
		$("div.popup").fadeOut("fast");
		$("#renamefile").fadeIn("fast");
		$("#filename").focus();
	});
		
	$("#editor h1").click(function(){
		$("div.popup").fadeOut("fast");
		$("#renamefile").fadeIn("fast");
		$("#filename").focus();
	});
	
	$("#renameCancel").click(function(){
		$("#renamefile").fadeOut("fast");
	});
		
	$("#restore").click(function(){
		$("div.popup").fadeOut("fast");
		$("#restorefile").fadeIn("fast");
		$("#restoreCancel").focus();
	});
	
	$("#restoreCancel").click(function(){
		$("#restorefile").fadeOut("fast");
	});
	
	$("#snip").click(function(){
		$("div.popup").fadeOut("fast");
		$("#snippets").fadeIn("fast");
		$("#snippetsCancel").focus();
		if ($("#snipfile").val() && snippetsimported == false) { 
			getSnippets();
		}
		snippetsimported = true;
	});
	
	$("#snippetsCancel").click(function(){
		$("#snippets").fadeOut("fast");
	});
	
	$("#save").click(function(){
		document.editor.submit();
	});
	
	$("a.addsnip").live("click", function(e){
		e.preventDefault();
		var targ = $(this).attr("rel");
		var code = unescape($("#"+targ).html());
		$("#filetxt").focus();
		$("#filetxt").insertAtCaret(code);
		$("#snippets").fadeOut();
	});
		
	$("#snipimport").click(function(e){
		e.preventDefault();
		getSnippets();
	});
	
	$("a.usediffsnip").live("click", function(e){
		e.preventDefault();
		$("#snippets form").show();
		$("#snippets ul").remove();
		$(this).remove();
	});
	
	$("a.filelink").click(function(e){
		e.preventDefault();
		var kind = $(this).attr("rel");
		var url  = $(this).attr("href");
		var carom = url.lastIndexOf("/")+1;
		var path = url.substring(0,carom);
		var file = url.substring(carom);
		var header = "index.php?path=" + path;
		if (kind != "folder") {
			header += "&file=" + file;
		}
		if (kind == "image") {
			header += "&image=true";
		}
		document.location.href = header;
	});
	
	$("a.viewbackup").click(function(e) {
		e.preventDefault();
		$("#restoretxt").each(function(e){
			$(this).remove();
		});
		var targ = $(this).attr("href");
		var thistr = $(this).attr("rel");
		$.get(targ,function(data){
			//data = escape(data);
			$("#"+thistr).after('<tr id="restoretxt"><td colspan="3"><textarea name="restoretxt" readonly="readonly" cols="10" rows="50"></textarea></td><tr>');
			$('#restoretxt textarea').val(data);
		});
	});
	
	$("div.popup").keyup(function (e) {
	    if (e.keyCode == 27) {
			$("div.popup").fadeOut("fast");
			e.preventDefault();
	    }
	});
	
	$('#filetxt').keydown(function(e) {
	    if (e.keyCode == 9 && !e.shiftKey) {	// tabs
	        var myValue = "\t";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        this.focus();
	        this.selectionStart = startPos + myValue.length;
	        this.selectionEnd = startPos + myValue.length;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }
	    if (e.keyCode == 57 && e.shiftKey) { //parentheses
	        var myValue = "()";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        this.focus();
	        this.selectionStart = startPos + myValue.length -1;
	        this.selectionEnd = startPos + myValue.length -1;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }
	    if (e.keyCode == 48 && e.shiftKey) {	// closing parentheses
	        var myValue = ")";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        var nextChar = this.value.substring(endPos, endPos+1);
	        if (nextChar != ")") {
	        	this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        }
	        this.focus();
	        this.selectionStart = startPos + myValue.length;
	        this.selectionEnd = startPos + myValue.length;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }
	    if (e.keyCode == 219 && !e.shiftKey) { //brackets
	        var myValue = "[]";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        this.focus();
	        this.selectionStart = startPos + myValue.length -1;
	        this.selectionEnd = startPos + myValue.length -1;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }
	    if (e.keyCode == 221 && !e.shiftKey) {	// closing brackets
	        var myValue = "]";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        var nextChar = this.value.substring(endPos, endPos+1);
	        if (nextChar != "]") {
	        	this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        }
	        this.focus();
	        this.selectionStart = startPos + myValue.length;
	        this.selectionEnd = startPos + myValue.length;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }
	    if (e.keyCode == 219 && e.shiftKey) { //curly braces
	        var myValue = "{}";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        this.focus();
	        this.selectionStart = startPos + myValue.length -1;
	        this.selectionEnd = startPos + myValue.length -1;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }
	    if (e.keyCode == 221 && e.shiftKey) {	// closing curly braces
	        var myValue = "}";
	        var startPos = this.selectionStart;
	        var endPos = this.selectionEnd;
	        var scrollTop = this.scrollTop;
	        var nextChar = this.value.substring(endPos, endPos+1);
	        if (nextChar != "}") {
	        	this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
	        }
	        this.focus();
	        this.selectionStart = startPos + myValue.length;
	        this.selectionEnd = startPos + myValue.length;
	        this.scrollTop = scrollTop;
	        e.preventDefault();
	    }

	});
	
});