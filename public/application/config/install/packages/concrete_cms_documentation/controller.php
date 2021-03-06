<?php
namespace Application\StartingPointPackage\ConcreteCmsDocumentation;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\Routine\AttachModeInstallRoutine;
use Concrete\Core\Package\StartingPointInstallRoutine;
use Concrete\Core\Package\StartingPointPackage;

class Controller extends StartingPointPackage
{
    protected $pkgHandle = 'concrete_cms_documentation';

    public function getPackageName()
    {
        return t('documentation.concretecms.org');
    }

    public function getPackageDescription()
    {
        return 'Installs the documentation.concretecms.org starting point.';
    }
    
}
