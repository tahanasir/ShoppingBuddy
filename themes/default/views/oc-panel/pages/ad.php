<?php defined('SYSPATH') or die('No direct script access.');?>

<?=Alert::show()?>
<div class="page-header">
	<?if(Core::get('status') == Model_Ad::STATUS_UNAVAILABLE):?>
		<? $current_url = Model_Ad::STATUS_UNAVAILABLE?>
		<h1><?=__('Unavailable')?></h1>
	<?elseif (Core::get('status') == Model_Ad::STATUS_UNCONFIRMED):?>
		<? $current_url = Model_Ad::STATUS_UNCONFIRMED?>
		<h1><?=__('Unconfirmed')?></h1>
	<?elseif (Core::get('status') == Model_Ad::STATUS_SPAM):?>
		<? $current_url = Model_Ad::STATUS_SPAM?>
		<h1><?=__('Spam')?></h1>
	<?else:?>
		<? $current_url = Model_Ad::STATUS_PUBLISHED?>
		<h1><?=__('Advertisements')?></h1>
		<a target='_blank' href='http://open-classifieds.com/2013/08/29/how-to-manage-advertisements/'><?=__('Read more')?></a>
		<form id="advert_search" method="GET" class="pull-right" enctype="multipart/form-data">
		  	<input type="text" name="search" id="search" class="form-control" placeholder="<?=__('Search')?>">
		</form>
	<?endif?>
</div>		


<div class="btn-group">
    <a href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index'))?>" class="btn btn-success">
        <i class=" glyphicon  glyphicon-globe glyphicon"></i> <?=__('All ads')?>
    </a>
    <a class="btn btn-warning ajax-load" type="submit" value="spam" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_SPAM?>" rel="tooltip" title="<?=__('Spam Sort')?>">
    <i class="glyphicon   glyphicon-fire"></i> <?=__('Spam')?>
    </a>
    <a class="btn btn-default ajax-load" type="submit" value="unavailable" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_UNAVAILABLE?>" rel="tooltip" title="<?=__('Unavailable Sort')?>">
        <i class=" glyphicon  glyphicon-exclamation-sign glyphicon"></i> <?=__(' Unavailable')?>
    </a>
    <a class="btn btn-info ajax-load" type="submit" value="unconfirmed" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_UNCONFIRMED?>" rel="tooltip" title="<?=__('Unconfirmed Sort')?>">
        <i class=" glyphicon  glyphicon-plane glyphicon"></i> <?=__(' Unconfirmed')?>
    </a>

    <?if(Core::get('status') == Model_Ad::STATUS_UNAVAILABLE OR Core::get('status') == Model_Ad::STATUS_UNCONFIRMED OR Core::get('status') == Model_Ad::STATUS_SPAM):?>
    <a class="btn btn-danger pull-right ajax-load" type="submit" value="unavailable" onclick="return confirm('<?=__('Delete All?')?>');" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete_all')).'?status='.Core::get('status')?>" rel="tooltip" title="<?=__('Delete All')?>">
        <i class=" glyphicon  glyphicon-minus glyphicon"></i> <?=__('Delete All')?>
    </a>
    <?endif?>
</div>


<table class="table table-bordered">
	<tr>
		<th>
			<input type="checkbox" id="select-all" onclick="check_all();">
		</th>
        <th>#</th>
		<th><?=__('Name')?></th>
		<th><?=__('Category')?></th>
		<th><?=__('Location')?></th>
        <?if(core::config('advertisement.count_visits')==1):?>
		<th><?=__('Hits')?></th>
        <?endif?>
		<th><?=__('Status')?></th>
		<th><?=__('Date')?></th>
		<!-- in case there are no ads we dont show buttons -->
		<?if(isset($res)):?>
		<th>
			<div class="toolbar btn btn-default"><i class="glyphicon glyphicon-cog"></i>
			<div id="user-toolbar-options-all" class="hide user-toolbar-options">
				<?if(Core::get('status') != Model_Ad::STATUS_SPAM):?>
				<a class="spam btn btn-warning  " 
					href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam'))?>" 
					onclick="return confirm('<?=__('Spam?')?>');"
					rel="tooltip" title="<?=__('Spam')?>">
					<i class="glyphicon   glyphicon-fire"></i>
				</a>
				<?endif?>
				<?if(Core::get('status') != Model_Ad::STATUS_UNAVAILABLE):?>
				<a class="deactivate btn btn-warning " 
					href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate'))?>" 
					onclick="return confirm('<?=__('Deactivate?')?>');"
					rel="tooltip" title="<?=__('Deactivate')?>">
					<i class="glyphicon   glyphicon-remove"></i>
				</a>
				<?endif?>
				<?if($current_url != Model_Ad::STATUS_PUBLISHED):?>
				<a class="activate btn btn-success " 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'activate'))?>" 
						onclick="return confirm('<?=__('Activate?')?>');"
						rel="tooltip" title="<?=__('Activate')?>">
						<i class="glyphicon   glyphicon-ok-sign"></i>
				</a>
				<?endif?>
				<a class="delete btn btn-danger  " 
					href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete'))?>"
					onclick="return confirm('<?=__('Delete?')?>');"
				    rel="tooltip" title="<?=__('Delete')?>" data-id="tr1" data-text="<?=__('Are you sure you want to delete?')?>">
					<i class="glyphicon   glyphicon-remove"></i>
				</a>
				<?if($current_url == Model_Ad::STATUS_PUBLISHED):?>
				<a class="featured btn btn-primary " 
					href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'featured'))?>"
					onclick="return confirm('<?=__('Are you sure you want to make it featured?')?>');"
				    rel="tooltip" title="<?=__('Featured')?>" data-id="tr1" data-text="<?=__('Are you sure you want to make it featured?')?>">
					<i class="glyphicon   glyphicon-bookmark"></i>
				</a>
				<a class="to_top btn btn-info" 
					href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'to_top'))?>"
					onclick="return confirm('<?=__('Are you sure you want to make it to top?')?>');"
				    rel="tooltip" title="<?=__('To top')?>" data-id="tr1" data-text="<?=__('Are you sure you want to make it to top?')?>">
					<i class="glyphicon   glyphicon-circle-arrow-up"></i>
				</a>
				<?endif?>
			</div>
			</div>
		</th>
		<?endif?>
	</tr>
<?if(isset($res)):?>
	<? $i = 0; foreach($res as $ad):?>
	<tbody>
		<tr>
			<td>
				<input type="checkbox" id="<?= $ad->id_ad.'_'?>" class="checkbox">
			</td>

            <td><?=$ad->id_ad?>
			
			<td><a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->name,'seotitle'=>$ad->seotitle))?>"><?= wordwrap($ad->title, 15, "<br />\n"); ?></a>
            </td>

            <td><?= wordwrap($ad->category->name, 15, "<br />\n"); ?>
                 
            <td>
                <?if($ad->location->loaded()):?>
                    <?=wordwrap($ad->location->name, 15, "<br />\n");?>
                <?else:?>
                    n/a
                <?endif?>
            </td>

            <?if(core::config('advertisement.count_visits')==1):?>
            <td><?=$ad->visits->count_all();?></td>
			<?endif?>

			<td>
            <?if($ad->status == Model_Ad::STATUS_NOPUBLISHED):?>
                <?=__('Not published')?>
            <? elseif($ad->status == Model_Ad::STATUS_PUBLISHED):?>
                <?=__('Published')?>
            <? elseif($ad->status == Model_Ad::STATUS_SPAM):?>
                <?=__('Spam')?>
            <? elseif($ad->status == Model_Ad::STATUS_UNAVAILABLE):?>
                <?=__('Unavailable')?>
            <?endif?>

            <?if( ($order = $ad->get_order())!==FALSE ):?>
                <a class="label <?=($order->status==Model_Order::STATUS_PAID)?'label-success':'label-warning'?> " 
                    href="<?=Route::url('oc-panel', array('controller'=> 'order','action'=>'update','id' => $order->id_order))?>">
                <?if ($order->status==Model_Order::STATUS_CREATED):?>
                    <?=__('Not paid')?>
                <?elseif ($order->status==Model_Order::STATUS_PAID):?>
                    <?=__('Paid')?>
                <?endif?>
                    <?=i18n::format_currency($order->amount,$order->currency)?>
                </a>
            <?endif?>
	    	
	    	<td><?= Date::format($ad->published, core::config('general.date_format'))?></td>
			<td width="120" style="width:120px">
				<a class="btn btn-primary ajax-load" 
					href="<?=Route::url('oc-panel', array('controller'=>'profile','action'=>'update','id'=>$ad->id_ad))?>" 
					rel="tooltip" title="<?=__('Update')?>">
					<i class="glyphicon   glyphicon-edit"></i>
				</a>
				<div class="toolbar btn btn-default"><i class="glyphicon glyphicon-cog"></i>
				<div id="user-toolbar-options<?=$ad->id_ad?>" class="hide user-toolbar-options">
					
					<?if($ad->status != Model_Ad::STATUS_SPAM):?>
					
					<a class="btn btn-warning " 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Spam?')?>');"
						rel="tooltip" title="<?=__('Spam')?>">
						<i class="glyphicon   glyphicon-fire"></i>
					</a>
					<?endif?>
					<?if($ad->status != Model_Ad::STATUS_UNAVAILABLE):?>
					<a class="btn btn-warning " 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Deactivate?')?>');"
						rel="tooltip" title="<?=__('Deactivate')?>">
						<i class="glyphicon   glyphicon-remove"></i>
					</a>
					<?endif?>
					<?if( $ad->status != Model_Ad::STATUS_PUBLISHED ):?>
					<a class="btn btn-success " 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'activate','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Activate?')?>');"
						rel="tooltip" title="<?=__('Activate')?>">
						<i class="glyphicon   glyphicon-ok-sign"></i>
					</a>
					<?endif?>
					<!-- sel_url_to_redirect is important because is quick selector. This works with dynamic check boxes, where we select href to build new url -->
					<a class="btn btn-danger  sel_url_to_redirect" 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Delete?')?>');"
					    rel="tooltip" title="<?=__('Delete')?>" data-id="tr1" data-text="<?=__('Are you sure you want to delete?')?>">
						<i class="glyphicon   glyphicon-remove"></i>
					</a>
					<?if($current_url == Model_Ad::STATUS_PUBLISHED):?>
					<?if($ad->featured == NULL):?>
					<a class="btn btn-primary " 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'featured','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Make featured?')?>');"
					    rel="tooltip" title="<?=__('Featured')?>" data-id="tr1" data-text="<?=__('Are you sure you want to make it featured?')?>">
						<i class="glyphicon   glyphicon-bookmark"></i>
					</a>
					<?else:?>
					<a class="btn btn-default " 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'featured','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Deactivate featured?')?>');"
					    rel="tooltip" title="<?=__('Deactivate Featured')?>" data-id="tr1" data-text="<?=__('Are you sure you want to deactivate featured advertisement?')?>">
						<i class="glyphicon   glyphicon-bookmark"></i>
					</a>
					<?endif?>
					<a class="btn btn-info" 
						href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'to_top','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>" 
						onclick="return confirm('<?=__('Refresh listing, go to top?')?>');"
					    rel="tooltip" title="<?=__('Go to top')?>" data-id="tr1" data-text="<?=__('Are you sure you want to refresh listing and go to top?')?>">
						<i class="glyphicon   glyphicon-circle-arrow-up"></i>
					</a>
					<?endif?>
				</div>
				</div>
			</td>
		</tr>
	<?endforeach?>
	<?endif?>
	</tbody>
</table>
<?if(isset($pagination)):?>
<?=$pagination?>
<?endif?>

