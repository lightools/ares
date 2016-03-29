<?php

namespace Lightools\Tests;

use Bitbang\Http\BadResponseException;
use Bitbang\Http\IClient;
use Bitbang\Http\Request;
use Bitbang\Http\Response;
use Lightools\Ares\CompanyFinder;
use Lightools\Ares\LookupFailedException;
use Lightools\Xml\XmlLoader;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

/**
 * @testCase
 * @author Jan Nedbal
 */
class CompanyFinderTest extends TestCase {

    public function testFound() {
        $response = file_get_contents(__DIR__ . '/responses/found.xml');
        $client = $this->createClientMock($response);

        $loader = new XmlLoader();
        $finder = new CompanyFinder($client, $loader);

        $company = $finder->find('27082440');
        Assert::same('27082440', $company->getIdentification());
        Assert::same('Alza.cz a.s.', $company->getCompanyname());
        Assert::same('CZ27082440', $company->getVatNumber());
        Assert::same('17000', $company->getPostalCode());
        Assert::same('1530/33', $company->getHouseNumber());
    }

    public function testNotFound() {
        $response = file_get_contents(__DIR__ . '/responses/not-found.xml');
        $client = $this->createClientMock($response);

        $loader = new XmlLoader();
        $finder = new CompanyFinder($client, $loader);

        $company = $finder->find('66872944');
        Assert::null($company);
    }

    public function testLookupFail() {
        $client = Mockery::mock(IClient::class);
        $client->shouldReceive('process')->with(Request::class)->once()->andThrow(BadResponseException::class);

        $loader = new XmlLoader();
        $finder = new CompanyFinder($client, $loader);

        Assert::exception(function () use ($finder) {
            $finder->find('66872944');
        }, LookupFailedException::class);
    }

    protected function tearDown() {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @param string $responseBody
     * @return MockInterface
     */
    private function createClientMock($responseBody) {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('getCode')->once()->andReturn(Response::S200_OK);
        $response->shouldReceive('getBody')->once()->andReturn($responseBody);

        $client = Mockery::mock(IClient::class);
        $client->shouldReceive('process')->with(Request::class)->once()->andReturn($response);

        return $client;
    }

}

(new CompanyFinderTest)->run();
