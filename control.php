<?php

require("mpd.class.php");

$mpd = new Mpd();
$mpd->RefreshInfo();

if(!isset($_GET["cmd"])) $cmd = "";
$cmd = $_GET["cmd"];

if($cmd == "playlist") {
    print json_encode($mpd->playlist);
} else if($cmd == "idle") {
    $out = Array();
    exec("mpc idle", $out, $ok);
    // if(!$ok or count($out) == 0) print "error";
    //else
    print $out[0];
} else if($cmd == "status") {
    print json_encode($mpd->status);
} else if($cmd == "statistics") {
    print json_encode($mpd->statistics);
} else if($cmd == "playlist") {
    print json_encode($mpd->playlist);
} else if($cmd == "action") {
    $m = $_GET["method"];
    if(isset($_GET["arg2"]) and isset($_GET["arg1"]))
        print json_encode($mpd->$m($_GET["arg1"], $_GET["arg2"]));
    else if(isset($_GET["arg1"]))
        print json_encode($mpd->$m($_GET["arg1"]));
    else
        print json_encode($mpd->$m());
} else {
    print "Unknown command.";
}

?>

