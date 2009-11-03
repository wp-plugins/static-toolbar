(function($) { 

var interval;
var refresh;
var bandeau;
var statut;
var elements;

function closeStaticBar(){
	bandeau.css('height',"16px");
	stopDefil();
	blocs = $(this).parent().parent().children('li:not(#static-toolbar-button)');
	blocs.each(function(){
		$(this).css('display','none');
	});	
	$(this).css('display','none');
	$('img#static-toolbar-open').css('display','inline');

}
function openStaticBar(){
	startDefil();
	bandeau.css('height',"40px");
	blocs = $(this).parent().parent().children('li:not(#static-toolbar-button)');
	blocs.each(function(){
		$(this).css('display','block');
	});	
	$(this).css('display','none');
	$('img#static-toolbar-close').css('display','inline');
}

$.fn.loadDefil = function(){
	interval =  0;
	refresh = 5000;
	bandeau = $(this);
	defileur = $('#static-toolbar-posts');
	statut = 0;
   	elements = $(defileur).children('ul').children('li');
	entry = elements[statut];
	$(entry).attr('class','entryActive');
	initDefil();
	$('#static-toolbar-button img#static-toolbar-close').bind('click',closeStaticBar);
	$('#static-toolbar-button img#static-toolbar-open').bind('click',openStaticBar);
 }
function initDefil(){
	startDefil();
	defileur.bind('mouseout',startDefil);
	defileur.bind('mouseover',stopDefil);
}
function startDefil(){
     if(!interval) {
      interval=window.setInterval(doDefil,refresh);
     }
}

function stopDefil(){
     if (interval) {
      window.clearInterval(interval);
      interval=0;
     }
}
function doDefil(){
	var entry = elements[statut];
	$(entry).attr('class','entry');		
	statut++;
	if(statut >= elements.length) statut = 0;
	var entry = elements[statut];
	$(entry).attr('class','entryActive');
}

})(jQuery);


jQuery(document).ready(function($){

	function manageSocialNetworkPanel(){
		$('div#static-toolbar-share-panel').css('display','none');
		var panel = $('div#static-toolbar-social-network-panel');
		panel.toggle('blind');

	}
	function manageSharePanel(){
		$('div#static-toolbar-social-network-panel').css('display','none');
		var panel = $('div#static-toolbar-share-panel');
		panel.toggle('blind');

	}	

	$('ul#static-toolbar-blocs').corner('top');
	$('#static-toolbar').loadDefil();
	$('div#static-toolbar li#static-toolbar-social-network img,div#static-toolbar li#static-toolbar-social-network div#static-toolbar-social-network-panel p span').bind('click',manageSocialNetworkPanel);
	$('div#static-toolbar li#static-toolbar-share img,div#static-toolbar li#static-toolbar-share div#static-toolbar-share-panel p span').bind('click',manageSharePanel);
	if($.browser.msie && $.browser.version.substr(0,1)<7){
		$('#static-toolbar').css('display','none');
	}
});
