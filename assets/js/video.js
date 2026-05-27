  var video = document.getElementById('myVideo');
  var src = 'wp-content/themes/WpBlueprint/assets/videos/output/playlist.m3u8';

  if (Hls.isSupported()) {
    var hls = new Hls();
    hls.loadSource(src);
    hls.attachMedia(video);
  } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
    video.src = src;
  }
