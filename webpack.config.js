const Encore = require('@symfony/webpack-encore');
const packageJson = require('./package.json');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/app.js')
  .disableSingleRuntimeChunk()
  .splitEntryChunks()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .cleanupOutputBeforeBuild()
  .enableSassLoader((options) => ({
    ...options,
    sassOptions: {
      ...(options.sassOptions ?? {}),
      quietDeps: true,
    },
  }))
  .enableIntegrityHashes()
  .configureDevServerOptions((options) => ({
    ...options,
    allowedHosts: 'all',
    host: '0.0.0.0',
    port: 3000,

    client: {
      overlay: {
        errors: true,
        runtimeErrors: false,
        warnings: true,
      },
      webSocketURL: {
        hostname: 'localhost',
        pathname: '/_webpack',
        port: 80,
        protocol: 'ws',
      },
    },
    watchFiles: [
      'assets/**/*',
    ],
  }))
  .configureDefinePlugin((constants) => ({
    ...constants,
    __INTLIFY_PROD_DEVTOOLS__: false,
    __VUE_I18N_FULL_INSTALL__: true,
    __VUE_I18N_LEGACY_API__: true,
    __VUE_OPTIONS_API__: true,
    __VUE_PROD_DEVTOOLS__: false,
    __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
    VUE_APP_VERSION: packageJson.version,
  }));

if (Encore.isDevServer()) {
  Encore
    .setPublicPath('/_webpack/build')
    .setManifestKeyPrefix('build/');
}

if (Encore.isDev()) {
  Encore.enableBuildCache({
    config: [__filename],
  });
}

const config = Encore.getWebpackConfig();

config.name = '_default';

config.module.rules.push({
  test: /\.ya?ml$/,
  use: 'yaml-loader',
});

if (Encore.isDevServer()) {
  config.output.publicPath = 'http://localhost/_webpack/build';
}

module.exports = [config];
