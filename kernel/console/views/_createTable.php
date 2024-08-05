<?php

/**
 * Creates a call for the method `yii\db\Migration::createTable()`.
 */
/* @var $table string the name table */
/* @var $fields array the fields */
/* @var $foreignKeys array the foreign keys */

?>
		$tableName = '<?= $table ?>';
		$this->table($tableName, [
<?php foreach ($fields as $field):
    if (empty($field['decorators'])): ?>
            '<?= $field['property'] ?>',
<?php else: ?>
            <?= "'{$field['property']}' => \$this->{$field['decorators']}" ?>,
<?php endif;
endforeach; ?>
        ]);
<?= $this->render('@yii/views/_addForeignKeys', [
    'table' => $table,
    'foreignKeys' => $foreignKeys,
]);
