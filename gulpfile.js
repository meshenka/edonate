/**
 *
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @since 2.4.1
 * @package eDonate\Build
 * @license  http://opensource.org/licenses/MIT MIT
 * @copyright 2015 Agence Ecedi http://ecedi.fr
 */

'use strict';
/* global require */
/* global console */
/* global __dirname */

//require
var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var header = require('gulp-header');
var less = require('gulp-less');

//var minifyCSS = require('gulp-minify-css');

var cssnano = require('gulp-cssnano');
var sourcemaps = require('gulp-sourcemaps');

var LessPluginCleanCSS = require('less-plugin-clean-css');
var cleancss = new LessPluginCleanCSS({ advanced: true });
var livereload = require('gulp-livereload');
var path = require('path');
var pkg = require('./package.json');

var jsDest = 'web/js/build';
var bowerSrc = 'app/Resources/assets/vendor/';
var cssDest = 'web/css/build';
var src = 'src/Ecedi/Donate/';
var cssSrc = 'web/bundles/';

var banner = '/**\n'+
    ' * WARNING this filed is generated with gulp. Do not modify here!\n' +
    ' *\n' +
    ' * @author '+ pkg.author + '\n' +
    ' * @version '+ pkg.version + '\n' +
    ' * @package '+ pkg.name + '\n' +
    ' * @license '+ pkg.license + '\n' +
    ' * @copyright '+ pkg.copyright + '\n' +
    ' */\n';

gulp.task('default', [
    'js:ie',
    'js:admin',
    'css:admin',
    'fonts:bootstrap',
    'js:front:footer',
    'js:front:header',
    'css:front:style',
    'css:front:ie',
    'fonts:front',
    'image:front'
    ],
    function() {
});

gulp.task('watch', [
    'watch:js:ie',
    'watch:js:admin',
    'watch:css:admin',
    'watch:fonts:bootstrap',
    'watch:js:front:footer',
    'watch:js:front:header',
    'watch:css:front:style',
    'watch:css:front:ie',
    'watch:fonts:front',
    'watch:image:front'
    ],
    function() {
        gulp.watch('app/Resources/assets/**', ['default']);
        livereload.listen();
        // When dest changes, tell the browser to reload
        gulp.watch('web/**').on('change', livereload.changed);
});


/**
 * AdminBundle JS
 *
 *   {% javascripts
 *       '@jquery_js'
 *       '@DonateCoreBundle/Resources/public/components/modernizr/modernizr.js'
 *       '@bootstrap_js'
 *       '@jquery_ui_js'
 *       '@DonateCoreBundle/Resources/public/components/jquery-ui/ui/minified/datepicker.min.js'
 *       '@DonateCoreBundle/Resources/public/components/jquery-ui/ui/minified/i18n/datepicker-fr.min.js'
 *       '@DonateAdminBundle/Resources/public/js/admin.js'
 *    output='js/admin.js' filter='uglifyjs2' %}
 *       <script type="text/javascript" src="{{ asset_url }}"></script>
 *   {% endjavascripts %}
 */
var jsAdminPath = function(){
    var jqueryJs = bowerSrc +'jquery/dist/jquery.js';
    var modernizrJs = bowerSrc + 'modernizr/modernizr.js';
    var bootstrapJs = bowerSrc +'bootstrap/dist/js/bootstrap.js';
    var jqueryUiJs = bowerSrc + 'jquery-ui/jquery-ui.js';
    var datepickerJs = bowerSrc + 'jquery-ui/ui/datepicker.js';
    var datepickerL10nFrJs = bowerSrc + 'jquery-ui/ui/i18n/datepicker-fr.js';
    var adminJs = src + 'AdminBundle/Resources/public/js/admin.js';
    return [
        jqueryJs,
        modernizrJs,
        bootstrapJs,
        jqueryUiJs,
        datepickerJs,
        datepickerL10nFrJs,
        adminJs
    ];
};

gulp.task('js:admin', function(){

    console.log('build admin.js');

    gulp.src(jsAdminPath())
        .pipe(concat('admin.js'))
        .pipe(uglify())
        .pipe(header(banner))
        .pipe(gulp.dest(jsDest));
});

gulp.task('watch:js:admin', function(){
    return gulp.watch(jsAdminPath(), ['js:admin']);
});

/**
 * AdminBundle css
 *      {% stylesheets
 *           '@fix_bootstrap_css'
 *           'bundles/donateadmin/css/datepicker.css'
 *           'bundles/donateadmin/css/admin.css'
 *        filter='cssrewrite,uglifycss' output='css/admin.css' combine=true %}
 *       <link rel="stylesheet" href="{{ asset_url }}" />
 *       {% endstylesheets %}
 *
 */

var cssAdminPath = function() {

    var bootstrapCss = bowerSrc + 'bootstrap/dist/css/bootstrap.css';
    var datepickerCss = cssSrc + 'donateadmin/css/datepicker.css';
    var adminCss = cssSrc + 'donateadmin/css/admin.css';

    return [
        bootstrapCss,
        datepickerCss,
        adminCss
    ];
};

/**
 * compile admin css
 */
gulp.task('css:admin', function() {


   gulp.src(cssAdminPath())
        .pipe(concat('admin.css'))
        .pipe(sourcemaps.init())
        .pipe(cssnano())
        .pipe(header(banner))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(cssDest));

});

gulp.task('watch:css:admin', function() {
    return  gulp.watch(cssAdminPath(), ['css:admin']);
});

/**
 * move fonts to right folder
 */
var fontsBoostrapPath = function() {
    return [bowerSrc + 'bootstrap/fonts/*'];
};

gulp.task('fonts:bootstrap', function() {
    console.log('copy fonts');
    return gulp.src(fontsBoostrapPath())
        // Copy files to destination
        .pipe(gulp.dest('web/css/fonts'));
});

gulp.task('watch:fonts:bootstrap', function() {
    return gulp.watch(fontsBoostrapPath(), ['fonts:bootstrap']);
});

/**
 * Conditionnal JS
 *
 *  {% javascripts
 *       '@html5shiv_js'
 *       '@respond_js'
 *    output='js/ie.js' filter='uglifyjs2'%}
 *       <script type="text/javascript" src="{{ asset_url }}"></script>
 *   {% endjavascripts %}
 */
var jsIePath = function(){
    var html5shivJs = bowerSrc +'html5shiv/dist/html5shiv.js';
    var respondJs = bowerSrc +'respond/dest/respond.src.js';
    return [html5shivJs, respondJs];
};

gulp.task('js:ie', function(){
    console.log('build ie.js');

    gulp.src(jsIePath())
        .pipe(concat('ie.js'))
        .pipe(uglify())
        .pipe(header(banner))
        .pipe(gulp.dest(jsDest));
});

gulp.task('watch:js:ie', function(){
    gulp.watch(jsIePath(),['js:ie']);
});

/**
 * FrontBundle header js
 *
 *       {% javascripts
 *           '@jquery_js'
 *           '@bootstrap_js'
 *           '@DonateFrontBundle/Resources/public/js/*.min.js'
 *           '@DonateFrontBundle/Resources/public/js/jquery-amountselector.js'
 *        output='js/main.js' filter='uglifyjs2' %}
 *           <script type="text/javascript" src="{{ asset_url }}"></script>
 *       {% endjavascripts %}
 */

var jsFrontHeaderPath = function(){
    var jqueryJs = bowerSrc +'jquery/dist/jquery.js';
    var bootstrapJs = bowerSrc +'bootstrap/dist/js/bootstrap.js';
    var frontJs =  src + 'FrontBundle/Resources/public/js/*.min.js';
    var amountJs = src + 'FrontBundle/Resources/public/js/jquery-amountselector.js';

    return [
        jqueryJs,
        bootstrapJs,
        frontJs,
        amountJs
    ];
};

gulp.task('js:front:header', function(){
    console.log('build main.js');

    gulp.src(jsFrontHeaderPath())
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(header(banner))
    .pipe(gulp.dest(jsDest));
});

gulp.task('watch:js:front:header', function() {
    return gulp.watch(jsFrontHeaderPath(), ['js:front:header']);
});

/**
 * FrontBundle Footer JS
 *         {% javascripts
 *           '@DonateFrontBundle/Resources/public/js/calculator.js'
 *           '@DonateFrontBundle/Resources/public/js/form.js'
 *        output='js/fo.js'  filter='uglifyjs2' %}
 *           <script type="text/javascript" src="{{ asset_url }}"></script>
 *        {% endjavascripts %}
 *
 */
var jsFrontFooterPath = function() {
    var calculatorJs = src + 'FrontBundle/Resources/public/js/calculator.js';
    var formJs = src + 'FrontBundle/Resources/public/js/form.js';

    return [
        calculatorJs,
        formJs,
    ];
};

gulp.task('js:front:footer', function(){

    console.log('build fo.js');

    gulp.src(jsFrontFooterPath())
    .pipe(concat('fo.js'))
    .pipe(uglify())
    .pipe(header(banner))
    .pipe(gulp.dest(jsDest));
});

gulp.task('watch:js:front:footer', function(){
    return gulp.watch(jsFrontFooterPath(), ['js:front:footer']);
});

/**
 * FrontBundle css
 *
 *         {% stylesheets
 *           '@fix_bootstrap_css'
 *           'bundles/donatecore/css/*.min.css'
 *           'bundles/donatefront/css/chosen.css'
 *           'bundles/donatefront/css/*.less'
 *       filter='cssrewrite,uglifycss' output='css/style.css'%}
 *       <link rel="stylesheet" href="{{ asset_url }}" />
 *      {% endstylesheets %}
 */
var cssFrontStylePath = function() {

    var bootstrapCss = bowerSrc + 'bootstrap/dist/css/bootstrap.css';
    var coreMinCsss = cssSrc + 'donatecore/css/*.min.css';
    var chosenCss = cssSrc + 'donatefront/css/chosen.css';
    var frontLess = cssSrc + 'donatefront/css/front.less';

    return [
        bootstrapCss,
        coreMinCsss,
        chosenCss,
        frontLess
    ];
};

gulp.task('css:front:style', function(){


    gulp.src(cssFrontStylePath())
    .pipe(less({
        plugins: [cleancss],
        paths: [ path.join(__dirname, cssSrc, 'donatefront/css/') ]
    }))
    .pipe(concat('style.css'))
    .pipe(sourcemaps.init())
    .pipe(cssnano())
    .pipe(header(banner))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(cssDest));


});

gulp.task('watch:css:front:style', function(){

    return gulp.watch(cssFrontStylePath(), ['css:front:style']);
});

/**
  * FrontBundle ie conditionnal css
  *
  *      {% stylesheets
  *          'bundles/donatefront/css/ie.css'
  *          filter='cssrewrite,uglifycss' output='css/ie.css'%}
  *          <link rel="stylesheet" href="{{ asset_url }}" />
  *      {% endstylesheets %}
  *
  */
var cssFrontIePath = function() {
    return [cssSrc + 'donatefront/css/ie.css'];
};

gulp.task('css:front:ie', function() {

    gulp.src(cssFrontIePath())
    .pipe(concat('ie.css'))
    .pipe(sourcemaps.init())
    .pipe(cssnano())
    .pipe(header(banner))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(cssDest));



});

gulp.task('watch:css:front:ie', function() {
    return gulp.watch(cssFrontIePath(), ['css:front:ie']);
});


/**
 * FrontBundle fonts
 */
var fontsFrontPath = function() {
    return [src + 'FrontBundle/Resources/public/css/polices/*'];
};

gulp.task('fonts:front', function() {
    return gulp.src(fontsFrontPath())
        // Copy files to destination
        .pipe(gulp.dest('web/css/build/polices'));
});


gulp.task('watch:fonts:front', function() {
    return gulp.watch(fontsFrontPath(), ['fonts:front']);
});


/**
 * FrontBundle Images
 */
var imageFrontPaths = function() {
    return [src + 'FrontBundle/Resources/public/images/*'];
};

gulp.task('image:front', function() {
    return gulp.src(imageFrontPaths())
        // Copy files to destination
        .pipe(gulp.dest('web/css/images'));

});

gulp.task('watch:image:front', function() {
    return gulp.watch([imageFrontPaths()], ['image:front']);

});
