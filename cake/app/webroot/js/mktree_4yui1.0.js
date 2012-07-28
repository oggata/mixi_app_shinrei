// ツリー用のデータをJavaScriptの配列データとしてツリー構造に作成するだけで、
// メニューツリーを作成するYUI用の支援ツール
// mktree_4yui1.0.js
// http://jsgt.org/mt/01/
//
YAHOO.namespace('tato');//カスタマイズした関数など用に名前空間を用意しておきます
YAHOO.tato.tree = function(id) {
	
	this.tree = new YAHOO.widget.TreeView(id); //idはツリーを表示するDIVのID名です
	
	//Tree描画	by Array
	YAHOO.tato.tree.prototype.mkTreeByArray = function (treeData,treeNode){
	
		if(!treeNode)treeNode = this.tree.getRoot(); 
		
		for(var i in treeData){
		
			if(treeData[i][0]){
			
				if(!(treeData[i][0]=="_open"||treeData[i][0]=="_close"||treeData[i][0]=="_load"))
					var tmpNode = new YAHOO.widget.TextNode(""+treeData[i][0],treeNode, 
						(treeData[i][1])?(treeData[i][1][0]=="_open"):false);
				
				if(typeof treeData[i][1] == "string"){
				
					if(YAHOO.tato.templateForMakeTreeATagAttr)
						templateForMakeTreeATagAttr(tmpNode,treeData[i]);

				} else 
				if(typeof treeData[i][1] == "object"){

					var swt = treeData[i][1][0];
					if(typeof swt == "object"){swt = treeData[i][1][0][0]}
					switch(swt){
						case		"_open"	 : tmpNode.expand();break;
						case		"_close" : tmpNode.collapse();break;
						case		"_load"	 : YAHOO.tato.loadTreeData(this,tmpNode,treeData[i]);break;
						dafault				 : tmpNode.collapse();break;
					}
					if(swt != "_load"){this.mkTreeByArray(treeData[i][1],tmpNode); }
				}
			}
		}
		this.tree.draw();
	}
}

YAHOO.tato.loadTreeData = function(oj,tmpNode,treeDataFrg){
	if(!!YAHOO.util.Connect){
			if(treeDataFrg[1][0][1]){
				tmpNode.method=(treeDataFrg[1][0][1].method)?treeDataFrg[1][0][1].method:"GET";
				tmpNode.url=(treeDataFrg[1][0][1].url)?treeDataFrg[1][0][1].url:"";
			}
			tmpNode.setDynamicLoad(
				function (node,onCompleteCallback ){
					tmpNode =new YAHOO.widget.Node("",tmpNode.pearent,false);
					var delay = YAHOO.tato.loadTreeData.delay ;
					if(YAHOO.tato.loadTreeData.delay>0)setTimeout(onCompleteCallback,delay);
					else onCompleteCallback();
				}
			);
			
			oj.tree.onExpand= function(node) {
				if(tmpNode.label==node.label)
				if(node.children.length<=0){
					YAHOO.util.Connect.asyncRequest(node.method,node.url,{
						argument:{'node':node},scope:oj,success: getResponse
					},null);
				}
			}
			getResponse= function(oj){//alert(oj.argument.node.hasChildren(true))
				data = eval(oj.responseText);
				this.mkTreeByArray (data,oj.argument.node); 
			} 
	}
}
YAHOO.tato.loadTreeData.delay = 100;

//ツリー出力関数
YAHOO.tato.mkTree = function(menuId,arrayData,delay){

	if(!delay)delay=YAHOO.tato.loadTreeData.delay ;
	
	//"treeDiv1"はメニューを出力するDIV名です
	YAHOO.tato.test1 = new YAHOO.tato.tree(menuId);	
	//ここでメニュー用JSONデータを渡します
	YAHOO.tato.test1.mkTreeByArray(arrayData);	 
	// Ajaxロードを100/1000秒遅らせてアイコンを表示します。
	//省略か0にすれば、遅延無し。
	YAHOO.tato.loadTreeData.delay =delay; 
	
	//Array dataの読み込みとTree置換え
	YAHOO.tato.replaceTree = function(url){
		YAHOO.util.Connect.asyncRequest("POST", url,{
			success : function(oj){
				YAHOO.tato.mkTree(menuId,eval(oj.responseText),delay);
			}
		});
	}
}

//簡易extendメソッド
YAHOO.tato.e = function(oj1,oj2){for(var i in oj2)oj1[i]=oj2[i];return oj1}
//キャッシュ付ちょい$関数 http://jsgt.org/mt/archives/01/001175.html
YAHOO.tato.$ = function (e){return YAHOO.tato.$[e]||(YAHOO.tato.$[e]=(document.getElementById(e)||e))}
