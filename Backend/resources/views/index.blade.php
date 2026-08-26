<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Miss Miracle Cosmetics · Miracle Lab</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
  <nav class="landing-nav">
    <div class="logo-container" style="display: flex; align-items: center;">
      <img src="/logo.png.png" alt="Miracle Lab Logo" style="height: 80px; width: auto; transform: scale(1.3); transform-origin: left center; mix-blend-mode: darken;" />
    </div>
    <div class="nav-links">
      <a class="nav-link" id="nav-pricing">S'abonner</a>
      <button class="btn-login" id="btn-goto-login">Connexion</button>
    </div>
    <!-- Hamburger (visible mobile/tablet only via CSS) -->
    <button class="landing-hamburger" id="landing-hamburger" aria-label="Menu">☰</button>
  </nav>

  <!-- Mobile Drawer Menu -->
  <div class="landing-mobile-menu" id="landing-mobile-menu">
    <div class="landing-mobile-drawer">
      <button class="landing-mobile-close" id="landing-mobile-close" aria-label="Fermer">✕</button>
      <a class="landing-mobile-link" href="#features-section">Fonctionnalités</a>
      <a class="landing-mobile-link" href="#roadmap-section">Comment ça marche</a>
      <a class="landing-mobile-link" href="#pricing-section">S'abonner</a>
      <a class="landing-mobile-cta" href="/login">Connexion</a>
      <a class="landing-mobile-cta" href="/register" style="background:#FFD700; color:#1A1A00; box-shadow: 0 4px 12px rgba(255,215,0,0.3);">Accéder au Miracle Lab</a>
    </div>
  </div>

  <header class="hero-section">
    <span class="hero-tag">Laboratoire Révolutionnaire</span>
    <h1 class="hero-title">Perfectionnez vos <span>formulations cosmétiques</span></h1>
    <p class="hero-subtitle">
      Calculez vos pourcentages instantanément, gérez vos phases de fabrication, évitez les conflits d'actifs et exportez vos fiches de fabrication en un clic.
    </p>
    <div class="hero-ctas">
      <button class="btn-primary" id="btn-hero-cta">Accéder au Miracle Lab</button>
    </div>
  </header>

  <section class="features-grid" id="features-section">
    <div class="feature-card">
      <div class="feature-icon">
        <i data-lucide="calculator" style="width: 28px; height: 28px; color: #7C3AED;"></i>
      </div>
      <h3>Calculette Intelligente</h3>
      <p>Ajustement automatique des proportions au gramme près en fonction du poids total de votre lot cosmétique.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <i data-lucide="shield-alert" style="width: 28px; height: 28px; color: #7C3AED;"></i>
      </div>
      <h3>Sécurité & Compatibilité</h3>
      <p>Un moteur chimique intelligent vous alerte en temps réel en cas d'incompatibilité d'actifs (ex: Vitamine C + Rétinol).</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <i data-lucide="coins" style="width: 28px; height: 28px; color: #7C3AED;"></i>
      </div>
      <h3>Coût de revient</h3>
      <p>Évaluez en temps réel le coût de fabrication de vos préparations à partir du prix au kilo de vos ingrédients.</p>
    </div>
  </section>

  <section class="roadmap-section" id="roadmap-section">
    <div class="roadmap-container">
      <div class="roadmap-badge">SIMPLE & RAPIDE</div>
      <h2 class="roadmap-title">Comment ça marche ?</h2>
      <p class="roadmap-subtitle">3 étapes pour formuler vos produits cosmétiques.</p>
      
      <div class="roadmap-steps">
        <div class="roadmap-step">
          <div class="step-circle-wrapper">
            <div class="step-circle">1</div>
          </div>
          <h3 class="step-title">Choisissez</h3>
          <p class="step-desc">Sélectionnez le type de produit (Skincare ou Haircare) et ajoutez vos matières premières dans la bibliothèque.</p>
        </div>
        
        <div class="roadmap-step">
          <div class="step-circle-wrapper">
            <div class="step-circle">2</div>
          </div>
          <h3 class="step-title">Formulez</h3>
          <p class="step-desc">Ajustez vos pourcentages. Le moteur calcule au gramme près et signale les éventuelles incompatibilités.</p>
        </div>
        
        <div class="roadmap-step">
          <div class="step-circle-wrapper">
            <div class="step-circle">3</div>
          </div>
          <h3 class="step-title">Produisez</h3>
          <p class="step-desc">Validez votre composition et générez instantanément votre fiche de fabrication au format PDF.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="pricing-section" id="pricing-section">
    <div class="pricing-container">
      <h2 class="pricing-title">Un tarif unique et transparent</h2>
      <div class="pricing-card">
        <div style="font-weight: 800; font-size: 16px; color: #A78BFA; text-transform: uppercase;">Accès Formulateur</div>
        <div class="price-value">
          <div class="price-main">
            <span class="price-number">15 000</span>
            <span class="price-currency">FCFA</span>
          </div>
        </div>
        <ul class="pricing-features">
          <li><i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Calculette Miracle Lab illimitée (Skincare & Haircare)</li>
          <li><i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Moteur de détection de conflits chimiques complet</li>
          <li><i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Historique des compositions & duplication</li>
          <li><i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Génération de fiches de fabrication PDF professionnelles</li>
          <li><i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Support technique & formulation en direct par chat</li>
        </ul>
        <button class="btn-pricing-cta" id="btn-pricing-buy">Démarrer maintenant</button>
      </div>
    </div>
  </section>

  <footer style="padding: 40px 20px; border-top: 1px solid rgba(124, 58, 237, 0.1); background: #ffffff; text-align: center;">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
        <img src="/logo.png.png" alt="Miracle Lab Logo" style="height: 100px; width: auto; transform: scale(1.4); mix-blend-mode: darken;" />
      </div>
      <div style="font-size: 13px; color: #64748B; font-weight: 500;">© 2026 The Miracle Lab. Tous droits réservés.</div>
    </div>
  </footer>

  @vite(['resources/js/shared.js'])
  <script>
    // Event listeners
    document.getElementById("btn-goto-login")?.addEventListener("click", () => { window.location.href = "/login"; });
    document.getElementById("nav-pricing")?.addEventListener("click", () => { document.getElementById("pricing-section")?.scrollIntoView({ behavior: 'smooth' }); });
    document.getElementById("btn-hero-cta")?.addEventListener("click", () => { window.location.href = "/register"; });
    document.getElementById("btn-pricing-buy")?.addEventListener("click", () => { window.location.href = "/register"; });

    // Mobile Hamburger Menu
    const hamburger = document.getElementById("landing-hamburger");
    const mobileMenu = document.getElementById("landing-mobile-menu");
    const mobileClose = document.getElementById("landing-mobile-close");
    
    hamburger?.addEventListener("click", () => { mobileMenu?.classList.add("open"); });
    mobileClose?.addEventListener("click", () => { mobileMenu?.classList.remove("open"); });
    mobileMenu?.addEventListener("click", (e) => { if (e.target === mobileMenu) mobileMenu.classList.remove("open"); });

    // Close drawer when clicking a link and scroll smoothly
    document.querySelectorAll(".landing-mobile-link").forEach(link => {
      link.addEventListener("click", (e) => {
        mobileMenu?.classList.remove("open");
        const href = link.getAttribute("href");
        if (href && href.startsWith("#")) {
          e.preventDefault();
          document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  </script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    if (window.lucide) {
      lucide.createIcons();
    }
  </script>
</body>
</html>
