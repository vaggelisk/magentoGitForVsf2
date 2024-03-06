<?php

namespace Netsteps\Base\Observer;

use Magento\Framework\Event\ObserverInterface;

class IncludePathsFixObserver implements ObserverInterface {

    /**
     * author:aimilios
     * Edo egine i prospatheia na trexei kapoios Kodikas se kathe Run tou magento
    https://magento.stackexchange.com/questions/122365/magento-2-run-function-on-every-page
     * epeidi omos sta ajax den epeze sosta to sygkekrimeno event, exoume ena tropo gia na dokimazoume poia event pragmatika trexoune.
     * peirazoume gia "ligo" to vendor Event/Manager etsi:
    //DEBUG EVENTS WITH THIS:
    //vendor/magento/framework/Event/Manager.php -> public function dispatch($eventName, array $data = [])
     *  vazoume log to event name
     * error_log($eventName);
     * k epeita kanoume tail -f php.log
     * k antigrafoume to event pou mas endiaferei.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        //Debug Lines for log if its needed
        $event_name = $observer->getName();
        //REQUEST_URI
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['HTTP_URI'])) {
            $hit=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }

        //error_log('my_observer-'."Event: $event_name"." URI:".$hit);

        //Here we solve the error for :
        //] main.CRITICAL: Validate class not found from basename 'Magento\Framework\Validator\EmailAddress'
        //these were some path examples
        // $path_to_zend_lib='/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/vendor/magento/zendframework1/library';
        ////        $path_to_zend_lib='/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/generated/code:/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/generated/code:/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/vendor/magento/zendframework1/library:/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/vendor/phpunit/php-file-iterator:/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/vendor/phpunit/phpunit:/var/www/public/lenak.netsteps-apps.gr/public_html/kaissa.gr/vendor/symfony/yaml';
        ////        error_log( $final_incl_path);
        //We Just Remove /usr/share/php because magento\zendframework1\library\Zend\Validate.php  doesnt find Magento\Framework\Validator\EmailAddress class if this path is on
        $all_include_paths=get_include_path();
//        error_log($all_include_paths );

        $all_include_paths_arr = explode(":",$all_include_paths);
//        error_log(json_encode($all_include_paths_arr));
        $all_include_paths_final_arr=[];
        foreach ($all_include_paths_arr as $item) {
//            error_log(json_encode(stripos($item,"/usr/share/php")));
            if (!empty($item) && stripos($item,"/usr/share/php")===false) {
                $all_include_paths_final_arr[]= trim($item);
            }
        }
//        error_log(json_encode($all_include_paths_final_arr));
        //join again all paths
        $final_incl_path = implode(":",$all_include_paths_final_arr);
        set_include_path( $final_incl_path);
        //this is the same as the previous ;)
        // ini_set('include_path', $path_to_zend_lib);


    }
}
