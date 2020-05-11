$(document).ready(function(){
	$(function() {
		$.curCSS = function (element, attrib, val) {
			$(element).css(attrib, val);
		};
		var ac = {
			source : function(request, response) {
				$.ajax({
					url : "http://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?",
					dataType : "jsonp",
					data : {
						q : function() {
							return $("#cdek_city").val()
						},
						name_startsWith : function() {
							return $("#cdek_city").val()
						}
					},
					success : function(data) {
						
						response($.map(data.geonames, function(item) {
							console.log(item.postCodeArray[0])
							return {
								label : item.name,
								value : item.name,
								id : item.id,
								zip : item.postCodeArray[0]
							}
						}));
					}
				});
			},
			minLength : 2,
			select : function(event, ui) {
				;
				$("#cdek_city").val(ui.item.value);
				$('#cdek_zip').val(ui.item.zip);
				Commerce.updateOrderData($('form.big_cart'));
			}
		};
		$(document).on('keydown.autocomplete','#cdek_city',function(){
			$(this).autocomplete(ac);
		});	
	});
});