<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
	<h1><?= $post->title;?></h1>
</div>

<div class="well">
    <span class="label label-default"><?=$post->user->name?></span>
    <div class="pull-right">
        <span class="label label-info"><?=Date::format($post->created, core::config('general.date_format'))?></span>
    </div>    
</div>

<br/>

<div>
    <?=$post->description?>
</div>  

<div class="pull-right">
    <?if($previous->loaded()):?>
        <a class="btn btn-success" href="<?=Route::url('blog',  array('seotitle'=>$previous->seotitle))?>" title="<?=HTML::chars($previous->title)?>">
        <i class="glyphicon glyphicon-backward glyphicon"></i> <?=$previous->title?></i></a>
    <?endif?>
    <?if($next->loaded()):?>
        <a class="btn btn-success" href="<?=Route::url('blog',  array('seotitle'=>$next->seotitle))?>" title="<?=HTML::chars($next->title)?>">
        <?=$next->title?> <i class="glyphicon glyphicon-forward glyphicon"></i></a>
    <?endif?>
</div>  
    
<?=$post->disqus()?>