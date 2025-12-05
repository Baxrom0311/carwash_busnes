<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use App\Models\WageEntry;
use App\Models\WageRule;
use Illuminate\Contracts\Queue\ShouldQueue; // Hozircha bu kerak emas

class CalculateWagesListener
{
    public function __construct()
    {
        //
    }

    public function handle(OrderUpdated $event): void
    {
        $order = $event->order;

        if ($order->wasChanged('status') && $order->status === 'paid') {
            foreach ($order->items as $item) {
                if (!$item->worker_id) {
                    continue;
                }

                $alreadyExists = WageEntry::where('order_item_id', $item->id)->exists();
                if ($alreadyExists) {
                    continue;
                }

                $rule = WageRule::where('tenant_id', $order->tenant_id)
                    ->where('service_id', $item->service_id)
                    ->where('is_active', true)
                    ->first();

                if (!$rule) {
                    $rule = WageRule::where('tenant_id', $order->tenant_id)
                        ->whereNull('service_id')
                        ->where('is_active', true)
                        ->first();
                }

                if (!$rule) {
                    continue;
                }

                $wageAmount = 0;
                if ($rule->rule_type === 'percent') {
                    $wageAmount = ($item->line_total * $rule->value) / 100;
                } else {
                    $wageAmount = $rule->value;
                }

                WageEntry::create([
                    'tenant_id' => $order->tenant_id,
                    'worker_id' => $item->worker_id,
                    'order_item_id' => $item->id,
                    'amount' => $wageAmount,
                    'period_date' => $order->created_at->toDateString(),
                ]);
            }
        }
    }
}
