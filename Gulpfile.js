'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    livereload = require('gulp-livereload');

gulp.task('livereload', function(){
  gulp.src('templates/*.html')
  .pipe(livereload());
});

gulp.task('sass', function(){
  gulp.src('public/css/*.sass')
    .pipe(sass({indentedSyntax: true}))
    .pipe(gulp.dest('public/css/'));
});

gulp.task('watch', function(){
  livereload.listen();
  gulp.watch('templates/*.html', ['livereload']);
  gulp.watch('public/css/*.sass', ['sass', 'livereload']);
});

gulp.task('default', ['livereload', 'sass', 'watch']);