<?php // index.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Alpaca Travels - I’ll pack the courage to explore.</title>
  <meta name="description" content="Search through the top cities of the world to see new cultures." />
  <meta name="robots" content="index, follow" />
  <meta name="google-site-verification" content="kAwlgTHAoaQm5AUjc8bPJLnXnsc5tR7zNfob1M1i-As" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="images/favicon.ico" />
  <link rel="stylesheet" href="styles.css?v52" />
</head>
<body>
  <header class="top-header">
    <div class="brand">
      <img src="images/logo.png" alt="Alpaca Travels logo" class="brand-logo" />
      <div class="logo-text">Alpaca Travels</div>
    </div>
    <div class="country-picker" id="country-nav">
      <label for="country-select">Country</label>
      <select id="country-select" aria-label="Choose a country">
        <option value="">Loading countries...</option>
      </select>
    </div>
  </header>

  <section class="sub-header">
    <div class="sub-title">Cities</div>
    <nav id="city-nav" class="city-nav">
      <!-- City buttons injected by script.js -->
    </nav>
  </section>

  <main class="main-content">
    <!-- HERO summary for selected city -->
    <section class="hero">
      <div class="hero-text">
        <h1 id="hero-title">Alpaca Travels</h1>
        <p id="hero-intro" class="hero-quote">
          Follow a wandering trail of memories, one city at a time.
        </p>
      </div>
    </section>

    <!-- JOURNAL GRID -->
    <section id="journal-grid" class="journal-grid">
      <!-- Journal cards injected by script.js -->
    </section>

    <!-- NEXT CITY BUTTON -->
    <div class="next-city-wrapper">
      <button id="next-city-button" class="next-city-button">
        Next city in this country &rarr;
      </button>
    </div>

    <div id="empty-state" class="empty-state">
      Choose a country and city to see journals.
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <img src="images/logo.png" alt="" class="footer-logo" aria-hidden="true" />
        <div>
          <div class="footer-name">Alpaca Travels</div>
          <p class="footer-slogan">I&apos;ll pack the courage to travel</p>
        </div>
      </div>

      <nav class="footer-links" aria-label="Footer links">
        <a href="#country-select">Explore cities</a>
        <a href="login.php">Log in</a>
        <a href="mailto:info@alpacatravels.com">info@alpacatravels.com</a>
      </nav>
    </div>
  </footer>

  <script src="script.js?v17"></script>
</body>
</html>
