<?php
//**********************************************************************************************
//* EmailHtmlFormatter.class.php
/** * ********************************************************************************************
 * EmailHtmlFormatter.class.php
 *
 * Summary: extends monolog to send emails on critical or error logs
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 *
 *
 *
 * @package monlog
 * @since 0.3.0
 *
 * @example
 *
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************
//***********************************************************************************************************
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

use Monolog\Logger;
use Monolog\Formatter\NormalizerFormatter;

//***********************************************************************************************************
/**
 * Formats incoming records into an HTML table
 *
 * This is especially useful for html email logging
 *
 * @author Tiago Brito <tlfbrito@gmail.com>
//************************************************************************************************************/
class EmailHtmlFormatter extends NormalizerFormatter
{

    /**
     * Translates Monolog log levels to html color priorities.
     */
    protected $logLevels = array(
        Logger::DEBUG     => '#cccccc',
        Logger::INFO      => '#468847',
        Logger::NOTICE    => '#3a87ad',
        Logger::WARNING   => '#c09853',
        Logger::ERROR     => '#f0ad4e',
        Logger::CRITICAL  => '#FF7708',
        Logger::ALERT     => '#C12A19',
        Logger::EMERGENCY => '#000000',
    );

	//-----------------------------------------------------------------------------------------------
     /* @param string $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct($dateFormat = null)
    {
        parent::__construct($dateFormat);
    }

	//-----------------------------------------------------------------------------------------------
     /* Creates an HTML table row
     *
     * @param  string $th       Row header content
     * @param  string $td       Row standard cell content
     * @param  bool   $escapeTd false if td content must not be html escaped
     * @return string
     */
    protected function addRow($th, $td = ' ', $escapeTd = true)
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
        if ($escapeTd) {
            $td = '<pre>'.htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8').'</pre>';
        }

        return "<tr style=\"padding: 4px;text-align: left;\">\n<th style=\"vertical-align: top;background: #ccc;color: #000\" width=\"100\">$th:</th>\n<td style=\"padding: 4px;text-align: left;vertical-align: top;background: #eee;color: #000\">".$td."</td>\n</tr>";
    }

	//-----------------------------------------------------------------------------------------------
     /* Create a HTML h1 tag
     *
     * @param  string $title Text to be in the h1
     * @param  int    $level Error level
     * @return string
     */
    protected function addTitle($title, $level)
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');

        return '<h1 style="background: '.$this->logLevels[$level].';color: #ffffff;padding: 5px;" class="monolog-output">'.$title.'</h1>';
    }

	//-----------------------------------------------------------------------------------------------
     /* Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $output = $this->addTitle($record['level_name'], $record['level']);
        $output .= '<table cellspacing="1" width="100%" class="monolog-output">';

        $output .= $this->addRow('Message', (string) $record['message']);
        $output .= $this->addRow('Time', $record['datetime']->format($this->dateFormat));
        $output .= $this->addRow('Channel', $record['channel']);
        if ($record['context']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record['context'] as $key => $value) {
            	if ($key != Settings::GetProtected( 'CRITICAL_EMAIL_PAYLOAD')){
	                $embeddedTable .= $this->addRow($key, $this->convertToString($value));
	            }
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Context', $embeddedTable, false);
        }
        if ($record['extra']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record['extra'] as $key => $value) {
            	if ($key != Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD')){
                	$embeddedTable .= $this->addRow($key, $this->convertToString($value));
            	}
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Extra', $embeddedTable, false);
        }

		$output .= '</table>';
		$output .= '<pre>';
		if ( !empty( Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD_EXTRA'))) {
			$output .= '<h2>-Extra-</h2>';
			//$output .= print_r(($record['extra'][Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD_EXTRA')] ?? '') , true) ;
			$output .= print_r(Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD_EXTRA') , true) ;
		}

		if (!empty(Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD_CONTEXT') )){
			$output .= '<h2>-Context-</h2>';
			//$output .= print_r(($record['context'][Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD_CONTEXT')] ?? ''), true) ;
			$output .= print_r(Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD_CONTEXT'), true) ;
		}
		$output .= '</pre>';

        return $output;
    }

	//-----------------------------------------------------------------------------------------------
     /* Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

	//-----------------------------------------------------------------------------------------------
    protected function convertToString($data)
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        $data = $this->normalize($data);
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return str_replace('\\/', '/', json_encode($data));
    }
}
