// MARKKIT
// Copyright 2007 (c) Slim Amamou <slim.amamou@gmail.com>

if (!window['Node']) {
    window.Node = new Object();
	Node.ELEMENT_NODE = 1;
	Node.ATTRIBUTE_NODE = 2;
	Node.TEXT_NODE = 3;
	Node.CDATA_SECTION_NODE = 4;
	Node.ENTITY_REFERENCE_NODE = 5;
	Node.ENTITY_NODE = 6;
	Node.PROCESSING_INSTRUCTION_NODE = 7;
	Node.COMMENT_NODE = 8;
	Node.DOCUMENT_NODE = 9;
	Node.DOCUMENT_TYPE_NODE = 10;
	Node.DOCUMENT_FRAGMENT_NODE = 11;
	Node.NOTATION_NODE = 12;
}

/**
 * This is an implementation of http://www.w3.org/TR/ElementTraversal/
 * Known issues:
 *   - entity reference nodes are ignored
 *   - setting isn't treated the same way as native readonly attributes
 */

if (!Element.prototype.__lookupGetter__("parentElement")) {
  Element.prototype.__defineGetter__("parentElement", function() {
    var e = this.parentNode;
    while (e) {
      if (e.nodeType == 1)
        return e;
      e = e.parentNode;
    }
    return null;
  });
}

if (!Element.prototype.__lookupGetter__("firstElementChild")) {
  Element.prototype.__defineGetter__("firstElementChild", function() {
    var e = this.firstChild;
    while (e) {
      if (e.nodeType == 1)
        return e;
      e = e.nextSibling;
    }
    return null;
  });
}

if (!Element.prototype.__lookupGetter__("lastElementChild")) {
  Element.prototype.__defineGetter__("lastElementChild", function() {
    var e = this.lastChild;
    while (e) {
      if (e.nodeType == 1)
        return e;
      e = e.previousSibling;
    }
    return null;
  });
}

if (!Element.prototype.__lookupGetter__("previousElementSibling")) {
  Element.prototype.__defineGetter__("previousElementSibling", function() {
    var e = this.previousSibling;
    while (e) {
      if (e.nodeType == 1)
        return e;
      e = e.previousSibling;
    }
    return null;
  });
}

if (!Element.prototype.__lookupGetter__("nextElementSibling")) {
  Element.prototype.__defineGetter__("nextElementSibling", function() {
    var e = this.nextSibling;
    while (e) {
      if (e.nodeType == 1)
        return e;
      e = e.nextSibling;
    }
    return null;
  });
}

if (!Element.prototype.__lookupGetter__("childElementCount")) {
  Element.prototype.__defineGetter__("childElementCount", function() {
    var e = this.firstChild;
    var count = 0;
    while (e) {
      if (e.nodeType == 1)
        count++;
      e = e.nextSibling;
    }
    return count;
  });
}

MARKKIT = { me: "incongnito", 
		    markid: 0,
			defaultLinkURL: "http://markkit.net/search/?cx=004024948312479226434%3Ax7cycutkoxy&cof=FORID%3A9&sa=Search&q=",
			setuser: function(xhr) { MARKKIT.me = xhr.responseText || 'anonymous'; }
		   }

function NodePath(root) {
	var rootNode = root || document.body,
		nodeNumbers = [];
	
	this.toString = function() {
		return nodeNumbers.join(',');
	}

	this.addParent = function(nodeNumber) {
		var oldNumbers = nodeNumbers, i;
		
		nodeNumbers = [nodeNumber]; // FIXME this should be a method of Array
		for (i=0; i < oldNumbers.length; i++) {
			nodeNumbers[i+1] = oldNumbers[i];
		}
		
		return this;
	}
	
	this.addChild = function(nodeNumber) {
		nodeNumbers[nodeNumbers.length] = nodeNumber;
		
		return this;
	}
	
	this.getNode = function() {
		var i, n;
		
		n = rootNode;
		for (i=0; i < nodeNumbers.length - 1; i++) {
			num = nodeNumbers[i].toString().split('#')[0];
			id = nodeNumbers[i].toString().split('#')[1];
			if (id) {
				n = document.getElementById(id);
			} else {
				n = n.getElementChild(num);
			}
		}
		lastnum = nodeNumbers[nodeNumbers.length - 1].toString().split('#')[0];
		n = n.getChild(lastnum);
		
		return n;
	}
	
	this.getNumbers = function() {
		return nodeNumbers;
	}

	this.setNumbers = function(numbers) {
		nodeNumbers = numbers;
		return this;
	}
	
	this.length = function() {
		return nodeNumbers.length;
	}
	
	this.last = function() {
		return nodeNumbers[nodeNumbers.length - 1];
	}
	
	this.eq = function(otherNodePath) {
		var i;
		
		if (! (otherNodePath instanceof NodePath)) {
			return false;
		}
		
		othersNodeNumbers = otherNodePath.getNumbers();
		if (othersNodeNumbers.length != nodeNumbers.length) {
			return false;
		}
		
		for (i=0; i < nodeNumbers; i++) {
			if (nodeNumbers[i] != othersNodeNumbers[i]) {
				return false;
			}
		}
		
		return true;
	}
}

Node.prototype.siblingNumber = function () {
	var e = this
		i = 0; 
	while (e = e.previousSibling) {
		if (e.isNotOriginal) {
			continue;
		}
		i++;
	}
	
	return i;
}

Node.prototype.elementSiblingNumber = function () {
	var e = this
		i = 0; 
	while (e = e.previousElementSibling) {
		if (e.isNotOriginal) {
			continue;
		}
		i++;
	}
	
	return i;
}

Node.prototype.getChild = function (number) {
	var e, i; 
	
	e = this.firstChild;
	for ( i=1; i <= number; i++) {
		e = e.nextSibling;
		while (e.isNotOriginal) {
			e = e.nextSibling;
		}
	}
	
	return e;
}

Node.prototype.getElementChild = function (number) {
	var e, i; 
	
	e = this.firstElementChild;
	for ( i=1; i <= number; i++) {
		e = e.nextElementSibling;
		while (e.isNotOriginal) {
			e = e.nextElementSibling;
		}
	}
	
	return e;
}

Node.prototype.getNodePath = function(root) {
	var np = new NodePath(root);
	
	nodeid = this.siblingNumber();
	if (this.id) nodeid = nodeid+"#"+this.id;
	np.addParent(nodeid);
	e = this.parentNode;
	while (e != root) {
		eid = e.elementSiblingNumber();
		if (e.id) eid = eid+"#"+e.id;
		np.addParent(eid);
		e = e.parentNode;
	}
	
	return np;
}

Range.prototype.getMark = function(root) {
	var location = new Mark;
	
	if (! root) {
		root = document.body;
	}
	location.startNodePath = this.startContainer.getNodePath(root);
	location.endNodePath = this.endContainer.getNodePath(root);
	location.startOffset = this.startOffset;
	location.endOffset = this.endOffset;
	
	return location;
}

Range.prototype.setMark = function(location) {
	var startNode, startOffset, endNode, endOffset;

	startNode   = location.startNodePath.getNode();
	endNode     = location.endNodePath.getNode();
	startOffset = location.startOffset;
	endOffset   = location.endOffset;
		
	this.setStart(startNode, startOffset);
	this.setEnd(endNode, endOffset);
} 


function textSelected() {
	selection = window.getSelection();
	textRange = document.createRange();
	if ( ! selection.isCollapsed) {
		textRange = selection.getRangeAt(0);
	}
	selection.removeAllRanges();
	
	return textRange;
}

function unmark(markId) {
	markElement = document.getElementById(markId);
	markElement.parentNode.replaceChild(markElement.firstChild, markElement);
}

function Mark(location) {
	
	var id = 'mark_' + MARKKIT.markid++;
	this.text = '';
	
	this.pageUrl = document.location.href;
	this.owner = MARKKIT.me;
	this.startNodePath = new NodePath(document.body);
	this.startOffset = 0;
	this.endNodePath = new NodePath(document.body);
	this.endOffset = 0;

	this.showLinkOptions = function (event) {
	overlib('<a class=\'marked\' onclick=\'unmark("' + id + '")\' >unmark</a>', STICKY, MOUSEOFF, TIMEOUT, 2000, WRAP, CELLPAD, 5, FGCOLOR, 'white', BGCOLOR, 'red');
	}
	
	this.toString = function () {
		return this.text;
	}
	
	this.save = function(markServer) {
		markServer.saveMark(this);
	}
	
	this.createLink = function() {
		var range = document.createRange();

		range.setMark(this);
		this.text = range.toString();
		this.link = document.createElement("a");
		this.link.isNotOriginal = true;
		this.link.id = id;
		this.link.href = MARKKIT.defaultLinkURL + encodeURIComponent(range);
		this.link.style.background = "yellow";
		if (this.text.length < 15) {
			this.link.style.color = "blue";
		} else {
			this.link.style.textDecoration = "none";
		}
		this.link.addEventListener( "mouseover", this.showLinkOptions, false);
		range.surroundContents(this.link);
	}

	if (location instanceof Range) {
		this.startNodePath = location.getMark().startNodePath;
		this.startOffset = location.getMark().startOffset;
		this.endNodePath = location.getMark().endNodePath;
		this.endOffset = location.getMark().endOffset;
		this.createLink();
	} 

}

parseJSONmarks = function(json) {
	var marks = [];

	eval("locations="+json);
	for (mn=0; mn < locations.length; mn++ ) {
		marks[mn] = new Mark();
		marks[mn].pageUrl = locations[mn].pageUrl;
		marks[mn].startNodePath = new NodePath().setNumbers(locations[mn].startNodePath.split(','));
		marks[mn].startOffset = locations[mn].startOffset;
		marks[mn].endNodePath = new NodePath().setNumbers(locations[mn].endNodePath.split(','));
		marks[mn].endOffset = locations[mn].endOffset;
		marks[mn].createLink();
	}

	return marks;
}

function MarkServer(saveUrl, loadUrl) {
	var saveUrl = saveUrl,
		loadUrl = loadUrl;
		
		
	this.loading = false;
	MarkServer.prototype.marks = [];

	this.handleRequest = function(transport) {
		MarkServer.prototype.marks = parseJSONmarks(transport.responseText);
		this.loading = false;
	}
	
	this.loadMarks = function (pageUrl) {
		var pageUrl = pageUrl || document.location.href;
		this.loading = true;
		handleRequest = this.handleRequest;
		request = new Ajax.Request(loadUrl, {
			method: "GET",
			onSuccess: handleRequest
		});

		return MarkServer.marks;
	}
	
	this.saveMark = function (mark) {
		var serverMark = { pageUrl: mark.pageUrl,
						   startNodePath: mark.startNodePath.toString(),
						   endNodePath: mark.endNodePath.toString(),
						   startOffset: mark.startOffset,
						   endOffset: mark.endOffset,
						   text: mark.toString(),
						   owner: mark.owner };

		new Ajax.Request(saveUrl, {
			method: "POST",
			parameters: serverMark
		});
	}
}

function markSelected(event) {
	selection = textSelected();
	if ( ! selection.collapsed) {
		mark = new Mark(selection);
		mark.save(pageMarkServer);
	}
}

function showRange(event) {
	selection = textSelected();
	alert("start : " + selection.startContainer.parentNode + "/" + selection.startOffset + " end : " + selection.endContainer + "/" + selection.endOffset);
}

function gup( name ) {
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}

function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ((!start) && (name != document.cookie.substring(0, name.length))) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ';', len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}
