function resize(scale_x,scale_y,quality) {
 	if ((scale_x != null) || (scale_y != null) || (quality != null)){
		//Close the file browser window.
		resizeFile.window.close(); 	 
		actualURL = "includes/resize/resize.php?w="+scale_x+"&h="+scale_y+"&q="+quality+"&src=" + historyImages[historyPosition];	
		setTimeout("callEffect()", 0);
	} else {
		resizeFile = window.open("includes/resize/resizeFile.php?src="+ historyImages[historyPosition],"resizeFile","width=500,height=250,scrollbars=NO");	// width=300,height=200,
	}
}