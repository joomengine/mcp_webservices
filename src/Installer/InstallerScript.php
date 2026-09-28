<?php
/**
 * @package    JoomEngine.Mcp
 * @created    28 September 2026
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @copyright  Copyright (C) 2026 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE
 */
namespace VDM\Plugin\Webservices\JoomEngineMcp\Installer;


use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;


/**
 * Enable a new installation while preserving administrator choices on updates.
 *
 * @since 0.1.1
 */
final class InstallerScript implements InstallerScriptInterface
{
	/** @var DatabaseInterface Joomla extension registry. @since 0.1.1 */
	private DatabaseInterface $database;

	/** @param DatabaseInterface $database Native database. @since 0.1.1 */
	public function __construct(DatabaseInterface $database)
	{
		$this->database = $database;
	}

	/** @inheritDoc */
	public function install(InstallerAdapter $adapter): bool
	{
		$db = $this->database;
		$query = $db->createQuery()->update($db->quoteName('#__extensions'))->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('webservices'))
			->where($db->quoteName('element') . ' = ' . $db->quote('joomengine_mcp'));
		$db->setQuery($query)->execute();

		return true;
	}

	/** @inheritDoc */
	public function update(InstallerAdapter $adapter): bool
	{
		return true;
	}

	/** @inheritDoc */
	public function uninstall(InstallerAdapter $adapter): bool
	{
		return true;
	}

	/** @inheritDoc */
	public function preflight(string $type, InstallerAdapter $adapter): bool
	{
		return true;
	}

	/** @inheritDoc */
	public function postflight(string $type, InstallerAdapter $adapter): bool
	{
		return true;
	}
}
