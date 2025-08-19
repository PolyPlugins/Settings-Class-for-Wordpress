jQuery(document).ready(function ($) {
  // Define variables
  let $hash = $(location).attr('hash').slice(1);
  let toggled = false;

  init();

  // Initialize
  function init() {
    loaded();
    colorPicker();
    errorCleanup();
    prepareTabs();
    switchTabHandling();
    dropdownToggleHandling();
    helperHandling();
    validationHandling();
    inputGroupEqualWidth();
    optionsMinHeight();
  }

  // We used a loader as a placeholder to prevent layout shifting on load
  function loaded() {
    $(".load-settings").remove();

    // Window loaded, lets show settings
    $(".settings-container").show();
  }

  // Initialize Color Picker
  function colorPicker() {
    $('.color-picker').wpColorPicker();
  }

  // Let's clean up all the notices from other plugins to make it look nicer
  function errorCleanup() {
    $(".error").remove();
    $('[class*="notice"]').each(function() {
      let $element = $(this);

      if (!$element.hasClass("settings-error")) {
        $(this).remove();
      }
    });
  }
  
  // Prepare tabs on load
  function prepareTabs() {
    if ($hash) {
      let $selected_section = $hash;

      $(".nav-link").removeClass("active previous");
      $(".nav-link[selected-section=" + $selected_section + "]")
        .addClass("active")
        .prevAll().addClass("previous");

      $(".row.top-bar").text(slug_to_title($selected_section));

      $(".options").each(function() {
        let $section = $(this).attr("section");
        $(this).toggle($section === $selected_section);
      });
    }
  }

  // Process switching tabs
  function switchTabHandling() {
    $(".nav-link").on("click", function(e) {
      let $selected_section = $(this).attr('selected-section');

      // Reset active/previous classes
      $(".nav-link").removeClass("active previous");
      $(this).addClass("active");
      $(this).prevAll().addClass("previous");

      // Update Title Bar
      $(".row.top-bar").text(slug_to_title($selected_section));

      $(".options").each(function() {
        let $section = $(this).attr("section");
        $(this).toggle($section === $selected_section);
      });

      // Close helpers
      toggled = false;
      $(this).closest(".bootstrap-wrapper").find(".col-lg-8").toggleClass("col-lg-10 col-lg-8");
      $(this).closest(".bootstrap-wrapper").find(".col-md-10").toggleClass("col-md-12 col-md-10");
      $(".helper-sidebar").hide();

      inputGroupEqualWidth($selected_section);
    });
  }
  
  // Helper Sidebar
  function helperHandling() {
    $(".helper-icon").click(function() {
      var helper = $(this).parents('.field-container').find('.helper-placeholder').text();
      
      if (!toggled) {
        toggled = true;

        $(".bootstrap-wrapper").find(".col-lg-10").toggleClass("col-lg-10").toggleClass("col-lg-8");
        $(".bootstrap-wrapper").find(".col-md-12").toggleClass("col-md-12").toggleClass("col-md-10");
        $(".helper-sidebar").toggle();
      }
      $(".helper-text").text(helper);
      
      $('html,body').animate({ scrollTop: 0 }, 'fast');
    });

    $(".helper-close").click(function() {
      toggled = false;

      $(".bootstrap-wrapper").find(".col-lg-8").toggleClass("col-lg-10").toggleClass("col-lg-8");
      $(".bootstrap-wrapper").find(".col-md-10").toggleClass("col-md-12").toggleClass("col-md-10");
      $(".helper-sidebar").toggle();
    });
  }

  // Validation
  function validationHandling() {
    $("#submit").click(function(e) {
      let validated = true;

      // Number
      $("[type=number]").each(function() {
        let $number = $(this).val();

        $(this).removeClass('invalid');
        if($number && !validator.isInt($number)) {
          $(this).addClass('invalid');
          $(this).addText('invalid');
          validated = false;
        }
      });

      // Email
      $("[type=email]").each(function() {
        let $email = $(this).val();

        $(this).removeClass('invalid');
        if($email && !validator.isEmail($email)) {
          $(this).addClass('invalid');
          $(this).addText('invalid');
          validated = false;
        }
      });

      // URL
      $("[type=url]").each(function() {
        let $url = $(this).val();

        $(this).removeClass('invalid');
        if($url && !validator.isURL($url)) {
          $(this).addClass('invalid');
          $(this).addText('invalid');
          validated = false;
        }
      });
      
      if (!validated) {
        e.preventDefault();
      }
    });
  }
  
  // Dropdown Toggle Initial Load Handling
  function dropdownToggleHandling() {
    $('.toggle').each(function() {
      $(this).find('.togglable').hide();
      let dropdown_value = $(this).find('.toggle-select').val() ? $(this).find('.toggle-select').val().toLowerCase() : '';

      if (!dropdown_value) {
        return;
      }

      $('.field-container' + '.' + dropdown_value).each(function() {
        $(this).show();
        
        if($(this)[0] === $('.field-container' + '.' + dropdown_value).last()[0]) {
          $(this).css("margin-bottom", 0)
        }
      });
    });

    // Dropdown Toggle Change Handling
    $('.toggle-select').on('change', function() {
      let dropdown_value = $(this).val().toLowerCase();
      let section        = $(this).closest('[section]').attr('section');

      $(this).parent().parent().find('.togglable').each(function() {
        $(this).hide();
      });

      $('.field-container' + '.' + dropdown_value).each(function() {
        $(this).show();

        if($(this)[0] === $('.field-container' + '.' + dropdown_value).last()[0]) {
          $(this).css("margin-bottom", 0)
        }
      });

      // Lets set the group width
      inputGroupEqualWidth(section);
    });
  }
  
  function inputGroupEqualWidth(selected_section = '') {
    let input_group_text_max_width = 0;
    let $active_tab = $(".nav-link.active").attr('selected-section');
  
    let $selected_section_inputs = selected_section
      ? $("[section='" + selected_section + "']").find('.field-container-text:visible')
      : $("[section='" + $active_tab + "']").find('.field-container-text:visible');
  
    $selected_section_inputs.css("width", "auto");
  
    $selected_section_inputs.each(function() {
      let input_group_width = $(this).outerWidth();
  
      if (input_group_width > input_group_text_max_width) {
        input_group_text_max_width = input_group_width;
      }
    });
  
    if ($selected_section_inputs.length > 1) {
      $selected_section_inputs.css("width", input_group_text_max_width + "px");
    }
  }

  function optionsMinHeight() {
    let $tab_height = $(".tabs").height();
    $(".options").css("min-height", $tab_height);
  }

  function slug_to_title(str) {
    var split_str = str.split('-');
    
    for (var i = 0; i < split_str.length; i++) {
      split_str[i] = split_str[i].toUpperCase();     
    }

    return split_str.join(' '); 
  }
});
