<?php

namespace GIS\CatalogExportYml\Helpers;

use GIS\CategoryProduct\Facades\ProductActions;
use GIS\CategoryProduct\Interfaces\CategoryInterface;
use GIS\CategoryProduct\Interfaces\ProductInterface;
use GIS\CategoryProduct\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class YmlActionsManager
{
    protected SimpleXMLElement|false $catalog;
    protected SimpleXMLElement|false $shop;
    protected $categories;
    protected $offers;

    public function generateNewFile(string $fileName = null): void
    {
        if (empty($fileName)) { $fileName = config("catalog-export-yml.fileName"); }
        $fileFolder = config("catalog-export-yml.fileFolder");

        if (! Storage::disk("public")->exists($fileFolder)) {
            Storage::disk("public")->makeDirectory($fileFolder);
        }

        $this->fillDocument();
        $xmlStr = $this->catalog->asXML();
        if (empty($xmlStr)) {
            Log::error("Can't create XML string in YmlActionsManager::generateNewFile");
            return;
        }

        Storage::disk("public")->put($fileFolder . "/$fileName", $xmlStr);
    }

    public function getXMLContent(): string|null
    {
        $key = config("catalog-export-yml.ymlCacheKey");
        $lifetime = config("catalog-export-yml.ymlCacheLifetime");

        return Cache::remember($key, $lifetime, function () {
            $this->fillDocument();
            return $this->catalog->asXML();
        });
    }

    protected function fillDocument(): void
    {
        $this->initCatalog();

        $categories = $this->getRootCategories();

        foreach ($categories as $category) {
            $this->fillCategoryProducts($category);
        }
    }

    protected function initCatalog(): void
    {
        $this->catalog = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?><yml_catalog></yml_catalog>");
        $this->catalog->addAttribute("date", now()->toIso8601String());

        $this->shop = $this->catalog->addChild("shop");
        $this->shop->addChild("name", config("catalog-export-yml.shopName"));
        $this->shop->addChild("company", config("catalog-export-yml.companyName"));
        $this->shop->addChild("url", config("app.url"));
        $this->initCurrencies();
        $this->categories = $this->shop->addChild("categories");
        $this->offers = $this->shop->addChild("offers");
    }

    protected function initCurrencies(): void
    {
        if (! config("catalog-export-yml.currencyEnabled")) { return;}
        $currencies = $this->shop->addChild("currencies");
        $currency = $currencies->addChild("currency");
        $currency->addAttribute("id", config("catalog-export-yml.currencyId"));
        $currency->addAttribute("rate", config("catalog-export-yml.currencyRate"));
    }

    protected function fillCategoryProducts(CategoryInterface $category): void
    {
        $this->addCategory($category);

        $children = $this->getCategoryChildren($category);
        if ($children->count()) {
            foreach ($children as $child) {
                $this->fillCategoryProducts($child);
            }
        }

        $products = $this->getCategoryProducts($category);
        if (! $products->count()) { return; }
        foreach ($products as $product) {
            $this->addOffer($product);
        }
    }

    protected function addOffer(ProductInterface $product): void
    {
        $image = $this->getProductImageUrl($product);
        $description = $this->getProductDescription($product);
        $shortDescription = $this->getProductShortDescription($product);
        list($origin, $specifications) = $this->getProductSpecifications($product);

        foreach ($product->orderedVariations as $variation) {
            if ($variation->price <= 0) { continue; }
            if (empty($shortDescription)) { $shortDescription = $variation->title; }
            else { $shortDescription .= " ($variation->title)"; }

            $element = $this->offers->addChild("offer");
            $element->addAttribute("id", $variation->id);
            $element->addChild("categoryId", $product->category_id);
            $element->addChild("name", htmlspecialchars($product->title));
            $element->addChild("url", route("web.product", ["product" => $product]));

            $element->addChild("price", $variation->price);
            if (! empty($variation->sale) && ! empty($variation->old_price)) {
                $element->addChild("oldprice", $variation->old_price);
            }
            $element->addChild("currencyId", config("catalog-export-yml.currencyId"));

            $element->addChild("description", $description);
            $element->addChild("shortDescription", $shortDescription);

            $availableValue = $variation->published_at ? "true" : "false";
            $element->addChild("available", $availableValue);
            $element->addChild("store", $availableValue);

            if ($image) { $element->addChild("picture", $image); }

            if ($origin) { $element->addChild("country_of_origin", $origin); }
            foreach ($specifications as $specification) {
                $element
                    ->addChild("param", $specification->value)
                    ->addAttribute("name", $specification->title);
            }
        }
    }

    protected function getProductSpecifications(ProductInterface $product): array
    {
        $origin = null;
        $specifications = [];
        $rawData = ProductActions::getSpecificationFullList($product);
        $originTitlesStr = config("catalog-export-yml.productOriginTitles");
        $originTitles = empty($originTitlesStr) ? [] : explode(",", $originTitlesStr);
        foreach ($rawData as $item) {
            if (in_array($item->title, $originTitles)) {
                $origin = $item->stringValues;
                continue;
            }
            if (empty($item->values[0])) { continue; }
            $specifications[] = (object)[
                "title" => $item->title,
                "value" => $item->values[0],
            ];
        }
        return [$origin, $specifications];
    }

    protected function getProductShortDescription(ProductInterface $product): ?string
    {
        return config("catalog-export-yml.productDescriptionField") !== "short" ? $product->short : null;
    }

    protected function getProductDescription(ProductInterface $product): ?string
    {
        if (config("catalog-export-yml.productDescriptionField") !== "description") {
            return $product->short;
        }
        if (config("catalog-export-yml.productDescriptionStripTags")) {
            return htmlspecialchars(strip_tags($product->markdown), ENT_XML1);
        } else {
            if (empty($product->description)) { return null; }
            return "<![CDATA[ " . htmlspecialchars($product->markdown, ENT_XML1) . " ]]>";
        }
    }

    protected function getProductImageUrl(ProductInterface $product): ?string
    {
        if (! $product->cover) { return null; }
        $template = config("catalog-export-yml.productImageTemplate");
        return route("thumb-img", ["template" => $template, "filename" => $product->cover->file_name]);
    }

    protected function addCategory(CategoryInterface $category): void
    {
        $element = $this->categories->addChild("category", $category->title);
        $element->addAttribute("id", $category->id);
        if ($category->parent_id) {
            $element->addAttribute("parentId", $category->parent_id);
        }
    }

    protected function getCategoryProducts(CategoryInterface $category): Collection
    {
        return $category->products()
            ->with("orderedVariations", "cover")
            ->whereNotNull("published_at")
            ->get();
    }

    protected function getCategoryChildren(CategoryInterface $category): Collection
    {
        return $category->children()
            ->select("id", "parent_id", "slug", "title")
            ->whereNotNull("published_at")
            ->get();
    }

    protected function getRootCategories(): Collection
    {
        $categoryModel = config("category-product.customCategoryModel") ?? Category::class;
        return $categoryModel::query()
            ->select("id", "parent_id", "slug", "title")
            ->whereNotNull("published_at")
            ->whereNull("parent_id")
            ->get();
    }
}
