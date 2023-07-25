jQuery(document).ready(function($) {

	$.fn.theiaStickySidebar=function(i){function t(i,t){return!0===i.initialized||!($("body").width()<i.minWidth)&&(function(i,t){i.initialized=!0,0===$("#theia-sticky-sidebar-stylesheet-"+i.namespace).length&&$("head").append($('<style id="theia-sticky-sidebar-stylesheet-'+i.namespace+'">.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>'));t.each(function(){var t={};if(t.sidebar=$(this),t.options=i||{},t.container=$(t.options.containerSelector),0==t.container.length&&(t.container=t.sidebar.parent()),t.sidebar.parents().css("-webkit-transform","none"),t.sidebar.css({position:t.options.defaultPosition,overflow:"visible","-webkit-box-sizing":"border-box","-moz-box-sizing":"border-box","box-sizing":"border-box"}),t.stickySidebar=t.sidebar.find(".theiaStickySidebar"),0==t.stickySidebar.length){var o=/(?:text|application)\/(?:x-)?(?:javascript|ecmascript)/i;t.sidebar.find("script").filter(function(i,t){return 0===t.type.length||t.type.match(o)}).remove(),t.stickySidebar=$("<div>").addClass("theiaStickySidebar").append(t.sidebar.children()),t.sidebar.append(t.stickySidebar)}t.marginBottom=parseInt(t.sidebar.css("margin-bottom")),t.paddingTop=parseInt(t.sidebar.css("padding-top")),t.paddingBottom=parseInt(t.sidebar.css("padding-bottom"));var a=t.stickySidebar.offset().top,n=t.stickySidebar.outerHeight();function s(){t.fixedScrollTop=0,t.sidebar.css({"min-height":"1px"}),t.stickySidebar.css({position:"static",width:"",transform:"none"})}t.stickySidebar.css("padding-top",1),t.stickySidebar.css("padding-bottom",1),a-=t.stickySidebar.offset().top,n=t.stickySidebar.outerHeight()-n-a,0==a?(t.stickySidebar.css("padding-top",0),t.stickySidebarPaddingTop=0):t.stickySidebarPaddingTop=1,0==n?(t.stickySidebar.css("padding-bottom",0),t.stickySidebarPaddingBottom=0):t.stickySidebarPaddingBottom=1,t.previousScrollTop=null,t.fixedScrollTop=0,s(),t.onScroll=function(t){if(t.stickySidebar.is(":visible"))if($("body").width()<t.options.minWidth)s();else{if(t.options.disableOnResponsiveLayouts){var o=t.sidebar.outerWidth("none"==t.sidebar.css("float"));if(o+50>t.container.width())return void s()}var a,n,d=$(document).scrollTop(),r="static";if(d>=t.sidebar.offset().top+(t.paddingTop-t.options.additionalMarginTop)){var c,p=t.paddingTop+i.additionalMarginTop,b=t.paddingBottom+t.marginBottom+i.additionalMarginBottom,l=t.sidebar.offset().top,f=t.sidebar.offset().top+(a=t.container,n=a.height(),a.children().each(function(){n=Math.max(n,$(this).height())}),n),h=0+i.additionalMarginTop,g=t.stickySidebar.outerHeight()+p+b<$(window).height();c=g?h+t.stickySidebar.outerHeight():$(window).height()-t.marginBottom-t.paddingBottom-i.additionalMarginBottom;var S=l-d+t.paddingTop,u=f-d-t.paddingBottom-t.marginBottom,m=t.stickySidebar.offset().top-d,y=t.previousScrollTop-d;"fixed"==t.stickySidebar.css("position")&&"modern"==t.options.sidebarBehavior&&(m+=y),"stick-to-top"==t.options.sidebarBehavior&&(m=i.additionalMarginTop),"stick-to-bottom"==t.options.sidebarBehavior&&(m=c-t.stickySidebar.outerHeight()),m=y>0?Math.min(m,h):Math.max(m,c-t.stickySidebar.outerHeight()),m=Math.max(m,S),m=Math.min(m,u-t.stickySidebar.outerHeight());var k=t.container.height()==t.stickySidebar.outerHeight();r=(k||m!=h)&&(k||m!=c-t.stickySidebar.outerHeight())?d+m-t.sidebar.offset().top-t.paddingTop<=i.additionalMarginTop?"static":"absolute":"fixed"}if("fixed"==r){var v=$(document).scrollLeft();t.stickySidebar.css({position:"fixed",width:e(t.stickySidebar)+"px",transform:"translateY("+m+"px)",left:t.sidebar.offset().left+parseInt(t.sidebar.css("padding-left"))-v+"px",top:"0px"})}else if("absolute"==r){var x={};"absolute"!=t.stickySidebar.css("position")&&(x.position="absolute",x.transform="translateY("+(d+m-t.sidebar.offset().top-t.stickySidebarPaddingTop-t.stickySidebarPaddingBottom)+"px)",x.top="0px"),x.width=e(t.stickySidebar)+"px",x.left="",t.stickySidebar.css(x)}else"static"==r&&s();"static"!=r&&1==t.options.updateSidebarHeight&&t.sidebar.css({"min-height":t.stickySidebar.outerHeight()+t.stickySidebar.offset().top-t.sidebar.offset().top+t.paddingBottom}),t.previousScrollTop=d}},t.onScroll(t),$(document).on("scroll."+t.options.namespace,function(i){return function(){i.onScroll(i)}}(t)),$(window).on("resize."+t.options.namespace,function(i){return function(){i.stickySidebar.css({position:"static"}),i.onScroll(i)}}(t)),"undefined"!=typeof ResizeSensor&&new ResizeSensor(t.stickySidebar[0],function(i){return function(){i.onScroll(i)}}(t))})}(i,t),!0)}function e(i){var t;try{t=i[0].getBoundingClientRect().width}catch(i){}return void 0===t&&(t=i.width()),t}return(i=$.extend({containerSelector:"",additionalMarginTop:0,additionalMarginBottom:0,updateSidebarHeight:!0,minWidth:0,disableOnResponsiveLayouts:!0,sidebarBehavior:"modern",defaultPosition:"relative",namespace:"TSS"},i)).additionalMarginTop=parseInt(i.additionalMarginTop)||0,i.additionalMarginBottom=parseInt(i.additionalMarginBottom)||0,function(i,e){t(i,e)||(console.log("TSS: Body width smaller than options.minWidth. Init is delayed."),$(document).on("scroll."+i.namespace,function(i,e){return function(o){var a=t(i,e);a&&$(this).unbind(o)}}(i,e)),$(window).on("resize."+i.namespace,function(i,e){return function(o){var a=t(i,e);a&&$(this).unbind(o)}}(i,e)))}(i,this),this};

	var configurator = $('.dtc-wrapper');
	var form = configurator.find('form.ht-configurator');
	var image_wrapper = configurator.find('.dtc-image-wrapper');


	htc_resize();
	htc_form_change();



	// Listen for changes in any input field within the form and trigger 'submit' event
	form.find('input, select').on('change select', function() {
		// form.trigger('submit');
		htc_form_change();
	});

	function htc_form_change() {
		var form_fields = {};
		$.each(form.serializeArray(), function (index, item) {
			// Initialize as an empty array if undefined
			if (!form_fields[item.name]) {
				form_fields[item.name] = [];
			}

			form_fields[item.name].push(item.value);
		});

		configurator.addClass('htc-loading');

		var data = {
			action: 'htc_form_change', 
			form_fields: form_fields,
	};

		$.post(
			ht_configurator.ajaxurl, 
			data, 
			function () {}
		)
		.always(function () {
			// configurator.removeClass('htc-loading');
		})
		.done(function (response) {
			// load image
			if(response.data.image_url) {
				configurator.find('.dtc-image').attr('src', response.data.image_url)
			} else {
				configurator.removeClass('htc-loading');
			}

			// set price
			if(response.data.price) {
				configurator.find('.dtc-total-price').text(response.data.price)
			}

		});
	}

	$('img.dtc-image').on('load', function() {
		configurator.removeClass('htc-loading');
	})

	$(window).on('resize', function() {
		htc_resize();
	})

	$('.dtc-fieldset-popup-open').on('click', function(e) {
		e.preventDefault();
		$(this).closest('fieldset').find('.dtc-fieldset-popup').show();
	})

	$('.dtc-fieldset-popup-ok').on('click', function(e) {
		e.preventDefault();
		$(this).closest('.dtc-fieldset-popup').hide();
	})









	function htc_resize() {
		var element_top = configurator.offset().top;
		var viewport_height = $(window).height();
		var distance_to_screen_bottom = viewport_height - element_top;
		configurator.height(distance_to_screen_bottom);
	} 

});