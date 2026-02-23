<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VisitEase | Explore Exhibits</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --cream:   #f7f2ea;
    --cream2:  #efe8d8;
    --cream3:  #e8dfc8;
    --white:   #ffffff;
    --dark:    #231e14;
    --muted:   #6b6050;
    --light:   #a39078;
    --gold:    #b8842a;
    --brown:   #7c5c2e;
    --terra:   #c4704a;
    --sage:    #6b7c5c;
    --black:   #1e1a14;
    --border:  rgba(184,132,42,0.18);
    --shadow:  rgba(124,92,46,0.10);
    --fd: 'Playfair Display', serif;
    --fh: 'Cormorant Garamond', serif;
    --fb: 'DM Sans', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { font-family: var(--fb); color: var(--dark); background: var(--cream); overflow-x: hidden; }
h1,h2,h3,h4 { font-family: var(--fh); color: var(--dark) !important; }
h5,h6 { font-family: var(--fb); color: var(--dark) !important; }

/* ── NAVBAR ── */
.navbar { background: rgba(247,242,234,0.97); backdrop-filter: blur(20px); padding: 0.42rem 0; border-bottom: 1px solid var(--border); z-index: 1000; }
.navbar-brand { font-family: var(--fh); font-size: 1.4rem; letter-spacing: 3px; color: var(--brown) !important; font-weight: 600; }
.nav-link-custom { color: var(--dark); font-weight: 500; font-size: 0.72rem; margin-left: 18px; text-decoration: none; transition: color .3s; text-transform: uppercase; letter-spacing: 2px; position: relative; }
.nav-link-custom::after { content:''; position:absolute; bottom:-3px; left:0; width:0; height:1px; background:var(--gold); transition:width .3s; }
.nav-link-custom:hover::after, .nav-link-custom.active::after { width:100%; }
.nav-link-custom:hover, .nav-link-custom.active { color: var(--gold) !important; }
.btn-book { background:var(--brown); color:#fff !important; padding:6px 18px; border-radius:2px; font-size:0.7rem; font-weight:500; text-transform:uppercase; letter-spacing:2px; border:1px solid var(--brown); text-decoration:none; transition:all .3s; }
.btn-book:hover { background:transparent; color:var(--brown) !important; }

/* ── HERO ── */
.hero { margin-top: 50px; background: var(--black); position: relative; overflow: hidden; padding: 60px 0 40px; }
.hero::before { content:''; position:absolute; top:0; left:-100%; width:300%; height:100%; background: repeating-linear-gradient(72deg, transparent 0, transparent 60px, rgba(184,132,42,.04) 60px, rgba(184,132,42,.04) 61px); animation: stripes 18s linear infinite; }
@keyframes stripes { to { transform: translateX(33.33%); } }
.hero::after { content:''; position:absolute; top:-40%; right:-10%; width:580px; height:580px; background: radial-gradient(circle, rgba(184,132,42,.13) 0%, transparent 65%); border-radius:50%; pointer-events:none; animation: orb 8s ease-in-out infinite alternate; }
@keyframes orb { from{transform:translateY(0) scale(1)} to{transform:translateY(28px) scale(1.05)} }
.hero-inner { position:relative; z-index:2; text-align:center; }
.hero-eyebrow { display:inline-flex; align-items:center; gap:10px; font-family:var(--fb); font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:4px; color:var(--gold) !important; margin-bottom:12px; }
.hero-eyebrow::before, .hero-eyebrow::after { content:''; width:24px; height:1px; background:var(--gold); }
.hero h1 { font-family:var(--fd); font-size:3.2rem; font-weight:700; color:#f2ece0 !important; line-height:1.08; margin-bottom:12px; }
.hero h1 em { color:var(--gold) !important; font-style:italic; }
.hero-sub { font-size:.9rem; color:rgba(242,236,224,.6); font-weight:300; max-width:520px; margin:0 auto 20px; line-height:1.6; }
.breadcrumb-bar { display:inline-flex; align-items:center; gap:8px; font-size:.7rem; font-weight:500; text-transform:uppercase; letter-spacing:2px; color:rgba(242,236,224,.4); }
.breadcrumb-bar a { color:rgba(242,236,224,.4); text-decoration:none; }
.breadcrumb-bar a:hover { color:var(--gold); }
.breadcrumb-bar .sep { color:var(--gold); font-size:.6rem; }

/* ── COUNTERS ── */
.counters { background:var(--black); border-top:1px solid rgba(184,132,42,.2); border-bottom:1px solid rgba(184,132,42,.2); padding:30px 0; }
.cnt-item { text-align:center; padding:0 10px; border-right:1px solid rgba(255,255,255,.07); }
.cnt-item:last-child { border-right:none; }
.cnt-val { font-family:var(--fd); font-size:2rem; font-weight:700; color:var(--gold); line-height:1; }
.cnt-lbl { font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:2px; color:rgba(242,236,224,.42); margin-top:5px; }

/* ── SECTION HELPERS ── */
.sp  { padding: 60px 0; }
.sps { padding: 52px 0; }
.slabel { display:inline-flex; align-items:center; gap:8px; font-family:var(--fb); font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:3px; color:var(--terra) !important; margin-bottom:8px; }
.slabel::after { content:''; width:25px; height:1px; background:var(--terra); }
.ornament { display:flex; align-items:center; justify-content:center; gap:12px; margin:10px auto 0; color:var(--gold); font-size:.7rem; }
.ornament::before, .ornament::after { content:''; width:40px; height:1px; background:linear-gradient(90deg,transparent,var(--gold)); }
.ornament::after { background:linear-gradient(90deg,var(--gold),transparent); }

/* ── FEATURE CARDS ── */
#features { background:var(--cream); }
.fcard { background:var(--white); border:1px solid rgba(184,132,42,.1); border-radius:4px; padding:26px 22px; height:100%; position:relative; overflow:hidden; box-shadow:0 2px 12px var(--shadow); transition:all .35s ease; }
.fcard::before { content:''; position:absolute; bottom:0; left:0; width:100%; height:0; background:linear-gradient(to top,rgba(184,132,42,.04),transparent); transition:height .35s; }
.fcard::after  { content:''; position:absolute; top:0; left:0; width:3px; height:0; background:var(--gold); transition:height .35s; }
.fcard:hover { transform:translateY(-5px); box-shadow:0 14px 32px rgba(124,92,46,.13); border-color:rgba(184,132,42,.3); }
.fcard:hover::before, .fcard:hover::after { height:100%; }
.fcard:hover .ficon { background:var(--gold); }
.fcard:hover .ficon i { color:#fff; }
.fnum { position:absolute; top:14px; right:16px; font-family:var(--fd); font-size:2.6rem; font-weight:700; color:rgba(184,132,42,.07); line-height:1; pointer-events:none; transition:color .35s; }
.fcard:hover .fnum { color:rgba(184,132,42,.14); }
.ficon { width:44px; height:44px; background:var(--cream2); border:1px solid var(--border); border-radius:3px; display:flex; align-items:center; justify-content:center; margin-bottom:14px; transition:all .3s; }
.ficon i { color:var(--gold); font-size:.95rem; transition:color .3s; }
.fcard h6 { font-weight:700 !important; font-size:.95rem !important; color:var(--dark) !important; margin-bottom:7px; }
.fcard p  { color:var(--muted); font-size:.84rem; line-height:1.65; margin:0; }

/* ── BIOGRAPHY (SAKTO LANG, WALANG SOBRANG SPACE) ── */
#bio { background:var(--cream2); border-top:1px solid var(--border); border-bottom:1px solid var(--border); }

.bio-title-block { margin-bottom: 20px; }
.bio-title-block .slabel { color: var(--brown) !important; margin-bottom: 5px;}
.bio-title-block .slabel::after { background: var(--brown); }
.bio-main-title { font-family: var(--fd); font-size: 2.1rem; font-weight: 700; color: var(--dark) !important; line-height: 1.2; margin-bottom: 0; }
.bio-main-title em { color: var(--gold) !important; font-style: italic; }

/* Inalis na lahat ng sobrang max-width, sakto lang para sa content */
.bio-wrapper { max-width: 900px; margin: 0 auto; } 

/* Photo — Pinaliit at inayos para sumakto sa text */
.bio-photo { border-radius:4px; overflow:hidden; position:relative; height:100%; box-shadow:0 8px 24px rgba(30,26,20,.15); border:1px solid rgba(184,132,42,.2); }
.bio-photo img { width:100%; height:100%; object-fit:cover; object-position:top center; display:block; }
.bio-photo-overlay { position:absolute; bottom:0; left:0; right:0; background:linear-gradient(to top,rgba(30,26,20,.95) 0%,rgba(30,26,20,.4) 60%,transparent 100%); padding:20px 15px 12px; }
.bio-photo::before { content:''; position:absolute; top:8px; left:8px; width:25px; height:25px; border-top:2px solid var(--gold); border-left:2px solid var(--gold); z-index:2; }
.bio-photo::after  { content:''; position:absolute; bottom:8px; right:8px; width:25px; height:25px; border-bottom:2px solid var(--gold); border-right:2px solid var(--gold); z-index:2; }
.bio-badge { display:inline-block; background:var(--gold); color:var(--black); font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; padding:3px 8px; margin-bottom:4px; }

/* Pill */
.pill { display:inline-flex; align-items:center; gap:6px; background:var(--white); border:1px solid var(--border); color:var(--brown); padding:5px 14px; border-radius:50px; font-size:.75rem; font-weight:600; letter-spacing:1px; box-shadow:0 2px 5px var(--shadow); margin-bottom: 10px;}
.pill i { color:var(--gold); font-size:.3rem; }

/* Bio intro — tinanggal ang malaking padding */
.bio-intro { background:var(--white); border-radius:4px; padding:12px 16px; border-left:3px solid var(--gold); border-top:1px solid var(--border); border-right:1px solid var(--border); border-bottom:1px solid var(--border); box-shadow:0 2px 8px var(--shadow); margin-bottom: 10px; }
.bio-intro p { color:var(--muted); font-size:.85rem; line-height:1.6; margin:0; }
.hl { color:var(--brown); font-weight:600; }

/* Edu card — Tinanggal ang lahat ng sobrang space */
.edu-card { background:var(--white); border-radius:4px; padding:14px 18px; border:1px solid var(--border); box-shadow:0 2px 8px var(--shadow); }
.edu-head { display:flex; align-items:center; gap:8px; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid var(--border); }
.edu-icon { width:28px; height:28px; background:var(--brown); border-radius:3px; display:flex; align-items:center; justify-content:center; font-size:.75rem; color:#fff; flex-shrink:0; }
.edu-head h4 { font-family:var(--fb) !important; font-size:.72rem !important; font-weight:700 !important; text-transform:uppercase; letter-spacing:1.5px; color:var(--light) !important; margin:0; }
.edu-card p { color:var(--muted); font-size:.85rem; line-height:1.6; margin-bottom:8px; text-align:justify; }
.edu-card p:last-child { margin-bottom:0; }

/* ── AWARDS ── */
#awards { background:var(--cream); }
.trophy-box { width:55px; height:55px; background:var(--brown); border-radius:4px; display:inline-flex; align-items:center; justify-content:center; font-size:1.3rem; color:#fff; margin:0 auto 12px; box-shadow:0 8px 20px rgba(124,92,46,.25); }
.acard { border-radius:4px; padding:20px 18px; height:100%; position:relative; overflow:hidden; box-shadow:0 3px 12px rgba(0,0,0,.08); transition:all .3s ease; }
.acard:hover { transform:translateY(-4px); box-shadow:0 12px 25px rgba(0,0,0,.15); }
.acard::after { content:''; position:absolute; top:-50%; left:-60%; width:40%; height:200%; background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.12) 50%,transparent 60%); transition:left .5s; }
.acard:hover::after { left:120%; }
.a-icon-row { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.a-icon { width:32px; height:32px; border-radius:3px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:.85rem; color:#fff; flex-shrink:0; }
.a-line { height:2px; width:20px; border-radius:2px; }
.acard h5 { font-family:var(--fb) !important; font-weight:700 !important; font-size:.85rem !important; color:#fff !important; margin-bottom:5px; text-transform:none; }
.acard p  { font-size:.75rem; color:rgba(255,255,255,.7); margin:0; line-height:1.5; }
.ag1{background:linear-gradient(135deg,#9c6b24,#7a4f16)} .ag2{background:linear-gradient(135deg,#4a5a9e,#35447e)}
.ag3{background:linear-gradient(135deg,#9e3e6b,#782d50)} .ag4{background:linear-gradient(135deg,#3d7854,#28553b)}
.ag5{background:linear-gradient(135deg,#a83c50,#7d2538)} .ag6{background:linear-gradient(135deg,#3a6494,#244870)}
.al1{background:#fde68a} .al2{background:#bfdbfe} .al3{background:#fbcfe8} .al4{background:#bbf7d0} .al5{background:#fecaca} .al6{background:#bfdbfe}

/* ── ABOUT / COLLAGE ── */
#about { background:var(--cream2); border-top:1px solid var(--border); }
.cgrid { display:grid; grid-template-columns:repeat(3,1fr); grid-template-rows:repeat(3,110px); gap:6px; }
.ci { width:100%; height:100%; object-fit:cover; border-radius:3px; background:var(--cream3); transition:all .4s; cursor:pointer; border:1px solid var(--border); }
.ci:hover { transform:scale(1.03); z-index:2; box-shadow:0 8px 20px rgba(30,26,20,.2); border-color:var(--gold); }
.c1{grid-column:1/3;grid-row:1/3} .c2{grid-column:3/4;grid-row:1/2} .c3{grid-column:3/4;grid-row:2/3}
.c4{grid-column:1/2;grid-row:3/4} .c5{grid-column:2/3;grid-row:3/4} .c6{grid-column:3/4;grid-row:3/4}
@media(max-width:768px){
.cgrid{grid-template-columns:repeat(2,1fr);grid-template-rows:repeat(4,100px)}
.c1{grid-column:1/3;grid-row:1/3} .c2{grid-column:1/2;grid-row:3/4} .c3{grid-column:2/3;grid-row:3/4}
.c4{grid-column:1/2;grid-row:4/5} .c5{grid-column:2/3;grid-row:4/5} .c6{display:none}
}
.abt h2 { font-family:var(--fd) !important; font-size:2.2rem !important; color:var(--dark) !important; line-height:1.15; margin-bottom:12px; }
.abt h2 em { color:var(--brown) !important; font-style:italic; }
.abt p { color:var(--muted); font-size:.85rem; line-height:1.7; margin-bottom:12px; }
.hlist { list-style:none; padding:0; margin:12px 0 18px; }
.hlist li { display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid rgba(184,132,42,.1); font-size:.8rem; color:var(--muted); line-height:1.4; }
.hlist li:last-child { border-bottom:none; }
.hlist li i { color:var(--gold); font-size:.5rem; margin-top:5px; flex-shrink:0; }
.btn-plan { background:var(--brown); color:#fff !important; padding:10px 25px; border-radius:2px; font-size:.75rem; font-weight:500; text-transform:uppercase; letter-spacing:1.5px; border:1px solid var(--brown); text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:all .3s; }
.btn-plan:hover { background:transparent; color:var(--brown) !important; transform:translateY(-2px); box-shadow:0 6px 15px var(--shadow); }

/* ── FOOTER ── */
footer { background:var(--black); padding:40px 0 20px; border-top:1px solid rgba(255,255,255,.06); }
footer h5, footer h6 { color:#ede5d3 !important; font-family:var(--fh); font-weight:600 !important; }
footer p { color:#a89880; font-size:.8rem; }
.flink { color:#a89880; font-size:.8rem; text-decoration:none; display:block; margin-bottom:6px; transition:all .2s; }
.flink:hover { color:var(--gold); padding-left:5px; }
.fbrand { font-family:var(--fh); font-size:1.4rem; color:#f2ece0 !important; letter-spacing:2px; font-weight:600; display:block; margin-bottom:8px; }

/* ── ANIMATIONS ── */
.rv  { opacity:0; transform:translateY(20px);  transition:opacity .6s ease, transform .6s ease; }
.rvl { opacity:0; transform:translateX(-20px); transition:opacity .6s ease, transform .6s ease; }
.rvr { opacity:0; transform:translateX(20px);  transition:opacity .6s ease, transform .6s ease; }
.rv.on, .rvl.on, .rvr.on { opacity:1; transform:none; }
.d1{transition-delay:.1s} .d2{transition-delay:.2s} .d3{transition-delay:.3s} .d4{transition-delay:.4s}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
<div class="container-fluid px-4 px-lg-5">
<a class="navbar-brand" href="index.php">VISITEASE</a>
<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
<i class="fas fa-bars" style="color:var(--brown)"></i>
</button>
<div class="collapse navbar-collapse justify-content-end" id="nav">
<div class="navbar-nav align-items-center">
<a href="index.php"        class="nav-link-custom">Home</a>
<a href="exhibits.php"     class="nav-link-custom active">Explore</a>
<a href="gallery.php"      class="nav-link-custom">Gallery</a>
<a href="check_status.php" class="nav-link-custom">Track Status</a>
<a href="book.php"         class="btn-book ms-4">Book Visit</a>
</div>
</div>
</div>
</nav>

<section class="hero">
<div class="container-fluid px-4 px-lg-5">
<div class="hero-inner">
<div class="hero-eyebrow">Pedro S. Tolentino Museum</div>
<h1>Explore the <em>Exhibits</em></h1>
<p class="hero-sub">Journey through history, legacy, and culture. Discover the life and achievements of Batangas City's first and most celebrated mayor.</p>
<div class="breadcrumb-bar">
<a href="index.php">Home</a>
<span class="sep"><i class="fas fa-chevron-right fa-xs"></i></span>
<span style="color:var(--gold)">Explore</span>
</div>
</div>
</div>
</section>

<div class="counters">
<div class="container-fluid px-4 px-lg-5">
<div class="row g-0">
<div class="col-6 col-md-3 cnt-item rv d1">
<div class="cnt-val" data-to="5000" data-sfx="+">5000+</div>
<div class="cnt-lbl">Artifacts</div>
</div>
<div class="col-6 col-md-3 cnt-item rv d2">
<div class="cnt-val" data-to="12" data-sfx="">12</div>
<div class="cnt-lbl">Galleries</div>
</div>
<div class="col-6 col-md-3 cnt-item rv d3">
<div class="cnt-val" data-to="70" data-sfx="">70</div>
<div class="cnt-lbl">Years of Legacy</div>
</div>
<div class="col-6 col-md-3 cnt-item rv d4">
<div class="cnt-val" data-to="98" data-sfx="%">98%</div>
<div class="cnt-lbl">Visitor Satisfaction</div>
</div>
</div>
</div>
</div>

<section id="features" class="sp">
<div class="container-fluid px-4 px-lg-5">
<div class="text-center mb-4 rv">
<div class="slabel">What We Offer</div>
<h2 style="font-family:var(--fd);font-size:2.2rem;margin-bottom:8px">Museum Features</h2>
<p style="color:var(--muted);max-width:480px;margin:0 auto;font-size:.85rem;font-weight:300">Discover what makes our collection unique and the diverse range of experiences we offer.</p>
<div class="ornament"><i class="fas fa-landmark fa-xs"></i></div>
</div>
<div class="row g-3">
<div class="col-md-6 col-lg-4 rv d1"><div class="fcard"><span class="fnum">01</span><div class="ficon"><i class="fas fa-landmark"></i></div><h6>Historic Collections</h6><p>Explore our extensive collection of historical artifacts spanning multiple centuries of Filipino heritage.</p></div></div>
<div class="col-md-6 col-lg-4 rv d2"><div class="fcard"><span class="fnum">02</span><div class="ficon"><i class="fas fa-palette"></i></div><h6>Art Exhibitions</h6><p>Experience stunning artwork and curated exhibitions that bring the story of the era to vivid life.</p></div></div>
<div class="col-md-6 col-lg-4 rv d3"><div class="fcard"><span class="fnum">03</span><div class="ficon"><i class="fas fa-book-open"></i></div><h6>Educational Programs</h6><p>Join our educational workshops, guided tours, and interactive learning experiences for all ages.</p></div></div>
<div class="col-md-6 col-lg-4 rv d1"><div class="fcard"><span class="fnum">04</span><div class="ficon"><i class="fas fa-users"></i></div><h6>Group Tours</h6><p>Perfect for schools and organizations. Book a guided group tour tailored to your needs and interests.</p></div></div>
<div class="col-md-6 col-lg-4 rv d2"><div class="fcard"><span class="fnum">05</span><div class="ficon"><i class="fas fa-star"></i></div><h6>Special Events</h6><p>Attend exclusive events, commemorative exhibitions, and cultural celebrations throughout the year.</p></div></div>
<div class="col-md-6 col-lg-4 rv d3"><div class="fcard"><span class="fnum">06</span><div class="ficon"><i class="fas fa-clock"></i></div><h6>Flexible Hours</h6><p>Convenient visiting hours with advance booking available online to secure your preferred time slot.</p></div></div>
</div>
</div>
</section>

<section id="bio" class="sps">
<div class="container-fluid px-4 px-lg-5">

  <div class="bio-title-block rv text-center">
    <div class="slabel">Kasaysayan</div>
    <h2 class="bio-main-title">Maikling Talambuhay ni <em>Pedro S. Tolentino</em></h2>
  </div>

  <div class="bio-wrapper row g-3 align-items-stretch">

    <div class="col-md-5 rvl">
      <div class="bio-photo">
        <img src="pedro.png" alt="Pedro S. Tolentino">
        <div class="bio-photo-overlay">
          <div class="bio-badge">Legacy</div>
          <h3 style="font-family:var(--fd);font-size:1.3rem;color:#f2ece0;font-weight:700;margin-bottom:1px;line-height:1.1">Pedro S. Tolentino</h3>
          <p style="color:var(--gold);font-size:.65rem;font-weight:600;margin:0;letter-spacing:.5px">Kauna-unahang Punong Lungsod</p>
        </div>
      </div>
    </div>

    <div class="col-md-7 rvr d-flex flex-column">

      <div class="d-flex align-items-center gap-2">
        <div class="pill"><i class="fas fa-circle"></i>1905 — 1975<i class="fas fa-circle"></i></div>
        <div style="height:1px;flex:1;background:var(--border)"></div>
      </div>

      <div class="bio-intro">
        <p>Si <span class="hl">Pedro S. Tolentino</span> ay pinanganak noong ika-<span class="hl">29 ng Abril 1905</span> sa baryo Ilijan, Batangas. Bunsong anak nina <span class="hl">Santiago Tolentino</span> at <span class="hl">Emerenciana Silang</span>, at ikinasal kay <span class="hl">Nellei Reyes</span> ng Taal.</p>
      </div>

      <div class="edu-card flex-grow-1">
        <div class="edu-head">
          <div class="edu-icon"><i class="fas fa-landmark"></i></div>
          <h4>Edukasyon at Serbisyo</h4>
        </div>
        <p>Nagsimula siyang mag-aral sa baryo bago inilipat sa <span class="hl">Batangas Intermediate School</span> dahil sa angking talino. Nagtapos ng sekondarya sa <span class="hl">Batangas National High School</span> noong 1924. Naging guro mula 1925 hanggang 1928, at pinadala bilang pensionado sa <span class="hl">Philippine Normal School</span> kung saan nagtapos noong 1931. Kumuha rin ng abogasya at naging ganap na abogado.</p>
        <p>Pumasok siya sa pulitika bilang <span class="hl">Punong Bayan</span> at naging tanyag sa tapat na pamamahala. Siya ang pangunahing tulay upang opisyal na maging lungsod ang Batangas noong Hulyo 23, 1969, kaya kinikilala bilang <span class="hl">Kauna-unahang City Mayor</span> nito.</p>
      </div>

    </div>
  </div>
</div>
</section>

<section id="awards" class="sp">
<div class="container-fluid px-4 px-lg-5">
<div class="text-center mb-4 rv">
<div class="trophy-box"><i class="fas fa-trophy"></i></div>
<div class="slabel" style="justify-content:center">Recognition</div>
<h2 style="font-family:var(--fd);font-size:2.2rem;margin-bottom:8px">Mga Parangal at Pagkilala</h2>
<p style="color:var(--muted);max-width:480px;margin:0 auto;font-size:.85rem;font-weight:300">Mga natatanging karangalan na iginawad kay Mayor Pedro S. Tolentino</p>
</div>
<div class="row g-3">
<div class="col-md-6 col-lg-4 rv d1"><div class="acard ag1"><div class="a-icon-row"><div class="a-icon"><i class="fas fa-award"></i></div><div class="a-line al1"></div></div><h5>Most Outstanding Mayor</h5><p>Nahirang bilang pinakatanyag na punong bayan sa buong lalawigan ng Batangas</p></div></div>
<div class="col-md-6 col-lg-4 rv d2"><div class="acard ag2"><div class="a-icon-row"><div class="a-icon"><i class="fas fa-briefcase"></i></div><div class="a-line al2"></div></div><h5>President ng Batangas Lawyers</h5><p>Nahirang na presidente ng Batangas Lawyers Association</p></div></div>
<div class="col-md-6 col-lg-4 rv d3"><div class="acard ag3"><div class="a-icon-row"><div class="a-icon"><i class="fas fa-globe"></i></div><div class="a-line al3"></div></div><h5>International Rep.</h5><p>Nahalal na representante ng Pilipinas sa Int'l Union of Local Authorities sa Hague.</p></div></div>
<div class="col-md-6 col-lg-4 rv d1"><div class="acard ag4"><div class="a-icon-row"><div class="a-icon"><i class="fas fa-medal"></i></div><div class="a-line al4"></div></div><h5>Kauna-unahang City Mayor</h5><p>Naging kauna-unahang City Mayor ng Batangas noong 1968</p></div></div>
<div class="col-md-6 col-lg-4 rv d2"><div class="acard ag5"><div class="a-icon-row"><div class="a-icon"><i class="fas fa-graduation-cap"></i></div><div class="a-line al5"></div></div><h5>Edukasyon at Serbisyo</h5><p>Pagbuo ng Ilijan Experiment High School — ngayon ay Pedro S. Tolentino Mem. Nat'l High School</p></div></div>
<div class="col-md-6 col-lg-4 rv d3"><div class="acard ag6"><div class="a-icon-row"><div class="a-icon"><i class="fas fa-city"></i></div><div class="a-line al6"></div></div><h5>Pagpapaunlad ng Lungsod</h5><p>Pagbuo ng lungsod ng Batangas sa bisa ng Batas Tagapagpaganap 18919 noong Hulyo 23, 1969</p></div></div>
</div>
</div>
</section>

<section id="about" class="sp">
<div class="container-fluid px-4 px-lg-5">
<div class="row align-items-center g-4">
<div class="col-lg-6 mb-4 mb-lg-0 rvl">
<div style="padding-right:2rem;border-right:1px solid var(--border)">
<div class="cgrid">
<img class="ci c1" src="https://images.unsplash.com/photo-1518998053401-b4373eb8b2bb?auto=format&fit=crop&w=600&q=80" alt="">
<img class="ci c2" src="https://images.unsplash.com/photo-1544928147-79a2dbc1f389?auto=format&fit=crop&w=300&q=80" alt="">
<img class="ci c3" src="https://images.unsplash.com/photo-1566127444979-b3d2b654e3d7?auto=format&fit=crop&w=300&q=80" alt="">
<img class="ci c4" src="https://images.unsplash.com/photo-1574161946006-218412fc6e7c?auto=format&fit=crop&w=300&q=80" alt="">
<img class="ci c5" src="https://images.unsplash.com/photo-1599939571322-792a326e479a?auto=format&fit=crop&w=300&q=80" alt="">
<img class="ci c6" src="https://images.unsplash.com/photo-1533157577549-347e387190d6?auto=format&fit=crop&w=300&q=80" alt="">
</div>
</div>
</div>
<div class="col-lg-6 abt rvr" style="padding-left:2rem">
<div class="slabel">About Our Museum</div>
<h2>About Our <em>Museum</em></h2>
<p>Our museum is a treasure trove of history, art, and culture. We proudly showcase an extensive collection featuring artifacts, photographs, garments, and documents that bring Mayor Tolentino's extraordinary life to light.</p>
<ul class="hlist">
<li><i class="fas fa-circle"></i>Over 5,000 carefully preserved artifacts and documents</li>
<li><i class="fas fa-circle"></i>Dedicated to the life and legacy of Mayor Pedro S. Tolentino</li>
<li><i class="fas fa-circle"></i>Guided tours available for groups and school visits</li>
<li><i class="fas fa-circle"></i>Preserving Batangas City's cultural heritage</li>
</ul>
<a href="book.php" class="btn-plan"><i class="fas fa-arrow-right"></i> Plan Your Visit</a>
</div>
</div>
</div>
</section>

<footer>
<div class="container-fluid px-4 px-lg-5">
<div class="row g-4 border-bottom pb-3 mb-3" style="border-color:rgba(255,255,255,.07)!important">
<div class="col-md-4">
<span class="fbrand">VISITEASE</span>
<p style="color:#8a7e6e;line-height:1.6;max-width:270px">Making museum visits seamless and memorable. Explore history and culture with ease.</p>
</div>
<div class="col-6 col-md-2">
<h6 class="mb-2" style="font-family:var(--fb)!important;font-size:.65rem!important;letter-spacing:2px;text-transform:uppercase">Explore</h6>
<a class="flink" href="exhibits.php">Explore Exhibits</a>
<a class="flink" href="gallery.php">Gallery</a>
<a class="flink" href="#">Virtual Tour</a>
</div>
<div class="col-6 col-md-2">
<h6 class="mb-2" style="font-family:var(--fb)!important;font-size:.65rem!important;letter-spacing:2px;text-transform:uppercase">Visit</h6>
<a class="flink" href="book.php">Book Tickets</a>
<a class="flink" href="check_status.php">Track Status</a>
<a class="flink" href="#">Guidelines</a>
</div>
<div class="col-md-4">
<h6 class="mb-2" style="font-family:var(--fb)!important;font-size:.65rem!important;letter-spacing:2px;text-transform:uppercase">Location</h6>
<p style="color:#8a7e6e;line-height:1.6;margin-bottom:6px"><i class="fas fa-map-marker-alt me-2" style="color:var(--gold)"></i>Barangay Ilijan, Batangas City, Philippines</p>
<p style="color:#8a7e6e;margin-bottom:4px"><i class="fas fa-clock me-2" style="color:var(--gold)"></i>Mon–Sat: 9:00 AM – 6:00 PM</p>
<p style="color:#8a7e6e;margin:0"><i class="fas fa-clock me-2" style="color:var(--gold)"></i>Sunday: 10:00 AM – 5:00 PM</p>
</div>
</div>
<div class="text-center" style="color:#5a5045;font-size:.7rem">
&copy; 2026 VisitEase Museum — Pedro S. Tolentino Museum, Barangay Ilijan, Batangas City. All Rights Reserved.
</div>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const obs = new IntersectionObserver(entries => {
entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('on'); });
}, { threshold: 0.08 });
document.querySelectorAll('.rv,.rvl,.rvr').forEach(el => obs.observe(el));
function runCnt(el) {
const to=parseInt(el.dataset.to), sfx=el.dataset.sfx||'', dur=1500; let t0=null;
(function step(ts){ if(!t0)t0=ts; const p=Math.min((ts-t0)/dur,1), v=Math.floor((1-Math.pow(1-p,3))*to);
el.textContent=v.toLocaleString()+sfx; if(p<1)requestAnimationFrame(step); })(performance.now());
}
const cobs=new IntersectionObserver(entries=>{
entries.forEach(e=>{ if(e.isIntersecting){ runCnt(e.target); cobs.unobserve(e.target); } });
},{threshold:0.5});
document.querySelectorAll('.cnt-val').forEach(el=>cobs.observe(el));
window.addEventListener('load',()=>{
document.querySelectorAll('.hero-eyebrow,.hero h1,.hero-sub,.breadcrumb-bar').forEach((el,i)=>{
el.style.cssText=`opacity:0;transform:translateY(20px);transition:opacity .6s ease ${i*.1}s,transform .6s ease ${i*.1}s`;
setTimeout(()=>{ el.style.opacity='1'; el.style.transform='none'; },40);
});
});
</script>
</body>
</html>