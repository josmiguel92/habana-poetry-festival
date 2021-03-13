/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import WOW from "wow.js/dist/wow.min";


//WOW Scroll Spy
const wow = new WOW({
    //disabled for mobile
    mobile: false
});
wow.init();
