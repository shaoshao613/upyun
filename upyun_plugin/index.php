<?php
/*
Plugin Name: WordPress_Upyun
Plugin URI: https://gitcafe.com/shaoshao613/plugin-of-wordpress-for-UPYUN
Description: 又拍云 wordpress插件
Version:  1.0
Author: ting
Author URI: http://wp.sotapit.com
*/

/*  Copyright 2014  ting  (email : shaoshao613@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('sdk/upyun.php');
include_once('upyun_admin_settings.php');


	function upyun_enqueue_styles() {
		wp_enqueue_style('upyun_css', BASE_URL . "/upyun.css");
	}

	function upyun_upload($attachment) {
		
		return $attachment;
	}
	 function upyun_upload_prefilter($file) {
		return $file;
	}
	function upyun_attachment_metadata($metadata) {

		return $metadata;
	}

	function upload_upyun($attrs) {
		$wpuploadurl=wp_upload_dir();

		$file=$attrs['file'];
		$array=explode('/',$file);
		$file=$array[count($array)-1];
		if($file&&$attrs['file']){
			$imgurl= $wpuploadurl['baseurl']."/".$attrs['file'];
			if(get_option('upyun_user_name')&&get_option('upyun_bucket_name')&&get_option('upyun_password')){
				try{
					$upyun = new UpYun(get_option('upyun_bucket_name'),get_option('upyun_user_name'), get_option('upyun_password'));
					$opts = array(UpYun::CONTENT_MD5 => md5(file_get_contents($imgurl)));
					$fh =  file_get_contents ($imgurl, 'rb');					
					$rsp = $upyun->writeFile(get_option('upyun_upload_directory').$file, $fh, True, $opts);   // 上传图片，自动创建目录				
				}
				catch(Exception $e) {
					echo $e->getCode();
					 echo $e->getMessage();
				}
			}
		}
		return $attrs;
	}


if (!is_admin()) {
    /* Short code to load upyun_gallery  plugin.  Detects the word
     * [upyun_gallery] in posts or pages and loads the gallery.
     */
    add_shortcode('upyun_gallery', 'upyun_display_gallery');
    add_filter('widget_text', 'do_shortcode', 11);
    add_action('wp_print_styles', 'upyun_enqueue_styles');
	 
}
add_action( 'wp_update_attachment_metadata', 'upload_upyun' );



/* Main function that loads the gallery. */
function upyun_display_gallery ($atts){
    global $size_heading_map, $afg_text_color_map, $pf;
	extract( shortcode_atts( array( 'id' => '0', ), $atts ) );
	if(get_option("upyun_code_".$id))
		$upyun_directory=get_option("upyun_code_".$id);
	else
		$upyun_directory='/';
	$cur_page = 1;
	$upyun_bucket_name=get_option('upyun_bucket_name');
	$upyun_user_name=get_option('upyun_user_name');
	$upyun_password=get_option('upyun_password');
    $photoset_id = NULL;
    $gallery_id = NULL;
    $group_id = NULL;
    $tags = NULL;
    $popular = false;
    if (!isset($gallery['photo_source'])) $gallery['photo_source'] = 'photostream';
    if ($gallery['photo_source'] == 'photoset') $photoset_id = $gallery['photoset_id'];
    else if ($gallery['photo_source'] == 'gallery') $gallery_id = $gallery['gallery_id'];
    else if ($gallery['photo_source'] == 'group') $group_id = $gallery['group_id'];
    else if ($gallery['photo_source'] == 'tags') $tags = $gallery['tags'];
    else if ($gallery['photo_source'] == 'popular') $popular = true;
    $disp_gallery = "<!-- Upyun Gallery Start -->";  
    $extras = 'url_l, description, date_upload, date_taken, owner_name';
    $disp_gallery .= "<div >";
    $disp_gallery .= "<div class='upyun-table' style='width:100%'>";
    $photo_count = 1;
	$upyun = new UpYun($upyun_bucket_name,$upyun_user_name,$upyun_password);
	try {
		 $list = $upyun->getList($upyun_directory);
	}
	catch(Exception $e) {
		echo $e->getCode();
		echo $e->getMessage();
	}
	$cur_col = 0;
	echo "<ul>";
	foreach($list as $num => $photo) {
		if($photo['type']=="file"){
			$photo_url = "http://value.b0.upaiyun.com".$upyun_directory.$photo['name'];
			echo "<li class='liindex'  ><img onclick='showImg(this);' class='imgindex' src=".$photo_url." />";echo  	"</li>";
		}
	}
	echo 	"</ul>"	;
?>
	<div id="yy" style="background-color: rgb(51, 51, 51);width: 624px;height: 435px;opacity: 0.8;position: absolute;left: 31%;top: 3%;z-index: 11;display: none;cursor: pointer;" onclick="divhide();">
		<p style="font-size: 47px;float: right;margin-top: -3%;margin-right: 2%;cursor: pointer;z-index: 13;color: #d30;"> x</p>
	</div>
	<div>
		<img id="img" style="opacity: 1;width: 551px;position: absolute;top: 6%;left: 33.6%;z-index: 11;border: 7px solid rgb(255, 255, 255);display: none" src="http://value.b0.upaiyun.com/demo/sample_thumb_1.jpeg">
	</div>




<script type="text/javascript">
	function divhide(){

		var ui=document.getElementById("yy")
		ui.style.display="none";
		var img=document.getElementById("img")
		img.style.display="none";
	}


	function showImg(imgdim){

		 var ui=document.getElementById("yy")
			ui.style.display="block";
		 var img=document.getElementById("img")
		 img.src=imgdim.src;
		 console.log(imgdim.width);
		 img.width=imgdim.width;
		 img.style.display="block";
		
	}

</script>


<?php 
	
	

    return $disp_gallery;
}
?>
