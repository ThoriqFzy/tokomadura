/* =========================================================================
   TOKO SEMBAKO — main.js
   Perilaku umum lintas halaman (selain auth & POS):
   - Form submit → toast sukses
   - Live search (tabel produk, stok)
   - Transisi masuk halaman halus
   ========================================================================= */
(function () {
  "use strict";

  // ---- Toast helper (shared dengan pos.js) ----
  function flash(msg, type) {
    var t = document.getElementById("posToast") || document.getElementById("mainToast");
    if (!t) {
      // buat toast element jika belum ada
      t = document.createElement("div");
      t.id = "mainToast";
      t.className = "toast";
      document.body.appendChild(t);
    }
    t.textContent = msg;
    if (type === "error") t.style.background = "var(--danger)";
    else if (type === "warn") t.style.background = "var(--amber-700)";
    else t.style.background = "";
    t.classList.add("show");
    clearTimeout(t._t);
    t._t = setTimeout(function () { t.classList.remove("show"); }, 2200);
  }

  // ---- Live search helper ----
  function liveSearch(inputSel, tableSel) {
    var inp = document.querySelector(inputSel);
    var tbl = document.querySelector(tableSel);
    if (!inp || !tbl) return;
    var rows = Array.prototype.slice.call(tbl.querySelectorAll("tbody tr"));
    inp.addEventListener("input", function () {
      var q = inp.value.trim().toLowerCase();
      rows.forEach(function (r) {
        var text = r.textContent.toLowerCase();
        r.style.display = (!q || text.indexOf(q) > -1) ? "" : "none";
      });
    });
  }

  // ---- Init ----
  document.addEventListener("DOMContentLoaded", function () {

    // === FORM SUBMIT → TOAST ===
    $all("form[data-toast]").forEach(function (f) {
      f.addEventListener("submit", function (e) {
        e.preventDefault();
        var msg = f.getAttribute("data-toast") || "Berhasil disimpan!";
        flash(msg);
      });
    });

    // Tambah tombol submit也为 data-toast form (btn click tanpa form)
    $all("button[data-toast]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var msg = btn.getAttribute("data-toast") || "Berhasil!";
        flash(msg);
      });
    });

    // === LIVE SEARCH: tabel produk ===
    liveSearch(".tbl-search", "#produkTable tbody");
    liveSearch(".tbl-search", "#stokTable tbody");

    // === TRANSISI MASUK ===
    var main = document.querySelector(".main");
    if (main) {
      main.style.opacity = "0";
      main.style.transform = "translateY(6px)";
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          main.style.transition = "opacity .35s ease, transform .35s ease";
          main.style.opacity = "1";
          main.style.transform = "translateY(0)";
        });
      });
    }
  });

  function $all(s) { return Array.prototype.slice.call(document.querySelectorAll(s)); }

  // Expose flash global (bisa dipanggil dari inline onclick)
  window._flash = flash;
})();
