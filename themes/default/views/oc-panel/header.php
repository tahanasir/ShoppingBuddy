<?php defined('SYSPATH') or die('No direct script access.');?>
<header class="navbar navbar-inverse navbar-fixed-top bs-docs-nav">
    <div class="header-container">
        <div class="navbar-header">        </div> 

            <button class="navbar-toggle pull-left" type="button" data-toggle="collapse" id="mobile_header_btn">
                <span class="sr-only"><?=__('Toggle navigation')?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'home'))?>"><i class="glyphicon glyphicon-th-large"></i> <?=__('Panel')?></a>
            <div class="btn-group pull-right ml-20">
                <?=View::factory('oc-panel/widget_login')?>
            </div>
            
            <div class="navbar-collapse collapse" id="mobile-menu-panel">
                <ul class="nav navbar-nav">
                    <?=Theme::admin_link(__('Market'), 'market','index','oc-panel','glyphicon glyphicon-gift')?>
                    <? if(core::config('general.moderation') == 1 OR // moderation on  
                          core::config('general.moderation') == 4 OR // email confiramtion with moderation
                          core::config('general.moderation') == 5):  // payment with moderation?>
                    <?=Theme::admin_link(__('Moderation'),'ad','moderate','oc-panel','glyphicon glyphicon-ban-circle')?>
                    <? endif?>
                	<?=Theme::admin_link(__('Stats'),'stats','index','oc-panel','glyphicon glyphicon-align-left')?>
                    <?=Theme::admin_link(__('Widgets'),'widget','index','oc-panel','glyphicon glyphicon-move')?>
                    <?=Theme::admin_link(__('Cache'),'tools','cache','oc-panel','  glyphicon-cog glyphicon')?>
                    <? if(Auth::instance()->get_user()->id_role==Model_Role::ROLE_ADMIN):?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-cog"></i> <?=__('Cache')?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <?=Theme::admin_link(__('Cache'),'tools','cache','oc-panel','glyphicon glyphicon-cog')?>
                            <?=Theme::admin_link(__('Delete all'),'tools','cache?force=1','oc-panel','glyphicon glyphicon-remove-sign')?>
                            <?=Theme::admin_link(__('Delete expired'),'tools','cache?force=2','oc-panel','glyphicon glyphicon-remove-circle')?>
                        </ul>
                    </li>
            	    <li  class="dropdown ">
                        <a href="#" class="dropdown-toggle"
            		      data-toggle="dropdown"><i class="glyphicon glyphicon-plus"></i> <?=__('New')?> <b class="caret"></b></a>
                    	<ul class="dropdown-menu">
                            <?=Theme::admin_link(__('Category'),'category','create')?>
                            <?=Theme::admin_link(__('Location'),'location','create')?>
                            <?=Theme::admin_link(__('Blog post'),'blog','create')?>
                            <?=Theme::admin_link(__('FAQ'),'content','create?type=help&locale_select='.core::config('i18n.locale'),'oc-panel')?>
                            <?=Theme::admin_link(__('Page'), 'content','create?type=page&locale_select='.core::config('i18n.locale'),'oc-panel')?>
                    		<li class="divider"></li>
                    		<li><a href="<?=Route::url('post_new')?>">
                    			<i class="glyphicon  glyphicon-pencil"></i> <?=__('Publish new')?></a>	</li>
                    	</ul>
            	   </li> 
                   <?else:?>
                    <li><a href="<?=Route::url('post_new')?>">
                                <i class="glyphicon glyphicon-pencil"></i> <?=__('Publish new')?></a>
                    </li>
                   <?endif?>
                </ul>
                <div class=""></div>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="<?=Route::url('default')?>">
                            <i class="glyphicon-home glyphicon"></i>
                            <?if (Auth::instance()->get_user()->id_role != Model_Role::ROLE_USER):?>
	                            <?=__('Go to Your Website')?>
                            <?else:?>
	                            <?=__('Go to Website')?>
                            <?endif?>
                        </a>
                    </li>
                </ul>
            </div> <!--/.nav-collapse -->
    </div><!--/. -->
</header><!--/.navbar -->

<?if (Auth::instance()->get_user()->id_role != Model_Role::ROLE_USER):?>
<div class="col-sm-3 col-md-3 hidden-xs pull-right search-container-oc-faq">
    <form class="navbar-form pull-right p_m0" role="search" action="http://open-classifieds.com/" target="_blank">
    <div class="input-group">
        <input type="text" class="form-control header-oc-faq" placeholder="<?=__('Search FAQs')?>" name="s" style="display: none;">
        <input name="category_name" type="hidden" value="howto"/>
        <div class="input-group-btn">
            <button type="button" class="btn btn-default pull-right oc-faq-btn">
                <i class="glyphicon glyphicon-question-sign"></i>
            </button>
        </div>
    </div>
    </form>
</div> 
<?endif?>
