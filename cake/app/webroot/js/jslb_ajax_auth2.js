//==============================================================================
//  SYSTEM      :  暫定版クロスブラウザAjax用ライブラリ
//  PROGRAM     :  XMLHttpRequestによる送受信を行います
//                 open()ではなくリクエストヘッダでAuthするためのブランチ。
//                 高度な JavaScript 技集のbase64エンコードを利用させていただい
//                 てます。
//                 http://www.onicos.com/staff/iz/amuse/javascript/expert/
//  FILE NAME   :  ajasql_ajaxXXX_auth2.js
//  CALL FROM   :  Ajax クライアント
//  AUTHER      :  Toshirou Takahashi http://jsgt.org/mt/01/
//  SUPPORT URL :  http://jsgt.org/mt/archives/01/000409.html
//  CREATE      :  2005.6.26
//  UPDATE      :  v0.36_auth2 2005.7.25 リクエストヘッダでAuthorization
//  UPDATE      :  v0.36 2005.7.20 (oj.setRequestHeader)がwinieでunknown
//                  を返すことが判明し修正（unknownなのに、動作はします）
//  UPDATE      :  v0.35 2005.7.19 POSTのContent-Type設定をOpera8.01対応
//  UPDATE      :  v0.34 2005.7.16 sendRequest_auth2()にuser,password引数を追加
//  UPDATE      :  v0.33 2005.7.3  Query Component(GET)の&と=以外を
//                                encodeURIComponentで完全エスケープ。
//  TEST-URL    :  ヘッダ http://jsgt.org/ajax/ref/lib/test_head.htm
//  TEST-URL    :  認証   http://jsgt.org/mt/archives/01/000428.html
//  TEST-URL    :  非同期 
//        http://allabout.co.jp/career/javascript/closeup/CU20050615A/index.htm
//  TEST-URL    :  SQL     http://jsgt.org/mt/archives/01/000392.html
//------------------------------------------------------------------------------
// 最新情報 http://jsgt.org/mt/archives/01/000409.html 
// 上記コメント削除不可。商用利用、改造、自由。連絡不要。改造情報は
// 下記へ追加してください。
//
//

	////
	// XMLHttpRequestオブジェクト生成
	//
	// @sample           oj = createHttpRequest()
	// @return           XMLHttpRequestオブジェクト
	//
	function createHttpRequest()
	{
		if(window.ActiveXObject){
			 //Win e4,e5,e6用
			try {
				return new ActiveXObject("Msxml2.XMLHTTP") ;
			} catch (e) {
				try {
					return new ActiveXObject("Microsoft.XMLHTTP") ;
				} catch (e2) {
					return null ;
	 			}
	 		}
		} else if(window.XMLHttpRequest){
			 //Win Mac Linux m1,f1,o8 Mac s1 Linux k3用
			return new XMLHttpRequest() ;
		} else {
			return null ;
		}
	}
	
	////
	// 送信関数
	//
	// @sample           sendRequest_auth2(onloaded,'&prog=1','POST','./about2.php',true,true)
	// @param callback   受信時に起動する関数名
	// @param data	     送信するデータ
	// @param method     "POST" or "GET"
	// @param url        リクエストするファイルのURL
	// @param async	     非同期ならtrue 同期ならfalse
	// @param sload	     スーパーロード trueで強制、省略またはfalseでデフォルト
	// @param user	     認証ページ用ユーザー名
	// @param password   認証ページ用パスワード
	//
	function sendRequest_auth2(callback,data,method,url,async,sload,user,password)
	{
		//XMLHttpRequestオブジェクト生成
		var oj = createHttpRequest();
		if( oj == null ) return null;
		
		//強制ロードの設定
		var sload = (!!sendRequest_auth2.arguments[5])?sload:false;
		if(sload)url=url+"?t="+(new Date()).getTime();
		
		//ブラウザ判定
		var ua = navigator.userAgent;
		var safari	= ua.indexOf("Safari")!=-1;
		var konqueror = ua.indexOf("Konqueror")!=-1;
		var mozes	 = ((a=navigator.userAgent.split("Gecko/")[1] )
				?a.split(" ")[0]:0) >= 20011128 ;
		
		//受信処理
		//operaはonreadystatechangeに多重レスバグがあるのでonloadが安全
		//Moz,FireFoxはoj.readyState==3でも受信するので通常はonloadが安全
		//Win ieではonloadは動作しない
		//Konquerorはonloadが不安定
		//参考http://jsgt.org/ajax/ref/test/response/responsetext/try1.php
		if(window.opera || safari || mozes){
			oj.onload = function () { callback(oj); }
		} else {
		
			oj.onreadystatechange =function () 
			{
				if ( oj.readyState == 4 ){
					callback(oj);
				}
			}
		}

		//URLエンコード
		if(method == 'GET') {
			if(data!=""){
				//&と=で一旦分解しencode
				var encdata = '';
				var datas = data.split('&');
				//
				for(i=0;i<datas.length;i++)
				{
					var dataq = datas[i].split('=');
					encdata += '&'+encodeURIComponent(dataq[0])+'='+encodeURIComponent(dataq[1]);
				}
				url=url + encdata;
			}
		}
		
		//open メソッド
		oj.open(method,url,async);


		//base64 Basic認証
		var b64 = base64encode(user+':'+password)
		oj.setRequestHeader('Authorization','Basic '+b64);
		
		
		//ヘッダセット
		if(method == 'POST') {
		
			//このメソッドがWin Opera8.0でエラーになったので分岐(8.01はOK)
			if(!window.opera){
				oj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			} else {
				if((typeof oj.setRequestHeader) == 'function')
					oj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			}
		} 

		//デバック
		//alert("////jslb_ajaxxx.js//// \n data:"+data+" \n method:"+method+" \n url:"+url+" \n async:"+async);
		
		//send メソッド
		oj.send(data);
		

	}

