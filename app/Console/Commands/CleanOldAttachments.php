<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanOldAttachments extends Command
{
    protected $signature = 'app:clean-old-attachments';
    protected $description = 'حذف المرفقات غير المستخدمة والتي مر عليها أكثر من 30 يوم';

    public function handle()
    {
        $threshold = Carbon::now()->subDays(30);
        $attachments = Attachment::whereNull('attachable_id')
            ->where('created_at', '<', $threshold)
            ->get();

        $count = 0;
        foreach ($attachments as $attachment) {
            if ($attachment->path && Storage::disk($attachment->disk)->exists($attachment->path)) {
                Storage::disk($attachment->disk)->delete($attachment->path);
            }
            $attachment->delete();
            $count++;
        }

        $this->info("تم حذف $count مرفق قديم غير مستخدم.");
        return 0;
    }
}
