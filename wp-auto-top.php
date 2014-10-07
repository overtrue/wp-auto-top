<?php
/**
 * Plugin Name: wp auto top
 * Plugin URI: http://wordpress.org/plugins/wp-auto-top/
 * Description: 博客侧边滑动，返回顶部，查看评论的小工具
 * Version: 2.92
 * Author: Carlos
 * GitHub: https://github.com/overtrue/wp-auto-top
 * Author URI: http://weibo.com/joychaocc
 */

$autotopPluginsName = 'wp-auto-top';
$adminMenuName = "Wp Auto Top";

global $autotopOptions;

$autotopOptions = array (
    'autotop_comment_id_type' => 'name',
    'autotop_comment_id'      => 'comments',
    'autotop_color_img'       => '1.png',
    'autotop_position'        => 'left',
    'autotop_margin'          => '540',
    'autotop_margin_unit'     => 'px',
    'autotop_zindex'          => '9999',
    'autotop_top_val'         => '45',
    'autotop_top_unit'        => '%',
    'autotop_scroll_speed'    => 1,
    'autotop_enable_home'     => 1,
    'autotop_enable_single'   => 1,
    'autotop_enable_page'     => 1,
    'autotop_enable_tags'     => 1,
    'autotop_enable_archive'  => 1,
);


function auto_top_active(){
   global $autotopOptions;
   foreach ($autotopOptions as $key => $value) {
      if(get_option($key) === false)
        update_option($key, $value);
    }
}

function auto_top_add_admin() {
    global $autotopPluginsName, $autotopOptions, $adminMenuName;
    if(!empty($_POST)){
        if ( isset($_POST['autotopsave']) and $_POST['autotopsave'] == true ) {
            foreach ($autotopOptions as $key => $value) {
                if(isset($_POST[$key]))
                    update_option($key, $_POST[$key]);
                else
                    update_option($key, "0");
            }
            header('Location:options-general.php?page=wp-auto-top&saved=true');
        } elseif( isset($_POST['autotopreset']) and $_POST['autotopreset'] == true ) {
             foreach ($autotopOptions as $key => $value) {
                    delete_option($key);
                    update_option($key, $value);
            }
            header('Location:options-general.php?page=wp-auto-top&reset=true');
        }
    }
    add_options_page($adminMenuName, $adminMenuName, 'manage_options', basename(__FILE__), 'auto_top_plugin_admin');
}

function addPluginLinks($links, $file)
{
    if ($file == plugin_basename(__FILE__)) {
        array_unshift($links, '<a href="options-general.php?page=' . basename(__FILE__).'">'.__('设置').'</a>');
    }

    return $links;
}
//setting menu
add_filter('plugin_action_links', 'addPluginLinks', 10, 2);

function auto_top_plugin_admin() {
    global $autotopPluginsName, $autotopOptions, $adminMenuName;

    $autoSavedOptions = array();

    if(get_option('autotop_color_img') == ''){
      foreach ($autotopOptions as $key => $value) {
          update_option($key, $value);
      }
    }

    foreach ($autotopOptions as $key => $value) {
        $val = get_option($key);
        if( $val !== false){
            $autoSavedOptions[$key] = $val;
        }else{
            $autoSavedOptions[$key] = $value;
        }
    }

    extract($autoSavedOptions);

    if ( $_GET['saved'] ) echo '<div class="updated"><p><strong>设置已保存</strong></p></div>';
    if ( $_GET['reset'] ) echo '<div class="updated"><p><strong>设置已重置</strong></p></div>';
?>
    <div class="wrap joychao-plugins">
        <?php screen_icon(); ?>
        <h2><?php echo $adminMenuName.' 设置';?></h2>
        <form method="post">
            <table class="form-table" >
                <tr>
                    <td>图片配色</td><td>
                        <?php
                        $baseUrl = plugins_url('img/',__FILE__);
                        $i = 0;
                        foreach (glob(dirname(__FILE__).'/img/*.*') as $img) {
                          if($i++ % 6 == 0) echo '<br />';
                           $checked = basename($img) == $autotop_color_img?'checked':'';
                           echo '<label><input type="radio" '.$checked.' name="autotop_color_img" value="'.basename($img).'" /><div class="autoimgbox"><img src="'.$baseUrl.basename($img).'" /></div></label>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                  <td>显示页面</td>
                  <td>
                    <label><input type="checkbox" <?php if($autotop_enable_home) echo 'checked="checked"' ?> name="autotop_enable_home" value="1" /> 首页</label>
                    <label><input type="checkbox" <?php if($autotop_enable_single) echo 'checked="checked"' ?> name="autotop_enable_single" value="1" /> 文章内页</label>
                    <label><input type="checkbox" <?php if($autotop_enable_page) echo 'checked="checked"' ?> name="autotop_enable_page" value="1" /> 页面内面</label>
                    <label><input type="checkbox" <?php if($autotop_enable_archive) echo 'checked="checked"' ?> name="autotop_enable_archive" value="1" /> 分类和归档页</label>
                    <label><input type="checkbox" <?php if($autotop_enable_tags) echo 'checked="checked"' ?> name="autotop_enable_tags" value="1" /> 标签页</label>
                  </td>
                </tr>
                <tr>
                    <td>显示位置</td><td><select name="autotop_position" style="width:100px;" ><option value="left"<?php if($autotop_position == 'left') echo 'selected="selected"';?>>左</option><option value="right" <?php if($autotop_position == 'right') echo 'selected="selected"';?>>右</option></select> 相对网页正中间线距离:<input type="number" name="autotop_margin"  style="width:60px;" value="<?php echo $autotop_margin;?>" /><select name="autotop_margin_unit"><option value="px" <?php if($autotop_margin_unit == 'px') echo 'selected'; ?>>px</option><option value="%" <?php if($autotop_margin_unit == '%') echo 'selected'; ?>>%</option></select>, 距离顶部:<input type="number" style="width:60px;" name="autotop_top_val" value="<?php echo $autotop_top_val; ?>" /><select name="autotop_top_unit"><option value="px" <?php if($autotop_top_unit == 'px') echo 'selected'; ?>>px</option><option value="%" <?php if($autotop_top_unit == '%') echo 'selected'; ?>>%</option></select></td>
                </tr>
                <tr>
                    <td>浮层层高</td><td><input type="number" name="autotop_zindex" value="<?php echo $autotop_zindex ?>" /> 如果被其它层挡住在下面看不见时，请增大这个值，建议以10倍增加。</td>
                </tr>
                <tr>
                    <td>文章评论位置标记</td><td><select name="autotop_comment_id_type"><option value="id">id</option><option value="class">class</option><option value="name">name</option></select> = <input type="text" name="autotop_comment_id" value="<?php echo $autotop_comment_id ?>" /></td>
                </tr>
                <tr>
                    <td>鼠标悬停时滚动速度</td><td><input type="number" name="autotop_scroll_speed" value="<?php echo $autotop_scroll_speed ?>" /> * 越大越快</td>
                </tr>
                <tr>
                    <td colspan="2">
                      <div>作者：<a  style="text-decoration:none;" href="http://weibo.com/joychaocc" target="_blank">@安正超 </a>
                      源码：<a href="https://github.com/overtrue/wp-auto-top" target="_blank">overtrue/wp-auto-top</a>
                      </div>
                      <!-- Baidu Button BEGIN -->
                      <div style="font-size:22px;height:50px; float:left; line-height:50px;">推荐给你的朋友们吧！</div>
                      <div class="bdsharebuttonbox">
                          <a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
                          <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
                          <a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
                          <a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
                          <a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
                          <a href="#" class="bds_more" data-cmd="more"></a>
                      </div>
                      <script>
                      window._bd_share_config={
                      "common":{
                          "bdMini":"2",
                          "bdMiniList":false,
                          "bdPic":"",
                          "bdStyle":"1",
                          "bdSize":"24",
                          'bdDes':'推荐一款wordpress返回顶部插件：http://wordpress.org/plugins/wp-auto-top/',    //'请参考自定义分享摘要'
                          'bdText':'给大家推荐一款wordpress返回顶部插件,30种默认配色，自定义显示位置，还可以自定义图片哦！详情猛击这里->http://wordpress.org/plugins/wp-auto-top/',   //'请参考自定义分享内容'
                          'bdComment':'非常棒的wordpress返回顶部插件',  //'请参考自定义分享评论'
                          'bdPic':'http://mystorage.qiniudn.com/wp-auto-top.jpg', //'请参考自定义分享出去的图片'
                          'searchPic':false,
                          'wbUid':'2193182644',   //'请参考自定义微博 id'
                          'bdSnsKey':{'tsina':'4000238328'}   //'请参考自定义分享到平台的appkey'
                      },
                      "share":{},
                      "image":{
                          "viewList":["qzone", "tsina", "tqq", "renren","weixin"],
                          "viewText":"分享到：",
                          "viewSize":"16"
                      },
                      "selectShare":{
                          "bdContainerClass":null,
                          "bdSelectMiniList":["qzone", "tsina", "tqq", "renren","weixin"]
                          }
                      };
                      with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
                </script>
                </div>

            <!-- Baidu Button END -->
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="button-primary" name="autotopsave" type="submit" value="保存设置" />
                        <input class="button-secondary" name="autotopreset" type="submit" value="重置设置" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <style type="text/css">
    .joychao-plugins *{font-family: 'Microsoft YaHei';}
     .joychao-plugins table tr td label img{margin-top:-28px;vertical-align: middle;padding: 5px; margin-right: 5px;}
     .joychao-plugins table tr td label{margin-right: 8px;}
     .joychao-plugins table tr td label .autoimgbox{height: 35px;overflow: hidden; display: inline-block;}
    </style>
    <?php
}

function add_auto_top_foot_code() {
    wp_reset_query();
    $hasComments = '<div id="wp-auto-top"><div id="wp-auto-top-top"></div><div id="wp-auto-top-comment"></div><div id="wp-auto-top-bottom"></div></div>
';
    $noCommnets = '<div id="wp-auto-top"><div id="wp-auto-top-top"></div><div id="wp-auto-top-bottom"></div></div>';
    if ((is_single() and get_option('autotop_enable_single') == 1) or (is_page() and get_option('autotop_enable_page') == 1)) {
       //echo comments_open() ? $hasComments : $noCommnets;
       echo $hasComments;
    } elseif ((is_home() and get_option('autotop_enable_home') == 1)
      or (is_tag() and get_option('autotop_enable_tags') == 1)
      or (is_archive() and get_option('autotop_enable_archive') == 1)) {
        echo $noCommnets;
    }

    switch (get_option('autotop_comment_id_type')) {
      case 'id':
           $commentPositionId = '#'.get_option('autotop_comment_id');
           break;
       case 'class':
           $commentPositionId = '.'.get_option('autotop_comment_id').':last';
           break;
      default:
           $commentPositionId = '[name="'.get_option('autotop_comment_id').'"]:last';
           break;
   }

   echo '<script> var commentPositionId = \''.$commentPositionId.'\';var wpAutoTopSpeed = '.get_option('autotop_scroll_speed', 1).';</script>';
   wp_enqueue_script('wp-auto-top',plugins_url('wp-auto-top.js',__FILE__),array('jquery'));
   echo '<style>
   #wp-auto-top{position:fixed;top:'.get_option('autotop_top_val').get_option('autotop_top_unit').';'
   .get_option('autotop_position').':50%;display:block;margin-'.get_option('autotop_position').':-'.get_option('autotop_margin').get_option('autotop_margin_unit').'; z-index:'.get_option('autotop_zindex').';}
   #wp-auto-top-top,#wp-auto-top-comment,#wp-auto-top-bottom{background:url('.plugins_url('img/',__FILE__).get_option('autotop_color_img').') no-repeat;position:relative;cursor:pointer;height:25px;width:29px;margin:10px 0 0;}
   #wp-auto-top-comment{background-position:left -30px;height:32px;}#wp-auto-top-bottom{background-position:left -68px;}
   #wp-auto-top-comment:hover{background-position:right -30px;}
   #wp-auto-top-top:hover{background-position:right 0;}
   #wp-auto-top-bottom:hover{background-position:right -68px ;}
   </style>';
}

register_activation_hook(__FILE__,'auto_top_active');
add_action('admin_menu', 'auto_top_add_admin');
add_action('wp_footer','add_auto_top_foot_code',1);