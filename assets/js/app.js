var toggle = document.querySelector('.menu-toggle');
var sidebar = document.querySelector('.sidebar');

if (toggle && sidebar) {
  toggle.addEventListener('click', function () {
    var open = sidebar.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
}
