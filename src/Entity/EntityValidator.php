<?php

declare(strict_types=1);

namespace App\Entity;

use App\Http\Error\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
        
    }

    public function validate(EntityInterface $entity, ServerRequestInterface $request)
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) === 0) return;

        $validationErrors = [];

        foreach($errors as $error) {
            $validationErrors[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }

        // Create a ValidationException
        $validationException = new ValidationException($request);

        // Add errors to the ValidationException
        $validationException->setErrors($validationErrors);

        // Throw the exception
        throw $validationException;
    }
}