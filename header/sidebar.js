(function () {
  var st = document.createElement('style');
  st.id = 'sidebar-css';
  st.textContent = `/* ---------- Sidebar (drawer) ---------- */
.no-scroll { overflow: hidden; }
.overlay {
  position: fixed; inset: 0; z-index: 40;
  background: rgba(18, 41, 58, .5);
  opacity: 0; transition: opacity .25s ease;
}
.overlay.show { opacity: 1; }

.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; z-index: 50;
  width: 272px; max-width: 86vw;
  display: flex; flex-direction: column;
  padding: 16px 16px 20px;
  background: #2B5F5A; color: #fff;
  box-shadow: 4px 0 18px rgba(0,0,0,.25);
  transform: translateX(-100%);
  transition: transform .25s ease;
  overflow-y: auto;
}
.sidebar.open { transform: translateX(0); }

.sidebar__top { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; }
.sidebar__brand {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
  min-height: 54px; padding: 0 12px;
  background: #fff; border-radius: 999px;
  color: #1E4D2B; font-weight: 700; font-size: 16px; text-decoration: none;
}
.sidebar__close {
  display: grid; place-items: center; flex: none;
  width: 44px; height: 44px; border: 0; border-radius: 10px;
  background: transparent; color: #fff; cursor: pointer;
}
.sidebar__close:hover { background: rgba(255,255,255,.14); }

.sidebar__nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }
.sidebar__label { margin: 10px 8px 2px; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; color: #CFE2DF; }
.sidebar__line { width: 100%; margin: 12px 0 0; border: 0; border-top: 1px solid rgba(255,255,255,.25); }

.side-link {
  display: flex; align-items: center; gap: 14px;
  min-height: 46px; padding: 0 14px; border-radius: 10px;
  color: #fff; text-decoration: none; font-size: 15px; font-weight: 500;
}
.side-link:hover { background: rgba(255,255,255,.12); }
.side-link.active { background: rgba(255,255,255,.2); font-weight: 600; }

.sidebar__bottom { margin-top: 20px; }
.side-logout {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  min-height: 46px; border-radius: 10px;
  background: #D40F0F; color: #fff; text-decoration: none; font-weight: 600; font-size: 15px;
}
.side-logout:hover { background: #B80C0C; }
.sidebar a:focus-visible, .sidebar button:focus-visible {
  outline: 3px solid #fff; outline-offset: 2px; box-shadow: 0 0 0 5px #12293A;
}

@media (prefers-reduced-motion: reduce) {
  .sidebar, .overlay { transition: none; }
}
`;
  document.head.appendChild(st);

  var ico = function (p) {
    return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + p + '</svg>';
  };

  var html =
    '<div class="overlay" id="sidebarOverlay" hidden></div>' +
    '<aside class="sidebar" id="sidebar" aria-label="Menu navigasi" inert>' +
      '<div class="sidebar__top">' +
        '<a class="sidebar__brand" href="dashboard.php">' +
          '<img class="sidebar__logo" src="images/logo.png" alt="RecyclePoint" width = "90%">' +
        '</a>' +
        '<button class="sidebar__close" type="button" aria-label="Tutup menu">' +
          '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>' +
      '</div>' +
      '<nav class="sidebar__nav" aria-label="Menu utama">' +
        '<p class="sidebar__label">Menu</p>' +
        '<a class="side-link" href="dashboard.php">' + ico('<path d="M3 11l9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>') + 'Dashboard</a>' +
        '<a class="side-link" href="daftar_lokasi.php">' + ico('<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>') + 'Daftar &amp; Lokasi</a>' +
        '<a class="side-link" href="riwayat_setor.php">' + ico('<path d="M7 19l-3-5 3-5M17 5l3 5-3 5M9 5h6M9 19h6M4 14h5M15 10h5"/>') + 'Riwayat Setor</a>' +
        '<a class="side-link" href="marketpoin.php">' + ico('<path d="M4 8l1-4h14l1 4M4 8h16M5 8v12h14V8M10 20v-6h4v6"/>') + 'Market Poin</a>' +
        '<a class="side-link" href="riwayat_transaksi.php">' + ico('<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>') + 'Riwayat Poin</a>' +
        '<hr class="sidebar__line">' +
        '<p class="sidebar__label">Lainnya</p>' +
        '<a class="side-link" href="profile.php"><img src="images/settingputih.png" width="20" height="20" alt="">Pengaturan</a>' +
        '<a class="side-link" href="notifikasi.php">' + ico('<path d="M6 8a6 6 0 1 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/>') + 'Notifikasi</a>' +
      '</nav>' +
      '<div class="sidebar__bottom">' +
        '<a class="side-logout" href="index.php">' + ico('<path d="M9 21H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4M16 17l5-5-5-5M21 12H9"/>') + 'Logout</a>' +
      '</div>' +
    '</aside>';

  var wrap = document.createElement('div');
  wrap.innerHTML = html;
  while (wrap.firstChild) document.body.appendChild(wrap.firstChild);

  var BTN = '[aria-label="Buka menu"], [data-menu-toggle]';
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  var closeBtn = sidebar.querySelector('.sidebar__close');
  var lastBtn = null;

  function setExpanded(v) {
    document.querySelectorAll(BTN).forEach(function (b) {
      b.setAttribute('aria-controls', 'sidebar');
      b.setAttribute('aria-expanded', v ? 'true' : 'false');
    });
  }

  function open() {
    sidebar.inert = false;
    overlay.hidden = false;
    requestAnimationFrame(function () {
      sidebar.classList.add('open');
      overlay.classList.add('show');
    });
    document.body.classList.add('no-scroll');
    setExpanded(true);
    closeBtn.focus();
  }
  function close() {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    sidebar.inert = true;
    document.body.classList.remove('no-scroll');
    setExpanded(false);
    setTimeout(function () { if (!sidebar.classList.contains('open')) overlay.hidden = true; }, 250);
    if (lastBtn) lastBtn.focus();
  }

  document.addEventListener('click', function (e) {
    var b = e.target.closest(BTN);
    if (!b) return;
    lastBtn = b;
    sidebar.classList.contains('open') ? close() : open();
  });
  closeBtn.addEventListener('click', close);
  overlay.addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) close();
  });
  setExpanded(false);
})();