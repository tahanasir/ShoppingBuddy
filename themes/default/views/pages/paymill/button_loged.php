<?php defined('SYSPATH') or die('No direct script access.');?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<form action="<?=Route::url('default',array('controller'=>'paymill','action'=>'pay','id'=>$order->id_order))?>" method="post">
    <script
        src="https://button.paymill.com/v1/"
        id="button"
        data-label="<?=__('Pay with Card')?>"
        data-title="<?=Model_Order::product_desc($order->id_product)?>"
        data-description="<?=Text::limit_chars(Text::removebbcode($order->description),30,NULL, TRUE)?>"
        data-amount="<?=Paymill::money_format($order->amount)?>"
        data-currency="<?=$order->currency?>"
        data-submit-button="<?=__('Pay')?> <?=i18n::format_currency($order->amount,$order->currency)?>"
        data-elv="false"
        data-lang="en-GB"
        data-public-key="<?=Core::config('payment.paymill_public')?>"
        >
    </script>
</form>