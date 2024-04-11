<?php

namespace app\kernel\common\controller;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\exceptions\http\ValidateHttpException;
use app\kernel\web\http\resources\JsonResource;
use yii\base\InvalidRouteException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class AppController extends Controller
{
	protected array $exceptAuthActions = [];

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
					'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
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
				'errors' => $e->getModel()->getErrors()
			];
		}
	}
}