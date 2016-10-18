<?php
namespace NYPL\API\Model\ModelTrait;

use NYPL\API\DB;
use NYPL\API\Model;

trait DBDeleteTrait
{
    public function delete($id = '')
    {
        $sql = DB::getDatabase()->delete()
            ->from($this->getTableName())
            ->where($this->getIdName(), '=', $id);

        $sql->execute();

        return true;
    }
}
