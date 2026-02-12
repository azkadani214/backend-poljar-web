<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\Membership;
use App\Models\Role;
use App\Exceptions\Api\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class UserImportService
{
    /**
     * Import users from CSV file
     * 
     * @param UploadedFile $file
     * @return array Summary of import
     */
    public function importFromCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');
        
        // Normalize header
        $header = array_map(function($h) {
            return strtolower(trim($h));
        }, $header);

        $rows = [];
        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $rows[] = array_combine($header, $row);
        }
        fclose($handle);

        return $this->importFromArray($rows);
    }

    /**
     * Import users from array of data
     * 
     * @param array $rows
     * @return array Summary of import
     */
    public function importFromArray(array $rows): array
    {
        $results = [
            'success_count' => 0,
            'failure_count' => 0,
            'errors' => []
        ];

        foreach ($rows as $index => $data) {
            $rowNumber = $index + 1;
            
            try {
                DB::beginTransaction();

                $this->processRow($data);

                DB::commit();
                $results['success_count']++;

            } catch (\Exception $e) {
                DB::rollBack();
                $results['failure_count']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $data['email'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process a single import row
     * 
     * @param array $data
     * @throws \Exception
     */
    private function processRow(array $data): void
    {
        // 1. Validate & Create User
        if (empty($data['email'])) {
            throw new \Exception("Email is empty");
        }

        $user = User::where('email', $data['email'])->first();
        
        $userData = [
            'name' => $data['name'] ?? 'No Name',
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => isset($data['gender']) ? strtolower($data['gender']) : null,
            'birth_date' => !empty($data['birth_date']) ? $data['birth_date'] : null,
            'address' => $data['address'] ?? null,
            'bio' => $data['bio'] ?? null,
            'quotes' => $data['quotes'] ?? null,
            'website' => $data['website'] ?? null,
            'status' => isset($data['status']) ? strtolower($data['status']) : 'active',
        ];

        if (!$user) {
            $userData['password'] = Hash::make($data['password'] ?? 'poljar123');
            $user = User::create($userData);
        } else {
            $user->update($userData);
        }

        // 2. Assign Role
        if (!empty($data['role'])) {
            $roleNames = array_map('trim', explode('|', $data['role']));
            $user->syncRoles($roleNames);
        }

        // 3. Handle Division & Position (Membership)
        if (!empty($data['division']) && !empty($data['position'])) {
            // Find or Create Division
            $division = Division::firstOrCreate(['name' => trim($data['division'])]);

            // Find or Create Position
            $position = Position::firstOrCreate([
                'division_id' => $division->id,
                'name' => trim($data['position'])
            ], [
                'level' => (int)($data['level'] ?? 3) // Default to staff level if not provided
            ]);

            // Create/Update Membership
            Membership::updateOrCreate([
                'user_id' => $user->id,
                'division_id' => $division->id,
            ], [
                'position_id' => $position->id,
                'is_active' => true,
                'period' => $data['period'] ?? date('Y')
            ]);
        }
    }
}
