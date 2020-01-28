<?php 
/**
 * @package Sun Media COTA
 * @author Arifin N <arifinofficial@outlook.com>
 */

namespace Inc;

final class Init
{
    /**
     * Store all classes inside an array
     *
     * @return array
     */
    public static function get_services()
    {
        return [
            Pages\Admin::class,
            Base\Enqueue::class,
            Base\CustomPostType::class,
            Base\CustomMetaBox::class,
            Base\Shortcode::class
        ];
    }

    /**
     * Loop the classes and call register method() if exists
     *
     * @return 
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Init the class
     *
     * @param class $class from the services
     *
     * @return void
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}