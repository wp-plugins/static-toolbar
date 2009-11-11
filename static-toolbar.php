<?php
/*
Plugin Name: Static Toolbar
Version: 0.2
Plugin URI: http://julienappert.com/developpements-wordpress/plugin-static-toolbar
Author: Julien Appert
Description: Static Toolbar displays a static toolbar in the bottom of the page, like the facebook's one. The toolbar may contain last posts, rss feed, search engine, social network and sharing links.
*/

class WPStaticToolbar{

	function WPStaticToolbar(){$this->__construct();}
		
	function __construct(){

		add_action('init',array(&$this,'init'));
		add_action('wp_head',array(&$this,'head'));
		add_action('admin_head',array(&$this,'admin_head'));
		add_action('wp_footer',array(&$this,'footer'));
		add_action('admin_menu', array(&$this,'admin_menu'));
		register_activation_hook( __FILE__, array(&$this,'activate') );

		$this->networks = array(
			'delicious'			=> 'http://delicious.com/',
			'digg'				=> 'http://digg.com/users/',
			'facebook'			=> 'http://www.facebook.com/',
			'flickr'				=> 'http://www.flickr.com/photos/',
			'friendfeed'		=> 'http://friendfeed.com/',
			'linkedin'			=> 'http://www.linkedin.com/in/',
			'myspace'			=> 'http://www.myspace.com/',
			'netvibes'			=> ' http://www.netvibes.com/',
			'picasa'				=> 'http://picasaweb.google.com/',
			'skype'				=> 'skype:',
			'stumbleupon'	=> 'http://www.stumbleupon.com/stumbler/',
			'technorati'		=> 'http://technorati.com/people/',
			'twitter'				=> 'http://twitter.com/',
			'youtube'			=> 'http://www.youtube.com/user/'
		); 
		$this->sharing = array(
			'facebook'	=>	'http://www.facebook.com/share.php?u=%URL%&amp;t=%TITLE%',
			'twitter'		=>	'http://twitter.com/home?status=%TITLE% - %URL%',
			'friendfeed'=>	'http://www.friendfeed.com/share?title=%TITLE%&amp;link=%URL%',
			'netvibes'	=>	'http://www.netvibes.com/share?title=%TITLE%&amp;url=%URL%',
			'digg'		=>	'http://digg.com/submit/?phase=2&amp;url=%URL%&amp;title=%TITLE%',
			'technorati'	=>	'http://technorati.com/faves?add=%URL%',
			'wikio'		=>	'http://www.wikio.fr/vote?url=%URL%',
			'myspace'		=>	'http://www.myspace.com/Modules/PostTo/Pages/?u=%URL%&amp;t=%TITLE%',
			'yahoobuzz'	=>	'http://buzz.yahoo.com/submit/?submitUrl=%URL%&amp;submitHeadline=%TITLE%',
			'email'		=>	'mailto:?subject=%TITLE%&amp;body=%URL%'
		);
	}
	
	function head(){
		?>
		<style type="text/css">
			<?php 
			$opacity = get_option('statictoolbar_opacity');
			if(strlen($opacity)>0){ ?>
		div#static-toolbar{
		opacity: .<?php echo (int)$opacity; ?>;	
		-moz-opacity:0.<?php echo (int)$opacity; ?>;
		filter : alpha(opacity=<?php echo $opacity; ?>); 
		}	
		<?php } ?>
		div#static-toolbar li, div#static-toolbar a, 
		div#static-toolbar li#static-toolbar-social-network div#static-toolbar-social-network-panel p,
		div#static-toolbar li#static-toolbar-share div#static-toolbar-share-panel p{ color:<?php echo get_option('statictoolbar_txtcolor'); ?>; }
		div#static-toolbar ul#static-toolbar-blocs,
		div#static-toolbar li#static-toolbar-share div#static-toolbar-share-panel,
		div#static-toolbar li#static-toolbar-social-network div#static-toolbar-social-network-panel{ background-color:<?php echo get_option('statictoolbar_bgcolor'); ?>}
		<?php 
			foreach($this->sharing as $key=>$value){ 
				if(get_option('statictoolbar_share_'.$key) == 1){  ?>
		div#static-toolbar li#static-toolbar-share div#static-toolbar-share-list a.static-toolbar-<?php echo $key; ?>{	background:url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/static-toolbar/images/share/<?php echo $key; ?>.png) no-repeat left; }
		<?php } } ?>
		</style>
		<?php
	}

	function activate(){
		if(!get_option('statictoolbar_bgcolor')){
			add_option('statictoolbar_bgcolor','#849EB2');
		}
		if(!get_option('statictoolbar_txtcolor')){
			add_option('statictoolbar_txtcolor','#484848');
		}	
		if(!get_option('statictoolbar_rss')){
			add_option('statictoolbar_rss','on');
		}	
		if(!get_option('statictoolbar_search')){
			add_option('statictoolbar_search','on');
		}	
		if(!get_option('statictoolbar_opacity')){
			add_option('statictoolbar_opacity',90);
		}	
		if(!get_option('statictoolbar_nb')){
			add_option('statictoolbar_nb',5);
		}			
	}

	function admin_menu(){
		add_options_page('Static Toolbar', 'Static toolbar', 8, 'static-toolbar.php',array(&$this,'adminpage'));
	}

	function maj_option($name,$value){
		if ( get_option($name) === false ) {
			add_option($name, $value);	
		} else {
			update_option($name, $value);
		}
	}

	function admin_head(){
		if(is_admin() && $_SERVER['QUERY_STRING'] == 'page=static-toolbar.php'){
			?>
			<style type="text/css">
			#statictoolbar #social_network p,#statictoolbar #colors p{	overflow:hidden;	}
			#statictoolbar label.general{	width:150px; float:left; line-height:25px;	}
			#statictoolbar #social_network label{	 width:280px;  float:left; line-height:25px; text-align:right; color:#5B5B5B;}
			#statictoolbar #social_network input.text{	height:25px; padding-left:30px; width:150px;}
			<?php foreach($this->networks as $network=>$val){ ?>
			#social_network input#<?php echo $network; ?>{	background:url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/static-toolbar/images/social-network/<?php echo $network; ?>.png) no-repeat left;}
			<?php } ?>
			#statictoolbar .ui-tabs-panel{overflow:hidden;}	
			#statictoolbar #sharing{	overflow:hidden;}
			#statictoolbar #sharing .share-link{	width:150px; float:left;}	
			#statictoolbar .share-link label{ padding-left:20px;}
			<?php foreach($this->sharing as $share=>$val){ ?>
			#statictoolbar .share-link label#<?php echo $share; ?>-share-label{background:url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/static-toolbar/images/share/<?php echo $share; ?>.png) no-repeat left;}
			<?php } ?>
			</style>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				var f = $.farbtastic('#picker');
				var p = $('#picker').css('display','none');
				var selected;
				$('.colorwell')
				.each(function () { f.linkTo(this);  })
				.focus(function() {
					f.linkTo(this);
					p.css('display', 'block');
				})
				.blur(function() {
					f.linkTo(this);
					p.css('display', 'none');
				});	
			 
				$("#statictoolbar-tabs").tabs(); 
				<?php if(isset($_POST['tab'])){ ?>
				$("#statictoolbar-tabs").tabs("select",<?php echo $_POST['tab']; ?>); 
				<?php } ?>			 
			 
			 });
			</script>		
			<?php
		}
	}

	function showDonate(){
	if(WPLANG == 'fr_FR'){
		echo '
			<div style="float:right;width:250px;text-align:center;">
				<p><strong>Ce plugin vous rend service ? Pour aider à pérenniser son développement, merci de... </strong></p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
		">
					<input type="image" src="https://www.paypal.com/fr_FR/FR/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
					<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>			
		';
	}
	else{
		echo '
			<div style="float:right;width:250px;text-align:center;">
				<p><strong>Does this plugin help you ? Help keep it actively developed by clicking the donate button. </strong></p>		
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
					">
					<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
		';
	
	}
	
	}
	
	function adminpage(){
		if(isset($_POST['statictoolbar-submit'])){
			foreach($this->networks as $network=>$val){
				$this->maj_option('statictoolbar_'.$network,$_POST[$network]);
			}
			if(count($_POST['share'])>0){
				foreach($this->sharing as $share=>$val){
					if(in_array($share,$_POST['share'])){
						$this->maj_option('statictoolbar_share_'.$share,1);
					}
					else{
						$this->maj_option('statictoolbar_share_'.$share,0);
					}
				}			
			}
			$this->maj_option('statictoolbar_nb',$_POST['nb']);
			$this->maj_option('statictoolbar_rss',$_POST['rss']);
			$this->maj_option('statictoolbar_search',$_POST['search']);
			$this->maj_option('statictoolbar_opacity',$_POST['opacity']);
			$this->maj_option('statictoolbar_bgcolor',$_POST['bgcolor']);
			$this->maj_option('statictoolbar_txtcolor',$_POST['txtcolor']);
		}

		?>
		<div class="wrap" id="statictoolbar">
			<h2><?php  _e("Static toolbar options","statictoolbar"); ?></h2>
									
			<form action="" method="post" onsubmit="jQuery('#statictoolbar-tab').val(jQuery('#statictoolbar-tabs').tabs().tabs('option', 'selected')); ">
				<input type="hidden" name="tab" value="0" id="statictoolbar-tab" />
				<div id="statictoolbar-tabs">
					<ul>
						<li><a href="#statictoolbar-tabs-1"><?php echo __("General","statictoolbar"); ?></a></li>			
						<li><a href="#statictoolbar-tabs-2"><?php echo __("Social Network","statictoolbar"); ?></a></li>			
						<li><a href="#statictoolbar-tabs-3"><?php echo __("Sharing","statictoolbar"); ?></a></li>	
						<li><a href="#statictoolbar-tabs-4"><?php echo __("Credits","statictoolbar"); ?></a></li>	
					</ul>
					
					<div id="statictoolbar-tabs-1">
						<?php $this->showDonate(); ?>				
						<p>
							<input type="checkbox" name="rss" id="rss"  <?php if(get_option('statictoolbar_rss') == 'on') echo 'checked="checked"'; ?>/>
							<label for="rss"><?php _e('Show RSS link','statictoolbar'); ?></label>
						</p>
						<p>
							<input type="checkbox" name="search" id="search"  <?php if(get_option('statictoolbar_search') == 'on') echo 'checked="checked"'; ?>/>
							<label for="search"><?php _e('Show search engine','statictoolbar'); ?></label>
						</p>	
						<p>
							<label for="opacity" class="general likeColor"><?php _e('Toolbar opacity','statictoolbar'); ?></label>
							<input type="text" name="opacity" id="opacity"  value="<?php echo get_option('statictoolbar_opacity');  ?>" /> %						
						</p>	
						<p>
							<label for="nb" class="general"><?php _e('Number of posts','statictoolbar'); ?></label>
							<input type="text" name="nb" id="nb"  value="<?php echo get_option('statictoolbar_nb');  ?>" />				
						</p>							
						<div id="colors">
							<div id="picker" ></div>
							<h3><?php _e('colors','statictoolbar'); ?></h3>
							<p>
								<label for="bgcolor" class="general"><?php _e('Background color','statictoolbar'); ?></label>
								<input type="text" class="colorwell" id="bgcolor" name="bgcolor" value="<?php echo get_option('statictoolbar_bgcolor'); ?>" />
							</p>
							<p>
								<label for="txtcolor" class="general"><?php _e('Text color','statictoolbar'); ?></label>
								<input type="text" class="colorwell" id="txtcolor" name="txtcolor" value="<?php echo get_option('statictoolbar_txtcolor'); ?>" />
							</p>				
						</div>
					</div>
					<div id="statictoolbar-tabs-2">
						<?php $this->showDonate(); ?>						
					
						<div id="social_network">
							<h3><?php _e('Social network','statictoolbar'); ?></h3>
							<?php 
							foreach($this->networks as $network=>$link){ ?>				
							<p>
								<label for="<?php echo $network; ?>" id="<?php echo $network; ?>-label"><?php echo $link; ?></label>
								<input type="text" class="text" name="<?php echo $network; ?>" id="<?php echo $network; ?>" value="<?php echo str_replace($link,'',get_option('statictoolbar_'.$network)); ?>" />
							</p>
							<?php } ?>
						</div>
					</div>
					<div id="statictoolbar-tabs-3">
						<?php $this->showDonate(); ?>		
						<div id="sharing">
						<?php foreach($this->sharing as $key=>$value){ ?>
							<div class="share-link">
								<input type="checkbox" name="share[]" value="<?php echo $key; ?>" id="share_<?php echo $key; ?>" <?php if(get_option('statictoolbar_share_'.$key) == 1){ echo 'checked="checked"'; } ?>/>
								<label for="share_<?php echo $key; ?>" id="<?php echo $key; ?>-share-label"><?php echo $key; ?></label>
							</div>
						<?php } ?>
						</div>
					</div>
					<div id="statictoolbar-tabs-4">
						<?php $this->showDonate(); ?>
						<p>Plugin développé par Julien Appert.
						<br/><a href="http://julienappert.com">http://julienappert.com</a>
						<br/><a href="http://twitter.com/apperisphere">http://twitter.com/apperisphere</a>
						</p>
						
					</div>
				</div>
				<p class="submit">
					<input type="submit" name="statictoolbar-submit" class="button-primary" value="<?php echo _e('Save the configuration','statictoolbar'); ?>" />
				</p>				
			</form>
		</div>
		<?php
	}

	function sharePermalink(){
		if(is_page() || is_single()){
			return get_permalink();
		}
		elseif(is_home()){
			return get_bloginfo('url');
		}
		elseif(is_category()){
			return get_category_link( get_cat_ID(single_cat_title('',false)));
		}
		elseif(is_tag()){
			global $wp_query,$post;
			if(strlen(get_option('tag_base'))>0){
				return get_bloginfo('url').'/'.get_option('tag_base').'/'.$wp_query->query_vars['tag'];
			}
			else{
				return get_bloginfo('url').'/tag/'.$wp_query->query_vars['tag'];
			}
		}
	}
	function shareTitle(){
		if(is_page() || is_single()){
			return the_title('','',false);
		}
		elseif(is_home()){
			return get_bloginfo('name');
		}
		elseif(is_category()){
			return single_cat_title('',false);
		}
		elseif(is_tag()){
			return single_tag_title('',false);
		}
	}

	function footer(){ 
		?>
		<div id="static-toolbar">
			<ul id="static-toolbar-blocs">
				<?php if(get_option('statictoolbar_search') == 'on'){ ?>
				<li id="static-toolbar-search">
					<form action="<?php bloginfo('url'); ?>" method="get">
						<input type="text" name="s" id="static-toolbar-search-input" value="<?php echo addslashes(__('Search...','statictoolbar')); ?>" onfocus="if(this.value=='<?php echo addslashes(__('Search...','statictoolbar')); ?>') this.value=''" onblur="if(this.value=='') this.value='<?php echo addslashes(__('Search...','statictoolbar')); ?>'" />
						<button type="submit"  id="static-toolbar-search-submit">
							<img src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/search.png'; ?>" alt="<?php echo addslashes(__('Find','statictoolbar')); ?>"/>
						</button>
					</form>
				</li>	
				<?php  } ?>
				<?php
				$hasNetwork = false;
				 foreach($this->networks as $network=>$val){
					if(strlen(get_option('statictoolbar_'.$network))>0){	$hasNetwork = true; break;	}
				}
				if($hasNetwork){
				?>
				<li id="static-toolbar-social-network">
					<img src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/users.png'; ?>" alt="<?php _e('Social Network','statictoolbar'); ?>" title="<?php _e('Social Network','statictoolbar'); ?>" />
					<div id="static-toolbar-social-network-panel">
						<p><?php _e('Social Network','statictoolbar'); ?> <span>X</span></p>
						<div id="static-toolbar-social-network-list">
							<?php foreach($this->networks as $network=>$link){
								 if(strlen(get_option('statictoolbar_'.$network))>0){ ?>
									<a href="<?php echo $link.str_replace($link,'',get_option('statictoolbar_'.$network)); ?>" title="<?php _e('follow me on','statictoolbar'); ?> <?php echo $network; ?>"><img src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/social-network/'.$network.'.png'; ?>" alt="<?php _e('follow me on ','statictoolbar'); ?> <?php echo $network; ?>"/></a>
								<?php  } 
							} ?>
						</div>
					</div>
				</li>
				<?php } ?>
				<?php if(!is_404()){ 
					$hasShare = false;
					foreach($this->sharing as $key=>$value){ 
						if(get_option('statictoolbar_share_'.$key) == 1){	$hasShare = true; break;	}
					}
					if($hasShare){
					?>
				<li id="static-toolbar-share">
					<img src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/share.png'; ?>" alt="<?php _e('Share','statictoolbar'); ?>" title="<?php _e('Share','statictoolbar'); ?>" />
					<div id="static-toolbar-share-panel">
						<p><?php _e('Share this page','statictoolbar'); ?> <span>X</span></p>
						<div id="static-toolbar-share-list">		
							<ul>
								<?php 
								foreach($this->sharing as $key=>$value){ 
									if(get_option('statictoolbar_share_'.$key) == 1){  ?>
								<li><a class="static-toolbar-<?php echo $key; ?>" href="<?php echo str_replace(array('%TITLE%','%URL%'), array($this->shareTitle(),$this->sharePermalink()),$value); ?>"><?php echo $key; ?></a></li>
								<?php }
								}  ?>
							</ul>
						</div>
					</div>				
				</li>
				<?php }	} ?>
				<?php if(get_option('statictoolbar_rss') == 'on'){ ?>
				<li id="static-toolbar-feed">
					<a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e("subscribe to the blog","statictoolbar"); ?>"><img src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/feed.png'; ?>" alt="<?php _e("subscribe to the blog","statictoolbar"); ?>"/></a>
				</li>
				<?php } ?>			
				<li id="static-toolbar-posts">			
					<ul>
					<?php
					$nb = get_option('statictoolbar_nb');
					if(!$nb) $nb = 5;
					$posts = get_posts('numberposts='.$nb);
					$i = 0;
					foreach($posts as $post){
						$class = ($i==0)	?	' class="entryActive"'	:	'';
						setup_postdata($post);
						echo '<li'.$class.'><a href="'.get_permalink($post->ID).'">'.get_the_title($post->ID).'</a></li>';
						$i++;
					}
					?>
					</ul>
				</li>
				<li id="static-toolbar-button">
					<img title="<?php _e('Close static bar','statictoolbar'); ?>" id="static-toolbar-close" src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/close.png'; ?>" alt="<?php echo addslashes(__('Close','statictoolbar')); ?>"/>
					<img title="<?php _e('Open static bar','statictoolbar'); ?>" style="display:none" id="static-toolbar-open" src="<?php echo WP_PLUGIN_URL.'/static-toolbar/images/open.png'; ?>" alt="<?php echo addslashes(__('Open','statictoolbar')); ?>"/>
				</li>
			</ul>
		</div>
		<?php
	}


	function init(){
		if(!is_admin()){
			wp_enqueue_script('static-toolbar-js',  WP_PLUGIN_URL . '/static-toolbar/static-toolbar.js',array('jquery'));
			wp_enqueue_script('jquery-corner',  WP_PLUGIN_URL . '/static-toolbar/jquery.corner.js',array('jquery'));
			wp_enqueue_style('static-toolbar-css', WP_PLUGIN_URL . '/static-toolbar/static-toolbar.css');
		}
		elseif($_SERVER['QUERY_STRING'] == 'page=static-toolbar.php'){
			wp_enqueue_style('farbtastic-css', WP_PLUGIN_URL . '/static-toolbar/farbtastic.css');
			wp_enqueue_script('farbtastic-js',  WP_PLUGIN_URL . '/static-toolbar/farbtastic.js',array('jquery'));			
			wp_enqueue_script('jquery-ui',WP_PLUGIN_URL . '/static-toolbar/jquery-ui.js',array('jquery'),'');
			wp_enqueue_style('jquery-ui-style',WP_PLUGIN_URL . '/static-toolbar/css/ui.all.css',array(),false,'all');		
		}
	}
}

new WPStaticToolbar();
?>