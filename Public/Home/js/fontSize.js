try{
var docEl = document.documentElement,
resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function () {
		var clientWidth = docEl.clientWidth;
		if (!clientWidth) return;
		if(clientWidth>=750){
		docEl.style.fontSize = '100px';
		 // docEl.style.fontSize = 100 * (clientWidth / 640) + 'px';
	}else{
	  docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
	}
	// recalc();
};

if (document.addEventListener){
window.addEventListener(resizeEvt, recalc, false);
document.addEventListener('DOMContentLoaded', recalc, false);
}
} catch (e) {}



$(function(){
 	$("nav a").click(function() {
 		var href=$(this).attr('data-href');
 		$(this).addClass('mui-active').siblings().removeClass('mui-active');
	    if(href){
	   	   window.location.href=href;

	    }
	   
	});
	
});