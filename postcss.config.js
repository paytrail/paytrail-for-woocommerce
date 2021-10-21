module.exports = {
    plugins: {
        autoprefixer: {
            browsers: [ '> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1' ]
        },
        cssnano: {
            preset: 'default'
        }
    }
};