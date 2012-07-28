// =============================================================================
// XMLParser version 0.1.0
// (c) 2006 Amonya.com shizumaru
// 
// web site: http://ecs.amonya.com/
// license : X11 license
// =============================================================================
// 2006/04/04 ver0.1.0 new release
// =============================================================================

// XMLParser
// node name is '$ + xml tag name'
var XMLParser = {
	parse: function(documentElement){
		return new XMLParser.Element(documentElement);
	}
};

XMLParser.NODE_TYPE = {
	ELEMENT_NODE: 1,
	ATTRIBUTE_NODE: 2,
	TEXT_NODE: 3,
	CDATA_SECTION_NODE: 4,
	ENTITY_REFERENCE_NODE: 5,
	ENTITY_NODE: 6,
	PROCESSING_INSTRUCTION_NODE: 7,
	COMMENT_NODE: 8,
	DOCUMENT_NODE: 9,
	DOCUMENT_TYPE_NODE: 10,
	DOCUMENT_FRAGMENT_NODE: 11,
	NOTATION_NODE: 12
};


// XMLParser.Element
XMLParser.Element = function(documentElement){
//	if (!documentElement || !documentElement.nodeName){return null;}
	this['$'+documentElement.nodeName] = new XMLParser.Node(documentElement);
}

XMLParser.Element.prototype = {
	// example: path('nodeA->nodeB->0->nodeC')
	path: function(path){
		var nodes = path.split('->');
		var element = this;
		for (var i = 0; i < nodes.length; i++){
			if (nodes[i].match('[^0-9]')){
				element = element['$'+nodes[i]];
			}else{
				element = element[nodes[i]];
			}
			if (!element){return null;}
		}
		return element;
	},
	dump: function(space_num){
		space_num = (space_num) ? space_num : 0;
		var space = '';
		for (var i = 0; i < space_num; i++){
			space += '-';
		}
		
		var result = [];
		for (var property in this){
			if (this[property] instanceof Array){
				for (var i = 0; i < this[property].length; i++){
					result.push(this._dump(this[property][i], space));
					result = result.concat(this[property][i].dump(space_num+1));
				}
			}else if ((this[property] instanceof XMLParser.Node) && property != 'parent'){
				result.push(this._dump(this[property], space));
				result = result.concat(this[property].dump(space_num+1));
			}
		}
		return result;
	},
	dumpHTML: function(){
		var result = this.dump();
		for (var i = 0; i < result.length; i++){
			result[i] = result[i].replace(/</g, '&lt;');
			result[i] = result[i].replace(/>/g, '&gt;');
		}
		return result.join('<br />');
	},
	_dump: function(node, space){
		var result = '';
		result += space + '[' + node.name + ']';
		if (node.value){
			result += '="' + node.value + '"';
		}
		if (node.attributes.length > 0){
			result += ' (';
			for (var i = 0; i < node.attributes.length; i++){
				result += (i == 0) ? '' : ', ';
				result += node.attributes[i].name + '="' + node.attributes[i].value + '"';
			}
			result += ')';
		}
		return result;
	}
};


// XMLParser.Node
XMLParser.Node = function (node, parent){
	this.parent = (parent instanceof XMLParser.Node) ? parent : null;
	this.attributes = node.attributes;
	this.name = node.nodeName;
	this.value = '';
	
	for (var i = 0; i < node.childNodes.length; i++){
		var n = node.childNodes[i];
		switch (n.nodeType){
		case XMLParser.NODE_TYPE.TEXT_NODE :
		case XMLParser.NODE_TYPE.CDATA_SECTION_NODE :
			var value = n.nodeValue;
			if (n.nodeType == XMLParser.NODE_TYPE.TEXT_NODE){
				value = value.replace(/[\r\n\t]/g, '');
			}
			if (value){
				this.value = value;
			}
			break;
		default: 
			var name = '$'+n.nodeName;
			if (this[name]){
				if (!(this[name] instanceof Array)){
					var b = this[name];
					this[name] = [];
					this[name].push(b)
				}
				this[name].push(new XMLParser.Node(n, this));
			}else{
				this[name] = new XMLParser.Node(n, this);
			}
			break;
		}
	}
};

XMLParser.Node.prototype = {
	getParent: function(){
		return this.parent;
	},
	getAttribute: function(name){
		return this.attributes.getNamedItem(name);
	},
	getAttributes: function(){
		return this.attributes;
	},
	path: function(path){
		return XMLParser.Element.prototype.path.apply(this, arguments);
	},
	dump: function(){
		return XMLParser.Element.prototype.dump.apply(this, arguments);
	},
	dumpHTML: function(){
		return XMLParser.Element.prototype.dumpHTML.apply(this, arguments);
	},
	_dump: function(){
		return XMLParser.Element.prototype._dump.apply(this, arguments);
	}
};
