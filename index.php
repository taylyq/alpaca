<?php // index.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Alpaca Travels - I’ll pack the courage to explore.</title>
  <meta name="description" content="Search through the top cities of the world to see new cultures." />
  <meta name="robots" content="index, follow" />
  <meta name="google-site-verification" content="kAwlgTHAoaQm5AUjc8bPJLnXnsc5tR7zNfob1M1i-As" />
  <meta name="robots" content="index, follow" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="images/favicon.ico" />
  <link rel="stylesheet" href="styles.css?v50" />
</head>
<body>
  <header class="top-header">
    <div class="brand">
      <img src="images/logo.png" alt="Alpaca Travels logo" class="brand-logo" />
      <div class="logo-text">Alpaca Travels</div>
    </div>
    <nav id="country-nav" class="country-nav">
      <!-- Country buttons injected by script.js -->
    </nav>
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

  <script src="script.js?v16"></script>
</body>
</html>
