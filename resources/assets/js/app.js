
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

require('datatables.net-bs4');
require('datatables.net-buttons-bs4');
require('datatables.net-buttons/js/buttons.html5');
require('datatables.net-select-bs4' );

window.JSZip = require('jszip/dist/jszip.min.js');
window.vfs = require('pdfmake/build/vfs_fonts.js');
window.pdfMake = require('pdfmake/build/pdfmake.min.js');
window.jsPDF = require('jspdf/dist/jspdf.min.js')

window.moment = require('moment');

//window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('example-component', require('./components/ExampleComponent.vue'));

// const app = new Vue({
//     el: '#app'
// });



String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};
