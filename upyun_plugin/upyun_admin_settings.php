<?php
require_once('sdk/upyun.php');


define('BASE_URL', plugins_url() . '/' . basename(dirname(__FILE__)));
define('DEBUG', true);
define('VERSION', '1.0.0');



add_action('admin_menu', 'upyun_admin_menu');


function upyun_admin_menu() {
	include_once('upyun_add_galleries.php');
	wp_enqueue_style('upyun_admin_css', BASE_URL . "/upyun_admin.css");
    add_menu_page('Upyun Gallery', 'Upyun Gallery', 'publish_pages', 'upyun_plugin_page', 'upyun_admin_html_page', BASE_URL . "/images/logo.png", 898);
	$upyun_main_page = add_submenu_page('upyun_plugin_page', 'Default Settings | Awesome Flickr Gallery', 'Default Settings', 'publish_pages', 'upyun_plugin_page', 'upyun_admin_html_page');
	$upyun_add_page = add_submenu_page('upyun_plugin_page', 'Add Gallery | Awesome Flickr Gallery', 'ShortCode Generator', 'edit_posts', 'upyun_add_gallery_page', 'upyun_add_gallery');

    // adds "Settings" link to the plugin action page
    add_filter( 'plugin_action_links', 'upyun_add_settings_links', 10, 2);

}

function upyun_add_settings_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__)) . '/index.php' ) {
        $settings_link = '<a href="plugins.php?page=upyun_plugin_page">' . 'Settings</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}

function upyun_admin_settings_header() {
    wp_enqueue_script('admin-settings-script');
 
}















function upyun_admin_html_page() {
    global $upyun_photo_size_map, $upyun_on_off_map, $upyun_descr_map,
        $upyun_columns_map, $upyun_bg_color_map, $upyun_width_map, $pf,
        $upyun_sort_order_map, $upyun_slideshow_map;
?>
<?php


if ($_POST)
{
    global $pf, $custom_size_err_msg;

        if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Cached Galleries') {
            delete_upyun_caches();
            echo "<div class='updated'><p><strong>Cached data deleted successfully.</strong></p></div>";
        }
        else if (isset($_POST['submit']) && $_POST['submit'] == 'Save Changes') {
            update_option('upyun_user_name', $_POST['upyun_user_name']);
            update_option('upyun_password', $_POST['upyun_password']);
            update_option('upyun_bucket_name', $_POST['upyun_bucket_name']);
			 update_option('upyun_upload_directory', $_POST['upyun_upload_directory']);
    
		
		$upyun = new UpYun($_POST['upyun_bucket_name'], $_POST['upyun_user_name'], $_POST['upyun_password']);
		
		
		try {
		$size_res =$upyun->getFolderUsage ( '/' );
				if ($size_res === true) 
            echo "<div class='updated'><p><strong>Settings updated successfully.</div>";
		}
			catch(Exception $e) {
			  echo "<div class='updated'><p><strong>connection failed. ".$e->getCode()." ".$e->getMessage()."</div>";
		}


          
        }
 
    }
    $url=$_SERVER['REQUEST_URI'];
?>

    <form method='post' action='<?php echo $url ?>'>
   <div id='upyun-wrap'>
        <h2><a href=''><img src="<?php
        echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Gallery Settings</h2>


            <div id="upyun-main-box">
                        <h3>User Settings</h3>
                            <table class='widefat upyun-settings-box'>
                                <tr>
                                    <th class="upyun-label"></th>
                                    <th class="upyun-input"></th>
                                    <th class="upyun-help-bubble"></th>
                                </tr>
                                <tr>
                                  <td>Bucket Name </td>
                                  <td><input class='upyun-input' type='text' name='upyun_bucket_name' value="<?php echo get_option('upyun_bucket_name'); ?>" /><b>*</b></td>
                                  <td><div class="upyun-help">Don't have upyun Bucket ? Click <a href="https://www.upyun.com/index.html" target='blank'>here.</a></div></td>
                               </tr>
                                <tr>
                                  <td>UserName</td>
                                  <td><input class='upyun-input' type='text' name='upyun_user_name' value="<?php echo get_option('upyun_user_name'); ?>" ><b>*</b></input> </td>
                                   </tr>
                                    <tr>
                                        <td>Password</td>
                               <td><input class='upyun-input' type='text' name='upyun_password' id='upyun_password' value="<?php echo get_option('upyun_password'); ?>"/><b>*</b></input> </td>
                                <br /><br />
                          
                        </tr>
        </table>
                        <table class='widefat upyun-settings-box'>
                        <h3>Upload Settings</h3>
                           <tr>
                               <th class="upyun-label"></th>
                               <th class="upyun-input"></th>
                               <th class="upyun-help-bubble"></th>
                            </tr>
                            <tr>
                              <td>Choose Directory:</td>
                              <td><?php 	
										 echo "<select name='upyun_upload_directory'>";
										 echo "<option value='/'>/</option>";
										 if(get_option('upyun_user_name')&&get_option('upyun_bucket_name')&&get_option('upyun_password')){
											 $upyun = new UpYun(get_option('upyun_bucket_name'),get_option('upyun_user_name'), get_option('upyun_password'));
										
											try {
												$list = $upyun->getList('/');
												foreach($list as $num => $photo){
												if($photo['type']=="folder"){
													$upyun_upload_directory="/".$photo['name']."/";
													if(get_option('upyun_upload_directory')==	$upyun_upload_directory)
													echo "<option selected='selected' value='/".$photo['name']."/'>/".$photo['name']."/</option>";
													else
													echo "<option  value='/".$photo['name']."/'>/".$photo['name']."/</option>";
												}
												}
											}
											catch(Exception $e) {
												echo $e->getCode();
												echo $e->getMessage();
											}
										 }
									 echo "</select>";?></td>
                           </tr>

                   

                      




                

                 
                              </table>
                        <br />
                        <input type="submit" name="submit" id="upyun_save_changes" class="button-primary" value="Save Changes" />
                        <br /><br />
</div>
            </form>
		
<?php

}
?>
