#!/usr/bin/env python3
"""Gera as imagens Open Graph (1200x630) de cada página, com ilustrações vetoriais originais."""
import os, subprocess
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, 'public/assets/img')
OUT = os.path.join(IMG, 'og')
os.makedirs(OUT, exist_ok=True)

C, B, V, M = '#00c2ff', '#2e6bff', '#7b5cff', '#00e0a4'
def R(x, y, w, h, rx=12, fill='none', stroke='none', sw=2, op=1, extra=''):
    return f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="{rx}" fill="{fill}" stroke="{stroke}" stroke-width="{sw}" opacity="{op}" {extra}/>'
def Ci(cx, cy, r, fill='none', stroke='none', sw=2, op=1):
    return f'<circle cx="{cx}" cy="{cy}" r="{r}" fill="{fill}" stroke="{stroke}" stroke-width="{sw}" opacity="{op}"/>'
def P(d, fill='none', stroke='#fff', sw=3, op=1, cap='round'):
    return f'<path d="{d}" fill="{fill}" stroke="{stroke}" stroke-width="{sw}" stroke-linecap="{cap}" stroke-linejoin="round" opacity="{op}"/>'
GL = 'rgba(255,255,255,.08)'; GS = 'rgba(255,255,255,.22)'

def racks(y0=70, n=3):
    o = []
    for i in range(n):
        y = y0 + i * 110
        o.append(R(90, y, 340, 92, 18, GL, GS))
        o.append(Ci(124, y + 46, 7, M)); o.append(Ci(150, y + 46, 7, C, op=.8))
        o.append(R(190, y + 30, 190, 10, 5, 'rgba(255,255,255,.28)')); o.append(R(190, y + 52, 130, 10, 5, 'rgba(255,255,255,.14)'))
    return ''.join(o)

def cloud(x, y, s=1):
    return f'<g transform="translate({x} {y}) scale({s})">' + P('M60 90a38 38 0 0 1-4-76 54 54 0 0 1 104 12 34 34 0 0 1 4 64z', 'rgba(0,194,255,.12)', C, 4) + '</g>'

def wavebars(x, y, n=14, w=10, gap=8, hmax=150, col=C):
    import math
    o = []
    for i in range(n):
        h = 30 + (hmax - 30) * abs(math.sin(i * 0.9 + 0.6))
        o.append(R(x + i * (w + gap), y - h / 2, w, h, 5, col, op=.9 if i % 3 else 1))
    return ''.join(o)

def mic():
    return (R(210, 60, 100, 170, 50, GL, '#fff', 4) + P('M170 190a90 90 0 0 0 180 0', stroke='#fff', sw=4) + P('M260 280v70M205 350h110', stroke='#fff', sw=5)
            + P('M180 120h-0M350 100c28 20 28 70 0 90', stroke=C, sw=4, op=.9) + P('M385 80c46 34 46 112 0 146', stroke=C, sw=4, op=.55)
            + P('M170 100c-28 20-28 70 0 90', stroke=C, sw=4, op=.9) + P('M135 80c-46 34-46 112 0 146', stroke=C, sw=4, op=.55)
            + wavebars(105, 410, 18, 8, 10, 70, M))

def player_ui(x, y, w, h):
    return (R(x, y, w, h, 16, '#0f1b2e', GS) + Ci(x + 52, y + h / 2, 26, B) + P(f'M{x+45} {y+h/2-12}l22 12-22 12z', '#fff', '#fff', 2)
            + wavebars(x + 100, y + h / 2, 12, 6, 7, min(60, h - 30), C))

def laptop():
    return (R(60, 90, 330, 220, 16, '#0b1424', '#fff', 4) + R(76, 106, 298, 188, 8, '#0f1b2e') + player_ui(92, 130, 266, 78)
            + R(92, 224, 120, 10, 5, 'rgba(255,255,255,.3)') + R(92, 246, 190, 10, 5, 'rgba(255,255,255,.15)') + R(30, 318, 390, 18, 9, '#1f3654', '#fff', 3)
            + R(330, 150, 150, 270, 26, '#0b1424', '#fff', 4) + R(342, 176, 126, 218, 10, '#0f1b2e') + Ci(405, 255, 34, B) + P('M396 241l26 14-26 14z', '#fff', '#fff', 2)
            + wavebars(352, 340, 9, 6, 7, 40, C))

def browser():
    return (R(50, 80, 400, 290, 18, '#0b1424', '#fff', 4) + R(50, 80, 400, 40, 18, '#16273f') + Ci(78, 100, 6, '#ff6b6b') + Ci(98, 100, 6, '#ffd166') + Ci(118, 100, 6, M)
            + R(70, 136, 360, 100, 12, 'url(#g1)') + R(90, 156, 150, 14, 7, 'rgba(255,255,255,.9)') + R(90, 182, 210, 10, 5, 'rgba(255,255,255,.55)') + R(90, 204, 90, 22, 11, '#fff')
            + player_ui(70, 252, 360, 66) + R(70, 332, 110, 24, 8, 'rgba(255,255,255,.15)') + R(194, 332, 110, 24, 8, 'rgba(255,255,255,.15)')
            + R(340, 190, 120, 200, 22, '#0b1424', '#fff', 4) + R(350, 214, 100, 150, 8, 'url(#g1)') + R(358, 376, 84, 6, 3, 'rgba(255,255,255,.5)'))

def network():
    pts = [(260, 110), (110, 180), (410, 180), (150, 330), (370, 330)]
    o = [P(f'M260 235L{x} {y}', stroke=GS, sw=3) for x, y in pts]
    o += [Ci(x, y, 34, GL, C, 3) for x, y in pts] + [Ci(260, 235, 62, 'url(#g1)', '#fff', 4)]
    o += [P('M236 258V222l24 26 24-26v36', '#fff'.replace('#fff', 'none'), '#fff', 7)]
    o += [wavebars(95, 425, 20, 8, 8, 60, M)]
    return ''.join(o)

def folder_sync():
    return (cloud(150, 50, 1.4) + P('M260 235v70m0 0l-24-24m24 24 24-24', stroke=M, sw=6)
            + R(90, 320, 340, 110, 16, GL, GS) + P('M90 350h110l16-16h214', stroke=GS, sw=3)
            + ''.join(R(120 + i * 100, 372, 80, 40, 8, ['rgba(0,194,255,.35)', 'rgba(46,107,255,.4)', 'rgba(123,92,255,.4)'][i]) for i in range(3)))

def radio():
    return (R(60, 110, 400, 270, 28, '#0f1b2e', '#fff', 4) + P('M110 110L400 40', stroke='#fff', sw=5) + Ci(160, 250, 80, '#16273f', C, 5)
            + ''.join(P(f'M{160 + 56 * __import__("math").cos(a)} {250 + 56 * __import__("math").sin(a)}L{160 + 70 * __import__("math").cos(a)} {250 + 70 * __import__("math").sin(a)}', stroke=C, sw=3) for a in [0.4 * i for i in range(16)])
            + P('M160 250l30-30', stroke=M, sw=6) + ''.join(R(290, 190 + i * 30, 130, 12, 6, 'rgba(255,255,255,.28)') for i in range(4)) + Ci(420, 150, 12, M))

def download():
    return (R(110, 90, 300, 300, 40, GL, GS) + P('M260 150v130m0 0l-48-48m48 48 48-48', stroke=C, sw=12) + P('M170 330h180', stroke='#fff', sw=8)
            + R(70, 340, 90, 70, 12, 'rgba(46,107,255,.5)', '#fff', 3) + R(360, 330, 90, 70, 12, 'rgba(123,92,255,.5)', '#fff', 3))

def blog():
    return (R(80, 70, 280, 350, 20, '#0f1b2e', '#fff', 4) + R(104, 100, 232, 110, 12, 'url(#g1)') + ''.join(R(104, 232 + i * 28, 232 - i * 40, 10, 5, 'rgba(255,255,255,.3)') for i in range(5))
            + R(270, 150, 170, 270, 20, '#16273f', GS, 3) + ''.join(R(292, 182 + i * 30, 126, 9, 4, 'rgba(255,255,255,.3)') for i in range(5)) + P('M380 340l44-44 20 20-44 44-26 6z', 'none', M, 5))

def vps():
    return (P('M260 70l170 90v180l-170 90-170-90V160z', GL, '#fff', 4) + P('M90 160l170 90 170-90M260 250v180', stroke='#fff', sw=4)
            + R(190, 290, 140, 70, 10, '#0b1424', C, 3) + P('M206 316l16 12-16 12M236 342h40', stroke=M, sw=4) + Ci(430, 100, 10, M))

def home():
    o = []
    tiles = [(60, 60, 'rgba(46,107,255,.30)'), (270, 100, 'rgba(0,194,255,.22)'), (60, 240, 'rgba(123,92,255,.28)'), (270, 280, 'rgba(0,224,164,.22)')]
    for x, y, f in tiles:
        o.append(R(x, y, 190, 150, 28, f, GS, 3))
    # servidor
    o += [R(90, 90, 130, 34, 10, GL, '#fff', 3), R(90, 136, 130, 34, 10, GL, '#fff', 3), Ci(108, 107, 5, M), Ci(108, 153, 5, C)]
    # onda
    o += [wavebars(292, 175, 8, 8, 9, 80, '#fff')]
    # M / painel
    o += [P('M95 355V305l35 36 35-36v50', stroke='#fff', sw=8)]
    # nuvem
    o += [cloud(285, 292, .85)]
    return ''.join(o)

ART = {
 'home': home, 'hospedagem': lambda: racks() + cloud(300, 10, .8), 'revenda-hospedagem': lambda: racks(70, 2) + R(300, 250, 150, 130, 16, 'rgba(46,107,255,.35)', '#fff', 3) + ''.join(Ci(335 + i * 40, 300, 12, '#fff') for i in range(3)) + P('M320 345h110', stroke='#fff', sw=4),
 'streaming': mic, 'web-radio-completa': laptop, 'revenda-streaming': network, 'sites-para-radio': browser,
 'conteudos-para-radio': folder_sync, 'radios': radio, 'downloads': download, 'blog': blog, 'vps': vps,
 'institucional': home,
}
TEXT = {
 'home': ('HOSPEDAGEM · STREAMING · AUTOMAÇÃO', 'Tudo para seu site ou rádio ficar online', 'Hospedagem, streaming, automação, sites e conteúdos em um só lugar.'),
 'hospedagem': ('HOSPEDAGEM CPANEL', 'Hospedagem rápida e segura', 'Disco NVMe, SSL grátis e suporte humano.'),
 'revenda-hospedagem': ('REVENDA DE HOSPEDAGEM', 'Abra sua empresa de hospedagem', 'Painel WHM, contas cPanel e marca própria.'),
 'streaming': ('STREAMING DE ÁUDIO', 'Sua rádio não pode parar', 'AutoDJ, ouvintes ilimitados e site incluso.'),
 'web-radio-completa': ('WEB RÁDIO COMPLETA', 'Sua rádio online completa', 'Streaming, AutoDJ, site, player e painel.'),
 'revenda-streaming': ('REVENDA DE STREAMING', 'Streaming é o seu negócio', 'Painel de revenda, marca própria e sub-revendas.'),
 'sites-para-radio': ('SITES PARA RÁDIO', 'O site da sua rádio no ar', '15 modelos com player ao vivo e painel fácil.'),
 'automacao-radio': ('AUTOMAÇÃO DE RÁDIO', 'Mídia Rádio Studio', 'Playlist, cartucheira, grade e hora certa. Teste grátis.'),
 'conteudos-para-radio': ('CONTEÚDOS PARA RÁDIOS', 'Conteúdo novo na sua rádio', 'Programas e programetes atualizados todos os dias.'),
 'radios': ('RÁDIOS ONLINE', 'Ouça rádios ao vivo', 'Cadastre a sua rádio grátis no diretório.'),
 'downloads': ('DOWNLOADS', 'Programas para rádio', 'Automação, encoders e utilitários.'),
 'blog': ('BLOG', 'Novidades e tutoriais', 'Hospedagem, streaming e web rádio.'),
 'vps': ('VPS LINUX · EM BREVE', 'VPS Linux chegando', 'Servidores virtuais para sites e automações.'),
 'institucional': ('MÍDIA SERVER', 'Tecnologia para rádios e sites', 'Hospedagem, streaming e suporte humano.'),
}
def page(slug):
    pill, title, sub = TEXT[slug]
    logo = open(os.path.join(IMG, 'logo-horizontal-dark.svg'), encoding='utf8').read()
    logo = logo.replace('<svg ', '<svg style="height:54px;width:auto" ', 1)
    if slug == 'automacao-radio':
        art = ('<div style="width:520px;height:460px;display:flex;align-items:center"><div style="width:520px;border-radius:16px;overflow:hidden;border:4px solid #fff;box-shadow:0 30px 60px rgba(0,0,0,.5)">'
               f'<img src="file://{IMG}/studio/principal.webp" style="width:100%;display:block"></div></div>')
    else:
        art = f'<svg width="520" height="460" viewBox="0 0 520 460"><defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{C}"/><stop offset=".55" stop-color="{B}"/><stop offset="1" stop-color="{V}"/></linearGradient></defs>{ART[slug]()}</svg>'
    size = 66 if len(title) < 27 else 56
    return f'''<html><head><meta charset="utf-8"><style>
@font-face{{font-family:Sora;font-weight:800;src:url(file://{ROOT}/public/assets/fonts/sora-800.woff2)}}
@font-face{{font-family:Inter;font-weight:500;src:url(file://{ROOT}/public/assets/fonts/inter-500.woff2)}}
@font-face{{font-family:Inter;font-weight:600;src:url(file://{ROOT}/public/assets/fonts/inter-600.woff2)}}
*{{box-sizing:border-box}}body{{margin:0;width:1200px;height:630px;overflow:hidden;font-family:Inter;color:#fff;
background:radial-gradient(900px 500px at 90% -10%,rgba(46,107,255,.55),transparent 60%),radial-gradient(700px 420px at -5% 110%,rgba(123,92,255,.4),transparent 60%),#0b1424;position:relative}}
body:before{{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:48px 48px}}
.l{{position:absolute;left:70px;top:64px;width:600px;height:500px;display:flex;flex-direction:column}}
.pill{{align-self:flex-start;font:600 19px Inter;letter-spacing:.1em;color:{C};border:2px solid rgba(0,194,255,.5);border-radius:999px;padding:8px 20px;margin-bottom:30px}}
h1{{font:800 {size}px/1.08 Sora;margin:0 0 22px;letter-spacing:-.02em}}
.sub{{font:500 28px/1.35 Inter;color:#aab4c8;max-width:540px}}
.foot{{margin-top:auto;display:flex;align-items:center;gap:22px}}.url{{font:600 24px Inter;color:#aab4c8}}
.r{{position:absolute;right:50px;top:85px}}
</style></head><body><div class="l"><div class="pill">{pill}</div><h1>{title}</h1><div class="sub">{sub}</div><div class="foot">{logo}<span class="url">midiaserver.com.br</span></div></div><div class="r">{art}</div></body></html>'''

if __name__ == '__main__':
    for slug in TEXT:
        h = f'/tmp/og_{slug}.html'
        open(h, 'w', encoding='utf8').write(page(slug))
        subprocess.check_call(['node', os.path.join(ROOT, 'tools/shot.js'), h, os.path.join(OUT, slug + '.png'), '1200', '630'])
        print('og', slug)
