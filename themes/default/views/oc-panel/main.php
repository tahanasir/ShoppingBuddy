<?php defined('SYSPATH') or die('No direct script access.');?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?=i18n::html_lang()?>"> <!--<![endif]-->
<head>
	<meta charset="<?=Kohana::$charset?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?=$title?></title>
    <meta name="keywords" content="<?=$meta_keywords?>" >
    <meta name="description" content="<?=$meta_description?>" >
    
    <?if (Theme::get('premium')!=1):?>
    <meta name="author" content="open-classifieds.com">
    <meta name="copyright" content="<?=Core::config('general.site_name')?>" >
    <?else:?>
    <meta name="copyright" content="<?=$meta_copyright?>" >
    <?endif?>
	
	<meta name="viewport" content="width=device-width,initial-scale=1">
	
	<!--  Disallow Bots -->
	<meta name="robots" content="noindex,nofollow,noodp,noydir">
	<meta name="googlebot" content="noindex,noarchive,nofollow,noodp">
	<meta name="slurp" content="noindex,nofollow,noodp">
	<meta name="bingbot" content="noindex,nofollow,noodp,noydir">
	<meta name="msnbot" content="noindex,nofollow,noodp,noydir">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <?=HTML::script('http://html5shim.googlecode.com/svn/trunk/html5.js')?>
    <![endif]-->
    
    <?=Theme::styles($styles,'default')?>	
	<?=Theme::scripts($scripts,'header','default')?>
    <link rel="shortcut icon" href="<?=core::config('general.base_url').'images/favicon.ico'?>">

  </head>

  <body>
	<?=$header?>
  <?=View::factory('oc-panel/sidebar',array('user'=>$user))?>
    <div class="bs-docs-nav">
  
		
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 main pull-left" id="content">
				<?=Breadcrumbs::render('oc-panel/breadcrumbs')?>      
				<?=Alert::show()?>
                <?if (!isset($_COOKIE['donation_alert'])  AND Theme::get('premium')!=1 AND $user->id_role==Model_Role::ROLE_ADMIN):?>
                   <div class="alert alert-warning fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick='setCookie("donation_alert",1,7)'>×</button>
                    <h4><?=__('Help us improve!')?></h4>
                    <p><?=__('Open Classifieds is an amazing free Open Source Software. With a small donation you are helping us keep the project alive and updated. Thanks!')?></p>
                    <p>
                      <a href="http://j.mp/thanksdonate" onclick='setCookie("donation_alert",1,30)'>
                            <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="">
                      </a>
                      <?=__('Or')?>
                      <a href="<?=Route::url('oc-panel',array('controller'=>'market'))?>" class="btn btn-success">
                        <i class="glyphicon glyphicon-gift"></i> <?=__('Buy a Theme')?>
                       </a>
                    </p>
                  </div>
                <?endif?>
				<?=$content?>
                <?=(Kohana::$environment === Kohana::DEVELOPMENT)? View::factory('profiler'):''?>
	    	</div><!--/span--> 
	    	
    
    </div><!--/.fluid-->
    <div class="clearfix"></div>
    <?=$footer?>
	<?=Theme::scripts($scripts,'footer','default')?>
  
  </body>
</html>

