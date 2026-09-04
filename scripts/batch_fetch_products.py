#!/usr/bin/env python3
"""
Batch unduh gambar produk dari Tokopedia via SATU session Camoufox.
Untuk tiap produk: buka halaman pencarian, ambil URL gambar produk real,
unduh via curl ke target. Verifikasi ukuran > 3000 byte.

Usage: python batch_fetch_products.py
"""
import os, sys, subprocess, json
from camoufox.sync_api import Camoufox

DIR = "/home/hermes/project/tokomadura/public/img/products"
os.makedirs(DIR, exist_ok=True)

# (query_tokopedia, outfile)
PRODUCTS = [
    ("mie sedaap soto",           "mie-sedaap-soto.jpg"),
    ("mie sedaap goreng",         "mie-sedaap-goreng.jpg"),
    ("mie sedaap kari ayam",      "mie-sedaap-kari.jpg"),
    ("mie sedaap ayam bawang",    "mie-sedaap-ayam-bawang.jpg"),
    ("indomie goreng",            "indomie-goreng.jpg"),
    ("indomie soto",              "indomie-soto.jpg"),
    ("supermi goreng",            "supermi-goreng.jpg"),
    ("minyak goreng bimoli 1 liter", "minyak-bimoli.jpg"),
    ("minyak goreng fortune 1 liter", "minyak-fortune.jpg"),
    ("minyak goreng sunco 1 liter",   "minyak-sunco.jpg"),
    ("minyak goreng sania 1 liter",   "minyak-sania.jpg"),
    ("minyak goreng filma 1 liter",   "minyak-filma.jpg"),
    ("beras premium 5kg",         "beras-premium-5kg.jpg"),
    ("beras medium 5kg",          "beras-medium-5kg.jpg"),
    ("beras merah 1kg",           "beras-merah-1kg.jpg"),
    ("beras ketan 1kg",           "beras-ketan-1kg.jpg"),
    ("gula pasir 1kg",            "gula-pasir-1kg.jpg"),
    ("gula merah",                "gula-merah.jpg"),
    ("tepung terigu segitiga biru 1kg", "tepung-segitiga-1kg.jpg"),
    ("tepung terigu bola salju 1kg",    "tepung-bola-salju-1kg.jpg"),
    ("telur ayam 1kg",            "telur-ayam-1kg.jpg"),
    ("kecap manis abc 275ml",     "kecap-abc.jpg"),
    ("saus sambal abc",           "saus-sambal-abc.jpg"),
    ("minyak kelapa 250ml",       "minyak-kelapa.jpg"),
    ("air mineral 600ml",         "air-mineral-600.jpg"),
    ("teh botol sosro 350ml",     "teh-botol-sosro.jpg"),
    ("kopi kapal api sachet",     "kopi-kapal-api.jpg"),
    ("susu ultra 250ml",          "susu-ultra-250.jpg"),
    ("susu ultra 1 liter",        "susu-ultra-1l.jpg"),
    ("keripik singkong",          "keripik-singkong.jpg"),
    ("kacang garuda 250g",        "kacang-garuda.jpg"),
    ("biskuit roma kelapa",       "roma-kelapa.jpg"),
    ("wafer nabati keju",         "nabati-keju.jpg"),
    ("ciki mie sapi panggang",    "ciki-mie.jpg"),
    ("chocolatos wafer",          "chocolatos.jpg"),
    ("shampo sachet",             "shampo-sachet.jpg"),
    ("sabun mandi lifebuoy",      "sabun-lifebuoy.jpg"),
    ("sabun cuci piring",         "sabun-cuci-piring.jpg"),
    ("deterjen rinso 500g",       "deterjen-rinso.jpg"),
]

def is_product_img(url):
    if not url.startswith("http"): return False
    low = url.lower()
    if ".svg" in low: return False
    if any(b in low for b in ["logo","badge","icon","zeus","assets-tokopedia","official_store"]): return False
    if "~tplv-" in url and "white-pad" in url: return True
    if "/img/cache/" in url: return True
    return False

def download(url, outpath):
    subprocess.run(["curl","-sL","-A","Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36",
                    "-e","https://www.tokopedia.com/","-o",outpath,url],
                   capture_output=True)
    return os.path.getsize(outpath) if os.path.exists(outpath) else 0

def main():
    results = {}
    with Camoufox(headless=True) as browser:
        page = browser.new_page()
        for query, outfile in PRODUCTS:
            url = "https://www.tokopedia.com/find/" + query.replace(" ", "-").lower()
            print(f"\n=== {query} -> {outfile}", flush=True)
            try:
                page.goto(url, timeout=45000, wait_until="domcontentloaded")
                try: page.wait_for_load_state("networkidle", timeout=12000)
                except Exception: pass
                page.wait_for_timeout(2500)
                for _ in range(5):
                    page.mouse.wheel(0, 1200); page.wait_for_timeout(500)
                page.wait_for_timeout(1500)
                imgs = page.eval_on_selector_all("img", "els => els.map(e => e.src)")
                cands = [s for s in imgs if is_product_img(s)]
                seen=set(); final=[s for s in cands if not (s in seen or seen.add(s))]
                if not final:
                    print("  KOSONG", flush=True); results[outfile]="empty"; continue
                chosen = final[0]
                sz = download(chosen, os.path.join(DIR,outfile))
                ok = sz > 3000
                print(f"  x {sz} bytes {'OK' if ok else 'KECIL'}", flush=True)
                results[outfile]="ok" if ok else f"small:{sz}"
            except Exception as e:
                print(f"  ERR {e}", flush=True); results[outfile]="error"
    print("\n===== HASIL =====", flush=True)
    for k,v in results.items(): print(f"{k}: {v}", flush=True)

if __name__ == "__main__":
    main()
