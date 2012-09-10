<html>
    <head>
        <title>MPControl</title>
        <link rel="stylesheet" href="style.css" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Ropa+Sans' rel='stylesheet' type='text/css'>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"> </script>
        <script src="https://raw.github.com/isocra/TableDnD/master/stable/jquery.tablednd.js"> </script>
        <script src="script.js"> </script>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <div id="wrapper">
            <div id="current_track">
                <div id="album_"><span id="album">Album</span></div>
                <div id="title_"><span id="title">Title</span></div>
                <div id="artist_">by <span id="artist">Artist</span></div>
            </div>

            <div id="info_"><span id="info">No info available</span></div>

            <button id="prev">❚◀</button>
            <button id="stop">◾</button>
            <button id="play">
                <span class="play">▶</span>
                <span class="pause">❚❚</span>
            </button>
            <button id="next"> ▶❚ </button>
            <button id="update">Refresh</button><br>
            <button id="voldown">-</button>
            <button id="volup">+</button>
            Volume: <span id="volume_display">?</span>%

            <div id="bar">
                <div id="bar_inner" style="width: 30%">
                    <div id="slider"></div>
                </div>
            </div>

            <div id="playlist">
                <table>
                    <tr class="loading">
                        <td>
                            <div class="loader">
                                <img src="gfx/loader.gif">
                                <span>Loading playlist...</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </body>
</html>
