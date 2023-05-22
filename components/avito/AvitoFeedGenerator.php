<?php

namespace app\components\avito;

use DOMDocument;
use DOMElement;
use DOMException;
use phpseclib3\Common\Functions\Strings;

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
                $elem = $this->xml->createElement($item->name);
                if(is_array($item->value)){
                    $this->setOptionElements($elem, $item->value);
                } else {
                    $elem->nodeValue = $item->value;
                }

                $ad->appendChild($elem);
            }

            $ads->appendChild($ad);
        }
    }

    /**
     * @param DOMElement $elem
     * @param array $options
     * @return void
     * @throws DOMException
     */
    private function setOptionElements(DOMElement $elem, array $options): void
    {
        foreach ($options as $option) {
            $optEl = $this->xml->createElement($option['tag'], $option['value']);
            foreach ($option['attributes'] as $attribute => $value) {
                $optEl->setAttribute($attribute, $value);
            }
            $elem->appendChild($optEl);
        }
    }
}