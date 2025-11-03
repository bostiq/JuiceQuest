(function () {
  console.log('Running stockists.js version 1.2.4 - Cache Bust Test');
  // -----------------------
  // Data
  // -----------------------
  let businesses = []; // will be loaded from JSON

  const tabs = [
    { key: 'All', id: 'all' },
    { key: 'Destinational', id: 'destinational' },
    { key: 'inner city', id: 'inner-city' },
    { key: 'in the burbs', id: 'in-the-burbs' },
    { key: 'Adelaide hills', id: 'adelaide-hills' }
  ];

  // -----------------------
  // Helpers
  // -----------------------
  // function googleMapsLink(address, suburb) {
  //   const query = `${address}, ${suburb}, South Australia`;
  //   return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`;
  // }
  
  function googleMapsLink(name, suburb) {
    const query = `${name}, ${suburb}, South Australia`;
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`;
  }

  // -----------------------
  // Rendering
  // -----------------------
  function revealCards(panel) {
  const cards = panel.querySelectorAll('.card');
  cards.forEach((card, i) => {
    card.classList.remove('visible'); // reset
    setTimeout(() => {
      card.classList.add('visible');
    }, i * 50); // stagger by 50ms
  });
}
  // CSV parsing helper function
  function parseCSV(csvText) {
    // Use a regex to handle different line endings (\r\n, \n)
    const lines = csvText.trim().split(/\r\n?|\n/);
    
    if (lines.length === 0) {
      console.error('CSV file is empty');
      return [];
    }
    
    const headers = lines[0].split(',').map(h => h.trim().replace(/^"|"$/g, ''));
    console.log('CSV Headers:', headers);
    
    const data = lines.slice(1).map((line, lineIndex) => {
      // Skip empty lines
      if (line.trim() === '') {
        return null;
      }

      const values = [];
      let currentValue = '';
      let insideQuotes = false;
      
      for (let i = 0; i < line.length; i++) {
        const char = line[i];
        
        if (char === '"') {
          insideQuotes = !insideQuotes;
        } else if (char === ',' && !insideQuotes) {
          values.push(currentValue.trim().replace(/^"|"$/g, ''));
          currentValue = '';
        } else {
          currentValue += char;
        }
      }
      
      // Add the last value
      values.push(currentValue.trim().replace(/^"|"$/g, ''));
      
      // Create object from headers and values
      const obj = {};
      headers.forEach((header, index) => {
        obj[header] = values[index] || '';
      });
      
      // Debug first few rows
      if (lineIndex < 3) {
        console.log(`Row ${lineIndex + 1}:`, obj);
      }
      
      return obj;
    }).filter(Boolean); // Filter out any null (empty) lines
    
    console.log(`Parsed ${data.length} businesses from CSV`);
    return data;
  }

  function renderCards() {
    tabs.forEach(tab => {
      const panel = document.getElementById(tab.id);
      if (!panel) return;

      const grid = panel.querySelector('.grid');
      if (!grid) return;

      grid.innerHTML = '';

      const items = tab.key === 'All'
        ? businesses.slice().sort((a, b) => a.name.localeCompare(b.name, 'en', { sensitivity: 'base' }))
        : businesses
            .filter(b => b.category.toLowerCase() === tab.key.toLowerCase())
            .sort((a, b) => a.name.localeCompare(b.name, 'en', { sensitivity: 'base' }));

      // Update badge count
      const navItem = document.querySelector(`.tab-nav li[data-tab="${tab.id}"]`);
      if (navItem) {
        let badge = navItem.querySelector('.counter-badge');
        if (!badge) {
          badge = document.createElement('div');
          badge.className = 'counter-badge';
          navItem.appendChild(badge);
        }
        badge.textContent = items.length;
      }

      items.forEach(b => {
        const card = document.createElement('article');
        card.className = 'card';
        card.dataset.suburb = (b.suburb || '').toLowerCase();

        const mapsHref = googleMapsLink(b.name, b.suburb);
        const igHandle = (b.instagram || '').replace(/^@/, '');
        const igHref = igHandle ? `https://instagram.com/${igHandle}` : '#';

        card.innerHTML = `
          <h4 class="biz-name">${b.name}</h4>
          <p class="biz-meta"><span class="biz-suburb">${b.suburb}</span></p>
          <p class="biz-address">
            <a href="${mapsHref}" target="_blank" rel="noopener">${b.address}</a>
          </p>
          <p class="biz-instagram">
            <a href="${igHref}" target="_blank" rel="noopener">@${igHandle}</a>
          </p>
        `;
        grid.appendChild(card);
        revealCards(panel);
      });

      const noResults = panel.querySelector('.no-results');
      if (noResults) noResults.hidden = items.length !== 0;
    });
  }

  function setupTabs() {
    const navItems = document.querySelectorAll('.tab-nav li');
    const panels = document.querySelectorAll('.tab-content');

    navItems.forEach(li => {
      li.addEventListener('click', () => {
        const target = li.getAttribute('data-tab');
        if (!target) return;

        navItems.forEach(n => n.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));

        li.classList.add('active');

        const panel = document.getElementById(target);
        if (!panel) return;

        panel.classList.add('active');

        // Clear search on tab switch and show all cards
        const input = panel.querySelector('.search-input');
        if (input) {
          input.value = '';
          filterPanel(panel, '');
        }
      });
    });
  }

  function setupSearch() {
    document.querySelectorAll('.tab-content').forEach(panel => {
      const input = panel.querySelector('.search-input');
      if (!input) return;

      input.addEventListener('input', e => {
        const q = e.target.value.trim().toLowerCase();
        filterPanel(panel, q);
      });
    });
  }

  function filterPanel(panel, query) {
    const cards = panel.querySelectorAll('.card');
    let visible = 0;

    cards.forEach(card => {
      const suburb = card.dataset.suburb || '';
      const show = query === '' || suburb.includes(query);
      card.style.display = show ? '' : 'none';
      if (show) visible++;
      revealCards(panel);
    });

    const noResults = panel.querySelector('.no-results');
    if (noResults) noResults.hidden = visible !== 0;
  }

  function init() {
    renderCards();
    setupTabs();
    setupSearch();

    // Ensure default tab has proper filter state
    let active = document.querySelector('.tab-content.active');
    if (!active) {
      // Fallback to first panel if none marked active
      active = document.querySelector('.tab-content');
      if (active) active.classList.add('active');
    }
    if (active) filterPanel(active, '');
  }

  function fetchBusinessesAndInit() {
    // This URL is a custom REST API endpoint set up in your WordPress theme.
    // It acts as a proxy to fetch the Google Drive CSV, avoiding CORS issues.
    const csvUrl = '/wp-json/jq-stockists/v1/get-csv';

    fetch(csvUrl)
      .then(res => {
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.text();
      })
      .then(csvText => {
        console.log('CSV loaded, first 200 chars:', csvText.substring(0, 200));
        businesses = parseCSV(csvText);
        if (businesses.length === 0) {
          throw new Error('No businesses parsed from CSV');
        }
        init();
      })
      .catch(err => {
        console.error('Failed to load businesses.csv', err);
      });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fetchBusinessesAndInit);
  } else {
    fetchBusinessesAndInit();
  }
})();
