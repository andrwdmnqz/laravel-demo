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
                $rooms = null;
                $square = null;
                $floor = null;

                $price = trim($item->textContent);

                $priceForMeterNode = $xpath->query("//span[contains(@class, 'size14 point-before')]");
                $priceForMeter = $priceForMeterNode->length > 0 ? trim($priceForMeterNode[0]->textContent) : '';

                $nameNode = $xpath->query("//a[contains(@class, 'realty-link size22 bold break')]");
                $name = $nameNode->length > 0 ? trim($nameNode[0]->textContent) : '';

                $areaNode = $xpath->query("//a[contains(@class, 'mb-5 i-block p-rel')]");
                $area = $areaNode->length > 0 ? trim($areaNode[0]->textContent) : '';

                $cityNode = $xpath->query("//a[contains(@class, 'mb-5 i-block p-rel')]/following-sibling::*[1]");
                $city = $cityNode->length > 0 ? trim($cityNode[0]->textContent) : '';

                $charsNode = $xpath->query("//span[contains(@class, 'point-before')]");
                if ($charsNode->length > 0) {
                    $rooms = $charsNode->item(1)->textContent;
                    $square = $charsNode->item(2)->textContent;
                    $floor = $charsNode->item(3)->textContent;
                }

                $price = preg_replace('/\s+/', ' ', trim($price));
                $name = preg_replace('/\s+/', ' ', trim($name));
                $area = preg_replace('/\s+/', ' ', trim($area));
                $city = preg_replace('/\s+/', ' ', trim($city));

                $price = str_replace(" Додати в обране", "", $price);
                $name = str_replace(" \u{A0}", "", $name);
                $area = str_replace(" ·", "", $area);

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
                    break;
                }
            }
        }

        return view('buy-house', ['data' => $data]);
    }
}
