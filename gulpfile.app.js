const gulpBase = require('./gulp.base');

gulpBase({
  server: {
    proxy: 'localhost'
  },
  scripts: {
    files: ['./default/public/**/*.js', './default/**/*.phtml']
  },
  sass: {
    files: [
      './default/public/scss/**/*.scss'
    ],
    includePaths: [
      './',
      './node_modules/foundation-sites/scss',
      './node_modules/mdi/scss'
    ],
    output: './default/public/css'
  }
});
