$(function(){
    
    //sceditorBBCodePlugin for validation, updates iframe on submit 
$("button[name=submit]").click(function(){
    $("textarea[name=description]").data("sceditor").updateTextareaValue();
});
    
    //chosen enable/disable
    $('select').chosen({
        no_results_text: getChosenLocalization("no_results_text"),
        placeholder_text_multiple: getChosenLocalization("placeholder_text_multiple"),
        placeholder_text_single: getChosenLocalization("placeholder_text_single")
    });
    $("#category_subscribe").chosen({
        no_results_text: getChosenLocalization("no_results_text"),
        placeholder_text_multiple: getChosenLocalization("placeholder_text_multiple"),
        placeholder_text_single: getChosenLocalization("placeholder_text_single")
    }); 
    $('select').each(function(){
        if($(this).hasClass('disable-chosen')){
            $(this).chosen('destroy');      
        } 
    });
    
    $('input, select, textarea, .btn').tooltip();

    //datepicker in case date field exists
    if($('.cf_date_fields').length != 0){
        $('.cf_date_fields').datepicker();}

	$('.tips').popover();

	$('.slider_subscribe').slider();

    $('.radio > input:checked').parentsUntil('div .accordion').addClass('in');

    $(window).load(function(){
        $('#accept_terms_modal').modal('show');
    });

    //online offline message
    window.addEventListener("offline", function(e) {
        $('.off-line').show();
    }, false);

    window.addEventListener("online", function(e) {
        $('.off-line').hide();
    }, false);

});

function setCookie(c_name,value,exdays)
{
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays==null) ? "" : ";path=/; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}
