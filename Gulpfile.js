'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    livereload = require('gulp-livereload'),
		tar = require('gulp-tar'),
		gzip = require('gulp-gzip');

gulp.task('phpunit', function(){
  var sys = require('sys'),
  exec = require('child_process').exec;

  exec('php vendor/phpunit/phpunit/phpunit', function(error, stdout){
    sys.puts(stdout);
  });
});

gulp.task('makeDist', ['compressJs', 'sass'], function(){

		gulp.src([
			'dbTables.sql',
			'scripts/**',
			'src/**',
			'templates/**',
			'vendor/**',
			'public/**',
			'!public/storage/**',
			], {base: '.', dot: true})
			.pipe(tar('dist.tar'))
			.pipe(gzip())
			.pipe(gulp.dest('.'));

});

gulp.task('compressJs', function(){
  gulp.src(['src_frontend/js/controllers.js', 'src_frontend/js/*.js'])
    .pipe( concat('app.js') )
    //.pipe( uglify() )
    .pipe( gulp.dest('public/js/') );
});

gulp.task('watchPhpunit', function(){
  gulp.watch(['src/*/*.php', 'tests/*/*.php'], { debounceDelay: 2000 }, ['phpunit']);
});

gulp.task('livereload', function(){
  gulp.src('templates/*.html')
  .pipe(livereload());
});

gulp.task('sass', function(){
  gulp.src('src_frontend/sass/*.sass')
    .pipe(sass({indentedSyntax: true, errLogToConsole: true}))
    .pipe(gulp.dest('public/css/'));

  gulp.src('public/landing2/css/*.sass')
    .pipe(sass({indentedSyntax: true, errLogToConsole: true}))
    .pipe(gulp.dest('public/landing2/css/'));
});

gulp.task('watch', function(){
  livereload.listen();
  gulp.watch(['templates/*.html', 'public/*.html', 'public/js/*.js'], ['livereload']);
  gulp.watch(['src_frontend/sass/*.sass', 'public/**/*.sass'], ['sass', 'livereload']);
  gulp.watch('src_frontend/js/*.js', ['compressJs', 'livereload']);
});

gulp.task('default', ['livereload', 'sass', 'watch']);
