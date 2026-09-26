var toggle = document.querySelector('.menu-toggle');
var sidebar = document.querySelector('.sidebar');

function closeMenu() {
  if (sidebar) {
    sidebar.classList.remove('open');
  }

  if (toggle) {
    toggle.setAttribute('aria-expanded', 'false');
  }
}

if (toggle && sidebar) {
  toggle.addEventListener('click', function () {
    var open = sidebar.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
}

document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    closeMenu();
    if (toggle) {
      toggle.focus();
    }
  }
});

document.addEventListener('click', function (event) {
  if (sidebar && toggle && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
    closeMenu();
  }
});

var fileInputs = document.querySelectorAll('input[type="file"]');

for (var i = 0; i < fileInputs.length; i++) {
  fileInputs[i].addEventListener('change', function () {
    var files = this.files;
    this.setCustomValidity('');

    for (var j = 0; j < files.length; j++) {
      if (files[j].size > 20 * 1024 * 1024) {
        this.setCustomValidity('Pojedynczy plik może mieć najwyżej 20 MB.');
        this.reportValidity();
        break;
      }
    }
  });
}
