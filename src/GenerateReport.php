<?php

namespace SigeTurbo\Report;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class GenerateReport
{
    protected $client;
    private $server;
    private $username;
    private $password;
    private $organization;

    /**
     * Generate constructor.
     * @param $username
     * @param $password
     * @param $organization
     * @internal param $client
     */
    public function __construct($username, $password, $organization)
    {
        $this->server = config('report.message');
        $this->username = config('report.username');
        $this->password = config('report.password');
        $this->organization = config('report.organization');
    }

    /**
     * Run Report
     * @param $uri
     * @param string $format
     * @param string $filename
     * @param array $controls
     */
    public function run($uri, $format = 'pdf', $filename = 'report', $controls = [])
    {
        $this->client = new Client();
        $myFile = fopen(storage_path() . '/sigeturbo/report/report1.xlsx',"w") or die("Problems");
        $request = new Request('GET','http://23.253.151.166:8080/jasperserver/rest_v2/reports/reports/sigeturbo/Purchases/Purchase.xlsx?interactive=true&onePagePerSheet=false&freshData=true&saveDataSnapshot=false&codeID=20160715-01');
        $this->client->send($request, [
            'auth' => [$this->username, $this->password],
            'alt' => 'media',
            'sink'=>$myFile
        ]);

    }



}