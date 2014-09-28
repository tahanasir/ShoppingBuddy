<?php defined('SYSPATH') or die('No direct access allowed.');
/**
  * Theme Name: Ocean Free
  * Description: Clean free theme that includes full admin. It has publicity. Do not delete this theme, all the views depend in this theme.
  * Tags: HTML5, Admin, Free
  * Version: 2.2.1
  * Author: Chema <chema@open-classifieds.com> , <slobodan@open-classifieds.com>
  * License: GPL v3
  */


/**
 * placeholders for this theme
 */
Widgets::$theme_placeholders	= array('footer', 'sidebar');


/**
 * styles and themes, loaded in this order
 */

Theme::$styles = array( '//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' => 'screen',
                        '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' => 'screen',
                        '//cdn.jsdelivr.net/blueimp-gallery/2.14.0/css/blueimp-gallery.min.css' => 'screen', 
                        '//cdn.jsdelivr.net/bootstrap.datepicker/0.1/css/datepicker.css' => 'screen',
                        '//cdn.jsdelivr.net/chosen/1.0.0/chosen.css' => 'screen',
                        'css/styles.css?v='.Core::VERSION => 'screen',
                        'css/slider.css' => 'screen',
        				);


Theme::$scripts['footer']	= array('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js',
                                    '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js',
                                    '//cdn.jsdelivr.net/chosen/1.0.0/chosen.jquery.min.js',
                                    Route::url('jslocalization', array('controller'=>'jslocalization', 'action'=>'chosen')),
                                    '//cdn.jsdelivr.net/blueimp-gallery/2.14.0/js/jquery.blueimp-gallery.min.js',
                                    '//cdn.jsdelivr.net/bootstrap.datepicker/0.1/js/bootstrap-datepicker.js',
                                    'js/bootstrap-slider.js',
                                    'js/default.init.js?v='.Core::VERSION,
                                    'js/theme.init.js?v='.Core::VERSION,
                                    );


/**
 * custom options for the theme
 * @var array
 */
Theme::$options = array();


/**
 * custom error alerts
 */
Form::$errors_tpl 	= '<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a>
			       		<h4 class="alert-heading">%s</h4>
			        	<ul>%s</ul></div>';

Form::$error_tpl 	= '<div class="alert "><a class="close" data-dismiss="alert">×</a>%s</div>';


Alert::$tpl 	= 	'<div class="alert alert-%s">
					<a class="close" data-dismiss="alert" href="#">×</a>
					<h4 class="alert-heading">%s</h4>%s
					</div>';