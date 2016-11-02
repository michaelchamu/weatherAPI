<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RainTest extends TestCase
{

    public function testEndpoints() {
        $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost:3000/api/']);
        //MCS TestCase
        $endPoint1 = $client->request('GET', 'rain/Namibia/stationtype/MCS/monthly/2015-10-10/2016-01-01');
        $endPoint2 = $client->request('GET', 'rain/Namibia/stationtype/MCS/monthly/2015-10');
        $endPoint10 = $client->request('GET', 'rain/Namibia/stationtype/MCS/daily/2015-10-10/2016-01-01');
        $endPoint11 = $client->request('GET', 'rain/Namibia/stationtype/MCS/daily/2015-10-10');
        $endPoint12 = $client->request('GET', 'rain/Namibia/stationtype/MCS/hourly/2015-10-10/2016-01-01');
        $endPoint13 = $client->request('GET', 'rain/Namibia/stationtype/MCS/hourly/2015-10-10');
        //Type1 TestCase
        $endPoint14 = $client->request('GET', 'rain/Angola/stationtype/typ1/monthly/2015-10/2016-01');
        $endPoint15 = $client->request('GET', 'rain/Angola/stationtype/typ1/monthly/2015-10');
        $endPoint16 = $client->request('GET', 'rain/Angola/stationtype/typ1/daily/2015-10-10/2016-01-01');
        $endPoint17 = $client->request('GET', 'rain/botswana/stationtype/typ1/daily/2015-10-10');
        $endPoint18 = $client->request('GET', 'rain/Angola/stationtype/typ1/hourly/2015-10-10/2016-01-01');
        $endPoint19 = $client->request('GET', 'rain/South africa/stationtype/typ1/hourly/2015-10-10');

        $endPoint3 = $client->request('GET', 'rain/Namibia/interval/2015-10-10/2016-10/monthly');
        $endPoint5 = $client->request('GET', 'rain/zambia/interval/2015-10-10/2016-10-10/daily');
        $endPoint9 = $client->request('GET', 'rain/Namibia/interval/2015-10-10/2016-10-10/hourly');

        $endPoint4 = $client->request('GET', 'rain/Namibia/2016/1');
        $endPoint6 = $client->request('GET', 'rain/Namibia/');

        $endPoint7 = $client->request('GET', 'rain/zambia/dates/2015-10-10/2016-01-01');
        $endPoint8 = $client->request('GET', 'rain/Namibia/dates/2015-10-10');

        $this->assertContains('Rain_By_Station', $endPoint1->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint2->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint10->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint11->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint12->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint13->getBody()->getContents());

        $this->assertContains('Rain_By_Station', $endPoint14->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint15->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint16->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint17->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint18->getBody()->getContents());
        $this->assertContains('Rain_By_Station', $endPoint19->getBody()->getContents());

        $this->assertContains('Rain_By_Interval', $endPoint3->getBody()->getContents());
        $this->assertContains('Rain_By_Interval', $endPoint5->getBody()->getContents());
        $this->assertContains('Rain_By_Interval', $endPoint9->getBody()->getContents());

        $this->assertContains('Rain_by_Year', $endPoint4->getBody()->getContents());
        $this->assertContains('Rain_by_Year', $endPoint6->getBody()->getContents());

        $this->assertContains('Rain_By_Date', $endPoint7->getBody()->getContents());
        $this->assertContains('Rain_By_Date', $endPoint8->getBody()->getContents());
    }

}
