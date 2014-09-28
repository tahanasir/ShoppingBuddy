<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
	<h1><?=__('Sitemap')?></h1>
    <a target='_blank' href='http://open-classifieds.com/2014/08/18/sitemap-classifieds-website/'><?=__('Read more')?></a>
</div>

<p><?=__('Last time generated')?> <?=Date::unix2mysql(Sitemap::last_generated_time())?> <a class="btn btn-primary ajax-load" title="<?=__('Sitemap')?>" href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'sitemap'))?>?force=1">
  <?=__('Generate')?></a><br>



 <?=__('Your sitemap XML to submit to engines')?>
<input type="text" value="<?=core::config('general.base_url')?><?=(file_exists(DOCROOT.'sitemap-index.xml'))? 'sitemap-index.xml':'sitemap.xml.gz'?>" />


</p>