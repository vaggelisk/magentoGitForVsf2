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


## CustomerId in loogedin customer

This is a little bit strange. The `customerId` is kind of secret parameter



For more details you can see here
https://magento.stackexchange.com/questions/236076/how-to-get-customerid-in-api-in-magento-2-and-how-magento-gets-customerid-for-t
