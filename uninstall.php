<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

delete_option('ytvideobg_pages');
delete_option('ytvideobg_video_id');
delete_option("ytvideobg_start_time");
delete_option("ytvideobg_end_time");
delete_option("ytvideobg_desktop_transform");
delete_option("ytvideobg_mobile_transform");
delete_option("ytvideobg_desktop_ratio");
delete_option("ytvideobg_mobile_ratio");

// for site options in case of multisite
delete_site_option('ytvideobg_pages');
delete_site_option('ytvideobg_video_id');
delete_site_option("ytvideobg_start_time");
delete_site_option("ytvideobg_end_time");
delete_site_option("ytvideobg_desktop_transform");
delete_site_option("ytvideobg_mobile_transform");
delete_site_option("ytvideobg_desktop_ratio");
delete_site_option("ytvideobg_mobile_ratio");
