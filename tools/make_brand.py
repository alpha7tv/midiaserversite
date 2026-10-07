#!/usr/bin/env python3
"""Gera a identidade visual da Mídia Server (SVG com texto vetorizado a partir da fonte Sora)."""
import os, subprocess, sys
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, 'public/assets/img')
FONTS = os.path.join(ROOT, 'public/assets/fonts')

NAVY, BLUE, CYAN, VIOLET, MINT = '#0b1424', '#2e6bff', '#00c2ff', '#7b5cff', '#00e0a4'

def text_path(text, weight, size, x, y, spacing=0.0):
    font = TTFont(os.path.join(FONTS, f'sora-{weight}.woff2'))
    gs, cmap, hmtx = font.getGlyphSet(), font.getBestCmap(), font['hmtx']
    upm = font['head'].unitsPerEm
    sc = size / upm
    cx, d = x, []
    for ch in text:
        gn = cmap.get(ord(ch))
        if gn is None:
            cx += size * 0.3
            continue
        pen = SVGPathPen(gs)
        gs[gn].draw(TransformPen(pen, (sc, 0, 0, -sc, cx, y)))
        d.append(pen.getCommands())
        cx += hmtx[gn][0] * sc + spacing
    return ' '.join(d), cx

GRAD = f'<linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{CYAN}"/><stop offset=".55" stop-color="{BLUE}"/><stop offset="1" stop-color="{VIOLET}"/></linearGradient>'

def symbol(x=0, y=0, s=64, mono=False):
    k = s / 64
    tile = NAVY if mono else 'url(#g)'
    return (f'<g transform="translate({x} {y}) scale({k})">'
            f'<rect width="64" height="64" rx="16" fill="{tile}"/>'
            f'<path d="M16 46V21l16 17 16-17v25" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>'
            f'<circle cx="50" cy="14" r="3.6" fill="{MINT}"/>'
            f'<circle cx="50" cy="14" r="7" fill="none" stroke="{MINT}" stroke-width="1.6" opacity=".5"/>'
            f'</g>')

def svg(w, h, body, defs=True):
    return (f'<svg xmlns="http://www.w3.org/2000/svg" width="{w}" height="{h}" viewBox="0 0 {w} {h}" role="img" aria-label="Mídia Server">'
            f'<defs>{GRAD}</defs>{body}</svg>\n')

def horizontal(dark=False):
    s = 56
    p1, cx = text_path('MÍDIA', 800, 30, 70, 36)
    p2, ex = text_path('SERVER', 600, 30, cx + 9, 36, spacing=1.5)
    c1, c2 = ('#ffffff', CYAN) if dark else (NAVY, BLUE)
    w = int(ex) + 4
    body = symbol(0, 0, s) + f'<path d="{p1}" fill="{c1}"/><path d="{p2}" fill="{c2}"/>'
    return svg(w, s, body), w, s

def stacked(dark=False):
    p1, cx = text_path('MÍDIA', 800, 26, 0, 0)
    p2, ex = text_path('SERVER', 600, 26, cx + 8, 0, spacing=1.2)
    tw = ex
    W = int(max(tw, 64)) + 8
    c1, c2 = ('#ffffff', CYAN) if dark else (NAVY, BLUE)
    tx = (W - tw) / 2
    p1, cx = text_path('MÍDIA', 800, 26, tx, 100)
    p2, ex = text_path('SERVER', 600, 26, cx + 8, 100, spacing=1.2)
    body = symbol((W - 64) / 2, 0, 64) + f'<path d="{p1}" fill="{c1}"/><path d="{p2}" fill="{c2}"/>'
    return svg(W, 112, body), W, 112

def write(name, content):
    with open(os.path.join(IMG, name), 'w', encoding='utf8') as f:
        f.write(content)

def raster(svg_name, png_name, w, h, transparent=True, pad=0):
    html = os.path.join('/tmp', 'brand_tmp.html')
    with open(os.path.join(IMG, svg_name), encoding='utf8') as f:
        s = f.read()
    with open(html, 'w', encoding='utf8') as f:
        f.write(f'<html><body style="margin:0;background:transparent"><div style="width:{w}px;height:{h}px;display:flex;align-items:center;justify-content:center;padding:{pad}px;box-sizing:border-box">'
                + s.replace('<svg ', f'<svg style="width:100%;height:100%" ', 1) + '</div></body></html>')
    subprocess.check_call(['node', os.path.join(ROOT, 'tools/shot.js'), html, os.path.join(IMG, png_name), str(w), str(h), '0', '1' if transparent else '0'])

if __name__ == '__main__':
    os.makedirs(IMG, exist_ok=True)
    write('logo-symbol.svg', svg(64, 64, symbol()))
    write('logo-symbol-mono.svg', svg(64, 64, symbol(mono=True)))
    for dark in (False, True):
        s, w, h = horizontal(dark)
        write('logo-horizontal-dark.svg' if dark else 'logo-horizontal.svg', s)
        s2, w2, h2 = stacked(dark)
        write('logo-stacked-dark.svg' if dark else 'logo-stacked.svg', s2)
    # favicon: tile + M mais grosso, sem os arcos (legível em 16 px)
    fav = svg(64, 64, f'<rect width="64" height="64" rx="14" fill="url(#g)"/><path d="M15 47V20l17 18 17-18v27" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>')
    write('favicon.svg', fav)
    raster('favicon.svg', 'favicon-32.png', 32, 32)
    raster('favicon.svg', 'apple-touch-icon.png', 180, 180)
    raster('favicon.svg', 'icon-192.png', 192, 192)
    raster('favicon.svg', 'icon-512.png', 512, 512)
    # avatar quadrado p/ WhatsApp/Facebook/Instagram
    av = svg(64, 64, f'<rect width="64" height="64" fill="url(#g)"/><path d="M15 47V20l17 18 17-18v27" fill="none" stroke="#fff" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" transform="translate(32 32) scale(.8) translate(-32 -32)"/>')
    write('avatar-social.svg', av)
    raster('avatar-social.svg', 'avatar-social-1024.png', 1024, 1024, transparent=False)
    s, w, h = horizontal(False)
    raster('logo-horizontal.svg', 'logo-horizontal.png', w * 4, h * 4)
    raster('logo-horizontal-dark.svg', 'logo-horizontal-dark.png', w * 4, h * 4)
    print('marca gerada')
