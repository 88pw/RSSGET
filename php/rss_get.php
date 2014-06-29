<?php

xmlget(
	$_POST['site'],
	$_POST['link_show'],
	$_POST['link_target'],
	$_POST['title_show'],
	$_POST['date_show'],
	$_POST['date_fmt'],
	$_POST['desc_show'],
	$_POST['desc_lengths'],
	$_POST['sort_for'],
	$_POST['sort_to'],
	$_POST['count']
);


function xmlget($site,$link_show,$link_target,$title_show,$date_show,$date_fmt,
	$desc_show,$desc_lengths,$sort_for,$sort_to,$count){
	
	$ds = array();
	$cnt = $count;
	$ary_cnt = 0;
	
	// 出力時に echo 書くの面倒くさいから
	function t_or_f($atai,$return){	if($atai == true) echo $return;	}

	// 昇順・降順を設定する為の関数
	function a_or_d($sort_to){
		if( $sort_to == 'ascend' ) return SORT_ASC;
		else if( $sort_to == 'descend' ) return SORT_DESC;
	}


	foreach ($site as $url){

		// コンテンツ取得して文字コード変更
		$content = file_get_contents($url);
		$content = str_replace( '<link>','<links>',$content );
		$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
		
		// DOMツリーを配列に格納
		$dom = @DOMDocument::loadHTML($content);
		$xml = simplexml_import_dom($dom);
		$items = $xml->xpath('//item');
		
		// これはforeachの中じゃないとアカン
		$i = 0;

		// $itemsの配列から値を取得
		foreach ($items as $item) {
			
			$link    = $item->xpath( '//item/links/text()' );
			$title   = $item->xpath( '//item/title' );
			$desc    = $item->xpath( '//item/links/description' );
			$pubdate = $item->xpath( '//item/links/pubdate' );
			$date    = date($date_fmt, strtotime($pubdate[$i][0]));
			// 日付＋秒で並び替えるためのやつ
			$sort_pd = date('Y.m.d.s', strtotime($pubdate[$i][0]));

			//　あとで連想配列の並び替えをするために配列に格納
			if( strlen($title[$i][0]) != 0 && $i <= ($cnt-1) )

				// なんか重くなりそうなので、descriptionを切り出してから格納
				if( strlen($desc[$i]) > $desc_lengths) $desc[$i] = mb_substr($desc[$i],0,$desc_lengths,'UTF-8').'…';
				else $desc[$i] = mb_substr($desc[$i],0,$desc_lengths,'UTF-8');

				// それでは、格納します
				$ds[$ary_cnt] = array(
					'title'       => $title[$i],
					'link'        => $link[$i],
					'description' => $desc[$i],
					'date'        => $date,
					// 日付＋秒で並び替えるために格納
					'pubdate'     => $sort_pd
				);
			$i ++;
			//複数ループ時に配列を何番まで生成したか記憶しておく
			$ary_cnt ++;
			}
	}
	
	if( $sort_for == 'title' ){
		// タイトル順にソート（TierdWorks用） titleでソートする場合、SORT_STRINGが必要になる
		foreach($ds as $key => $val) $sort[$key] = $val['title'];
		array_multisort($sort, a_or_d($sort_to),SORT_STRING, $ds);

	} else if( $sort_for == 'date' ) {
		// 日付＋秒で並び替えるために ['pubdate'] に設定
		foreach($ds as $key => $val) $sort[$key] = $val['pubdate'];
		array_multisort($sort, a_or_d($sort_to), $ds);
	}

	// ループさせる回数の調整
	if( $cnt >= count($ds) ) $kiji_cnt = count($ds);
	else $kiji_cnt = $cnt;
	
	// 記事結果の出力
	echo '<ul>';
	for ($i=0; $i < $kiji_cnt; $i++){
		echo '<li>';
		t_or_f($date_show,'<span>'.$ds[$i]['date'].'</span>');
		t_or_f($link_show,'<a href="'.$ds[$i]['link'].'" target="'.$link_target.'">');
		t_or_f($title_show,'<p>'.$ds[$i]['title'].'</p>');
		t_or_f($link_show,'</a>');
		t_or_f($desc_show,'<p>'.$ds[$i]['description'].'</p>');
		// echo $ds[$i]['pubdate']; //pubdateの検証用
		echo '</li>';}
	echo '</ul>';
}



?>