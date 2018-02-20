function crop(){
    if (currentSelection != new Object()) { if (currentSelection.width > 1 && currentSelection.height > 1) {
	actualURL = "includes/crop/crop.php?x="+currentSelection.x+"&y="+currentSelection.y+"&w="+currentSelection.width+"&h="+currentSelection.height+"&src=" + historyImages[historyPosition];
	}}
	setTimeout("callEffect()", 0); 
}

//crop = new crop();