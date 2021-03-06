$(function () {

	// function stopOrder (){
	// 	$('.wrap').append('<div id="modal-message">Вибачте, на сьогодні замовлення не приймаються, але Ви можете замовити на завтра <img src="https://cdn3.iconfinder.com/data/icons/emoticons-50/24/scared_emoticon_emoticons_emoji_emote-128.png"></div>')
		
	// 	$( "#modal-message" ).dialog({
	// 	    modal: true,
	// 	    height: 300,
	// 	    buttons: [
	// 	    {
	// 	    	text: "Закрити",
	// 	        click: function() {
	// 	          $( this ).dialog( "close" );
	// 	        }
	// 	      }
	// 	    ]
	//     });
	//     $('.ui-widget-overlay').css(
	//     	{
	//     		'position': 'fixed',
	//     		'top': '0',
	//     		'left': '0',
	//     		'width': '100%',
	//     		'height': '100%',
	//     		'background-color': '#cccccc',
	//     		'opacity': '0.5'
	//     	}
	//     )
	// }

	// if (true) {
	// 	stopOrder();
	// } 


// модалька с описанием суши


function sushiDetail(title, image, description, weight, quantityRolls ){

	var sushiTitle = "<div class='sushi-modal__title'>" + title + "</div>";
	var sushiImage = "<img src=" + image + " class='sushi-modal__image' alt=" + title + ">";
	var sushiWeightQty = "<p class='sushi-modal__weight-qty'>" + weight + "<span class='sushi-modal__quantity'>" + quantityRolls + "</span></p>";
	var sushiDescription = "<p class='sushi-modal__description'><span class='sushi-modal__description-title'>Склад:<br></span>" + description + "</p>";
	var sushiDesWrap = "<div class='sushi-modal__wrap-desc'>"+sushiImage + sushiWeightQty + sushiDescription +"</div>"
	var sushiCloseModal = '<a href="#" rel="modal:close" class="sushi-modal__close-btn">Закрити</a>';

	$("#menu-item__modal-window").append(sushiTitle, sushiDesWrap, sushiCloseModal);
}

	

$('.menu-item__modal').click(function(){
	var getTitle = $(this).children('.menu-item__name').text();
	var getImg = $(this).children('.menu-item__img').attr("src")
	var getDescription = 'Рис, лосось, сир і т.п.';
	var getWeight = '300 гр.';
	var getQuantityRolls = '6 шт.'

	sushiDetail(getTitle, getImg, getDescription, getWeight, getQuantityRolls)

	$("#menu-item__modal-window").modal();

  	return false;
});

// конец модальки с описанием сусши

	$('#tabs').tabs();
	$('#cart-container').click(function(event){
		event.preventDefault();
		$('.order-list').toggle();
		$('.tabs__list').toggleClass('sushi-menu__adaptive');
		$('.hide-menu__item').toggleClass('rotate-arrow');
	})
	var totalCartSum = $('#cartSumTotal').text();
		var sumFreeShipping = parseInt($("#order-list").attr("data-free-del-price"));
		if (totalCartSum < sumFreeShipping) {
            $('.cost-block__item').css('display', 'none');
            $('.cost-block__free-del span').text(sumFreeShipping-totalCartSum);
        }
        else{
            $('.cost-block__free-del').css('display', 'none');
            $('.cost-block__item').css('display', 'block');
        }
	

	$('.header-block__menu-icon').click(function(){
		$('.header-nav').toggle();
		$('.header-nav').css('padding-top', '30px');
		$('.header-nav').removeClass('hide-nav');
	});
	function nullCart(){
	var notActive = $('#cartSumTotal').html();
	if(notActive > 1){
		$('.makeOrderButton, .cost-block').css('opacity', '1');
		
	}else{
		$('.makeOrderButton, .cost-block').css('opacity', '0.3');
		
	}
	};
	nullCart();
	if($(window).width() < 767) {
			$('.header-nav__link').click(function(){
			$('.header-nav').toggleClass('hide-nav');
			$('.hide-nav').css({'display' : 'none'});
		});
			$('.header-nav__item').addClass('hide-nav__md')	
		} else {
		$('.header-nav__item').removeClass('hide-nav__md')
		
		};
		if
			($(window).width() > 767) {
				$('.order-list').css({'display' : 'block'});
				$('.header-nav__item').removeClass('hide-nav__md')
				//$("#makeOrderButton span").text("Оформити замовлення");
    } else {
		$('.order-list').css({'display' : 'none'})
		//$("#makeOrderButton span").text("Оформити<br>замовлення");
		$('.header-nav__item').addClass('hide-nav__md')
	};
	
//перенести в index 	
 function initMap() {
        var uluru = {lat: 50.906828,  lng: 34.796613};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 17,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
     }
initMap();


});

$(document).ready(function(){
	var itemCount = $("#itemCount").attr("itemData");
	$('#itemCount').html(itemCount).css('display', 'block');
	$('.for-modal').click(modalWin);
});

function CheckfreeDelivery(totalCartSum, sumFreeShipping){
	//var forFreeDel = $('#cartSumTotal').text();
	//var sumFreeShipping = $("#order-list").attr("data-free-del-price");
	console.log();
	if (totalCartSum < sumFreeShipping ) {
            $('.cost-block__free-del').css('display', 'block');
            $('.cost-block__item').css('display', 'none');
            $('.cost-block__free-del span').text(sumFreeShipping-totalCartSum);
		}
		else{
		    $('.cost-block__free-del').css('display', 'none');
            $('.cost-block__item').css('display', 'block');
		}
}

function modalWin (){
	$('.wrap').css('overflow', 'hidden');
	var modal = $("#dialog-item-modal");
	if ((modal.length) == false) {
		modal = document.createElement('div');
		modal.id = "dialog-item-modal";
		modal.className = 'order-block__modal';
	} else {return 0};
	$('#dialog-item-modal').dialog({
			className: 'order-block__modal',	
			autoOpen: false,
			  show: {
				effect: "blind",
				duration: 1000
			  },
			  hide: {
				effect: "explode",
				duration: 1000
			  },
			  /*close: function(event, ui) {
				console.log('closing1');
				$('.ui-dialog').remove();
				//$('.order-block__modal').remove();  
				$(this).dialog('destroy').remove();
				$('.overlay').remove();
				console.log('closing2');
			  }*/
		});
			//console.log($('#dialog-item-modal111'));
			var res = $(this).text();
			var itemID_string = $(this).attr("id");
			var itemID = itemID_string.replace("qty", "");
			var itemName = $('#name'+itemID).text();
			//var modal = document.createElement('div');
			
				//modal.innerHTML = '<button class="modal-btn minus-btn">-</button>'+'<input class="qty-val" type="text" min="1" max="99" maxlength="2" name="" value="'+res+'">'+'<button class="modal-btn plus-btn">+</button>'+'<br>'+'<button class="modal-btn modal-ok-btn" onClick="setCartItemQty("'+itemID+'","'+res+'")>Застосувати</button>';
				modal.innerHTML = "<form><button class='modal-btn minus-btn'>-</button><input id = 'qty-val"+itemID+"' class='qty-val' type='number' minlength='1' maxlength='2'  value='"+res+"' pattern='[0-9]{,2}' required><button class='modal-btn plus-btn'>+</button><br><button class='modal-btn modal-ok-btn' onClick='setCartItemQty("+itemID+",0,1,1)'>Застосувати</button></form>";
			var overlay = document.createElement('div');
				overlay.className = ('overlay');	

				document.body.appendChild(modal);
				document.body.appendChild(overlay);



				$('.order-block__modal').attr({
					title: itemName//'Введіть кількість'
				})

				$('.order-block__modal').dialog(open);

				$('.overlay').click(DestroyModalFormElms);/*function(){
					$('.ui-dialog').remove();
					$('.order-block__modal').remove();
					$('#dialog-item-modal').dialog('destroy').remove();
					$(this).remove();
				});*/
				$('.ui-button-icon').click(DestroyModalFormElms);/*{
					$('.ui-dialog').remove();
					$('.order-block__modal').remove();  
					$('#dialog-item-modal').dialog('destroy').remove();
					$('.overlay').remove();
					//console.log('closing2');
				});*/
				$('.minus-btn').click(function(){
					 var $input = $(this).parent().find('input');
					 var count = parseInt($input.val()) - 1;
						count = count < 1 ? 1 : count;
						$input.val(count);
						$input.change();
						return false;
				})
				$('.plus-btn').click(function(){
				var $input = $(this).parent().find('input');
					$input.val(parseInt($input.val()) + 1);
					$input.change();
					return false;
					
				})

	};

function nullCartSrv(sumOrderTotal){
	
	if(sumOrderTotal > 1){
		$('.makeOrderButton, .cost-block').css('opacity', '1');
		
		
	}else{
		$('.makeOrderButton, .cost-block').css('opacity', '0.3');
		
	}
	};

function DestroyModalFormElms() {
	$('.ui-dialog').remove();
	$('.order-block__modal').remove();  
	$('#dialog-item-modal').dialog('destroy').remove();
	$('.overlay').remove();
	$('.wrap').css('overflow', 'visible');
};

function setCartItemQty(itemId, qty = 1, isSet = 0, getQty = 0) {
	
	if (getQty == 1) {
		qty = $('#qty-val'+itemId).val();
		$('#qty-val'+itemId).remove();
		DestroyModalFormElms();		
	}
	if (itemId == 0){
		return;
	}
	//console.log(qty);
	$.ajax({
		type: "POST",
		url: "setcartitemcountajax/"+itemId+"/"+qty+"/"+isSet,
		contentType: 'application/json; charset=UTF-8',
		data: {'product_id': itemId},
		dataType: "json",
		success: function(response_data, textStatus, jqXHR) {
			var jsonText = jqXHR.responseText;
			var curQtyEl = $("#qty"+itemId); 
			var curQty = 0;
			/*var curQty = curQtyEl.html();
			if (curQty == undefined)  {
				curQty = 0;
			}*/
			
			var json_obj = JSON.parse(jsonText);	
			$('#itemCount').html(json_obj.itemCountTotal);
			$('#itemCount').attr('itemData', json_obj.itemCountTotal);
			$('#cartSumTotal').text(json_obj.sumOrderTotal.toFixed(2));		
			nullCartSrv(json_obj.sumOrderTotal);
			if ((json_obj.products_cart.length == 1 && json_obj.products_cart[0].id == 0) == false){
				$('#cart-string0').remove();

			}
			for (var i = 0; i < json_obj.products_cart.length; i++) {
				var item_object = json_obj.products_cart[i];
				if  (item_object.id == itemId) {
					curQty = item_object.qty;
					//if (curQty == 0){
					if ($("#row_num"+item_object.id).length == false) {
						var newRawString = "<tr class='order-block__to-order' id = 'cart-string"+item_object.id+"'>";
						newRawString = newRawString + "<td id = 'row_num"+item_object.id+"'>"+item_object.row_num+"</td>";
						newRawString = newRawString + "<td id = 'name"+item_object.id+"' style='text-align: left'>"+item_object.name+"</td>";
						newRawString = newRawString + "<td id = 'qty"+item_object.id+"' class='for-modal'>"+ item_object.qty +"</td>";
						newRawString = newRawString + "<td id = 'price"+item_object.id+"'>"+item_object.price+"</td>";
						//newRawString = newRawString + "<td><button class='delFromCartButton' id='delFromCartButton"+item_object.id+"' onclick='setCartItemQty("+item_object.id+",-1)'>"+(item_object.id > 0) ? "<img class='delImgButton' src='img/delete-button.png'/>" : ""+"</button></td>";
						newRawString = newRawString + "<td><button class='delFromCartButton' id='delFromCartButton"+item_object.id+"' onclick='setCartItemQty("+item_object.id+",-1)'><img class='delImgButton' src='img/delete-button.png'/></button></td>";
						newRawString = newRawString + "</tr>";
						$('#cart-table tr:last').after($(newRawString));
						$('.for-modal').click(modalWin);
					} else {
						curQtyEl.html(item_object.qty);
					}
				}else {
					$("#row_num"+item_object.id).html(item_object.row_num);
				}
			}
			//if (json_obj.products_cart.length == 1 && json_obj.products_cart[0].id == 0 && json_obj.sumOrderTotal == 0){
			/*if ((curQty <= 0)&&(((json_obj.products_cart.length+1) == 1 && json_obj.sumOrderTotal>0)==false)){*/
			if (curQty == 0) {
				$('#cart-string'+itemId).remove();

			}
			
			if (json_obj.products_cart.length == 1 && json_obj.products_cart[0].id == 0 && json_obj.sumOrderTotal == 0){
				if ($('#row_num0').length == false) {					
					var newRawString = "<tr class='order-block__to-order' id = 'cart-string0'>";
					newRawString = newRawString + "<td id = 'row_num0'></td>";
					newRawString = newRawString + "<td id = 'name0' style='text-align: left'>Кошик порожній</td>";
					newRawString = newRawString + "<td id = 'qty0'></td>";
					newRawString = newRawString + "<td id = 'price0'></td>";
					//newRawString = newRawString + "<td><button class='delFromCartButton' id='delFromCartButton0' onclick='setCartItemQty(0)'><img class='delImgButton' src='img/delete-button.png' width='14' height='14'/></button></td>";
					newRawString = newRawString + "<td><button class='delFromCartButton' id='delFromCartButton0' onclick='setCartItemQty(0)'></button></td>";
					newRawString = newRawString + "</tr>";
					$('#cart-table tr:last').after($(newRawString));
				}
			}
			CheckfreeDelivery(parseInt(json_obj.sumOrderTotal), parseInt(json_obj.sumFreeDeliveryPrice));

		},

		error: function(response, desc, err) {
			alert(desc+": "+err);
		}
	 });

	


		
			
}

function goToDeliveryInformation() {
	
	
	//window.prompt("sometext","defaultText");
	//window.location.href = "/delivery";

	$.get({
		type: "POST",
		url: "checkcartajax",
		contentType: 'application/json; charset=UTF-8',
		dataType: "json",
		success: function(response_data, textStatus, jqXHR) {
			var jsonText = jqXHR.responseText;
			var json_obj = JSON.parse(jsonText);
			if (json_obj.cartIsEmpty) {
				// alert("Ваш кошик порожній!");
			}else {
				window.location.href = "/delivery";
			}
		},
		error: function(response, desc, err) {
			alert(desc+": "+err);
		}
	 });

}

function goToFinishOrder() {
	
	$.get({
		type: "POST",
		url: "checkcartajax",
		contentType: 'application/json; charset=UTF-8',
		dataType: "json",
		success: function(response_data, textStatus, jqXHR) {
			var jsonText = jqXHR.responseText;
			var json_obj = JSON.parse(jsonText);
			if (json_obj.cartIsEmpty) {
				// alert("Ваш кошик порожній!");
				window.location.href = "";
			}else {
				
				window.location.href = "/orderfinish";
			}
		},
		error: function(response, desc, err) {
			alert(desc+": "+err);
		}
	 });


	}
	