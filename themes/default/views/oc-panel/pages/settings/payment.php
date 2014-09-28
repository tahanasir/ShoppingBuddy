<?php defined('SYSPATH') or die('No direct script access.');?>

	
		 <?=Form::errors()?>
		<div class="page-header">
			<h1><?=__('Payments Configuration')?></h1>
            <p class=""><?=__('List of payment configuration values. Replace input fields with new desired values.')?> <a target='_blank' href='http://open-classifieds.com/2014/08/21/setup-payment-gateways/'><?=__('Read more')?></a></p>
            <?if (Theme::get('premium')!=1):?>
                <p class="well"><span class="label label-info"><?=__('Heads Up!')?></span> 
                    Authorize, Stripe, Paymill and Bitpay <?=__('only available with premium themes!').'<br/>'.__('Upgrade your Open Classifieds site to activate this feature.')?>
                    <a class="btn btn-success pull-right" href="<?=Route::url('oc-panel',array('controller'=>'theme'))?>"><?=__('Browse Themes')?></a>
                </p>
            <?endif?>
		</div>


		<div class="well">
		<?= FORM::open(Route::url('oc-panel',array('controller'=>'settings', 'action'=>'payment')), array('class'=>'form-horizontal', 'enctype'=>'multipart/form-data'))?>
			<fieldset>
				<?foreach ($config as $c):?>
					<?$forms[$c->config_key] = array('key'=>$c->config_key, 'value'=>$c->config_value)?>
				<?endforeach?>

				<div class="form-group">
					<?= FORM::label($forms['paypal_currency']['key'], __('Site currency'), array('class'=>'control-label col-sm-3', 'for'=>$forms['paypal_currency']['key']))?>
					<div class="col-sm-4">
						<?= FORM::select($forms['paypal_currency']['key'], $paypal_currency , array_search($forms['paypal_currency']['value'], $paypal_currency), array(
						'placeholder' => "USD", 
						'class' => 'tips form-control', 
						'id' => $forms['paypal_currency']['key'], 
						'data-original-title'=> __("Currency"),
						'data-trigger'=>"hover",
						'data-placement'=>"right",
						'data-toggle'=>"popover",
						'data-content'=>__("Please be sure you are using a currency that your payment gateway supports."),
						))?> 
					</div>
				</div>
				<div class="form-group">
                    <?= FORM::label($forms['to_featured']['key'], __('Featured Ads'), array('class'=>'control-label col-sm-3', 'for'=>$forms['to_featured']['key']))?>
                    <div class="col-sm-4">
                        <div class="onoffswitch">
                            <?= FORM::hidden($forms['to_featured']['key'], 0);?>
                            <?= Form::checkbox($forms['to_featured']['key'], 1, (bool) $forms['to_featured']['value'], array(
                            'placeholder' => "", 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['to_featured']['key'],
                            'data-original-title'=> __("Featured ads"),
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-content'=>__("Featured ads will be highlighted for a defined number of days."), 
                            ))?>
                            <?= FORM::label($forms['to_featured']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['to_featured']['key']))?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?= FORM::label($forms['pay_to_go_on_feature']['key'], __('Price for featuring the Ad'), array('class'=>'control-label col-sm-3', 'for'=>$forms['pay_to_go_on_feature']['key']))?>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <?= FORM::hidden($forms['to_top']['key'], 0);?>
                            <?= FORM::input($forms['pay_to_go_on_feature']['key'], $forms['pay_to_go_on_feature']['value'], array(
                            'placeholder' => "", 
                            'class' => 'tips form-control col-sm-3', 
                            'id' => $forms['pay_to_go_on_feature']['key'],
                            'data-original-title'=> __("Pricing"),
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-content'=>__("How much the user needs to pay to feature an Ad"),  
                            ));?> 
                        
                        <span class="input-group-addon"><?=core::config('payment.paypal_currency')?></span></div>
                    </div>
                </div>
                <div class="form-group">
                <?= FORM::label($forms['featured_days']['key'], __('Days Featured'), array('class'=>'control-label col-sm-3', 'for'=>$forms['featured_days']['key']))?>
                <div class="col-sm-4">
                    <div class="input-group">
                        <?= FORM::input($forms['featured_days']['key'], $forms['featured_days']['value'], array(
                        'placeholder' => $forms['featured_days']['value'], 
                        'class' => 'tips form-control col-sm-3', 
                        'id' => $forms['featured_days']['key'], 
                        'data-original-title'=> __("Featured length"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-content'=>__("How many days an ad will be featured after paying."),
                        ));?>
                        <span class="input-group-addon"><?=__("Days")?></span>
                    </div> 
                </div>
            </div>
				<div class="form-group">
                    <?= FORM::label($forms['to_top']['key'], __('Bring to top Ad'), array('class'=>'control-label col-sm-3', 'for'=>$forms['to_top']['key']))?>
                    <div class="col-sm-4">
                        <div class="onoffswitch">
                            <?= FORM::checkbox($forms['to_top']['key'], 1, (bool) $forms['to_top']['value'], array(
                            'placeholder' => __("TRUE or FALSE"), 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['to_top']['key'],
                            'data-content'=> '',
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-original-title'=>'', 
                            ))?>
                            <?= FORM::label($forms['to_top']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['to_top']['key']))?>
                        </div>
                    </div>
                </div>
				<div class="form-group">
					<?= FORM::label($forms['pay_to_go_on_top']['key'], __('To top price'), array('class'=>'control-label col-sm-3', 'for'=>$forms['pay_to_go_on_top']['key']))?>
					<div class="col-sm-4">
						<div class="input-group">
							<?= FORM::input($forms['pay_to_go_on_top']['key'], $forms['pay_to_go_on_top']['value'], array(
							'placeholder' => "", 
							'class' => 'tips form-control col-sm-3', 
							'id' => $forms['pay_to_go_on_top']['key'],
							'data-original-title'=> __("Pricing"),
							'data-trigger'=>"hover",
							'data-placement'=>"right",
							'data-toggle'=>"popover",
							'data-content'=>__("How much the user needs to pay to top up an Ad"),  
							));?> 
								<span class="input-group-addon"><?=core::config('payment.paypal_currency')?></span>
						</div>
					</div>
				</div>
				

                <div class="form-group">
                    <?= FORM::label($forms['alternative']['key'], __('Alternative Payment'), array('class'=>'col-md-3 control-label', 'for'=>$forms['alternative']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::select($forms['alternative']['key'], $pages, $forms['alternative']['value'], array( 
                        'class' => 'tips form-control', 
                        'id' => $forms['alternative']['key'], 
                        'data-content'=> __("A button with the page title appears next to other pay button, onclick model opens with description."),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>__("Alternative Payment"),
                        ))?> 
                    </div>
                </div>

                <hr>
                <h2>Paypal</h2>
                <div class="form-group">
                    <?= FORM::label($forms['paypal_account']['key'], __('Paypal account'), array('class'=>'control-label col-sm-3', 'for'=>$forms['paypal_account']['key']))?>
                    <div class="col-sm-4">
                        <?= FORM::hidden($forms['paypal_account']['key'], 0);?>
                        <?= FORM::input($forms['paypal_account']['key'], $forms['paypal_account']['value'], array(
                        'placeholder' => "some@email.com", 
                        'class' => 'tips form-control', 
                        'id' => $forms['paypal_account']['key'],
                        'data-original-title'=> __("Paypal mail address"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-content'=>__("The paypal email address where the payments will be sent"), 
                        ))?> 
                        </div>
                </div>

                <div class="form-group">
                    <?= FORM::label($forms['sandbox']['key'], __('Sandbox'), array('class'=>'control-label col-sm-3', 'for'=>$forms['sandbox']['key']))?>
                    <div class="col-sm-4">
                        <div class="onoffswitch">
                            <?= FORM::hidden($forms['sandbox']['key'], 0);?>
                            <?= Form::checkbox($forms['sandbox']['key'], 1, (bool) $forms['sandbox']['value'], array(
                            'placeholder' => __("TRUE or FALSE"), 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['sandbox']['key'],
                            'data-content'=> '',
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-original-title'=>'', 
                            ))?>
                            <?= FORM::label($forms['sandbox']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['sandbox']['key']))?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <?= FORM::label($forms['paypal_seller']['key'], "<a target='_blank' href='http://open-classifieds.com/2013/09/02/pay-directly-from-ad/'>".__('User paypal link')."</a>", array('class'=>'control-label col-sm-3', 'for'=>$forms['paypal_seller']['key']))?>
                    <div class="col-sm-4">
                        <div class="onoffswitch">
                            <?= FORM::hidden($forms['paypal_seller']['key'], 0);?>
                            <?= Form::checkbox($forms['paypal_seller']['key'], 1, (bool) $forms['paypal_seller']['value'], array(
                            'placeholder' => __("TRUE or FALSE"), 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['paypal_seller']['key'],
                            'data-content'=> '',
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-original-title'=>'', 
                            ))?>
                            <?= FORM::label($forms['paypal_seller']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['paypal_seller']['key']))?>
                        </div>
                    </div>
                </div>  
                <div class="form-group">
                    <?= FORM::label($forms['stock']['key'], "<a target='_blank' href='http://open-classifieds.com/2013/09/02/pay-directly-from-ad/'>".__('Paypal link stock control')."</a>", array('class'=>'control-label col-sm-3', 'for'=>$forms['stock']['key']))?>
                    <div class="col-sm-4">
                        <div class="onoffswitch">
                            <?= FORM::hidden($forms['stock']['key'], 0);?>
                            <?= Form::checkbox($forms['stock']['key'], 1, (bool) $forms['stock']['value'], array(
                            'placeholder' => __("TRUE or FALSE"), 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['stock']['key'],
                            'data-content'=> '',
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-original-title'=>'', 
                            ))?>
                            <?= FORM::label($forms['stock']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['stock']['key']))?>
                        </div>
                    </div>
                </div>  

                <hr>
                <h2>Authorize.net</h2>
                <div class="form-group">
                    <label class="col-md-5 col-md-offset-3">
                        <p><?=sprintf(__('To get paid via Credit card you need a %s account'),'Authorize.net')?>. <?=__('You will need also a SSL certificate')?>, <a href="https://www.ssl.com/code/49" target="_blank"><?=__('buy your SSL certificate here')?></a>.</p>
                        <?=__('Register')?>
                        <a class="btn btn-success" target="_blank" href="http://reseller.authorize.net/application/signupnow/?id=AUAffiliate&rid=26776">
                            </i> US/Canada</a>
                        <a class="btn btn-success" target="_blank" href="http://reseller.authorize.net/application/?id=5561123">
                            UK/Europe</a>
                    </label>
                </div>
                <div class="form-group">
                    <?= FORM::label($forms['authorize_sandbox']['key'], __('Sandbox'), array('class'=>'control-label col-sm-3', 'for'=>$forms['authorize_sandbox']['key']))?>
                    <div class="col-sm-4">
                        <div class="onoffswitch">
                            <?= Form::checkbox($forms['authorize_sandbox']['key'], 1, (bool) $forms['authorize_sandbox']['value'], array(
                            'placeholder' => __("TRUE or FALSE"), 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['authorize_sandbox']['key'],
                            'data-content'=> '',
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-original-title'=>'', 
                            ))?>
                            <?= FORM::label($forms['authorize_sandbox']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['authorize_sandbox']['key']))?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    
                    <?= FORM::label($forms['authorize_login']['key'], __('Authorize API Login'), array('class'=>'col-md-3 control-label', 'for'=>$forms['authorize_login']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['authorize_login']['key'], $forms['authorize_login']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['authorize_login']['key'],
                        'data-content'=> __('Authorize API Login'),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>

                <div class="form-group">
                    <?= FORM::label($forms['authorize_key']['key'], __('Authorize transaction Key'), array('class'=>'col-md-3 control-label', 'for'=>$forms['authorize_key']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['authorize_key']['key'], $forms['authorize_key']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['authorize_key']['key'],
                        'data-content'=> __("Authorize transaction Key"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>

                <hr>
                <h2>Paymill</h2>

                <div class="form-group">
                    <label class="col-md-5 col-md-offset-3">
                        <p>To get paid via Credit card you need a Paymill account. It's free to register. They charge 2'95% of any sale.</p>
                        <a class="btn btn-success" target="_blank" href="https://app.paymill.com/en-en/auth/register?referrer=openclassifieds">
                            <i class="glyphicon glyphicon-pencil"></i> Register for free at Paymill</a>
                    </label>
                </div>
                <div class="form-group">
                    
                    <?= FORM::label($forms['paymill_private']['key'], __('Paymill private key'), array('class'=>'col-md-3 control-label', 'for'=>$forms['paymill_private']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['paymill_private']['key'], $forms['paymill_private']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['paymill_private']['key'],
                        'data-content'=> __("Paymill private key"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>

                <div class="form-group">
                    <?= FORM::label($forms['paymill_public']['key'], __('Paymill public key'), array('class'=>'col-md-3 control-label', 'for'=>$forms['paymill_public']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['paymill_public']['key'], $forms['paymill_public']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['paymill_public']['key'],
                        'data-content'=> __("Paymill public key"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>
                <hr>
                <h2>Stripe</h2>
                <div class="form-group">
                    <label class="col-md-5 col-md-offset-3">
                        <p>To get paid via Credit card you can also use a Stripe account. It's free to register. They charge 2'95% of any sale.</p>
                        <a class="btn btn-success" target="_blank" href="https://stripe.com">
                            <i class="glyphicon glyphicon-pencil"></i> Register for free at Stripe</a>
                    </label>
                </div>
                <div class="form-group">
                    
                    <?= FORM::label($forms['stripe_private']['key'], __('Stripe private key'), array('class'=>'col-md-3 control-label', 'for'=>$forms['stripe_private']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['stripe_private']['key'], $forms['stripe_private']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['stripe_private']['key'],
                        'data-content'=> __("Stripe private key"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>

                <div class="form-group">
                    <?= FORM::label($forms['stripe_public']['key'], __('Stripe public key'), array('class'=>'col-md-3 control-label', 'for'=>$forms['stripe_public']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['stripe_public']['key'], $forms['stripe_public']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['stripe_public']['key'],
                        'data-content'=> __("Stripe public key"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>

                <div class="form-group">
                    <?= FORM::label($forms['stripe_address']['key'], __('Requires address to pay for extra security'), array('class'=>'col-md-3 control-label', 'for'=>$forms['stripe_address']['key']))?>
                    <div class="col-md-5">
                        <div class="onoffswitch">
                            <?= Form::checkbox($forms['stripe_address']['key'], 1, (bool) $forms['stripe_address']['value'], array(
                            'placeholder' => __("TRUE or FALSE"), 
                            'class' => 'onoffswitch-checkbox', 
                            'id' => $forms['stripe_address']['key'], 
                            'data-content'=> '',
                            'data-trigger'=>"hover",
                            'data-placement'=>"right",
                            'data-toggle'=>"popover",
                            'data-original-title'=>'',                     
                            ))?>
                            <?= FORM::label($forms['stripe_address']['key'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['stripe_address']['key']))?>
                        </div>
                    </div>
                </div>
                <hr>
                <h2>Bitpay</h2>
                <div class="form-group">
                    <label class="col-md-5 col-md-offset-3">
                        <p>Accept bitcoins using Bitpay</p>
                        <a class="btn btn-success" target="_blank" href="https://bitpay.com">
                            <i class="glyphicon glyphicon-pencil"></i> Register for free at Bitpay</a>
                    </label>
                </div>
                <div class="form-group">
                    
                    <?= FORM::label($forms['bitpay_apikey']['key'], __('Bitpay api key'), array('class'=>'col-md-3 control-label', 'for'=>$forms['bitpay_apikey']['key']))?>
                    <div class="col-md-5">
                        <?= FORM::input($forms['bitpay_apikey']['key'], $forms['bitpay_apikey']['value'], array(
                        'placeholder' => "", 
                        'class' => 'tips form-control', 
                        'id' => $forms['bitpay_apikey']['key'],
                        'data-content'=> __("Bitpay api key"),
                        'data-trigger'=>"hover",
                        'data-placement'=>"right",
                        'data-toggle'=>"popover",
                        'data-original-title'=>'', 
                        ))?> 
                        </div>
                </div>

				
					<?= FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'payment'))))?>
				
			</fieldset>	
	</div><!--end col-md-10-->
