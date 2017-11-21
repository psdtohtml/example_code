
/* Sub-menu mobile styles 
-----------------------------------------------------------------*/
$('.sub-menu li').addClass('header-top__popup-title-box2');
$('.sub-menu li a').addClass('header-top__popup-title');

$('.sub-menu li .sub-menu li a').removeClass('header-top__popup-title');
$('.sub-menu li .sub-menu li a').addClass('header-top__popup-title2');

$('.sub-menu li .header-top__cross').removeClass('header-top__cross').addClass('header-top__cross2');
$('.sub-menu li .header-top__link-popup').removeClass('header-top__link-popup');


/* Validation and checking for text and files in the fields form Careers Page
--------------------------------------------------------------------------------*/
$('#wpcf7-f450-o1 form.wpcf7-form').addClass('careers-manager__job-cv-form');

$('.careers-manager__submit').on('click', function () {

	if( document.getElementById("form-file1").files.length == 0 ){
	    //console.log("no files selected");
	    $('#label-file1').css({'border': '1px solid red', 'color': 'red'});
	} else {
	 	$('#label-file1').css({'border': '1px solid #0099cc', 'color': '#0099cc'});
	}

	if( document.getElementById("form-file2").files.length == 0 ){
	    //console.log("no files selected");
	    $('#label-file2').css({'border': '1px solid red', 'color': 'red'});
	} else {
		$('#label-file2').css({'border': '1px solid #0099cc', 'color': '#0099cc'});
	}

});