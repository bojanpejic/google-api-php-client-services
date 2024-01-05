<?php

if (
    class_exists('Google\\Client')
    && (
        class_exists('Composer\\InstalledVersions')
        && version_compare(\Composer\InstalledVersions::getVersion('google/apiclient'), '2.7.2', '<=')
    )
) {
    load_legacy_namespace();
} elseif (
    !class_exists('Google_Client', false)
    && file_exists(__DIR__ . '/../apiclient/src/Google/Client.php')
) {
    require_once(__DIR__ . '/../apiclient/src/Google/Client.php');
    if (
        defined('Google_Client::LIBVER')
        && version_compare(Google_Client::LIBVER, '2.7.2', '<=')
    ) {
        load_legacy_namespace();
    }
}

function load_legacy_namespace()
{
    $servicesClassMap = [
        'Google\\Client' => 'Google_Client',
        'Google\\Service' => 'Google_Service',
        'Google\\Service\\Resource' => 'Google_Service_Resource',
        'Google\\Model' => 'Google_Model',
        'Google\\Collection' => 'Google_Collection',
    ];
    foreach ($servicesClassMap as $alias => $class) {
        class_alias($class, $alias);
    }
}
spl_autoload_register(function ($class) {
    if (0 === strpos($class, 'Google_Service_')) {
        // Autoload the new class, which will also create an alias for the
        // old class by changing underscores to namespaces:
        //     Google_Service_Speech_Resource_Operations
        //      => Google\Service\Speech\Resource\Operations
        $classExists = class_exists($newClass = str_replace('_', '\\', $class));
        if ($classExists) {
            return true;
        }
    }
}, true, true);