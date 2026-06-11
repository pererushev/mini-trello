<?php

namespace App\Support;

class BoardDefaults
{
    /**
     * @var list<string>
     */
    public const COLUMN_TITLES = ['К выполнению', 'В работе', 'Готово'];

    public const COLUMN_TODO = 'К выполнению';

    public const COLUMN_IN_PROGRESS = 'В работе';

    public const COLUMN_DONE = 'Готово';
}
