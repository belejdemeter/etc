const path = require('path');
const webpack = require('webpack')
//
const banner = new webpack.BannerPlugin({
    banner: '/*\n' +
        'Theme Name: WP Bootstrap Starter Child\n' +
        'Template: wp-bootstrap-starter\n' +
        '*/'
})


module.exports = {
    entry: [
        __dirname + '/scss/style.scss'
    ],
    output: {
        path: path.join(__dirname, '../'),
        // filename: 'js/app.min.js',
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [],
            },
            {
                test: /\.s[ac]ss$/i,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'file-loader',
                        options: { outputPath: '/', name: '[name].css'}
                    },
                    'sass-loader'
                ]
            }
        ]
    },
    plugins: [
        banner,
    ]
};