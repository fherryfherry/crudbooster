const gulp = require('gulp');
const postcss = require('gulp-postcss');
const tailwindcss = require('tailwindcss');
const autoprefixer = require('autoprefixer');
const concat = require('gulp-concat');
const cssnano = require('cssnano');

gulp.task('css', function () {
    return gulp.src([
        './src/Components/**/*.css',
        './src/Modules/**/*.css',
        './src/Themes/assets/css/app.css'
    ])
        .pipe(concat('app.min.css'))
        .pipe(postcss([
            tailwindcss('./tailwind.config.js'),
            autoprefixer,
            cssnano
        ]))
        .pipe(gulp.dest('../../public/vendor/crudbooster/themes/assets/css'))
        .pipe(gulp.dest('src/Themes/assets/css'));
});

gulp.task('watch', function () {
    gulp.watch([
        './src/**/*.css',
        './src/**/*.php'
    ], gulp.series('css'));
});

gulp.task('default', gulp.series('css'));
