    //sceditorBBCodePlugin for validation, updates iframe on submit 
    $("button[name=submit]").click(function(){
        $("textarea[name=description]").data("sceditor").updateTextareaValue();
    });
    
    // VALIDATION with chosen fix
    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );

    // some extra rules for custom fields
    if($('.cf_decimal_fields').length != 0)
        var $decimal = $(".cf_decimal_fields").attr("name");
    if($('.cf_integer_fields').length != 0)
        var $integer = $(".cf_integer_fields").attr("name");
    
    var $params = {rules:{}, messages:{}};
    $params['rules'][$integer] = {regex: "^[0-9]{1,18}([,.]{1}[0-9]{1,3})?$"};
    $params['messages'][$integer] = "Format is incorect";
    $params['rules'][$decimal] = {regex: "^[0-9]{1,18}([,.]{1}[0-9]{1,3})?$"};
    $params['messages'][$decimal] = "Format is incorect";
    $params['rules']['price'] = {regex: "^[0-9]{1,18}([,.]{1}[0-9]{1,3})?$"};
    $params['messages']['price'] = "Format is incorect";
    $params['rules']['website'] = {url: true};

    $.validator.setDefaults({ ignore: ":hidden:not(select)" });
    var $form = $(".edit_ad_form");
    $form.validate($params);

    //chosen fix
    var settings = $.data($form[0], 'validator').settings;
    settings.ignore += ':not(.cf_select_fields)'; // post_new location(any chosen) texarea
    // settings.ignore += ':not(.sceditor-container)'; // post_new description texarea
     settings.ignore += ':not(#description)'; // post_new description texarea
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

    // hide LOCATION if selected by POST
    $('.location_edit a').click(function(){
        $('.location_chained').removeClass('hide');
        $(this).parent().hide();
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
                showCustomFieldsByCategory($('input[name=category]'));
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

    showCustomFieldsByCategory($("input[name=category]"));

    // hide CATEGORY if selected by POST
    $('.category_edit a').click(function(){
        $('.category_chained').removeClass('hide');
        $(this).parent().hide();
    });

    //turn off chosen
    $(window).load(function(){
        $('select').each(function(){
            $(this).chosen(); 
            $(this).chosen('destroy');      
        }); 
        $('select').change(function() {
            $('select').each(function(){
                $(this).chosen(); 
                $(this).chosen('destroy');      
            });
        });
    });
    
    function showCustomFieldsByCategory(element){
        id_categ = $(element).val();
        // only custom fields have class data-custom
        $(".data-custom").each(function(){
            // get data-category, contains json array of set categories
            field = $(this);
            dataCategories = field.attr('data-categories');
            if(dataCategories)
            {
                // show if cf fields if they dont have categories set
                if(dataCategories.length != 2){
                    field.closest('#cf_new').css('display','none');
                    field.prop('disabled', true);
                }
                else{
                    field.closest('#cf_new').css('display','block');
                    field.prop('disabled', false);
                }
                if(dataCategories !== undefined)  
                {   
                    if(dataCategories != "")
                    {
                        // apply if they have equal id_category 
                        $.each($.parseJSON(dataCategories), function (index, value) { 
                            if(id_categ == value){
                                // console.log(index);
                                field.closest('#cf_new').css('display','block');
                                field.prop('disabled', false);
                            }
                        });
                    }
                }
            }
        });
    }