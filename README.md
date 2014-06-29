RSSGET
=================================
  複数RSS（xml）の取得  
  ※atomなどの画像は現在考慮中。RDFも…

Setting
---------------------------------
1. head 内でjQueryを読み込んだあとにjquery.rssget.jsを読み込む  
2.RSSを出力したいところをターゲットにscriptを挿入  
  $('.target').RSSGet({  
    options : …,  
    options : …  
  });  


options
---------------------------------

###	site
	default  
	site         : ['']  
	how_to  
	site         : ['http://test.com/xxx.xml','http://test.com/yyy.xml']  


###	link_show
	default  
	link_show    : true  
	how_to  
	link_show    : true or false  


###	link_target
	default  
	link_target  : '_blank'  
	how_to  
	link_target  : '_blank' or '_self' or …  


###	title_show
	default  
	title_show   : true  
	how_to  
	title_show   : true or false  


###	date_show
	default  
	date_show    : true  
	how_to  
	date_show    : true or false  


###	date_fmt
	default  
	date_fmt     : 'Y年N月j日'  
	how_to  
	date_fmt     : 'Y.m.d'…  
	about //　<http://www.php.net/manual/ja/function.date.php>  


###	desc_show(description)
	default  
	desc_show    : true  
	how_to  
	desc_show    : true or false  


###	desc_lengths
	default  
	desc_lengths : 25  
	how_to  
	desc_lengths : 表示したい説明文の文字数  


###	sort_for
	default  
	sort_for     : 'date'  
	how_to  
	sort_for     : 'date' or 'title'  
	※CMSなどでタイトルに日付を入れている場合などに'title'を使用  
	　その際、data_fmtは'Y.m.d'に設定する  


###	sort_to
	default  
	sort_to      : 'ascend'  
	how_to  
	sort_to      : 'ascend' or 'descend'  


###	count
	default  
	count        : 5   
	how_to  
	count        : 表示したい件数  
