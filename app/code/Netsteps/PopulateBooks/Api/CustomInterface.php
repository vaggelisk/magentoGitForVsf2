<?php

namespace Netsteps\PopulateBooks\Api;

interface CustomInterface
{
    /**
    * POST for Post api
    * @api
    * @param string $value
    * @return string
    */
    public function searchBookInOurLibrary($value);


    /**
    * GET for Post api
    * @api
    * @param string $value
    * @return string
    */
    public function searchBookInBiblionet($value);

    /**
     * Upload Pdf
     *
     * @return string
     * @api
     */
    public function upload(): string;


    /**
    * GET for Post api
    * @api
    * @param string $customerId
    * @param string $title
    * @return string
    */
    public function createBook($customerId, $title);

}
