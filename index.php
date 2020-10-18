<?php
require_once('function.php');

$member = array('ときのそら', 'ロボ子さん', 'さくらみこ', '星街すいせい', '夜空メル', 'アキ・ローゼンタール', '赤井はあと', '白上フブキ', '夏色まつり', '湊あくあ', '紫咲シオン', '百鬼あやめ', '癒月ちょこ', '大空スバル', '大神ミオ', '猫又おかゆ', '戌神ころね', 'AZKi', '兎田ぺこら', '潤羽るしあ', '不知火フレア', '白銀ノエル', '宝鐘マリン', '天音かなた', '桐生ココ', '角巻わため', '常闇トワ', '姫森ルーナ', '雪花ラミィ', '桃鈴ねね', '獅白ぼたん', '尾丸ポルカ');
$say_sidebar = '<div class="sidebar"><h2>Membar</h2><ul>';
foreach ($member as $value) {
    $say_sidebar .= '<li><a href="?q=' . $value . '">■<span>' . $value . '</span></a></li>';
}
$say_sidebar .= '</ul></div>';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホロ動画チェッカー</title>
    <link rel="shortcut icon" type="image/png" href="../favicon.ico">
    <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
    <div id="wrapper">
        <header>
            <div class="title-wrapper">
                <a href="./">ホロ動画チェッカー</a>
            </div>
        </header>

        <div id="container">
            <nav id="sidebar">
                <?php
                echo $say_sidebar;
                ?>
            </nav>
            <main>
                <?php

                if (!empty("$_GET[q]") && array_search("$_GET[q]", $member, true) !== false) {
                    $call = new MemberList("$_GET[q]");
                    $call->create_body();
                    $call = null;
                } else {
                    $call = new MemberData('hololive ホロライブ', 'UCJFZiqLMntJufDCHc6bQixg', 'hololivetv');
                    $call->call_youtube_data_api();
                    $call->say_timeline();
                    $call = null;
                }

                ?>
            </main>
        </div>
        <footer>
        </footer>
    </div>
</body>

</html>

<script type="text/javascript">
    const getVideoId = (element) => {
        const clickedImageTitle = element.nextElementSibling.id;
        const clickedImagePublised = element.nextElementSibling.nextElementSibling.id;
        const clickedImageNumber = element.parentNode;

        const topVIdeoBlock = document.getElementById('video-block');
        const topVideoTitle = topVIdeoBlock.firstElementChild.id;
        const topVideo = document.getElementById(topVideoTitle).nextElementSibling.id;
        const topVideoPublished = topVIdeoBlock.lastElementChild.id;

        topVIdeoBlock.innerHTML = '<p id="' + clickedImageTitle + '">' + clickedImageTitle + '</p>' +
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + element.id + '" id=' + element.id + ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
            '<p id="' + clickedImagePublised + '">投稿日時：' + clickedImagePublised + '</p>';

        clickedImageNumber.innerHTML = '<img onclick=getVideoId(this) id="' + topVideo + '" src="https://i.ytimg.com/vi/' + topVideo + '/mqdefault.jpg">' +
            '<p id="' + topVideoTitle + '">' + topVideoTitle + '</p>' +
            '<p id="' + topVideoPublished + '">' + topVideoPublished + '</p>';

        scrollTo(0, 0);
    };
</script>