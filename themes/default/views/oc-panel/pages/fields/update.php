<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
	<h1><?=__('Edit Custom Field')?></h1>
</div>
        
<form class="well form-horizontal"  method="post" action="<?=Route::url('oc-panel',array('controller'=>'fields','action'=>'update','id'=>$name))?>">         
      <?=Form::errors()?>  
      
        <div class="form-group">
            <label class="control-label col-xs-1"><?=__('Name')?></label>
                <div class="col-sm-4">
                <input  DISABLED class="form-control" type="text" name="name" value="<?=$name?>" placeholder="<?=__('Name')?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-1"><?=__('Type')?></label>
                <div class="col-sm-4">
                <input  DISABLED class="form-control" type="text" id="cf_type_field_input" name="type" value="<?=$field_data['type']?>" placeholder="<?=__('Type')?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-xs-1"><?=__('Label')?></label>
                <div class="col-sm-4">
                <input class="form-control" type="text" name="label" value="<?=$field_data['label']?>" placeholder="<?=__('Label')?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-1"><?=__('Tooltip')?></label>
                <div class="col-sm-4">
                <input class="form-control" type="text" name="tooltip" value="<?=(isset($field_data['tooltip']))?$field_data['tooltip']:""?>" placeholder="<?=__('Tooltip')?>">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-1"><?=__('Values')?></label>
                <div class="col-sm-4">
                <input class="form-control" type="text" id="cf_values_input" name="values" value="<?=(is_array($field_data['values']))? implode(",", $field_data['values']): $field_data['values']?>" placeholder="<?=__('Comma separated for select')?>">
            </div>
        </div>

        <!-- multycategory selector -->
        <div class="form-group">
        <label class="control-label col-xs-1"><?=__('Categories')?></label>
            <div class="col-sm-4">
                
                <select id="categories" name="categories[]" multiple data-placeholder="<?=__('Choose 1 or several categories')?>">
                    <option></option>
                    <?foreach ($categories as $categ => $ctg):?>
                        <?if($categ !== 1 ):?>
                            <?if(isset($field_data['categories']) AND is_array($field_data['categories']) AND in_array($categ, $field_data['categories'])):?>
                                <option value="<?=$categ?>" selected><?=$ctg['name']?></option>
                            <?else:?>
                                <option value="<?=$categ?>"><?=$ctg['name']?></option>
                            <?endif?>
                        <?endif?>
                    <?endforeach?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="checkbox col-xs-offset-4">
                  <input type="checkbox" name="required" <?=($field_data['required']==TRUE)?'checked':''?>> 
                   <?=__('Required')?>
                </label>
            <div class="help-block col-xs-offset-4"><?=__('Required field to submit a new ad.')?></div></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="checkbox col-xs-offset-4">
                  <input type="checkbox" name="searchable" <?=($field_data['searchable']==TRUE)?'checked':''?>> 
                   <?=__('Searchable')?>
                </label>
            <div class="help-block col-xs-offset-4"><?=__('Search in ads will include this field as well.')?></div></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="checkbox col-xs-offset-4">
                  <input type="checkbox" name="admin_privilege" <?=(isset($field_data['admin_privilege']) AND $field_data['admin_privilege']==TRUE)?'checked':''?>> 
                   <?=__('Admin Privileged')?>
                </label>
            <div class="help-block col-xs-offset-4"><?=__('Can be seen and edited only by admin.')?></div></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="checkbox col-xs-offset-4">
                  <input type="checkbox" name="show_listing" <?=(isset($field_data['show_listing']) AND $field_data['show_listing']==TRUE)?'checked':''?>> 
                   <?=__('Show Listing')?>
                </label>
            <div class="help-block col-xs-offset-4"><?=__('Can be seen in the list of ads while browsing.')?></div></div>
        </div>
      
      <div class="form-actions">
        <a href="<?=Route::url('oc-panel',array('controller'=>'fields','action'=>'index'))?>" class="btn btn-default ajax-load"><?=__('Cancel')?></a>
        <button type="submit" class="btn btn-primary"><?=__('Save')?></button>
      </div>
</form>
