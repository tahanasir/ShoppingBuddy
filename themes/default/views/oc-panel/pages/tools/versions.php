<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
    <h1><?=__('Versions')?></h1>
    <p><?=__('Open Classifieds release history information.')?> 
        <?=__('Your installation version is')?> <span class="label label-info"><?=core::VERSION?></span>
    </p>
    <p><?=__('Your Hash Key for this installation is')?> 
         <span class="label label-info"><?=core::config('auth.hash_key')?></span>
    </p>
        <a class="btn btn-primary pull-right ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'update','action'=>'index'))?>?reload=1">
            <?=__('Check for updates')?></a>

</div>

<table class="table table-striped">
<tr>
    <th><?=__('Version')?></th>
    <th><?=__('Name')?></th>
    <th><?=__('Date')?></th>
</tr>
<?foreach ($versions as $version=>$values):?> 
<tr>
    <td>
        <?=$version?>
        <?=($version==$latest_version)? '<span class="label label-success">'.__('Latest').'</span>':''?>
        <?=($version==core::VERSION)? '<span class="label label-info">'.__('Current').'</span>':''?>
    </td>
    <td>
        <?=$values['codename']?>    
    </td>
    <td>
        <a target="_blank" href="<?=$values['blog']?>"><?=$values['released']?></a>
    </td>
</tr>
<?endforeach?>
</table>
