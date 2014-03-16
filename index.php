<?php

if (!class_exists('Runtime')) {
	require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

$runner = new ContaoCommunityAlliance_Composer_Check_CheckRunner();
$multipleStatus = $runner->runAll();

$states = array();
foreach ($multipleStatus as $status) {
	$states[] = $status->getState();
}

function getContaoPath()
{
	$contaoPath = getcwd();

	do {
		$localconfigPath = $contaoPath
			. DIRECTORY_SEPARATOR . 'system'
			. DIRECTORY_SEPARATOR . 'config'
			. DIRECTORY_SEPARATOR . 'localconfig.php';

		if (file_exists($localconfigPath)) {
			return $contaoPath;
		}

		$contaoPath = dirname($contaoPath);
	}
	while ($contaoPath != '.' && $contaoPath != '/' && $contaoPath);

	return false;
}

function isComposerInstalled($contaoPath)
{
	return is_dir(
		$contaoPath
		. DIRECTORY_SEPARATOR . 'system'
		. DIRECTORY_SEPARATOR . 'modules'
		. DIRECTORY_SEPARATOR . '!composer'
	);
}

function mirror($source, $target)
{
	if (is_dir($source)) {
		mkdir($target, 0777, true);

		$files = scandir($source);

		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				mirror(
					$source . DIRECTORY_SEPARATOR . $file,
					$target . DIRECTORY_SEPARATOR . $file
				);
			}
		}
	}
	else {
		copy($source, $target);
	}
}

function remove($path)
{
	if (is_dir($path)) {
		$files = scandir($path);

		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				remove($path . DIRECTORY_SEPARATOR . $file);
			}
		}

		rmdir($path);
	}
	else {
		unlink($path);
	}
}

$contaoPath            = getContaoPath();
$installationSupported = class_exists('ZipArchive');
$composerInstalled     = isComposerInstalled($contaoPath);
$installationMessage   = false;
$requestUri            = preg_replace('~\?install.*~', '', $_SERVER['REQUEST_URI']);

if ($composerInstalled) {
	$installationMessage = Runtime::$translator->translate('messages', 'install.installed');
}
else if (!$contaoPath) {
	$installationMessage = Runtime::$translator->translate('messages', 'install.missing-contao');
}
else if (!$installationSupported) {
	$installationMessage = Runtime::$translator->translate('messages', 'install.unsupported');
}
else if (isset($_GET['install'])) {
	$tempFile      = tempnam(sys_get_temp_dir(), 'composer_');
	$tempDirectory = tempnam(sys_get_temp_dir(), 'composer_');

	unlink($tempDirectory);
	mkdir($tempDirectory);

	$archive = file_get_contents('https://github.com/contao-community-alliance/composer/archive/master.zip');
	file_put_contents($tempFile, $archive);
	unset($archive);

	$zip = new ZipArchive();
	$zip->open($tempFile);
	$zip->extractTo($tempDirectory);

	mirror(
		$tempDirectory
		. DIRECTORY_SEPARATOR . 'composer-master'
		. DIRECTORY_SEPARATOR . 'src'
		. DIRECTORY_SEPARATOR . 'system'
		. DIRECTORY_SEPARATOR . 'modules'
		. DIRECTORY_SEPARATOR . '!composer',
		$contaoPath
		. DIRECTORY_SEPARATOR . 'system'
		. DIRECTORY_SEPARATOR . 'modules'
		. DIRECTORY_SEPARATOR . '!composer'
	);

	remove($tempFile);
	remove($tempDirectory);

	$composerInstalled   = true;
	$installationMessage = Runtime::$translator->translate('messages', 'install.done');
}

?>
<!DOCTYPE html>
<html lang="<?php echo Runtime::$translator->getLanguage(); ?>">
<head>
	<meta charset="utf-8">
	<title>Composer Check 1.0</title>
	<meta name="robots" content="noindex,nofollow">
	<meta name="generator" content="Contao Community Alliance">
	<link rel="stylesheet" href="assets/cca/style.css">
	<link rel="stylesheet" href="assets/opensans/stylesheet.css">
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div id="wrapper">
	<header>
		<h1><a target="_blank" href="http://c-c-a.org/"><?php echo Runtime::$translator->translate('other', 'contao_community_alliance') ?></a></h1>
	</header>
	<section>
		<h2>Composer Check 1.0</h2>

		<?php if (count(Runtime::$errors)): ?>
			<h3><?php echo Runtime::$translator->translate('messages', 'errors.headline'); ?></h3>
			<p><?php echo Runtime::$translator->translate('messages', 'errors.description'); ?></p>
			<ul>
				<?php foreach (Runtime::$errors as $error): ?>
					<li class="check error">
						[<?php echo $error['errno']; ?>] <?php echo $error['errstr']; ?>
						<span><?php echo $error['errfile']; ?>:<?php echo $error['errline']; ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<hr/>
		<?php endif; ?>

		<h3><?php echo Runtime::$translator->translate('messages', 'checks.headline'); ?></h3>
		<ul>
			<?php foreach ($multipleStatus as $status): ?><li class="check <?php echo $status->getState(); ?>">
					<?php echo $status->getSummary() ?>
					<span><?php echo $status->getDescription(); ?></span>
				</li><?php endforeach; ?>
		</ul>

		<hr/>

		<h3><?php echo Runtime::$translator->translate('messages', 'status.headline'); ?></h3>
		<?php if (in_array(ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR, $states)): ?>
			<p class="check error"><?php echo Runtime::$translator->translate('messages', 'status.unsupported') ?></p>
		<?php elseif (in_array(ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_WARN, $states)): ?>
			<p class="check warning"><?php echo Runtime::$translator->translate('messages', 'status.maybe_supported'); ?></p>
		<?php elseif (in_array(ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK, $states)): ?>
			<p class="check ok"><?php echo Runtime::$translator->translate('messages', 'status.supported'); ?></p>
		<?php else: ?>
			<p class="check unknown"><?php echo Runtime::$translator->translate('messages', 'status.unknown'); ?></p>
		<?php endif; ?>

		<?php if ($installationMessage): ?>
			<p class="check <?php if (!$contaoPath || !$installationSupported): ?>error<?php else: ?>ok<?php endif; ?>"><?php echo $installationMessage ?></p>
		<?php endif; ?>
		<?php if (!$composerInstalled): if ($installationSupported && $contaoPath): ?>
			<p><a class="button" href="<?php echo $requestUri ?>?install"><?php echo Runtime::$translator->translate('messages', 'status.install'); ?></a></p>
		<?php else: ?>
			<p><span class="button disabled"><?php echo Runtime::$translator->translate('messages', 'status.install'); ?></span></p>
		<?php endif; endif; ?>
	</section>
</div>

<footer>
	<div class="inside">
		<p>&copy; <?php echo date('Y'); ?> <?php echo Runtime::$translator->translate('other', 'contao_community_alliance') ?></p>
		<ul>
			<li><a target="_blank" href="http://c-c-a.org/ueber-composer"><?php echo Runtime::$translator->translate('other', 'more_information') ?></a></li>
			<li><a target="_blank" href="https://github.com/contao-community-alliance/composer/issues"><?php echo Runtime::$translator->translate('other', 'ticket_system') ?></a></li>
			<li><a target="_blank" href="http://c-c-a.org/"><?php echo Runtime::$translator->translate('other', 'website') ?></a></li>
			<li><a target="_blank" href="https://github.com/contao-community-alliance"><?php echo Runtime::$translator->translate('other', 'github') ?></a></li>
		</ul>
	</div>
</footer>

</body>
</html>
