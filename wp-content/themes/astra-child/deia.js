// Fix Astra Main Menu Submenu issue not been clickable on the text area only the arrow
document.addEventListener("DOMContentLoaded", function () {
  const menuItems = document.querySelectorAll(
    ".main-navigation .menu-item-has-children > a"
  );

  //   console.log("Menu items found:", menuItems.length);

  menuItems.forEach((menuItem) => {
    menuItem.addEventListener("click", function (event) {
      event.preventDefault();
      const parent = this.parentElement;
      const submenu = parent.querySelector(".sub-menu");

      //   console.log("Clicked menu item:", this);
      //   console.log("Submenu found:", submenu);

      if (submenu) {
        if (submenu.classList.contains("submenu-open")) {
          submenu.style.display = "none";
          submenu.classList.remove("submenu-open");
          this.setAttribute("aria-expanded", "false");
        } else {
          submenu.style.display = "block";
          submenu.classList.add("submenu-open");
          this.setAttribute("aria-expanded", "true");
        }
      }
    });
  });

  // Initialize FancyBox
  if (typeof jQuery !== "undefined") {
    jQuery(document).ready(function ($) {
      $('[data-fancybox="gallery"]').fancybox({
        buttons: ["slideShow", "share", "zoom", "fullScreen", "close"],
      });
    });
  }
});
