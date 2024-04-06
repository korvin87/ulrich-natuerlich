// https://developers.google.com/youtube/iframe_api_reference
var $youtubevideos = $('.embedvideo--youtube');

if ($youtubevideos.length) {
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/player_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function onPlayerStateChange(e) {
        if (e.data === 1) {
            // video plays
        }
    }

    function onYouTubeIframeAPIReady() {
        $youtubevideos.each(function() {
            var $this = $(this);

            new YT.Player($this.data('placeholderid'), {
                height: $this.data('height'),
                width: $this.data('width'),
                videoId: $this.data('videoid'),
                host: 'https://www.youtube-nocookie.com',
                playerVars: {
                    modestbranding: 1,
                    showinfo: 1,
                    html5: 1,
                    rel: 0,
                    wmode: 'transparent',
                    autoplay: $this.data('autoplay')
                },
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        });
    }
}
