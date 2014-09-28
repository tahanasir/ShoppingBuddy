<?php defined('SYSPATH') or die('No direct script access.');?>
<header class="navbar navbar-inverse navbar-fixed-top bs-docs-nav">

    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?=Route::url('default')?>"> <img style="margin-top:-10px; margin-left:-20px; "height="40px" src="http://i.imgur.com/HV5woMW.png?1"/>
</a>
        </div>

    	<?
        $cats = Model_Category::get_category_count();
        $loc_seoname = NULL;

        if (Model_Location::current()->loaded())
            $loc_seoname = Model_Location::current()->seoname;
    
        ?>

    	<div class="collapse navbar-collapse" id="mobile-menu-panel">
    		<ul class="nav navbar-nav">
    		<?if (class_exists('Menu') AND count( $menus = Menu::get() )>0 ):?>
                <?foreach ($menus as $menu => $data):?>
                    <li class="<?=(Request::current()->uri()==$data['url'])?'active':''?>" >
                    <a href="<?=$data['url']?>" target="<?=$data['target']?>">
                        <?if($data['icon']!=''):?><i class="<?=$data['icon']?>"></i> <?endif?>
                        <?=$data['title']?></a> 
                    </li>
                <?endforeach?>
            <?else:?>
    			<?=Theme::nav_link(__('Deals'),'ad', 'glyphicon glyphicon-list' ,'listing', 'list')?>
    			<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=__('Stores')?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">

                  	<?foreach($cats as $c ):?>
                  		<?if($c['id_category_parent'] == 1 && $c['id_category'] != 1):?>
    						<li class="dropdown-submenu">
                                <a tabindex="-1" title="<?=HTML::chars($c['seoname'])?>" href="<?=Route::url('list', array('category'=>$c['seoname'],'location'=>$loc_seoname))?>">
                                    <?=$c['name']?>
                                </a>
                                <?if($c['has_siblings'] AND $c['id_category_parent'] == 1):?>
        							<ul class="dropdown-menu">							
            						 	<?foreach($cats as $chi):?>
                                    	<?if($chi['id_category_parent'] == $c['id_category']):?>
                                   			<li>
                                                <a title="<?=HTML::chars($chi['name'])?>" href="<?=Route::url('list', array('category'=>$chi['seoname'],'location'=>$loc_seoname))?>">
                                                    <span class="header_cat_list"><?=$chi['name']?></span> 
                                                    <span class="count_ads"><span class="badge badge-success"><?=$chi['count']?></span></span>
                                                </a>
                                            </li>
                                   		<?endif?>
                                 		<?endforeach?>
        							</ul>
                                <?endif?>
    						</li>
    					<?endif?>
    				<?endforeach?>
                  </ul>
                </li>
                <?if (core::config('general.blog')==1):?>
                    <?=Theme::nav_link(__('Blog'),'blog','','index','blog')?>
                <?endif?>
                <?if (core::config('general.faq')==1):?>
                    <?=Theme::nav_link(__('FAQ'),'faq','','index','faq')?>
                <?endif?>
                <?if (core::config('general.forums')==1):?>
                    <?=Theme::nav_link(__('Forums'),'forum','glyphicon glyphicon-tag','index','forum-home')?>
                <?endif?>
                <?if (core::config('advertisement.map')==1):?>
                    <?=Theme::nav_link('','map', 'glyphicon glyphicon-globe ', 'index', 'map')?>
                <?endif?>
                <?=Theme::nav_link('','contact', 'glyphicon glyphicon-envelope ', 'index', 'contact')?>
            <?endif?>
            </ul>

             <?= FORM::open(Route::url('search'), array('class'=>'col-xs-4', 'method'=>'GET', 'action'=>''))?>



                    <input type="text" name="search" class="search-query form-control" placeholder="<?=__('Search')?>" style="margin-top:10px;">

                 <?= FORM::close()?>
            
            <div class="btn-group pull-right btn-header-group">
                <?=View::factory('widget_login')?>
            
                <a class="btn btn-danger" href="<?=Route::url('post_new')?>">
                    <i class="glyphicon glyphicon-pencil glyphicon"></i>
                    <?=__('Publish new ')?>
                </a>                
            </div>	
    	</div><!--/.nav-collapse -->
    </div>
</header>

<?if (!Auth::instance()->logged_in()):?>
	<div id="login-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <a class="close" data-dismiss="modal" >&times;</a>
                  <h3><?=__('Login')?></h3>
                </div>
                
                <div class="modal-body">
    				<?=View::factory('pages/auth/login-form')?>
        		</div>
            </div>
        </div>
    </div>
    
    <div id="forgot-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <a class="close" data-dismiss="modal" >&times;</a>
                  <h3><?=__('Forgot password')?></h3>
                </div>
                
                <div class="modal-body">
    				<?=View::factory('pages/auth/forgot-form')?>
        		</div>
            </div>
        </div>
    </div>
    
     <div id="register-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <a class="close" data-dismiss="modal" >&times;</a>
                  <h3><?=__('Register')?></h3>
                </div>
                
                <div class="modal-body">
    				<?=View::factory('pages/auth/register-form')?>
        		</div>
            </div>
        </div>
    </div>
<?endif?>


