<?php defined('SYSPATH') or die('No direct script access.');?>

<?=Form::errors()?>

<div class="page-header">
    <h1><?=__('Image Configuration')?></h1>
    <p><?=__('Image configuration values. Replace input fields with new desired values')?></p>
</div>

<div class="well">
<?= FORM::open(Route::url('oc-panel',array('controller'=>'settings', 'action'=>'image')), array('class'=>'form-horizontal', 'enctype'=>'multipart/form-data'))?>
    <fieldset>
        <?foreach ($config as $c):?>
            <? $forms[$c->config_key] = array('key'=>'image['.$c->config_key.'][]', 'id'=>$c->config_key, 'value'=>$c->config_value)?>
        <?endforeach?>

        <div class="form-group">
            <?= FORM::label($forms['allowed_formats']['id'], __('Allowed image formats'), array('class'=>'control-label col-sm-3', 'for'=>$forms['allowed_formats']['id']))?>
            <div class="col-sm-4">
                <?= FORM::select($forms['allowed_formats']['key'], array('jpeg'=>'jpeg','jpg'=>'jpg','png'=>'png','webp'=>'webp','gif'=>'gif','raw'=>'raw'), explode(',', $forms['allowed_formats']['value']), array(
                'placeholder' => $forms['allowed_formats']['value'],
                'multiple' => 'true',
                'class' => 'tips form-control input-sm', 
                'id' => $forms['allowed_formats']['id'],
                'data-content'=> __("Set this up to restrict image formats that are being uploaded to your server."),
                'data-trigger'=>"hover",
                'data-placement'=>"right",
                'data-toggle'=>"popover",
                'data-original-title'=>__("Allowed image formats"), 
                ))?> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['max_image_size']['id'], __('Max image size'), array('class'=>'control-label col-sm-3', 'for'=>$forms['max_image_size']['id']))?>
            <div class="col-sm-4">
                <div class="input-group">
                    <?= FORM::input($forms['max_image_size']['key'], $forms['max_image_size']['value'], array(
                    'placeholder' => "5", 
                    'class' => 'tips form-control input-sm span', 
                    'id' => $forms['max_image_size']['id'],
                    'data-content'=> __("Control the size of images being uploaded. Enter an integer value to set maximum image size in mega bites(Mb)."),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Image size in mega bites(Mb)"), 
                    ))?>
                    <span class="input-group-addon">MB</span>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['height']['id'], __('Image height'), array('class'=>'control-label col-sm-3', 'for'=>$forms['height']['id']))?>
            <div class="col-sm-4">
                <div class="input-group">
                    <?= FORM::input($forms['height']['key'], $forms['height']['value'], array(
                    'placeholder' => "700", 
                    'class' => 'tips form-control input-sm', 
                    'id' => $forms['height']['id'], 
                    'data-content'=> __("Each image is resized when uploaded. This is the height of big image. Note: you can leave this field blank to set AUTO height resize."),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Image height in pixels(px)"),
                    ))?>
                    <span class="input-group-addon">px</span>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['width']['id'], __('Image width'), array('class'=>'control-label col-sm-3', 'for'=>$forms['width']['id']))?>
            <div class="col-sm-4">
                <div class="input-group">
                    <?= FORM::input($forms['width']['key'], $forms['width']['value'], array(
                    'placeholder' => "1024", 
                    'class' => 'tips form-control input-sm', 
                    'id' => $forms['width']['id'],
                    'data-content'=> __("Each image is resized when uploaded. This is the width of big image."),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Image width in pixels(px)"), 
                    ))?>
                    <span class="input-group-addon">px</span>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['height_thumb']['id'], __('Thumb height'), array('class'=>'control-label col-sm-3', 'for'=>$forms['height_thumb']['id']))?>
            <div class="col-sm-4">
                <div class="input-group">
                    <?= FORM::input($forms['height_thumb']['key'], $forms['height_thumb']['value'], array(
                    'placeholder' => "200", 
                    'class' => 'tips form-control input-sm', 
                    'id' => $forms['height_thumb']['id'],
                    'data-content'=> __("Thumb is a small image resized to fit certain elements. This is the height for this image."),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Thumb height in pixels(px)"), 
                    ))?>
                    <span class="input-group-addon">px</span>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['width_thumb']['id'], __('Thumb width'), array('class'=>'control-label col-sm-3', 'for'=>$forms['width_thumb']['id']))?>
            <div class="col-sm-4">
                <div class="input-group">
                    <?= FORM::input($forms['width_thumb']['key'], $forms['width_thumb']['value'], array(
                    'placeholder' => "200", 
                    'class' => 'tips form-control input-sm', 
                    'id' => $forms['width_thumb']['id'],
                    'data-content'=> __("Thumb is a small image resized to fit certain elements. This is width of this image."),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Thumb width in pixels(px)"),

                    ))?>
                    <span class="input-group-addon">px</span>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['quality']['id'], __('Image quality'), array('class'=>'control-label col-sm-3', 'for'=>$forms['quality']['id']))?>
            <div class="col-sm-4">
                <div class="input-group">
                    <?= FORM::input($forms['quality']['key'], $forms['quality']['value'], array(
                    'placeholder' => "95", 
                    'class' => 'tips form-control input-sm', 
                    'id' => $forms['quality']['id'],
                    'data-content'=> __("Choose the quality of the stored images (1-100% of the original)."),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Image Quality"),

                    ))?>
                    <span class="input-group-addon">%</span>
                </div> 
            </div>
        </div>
        
        <div class="form-group">
            <?= FORM::label($forms['watermark']['id'], "<a target='_blank' href='http://open-classifieds.com/2013/11/12/how-to-add-a-watermark/'>".__('Watermark')."</a>", array('class'=>'control-label col-sm-3', 'for'=>$forms['watermark']['id']))?>
            <div class="col-sm-4">
                <div class="onoffswitch">
                    <?= Form::checkbox($forms['watermark']['key'], 1, (bool) $forms['watermark']['value'], array(
                    'placeholder' => __("TRUE or FALSE"), 
                    'class' => 'onoffswitch-checkbox', 
                    'id' => $forms['watermark']['id'], 
                    'data-content'=> __("Appends watermark to images"),
                    'data-trigger'=>"hover",
                    'data-placement'=>"right",
                    'data-toggle'=>"popover",
                    'data-original-title'=>__("Watermark"),
                    ))?>
                    <?= FORM::label($forms['watermark']['id'], "<span class='onoffswitch-inner'></span><span class='onoffswitch-switch'></span>", array('class'=>'onoffswitch-label', 'for'=>$forms['watermark']['key']))?>
                    <?= FORM::hidden($forms['watermark']['key'], 0);?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <?= FORM::label($forms['watermark_path']['id'], __('Watermark path'), array('class'=>'control-label col-sm-3', 'for'=>$forms['watermark_path']['id']))?>
            <div class="col-sm-4">
                <?= FORM::input($forms['watermark_path']['key'], $forms['watermark_path']['value'], array(
                'placeholder' => "images/watermark.png", 
                'class' => 'tips form-control input-sm', 
                'id' => $forms['watermark_path']['id'],
                'data-content'=> __("Relative path to the image to use as watermark"),
                'data-trigger'=>"hover",
                'data-placement'=>"right",
                'data-toggle'=>"popover",
                'data-original-title'=>__("Watermark path"), 
                ))?> 
            </div>
        </div>
        <div class="form-group">
            <?= FORM::label($forms['watermark_position']['id'], __('Watermark position'), array('class'=>'control-label col-sm-3', 'for'=>$forms['watermark_position']['id']))?>
            <div class="col-sm-4">
                <?= FORM::select($forms['watermark_position']['key'], array(0=>"Center",1=>"Bottom",2=>"Top"), $forms['watermark_position']['value'], array(
                'placeholder' => $forms['watermark_position']['value'], 
                'class' => 'tips form-control input-sm ', 
                'id' => $forms['watermark_position']['id'],
                'data-content'=> __("Watermark position"),
                'data-trigger'=>"hover",
                'data-placement'=>"right",
                'data-toggle'=>"popover",
                'data-original-title'=>__("Watermark position"), 
                ))?> 
            </div>
        </div>

				<?= FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'image'))))?>
				
			</fieldset>	
	</div><!--end col-md-10-->
