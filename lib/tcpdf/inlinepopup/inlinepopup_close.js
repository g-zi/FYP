// originale windows-methode patchen
windowClose = window.close;

window.close = function () {
	if (!parent || typeof(parent.InlinePopup) == "undefined")
		windowClose();
	else
		parent.InlinePopup.close(self.name);
}