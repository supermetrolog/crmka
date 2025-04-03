<?php

declare(strict_types=1);

namespace app\usecases\Utilities;

use app\dto\Utilities\FixObjectPurposesUtilitiesDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\usecases\Object\ObjectService;

class UtilitiesService
{
    private ObjectService $objectService;

    public function __construct(
        ObjectService $objectService
    )
    {
        $this->objectService = $objectService;
    }

    /**
     * @throws SaveModelException
     */
    public function fixLandObjectPurposes(FixObjectPurposesUtilitiesDto $dto): void
    {
        $this->objectService->fixLandObjectPurposes($dto->object, $dto->purposes);
    }
}