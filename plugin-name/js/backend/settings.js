(function($) {
  $(window).load(function() {
    // We used a loader as a placeholder to prevent layout shifting on load
    $(".load-settings").remove();

    // Window loaded, lets show settings
    $(".settings-container").show();

    let $hash = $(location).attr('hash').slice(1);
    let toggled = false;

    if ($hash) {
      let $selected_section_section = $hash;

      // Add active class and add handling for before and after active element
      $(".nav-link[selected-section=" + $selected_section_section + "]").prevAll().removeClass('active');
      $(".nav-link[selected-section=" + $selected_section_section + "]").prevAll().addClass('previous');
      $(".nav-link[selected-section=" + $selected_section_section + "]").removeClass('previous');
      $(".nav-link[selected-section=" + $selected_section_section + "]").addClass('active');
      $(".nav-link[selected-section=" + $selected_section_section + "]").nextAll().removeClass('active');
      $(".nav-link[selected-section=" + $selected_section_section + "]").nextAll().removeClass('previous');

      // Update Title Bar
      $(".row.top-bar").text(slug_to_title($selected_section_section));

      // Handles displaying clicked tab while hiding others
      $(".options").each(function() {
        let $section_element = $(this);
        let $section = $(this).attr('section');

        if ($section != $selected_section_section) {
          $section_element.hide();
        } else {
          $section_element.show();
        }
      });
    }
    
    $(".nav-link").on("click", function(e) {
      let $selected_section_element = $(this);
      let $selected_section_section = $selected_section_element.attr('selected-section');

      // Add active class and add handling for before and after active element
      $(".nav-link[selected-section=" + $selected_section_section + "]").prevAll().removeClass('active');
      $(".nav-link[selected-section=" + $selected_section_section + "]").prevAll().addClass('previous');
      $(".nav-link[selected-section=" + $selected_section_section + "]").removeClass('previous');
      $(".nav-link[selected-section=" + $selected_section_section + "]").addClass('active');
      $(".nav-link[selected-section=" + $selected_section_section + "]").nextAll().removeClass('active');
      $(".nav-link[selected-section=" + $selected_section_section + "]").nextAll().removeClass('previous');

      // Update Title Bar
      $(".row.top-bar").text(slug_to_title($selected_section_section));

      // Handles displaying clicked tab while hiding others
      $(".options").each(function() {
        let $section_element = $(this);
        let $section = $(this).attr('section');

        if ($section != $selected_section_section) {
          $section_element.hide();
        } else {
          $section_element.show();
        }
      });

      // Close helpers
      toggled = false;

      $(this).parent().parent().parent().find(".col-lg-8").toggleClass("col-lg-10").toggleClass("col-lg-8");
      $(this).parent().parent().parent().find(".col-md-10").toggleClass("col-md-12").toggleClass("col-md-10");
      $(".helper-sidebar").hide();
    });

    // Helper Sidebar
    $(".helper-icon").click(function() {
      var helper = $(this).parent().parent().text();
      if (!toggled) {
        toggled = true;

        $(this).parent().parent().parent().parent().toggleClass("col-lg-10").toggleClass("col-lg-8");
        $(this).parent().parent().parent().parent().toggleClass("col-md-12").toggleClass("col-md-10");
        $(".helper-sidebar").toggle();
      }
      $(".helper-text").text(helper);
      
      $('html').animate({ scrollTop: 0 }, 'fast');
    });

    $(".helper-close").click(function() {
      toggled = false;

      $(this).parent().parent().parent().find(".col-lg-8").toggleClass("col-lg-10").toggleClass("col-lg-8");
      $(this).parent().parent().parent().find(".col-md-10").toggleClass("col-md-12").toggleClass("col-md-10");
      $(".helper-sidebar").toggle();
    });

    // Toasts
    $(".helper").click(function() {
      $(".toast").each(function() {
        $(this).addClass("hide")
      });
      $(this).parent().parent().find('.toast').toast('show');
    });

    function slug_to_title(str) {
      var split_str = str.split('-');
      
      for (var i = 0; i < split_str.length; i++) {
        split_str[i] = split_str[i].toUpperCase();     
      }

      return split_str.join(' '); 
    }

  });
})(jQuery);
