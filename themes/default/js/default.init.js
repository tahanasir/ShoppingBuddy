$(function(){
    
    //favorites system
	$('.add-favorite, .remove-favorite').click(function(event) {
		  event.preventDefault();
		  $this = $(this);
		  $.ajax({ url: $this.attr('href'),
				}).done(function ( data ) {
					$('#'+$this.data('id')+' a').toggleClass('add-favorite remove-favorite');
					$('#'+$this.data('id')+' a i').toggleClass('glyphicon-heart-empty glyphicon-heart');
				});
	});

});
