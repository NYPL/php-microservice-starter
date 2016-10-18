<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\DB;
use NYPL\Starter\Model;

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
