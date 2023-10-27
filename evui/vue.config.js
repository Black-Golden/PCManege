const CompressionWebpackPlugin = require('compression-webpack-plugin')

module.exports = {
  publicPath: './', // 基本路径
  outputDir: '../public/adminadmincc62673401b9d7c85f0491f6f0125863',
  assetsDir: 'assets',
  productionSourceMap: false,
  lintOnSave: false,
  transpileDependencies: ['element-ui'], // 需要兼容IE10要放开这个
  chainWebpack: (config) => {
    config.plugin('html').tap((options) => {
      options[0].title = '后台管理系统'
      return options
    })
    if (process.env.NODE_ENV === 'production') {
      config.plugin('compressionPlugin').use(
        new CompressionWebpackPlugin({
          test: /\.(js|css|less)$/,
          threshold: 10240, // 对超过10kb的文件压缩
          deleteOriginalAssets: false,
        })
      )
    }
  },
}
