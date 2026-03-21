(() => {
  // Mobilne menu w headerze Protokół 17:00™ na podstronach
  var mobileToggle = document.getElementById("p17-mobile-toggle");
  var mobileNav = document.querySelector(".p17-nav-mobile");
  if (!mobileToggle || !mobileNav) return;

  function close() {
    if (!mobileNav.classList.contains("hidden")) {
      mobileNav.classList.add("hidden");
    }
    mobileToggle.setAttribute("aria-expanded", "false");
  }

  mobileToggle.addEventListener("click", function () {
    var isOpen = !mobileNav.classList.contains("hidden");
    if (isOpen) {
      mobileNav.classList.add("hidden");
      mobileToggle.setAttribute("aria-expanded", "false");
    } else {
      mobileNav.classList.remove("hidden");
      mobileToggle.setAttribute("aria-expanded", "true");
    }
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth >= 768) {
      close();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      close();
    }
  });
})();
