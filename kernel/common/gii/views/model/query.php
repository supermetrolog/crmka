<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

use app\kernel\common\models\exceptions\ModelNotFoundException;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{

    /**
     * @return <?= $modelFullClassName ?>[]|\yii\db\ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return <?= $modelFullClassName ?>|\yii\db\ActiveRecord|null
	 */
    public function one($db = null): ?<?= $modelFullClassName . "\n" ?>
    {
        return parent::one($db);
    }

	/**
	 * @return <?= $modelFullClassName ?>|\yii\db\ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): <?= $modelFullClassName . "\n" ?>
	{
		return parent::oneOrThrow($db);
	}
}
