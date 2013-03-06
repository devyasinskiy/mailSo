<?php

namespace MailSo\Log;

/**
 * @category MailSo
 * @package Log
 */
abstract class Driver
{
	/**
	 * @var string
	 */
	protected $sDatePattern;

	/**
	 * @var string
	 */
	protected $sName;

	/**
	 * @var array
	 */
	protected $aPrefixes;

	/**
	 * @access protected
	 */
	protected function __construct()
	{
		$this->sDatePattern = 'H:i:s';
		$this->sName = 'INFO';
		$this->aPrefixes = array(
			\MailSo\Log\Enumerations\Type::INFO => '[DATA]',
			\MailSo\Log\Enumerations\Type::NOTICE => '[NOTICE]',
			\MailSo\Log\Enumerations\Type::WARNING => '[WARNING]',
			\MailSo\Log\Enumerations\Type::ERROR => '[ERROR]',
			\MailSo\Log\Enumerations\Type::SECURE => '[SECURE]',
			\MailSo\Log\Enumerations\Type::NOTE => '[NOTE]',
			\MailSo\Log\Enumerations\Type::TIME => '[TIME]',
			\MailSo\Log\Enumerations\Type::MEMORY => '[MEMORY]',
		);
	}

	/**
	 * @param string $sDesc
	 * @return bool
	 */
	abstract protected function writeImplementation($sDesc);

	/**
	 * @param string $sTimePrefix
	 * @param string $sDesc
	 * @param int $iDescType = \MailSo\Log\Enumerations\Type::INFO
	 * @param array $sName = ''
	 *
	 * @return string
	 */
	protected function loggerLineImplementation($sTimePrefix, $sDesc,
		$iDescType = \MailSo\Log\Enumerations\Type::INFO, $sName = '')
	{
		return '['.$sTimePrefix.'] '.$this->getTypedPrefix($iDescType, $sName).$sDesc;
	}

	/**
	 * @return bool
	 */
	protected function clearImplementation()
	{
		return true;
	}

	/**
	 * @return string
	 */
	protected function getTimeWithMicroSec()
	{
		$aMicroTimeItems = \explode(' ', \microtime());
		return \gmdate($this->sDatePattern, $aMicroTimeItems[1]).'.'.
			\str_pad((int) ($aMicroTimeItems[0] * 1000), 3, '0', STR_PAD_LEFT);
	}

	/**
	 * @param int $iDescType
	 * @param string $sName = ''
	 *
	 * @return string
	 */
	protected function getTypedPrefix($iDescType, $sName = '')
	{
		$sName = 0 < \strlen($sName) ? $sName : $this->sName;
		return isset($this->aPrefixes[$iDescType]) ? $sName.$this->aPrefixes[$iDescType].': ' : '';
	}

	/**
	 * @final
	 * @param string $sDesc
	 * @param int $iDescType = \MailSo\Log\Enumerations\Type::INFO
	 * @param array $sName = ''
	 *
	 * @return bool
	 */
	final public function Write($sDesc, $iDescType = \MailSo\Log\Enumerations\Type::INFO, $sName = '')
	{
		return $this->writeImplementation(
			$this->loggerLineImplementation($this->getTimeWithMicroSec(), $sDesc, $iDescType, $sName));
	}

	/**
	 * @final
	 * @return bool
	 */
	final public function Clear()
	{
		return $this->clearImplementation();
	}

	/**
	 * @return void
	 */
	public function WriteEmptyLine()
	{
	}
}
