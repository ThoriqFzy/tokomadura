/* =========================================================================
   TOKO SEMBAKO — app.js
   Auth dummy (sessionStorage, belum database) + role guard + menu mount
   Owner: admin/admin123  |  Kasir: kasir/kasir123
   ========================================================================= */
(function () {
  "use strict";

  // ---- Akun dummy ----
  var ACCOUNTS = {
    admin: { pass: "admin123", role: "owner", name: "Thoriq" },
    kasir: { pass: "kasir123", role: "kasir", name: "Dina" }
  };

  // ---- Halaman milik owner saja (kasir di-block) ----
  var OWNER_PAGES = ["dashboard.html", "produk.html", "stok.html", "laporan.html"];
  // Halaman yang boleh dibuka kasir
  var KASIR_PAGES = ["pos.html", "kasbon.html"];
  var ROOT = "mockup_v3/";

  // ---- helpers ----
  function $all(sel) { return Array.prototype.slice.call(document.querySelectorAll(sel)); }
  function getRole() { return sessionStorage.getItem("ts_role") || null; }
  function getLogin() { return sessionStorage.getItem("ts_login") || null; }
  var current = window.location.pathname.split("/").pop() || "";

  /* ---- LOGIN PAGE ---- */
  if (document.getElementById("loginForm")) {
    setupLogin();
    return; // stop here
  }

  /* ---- OTHER PAGES: enforce session ----
     Kalau belum login -> redirect ke login.
     Guard halaman owner-only untuk kasir.
  */
  var role = getRole();
  if (!role) { window.location.replace(ROOT + "login.html"); return; }

  // Guard: kasir coba buka halaman owner -> redirect ke pos
  if (role === "kasir" && OWNER_PAGES.indexOf(current) > -1) {
    window.location.replace(ROOT + "pos.html");
    return;
  }

  // Mount UX sesuai role
  mountRoleUI(role);

  /* ================= LOGIN ================= */
  function setupLogin() {
    var roleSwitch = document.getElementById("roleSwitch");
    var roles = $all(".role");
    var form = document.getElementById("loginForm");
    var btn = document.getElementById("btnLogin");
    var err = document.getElementById("loginErr");
    var errText = document.getElementById("loginErrText");
    var selRole = "owner";

    roles.forEach(function (r) {
      r.addEventListener("click", function () {
        roles.forEach(function (x) { x.classList.remove("active"); });
        r.classList.add("active");
        selRole = r.getAttribute("data-role");
      });
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var u = document.getElementById("user").value.trim().toLowerCase();
      var p = document.getElementById("pass").value;
      var acct = ACCOUNTS[u];

      if (!acct || acct.pass !== p) {
        showError("User atau password salah. Coba lagi.");
        return;
      }
      // Role harus cocok dengan akun (owner tidak bisa login sebagai kasir dll)
      if (acct.role !== selRole) {
        // Pesan statis dari kode sendiri (bukan input user) — aman pakai textContent
        showError(acct.role === "owner"
          ? "Akun ini untuk role Owner. Ubah pilihan role ke Owner."
          : "Akun ini untuk role Kasir. Ubah pilihan role ke Kasir.");
        return;
      }
      sessionStorage.setItem("ts_role", acct.role);
      sessionStorage.setItem("ts_login", u);
      btn.textContent = "Memasuki sistem…";
      btn.disabled = true;
      var dest = acct.role === "owner" ? ROOT + "dashboard.html" : ROOT + "pos.html";
      window.location.href = dest;
    });

    function showError(msg) {
      // textContent aman utk pesan statis (bukan HTML user input)
      errText.textContent = msg;
      err.style.display = "flex";
      err.classList.remove("shake");
      void err.offsetWidth; // restart animasi
      err.classList.add("shake");
    }
  }

  /* ================= MOUNT ROLE UI =================
     Meng-set: label user, menu sidebar & bottom-nav sesuai role,
     menyembunyikan elemen owner-only, menampilkan greeting.
  */
  function mountRoleUI(role) {
    // Avatar + nama
    var login = getLogin();
    var name = (ACCOUNTS[login] && ACCOUNTS[login].name) || "User";
    $all("[data-user-name]").forEach(function (el) { el.textContent = name; });
    var av = document.querySelector(".avatar");
    if (av) av.textContent = name.charAt(0);

    // badge role
    $all("[data-role-badge]").forEach(function (el) {
      el.textContent = role === "owner" ? "Owner" : "Kasir";
      el.className = "top-chip role-" + role;
    });

    // Tombol logout
    $all("[data-logout]").forEach(function (el) {
      el.addEventListener("click", function () {
        sessionStorage.removeItem("ts_role");
        sessionStorage.removeItem("ts_login");
        window.location.href = ROOT + "login.html";
      });
    });

    // Aktifkan link nav (class active utk current page)
    $all("nav a").forEach(function (a) {
      var href = a.getAttribute("href");
      if (href && href.split("/").pop() === current) a.classList.add("active");
    });

    // Hapus semua link ke halaman owner untuk kasir
    if (role === "kasir") {
      $all("nav a[data-owner]").forEach(function (a) { a.remove(); });
      $all("[data-owner-block]").forEach(function (el) { el.style.display = "none"; });
    }

    // Animasi fade-in stagger untuk kartu
    var cards = $all(".card, .stat, tbody tr");
    cards.forEach(function (c, i) {
      c.style.opacity = "0";
      c.style.transform = "translateY(8px)";
      (function (el, idx) {
        setTimeout(function () {
          el.style.transition = "opacity .4s ease, transform .4s ease";
          el.style.opacity = "1";
          el.style.transform = "translateY(0)";
        }, 60 * idx);
      })(c, i);
    });
  }
})();
