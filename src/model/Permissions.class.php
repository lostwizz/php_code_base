<?php

/** * **********************************************************************************************
 *
 */
Class Permissions {
	/*
	 * theses the the constants for ALL the possible permissions i.e. none, read,  write, dba and god (in hirachial order)
	 */

	const GOD_RIGHT = 'GOD';
	const DBA_RIGHT = 'DBA';
	const WRITE_RIGHT = 'Write';
	const READ_RIGHT = 'Read';
	const WILDCARD_RIGHT = '*';
	const NO_RIGHT = '__NO__RIGHT__';

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';



	protected static $permissions = array(
		self::GOD_RIGHT,
		self::DBA_RIGHT,
		self::WRITE_RIGHT,
		self::READ_RIGHT,
		self::WILDCARD_RIGHT,
		self::NO_RIGHT
	);

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $theRight
	 * @return bool
	 */
	public static function doesRightExists($theRight): bool {
		return \in_array($theRight, self::$permissions);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $arRights
	 * @return bool
	 */
	public static function areAllValidRights( ...$arRights) :bool {
		foreach ($arRights as $right) {
			if (!self::doesRightExists($right)) {
				return false;
			}
		}
		return true;
	}

}