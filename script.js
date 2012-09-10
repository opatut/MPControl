// script.js

var mpd_status = null;
var playlist = Array();
var last_status = null;

function refreshBar() {
    if(mpd_status == null) {
        return;
    }

    p = 0;
    if(mpd_status.current_track.length > 0) {
        d = 0;
        if(last_status && mpd_status.state == "play") {
            d = (new Date()).getTime() - last_status.getTime();
            d /= 1000.0;
        }

        p = (parseFloat(mpd_status.elapsed) + d) * 100.0 / mpd_status.current_track.length;
        p = Math.min(p, 100);
    }
    $("#bar_inner").css("width", p + "%");
}

function action(method, arg1, arg2) {
    var url = "control.php?cmd=action&method=" + escape(method)
    if(arg1 != undefined)
        url += "&arg1=" + escape(arg1)
    if(arg2 != undefined)
        url += "&arg2=" + escape(arg2)

    $.ajax({url: url, dataType: "text"});
    
}

function secsToTime(s) {
    var sec = s % 60;
    s = (s - sec) / 60;

    var min = s % 60;
    s = (s - min) / 60;

    var h = s;

    r = "";
    if (h > 0) {
        r += h + ":";
        if (min < 10) r += "0"
    }
    r += min + ":";
    if (sec < 10) r += "0"
    r += sec;

    return r
}

function initPlaylist() {
    $("#playlist table tr").click(function() {
        song = $(this).attr("id").split("-")[1];
        action("SkipTo", song);
    });

    $("#playlist table").tableDnD({
        onDragClass: "dragging",
        onDragStyle: "",
        onDrop: function(table, row) {
            var old_index = parseInt($(row).attr("id").split("-")[1]);
            var new_index = $(table.tBodies[0].rows).index(row);
            action("PLMoveTrack", old_index, new_index);
        }
    });
    $("#playlist table tr").css("cursor", "pointer");
}

function initFilelist() {
    $("#filelist table tr").click(function() {
        file = $(this).attr("filename").split("-")[1];
        action("PLAdd", file);
    });
    
    $("#filelist table tr").css("cursor", "pointer");
}

function loadPlaylist() {
    $.ajax({
        url: "control.php?cmd=playlist",
        context: $("#playlist table"),
        dataType: "json",
        success: function(data){
            playlist = data;
            $(this).html("");
            for(i in data) {
                var track = data[i];
                var $tr = $('<tr id="song-' + i + '"></tr>');
                $tr.append( $('<td class="duration">' + secsToTime(track.Time) + '</td>') );
                $tr.append( $('<td class="title">' + track.Title + '</td>') );
                $tr.append( $('<td class="artist">' + track.Artist + '</td>') );
                $(this).append($tr);
            }
            updateStatus();
            initPlaylist();
        }
    });
}

function loadFilelist() {
    $.ajax({
        url: "control.php?cmd=filelist",
        context: $("#filelist table"),
        dataType: "json",
        success: function(data){
            filelist = data;
            $(this).html("");
            for(i in data) {
                var file = data[i];
                var $tr = $('<tr id="file-' + i + '" filename="'+file.file+'"></tr>');
                $tr.append( $('<td class="filename">' + file.file + '</td>') );
                $(this).append($tr);
            }
            
            initFilelist();
        }
    });
}

function updateStatus(callback) {
    $.ajax({
        url: "control.php?cmd=status",
        dataType: "json",
        success: function(data) {
            mpd_status = data;
            last_status = new Date();

            $("#playlist tr").removeClass("current");
            $("#playlist tr#song-" + mpd_status.current_track.index).addClass("current");
            $("#volume_display").text(mpd_status.volume);

            $("#artist").text(mpd_status.current_track.info.Artist);
            $("#title").text(mpd_status.current_track.info.Title);
            $("#album").text(mpd_status.current_track.info.Album);

            $("#play").removeClass("play").removeClass("pause").addClass(mpd_status.state);

            refreshBar();
        }
    });
}

function idleWait() {
    $.ajax({
        url: "control.php?cmd=idle",
        dataType: "text",
        success: function(data){
            data = data.trim();
            if(data == "playlist") {
                loadPlaylist();
            } else if(data == "player" || data == "mixer") {
                updateStatus();
            }
            
            idleWait(); // loop this shit
        },
        error: idleWait
    });
}

function timer() {
    updateStatus();
    setTimeout(timer, 1000);
}

function timer2() {
    refreshBar();
    setTimeout(timer2, 50);
}

$(document).ready(function() {
    $("#prev").click(function() { action("Previous"); });
    $("#stop").click(function() { action("Stop"); });
    $("#play").click(function() { action(mpd_status.state == "play" ? "Pause" : "Play"); });
    $("#next").click(function() { action("Next"); });
    $("#shuffle").click(function() { action("Plshuffle"); });
    $("#update").click(function() { action("DBrefresh"); });
    $("#clear").click(function() { action("Plclear"); });
    $("#volup").click(function() { action("AdjustVolume", 5); });
    $("#voldown").click(function() { action("AdjustVolume", -5); });

    $("#bar,#inner_bar").click(function(e) {
        var x = e.pageX - $(this).offset().left;
        var w = $("#bar").width();
        action("SeekPercent", Math.round(x / w * 100));
    });
    loadPlaylist();
    loadFilelist();
    updateStatus();
    idleWait();
    timer();
    timer2();
});
