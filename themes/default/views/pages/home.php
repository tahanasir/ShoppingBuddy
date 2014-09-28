<?php defined('SYSPATH') or die('No direct script access.');?>
<?if(core::config('advertisement.ads_in_home') != 3):?>
<div class="well row">
    <?if(core::config('advertisement.ads_in_home') == 0):?>
    <h3><?=__('Latest Deals')?></h3>
    <?elseif(core::config('advertisement.ads_in_home') == 1 OR core::config('advertisement.ads_in_home') == 4):?>
    <h3><?=__('Featured Ads')?></h3>
    <?elseif(core::config('advertisement.ads_in_home') == 2):?>
    <h3><?=__('Popular Ads last month')?></h3>
    <?endif?>
    <div class="col-md-12">
        <?$i=0;
        foreach($ads as $ad):?>
        <div class="col-md-3">

            <div class="thumbnail latest_ads">
                <a href="<?=Route::url('ad', array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"  class="min-h">
                <?if($ad->get_first_image()!== NULL):?>
                    <img src="<?=URL::base()?><?=$ad->get_first_image()?>" >
                <?else:?>
                    <?if(( $icon_src = $ad->category->get_icon() )!==FALSE ):?>
                                <img src="<?=$icon_src?>" alt="<?=HTML::chars($ad->title)?>" >
                    <?elseif(( $icon_src = $ad->location->get_icon() )!==FALSE ):?>
                                <img src="<?=$icon_src?>" alt="<?=HTML::chars($ad->title)?>" >
                            <?else:?>
                                <img src="http://www.placehold.it/200x200&text=<?=$ad->category->name?>" alt="<?=HTML::chars($ad->title)?>"> 
                            <?endif?> 
                <?endif?>
                </a>
                <div class="caption">
                    <h5><a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?=$ad->title?></a></h5>

                    <p ><?=Text::limit_chars(Text::removebbcode($ad->description), 30, NULL, TRUE)?></p>

                </div>
            </div>
        </div>     
        <?endforeach?>
    </div>
</div>
<?endif?>


<div class='well row'>
    <h3><?=__("Stores")?></h3>
    <div class="col-md-12">
    <?$i=0;?>
        <?foreach($categs as $c):?>
        <?if($c['id_category_parent'] == 1 && $c['id_category'] != 1):?>
        <div class="col-md-4">
            <div class="category_box_title">
                <p><a title="<?=HTML::chars($c['name'])?>" href="<?=Route::url('list', array('category'=>$c['seoname']))?>"><?=mb_strtoupper($c['name']);?></a></p>
            </div>  


            <div class="well custom_box_content" style="padding: 8px 0;">
                <ul class="nav nav-list">
                    <?foreach($categs as $chi):?>
                        <?if($chi['id_category_parent'] == $c['id_category']):?>
                        <li><a title="<?=HTML::chars($chi['name'])?>" href="<?=Route::url('list', array('category'=>$chi['seoname']))?>"><?=$chi['name'];?> <span class="count_ads"><span class="badge badge-success"><?=$chi['count']?></span></span></a></li>
                        <?endif?>
                     <?endforeach?>
                </ul>
            </div>
        </div>


        <?
        $i++;
            if ($i%3 == 0) echo '<div class="clear"></div>';
            ?>
        <?endif?>
        <?endforeach?>
    </div>

</div>

     
