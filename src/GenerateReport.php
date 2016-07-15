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
    private $storage;
    private $mime_types = array(
        'html' => 'text/html',
        'pdf' => 'application/pdf',
        'xls' => 'application/vnd.ms-excel',
        'csv' => 'text/csv',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'rtf' => 'text/rtf',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'xlsx' => 'application/xlsx'
    );


    /**
     * Generate constructor.
     * @param $storage
     */
    public function __construct($storage)
    {
        $this->server = config('report.message');
        $this->username = config('report.username');
        $this->password = config('report.password');
        $this->organization = config('report.organization');
        $this->storage = $storage;
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
        if ($this->mimeType($format)) {
            $this->client = new Client();
            $myFile = fopen($this->storage . '/' . $filename . "." . $format, "w") or die("Problems");
            $request = new Request('GET', 'http://23.253.151.166:8080/jasperserver/rest_v2/reports/reports/sigeturbo/Purchases/Purchase.xlsx?interactive=true&onePagePerSheet=false&freshData=true&saveDataSnapshot=false&codeID=20160715-01');
            $this->client->send($request, [
                'auth' => [$this->username, $this->password],
                'alt' => 'media',
                'sink' => $myFile
            ]);
        }

    }


    private function mimeType($format)
    {
        if ($this->mime_types[$format]) {
            return true;
        }
        return false;
    }


}