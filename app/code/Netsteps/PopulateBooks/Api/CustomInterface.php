<?php

namespace Netsteps\PopulateBooks\Api;

interface CustomInterface
{
    /**
    * POST for Post api
    * @api
    * @param string $title
    * @param string $publisher
    * @return string
    */
    public function searchBookInOurLibrary(string $title, string $publisher): string;


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
     * @return string
     * @api
     */
    public function deleteBook(): string;


    /**
     * GET for Post api
     * @param string $customerId
     * @param string $Title
     * @param string $Subtitle
     * @param string $CoverImage
     * @param string $ISBN
     * @param string $PublisherID
     * @param string $Publisher
     * @param string $WriterID
     * @param string $Writer
     * @param string $WriterName
     * @param string $FirstPublishDate
     * @param string $CurrentPublishDate
     * @param string $PlaceID
     * @param string $Place
     * @param string $EditionNo
     * @param string $Cover
     * @param string $Dimensions
     * @param string $PageNo
     * @param string $Availability
     * @param string $Price
     * @param string $VAT
     * @param string $Weight
     * @param string $AgeFrom
     * @param string $AgeTo
     * @param string $Summary
     * @param string $LanguageID
     * @param string $Language
     * @param string $LanguageOriginalID
     * @param string $LanguageOriginal
     * @param string $LanguageTranslatedFromID
     * @param string $LanguageTranslatedFrom
     * @param string $Series
     * @param string $MultiVolumeTitle
     * @param string $VolumeNo
     * @param string $VolumeCount
     * @param string $Specifications
     * @param string $CategoryID
     * @param string $Category
     * @param string $SubjectsID
     * @param string $SubjectTitle
     * @param string $SubjectDDC
     * @param string $SubjectOrder
     * @param int $Contributor
     * @return string
     * @api
     */
    public function createBook(
        string $customerId,
        string $Title,
        string $Subtitle,
        string $CoverImage,
        string $ISBN,
        string $PublisherID,
        string $Publisher,
        string $WriterID,
        string $Writer,
        string $WriterName,
        string $FirstPublishDate,
        string $CurrentPublishDate,
        string $PlaceID,
        string $Place,
        string $EditionNo,
        string $Cover,
        string $Dimensions,
        string $PageNo,
        string $Availability,
        string $Price,
        string $VAT,
        string $Weight,
        string $AgeFrom,
        string $AgeTo,
        string $Summary,
        string $LanguageID,
        string $Language,
        string $LanguageOriginalID,
        string $LanguageOriginal,
        string $LanguageTranslatedFromID,
        string $LanguageTranslatedFrom,
        string $Series,
        string $MultiVolumeTitle,
        string $VolumeNo,
        string $VolumeCount,
        string $Specifications,
        string $CategoryID,
        string $Category,
        string $SubjectsID,
        string $SubjectTitle,
        string $SubjectDDC,
        string $SubjectOrder,
        int $Contributor,
    ): string;
}
