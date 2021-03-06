<?php
/**
 * Extended TCPDF class
 *
 * PHP version 5
 *
 * Copyright (C) Ere Maijala 2010-2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category MLInvoice
 * @package  Printing
 * @author   Ere Maijala <ere@labs.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://labs.fi/mlinvoice.eng.php
 */

use Michelf\Markdown;

/**
 * Extended TCPDF class
 *
 * @category MLInvoice
 * @package  Printing
 * @author   Ere Maijala <ere@labs.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://labs.fi/mlinvoice.eng.php
 */
class PDF extends TCPDF
{
    public $headerLeft = '', $headerCenter = '', $headerRight = '';
    public $footerLeft = '', $footerCenter = '', $footerRight = '';
    public $printHeaderOnFirstPage = false;
    public $printFooterOnFirstPage = false;
    public $headerLeftPos = 4;
    public $headerRightPos = 143;
    public $footerLeftPos = 4;
    public $footerRightPos = 143;
    public $markdown = false;

    /**
     * This method is used to render the page header.
     * It is automatically called by AddPage().
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    public function Header()
    {
        if ($this->PageNo() == 1 && !$this->printHeaderOnFirstPage) {
            return;
        }
        $this->SetY(10);
        $this->SetFont('Helvetica', '', 7);
        $this->SetX($this->headerLeftPos);
        $this->MultiCell(
            120, 5, $this->handlePageNum($this->headerLeft), 0, 'L', 0, 0
        );
        $this->SetX(75);
        $this->MultiCell(
            65, 5, $this->handlePageNum($this->headerCenter), 0, 'C', 0, 0
        );
        $this->SetX($this->headerRightPos);
        $this->MultiCell(
            60, 5, $this->handlePageNum($this->headerRight), 0, 'R', 0, 0
        );
    }

    /**
     * This method is used to render the page footer.
     * It is automatically called by AddPage().
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    public function Footer()
    {
        if ($this->PageNo() == 1 && !$this->printFooterOnFirstPage) {
            return;
        }
        $this->SetY(-17);
        $this->SetFont('Helvetica', '', 7);
        $this->SetX($this->footerLeftPos);
        $this->MultiCell(120, 5, $this->footerLeft, 0, 'L', 0, 0);
        $this->SetX(75);
        $this->MultiCell(65, 5, $this->footerCenter, 0, 'C', 0, 0);
        $this->SetX($this->footerRightPos);
        $this->MultiCell(60, 5, $this->footerRight, 0, 'R', 0, 0);
    }

    /**
     * MultiCell with Markdown support.
     *
     * This method allows printing text with line breaks.
     * They can be automatic (as soon as the text reaches the right border of the
     * cell) or explicit (via the \n character). As many cells as necessary are
     * output, one below the other.<br />
     * Text can be aligned, centered or justified. The cell block can be framed and
     * the background painted.
     *
     * @param float  $w     (float) Width of cells. If 0, they extend up to the
     * right margin of the page.
     * @param float  $h     (float) Cell minimum height. The cell extends
     * automatically if needed.
     * @param string $txt   (string) String to print
     * @param string $align (string) Allows to center or align the text.
     * Possible values are:<ul><li>L or empty string: left align</li>
     * <li>C: center</li><li>R: right align</li><li>J: justification (default value
     * when $ishtml=false)</li></ul>
     * @param int    $ln    (int) Indicates where the current position should
     * go after the call. Possible values are:<ul><li>0: to the right</li><li>1: to
     * the beginning of the next line [DEFAULT]</li><li>2: below</li></ul>
     * @param float  $maxh  (float) maximum height. It should be >= $h and less
     * then remaining space to the bottom of the page, or 0 for disable this feature.
     * This feature works only when $ishtml=false.
     * @param bool   $md    Whether the input is to be interpreted as Markdown.
     *
     * @return int Return the number of cells or 1 for html mode.
     */
    public function multiCellMD($w, $h, $txt, $align = 'J', $ln = 1,
        $maxh = 0, $md = false
    ) {
        if (!$md || !$this->markdown) {
            $this->MultiCell(
                $w,
                $h,
                $txt,
                0,
                $align,
                false,
                $ln,
                '',
                '',
                true,
                0,
                false,
                true,
                $maxh
            );
        } else {
            $html = Markdown::defaultTransform($txt);
            $this->writeHTMLCell(
                $w,
                $h,
                '',
                '',
                $html,
                0,
                $ln,
                false,
                true,
                $align,
                true
            );
        }
    }

    /**
     * Include page number in a header string if appropriate
     *
     * @param string $str Header string
     *
     * @return string
     */
    protected function handlePageNum($str)
    {
        return sprintf($str, $this->PageNo());
    }
}
