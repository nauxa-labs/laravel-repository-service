<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Abstracts;

use Nauxa\RepositoryService\Contracts\ServiceContract;

/**
 * Base Service Abstract Class
 *
 * This abstract class is intentionally left empty to provide maximum flexibility.
 * Extend this class in your service implementations and define your own methods
 * with any signature you need.
 *
 * Example usage:
 * ```php
 * class UserServiceImplement extends BaseService implements UserService
 * {
 *     public function create(UserDTO $dto, ?string $role = null): User
 *     {
 *         // Your implementation
 *     }
 * }
 * ```
 *
 * @package Nauxa\RepositoryService
 */
abstract class BaseService implements ServiceContract
{
    // This class is intentionally empty.
    // Implement your own service methods in child classes.
}
