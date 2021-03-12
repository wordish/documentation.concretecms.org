<?php

/**
 * @project:   ConcreteCMS Documentation
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

/** @noinspection PhpInconsistentReturnPointsInspection */

namespace PortlandLabs\ConcreteCms\Documentation\Migration\Publisher\Block;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use PortlandLabs\ConcreteCms\MigrationTool\Entity\Import\Batch;
use PortlandLabs\ConcreteCms\MigrationTool\Entity\Import\Area;
use PortlandLabs\ConcreteCms\MigrationTool\Entity\Import\BlockValue\BlockValue;
use Concrete\Core\Page\Page;

class MarkdownPublisher
{
    public function publish(Batch $batch, $bt, Page $page, $area, BlockValue $value)
    {
        $app = Application::getFacadeApplication();
        /** @var Navigation $navService */
        $navService = $app->make(Navigation::class);
        $data = $value->getRecords()->get(0)->getData();
        $inspector = $app->make('migration/import/value_inspector', [$batch]);
        $result = $inspector->inspect($data['content']);
        $text = $result->getReplacedContent();

        $text = preg_replace(
            [
                '/{CCM:BASE_URL}/i'
            ],
            [
                Application::getApplicationURL(),
            ],
            $text
        );

        // now we add in support for the links
        $text = preg_replace_callback(
            '/{CCM:CID_([0-9]+)}/i',
            function ($matches) use ($navService) {
                $cID = $matches[1];
                if ($cID > 0) {
                    $c = Page::getByID($cID, 'ACTIVE');
                    /** @noinspection PhpParamsInspection */
                    return $navService->getLinkToCollection($c);
                }
            },
            $text
        );

        // now we add in support for the links
        $text = preg_replace_callback(
            '/{CCM:FID_([0-9]+)}/i',
            function ($matches) {
                $fID = $matches[1];
                if ($fID > 0) {
                    $f = File::getByID($fID);
                    if ($f instanceof FileEntity) {
                        $fv = $f->getApprovedVersion();
                        if ($fv instanceof Version) {
                            return $fv->getURL();
                        }
                    }
                }
            },
            $text
        );

        // now files we download
        $text = preg_replace_callback(
            '/{CCM:FID_DL_([0-9]+)}/i',
            function ($matches) {
                $fID = $matches[1];
                if ($fID > 0) {
                    return (string)Url::to('/download_file', 'view', $fID);
                }
            },
            $text
        );

        $data['content'] = $text;

        $b = $page->addBlock($bt, $area, $data);

        return $b;
    }

}
