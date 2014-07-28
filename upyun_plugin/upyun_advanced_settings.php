<?php
require_once('upyun_libs.php');

function upyun_admin_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('upyun_custom_css_js', BASE_URL . "/CodeMirror/lib/codemirror.js");
    wp_enqueue_script('upyun_custom_css_theme_js', BASE_URL . "/CodeMirror/mode/css/css.js");
    wp_enqueue_style('upyun_custom_css_style', BASE_URL . "/CodeMirror/lib/codemirror.css");
    wp_enqueue_style('upyun_custom_css_theme_css', BASE_URL . "/CodeMirror/theme/cobalt.css");
    wp_enqueue_style('upyun_custom_css_style', BASE_URL . "/CodeMirror/css/docs.css");
    wp_enqueue_style('upyun_admin_css', BASE_URL . "/upyun_admin.css");
}

if (is_admin()) {
    add_action('admin_enqueue_scripts', 'upyun_admin_enqueue_scripts');
    add_action('admin_head', 'upyun_advanced_headers');
}

function upyun_advanced_headers() {
    echo "
          <link href=\"https://plus.google.com/110562610836727777499\" rel=\"publisher\" />
          <script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\"></script>
          ";
   }

   function upyun_advanced_settings_page() {
       $url=$_SERVER['REQUEST_URI'];
   ?>

   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
      echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Advanced Settings | Awesome Flickr Gallery</h2>

<?php
      if (isset($_POST['upyun_advanced_save_changes']) && $_POST['upyun_advanced_save_changes']) {
          update_option('upyun_disable_slideshow', isset($_POST['upyun_disable_slideshow'])? $_POST['upyun_disable_slideshow']: '');
          update_option('upyun_slideshow_option', $_POST['upyun_slideshow_option']);
          update_option('upyun_custom_css', $_POST['upyun_custom_css']);
          echo "<div class='updated'><p><strong>Settings updated successfully.</strong></p></div>";
      }
?>         
<form method='post' action='<?php echo $url ?>'>
<?php echo upyun_generate_version_line() ?>
   <div id='upyun-wrap'>
        <div id="upyun-main-box">
                     <h3>Custom CSS</h3>
                        <div style="background-color:#FFFFE0; border-color:#E6DB55; maargin:5px 0 15px; border-radius:3px 3px 3px 3px; border-width: 1px; border-style: solid; padding: 8px 10px; line-height: 20px">
                Check <a href='<?php echo BASE_URL . '/upyun.css';?>' target='_blank'>upyun.css</a> to see existing classes and properties for gallery which you can redefine here. Note that there is no validation applied to CSS Code entered here, so make sure that you enter valid CSS.
                    </div><br/>
                    <textarea id='upyun_custom_css' name='upyun_custom_css'><?php echo get_option('upyun_custom_css');?></textarea>
       <script type="text/javascript">var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('upyun_custom_css'), {
       lineNumbers: true, indentUnit: 4, theme: "cobalt", matchBrackets: true} );</script>
            <input style='margin-top:15px' type="submit" name="upyun_advanced_save_changes" id="upyun_advanced_save_changes" class="button-primary" value="Save Changes" />
        </div>
         <div id="upyun-side-box">
<?php
      $message = "Settings on this page are global and hence apply to all your Galleries.";
      echo upyun_box('Help', $message);
      echo upyun_donate_box();
      echo upyun_share_box();
?>
            </div>
      </div>
         </form>
    <?php
   }
?>
