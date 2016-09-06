var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');

var path = './assets/';

gulp.task('js', function () {
    return gulp.src(path + 'Menu.js')
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(path));
});

gulp.task('watch', function () {
    gulp.watch(path + 'Menu.js', ['js']);
});

gulp.task('default', ['js', 'watch']); 