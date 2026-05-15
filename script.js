const countryNav = document.getElementById('country-nav');
const countrySelect = document.getElementById('country-select');
const cityNav = document.getElementById('city-nav');
const journalGrid = document.getElementById('journal-grid');
const emptyState = document.getElementById('empty-state');
const heroTitle = document.getElementById('hero-title');
const heroIntro = document.getElementById('hero-intro');
const nextCityButton = document.getElementById('next-city-button');

let currentCountryId = null;
let currentCountryName = null;
let currentCityId = null;
let currentCities = [];
let firstLoadDone = false;

const COUNTRY_CONTINENTS = {
  Austria: 'Europe',
  China: 'Asia',
  Colombia: 'South America',
  'Costa Rica': 'North America',
  Croatia: 'Europe',
  Egypt: 'Africa',
  'El Salvador': 'North America',
  France: 'Europe',
  Germany: 'Europe',
  Greece: 'Europe',
  'Hong Kong': 'Asia',
  India: 'Asia',
  Ireland: 'Europe',
  Italy: 'Europe',
  Japan: 'Asia',
  Mexico: 'North America',
  Morocco: 'Africa',
  Netherlands: 'Europe',
  Poland: 'Europe',
  Portugal: 'Europe',
  Russia: 'Europe',
  'South Korea': 'Asia',
  Spain: 'Europe',
  Thailand: 'Asia',
  Turkey: 'Asia',
  Ukraine: 'Europe',
  USA: 'North America',
  Vietnam: 'Asia',
};

const CONTINENT_ORDER = [
  'Africa',
  'Asia',
  'Europe',
  'North America',
  'South America',
  'Oceania',
  'Other',
];

async function fetchJSON(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) {
      console.error('HTTP error', res.status, url);
      return null;
    }
    const data = await res.json();
    if (data && data.error) {
      console.error('API error', data.error, url);
      return null;
    }
    return data;
  } catch (err) {
    console.error('Network or parse error', err, url);
    return null;
  }
}

/* HERO */
function updateHeroFromCity(city, countryName) {
  if (!city) return;

  heroTitle.textContent = `${city.name}, ${countryName || ''}`
    .trim()
    .replace(/,\s*$/, '');

  if (city.summary && city.summary.trim() !== '') {
    heroIntro.textContent = city.summary;
  } else {
    heroIntro.textContent =
      `${city.name} carries its own rhythm—walk a few blocks, listen closely, and you begin to hear how this place breathes and tells its stories.`;
  }
}

/* COUNTRIES */
async function loadCountries() {
  const countries = await fetchJSON('api.php?action=countries');
  if (!countries || !Array.isArray(countries) || countries.length === 0) {
    console.error('No countries returned');
    if (countrySelect) {
      countrySelect.innerHTML = '<option value="">Countries unavailable</option>';
      countrySelect.disabled = true;
    }
    return;
  }

  renderCountrySelect(countries);

  if (!firstLoadDone) {
    const randomCountry = countries[Math.floor(Math.random() * countries.length)];
    await selectCountry(randomCountry, true);
    firstLoadDone = true;
  }
}

function renderCountrySelect(countries) {
  if (!countrySelect) return;

  const countriesByContinent = countries.reduce((groups, country) => {
    const continent = COUNTRY_CONTINENTS[country.name] || 'Other';
    if (!groups.has(continent)) {
      groups.set(continent, []);
    }
    groups.get(continent).push(country);
    return groups;
  }, new Map());

  countrySelect.innerHTML = '';
  countrySelect.disabled = false;

  CONTINENT_ORDER
    .filter(continent => countriesByContinent.has(continent))
    .forEach(continent => {
      const group = document.createElement('optgroup');
      group.label = continent;

      countriesByContinent.get(continent)
        .sort((a, b) => a.name.localeCompare(b.name))
        .forEach(country => {
          const option = document.createElement('option');
          option.value = country.id;
          option.textContent = country.name;
          option.dataset.name = country.name;
          group.appendChild(option);
        });

      countrySelect.appendChild(group);
    });

  countrySelect.addEventListener('change', () => {
    const selectedOption = countrySelect.selectedOptions[0];
    if (!selectedOption) return;

    selectCountry({
      id: selectedOption.value,
      name: selectedOption.dataset.name || selectedOption.textContent,
    }, false);
  });
}

async function selectCountry(country, autoPickRandomCity) {
  currentCountryId = country.id;
  currentCountryName = country.name;
  currentCityId = null;

  if (countrySelect && String(countrySelect.value) !== String(country.id)) {
    countrySelect.value = country.id;
  }

  clearJournals();
  await loadCities(country.id, autoPickRandomCity, country.name);
}

/* CITIES */
async function loadCities(countryId, autoPickRandomCity, countryName) {
  const cities = await fetchJSON(`api.php?action=cities&country_id=${countryId}`);
  if (!cities || !Array.isArray(cities)) {
    console.error('No cities returned for country', countryId);
    currentCities = [];
    updateNextButtonState();
    return;
  }

  currentCities = cities;
  currentCountryName = countryName || currentCountryName;
  updateNextButtonState();

  cityNav.innerHTML = '';

  cities.forEach(city => {
    const btn = document.createElement('button');
    btn.textContent = city.name;
    btn.dataset.id = city.id;
    btn.addEventListener('click', () => {
      currentCityId = city.id;
      setActiveButton(cityNav, btn);
      updateHeroFromCity(city, currentCountryName);
      loadJournals(city.id);
    });
    cityNav.appendChild(btn);
  });

  if (cities.length === 0) {
    emptyState.textContent = 'No cities yet for this country.';
    emptyState.style.display = 'block';
    return;
  }

  emptyState.textContent = 'Choose a city to see journals.';
  emptyState.style.display = 'block';

  if (autoPickRandomCity) {
    const randomCity = cities[Math.floor(Math.random() * cities.length)];
    currentCityId = randomCity.id;

    const randomCityBtn = Array.from(cityNav.querySelectorAll('button'))
      .find(b => Number(b.dataset.id) === Number(randomCity.id));
    if (randomCityBtn) {
      setActiveButton(cityNav, randomCityBtn);
    }

    updateHeroFromCity(randomCity, currentCountryName);
    await loadJournals(randomCity.id);
  }
}

/* JOURNALS (with optional video_url, collage sizing) */
async function loadJournals(cityId) {
  const journals = await fetchJSON(`api.php?action=journals&city_id=${cityId}`);
  journalGrid.innerHTML = '';

  if (!journals || !Array.isArray(journals) || journals.length === 0) {
    emptyState.textContent = 'No journals yet for this city.';
    emptyState.style.display = 'block';
    return;
  }

  emptyState.style.display = 'none';

  const sizeClasses = ['size-small', 'size-medium', 'size-large'];

  journals.forEach(j => {
    const card = document.createElement('article');
    card.className = 'journal-card';

    // Random collage size
    const randomClass = sizeClasses[Math.floor(Math.random() * sizeClasses.length)];
    card.classList.add(randomClass);

    const img = document.createElement('img');
    img.src = j.photo_url || 'https://via.placeholder.com/400x250?text=Travel+Photo';
    img.alt = j.title || 'Travel journal photo';

    const body = document.createElement('div');
    body.className = 'journal-card-body';

    const title = document.createElement('h3');
    title.className = 'journal-title';
    title.textContent = j.title;

    const content = document.createElement('div');
    content.className = 'journal-content';

    const textP = document.createElement('p');
    textP.textContent = j.content || '';
    content.appendChild(textP);

    const safeVideoUrl = j.video_url && j.video_url.trim() !== ''
      ? getSafeEmbedUrl(j.video_url)
      : null;

    if (safeVideoUrl) {
      const iframe = document.createElement('iframe');
      iframe.src = safeVideoUrl;
      iframe.title = 'YouTube video player';
      iframe.frameBorder = '0';
      iframe.allow =
        'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.referrerPolicy = 'strict-origin-when-cross-origin';
      iframe.allowFullscreen = true;

      const videoWrapper = document.createElement('div');
      videoWrapper.className = 'journal-video-wrapper';
      videoWrapper.appendChild(iframe);

      content.appendChild(videoWrapper);
    }

    body.appendChild(title);
    body.appendChild(content);
    card.appendChild(img);
    card.appendChild(body);
    journalGrid.appendChild(card);
  });
}

/* NEXT CITY BUTTON */
function updateNextButtonState() {
  if (!nextCityButton) return;
  const enabled = currentCities && currentCities.length > 1;
  nextCityButton.disabled = !enabled;
}

function goToNextCity() {
  if (!currentCities || currentCities.length === 0 || currentCityId == null) return;

  const idx = currentCities.findIndex(c => Number(c.id) === Number(currentCityId));
  if (idx === -1) return;

  const nextIdx = (idx + 1) % currentCities.length;
  const nextCity = currentCities[nextIdx];
  currentCityId = nextCity.id;

  const nextBtn = Array.from(cityNav.querySelectorAll('button'))
    .find(b => Number(b.dataset.id) === Number(nextCity.id));
  if (nextBtn) {
    setActiveButton(cityNav, nextBtn);
  }

  updateHeroFromCity(nextCity, currentCountryName);
  loadJournals(nextCity.id);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* HELPERS */
function setActiveButton(container, activeBtn) {
  Array.from(container.querySelectorAll('button')).forEach(btn => {
    btn.classList.remove('active');
  });
  activeBtn.classList.add('active');
}

function getSafeEmbedUrl(url) {
  try {
    const parsedUrl = new URL(url);
    const host = parsedUrl.hostname.replace(/^www\./, '');

    if (host === 'youtube.com' || host === 'youtube-nocookie.com') {
      if (parsedUrl.pathname.startsWith('/embed/')) {
        return parsedUrl.href;
      }

      if (parsedUrl.pathname === '/watch' && parsedUrl.searchParams.has('v')) {
        return `https://www.youtube-nocookie.com/embed/${encodeURIComponent(parsedUrl.searchParams.get('v'))}`;
      }
    }

    if (host === 'youtu.be') {
      const videoId = parsedUrl.pathname.replace('/', '');
      if (videoId) {
        return `https://www.youtube-nocookie.com/embed/${encodeURIComponent(videoId)}`;
      }
    }
  } catch (err) {
    console.error('Invalid video URL', err);
  }

  return null;
}

function clearJournals() {
  journalGrid.innerHTML = '';
  emptyState.textContent = 'Choose a city to see journals.';
  emptyState.style.display = 'block';
}

/* INIT */
document.addEventListener('DOMContentLoaded', () => {
  if (nextCityButton) {
    nextCityButton.addEventListener('click', goToNextCity);
  }
  loadCountries();
});
