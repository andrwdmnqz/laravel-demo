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
        $imageNode = $xpath->query("//a[contains(@class, 'realty-photo all-clickable is_shadow')]");

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
                $temp = null;
                $area = '';

                $price = trim($item->textContent);
                $city = $cityNode->length > 0 ? trim($cityNode[$itemCounter]->textContent) : '';

                $name = $nameNode->length > 0 ? trim($nameNode[$itemCounter]->textContent) : '';
                $href = $nameNode[$itemCounter]->getAttribute('href');
                $imageElement = $imageNode[$itemCounter]->getElementsByTagName('img')->item(0);
                $src = $imageElement->getAttribute('src');
                $name = str_replace(" \u{A0}", "", $name);
                $name = preg_replace('/\s+/', ' ', trim($name));

                $tempItem = 1;
                $temp = $xpath->query("preceding-sibling::*[1]", $cityNode[$itemCounter])->item(0);
                $temp = trim($temp->textContent);
                $temp = preg_replace('/\s+/', ' ', trim($temp));
                $temp = str_replace(" ·", "", $temp);

                while ($temp !== $name) {
                    $temp .= ", ";
                    $area .= $temp;
                    $tempItem++;
                    $temp = $xpath->query("preceding-sibling::*[$tempItem]", $cityNode[$itemCounter])->item(0);
                    if ($temp) {
                        $temp = trim($temp->textContent);
                        $temp = preg_replace('/\s+/', ' ', trim($temp));
                        $temp = str_replace(" ·", "", $temp);
                        $temp = str_replace(" \u{A0}", "", $temp);
                    } else {
                        break;
                    }
                }

                $area = trim($area);

                if ($charsNode->length > 0) {
                    $priceForMeter = $charsNode->item($itemCounter * 4)->textContent;
                    $rooms = $charsNode->item($itemCounter * 4 + 1)->textContent;
                    $square = $charsNode->item($itemCounter * 4 + 2)->textContent;
                    $floor = $charsNode->item($itemCounter * 4 + 3)->textContent;
                }

                $price = preg_replace('/\s+/', ' ', trim($price));
                $price = str_replace(" Додати в обране", "", $price);

                $tempDataItem["price"] = $price;
                $tempDataItem["meter_price"] = $priceForMeter;
                $tempDataItem["name"] = $name;
                $tempDataItem["area"] = $area;
                $tempDataItem["city"] = $city;
                $tempDataItem["rooms"] = $rooms;
                $tempDataItem["square"] = $square;
                $tempDataItem["floor"] = $floor;
                $tempDataItem["href"] = $href;
                $tempDataItem["src"] = $src;

                if ($tempDataItem) {
                    $data[] = $tempDataItem;
                }
                $itemCounter++;
            }
        }

        return view('buy-house', ['data' => $data]);
    }
}
