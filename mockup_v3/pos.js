/* =========================================================================
   TOKO SEMBAKO — pos.js  (logika POS/kasir interaktif)
   Di-load di pos.html. app.js tetap urusan auth/role.
   Fitur: klik produk -> masuk keranjang, qty +/- , hitung total,
   pilih metode bayar (Cash/QRIS/Utang), hitung kembalian, bayar -> reset.
   ========================================================================= */
(function () {
  "use strict";

  // ---- Katalog produk dummy ----
  var PRODUCTS = [
    { id: "BR5",  name: "Beras Premium 5kg",  cat: "Sembako", price: 68000,  stock: 42 },
    { id: "MG1",  name: "Minyak Goreng 1L",   cat: "Sembako", price: 20000,  stock: 3  },
    { id: "GP1",  name: "Gula Pasir 1kg",     cat: "Sembako", price: 18000,  stock: 2  },
    { id: "TY",   name: "Telur Ayam",         cat: "Sembako", price: 28000,  stock: 4  },
    { id: "KS",   name: "Kopi Sachet",        cat: "Minuman", price: 24000,  stock: 15 },
    { id: "MIE",  name: "Mie Instan Goreng",  cat: "Snack",   price: 110000, stock: 1  },
    { id: "AM6",  name: "Air Mineral 600ml",  cat: "Minuman", price: 4000,   stock: 60 },
    { id: "TB",   name: "Teh Botol",          cat: "Minuman", price: 6000,   stock: 48 },
    { id: "SC",   name: "Sabun Cair",         cat: "RT",      price: 18000,  stock: 9  },
    { id: "GB2",  name: "Garam 250gr",        cat: "Sembako", price: 5000,   stock: 20 },
    { id: "TEP",  name: "Tepung Terigu 1kg",  cat: "Sembako", price: 12000,  stock: 14 },
    { id: "KEC",  name: "Kecap Botol 600ml",  cat: "RT",      price: 15000,  stock: 6  }
  ];

  var CATS = [
    { key: "Semua", label: "Semua" },
    { key: "Sembako", label: "Sembako" },
    { key: "Minuman", label: "Minuman" },
    { key: "Snack", label: "Snack" },
    { key: "RT", label: "Rumah Tangga" }
  ];

  var cart = {};            // id -> qty
  var activeCat = "Semua";  // filter
  var payMethod = "cash";

  var gridEl, listEl, totalEl, subEl, itemCountEl, btnBayar;
  var paidEl, changeEl, payArea, utangField;

  // ---- format rupiah ----
  function rupiah(n) {
    return "Rp " + n.toLocaleString("id-ID");
  }
  function num(n) { return n.toLocaleString("id-ID"); }

  // ---- render produk grid (dengan filter + search) ----
  function renderGrid(q) {
    q = (q || "").trim().toLowerCase();
    var html = "";
    PRODUCTS.forEach(function (p) {
      if (activeCat !== "Semua" && p.cat !== activeCat) return;
      if (q && p.name.toLowerCase().indexOf(q) === -1 && p.id.toLowerCase().indexOf(q) === -1) return;
      var low = p.stock <= 3;
      var menipis = p.stock === 4;
      var stockCls = low ? "s-stock low" : (menipis ? "s-stock low" : "s-stock");
      var stockTxt = low ? "Stok " + p.stock + " · kritis" : "Stok " + p.stock;
      html += '<button class="sku" type="button" data-add="' + p.id + '">'
        + '<span class="s-name">' + p.name + '</span>'
        + '<span class="s-cat">' + p.cat + '</span>'
        + '<span class="s-price">' + rupiah(p.price) + '</span>'
        + '<span class="' + stockCls + '">' + stockTxt + '</span></button>';
    });
    gridEl.innerHTML = html || '<div class="muted">Tidak ada produk.</div>';
  }

  // ---- render keranjang ----
  function renderCart() {
    var itemCount = 0, subtotal = 0, html = "";
    Object.keys(cart).forEach(function (id) {
      var p = byId(id); if (!p) return;
      var q = cart[id];
      itemCount += q;
      var line = q * p.price;
      subtotal += line;
      if (html) html += '<div class="citem">'; // separator dashed
      else html += '<div class="citem">';
      html += '<div class="c-info"><div class="c-name">' + p.name + '</div>'
        + '<div class="c-meta">' + rupiah(p.price) + ' × ' + q + '</div></div>'
        + '<div class="qty"><button type="button" data-dec="' + id + '">−</button><span>' + q + '</span><button type="button" data-inc="' + id + '">+</button></div>'
        + '<div class="c-total num">' + num(line) + '</div></div>';
    });
    listEl.innerHTML = html || '<div class="muted" style="padding:10px 0;">Keranjang kosong — klik produk untuk menambah.</div>';
    itemCountEl.textContent = itemCount === 0 ? "Keranjang" : itemCount + " item";
    subEl.textContent = rupiah(subtotal);
    totalEl.textContent = rupiah(subtotal);

    // update section pembayaran
    if (itemCount === 0) {
      payArea.style.display = "none";
      btnBayar.disabled = true;
    } else {
      payArea.style.display = "block";
      btnBayar.disabled = false;
      calcChange();
    }
  }

  function byId(id) {
    for (var i = 0; i < PRODUCTS.length; i++) if (PRODUCTS[i].id === id) return PRODUCTS[i];
    return null;
  }

  function add(id) {
    var p = byId(id); if (!p) return;
    var cur = cart[id] || 0;
    if (cur >= p.stock) { flash("Stok " + p.name + " tinggal " + p.stock); return; }
    cart[id] = cur + 1;
    renderGrid(document.getElementById("q").value);
    renderCart();
  }
  function dec(id) {
    cart[id] = (cart[id] || 1) - 1;
    if (cart[id] <= 0) delete cart[id];
    renderCart();
  }
  function inc(id) { add(id); }

  // ---- metode bayar ----
  function setMethod(m) {
    payMethod = m;
    $all(".method").forEach(function (el) {
      el.classList.toggle("active", el.getAttribute("data-method") === m);
    });
    // tampilkan/smbunyikan field sesuai metode
    var isUtang = m === "utang";
    if (utangField) utangField.style.display = isUtang ? "" : "none";
    if (paidEl) paidEl.parentElement.style.display = isUtang ? "none" : "";
    calcChange();
  }
  function calcChange() {
    if (!paidEl) return;
    var totalAmt = parseTotal();
    if (payMethod === "cash" || payMethod === "qris") {
      paidEl.style.display = "";
      var paid = parseRupiah(paidEl.value);
      var change = paid - totalAmt;
      changeEl.textContent = "Kembalian: " + rupiah(change >= 0 ? change : 0);
      if (change < 0) changeEl.style.color = "var(--danger)";
      else changeEl.style.color = ""; // auto
      // highlight kurang bayar
      changeEl.style.color = change < 0 ? "var(--danger)" : "var(--primary-text)";
    } else {
      // Utang
      paidEl.style.display = "none";
      changeEl.textContent = "Barang keluar, kas tercatat sebagai piutang.";
      changeEl.style.color = "var(--amber-700)";
    }
  }
  function parseRupiah(v) { return parseInt(String(v).replace(/[^\d]/g, ""), 10) || 0; }
  function parseTotal() {
    // baca total dari totalEl (tampilan) — cari angka setelah Rp
    var t = totalEl.textContent.replace(/[^\d]/g, "");
    return parseInt(t, 10) || 0;
  }

  // ---- flash/toast ----
  function flash(msg) {
    var t = document.getElementById("posToast");
    if (!t) return;
    t.textContent = msg;
    t.classList.add("show");
    clearTimeout(t._t);
    t._t = setTimeout(function () { t.classList.remove("show"); }, 1600);
  }

  function $all(s) { return Array.prototype.slice.call(document.querySelectorAll(s)); }

  // ---- init ----
  document.addEventListener("DOMContentLoaded", function () {
    gridEl = document.getElementById("skuGrid");
    listEl = document.getElementById("cartList");
    totalEl = document.getElementById("grandTotal");
    subEl = document.getElementById("subTotal");
    itemCountEl = document.getElementById("cartTitle");
    btnBayar = document.getElementById("btnBayar");
    payArea = document.getElementById("cartPaySection");
    paidEl = document.getElementById("paidInput");
    changeEl = document.getElementById("changeLabel");
    utangField = document.getElementById("utangField");
    var pelangganEl = document.getElementById("pelangganSelect");
    var emptydEl = document.querySelector("#btnKosongkan");

    if (!gridEl) return; // bukan pos.html / belum ada

    // filter kategori
    var catBar = document.getElementById("catBar");
    if (catBar) {
      var ch = "";
      CATS.forEach(function (c) {
        ch += '<span class="badge ' + (c.key === activeCat ? "green" : "outline") + '" data-cat="' + c.key + '">' + c.label + '</span>';
      });
      catBar.innerHTML = ch;
    }

    // event delegation
    gridEl.addEventListener("click", function (e) {
      var btn = e.target.closest ? e.target.closest("[data-add]") : null;
      if (btn) add(btn.getAttribute("data-add"));
    });
    listEl.addEventListener("click", function (e) {
      var incB = e.target.closest ? e.target.closest("[data-inc]") : null;
      var decB = e.target.closest ? e.target.closest("[data-dec]") : null;
      if (incB) inc(incB.getAttribute("data-inc"));
      if (decB) dec(decB.getAttribute("data-dec"));
    });
    catBar.addEventListener("click", function (e) {
      var b = e.target.closest ? e.target.closest("[data-cat]") : null;
      if (!b) return;
      activeCat = b.getAttribute("data-cat");
      renderGrid(document.getElementById("q").value);
      $all("#catBar .badge").forEach(function (x) {
        x.className = "badge " + (x.getAttribute("data-cat") === activeCat ? "green" : "outline");
      });
    });
    document.getElementById("q").addEventListener("input", function () {
      renderGrid(this.value);
    });

    // metode bayar
    $all(".method").forEach(function (m) {
      m.addEventListener("click", function () { setMethod(m.getAttribute("data-method")); });
    });

    // perhitungan kembalian live
    if (paidEl) paidEl.addEventListener("input", calcChange);

    // kosongkan
    if (emptydEl) emptydEl.addEventListener("click", function () {
      cart = {}; renderCart();
    });

    // bayar
    btnBayar.addEventListener("click", function () {
      if (payMethod === "cash" || payMethod === "qris") {
        var paid = parseRupiah(paidEl.value);
        var totalAmt = parseTotal();
        if (paid < totalAmt) { flash("Uang kurang " + rupiah(totalAmt - paid)); return; }
      } else if (payMethod === "utang") {
        if (pelangganEl && !pelangganEl.value) { flash("Pilih nama pelanggan dulu!"); return; }
        var nm = pelangganEl.options[pelangganEl.selectedIndex].text;
        flash("Utang dicatat ke " + nm + ". Struk dikirim ke printer.");
        cart = {}; renderCart();
        return;
      }
      flash("Transaksi tersimpan! Struk dikirim ke printer.");
      cart = {};
      renderCart();
    });

    // seed keranjang contoh (supaya keliatan isi di awal review)
    cart = { BR5: 2, MG1: 1, GP1: 1, AM6: 3 };

    renderGrid("");
    renderCart();
  });
})();
