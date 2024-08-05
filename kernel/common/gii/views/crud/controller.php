<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->modelClass, '\\') ?>;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class <?= $controllerClass ?> extends AppController
{
	/**
	 * @throws ValidateException
	 */
    public function actionIndex(): ActiveDataProvider
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= $searchModelAlias ?? $searchModelClass ?>();
        return $searchModel->search(Yii::$app->request->queryParams);
<?php else: ?>
        return new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);
<?php endif; ?>
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionView(int <?= $actionParams ?>): <?= $modelClass . "\n" ?>
    {
		return $this->findModel(<?= $actionParams ?>);
    }

	/**
	 * @throws SaveModelException
	 */
    public function actionCreate(): <?= $modelClass ?>
    {
        $model = new <?= $modelClass ?>();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate(int <?= $actionParams ?>): <?= $modelClass ?>
    {
		$model = $this->findModel($id);

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
    public function actionDelete(int <?= $actionParams ?>): void
    {
		$this->findModel(<?= $actionParams ?>)->delete();
    }


	/**
	 * @throws NotFoundHttpException
	 */
    protected function findModel(int <?= $actionParams ?>): ?<?= $modelClass . "\n" ?>
    {
		if (($model = <?= $modelClass ?>::findOne(<?= $actionParams ?>)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
    }
}
