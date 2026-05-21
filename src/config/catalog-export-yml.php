<?php

return [
    "encoding" => "UTF-8",

    "shopName" => env("CATALOG_EXPORT_YML_SHOP_NAME", env("APP_NAME")),
    "companyName" => env("CATALOG_EXPORT_YML_COMPANY_NAME", env("APP_NAME")),

    "currencyId" => env("CATALOG_EXPORT_YML_CURRENCY_ID", "RUB"),
    "currencyRate" => env("CATALOG_EXPORT_YML_CURRENCY_RATE", 1),

    "fileName" => env("CATALOG_EXPORT_YML_FILE_NAME", "yandex-products.yml"),
    "fileFolder" => env("CATALOG_EXPORT_YML_FILE_FOLDER", "yml"),

    // Manager
    "customYmlActionsManager" => null,
];
