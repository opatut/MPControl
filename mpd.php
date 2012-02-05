<?php

class Track {
    var $title = "";
    var $artist = "";
    var $album = "";
    var $time = "";

    public function __construct($title, $artist, $album, $time) {
        $this->title = $title;
        $this->artist = $artist;
        $this->album = $album;
        $this->time = $time;
    }

    public function getDuration() {
        $s = array_reverse(explode(":", $this->time));
        $d = 0;
        $i = 1;
        foreach($s as $r) {
            $d += intval($r) * i;
            $i *= 60;
        }
        return $d;
    }

    public function getInfo() {
        return $this->artist . " - " . $this->title . " (" . $this->album . ") [" . $this->time . "]";
    }

    public function infoObject() {
        return Array(
            "title"     => $this->title,
            "artist"    => $this->artist,
            "album"     => $this->album,
            "time"      => $this->time
        );
    }
}

class MPD {
    var $playlist = Array();

    public function __construct() {}

    public function exec($cmd) {
        $out = Array();
        $ret = 0;
        exec("mpc " . $cmd, $out, $ret);

        if(count($out) == 0) return null;
        else if(count($out) == 1) return $out[0];
        return $out;
    }

    public function loadPlaylist() {
        $this->playlist = Array();
        $arr = $this->exec('playlist -f "%title%\t%artist%\t%album%\t%time%"');
        foreach($arr as $line) {
            $v = split("\t", $line);
            $this->playlist[] = new Track($v[0], $v[1], $v[2], $v[3]);
        }
    }

    public function status() {
        $this->loadPlaylist();
        $info = $this->exec('-f "%position%"');
        $trackNo = intval($info[0]);
        return Array(
            "status" => "playing",
            "track_number" => $trackNo,
            "track" => $this->playlist[$trackNo]
        );
    }

    public function idle() {
        return $this->exec("idle");
    }

    public function play() {
        $this->exec("play");
    }

    public function setTrack($n) {
        $this->exec("play " . intval($n));
    }

    public function toggle() {
        $this->exec("toggle");
    }

    public function pause() {
        $this->exec("pause");
    }

    public function stop() {
        $this->exec("stop");
    }

    public function prev() {
        $this->exec("prev");
    }

    public function next() {
        $this->exec("next");
    }

    public function seek($percent) {
        $this->exec("seek $percent%");
    }

    public function relativeSeek($seconds) {
        $this->exec("seek " . ($seconds >= 0 ? "+" : "-") . abs($seconds));
    }

    public function setVolume($vol) {
        $this->exec("volume " . $vol);
    }

    public function changeVolume($diff) {
        $this->exec("volume " . ($diff >= 0 ? "+" : "-") . abs($diff));
    }
}

if(!isset($mpd)) {
    $mpd = new MPD();
}

?>
