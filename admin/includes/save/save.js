function saveImage(){
    var newName= prompt('Enter the new name', historyImagePath+historyImages[historyPosition]);

    if (newName != null) {
	actualURL = "includes/save/save.php?newName="+newName+"&oldName=" + historyImages[historyPosition];
    }
	setTimeout("callEffect()", 0);
}