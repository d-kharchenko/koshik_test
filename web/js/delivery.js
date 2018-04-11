function editStik(){
		if ($('#addstick').val() >= 0) {
		$('.check-list__stick').text($('#addstick').val())

	}
	if ($('#addstick_stud').val() >= 0) {
		$('.check-list__nub-stick').text($('#addstick_stud').val())
	}
	}

function changeSticksValue(resValue, elementId){
	var curValue = document.getElementById(elementId).value;
	curValue = Number(curValue) + Number(resValue);
	curValue = Math.max(curValue, 0);
	document.getElementById(elementId).value = curValue;
	editStik();
	/*alert( "Animation complete." );
	document.getElementById("contact-form-sel-time").style.display = "none";
	document.getElementById("contact-form-sel-time").style.visibility = "hidden";*/
	//document.getElementById("contact-form-sel-time").style.visibility = "visible";
}
function fixTime(getTime,elementId){
	if (getTime < 10) {
		document.getElementById(elementId).value ='0' + getTime;
	}
	else{
		document.getElementById(elementId).value = getTime;
	}
}
function curTime(){
	var d = new Date();
	var curHours = d.toTimeString().substring(0, 2);
	var curMinutes = d.toTimeString().substring(3, 5);
	var inpHours = $('#sel-hours-input').val();
	var inpMinutes = $('#sel-minutes-input').val();
	if (inpHours <= curHours) {
		$('.sel-hours .minus-button').prop( "disabled", true );
		$('.sel-hours input').css('border', '1px solid red');
	}else{
		$('.sel-hours .minus-button').prop( "disabled", false );
		$('.sel-hours input').css('border', '1px solid #a0a0a0');
	}
	// if (inpMinutes < curMinutes ) {
	// 	$('.sel-minuts  input').css('border', '1px solid red');
	// }else{
	// 	$('.sel-minuts  input').css('border', '1px solid #a0a0a0');
	// }
}
function changeDateValue(resValue, maxValue, elementId) {
	var curValue = document.getElementById(elementId).value;
	curValue = Number(curValue) + Number(resValue);
	if (curValue > maxValue){
		curValue = 0
	} else if (curValue < 0) {
		curValue = maxValue;
	}	
	
	fixTime(curValue, elementId);
	var d = new Date();
	var curr_date = d.getDate();
	var curr_month = d.getMonth() + 1;
	var curr_year = d.getFullYear();
	var nowDate = curr_date + "." + curr_month + "." + curr_year;
	var inpHours = $('#datepicker').val();
	if(nowDate == inpHours){
		 curTime();
	}else{
		return true;
	}
	if (curValue == maxValue) {
		$('.sel-hours .plus-button').prop( "disabled", true );
	}else{
		$('.sel-hours .plus-button').prop( "disabled", false );
	}
}

function getElementValue(elementId){
	var curValue = document.getElementById(elementId).value;
	return curValue;
}

function showHideDeliveryTime() {
	
	if (document.getElementById("chbox").checked) {
		document.getElementById("contact-form-sel-time").style.display = "flex";
		document.getElementById("contact-form-sel-time").style.visibility = "visible";
		
	} else {
		document.getElementById("contact-form-sel-time").style.display = "none";
		document.getElementById("contact-form-sel-time").style.visibility = "hidden";
	}

}


$(function() {
	setCurrentDateToDelivery();
	$('input[type=radio][name=radio-sel]').change(function () {
		var curDeliverySum = $(this).attr('data-price');
		$('#del-sum').text(curDeliverySum);
		var curOrderSum = $('#order-sum').text();
		var curOrderSumWithDelivery = (parseFloat(curDeliverySum) + parseFloat(curOrderSum)).toFixed(2);
		$('#order-sum-with-del').text(curOrderSumWithDelivery);
	})
});

function setCurrentDateToDelivery(){
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yyyy = today.getFullYear();
	var hours = today.getHours();
	var minutes = today.getMinutes()+20;
		if(dd<10) {
	    	dd = '0'+dd
		} 

		if(mm<10) {
	    	mm = '0'+mm
		} 
	var setNowDate  = dd + "." + mm + "." + yyyy;
	$('#datepicker').val(setNowDate);
	
	// document.getElementById("date-input").value = yyyy + "-" + mm + "-" + dd;
	if ((minutes>0)&&(minutes>=30)){
		minutes = 0;
		hours = hours + 1;
	} else if ((minutes>=0)&&(minutes<30)) {
		minutes = 30;
	}
	if (minutes < 10 ) {
		document.getElementById("sel-minutes-input").value = '0' + minutes;
	}else{
		document.getElementById("sel-minutes-input").value = minutes;
	}
	if (hours < 10) {
		document.getElementById("sel-hours-input").value = '0' + hours;
	}else{
		document.getElementById("sel-hours-input").value = hours;
	}
	

}

$(function(){

// function setTodaydate(){
// 	var toDay = new Date();
// 	var curr_date = toDay.getDate();
// 	var curr_month = toDay.getMonth() + 1;
// 	var curr_year = toDay.getFullYear();
// 	var nowDate = curr_date + "." + curr_month + "." + curr_year;
// 	$('#datepicker').val(nowDate);
// }
// setTodaydate();


function stopOrder (){
	function getDate (){
		var getTodayDate = new Date();
		var getDay = getTodayDate.getDate();
		var getMonth = getTodayDate.getMonth()+1;
		var getYear = getTodayDate.getFullYear();
			if(getDay<10) {
		    	getDay = '0'+getDay
			} 

			if(getMonth<10) {
		    	getMonth = '0'+getMonth
			} 
		var getFullDate = getDay  + '.' + getMonth  + '.' + getYear;

		return getFullDate;
	}
	var formDate = $('#datepicker').val().replace(/\./g, "");
	var toDay = getDate().replace(/\./g, "");
	if (formDate == toDay) {
		$('#ui-datepicker-div').append('<div id="modal-message">Вибачте, на сьогодні замовлення не приймаються, але Ви можете замовити на завтра <img src="https://cdn3.iconfinder.com/data/icons/emoticons-50/24/scared_emoticon_emoticons_emoji_emote-128.png"></div>')
		
		$( "#modal-message" ).dialog({
		    modal: true,
		    height: 300,
		    buttons: [
		    {
		    	text: "Закрити",
		        click: function() {
		          $( this ).dialog( "close" );
		        }
		      }
		    ]
	    });
	    $('.ui-widget-overlay').css(
	    	{
	    		'position': 'fixed',
	    		'top': '0',
	    		'left': '0',
	    		'width': '100%',
	    		'height': '100%',
	    		'background-color': '#cccccc',
	    		'opacity': '0.5'
	    	}
	    )
	}
}
	
	


$( "#datepicker" ).datepicker({
	prevText: "&#x3C;",
	nextText: "&#x3E;",
  	dateFormat: "dd.mm.yy",
  	monthNames :  [ "Січень","Лютий","Березень","Квітень","Травень","Червень","Липень","Серпень","Вересень","Жовтень","Листопад","Грудень" ],
  	dayNamesMin: [ "Нд","Пн","Вт","Ср","Чт","Пт","Сб" ],
  	firstDay: 1,
  	minDate: 0,
    maxDate: "+30d",

});
            

// var dateFormat = $( "#datepicker" ).datepicker( "option", "dateFormat" );
 
// // Setter
// $( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
// $( "#datepicker" ).datepicker();
$('.check-list-wrap__item').click(function(){
	$('.check-list').toggle();
})
	$('#cart-container').click(function(event){
		event.preventDefault();
		$('.order-list').toggle();
		
		$('.hide-menu__item').toggleClass('rotate-arrow');
		// $('.sushi-menu__adaptive').css('margin-top', $(".order-list").height() + 140);
	})
	var itemCount = $("#itemCount").attr("itemData");
	$('#itemCount').html(itemCount).css('display', 'block');

function remActive(el){
	$('.check-list--active').removeClass('check-list--active');
	el.addClass('check-list--active')
}

function setDeliveryPriceToReceipt(){
	var curDeliverySum = $('.contact-form-type-del__list input:checked').attr('data-price');
	//var curDeliverySum = $('input:radio[name=radio-sel]:checked').attr('data-price');
	$('#del-sum').text(curDeliverySum);
}


	
	$('.del-to-house').click(function(){
		remActive($(this))
		
		$('.contact-form-addres__right-item').css('display', 'none');
		if ($('.hide-items').css('display') == 'none') {
			$('.contact-form-adr-self-srv input').prop('checked',false);
			$('.contact-form-addres__left-item  input').attr('required', true);
			$('#personal-home').attr('required', true);
			$('.hide-items').css('display', 'inherit');
			$('.contact-form-adr-self-srv').css('display', 'none');
			$('.contact-form-addres__right-item').css('display', 'none');
			$('.contact-form-adr-self-srv ').removeClass('red-rdo');
			
		}
		//setDeliveryPriceToReceipt();
	})
	$('.del-to-flat').click(function(){
		
		remActive($(this))
		if ($('.hide-items').css('display') == 'none') {
			$('.contact-form-addres__left-item  input').attr('required', true);
			$('.contact-form-adr-self-srv input').prop('checked',false);
			$('#personal-home').attr('required', true);
			$('.contact-form-addres__right-item input').attr('required', true);
			$('.hide-items').css('display', 'inherit');
			$('.contact-form-adr-self-srv').css('display', 'none');
			$('.contact-form-adr-self-srv ').removeClass('red-rdo');
		}else{
			$('.contact-form-addres__right-item').css('display', "inherit");
		}
		//setDeliveryPriceToReceipt();
	});
	function clearAdress(){
		$('.contact-form-addres__street  input').val('');
		$('.contact-form-addres__left-item  input').val('');
		$('.contact-form-addres__center-item  input').val('');
		$('.contact-form-addres__right-item input').val('');
	}
	$('.self-srv-bnt').click(function(){
		
		remActive($(this))
		$('.contact-form-addres__left-item  input').removeAttr('required');
		clearAdress();
		$('#personal-home').removeAttr('required');
		$('.contact-form-addres__right-item input').removeAttr('required');
		$('.hide-items').css('display', 'none');
		$('.contact-form-adr-self-srv').css('display', 'block')
		if ($('.contact-form-adr-self-srv input:checked').length == 0 ) {
			$('.contact-form-adr-self-srv ').addClass('red-rdo')
		}
		//setDeliveryPriceToReceipt();
		
	})
	$('.contact-form-adr__item').click(function(){
		if ($('.red-rdo').css('display') == 'block') {
			$('.contact-form-adr-self-srv ').removeClass('red-rdo')
		}
	})

	$('.next-button').click(function(){
		 stopOrder();
		if (($('.red-rdo').css('display') == 'block') ) {
			return false
		}else{
			return true;
		}
	})
})