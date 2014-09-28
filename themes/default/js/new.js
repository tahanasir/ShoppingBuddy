$('textarea[name=description]').sceditorBBCodePlugin({
    toolbar: "bold,italic,underline,strike,|left,center,right,justify|" +
    "bulletlist,orderedlist|link,unlink,youtube|source",
    resizeEnabled: "true"
});
	
// paste plain text in sceditor
$(".sceditor-container iframe").contents().find("body").bind('paste', function(e) {
	e.preventDefault();
	var text = (e.originalEvent || e).clipboardData.getData('text/plain');
	$(".sceditor-container iframe")[0].contentWindow.document.execCommand('insertText', false, text);
});	

// google map set marker on address
if($('#map').length != 0){
new GMaps({
  div: '#map',
  zoom: parseInt($('#map').attr('data-zoom')),
  lat: $('#map').attr('data-lat'),
  lng: $('#map').attr('data-lon')
}); 
var typingTimer;                //timer identifier
var doneTypingInterval = 500;  //time in ms, 5 second for example
//on keyup, start the countdown
$('#address').keyup(function(){
    clearTimeout(typingTimer);
    if ($(this).val()) {
       typingTimer = setTimeout(doneTyping, doneTypingInterval);
    }
});
//user is "finished typing," refresh map
function doneTyping () {
    GMaps.geocode({
      address: $('#address').val(),
      callback: function(results, status) {
        if (status == 'OK') {
          var latlng = results[0].geometry.location;
            map = new GMaps({
              div: '#map',
              lat: latlng.lat(),
              lng: latlng.lng(),

            }); 
          map.setCenter(latlng.lat(), latlng.lng());
          map.addMarker({
            lat: latlng.lat(),
            lng: latlng.lng()
          });
        }
      }
    });
}
}

    // VALIDATION with chosen fix
    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        }
    );

    // some extra rules for custom fields
    if($('.cf_decimal_fields').length != 0)
        var $decimal = $(".cf_decimal_fields").attr("name");
    if($('.cf_integer_fields').length != 0)
        var $integer = $(".cf_integer_fields").attr("name");
    
    var $params = {rules:{}, messages:{}};
    $params['rules'][$integer] = {regex: "^[0-9]{1,18}([,.]{1}[0-9]{1,3})?$"};
    $params['rules'][$decimal] = {regex: "^[0-9]{1,18}([,.]{1}[0-9]{1,3})?$"};
    $params['rules']['price'] = {regex: "^[0-9]{1,18}([,.]{1}[0-9]{1,3})?$"};
    $params['rules']['title'] = {maxlength: 145};
    $params['rules']['address'] = {maxlength: 145};
    $params['rules']['phone'] = {maxlength: 30};
    $params['rules']['website'] = {url: true, maxlength: 200};

    $.validator.setDefaults({ ignore: ":hidden:not(select)" });
    var $form = $(".post_new");
    $form.validate($params);
    
    //chosen fix
    var settings = $.data($form[0], 'validator').settings;
    settings.ignore += ':not(#location)'; // post_new location(any chosen) texarea
    settings.ignore += ':not([name="description"])'; // post_new description texarea

    
    // end VALIDATION

    //datepicker in case date field exists
    if($('.cf_date_fields').length != 0){
        $('.cf_date_fields').datepicker();}

    //LOCATIONS activate for each level chained select for 
    $('.location_chained_select').each(function(){
        var level = $(this).data('level');
        if('#level-loc-'+(level-1)){
            $('#level-loc-'+level).chained('#level-loc-'+(level-1));
        }
    });

    //LOCATION this will select the correct ID for uploading category
    $( ".location_chained_select" ).change(function() {

      $( "option:selected", this ).each(function() {
            var value_location_id = $(this).attr('value');

                $('#location-selected').attr('value',value_location_id);

                //coloring select, for user to know if he select option is taken or not
                if($('#location-selected').attr('value') != ''){
                    //adding green color, success
                    $(this).parent().css('background','#dff0d8');
                    $('.selected-location').html($('.location_chained_select option[value='+$('#location-selected').attr('value')+']').text()).one();
                }
        
                if($('#location-selected').attr('value') == ''){
                    $(this).parent().css('background','#fff');
                    $('.selected-location').html('');
                }
                     
        });
                
    });
    
    //CATEGORY activate for each level chained select
    $('.category_chained_select').each(function(){
        var level = $(this).data('level');
        if('#level-'+(level-1)){
            $('#level-'+level).chained('#level-'+(level-1));
        }
    });

    // this will select the correct ID for uploading category
    $( ".category_chained_select" ).change(function() {

      $( "option:selected", this ).each(function() {
            var value_category_id = $(this).attr('value');

            if($(this).parent().hasClass('is_parent') || $(this).parent().data('level') > 0){

                $('#category-selected').attr('value',value_category_id);
                $('.category-price').text('');
                if($(this).data('price') > 0)
                    $('.category-price').text($(this).data('price'));
            }
                //coloring select, for user to know if he select option is taken or not
                if($('#category-selected').attr('value') != ''){
                    //adding green color, success
                    $(this).parent().css('background','#dff0d8');
                    $('.selected-category').html($('.category_chained_select option[value='+$('#category-selected').attr('value')+']').text()).one();
                }
        
                if($('#category-selected').attr('value') == ''){
                    $(this).parent().css('background','#fff');
                    $('.selected-category').html('');
                }
                     
        });
                
    });

    var categ_selected = $('.category_chained_select option:selected').attr('value');
    $('.category_edit a').click(function(){
        $('.category_chained').removeClass('hide');
        $(this).parent().hide();
    });

    $('.location_edit a').click(function(){
        $('.location_chained').removeClass('hide');
        $(this).parent().hide();
    });
    
    //unhide next box image after selecting first
    $('.fileinput').on('change.bs.fileinput', function() {
       $(this).next('.fileinput').removeClass('hidden');
    });

    //publish new processing modal
    $(function(){
		$('#publish-new-btn').click(function () {
			if ($("#publish-new").valid()) {
				$('#processing-modal').modal('show');
			}
		});
    });
	
    //sure you want to leave alert
	$(function(){
		$('#publish-new').data('serialize',$('#publish-new').serialize());
		$("#publish-new-btn").click(function(){
            if ($("#publish-new").valid()) {
                $(this).data('clicked', true);
            }
		});
		$(window).bind('beforeunload', function(){
			if($('#publish-new').serialize()!=$('#publish-new').data('serialize') && !$('#publish-new-btn').data('clicked') ) {
				return $('#leaving_alert').val();
			}
		});
    });