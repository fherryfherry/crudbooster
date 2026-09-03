const gulp = require('gulp');
const postcss = require('gulp-postcss');
const concat = require('gulp-concat');
const cssnano = require('cssnano');
const tailwindcss = require('@tailwindcss/postcss');
const { execFileSync } = require('child_process');
const path = require('path');

// Tailwind v4: config lives in app.css itself (@import "tailwindcss",
// @source globs, @custom-variant dark). The entry is compiled with the
// @tailwindcss/cli binary (the v3 `tailwindcss()` PostCSS plugin no longer
// exists). The component/module CSS fragments keep using @apply, so they are
// compiled separately with @tailwindcss/postcss — @reference points them at
// the same Tailwind context.

gulp.task('tailwind', function (done) {
    execFileSync(
        path.join(__dirname, 'node_modules/.bin/tailwindcss'),
        ['-i', './src/Themes/assets/css/app.css', '-o', './src/Themes/assets/css/app.tailwind.css', '--minify'],
        { stdio: 'inherit', cwd: __dirname },
    );
    done();
});

// Compile the @apply fragments against the main entry via @reference.
// Only these files actually use @apply; the rest are plain CSS copied as-is.
const FRAGMENTS = [
    './src/Components/AlertMessage/css/alert.css',
    './src/Modules/PageBuilder/Elements/Heading/assets/heading.css',
    './src/Modules/PageBuilder/Elements/Image/assets/image.css',
];

gulp.task('fragments', function () {
    return gulp.src(FRAGMENTS, { base: './src' })
        .pipe(postcss([
            {
                postcssPlugin: 'prepend-reference',
                Once(root) {
                    const rel = path.relative(path.dirname(root.source.input.file), __dirname).split(path.sep).join('/');
                    const ref = `@reference "${rel}/src/Themes/assets/css/app.css";`;
                    if (!root.toString().includes('@reference')) {
                        root.prepend(ref);
                    }
                },
            },
            tailwindcss({ base: __dirname }),
        ]))
        .pipe(gulp.dest('./build/fragments'));
});

// Concat compiled Tailwind entry + compiled fragments + plain CSS, minify, ship.
gulp.task('css', gulp.series('tailwind', 'fragments', function () {
    return gulp.src([
        './src/Themes/assets/css/app.tailwind.css',
        './build/fragments/**/*.css',
        './src/Components/**/*.css',
        './src/Modules/**/*.css',
    ].concat(FRAGMENTS.map((f) => '!' + f.replace('./', ''))))
        .pipe(concat('app.min.css'))
        .pipe(postcss([cssnano]))
        .pipe(gulp.dest('src/Themes/assets/css'))
        .pipe(gulp.dest('../../public/vendor/crudbooster/themes/assets/css'));
}));

gulp.task('clean', function (done) {
    execFileSync('rm', ['-rf', './build', './src/Themes/assets/css/app.tailwind.css']);
    done();
});

gulp.task('watch', function () {
    gulp.watch([
        './src/**/*.css',
        './src/**/*.php',
    ], gulp.series('css'));
});

gulp.task('default', gulp.series('css'));