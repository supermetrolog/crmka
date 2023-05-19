<?php

namespace app\components\avito;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMException;
use InvalidArgumentException;
use yii\helpers\VarDumper;

class AvitoFeedGenerator
{
    /** @var array<AvitoObject[]>  */
    private array $data = [];
    private DOMDocument $xml;

    public function __construct()
    {
        $this->xml = new DOMDocument();
    }

    /**
     * @param array<AvitoObject[]> $avitoObjects
     */
    public function setAvitoObjects(array $avitoObjects): void
    {
        $this->data = $avitoObjects;
    }

    /**
     * @return string
     * @throws DOMException
     */
    public function generate(): string
    {
        $x = $this->xml;

        $x->encoding = 'utf-8';
        $x->xmlVersion = '1.0';
        $x->formatOutput = true;

        $ads = $x->createElement('Ads');
        $ads->setAttribute('target', 'Avito.ru');
        $ads->setAttribute('formatVersion', '3');

        $this->setItems($ads);

        $x->appendChild($ads);
        return $this->xml->saveXML();
    }

    /**
     * @param DOMElement $ads
     * @return void
     * @throws DOMException
     */
    private function setItems(DOMElement $ads): void
    {
        foreach ($this->data as $obj) {
            $ad = $this->xml->createElement('Ad');

            foreach ($obj as $item) {
                $elem = $this->xml->createElement($item->name, $item->value);
                $ad->appendChild($elem);
            }

            $ads->appendChild($ad);
        }
    }
}