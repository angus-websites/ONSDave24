<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'code' => 'AL',
                'name' => 'Annual Leave',
                'description' => 'Paid time off for vacation, personal, or other reasons.',
                'paid' => true,
                'core' => true,
            ],
            [
                'code' => 'SL',
                'name' => 'Sick Leave',
                'description' => 'Paid time off for illness or injury.',
                'paid' => true,
                'core' => true,
            ],
            [
                'code' => 'FL',
                'name' => 'Flexi Leave',
                'description' => 'Using flexi time to take time off.',
                'paid' => true,
                'core' => true,
            ],
            [
                'code' => 'PH',
                'name' => 'Public Holiday',
                'description' => 'Paid time off for public holidays.',
                'paid' => true,
                'core' => true,
            ],
            [
                'code' => 'UL',
                'name' => 'Unpaid Leave',
                'description' => 'Time off without pay.',
                'paid' => false,
            ],
            [
                'code' => 'ML',
                'name' => 'Maternity Leave',
                'description' => 'Paid time off for pregnancy and childbirth.',
                'paid' => true,
            ],
            [
                'code' => 'PL',
                'name' => 'Paternity Leave',
                'description' => 'Paid time off for the birth or adoption of a child.',
                'paid' => true,
            ],
            [
                'code' => 'BL',
                'name' => 'Bereavement Leave',
                'description' => 'Paid time off for the death of a family member.',
                'paid' => true,
            ],
            [
                'code' => 'PRL',
                'name' => 'Privilege Leave',
                'description' => 'Paid time off for privileged days.',
                'paid' => true,
            ],

        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
