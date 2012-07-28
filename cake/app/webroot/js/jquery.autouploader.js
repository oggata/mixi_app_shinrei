/**
 * jQuery auto uploader plugin
 * Copyright (C) KAYAC Inc. | http://www.kayac.com/
 * Dual licensed under the MIT <http://www.opensource.org/licenses/mit-license.php>
 * and GPL <http://www.opensource.org/licenses/gpl-license.php> licenses.
 * Date: 2008-12-05
 * @author kyo_ago <http://tech.kayac.com/archive/jquery-autouploader-plugin.html>
 * @version 1.0.1
 */

(function ($) {
	var name_space = 'autouploader';
	var param = (function () {
		var src = $('script[src*="'+name_space+'.js"]').attr('src');
		if (!src || src.indexOf('#') < 0) return {};
		var result = {};
		$.each(src.split('#').pop().split('&'), function () {
			var kv = this.split('=');
			result[kv[0]] = decodeURIComponent(kv[1]);
		});
		return result;
	})();
	$.fn[name_space] = function (options) {
		var settings = $.extend({
			'api' : '/api/image/set',
			'send_name' : 'img',
			'loading' : ''
		}, param, options);

		var form = $(this);
		if (!form.length) return;
		if (settings.loading) (new Image).src = settings.loading;
		form.find(':file').each(function () { var $_ = $(this), data = $.data(this);
			$_.change(function () { var $_ = $(this);
				var all_remove;
				var iframe = $_
					.after('<iframe name="'+name_space+data+'" src=""/>')
					.next('iframe')
					.addClass(name_space)
					.css({'width':0, 'height':0, 'border':'none'})
					.load(function () {
						if (!all_remove) return;
						try { load() }
						finally { all_remove() }
					})
				;
				function load () {
					var text = iframe.contents().find('body').text();
					if (!text) return;
					var uri = text.match(/[-\/\w.-?~%#=&:,]+\.\w\w+/);
					if (!uri) return;
					var div = my_load_func(uri.shift());
					div.find('button').click(function () {
						$_.val('');
						setTimeout(function () {
							div.remove();
						});
					});
				};
				var inform = $_
					.wrap('<form target="'+name_space+data+'" action="'+settings.api+'" method="post" enctype="multipart/form-data"/>')
					.parent('form')
					.addClass('autouploader')
				;
				if (settings.loading) inform.append('<img style="position:absolute" src="'+settings.loading+'"/>');
				form.bind('submit.'+name_space, function (e) {
					e.preventDefault();
				});
				setTimeout(function () {
					var name = $_.attr('name');
					$_.attr('name', settings.send_name);
					inform.submit();
					$_.attr('name', name);
					all_remove = function () {
						setTimeout(function () {
							iframe.remove();
							inform.replaceWith($_);
							form.unbind('submit.'+name_space);
							all_remove = function () {};
						}, 0);
					};
				}, 0);
				setTimeout(function () {
					all_remove();
				}, 5000);
			});
			function my_load_func (path) {
				var offset = $_.offset();
				var img_css = {
					'left' : (offset.left + $_.width() + 20) + 'px',
					'top' : (offset.top - 20) + 'px',
					'width' : '200px'
				};
				$('div.'+name_space+data).remove();
				var src = path + '?' + (new Date()).getTime();
				var div = $(document.body)
					.append('<div class="'+name_space+'-wrap"><span class="preview-msg">プレビュー画像</span><button>削除</button><img src="'+src+'"/></div>')
					.find('div.'+name_space+'-wrap:last')
					.addClass(name_space+data)
					.css(img_css)
					.css({
						'position' : 'absolute',
						'border' : '1px solid',
						'background-color' : '#FFF'
					})
					.fadeTo(0, 0.75)
				;
				div.find('img').css(img_css);
				if (!window.Draggable && !div.draggable) return div;
				(window.Draggable && new window.Draggable(div.get(0)))
				 ||
				(div.draggable && div.draggable());
				div.css('cursor', 'move');
				return div;
			}
		});

		return this;
	};
	if (!param.no_exec) $(function () { $('form[enctype="multipart/form-data"]').autouploader(); });
})(jQuery);
