<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Log;
use Throwable;

class PurchaseOrderNotifier
{
    public function __construct(private BrevoMailer $brevo) {}

    public function send(PurchaseOrder $po): bool
    {
        $po->loadMissing(['supplier', 'items.rawMaterial', 'creator']);

        $email = $po->supplier?->email;

        if (! $email) {
            Log::warning("PO {$po->po_no}: supplier tidak punya email.");
            return false;
        }

        try {
            $html = view('emails.purchase-order', ['po' => $po])->render();

            $this->brevo->send(
                $email,
                $po->supplier->name,
                'Purchase Order ' . $po->po_no,
                $html,
                $po->creator?->email
            );

            $po->update(['email_sent_at' => now()]);

            return true;
        } catch (Throwable $e) {
            Log::error("PO {$po->po_no}: email gagal dikirim. " . $e->getMessage());
            return false;
        }
    }
}