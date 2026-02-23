<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>visitEase | About Our Museum</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Same Elegant Palette */
            --bg-dark: #0f0f0f;
            --bg-card: #1a1a1a;
            --gold-accent: #c5a059;
            --text-white: #e0e0e0;
        }

        body {
            font-family: 'Raleway', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-white);
            overflow-x: hidden;
        }

        /* --- Navbar --- */
        .navbar {
            background-color: rgba(15, 15, 15, 0.95); /* Solid Dark Background for this page */
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(197, 160, 89, 0.2);
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            letter-spacing: 2px;
            color: var(--gold-accent) !important;
            text-transform: uppercase;
        }
        .nav-link-custom {
            color: #ccc;
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            margin-left: 25px;
            text-decoration: none;
            transition: color 0.3s;
            cursor: pointer;
        }
        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--gold-accent);
        }

        /* --- Header / Title Section --- */
        .page-header {
            padding: 100px 0 60px;
            text-align: center;
            background: linear-gradient(to bottom, #0f0f0f, #151515);
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            color: var(--gold-accent);
            font-style: italic;
            margin-bottom: 20px;
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: #888;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }

        /* --- About Section --- */
        .about-section {
            padding: 80px 0;
        }
        .about-img-wrapper {
            position: relative;
            padding: 20px;
            border: 1px solid var(--gold-accent);
        }
        .about-img {
            width: 100%;
            height: auto;
            display: block;
            filter: grayscale(30%);
            transition: filter 0.3s;
        }
        .about-img:hover {
            filter: grayscale(0%);
        }
        
        .about-content h3 {
            font-family: 'Playfair Display', serif;
            color: var(--gold-accent);
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .about-content p {
            font-size: 1.05rem;
            line-height: 1.9;
            color: #ccc;
            margin-bottom: 20px;
        }

        /* --- Gallery Section --- */
        .gallery-section {
            padding: 80px 0;
            background-color: var(--bg-card);
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid #333;
            transition: all 0.3s;
        }
        .gallery-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-item:hover {
            border-color: var(--gold-accent);
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            background: rgba(0,0,0,0.8);
            color: var(--gold-accent);
            font-family: 'Playfair Display', serif;
            text-align: center;
            transform: translateY(100%);
            transition: transform 0.3s;
        }
        .gallery-item:hover .gallery-caption {
            transform: translateY(0);
        }

        /* --- Modal Styles (Same as index) --- */
        .modal-content {
            background-color: var(--bg-card);
            border: 2px solid var(--gold-accent);
            border-radius: 0;
        }
        .modal-header { border-bottom: 1px solid rgba(197, 160, 89, 0.2); }
        .modal-title, .form-label { font-family: 'Playfair Display', serif; color: var(--gold-accent); }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #444;
            color: white;
            border-radius: 0;
        }
        .form-control:focus {
            background: rgba(197, 160, 89, 0.1);
            border-color: var(--gold-accent);
            color: white;
            box-shadow: none;
        }
        .btn-gold {
            background-color: var(--gold-accent);
            color: #000;
            border: none;
            padding: 12px;
            width: 100%;
            font-family: 'Playfair Display', serif;
            text-transform: uppercase;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-gold:hover { background-color: #e0b050; }
        
        /* SweetAlert Custom */
        div:where(.swal2-container) div:where(.swal2-popup) {
            border: 1px solid var(--gold-accent) !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">VISITEASE MUSEUM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars" style="color: var(--gold-accent);"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav align-items-center">
                    <a href="home.php" class="nav-link-custom active">Home</a> 
                    <a href="check_status.php" class="nav-link-custom">Track</a>
                    <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#bookingModal">Schedule</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="section-title">About Our Museum</h1>
            <p class="section-subtitle">
                A sanctuary of history, art, and culture. Discover the stories that shaped our world.
            </p>
        </div>
    </header>

    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="about-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1566127444979-b3d2b654e3d7?auto=format&fit=crop&q=80" alt="Museum Interior" class="about-img">
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <div class="about-content">
                        <h3>Preserving the Legacy</h3>
                        <p>
                            Welcome to <strong>VisitEase Museum</strong>, a place where the past comes alive. Established in 1925, our institution has been dedicated to the preservation of rare artifacts, classical art, and historical documents that tell the story of humanity.
                        </p>
                        <p>
                            Our collection spans over centuries, featuring masterpieces from the Renaissance, ancient tools from the Neolithic era, and modern installations that challenge the perception of time and space.
                        </p>
                        <p>
                            We believe that history is not just about remembering dates, but about experiencing the emotions and triumphs of those who came before us.
                        </p>
                        <div class="mt-4">
                            <span class="me-4"><i class="fas fa-landmark text-warning me-2"></i> 50+ Galleries</span>
                            <span><i class="fas fa-users text-warning me-2"></i> Guided Tours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gallery-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 style="font-family: 'Playfair Display'; color: var(--gold-accent);">Our Collection</h2>
                <div style="width: 60px; height: 3px; background: var(--gold-accent); margin: 10px auto;"></div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1572953109213-3be62398eb95?auto=format&fit=crop&q=80" alt="Sculpture">
                        <div class="gallery-caption">The Marble Hall</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1544212976-5917408892d1?auto=format&fit=crop&q=80" alt="Painting">
                        <div class="gallery-caption">Renaissance Art</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1518998053901-5348d3969105?auto=format&fit=crop&q=80" alt="Artifact">
                        <div class="gallery-caption">Ancient Relics</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1582555172866-fc2c7974e4ce?auto=format&fit=crop&q=80" alt="Hallway">
                        <div class="gallery-caption">The Grand Corridor</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1545239351-ef35f43d514b?auto=format&fit=crop&q=80" alt="Modern Art">
                        <div class="gallery-caption">Modern Exhibitions</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1564399579883-451a5d44ec08?auto=format&fit=crop&q=80" alt="Library">
                        <div class="gallery-caption">Historical Archives</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer style="background: #000; padding: 30px; text-align: center; border-top: 1px solid #333;">
        <p style="color: #666; margin: 0;">&copy; 2023 visitEase Museum. All Rights Reserved.</p>
    </footer>

    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Your Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background: #1a1a1a;">
                    <form action="process_booking.php" method="POST" id="bookingForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Time</label>
                                <input type="time" name="time" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Guests</label>
                            <input type="number" name="guests" class="form-control" value="1" min="1" max="10" required>
                        </div>
                        <button type="submit" class="btn btn-gold">Confirm Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SweetAlert functionality for consistency
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            let timerInterval;
            Swal.fire({
                title: 'Processing...',
                html: 'Checking availability.',
                timer: 1000,
                timerProgressBar: true,
                background: '#1a1a1a',
                color: '#c5a059',
                didOpen: () => { Swal.showLoading(); }
            }).then(() => {
                Swal.fire({
                    title: 'Booking Confirmed!',
                    text: 'We look forward to your visit.',
                    icon: 'success',
                    background: '#1a1a1a',
                    color: '#e0e0e0',
                    confirmButtonColor: '#c5a059'
                }).then(() => { form.submit(); });
            });
        });
    </script>
</body>
</html>