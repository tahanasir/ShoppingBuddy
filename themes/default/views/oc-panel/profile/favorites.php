<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
	<h1><?=__('My Favorites')?></h1>		
</div>

<div class="table-responsive">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
                <th><?=__('Advertisement') ?></th>
	            <th><?=__('Favorited') ?></th>
                <th></th>
			</tr>
		</thead>
		<tbody>
			<?foreach($favorites as $favorite):?>
				<tr id="tr<?=$favorite->id_favorite?>">
                    <td><a target="_blank" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$favorite->ad->category->seoname,'seotitle'=>$favorite->ad->seotitle))?>"><?= wordwrap($favorite->ad->title, 15, "<br />\n"); ?></a></td>
	                <td><?= Date::format($favorite->created, core::config('general.date_format'))?></td>
                    <td>
                        <a  data-text="<?=__('Are you sure you want to delete?')?>" 
                        data-id="tr<?=$favorite->id_favorite?>" class="btn btn-danger index-delete index-delete-inline" title="<?=__('Delete')?>" href="<?=Route::url('oc-panel', array('controller'=>'profile', 'action'=>'favorites','id'=>$favorite->id_ad))?>">
                        <i class="glyphicon   glyphicon-heart"></i>
                        </a>
                    </td>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>