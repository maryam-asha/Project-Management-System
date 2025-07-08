<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class UpdateOverdueTasks extends Command
{
    protected $signature = 'app:update-overdue-tasks';
    protected $description = 'تحديث حالة المهام المتأخرة إلى \"overdue\" تلقائياً';

    public function handle()
    {
        $now = Carbon::now();
        $count = Task::where('due_date', '<', $now)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'overdue')
            ->update(['status' => 'overdue']);

        $this->info("تم تحديث $count مهمة إلى حالة متأخرة (overdue).");
        return 0;
    }
}
