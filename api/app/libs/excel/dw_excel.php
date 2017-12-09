<?php

/**
 * Dailyscript - app | web | media
 *
 * Tamaño 286 para 'l' con 5 de x
 * Tamaño 186 para 'p'
 *
 * @category    Librería para el manejo de pdf's
 * @package     Libs
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
*/
require_once __DIR__.'/dw_sheet.php';

class DwExcel extends \PHPExcel {

    /**
     * Font Bold
     */
    const BOLD    =   1;

    /**
     * Font Italic
     */
    const ITALIC  =   2;

    /**
     * Text Left
     */
    const LEFT    =   4;

    /**
     * Text Right
     */
    const RIGHT   =   8;

    /**
     * Text Center
     */
    const CENTER  =  16;

    /**
     * Landscape
     */
    const LANDSCAPE	= 'landscape';

    /**
     * Portrait
     */
	const PORTRAIT	= 'portrait';

    /**
     * Spreadsheet filename
     * @var string
     */
    public $filename;

    /**
     * Spreadsheet title
     * @var string
     */
    public $title;

    /**
     * Excel object
     * @var \PHPExcel
     */
    public $excel;

    /**
     * Spreadsheet writer
     * @var object
     */
    public $writer;

    /**
     * Excel sheet
     * @var ExcelWorksheet
     */
    protected $sheet;

    /**
     * Default extension
     * @var string
     */
    public $ext = 'xlsx';

    /**
     * Path the file will be stored to
     * @var string
     */
    public $storagePath = 'exports';

    /**
     * Header Content-type
     * @var string
     */
    protected $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Create a new file
     * @param                $filename
     * @param  callable|null $callback
     */
    public static function create($filename, $callback = null) {

        $writer = new DwExcel();
        $writer->disconnectWorksheets();

        // Set the filename and title
        $writer->setFileName($filename);
        $writer->setTitle($filename);

        $writer->writer     = $writer;
        // Do the callback
        if ($callback instanceof Closure)
            call_user_func($callback, $writer);

        // Return the writer object
        return $writer;

    }

    /**
     * Set the spreadsheet title
     * @param string $title
     * @return  LaravelExcelWriter
     */
    public function setTitle($title) {

        $this->title = $title;
        $this->getProperties()->setTitle($title);

        return $this;

    }

    /**
     * Set the filename
     * @param  $name
     * @return $this
     */
    public function setFileName($name) {

        $this->filename = $name;
        return $this;

    }

    /**
     * Create a new sheet
     * @param  string        $title
     * @param  callback|null $callback
     */
    public function sheet($title, $callback = null) {

        // Clone the active sheet
        $this->sheet    = $this->createSheet(null, $title);

        // Do the callback
        if ($callback instanceof Closure)
            call_user_func($callback, $this->sheet);

        return $this;

    }

    public function createSheet($iSheetIndex = null, $title = false) {

        // Init new Excel worksheet
        $newSheet = new DwSheet($this, $title);
        $this->addSheet($newSheet, $iSheetIndex);
        // Return the sheet
        return $newSheet;

    }

    /**
     * Export the spreadsheet
     * @param string $ext
     * @param array  $headers
     * @throws LaravelExcelException
     */
    public function export($ext = 'xls', Array $headers = array()) {
        $this->writer->setActiveSheetIndex(0);
		$this->ext = $ext === 'xls' ? 'xlsx' : $ext;
        // Download the file
        $this->_download($headers);

    }

    /**
     * Export and download the spreadsheet
     * @param  string $ext
     * @param array   $headers
     */
    public function download($ext = 'xls', Array $headers = array()) {

        $this->export($ext, $headers);

    }

    /**
     * Download a file
     * @param array $headers
     * @throws LaravelExcelException
     */
    protected function _download() {

        // Set the headers
        $this->_setHeaders([
            'Content-Type'        => $this->contentType,
            'Content-Disposition' => 'attachment; filename="' . $this->filename . '.' . $this->ext . '"',
            'Expires'             => 'Mon, 26 Jul 1997 05:00:00 GMT', // Date in the past
            'Last-Modified'       => date('D, d M Y H:i:s'),
            'Cache-Control'       => 'cache, must-revalidate',
            'Pragma'              => 'public'
            ]
        );

        // Check if writer isset
        if (!$this->writer)
            throw new RestException('[ERROR] No writer was set.');

        $objWriter = PHPExcel_IOFactory::createWriter($this->writer, 'Excel2007');
        // Download
        $objWriter->save('php://output');

        // End the script to prevent corrupted xlsx files
        exit;

    }

    /**
     * Set the headers
     * @param $headers
     * @throws LaravelExcelException
     */
    protected function _setHeaders(Array $headers = array()) {

        if (headers_sent())
            throw new RestException('[ERROR]: Headers already sent');

        foreach ($headers as $header => $value) {
            header($header . ': ' . $value);
        }

    }

}
