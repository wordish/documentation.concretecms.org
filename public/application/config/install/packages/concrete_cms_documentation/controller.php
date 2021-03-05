<?php
namespace Application\StartingPointPackage\ConcreteCmsTraining;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\Routine\AttachModeInstallRoutine;
use Concrete\Core\Package\StartingPointInstallRoutine;
use Concrete\Core\Package\StartingPointPackage;

class Controller extends StartingPointPackage
{
    protected $pkgHandle = 'concrete_cms_training';

    public function getPackageName()
    {
        return t('training.concretecms.com');
    }

    public function getPackageDescription()
    {
        return 'Installs the training.concretecms.com starting point.';
    }
    
}
