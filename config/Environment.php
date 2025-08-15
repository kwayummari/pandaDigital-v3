<?php

class Environment
{
    private static $variables = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        $envFile = $path ?: dirname(__DIR__) . '/.env';

        if (!file_exists($envFile)) {
            // Try to load from env.example if .env doesn't exist
            $exampleFile = dirname(__DIR__) . '/env.example';
            if (file_exists($exampleFile)) {
                self::loadFromFile($exampleFile);
            }
            return;
        }

        self::loadFromFile($envFile);
        self::$loaded = true;
    }

    /**
     * Load variables from a specific file
     */
    private static function loadFromFile($file)
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                self::$variables[$key] = $value;
            }
        }
    }

    /**
     * Get an environment variable
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables[$key] ?? $default;
    }

    /**
     * Set an environment variable
     */
    public static function set($key, $value)
    {
        self::$variables[$key] = $value;
    }

    /**
     * Check if an environment variable exists
     */
    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$variables[$key]);
    }

    /**
     * Get all environment variables
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables;
    }

    /**
     * Get database configuration
     */
    public static function getDatabaseConfig()
    {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'name' => self::get('DB_NAME', 'u750269652_pandadigital'),
            'user' => self::get('DB_USER', 'u750269652_pandadigital'),
            'password' => self::get('DB_PASSWORD', 'PandaDigital.2020'),
            'charset' => self::get('DB_CHARSET', 'utf8mb4')
        ];
    }

    /**
     * Get application configuration
     */
    public static function getAppConfig()
    {
        return [
            'name' => self::get('APP_NAME', 'Panda Digital'),
            'url' => self::get('APP_URL', 'http://localhost/pandadigitalV3'),
            'env' => self::get('APP_ENV', 'development'),
            'debug' => self::get('APP_DEBUG', 'true') === 'true',
            'timezone' => self::get('APP_TIMEZONE', 'Africa/Dar_es_Salaam'),
            'key' => self::get('APP_KEY', '')
        ];
    }

    /**
     * Get mail configuration
     */
    public static function getMailConfig()
    {
        return [
            'host' => self::get('MAIL_HOST', 'smtp.gmail.com'),
            'port' => self::get('MAIL_PORT', '587'),
            'username' => self::get('MAIL_USERNAME', ''),
            'password' => self::get('MAIL_PASSWORD', ''),
            'encryption' => self::get('MAIL_ENCRYPTION', 'tls'),
            'from_address' => self::get('MAIL_FROM_ADDRESS', 'noreply@pandadigital.co.tz'),
            'from_name' => self::get('MAIL_FROM_NAME', 'Panda Digital')
        ];
    }

    /**
     * Get payment configuration
     */
    public static function getPaymentConfig()
    {
        return [
            'azampay_api_key' => self::get('AZAMPAY_CLIENT_ID', ''),
            'azampay_secret_key' => self::get('AZAMPAY_CLIENT_SECRET', ''),
            'azampay_environment' => self::get('AZAMPAY_ENVIRONMENT', 'sandbox')
        ];
    }

    /**
     * Get Google OAuth configuration
     */
    public static function getGoogleOAuthConfig()
    {
        return [
            'client_id' => self::get('GOOGLE_CLIENT_ID', ''),
            'client_secret' => self::get('GOOGLE_CLIENT_SECRET', ''),
            'redirect_uri' => self::get('GOOGLE_REDIRECT_URI', '')
        ];
    }

    /**
     * Get upload configuration
     */
    public static function getUploadConfig()
    {
        return [
            'max_size' => (int) self::get('UPLOAD_MAX_SIZE', 10485760),
            'allowed_types' => explode(',', self::get('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,gif,pdf,doc,docx')),
            'path' => self::get('UPLOAD_PATH', 'uploads/')
        ];
    }

    /**
     * Get social media configuration
     */
    public static function getSocialConfig()
    {
        return [
            'facebook' => self::get('FACEBOOK_URL', ''),
            'twitter' => self::get('TWITTER_URL', ''),
            'linkedin' => self::get('LINKEDIN_URL', ''),
            'instagram' => self::get('INSTAGRAM_URL', '')
        ];
    }

    /**
     * Get contact configuration
     */
    public static function getContactConfig()
    {
        return [
            'phone' => self::get('CONTACT_PHONE', '+25573428334'),
            'email' => self::get('CONTACT_EMAIL', 'info@pandadigital.co.tz'),
            'address' => self::get('CONTACT_ADDRESS', 'Dar es Salaam, Tanzania')
        ];
    }

    /**
     * Check if application is in development mode
     */
    public static function isDevelopment()
    {
        return self::get('APP_ENV', 'development') === 'development';
    }

    /**
     * Check if application is in production mode
     */
    public static function isProduction()
    {
        return self::get('APP_ENV', 'development') === 'production';
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebug()
    {
        return self::get('APP_DEBUG', 'true') === 'true';
    }
}
