<?php defined('SYSPATH') or die('No direct script access.');?>

<form class="form-inline" method="get" action="<?=URL::current();?>">
  	<div class="form-group pull-right">
  		<div class="">
	      	<input type="text" class="form-control search-query" name="email" placeholder="<?=__('email')?>" value="<?=core::get('email')?>">
		</div>
	</div>
</form>

<div class="page-header">
    
	<h1><?=__('Orders')?></h1>		

</div>
<div class="table-responsive">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
	            <th>#</th>
                <th><?=__('Status') ?></th>
                <th><?=__('Product') ?></th>
                <th><?=__('Amount') ?></th>
	            <th><?=__('User') ?></th>
	            <th><?=__('Ad') ?></th>
	            <th><?=__('Date') ?></th>
                <th><?=__('Date Paid') ?></th>
				<th><?=__('Actions') ?></th>
			</tr>
		</thead>
		<tbody>
			<?foreach($orders as $order):?>
				<tr id="tr<?=$order->pk()?>">
					
	                <td><?=$order->pk()?></td>

                    <td><?=Model_Order::$statuses[$order->status]?></td>

                    <td><?=Model_Order::product_desc($order->id_product)?></td>
                    
                    <td><?=i18n::format_currency($order->amount, $order->currency)?></td>

	                <td><a href="<?=Route::url('oc-panel', array('controller'=> 'user', 'action'=>'update','id'=>$order->user->pk())) ?>">
	                    <?=$order->user->name?></a> - <?=$order->user->email?>
	                </td>
	                <td><a href="<?=Route::url('oc-panel', array('controller'=> 'profile', 'action'=>'update','id'=>$order->ad->pk())) ?>" title="<?=HTML::chars($order->ad->title)?>">
	                    <?=Text::limit_chars($order->ad->title, 30, NULL, TRUE)?></a></td>
						                
	                <td><?=$order->created?></td>

                    <td><?=$order->pay_date?></td>

					<td width="80px">
						<?if ($controller->allowed_crud_action('update')):?>
						<a title="<?=__('Edit')?>" class="btn btn-primary ajax-load" href="<?=Route::url('oc-panel', array('controller'=> Request::current()->controller(), 'action'=>'update','id'=>$order->pk()))?>">
							<i class="glyphicon glyphicon-edit"></i>
						</a>
						<?endif?>
					</td>

				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>
<?=$pagination?>