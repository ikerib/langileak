const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('main', './assets/main.js')
    //.enableStimulusBridge('./assets/controllers.json')
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()
    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enableVueLoader(() => {}, {
        version: 3,
        runtimeCompilerBuild: true,
    })
    // .enableTypeScriptLoader(options => {
    //     options.appendTsSuffixTo = [/\.vue$/];
    // })
    //.enableReactPreset()
    //.enableIntegrityHashes(Encore.isProduction())
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
