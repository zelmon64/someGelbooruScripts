function rotate(){
	var angle2rotate= prompt('Angle to rotate (CW)', '90');
    if (angle2rotate != null) {
	    actualURL = "includes/rotate/rotate.php?x="+angle2rotate+"&src=" + historyImages[historyPosition];
	}
	setTimeout("callEffect()", 0);
}