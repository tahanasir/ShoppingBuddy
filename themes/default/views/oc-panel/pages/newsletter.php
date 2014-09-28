<?php defined('SYSPATH') or die('No direct script access.');?>

<?=View::factory('oc-panel/elasticemail')?>

  <?if (Theme::get('premium')!=1):?>
        <p class="well"><span class="label label-info"><?=__('Heads Up!')?></span> 
            <?=__('Only if you have a premium theme you will be able to filter by users!').'<br/>'.__('Upgrade your Open Classifieds site to activate this feature.')?>
            <a class="btn btn-success pull-right ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'theme'))?>"><?=__('Browse Themes')?></a>
        </p>
    <?endif?>

<div class="page-header">
    <h1><?=__('Newsletter')?></h1>
  <a href="http://open-classifieds.com/2013/08/23/how-to-send-the-newsletter/" target="_blank"><?=__('Read more')?></a>
    <a class="btn btn-primary pull-right ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'settings','action'=>'email'))?>?force=1">
  <?=__('Email Settings')?></a>
</div>

    <form class="well form-horizontal"  method="post" action="<?=Route::url('oc-panel',array('controller'=>'newsletter','action'=>'index'))?>">  

        <?=Form::errors()?> 

        <div class="form-group">
            <label class="col-md-2"><?=__('To')?>:</label>
            <div class="col-md-5">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="send_all" checked >
                        <?=__('All active users.')?> <span class="badge badge-info"><?=$count_all_users?></span>
                    </label>
                </div> 
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="send_featured"  >
                        <?=__('Users with featured ads.')?> <span class="badge badge-info"><?=$count_featured?></span>
                    </label>
                </div> 
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="send_featured_expired"  >
                        <?=__('Users with featured ads expired.')?> <span class="badge badge-info"><?=$count_featured_expired?></span>
                    </label>
                </div> 
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="send_unpub"  >
                        <?=__('Users without published ads.')?> <span class="badge badge-info"><?=$count_unpub?></span>
                    </label>
                </div> 
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="send_logged"  >
                        <?=__('Users not logged last 3 months')?> <span class="badge badge-info"><?=$count_logged?></span>
                    </label>
                </div> 
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="send_spam"  >
                        <?=__('Users marked a spam')?> <span class="badge badge-info"><?=$count_spam?></span>
                    </label>
                </div> 
            </div> 
        </div>
       

        <div class="form-group">
        <label class="col-md-2"><?=__('From')?>:</label>
        <div class="col-md-5">
        <input  type="text" name="from" value="<?=Auth::instance()->get_user()->name?>" class="col-md-6 form-control"  />
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-2"><?=__('From Email')?>:</label>
        <div class="col-md-5">
        <input  type="text" name="from_email" value="<?=Auth::instance()->get_user()->email?>" class="col-md-6 form-control"  />
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-2"><?=__('Subject')?>:</label>
        <div class="col-md-5">
        <input  type="text" name="subject" value="" class="col-md-6 form-control"  />
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-2"><?=__('Message')?>:</label>
        <div class="col-md-9">
        <textarea  name="description"  id="formorm_description" class="col-md-10 col-sm-10 col-xs-12 form-control" rows="15" ></textarea>
        </div>
      </div>
          
          
      <div class="form-actions">
        <a href="<?=Route::url('oc-panel')?>" class="btn btn-default"><?=__('Cancel')?></a>
        <button type="submit" class="btn btn-primary"><?=__('Send')?></button>
      </div>
    </form>    