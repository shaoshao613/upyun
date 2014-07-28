<?php


function upyun_add_gallery() {
    global $upyun_photo_size_map, $upyun_on_off_map,
        $upyun_descr_map, $upyun_columns_map, $upyun_bg_color_map,
        $upyun_photo_source_map, $pf;

    $user_id = get_option('upyun_user_id');

    $photosets_map = array();
    $groups_map = array();
    $galleries_map = array();

    ?>

   <div class='wrap'>
   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>ShortCode Generator</h2>

<?php
			$upyun_upload_directory="/";
        if ($_POST) {
		if($_POST['submit']=='GenerateShortCode'){
		$id=0;
          if(get_option('upyun_current_code_id'))
			  $id=get_option('upyun_current_code_id');
		  $id++;
		    update_option('upyun_current_code_id', $id);
		   update_option('upyun_code_'.$id, $_POST['upyun_upload_directory']);
?>
            <div class="updated"><p><strong>
                  <?php echo "Gallery \"{$_POST['upyun_upload_directory']}\" connected successfully. Shortcode for this gallery is </strong>[upyun_gallery id='$id']"; ?>
               </p></div>

<?php
       
}
$baseurl="";
if(isset($_POST['enterDirectory']))
	$baseurl=$_POST['enterDirectory'];
//echo $baseurl;
if($_POST['submit']=='Check')
	$baseurl=substr($_POST['upyun_upload_directory'],0,-1);

$upyun_upload_directory=$_POST['upyun_upload_directory'];
if(isset($_POST['Deletephoto']))
			{ if(get_option('upyun_user_name')&&get_option('upyun_bucket_name')&&get_option('upyun_password')){
									 $upyun = new UpYun(get_option('upyun_bucket_name'),get_option('upyun_user_name'), get_option('upyun_password'));
								try {
									$path=$_POST['Deletephoto'];
										$res=$upyun-> delete($path);
										if($res)
											echo "   <div class='updated'><p><strong>Delete successfully.  </p></div>";
									}
									catch(Exception $e) {
										 echo $e->getCode();
										   echo $e->getMessage();
									}
				}


			}



}

    $url=$_SERVER['REQUEST_URI'];
?>

            <form method='post' action='<?php echo $url ?>'>
               <div id="upyun-wrap">
              
                     <div id="upyun-main-box">
                        <table class='widefat upyun-settings-box'>
                            <tr>
                                <th class="upyun-label"></th>
                                <th class="upyun-input"></th>
                                <th class="upyun-help-bubble"></th>
                            </tr>
                      
                           <tr>
                              <td>Choose Directory:</td>
                              <td><?php 	
										 echo "<select name='upyun_upload_directory'>";
										 echo "<option value='".$baseurl."/'>".$baseurl."/</option>";
										 if(get_option('upyun_user_name')&&get_option('upyun_bucket_name')&&get_option('upyun_password')){
											 $upyun = new UpYun(get_option('upyun_bucket_name'),get_option('upyun_user_name'), get_option('upyun_password'));
										
											try {
												$list = $upyun->getList($baseurl."/");
												foreach($list as $num => $photo){
												if($photo['type']=="folder"){
										
													if("/".$photo['name']."/"==	$upyun_upload_directory)
													echo "<option selected='selected' value='".$baseurl."/".$photo['name']."/'>".$baseurl."/".$photo['name']."/</option>";
													else
													echo "<option  value='".$baseurl."/".$photo['name']."/'>".$baseurl."/".$photo['name']."/</option>";
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
					  	 <input type="text" id="submitHint" style="display:none" value="Check" name="submit"/>
<?php
 
?>
                  <br />
                  <input type="submit" id="upyun_save_changes" class="button-primary"  onclick="document.getElementById('submitHint').value='GenerateShortCode';"value="Generate ShortCode" name="GenerateShortCode"/>
				   <input type="submit" id="upyun_save_changes" class="button-primary"  value="Check File" name="Check File"/>
              </div>


                </form>
				<br>
				  
				     <div style="margin-top: 11%;" >
				 <h3>	explorer</h3>
				 <div id="upyun_explorer"><?php     
				 		 if(get_option('upyun_user_name')&&get_option('upyun_bucket_name')&&get_option('upyun_password')){
											 $upyun = new UpYun(get_option('upyun_bucket_name'),get_option('upyun_user_name'), get_option('upyun_password'));
											    $list = $upyun->getList($baseurl."/");
	 echo "<ul>";										 foreach($list as $num => $photo) {
  if($photo['type']=="file"){
  $photo_url = "http://value.b0.upaiyun.com".$baseurl."/".$photo['name'];
      
 echo " <form method='post' action='".$url."'><li class='liimg'><img class='imgsty' src=".$photo_url.">";echo  	"<br>url:<input readonly type='text' value='".$photo_url."' /><input style='display:none' name='Deletephoto' type='text' value='".$baseurl."/".$photo['name']."' />  <input type='submit' id='upyun_save_changes' class='button-primary' value='delete' name='delete'/></li></form>";
 }
 else
		 {


	 $docsrc=BASE_URL ."/images/d".rand(1,4).".png";
  echo " <form method='post' action='".$url."'><li class='liimg'><img class='imgsty' src=".$docsrc.">";
  echo  "<br><span> Directory:</span> <input style='width: 50%;' readonly name='enterDirectory' value='".$baseurl."/".$photo['name']."'  /> <input type='submit' id='upyun_save_changes' style='float: right;margin-right: 5%;margin-bottom: 3%;' class='button-primary'  value='enter' name='enter'/></li></form>";
 
 
 }

	}


echo 	"</ul>"	;
	
	
	
	}
	?>
		
				 </div>
					 </div>
<?php
}
