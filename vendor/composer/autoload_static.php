<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit13ae70ef6261ce64d7a5fa3d3f54a950
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\' => 42,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Activation' => __DIR__ . '/../..' . '/src/Activation.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Admin\\OneIdMetaFields' => __DIR__ . '/../..' . '/src/Admin/OneIdMetaFields.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Admin\\OneIdSettings' => __DIR__ . '/../..' . '/src/Admin/OneIdSettings.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictedWooCommerceProduct' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictedWooCommerceProduct.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestriction' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestriction.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionCallback' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionCallback.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionCart' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionCart.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionCheckout' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionCheckout.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionEmail' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionEmail.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionFeature' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionFeature.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionOrder' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionOrder.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionOrderStatus' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionOrderStatus.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionProduct' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionProduct.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionProductAdmin' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionProductAdmin.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionShortcode' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionShortcode.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionTaxonomy' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionTaxonomy.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionTaxonomyWalker' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionTaxonomyWalker.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionUninstaller' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionUninstaller.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeRestrictionUtils' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeRestrictionUtils.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeVerification\\AgeVerification' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeVerification/AgeVerification.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\AgeVerification\\AgeVerificationCollection' => __DIR__ . '/../..' . '/src/AgeRestriction/AgeVerification/AgeVerificationCollection.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\Checkout\\AddressPrePopulation' => __DIR__ . '/../..' . '/src/AgeRestriction/Checkout/AddressPrePopulation.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\AgeRestriction\\InvalidAgeRestrictionException' => __DIR__ . '/../..' . '/src/AgeRestriction/InvalidAgeRestrictionException.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Client\\FakeOneIdClient' => __DIR__ . '/../..' . '/src/Client/FakeOneIdClient.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Client\\OneIdClientInterface' => __DIR__ . '/../..' . '/src/Client/OneIdClientInterface.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Client\\OneIdOpenIdConnectClient' => __DIR__ . '/../..' . '/src/Client/OneIdOpenIdConnectClient.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Client\\OpenIdConnectClientFactory' => __DIR__ . '/../..' . '/src/Client/OpenIdConnectClientFactory.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\FeatureInterface' => __DIR__ . '/../..' . '/src/FeatureInterface.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\FeatureManager' => __DIR__ . '/../..' . '/src/FeatureManager.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\NullCallback' => __DIR__ . '/../..' . '/src/NullCallback.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdAdmin' => __DIR__ . '/../..' . '/src/OneIdAdmin.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdApp' => __DIR__ . '/../..' . '/src/OneIdApp.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdButtonRenderer' => __DIR__ . '/../..' . '/src/OneIdButtonRenderer.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdCallback' => __DIR__ . '/../..' . '/src/OneIdCallback.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdCallbackFactory' => __DIR__ . '/../..' . '/src/OneIdCallbackFactory.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdCallbackHandler' => __DIR__ . '/../..' . '/src/OneIdCallbackHandler.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdSettingsManager' => __DIR__ . '/../..' . '/src/OneIdSettingsManager.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\OneIdUninstaller' => __DIR__ . '/../..' . '/src/OneIdUninstaller.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Session\\OneIdFlashMessages' => __DIR__ . '/../..' . '/src/Session/OneIdFlashMessages.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Session\\OneIdFlashMessagesInterface' => __DIR__ . '/../..' . '/src/Session/OneIdFlashMessagesInterface.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Session\\OneIdSessionStorageInterface' => __DIR__ . '/../..' . '/src/Session/OneIdSessionStorageInterface.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Session\\SessionManager' => __DIR__ . '/../..' . '/src/Session/SessionManager.php',
        'DigitalIdentityNet\\OneId\\WordPress\\Plugin\\Session\\WooCommerceSessionStorage' => __DIR__ . '/../..' . '/src/Session/WooCommerceSessionStorage.php',
        'OneId_Vendor\\Jumbojett\\OpenIDConnectClient' => __DIR__ . '/../..' . '/vendor_prefixed/jumbojett/openid-connect-php/src/OpenIDConnectClient.php',
        'OneId_Vendor\\Jumbojett\\OpenIDConnectClientException' => __DIR__ . '/../..' . '/vendor_prefixed/jumbojett/openid-connect-php/src/OpenIDConnectClient.php',
        'OneId_Vendor\\phpseclib\\Crypt\\AES' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/AES.php',
        'OneId_Vendor\\phpseclib\\Crypt\\Base' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/Base.php',
        'OneId_Vendor\\phpseclib\\Crypt\\Blowfish' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/Blowfish.php',
        'OneId_Vendor\\phpseclib\\Crypt\\DES' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/DES.php',
        'OneId_Vendor\\phpseclib\\Crypt\\Hash' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/Hash.php',
        'OneId_Vendor\\phpseclib\\Crypt\\RC2' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/RC2.php',
        'OneId_Vendor\\phpseclib\\Crypt\\RC4' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/RC4.php',
        'OneId_Vendor\\phpseclib\\Crypt\\RSA' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/RSA.php',
        'OneId_Vendor\\phpseclib\\Crypt\\Random' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/Random.php',
        'OneId_Vendor\\phpseclib\\Crypt\\Rijndael' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/Rijndael.php',
        'OneId_Vendor\\phpseclib\\Crypt\\TripleDES' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/TripleDES.php',
        'OneId_Vendor\\phpseclib\\Crypt\\Twofish' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Crypt/Twofish.php',
        'OneId_Vendor\\phpseclib\\File\\ANSI' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/File/ANSI.php',
        'OneId_Vendor\\phpseclib\\File\\ASN1' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/File/ASN1.php',
        'OneId_Vendor\\phpseclib\\File\\ASN1\\Element' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/File/ASN1/Element.php',
        'OneId_Vendor\\phpseclib\\File\\X509' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/File/X509.php',
        'OneId_Vendor\\phpseclib\\Math\\BigInteger' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Math/BigInteger.php',
        'OneId_Vendor\\phpseclib\\Net\\SCP' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Net/SCP.php',
        'OneId_Vendor\\phpseclib\\Net\\SFTP' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Net/SFTP.php',
        'OneId_Vendor\\phpseclib\\Net\\SFTP\\Stream' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Net/SFTP/Stream.php',
        'OneId_Vendor\\phpseclib\\Net\\SSH1' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Net/SSH1.php',
        'OneId_Vendor\\phpseclib\\Net\\SSH2' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/Net/SSH2.php',
        'OneId_Vendor\\phpseclib\\System\\SSH\\Agent' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/System/SSH/Agent.php',
        'OneId_Vendor\\phpseclib\\System\\SSH\\Agent\\Identity' => __DIR__ . '/../..' . '/vendor_prefixed/phpseclib/phpseclib/phpseclib/System/SSH/Agent/Identity.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit13ae70ef6261ce64d7a5fa3d3f54a950::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit13ae70ef6261ce64d7a5fa3d3f54a950::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit13ae70ef6261ce64d7a5fa3d3f54a950::$classMap;

        }, null, ClassLoader::class);
    }
}
