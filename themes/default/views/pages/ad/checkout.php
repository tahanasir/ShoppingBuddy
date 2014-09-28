<?php defined('SYSPATH') or die('No direct script access.');?>


<div class="well col-xs-12 col-sm-12 col-md-12">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <address>
                <strong><?=Core::config('general.site_name')?></strong>
                <br>
                <?=Core::config('general.base_url')?>
            </address>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
            <p>
                <em><?=__('Date')?>: <?= Date::format($order->created, core::config('general.date_format'))?></em>
                <br>
                <em><?=__('Checkout')?> #: <?=$order->id_order?></em>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="text-center">
            <h1><?=__('Checkout')?></h1>
        </div>
        </span>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">#</th>
                    <th><?=__('Product')?></th>
                    <th class="text-center"><?=__('Price')?></th>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td class="col-md-1" style="text-align: center"><?=$order->id_product?></td>
                    <td class="col-md-9"><?=$order->description?> <em>(<?=Model_Order::product_desc($order->id_product)?>)</em></td>
                    <td class="col-md-2 text-center"><?=i18n::format_currency($order->amount, $order->currency)?></td>
                </tr>
                <tr>
                    <td class="col-md-1" style="text-align: center"><?=$order->ad->id_ad?></td>
                    <td colspan=2 class="col-md-12">
                        <em><?=$order->ad->title?></em>
                    </td>
                </tr>
                <tr>
                    <td>   </td>
                    <td class="text-right"><h4><strong><?=__('Total')?>: </strong></h4></td>
                    <td class="text-center text-danger"><h4><strong><?=i18n::format_currency($order->amount, $order->currency)?></strong></h4></td>
                </tr>
            </tbody>
        </table>

        <?if (Core::config('payment.paypal_account')!=''):?>
        <a class="btn btn-success btn-lg pull-right" href="<?=Route::url('default', array('controller'=> 'paypal','action'=>'pay' , 'id' => $order->id_order))?>">
        <?=__('Pay with Paypal')?>   <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
        <div class="clearfix"></div>
        <?endif?>
        
        <?if ($order->id_product!==Model_Order::PRODUCT_AD_SELL):?>
        <div class="pull-right">
            <?=$order->alternative_pay_button()?>
        </div>
        <?endif?>

    </div>
</div>

 