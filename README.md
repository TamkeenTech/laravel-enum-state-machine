[![Latest Version on Packagist](https://img.shields.io/packagist/v/tamkeen-tech/laravel-enum-state-machine.svg?style=flat-square)](https://packagist.org/packages/tamkeen-tech/laravel-enum-state-machine)
[![Total Downloads](https://img.shields.io/packagist/dt/tamkeen-tech/laravel-enum-state-machine.svg?style=flat-square)](https://packagist.org/packages/tamkeen-tech/laravel-enum-state-machine)

# Introduction
This package simplify controlling the transition between model states, allowing you to prevent unlogically transition and also controlling the initial state of the model using the PHP enums. Each enum allows you to define your states, the allowed transitions and the initial state, all in one place.

Laravel Enum State Machine, also allows you to automatically record the history of the state transition for each define state machine.

This package is built to help developers with the least amount of work needed to get it working.

# Installation
You can install the package via composer:
```bash
composer require tamkeen-tech/laravel-enum-state-machine
```
Next, if you going to record the history

```bash
php artisan vendor:publish --tag=enum-state-machine-migrations
```

# Usage
Imagine you have bill with statuses, so at your bill model you will need to use `HasStateMachines` trait, and define your `protected $stateMachines` array variable to include all the fields that you going to apply the state machine on, and finally an optional variable `protected $recordStateHistory` boolean to either save history or not.

```php
use TamkeenTech\LaravelEnumStateMachine\Traits\HasStateMachines;

class Bill extends Model
{
    use HasStateMachines;

    protected $fillable = [
        'status',
    ];

    protected $casts = [
        'status' => BillStatus::class,
    ];

    protected $recordStateHistory = true;

    protected $stateMachines = [
        'status'
    ];
}
```

In your `BillStatus` class you need to use trait `StateMachine` and to add two methods `transitions` to define your transition array and `initialState` to define the allowed initial states, both must return array.

```php
use TamkeenTech\LaravelEnumStateMachine\Traits\StateMachine;

enum BillStatus: string
{
    use StateMachine;

    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case EXPIRED = 'EXPIRED';
    case REFUNDED = 'REFUNDED';

    public function transitions(): array
    {
        return match ($this) {
            self::PENDING => [self::PAID, self::EXPIRED],
            self::PAID => [self::REFUNDED]
        };
    }

    public function initialState(): array
    {
        return [self::PENDING];
    }
}
```

From the example above the allowed transitions for `PENDING` case are `PAID` and `EXPIRED`, and the initail state for bill must be only `PENDING`

Now you are all done, you can start writing your code safely. So now if your tried to create a bill with different state you will receive an exception.

**Example**

- create bill with different initail state
  ```php
  // throw InitailStateIsNotAllowedException with message "Only allowed initial states: PENDING"
  Bill::create([
    'status' => BillStatus::PAID
  ]);
  ```

- try to update to invalid transition
  ```php
  $bill = Bill::create([
    'status' => BillStatus::PENDING
  ]);

  // throw StateTransitionNotAllowedException with message "Only allowed transition states: PAID, EXPIRED"
  $bill->update([
    'status' => BillStatus::REFUNDED
  ]);
  ```
### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email theshreif@gmail.com instead of using the issue tracker.

## Credits

- [Andrés laravel-eloquent-state-machines](https://github.com/asantibanez/laravel-eloquent-state-machines)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.