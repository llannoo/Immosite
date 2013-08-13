// Custom js
// TOP
$(function() {
        $(window).scroll(function() {
            if($(this).scrollTop() > 200) {
                $('#toTop').fadeIn();	
            } else {
                $('#toTop').fadeOut();
            }
        });
     
        $('#toTop').click(function() {
            $('body,html').animate({scrollTop:0},800);
        });	
    });
$(window).load(function() {
	//------MENU-------------
	ddsmoothmenu.init({
	mainmenuid: "smoothmenu1", //menu DIV id
	orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	classname: 'ddsmoothmenu', //class added to menu's outer DIV
	//customtheme: ["#1c5a80", "#18374a"],
	contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	})
	
	//------SELECTED MENU IPAD, IPHONE-------------
	 $(function() {
	   
      $("<select />").appendTo("nav");
      
      $("<option />", {
         "selected": "selected",
         "value"   : "",
         "text"    : "Go to..."
      }).appendTo("nav select");
      
      $("nav a").each(function() {
       var el = $(this);
       $("<option />", {
           "value"   : el.attr("href"),
           "text"    : el.text()
       }).appendTo("nav select");
      });

      $("nav select").change(function() {
        window.location = $(this).find("option:selected").val();
      });
	 
	 });
	
	//------TIPSY TOOLTIP-------------
	$('a.tip').tipsy({fade: true, gravity: 's'});
	
	//------FEATURED SLIDER-------------
	$('.flexslider').flexslider();		
	
	//------PRETTY PHOTO------------- 
	$("a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});
	var $container = $('#works-container');
});

//------LOGIN POP-UP-------------
$(document).ready(function() {
	$('a.login-window').click(function() {
		
                //Getting the variable's value from a link 
		var loginBox = $(this).attr('href');

		//Fade in the Popup
		$(loginBox).fadeIn(300);
		
		//Set the center alignment padding + border see css style
		var popMargTop = ($(loginBox).height() + 24) / 2; 
		var popMargLeft = ($(loginBox).width() + 24) / 2; 
		
		$(loginBox).css({ 
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
		
		// Add the mask to body
		$('body').append('<div id="mask"></div>');
		$('#mask').fadeIn(300);
		
		return false;
	});
	
	// When clicking on the button close or the mask layer the popup closed
	$('a.close, #mask').live('click', function() { 
	  $('#mask , .login-popup').fadeOut(300 , function() {
		$('#mask').remove();  
	}); 
	return false;
	});
});

//------MOSAIC-------------
jQuery(function($){
	$('.circle, .vid, .views').mosaic({
		opacity		:	0.8			//Opacity for overlay (0-1)
	});	    
});

//------LIST WORK-------------
function mycarousel_initCallback(carousel){
    // Disable autoscrolling if the user clicks the prev or next button.
    carousel.buttonNext.bind('click', function() {
        carousel.startAuto(0);
    });
    carousel.buttonPrev.bind('click', function() {
        carousel.startAuto(0);
    });
    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function() {
        carousel.stopAuto();
    }, function() {
        carousel.startAuto();
    });
};
jQuery(document).ready(function() {
    jQuery('#mycarousel').jcarousel({
        auto: 4,
        wrap: 'last',
        initCallback: mycarousel_initCallback
    });
});

//-------------TAB--------------------
//$(function() {        
 //   $("#tab").organicTabs({
  //  	"speed": 200
//	});    
//});

//-------------Quicksand--------------------
(function($) {
	
	$.fn.sorted = function(customOptions) {
		var options = {
			reversed: false,
			by: function(a) {
				return a.text();
			}
		};
		$.extend(options, customOptions);
	
		$data = $(this);
		arr = $data.get();
		arr.sort(function(a, b) {
			
		   	var valA = options.by($(a));
		   	var valB = options.by($(b));
			if (options.reversed) {
				return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;				
			} else {		
				return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;	
			}
		});
		return $(arr);
	};

})(jQuery);

$(function() {
  
  var read_button = function(class_names) {
    var r = {
      selected: false,
      type: 0
    };
    for (var i=0; i < class_names.length; i++) {
      if (class_names[i].indexOf('selected-') == 0) {
        r.selected = true;
      }
      if (class_names[i].indexOf('segment-') == 0) {
        r.segment = class_names[i].split('-')[1];
      }
    };
    return r;
  };
  
  var determine_sort = function($buttons) {
    var $selected = $buttons.parent().filter('[class*="selected-"]');
    return $selected.find('a').attr('data-value');
  };
  
  var determine_kind = function($buttons) {
    var $selected = $buttons.parent().filter('[class*="selected-"]');
    return $selected.find('a').attr('data-value');
  };
  
  var $preferences = {
    duration: 800,
    easing: 'easeInOutQuad',
    adjustHeight: false
  };
  
  var $list = $('#list');
  var $data = $list.clone();
  
  var $controls = $('ul.splitter ul');
  
  $controls.each(function(i) {
    
    var $control = $(this);
    var $buttons = $control.find('a');
    
    $buttons.bind('click', function(e) {
      
      var $button = $(this);
      var $button_container = $button.parent();
      var button_properties = read_button($button_container.attr('class').split(' '));      
      var selected = button_properties.selected;
      var button_segment = button_properties.segment;

      if (!selected) {

        $buttons.parent().removeClass('selected-0').removeClass('selected-1').removeClass('selected-2').removeClass('selected-3').removeClass('selected-4');
        $button_container.addClass('selected-' + button_segment);
        
        var sorting_type = determine_sort($controls.eq(1).find('a'));
        var sorting_kind = determine_kind($controls.eq(0).find('a'));
        
        if (sorting_kind == 'all') {
          var $filtered_data = $data.find('li');
        } else {
          var $filtered_data = $data.find('li.' + sorting_kind);
        }
        
        if (sorting_type == 'size') {
          var $sorted_data = $filtered_data.sorted({
            by: function(v) {
              return parseFloat($(v).find('span').text());
            }
          });
        } else {
          var $sorted_data = $filtered_data.sorted({
            by: function(v) {
              return $(v).find('strong').text().toLowerCase();
            }
          });
        }
        //// add function for lightbox and fade in and out for image.
        $list.quicksand($sorted_data, $preferences, 
		function(){
			//end callback
			imageHoverFade();
			setLightbox();
			
			}
			);
        
      }
      
      e.preventDefault();
    });
    
  }); 

  var high_performance = true;  
  var $performance_container = $('#performance-toggle');
  var $original_html = $performance_container.html();
  
  $performance_container.find('a').live('click', function(e) {
    if (high_performance) {
      $preferences.useScaling = false;
      $performance_container.html('CSS3 scaling turned off. Try the demo again. <a href="#toggle">Reverse</a>.');
      high_performance = false;
    } else {
      $preferences.useScaling = true;
      $performance_container.html($original_html);
      high_performance = true;
    }
    e.preventDefault();
  });
});

// set the the fade in and out of images
function imageHoverFade(){
	
	$('#list li a img').animate({'opacity' : 1}).hover(function() {
		$(this).animate({'opacity' : .2});
	}, function() {
		$(this).animate({'opacity' : 1});
	});
}

/* ---------------------------------------------------------------------- */
/*	Include Javascript Files
/* ---------------------------------------------------------------------- */