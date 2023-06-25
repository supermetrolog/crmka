<?php

namespace app\models\location;
use app\models\oldDb;

class Region extends oldDb\location\Region
{
    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        return [
            'id' => $fields['id'],
            'title' => $fields['title'],
            'title_eng' => $fields['title_eng'],
            'exclude' => $fields['exclude'],
            'deleted' => $fields['deleted'],
        ];
    }
}