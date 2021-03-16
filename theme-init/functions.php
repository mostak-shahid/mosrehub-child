<?php
require_once('file_dir/plugin-update-checker.php');
$themeInit = Puc_v4_Factory::buildUpdateChecker(
	'http://tem2.belocal.today/themes/mosremovals/theme.json',
	__FILE__,
	'mosremovals'
);