<?php

/**
 * Creates a call for the method `yii\db\Migration::dropTable()`.
 */
/* @var $table string the name table */
/* @var $foreignKeys array the foreign keys */

echo $this->render('@yii/views/_dropForeignKeys', [
    'table' => $table,
    'foreignKeys' => $foreignKeys,
]) ?>
		$tableName = '<?= $table ?>';
        $this->dropTable($tableName);
