<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\DB;
use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use NYPL\Starter\Model\LocalDateTime;

trait DBUpdateTrait
{
    /**
     * @param array $data
     *
     * @throws APIException
     */
    public function update($data = [])
    {
        if (!$this->getFilters()) {
            throw new APIException('No filters set for update request');
        }

        if (!$data) {
            throw new APIException('No data provided for update', null, 0, null, 404);
        }

        $data = $this->checkUpdatedDate($data);

        if (!$this->updateDbRecord($data)) {
            throw new APIException('No records were found to update', null, 0, null, 404);
        }

        $this->read();

        if ($this instanceof MessageInterface) {
            $this->publishMessage($this->getObjectName(), $this->createMessage());
        }
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function updateDbRecord(array $data = [])
    {
        $insertValues = $this->getValueArray(false, $data);

        $updateStatement = DB::getDatabase()->update($insertValues)
            ->table($this->getTableName());

        $this->applyFilters(
            $this->getFilters(),
            $updateStatement
        );

        return $updateStatement->execute();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function checkUpdatedDate(array $data = [])
    {
        $dateUpdatedGetter = 'getUpdatedDate';
        $dateUpdatedSetter = 'setUpdatedDate';

        if (method_exists($this, $dateUpdatedGetter) && method_exists($this, $dateUpdatedSetter)) {
            if (!$this->$dateUpdatedGetter()) {
                $this->$dateUpdatedSetter(new LocalDateTime(LocalDateTime::FORMAT_DATE_TIME_RFC));

                $data['updatedDate'] = $this->getJsonObjectValue($this->$dateUpdatedGetter());
            }
        }

        return $data;
    }
}
