<?php defined('SYSPATH') or die('No direct script access.');?>

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
	            <th><?=__('Ad') ?></th>
	            <th><?=__('Date') ?></th>
                <th><?=__('Date Paid') ?></th>
			</tr>
		</thead>
		<tbody>
			<?foreach($orders as $order):?>
				<tr id="tr<?=$order->pk()?>">
					
	                <td><?=$order->pk()?></td>

                    <td>
                        <?if ($order->status == Model_Order::STATUS_CREATED):?>
                        <a class="btn btn-warning" href="<?=Route::url('default', array('controller'=> 'ad','action'=>'checkout' , 'id' => $order->id_order))?>">
                        <i class="glyphicon glyphicon-shopping-cart"></i> <?=__('Pay')?>   
                        </a>
                        <?else:?>
                            <?=Model_Order::$statuses[$order->status]?>
                        <?endif?>
                    </td>

                    <td><?=Model_Order::product_desc($order->id_product)?></td>
                    
                    <td><?=i18n::format_currency($order->amount, $order->currency)?></td>

	                <td><a href="<?=Route::url('oc-panel', array('controller'=> 'profile', 'action'=>'update','id'=>$order->ad->pk())) ?>" title="<?=HTML::chars($order->ad->title)?>">
	                    <?=Text::limit_chars($order->ad->title, 30, NULL, TRUE)?></a></td>
						                
	                <td><?=$order->created?></td>

                    <td><?=$order->pay_date?></td>

				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>
<?=$pagination?>