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


class DwSheet extends \PHPExcel_Worksheet {

    /**
     * Parent Excel
     */
    protected $_parent;

    /**
     * Constructor
     */
    public function __construct(PHPExcel $pParent = null, $pTitle = 'Worksheet') {
        parent::__construct($pParent, $pTitle);
        $this->setParent($pParent);
    }
    /**
     * Set the parent (excel object)
     * @param PHPExcel $parent
     */
    public function setParent($parent) {
        $this->_parent = $parent;
    }

    /**
     * Get the parent excel obj
     * @return PHPExcel
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * Set orientation
     * @param type $orientation
     */
    public function setOrientation($orientation) {
        $this->getPageSetup()->setOrientation($orientation);
    }

    /**
     * Get cell
     * @param type $col
     * @param type $row
     * @return type
     */
    public function getCellName($col, $row) {
        $cell   = "";
        $col   += 65;
        $prefix = 64;
        while ($col > 90) {
            $prefix++;
            $col -= 26;
        }
        if ($prefix > 64) {
            $cell .= chr($prefix);
        }
        $cell .= chr($col);
        $cell .= $row;
        return $cell;
    }

    /**
     * Set Cell Value
     *
     * @param string $cell
     * @param string $value
     * @param integer $style
     */
    public function setCell($cell, $value, $style = null) {

        preg_match("/^([a-z]+)([0-9]+)$/i", $cell, $matches);

        $col = $matches[1];
        $row = $matches[2];

        $this->SetCellValue($cell, $value);

        $this->getColumnDimension($col)->setAutoSize(true);

        if (!$style) {
            $style  = DwExcel::LEFT;
        }

        if ($style & DwExcel::BOLD || is_array($style) && in_array(DwExcel::BOLD, $style)) {
            $this->getStyle($cell)->getFont()->setBold(true);
        }

        if ($style & DwExcel::ITALIC || is_array($style) && in_array(DwExcel::ITALIC, $style)) {
            $this->getStyle($cell)->getFont()->setItalic(true);
        }

        if ($style & DwExcel::LEFT || is_array($style) && in_array(DwExcel::LEFT, $style)) {
            $this->getStyle($cell)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        if ($style & DwExcel::RIGHT || is_array($style) && in_array(DwExcel::RIGHT, $style)) {
            $this->getStyle($cell)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }

        if ($style & DwExcel::CENTER || is_array($style) && in_array(DwExcel::CENTER, $style)) {
            $this->getStyle($cell)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

    }

}
