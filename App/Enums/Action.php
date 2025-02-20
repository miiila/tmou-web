<?php declare(strict_types=1);
namespace InstruktoriBrno\TMOU\Enums;

use Grifart\Enum\AutoInstances;
use Grifart\Enum\Enum;

final class Action extends Enum
{
    use AutoInstances;

    // Basic actions
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const CREATE = 'create';
    public const DELETE = 'delete';

    // Specific actions
    public const LOGIN = 'login';
    public const REGISTER = 'register';
    public const LOGOUT = 'logout';
    public const FORGOTTEN_PASSWORD = 'forgotten_password';
    public const RESET_PASSWORD = 'reset_password';
    public const CHANGE_DETAILS = 'change_details';

    public const QUALIFICATION = 'qualification';

    public const IMPERSONATE = 'impersonate';
    public const DEIMPERSONATE = 'deimpersonate';

    // Specific actions for simulating game clock
    public const CHANGE_GAME_CLOCK = 'change_game_clock';
}
