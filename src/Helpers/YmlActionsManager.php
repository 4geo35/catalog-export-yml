<?php

namespace GIS\CatalogExportYml\Helpers;

use GIS\CategoryProduct\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class YmlActionsManager
{
    protected YMLDocument $document;

    public function generateNewFile(string $fileName = null): void
    {
        $this->document = new YmlDocument();
        if (!empty($fileName)) { $this->document->setFileName($fileName); }

        $categories = $this->getRootCategories();
        debugbar()->info($categories);
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
