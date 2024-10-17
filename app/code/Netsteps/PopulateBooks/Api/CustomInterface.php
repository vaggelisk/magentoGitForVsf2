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
     * Upload Image File
     *
     * @return string
     * @api
     */
    public function upload(): string;


    /**
    * GET for Post api
    * @param string $customerId
    * @param string $Title
    * @param string $Subtitle
    * @param string $Summary
    * @return string
    *@api
    */
    public function createBook(
        string $customerId,
        string $Title,
        string $Subtitle,
        string $Summary,
    ): string;

}
