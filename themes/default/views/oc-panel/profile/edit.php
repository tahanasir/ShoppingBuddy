<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="row">
	<div class="col-md-10">
		<div class="page-header">
			<h1><?=__('Edit Profile')?></h1>
		</div>

		<?= FORM::open(Route::url('oc-panel',array('controller'=>'profile','action'=>'edit')), array('class'=>'well form-horizontal', 'enctype'=>'multipart/form-data'))?>
			<div class="form-group">
				<?= FORM::label('name', __('Name'), array('class'=>'col-xs-3 control-label', 'for'=>'name'))?>
				<div class="col-sm-4">
					<?= FORM::input('name', $user->name, array('class'=>'form-control', 'id'=>'name', 'required', 'placeholder'=>__('Name')))?>
				</div>
			</div>
			<div class="form-group">
				<?= FORM::label('email', __('Email'), array('class'=>'col-xs-3 control-label', 'for'=>'email'))?>
				<div class="col-sm-4">
					<?= FORM::input('email', $user->email, array('class'=>'form-control', 'id'=>'email', 'type'=>'email' ,'required','placeholder'=>__('Email')))?>
				</div>
			</div>
            <div class="form-group">
                <?= FORM::label('description', __('Description'), array('class'=>'col-xs-3 control-label', 'for'=>'description'))?>
                <div class="col-sm-4">
                    <?= FORM::input('description', $user->description, array('class'=>'form-control', 'id'=>'description', 'type'=>'description' ,'placeholder'=>__('Description')))?>
                </div>
            </div>
            <div class="col-md-offset-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="subscriber" value="1" <?=($user->subscriber)?'checked':NULL?> > <?=__('Subscribed to emails')?>
                </label>
            </div>
            </div>
			<button type="submit" class="btn btn-primary"><?=__('Update')?></button>
		<?= FORM::close()?>
	</div>
	<!--/span-->
	
	<div class="col-md-10">
    	<div class="page-header">
    		<h1><?=__('Change password')?></h1>
    	</div>
    	
    	<form class="well form-horizontal"  method="post" action="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'changepass'))?>">         
              <?=Form::errors()?>  
              
            <div class="form-group">
                <label class="col-xs-3 control-label"><?=__('New password')?></label>
                <div class="col-sm-4">
                <input class="form-control" type="password" name="password1" placeholder="<?=__('Password')?>">
                </div>
            </div>
              
            <div class="form-group">
                <label class="col-xs-3 control-label"><?=__('Repeat password')?></label>
                <div class="col-sm-4">
                <input class="form-control" type="password" name="password2" placeholder="<?=__('Password')?>">
                    <p class="help-block">
                  		<?=__('Type your password twice to change')?>
                    </p>
                </div>
            </div>
              
              
                <button type="submit" class="btn btn-primary"><?=__('Update')?></button>
              
    	</form>
    </div><!--end col-md-10-->

    <div class="col-md-10">
      <div class="page-header">
        <h1><?=__('Profile picture')?></h1>
      </div>
      
      <div class="row">
          <div class="col-md-3">
              <a class="thumbnail">
                  <img src="<?=$user->get_profile_image()?>" class="img-rounded" alt="<?=__('Profile Picture')?>" height='200px'>
              </a>
          </div>
      </div>
      <form class="well form-horizontal" enctype="multipart/form-data" method="post" action="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'image'))?>">         
              <?=Form::errors()?>  
            
            <div class="form-group">
              <?= FORM::label('profile_img', __('Profile picture'), array('class'=>'col-xs-3 ', 'for'=>'profile_img'))?>
              <div class="col-sm-4">
              <input type="file" name="profile_image" id="profile_img" />
              </div>  
            </div>
            
                <button type="submit" class="btn btn-primary"><?=__('Update')?></button> 
      </form>
    </div><!--end col-md-10-->
</div>
<!--/row-->