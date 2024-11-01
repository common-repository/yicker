<?php
/**************************************************************************

Plugin Name:  博客微博
Plugin URI:   http://code.google.com/p/yicker/
Version:      0.0.3
Description:  博客与微博客的好搭档！
Author:       LloydSheng
Author URI:   http://lloydsheng.com/

**************************************************************************/
include_once( 'yicker.util.php' );

if(is_admin()){
	add_action('admin_menu', 'add_yicker_menu');	
	add_action('publish_post', 'publish_post_2_microblog', 0); 
	$default_template="《{title}》-{link}-{content}";
	if(trim(get_option("yicker_template"))=="")
	{
		update_option("yicker_template",$default_template);
	}
}

function publish_post_2_microblog($post_ID){
	$text=yicker_build_text($post_ID);

	$update_url='http://yicker.com/api/update.php';
	$params="username=".get_option("yicker_username")."&text=".urlencode($text);
	//echo $params;
	$reponse=http($update_url,'POST',$params);
	//echo $reponse;
	//exit();
}
function clean_html($str) {
    $str  = str_replace(" ", "", $str);
    $preg = "/<(.[^>]*)>/i" ;
    $str  = preg_replace($preg, "", $str);
    return $str;
}

function yicker_build_text($post_ID)
{
	$title = trim($_POST['post_title']); 
	$content =clean_html(trim($_POST['post_content'])); 
	$tag_str=trim($_POST["tax_input"]['post_tag']);
	$link=get_permalink($post_ID);
	$tags=split ('[,]', $tag_str);
	
	$tag_str="";
	foreach($tags as $tag)
	{
		$tag_str=($tag_str.'#'.$tag.'# ');
	}
	//echo $link;
	$template=trim(get_option("yicker_template"));
	//echo $template;
	$text=$template;
	//echo strpos($text,"{title}");
	
	if(strpos($text,"{title}")>=0)
	{
		$text=str_replace("{title}",$title,$text);
	}
	if(strpos($text,"{tag}")>=0)
	{
		$text=str_replace("{tag}",$tag_str,$text);
	}
	if(strpos($text,"{link}")>=0)
	{
		$text=str_replace("{link}",$link,$text);
	}
	if(strpos($text,"{content}")>=0)
	{
		$text=str_replace("{content}",$content,$text);
	}
	
	if(strlen($text)>137*2)
	{
		$text=substr($text,0,136*2);
		$text=$text."...";
	}
	return $text;
}

function add_yicker_menu() {
	add_options_page('博客微博工具配置', '博客微博', 8, 'yicker_options', 'add_yicker_options');
}

function add_yicker_options() {
    $has_authorize=0;
    $index_url="http://yicker.com/api/index.php";
    $username=get_option("yicker_username");
		
    if($username!=null&&trim(username)!='')
    {				
	    $url=$index_url."?username=".$username;
	    $reponse=http($url,'GET');
	   $has_authorize=!strpos($reponse,"api.t.sina.com.cn");
    }
?><form name="formamt" method="post" action="options.php">
		<?php wp_nonce_field('update-options') ?>

			<div class="wrap">

			<h2>博客微博工具配置</h2>
			<?php 
			if(!$has_authorize)
			{?>
			<div class="updated fade" id="warming"><p>提醒：您还未授权博客微博工具访问微博客,点
			<strong><a  href="<?php echo 'http://yicker.com/api/index.php?username='.get_option("yicker_username")?>" target="_blank" id="yicker_auth_link" >授权链接</a>
			</strong>
			解决该问题，否则该工具无效!</p></div>
		<?php	}
			?>

			<table class="form-table">
			<tr>
			<th class="row">ScreenName</th>
			<td><input type="text"  name="yicker_username" class="regular-text" value="<?php echo get_option("yicker_username");?>"/>当前只仅支持新浪微博</tr>
			
			<tr><th class="row">消息模板
			</th><td><input type="text"  class="regular-text" name="yicker_template" value="<?php echo get_option("yicker_template");?>"/>可使用变量:{title},{tag},{link},{content}</td>
			</tr>
			<tr><th class="row"><input type="submit" value="更新配置" name="update_message"/></th><td></td>
			</tr>

			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="yicker_template,yicker_username,yicker_auth_link,yicker_widget_height,yicker_widget_weight,yicker_widget_status" />
			</table>
			</form>
			<script type="text/javascript">
			jQuery(function(){
				jQuery("input[name=yicker_username]").change(function(){
					var url="<?php echo $index_url?>?username="+jQuery(this).val();
					jQuery("a[id=yicker_auth_link]").attr("href",url);
				});
			});
			</script>
		<?php
} ?>