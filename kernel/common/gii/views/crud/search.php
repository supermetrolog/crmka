<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\models\Form;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

class <?= $searchModelClass ?> extends Form
{
	<?php foreach ($searchAttributes as $attribute): ?>
public $<?= $attribute ?>;
	<?php endforeach; ?>

    public function rules(): array
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

	/**
	 * @throws ValidateException
	 */
    public function search(array $params): ActiveDataProvider
    {
        $query = <?= $modelAlias ?? $modelClass ?>::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		$this->validateOrThrow();

        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
}
