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
     * @param string $hostname
     * @param string $port
     * @param string $baseUrl
     * @param null $organization
     */
    public function __construct($hostname = 'localhost', $port = '8080', $baseUrl = "/jasperserver", $organization = null)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->baseUrl = $baseUrl;
        $this->organization = $organization;
    }

    /**
     * Run Report
     * @param $storage
     * @param $uri
     * @param string $format
     * @param string $filename
     * @param array $controls
     * @param null $ignorePagination
     * @param null $pages
     * @param null $attachmentsPrefix
     * @param bool $interactive
     * @param bool $onePagePerSheet
     * @param bool $freshData
     * @param bool $saveDataSnapshot
     * @param null $transformerKey
     * @return bool
     */
    public function run($storage, $uri, $format = 'pdf', $filename = 'report', $controls = [], $ignorePagination = null, $pages = null, $attachmentsPrefix = null, $interactive = true, $onePagePerSheet = false, $freshData = true, $saveDataSnapshot = false, $transformerKey = null)
    {
        if ($this->mimeType($format)) {
            $this->client = new Client();
            $file = fopen($storage . '/' . $filename . "." . $format, "w") or die("Problems");
            $url = $this->getUrl($uri, $format, $controls, $pages, $ignorePagination, $attachmentsPrefix, $interactive, $onePagePerSheet, $freshData, $saveDataSnapshot, $transformerKey);
            $request = new Request('GET', $url);
            if ($format !== 'html') {
                $this->client->send($request, [
                    'auth' => [config('report.username'), config('report.password')],
                    'alt' => 'media',
                    'sink' => $file
                ]);
                return true;
            } else {
                $response = $this->client->send($request, [
                    'auth' => [config('report.username'), config('report.password')]
                ]);
                echo $response->getBody();
            }
        }

    }

    /**
     * Get URL
     * @param $uri
     * @param $format
     * @param $controls
     * @param $ignorePagination
     * @param $pages
     * @param $attachmentsPrefix
     * @param $interactive
     * @param $onePagePerSheet
     * @param $freshData
     * @param $saveDataSnapshot
     * @param $transformerKey
     * @return string
     */
    private function getUrl($uri, $format, $controls, $ignorePagination, $pages, $attachmentsPrefix, $interactive, $onePagePerSheet, $freshData, $saveDataSnapshot, $transformerKey)
    {

        $url = "http://" . $this->hostname . ":" . $this->port . $this->baseUrl . '/' . config('report.version') . "/reports" . $uri . "." . $format;
        if (empty($controls)) {
            $url .= '?' . Util::query_suffix(compact("ignorePagination", "pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"));
        } else {
            $url .= '?' . Util::query_suffix(array_merge(compact("ignorePagination", "pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"), $controls));
        }
        return $url;
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