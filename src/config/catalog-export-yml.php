<?php

return [
    // File settings
    "fileName" => env("CATALOG_EXPORT_YML_FILE_NAME", "yandex-products.yml"),
    "fileFolder" => env("CATALOG_EXPORT_YML_FILE_FOLDER", "yml"),

    // Route settings
    "ymlCacheKey" => "catalog-yml-export",
    "ymlCacheLifetime" => env("CATALOG_EXPORT_YML_CACHE_LIFETIME", 86400),
    "ymlPrefix" => env("CATALOG_EXPORT_YML_PREFIX", "catalog-export"),

    // Xml settings
    "encoding" => "UTF-8",

    "shopName" => env("CATALOG_EXPORT_YML_SHOP_NAME", env("APP_NAME")),
    "companyName" => env("CATALOG_EXPORT_YML_COMPANY_NAME", env("APP_NAME")),

    "currencyEnabled" => env("CATALOG_EXPORT_YML_CURRENCY_ENABLED", true),
    "currencyId" => env("CATALOG_EXPORT_YML_CURRENCY_ID", "RUR"),
    "currencyRate" => env("CATALOG_EXPORT_YML_CURRENCY_RATE", 1),

    "productImageTemplate" => env("CATALOG_EXPORT_YML_PRODUCT_IMAGE_TEMPLATE", "original"),
    "productDescriptionField" => env("CATALOG_EXPORT_YML_PRODUCT_DESCRIPTION_FIELD", "description"),
    "productDescriptionStripTags" => env("CATALOG_EXPORT_YML_PRODUCT_DESCRIPTION_STRIP_TAGS", false),
    "productOriginTitles" => env("CATALOG_EXPORT_YML_PRODUCT_ORIGIN_TITLES", "Производство,Производитель"),

    // Manager
    "customYmlActionsManager" => null,

    // Controllers
    "customExportCatalogController" => null,
];
