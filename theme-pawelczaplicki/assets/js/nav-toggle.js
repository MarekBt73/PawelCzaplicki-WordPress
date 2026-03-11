(() => {
  var toggle = document.querySelector(".pc-nav-toggle");
  var nav = document.querySelector(".pc-nav");
  if (!toggle || !nav) return;

  function close() {
    nav.classList.remove("pc-nav--open");
    document.body.classList.remove("pc-nav-open");
    toggle.setAttribute("aria-expanded", "false");
  }

  toggle.addEventListener("click", function () {
    var isOpen = nav.classList.toggle("pc-nav--open");
    document.body.classList.toggle("pc-nav-open", isOpen);
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth > 900) {
      close();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      close();
    }
  });
})();

