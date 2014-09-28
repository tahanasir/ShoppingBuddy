<?php defined('SYSPATH') or die('No direct script access.');?>
     

    <div class="page-header">
        <?if ($category!==NULL):?>
	       <h1><?=$category->name?></h1>
        <?elseif ($location!==NULL):?>
           <h1><?=$location->name?></h1>
        <?else:?>
           <h1><?=__('Deals')?></h1>
	    <?endif?>
	</div>

    <div class="well" id="recomentadion">
        <?if (Controller::$image!==NULL):?>
            <img src="<?=Controller::$image?>" class="img-responsive" alt="<?=($category!==NULL) ? HTML::chars($category->name) : (($location!==NULL AND $category===NULL) ? HTML::chars($location->name) : NULL)?>">
        <?endif?>

        <p>
        <?if ($category!==NULL):?>
            <?=$category->description?> 
        <?elseif ($location!==NULL):?>
            <?=$location->description?>
        <?endif?>
        </p>

        <i class="glyphicon glyphicon-pencil"></i> <a title="<?=__('New Advertisement')?>" 
            href="<?=Route::url('post_new')?>?category=<?=($category!==NULL)?$category->seoname:''?>&location=<?=($location!==NULL)?$location->seoname:''?>"><?=__('Publish new deal')?></a>
    </div><!--end of recomentadion-->

<div class="btn-group pull-right">
    <button type="button" id="sort" data-sort="<?=core::request('sort')?>" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-list-alt"></span><?=__('Sort')?> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" id="sort-list">
        <li><a href="?sort=title-asc"><?=__('Name (A-Z)')?></a></li>
        <li><a href="?sort=title-desc"><?=__('Name (Z-A)')?></a></li>
        <?if(core::config('advertisement.price')!=FALSE):?>
        <li><a href="?sort=price-asc"><?=__('Price (Low)')?></a></li>
        <li><a href="?sort=price-desc"><?=__('Price (High)')?></a></li>
        <?endif?>
        <li><a href="?sort=featured"><?=__('Featured')?></a></li>
        <li><a href="?sort=published-desc"><?=__('Newest')?></a></li>
        <li><a href="?sort=published-asc"><?=__('Oldest')?></a></li>
    </ul>
</div>
<div class="clearfix"></div>
    <?if(count($ads)):?>
        <?foreach($ads as $ad ):?>
            <?if($ad->featured >= Date::unix2mysql(time())):?>
                <article class="list well clearfix featured ">
                    <span class="label label-danger pull-right"><?=__('Featured')?></span>
            <?else:?>
                <article class="list well clearfix">
            <?endif?>
                <div class="pull-right favorite" id="fav-<?=$ad->id_ad?>">
                    <?if (Auth::instance()->logged_in()):?>
                        <?$fav = Model_Favorite::is_favorite($user,$ad);?>
                        <a data-id="fav-<?=$ad->id_ad?>" class="add-favorite <?=($fav)?'remove-favorite':''?>" title="<?=__('Add to Favorites')?>" href="<?=Route::url('oc-panel', array('controller'=>'profile', 'action'=>'favorites','id'=>$ad->id_ad))?>">
                        	<i class="glyphicon glyphicon-heart<?=($fav)?'':'-empty'?>"></i>
						</a>
                    <?else:?>
                        <a data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'login'))?>#login-modal">
                        	<i class="glyphicon glyphicon-heart-empty"></i>
						</a>
                    <?endif?>
                </div>
                <?if($ad->id_location != 1):?>
                    <a href="<?=Route::url('list',array('location'=>$ad->location->seoname))?>" title="<?=HTML::chars($ad->location->name)?>">
                        <span class="label label-default"><?=$ad->location->name?></span>
                    </a>
                <?endif?>
                <h2>
                    <a title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"> <?=$ad->title?></a>
                </h2>
                
                
                <div class="picture">
                    <a class="pull-left" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
                        <figure>
                            <?if($ad->get_first_image() !== NULL):?>
                                <img src="<?=URL::base()?><?=$ad->get_first_image()?>" alt="<?=HTML::chars($ad->title)?>" />
                            <?elseif(( $icon_src = $ad->category->get_icon() )!==FALSE ):?>
                                <img src="<?=$icon_src?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>" />
                            <?elseif(( $icon_src = $ad->location->get_icon() )!==FALSE ):?>
                                <img src="<?=$icon_src?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>" />
                            <?else:?>
                                <img src="http://www.placehold.it/200x200&text=<?=urlencode($ad->category->name)?>" width="200" height="200" />
                            <?endif?>
                        </figure>
                    </a>
                </div>
                
                <ul>
                    <?if ($ad->published!=0){?>
                        <li><b><?=__('Publish Date');?>:</b> <?=Date::format($ad->published, core::config('general.date_format'))?></li>
                    <? }?>
                    <?if ($ad->price!=0){?>
                        <li class="price"><?=__('Price');?>: <b><?=i18n::money_format( $ad->price)?></b></li>
                    <?}?>  
                </ul>
             
                <p><?=Text::limit_chars(Text::removebbcode($ad->description), 255, NULL, TRUE);?></p>
                
                <a title="<?=HTML::chars($ad->seotitle);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><i class="glyphicon glyphicon-share"></i><?=__('Read more')?></a>
                <?if ($user !== NULL && $user->id_role == Model_Role::ROLE_ADMIN):?>
                    <br />
                    <div class="toolbar btn btn-primary btn-xs"><i class="glyphicon glyphicon-cog"></i>
                        <div id="user-toolbar-options<?=$ad->id_ad?>" class="hide user-toolbar-options">
                            <a class="btn btn-primary btn-xs" href="<?=Route::url('oc-panel', array('controller'=>'profile','action'=>'update','id'=>$ad->id_ad))?>"><i class="glyphicon glyphicon-edit"></i> <?=__("Edit");?></a> |
                            <a class="btn btn-primary btn-xs" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate','id'=>$ad->id_ad))?>" 
                                onclick="return confirm('<?=__('Deactivate?')?>');"><i class="glyphicon glyphicon-off"></i><?=__("Deactivate");?>
                            </a> |
                            <a class="btn btn-primary btn-xs" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam','id'=>$ad->id_ad))?>" 
                                onclick="return confirm('<?=__('Spam?')?>');"><i class="glyphicon glyphicon-fire"></i><?=__("Spam");?>
                            </a> |
                            <a class="btn btn-primary btn-xs" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$ad->id_ad))?>" 
                                onclick="return confirm('<?=__('Delete?')?>');"><i class="glyphicon glyphicon-remove"></i><?=__("Delete");?>
                            </a>

                        </div>
                    </div>
                <?elseif($user !== NULL && $user->id_user == $ad->id_user):?>

                <br/>
                <div class="toolbar btn btn-primary btn-xs"><i class="glyphicon glyphicon-cog"></i>
                    <div id="user-toolbar-options<?=$ad->id_ad?>" class="hide user-toolbar-options">
                        <a class="btn btn-primary btn-xs" href="<?=Route::url('oc-panel', array('controller'=>'profile','action'=>'update','id'=>$ad->id_ad))?>"><i class="glyphicon glyphicon-edit"></i><?=__("Edit");?></a> |
                        <a class="btn btn-primary btn-xs" href="<?=Route::url('oc-panel', array('controller'=>'profile','action'=>'deactivate','id'=>$ad->id_ad))?>" 
                            onclick="return confirm('<?=__('Deactivate?')?>');"><i class="glyphicon glyphicon-off"></i><?=__("Deactivate");?>
                        </a>
                    </div>
                </div>
                <?endif?>
            </article>
        
        <?endforeach?>

        <?=$pagination?>
       <?elseif (count($ads) == 0):?>
       <!-- Case when we dont have ads for specific category / location -->
        <div class="page-header">
            <h3><?=__('We do not have any advertisements in this category')?></h3>
        </div>
      <?endif?>
