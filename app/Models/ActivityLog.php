<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    public $timestamps = false; // tabel hanya punya created_at

    protected $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id',
        'description', 'metadata', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Dipakai dari controller lain untuk mencatat aktivitas
    public static function record(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?string $description = null,
        array $metadata = []
    ): self {
        return static::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'description' => $description,
            'metadata'    => $metadata ?: null,
            'created_at'  => now(),
        ]);
    }

    public const MODULES = [
        'order'       => 'Order',
        'product'     => 'Produk',
        'category'    => 'Kategori',
        'inventory'   => 'Inventory',
        'material'    => 'Bahan Mentah',
        'procurement' => 'Procurement',
        'supplier'    => 'Supplier',
        'production'  => 'Produksi',
    ];

    public const ACTIONS = [
        'create'   => 'Menambah',
        'update'   => 'Mengubah',
        'delete'   => 'Menghapus',
        'complete' => 'Menyelesaikan',
        'send'     => 'Mengirim',
        'receive'  => 'Menerima',
        'adjust'   => 'Menyesuaikan',
        'process'  => 'Memproses',
    ];

    public function getModuleLabelAttribute(): string
    {
        return self::MODULES[$this->entity_type] ?? ucfirst($this->entity_type);
    }

    public function getTitleAttribute(): string
    {
        if (! empty($this->metadata['activity'])) {
            return $this->metadata['activity'];
        }

        $verb = self::ACTIONS[$this->action] ?? ucfirst($this->action);

        return $verb . ' ' . $this->module_label;
    }

    public function getDetailTitleAttribute(): string
    {
        return $this->metadata['title']
            ?? strtoupper($this->entity_type) . ($this->entity_id ? ' #' . $this->entity_id : '');
    }

    public function getDetailSubtitleAttribute(): string
    {
        return $this->metadata['subtitle'] ?? '';
    }

    // Class CSS avatar: admin / production
    public function getUserClassAttribute(): string
    {
        return $this->user?->role?->name === 'produksi' ? 'production' : 'admin';
    }
}