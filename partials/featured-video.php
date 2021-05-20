<!-- First, some JS that loads the video whent he thumbnail is clicked -->
<script type="text/javascript" async>
function featuredVideo() {
  // fetch the video link
  var videoURLElement = document.getElementById("featuredvideourl");
  var videoUrl = videoURLElement.innerHTML;

  // a function that takes a youtuve URl and returns the ID
  function youtube_parser(url) {
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    var match = url.match(regExp);
    return (match && match[7].length == 11) ? match[7] : false;
  }

  const videoId = youtube_parser(videoUrl);

  // listen for a click. Then remoe the image and replace with the video iframe
  const youtube = document.getElementById("featuredvideo");
  youtube.addEventListener("click", function() {
    var videoiframe = document.createElement("iframe");
    videoiframe.setAttribute("frameborder", "0");
    videoiframe.setAttribute("allowfullscreen", "");
    videoiframe.setAttribute("src", "https://www.youtube-nocookie.com/embed/" + videoId +
      "?rel=0&showinfo=0&autoplay=1");
    youtube.innerHTML = "";
    youtube.classList.add('responsive-video');
    youtube.appendChild(videoiframe);
  });
}
window.addEventListener("load", featuredVideo, false);
</script>

<?php 

// Get the language for the page this is being used on
$lang = get_query_var('lang');

// set the post object according to what langauge is set
if ($lang):
  $group = get_field($lang . '_featured_video', 'option');
  $featured_video_url = $group['url'];
  $featured_video_image = $group['image'];
  $featured_video_title = $group['title'];
  $featured_video_cta = $group['cta'];
endif;

?>
<section class="ft-c-featured-video">
  <div class="ft-c-featured-video__background">
    <div class="ft-l-container">
      <span class="ft-c-label ft-c-label--white">Featured Video</span>
      <h2><?php echo $featured_video_title ?></h2>
      <div id="featuredvideo" class="ft-c-featured-video__media ">
        <div class="ft-c-featured-video__play-icon">
          <img src="<?php echo get_template_directory_uri() . '/assets/images/play.svg' ?>" alt="play button" />
        </div>

        <?php if ($featured_video_image) {
          echo wp_get_attachment_image($featured_video_image, '16x9');
        };
        ?>
        <span id="featuredvideourl" style="display: none"><?php echo $featured_video_url ?></span>
      </div>
    </div>
  </div>
  <div class="ft-l-container">
    <div class="ft-c-featured-video__cta">
      <a class="mzp-c-cta-link" href="<?php echo $featured_video_cta['url'] ?>" target="
          <?php $featured_video_cta['target'] ? $featured_video_cta['target'] : '_self';?>">
          <?php echo $featured_video_cta['title'] ?>
        </a>
    </div>
  </div>
</section>