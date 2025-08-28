## Create Custom Rest API in Magento 2

To make an REST API Request,we follow this tutorial https://meetanshi.com/blog/create-custom-rest-api-in-magento-2/ BUT 

| ⚠ IMPORTANT:  There is a fault in the tutorial

More specifically in the very important file `etc/webapi.xml`
had forgotten the parameter: 

Had written
>   method="POST" url="/V1/custom/custom-api/">

instead of 

>  method="POST" url="/V1/custom/custom-api/:value">


For fixing this, we get advised by this https://inchoo.net/magento-2/magento-2-custom-api/



## Login As a Customer By Postman

You follow this very simple tutorial
https://meetanshi.com/blog/magento-2-api-get-customer-token/
and after that, you can access a 
> resource ref="self" 

webapi, if you go to Postman->Authorization Tab->Bearer Token
and put the Response of the Request `V1/integration/customer/token`
plain string, without `' '` or anything similar


## CustomerId in logedin customer

This is a bit strange. The `customerId` is kind of secret parameter



For more details you can see here
https://magento.stackexchange.com/questions/236076/how-to-get-customerid-in-api-in-magento-2-and-how-magento-gets-customerid-for-t


## How to make a custom attribute for category

You need only 2 files 
1. `app/code/Vendor/Module/view/adminhtml/ui_component/category_form.xml`
2. `app/code/Vendor/Module/Setup/Patch/Data/AddCategoryDecimalAttribute.php`

You can see on web the content of this files

Is important to know that after `magento 2.3`, the InstallData file/class is not working properly and you need the Patch 

Patches is important for building the category in the database, especially on this table

> SELECT attribute_code, frontend_label, entity_type_id
FROM eav_attribute;

For Desplaying the list of patches

> SELECT * 
FROM patch_list
WHERE patch_name LIKE '%AddCategoryCustomAttribute%';

If you see your patch class name → Magento thinks it already ran.

If you don’t see it → Magento never picked it up (maybe wrong file path / namespace).

If you see the patch, but you don't see the attribute on eav_attribute table,
you have to delete and run again se setup:upgrade


> DELETE FROM patch_list WHERE patch_id = <patch_id>


