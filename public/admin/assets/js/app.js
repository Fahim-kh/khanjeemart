(function ($) {
  'use strict';

  // sidebar submenu collapsible js
  $(".sidebar-menu .dropdown").on("click", function(){
    var item = $(this);
    item.siblings(".dropdown").children(".sidebar-submenu").slideUp();
    item.siblings(".dropdown").removeClass("dropdown-open");
    item.siblings(".dropdown").removeClass("open");
    item.children(".sidebar-submenu").slideToggle();
    item.toggleClass("dropdown-open");
  });

  $(".sidebar-toggle").on("click", function(){
    $(this).toggleClass("active");
    $(".sidebar").toggleClass("active");
    $(".dashboard-main").toggleClass("active");
  });

  $(".sidebar-mobile-toggle").on("click", function(){
    $(".sidebar").addClass("sidebar-open");
    $("body").addClass("overlay-active");
  });

  $(".sidebar-close-btn").on("click", function(){
    $(".sidebar").removeClass("sidebar-open");
    $("body").removeClass("overlay-active");
  });

  //to keep the current page active
  $(function () {
    for (
      var nk = window.location,
        o = $("ul#sidebar-menu a")
          .filter(function () {
            return this.href == nk;
          })
          .addClass("active-page") // anchor
          .parent()
          .addClass("active-page");
      ;
    ) {
      // li
      if (!o.is("li")) break;
      o = o.parent().addClass("show").parent().addClass("open");
    }
  });

  /**
  * Utility function to get a cookie value by name
  */
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
  }

  /**
  * Utility function to set a cookie
  */
  function setCookie(name, value, days = 365) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value}; ${expires}; path=/`;
  }

  /**
  * Utility function to calculate the current theme setting.
  * Checks localStorage first, then cookies, then defaults to light.
  */
  function calculateSettingAsThemeString() {
    // First check localStorage
    const localStorageTheme = localStorage.getItem("theme");
    if (localStorageTheme !== null) {
      return localStorageTheme;
    }
    
    // Then check cookies
    const cookieTheme = getCookie("theme");
    if (cookieTheme !== null) {
      return cookieTheme;
    }
    
    // Default to light theme
    return "light";
  }

  /**
  * Utility function to update the button text and aria-label.
  */
  function updateButton({ buttonEl, isDark }) {
    const newCta = isDark ? "dark" : "light";
    buttonEl.setAttribute("aria-label", newCta);
    buttonEl.innerText = newCta;
  }

  /**
  * Utility function to update the theme setting on the html tag.
  */
  function updateThemeOnHtmlEl({ theme }) {
    document.querySelector("html").setAttribute("data-theme", theme);
  }

  /**
  * 1. Grab what we need from the DOM
  */
  const button = document.querySelector("[data-theme-toggle]");

  /**
  * 2. Determine the current theme setting
  */
  let currentThemeSetting = calculateSettingAsThemeString();

  /**
  * 3. Update UI based on current theme
  */
  if (button) {
    updateButton({ buttonEl: button, isDark: currentThemeSetting === "dark" });
    updateThemeOnHtmlEl({ theme: currentThemeSetting });

    /**
    * 4. Add theme toggle functionality
    */
    button.addEventListener("click", (event) => {
      const newTheme = currentThemeSetting === "dark" ? "light" : "dark";

      // Save preference to both localStorage and cookie
      localStorage.setItem("theme", newTheme);
      setCookie("theme", newTheme);
      
      // Update UI
      updateButton({ buttonEl: button, isDark: newTheme === "dark" });
      updateThemeOnHtmlEl({ theme: newTheme });

      currentThemeSetting = newTheme;
    });
  } else {
    // If no button is found, just apply the current theme
    updateThemeOnHtmlEl({ theme: currentThemeSetting });
  }

  // =========================== Table Header Checkbox checked all js Start ================================
  $('#selectAll').on('change', function () {
    $('.form-check .form-check-input').prop('checked', $(this).prop('checked')); 
  }); 

  // Remove Table Tr when click on remove btn start
  $('.remove-btn').on('click', function () {
    $(this).closest('tr').remove(); 

    // Check if the table has no rows left
    if ($('.table tbody tr').length === 0) {
      $('.table').addClass('bg-danger');
      // Show notification
      $('.no-items-found').show();
    }
  });
  // Remove Table Tr when click on remove btn end
})(jQuery);