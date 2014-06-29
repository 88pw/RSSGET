(function($){
	$.fn.RSSGet = function(opt){
		
		// デフォルトの設定
		var def = {
			site         : '',
			link_show    : true,
			link_target  : '_blank',
			title_show   : true,
			date_show    : true,
			date_fmt     : 'Y年N月j日', 
			desc_show    : true,
			desc_lengths : '25',
			sort_for     : 'date',
			sort_to      : 'ascend',
			count        : 5
		}
		
		// def配列とオプションで指定したものをミックス
		var option = $.extend({}, def, opt);
		// success内で$(this)が読み込まれないので代入
		var target = $(this);

		// 受け渡し処理
		$.ajax({
			type: 'POST',
			url: 'php/rss_get.php',
			async: false,
			data:{
				site         : option.site,
				link_show    : option.link_show,
				link_target  : option.link_target,
				title_show   : option.title_show,
				date_show    : option.date_show,
				date_fmt     : option.date_fmt,
				desc_show    : option.desc_show,
				desc_lengths : option.desc_lengths,
				sort_for     : option.sort_for,
				sort_to      : option.sort_to,
				count        : option.count
			},
			success:function(data){ target.append(data) },
			error:function(){ alert('can not read') }
		});
	}
}(jQuery));