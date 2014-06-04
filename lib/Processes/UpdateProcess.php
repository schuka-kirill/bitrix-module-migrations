<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\Migrations\Processes;


use WS\Migrations\ChangeDataCollector\CollectorFix;
use WS\Migrations\Entities\AppliedChangesLogModel;
use WS\Migrations\Module;
use WS\Migrations\SubjectHandlers\BaseSubjectHandler;

class UpdateProcess extends BaseProcess {

    private $_beforeChangesSnapshots = array();

    public function update(CollectorFix $fix) {
    }

    public function rollback(AppliedChangesLogModel $log) {
    }

    public function beforeChange(BaseSubjectHandler $subjectHandler, $data) {
        $id = $subjectHandler->getIdByChangeMethod(Module::FIX_CHANGES_BEFORE_CHANGE_KEY, $data);
        $this->_beforeChangesSnapshots[$id] = $snapshot = $subjectHandler->getSnapshot($id);
    }

    public function afterChange(BaseSubjectHandler $subjectHandler, CollectorFix $fix, $data) {
        $id = $subjectHandler->getIdByChangeMethod(Module::FIX_CHANGES_AFTER_CHANGE_KEY, $data);
        $actualData = $subjectHandler->getSnapshot($id);
        $data = $subjectHandler->analysisOfChanges($actualData, $this->_beforeChangesSnapshots[$id]);
        $fix
            ->setSubject(get_class($subjectHandler))
            ->setProcess(get_class($this))
            ->setData($data);

        $applyLog = new AppliedChangesLogModel();
        $applyLog->subjectName = get_class($subjectHandler);
        $applyLog->processName = get_class($this);
        $applyLog->description = $subjectHandler->getName().' - '.$id;
        $applyLog->originalData = $this->_beforeChangesSnapshots[$id];
        $applyLog->updateData = $data;
        $applyLog->groupLabel = $fix->getLabel();
        $applyLog->save();
    }
}