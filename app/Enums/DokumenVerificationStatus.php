<?php

namespace App\Enums;

enum DokumenVerificationStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RevisionRequired = 'revision_required';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingReview => 'Menunggu Pemeriksaan',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::RevisionRequired => 'Perlu Revisi',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(
            fn (self $status): string => $status->value,
            self::cases()
        );
    }
}
