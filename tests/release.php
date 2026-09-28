<?php
/**
 * @package    JoomEngine.Mcp
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @copyright  Copyright (C) 2026 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE
 */

require dirname(__DIR__) . '/tools/release.php';
$root = sys_get_temp_dir() . '/mcp-webservices-release-' . bin2hex(random_bytes(8));
mkdir($root, 0700);
$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void
{
	if (!$condition)
	{
		throw new RuntimeException($message);
	}

	$checks++;
};
$reject = static function (string $command, string $version, string $message) use ($root, $check): void
{
	$before = array_map('file_get_contents', glob($root . '/*'));
	$rejected = false;

	try
	{
		mcpRelease([$command, $version], $root);
	}
	catch (RuntimeException)
	{
		$rejected = true;
	}

	$check($rejected && $before === array_map('file_get_contents', glob($root . '/*')), $message);
};

try
{
	$pending = '<changelog><element>plg_webservices_joomengine_mcp</element><type>plugin</type>'
		. '<version>[[[NEXT_VERSION]]]</version><fix><item>Release metadata.</item></fix></changelog>';
	file_put_contents($root . '/joomengine_mcp.xml', '<extension type="plugin" group="webservices">'
		. '<version>1.0.0</version><creationDate>January 2026</creationDate></extension>');
	file_put_contents($root . '/changelog.xml', '<changelogs>' . $pending . '</changelogs>');
	file_put_contents($root . '/CHANGELOG.md', "# Changelog\n\n## [[[NEXT_VERSION]]]\n\n### Fix\n\n- Release metadata.\n");
	file_put_contents($root . '/joomengine_mcp_update_server.xml', '<updates/>');
	$originalFeed = file_get_contents($root . '/joomengine_mcp_update_server.xml');
	$reject('feed', '1.0.0', 'An unprepared version cannot advertise a download.');
	$reject('prepare', '0.9.0', 'Release versions cannot precede the manifest.');

	foreach (['01.1.0', '1.0', '1.0.0-rc1', '1.0.0;false'] as $invalid)
	{
		$reject('prepare', $invalid, 'Only supported stable versions are accepted.');
	}

	foreach (['CHANGELOG.md', 'changelog.xml'] as $path)
	{
		$contents = file_get_contents($root . '/' . $path);
		file_put_contents($root . '/' . $path, str_replace('[[[NEXT_VERSION]]]', '1.0.0', $contents));
		$reject('prepare', '1.1.0', 'Both changelogs require a pending section.');
		file_put_contents($root . '/' . $path, str_replace('[[[NEXT_VERSION]]]', '[[[NEXT_VERSION]]] [[[NEXT_VERSION]]]', $contents));
		$reject('prepare', '1.1.0', 'Multiple pending markers are rejected.');
		file_put_contents($root . '/' . $path, $contents);
	}

	mcpRelease(['prepare', 'v1.0.0'], $root);
	$manifest = mcpReleaseXml($root . '/joomengine_mcp.xml');
	$check($manifest->getElementsByTagName('version')->item(0)->textContent === '1.0.0', 'First release may use the current manifest version.');
	$check($manifest->getElementsByTagName('creationDate')->item(0)->textContent === gmdate('F Y'), 'Manifest date is updated.');
	$check(!str_contains(file_get_contents($root . '/CHANGELOG.md'), '[[[NEXT_VERSION]]]')
		&& !str_contains(file_get_contents($root . '/changelog.xml'), '[[[NEXT_VERSION]]]'), 'Both changelogs are frozen.');
	$check(file_get_contents($root . '/joomengine_mcp_update_server.xml') === $originalFeed, 'Preparing a tag leaves the feed unchanged.');
	$reject('feed', '1.1.0', 'Feed publication must match the prepared manifest.');

	mcpRelease(['feed', '1.0.0'], $root);
	$feed = mcpReleaseXml($root . '/joomengine_mcp_update_server.xml');
	$query = new DOMXPath($feed);
	$check($query->evaluate('string(/updates/update/downloads/downloadurl)')
		=== 'https://github.com/joomengine/mcp_webservices/archive/refs/tags/v1.0.0.zip', 'Feed downloads the immutable source tag.');
	$check($query->query('/updates/update[element="joomengine_mcp" and type="plugin" and folder="webservices" and client="site"]')->length === 1,
		'Feed identifies the independent webservices plugin.');
	$check($query->evaluate('string(/updates/update/infourl)') === 'https://github.com/joomengine/mcp_webservices/tree/v1.0.0'
		&& $query->evaluate('string(/updates/update/changelogurl)') === 'https://raw.githubusercontent.com/joomengine/mcp_webservices/main/changelog.xml',
		'Feed links use the native Joomla metadata and fixed repository URLs.');
	$check($query->query('/updates/update/sha512')->length === 0, 'Checksum generation belongs to OctoShoom.');
	mcpReleaseAppend($feed->documentElement->firstChild, 'sha512', str_repeat('a', 128));
	$feed->save($root . '/joomengine_mcp_update_server.xml');
	$hashedFeed = file_get_contents($root . '/joomengine_mcp_update_server.xml');
	mcpRelease(['feed', '1.0.0'], $root);
	$check(file_get_contents($root . '/joomengine_mcp_update_server.xml') === $hashedFeed, 'Retry preserves existing feed bytes and hashes.');

	file_put_contents($root . '/changelog.xml', str_replace('<changelogs>', '<changelogs>' . $pending, file_get_contents($root . '/changelog.xml')));
	file_put_contents($root . '/CHANGELOG.md', "## [[[NEXT_VERSION]]]\n\n### Fix\n\n- Next release.\n\n" . file_get_contents($root . '/CHANGELOG.md'));
	$reject('prepare', '1.0.0', 'A pending section cannot reuse a published changelog version.');
	mcpRelease(['prepare', '1.1.0'], $root);
	mcpRelease(['feed', '1.1.0'], $root);
	$query = new DOMXPath(mcpReleaseXml($root . '/joomengine_mcp_update_server.xml'));
	$check($query->query('/updates/update')->length === 2, 'Next release retains the previous update.');
	$check($query->evaluate('string(/updates/update[1]/version)') === '1.1.0', 'Newest update is first.');
	$check($query->evaluate('string(/updates/update[version="1.0.0"]/sha512)') === str_repeat('a', 128), 'Next release retains the previous checksum.');
	$check((new DOMXPath(mcpReleaseXml($root . '/changelog.xml')))->query('/changelogs/changelog[version="1.0.0"]')->length === 1
		&& str_contains(file_get_contents($root . '/CHANGELOG.md'), '## 1.0.0'), 'Both changelogs preserve released history.');
	$reject('feed', '1.0.0', 'An older tag cannot update the current release feed.');

	echo json_encode(['checks' => $checks, 'metadataTransitions' => 'passed'], JSON_THROW_ON_ERROR) . "\n";
}
finally
{
	foreach (glob($root . '/*') as $path)
	{
		unlink($path);
	}

	rmdir($root);
}
