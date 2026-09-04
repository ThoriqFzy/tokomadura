#!/usr/bin/env python3
"""
Unduh gambar produk dari Tokopedia via Camoufox (anti-bot anti-deteksi).

1. Buka halaman pencarian Tokopedia, scroll & tunggu render
2. Ambil URL gambar produk real (filter: ~tplv-...white-pad; buang logo/badge/svg)
3. Upgrade resolusi kecil (200) -> 500, lalu unduh via curl ke target

Usage:
  python fetch_product_img.py "minyak goreng bimoli 1 liter" minyak-bimoli.jpg [--dir DIR] [--idx 0]
"""
import sys, os, re, subprocess
from camoufox.sync_api import Camoufox

TARGET_DIR = "/home/hermes/project/tokomadura/public/img/products"

def is_product_img(url):
    if not url.startswith("http"): return False
    if ".svg" in url: return False
    if "logo" in url or "badge" in url or "icon" in url or "zeus" in url: return False
    if "assets-tokopedia" in url: return False
    if "~tplv-" in url and "white-pad" in url: return True
    if "/img/cache/" in url: return True
    return "tokopedia-static.net/img/" in url or "objects-sg" in url and "product" in url

def upscale(url, to=500):
    return url.replace(":200:200", f":{to}:{to}").replace("/resize-jpeg:", "/resize-jpeg:").replace("200:200", f"{to}:{to}")

def download(url, outpath):
    ref = "https://www.tokopedia.com/"
    r = subprocess.run(["curl","-sL","-A","Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36","-e",ref,"-o",outpath,url],
                       capture_output=True, text=True)
    return os.path.getsize(outpath) if os.path.exists(outpath) else 0

def main(argv):
    if len(argv) < 2:
        print("usage: fetch_product_img.py <query> <outfile> [--idx N] [--dir DIR]"); return
    query = argv[0]; outfile = argv[1]
    idx = 0; 
    if "--idx" in argv: idx = int(argv[argv.index("--idx")+1])
    if "--dir" in argv: 
        global TARGET_DIR; TARGET_DIR = argv[argv.index("--dir")+1]
    os.makedirs(TARGET_DIR, exist_ok=True)

    url = "https://www.tokopedia.com/find/" + query.replace(" ", "-").lower()
    print("BUKA:", url, flush=True)

    with Camoufox(headless=True) as browser:
        page = browser.new_page()
        page.goto(url, timeout=50000, wait_until="domcontentloaded")
        try: page.wait_for_load_state("networkidle", timeout=15000)
        except Exception: pass
        page.wait_for_timeout(3000)
        for _ in range(6):
            page.mouse.wheel(0, 1200); page.wait_for_timeout(600)
        page.wait_for_timeout(2000)
        imgs = page.eval_on_selector_all("img", "els => els.map(e => e.src)")
        cands = [s for s in imgs if is_product_img(s)]
    seen = set(); final = []
    for s in cands:
        if s in seen: continue
        seen.add(s); final.append(s)
    print("KANDIDAT:", len(final), flush=True)
    for i,s in enumerate(final[:12]): print(f"IMG[{i}]: {s}", flush=True)

    if idx >= len(final):
        print("INDEX_ERR"); return
    chosen = final[idx]
    # unduh persis URL asli (200x200) — upscale melanggar signature CDN
    outpath = os.path.join(TARGET_DIR, outfile)
    sz = download(chosen, outpath)
    print(f"SAVED {outfile} size={sz}", flush=True)

if __name__ == "__main__":
    main(sys.argv[1:])
