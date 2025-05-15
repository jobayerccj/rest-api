<?php

declare(strict_types=1);

namespace App\Entity;

use Psr\Http\Message\ServerRequestInterface;
use App\Http\Error\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function validate(EntityInterface $entity, ServerRequestInterface $request, array $groups = []): void
    {
        $errors = $this->validator->validate(value: $entity, groups: $groups);

        if (count($errors) === 0) {
            return;
        }

        $validationErrors = [];

        foreach ($errors as $error) {
            $validationErrors[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }

        $validationException = new ValidationException($request);
        $validationException->setErrors($validationErrors);

        throw $validationException;
    }
  
}
