// create global $ and jQuery variables
global.$ = global.jQuery = $;
window.moment = require('moment');

// loads the Bootstrap jQuery plugins
import 'bootstrap-sass/assets/javascripts/bootstrap/collapse.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/dropdown.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/modal.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/transition.js';

// loads the code syntax highlighting library
import './highlight.js';

//datatables
import 'bootstrap-daterangepicker/daterangepicker.js';
import 'datatables.net/js/jquery.dataTables.js';
import './../../public/bundles/sgdatatables/js/pipeline.js';
