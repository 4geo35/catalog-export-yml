<?php

namespace GIS\CatalogExportYml\Helpers;

use Illuminate\Support\Facades\Storage;

class YMLDocument extends \DOMDocument
{
    protected string $fileName;
    protected $file;
    protected string $filePath;
    protected string $fileFolder;

    protected \DOMElement|false $shop;
    protected \DOMNameSpaceNode|\DOMElement|null|\DOMNode $currencies;
    protected \DOMNameSpaceNode|\DOMElement|null|\DOMNode $categories;
    protected \DOMNameSpaceNode|\DOMElement|null|\DOMNode $offers;

    protected array $existCategories = [];

    /**
     * @throws \DOMException
     */
    public function __construct()
    {
        $encoding = config("catalog-export-yml.encoding", "UTF-8");
        parent::__construct("1.0", $encoding);

        $root = $this->createElement("yml_catalog");
        $this->shop = $this->createElement("shop");

        $root->setAttribute("date", now()->toIso8601String());
        $root->setAttribute("encoding", $encoding);
        $root->appendChild($this->shop);
        $this->appendChild($root);

        $this->addShopChild("name", config("catalog-export-yml.shopName"))
            ->addShopChild("company", config("catalog-export-yml.companyName"))
            ->addShopChild("url", config("app.url"))
            ->addShopChild("currencies")
            ->addShopChild("categories")
            ->addShopChild("offers");

        $this->currencies = $this->getElementsByTagName("currencies")->item(0);
        $this->initCurrency();

        $this->categories = $this->getElementsByTagName("categories")->item(0);
        $this->offers = $this->getElementsByTagName("offers")->item(0);

        $this->fileName = config("catalog-export-yml.fileName");
        $this->existCategories = [];
        $this->fileFolder = config("catalog-export-yml.fileFolder");

        if (! Storage::disk("public")->exists($this->fileFolder)) {
            Storage::disk("public")->makeDirectory($this->fileFolder);
        }
    }

    public function setFileName(string $fileName): YMLDocument
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @throws \DOMException
     */
    protected function addShopChild(string $name, string $value = null): YMLDocument
    {
        if ($value) {
            $this->shop->appendChild($this->createElement($name, $value));
        } else {
            $this->shop->appendChild($this->createElement($name));
        }
        return $this;
    }

    /**
     * @throws \DOMException
     */
    protected function initCurrency(): void
    {
        $element = $this->createElement("currency");
        $element->setAttribute("id", config("catalog-export-yml.currencyId"));
        $element->setAttribute("rate", config("catalog-export-yml.currencyRate"));
        $this->currencies->appendChild($element);
    }
}
