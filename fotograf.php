<?php

interface CatalogInterface
{
   public function getProducts($column, $order_asc): array;
}

final class Catalog implements CatalogInterface
{
   private $products;
   
   public function __construct(array $products)
   {
      $this->products = $products;
   }

   public function getProducts($column, $order_asc=true): array
   {  
      $data = $this->products;
      $results=[];
      $callback = $this->makeCallback($column);
      foreach ($data as $key => $value) {
         $results[$key] = $callback($value, $key);
      }
      $order_asc ? asort($results) : arsort($results);
      foreach (array_keys($results) as $key) {
         $results[$key] = $data[$key];
      }
      return array_values($results);
   }

   private function makeCallback($value): closure
   {
        if (is_callable($value)) {
            return $value;
        }

        return function ($item) use ($value) {
            return $item[$value];
        };
    }
}

$products = [
   [
      'id' => 1,
      'name' => 'Alabaster Table',
      'price' => 12.99,
      'created' => '2019-01-04',
      'sales_count' => 32,
      'views_count' => 730,
   ],
   [
      'id' => 2,
      'name' => 'Zebra Table',
      'price' => 44.49,
      'created' => '2012-01-04',
      'sales_count' => 301,
      'views_count' => 3279,
   ],
    [
      'id' => 3,
      'name' => 'Coffee Table',
      'price' => 10.00,
      'created' => '2014-05-28',
      'sales_count' => 1048,
      'views_count' => 20123,
   ]
];

$productPriceSorter = 'price';
$productSalesPerViewSorter = function ($item, $key) {
    return $item['sales_count'] / $item['views_count'];
};

$catalog = new Catalog($products);

$productsSortedByPrice = $catalog->getProducts($productPriceSorter);
$productsSortedBySalesPerView = $catalog->getProducts($productSalesPerViewSorter, false);
