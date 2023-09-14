<?php

namespace App\Http\Controllers;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class ScrapeController extends Controller
{

    public function scrapeData() {
        $client = new Client();
        $url = 'https://dom.ria.com/uk/prodazha-kvartir/kremenchug/';

        $body = Http::get($url)->body();
        $doc = new DOMDocument();
        @$doc->loadHTML($body);
        $xpath = new DOMXPath($doc);

        $data = [];
        $res = $xpath->query("//b[contains(@class, 'size22')]");
        $itemCounter = 0;

        $nameNode = $xpath->query("//a[contains(@class, 'realty-link size22 bold break')]");
        $cityNode = $xpath->query("//a[@data-level='city']");
        $charsNode = $xpath->query("//span[contains(@class, 'point-before')]");

        if ($res->length > 0) {
            foreach ($res as $item) {

                $parent = $item->parentNode;

                if($parent instanceof DOMDocument){
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];
                $priceForMeter = null;
                $rooms = null;
                $square = null;
                $floor = null;

                $price = trim($item->textContent);
                $city = $cityNode->length > 0 ? trim($cityNode[$itemCounter]->textContent) : '';
                $area = $xpath->query("preceding-sibling::*[1]", $cityNode[$itemCounter])->item(0);
                $area = trim($area->textContent);
                $name = $nameNode->length > 0 ? trim($nameNode[$itemCounter]->textContent) : '';

                if ($charsNode->length > 0) {
                    $priceForMeter = $charsNode->item($itemCounter * 4)->textContent;
                    $rooms = $charsNode->item($itemCounter * 4 + 1)->textContent;
                    $square = $charsNode->item($itemCounter * 4 + 2)->textContent;
                    $floor = $charsNode->item($itemCounter * 4 + 3)->textContent;
                }

                $price = preg_replace('/\s+/', ' ', trim($price));
                $name = preg_replace('/\s+/', ' ', trim($name));
                $area = preg_replace('/\s+/', ' ', trim($area));

                $price = str_replace(" Додати в обране", "", $price);
                $name = str_replace(" \u{A0}", "", $name);
                $area = str_replace(" ·", "", $area);

                if ($name === $area) {
                    $area = null;
                }

                $tempDataItem["price"] = $price;
                $tempDataItem["meter_price"] = $priceForMeter;
                $tempDataItem["name"] = $name;
                $tempDataItem["area"] = $area;
                $tempDataItem["city"] = $city;
                $tempDataItem["rooms"] = $rooms;
                $tempDataItem["square"] = $square;
                $tempDataItem["floor"] = $floor;

                if ($tempDataItem) {
                    $data[] = $tempDataItem;
                }
                $itemCounter++;
            }
        }

        return view('buy-house', ['data' => $data]);
    }
}
