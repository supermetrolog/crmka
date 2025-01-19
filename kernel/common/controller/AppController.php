<?php

namespace app\kernel\common\controller;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\resources\JsonResource;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use Yii;
use yii\base\InvalidRouteException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\User;

class AppController extends Controller
{
	protected User  $user;
	protected array $exceptAuthActions = [];

	public function __construct($id, $module, $config = [])
	{
		$this->user = Yii::$app->user;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'contentNegotiator' => [
				'class'   => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
					'application/xml'  => Response::FORMAT_XML,
				],
			],
			'verbFilter'        => [
				'class'   => VerbFilter::class,
				'actions' => $this->verbs(),
			],
			'rateLimiter'       => [
				'class' => RateLimiter::class,
			],
			'corsFilter'        => [
				'class' => Cors::class,
				'cors'  => [
					'Origin'                         => ['*'],
					'Access-Control-Request-Method'  => ['*'],
					'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Expose-Headers'  => ['*']
				]
			],
			'authenticator'     => [
				'class'  => HttpBearerAuth::class,
				'except' => $this->exceptAuthActions,
			]
		];
	}

	/**
	 * @param       $id
	 * @param array $params
	 *
	 * @return array|mixed|null
	 * @throws InvalidRouteException
	 * @throws NotFoundHttpException
	 */
	public function runAction($id, $params = [])
	{
		try {
			$res = parent::runAction($id, $params);

			if ($res instanceof JsonResource) {
				return $res->toArray();
			}

			return $res;
		} catch (ValidateException|SaveModelException $e) {
			$this->response->setStatusCode(422);

			return [
				'success' => false,
				'errors'  => $e->getModel()->getErrors()
			];
		} catch (ModelNotFoundException $e) {
			throw new NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @param $action
	 *
	 * @return bool
	 * @throws BadRequestHttpException
	 */
	public function beforeAction($action): bool
	{
		$this->response->on(Response::EVENT_BEFORE_SEND, function () {
			$this->response->headers->remove('link');
		});

		return parent::beforeAction($action);
	}


	public function success(?string $message = null, int $code = 200): SuccessResponse
	{
		$this->response->setStatusCode($code);

		return new SuccessResponse($message);
	}

	public function successf(string $pattern, array $params, int $code = 200): SuccessResponse
	{
		return $this->success(sprintf($pattern, ...$params), $code);
	}

	public function error(?string $message = null, int $code = 400): ErrorResponse
	{
		$this->response->setStatusCode($code);

		return new ErrorResponse($message);
	}

	public function errorf(string $pattern, array $params, int $code = 400): ErrorResponse
	{
		return $this->error(sprintf($pattern, ...$params), $code);
	}
}