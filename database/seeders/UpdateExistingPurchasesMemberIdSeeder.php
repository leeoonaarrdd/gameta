<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\Member;

class UpdateExistingPurchasesMemberIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all purchases and filter in PHP instead of using whereRaw
        $purchases = Purchase::all();
        
        foreach ($purchases as $purchase) {
            $notes = json_decode($purchase->notes, true) ?? [];
            
            // Check if this is a member price purchase
            if (isset($notes['is_member_price']) && $notes['is_member_price'] === true) {
                $whatsapp = $notes['whatsapp'] ?? null;
                
                if ($whatsapp) {
                    // Find member by phone number
                    $member = Member::where('phone', $whatsapp)->first();
                    
                    if ($member) {
                        $purchase->update(['member_id' => $member->id]);
                        $this->command->info("Updated purchase {$purchase->order_id} with member_id {$member->id}");
                    } else {
                        $this->command->warn("Member not found for phone: {$whatsapp}");
                    }
                }
            }
        }
        
        $this->command->info("Finished updating member_id for existing purchases");
    }
}
