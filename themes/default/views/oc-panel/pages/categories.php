<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
    <h1><?=__('Categories')?></h1>
    <p><?=__("Change the order of your categories. Keep in mind that more than 2 levels nested probably won´t be displayed in the theme (it is not recommended).")." <a target='_blank' href='http://open-classifieds.com/2013/08/12/how-to-add-categories/'>".__('Read more')."</a>"?></p>
    <a class="btn btn-primary ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'category','action'=>'create'))?>">
  <?=__('New Category')?></a>
</div>
<div class="col-md-7">

<div class="well">
    <span class="label label-info"><?=__('Heads Up!')?> <?=__('Quick category creator.')?></span>
    <div class="clearfix"></div> 
    <?=__('Add names for multiple categories, for each one push enter.')?>
    <div class="clearfix"></div><br>
    
    <?= FORM::open(Route::url('oc-panel',array('controller'=>'category','action'=>'multy_categories')), array('class'=>'form-inline', 'role'=>'form','enctype'=>'multipart/form-data'))?>
        <div class="form-group">
            <div class="">
            <?= FORM::label('multy_categories', __('Name').':', array('class'=>'control-label', 'for'=>'multy_categories'))?>
            <?= FORM::input('multy_categories', '', array('placeholder' => __('Hit enter to confirm'), 'class' => 'form-control', 'id' => 'multy_categories', 'type' => 'text','data-role'=>'tagsinput'))?>
            </div>
        </div>
        <div class="clearfix"></div>
        <?= FORM::button('submit', __('Send'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'category','action'=>'multy_categories'))))?>
    <?= FORM::close()?>
    </div>
</div>
<ol class='plholder col-md-8' id="ol_1" data-id="1">
<?=_('Home')?>
<?function lili($item, $key,$cats){?>
    <li data-id="<?=$key?>" id="li_<?=$key?>"><i class="glyphicon   glyphicon-move"></i> <?=$cats[$key]['name']?>
        
        <a data-text="<?=__('Are you sure you want to delete? We will move the siblings categories and ads to the parent of this category.')?>" 
           data-id="li_<?=$key?>"
           onclick="return confirm('<?=__('Delete?')?>');" 
           class="btn btn-xs btn-danger pull-right"  
           href="<?=Route::url('oc-panel', array('controller'=> 'category', 'action'=>'delete','id'=>$key))?>">
                    <i class="glyphicon   glyphicon-trash"></i>
        </a>

        <a class="btn btn-xs btn-primary pull-right ajax-load" 
            href="<?=Route::url('oc-panel',array('controller'=>'category','action'=>'update','id'=>$key))?>">
            <?=__('Edit')?>
        </a>

        <ol data-id="<?=$key?>" id="ol_<?=$key?>">
            <? if (is_array($item)) array_walk($item, 'lili', $cats);?>
        </ol><!--ol_<?=$key?>-->

    </li><!--li_<?=$key?>-->
<?}array_walk($order, 'lili',$cats);?>
</ol><!--ol_1-->

<span id='ajax_result' data-url='<?=Route::url('oc-panel',array('controller'=>'category','action'=>'saveorder'))?>'></span>
