'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    livereload = require('gulp-livereload');

gulp.task('phpunit', function(){
  var sys = require('sys'),
  exec = require('child_process').exec;

  exec('php vendor/phpunit/phpunit/phpunit', function(error, stdout){
    sys.puts(stdout);
  });
});

gulp.task('compress', function(){
  gulp.src('public/js/*.js')
    .pipe( concat('final.js') )
    .pipe( uglify() )
    .pipe( gulp.dest('public/js/dist/') );
});

gulp.task('watchPhpunit', function(){
  gulp.watch(['src/*/*.php', 'tests/*/*.php'], { debounceDelay: 2000 }, ['phpunit']);
});

gulp.task('livereload', function(){
  gulp.src('templates/*.html')
  .pipe(livereload());
});

gulp.task('sass', function(){
  gulp.src('public/css/*.sass')
    .pipe(sass({indentedSyntax: true, errLogToConsole: true}))
    .pipe(gulp.dest('public/css/'));
});

gulp.task('watch', function(){
  livereload.listen();
  gulp.watch(['templates/*.html', 'public/*.html', 'public/js/*.js'], ['livereload']);
  gulp.watch('public/css/*.sass', ['sass', 'livereload']);
});

gulp.task('default', ['livereload', 'sass', 'watch']);