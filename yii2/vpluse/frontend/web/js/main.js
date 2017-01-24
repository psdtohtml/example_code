$(document).ready(function(){

  setTimeout(function() {
    $('.sidebar, .content, .content__addit, .content__addit_gray').css('transition','0.4s');
  },400);


  var statusSidebar = localStorage.getItem("sidebar");
      statusMenu = localStorage.getItem('js-drop'),
      submenu = localStorage.getItem('submenu-active'),
      $this = $('.sidebar__item').eq(statusMenu) || null,
      statusSubmenu = null;


  if(statusMenu != null) {

    if($('.sidebar__item').eq(statusMenu).hasClass('js-drop')) {
          statusSubmenu = 'open';
      if(statusSidebar == 'sidebar_open') {
        $('.sidebar__item').eq(statusMenu).addClass('active').next().attr('data-opened', true).show();
      } else {
        $('.sidebar__item').eq(statusMenu).addClass('active-empty');
      }
    } else {
      $('.sidebar__item').eq(statusMenu).addClass('active-empty');
    }
    if(submenu !== null) {
      $('.sidebar__item').eq(statusMenu).next().find('.dropdown__row').eq(submenu).find('.dropdown__item').addClass('active');
    }
  }


    function heightSidebars () {
    if($(window).outerHeight() < $('.wrapper').outerHeight()-$('.header').outerHeight()) {
      $('.sidebar').outerHeight($('.wrapper').outerHeight()-$('.header').outerHeight());
    }
    else {
      $('.sidebar').outerHeight($(window).outerHeight());
      $('body').outerHeight($(window).outerHeight());
    }
  }
  window.addEventListener('load', heightSidebars);
  window.addEventListener('resize', heightSidebars);


//check the availability of the right sidebar
  if($('.content__addit_gray').length) {
    $('.content').addClass('second-page');
  } else {
    $('.content__main').css('width','64%');
  }







  function openSidebarItem ($this) {

    if($this.attr('data-toggle-button')) {
      var toggleBlock = $('[data-toggle-block="' + $this.attr('data-toggle-button') + '"]');
    }
    else {
      var toggleBlock = $this.next();
    }

    function effectShow(element) {
      var effect = element.attr('data-effect');
      if(effect == 'fade') element.fadeIn();
      else if(effect == 'slide') element.slideDown();
      else element.show();
    }
    function effectHide(element) {
      var effect = element.attr('data-effect');
      if(effect == 'fade') element.fadeOut();
      else if(effect == 'slide') element.slideUp();
      else element.hide();
    }

    if(toggleBlock.is(":hidden")) {
      $this.addClass('active');
    } else {
      $this.removeClass('active').addClass('active-empty');
    }


    $("[data-opened]").each(function() {
      effectHide($(this));
    });
    $("[data-opened]").removeAttr('data-opened');
    if($this.hasClass('active')) {
      toggleBlock.attr("data-opened", true);
      effectShow(toggleBlock);
    }
  }






// sidebar__nav
  $('.sidebar__item').on('click', function(event) {
    event.stopPropagation();

    $this = $(this);

    localStorage.setItem('js-drop',$('.sidebar__item').index($this));
    localStorage.setItem('sidebar',$('body').attr('class'));
    $('.sidebar__item').removeClass('active').removeClass('active-empty');
    $('.dropdown__item').removeClass('active');

    if(!$this.hasClass('js-drop')) {
      $(this).addClass('active-empty');
      statusSubmenu = 'close';
    } else {
      setTimeout(function() {
        if($this.hasClass('active')) {
          statusSubmenu = 'open';
        } else {
          statusSubmenu = 'close';
        }
      },400);
    }


    if($('body').hasClass('sidebar_close')) {
      $('body').removeClass('sidebar_close').addClass('sidebar_open');
      setTimeout(function() {
        openSidebarItem($this);
        localStorage.setItem('sidebar', 'sidebar_open');
      },250);
    } else {
      openSidebarItem($this);
    }

    localStorage.setItem('submenu-active',null);
  });


  //DROPDOWN__ITEM CLICK
  $('.dropdown__item').on('click', function() {
    $('.dropdown__item').removeClass('active');
    $('.sidebar__item').removeClass('active').removeClass('active-empty');

    $(this).addClass('active').closest('.sidebar__row').find('.sidebar__item').addClass('active');
    $this = $(this).closest('.dropdown').prev();
    localStorage.setItem('js-drop',$('.sidebar__item').index($(this).closest('.dropdown').prev()));
    localStorage.setItem('submenu-active',$(this).parent().parent().find('.dropdown__row').index($(this).parent()));
  });


// scrollbar 
$(".dropdown").mCustomScrollbar({
    theme:"dark",
    axis:"y"
});

// selects
$(".js-select").select2();

// select form send
$(".form-send .js-select").select2({
    placeholder: "Выберите компанию"
  });


// sidebar
  (function() {
    var $body = document.body,
        $menu_trigger = $body.getElementsByClassName('show-menu')[0];

    if(localStorage.getItem('sidebar') == null) {
      $('body').addClass('sidebar_open');
      if($(window).outerWidth() < 1100) {
        $('body').removeClass('sidebar_open').addClass('sidebar_close');
      }
    } else {
      $('body').addClass(localStorage.getItem('sidebar'));
    }


    if ( typeof $menu_trigger !== 'undefined' ) {
      $menu_trigger.addEventListener('click', function() {
        $body.className = ( $body.className == 'sidebar_open' )? 'sidebar_close' : 'sidebar_open';
        localStorage.setItem('sidebar', $body.className);


        if($this != null || statusMenu != null) {

          if($this == null && statusMenu != null) {
            $this = $('.sidebar__item').eq(statusMenu);
            openSidebarItem($this);
          } else {
            if($this.next().attr('data-opened')) {
              openSidebarItem($this);
            }
          }


          setTimeout(function(){
            if($('body').hasClass('sidebar_open') && statusSubmenu == 'open' && $this.hasClass('js-drop')) {
              $this.removeClass('active-empty').addClass('active');
              openSidebarItem($this);
            }
          },400);
        }



        setTimeout(function() {
          if($(window).outerHeight() < $('.wrapper').outerHeight()-$('.header').outerHeight()) {
            $('.sidebar').outerHeight($('.wrapper').outerHeight()-$('.header').outerHeight());
          }
          else {
            $('.sidebar').outerHeight($(window).outerHeight());
            $('body').outerHeight($(window).outerHeight());
          }
        },400);

      });
    }

  }).call(this);




  $('.logout, .logo').on('click', function() {
    localStorage.clear();
  });
}); // end jquery



/*!
 * iOS doesn't respect the meta viewport tag inside a frame
 * add a link to the debug view (for demo purposes only)
 */
if (/(iPhone|iPad|iPod)/gi.test(navigator.userAgent) && window.location.pathname.indexOf('/full') > -1) {
  var p = document.createElement('p');
  p.innerHTML = '<a target="_blank" href="http://s.codepen.io/dbushell/debug/wGaamR"><b>Click here to view this demo properly on iOS devices (remove the top frame)</b></a>';
  document.body.insertBefore(p, document.body.querySelector('h1'));
}