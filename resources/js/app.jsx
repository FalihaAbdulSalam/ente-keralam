import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';

// Import FontAwesome from node_modules (official)
import '@fortawesome/fontawesome-free/css/all.min.css';

// Import other CSS files from design
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import '../css/index.css'
import '../css/owl.carousel.css'
import '../css/flaticon-5.css'
import '../css/animate.css'
import '../css/bootstrap.min.css'
import '../css/video.min.css'
import '../css/slick.css'
import '../css/side-demo.css'
import '../css/it-source-2.css'
import '../css/app.css'

const container = document.getElementById('app');
const root = createRoot(container);
root.render(<App />);

// Malayalam font auto-detection and tagging
function containsMalayalam(text) {
  if (!text) return false;
  return /[\u0D00-\u0D7F]/.test(text);
}

function tagMalayalamForElement(node) {
  if (!node) return;

  if (node.nodeType === Node.TEXT_NODE) {
    const value = node.nodeValue;
    if (containsMalayalam(value)) {
      const parent = node.parentElement;
      if (parent && !parent.getAttribute('data-lang')) {
        parent.setAttribute('data-lang', 'ml');
      }
    }
    return;
  }

  if (node.nodeType !== Node.ELEMENT_NODE) return;

  // Attributes that can carry visible strings
  const attributesToCheck = ['placeholder', 'title', 'aria-label', 'alt'];
  for (const attr of attributesToCheck) {
    const val = node.getAttribute ? node.getAttribute(attr) : null;
    if (val && containsMalayalam(val)) {
      if (!node.getAttribute('data-lang')) node.setAttribute('data-lang', 'ml');
      break;
    }
  }

  // Check text content via child nodes to be precise
  const childNodes = node.childNodes ? Array.from(node.childNodes) : [];
  for (const child of childNodes) {
    tagMalayalamForElement(child);
  }
}

function scanMalayalam(root = document.body) {
  try {
    tagMalayalamForElement(root);
  } catch (e) {
    // no-op
  }
}

// Initial scan after DOM ready and after React render
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => scanMalayalam());
} else {
  scanMalayalam();
}

// Observe future DOM changes (dynamic/backend-rendered content)
try {
  const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      if (mutation.type === 'childList') {
        mutation.addedNodes && mutation.addedNodes.forEach((n) => scanMalayalam(n));
      } else if (mutation.type === 'characterData') {
        scanMalayalam(mutation.target);
      } else if (mutation.type === 'attributes' && mutation.target) {
        scanMalayalam(mutation.target);
      }
    }
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
    characterData: true,
    attributes: true,
    attributeFilter: ['placeholder', 'title', 'aria-label', 'alt']
  });
} catch (_) {
  // ignore if MutationObserver unavailable
}
