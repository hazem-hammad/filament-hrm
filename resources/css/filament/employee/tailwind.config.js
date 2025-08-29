import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Employee/**/*.php',
        './resources/views/filament/employee/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
