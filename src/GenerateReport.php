<?php

namespace SigeTurbo\Report;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use SigeTurbo\Report\Tool\Util;

class GenerateReport
{

    protected $client;
    private $hostname;
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
     * @param string $hostname
     * @param string $port
     * @param string $baseUrl
     * @param null $organization
     */
    public function __construct($storage, $hostname = 'localhost', $port = '8080', $baseUrl = "/jasperserver", $organization = null)
    {
        $this->storage = $storage;
        $this->hostname = $hostname;
        $this->port = $port;
        $this->baseUrl = $baseUrl;
        $this->organization = $organization;
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
            $file = fopen($this->storage . '/' . $filename . "." . $format, "w") or die("Problems");
            $url = $this->getUrl($uri, $format, $controls);
            $request = new Request('GET', $url);
            $this->client->send($request, [
                'auth' => [config('report.username'), config('report.password')],
                'alt' => 'media',
                'sink' => $file
            ]);
        }

    }

    /**
     * Get URL
     * @param $uri
     * @param $format
     * @param $controls
     * @return string
     */
    private function getUrl($uri, $format, $controls)
    {

        $url = "http://" . $this->hostname . ":" . $this->port . $this->baseUrl . config('report.version') . "/reports" . $uri . "." . $format;
        if (empty($controls)) {
            $url .= '?' . Util::query_suffix(compact("pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"));
        } else {
            $url .= '?' . Util::query_suffix(array_merge(compact("pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"), $controls));
        }
        //return $url;
        dd($url);
        exit();
        //'http://23.253.151.166:8080/jasperserver/rest_v2/reports/reports/sigeturbo/Purchases/Purchase.xlsx?interactive=true&onePagePerSheet=false&freshData=true&saveDataSnapshot=false&codeID=20160715-01'
    }


    /**
     * Verify Valid MimeType
     * @param $format
     * @return bool
     */
    private function mimeType($format)
    {
        if ($this->mime_types[$format]) {
            return true;
        }
        return false;
    }


}