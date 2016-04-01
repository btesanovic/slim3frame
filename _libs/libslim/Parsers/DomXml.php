<?php

namespace Libslim\Parsers;

/**
 * 
 * XML Parser for parsing API responses.
 * 
 * @author andreja, bojan, radovan
 * @copyright  Vast.com, Inc
 * @category   Vast
 * @package    Vast
 */
class DomXml extends \DOMDocument {

    /**
     * 
     * DOMXPath object
     * @var DOMXPath
     */
    private $xpath;

    function __construct() {
        
    }

    /**
     * 
     * Creates a new DOMXPath object from passed XML string or file/url path
     * @param string - $xml - file or URL path, or xml string
     */
    public function doDom($xml) {
        if (substr($xml, 0, 7) == "http://") {
            $this->load($xml);
        } elseif (is_file($xml)) {
            $this->load($xml);
        } elseif (is_string($xml)) {
            $this->loadXML($xml, LIBXML_NOCDATA);
        } elseif ($xml instanceof DOMDocument) {
            $this->loadXML($xml->saveXML());
        }
        $this->xpath = new \DOMXPath($this);
        return $this;
    }

    /**
     * 
     * ToArray passed contextnode or root if no node passed 
     * @param xml $node
     * @return array
     */
    public function toArray($node = false, $fixName = 'afeed') {
        if (!$node) {
            $node = $this;
            $rootNode = "/";
        } else {
            $rootNode = "./";
        }

        $arr = array();


        $hasAttribs = false;
        if ($node->hasAttributes()) {
            $hasAttribs = 1;
            foreach ($node->attributes as $att) {
                $arr[$fixName . '$' . $att->nodeName] = $att->nodeValue;
            }
        }

        $out = '';
        if ($node->firstChild && $node->firstChild->nodeType == XML_TEXT_NODE && trim($node->firstChild->nodeValue) != '') {
            $out = html_entity_decode($node->firstChild->nodeValue);
            $out = preg_replace("/\n+/si", " ", $out);
            //$arr['text'] = $out;
            $arr['text'] = $out;
        }
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $subnode) {
                if ($subnode->nodeType != XML_TEXT_NODE) {
                    $tag_names = $this->xpath->query("./*[local-name()='" . $subnode->localName . "']", $node);

                    //echo $subnode->localName .' - ';
                    $fixedLocalName = str_replace('-', '_', $subnode->localName);
                    if ($subnode->hasAttributes()) {
                        $hasAttribs = 1;
                        foreach ($subnode->attributes as $att) {
                            $arr[$fixedLocalName . '$$' . $att->nodeName] = $att->nodeValue;
                        }
                    }
                    
                    if ($tag_names->length == 1) {
                        $arr[$fixedLocalName] = $this->toArray($subnode, $fixedLocalName);
                    } else {
                        $arr[$fixedLocalName][] = $this->toArray($subnode, $fixedLocalName);
                    }

                    
                }
            }
        }

        if (!$hasAttribs && $out) {
            return $out;
        }

        if (count($arr)) {
            return $arr;
        }
    }

    /**
     * 
     * Queries the XML DOM root or contextnode
     * @param xpath string $xpath
     * @param contextnode $node
     * 
     * @return - Returns a DOMNodeList containing all nodes matching the given XPath expression. 
     */
    public function query($xpath, $node = false) {
        return $this->xpath->query($xpath, $node);
    }

    /**
     * 
     * ToArray passed contextnode or root if no node passed 
     * @param xml $node
     * @return array
     */
    public function toArrayOld($node = false) {
        if (!$node) {
            $node = $this;
            $rootNode = "/";
        } else {
            $rootNode = "./";
        }

        $arr = array();

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $att) {
                echo "$att->nodeName | ";
                $arr[$att->nodeName] = $att->nodeValue;
            }
        }

        if ($node->firstChild && $node->firstChild->nodeType == XML_TEXT_NODE && trim($node->firstChild->nodeValue) != '') {
            $out = html_entity_decode($node->firstChild->nodeValue);
            $out = preg_replace("/\n+/si", " ", $out);
            $arr['text'] = $out;
        }
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $subnode) {
                if ($subnode->nodeType != XML_TEXT_NODE) {
                    $tag_names = $this->xpath->query("./*[local-name()='" . $subnode->localName . "']", $node);

                    //echo $subnode->localName .' - ';
                    $fixedLocalName = str_replace('-', '_', $subnode->localName);
                    if ($tag_names->length == 1) {
                        $arr[$fixedLocalName] = $this->toArray($subnode);
                    } else {
                        $arr[$fixedLocalName][] = $this->toArray($subnode);
                    }
                }
            }
        }

        if (count($arr)) {
            return $arr;
        }
    }

}
