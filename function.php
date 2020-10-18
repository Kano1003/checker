<?php
class VideoData
{
    private $video_id;
    private $published;
    private $title;
    private $order;

    public function __construct(string $video_id, string $published, string $title, int $order)
    {
        $this->video_id = $video_id;
        $this->published = $published;
        $this->title = $title;
        $this->order = $order;
    }

    public function body()
    {
        if ($this->order === 0) {
            $body = '<div class="video-wrapper">';
            $body .= '<div id=video-block class="video-block">';
            $body .= '<p id="' . $this->title . '">' . $this->title . '</p>';
            $body .= '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $this->video_id . '" id=' . $this->video_id . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            $body .= '<p id="' . $this->published . '">投稿日時：' . $this->published . '</p>';
            $body .= '</div></div>';
            $body .=  '<div class="thumbnail-wrapper">';
        }

        if ($this->order !== 0) {
            $body = '<div id="video' . $this->order . '" class="thumbnail">';
            $body .= '<img onclick=getVideoId(this) id="' . $this->video_id . '" src="https://i.ytimg.com/vi/' . $this->video_id . '/mqdefault.jpg">';
            $body .= '<p id="' . $this->title . '">' . $this->title . '</p>';
            $body .= '<p id="' . $this->published . '">' . $this->published . '</p>';
            $body .= '</div>';
        }

        echo $body;
    }
}

class MemberData
{
    private $name;
    private $channel_id;
    private $twitter_id;

    public function __construct(string $name, string $channel_id, string $twitter_id)
    {
        $this->name = $name;
        $this->channel_id = $channel_id;
        $this->twitter_id = $twitter_id;
    }

    public function call_youtube_data_api()
    {
        echo '<div class="latest">' . $this->name . ' 最新動画</div>';

        $url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=';
        $url .= $this->channel_id;
        $url .= {APIkey};

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $list_id = json_decode($res, true)['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

        $url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=';
        $url .= $list_id;
        $url .= '&maxResults=5&key={APIkey}';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true)['items'];
        $order = 0;

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ($key == 'snippet') {
                    $calculate_time = new DateTime($val['publishedAt']);
                    $calculate_time->setTimezone(new DateTimeZone('Asia/Tokyo'));
                    $published = $calculate_time->format('Y.m.d H:i');
                    $title = $val['title'];
                    $video_id = $val['resourceId']['videoId'];
                }
            }
            $test = new VideoData($video_id, $published, $title, $order);
            $test->body();
            $order++;
        }
        echo '</div>';
    }

    public function say_timeline()
    {
        $timeline =
            '<div class="twitter-wrapper">
        <div class="twitter-header"><p>Twitter</p></div>
        <div class="twitter" style="width:320px; height: 598px; overflow: auto;">
        <a class="twitter-timeline" data-chrome=”noheader” href="https://twitter.com/' . $this->twitter_id . '?ref_src=twsrc%5Etfw" data-lang="ja">Tweets by ' . $this->twitter_id . '</a>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>
        </div>';

        echo $timeline;
    }
}

class MemberList
{
    private $member_name;

    public function __construct($member_name)
    {
        $this->member_name = $member_name;
    }

    public function db_connect()
    {
        $dsn = 'mysql:host={host};dbname={dbname};charset=utf8;';
        $user = {username};
        $password = {dbpassword};

        try {
            $dbh = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
        } catch (PDOException $e) {
            echo 'Error:' . $e->getMessage();
            die();
        }
        return $dbh;
    }

    public function create_body()
    {
        $rows = array();
        $dbh = $this->db_connect();
        $sql = 'SELECT * FROM member_list WHERE name=:member_name';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':member_name', $this->member_name, PDO::PARAM_STR);
            $stmt->execute();
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $result;
            }
        } catch (Exception $e) {
            echo 'Error:' . $e->getMessage();
            die();
        }

        $dbh = $stmt = null;
        if ($rows !== array()) {
            $call = new MemberData($rows[0]['name'], $rows[0]['channel_id'], $rows[0]['twitter_id']);
            $call->call_youtube_data_api();
            $call->say_timeline();
            $call = null;
        }
    }
}