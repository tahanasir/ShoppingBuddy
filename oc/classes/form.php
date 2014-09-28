<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Form helper class.
 * Modifies the Kohana_Form methods to force the addition of "form_" prefix to the "id" fields attributes.
 *
 * @package    OC
 * @category   Helpers
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */

class Form extends OC_Form {

    /**
     * get the html tag code for a field for a custom field
     * @param  string $name input name
     * @param  array  $options as defined
     * @param  mixed $value value of the field, optional.
     * @param  boolean  $old =TRUE renders bs2 styles
     * @param  boolean  $inline renders inline form
     * @return string        HTML
     */
    public static function cf_form_tag($name, $options, $value = NULL, $old = FALSE, $inline = FALSE)
    {
        if ($options['display'] != 'hidden')
            $label = FORM::label($name, (isset($options['label']))?$options['label']:$name, array('class'=>(!$old)?'control-label':'', 'for'=>$name));
        else
            $label = '';

        if ($value === NULL)
            $value = (isset($options['default'])) ? $options['default']:NULL;
        
        // dependent classes on type
        $class = 'form-control '.'cf_'.$options['display'].'_fields data-custom ';
        switch ($options['display']) {
            case 'textarea':
                $class .= 'span6';
                break;
            case 'checkbox':
                $class = 'cf_'.$options['display'].'_fields data-custom';
                break;
            case 'radio':
                $class = 'cf_'.$options['display'].'fields data-custom';
                $required = (isset($options['required']) AND $options['required']== TRUE)?'required':NULL;
                $data_categories = (isset($options['categories'])) ? json_encode($options['categories']):NULL;
                $title = (isset($options['tooltip'])) ? $options['tooltip']:NULL;
                break;
            default:
                $class .= " ";
                break;
        }
        $attributes = array('placeholder'       => (isset($options['placeholder'])) ? $options['placeholder'] : ((isset($options['label'])) ? $options['label'] : $name),
                            'data-placeholder'       => (isset($options['data-placeholder'])) ? $options['data-placeholder'] : ((isset($options['label'])) ? $options['label'] : $name),
                            'title'             => (isset($options['tooltip'])) ? $options['tooltip']:NULL, 
                            'data-categories'   => (isset($options['categories'])) ? json_encode($options['categories']):NULL,
                            'class'             => $class, 
                            'id'                => $name,
                            (isset($options['required']) AND $options['required']== TRUE)?'required':NULL
                    );

        switch ($options['display']) 
        {
            case 'select':
                $input = FORM::select($name, $options['options'], (!is_array($value))?$value:NULL, $attributes);
                break;
            case 'text':
                $input = FORM::input($name, $value, $attributes);
                break;
            case 'textarea':
                $input = FORM::textarea($name, $value, $attributes);
                break;
            case 'hidden':
                $input = FORM::hidden($name, $value, $attributes);
                break;
            case 'date':
                $attributes['data-date'] = "data-date";
                $attributes['data-date-format'] = "yyyy-mm-dd";

                $input = FORM::input($name, $value, $attributes);
                break;
            case 'checkbox':
                $checked = isset($value); // value can be 1 or 'on'

                $label = '<b>'.$options['label'].'</b>';

                $input = '<div class="checkbox"><label>'.FORM::checkbox($name, NULL, $checked, $attributes).'</label></div>';
                
                break;
            case 'radio':
                $input = '';
                $label = '<b>'.$options['label'].'</b>';
                $index = 0;
                
                foreach($options['options'] as $id => $radio_name)
                {
                    $checked = ($value == $index) ? TRUE : FALSE ;
                    if($id !== "")
                        $input .= '<div class="radio"><label>'.Form::radio($name, $index, $checked, $attributes).$radio_name.'</label></div>';
                    
                    $index++;
                }
                break;
            case 'string':
            default:
                $input = FORM::input($name, $value, $attributes);
                break;
        }

        if(!$old){
            (!$inline)?$is_inline = "class='col-md-5 col-sm-8 col-xs-11'":$is_inline = "";
            $out = '<div '.$is_inline.'>'.$label.'<div class="control mr-30">'.$input.'</div></div>';
        }
        else{
            $out = $label.'<div>'.$input.'</div>';
        }

        return $out;
    }

} // End OC_Form
