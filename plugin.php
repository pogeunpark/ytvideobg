<?php
/*
Plugin Name:  유투브 영상 배경
Plugin URI:   https://sitebuilder.kr
Description:  유투브 영상을 배경으로 사용하게 해주는 플러그인 입니다.
Version:      2.1
Author:       사이트빌더
Author URI:   https://sitebuilder.kr
*/

if (!defined("ABSPATH")) {
  exit;
}


function admin_link()
{
  add_menu_page("유투브 배경", "유투브 배경", "manage_options", "ytvideobg", "ytvideobg_admin_menu_page_html", "dashicons-video-alt3", 99);
}


function ytvideobg_admin_menu_page_html()
{
  $page = (int)get_option('ytvideobg_page');
?>
  <div class="wrap">
    <h2>유투브 영상 배경 설정</h2>
    <p>영상 전체를 재생하려면 "재생 시작 시간" 과 "재생 종료 시간"을 비워두십시오. "재생 시작 시간"이나 "재생 종료 시간"만 설정하셔도 됩니다. 시간의 형식은 "초" 입니다 예를들어, 1:30 구간에서 시작하기 원하시면 90을 입력해 주십시오. 자세한 사용법은 <a href="https://sitebuilder.kr/%ec%9b%8c%eb%93%9c%ed%94%84%eb%a0%88%ec%8a%a4%ec%97%90%ec%84%9c-%ec%9c%a0%ed%88%ac%eb%b8%8c-%ec%98%81%ec%83%81%ec%9d%84-%eb%b0%b0%ea%b2%bd%ec%9c%bc%eb%a1%9c-%ec%82%ac%ec%9a%a9%ed%95%98%ea%b8%b0/" target="_blank">블로그</a>를 확인해 주십시오.</p>
    <section class="section">
      <header class="section-header">
        <h3>페이지 선택</h3>
      </header>
      <div class="section-content">
        <form method="post">
          <table class="form-table">
            <tbody>
              <th scope="row"><label for="ytvideobg_page">페이지 선택</label></th>
              <td>
                <select name="ytvideobg_page" id="ytvideobg_page" required>
                  <option value="0">유투브 배경을 사용할 페이지를 선택하세요</option>
                  <?php
                  $args = array(
                    'post_type' => 'page',
                    'posts_per_page' => -1,
                  );

                  $pages = new WP_Query($args);

                  if ($pages->have_posts()) {
                    while ($pages->have_posts()) {
                      $pages->the_post();
                      $post_id = get_the_ID();
                      $post_title = get_the_title();
                      if ($page == $post_id) {
                        echo "<option value='{$post_id}' selected>{$post_title}</option>";
                      } else {
                        echo "<option value='{$post_id}'>{$post_title}</option>";
                      }
                    }
                    wp_reset_postdata();
                  }
                  ?>
                </select>
              </td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_video_id">유투브 영상 ID</label></th>
                <td><input name="ytvideobg_video_id" type="text" id="ytvideobg_video_id" class="regular-text" value="<?php echo get_option("ytvideobg_video_id"); ?>" required></td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_start_time">재생 시작 시간 (초)</label></th>
                <td><input name="ytvideobg_start_time" type="number" id="ytvideobg_start_time" class="regular-text" min="1" value="<?php echo get_option("ytvideobg_start_time"); ?>"></td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_end_time">재생 종료 시간 (초)</label></th>
                <td><input name="ytvideobg_end_time" type="number" id="ytvideobg_end_time" class="regular-text" min="1" value="<?php echo get_option("ytvideobg_end_time"); ?>"></td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_desktop_transform">데스크탑 확대비율</label></th>
                <td><input name="ytvideobg_desktop_transform" type="text" id="ytvideobg_desktop_transform" class="regular-text" placeholder="기본값 1.4" value="<?php echo get_option("ytvideobg_desktop_transform"); ?>"></td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_mobile_transform">모바일 확대비율</label></th>
                <td><input name="ytvideobg_mobile_transform" type="text" id="ytvideobg_mobile_transform" class="regular-text" placeholder="기본값 4" value="<?php echo get_option("ytvideobg_mobile_transform"); ?>"></td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_desktop_ratio">데스크탑 화면비율</label></th>
                <td><input name="ytvideobg_desktop_ratio" type="text" id="ytvideobg_desktop_ratio" class="regular-text" placeholder="기본값 16/9" value="<?php echo get_option("ytvideobg_desktop_ratio"); ?>"></td>
              </tr>
              <tr>
                <th scope="row"><label for="ytvideobg_mobile_ratio">모바일 화면비율</label></th>
                <td><input name="ytvideobg_mobile_ratio" type="text" id="ytvideobg_mobile_ratio" class="regular-text" placeholder="기본값 9/16" value="<?php echo get_option("ytvideobg_mobile_ratio"); ?>"></td>
              </tr>
            </tbody>
          </table>
          <div class="submit">
            <input class="button button-primary button-large" name="save_ytvideobg_options" type="submit" value="설정 저장">
          </div>
        </form>
      </div>
    </section>
  </div>
<?php
}

if (isset($_POST['save_ytvideobg_options'])) {
  update_option("ytvideobg_page", sanitize_text_field($_POST['ytvideobg_page']));
  update_option("ytvideobg_video_id", sanitize_text_field($_POST['ytvideobg_video_id']));
  update_option("ytvideobg_start_time", sanitize_text_field($_POST['ytvideobg_start_time']));
  update_option("ytvideobg_end_time", sanitize_text_field($_POST['ytvideobg_end_time']));
  update_option("ytvideobg_desktop_transform", sanitize_text_field($_POST['ytvideobg_desktop_transform']));
  update_option("ytvideobg_mobile_transform", sanitize_text_field($_POST['ytvideobg_mobile_transform']));
  update_option("ytvideobg_desktop_ratio", sanitize_text_field($_POST['ytvideobg_desktop_ratio']));
  update_option("ytvideobg_mobile_ratio", sanitize_text_field($_POST['ytvideobg_mobile_ratio']));
  header("refresh:0");
}

function ytvideobg_style()
{
  $set_page = (int)get_option('ytvideobg_page');
  if (!is_page($set_page)) {
    return;
  }
  $desktop_transform = (get_option('ytvideobg_desktop_transform')) ? get_option('ytvideobg_desktop_transform') : "1.4";
  $desktop_ratio = (get_option('ytvideobg_desktop_ratio')) ? get_option('ytvideobg_desktop_ratio') : "16/9";
  $mobile_transform = (get_option('ytvideobg_mobile_transform')) ? get_option('ytvideobg_mobile_transform') : "4";
  $mobile_ratio = (get_option('ytvideobg_mobile_ratio')) ? get_option('ytvideobg_mobile_ratio') : "9/16"
?>
  <style>
    .yt-background-container {
      position: relative;
      aspect-ratio: <?php echo $desktop_ratio; ?>;
      overflow: hidden;
    }

    .yt-background {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      transform: scale(<?php echo $desktop_transform; ?>);
    }

    @media(max-width: 768px) {
      .yt-background-container {
        aspect-ratio: <?php echo $mobile_ratio; ?>;
      }

      .yt-background {
        transform: scale(<?php echo $mobile_transform; ?>);
      }
    }
  </style>
<?php
}

function ytvideobg_script()
{
  $set_page = (int)get_option('ytvideobg_page');
  if (!is_page($set_page)) {
    return;
  }
  $video_id = get_option('ytvideobg_video_id');
  $start_time = (get_option('ytvideobg_start_time')) ? get_option('ytvideobg_start_time') : 0;
  $end_time = (get_option('ytvideobg_end_time')) ? get_option('ytvideobg_end_time') : 0;

?>
  <script>
    var tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    var player;
    var videoId = '<?php echo $video_id; ?>';
    var startSeconds = <?php echo $start_time; ?>;
    var endSeconds = <?php echo $end_time; ?>;

    var playerConfig = {
      height: '1080',
      width: '1920',
      videoId: videoId,
      playerVars: {
        autoplay: 1,
        mute: 1,
        controls: 0,
        modestbranding: 1,
        fs: 1,
        cc_load_policy: 0,
        iv_load_policy: 3,
      },
      events: {
        'onStateChange': onStateChange,
        'onReady': function(event) {
          event.target.playVideo();
        }
      }
    }

    if (startSeconds) {
      playerConfig.playerVars.start = startSeconds;
    }

    if (endSeconds) {
      playerConfig.playerVars.end = endSeconds;
    }

    function onYouTubePlayerAPIReady() {
      player = new YT.Player('ytbg', playerConfig);
    }

    var reloadConfig = {
      videoId: videoId,
    }

    if (startSeconds) {
      reloadConfig.startSeconds = startSeconds;
    }

    if (endSeconds) {
      reloadConfig.endSeconds = endSeconds;
    }

    function onStateChange(state) {
      if (state.data === YT.PlayerState.ENDED) {
        player.loadVideoById(reloadConfig);
      }
    }
  </script>
<?php
}

add_action("admin_menu", "admin_link");
add_action('wp_head', 'ytvideobg_style');
add_action('wp_footer', 'ytvideobg_script');
