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
var minifyCSS = require('gulp-minify-css');
var LessPluginCleanCSS = require('less-plugin-clean-css'),
    cleancss = new LessPluginCleanCSS({ advanced: true });

var livereload = require('gulp-livereload');

var path = require('path');

// var autoprefixer = require('gulp-autoprefixer');
//var concatCss = require('gulp-concat-css');
// var minifyCSS = require('gulp-minify-css');
// var LessPluginCleanCSS = require('less-plugin-clean-css');
// // var cleancss = new LessPluginCleanCSS({ advanced: true });
// // var livereload = require('gulp-livereload');

var jsDest = 'web/js/build';
var cssDest = 'web/css/build';
var src = 'src/Ecedi/Donate/';
var banner = '/**\n * WARNING this filed is generated with gulp. Do not modify here!\n */\n';

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

/*
replace assetic tag

    {% javascripts
        '@jquery_js'
        '@DonateCoreBundle/Resources/public/components/modernizr/modernizr.js'
        '@bootstrap_js'
        '@jquery_ui_js'
        '@DonateCoreBundle/Resources/public/components/jquery-ui/ui/minified/datepicker.min.js'
        '@DonateCoreBundle/Resources/public/components/jquery-ui/ui/minified/i18n/datepicker-fr.min.js'
        '@DonateAdminBundle/Resources/public/js/admin.js'
     output='js/admin.js' filter='uglifyjs2' %}
        <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}
*/
gulp.task('js:admin', function(){

    console.log('build admin.js');

    var jqueryJs = src +'CoreBundle/Resources/public/components/jquery/dist/jquery.js';
    var modernizrJs = src + 'CoreBundle/Resources/public/components/modernizr/modernizr.js';
    var bootstrapJs = src +'CoreBundle/Resources/public/components/bootstrap/dist/js/bootstrap.js';
    var jqueryUiJs = src + 'CoreBundle/Resources/public/components/jquery-ui/jquery-ui.js';
    var datepickerJs = src + 'CoreBundle/Resources/public/components/jquery-ui/ui/datepicker.js';
    var datepickerL10nFrJs = src + 'CoreBundle/Resources/public/components/jquery-ui/ui/i18n/datepicker-fr.js';
    var adminJs = src + 'AdminBundle/Resources/public/js/admin.js';

    gulp.src([
            jqueryJs,
            modernizrJs,
            bootstrapJs,
            jqueryUiJs,
            datepickerJs,
            datepickerL10nFrJs,
            adminJs
        ])
        .pipe(concat('admin.js'))
        .pipe(uglify())
        .pipe(header(banner))
        .pipe(gulp.dest(jsDest));
});

/*
        {% stylesheets
            '@fix_bootstrap_css'
            'bundles/donateadmin/css/datepicker.css'
            'bundles/donateadmin/css/admin.css'
         filter='cssrewrite,uglifycss' output='css/admin.css' combine=true %}
        <link rel="stylesheet" href="{{ asset_url }}" />
        {% endstylesheets %}

 */
/**
 * compile admin css
 */
gulp.task('css:admin', function() {

    console.log('build admin.css');
    var cssSrc = 'web/bundles/';

    var bootstrapCss = cssSrc + 'donatecore/components/bootstrap/dist/css/bootstrap.css';
    var bootstraThemeCss =  cssSrc + 'donatecore/components/bootstrap/dist/css/bootstrap.css';
    var datepickerCss = cssSrc + 'donateadmin/css/datepicker.css';
    var adminCss = cssSrc + 'donateadmin/css/admin.css';

   gulp.src([
            bootstrapCss,
            bootstraThemeCss,
            datepickerCss,
            adminCss
        ])
        .pipe(concat('admin.css'))
        .pipe(minifyCSS())
        .pipe(header(banner))
        .pipe(gulp.dest(cssDest));

});

/**
 * move fonts to right folder
 */
gulp.task('fonts:bootstrap', ['css:admin'], function() {
    console.log('copy fonts');
    return gulp.src(src + 'CoreBundle/Resources/public/components/bootstrap/fonts/*')
        // Copy files to destination
        .pipe(gulp.dest('web/css/fonts'));
});

/*
   {% javascripts
        '@html5shiv_js'
        '@respond_js'
     output='js/ie.js' filter='uglifyjs2'%}
        <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}
*/
gulp.task('js:ie', function(){
    console.log('build ie.js');

    var html5shivJs = src +'CoreBundle/Resources/public/components/html5shiv/dist/html5shiv.js';
    var respondJs = src +'CoreBundle/Resources/public/components/respond/dest/respond.src.js';
    gulp.src([
            html5shivJs,
            respondJs
        ])
        .pipe(concat('ie.js'))
        .pipe(uglify())
        .pipe(header(banner))
        .pipe(gulp.dest(jsDest));
});


/*
        {% javascripts
            '@jquery_js'
            '@bootstrap_js'
            '@DonateFrontBundle/Resources/public/js/*.min.js'
            '@DonateFrontBundle/Resources/public/js/jquery-amountselector.js'
         output='js/main.js' filter='uglifyjs2' %}
            <script type="text/javascript" src="{{ asset_url }}"></script>
        {% endjavascripts %}
*/
gulp.task('js:front:header', function(){
    console.log('build main.js');

    var jqueryJs = src +'CoreBundle/Resources/public/components/jquery/dist/jquery.js';
    var bootstrapJs = src +'CoreBundle/Resources/public/components/bootstrap/dist/js/bootstrap.js';
    var frontJs =  src + 'FrontBundle/Resources/public/js/*.min.js';
    var amountJs = src + 'FrontBundle/Resources/public/js/jquery-amountselector.js';

    gulp.src([
        jqueryJs,
        bootstrapJs,
        frontJs,
        amountJs
    ])
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(header(banner))
    .pipe(gulp.dest(jsDest));
});


/**
 *         {% javascripts
 *           '@DonateFrontBundle/Resources/public/js/calculator.js'
 *           '@DonateFrontBundle/Resources/public/js/form.js'
 *        output='js/fo.js'  filter='uglifyjs2' %}
 *           <script type="text/javascript" src="{{ asset_url }}"></script>
 *        {% endjavascripts %}
 *
 */
gulp.task('js:front:footer', function(){

    console.log('build fo.js');
    var calculatorJs = src + 'FrontBundle/Resources/public/js/calculator.js';
    var formJs = src + 'FrontBundle/Resources/public/js/form.js';

    gulp.src([
        calculatorJs,
        formJs,
    ])
    .pipe(concat('fo.js'))
    .pipe(uglify())
    .pipe(header(banner))
    .pipe(gulp.dest(jsDest));
});


/**
 *         {% stylesheets
 *           '@fix_bootstrap_css'
 *           'bundles/donatecore/css/*.min.css'
 *           'bundles/donatefront/css/chosen.css'
 *           'bundles/donatefront/css/*.less'
 *       filter='cssrewrite,uglifycss' output='css/style.css'%}
 *       <link rel="stylesheet" href="{{ asset_url }}" />
 *      {% endstylesheets %}
 */
gulp.task('css:front:style', function(){
    var cssSrc = 'web/bundles/';

    var bootstrapCss = cssSrc + 'donatecore/components/bootstrap/dist/css/bootstrap.css';
    var coreMinCsss = cssSrc + 'donatecore/css/*.min.css';
    var chosenCss = cssSrc + 'donatefront/css/chosen.css';
    var frontLess = cssSrc + 'donatefront/css/front.less';

    gulp.src([
        bootstrapCss,
        coreMinCsss,
        chosenCss,
        frontLess
    ])
    .pipe(less({
        plugins: [cleancss],
        paths: [ path.join(__dirname, cssSrc, 'donatefront/css/') ]
    }))
    .pipe(concat('style.css'))
    .pipe(minifyCSS())
    .pipe(header(banner))
    .pipe(gulp.dest(cssDest));


});

/*
        {% stylesheets
            'bundles/donatefront/css/ie.css'
            filter='cssrewrite,uglifycss' output='css/ie.css'%}
            <link rel="stylesheet" href="{{ asset_url }}" />
        {% endstylesheets %}

 */
gulp.task('css:front:ie', function() {
    var cssSrc = 'web/bundles/';

    var ieCss = cssSrc + 'donatefront/css/ie.css';

    gulp.src(ieCss)
    .pipe(concat('ie.css'))
    .pipe(minifyCSS())
    .pipe(header(banner))
    .pipe(gulp.dest(cssDest));



});


/**
 * move fonts to right folder
 */
gulp.task('fonts:front', function() {
    return gulp.src(src + 'FrontBundle/Resources/public/css/polices/*')
        // Copy files to destination
        .pipe(gulp.dest('web/css/build/polices'));
});


gulp.task('image:front', function() {
    return gulp.src(src + 'FrontBundle/Resources/public/images/*')
        // Copy files to destination
        .pipe(gulp.dest('web/css/images'));

});

/**
 * watch
 *
 * TODO ajuster tous ca!!
 */
gulp.task('watch', function() {

    // Folders to watch and tasks to execute
    gulp.watch([src + '/fonts/*'], ['fonts']);
    gulp.watch([src + '/less/*'], ['less']);
    gulp.watch([src + '/js/*'], ['js']);
    gulp.watch([src + '/img/*'], ['img']);
    gulp.watch([src + '/html/*'], ['html']);
    gulp.watch([bower_dir + '/**/*.css'], ['libJS', 'libCSS', 'libFonts']);

    livereload.listen();
    // When dest changes, tell the browser to reload
    gulp.watch(dest + '/**').on('change', livereload.changed);
});