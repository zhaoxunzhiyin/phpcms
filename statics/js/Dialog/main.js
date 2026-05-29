(function() {
	function getCurrentScript(base) {
        if (document.currentScript) {  
            return document.currentScript.src; //FF,Chrome  
        };  
        var stack;
        try {
            a.b.c(); //强制报错,以便捕获e.stack
        } catch (e) { //safari的错误对象只有line,sourceId,sourceURL
            stack = e.stack;
            if (!stack && window.opera) {
                //opera 9没有e.stack,但有e.Backtrace,但不能直接取得,需要对e对象转字符串进行抽取
                stack = (String(e).match(/of linked script \S+/g) || []).join(" ");
            }
        }
        if (stack) {
            /**e.stack最后一行在所有支持的浏览器大致如下:
             *chrome23:
             * at http://hostname/data.js:4:1
             *firefox17:
             *@http://hostname/query.js:4
             *opera12:http://www.oldapps.com/opera.php?system=Windows_XP
             *@http://hostname/data.js:4
             *IE10:
             *  at Global code (http://hostname/data.js:4:1)
             *  //firefox4+ 可以用document.currentScript
             */
            stack = stack.split(/[@ ]/g).pop(); //取得最后一行,最后一个空格或@之后的部分
            stack = stack[0] === "(" ? stack.slice(1, -1) : stack.replace(/\s/, ""); //去掉换行符
            return stack.replace(/(:\d+)?:\d+$/i, ""); //去掉行号与或许存在的出错字符起始位置
        }
        var nodes = (base ? document : head).getElementsByTagName("script"); //只在head标签中寻找
        for (var i = nodes.length, node; node = nodes[--i]; ) {
            if (node.readyState === "interactive") {
                return node.src;
            }
        }
    }
	var _Cms = window.Cms;
    var jspath = getCurrentScript(true);
	var scripts = document.getElementsByTagName('script'),
		script = scripts[scripts.length - 1];
    if(!jspath){
		jspath = script.hasAttribute ? script.src : script.getAttribute('src', 4); //ie下通过getAttribute('src', 4)才能获取全路径
	}
    var contextPath = script.getAttribute('contextpath');
    script=null;
	//将URI处理为符合变量命名规则的字符串，可作前缀用于创建各页面不同复的命名空间或对象
/*
	<script src="main.js" cofnig="context:frontend">
	配置项说明:
	context:frontend|backend 默认为后台
	namespace:符合变量命名规则的字符串；当值为‘window’时为特殊情况，即复制Cms下所有对象到window下，用于省略根命名空间引用对象。
	debug:yes|no是否开启调试，默认为no
*/
	var z = {
		version: '3.0',
		JSLIBPATH: jspath.substr(0, jspath.lastIndexOf('/') + 1),
		Config: {
			namespace: 'window',
			context: 'backend',
			debug: 'no',
			skin: 'default'
		}
	};

	if(z.JSLIBPATH.indexOf(location.protocol + '//' + location.host + '/')==0){
		z.JSLIBPATH=z.JSLIBPATH.replace(location.protocol + '//' + location.host,'');
	}
	if (_Cms && _Cms.version === z.version && _Cms.JSLIBPATH === z.JSLIBPATH) {
		return; //防止重复加载
	} else {
		window.Cms = z;
	}
    z.startTime= +new Date();
	//再外部没配置应用路径时才使用默认路径
	if(!(z.CONTEXTPATH=contextPath)){
		z.CONTEXTPATH = z.JSLIBPATH.replace(/[^\/]+\/?$/, '');
		if(z.CONTEXTPATH.indexOf('/preview/') != -1){
			z.CONTEXTPATH=z.CONTEXTPATH.substr(0,z.CONTEXTPATH.indexOf('preview/'));
		}
	}
	if(z.CONTEXTPATH.indexOf(location.protocol + '//' + location.host + '/')==0){
		z.CONTEXTPATH=z.CONTEXTPATH.replace(location.protocol + '//' + location.host,'');
	}
	
	/**
	 加载脚本
	 url:js文件路径，因有加z.PATH，所以路径是相对于js框架根目录开始
	 **/
	z.importJS = z.importJs = function(url) {
		if (!/^\/|^\w+\:\/\//.test(url)) {
			url = z.JSLIBPATH + url;
		}
		document.write('<script type="text/javascript" src="' + url + '"><\/script>');
	};
	/**
	 异步加载CSS文件
	 url:css文件路径，相对于引用js框架的页面，如果要从js框架根目录开始引用需自行加上z.JSLIBPATH
	 **/
	//往指定的同源页面窗口加载样式文件（求url为相对于win中页面的地址）
	z.loadCSS = z.loadCss = function(url,win) {
			win=win&&z.isWindow(win)?win:window;
			var document=win.document;
			
			var head = document.getElementsByTagName('head')[0] || document.documentElement;
			if (document.createStyleSheet) {//注意：IE11的不再支持document.createStyleSheet
				document.createStyleSheet(url);
			} else {
				var e = document.createElement('link');
				e.rel = 'stylesheet';
				e.type = 'text/css';
				e.href = url;
				head.appendChild(e);
			}
	};
	/**
	 加载CSS文件
	 url:css文件路径，因有加z.PATH，所以路径是相对于js框架根目录开始
	 **/
	z.importCSS = z.importCss = function(url,win) {
		win=win&&z.isWindow(win)?win:window;
		var document=win.document;
		
		if (!/^\/|^\w+\:\/\//.test(url)) {
			url = z.JSLIBPATH + url;
		}
		if (!document.body || document.readyState == 'loading') {
			document.write('<link rel="stylesheet" type="text/css" href="' + url + '" />');
		} else {
			z.loadCSS(url);
		}

	};
	z.importCSS('scrollbar.css');
	z.importCSS('default.css');
	z.importCss('components.css');
	if(!window.jQuery){
		z.importJs('jquery.min.js');
	}
	z.importJs('core.min.js');
	z.importJs('components.min.js');
})();