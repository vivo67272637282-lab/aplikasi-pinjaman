document.addEventListener('DOMContentLoaded', function () {
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!reduceMotion) {
    document.addEventListener('click', function (event) {
      var target = event.target.closest(
        '.btn:not(:disabled), button:not(:disabled, .btn-close), .sidebar-link, .logout-btn, .page-link, .searchable-option'
      );

      if (!target || target.getAttribute('aria-disabled') === 'true') return;

      var bounds = target.getBoundingClientRect();
      var diameter = Math.max(bounds.width, bounds.height);
      var ripple = document.createElement('span');

      ripple.className = 'click-ripple';
      ripple.style.width = ripple.style.height = diameter + 'px';
      ripple.style.left = (event.clientX - bounds.left - diameter / 2) + 'px';
      ripple.style.top = (event.clientY - bounds.top - diameter / 2) + 'px';
      target.appendChild(ripple);

      ripple.addEventListener('animationend', function () {
        ripple.remove();
      }, { once: true });
    });
  }

  var btn = document.getElementById('sidebarToggle');
  if (btn) {
    btn.addEventListener('click', function () {
      var sidebar = document.getElementById('sidebar');
      if (sidebar) sidebar.classList.toggle('d-none');
    });
  }

  document.querySelectorAll('.searchable-select-wrap').forEach(function (wrapper) {
    var input = wrapper.querySelector('.searchable-select-input');
    var list = wrapper.querySelector('.searchable-select-list');
    var hidden = wrapper.querySelector('input[type="hidden"]');
    var options = wrapper.querySelectorAll('.searchable-option');

    if (!input || !list || !hidden) return;

    function applySelection(value, label) {
      hidden.value = value;
      input.value = label;
      list.classList.add('d-none');
    }

    if (!hidden.value) {
      input.value = '';
    } else {
      var selected = Array.from(options).find(function (option) {
        return option.dataset.value === hidden.value;
      });
      if (selected) {
        input.value = selected.dataset.label;
      }
    }

    input.addEventListener('focus', function () {
      list.classList.remove('d-none');
    });

    input.addEventListener('input', function () {
      var keyword = input.value.toLowerCase();
      list.classList.remove('d-none');

      options.forEach(function (option) {
        var text = option.dataset.label.toLowerCase();
        option.style.display = text.indexOf(keyword) !== -1 ? 'block' : 'none';
      });
    });

    options.forEach(function (option) {
      option.addEventListener('click', function () {
        applySelection(option.dataset.value, option.dataset.label);
        hidden.dispatchEvent(new Event('change'));
      });
    });

    document.addEventListener('click', function (event) {
      if (!wrapper.contains(event.target)) {
        list.classList.add('d-none');
      }
    });
  });

  function formatCurrency(value) {
    var v = (value || '').replace(/[^0-9]/g, '');
    if (!v) return '';
    return v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  document.querySelectorAll('input.currency').forEach(function (el) {
    el.value = formatCurrency(el.value);

    el.addEventListener('input', function () {
      var start = el.selectionStart;
      var oldLength = el.value.length;
      el.value = formatCurrency(el.value);
      var newLength = el.value.length;
      el.selectionStart = el.selectionEnd = start + (newLength - oldLength);
    });
  });

  function formatReadOnlyEmployeeInfo() {
    var hidden = document.querySelector('input[name="karyawan_id"]');
    if (!hidden) return;

    var total = document.getElementById('karyawan_total_pinjaman');
    var kasbon = document.getElementById('karyawan_sisa_kasbon');
    var wajib = document.getElementById('karyawan_simpanan_wajib');
    var sukarela = document.getElementById('karyawan_simpanan_sukarela');

    var potonganPinjaman = document.querySelector('input[name="potongan_pinjaman"]');
    var potonganKasbon = document.querySelector('input[name="potongan_kasbon"]');
    var potonganWajib = document.querySelector('input[name="potongan_simpanan_wajib"]');
    var potonganSukarela = document.querySelector('input[name="potongan_simpanan_sukarela"]');

    if (!total || !kasbon || !wajib || !sukarela) return;

    function applySelection(value) {
      var option = document.querySelector('.searchable-option[data-value="' + value + '"]');
      if (!option) {
        total.value = '0';
        kasbon.value = '0';
        wajib.value = '0';
        sukarela.value = '0';
        if (potonganPinjaman) potonganPinjaman.value = '0';
        if (potonganKasbon) potonganKasbon.value = '0';
        if (potonganWajib) potonganWajib.value = '0';
        if (potonganSukarela) potonganSukarela.value = '0';
        return;
      }

      var totalPinjaman = Number(option.dataset.totalPinjaman || 0);
      var sisaKasbon = Number(option.dataset.sisaKasbon || 0);
      var simpananWajib = Number(option.dataset.simpananWajib || 0);
      var simpananSukarela = Number(option.dataset.simpananSukarela || 0);

      total.value = formatCurrency(String(totalPinjaman));
      kasbon.value = formatCurrency(String(sisaKasbon));
      wajib.value = formatCurrency(String(simpananWajib));
      sukarela.value = formatCurrency(String(simpananSukarela));

      if (potonganPinjaman) potonganPinjaman.value = formatCurrency(String(totalPinjaman));
      if (potonganKasbon) potonganKasbon.value = formatCurrency(String(sisaKasbon));
      if (potonganWajib) potonganWajib.value = formatCurrency(String(simpananWajib));
      if (potonganSukarela) potonganSukarela.value = formatCurrency(String(simpananSukarela));
    }

    hidden.addEventListener('change', function () {
      applySelection(hidden.value);
    });

    if (hidden.value) {
      applySelection(hidden.value);
    }
  }

  formatReadOnlyEmployeeInfo();

  document.querySelectorAll('form').forEach(function (f) {
    f.addEventListener('submit', function () {
      f.querySelectorAll('input.currency').forEach(function (c) {
        c.value = (c.value || '').replace(/[.,]/g, '');
      });
    });
  });
});
