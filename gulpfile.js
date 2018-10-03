var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');

var path = './assets/';

gulp.task('js', function () {
    return gulp.src(path + 'menu.js')
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(path));
});

gulp.task('watch', function () {
    gulp.watch(path + 'Menu.js', gulp.series('js'));
});

gulp.task('default', gulp.series('js', 'watch'));