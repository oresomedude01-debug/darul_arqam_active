<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\AcademicTerm;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoAcknowledgeTermDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:acknowledge-terms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create calendar events for term beginning and end dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $terms = AcademicTerm::all();
        $createdCount = 0;

        foreach ($terms as $term) {
            // Create event for term beginning
            $beginningEventExists = Event::where('academic_term_id', $term->id)
                ->where('type', 'term_begin')
                ->where('start_date', $term->start_date)
                ->exists();

            if (!$beginningEventExists) {
                Event::create([
                    'title' => "Term Begins: {$term->name}",
                    'type' => 'term_begin',
                    'start_date' => $term->start_date,
                    'end_date' => $term->start_date,
                    'description' => "Academic term '{$term->name}' officially begins",
                    'color' => '#10b981', // Green
                    'academic_term_id' => $term->id,
                    'is_auto_acknowledged' => true,
                    'created_by' => 1, // System user
                ]);
                $createdCount++;
                $this->info("✓ Created 'Term Begins' event for {$term->name}");
            }

            // Create event for term ending
            $endingEventExists = Event::where('academic_term_id', $term->id)
                ->where('type', 'term_end')
                ->where('start_date', $term->end_date)
                ->exists();

            if (!$endingEventExists) {
                Event::create([
                    'title' => "Term Ends: {$term->name}",
                    'type' => 'term_end',
                    'start_date' => $term->end_date,
                    'end_date' => $term->end_date,
                    'description' => "Academic term '{$term->name}' officially ends",
                    'color' => '#ef4444', // Red
                    'academic_term_id' => $term->id,
                    'is_auto_acknowledged' => true,
                    'created_by' => 1, // System user
                ]);
                $createdCount++;
                $this->info("✓ Created 'Term Ends' event for {$term->name}");
            }
        }

        $this->info("\n✅ Process complete! Created {$createdCount} new term events.");
        return 0;
    }
}
