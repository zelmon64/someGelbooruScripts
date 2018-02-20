function openImage(filename, filepath) {
 	if (filename != null && filepath != null){
		mainImage.src = imagePath + filename;

		// Clear the cache array
		historyPosition = 0;
		historyImages[historyPosition] = filename;
		historyImagePath = filepath;

		//Close the file browser window
		openFile.window.close();
	} else {
		openFile = window.open("includes/open/openFile.php","openFile","width=650,height=600,scrollbars=NO,resizable=1");	// width=650,height=600, width=400,height=375,
		if( !openFile ) { return; } //browser is blocking popups
		//var contentWidth = document.getElementById("fileListTable").offsetWidth;
		//var contentHeight = document.getElementById("fileListTable").offsetHeight;
		//openFile.resizeTo(contentWidth,contentHeight);
	}
	
}