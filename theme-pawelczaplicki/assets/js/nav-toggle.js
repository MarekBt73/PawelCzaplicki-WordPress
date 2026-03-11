(() => {
  const toggle = document.querySelector<HTMLButtonElement>(".pc-nav-toggle");
  const nav = document.querySelector<HTMLElement>(".pc-nav");
  if (!toggle || !nav) return;

  const close = () => {
    nav.classList.remove("pc-nav--open");
    document.body.classList.remove("pc-nav-open");
    toggle.setAttribute("aria-expanded", "false");
  };

  toggle.addEventListener("click", () => {
    const isOpen = nav.classList.toggle("pc-nav--open");
    document.body.classList.toggle("pc-nav-open", isOpen);
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 900) {
      close();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      close();
    }
  });
})();

