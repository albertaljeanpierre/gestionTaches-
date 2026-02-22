import './stimulus_bootstrap.js';
import './vendor/@popperjs/core/core.index.js'; 
import * as Popper from "@popperjs/core"
import './vendor/bootstrap/bootstrap.index.js';

import './vendor/bootstrap/dist/css/bootstrap.min.css'; 
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
 import * as bootstrap from 'bootstrap'
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
