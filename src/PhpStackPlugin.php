<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of PhpStack for BASE3 Framework.
 *
 * PhpStack provides centrally bundled server-side PHP libraries for
 * BASE3 plugins without introducing plugin-local Composer installations.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 **********************************************************************/

namespace PhpStack;

use Base3\Api\ICheck;
use Base3\Api\IContainer;
use Base3\Api\IPlugin;
use Dompdf\Dompdf;

final class PhpStackPlugin implements IPlugin, ICheck {

	public function __construct(
		private readonly IContainer $container
	) {}

	// Implementation of IBase

	public static function getName(): string {
		return 'phpstackplugin';
	}

	// Implementation of IPlugin

	public function init() {
		$this->container
			->set(self::getName(), $this, IContainer::SHARED);

		$autoload = $this->getDompdfAutoloadFile();
		if (is_file($autoload)) {
			require_once $autoload;
		}
	}

	// Implementation of ICheck

	public function checkDependencies() {
		$autoload = $this->getDompdfAutoloadFile();

		return [
			'dompdf_distribution' => is_file($autoload) ? 'Ok' : 'dompdf packaged release not installed',
			'dompdf_class' => class_exists(Dompdf::class) ? 'Ok' : 'dompdf not loaded'
		];
	}

	private function getDompdfAutoloadFile(): string {
		return dirname(__DIR__)
			. DIRECTORY_SEPARATOR . 'lib'
			. DIRECTORY_SEPARATOR . 'dompdf'
			. DIRECTORY_SEPARATOR . 'autoload.inc.php';
	}
}
