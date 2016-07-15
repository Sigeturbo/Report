<?php

namespace SigeTurbo\Report;

use Jaspersoft\Client\Client;

class GenerateReport
{
    protected $client;
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
    private $server;
    private $username;
    private $password;
    private $organization;

    /**
     * Generate constructor.
     * @param $server
     * @param $username
     * @param $password
     * @param $organization
     * @internal param $client
     */
    public function __construct($server, $username, $password, $organization)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
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
        $this->client = new Client($this->server, $this->username, $this->password, $this->organization);
        if (isset($uri) && isset($format)) {
            $report_data = $this->client->reportService()->runReport($uri, $format, null, null, $controls, true);
            if ($format !== 'html') {
                echo $this->prepareForDownload($report_data, $filename, $format);
            } else {
                echo $report_data;
            }
        }
    }

    /**
     * Prepare For Download
     * @param $data
     * @param $filename
     * @param $format
     */
    private function prepareForDownload($data, $filename, $format)
    {

        header('Cache-Control: max-age=0');
        header('Pragma: public');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment;filename=' . $filename . '.' . $format);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($data));
        if (isset($this->mime_types[$format])) {
            header('Content-Type: ' . $this->mime_types[$format]);
        } else {
            header('Content-Type: application/octet-stream');
        }

        echo $data;
    }


}