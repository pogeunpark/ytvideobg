<?php
/*
Plugin Name:  유투브 영상 배경
Plugin URI:   https://sitebuilder.kr
Description:  유투브 영상을 배경으로 사용하게 해주는 플러그인 입니다.
Version:      2.2
Author:       사이트빌더
Author URI:   https://sitebuilder.kr
*/

if (!defined("ABSPATH")) {
  exit;
}

class Ytvideo
{
  private $saved_data;
  private $current_page_data;
  private $pages;
  private $set_pages;
  private $is_set_page;
  private $current_page_id;

  public function __construct()
  {
    $this->pages = get_pages();
    $this->saved_data = get_option("ytvideobg_data");
    add_action("wp_head", [$this, "style"]);
    add_action("wp_footer", [$this, "script"]);
    add_action("admin_menu", [$this, "admin_link"]);
    if (!get_option("ytvideobg_migrated")) {
      $this->migrate();
    }
  }

  public function admin_menu_html()
  {
    $quality_options = [
      [
        "value" => "default",
        "label" => "자동",
      ],
      [
        "value" => "small",
        "label" => "Small",
      ],
      [
        "value" => "medium",
        "label" => "Medium",
      ],
      [
        "value" => "large",
        "label" => "Large",
      ],
      [
        "value" => "hd720",
        "label" => "720p",
      ],
      [
        "value" => "hd1080",
        "label" => "1080p",
      ],
      [
        "value" => "highres",
        "label" => "최고 화질",
      ],
    ];
?>
    <div class="wrap">
      <h2>유투브 영상 배경 설정</h2>
      <p>영상 전체를 재생하려면 "재생 시작 시간" 과 "재생 종료 시간"을 비워두십시오. "재생 시작 시간"이나 "재생 종료 시간"만 설정하셔도 됩니다. 시간의 형식은 "초" 입니다.<br> 예를들어, 1:30 구간에서 시작하기 원하시면 90을 입력해 주십시오. 자세한 사용법은 <a href="https://sitebuilder.kr/%ec%9b%8c%eb%93%9c%ed%94%84%eb%a0%88%ec%8a%a4%ec%97%90%ec%84%9c-%ec%9c%a0%ed%88%ac%eb%b8%8c-%ec%98%81%ec%83%81%ec%9d%84-%eb%b0%b0%ea%b2%bd%ec%9c%bc%eb%a1%9c-%ec%82%ac%ec%9a%a9%ed%95%98%ea%b8%b0/" target="_blank">블로그</a>를 확인해 주십시오.</p>
      <section class="section">
        <div class="section-content">
          <form method="post">
            <hr>
            <h3>새로 추가</h3>
            <table class="form-table">
              <tbody>
                <tr>
                  <th scope="row"><label for="page_id">페이지 선택</label></th>
                  <td>
                    <select name="page_id" id="page_id">
                      <option value="0">페이지 선택</option>
                      <?php
                      foreach ($this->pages as $page) {
                        echo "<option value='{$page->ID}'>{$page->post_title}</option>";
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row"><label for="video_id">유투브 영상 ID</label></th>
                  <td><input name="video_id" type="text" id="video_id" class="regular-text"></td>
                </tr>
                <tr>
                  <th scope="row"><label for="quality">화질 선택</label></th>
                  <td>
                    <select name="quality" id="quality">
                      <?php
                      foreach ($quality_options as $option) {
                        echo "<option value='{$option["value"]}'>{$option["label"]}</option>";
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row"><label for="start_time">재생 시작 시간 (초)</label></th>
                  <td><input name="start_time" type="number" id="start_time" class="regular-text" min="1"></td>
                </tr>
                <tr>
                  <th scope="row"><label for="end_time">재생 종료 시간 (초)</label></th>
                  <td><input name="end_time" type="number" id="end_time" class="regular-text" min="1"></td>
                </tr>
                <tr>
                  <th scope="row"><label for="desktop_transform">데스크탑 확대비율</label></th>
                  <td><input name="desktop_transform" type="text" id="desktop_transform" class="regular-text" placeholder="기본값 1.4"></td>
                </tr>
                <tr>
                  <th scope="row"><label for="mobile_transform">모바일 확대비율</label></th>
                  <td><input name="mobile_transform" type="text" id="mobile_transform" class="regular-text" placeholder="기본값 4"></td>
                </tr>
                <tr>
                  <th scope="row"><label for="desktop_ratio">데스크탑 화면비율</label></th>
                  <td><input name="desktop_ratio" type="text" id="desktop_ratio" class="regular-text" placeholder="기본값 16/9"></td>
                </tr>
                <tr>
                  <th scope="row"><label for="mobile_ratio">모바일 화면비율</label></th>
                  <td><input name="mobile_ratio" type="text" id="mobile_ratio" class="regular-text" placeholder="기본값 9/16"></td>
                </tr>
              </tbody>
            </table>
            <div class="submit">
              <input class="button button-primary button-large" name="save_video_settings" type="submit" value="설정 저장">
            </div>
            <hr>
            <h3>저장된 설정</h3>
            <?php
            if (empty($this->saved_data)) {
              echo "<p>저장된 설정이 없습니다.</p>";
            }
            foreach ($this->saved_data as $key => $data) {
            ?>
              <table class="form-table">
                <tbody>
                  <tr>
                    <th scope="row"><label for="saved_page_id_<?php echo $key; ?>">페이지 선택</label></th>
                    <td>
                      <select name="saved_page_id_<?php echo $key; ?>" id="saved_page_id_<?php echo $key; ?>">
                        <?php
                        foreach ($this->pages as $page) {
                          $selected = $data["page_id"] === $page->ID ? " selected" : "";
                          echo "<option value='{$page->ID}'{$selected}>{$page->post_title}</option>";
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_video_id_<?php echo $key; ?>">유투브 영상 ID</label></th>
                    <td><input name="saved_video_id_<?php echo $key; ?>" type="text" id="saved_video_id_<?php echo $key; ?>" class="regular-text" value="<?php echo $data["video_id"]; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_quality_<?php echo $key; ?>">화질 선택</label></th>
                    <td>
                      <select name="saved_quality_<?php echo $key; ?>" id="saved_quality_<?php echo $key; ?>">
                        <?php
                        foreach ($quality_options as $option) {
                          $selected = $data["quality"] === $option["value"] ? " selected" : "";
                          echo "<option value='{$option["value"]}' {$selected}>{$option["label"]}</option>";
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_start_time_<?php echo $key; ?>">재생 시작 시간 (초)</label></th>
                    <td><input name="saved_start_time_<?php echo $key; ?>" type="number" id="saved_start_time_<?php echo $key; ?>" class="regular-text" min="1" value="<?php echo $data["start_time"]; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_end_time_<?php echo $key; ?>">재생 종료 시간 (초)</label></th>
                    <td><input name="saved_end_time_<?php echo $key; ?>" type="number" id="saved_end_time_<?php echo $key; ?>" class="regular-text" min="1" value="<?php echo $data["end_time"]; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_desktop_transform_<?php echo $key; ?>">데스크탑 확대비율</label></th>
                    <td><input name="saved_desktop_transform_<?php echo $key; ?>" type="text" id="saved_desktop_transform_<?php echo $key; ?>" class="regular-text" placeholder="기본값 1.4" value="<?php echo $data["desktop_transform"]; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_mobile_transform_<?php echo $key; ?>">모바일 확대비율</label></th>
                    <td><input name="saved_mobile_transform_<?php echo $key; ?>" type="text" id="saved_mobile_transform_<?php echo $key; ?>" class="regular-text" placeholder="기본값 4" value="<?php echo $data["mobile_transform"]; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_desktop_ratio_<?php echo $key; ?>">데스크탑 화면비율</label></th>
                    <td><input name="saved_desktop_ratio_<?php echo $key; ?>" type="text" id="saved_desktop_ratio_<?php echo $key; ?>" class="regular-text" placeholder="기본값 16/9" value="<?php echo $data["desktop_ratio"]; ?>"></td>
                  </tr>
                  <tr>
                    <th scope="row"><label for="saved_mobile_ratio_<?php echo $key; ?>">모바일 화면비율</label></th>
                    <td><input name="saved_mobile_ratio_<?php echo $key; ?>" type="text" id="saved_mobile_ratio_<?php echo $key; ?>" class="regular-text" placeholder="기본값 9/16" value="<?php echo $data["mobile_ratio"]; ?>"></td>
                  </tr>
                </tbody>
              </table>
              <hr>
            <?php
            }
            ?>
          </form>
        </div>
      </section>
    </div>
    <?php
    if (isset($_POST["save_video_settings"])) {

      if (is_array($this->saved_data) && !empty($this->saved_data)) {
        foreach ($this->saved_data as $key => &$data) {
          if (empty($_POST["saved_video_id_{$key}"])) {
            unset($this->saved_data[$key]);
            continue;
          }
          $data["page_id"] = (int)sanitize_text_field($_POST["saved_page_id_{$key}"]);
          $data["video_id"] = sanitize_text_field($_POST["saved_video_id_{$key}"]);
          $data["quality"] = sanitize_text_field($_POST["saved_quality_{$key}"]);
          $data["start_time"] = sanitize_text_field($_POST["saved_start_time_{$key}"]);
          $data["end_time"] = sanitize_text_field($_POST["saved_end_time_{$key}"]);
          $data["desktop_transform"] = sanitize_text_field($_POST["saved_desktop_transform_{$key}"]);
          $data["mobile_transform"] = sanitize_text_field($_POST["saved_mobile_transform_{$key}"]);
          $data["desktop_ratio"] = sanitize_text_field($_POST["saved_desktop_ratio_{$key}"]);
          $data["mobile_ratio"] = sanitize_text_field($_POST["saved_mobile_ratio_{$key}"]);
        }
      }

      if (!empty($_POST["video_id"])) {
        $this->saved_data[] = [
          "page_id" => (int)sanitize_text_field($_POST["page_id"]),
          "video_id" => sanitize_text_field($_POST["video_id"]),
          "quality" => sanitize_text_field($_POST["quality"]),
          "start_time" => sanitize_text_field($_POST["start_time"]),
          "end_time" => sanitize_text_field($_POST["end_time"]),
          "desktop_transform" => sanitize_text_field($_POST["desktop_transform"]),
          "mobile_transform" => sanitize_text_field($_POST["mobile_transform"]),
          "desktop_ratio" => sanitize_text_field($_POST["desktop_ratio"]),
          "mobile_ratio" => sanitize_text_field($_POST["mobile_ratio"]),
        ];
      }

      update_option("ytvideobg_data", $this->saved_data, false);
      echo "<meta http-equiv='refresh' content='0'>";
    }
  }

  public function style()
  {
    if (!is_array($this->saved_data) || empty($this->saved_data)) {
      return;
    }
    $this->current_page_id = get_queried_object_id();
    $this->current_page_data = reset(array_filter($this->saved_data, function ($n) {
      if ($n["page_id"] === $this->current_page_id) return $n;
    }));
    $this->set_pages = array_column($this->saved_data, "page_id");
    $this->is_set_page = is_page($this->set_pages);
    if (!$this->is_set_page) {
      return;
    }

    $desktop_transform = !empty($this->current_page_data["desktop_transform"]) ? $this->current_page_data["desktop_transform"] : "1.4";
    $desktop_ratio = !empty($this->current_page_data["desktop_ratio"]) ? $this->current_page_data["desktop_ratio"] : "16/9";
    $mobile_transform = !empty($this->current_page_data["mobile_transform"]) ? $this->current_page_data["mobile_transform"] : "4";
    $mobile_ratio = !empty($this->current_page_data["mobile_ratio"]) ? $this->current_page_data["mobile_ratio"] : "9/16"
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

  public function script()
  {
    if (!is_array($this->saved_data) || empty($this->saved_data)) {
      return;
    }
    if (!$this->is_set_page) {
      return;
    }

    $video_id = $this->current_page_data["video_id"];
    $quality = $this->current_page_data["quality"];
    $start_time = !empty($this->current_page_data["start_time"]) ? $this->current_page_data["start_time"] : 0;
    $end_time = !empty($this->current_page_data["end_time"]) ? $this->current_page_data["end_time"] : 0;
  ?>
    <script>
      var tag = document.createElement('script');
      tag.src = 'https://www.youtube.com/iframe_api';
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      var player;
      var videoId = '<?php echo $video_id; ?>';
      var quality = '<?php echo $quality; ?>';
      var startSeconds = <?php echo $start_time; ?>;
      var endSeconds = <?php echo $end_time; ?>;

      var playerConfig = {
        height: '1080',
        width: '1920',
        videoId: videoId,
        suggestedQuality: quality, // small, medium, large, hd720, hd1080, highres, default
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

  public function admin_link()
  {
    add_menu_page("유튜브 배경", "유튜브 배경", "manage_options", "ytvideo", [$this, "admin_menu_html"], "dashicons-video-alt3", 99);
  }

  private function migrate()
  {
    // migrate previous version data
    $migrated_data = [
      "page_id" => (int)get_option('ytvideobg_page'),
      "video_id" => get_option('ytvideobg_video_id'),
      "quality" => "default",
      "start_time" => get_option("ytvideobg_start_time"),
      "end_time" => get_option("ytvideobg_end_time"),
      "desktop_transform" => get_option("ytvideobg_desktop_transform"),
      "mobile_transform" => get_option("ytvideobg_mobile_transform"),
      "desktop_ratio" => get_option("ytvideobg_desktop_ratio"),
      "mobile_ratio" => get_option("ytvideobg_mobile_ratio"),
    ];
    update_option("ytvideobg_data", [$migrated_data], false);
    // delete previous version options
    delete_option('ytvideobg_page');
    delete_option('ytvideobg_video_id');
    delete_option("ytvideobg_start_time");
    delete_option("ytvideobg_end_time");
    delete_option("ytvideobg_desktop_transform");
    delete_option("ytvideobg_mobile_transform");
    delete_option("ytvideobg_desktop_ratio");
    delete_option("ytvideobg_mobile_ratio");
    delete_site_option('ytvideobg_pages');
    delete_site_option('ytvideobg_video_id');
    delete_site_option("ytvideobg_start_time");
    delete_site_option("ytvideobg_end_time");
    delete_site_option("ytvideobg_desktop_transform");
    delete_site_option("ytvideobg_mobile_transform");
    delete_site_option("ytvideobg_desktop_ratio");
    delete_site_option("ytvideobg_mobile_ratio");
    // flag migration status
    update_option("ytvideobg_migrated", true, false);
  }
}
$ytvideo = new Ytvideo();
