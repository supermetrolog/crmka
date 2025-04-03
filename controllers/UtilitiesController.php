<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Utilities\UtilitiesFixPurposesForm;
use app\usecases\Utilities\UtilitiesService;

class UtilitiesController extends AppController
{
    private UtilitiesService $service;

    public function __construct(
        $id,
        $module,
        UtilitiesService $service,
        array $config = []
    )
    {
        $this->service = $service;

        parent::__construct($id, $module, $config);
    }

    /**
     * @throws SaveModelException
     * @throws ValidateException
     */
    public function actionFixLandObjectPurposes(): SuccessResponse
    {
        $form = new UtilitiesFixPurposesForm();

        $form->load($this->request->post());

        $form->validateOrThrow();

        $this->service->fixLandObjectPurposes($form->getDto());

        return $this->success('Назначение успешно исправлено');
    }
}
