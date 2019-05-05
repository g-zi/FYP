/**
* Inline Popupfenster
*
* Version: BETA (2006-11-20)
*
* erstellt von Felix Riesterer (Felix.Riesterer@gmx.net)
*/

InlinePopup = {

	idCount : 0,
	baseURL : false, // Verzeichnis, in welchem das Script steht (wird dynamisch ermittelt)
	oldWinOnLoad : null,
	oldDocOnMouseMove : null,
	popupWindows : new Array(), // enthält die IDs der DIV-Elemente (die mit window.name des jeweiligen IFrames korrespondieren)
	dragElm : false, // hier steht später das zu verschiebende "Fenster"
	dragMode : false, // enthält entweder "move" oder "resize"
	mouseLastCoords : false, // Maus-Koordinaten bei Beginn des Drag&Drops
	mouseCurrentCoords : null, // aktuelle Maus-Koordinaten
	winInnerWidth : 100,
	winInnerHeight : 100,
	// Quirksmode des IE
	IE : (document.compatMode && document.compatMode == "CSS1Compat") ? document.documentElement : document.body || null,

	// Funktion zum Ermitteln der maximalen Fensterfläche
	getBrowserDimensions : function () {
		if (window.innerWidth) {
			InlinePopup.winInnerWidth = window.innerWidth;
			InlinePopup.winInnerHeight = window.innerHeight;
		} else {
			InlinePopup.winInnerWidth = InlinePopup.IE.offsetWidth - 25;
			InlinePopup.winInnerHeight = InlinePopup.IE.offsetHeight - 8;
		}
	},


	// öffnet neues Fenster innerhalb eines IFrames
	open : function (URL, winName, params) {
		// Viewport ausmessen
		InlinePopup.getBrowserDimensions();

		if (!URL || URL == "" || URL.match(/about[ ]*:[ ]*_?blank\b/gi))
			URL = InlinePopup.baseURL + "/blank.html";

		// Default-Einstellungen für das Fenster
		var ID = InlinePopup.idCount++;
		var settings = {
			width : Math.floor(InlinePopup.winInnerWidth / 2),
			height : Math.floor(InlinePopup.winInnerHeight / 2),
			statusbar : "no",
			resizable : "no",
			top : false,
			left : false,
			scrollbars : "yes",
			winName : "InlinePopup_" + ID,
			title : "Inline Popup"
		};

		if (typeof(window.frames[winName]) == "undefined" && winName != "")
			settings.winName = winName;

		// angegebene Parameter auswerten
		var parameters = params.split(",");
		for (var param in settings) {
			for (var i = 0; i < parameters.length; i++) {
				var test = parameters[i].split("=");
				if (test[0] == param)
					settings[param] = test[1];
			}
		}

		// letzte Mausposition, falls keine andere Angaben
		if (!settings.top)
			settings.top = InlinePopup.mouseLastCoords.top;
//			if (window.innerHeight)
//				settings.top = Math.ceil((InlinePopup.winInnerHeight - settings.height) / 2) + window.pageYOffset;
//			else
//				settings.top = Math.ceil((InlinePopup.winInnerHeight - settings.height) / 2) + InlinePopup.IE.scrollTop;

		if (!settings.left)
			settings.left = InlinePopup.mouseLastCoords.left;
//			if (window.innerHeight)
//				settings.left = Math.ceil((InlinePopup.winInnerWidth - settings.width) / 2) + window.pageXOffset;
//			else
//				settings.left = Math.ceil((InlinePopup.winInnerWidth - settings.width) / 2) + InlinePopup.IE.scrollLeft;

		var div = document.createElement("div");
		div.id = settings.winName;
		div.className = "InlinePopup";
		div.style.zIndex = 501 + ID;

		var contentHeight = settings.height - 17 - ((settings.statusbar == "yes") ? 17 : 0);
		var HTML = '<div class="InlinePopupWindow"';
		HTML += ' style="';
		HTML += 'width:' + settings.width + 'px;';
		HTML += 'height:' + settings.height + 'px;';
		HTML += 'top:' + settings.top + 'px;';
		HTML += 'left:' + settings.left + 'px;';
		HTML += '">';
		HTML += '<span class="InlinePopupTitlebar"';
		HTML += ' onmousedown="InlinePopup.dragStart(this, \'move\')"';
		HTML += ' onmouseup="InlinePopup.dragStop()">';
		HTML += '<img src="' + InlinePopup.baseURL + "/images/window_close.gif";
		HTML += '" onmouseup="InlinePopup.close(\'' + settings.winName + '\')" />';
		HTML += '<span>' + settings.title + '</span></span>';
		HTML += '<span class="InlinePopupBody" style="height:' + contentHeight + 'px">';
		HTML += '<iframe src="' + URL + '" name="' + settings.winName + '"';
		HTML += ' style="overflow:' + ((settings.scrollbars == "yes") ? 'auto' : 'hidden') + ';';
		HTML += 'height:' + contentHeight + 'px"></iframe>';
		HTML += '</span>';

		if (settings.statusbar == "yes")
			HTML += '<span class="InlinePopupStatusbar">&nbsp;</span>';

		if (settings.resizable == "yes") {
			HTML += '<span class="InlinePopupResizer"';
			HTML += ' onmousedown="InlinePopup.dragStart(this, \'resize\')"';
			HTML += ' onmouseup="InlinePopup.dragStop()"></span>';
		}

		HTML += '</div>';

		div.innerHTML = HTML;

		// Eventblocker erzeugen (Div mit spacer.gif, das den kompletten Anzeigebereich ausfüllt)
		var eventBlocker = document.createElement("div");
		eventBlocker.id = "InlinePopupEventBlocker_" + settings.winName;
		eventBlocker.style.width = InlinePopup.winInnerWidth + "px";
		eventBlocker.style.height = InlinePopup.winInnerHeight + "px";
		eventBlocker.style.position = "absolute";
		eventBlocker.style.top = "0px";
		eventBlocker.style.left = "0px";
		eventBlocker.style.position = "absolute";
		eventBlocker.style.overflow = "hidden";
		eventBlocker.style.zIndex = 501 + ID;

		var innerHTML = '<img src="' + InlinePopup.baseURL + '/images/spacer.gif" alt="" ';
		innerHTML += 'style="display: absolute; top: 0px; left: 0px; margin: 0px; padding: 0px; border: none; overflow: hidden; ';
		innerHTML += 'width: ' + InlinePopup.winInnerWidth + 'px; height: ' + InlinePopup.winInnerHeight + 'px;';
		innerHTML += '" />';

		eventBlocker.innerHTML = innerHTML;

		// Event-Blocker einfügen
		document.getElementsByTagName("body")[0].appendChild(eventBlocker);


		// Popup-Fenster einfügen
		document.getElementsByTagName("body")[0].appendChild(div);

		return window.frames[settings.winName];
	},

	// schließt ein "Fenster", indem es das dynamisch erzeugte <div> samt IFrame aus dem Dokument entfernt
	close : function (winName) {
		// Popup-Fenster (also sein umschließendes DIV) aus dem Dokument entfernen
		var elm = document.getElementById(winName);
		elm.parentNode.removeChild(elm);

		// Event-Blocker für das Popup-Fenster entfernen
		var eventBlocker = document.getElementById("InlinePopupEventBlocker_" + winName);
		eventBlocker.parentNode.removeChild(eventBlocker);

		return false;
	},

	// drag & drop starten
	dragStart : function (elm, mode) {
		if (!InlinePopup.dragElm || !InlinePopup.dragMode) {
			InlinePopup.dragMode = mode;
			// <div class="InlinePopupWindow">-Element finden
			var div = false;
			var parentElm = elm;
			while (!div) {
				parentElm = parentElm.parentNode;
				if (typeof(parentElm.tagName) != "undefined" && parentElm.tagName.toLowerCase() == "div")
					div = parentElm;
			}
			InlinePopup.dragElm = div;
		}
	},

	// Was soll per Drag&Drop getan werden?
	drag : function (e) {

		if (!e)
			e = window.event;

		var pos = {
			left : e.clientX,
			top : e.clientY
		};

		var IE = (window.document.compatMode && window.document.compatMode == "CSS1Compat") ?
			window.document.documentElement : window.document.body || null;

		if (IE) {
			pos.left += IE.scrollLeft;
			pos.top +=  IE.scrollTop;
		}

		// Abstand zu den letzten Mauskoordinaten berechnen
		var dx = InlinePopup.mouseLastCoords.left - pos.left;
		var dy = InlinePopup.mouseLastCoords.top - pos.top;
		// Mauskoordinaten speichern
		InlinePopup.mouseLastCoords = pos;

		switch (InlinePopup.dragMode) {
			// Fenstergröße ändern
			case "resize" :
				var iFrame = InlinePopup.dragElm.getElementsByTagName("iframe")[0];
				var iFrameHeight = parseInt(iFrame.style.height);
				var elmWidth = parseInt(InlinePopup.dragElm.style.width);
				var elmHeight = parseInt(InlinePopup.dragElm.style.height);

				InlinePopup.dragElm.style.width = elmWidth - dx + "px";
				InlinePopup.dragElm.style.height = elmHeight - dy + "px";
				iFrame.style.height = iFrameHeight - dy + "px";
				iFrame.parentNode.style.height = iFrameHeight - dx + "px";
			break;

			// Fenster verschieben
			case "move" :
				var top = parseInt(InlinePopup.dragElm.style.top);
				var left = parseInt(InlinePopup.dragElm.style.left);
				InlinePopup.dragElm.style.top = top - dy + "px";
				InlinePopup.dragElm.style.left = left - dx + "px";
			break;
		}
	},

	// drag & drop beenden
	dragStop : function () {
		InlinePopup.dragElm = false;
		InlinePopup.dragMode = false;
	},

	// Initialisierung
	init : function () {
		var baseURL = false;
		var scripts = document.getElementsByTagName("script");
		for (var i = 0; i < scripts.length; i++) {
			if (scripts[i].src && scripts[i].src.match(/inlinepopup.js$/gi))
				baseURL = scripts[i].src.replace(/\/inlinepopup.js$/gi, "");
		}
		InlinePopup.baseURL = baseURL;

		// InlinePopup-CSS einbinden
		var css = document.createElement("link");
		css.rel = "stylesheet";
		css.type = "text/css";
		css.media = "screen, projection";
		css.href = baseURL + "/css/inlinepopup.css";
		document.getElementsByTagName("head")[0].appendChild(css);

		// onmousemove umleiten
		InlinePopup.oldDocOnMouseMove = document.onmousemove;
		document.onmousemove = function (e) {
			if (typeof(InlinePopup.oldDocOnMouseMove) == "function")
				InlinePopup.oldDocOnMouseMove(e);
			InlinePopup.drag(e);
		}

		// Quirksmode des Internet Explorers ermitteln
		InlinePopup.IE = (document.compatMode && document.compatMode == "CSS1Compat") ? document.documentElement : document.body || null;
	}

};

// InlinePopup einbinden
InlinePopup.oldWinOnLoad = window.onload;

window.onload = function () {
	if (typeof(InlinePopup.oldWinOnLoad) == "function")
		InlinePopup.oldWinOnLoad();
	InlinePopup.init();
}
