<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemperatureTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTemperatureEndpoints(){
        $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost:3000/api/']);
        //temp by dates
        $endPoint1 = $client->request('GET', 'temperature/Namibia/dates/2015-10-10/2016-01-01');
        $endPoint2 = $client->request('GET', 'temperature/Zambia/dates/2016-01-01');
        //temp by stationtype and interval
        $endPoint3 = $client->request('GET', 'temperature/Botswana/stationtype/mcs/monthly/2014-01-01/2014-02-02');
        $endPoint4 = $client->request('GET', 'temperature/Namibia/stationtype/typ1/daily/2014-01-01');
        $endPoint5 = $client->request('GET', 'temperature/Botswana/stationtype/typ1/hourly/2015-01-01/2015-02-02');
        //temp by year and month
        $endPoint6 = $client->request('GET', 'temperature/Botswana/2014/3');
        $endPoint7 = $client->request('GET', 'temperature/Angola/2014');
        //$endPoint8 = $client->request('GET', 'temperature/south africa/');
        //temp by date ranges only
        $endPoint9 = $client->request('GET', 'temperature/Botswana/interval/2014-01-01/2014-02-02/daily');
        $endPoint10 = $client->request('GET', 'temperature/Zambia/interval/2014-01-01/2014-02-02/monthly');
        $endPoint11 = $client->request('GET', 'temperature/Namibia/interval/2014-01-01/2014-02-02/hourly');

        $this->assertContains('Temperature_By_Dates', $endPoint1->getBody()->getContents());
        $this->assertContains('Temperature_By_Dates', $endPoint2->getBody()->getContents());

        $this->assertContains('Temperature_By_Station', $endPoint3->getBody()->getContents());
        $this->assertContains('Temperature_By_Station', $endPoint4->getBody()->getContents());
        $this->assertContains('Temperature_By_Station', $endPoint5->getBody()->getContents());

        $this->assertContains('Temperature_By_YearMonth', $endPoint6->getBody()->getContents());
        $this->assertContains('Temperature_By_YearMonth', $endPoint7->getBody()->getContents());
        //$this->assertContains('Temperature_By_YearMonth', $endPoint8->getBody()->getContents());

        $this->assertContains('Temperature_By_DateRange', $endPoint9->getBody()->getContents());
        $this->assertContains('Temperature_By_DateRange', $endPoint10->getBody()->getContents());
        $this->assertContains('Temperature_By_DateRange', $endPoint11->getBody()->getContents());
    }
}
