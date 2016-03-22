<?php

namespace Lightools\Ares;

use Bitbang\Http\BadResponseException;
use Bitbang\Http\IClient;
use Bitbang\Http\Request;
use Bitbang\Http\Response;
use Lightools\Xml\XmlException;
use Lightools\Xml\XmlLoader;

/**
 * @author Jan Nedbal
 */
class CompanyFinder {

    /**
     * @var string
     */
    const ENDPOINT = 'https://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ver=1.0.2&ico=';

    /**
     * @var IClient
     */
    private $httpClient;

    /**
     * @var XmlLoader
     */
    private $xmlLoader;

    /**
     * @param IClient $httpClient
     * @param XmlLoader $xmlLoader
     */
    public function __construct(IClient $httpClient, XmlLoader $xmlLoader) {
        $this->httpClient = $httpClient;
        $this->xmlLoader = $xmlLoader;
    }

    /**
     * Find company by identification given
     *
     * @param string $identification (8 digit string)
     * @return null|Company NULL if not found
     * @throws LookupFailedException
     */
    public function find($identification) {

        if (!preg_match('~^[0-9]{8}$~', $identification)) {
            return NULL;
        }

        try {
            $request = new Request(Request::GET, self::ENDPOINT . $identification);
            $response = $this->httpClient->process($request);

            if ($response->getCode() !== Response::S200_OK) {
                throw new LookupFailedException('Unexpected HTTP code from ARES API.');
            }

            $xmlDom = $this->xmlLoader->loadXml($response->getBody());
            $xml = simplexml_import_dom($xmlDom);

        } catch (XmlException $e) {
            throw new LookupFailedException('Invalid XML from ARES', NULL, $e);

        } catch (BadResponseException $e) {
            throw new LookupFailedException('HTTP request to ARES failed', NULL, $e);
        }

        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are'])->Odpoved->children($ns['dtt']);

        if (!$data->Pocet_zaznamu || (int) $data->Pocet_zaznamu === 0) {
            return NULL;
        }

        $companyData = $data->Vypis_basic;
        $address = $companyData->Adresa_ARES;
        $companyname = (string) $companyData->Obchodni_firma;
        $vatNumber = (string) $companyData->DIC;
        $postalCode = (string) $address->PSC;
        $city = (string) $address->Nazev_obce;
        $cityPart = (string) $address->Nazev_casti_obce;
        $cityDistrict = (string) $address->Nazev_mestske_casti;
        $street = (string) $address->Nazev_ulice;

        $houseNum = (string) $address->Cislo_domovni;
        $orientNum = (string) $address->Cislo_orientacni;
        $addrNum = (string) $address->Cislo_do_adresy;

        if (!empty($houseNum)) {
            $houseNumber = $houseNum;

            if (!empty($orientNum)) {
                $houseNumber .= '/' . $orientNum;
            }
        } elseif (!empty($addrNum)) {
            $houseNumber = $addrNum;
        } else {
            $houseNumber = '';
        }

        if ($vatNumber === 'Skupinove_DPH') {
            $vatNumber = '';
        }

        return new Company(
            $identification,
            $companyname,
            $vatNumber,
            $city,
            $cityPart,
            $cityDistrict,
            $street,
            $houseNumber,
            $postalCode
        );
    }

}
