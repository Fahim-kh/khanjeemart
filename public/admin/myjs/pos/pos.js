 "use strict";

 document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("fullscreen-toggle");
    if (!toggleBtn) {
      console.warn("#fullscreen-toggle not found in DOM.");
      return;
    }
  
    const icon = toggleBtn.querySelector("i");
    // helper to set icon safely
    function setIcon(isFullscreen) {
      if (!icon) return;
      // use classes explicitly to avoid leftover classes
      icon.classList.remove("fa-expand", "fa-compress-arrows-alt", "fa-compress");
      if (isFullscreen) {
        // If compress-arrows-alt doesn't exist in your FA pack, use 'fa-compress' instead
        icon.classList.add("fa-compress-arrows-alt");
      } else {
        icon.classList.add("fa-expand");
      }
    }
  
    // Choose target element for fullscreen. To fullscreen only the POS container:
    // const targetElement = document.getElementById('pos-container') || document.documentElement;
    const targetElement = document.documentElement; // full page
  
    toggleBtn.addEventListener("click", async function () {
      try {
        if (!document.fullscreenElement) {
          if (targetElement.requestFullscreen) {
            await targetElement.requestFullscreen();
          } else if (targetElement.webkitRequestFullscreen) { // Safari
            await targetElement.webkitRequestFullscreen();
          } else {
            console.warn("Fullscreen API is not supported by this browser.");
            return;
          }
          setIcon(true);
        } else {
          if (document.exitFullscreen) {
            await document.exitFullscreen();
          } else if (document.webkitExitFullscreen) {
            await document.webkitExitFullscreen();
          }
          setIcon(false);
        }
      } catch (err) {
        console.error("Failed to toggle fullscreen:", err);
      }
    });
  
    // keep icon in sync if user presses ESC or otherwise exits fullscreen
    document.addEventListener("fullscreenchange", function () {
      setIcon(!!document.fullscreenElement);
    });
  
    // also cover webkit prefixed event
    document.addEventListener("webkitfullscreenchange", function () {
      setIcon(!!document.fullscreenElement);
    });
  });