<?php

$ds = array();
$cnt = $_POST['count'];
$ary_cnt = 0;
$replace_txt = array(
	array('/<.*?>(.*)<\/.*?>/','$1'),
	array('/<font\s.*?>(.*)<\/font>/','$1'),
	array('/&nbsp;/',''),
	array('/<img(.*?)>/','')
);

// 出力時に echo 書くの面倒くさいから
function t_or_f($atai,$return){	if($atai == true) echo $return;	}

// 昇順・降順を設定する為の関数
function a_or_d($sort_to){
	if( $sort_to == 'ascend' ) return SORT_ASC;
	else if( $sort_to == 'descend' ) return SORT_DESC;
}

foreach ($_POST['site'] as $url){

	// コンテンツ取得して文字コード変更
	$content = file_get_contents($url);
	// linkの閉じタグがないの回避
	$content = preg_replace('/<link>(.*)/', '<links>$1</links>', $content);

	$content = preg_replace('/<!(.*)\[CDATA\[ <p(.*)>/', '', $content);
	// $content = preg_replace('/(\]\]>)/', '', $content);
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
		$desc    = $item->xpath( '//item/description' );
		$pubdate = $item->xpath( '//item/pubdate' );
		$date    = date($_POST['date_fmt'], strtotime($pubdate[$i][0]));
		// 日付＋秒で並び替えるためのやつ
		$sort_pd = date('Y.m.d.H.i.s', strtotime($pubdate[$i][0]));

		// replece_text内の文字列を処理する
		foreach ($replace_txt as $val) $desc[$i] = preg_replace($val[0],$val[1],$desc[$i]);
		//　あとで連想配列の並び替えをするために配列に格納
			// なんか重くなりそうなので、descriptionを切り出してから格納
			if( strlen($desc[$i]) >= $_POST['desc_lengths'] ){
				$desc[$i] = mb_substr($desc[$i],0,$_POST['desc_lengths'],'UTF-8').'…';
			}else{
				$desc[$i] = mb_substr($desc[$i],0,$_POST['desc_lengths'],'UTF-8');
			}
	
			// それでは、格納します
			if(strlen($title[$i])!=0){
				$ds[$ary_cnt] = array(
					'title'       => $title[$i],
					'link'        => $link[$i],
					'description' => $desc[$i],
					'date'        => $date,
					// 日付＋秒で並び替えるために格納
					'pubdate'     => $sort_pd
				);
			}
			$i ++;
			//複数ループ時に配列を何番まで生成したか記憶しておく
			$ary_cnt ++;
	}
}

if( $_POST['sort_for'] == 'title' ){
	// タイトル順にソート（pubdateを利用せずタイトルに日付を入れたい時用） titleでソートする場合、SORT_STRINGが必要になる
	foreach($ds as $key => $val) $sort[$key] = $val['title'];
	array_multisort($sort, a_or_d($_POST['sort_to']),SORT_STRING, $ds);

} else if( $_POST['sort_for'] == 'date' ) {
	// 日付＋秒で並び替えるために ['pubdate'] に設定
	foreach($ds as $key => $val) $sort[$key] = $val['pubdate'];
	array_multisort($sort, a_or_d($_POST['sort_to']), $ds);
}

// ループさせる回数の調整
if( $cnt >= count($ds) ) $kiji_cnt = count($ds);
else $kiji_cnt = $cnt;

// 記事結果の出力
echo '<ul>';
for ($i=0; $i < $kiji_cnt; $i++){
		echo '<li>';
		t_or_f($_POST['date_show'],'<span>'.$ds[$i]['date'].'</span>');
		t_or_f($_POST['link_show'],'<a href="'.$ds[$i]['link'].'" target="'.$_POST['link_target'].'">');
		t_or_f($_POST['title_show'],'<p>'.$ds[$i]['title'].'</p>');
		t_or_f($_POST['link_show'],'</a>');
		t_or_f($_POST['desc_show'],'<p>'.$ds[$i]['description'].'</p>');
		echo '</li>';
}
echo '</ul>';

?>
