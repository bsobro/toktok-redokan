<?php

namespace ContentEgg\application\modules\Amazon;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataAmazon class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class ExtraDataAmazon extends ExtraData {

    public $locale;
    public $associate_tag;
    public $itemLinks = array();
    public $imageSet = array();
    public $AmountSaved;
    public $PercentageSaved;
    public $IsEligibleForSuperSaverShipping;
    public $customerReviews;
    public $editorialReviews = array();
    public $smallImage;
    public $mediumImage;
    public $largeImage;
    public $addToCartUrl;
    public $ASIN;
    public $itemAttributes;
    public $toLowToDisplay;
    public $availability;
    public $lowestNewPrice;
    public $lowestUsedPrice;
    public $lowestCollectiblePrice;
    public $lowestRefurbishedPrice;
    public $totalNew;
    public $totalUsed;
    public $totalCollectible;
    public $totalRefurbished;

}

class ExtraAmazonItemLinks {

    public $Description;
    public $URL;

}

class ExtraAmazonImageSet {

    public $attributes = array();
    public $SwatchImage;
    public $SmallImage;
    public $ThumbnailImage;
    public $TinyImage;
    public $MediumImage;
    public $LargeImage;

}

//параметры из AmazonProduct->parseCustomerReviews
class ExtraAmazonCustomerReview {

    public $Content;
    public $Summary;
    public $Date;
    public $Name;
    public $Rating;

}

class ExtraAmazonEditorialReviews {

    public $Source;
    public $Content;

}

class ExtraAmazonItemAttributes {

    public $Actor;
    public $Artist;
    public $AspectRatio;
    public $AudienceRating;
    public $AudioFormat;
    public $Binding;
    public $Brand;
    public $CEROAgeRating;
    public $ClothingSize;
    public $Color;
    public $Creator;
    public $Department;
    public $Director;
    public $EAN;
    public $EANList;
    public $Edition;
    public $EISBN;
    public $EpisodeSequence;
    public $ESRBAgeRating;
    public $Feature = array();
    public $Format;
    public $Genre;
    public $HardwarePlatform;
    public $HazardousMaterialType;
    public $IsAdultProduct;
    public $IsAutographed;
    public $ISBN;
    public $IsEligibleForTradeIn;
    public $IsMemorabilia;
    public $IssuesPerYear;
    public $ItemDimensions;
    public $ItemPartNumber;
    public $Label;
    public $Languages;
    public $LegalDisclaimer;
    public $ManufacturerMaximumAge;
    public $ManufacturerMinimumAge;
    public $ManufacturerPartsWarrantyDescription;
    public $MediaType;
    public $Model;
    public $MPN;
    public $NumberOfDiscs;
    public $NumberOfIssues;
    public $NumberOfItems;
    public $NumberOfPages;
    public $NumberOfTracks;
    public $OperatingSystem;
    public $PackageQuantity;
    public $PartNumber;
    public $Platform;
    public $ProductGroup;
    public $ProductTypeSubcategory;
    public $PublicationDate;
    public $Publisher;
    public $RegionCode;
    public $ReleaseDate;
    public $RunningTime;
    public $SeikodoProductCode;
    public $Size;
    public $SKU;
    public $Studio;
    public $SubscriptionLength;
    public $TradeInValue;
    public $UPC;
    public $UPCList;
    public $Warranty;
    public $WEEETaxValue;
    public $PackageDimensions;

}
