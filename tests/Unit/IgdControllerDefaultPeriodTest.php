<?php

namespace Tests\Unit;

use App\Http\Controllers\IgdController;
use Tests\TestCase;

class IgdControllerDefaultPeriodTest extends TestCase
{
    public function test_igd_default_period_uses_the_same_30_day_window_as_other_service_submenus(): void
    {
        $controller = new IgdController();

        [$awal, $akhir] = $controller->resolveDefaultPeriod('2026-09-14 08:20:00');

        $this->assertSame('2026-08-16', $awal->format('Y-m-d'));
        $this->assertSame('2026-09-14', $akhir->format('Y-m-d'));
    }
}
