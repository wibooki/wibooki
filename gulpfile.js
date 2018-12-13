var gulp = require('gulp');
var plugins = require('gulp-load-plugins')();
var del = require('del');

var config = {
    assetsDir: 'app/Resources/assets',
    sassPattern: 'sass/**/*.scss',
    production: !!plugins.util.env.production,
    sourceMaps: !plugins.util.env.production,
    bowerDir: 'vendor/bower_components',
    revManifestPath: 'app/Resources/assets/rev-manifest.json'
};

var app = {};
app.addStyle = function(paths, outputFilename) {
    gulp.src(paths)
        .pipe(plugins.if(!plugins.util.env.production, plugins.plumber(function(error) {
            console.log(error.toString());
            this.emit('end');
        })))
        .pipe(plugins.if(config.sourceMaps, plugins.sourcemaps.init()))
        .pipe(plugins.sass())
        .pipe(plugins.concat('css/'+outputFilename))
        .pipe(config.production ? plugins.cleancss({compatibility: 'ie8'}) : plugins.util.noop())
        .pipe(plugins.rev())
        .pipe(plugins.if(config.sourceMaps, plugins.sourcemaps.write('.')))
        .pipe(gulp.dest('web'))
        // write the rev-manifest.json file for gulp-rev
        .pipe(plugins.rev.manifest(config.revManifestPath, {
            merge: true
        }))
        .pipe(gulp.dest('.'));
};

app.addScript = function(paths, outputFilename) {
    gulp.src(paths)
        .pipe(plugins.if(!plugins.util.env.production, plugins.plumber(function(error) {
            console.log(error.toString());
            this.emit('end');
        })))
        .pipe(plugins.if(config.sourceMaps, plugins.sourcemaps.init()))
        .pipe(plugins.concat('js/'+outputFilename))
        .pipe(config.production ? plugins.uglify() : plugins.util.noop())
        .pipe(plugins.rev())
        .pipe(plugins.if(config.sourceMaps, plugins.sourcemaps.write('.')))
        .pipe(gulp.dest('web'))
        // write the rev-manifest.json file for gulp-rev
        .pipe(plugins.rev.manifest(config.revManifestPath, {
            merge: true
        }))
        .pipe(gulp.dest('.'));
};

app.copy = function(srcFiles, outputDir) {
    gulp.src(srcFiles)
        .pipe(gulp.dest(outputDir));
};

gulp.task('styles', function() {
    app.addStyle([
        config.bowerDir+'/bootstrap/dist/css/bootstrap.css',
        config.bowerDir+'/font-awesome/css/font-awesome.css',
        config.assetsDir+'/sass/styles.scss'
    ], 'main.css');
    app.addStyle([
        config.assetsDir+'/css/emails/registration/style.css'
    ], '/emails/registration/style.css');
});

gulp.task('scripts', function() {
    app.addScript([
        config.bowerDir+'/jquery/dist/jquery.js',
        config.bowerDir+'/bootstrap/dist/js/bootstrap.js',
        config.bowerDir+'/bootstrap/js/collapse.js',
        config.bowerDir+'/bootstrap/js/tooltip.js',
        config.bowerDir+'/bootstrap/js/transition.js',
        config.assetsDir+'/js/wibooki.js'
    ], 'site.js');
});

gulp.task('ownscripts', function() {
    app.addScript([
        config.assetsDir+'/js/profileuploadfile.js'
    ], 'profileuploadfile.js');
    app.addScript([
        config.assetsDir+'/js/deleteuserbutton.js'
    ], 'deleteuserbutton.js');
    app.addScript([
        config.assetsDir+'/js/addItineraryLink.js'
    ], 'addItineraryLink.js');
});

gulp.task('fonts', function() {
    app.copy(
        config.bowerDir+'/font-awesome/fonts/*',
        'web/fonts'
    );
});

gulp.task('images', function() {
    app.copy(
        config.assetsDir+'/images/**/*.*',
        'web/images'
    );
});

gulp.task('clean', function() {
    del(config.revManifestPath);
    del('web/css/*');
    del('web/js/*');
    del('web/fonts/*');
});

gulp.task('watch', function() {
    gulp.watch(config.assetsDir+'/'+config.sassPattern, ['styles']);
    gulp.watch(config.assetsDir+'/js/**/*.js', ['scripts']);
});

gulp.task('default', ['clean', 'fonts', 'images', 'styles', 'scripts', 'ownscripts', 'watch']);